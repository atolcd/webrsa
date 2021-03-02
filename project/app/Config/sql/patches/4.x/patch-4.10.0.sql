SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Ajout de la vue permettant de calculer les statistiques plan pauvreté
DROP MATERIALIZED VIEW IF EXISTS public.statppview;
CREATE MATERIALIZED VIEW public.statppview
TABLESPACE pg_default
AS WITH liste_mois AS (
         SELECT dd.d::date AS d
           FROM generate_series('2016-12-01'::date::timestamp with time zone, '2022-01-01'::date::timestamp with time zone, '1 mon'::interval) dd(d)
        ), liste_personne AS (
         SELECT DISTINCT historiquedroit.personne_id AS idpersonne
           FROM historiquesdroits historiquedroit
          WHERE historiquedroit.etatdosrsa::text = '2'::text
        ), personne_adresse_raw AS (
         SELECT lp.idpersonne,
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
        ), personne_adresse AS (
         SELECT personne_adresse_raw.idpersonne,
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
        ), personne_first_apparition AS (
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
        ), liste_personne_updated AS (
         SELECT lp.idpersonne,
            pfa.first_apparition
           FROM liste_personne lp
             JOIN personne_first_apparition pfa ON pfa.id = lp.idpersonne
        ), liste_historique AS (
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
        ), list_histo_clean AS (
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
        )
 SELECT lh.annee,
    lh.mois,
    lh.idpersonne,
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
    NOT lh.first_apparition AND lag(lh.historiquedroit__etatdosrsa, 1) OVER (PARTITION BY lh.idpersonne ORDER BY lh.annee, lh.mois) IS NULL OR (lag(lh.historiquedroit__etatdosrsa, 1) OVER (PARTITION BY lh.idpersonne ORDER BY lh.annee, lh.mois)::text = ANY (ARRAY['5'::character varying::text, '6'::character varying::text])) AND lh.historiquedroit__etatdosrsa::text = '2'::text AS nouvel_entrant,
    lag(lh.historiquedroit__etatdosrsa, 1) OVER (PARTITION BY lh.idpersonne ORDER BY lh.annee, lh.mois) AS previous_etat,
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
    lci.datevalidation_ci AS contratinsertion__datevalidation_ci,
    lci.rg_ci AS contratinsertion__rg_ci,
    lcui.faitle AS cui__faitle,
    lcui.decision_cui AS cui__decision_cui
   FROM list_histo_clean lh
     JOIN personne_adresse pa ON lh.idpersonne = pa.idpersonne AND lh.date >= pa.dtemm AND lh.date <= pa.dtdem
     LEFT JOIN orientsstructs lo ON date_trunc('month'::text, lh.date) = date_trunc('month'::text, lo.date_valid::timestamp with time zone) AND lo.personne_id = lh.idpersonne
     LEFT JOIN typesorients typeorient ON typeorient.id = lo.typeorient_id
     LEFT JOIN structuresreferentes structurereferente ON structurereferente.id = lo.structurereferente_id
     LEFT JOIN contratsinsertion lci ON lci.rg_ci = 1 AND lci.datevalidation_ci > lo.date_valid AND lci.personne_id = lh.idpersonne AND lci.decision_ci = 'V'::bpchar
     LEFT JOIN cuis lcui ON lcui.decision_cui::text = 'V'::text AND date_trunc('month'::text, lh.date) >= date_trunc('month'::text, lcui.dateembauche::timestamp with time zone) AND date_trunc('month'::text, lh.date) <= date_trunc('month'::text, lcui.findecontrat::timestamp with time zone) AND lcui.personne_id = lh.idpersonne
WITH DATA;

-- Relances SMS
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, created, modified)
select 'relances.chemin.export', '"app/Vendor/relances/"', 'Chemin d''export des fichiers de relance SMS.', current_timestamp, current_timestamp
where not exists (select id from configurations where lib_variable LIKE 'relances.chemin.export');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Relances' AND configurations.lib_variable LIKE 'relances.chemin.export';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************