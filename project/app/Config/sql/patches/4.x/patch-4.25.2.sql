SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.25.2', CURRENT_TIMESTAMP);

--Modification de la vue matérialisée pour prendre en compte la vraie date de début d'ouverture du droit
drop MATERIALIZED VIEW if exists public.statppview;

CREATE MATERIALIZED VIEW public.statppview
TABLESPACE pg_default
AS WITH liste_mois AS (
         SELECT dd.d::date AS d
           FROM generate_series('2016-12-01'::date::timestamp with time zone, '2024-01-01'::date::timestamp with time zone, '1 mon'::interval) dd(d)
        ), liste_personne AS (
         SELECT DISTINCT historiquedroit.personne_id AS idpersonne
           FROM historiquesdroits historiquedroit
          WHERE historiquedroit.etatdosrsa::text = '2'::text
        ), personne_adresse_raw AS (
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
        ), personne_adresse AS (
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
         	CASE
				WHEN
					lag(historiquedroit.etatdosrsa) OVER (PARTITION BY historiquedroit.personne_id ORDER BY historiquedroit.created) != historiquedroit.etatdosrsa
					OR lag(historiquedroit.etatdosrsa) OVER (PARTITION BY historiquedroit.personne_id ORDER BY historiquedroit.created) IS NULL
				THEN true
				ELSE false
			END AS first_date,
            rank() OVER (PARTITION BY (date_trunc('month'::text, liste_mois.d::timestamp with time zone)), historiquedroit.personne_id ORDER BY historiquedroit.created DESC) AS ranking
           FROM liste_mois
             JOIN historiquesdroits historiquedroit ON date_trunc('month'::text, liste_mois.d::timestamp with time zone) >= date_trunc('month'::text, historiquedroit.created) AND date_trunc('month'::text, liste_mois.d::timestamp with time zone) <= date_trunc('month'::text, historiquedroit.modified)
             JOIN liste_personne_updated lp ON lp.idpersonne = historiquedroit.personne_id
             JOIN personnes p ON p.id = historiquedroit.personne_id
             JOIN foyers f ON f.id = p.foyer_id
        ), liste_histo_par_mois AS (
         select
         	*
          FROM liste_historique
          WHERE liste_historique.ranking = 1
        ), date_debut_droit as (
        	select distinct on (l1.historiquedroit__id) l1.historiquedroit__id, l2.historiquedroit__created
        	from liste_histo_par_mois l1 join liste_historique l2
        		on l2.first_date = true
        		and l1.idpersonne = l2.idpersonne
        		and l2.historiquedroit__created <= l1.historiquedroit__created
        	where l1.ranking = 1
        	order by l1.historiquedroit__id, l2.historiquedroit__created desc
        ), list_histo_clean as (
	        select
		    	liste_histo_par_mois.annee,
		        liste_histo_par_mois.mois,
		        liste_histo_par_mois.date,
		        liste_histo_par_mois.idpersonne,
		        liste_histo_par_mois.historiquedroit__id,
		        liste_histo_par_mois.first_apparition,
		        liste_histo_par_mois.historiquedroit__etatdosrsa,
		        liste_histo_par_mois.historiquedroit__toppersdrodevorsa,
		        date_debut_droit.historiquedroit__created as historiquedroit__created,
		        liste_histo_par_mois.historiquedroit__modified
	        from liste_histo_par_mois join date_debut_droit on date_debut_droit.historiquedroit__id = liste_histo_par_mois.historiquedroit__id
        ), orient_un_mois AS (
         SELECT o2.personne_id,
            o2.date_valid,
            rank() OVER (PARTITION BY o2.personne_id, (date_trunc('month'::text, o2.date_valid::timestamp with time zone)) ORDER BY o2.date_valid DESC) AS ranking,
            (d2.lib_dreesorganisme_code != 'orientes_pole_emploi') as horspe
            FROM orientsstructs o2
	        left join structuresreferentes s on o2.structurereferente_id = s.id
	        left join dreesorganismes d2 on d2.id = s.dreesorganisme_id
        ), generate_pa_ne AS (
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
            lh.first_apparition AND NOT (lh.historiquedroit__etatdosrsa::text = '2'::text AND lh.historiquedroit__toppersdrodevorsa::text = '1'::text) AND lag(lh.historiquedroit__etatdosrsa, 1) OVER (PARTITION BY lh.idpersonne ORDER BY lh.annee, lh.mois) IS NULL AS primo_temp,
            lh.historiquedroit__etatdosrsa::text = '2'::text
               and(
                	(
                		(
	                		lag(lh.historiquedroit__etatdosrsa, 1) over (partition by lh.idpersonne
			                order by
			                lh.annee,
			                lh.mois) is null
		                )
		                and not lh.first_apparition
		            )
	                or
	                (
	                	lag(lh.historiquedroit__etatdosrsa, 1) over (partition by lh.idpersonne
		                order by
		                lh.annee,
		                lh.mois)::text = any (array['5'::character varying::text,
		                '6'::character varying::text])
		            )
	                or
	                (
	                	(
		                	lag(lh.historiquedroit__etatdosrsa, 1) over (partition by lh.idpersonne
			                order by
			                lh.annee,
			                lh.mois)::text = any (array['3'::character varying::text,
			                '4'::character varying::text])
			            )
	                	and
	                	(
	                		lag(lh.historiquedroit__created, 1) OVER (PARTITION BY lh.idpersonne ORDER BY lh.annee, lh.mois) < lh.historiquedroit__created - interval '1 year'
	                	)
		            )
		         )
            as nouvel_entrant,
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
            lcui.decision_cui AS cui__decision_cui,
            lo_un_mois.horspe as Orientation_hors_pe
           FROM list_histo_clean lh
             JOIN personne_adresse pa ON lh.idpersonne = pa.idpersonne AND lh.date >= pa.dtemm AND lh.date <= pa.dtdem
             LEFT JOIN orientsstructs lo ON  date_trunc('month'::text, lh.date) = date_trunc('month'::text, lo.date_valid::timestamp with time zone) AND lo.personne_id = lh.idpersonne
             LEFT JOIN orient_un_mois lo_un_mois ON lo_un_mois.date_valid >= lh.historiquedroit__created AND lo_un_mois.date_valid <= (lh.historiquedroit__created + '1 mon'::interval) AND lo_un_mois.personne_id = lh.idpersonne AND lo_un_mois.ranking = 1
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
   gpn.orientation_hors_pe,
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
