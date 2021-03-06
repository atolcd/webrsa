SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Ajout de la vue permettant de calculer les statistiques plan pauvreté
DROP MATERIALIZED VIEW IF EXISTS public.statppview;
CREATE MATERIALIZED VIEW public.statppview
TABLESPACE pg_default
AS
-- liste_mois permet de générer les années et les mois permettant d'associer l'état de la personne pour la bonne année et le bon mois
WITH liste_mois AS (
         SELECT dd.d::date AS d
           FROM generate_series('2016-12-01'::date::timestamp with time zone, '2022-01-01'::date::timestamp with time zone, '1 mon'::interval) dd(d)
        ),
        -- liste_personne liste les personnes présentes dans l'historique étant DOV
        liste_personne AS (
         SELECT DISTINCT historiquedroit.personne_id AS idpersonne
           FROM historiquesdroits historiquedroit
          WHERE historiquedroit.etatdosrsa::text = '2'::text
        ),
        -- personne_adresse_raw permet de mettre la dernière date de déménagement à l'année 3000 pour ne pas avoir de valeur NULL
        personne_adresse_raw AS (
         SELECT lp.idpersonne,
            f.dossier_id,
            a.nomvoie,
            a.nomcom,
            a.numcom,
            c.canton,
            af.dtemm,
            rank() OVER (PARTITION BY lp.idpersonne ORDER BY af.dtemm) AS ranking,
            COALESCE(lead(af.dtemm, 1) OVER (PARTITION BY lp.idpersonne ORDER BY af.dtemm), '3000-01-01'::date) AS dtdem
           FROM liste_personne lp
             JOIN personnes p ON p.id = lp.idpersonne
             JOIN foyers f ON f.id = p.foyer_id
             LEFT JOIN adressesfoyers af ON af.foyer_id = f.id
             LEFT JOIN adresses a ON a.id = af.adresse_id
             LEFT JOIN adresses_cantons ac ON ac.adresse_id = a.id
             LEFT JOIN cantons c ON c.id = ac.canton_id
        ),
        -- personne_adresse permet de mettre la première date d'emménagement à l'année 1900 pour ne pas confondre les adresses
        personne_adresse AS (
         SELECT personne_adresse_raw.idpersonne,
            personne_adresse_raw.dossier_id,
            personne_adresse_raw.nomvoie,
            personne_adresse_raw.nomcom,
            personne_adresse_raw.numcom,
            personne_adresse_raw.canton,
                CASE personne_adresse_raw.ranking
                    WHEN 1 THEN '1900-01-01'::date
                    ELSE personne_adresse_raw.dtemm
                END AS dtemm,
            personne_adresse_raw.dtdem
           FROM personne_adresse_raw
        ), nir_conflict AS (
         SELECT p.id,
            p.id < p2.id AS first_apparition
           FROM personnes p
             JOIN personnes p2 ON p.nom::text = p2.nom::text AND p.prenom::text = p2.prenom::text AND p.dtnai = p2.dtnai
             JOIN prestations presta ON presta.personne_id = p.id
             JOIN prestations presta2 ON presta2.personne_id = p2.id
          WHERE p.id <> p2.id AND (presta.rolepers = ANY (ARRAY['DEM'::bpchar, 'CJT'::bpchar])) AND presta.natprest = 'RSA'::bpchar AND (presta2.rolepers = ANY (ARRAY['DEM'::bpchar, 'CJT'::bpchar])) AND presta2.natprest = 'RSA'::bpchar AND (p.nir IS NULL AND p2.nir IS NOT NULL OR p.nir IS NOT NULL AND p2.nir IS NULL)
        ),
        -- personne_first_apparition permet d'être sûr de la 1ere apparition de la personne dans le système, basé sur son prénom / nom / date de naissance / nir
        personne_first_apparition AS (
         SELECT p.id,
                CASE
                    WHEN nc.id IS NOT NULL THEN nc.first_apparition
                    ELSE
                    CASE
                        WHEN length(p.nir) = ANY (ARRAY[13, 15]) THEN rank() OVER (PARTITION BY p.nir ORDER BY p.id) = 1
                        ELSE rank() OVER (PARTITION BY p.nom, p.prenom, p.dtnai ORDER BY p.id) = 1
                    END
                END AS first_apparition
           FROM personnes p
             JOIN prestations presta ON presta.personne_id = p.id
             LEFT JOIN nir_conflict nc ON nc.id = p.id
          WHERE (presta.rolepers = ANY (ARRAY['DEM'::bpchar, 'CJT'::bpchar])) AND presta.natprest = 'RSA'::bpchar
        ),
        -- liste_personne_updated met à jour liste_personne en utilisant le filtre créé par personne_first_apparition
        liste_personne_updated AS (
         SELECT lp.idpersonne,
            pfa.first_apparition
           FROM liste_personne lp
             JOIN personne_first_apparition pfa ON pfa.id = lp.idpersonne
        ),
        -- liste_historique permet de lier la liste des personnes avec l'historique et la liste des mois
        -- et de mettre un rang sur la dernière date de création d'historique
        liste_historique AS (
         SELECT date_part('year'::text, liste_mois.d) AS annee,
            date_part('month'::text, liste_mois.d) AS mois,
            date_trunc('month'::text, liste_mois.d::timestamp with time zone) AS date,
            historiquedroit.personne_id AS idpersonne,
            historiquedroit.id AS historiquedroit__id,
            historiquedroit.etatdosrsa AS historiquedroit__etatdosrsa,
            historiquedroit.toppersdrodevorsa AS historiquedroit__toppersdrodevorsa,
            historiquedroit.created AS historiquedroit__created,
            historiquedroit.modified AS historiquedroit__modified,
            lp.first_apparition,
            rank() OVER (PARTITION BY (date_trunc('month'::text, liste_mois.d::timestamp with time zone)), historiquedroit.personne_id ORDER BY historiquedroit.created DESC) AS ranking
           FROM liste_mois
             JOIN historiquesdroits historiquedroit ON date_trunc('month'::text, liste_mois.d::timestamp with time zone) >= date_trunc('month'::text, historiquedroit.created) AND date_trunc('month'::text, liste_mois.d::timestamp with time zone) <= date_trunc('month'::text, historiquedroit.modified)
             JOIN liste_personne_updated lp ON lp.idpersonne = historiquedroit.personne_id
             JOIN personnes p ON p.id = historiquedroit.personne_id
             JOIN foyers f ON f.id = p.foyer_id
        ),
        -- list_histo_clean permet de ne prendre par mois que la dernière date de création d'historique
        list_histo_clean AS (
         SELECT liste_historique.annee,
            liste_historique.mois,
            liste_historique.date,
            liste_historique.idpersonne,
            liste_historique.historiquedroit__id,
            liste_historique.first_apparition,
            liste_historique.historiquedroit__etatdosrsa,
            liste_historique.historiquedroit__toppersdrodevorsa,
            liste_historique.historiquedroit__created,
            liste_historique.historiquedroit__modified
           FROM liste_historique
          WHERE liste_historique.ranking = 1
        ),
        -- orient_un_mois permet mettre un rang pour la date de validation de l'orientation  pour ne prendre que la 1ere orientation dans le mois
        orient_un_mois AS (
        	SELECT
        		o2.personne_id,
        		o2.date_valid,
            rank() OVER (PARTITION BY o2.personne_id, date_trunc('month', o2.date_valid) ORDER BY o2.date_valid DESC) AS ranking
        	FROM orientsstructs o2
        ),
        -- generate_pa_ne lie tous les CTE ensemble et met les jointure nécessaires aux stat, tout en calculant les primo, la base du nouvel entrant, l'état précédent de la personne
        -- ainsi que la génération d'un booleen pour l'orientation en moins d'un mois
        generate_pa_ne AS (
         SELECT lh.annee,
            lh.mois,
            lh.idpersonne,
            pa.dossier_id,
            pa.nomvoie,
            pa.nomcom,
            pa.numcom,
            pa.canton,
            pa.dtemm,
            pa.dtdem,
            lh.date,
            lh.historiquedroit__etatdosrsa,
            lh.historiquedroit__toppersdrodevorsa,
            lh.historiquedroit__created,
            lh.first_apparition AND lag(lh.historiquedroit__etatdosrsa, 1) OVER (PARTITION BY lh.idpersonne ORDER BY lh.annee, lh.mois) IS NULL AS primo,
            lh.first_apparition and not (lh.historiquedroit__etatdosrsa = '2' and lh.historiquedroit__toppersdrodevorsa = '1') AND lag(lh.historiquedroit__etatdosrsa, 1) OVER (PARTITION BY lh.idpersonne ORDER BY lh.annee, lh.mois) IS NULL AS primo_temp,
            NOT lh.first_apparition AND lag(lh.historiquedroit__etatdosrsa, 1) OVER (PARTITION BY lh.idpersonne ORDER BY lh.annee, lh.mois) IS NULL OR (lag(lh.historiquedroit__etatdosrsa, 1) OVER (PARTITION BY lh.idpersonne ORDER BY lh.annee, lh.mois)::text = ANY (ARRAY['5'::character varying::text, '6'::character varying::text])) AND lh.historiquedroit__etatdosrsa::text = '2'::text AS nouvel_entrant,
            lag(lh.historiquedroit__etatdosrsa, 1) OVER (PARTITION BY lh.idpersonne ORDER BY lh.annee, lh.mois) AS previous_etat,
            date_trunc('month'::text, lh.date) = date_trunc('month'::text, lh.historiquedroit__created) AND lo_un_mois.date_valid IS NOT NULL AS orientation_un_mois,
            lo_un_mois.date_valid AS orient_un_mois_date,
            lo.id AS orientstruct__id,
            lo.statut_orient AS orientstruct__statut_orient,
            lo.date_valid AS orientstruct__date_valid,
            lo.rgorient AS orientstruct__rgorient,
            typeorient.id AS typeorient__id,
            typeorient.lib_type_orient AS typeorient__lib_type_orient,
            typeorient.modele_notif AS typeorient__modele_notif,
            typeorient.parentid AS typeorient__parentid,
            structurereferente.typestructure AS structurereferente__typestructure,
            structurereferente.type_struct_stats AS structurereferente__type_struct_stats,
            structurereferente.code_stats AS structurereferente__code_stats,
            lci.date_saisi_ci AS contratinsertion__date_saisi_ci,
            lci.datevalidation_ci AS contratinsertion__datevalidation_ci,
            lci.rg_ci AS contratinsertion__rg_ci,
            lcui.faitle AS cui__faitle,
            lcui.decision_cui AS cui__decision_cui
           FROM list_histo_clean lh
             JOIN personne_adresse pa ON lh.idpersonne = pa.idpersonne AND lh.date >= pa.dtemm AND lh.date <= pa.dtdem
             LEFT JOIN orientsstructs lo ON date_trunc('month'::text, lh.date) = date_trunc('month'::text, lo.date_valid::timestamp with time zone) AND lo.personne_id = lh.idpersonne
             LEFT JOIN orient_un_mois lo_un_mois ON ( (lo_un_mois.date_valid BETWEEN lh.historiquedroit__created AND lh.historiquedroit__created + INTERVAL '1 month'  ) AND lo_un_mois.personne_id = lh.idpersonne AND lo_un_mois.ranking = 1)
             LEFT JOIN typesorients typeorient ON typeorient.id = lo.typeorient_id
             LEFT JOIN structuresreferentes structurereferente ON structurereferente.id = lo.structurereferente_id
             LEFT JOIN contratsinsertion lci ON lci.rg_ci = 1 AND lci.date_saisi_ci >= COALESCE(lo_un_mois.date_valid, lo.date_valid) AND lci.personne_id = lh.idpersonne AND lci.decision_ci = 'V'::bpchar
             LEFT JOIN cuis lcui ON lcui.decision_cui::text = 'V'::text AND date_trunc('month'::text, lh.date) >= date_trunc('month'::text, lcui.dateembauche::timestamp with time zone) AND date_trunc('month'::text, lh.date) <= date_trunc('month'::text, lcui.findecontrat::timestamp with time zone) AND lcui.personne_id = lh.idpersonne
        )
         SELECT gpn.annee,
            gpn.mois,
            gpn.idpersonne,
            gpn.dossier_id,
            gpn.nomvoie,
            gpn.nomcom,
            gpn.numcom,
            gpn.canton,
            gpn.dtemm,
            gpn.dtdem,
            gpn.date,
            gpn.historiquedroit__etatdosrsa,
            gpn.historiquedroit__toppersdrodevorsa,
            gpn.historiquedroit__created,
            gpn.primo,
            gpn.nouvel_entrant,
            gpn.nouvel_entrant AND gpn.historiquedroit__toppersdrodevorsa::text = '1'::text AS vrai_nouvel_entrant,
            gpn.previous_etat,
            gpn.orientation_un_mois,
            gpn.orient_un_mois_date,
            gpn.orientstruct__id,
            gpn.orientstruct__statut_orient,
            gpn.orientstruct__date_valid,
            gpn.orientstruct__rgorient,
            gpn.typeorient__id,
            gpn.typeorient__lib_type_orient,
            gpn.typeorient__modele_notif,
            gpn.typeorient__parentid,
            gpn.structurereferente__typestructure,
            gpn.structurereferente__type_struct_stats,
            gpn.structurereferente__code_stats,
            gpn.contratinsertion__date_saisi_ci,
            gpn.contratinsertion__datevalidation_ci,
            gpn.contratinsertion__rg_ci,
            gpn.cui__faitle,
            gpn.cui__decision_cui
           FROM generate_pa_ne gpn
WITH DATA;


-- *****************************************************************************
COMMIT;
-- *****************************************************************************