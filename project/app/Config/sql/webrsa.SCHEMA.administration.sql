--
-- PostgreSQL database dump
--

-- Dumped from database version 10.9
-- Dumped by pg_dump version 10.9

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: administration; Type: SCHEMA; Schema: -; Owner: webrsa
--

CREATE SCHEMA administration;


ALTER SCHEMA administration OWNER TO webrsa;

--
-- Name: brsa_rdv_honoresvues(text, text); Type: FUNCTION; Schema: administration; Owner: webrsa
--

CREATE FUNCTION administration.brsa_rdv_honoresvues(dd_rdv_honore_d1 text, df__rdv_honore_d1 text) RETURNS integer
    LANGUAGE plpgsql STRICT
    AS $_$

DECLARE
	
	/************************************************
	************* Liste des paramètres **************
	************************************************/
	-- dates de début de fin et année des rdv honores d1
	dd_rdv_honore_d1 CONSTANT date NOT NULL := $1;
	df_rdv_honore_d1 CONSTANT date NOT NULL := $2;
	
	--annee CONSTANT varchar(4) := EXTRACT (YEAR FROM dd_rdv_honore_d1);
	

	BEGIN
		/* ***********************************************
		******* LISTE DES TABLES TEMP COMMUNES ************
		*************************************************/

		drop view if exists bRSA_RDV;
		drop table if exists RDV_sansd1_allPDV;

			create table RDV_sansd1_allPDV as
			(
			SELECT distinct
			  personnes.id as personnes_id, 
			  personnes.qual, 
			  personnes.nom, 
			  personnes.prenom, 
			 /* personnes.dtnai, 
			  personnes.nir, 
			  personnes.sexe, 
			  adresses.numvoie, 
			  adresses.typevoie, 
			  adresses.nomvoie, 
			  adresses.complideadr, */
			  adresses.codepos, 
			--  adresses.locaadr, 
			 -- foyers.sitfam, 
			  dossiers.numdemrsa, 
			--  dossiers.dtdemrsa, 
			--  dossiers.dtdemrmi, 
			  dossiers.matricule, 
			  rendezvous.id as rendezvous_id, 
			  rendezvous.structurereferente_id, 
			  structuresreferentes.lib_struc as structuresreferentes_lib_struc, 
			  --structuresreferentes.ville as structuresreferentes_ville, 
			  rendezvous.daterdv,
			  --case when thematiquesrdvs."name"!='premier RV' then 'non_1er_rdv' 
				--else null 
				--when thematiquesrdvs."name"!='premier RV' then null
				--end as them_rdv, 
			  --rendezvous.objetrdv, 
			  typesrdv.libelle as typesrdv_libelle, 
			  referents.nom as nom_ref,
			  referents.prenom as prenom_ref,
			  referents.fonction as fonction_ref,
			  pdv_format_export.pdv as structure_referente_pdv, 
			  pdv_format_export.plaine_co as plaine_co,


			--  rendezvous.heurerdv, 
			  statutsrdvs.libelle as statutsrdvs_libelle,
			  case when questionnairesd1pdvs93.id is not null then 'OUI' else 'non' end as d1_existe,
			  questionnairesd1pdvs93.date_validation as datevalid_d1,
			  questionnairesd1pdvs93.created as datecrea_d1,  questionnairesd1pdvs93.modified as datemodif_d1

			FROM personnes 
			INNER JOIN rendezvous ON (rendezvous.personne_id = personnes.id)
			left outer join questionnairesd1pdvs93  ON (questionnairesd1pdvs93.rendezvous_id = rendezvous.id)

			LEFT OUTER JOIN typesrdv ON (rendezvous.typerdv_id = typesrdv.id)
			LEFT OUTER JOIN statutsrdvs ON (rendezvous.statutrdv_id = statutsrdvs.id)
			LEFT OUTER JOIN thematiquesrdvs ON (thematiquesrdvs.typerdv_id = typesrdv.id)
			LEFT OUTER JOIN referents ON (rendezvous.referent_id = referents.id)
			LEFT OUTER JOIN structuresreferentes ON (rendezvous.structurereferente_id = structuresreferentes.id)
			LEFT OUTER JOIN administration.pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
			LEFT OUTER JOIN foyers ON (personnes.foyer_id = foyers.id)
			LEFT OUTER JOIN adressesfoyers ON (adressesfoyers.foyer_id = foyers.id)
			LEFT OUTER JOIN adresses ON (adressesfoyers.adresse_id = adresses.id)
			LEFT OUTER JOIN dossiers ON (foyers.dossier_id = dossiers.id)
			WHERE 
			   adressesfoyers.rgadr='01'
			   -- RDV en annee
			   --AND EXTRACT (YEAR from daterdv)=annee
			   AND daterdv between (dd_rdv_honore_d1) and (df_rdv_honore_d1)
			   AND typesrdv.libelle='Individuel'
			   AND statutsrdvs.libelle IN ('honoré','prévu')
			   
				-- sans qd1 du tout
				  AND personnes.id NOT IN 
				(
				SELECT DISTINCT personnes.id
				FROM personnes inner join questionnairesd1pdvs93  ON (questionnairesd1pdvs93.personne_id = personnes.id)
				where extract(year from questionnairesd1pdvs93.date_validation)=2016
				)
			);
			   --and questionnairesd1pdvs93.id is null
			--ORDER BY personnes.nom, personnes.prenom, rendezvous.id);
			
			/* ***********************************************
			******* VUE **************************************
			*************************************************/
			
			-- afficher le premier rdv en date;
			CREATE OR REPLACE VIEW bRSA_RDV AS
			select distinct RDV_sansd1_allPDV.*
			from RDV_sansd1_allPDV
			where (rendezvous_id, personnes_id) in (select min(rendezvous_id), personnes_id from RDV_sansd1_allPDV group by personnes_id)
			--and thematiquesrdvs."name"!='premier RV'
			order by nom, structuresreferentes_lib_struc, daterdv;

		
		RETURN 0;
	END;

	
$_$;


ALTER FUNCTION administration.brsa_rdv_honoresvues(dd_rdv_honore_d1 text, df__rdv_honore_d1 text) OWNER TO webrsa;

--
-- Name: dec_pdv_b1b2_vues(text, text, text, text, integer, integer, integer); Type: FUNCTION; Schema: administration; Owner: webrsa
--

CREATE FUNCTION administration.dec_pdv_b1b2_vues(dd_periode_orient text, df_periode_orient text, dd_periode text, df_periode text, nb_mois integer, rdv_individuel integer, rdv_honore integer) RETURNS integer
    LANGUAGE plpgsql STRICT
    AS $_$

DECLARE
	
	/************************************************
	************* Liste des paramètres **************
	************************************************/
	
	-- dates de début de fin et année de periode pour les orientations
	dd_periode_orient CONSTANT date NOT NULL := $1;
	df_periode_orient CONSTANT date NOT NULL := $2;
	annee_periode_orient CONSTANT varchar(4) := EXTRACT (YEAR FROM dd_periode_orient);

	-- dates de début de fin et année de periode HORS orientations
	dd_periode CONSTANT date NOT NULL := $3;
	df_periode CONSTANT date NOT NULL := $4;
	annee_periode CONSTANT double precision := EXTRACT (YEAR FROM dd_periode);
	annee_prec CONSTANT double precision := annee_periode - 1;
	annee_prec2 CONSTANT double precision := annee_periode - 2;
	
	-- periode (6 ou 12)
	nb_mois CONSTANT integer := $5; 

	rdv_individuel CONSTANT integer := $6;

	rdv_honore CONSTANT integer := $7;

	BEGIN
		/* ***********************************************
		******* LISTE DES TABLES TEMP COMMUNES ************
		*************************************************/

		-- valid_orient_durant_periode
		CREATE TEMPORARY TABLE valid_orient_durant_periode AS
			SELECT DISTINCT orientsstructs.*, structuresreferentes.id AS strucref_orient	
			FROM orientsstructs 
				LEFT OUTER JOIN structuresreferentes ON (orientsstructs.structurereferente_id=structuresreferentes.id )	
			WHERE date_valid BETWEEN dd_periode_orient AND df_periode_orient 			
		;

		-- valid_orient_av_fin_periode	
		CREATE TEMPORARY TABLE valid_orient_av_fin_periode AS
			SELECT DISTINCT orientsstructs.*, structuresreferentes.id AS strucref_orient	
			FROM orientsstructs 
				LEFT OUTER JOIN structuresreferentes ON (orientsstructs.structurereferente_id=structuresreferentes.id )	
			WHERE date_valid <= df_periode_orient			
		;
	
		-- der_valid_orient_av_fin_periode
		CREATE TEMPORARY TABLE der_valid_orient_av_fin_periode AS
			SELECT DISTINCT * 
			FROM valid_orient_av_fin_periode  
			WHERE (id, personne_id) IN (
				SELECT MAX(valid_orient_av_fin_periode.id), valid_orient_av_fin_periode.personne_id	
				FROM valid_orient_av_fin_periode 
				GROUP BY personne_id	
			)	
			ORDER BY personne_id	
		;
		
		-- dd_durant_periode (
		CREATE TEMPORARY TABLE dd_durant_periode AS
			SELECT DISTINCT personne_id
			FROM historiquesdroits
			WHERE 
			(
				--1-- personnes avec DD créé ou modifié durant la période
				personne_id IN
				(
					SELECT DISTINCT personne_id FROM historiquesdroits
					WHERE historiquesdroits.toppersdrodevorsa =  '1'
						AND ( historiquesdroits.created, historiquesdroits.modified ) OVERLAPS ( dd_periode, df_periode ) 
				)
				OR 
				--2-- personnes sans enregistrement durant la période mais pour lesquelles le MAX histo de l'année precedente est DD
				( 
					personne_id NOT IN  
					(
						-- personnes avec un enregistrement créé ou modifié durant la période
						SELECT DISTINCT personne_id 
						FROM historiquesdroits
						WHERE ( historiquesdroits.created, historiquesdroits.modified ) OVERLAPS ( dd_periode, df_periode ) 
					)
					-- personnes dont le MAX histo est DD créé ou modifié l'année precedente
					AND (
						(personne_id,historiquesdroits.created ) IN 
							(SELECT DISTINCT personne_id,MAX(historiquesdroits.created) 
								FROM historiquesdroits
								WHERE EXTRACT(YEAR FROM historiquesdroits.created)=annee_prec 
									OR EXTRACT (YEAR FROM historiquesdroits.modified)=annee_prec
								GROUP BY personne_id
							)
						AND historiquesdroits.toppersdrodevorsa =  '1'
					)
				)
				OR
				--3-- personnes sans DD durant la période + non DD durant la période créé ou modifié après le début de la période  + MAX histo de l'année precedente=DD
				(
					personne_id NOT IN 
					(
						-- personnes avec DD créé ou modifié durant la période
						SELECT DISTINCT personne_id FROM historiquesdroits
						WHERE historiquesdroits.toppersdrodevorsa =  '1'
							AND ( historiquesdroits.created, historiquesdroits.modified ) OVERLAPS ( dd_periode, df_periode ) 
					)
					AND personne_id IN 
					(
						-- personnes avec non DD créé ou modifié après le début de la période
						SELECT DISTINCT personne_id FROM historiquesdroits
						WHERE historiquesdroits.toppersdrodevorsa =  '0'
							AND ( historiquesdroits.created, historiquesdroits.modified ) OVERLAPS ( dd_periode, df_periode )
					)
					-- personnes dont le MAX histo est DD créé ou modifié l'année précédente
					AND (
						(personne_id,historiquesdroits.created ) IN 
							(SELECT DISTINCT personne_id,MAX(historiquesdroits.created) 
								FROM historiquesdroits
								WHERE EXTRACT(YEAR FROM historiquesdroits.created)=annee_prec 
									OR EXTRACT (YEAR FROM historiquesdroits.modified)=annee_prec
								GROUP BY personne_id
							)
						AND historiquesdroits.toppersdrodevorsa =  '1'
					)
				)
			)
		;

		-- valid_orient_annee_prec
		CREATE TEMPORARY TABLE valid_orient_annee_prec AS
			SELECT DISTINCT *
			FROM orientsstructs
			WHERE EXTRACT (YEAR FROM orientsstructs.date_valid)=annee_prec 
		;

		-- dd_fin_annee_prec 
		CREATE TEMPORARY TABLE dd_fin_annee_prec AS
			SELECT DISTINCT personne_id
			FROM historiquesdroits
			WHERE 
				personne_id IN 
				(SELECT DISTINCT personne_id FROM historiquesdroits
					WHERE historiquesdroits.toppersdrodevorsa =  '1'
					AND 
					(EXTRACT (YEAR FROM historiquesdroits.created)=annee_prec
					OR
					EXTRACT (YEAR FROM historiquesdroits.modified)=annee_prec)
				)
				OR 
				(
					personne_id not IN  
						(SELECT DISTINCT personne_id 
						FROM historiquesdroits
						WHERE historiquesdroits.toppersdrodevorsa =  '1'
						AND 
						(EXTRACT (YEAR FROM historiquesdroits.created)=annee_prec
						OR
						EXTRACT (YEAR FROM historiquesdroits.modified)=annee_prec)
					)
					AND
					((personne_id,historiquesdroits.created ) IN  
						(SELECT DISTINCT personne_id,MAX(historiquesdroits.created) 
							FROM historiquesdroits
							WHERE EXTRACT(YEAR FROM historiquesdroits.created)=annee_prec2 
								OR EXTRACT( YEAR FROM historiquesdroits.modified)=annee_prec2
							GROUP BY personne_id
						)
					AND historiquesdroits.toppersdrodevorsa =  '1'
					)
				)
		; 

		/* ***********************************************
		******* LISTE DES TABLES TEMP partie 2 ************
		*************************************************/

		-- orient_tot : orientation durant la période
		-- DROP TABLE orient_tot CASCADE;
		CREATE TEMPORARY TABLE orient_tot AS
			SELECT DISTINCT structuresreferentes.id, pdv_format_export.pdv,pdv_format_export.plaine_co,COUNT( personnes.id) AS "Nb de psnes avec orientations validées dans la période" 	
			FROM valid_orient_durant_periode
				INNER JOIN personnes ON (personnes.id=valid_orient_durant_periode.personne_id)
				INNER JOIN structuresreferentes ON (valid_orient_durant_periode.structurereferente_id=structuresreferentes.id)
				LEFT OUTER JOIN administration.dec_pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id) 
			GROUP BY structuresreferentes.id, pdv_id, pdv, plaine_co
		;



		/* ***********************************************
		******* LISTE DES TABLES TEMP partie 3 ************
		*************************************************/

		-- suivi_annee_prec D1 validé en 2014 + RDV individuel 
		CREATE TEMPORARY TABLE suivi_annee_prec AS
			SELECT DISTINCT questionnairesd1pdvs93.*,rendezvous.daterdv
			FROM questionnairesd1pdvs93
				INNER JOIN rendezvous ON (questionnairesd1pdvs93.rendezvous_id=rendezvous.id)
			WHERE 
				EXTRACT (YEAR FROM questionnairesd1pdvs93.date_validation)=annee_prec 
				AND typerdv_id=rdv_individuel
		; 

	
		-- suivis_annee_prec_dd : suivi_annee_precedente + DD_annee_precedente (dans histo ou D1)
		CREATE TEMPORARY TABLE suivis_annee_prec_dd AS
			SELECT DISTINCT 
				personnes.id AS personne_id, personnes.nom, personnes.prenom, 
				CASE WHEN personnes.sexe='1' THEN 'homme' WHEN personnes.sexe='2' THEN 'femme' END AS sexe, 
				personnes.dtnai, 
				dossiers.matricule AS num_CAF,
				suivi_annee_prec.daterdv,
				structuresreferentes.id AS strucref_d1_annee_precedt_id, 
				pdv_format_export.pdv, 
				pdv_format_export.plaine_co
			FROM personnes
				-- D1 durant l'année précédent la periode traitée
				INNER JOIN suivi_annee_prec ON (suivi_annee_prec.personne_id = personnes.id)
				INNER JOIN questionnairesd1pdvs93 ON (questionnairesd1pdvs93.id=suivi_annee_prec.id)
				INNER JOIN situationsallocataires ON (questionnairesd1pdvs93.situationallocataire_id=situationsallocataires.id)
				INNER JOIN rendezvous ON (rendezvous.id = suivi_annee_prec.rendezvous_id )
				LEFT OUTER JOIN foyers ON (personnes.foyer_id = foyers.id)
				LEFT OUTER JOIN dossiers ON (foyers.dossier_id = dossiers.id)
				-- structure référente D1
				LEFT OUTER JOIN structuresreferentes ON (rendezvous.structurereferente_id = structuresreferentes.id)
				LEFT OUTER JOIN administration.dec_pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
				-- Soit DD dans histo droit
				LEFT OUTER JOIN dd_fin_annee_prec ON (dd_fin_annee_prec.personne_id=personnes.id)
			WHERE 
				--Soit DD dans histo droit
				dd_fin_annee_prec.personne_id IS NOT NULL
				OR
				-- Soit DD dans D1 annee_prec
				situationsallocataires.toppersdrodevorsa='1'
		;
				
		
		-- suivi_durant_periode : D1 validé durant periode + RDV individuel
		-- DROP TABLE suivi_durant_periode;
		CREATE TEMPORARY TABLE suivi_durant_periode AS
			SELECT DISTINCT questionnairesd1pdvs93.*,rendezvous.id as rdv_id,rendezvous.daterdv, rendezvous.referent_id
			FROM questionnairesd1pdvs93
				INNER JOIN rendezvous ON (questionnairesd1pdvs93.rendezvous_id=rendezvous.id)
			WHERE 
				EXTRACT (YEAR FROM questionnairesd1pdvs93.date_validation)=annee_periode 
				AND 
				EXTRACT (MONTH FROM questionnairesd1pdvs93.date_validation) BETWEEN 1 AND nb_mois
				AND typerdv_id=rdv_individuel
		;


		-- 1er RDV en N-1: MAJ
		-- DROP TABLE premier_rdv_indiv_honore_annee_prec;
		CREATE TEMPORARY TABLE premier_rdv_indiv_honore_annee_prec AS
			SELECT DISTINCT rendezvous.*
			FROM rendezvous 
			WHERE 
			(personne_id,rendezvous.id) IN
				(
				SELECT personne_id, MIN(rendezvous.id)
				FROM rendezvous 
				WHERE 
				EXTRACT (YEAR FROM daterdv)=annee_prec
				AND typerdv_id=rdv_individuel 
				AND statutrdv_id=rdv_honore
				GROUP BY personne_id
				ORDER BY personne_id
				)
			ORDER BY personne_id
		;


		-- 1er RDV en N: MAJ
		-- DROP TABLE premier_rdv_indiv_honore_periode;
		CREATE TEMPORARY TABLE premier_rdv_indiv_honore_periode AS
			SELECT DISTINCT rendezvous.*
			FROM rendezvous 
			WHERE 
			(personne_id,rendezvous.id) IN
				(
				SELECT personne_id, MIN(rendezvous.id)
				FROM rendezvous 
				WHERE 
				EXTRACT (YEAR FROM daterdv)=annee_periode
				AND typerdv_id=rdv_individuel 
				AND statutrdv_id=rdv_honore
				GROUP BY personne_id
				ORDER BY personne_id
				)
			ORDER BY personne_id
		;


		-- tout 1er RDV individuel honoré de la personne: MAJ
		-- DROP TABLE tout_premier_rdv_indiv_honore;
		CREATE TEMPORARY TABLE tout_premier_rdv_indiv_honore AS
			SELECT DISTINCT rendezvous.*
			FROM rendezvous 
			WHERE 
			(personne_id,rendezvous.id) IN
				(
				SELECT personne_id, MIN(rendezvous.id)
				FROM rendezvous 
				WHERE 
				typerdv_id=rdv_individuel 
				AND statutrdv_id= 1
				GROUP BY personne_id
				ORDER BY personne_id
				)
			ORDER BY personne_id
		;


		-- Dernier CER validé de l'année N: MAJ
		-- DROP TABLE dernier_cer_valide_N;
		CREATE TEMPORARY TABLE dernier_cer_valide_N AS
			SELECT DISTINCT 
			contratsinsertion.personne_id,
			contratsinsertion.id,
			contratsinsertion.dd_ci, 
			contratsinsertion.df_ci,
			cers93.duree, 
			cers93.datesignature, 
			contratsinsertion.datevalidation_ci,
			CASE WHEN cers93.positioncer='99valide' THEN 'oui' END AS "Valide CG?",
			sujetscers93_autres."name" AS "sujetscers93_autres",
			sujetscers93_formation."name" AS "sujetscers93_formation",
			sujetscers93_sante."name" AS "sujetscers93_sante",
			sujetscers93_autonomie_sociale."name" AS "sujetscers93_autonomie_sociale",
			sujetscers93_logement."name" AS "sujetscers93_logement",
			sujetscers93_emploi."name" AS "sujetscers93_emploi"
			FROM contratsinsertion
			INNER JOIN cers93 ON (cers93.contratinsertion_id = contratsinsertion.id AND cers93.positioncer='99valide')
			LEFT OUTER JOIN cers93_sujetscers93 cers93_sujetscers93_autres ON (cers93_sujetscers93_autres.cer93_id = cers93.id AND cers93_sujetscers93_autres.sujetcer93_id=6)
			LEFT OUTER JOIN sujetscers93 sujetscers93_autres ON (cers93_sujetscers93_autres.sujetcer93_id = sujetscers93_autres.id AND sujetscers93_autres."name"='Autre')
			LEFT OUTER JOIN cers93_sujetscers93 cers93_sujetscers93_formation ON (cers93_sujetscers93_formation.cer93_id = cers93.id AND cers93_sujetscers93_formation.sujetcer93_id=2)
			LEFT OUTER JOIN sujetscers93 sujetscers93_formation ON (cers93_sujetscers93_formation.sujetcer93_id = sujetscers93_formation.id AND sujetscers93_formation."name"='La Formation')
			LEFT OUTER JOIN cers93_sujetscers93 cers93_sujetscers93_sante ON (cers93_sujetscers93_sante.cer93_id = cers93.id AND cers93_sujetscers93_sante.sujetcer93_id=4)
			LEFT OUTER JOIN sujetscers93 sujetscers93_sante ON (cers93_sujetscers93_sante.sujetcer93_id = sujetscers93_sante.id AND sujetscers93_sante."name"='La Santé')
			LEFT OUTER JOIN cers93_sujetscers93 cers93_sujetscers93_aut_soc ON (cers93_sujetscers93_aut_soc.cer93_id = cers93.id AND cers93_sujetscers93_aut_soc.sujetcer93_id=3)
			LEFT OUTER JOIN sujetscers93 sujetscers93_autonomie_sociale ON (cers93_sujetscers93_aut_soc.sujetcer93_id = sujetscers93_autonomie_sociale.id AND sujetscers93_autonomie_sociale."name"='L''Autonomie sociale')
			LEFT OUTER JOIN cers93_sujetscers93 cers93_sujetscers93_logement ON (cers93_sujetscers93_logement.cer93_id = cers93.id AND cers93_sujetscers93_logement.sujetcer93_id=5)
			LEFT OUTER JOIN sujetscers93 sujetscers93_logement ON (cers93_sujetscers93_logement.sujetcer93_id = sujetscers93_logement.id AND sujetscers93_logement."name"='Le Logement')
			LEFT OUTER JOIN cers93_sujetscers93 cers93_sujetscers93_emploi ON (cers93_sujetscers93_emploi.cer93_id = cers93.id AND cers93_sujetscers93_emploi.sujetcer93_id=1)
			LEFT OUTER JOIN sujetscers93 sujetscers93_emploi ON (cers93_sujetscers93_emploi.sujetcer93_id = sujetscers93_emploi.id AND sujetscers93_emploi."name"='L''Emploi')
			WHERE 
			(personne_id, contratsinsertion.id) IN
				(
				SELECT personne_id,  MAX(contratsinsertion.id)
				FROM contratsinsertion 
				WHERE EXTRACT (YEAR FROM contratsinsertion.datevalidation_ci)=annee_periode
				GROUP BY personne_id
				ORDER BY personne_id
				)
			ORDER BY personne_id
		;


		-- Table difficultés de santé: MAJ
		-- DROP TABLE difficultes_sante;
		CREATE TEMPORARY TABLE difficultes_sante AS
			( 
				SELECT DISTINCT
				'sante'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id,
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev ,
				personnes.*
				FROM personnes 
				INNER JOIN dsps on (personnes.id=dsps.personne_id) 
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id) 
				LEFT OUTER JOIN detailsdifsocs AS sante ON ( dsps.id = sante.dsp_id AND sante.difsoc IN ( '0402', '0403' ) ) 
				LEFT OUTER JOIN dsps_revs ON 
					( 
					dsps.personne_id = dsps_revs.personne_id 
					AND (dsps_revs.personne_id, dsps_revs.id) IN 
						( 
						SELECT personne_id, MAX(dsps_revs.id) 
						FROM dsps_revs GROUP BY personne_id
						)
					) 
				LEFT OUTER JOIN detailsdifsocs_revs sante_revs ON ( dsps_revs.id = sante_revs.dsp_rev_id AND sante_revs.difsoc IN ( '0402', '0403' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND sante.difsoc IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND sante_revs.difsoc IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
				ORDER BY nom, prenom, dtnai
			)
		; 


		-- Table difficultés de logement: MAJ
		-- DROP TABLE difficultes_logement;
		CREATE TEMPORARY TABLE difficultes_logement AS
			( 
				SELECT 'logement'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id,
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev 
				, personnes.*
				FROM personnes 
				INNER JOIN dsps on (personnes.id=dsps.personne_id) 
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id)  
				LEFT OUTER JOIN detailsdiflogs AS detailsdiflogs ON ( dsps.id = detailsdiflogs.dsp_id AND detailsdiflogs.diflog IN ( '1004', '1005', '1006', '1007', '1008', '1009' ) ) 
				LEFT OUTER JOIN dsps_revs ON 
				( dsps.personne_id = dsps_revs.personne_id 
					AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
						SELECT personne_id, MAX(dsps_revs.id) 
						FROM dsps_revs 
						GROUP BY personne_id
					) 
				) 
				LEFT OUTER JOIN detailsdiflogs_revs detailsdiflogs_revs ON ( dsps_revs.id = detailsdiflogs_revs.dsp_rev_id AND detailsdiflogs_revs.diflog IN ( '1004', '1005', '1006', '1007', '1008', '1009' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND detailsdiflogs.diflog IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND detailsdiflogs_revs.diflog IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 

	
		-- Table difficultés familiales: MAJ
		-- DROP TABLE difficultes_familial;
		CREATE TEMPORARY TABLE difficultes_familial AS 
			( 
				SELECT 'familiales'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id,
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev
				,personnes.* 
				FROM personnes 
				INNER JOIN dsps on (personnes.id=dsps.personne_id) 
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id) 
				LEFT OUTER JOIN detailsaccosocfams AS detailsaccosocfams ON ( dsps.id = detailsaccosocfams.dsp_id AND detailsaccosocfams.nataccosocfam IN ( '0412' ) )
				LEFT OUTER JOIN dsps_revs ON 
				( dsps.personne_id = dsps_revs.personne_id 
					AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					) 
				) 
				LEFT OUTER JOIN detailsaccosocfams_revs detailsaccosocfams_revs ON ( dsps_revs.id = detailsaccosocfams_revs.dsp_rev_id AND detailsaccosocfams_revs.nataccosocfam IN ( '0412' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND detailsaccosocfams.nataccosocfam IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND detailsaccosocfams_revs.nataccosocfam IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 


		-- Table difficultés de mode de garde: MAJ
		-- DROP TABLE difficultes_mode_garde;
		CREATE TEMPORARY TABLE difficultes_mode_garde AS 
			( 
				SELECT 'modes_gardes'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id,
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev 
				FROM personnes 
				INNER JOIN dsps on (personnes.id=dsps.personne_id) 
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id) 
				LEFT OUTER JOIN detailsdifdisps AS detailsdifdisps ON ( dsps.id = detailsdifdisps.dsp_id AND detailsdifdisps.difdisp IN ( '0502', '0503', '0504' ) ) 
				LEFT OUTER JOIN dsps_revs ON 
				( dsps.personne_id = dsps_revs.personne_id 
					AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					)
				) 
				LEFT OUTER JOIN detailsdifdisps_revs detailsdifdisps_revs ON ( dsps_revs.id = detailsdifdisps_revs.dsp_rev_id AND detailsdifdisps_revs.difdisp IN ( '0502', '0503', '0504' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND detailsdifdisps.difdisp IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND detailsdifdisps_revs.difdisp IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
			AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 

	
		-- Table difficultés surendettement: MAJ
		-- DROP TABLE difficultes_surendettement;
		CREATE TEMPORARY TABLE difficultes_surendettement AS  
			( 
				SELECT 'surendettement'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id,
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev 
				FROM personnes 
				INNER JOIN dsps on (personnes.id=dsps.personne_id) 
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id) 
				LEFT OUTER JOIN detailsdifsocs AS surendettement ON ( dsps.id = surendettement.dsp_id AND surendettement.difsoc IN ( '0406' ) ) 
				LEFT OUTER JOIN dsps_revs ON 
				( dsps.personne_id = dsps_revs.personne_id 
					AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					)
				) 
				LEFT OUTER JOIN detailsdifsocs_revs surendettement_revs ON ( dsps_revs.id = surendettement_revs.dsp_rev_id AND surendettement_revs.difsoc IN ( '0406' ) )
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND surendettement.difsoc IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND surendettement_revs.difsoc IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 


		-- Table difficultés administratives: MAJ
		-- DROP TABLE difficultes_administratives;
		CREATE TEMPORARY TABLE difficultes_administratives AS    
			( 
				SELECT 'administratives'::text AS "difficultes_exprimees",
				personnes.id AS personne_id,
				dsps.id AS dsp,
				dsps_revs.id AS dsp_rev 
				FROM personnes
				INNER JOIN dsps on (personnes.id=dsps.personne_id)
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id)
				LEFT OUTER JOIN detailsdifsocs AS administratives ON ( dsps.id = administratives.dsp_id AND administratives.difsoc IN ( '0405' ) )
				LEFT OUTER JOIN dsps_revs ON 
				( dsps.personne_id = dsps_revs.personne_id 
					AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					) 
				)
				LEFT OUTER JOIN detailsdifsocs_revs administratives_revs ON ( dsps_revs.id = administratives_revs.dsp_rev_id AND administratives_revs.difsoc IN ( '0405' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND administratives.difsoc IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND administratives_revs.difsoc IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 

	
		-- Table difficultés linguistiques: MAJ
		-- DROP TABLE difficultes_linguistiques;
		CREATE TEMPORARY TABLE difficultes_linguistiques AS   
			( 
				SELECT 'linguistiques'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id,
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev 
				FROM personnes
				INNER JOIN dsps on (personnes.id=dsps.personne_id)
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id)
				LEFT OUTER JOIN detailsdifsocs AS linguistiques ON ( dsps.id = linguistiques.dsp_id AND linguistiques.difsoc IN ( '0404' ) )
				LEFT OUTER JOIN dsps_revs ON 
				( 
				dsps.personne_id = dsps_revs.personne_id 
				AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					) 
				)
				LEFT OUTER JOIN detailsdifsocs_revs linguistiques_revs ON ( dsps_revs.id = linguistiques_revs.dsp_rev_id AND linguistiques_revs.difsoc IN ( '0404' ) ) WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND linguistiques.difsoc IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND linguistiques_revs.difsoc IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 

	
		-- Table difficultés de qualifications professionnelles: MAJ
		-- DROP TABLE difficultes_qualification_pro;
		CREATE TEMPORARY TABLE difficultes_qualification_pro AS  
			( 
				SELECT 'qualification_professionnelle'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id,
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev 
				FROM personnes
				INNER JOIN dsps on (personnes.id=dsps.personne_id)
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id)
				LEFT OUTER JOIN dsps_revs ON ( 
				dsps.personne_id = dsps_revs.personne_id 
				AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					) 
				)
				LEFT OUTER JOIN dsps AS nivetu ON ( dsps.id = nivetu.id AND nivetu.nivetu IN ( '1206', '1207' ) )
				LEFT OUTER JOIN dsps_revs AS nivetu_revs ON ( dsps_revs.id = nivetu_revs.id AND nivetu_revs.nivetu IN ( '1206', '1207' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND nivetu.nivetu IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND nivetu_revs.nivetu IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 

	
		-- Table difficultés d'accès à l'emploi : MAJ
		-- DROP TABLE difficultes_acces_emploi;
		CREATE TEMPORARY TABLE difficultes_acces_emploi AS   
			( 
				SELECT 'acces_emploi'::text AS "difficultes_exprimees",
				personnes.id AS personne_id, 
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev 
				FROM personnes
				INNER JOIN dsps on (personnes.id=dsps.personne_id)
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id)
				LEFT OUTER JOIN dsps_revs ON ( 
				dsps.personne_id = dsps_revs.personne_id 
				AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					) 
				)
				LEFT OUTER JOIN dsps AS topengdemarechemploi ON ( dsps.id = topengdemarechemploi.id AND topengdemarechemploi.topengdemarechemploi IN ( '0' ) )
				LEFT OUTER JOIN dsps_revs AS topengdemarechemploi_revs ON ( dsps_revs.id = topengdemarechemploi_revs.id AND topengdemarechemploi_revs.topengdemarechemploi IN ( '0' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND topengdemarechemploi.topengdemarechemploi IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND topengdemarechemploi_revs.topengdemarechemploi IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 

	
		-- Table difficultés "autres" : MAJ
		-- DROP TABLE difficultes_autres;
		CREATE TEMPORARY TABLE difficultes_autres AS   
			( 
				SELECT 'autres'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id, 
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev 
				FROM personnes
				INNER JOIN dsps on (personnes.id=dsps.personne_id)
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id)
				LEFT OUTER JOIN detailsaccosocindis AS detailsaccosocindis ON ( dsps.id = detailsaccosocindis.dsp_id AND detailsaccosocindis.nataccosocindi IN ( '0420' ) )
				LEFT OUTER JOIN dsps_revs ON 
				( dsps.personne_id = dsps_revs.personne_id 
				AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					) 
				)
				LEFT OUTER JOIN detailsaccosocindis_revs detailsaccosocindis_revs ON ( dsps_revs.id = detailsaccosocindis_revs.dsp_rev_id AND detailsaccosocindis_revs.nataccosocindi IN ( '0420' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND detailsaccosocindis.nataccosocindi IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND detailsaccosocindis_revs.nataccosocindi IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
			;


		-- suivi_durant_periode_dd : suivi_periode + DD_periode
		-- DROP TABLE suivi_durant_periode_dd_1;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_1 AS
			SELECT DISTINCT 
				personnes.id AS personne_id, 
				personnes.foyer_id,
				personnes.nom AS "Nom", 
				personnes.prenom AS "Prenom", 
				CASE 
					WHEN personnes.sexe='1' then 'homme' 
					WHEN personnes.sexe='2' then 'femme' end as "Sexe", 
				personnes.dtnai AS "Date de naissance",
				suivi_durant_periode.rdv_id,
				suivi_durant_periode.daterdv,
				suivi_durant_periode.referent_id,
				structuresreferentes.id AS id, 
				pdv_format_export.pdv,
				pdv_format_export.plaine_co
				
			FROM personnes
				-- D1 durant la periode traité
				INNER JOIN suivi_durant_periode ON (suivi_durant_periode.personne_id = personnes.id)
				INNER JOIN questionnairesd1pdvs93 ON (questionnairesd1pdvs93.id=suivi_durant_periode.id)
				INNER JOIN situationsallocataires ON (questionnairesd1pdvs93.situationallocataire_id=situationsallocataires.id)
				INNER JOIN rendezvous ON (rendezvous.id = suivi_durant_periode.rendezvous_id )
				-- structure référente D1
				LEFT OUTER JOIN structuresreferentes ON (rendezvous.structurereferente_id = structuresreferentes.id)
				LEFT OUTER JOIN administration.dec_pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
				LEFT OUTER JOIN dd_durant_periode ON (dd_durant_periode.personne_id=personnes.id)
				
			WHERE 
				--Soit DD dans histo droit
				dd_durant_periode.personne_id IS NOT NULL
				OR
				-- Soit DD dans D1
				situationsallocataires.toppersdrodevorsa='1'
		;

		
		-- DROP TABLE suivi_durant_periode_dd_1bis;
		--SELECT * FROM suivi_durant_periode_dd_1bis;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_1bis AS
		(
			SELECT DISTINCT suivi_durant_periode_dd_1.*,
				/*adresses.numvoie AS "Numero de voie",
				adresses.libtypevoie AS "Type de voie",
				adresses.nomvoie AS "Nom de voie", 
				adresses.codepos AS "CP",
				adresses.nomcom,
				case 
				when adresses.nomcom='AUBERVILLIERS' THEN 'AUBERVILLIERS'
				when adresses.nomcom='AULNAY SOUS BOIS' THEN 'AULNAY SOUS BOIS'
				when adresses.nomcom='BAGNOLET' THEN 'BAGNOLET'
				when adresses.nomcom IN ('BOBIGNY', 'BOBIGNY CEDEX') THEN 'BOBIGNY'
				when adresses.nomcom='BONDY' THEN 'BONDY'
				when adresses.nomcom='CLICHY SOUS BOIS' THEN 'CLICHY SOUS BOIS'
				when adresses.nomcom='COUBRON' THEN 'COUBRON'
				when adresses.nomcom IN ('DRANCY','DRANCY CEDEX') THEN 'DRANCY'
				when adresses.nomcom='DUGNY' THEN 'DUGNY'
				when adresses.nomcom='EPINAY SUR SEINE' THEN 'EPINAY SUR SEINE'
				when adresses.nomcom IN ('GAGNY','GAGNY CEDEX') THEN 'GAGNY'
				when adresses.nomcom='GOURNAY SUR MARNE' THEN 'GOURNAY SUR MARNE'
				when adresses.nomcom IN ('L ILE ST DENIS','ILE ST DENIS') THEN 'L ILE ST DENIS'
				when adresses.nomcom='LA COURNEUVE' THEN 'LA COURNEUVE'
				when adresses.nomcom IN ('LA PLAINE ST DENIS','SAINT DENIS','ST DENIS','ST DENIS CEDEX','LA PLAINE ST DENIS CEDEX') THEN 'SAINT DENIS'
				when adresses.nomcom='LE BLANC MESNIL' THEN 'LE BLANC MESNIL'
				when adresses.nomcom='LE BOURGET' THEN 'LE BOURGET'
				when adresses.nomcom='LE PRE ST GERVAIS' THEN 'LE PRE ST GERVAIS'
				when adresses.nomcom='LE RAINCY' THEN 'LE RAINCY'
				when adresses.nomcom='LES LILAS' THEN 'LES LILAS'
				when adresses.nomcom IN ('LES PAVILLONS SOUS BOIS','PAVILLONS SOUS BOIS') THEN 'LES PAVILLONS SOUS BOIS'
				when adresses.nomcom='LIVRY GARGAN' THEN 'LIVRY GARGAN'
				when adresses.nomcom='MONTFERMEIL' THEN 'MONFERMEIL'
				when adresses.nomcom IN ('MONTREUIL','MONTREUIL CEDEX') THEN 'MONTREUIL'
				when adresses.nomcom='NEUILLY PLAISANCE' THEN 'NEUILLY PLAISANCE'
				when adresses.nomcom='NEUILLY SUR MARNE' THEN 'NEUILLY SUR MARNE'
				when adresses.nomcom='NOISY LE GRAND' THEN 'NOISY LE GRAND'
				when adresses.nomcom='NOISY LE GRAND CEDEX' THEN 'NOISY LE GRAND'
				when adresses.nomcom='NOISY LE SEC' THEN 'NOISY LE SEC'
				when adresses.nomcom='PANTIN' THEN 'PANTIN'
				when adresses.nomcom IN ('PIERREFITTE','PIERREFITTE SUR SEINE') THEN 'PIERREFITTE SUR SEINE'
				when adresses.nomcom='ROMAINVILLE' THEN 'ROMAINVILLE'
				when adresses.nomcom IN ('ROSNY SOUS BOIS','ROSNY SOUS BOIS CEDEX') THEN 'ROSNY SOUS BOIS'
				when adresses.nomcom IN ('SAINT OUEN','ST OUEN') THEN 'SAINT OUEN'
				when adresses.nomcom='SEVRAN' THEN 'SEVRAN'
				when adresses.nomcom='STAINS' THEN 'STAINS'
				when adresses.nomcom='TREMBLAY EN FRANCE' THEN 'TREMBLAY EN FRANCE'
				when adresses.nomcom='TREMBLAY EN FRANCE CEDEX' THEN 'TREMBLAY EN FRANCE'
				when adresses.nomcom='VAUJOURS' THEN 'VAUJOURS'
				when adresses.nomcom='VILLEMOMBLE' THEN 'VILLEMOMBLE'
				when adresses.nomcom='VILLEPINTE' THEN 'VILLEPINTE'
				when adresses.nomcom='VILLETANEUSE' THEN 'VILLETANEUSE'
				when (adresses.codepos < '93000' or adresses.codepos >= '94000') THEN 'Z_Hors_93'
				END AS "Ville domicile", */
				dossiers.matricule AS "Matricule CAF", 
				dossiers.dtdemrsa AS "Date demande RSA", 
				dossiers.dtdemrmi AS "Date demande RMI",
				
				-- Situation familiale (MAJ après réunion site pilote)
				CASE 
					WHEN foyers.sitfam='ABA' then 'DISPARU (JUGEMENT D ABSENCE)'
					WHEN foyers.sitfam='CEL' then 'CELIBATAIRE'
					WHEN foyers.sitfam='DIV' then 'DIVORCE'
					WHEN foyers.sitfam='ISO' then 'ISOLEMENT APRES VIE MARITALE OU PACS'
					WHEN foyers.sitfam='MAR' then 'MARIAGE'
					WHEN foyers.sitfam='PAC' then 'PACS'
					WHEN foyers.sitfam='RPA' then 'REPRISE VIE COMMUNE SUR PACS'
					WHEN foyers.sitfam='RVC' then 'REPRISE VIE MARITALE'
					WHEN foyers.sitfam='RVM' then 'REPRISE MARIAGE'
					WHEN foyers.sitfam='SEF' then 'SEPARATION DE FAIT'
					WHEN foyers.sitfam='SEL' then 'SEPARATION LEGALE'
					WHEN foyers.sitfam='VEU' then 'VEUVAGE'
					WHEN foyers.sitfam='VIM' then 'VIE MARITALE'
					end as "Situation familiale",
				foyers.ddsitfam AS "Ds cette situation familiale depuis le",
				
				-- Nb d'enfants à charge (MAJ après réunion site pilote)
				detailsdroitsrsa.nbenfautcha AS "Nb enf et autre personnes à charge"
			FROM
			suivi_durant_periode_dd_1
			INNER JOIN personnes ON (personnes.id=suivi_durant_periode_dd_1.personne_id)
			LEFT OUTER JOIN foyers ON (personnes.foyer_id = foyers.id)
			LEFT OUTER JOIN dossiers ON (foyers.dossier_id = dossiers.id)
			LEFT OUTER JOIN detailsdroitsrsa ON (detailsdroitsrsa.dossier_id = dossiers.id) -- MAJ
			
			/*-- Adresses (demande PDV annulée suite à la r° site pilote du 16/11/2015)
			LEFT OUTER JOIN adressesfoyers ON (adressesfoyers.foyer_id = foyers.id AND adressesfoyers.rgadr ='01') -- MAJ
			LEFT OUTER JOIN adresses ON (adressesfoyers.adresse_id = adresses.id) -- MAJ*/
		)
		;
		
		
		-- DROP TABLE suivi_durant_periode_dd_2;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_2 AS
		(
			SELECT DISTINCT suivi_durant_periode_dd_1bis.*,		
			-- Infos sur le référent (MAJ après réunion site pilote)
			--referents.id AS referent_id,
			referents.qual AS "Civilite referent", 
			referents.nom AS "Nom referent", 
			referents.prenom AS "Prenom referent", 
			referents.fonction AS "Fonction referent"
			--personnes_referents.dddesignation AS "Date debut designation referent", 
			--personnes_referents.dfdesignation AS "Date fin designation referent"
			FROM
			suivi_durant_periode_dd_1bis
			-- avec infos sur le dernier référent de parcours
			LEFT OUTER JOIN referents ON (suivi_durant_periode_dd_1bis.referent_id = referents.id)--MAJ
		)
		;


		-- DROP TABLE suivi_durant_periode_dd_3;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_3 AS
		(
			SELECT DISTINCT suivi_durant_periode_dd_2.*,
			-- Date du 1er RDV en N-1 (MAJ après réunion site pilote)
			premier_rdv_indiv_honore_annee_prec.daterdv AS "Date 1er RDV indiv honore N-1",
			-- Date du 1er RDV en N (MAJ après réunion site pilote)
			premier_rdv_indiv_honore_periode.daterdv AS "Date 1er RDV indiv honore N",
			-- Date du tout 1er RDV (MAJ après réunion site pilote)
			tout_premier_rdv_indiv_honore.daterdv AS "Date tout 1er RDV indiv honore"
			
			FROM 
			suivi_durant_periode_dd_2
			-- avec infos sur les rdvs
			LEFT OUTER JOIN premier_rdv_indiv_honore_annee_prec ON (premier_rdv_indiv_honore_annee_prec.personne_id=suivi_durant_periode_dd_2.personne_id)-- MAJ
			LEFT OUTER JOIN premier_rdv_indiv_honore_periode ON (premier_rdv_indiv_honore_periode.personne_id=suivi_durant_periode_dd_2.personne_id)-- MAJ
			LEFT OUTER JOIN tout_premier_rdv_indiv_honore ON (tout_premier_rdv_indiv_honore.personne_id=suivi_durant_periode_dd_2.personne_id)-- MAJ
		)
		;
		
		
		-- DROP TABLE suivi_durant_periode_dd_4;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_4 AS
		(
			SELECT DISTINCT suivi_durant_periode_dd_3.*,
			-- Infos sur le dernier CER validé en N (MAJ après réunion site pilote)
			dernier_cer_valide_N.dd_ci AS "Date debut dernier CER valide en N", 
			dernier_cer_valide_N.df_ci AS "Date fin dernier CER valide en N",
			dernier_cer_valide_N.duree AS "Duree dernier CER valide en N", 
			dernier_cer_valide_N.datesignature AS "Date signature dernier CER valide en N", 
			dernier_cer_valide_N.datevalidation_ci AS "Date validation du dernier CER valide en N", 
			dernier_cer_valide_N.sujetscers93_autres AS "Sujet_autres dernier CER valide en N",
			dernier_cer_valide_N.sujetscers93_formation AS "Sujet_formation dernier CER valide en N",
			dernier_cer_valide_N.sujetscers93_sante AS "Sujet_sante dernier CER valide en N",
			dernier_cer_valide_N.sujetscers93_autonomie_sociale AS "Sujet_aut_soc dernier CER valide en N",
			dernier_cer_valide_N.sujetscers93_logement AS "Sujet_logement dernier CER valide en N",
			dernier_cer_valide_N.sujetscers93_emploi AS "Sujet_emploi dernier CER valide en N"
			FROM suivi_durant_periode_dd_3
			-- Infos sur
			LEFT OUTER JOIN dernier_cer_valide_N ON ( dernier_cer_valide_N.personne_id = suivi_durant_periode_dd_3.personne_id)
		)
		;

		
		-- DROP TABLE suivi_durant_periode_dd_5a;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_5a AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_4.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_sante."difficultes_exprimees" AS "Difficulte sante?"
			FROM suivi_durant_periode_dd_4
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_sante ON (difficultes_sante.personne_id=suivi_durant_periode_dd_4.personne_id)
		)
		;
		
		
		-- DROP TABLE suivi_durant_periode_dd_5b;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_5b AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5a.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_logement."difficultes_exprimees" AS "Difficulte logement?"
			FROM suivi_durant_periode_dd_5a
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_logement ON (difficultes_logement.personne_id=suivi_durant_periode_dd_5a.personne_id)
		)
		;

		
		-- DROP TABLE suivi_durant_periode_dd_5c;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_5c AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5b.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_familial."difficultes_exprimees" AS "Difficulte familiale?"
			FROM suivi_durant_periode_dd_5b
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_familial ON (difficultes_familial.personne_id=suivi_durant_periode_dd_5b.personne_id)
		)
		;
		
			
		-- DROP TABLE suivi_durant_periode_dd_5d;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_5d AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5c.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_mode_garde."difficultes_exprimees" AS "Difficulte mode de garde?"
			FROM suivi_durant_periode_dd_5c
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_mode_garde ON (difficultes_mode_garde.personne_id=suivi_durant_periode_dd_5c.personne_id)
		)
		;

			
		-- DROP TABLE suivi_durant_periode_dd_5e;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_5e AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5d.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_surendettement."difficultes_exprimees" AS "Difficulte surrendettement?"
			FROM suivi_durant_periode_dd_5d
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_surendettement ON (difficultes_surendettement.personne_id=suivi_durant_periode_dd_5d.personne_id)
		)
		;
		
			
		-- DROP TABLE suivi_durant_periode_dd_5f;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_5f AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5e.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_administratives."difficultes_exprimees" AS "Difficulte administrative?"
			FROM suivi_durant_periode_dd_5e
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_administratives ON (difficultes_administratives.personne_id=suivi_durant_periode_dd_5e.personne_id)
		)
		;

			
		-- DROP TABLE suivi_durant_periode_dd_5g;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_5g AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5f.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_linguistiques."difficultes_exprimees" AS "Difficulte linguistique?"
			FROM suivi_durant_periode_dd_5f
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_linguistiques ON (difficultes_linguistiques.personne_id=suivi_durant_periode_dd_5f.personne_id)
		)
		;
		
			
		-- DROP TABLE suivi_durant_periode_dd_5h;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_5h AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5g.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_qualification_pro."difficultes_exprimees" AS "Difficulte qualification professionnelle?"
			FROM suivi_durant_periode_dd_5g
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_qualification_pro ON (difficultes_qualification_pro.personne_id=suivi_durant_periode_dd_5g.personne_id)
		)
		;

			
		-- DROP TABLE suivi_durant_periode_dd_5i;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_5i AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5h.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_acces_emploi."difficultes_exprimees" AS "Difficulte acces emploi?"
			FROM suivi_durant_periode_dd_5h
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_acces_emploi ON (difficultes_acces_emploi.personne_id=suivi_durant_periode_dd_5h.personne_id)
		)
		;
		
			
		-- DROP TABLE suivi_durant_periode_dd_5j;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_5j AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5i.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_autres."difficultes_exprimees" AS "Autres difficultes?"
			FROM suivi_durant_periode_dd_5i
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_autres ON (difficultes_autres.personne_id=suivi_durant_periode_dd_5i.personne_id)
		)
		;


		-- DROP TABLE suivi_durant_periode_dd_6;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_6 AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5j.*,
			questionnairesd1pdvs93.id AS questionnairesd1pdvs93_id,
			-- Tous les champs du questionnaire D1 (MAJ après réunion site pilote)
			questionnairesd1pdvs93.marche_travail AS "D1_Ligne2_Marche de l''emploi",
			CASE 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtnai ) ) BETWEEN 0 AND 14 THEN '0_14' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtnai ) ) BETWEEN 15 AND 24 THEN '15_24' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtnai ) ) BETWEEN 25 AND 44 THEN '25_44' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtnai ) ) BETWEEN 45 AND 54 THEN '45_54' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtnai ) ) BETWEEN 55 AND 64 THEN '55_64' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtnai ) ) BETWEEN 65 AND 999 THEN '65_999' 
				ELSE 'NC' 
			END AS "D1_Ligne3_Tranche d'age", 
				 
			questionnairesd1pdvs93.vulnerable AS "D1_Ligne4_Groupes vulnerables", 
				
			CASE
				WHEN questionnairesd1pdvs93.nivetu='1201' THEN 'Niveau_I/II_Enseignement_superieur'
				WHEN questionnairesd1pdvs93.nivetu='1202' THEN 'Niveau_III_Bac+2'
				WHEN questionnairesd1pdvs93.nivetu='1203' THEN 'Niveau_IV_Bac/Equivalent'
				WHEN questionnairesd1pdvs93.nivetu='1204' THEN 'Niveau_V_CAP/BEP'
				WHEN questionnairesd1pdvs93.nivetu='1205' THEN 'Niveau_Vbis_Fin_scolarite_obligatoire'
				WHEN questionnairesd1pdvs93.nivetu='1206' THEN 'Niveau_VI_Pas_de_niveau'
				WHEN questionnairesd1pdvs93.nivetu='1207' THEN 'Niveau_VII_Jamais_scolarise'
			END AS "D1_Ligne5_Niveau d'instruction",
					
			questionnairesd1pdvs93.categorie_sociopro AS "D1_Ligne6_Professions et CSP",
				   
			questionnairesd1pdvs93.autre_caracteristique AS "D1_Ligne7_Autres caracteristiques", 
				
			CASE 
				WHEN ( situationsallocataires.natpf_socle = '1' AND situationsallocataires.natpf_activite = '1' AND situationsallocataires.natpf_majore = '1' ) THEN 'majore' 
				WHEN ( situationsallocataires.natpf_socle = '1' AND situationsallocataires.natpf_activite = '1' AND situationsallocataires.natpf_majore = '0' ) THEN 'socle_activite' 
				WHEN ( situationsallocataires.natpf_socle = '1' AND situationsallocataires.natpf_activite = '0' AND situationsallocataires.natpf_majore = '1' ) THEN 'majore' 
				WHEN ( situationsallocataires.natpf_socle = '1' AND situationsallocataires.natpf_activite = '0' AND situationsallocataires.natpf_majore = '0' ) THEN 'socle' 
				WHEN ( situationsallocataires.natpf_socle = '0' AND situationsallocataires.natpf_activite = '1' AND situationsallocataires.natpf_majore = '1' ) THEN 'majore' 
				WHEN ( situationsallocataires.natpf_socle = '0' AND situationsallocataires.natpf_activite = '1' AND situationsallocataires.natpf_majore = '0' ) THEN 'NC' 
				WHEN ( situationsallocataires.natpf_socle = '0' AND situationsallocataires.natpf_activite = '0' AND situationsallocataires.natpf_majore = '1' ) THEN 'NC' 
				WHEN ( situationsallocataires.natpf_socle = '0' AND situationsallocataires.natpf_activite = '0' AND situationsallocataires.natpf_majore = '0' ) THEN 'NC' 
				ELSE 'NC' 
			END AS "D1_Ligne8_Type de beneficiaires", 
				  
			CASE 
				WHEN situationsallocataires.nati='F' THEN 'Francaise' 
				WHEN situationsallocataires.nati='C' THEN 'Union_Europeenne' 
				WHEN situationsallocataires.nati='A' THEN 'Hors_Union_Europeenne'
				ELSE 'NC' 
			END AS "D1_Ligne9_Nationalite", 
				  
			CASE 
				WHEN situationsallocataires.sitfam IN ('CEL', 'DIV', 'ISO', 'SEF', 'SEL', 'VEU') AND situationsallocataires.nbenfants = 0 THEN 'Isole_sans_enfant' 
				WHEN situationsallocataires.sitfam IN ('CEL', 'DIV', 'ISO', 'SEF', 'SEL', 'VEU') AND situationsallocataires.nbenfants > 0 THEN 'Isole_avec_enfant' 
				WHEN situationsallocataires.sitfam IN ('MAR', 'PAC', 'RPA', 'RVC', 'RVM', 'VIM') AND situationsallocataires.nbenfants = 0 THEN 'En_couple_sans_enfant' 
				WHEN situationsallocataires.sitfam IN ('MAR', 'PAC', 'RPA', 'RVC', 'RVM', 'VIM') AND situationsallocataires.nbenfants > 0 THEN 'En_couple_avec_enfant' 
				ELSE 'NC' 
			END AS "D1_Ligne10_Situation familiale",
				  
			questionnairesd1pdvs93.conditions_logement AS "D1_Ligne11_Condition de logement", 
			
			CASE
				WHEN questionnairesd1pdvs93.inscritpe='1' THEN 'Inscrits'
				WHEN questionnairesd1pdvs93.inscritpe='0' THEN 'Non_inscrits'
				ELSE 'NC' 
			END AS "D1_Ligne12_Inscription Pole Emploi",
				 
			CASE 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtdemrsa ) ) BETWEEN 0 AND 0 THEN '0_0' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtdemrsa ) ) BETWEEN 1 AND 2 THEN '1_2' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtdemrsa ) ) BETWEEN 3 AND 5 THEN '3_5' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtdemrsa ) ) BETWEEN 6 AND 8 THEN '6_8' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtdemrsa ) ) BETWEEN 9 AND 999 THEN '9_999' 
				ELSE 'NC' 
			END AS "D1_Ligne13_Anciennete ds le dispositif (en annees)",
				  
			CASE WHEN questionnairesd1pdvs93.nivetu='1207' THEN 'jamais_scolarise' END AS "D1_Ligne14_Non scolarise",
				  
			CASE WHEN questionnairesd1pdvs93.diplomes_etrangers='1' THEN 'oui' END AS "D1_Ligne15_Diplomes etrangers non reconnus en France"

			FROM
			questionnairesd1pdvs93
			INNER JOIN situationsallocataires ON (questionnairesd1pdvs93.situationallocataire_id=situationsallocataires.id)
			INNER JOIN suivi_durant_periode_dd_5j  ON (questionnairesd1pdvs93.personne_id=suivi_durant_periode_dd_5j.personne_id )

			WHERE
			questionnairesd1pdvs93.id IN (SELECT DISTINCT id FROM suivi_durant_periode)
			AND suivi_durant_periode_dd_5j.rdv_id =questionnairesd1pdvs93.rendezvous_id
			ORDER BY personne_id, "Nom", "Prenom"
		)
		;

		
		--DROP TABLE suivi_durant_periode_dd_7;
		CREATE TEMPORARY TABLE suivi_durant_periode_dd_7 AS
		(
			SELECT DISTINCT suivi_durant_periode_dd_6.*,
				-- Tous les champs du questionnaire D2 (MAJ après réunion site pilote)
				questionnairesd2pdvs93.situationaccompagnement AS "D2_Situation accompagnement",
				sortiesaccompagnementsd2pdvs93."name" AS "D2_Type de sortie accompagnement",
				questionnairesd2pdvs93.chgmentsituationadmin AS "D2_Type de changement de situation"
		
			FROM 
			suivi_durant_periode_dd_6
			-- avec ou sans infos D2
			LEFT OUTER JOIN questionnairesd2pdvs93 ON (questionnairesd2pdvs93.questionnaired1pdv93_id=suivi_durant_periode_dd_6.questionnairesd1pdvs93_id)
			LEFT OUTER JOIN sortiesaccompagnementsd2pdvs93 ON (questionnairesd2pdvs93.sortieaccompagnementd2pdv93_id = sortiesaccompagnementsd2pdvs93.id)-- MAJ
		)
		;
		
		
		-- recond_durant_periode : suivi_durant_periode_dd + suivis_annee_prec_dd
		CREATE TEMPORARY TABLE recond_durant_periode AS
			SELECT DISTINCT suivi_durant_periode_dd_7.*,
				CASE WHEN suivi_durant_periode_dd_7.personne_id IS NOT NULL THEN 'Reconduit' 
				END AS suivi	
			FROM suivi_durant_periode_dd_7 
				INNER JOIN suivis_annee_prec_dd ON (suivi_durant_periode_dd_7.personne_id=suivis_annee_prec_dd.personne_id)
		;
		
		
		-- nouv_durant_periode : suivi_durant_periode_dd + NOT suivis_annee_prec_dd
		CREATE TEMPORARY TABLE nouv_durant_periode AS
			SELECT DISTINCT suivi_durant_periode_dd_7.*,
				CASE WHEN suivi_durant_periode_dd_7.personne_id IS NOT NULL THEN 'Nouveau' 
				END AS suivi	
			FROM suivi_durant_periode_dd_7 
			WHERE personne_id NOT IN ( 
				SELECT suivis_annee_prec_dd.personne_id 
				FROM suivis_annee_prec_dd 
			)
		;
		
		
		-- suivis_pdv_durant_periode : Reconduits_periode + Nouveau_periode
		CREATE TEMPORARY TABLE suivis_pdv_durant_periode AS
			SELECT DISTINCT * 
			FROM recond_durant_periode 
			UNION
			SELECT DISTINCT *  
			FROM nouv_durant_periode
		;
		

		-- strucref_type_b1b2 : types de structure referente
		CREATE TEMPORARY TABLE strucref_type_b1b2 AS
		(
			SELECT DISTINCT *, 'Le Pôle Emploi' AS type_struc_orient_b1b2
			FROM structuresreferentes
			WHERE (structuresreferentes.lib_struc LIKE 'Pole Emploi%' OR structuresreferentes.lib_struc LIKE 'Pôle Emploi%')
		)
		UNION
		(
			SELECT DISTINCT * , 'Le Service Social' AS type_struc_orient_b1b2
			FROM structuresreferentes
			WHERE (structuresreferentes.lib_struc LIKE 'Service Social%')
		)
		UNION
		(
			SELECT DISTINCT * , 'Une Asso conventionnée' as type_struc_orient_b1b2
			FROM structuresreferentes
			WHERE (structuresreferentes.lib_struc IS NOT NULL
					AND structuresreferentes.lib_struc NOT LIKE 'Service Social%'
					AND structuresreferentes.lib_struc NOT LIKE 'Pole Emploi%'
					AND structuresreferentes.lib_struc NOT LIKE 'Pôle Emploi%'
					AND structuresreferentes.lib_struc NOT LIKE '%Projet de Ville%'
					AND structuresreferentes.lib_struc NOT LIKE 'Centre Communal d Action Sociale Le Raincy')
		)
		UNION
		(
			SELECT DISTINCT * , 'Un PDV' as type_struc_orient_b1b2
			FROM structuresreferentes
			WHERE (structuresreferentes.lib_struc IS NOT NULL
				AND structuresreferentes.lib_struc LIKE '%Projet de Ville%'
			)
		)
		;


		-- partie_3 : suivis_pdv_periode AVEC : RAPATRIER LA DERNIERE ORIENTATION et LA CODER SELON LA TYPO B1/B2 (PARTIE 3.B);
		CREATE TEMPORARY TABLE partie_3 AS
			SELECT DISTINCT 
			suivis_pdv_durant_periode.personne_id,
			suivis_pdv_durant_periode.foyer_id,
			suivis_pdv_durant_periode."Nom",
			suivis_pdv_durant_periode."Prenom",
			suivis_pdv_durant_periode."Sexe",
			suivis_pdv_durant_periode."Date de naissance", 
			suivis_pdv_durant_periode."Matricule CAF",
			suivis_pdv_durant_periode."Date demande RSA", 
			suivis_pdv_durant_periode."Date demande RMI", 
			suivis_pdv_durant_periode."Situation familiale", 
			suivis_pdv_durant_periode."Ds cette situation familiale depuis le", 
			suivis_pdv_durant_periode."Nb enf et autre personnes à charge",
			suivis_pdv_durant_periode."Civilite referent", 
			suivis_pdv_durant_periode."Nom referent", 
			suivis_pdv_durant_periode."Prenom referent", 
			suivis_pdv_durant_periode."Fonction referent", 
			suivis_pdv_durant_periode."Date 1er RDV indiv honore N-1", 
			suivis_pdv_durant_periode."Date 1er RDV indiv honore N", 
			suivis_pdv_durant_periode."Date tout 1er RDV indiv honore",
			suivis_pdv_durant_periode."Date debut dernier CER valide en N",
			suivis_pdv_durant_periode."Date fin dernier CER valide en N", 
			suivis_pdv_durant_periode."Duree dernier CER valide en N", 
			suivis_pdv_durant_periode."Date signature dernier CER valide en N", 
			suivis_pdv_durant_periode."Date validation du dernier CER valide en N", 
			suivis_pdv_durant_periode."Sujet_formation dernier CER valide en N", 
			suivis_pdv_durant_periode."Sujet_sante dernier CER valide en N",
			suivis_pdv_durant_periode."Sujet_aut_soc dernier CER valide en N", 
			suivis_pdv_durant_periode."Sujet_logement dernier CER valide en N", 
			suivis_pdv_durant_periode."Sujet_emploi dernier CER valide en N",
			suivis_pdv_durant_periode."Sujet_autres dernier CER valide en N",
			suivis_pdv_durant_periode."Difficulte sante?", 
			suivis_pdv_durant_periode."Difficulte logement?",
			suivis_pdv_durant_periode."Difficulte familiale?", 
			suivis_pdv_durant_periode."Difficulte mode de garde?", 
			suivis_pdv_durant_periode."Difficulte surrendettement?", 
			suivis_pdv_durant_periode."Difficulte administrative?", 
			suivis_pdv_durant_periode."Difficulte linguistique?", 
			suivis_pdv_durant_periode."Difficulte qualification professionnelle?", 
			suivis_pdv_durant_periode."Difficulte acces emploi?", 
			suivis_pdv_durant_periode."Autres difficultes?",
			suivis_pdv_durant_periode.daterdv AS "Date de RDV D1", 
			suivis_pdv_durant_periode.id,
			suivis_pdv_durant_periode.pdv AS "PDV de D1",  
			suivis_pdv_durant_periode.plaine_co,
			suivis_pdv_durant_periode."D1_Ligne2_Marche de l''emploi",
			suivis_pdv_durant_periode."D1_Ligne3_Tranche d'age", 
			suivis_pdv_durant_periode."D1_Ligne4_Groupes vulnerables", 
			suivis_pdv_durant_periode."D1_Ligne5_Niveau d'instruction",
			suivis_pdv_durant_periode."D1_Ligne6_Professions et CSP", 
			suivis_pdv_durant_periode."D1_Ligne7_Autres caracteristiques",
			suivis_pdv_durant_periode."D1_Ligne8_Type de beneficiaires", 
			suivis_pdv_durant_periode."D1_Ligne9_Nationalite", 
			suivis_pdv_durant_periode."D1_Ligne10_Situation familiale", 
			suivis_pdv_durant_periode."D1_Ligne11_Condition de logement", 
			suivis_pdv_durant_periode."D1_Ligne12_Inscription Pole Emploi",
			suivis_pdv_durant_periode."D1_Ligne13_Anciennete ds le dispositif (en annees)", 
			suivis_pdv_durant_periode."D1_Ligne14_Non scolarise", 
			suivis_pdv_durant_periode."D1_Ligne15_Diplomes etrangers non reconnus en France", 
			suivis_pdv_durant_periode."D2_Situation accompagnement",
			suivis_pdv_durant_periode."D2_Type de sortie accompagnement", 
			suivis_pdv_durant_periode."D2_Type de changement de situation", 
			suivi AS "Type de suivi",
			der_valid_orient_av_fin_periode.date_valid AS "Date de validation orientation",--date_valid_orient,
			CASE 
				WHEN (suivis_pdv_durant_periode.id!=der_valid_orient_av_fin_periode.structurereferente_id AND type_struc_orient_b1b2='Un PDV') THEN 'Un autre PDV'
				WHEN suivis_pdv_durant_periode.id!=der_valid_orient_av_fin_periode.structurereferente_id THEN type_struc_orient_b1b2
				WHEN suivis_pdv_durant_periode.id=der_valid_orient_av_fin_periode.structurereferente_id THEN 'Le PDV'
				WHEN der_valid_orient_av_fin_periode.structurereferente_id IS NULL THEN 'Sans Orientation'
			END AS "Service referent (orientation)" --service_ref_orient
			FROM suivis_pdv_durant_periode
				LEFT OUTER JOIN der_valid_orient_av_fin_periode ON (suivis_pdv_durant_periode.personne_id=der_valid_orient_av_fin_periode.personne_id)
				LEFT OUTER JOIN strucref_type_b1b2 ON (strucref_type_b1b2.id=der_valid_orient_av_fin_periode.structurereferente_id)
			ORDER BY "PDV de D1", "Service referent (orientation)"
		;



		/* ***********************************************
		******* LISTE DES TABLES TEMP partie 4 ************
		*************************************************/

		-- rdv_ind_honore_durant_periode : RDV individuel + honore + durant periode 
		CREATE TEMPORARY TABLE rdv_ind_honore_durant_periode AS
			SELECT DISTINCT rendezvous.*
			FROM rendezvous 
				INNER JOIN typesrdv ON (rendezvous.typerdv_id = typesrdv.id)
				INNER JOIN statutsrdvs ON (rendezvous.statutrdv_id = statutsrdvs.id)
			WHERE EXTRACT (YEAR FROM rendezvous.daterdv)=annee_periode
				AND EXTRACT (MONTH FROM rendezvous.daterdv) BETWEEN 1 AND nb_mois
				AND typesrdv.libelle='Individuel' 
				AND statutsrdvs.libelle= 'honoré' 
		;

		-- nb_rdv_par_personne : suivi_periode + DD_periode + rdv_ind_honore_periode + (SR D1==SR RDV)
		CREATE TEMPORARY TABLE nb_rdv_par_personne AS 
			SELECT personnes.id AS personne_id,
				structuresreferentes.id,pdv_format_export.pdv, pdv_format_export.plaine_co,
				COUNT( DISTINCT( rdv_ind_honore_durant_periode.id ) ) AS "count rdv_id"
			FROM personnes
				INNER JOIN suivi_durant_periode ON (suivi_durant_periode.personne_id = personnes.id)
				INNER JOIN questionnairesd1pdvs93 ON (questionnairesd1pdvs93.id=suivi_durant_periode.id)
				INNER JOIN situationsallocataires ON (questionnairesd1pdvs93.situationallocataire_id=situationsallocataires.id)
				INNER JOIN rendezvous ON (rendezvous.id = suivi_durant_periode.rendezvous_id )
				LEFT OUTER JOIN foyers ON (personnes.foyer_id = foyers.id)
				LEFT OUTER JOIN dossiers ON (foyers.dossier_id = dossiers.id)
				-- structure référente du RDV_D1
				LEFT OUTER JOIN structuresreferentes ON (rendezvous.structurereferente_id = structuresreferentes.id)
				LEFT OUTER JOIN administration.dec_pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
				LEFT OUTER JOIN dd_durant_periode ON (dd_durant_periode.personne_id=personnes.id)
				INNER JOIN rdv_ind_honore_durant_periode ON (rdv_ind_honore_durant_periode.personne_id = personnes.id)
			WHERE 	-- SR D1 = SR RDV (?)
				rdv_ind_honore_durant_periode.structurereferente_id=structuresreferentes.id
				AND (
					--Soit DD dans histo droit
					dd_durant_periode.personne_id IS NOT NULL
					OR
					-- Soit DD dans D1
					situationsallocataires.toppersdrodevorsa='1'
				)
			GROUP BY personnes.id,structuresreferentes.id,pdv, pdv_format_export.plaine_co;
		
		-- nb_psn_rdv_periode_d1 : 
		CREATE TEMPORARY TABLE nb_psn_rdv_periode_d1 AS
		(
			SELECT 	DISTINCT id, pdv,plaine_co,
				CASE 
					WHEN "count rdv_id" BETWEEN 1 AND 3 THEN '1 à 3 RDV honorés' 
					WHEN "count rdv_id" BETWEEN 4 AND 6 THEN '4 à 6 RDV honorés' 
					WHEN "count rdv_id" >= 7 THEN  '7 et + RDV honorés' 
					END AS "Nb de RDV honorés",
				COUNT(*) as "Nb personnes"
			FROM 
				nb_rdv_par_personne
			GROUP BY  id, pdv,plaine_co,"Nb de RDV honorés"
		)
		UNION 
		(
			SELECT DISTINCT id, pdv,plaine_co, 'Total'  AS "Nb de RDV honorés", COUNT(*) AS "Nb personnes"
			FROM 
			(
				SELECT 	personnes.id AS personne_id,
					structuresreferentes.id, pdv,plaine_co,
					COUNT( DISTINCT( rdv_ind_honore_durant_periode.id ) ) AS "count rdv_id"
				FROM personnes
					INNER JOIN suivi_durant_periode ON (suivi_durant_periode.personne_id = personnes.id)
					INNER JOIN questionnairesd1pdvs93 ON (questionnairesd1pdvs93.id=suivi_durant_periode.id)
					INNER JOIN situationsallocataires ON (questionnairesd1pdvs93.situationallocataire_id=situationsallocataires.id)
					INNER JOIN rendezvous ON (rendezvous.id = suivi_durant_periode.rendezvous_id )
					LEFT OUTER JOIN foyers ON (personnes.foyer_id = foyers.id)
					LEFT OUTER JOIN dossiers ON (foyers.dossier_id = dossiers.id)
					-- structure référente du RDV_D1
					LEFT OUTER JOIN structuresreferentes ON (rendezvous.structurereferente_id = structuresreferentes.id)
					LEFT OUTER JOIN administration.dec_pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
					LEFT OUTER JOIN dd_durant_periode ON (dd_durant_periode.personne_id=personnes.id)
					INNER JOIN rdv_ind_honore_durant_periode ON (rdv_ind_honore_durant_periode.personne_id = personnes.id)
				WHERE 	-- SR D1 = SR RDV 
					rdv_ind_honore_durant_periode.structurereferente_id=structuresreferentes.id
					AND (
					--Soit DD dans histo droit
					dd_durant_periode.personne_id IS NOT NULL
					OR
					-- Soit DD dans D1
					situationsallocataires.toppersdrodevorsa='1'
				)
				GROUP BY personnes.id,structuresreferentes.id,pdv, plaine_co
			) AS nb_rdv_par_personne
		GROUP BY id, pdv, plaine_co, "Nb de RDV honorés"
		)
		ORDER BY id, pdv,plaine_co, "Nb de RDV honorés"
		;

		-- rdv_hon_total
		CREATE TEMPORARY TABLE rdv_hon_total AS
			SELECT id, pdv, plaine_co,
				0 AS nb_pers_1_3_rdv,
				0 AS nb_pers_4_6_rdv,
				0 AS nb_pers_7_plus_rdv,
				nb_psn_rdv_periode_d1."Nb personnes" AS nb_pers_total_rdv,
				0 AS total_RDV, 
				0 AS dt_rdv_psy
			FROM nb_psn_rdv_periode_d1 
			WHERE "Nb de RDV honorés"='Total';

		-- rdv_hon_1_3
		CREATE TEMPORARY TABLE rdv_hon_1_3 AS
		SELECT 	id, pdv,  plaine_co,
			nb_psn_rdv_periode_d1."Nb personnes" AS nb_pers_1_3_rdv,
			0 AS nb_pers_4_6_rdv,
			0 AS nb_pers_7_plus_rdv,
			0 AS nb_pers_total_rdv,
			0 AS total_RDV, 
			0 AS dt_rdv_psy
		FROM nb_psn_rdv_periode_d1 
		WHERE "Nb de RDV honorés"='1 à 3 RDV honorés';

		-- rdv_hon_4_6
		CREATE TEMPORARY TABLE rdv_hon_4_6 AS
		SELECT 	id, pdv,  plaine_co,
			0 AS nb_pers_1_3_rdv,
			nb_psn_rdv_periode_d1."Nb personnes" AS nb_pers_4_6_rdv ,
			0 AS  nb_pers_7_plus_rdv,
			0 AS nb_pers_total_rdv,
			0 AS total_RDV, 
			0 AS dt_rdv_psy
		FROM nb_psn_rdv_periode_d1 
		WHERE "Nb de RDV honorés"='4 à 6 RDV honorés';

		-- rdv_hon_7_et_plus
		CREATE TEMPORARY TABLE rdv_hon_7_et_plus AS
		SELECT	id, pdv,  plaine_co,
			0 AS nb_pers_1_3_rdv,
			0 AS nb_pers_4_6_rdv,
			nb_psn_rdv_periode_d1."Nb personnes" AS nb_pers_7_plus_rdv,
			0 AS nb_pers_total_rdv,
			0 AS total_RDV, 
			0 AS dt_rdv_psy
		FROM nb_psn_rdv_periode_d1 
		WHERE "Nb de RDV honorés"='7 et + RDV honorés';

		-- total_rdv_periode_d1 : 
		CREATE TEMPORARY TABLE total_rdv_periode_d1 AS
		(
			SELECT DISTINCT rdv.*, rdv_psy."Dont Total RDV Psy" FROM (
				SELECT 
					DISTINCT structuresreferentes.id, pdv_format_export.pdv, pdv_format_export.plaine_co, 
					COUNT( DISTINCT( rdv_ind_honore_durant_periode.id ) ) AS "Total RDV"
				FROM personnes
					INNER JOIN suivi_durant_periode ON (suivi_durant_periode.personne_id = personnes.id)
					INNER JOIN questionnairesd1pdvs93 ON (questionnairesd1pdvs93.id=suivi_durant_periode.id)
					INNER JOIN situationsallocataires ON (questionnairesd1pdvs93.situationallocataire_id=situationsallocataires.id)
					INNER JOIN rendezvous ON (rendezvous.id = suivi_durant_periode.rendezvous_id )
					LEFT OUTER JOIN foyers ON (personnes.foyer_id = foyers.id)
					LEFT OUTER JOIN dossiers ON (foyers.dossier_id = dossiers.id)
					LEFT OUTER JOIN structuresreferentes ON (rendezvous.structurereferente_id = structuresreferentes.id)
					LEFT OUTER JOIN administration.dec_pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
					INNER JOIN dd_durant_periode ON (dd_durant_periode.personne_id=personnes.id)
					INNER JOIN rdv_ind_honore_durant_periode ON (rdv_ind_honore_durant_periode.personne_id = personnes.id)
					INNER JOIN referents ON ( rdv_ind_honore_durant_periode.referent_id = referents.id)
				WHERE 	-- SR D1 = SR RDV 
					rdv_ind_honore_durant_periode.structurereferente_id=structuresreferentes.id
					AND (
						--Soit DD dans histo droit
						dd_durant_periode.personne_id IS NOT NULL
						OR
						-- Soit DD dans D1
						situationsallocataires.toppersdrodevorsa='1'
					)
				GROUP BY structuresreferentes.id,pdv, plaine_co
			) AS rdv LEFT OUTER JOIN 
		--union
			(
			SELECT 
				DISTINCT structuresreferentes.id, pdv_format_export.pdv,pdv_format_export.plaine_co, 
				COUNT( DISTINCT( rdv_ind_honore_durant_periode.id ) ) AS "Dont Total RDV Psy"

			FROM personnes
				-- D1 durant la periode traité
				INNER JOIN suivi_durant_periode ON (suivi_durant_periode.personne_id = personnes.id)
				INNER JOIN questionnairesd1pdvs93 ON (questionnairesd1pdvs93.id=suivi_durant_periode.id)
				INNER JOIN situationsallocataires ON (questionnairesd1pdvs93.situationallocataire_id=situationsallocataires.id)
				INNER JOIN rendezvous ON (rendezvous.id = suivi_durant_periode.rendezvous_id )
				LEFT OUTER JOIN foyers ON (personnes.foyer_id = foyers.id)
				LEFT OUTER JOIN dossiers ON (foyers.dossier_id = dossiers.id)
				LEFT OUTER JOIN structuresreferentes ON (rendezvous.structurereferente_id = structuresreferentes.id)
				LEFT OUTER JOIN administration.dec_pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
				INNER JOIN dd_durant_periode ON (dd_durant_periode.personne_id=personnes.id)
				INNER JOIN rdv_ind_honore_durant_periode ON (rdv_ind_honore_durant_periode.personne_id = personnes.id)
				INNER JOIN referents ON ( rdv_ind_honore_durant_periode.referent_id = referents.id)
			WHERE 	-- SR D1 = SR RDV 
				rdv_ind_honore_durant_periode.structurereferente_id=structuresreferentes.id
				AND (
					--Soit DD dans histo droit
					dd_durant_periode.personne_id IS NOT NULL
					OR
					-- Soit DD dans D1
					situationsallocataires.toppersdrodevorsa='1'
				)
				AND fonction= 'Psychologue'
			GROUP BY structuresreferentes.id,pdv, plaine_co
			)  AS rdv_psy ON rdv.pdv=rdv_psy.pdv

		);

		-- total_rdv :
		CREATE TEMPORARY TABLE total_rdv AS
			SELECT 	id, pdv, plaine_co,
				0 AS nb_pers_1_3_rdv, 
				0 AS nb_pers_4_6_rdv, 
				0 AS nb_pers_7_plus_rdv,
				0 AS nb_pers_total_rdv,
				total_rdv_periode_d1."Total RDV" AS total_RDV, 
				total_rdv_periode_d1."Dont Total RDV Psy" AS dt_rdv_psy
			FROM total_rdv_periode_d1
		;

		/* ***********************************************
		*************** LISTE DES VUES *******************
		*************************************************/

		/**
		-- EXPORT 1 : Listing Partie 2a
		-- ** PARTIE ORIENTATIONS ** --
		**/
		CREATE TABLE administration.b1b2_liste_2a AS
			SELECT DISTINCT 
				personnes.id AS personne_id, 
				orient_tot.id,
				orient_tot.pdv AS structure_referente_pdv,
				orient_tot.plaine_co,
				personnes.nom, personnes.prenom, 
				CASE 	WHEN personnes.sexe='1' THEN 'homme' 
					WHEN personnes.sexe='2' THEN 'femme' 
				END AS sexe, 
				personnes.dtnai, 
				dossiers.matricule AS num_CAF,
				valid_orient_durant_periode.date_valid AS date_validation_orientation, 
				valid_orient_durant_periode.origine AS methode_orientation
				FROM orient_tot, foyers, dossiers, personnes 
				INNER JOIN valid_orient_durant_periode ON (personnes.id=valid_orient_durant_periode.personne_id)
			WHERE 
				valid_orient_durant_periode.structurereferente_id=orient_tot.id
				AND foyers.id = personnes.foyer_id
				AND dossiers.id = foyers.dossier_id
				AND pdv IS NOT NULL  
			ORDER BY personnes.id
		;

		/**
		-- EXPORT 2 : Listing Partie 3
		-- PARTIE PERSONNES SUIVIES
		**/
		CREATE TABLE administration.b1b2_liste_3 AS 
			SELECT DISTINCT *
			FROM partie_3
			ORDER BY "PDV de D1", "Service referent (orientation)"
		;

		/**
		-- EXPORT 3 : Listing Partie 4
		**/
		CREATE TABLE administration.b1b2_stat_4 AS
			SELECT DISTINCT 
				rdv_hon_1_3.id, rdv_hon_1_3.pdv, 
				rdv_hon_1_3.plaine_co,
				rdv_hon_1_3.nb_pers_1_3_rdv,
				rdv_hon_4_6.nb_pers_4_6_rdv,
				rdv_hon_7_et_plus.nb_pers_7_plus_rdv,
				rdv_hon_total.nb_pers_total_rdv,
				total_rdv.total_rdv,
				total_rdv.dt_rdv_psy
			FROM	rdv_hon_1_3  
				LEFT OUTER JOIN rdv_hon_4_6 ON (rdv_hon_1_3.id=rdv_hon_4_6.id)
				LEFT OUTER JOIN rdv_hon_7_et_plus ON (rdv_hon_1_3.id=rdv_hon_7_et_plus.id)
				LEFT OUTER JOIN rdv_hon_total ON (rdv_hon_1_3.id=rdv_hon_total.id)
				LEFT OUTER JOIN total_rdv ON (rdv_hon_1_3.id=total_rdv.id)
			WHERE rdv_hon_1_3.pdv IS NOT NULL
		;
		
		-- Listing partie 4 : liste des personnes avec leur nb de RDV individuels
		CREATE TABLE administration.b1b2_liste_4_1 AS
			SELECT DISTINCT personne_id, nom, prenom, dtnai AS "date de naissance", matricule, 
			pdv, plaine_co,
			"count rdv_id" AS "nb de RDV individuels"
			FROM nb_rdv_par_personne 
				INNER JOIN personnes ON (personnes.id=nb_rdv_par_personne.personne_id) 
				INNER JOIN foyers ON (foyers.id=personnes.foyer_id)
				INNER JOIN dossiers ON (dossiers.id=foyers.dossier_id)
			WHERE pdv IS NOT NULL
			ORDER BY pdv, personne_id
		;

		-- Listing partie 4 : liste des RDV individuels par personne
		CREATE TABLE administration.b1b2_liste_4_2 AS
			SELECT nb_rdv_par_personne.personne_id, nom, prenom, dtnai AS "date de naissance", matricule, 
			pdv, plaine_co,
			daterdv, heurerdv
			FROM nb_rdv_par_personne 
				INNER JOIN personnes ON (personnes.id=nb_rdv_par_personne.personne_id) 
				INNER JOIN foyers ON (foyers.id=personnes.foyer_id)
				INNER JOIN dossiers ON (dossiers.id=foyers.dossier_id)
				INNER JOIN rdv_ind_honore_durant_periode ON (rdv_ind_honore_durant_periode.personne_id=nb_rdv_par_personne.personne_id AND structurereferente_id=nb_rdv_par_personne.id)
			WHERE pdv IS NOT NULL
			ORDER BY pdv, personne_id, daterdv, heurerdv
		;

		/**
		-- Export 4 : B1 tableau 
		**/
		CREATE TABLE administration.b1b2_stat_3 AS
			SELECT id, "PDV de D1", plaine_co,
				SUM(CASE WHEN "Type de suivi" = 'Reconduit' THEN 1 ELSE 0 END) AS "Nb de psnes reconduites", --3aa
				SUM(CASE WHEN "Type de suivi" = 'Nouveau' THEN 1 ELSE 0 END) AS "Nb de nouvelles psnes suivies", --3ab
				SUM(CASE WHEN "Service referent (orientation)" = 'Le PDV' THEN 1 ELSE 0 END) AS "Nb de personnes suivies avec référent PDV", --3ba
				SUM(CASE WHEN "Service referent (orientation)" = 'Le Pôle Emploi' THEN 1 ELSE 0 END) AS "Nb de personnes suivies avec référent Pôle Emploi", --3bb
				SUM(CASE WHEN "Service referent (orientation)" = 'Le Service Social' THEN 1 ELSE 0 END) AS "Nb de personnes suivies avec référent Service Social", --3bc
				SUM(CASE WHEN "Service referent (orientation)" = 'Une Asso conventionnée' THEN 1 ELSE 0 END) AS "Nb de personnes suivies avec référent assoc conv par le Dpt", --3bd
				SUM(CASE WHEN "Service referent (orientation)" = 'Un autre PDV' THEN 1 ELSE 0 END) AS "Nb de personnes suivies avec référent un autre PDV", --3be
				SUM(CASE WHEN "Service referent (orientation)" != 'Sans Orientation' THEN 1 ELSE 0 END) AS "Nb de personnes suivies avec référent", --3b1
				SUM(CASE WHEN "Service referent (orientation)" = 'Sans Orientation' THEN 1 ELSE 0 END) AS "Nb de personnes suivies sans orientation", --3b2
				COUNT(*) AS "Nb de personnes suivies" --3
			FROM partie_3 
			GROUP BY id, "PDV de D1", plaine_co ORDER BY id, "PDV de D1";
		
		---------------------
		-- Requete globale -- liste où au moins un PDV est renseigné parmi les tables temporaires
		---------------------
		CREATE TABLE administration.b1b2_global AS
			SELECT 
				structuresreferentes.id, pdv_format_export.pdv, 
				pdv_format_export.plaine_co,
				-- PARTIE 2 --
				orient_tot."Nb de psnes avec orientations validées dans la période" AS "2.a Nb de psnes avec orientations validees dans la periode", --2c
				-- PARTIE 3 --
				b1b2_stat_3."Nb de psnes reconduites" AS "3.A.a Nb de psnes reconduites", --3aa
				b1b2_stat_3."Nb de nouvelles psnes suivies" AS "3.A.b Nb de nouvelles psnes suivies", --3ab
				b1b2_stat_3."Nb de personnes suivies" AS "3.A Nb de personnes suivies", --3a
				b1b2_stat_3."Nb de personnes suivies avec référent PDV" AS "3.B.a Nb de personnes suivies avec referent PDV", --3ba
				b1b2_stat_3."Nb de personnes suivies avec référent Pôle Emploi" AS "3.B.b Nb de personnes suivies avec referent Pole Emploi", --3bb
				b1b2_stat_3."Nb de personnes suivies avec référent Service Social" AS "3.B.c Nb de personnes suivies avec referent Service Social", --3bc
				b1b2_stat_3."Nb de personnes suivies avec référent assoc conv par le Dpt" AS "3.Bd Nb de personnes suivies avec referent assoc conv par Dpt", --3bd
				b1b2_stat_3."Nb de personnes suivies avec référent un autre PDV" AS "3.B.e Nb de personnes suivies avec referent un autre PDV", --3be
				b1b2_stat_3."Nb de personnes suivies avec référent" AS "3.B.1 Nb de personnes suivies avec referent", --3b1
				b1b2_stat_3."Nb de personnes suivies sans orientation" AS "3.B.2 Nb de personnes suivies sans orientation", --3b2
				b1b2_stat_3."Nb de personnes suivies" AS "3.B Nb de personnes suivies", --3b
				-- PARTIE 4 --
				b1b2_stat_4.nb_pers_1_3_rdv AS "4.A.a 1 a 3 RDV honores", --4aa
				b1b2_stat_4.nb_pers_4_6_rdv AS "4.A.b 4 a 6 RDV honores", --4ab
				b1b2_stat_4.nb_pers_7_plus_rdv AS "4.A.c 7 et + RDV honores", --4ac
				b1b2_stat_4.nb_pers_total_rdv AS "4.A Total personnes ayant eu 1 ou + RDV", --4a
				b1b2_stat_4.total_RDV AS "4.B Total RDV", --4b
				b1b2_stat_4.dt_rdv_psy AS "4.C Dont Total RDV psy" --4c
			FROM structuresreferentes LEFT OUTER JOIN administration.dec_pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
				-- PARTIE 2
				LEFT OUTER JOIN orient_tot ON (orient_tot.id=structuresreferentes.id)
				-- PARTIE 3
				LEFT OUTER JOIN b1b2_stat_3 ON (b1b2_stat_3.id = structuresreferentes.id)
				--PARTIE 4
				LEFT OUTER JOIN b1b2_stat_4 ON (b1b2_stat_4.id=structuresreferentes.id)
			WHERE orient_tot.pdv IS NOT NULL
				OR b1b2_stat_3."PDV de D1" IS NOT NULL
				OR b1b2_stat_4.pdv IS NOT NULL
			ORDER BY structuresreferentes.id, structuresreferentes.lib_struc;



		CREATE TABLE administration.b1b2_global_plaine_co AS
			SELECT 
				-- PARTIE 2 --
				SUM(orient_tot."Nb de psnes avec orientations validées dans la période") AS "2.a Nb de psnes avec orientations validees dans la periode", --2c
				-- PARTIE 3 --
				SUM(b1b2_stat_3."Nb de psnes reconduites") AS "3.A.a Nb de psnes reconduites", --3aa
				SUM(b1b2_stat_3."Nb de nouvelles psnes suivies") AS "3.A.b Nb de nouvelles psnes suivies", --3ab
				SUM(b1b2_stat_3."Nb de personnes suivies") AS "3.A Nb de personnes suivies", --3a
				SUM(b1b2_stat_3."Nb de personnes suivies avec référent PDV") AS "3.B.a Nb de personnes suivies avec referent PDV", --3ba
				SUM(b1b2_stat_3."Nb de personnes suivies avec référent Pôle Emploi") AS "3.B.b Nb de personnes suivies avec referent Pole Emploi", --3bb
				SUM(b1b2_stat_3."Nb de personnes suivies avec référent Service Social") AS "3.B.c Nb de personnes suivies avec referent Service Social", --3bc
				SUM(b1b2_stat_3."Nb de personnes suivies avec référent assoc conv par le Dpt") AS "3.Bd Nb de personnes suivies avec referent assoc conv par Dpt", --3bd
				SUM(b1b2_stat_3."Nb de personnes suivies avec référent un autre PDV") AS "3.B.e Nb de personnes suivies avec referent un autre PDV", --3be
				SUM(b1b2_stat_3."Nb de personnes suivies avec référent") AS "3.B.1 Nb de personnes suivies avec referent", --3b1
				SUM(b1b2_stat_3."Nb de personnes suivies sans orientation") AS "3.B.2 Nb de personnes suivies sans orientation", --3b2
				SUM(b1b2_stat_3."Nb de personnes suivies") AS "3.B Nb de personnes suivies", --3b
				-- PARTIE 4 --
				SUM(b1b2_stat_4.nb_pers_1_3_rdv) AS "4.A.a 1 a 3 RDV honores", --4aa
				SUM(b1b2_stat_4.nb_pers_4_6_rdv) AS "4.A.b 4 a 6 RDV honores", --4ab
				SUM(b1b2_stat_4.nb_pers_7_plus_rdv) AS "4.A.c 7 et + RDV honores", --4ac
				SUM(b1b2_stat_4.nb_pers_total_rdv) AS "4.A Total personnes ayant eu 1 ou + RDV", --4a
				SUM(b1b2_stat_4.total_RDV) AS "4.B Total RDV", --4b
				SUM(b1b2_stat_4.dt_rdv_psy) AS "4.C Dont Total RDV psy" --4c
			FROM structuresreferentes LEFT OUTER JOIN administration.dec_pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
				-- PARTIE 2
				LEFT OUTER JOIN orient_tot ON (orient_tot.id=structuresreferentes.id)
				-- PARTIE 3
				LEFT OUTER JOIN b1b2_stat_3 ON (b1b2_stat_3.id = structuresreferentes.id)
				--PARTIE 4
				LEFT OUTER JOIN b1b2_stat_4 ON (b1b2_stat_4.id=structuresreferentes.id)
			WHERE (orient_tot.pdv IS NOT NULL
				OR b1b2_stat_3."PDV de D1" IS NOT NULL
				OR b1b2_stat_4.pdv IS NOT NULL)
				AND pdv_format_export.plaine_co='oui'
			;

		RETURN 0;
	END;

	
$_$;


ALTER FUNCTION administration.dec_pdv_b1b2_vues(dd_periode_orient text, df_periode_orient text, dd_periode text, df_periode text, nb_mois integer, rdv_individuel integer, rdv_honore integer) OWNER TO webrsa;

--
-- Name: get_sequence(character varying); Type: FUNCTION; Schema: administration; Owner: webrsa
--

CREATE FUNCTION administration.get_sequence(p_sequence_name character varying) RETURNS SETOF bigint
    LANGUAGE plpgsql
    AS $$
BEGIN
 RETURN QUERY EXECUTE 'SELECT last_value from ' || quote_ident(p_sequence_name) ;
END
$$;


ALTER FUNCTION administration.get_sequence(p_sequence_name character varying) OWNER TO webrsa;

--
-- Name: grille(); Type: FUNCTION; Schema: administration; Owner: webrsa
--

CREATE FUNCTION administration.grille() RETURNS integer
    LANGUAGE plpgsql STRICT
    AS $$

DECLARE
	
	/************************************************
	************* Liste des paramètres **************
	************************************************/

	BEGIN
		/* ***********************************************
		*******  ************
		*************************************************/

		DROP TABLE if exists grille;

			CREATE TABLE grille
			(
			dtnai date,
			matricule character(15),
			sexe character(5)
			)
			WITH (
			OIDS=FALSE
			);
			ALTER TABLE grille
			OWNER TO webrsa;

			COPY grille FROM '/tmp/grille.csv' DELIMITERS ';' CSV HEADER;

			UPDATE grille SET sexe='1' WHERE sexe='Homme';
			UPDATE grille SET sexe='2' WHERE sexe='Femme';
			ALTER TABLE grille ALTER COLUMN sexe TYPE character(1);
			UPDATE grille  SET matricule=rpad(matricule, 15, '0'); -- Ajout 25/09

			drop table if exists grillefinal;
			create table grillefinal as
			SELECT p.id,g.* FROM personnes p
			inner join foyers f on (f.id=p.foyer_id)
			inner join dossiers d on (d.id=f.dossier_id)
			inner join grille g on (g.matricule=d.matricule)
			where  d.matricule=g.matricule and p.sexe=g.sexe and g.dtnai=p.dtnai
			and  d.id IN ( SELECT "derniersdossiersallocataires"."dossier_id" FROM derniersdossiersallocataires WHERE "derniersdossiersallocataires"."personne_id" = p.id )
			order by d.matricule;
			DROP TABLE if exists grille;

			-- 1 Table temporaire fusionnant les tables informationspe & historiqueetatspe
			CREATE TEMPORARY TABLE a_pe_temp AS 
			SELECT nom as nom, prenom as prenom, dtnai as dtnai, nir, identifiantpe, historiqueetatspe.id as id_histpe, historiqueetatspe.date as date_etat, etat, code, motif
			FROM informationspe, historiqueetatspe
			WHERE 
			informationspe.id = historiqueetatspe.informationpe_id 
			AND etat='inscription'
			AND date >'1910-12-31'
			--AND date between '2016-11-01' AND '2016-11-30'
			ORDER BY date;

			-- 3 Tables fusionnant dans une table les données PE avec personnes.id selon 3 clés (fusion de la 1ere table avec la table personne en utilisant les 3 clés; 

			--1ere clé=nom;
			CREATE TEMPORARY TABLE a_pe_psnid_nom_temp AS
			SELECT DISTINCT personnes.id as psnid, a_pe_temp.*

			FROM a_pe_temp , personnes 
			where
				(personnes.nom=a_pe_temp.nom AND personnes.prenom=a_pe_temp.prenom and personnes.dtnai=a_pe_temp.dtnai)
				; 
				
			--2e clé=nir;
			CREATE TEMPORARY TABLE a_pe_psnid_nir_temp AS
			SELECT DISTINCT personnes.id as psnid, a_pe_temp.*

			FROM a_pe_temp , personnes 
			where	
			(personnes.nir IS NOT NULL AND a_pe_temp.nir IS NOT NULL AND personnes.nir=SUBSTRING(a_pe_temp.nir FROM 1 FOR 13))
			; 

			--3e clé=idassedic
			CREATE TEMPORARY TABLE a_pe_psnid_idpe_temp AS
			SELECT DISTINCT personnes.id as psnid, a_pe_temp.*


			FROM a_pe_temp , personnes 
			where
				(personnes.idassedic IS NOT NULL AND a_pe_temp.identifiantpe IS NOT NULL AND personnes.idassedic=SUBSTRING(a_pe_temp.identifiantpe from 4 for 8))
			; 


			--fusion des 3 tables
			--méthode contcatenation = ok si pas de intersection;
			CREATE TEMPORARY TABLE a_pe_psnid as 

			SELECT a_pe_psnid_nom_temp.*
			FROM a_pe_psnid_nom_temp

			UNION
			SELECT a_pe_psnid_nir_temp.*
			FROM a_pe_psnid_nir_temp
			UNION
			SELECT a_pe_psnid_idpe_temp.*
			FROM a_pe_psnid_idpe_temp

			order by  id_histpe  desc, psnid;

			--création table avec derniere info pe
			CREATE TABLE public.a_pe_psnid_maxid as 
			SELECT distinct a_pe_psnid.*
			FROM a_pe_psnid
			WHERE (id_histpe ,psnid) in (select max(id_histpe), psnid from a_pe_psnid group by psnid);


			-- Requête 3
			create table tmpgrille as
			SELECT distinct g.id,
			public.personnes.qual as Civilite, personnes.nom, personnes.prenom, personnes.nir, personnes.dtnai as dateNaissance, 

			case 
			when nati='F' then 'Francaise'
			when nati='C' then 'CEE ou Suisse'
			when nati='A' then 'Autres que CEE ou Suisse'
			end as Nationalite,
			nati,
			numvoie,nomvoie,adresses.codepos as cpvilledom,adresses.nomcom as communedom,adresses.numcom as villedom,public.dossiers.dtdemrsa as dateDemandeRsa, DTDEMRMI as dateDemandeRMI, 
			prestations.rolepers,
			dossiers.numdemrsa,dossiers.matricule,
			case 
			when Toppersdrodevorsa='0' then 'NON'
			when Toppersdrodevorsa='1' then 'OUI'
			end as TopDroitDvr,

			case 
			when foyers.sitfam='ABA' then 'DISPARU (JUGEMENT D ABSENCE)'
			when foyers.sitfam='CEL' then 'CELIBATAIRE'
			when foyers.sitfam='DIV' then 'DIVORCE'
			when foyers.sitfam='ISO' then 'ISOLEMENT APRES VIE MARITALE OU PACS'
			when foyers.sitfam='MAR' then 'MARIAGE'
			when foyers.sitfam='PAC' then 'PACS'
			when foyers.sitfam='RPA' then 'REPRISE VIE COMMUNE SUR PACS'
			when foyers.sitfam='RVC' then 'REPRISE VIE MARITALE'
			when foyers.sitfam='RVM' then 'REPRISE MARIAGE'
			when foyers.sitfam='SEF' then 'SEPARATION DE FAIT'
			when foyers.sitfam='SEL' then 'SEPARATION LEGALE'
			when foyers.sitfam='VEU' then 'VEUVAGE'
			when foyers.sitfam='VIM' then 'VIE MARITALE'
			end as situation_familiale,	
			nbenfautcha,
			case 
			when natpf='RSB' then 'RSA Socle Local (Financement sur fonds Conseil général)'
			when natpf='RSD' then 'RSA Socle (Financement sur fonds Conseil général)'
			when natpf='RSI' then 'RSA Socle majoré (Financement sur fonds Conseil général)'
			when natpf='RCB' then 'RSA Activité Local (Financement sur fonds Conseil général)'
			when natpf='RCD' then 'RSA Activité (Financement sur fonds Etat)'
			when natpf='RCI' then 'RSA Activité majoré (Financement sur fonds Etat)'
			when natpf='RCJ' then 'RSA Activité Jeune  (Financement sur fonds Etat)'
			when natpf='RCU' then 'RSA Activité Etat Contrat aidé (Financement sur fonds Etat)'
			when natpf='RSJ' then 'RSA Socle Jeune  (Financement sur fonds Etat)'
			when natpf='RSU' then 'RSA Socle Etat Contrat aidé  (Financement sur fonds Etat)'
			end as nature_presta_fam,

			case 
			when etatdosrsa='0' then 'Nouvelle demande en attente de décision CG pour ouverture du droit'
			when etatdosrsa='1' then 'Droit refusé'
			when etatdosrsa='2' then 'Droit ouvert et versable'
			when etatdosrsa='3' then 'Droit ouvert et suspendu (le montant du droit est calculable, mais l existence du droit est remis en cause)'
			when etatdosrsa='4' then 'Droit ouvert mais versement suspendu (le montant du droit n est pas calculable)'
			when etatdosrsa='5' then 'Droit clos'
			when etatdosrsa='6' then 'Droit clos sur mois antérieur ayant eu des créances transférées ou une régularisation dans le mois de référence pour une période antérieure.'
			end as etat_dossier,

			case 
			when a.psnid IS NULL then 'NON'
			when a.psnid IS NOT NULL then 'OUI'	
			end as Pole_emploi,

			case 
			when val.name!='garde d''enfant' then 'NON'
			when val.name='garde d''enfant' then 'OUI'	
			end as Garde_enfant,

			REPLACE(c.engag_object, '', '') as them_sautl,
			length(REPLACE(c.engag_object, '', '')) 
			as lg_themsautl,	
			c.dd_ci,
			c.df_ci,
			c.duree_engag,
			c.decision_ci,
			c.datevalidation_ci AS "Date de validation du CER",
			c.date_saisi_ci,
			c.lieu_saisi_ci,
			c.emp_trouv,
			c.forme_ci,
			c.diplomes,
			c.form_compl As "Formation compl",
			questionnairesd1pdvs93.date_validation AS "Date de validation du questionnaires D1",
			questionnairesd1pdvs93.marche_travail

			FROM 
				public.grillefinal g
				LEFT OUTER JOIN public.personnes on (g.id=personnes.id)
			LEFT OUTER JOIN public.a_pe_psnid_maxid a on (a.psnid=g.id) 
				LEFT OUTER JOIN public.contratsinsertion c on (c.personne_id = g.id and dd_ci between '2015-01-01' and '2015-12-01')        
			LEFT OUTER JOIN public.orientsstructs ON ( g.id = public.orientsstructs.personne_id )
			LEFT OUTER JOIN public.foyers ON ( foyers.id = personnes.foyer_id)
			LEFT OUTER JOIN public.prestations ON ( g.id=prestations.personne_id )
			LEFT OUTER JOIN public.calculsdroitsrsa ON ( calculsdroitsrsa.personne_id = g.id )
				LEFT OUTER JOIN public.dossiers ON ( dossiers.id = foyers.dossier_id )
			LEFT OUTER JOIN public.adressesfoyers on (adressesfoyers.foyer_id = foyers.id and adressesfoyers.rgadr ='01')
			LEFT OUTER JOIN public.adresses on (adressesfoyers.adresse_id = adresses.id)
			LEFT OUTER JOIN public.situationsdossiersrsa ON ( dossiers.id = situationsdossiersrsa.dossier_id )
			LEFT OUTER JOIN public.detailsdroitsrsa ON ( dossiers.id = detailsdroitsrsa.dossier_id )
			LEFT OUTER JOIN public.detailscalculsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id )
			LEFT OUTER JOIN public.questionnairesd1pdvs93 ON ( questionnairesd1pdvs93.personne_id = g.id and date_validation between '2017-01-01' and '2017-12-01' )
			LEFT OUTER JOIN public.cers93 on (cers93.contratinsertion_id=c.id)
			LEFT OUTER JOIN public.cers93_sujetscers93 ce on (ce.cer93_id=cers93.contratinsertion_id)
			LEFT OUTER JOIN public.sujetscers93 suj on (suj.id = ce.sujetcer93_id) 
			LEFT OUTER JOIN public.soussujetscers93 ssuj on ( ce.soussujetcer93_id = ssuj.id)
			LEFT OUTER JOIN public.valeursparsoussujetscers93 val on (ce.valeurparsoussujetcer93_id = val.id ),
	
			public.structuresreferentes;
	
			drop table if exists a_pe_psnid_maxid;
			drop table if exists grille;
			drop table if exists grillefinal;

			--COPY ( SELECT * FROM tmpgrille  ) TO '/tmp/newgrille.csv' WITH DELIMITER AS ';' CSV HEADER;
			
			
			/* ***********************************************
			******* VUE **************************************
			*************************************************/
			
			-- afficher le premier rdv en date;
			CREATE OR REPLACE VIEW grille AS
			select * from tmpgrille;

			--drop table if exists tmpgrille;			
		
		RETURN 0;
	END;

	
$$;


ALTER FUNCTION administration.grille() OWNER TO webrsa;

--
-- Name: pdv_b1b2_vues(text, text, text, text, integer, integer, integer); Type: FUNCTION; Schema: administration; Owner: webrsa
--

CREATE FUNCTION administration.pdv_b1b2_vues(dd_periode_orient text, df_periode_orient text, dd_periode text, df_periode text, nb_mois integer, rdv_individuel integer, rdv_honore integer) RETURNS integer
    LANGUAGE plpgsql STRICT
    AS $_$

DECLARE
	
	/************************************************
	************* Liste des paramètres **************
	************************************************/
	
	-- dates de début de fin et année de periode pour les orientations
	dd_periode_orient CONSTANT date NOT NULL := $1;
	df_periode_orient CONSTANT date NOT NULL := $2;
	annee_periode_orient CONSTANT varchar(4) := EXTRACT (YEAR FROM dd_periode_orient);

	-- dates de début de fin et année de periode HORS orientations
	dd_periode CONSTANT date NOT NULL := $3;
	df_periode CONSTANT date NOT NULL := $4;
	annee_periode CONSTANT double precision := EXTRACT (YEAR FROM dd_periode);
	annee_prec CONSTANT double precision := annee_periode - 1;
	annee_prec2 CONSTANT double precision := annee_periode - 2;
	
	-- periode (6 ou 12)
	nb_mois CONSTANT integer := $5; 

	rdv_individuel CONSTANT integer := $6;

	rdv_honore CONSTANT integer := $7;

	BEGIN
		/* ***********************************************
		******* LISTE DES TABLES TEMP COMMUNES ************
		*************************************************/

		-- valid_orient_durant_periode
		DROP TABLE IF EXISTS valid_orient_durant_periode CASCADE;
		CREATE TABLE valid_orient_durant_periode AS
			SELECT DISTINCT orientsstructs.*, structuresreferentes.id AS strucref_orient	
			FROM orientsstructs 
				LEFT OUTER JOIN structuresreferentes ON (orientsstructs.structurereferente_id=structuresreferentes.id )	
			WHERE date_valid BETWEEN dd_periode_orient AND df_periode_orient 			
		;

		-- valid_orient_av_fin_periode
		DROP TABLE IF EXISTS valid_orient_av_fin_periode CASCADE;
		CREATE TABLE valid_orient_av_fin_periode AS
			SELECT DISTINCT orientsstructs.*, structuresreferentes.id AS strucref_orient	
			FROM orientsstructs 
				LEFT OUTER JOIN structuresreferentes ON (orientsstructs.structurereferente_id=structuresreferentes.id )	
			WHERE date_valid <= df_periode_orient			
		;
	
		-- der_valid_orient_av_fin_periode
		DROP TABLE IF EXISTS der_valid_orient_av_fin_periode CASCADE;
		CREATE TABLE der_valid_orient_av_fin_periode AS
			SELECT DISTINCT * 
			FROM valid_orient_av_fin_periode  
			WHERE (id, personne_id) IN (
				SELECT MAX(valid_orient_av_fin_periode.id), valid_orient_av_fin_periode.personne_id	
				FROM valid_orient_av_fin_periode 
				GROUP BY personne_id	
			)	
			ORDER BY personne_id	
		;
		
		-- dd_durant_periode (
		DROP TABLE IF EXISTS dd_durant_periode CASCADE;
		CREATE TABLE dd_durant_periode AS
			SELECT DISTINCT personne_id
			FROM historiquesdroits
			WHERE 
			(
				--1-- personnes avec DD créé ou modifié durant la période
				personne_id IN
				(
					SELECT DISTINCT personne_id FROM historiquesdroits
					WHERE historiquesdroits.toppersdrodevorsa =  '1'
						AND ( historiquesdroits.created, historiquesdroits.modified ) OVERLAPS ( dd_periode, df_periode ) 
				)
				OR 
				--2-- personnes sans enregistrement durant la période mais pour lesquelles le MAX histo de l'année precedente est DD
				( 
					personne_id NOT IN  
					(
						-- personnes avec un enregistrement créé ou modifié durant la période
						SELECT DISTINCT personne_id 
						FROM historiquesdroits
						WHERE ( historiquesdroits.created, historiquesdroits.modified ) OVERLAPS ( dd_periode, df_periode ) 
					)
					-- personnes dont le MAX histo est DD créé ou modifié l'année precedente
					AND (
						(personne_id,historiquesdroits.created ) IN 
							(SELECT DISTINCT personne_id,MAX(historiquesdroits.created) 
								FROM historiquesdroits
								WHERE EXTRACT(YEAR FROM historiquesdroits.created)=annee_prec 
									OR EXTRACT (YEAR FROM historiquesdroits.modified)=annee_prec
								GROUP BY personne_id
							)
						AND historiquesdroits.toppersdrodevorsa =  '1'
					)
				)
				OR
				--3-- personnes sans DD durant la période + non DD durant la période créé ou modifié après le début de la période  + MAX histo de l'année precedente=DD
				(
					personne_id NOT IN 
					(
						-- personnes avec DD créé ou modifié durant la période
						SELECT DISTINCT personne_id FROM historiquesdroits
						WHERE historiquesdroits.toppersdrodevorsa =  '1'
							AND ( historiquesdroits.created, historiquesdroits.modified ) OVERLAPS ( dd_periode, df_periode ) 
					)
					AND personne_id IN 
					(
						-- personnes avec non DD créé ou modifié après le début de la période
						SELECT DISTINCT personne_id FROM historiquesdroits
						WHERE historiquesdroits.toppersdrodevorsa =  '0'
							AND ( historiquesdroits.created, historiquesdroits.modified ) OVERLAPS ( dd_periode, df_periode )
					)
					-- personnes dont le MAX histo est DD créé ou modifié l'année précédente
					AND (
						(personne_id,historiquesdroits.created ) IN 
							(SELECT DISTINCT personne_id,MAX(historiquesdroits.created) 
								FROM historiquesdroits
								WHERE EXTRACT(YEAR FROM historiquesdroits.created)=annee_prec 
									OR EXTRACT (YEAR FROM historiquesdroits.modified)=annee_prec
								GROUP BY personne_id
							)
						AND historiquesdroits.toppersdrodevorsa =  '1'
					)
				)
			)
		;

		-- valid_orient_annee_prec
		DROP TABLE IF EXISTS valid_orient_annee_prec CASCADE;
		CREATE TABLE valid_orient_annee_prec AS
			SELECT DISTINCT *
			FROM orientsstructs
			WHERE EXTRACT (YEAR FROM orientsstructs.date_valid)=annee_prec 
		;

		-- dd_fin_annee_prec 
		DROP TABLE IF EXISTS dd_fin_annee_prec CASCADE;
		CREATE TABLE dd_fin_annee_prec AS
			SELECT DISTINCT personne_id
			FROM historiquesdroits
			WHERE 
				personne_id IN 
				(SELECT DISTINCT personne_id FROM historiquesdroits
					WHERE historiquesdroits.toppersdrodevorsa =  '1'
					AND 
					(EXTRACT (YEAR FROM historiquesdroits.created)=annee_prec
					OR
					EXTRACT (YEAR FROM historiquesdroits.modified)=annee_prec)
				)
				OR 
				(
					personne_id not IN  
						(SELECT DISTINCT personne_id 
						FROM historiquesdroits
						WHERE historiquesdroits.toppersdrodevorsa =  '1'
						AND 
						(EXTRACT (YEAR FROM historiquesdroits.created)=annee_prec
						OR
						EXTRACT (YEAR FROM historiquesdroits.modified)=annee_prec)
					)
					AND
					((personne_id,historiquesdroits.created ) IN  
						(SELECT DISTINCT personne_id,MAX(historiquesdroits.created) 
							FROM historiquesdroits
							WHERE EXTRACT(YEAR FROM historiquesdroits.created)=annee_prec2 
								OR EXTRACT( YEAR FROM historiquesdroits.modified)=annee_prec2
							GROUP BY personne_id
						)
					AND historiquesdroits.toppersdrodevorsa =  '1'
					)
				)
		; 

		/* ***********************************************
		******* LISTE DES TABLES TEMP partie 2 ************
		*************************************************/

		-- orient_tot : orientation durant la période
		-- DROP TABLE orient_tot CASCADE;
		DROP TABLE IF EXISTS orient_tot CASCADE;
		CREATE TABLE orient_tot AS
			SELECT DISTINCT structuresreferentes.id, pdv_format_export.pdv,pdv_format_export.plaine_co,COUNT( personnes.id) AS "Nb de psnes avec orientations validées dans la période" 	
			FROM valid_orient_durant_periode
				INNER JOIN personnes ON (personnes.id=valid_orient_durant_periode.personne_id)
				INNER JOIN structuresreferentes ON (valid_orient_durant_periode.structurereferente_id=structuresreferentes.id)
				LEFT OUTER JOIN administration.pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id) 
			GROUP BY structuresreferentes.id, pdv_id, pdv, plaine_co
		;



		/* ***********************************************
		******* LISTE DES TABLES TEMP partie 3 ************
		*************************************************/

		-- suivi_annee_prec D1 validé en 2014 + RDV individuel 
		DROP TABLE IF EXISTS suivi_annee_prec CASCADE;
		CREATE TABLE suivi_annee_prec AS
			SELECT DISTINCT questionnairesd1pdvs93.*,rendezvous.daterdv
			FROM questionnairesd1pdvs93
				INNER JOIN rendezvous ON (questionnairesd1pdvs93.rendezvous_id=rendezvous.id)
			WHERE 
				EXTRACT (YEAR FROM questionnairesd1pdvs93.date_validation)=annee_prec 
				AND typerdv_id=rdv_individuel
		; 

	
		-- suivis_annee_prec_dd : suivi_annee_precedente + DD_annee_precedente (dans histo ou D1)
		DROP TABLE IF EXISTS suivis_annee_prec_dd CASCADE;
		CREATE TABLE suivis_annee_prec_dd AS
			SELECT DISTINCT 
				personnes.id AS personne_id, personnes.nom, personnes.prenom, 
				CASE WHEN personnes.sexe='1' THEN 'homme' WHEN personnes.sexe='2' THEN 'femme' END AS sexe, 
				personnes.dtnai, 
				dossiers.matricule AS num_CAF,
				suivi_annee_prec.daterdv,
				structuresreferentes.id AS strucref_d1_annee_precedt_id, 
				pdv_format_export.pdv, 
				pdv_format_export.plaine_co
			FROM personnes
				-- D1 durant l'année précédent la periode traitée
				INNER JOIN suivi_annee_prec ON (suivi_annee_prec.personne_id = personnes.id)
				INNER JOIN questionnairesd1pdvs93 ON (questionnairesd1pdvs93.id=suivi_annee_prec.id)
				INNER JOIN situationsallocataires ON (questionnairesd1pdvs93.situationallocataire_id=situationsallocataires.id)
				INNER JOIN rendezvous ON (rendezvous.id = suivi_annee_prec.rendezvous_id )
				LEFT OUTER JOIN foyers ON (personnes.foyer_id = foyers.id)
				LEFT OUTER JOIN dossiers ON (foyers.dossier_id = dossiers.id)
				-- structure référente D1
				LEFT OUTER JOIN structuresreferentes ON (rendezvous.structurereferente_id = structuresreferentes.id)
				LEFT OUTER JOIN administration.pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
				-- Soit DD dans histo droit
				LEFT OUTER JOIN dd_fin_annee_prec ON (dd_fin_annee_prec.personne_id=personnes.id)
			WHERE 
				--Soit DD dans histo droit
				dd_fin_annee_prec.personne_id IS NOT NULL
				OR
				-- Soit DD dans D1 annee_prec
				situationsallocataires.toppersdrodevorsa='1'
		;
				
		
		-- suivi_durant_periode : D1 validé durant periode + RDV individuel
		-- DROP TABLE suivi_durant_periode;
		DROP TABLE IF EXISTS suivi_durant_periode CASCADE;
		CREATE TABLE suivi_durant_periode AS
			SELECT DISTINCT questionnairesd1pdvs93.*,rendezvous.id as rdv_id,rendezvous.daterdv, rendezvous.referent_id
			FROM questionnairesd1pdvs93
				INNER JOIN rendezvous ON (questionnairesd1pdvs93.rendezvous_id=rendezvous.id)
			WHERE 
				EXTRACT (YEAR FROM questionnairesd1pdvs93.date_validation)=annee_periode 
				AND 
				EXTRACT (MONTH FROM questionnairesd1pdvs93.date_validation) BETWEEN 1 AND nb_mois
				AND typerdv_id=rdv_individuel
		;


		-- 1er RDV en N-1: MAJ
		-- DROP TABLE premier_rdv_indiv_honore_annee_prec;
		DROP TABLE IF EXISTS premier_rdv_indiv_honore_annee_prec CASCADE;
		CREATE TABLE premier_rdv_indiv_honore_annee_prec AS
			SELECT DISTINCT rendezvous.*
			FROM rendezvous 
			WHERE 
			(personne_id,rendezvous.id) IN
				(
				SELECT personne_id, MIN(rendezvous.id)
				FROM rendezvous 
				WHERE 
				EXTRACT (YEAR FROM daterdv)=annee_prec
				AND typerdv_id=rdv_individuel 
				AND statutrdv_id=rdv_honore
				GROUP BY personne_id
				ORDER BY personne_id
				)
			ORDER BY personne_id
		;


		-- 1er RDV en N: MAJ
		-- DROP TABLE premier_rdv_indiv_honore_periode;
		DROP TABLE IF EXISTS premier_rdv_indiv_honore_periode CASCADE;
		CREATE TABLE premier_rdv_indiv_honore_periode AS
			SELECT DISTINCT rendezvous.*
			FROM rendezvous 
			WHERE 
			(personne_id,rendezvous.id) IN
				(
				SELECT personne_id, MIN(rendezvous.id)
				FROM rendezvous 
				WHERE 
				EXTRACT (YEAR FROM daterdv)=annee_periode
				AND typerdv_id=rdv_individuel 
				AND statutrdv_id=rdv_honore
				GROUP BY personne_id
				ORDER BY personne_id
				)
			ORDER BY personne_id
		;


		-- tout 1er RDV individuel honoré de la personne: MAJ
		-- DROP TABLE tout_premier_rdv_indiv_honore;
		DROP TABLE IF EXISTS tout_premier_rdv_indiv_honore CASCADE;
		CREATE TABLE tout_premier_rdv_indiv_honore AS
			SELECT DISTINCT rendezvous.*
			FROM rendezvous 
			WHERE 
			(personne_id,rendezvous.id) IN
				(
				SELECT personne_id, MIN(rendezvous.id)
				FROM rendezvous 
				WHERE 
				typerdv_id=rdv_individuel 
				AND statutrdv_id= 1
				GROUP BY personne_id
				ORDER BY personne_id
				)
			ORDER BY personne_id
		;


		-- Dernier CER validé de l'année N: MAJ
		-- DROP TABLE dernier_cer_valide_N;
		DROP TABLE IF EXISTS dernier_cer_valide_N CASCADE;
		CREATE TABLE dernier_cer_valide_N AS
			SELECT DISTINCT 
			contratsinsertion.personne_id,
			contratsinsertion.id,
			contratsinsertion.dd_ci, 
			contratsinsertion.df_ci,
			cers93.duree, 
			cers93.datesignature, 
			contratsinsertion.datevalidation_ci,
			CASE WHEN cers93.positioncer='99valide' THEN 'oui' END AS "Valide CG?",
			sujetscers93_autres."name" AS "sujetscers93_autres",
			sujetscers93_formation."name" AS "sujetscers93_formation",
			sujetscers93_sante."name" AS "sujetscers93_sante",
			sujetscers93_autonomie_sociale."name" AS "sujetscers93_autonomie_sociale",
			sujetscers93_logement."name" AS "sujetscers93_logement",
			sujetscers93_emploi."name" AS "sujetscers93_emploi"
			FROM contratsinsertion
			INNER JOIN cers93 ON (cers93.contratinsertion_id = contratsinsertion.id AND cers93.positioncer='99valide')
			LEFT OUTER JOIN cers93_sujetscers93 cers93_sujetscers93_autres ON (cers93_sujetscers93_autres.cer93_id = cers93.id AND cers93_sujetscers93_autres.sujetcer93_id=6)
			LEFT OUTER JOIN sujetscers93 sujetscers93_autres ON (cers93_sujetscers93_autres.sujetcer93_id = sujetscers93_autres.id AND sujetscers93_autres."name"='Autre')
			LEFT OUTER JOIN cers93_sujetscers93 cers93_sujetscers93_formation ON (cers93_sujetscers93_formation.cer93_id = cers93.id AND cers93_sujetscers93_formation.sujetcer93_id=2)
			LEFT OUTER JOIN sujetscers93 sujetscers93_formation ON (cers93_sujetscers93_formation.sujetcer93_id = sujetscers93_formation.id AND sujetscers93_formation."name"='La Formation')
			LEFT OUTER JOIN cers93_sujetscers93 cers93_sujetscers93_sante ON (cers93_sujetscers93_sante.cer93_id = cers93.id AND cers93_sujetscers93_sante.sujetcer93_id=4)
			LEFT OUTER JOIN sujetscers93 sujetscers93_sante ON (cers93_sujetscers93_sante.sujetcer93_id = sujetscers93_sante.id AND sujetscers93_sante."name"='La Santé')
			LEFT OUTER JOIN cers93_sujetscers93 cers93_sujetscers93_aut_soc ON (cers93_sujetscers93_aut_soc.cer93_id = cers93.id AND cers93_sujetscers93_aut_soc.sujetcer93_id=3)
			LEFT OUTER JOIN sujetscers93 sujetscers93_autonomie_sociale ON (cers93_sujetscers93_aut_soc.sujetcer93_id = sujetscers93_autonomie_sociale.id AND sujetscers93_autonomie_sociale."name"='L''Autonomie sociale')
			LEFT OUTER JOIN cers93_sujetscers93 cers93_sujetscers93_logement ON (cers93_sujetscers93_logement.cer93_id = cers93.id AND cers93_sujetscers93_logement.sujetcer93_id=5)
			LEFT OUTER JOIN sujetscers93 sujetscers93_logement ON (cers93_sujetscers93_logement.sujetcer93_id = sujetscers93_logement.id AND sujetscers93_logement."name"='Le Logement')
			LEFT OUTER JOIN cers93_sujetscers93 cers93_sujetscers93_emploi ON (cers93_sujetscers93_emploi.cer93_id = cers93.id AND cers93_sujetscers93_emploi.sujetcer93_id=1)
			LEFT OUTER JOIN sujetscers93 sujetscers93_emploi ON (cers93_sujetscers93_emploi.sujetcer93_id = sujetscers93_emploi.id AND sujetscers93_emploi."name"='L''Emploi')
			WHERE 
			(personne_id, contratsinsertion.id) IN
				(
				SELECT personne_id,  MAX(contratsinsertion.id)
				FROM contratsinsertion 
				WHERE EXTRACT (YEAR FROM contratsinsertion.datevalidation_ci)=annee_periode
				GROUP BY personne_id
				ORDER BY personne_id
				)
			ORDER BY personne_id
		;


		-- Table difficultés de santé: MAJ
		-- DROP TABLE difficultes_sante;
		DROP TABLE IF EXISTS difficultes_sante CASCADE;
		CREATE TABLE difficultes_sante AS
			( 
				SELECT DISTINCT
				'sante'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id,
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev ,
				personnes.*
				FROM personnes 
				INNER JOIN dsps on (personnes.id=dsps.personne_id) 
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id) 
				LEFT OUTER JOIN detailsdifsocs AS sante ON ( dsps.id = sante.dsp_id AND sante.difsoc IN ( '0402', '0403' ) ) 
				LEFT OUTER JOIN dsps_revs ON 
					( 
					dsps.personne_id = dsps_revs.personne_id 
					AND (dsps_revs.personne_id, dsps_revs.id) IN 
						( 
						SELECT personne_id, MAX(dsps_revs.id) 
						FROM dsps_revs GROUP BY personne_id
						)
					) 
				LEFT OUTER JOIN detailsdifsocs_revs sante_revs ON ( dsps_revs.id = sante_revs.dsp_rev_id AND sante_revs.difsoc IN ( '0402', '0403' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND sante.difsoc IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND sante_revs.difsoc IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
				ORDER BY nom, prenom, dtnai
			)
		; 


		-- Table difficultés de logement: MAJ
		-- DROP TABLE difficultes_logement;
		DROP TABLE IF EXISTS difficultes_logement CASCADE;
		CREATE TABLE difficultes_logement AS
			( 
				SELECT 'logement'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id,
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev 
				, personnes.*
				FROM personnes 
				INNER JOIN dsps on (personnes.id=dsps.personne_id) 
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id)  
				LEFT OUTER JOIN detailsdiflogs AS detailsdiflogs ON ( dsps.id = detailsdiflogs.dsp_id AND detailsdiflogs.diflog IN ( '1004', '1005', '1006', '1007', '1008', '1009' ) ) 
				LEFT OUTER JOIN dsps_revs ON 
				( dsps.personne_id = dsps_revs.personne_id 
					AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
						SELECT personne_id, MAX(dsps_revs.id) 
						FROM dsps_revs 
						GROUP BY personne_id
					) 
				) 
				LEFT OUTER JOIN detailsdiflogs_revs detailsdiflogs_revs ON ( dsps_revs.id = detailsdiflogs_revs.dsp_rev_id AND detailsdiflogs_revs.diflog IN ( '1004', '1005', '1006', '1007', '1008', '1009' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND detailsdiflogs.diflog IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND detailsdiflogs_revs.diflog IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 

	
		-- Table difficultés familiales: MAJ
		-- DROP TABLE difficultes_familial;
		DROP TABLE IF EXISTS difficultes_familial CASCADE;
		CREATE TABLE difficultes_familial AS 
			( 
				SELECT 'familiales'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id,
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev
				,personnes.* 
				FROM personnes 
				INNER JOIN dsps on (personnes.id=dsps.personne_id) 
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id) 
				LEFT OUTER JOIN detailsaccosocfams AS detailsaccosocfams ON ( dsps.id = detailsaccosocfams.dsp_id AND detailsaccosocfams.nataccosocfam IN ( '0412' ) )
				LEFT OUTER JOIN dsps_revs ON 
				( dsps.personne_id = dsps_revs.personne_id 
					AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					) 
				) 
				LEFT OUTER JOIN detailsaccosocfams_revs detailsaccosocfams_revs ON ( dsps_revs.id = detailsaccosocfams_revs.dsp_rev_id AND detailsaccosocfams_revs.nataccosocfam IN ( '0412' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND detailsaccosocfams.nataccosocfam IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND detailsaccosocfams_revs.nataccosocfam IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 


		-- Table difficultés de mode de garde: MAJ
		-- DROP TABLE difficultes_mode_garde;
		DROP TABLE IF EXISTS difficultes_mode_garde CASCADE;
		CREATE TABLE difficultes_mode_garde AS 
			( 
				SELECT 'modes_gardes'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id,
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev 
				FROM personnes 
				INNER JOIN dsps on (personnes.id=dsps.personne_id) 
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id) 
				LEFT OUTER JOIN detailsdifdisps AS detailsdifdisps ON ( dsps.id = detailsdifdisps.dsp_id AND detailsdifdisps.difdisp IN ( '0502', '0503', '0504' ) ) 
				LEFT OUTER JOIN dsps_revs ON 
				( dsps.personne_id = dsps_revs.personne_id 
					AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					)
				) 
				LEFT OUTER JOIN detailsdifdisps_revs detailsdifdisps_revs ON ( dsps_revs.id = detailsdifdisps_revs.dsp_rev_id AND detailsdifdisps_revs.difdisp IN ( '0502', '0503', '0504' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND detailsdifdisps.difdisp IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND detailsdifdisps_revs.difdisp IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
			AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 

	
		-- Table difficultés surendettement: MAJ
		-- DROP TABLE difficultes_surendettement;
		DROP TABLE IF EXISTS difficultes_surendettement CASCADE;
		CREATE TABLE difficultes_surendettement AS  
			( 
				SELECT 'surendettement'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id,
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev 
				FROM personnes 
				INNER JOIN dsps on (personnes.id=dsps.personne_id) 
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id) 
				LEFT OUTER JOIN detailsdifsocs AS surendettement ON ( dsps.id = surendettement.dsp_id AND surendettement.difsoc IN ( '0406' ) ) 
				LEFT OUTER JOIN dsps_revs ON 
				( dsps.personne_id = dsps_revs.personne_id 
					AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					)
				) 
				LEFT OUTER JOIN detailsdifsocs_revs surendettement_revs ON ( dsps_revs.id = surendettement_revs.dsp_rev_id AND surendettement_revs.difsoc IN ( '0406' ) )
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND surendettement.difsoc IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND surendettement_revs.difsoc IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 


		-- Table difficultés administratives: MAJ
		-- DROP TABLE difficultes_administratives;
		DROP TABLE IF EXISTS difficultes_administratives CASCADE;
		CREATE TABLE difficultes_administratives AS    
			( 
				SELECT 'administratives'::text AS "difficultes_exprimees",
				personnes.id AS personne_id,
				dsps.id AS dsp,
				dsps_revs.id AS dsp_rev 
				FROM personnes
				INNER JOIN dsps on (personnes.id=dsps.personne_id)
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id)
				LEFT OUTER JOIN detailsdifsocs AS administratives ON ( dsps.id = administratives.dsp_id AND administratives.difsoc IN ( '0405' ) )
				LEFT OUTER JOIN dsps_revs ON 
				( dsps.personne_id = dsps_revs.personne_id 
					AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					) 
				)
				LEFT OUTER JOIN detailsdifsocs_revs administratives_revs ON ( dsps_revs.id = administratives_revs.dsp_rev_id AND administratives_revs.difsoc IN ( '0405' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND administratives.difsoc IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND administratives_revs.difsoc IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 

	
		-- Table difficultés linguistiques: MAJ
		-- DROP TABLE difficultes_linguistiques;
		DROP TABLE IF EXISTS difficultes_linguistiques CASCADE;
		CREATE TABLE difficultes_linguistiques AS   
			( 
				SELECT 'linguistiques'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id,
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev 
				FROM personnes
				INNER JOIN dsps on (personnes.id=dsps.personne_id)
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id)
				LEFT OUTER JOIN detailsdifsocs AS linguistiques ON ( dsps.id = linguistiques.dsp_id AND linguistiques.difsoc IN ( '0404' ) )
				LEFT OUTER JOIN dsps_revs ON 
				( 
				dsps.personne_id = dsps_revs.personne_id 
				AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					) 
				)
				LEFT OUTER JOIN detailsdifsocs_revs linguistiques_revs ON ( dsps_revs.id = linguistiques_revs.dsp_rev_id AND linguistiques_revs.difsoc IN ( '0404' ) ) WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND linguistiques.difsoc IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND linguistiques_revs.difsoc IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 

	
		-- Table difficultés de qualifications professionnelles: MAJ
		-- DROP TABLE difficultes_qualification_pro;
		DROP TABLE IF EXISTS difficultes_qualification_pro CASCADE;
		CREATE TABLE difficultes_qualification_pro AS  
			( 
				SELECT 'qualification_professionnelle'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id,
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev 
				FROM personnes
				INNER JOIN dsps on (personnes.id=dsps.personne_id)
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id)
				LEFT OUTER JOIN dsps_revs ON ( 
				dsps.personne_id = dsps_revs.personne_id 
				AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					) 
				)
				LEFT OUTER JOIN dsps AS nivetu ON ( dsps.id = nivetu.id AND nivetu.nivetu IN ( '1206', '1207' ) )
				LEFT OUTER JOIN dsps_revs AS nivetu_revs ON ( dsps_revs.id = nivetu_revs.id AND nivetu_revs.nivetu IN ( '1206', '1207' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND nivetu.nivetu IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND nivetu_revs.nivetu IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 

	
		-- Table difficultés d'accès à l'emploi : MAJ
		-- DROP TABLE difficultes_acces_emploi;
		DROP TABLE IF EXISTS difficultes_acces_emploi CASCADE;
		CREATE TABLE difficultes_acces_emploi AS   
			( 
				SELECT 'acces_emploi'::text AS "difficultes_exprimees",
				personnes.id AS personne_id, 
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev 
				FROM personnes
				INNER JOIN dsps on (personnes.id=dsps.personne_id)
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id)
				LEFT OUTER JOIN dsps_revs ON ( 
				dsps.personne_id = dsps_revs.personne_id 
				AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					) 
				)
				LEFT OUTER JOIN dsps AS topengdemarechemploi ON ( dsps.id = topengdemarechemploi.id AND topengdemarechemploi.topengdemarechemploi IN ( '0' ) )
				LEFT OUTER JOIN dsps_revs AS topengdemarechemploi_revs ON ( dsps_revs.id = topengdemarechemploi_revs.id AND topengdemarechemploi_revs.topengdemarechemploi IN ( '0' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND topengdemarechemploi.topengdemarechemploi IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND topengdemarechemploi_revs.topengdemarechemploi IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
		; 

	
		-- Table difficultés "autres" : MAJ
		-- DROP TABLE difficultes_autres;
		DROP TABLE IF EXISTS difficultes_autres CASCADE;
		CREATE TABLE difficultes_autres AS   
			( 
				SELECT 'autres'::text AS "difficultes_exprimees", 
				personnes.id AS personne_id, 
				dsps.id AS dsp, 
				dsps_revs.id AS dsp_rev 
				FROM personnes
				INNER JOIN dsps on (personnes.id=dsps.personne_id)
				INNER JOIN rendezvous ON (dsps.personne_id = rendezvous.personne_id)
				LEFT OUTER JOIN detailsaccosocindis AS detailsaccosocindis ON ( dsps.id = detailsaccosocindis.dsp_id AND detailsaccosocindis.nataccosocindi IN ( '0420' ) )
				LEFT OUTER JOIN dsps_revs ON 
				( dsps.personne_id = dsps_revs.personne_id 
				AND (dsps_revs.personne_id, dsps_revs.id) IN 
					( 
					SELECT personne_id, MAX(dsps_revs.id) 
					FROM dsps_revs 
					GROUP BY personne_id
					) 
				)
				LEFT OUTER JOIN detailsaccosocindis_revs detailsaccosocindis_revs ON ( dsps_revs.id = detailsaccosocindis_revs.dsp_rev_id AND detailsaccosocindis_revs.nataccosocindi IN ( '0420' ) ) 
				WHERE 
				-- si pas de DSP MAJ on prend la DSP CAF 
				( (dsps_revs.id IS NULL AND detailsaccosocindis.nataccosocindi IS NOT NULL) OR (dsps_revs.id IS NOT NULL AND detailsaccosocindis_revs.nataccosocindi IS NOT NULL) ) 
				-- pour la structure referente X (eventuellement) 
				-- De plus, on restreint les structures referentes a celles qui apparaissent dans le select 
				AND structurereferente_id IN ( 19,24,114,11,10,13,17,1,21,15,31,29,33,32,70,41,40,45,43,61,62,63,64,27,38,65,66,69,67,68,7,71,72,73 ) 
			)
			;


		-- suivi_durant_periode_dd : suivi_periode + DD_periode
		-- DROP TABLE suivi_durant_periode_dd_1;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_1 CASCADE;
		CREATE TABLE suivi_durant_periode_dd_1 AS
			SELECT DISTINCT 
				personnes.id AS personne_id, 
				personnes.foyer_id,
				personnes.nom AS "Nom", 
				personnes.prenom AS "Prenom", 
				CASE 
					WHEN personnes.sexe='1' then 'homme' 
					WHEN personnes.sexe='2' then 'femme' end as "Sexe", 
				personnes.dtnai AS "Date de naissance",
				suivi_durant_periode.rdv_id,
				suivi_durant_periode.daterdv,
				suivi_durant_periode.referent_id,
				structuresreferentes.id AS id, 
				pdv_format_export.pdv,
				pdv_format_export.plaine_co
				
			FROM personnes
				-- D1 durant la periode traité
				INNER JOIN suivi_durant_periode ON (suivi_durant_periode.personne_id = personnes.id)
				INNER JOIN questionnairesd1pdvs93 ON (questionnairesd1pdvs93.id=suivi_durant_periode.id)
				INNER JOIN situationsallocataires ON (questionnairesd1pdvs93.situationallocataire_id=situationsallocataires.id)
				INNER JOIN rendezvous ON (rendezvous.id = suivi_durant_periode.rendezvous_id )
				-- structure référente D1
				LEFT OUTER JOIN structuresreferentes ON (rendezvous.structurereferente_id = structuresreferentes.id)
				LEFT OUTER JOIN administration.pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
				LEFT OUTER JOIN dd_durant_periode ON (dd_durant_periode.personne_id=personnes.id)
				
			WHERE 
				--Soit DD dans histo droit
				dd_durant_periode.personne_id IS NOT NULL
				OR
				-- Soit DD dans D1
				situationsallocataires.toppersdrodevorsa='1'
		;

		
		-- DROP TABLE suivi_durant_periode_dd_1bis;
		--SELECT * FROM suivi_durant_periode_dd_1bis;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_1bis CASCADE;
		CREATE TABLE suivi_durant_periode_dd_1bis AS
		(
			SELECT DISTINCT suivi_durant_periode_dd_1.*,
				/*adresses.numvoie AS "Numero de voie",
				adresses.libtypevoie AS "Type de voie",
				adresses.nomvoie AS "Nom de voie", 
				adresses.codepos AS "CP",
				adresses.nomcom,
				case 
				when adresses.nomcom='AUBERVILLIERS' THEN 'AUBERVILLIERS'
				when adresses.nomcom='AULNAY SOUS BOIS' THEN 'AULNAY SOUS BOIS'
				when adresses.nomcom='BAGNOLET' THEN 'BAGNOLET'
				when adresses.nomcom IN ('BOBIGNY', 'BOBIGNY CEDEX') THEN 'BOBIGNY'
				when adresses.nomcom='BONDY' THEN 'BONDY'
				when adresses.nomcom='CLICHY SOUS BOIS' THEN 'CLICHY SOUS BOIS'
				when adresses.nomcom='COUBRON' THEN 'COUBRON'
				when adresses.nomcom IN ('DRANCY','DRANCY CEDEX') THEN 'DRANCY'
				when adresses.nomcom='DUGNY' THEN 'DUGNY'
				when adresses.nomcom='EPINAY SUR SEINE' THEN 'EPINAY SUR SEINE'
				when adresses.nomcom IN ('GAGNY','GAGNY CEDEX') THEN 'GAGNY'
				when adresses.nomcom='GOURNAY SUR MARNE' THEN 'GOURNAY SUR MARNE'
				when adresses.nomcom IN ('L ILE ST DENIS','ILE ST DENIS') THEN 'L ILE ST DENIS'
				when adresses.nomcom='LA COURNEUVE' THEN 'LA COURNEUVE'
				when adresses.nomcom IN ('LA PLAINE ST DENIS','SAINT DENIS','ST DENIS','ST DENIS CEDEX','LA PLAINE ST DENIS CEDEX') THEN 'SAINT DENIS'
				when adresses.nomcom='LE BLANC MESNIL' THEN 'LE BLANC MESNIL'
				when adresses.nomcom='LE BOURGET' THEN 'LE BOURGET'
				when adresses.nomcom='LE PRE ST GERVAIS' THEN 'LE PRE ST GERVAIS'
				when adresses.nomcom='LE RAINCY' THEN 'LE RAINCY'
				when adresses.nomcom='LES LILAS' THEN 'LES LILAS'
				when adresses.nomcom IN ('LES PAVILLONS SOUS BOIS','PAVILLONS SOUS BOIS') THEN 'LES PAVILLONS SOUS BOIS'
				when adresses.nomcom='LIVRY GARGAN' THEN 'LIVRY GARGAN'
				when adresses.nomcom='MONTFERMEIL' THEN 'MONFERMEIL'
				when adresses.nomcom IN ('MONTREUIL','MONTREUIL CEDEX') THEN 'MONTREUIL'
				when adresses.nomcom='NEUILLY PLAISANCE' THEN 'NEUILLY PLAISANCE'
				when adresses.nomcom='NEUILLY SUR MARNE' THEN 'NEUILLY SUR MARNE'
				when adresses.nomcom='NOISY LE GRAND' THEN 'NOISY LE GRAND'
				when adresses.nomcom='NOISY LE GRAND CEDEX' THEN 'NOISY LE GRAND'
				when adresses.nomcom='NOISY LE SEC' THEN 'NOISY LE SEC'
				when adresses.nomcom='PANTIN' THEN 'PANTIN'
				when adresses.nomcom IN ('PIERREFITTE','PIERREFITTE SUR SEINE') THEN 'PIERREFITTE SUR SEINE'
				when adresses.nomcom='ROMAINVILLE' THEN 'ROMAINVILLE'
				when adresses.nomcom IN ('ROSNY SOUS BOIS','ROSNY SOUS BOIS CEDEX') THEN 'ROSNY SOUS BOIS'
				when adresses.nomcom IN ('SAINT OUEN','ST OUEN') THEN 'SAINT OUEN'
				when adresses.nomcom='SEVRAN' THEN 'SEVRAN'
				when adresses.nomcom='STAINS' THEN 'STAINS'
				when adresses.nomcom='TREMBLAY EN FRANCE' THEN 'TREMBLAY EN FRANCE'
				when adresses.nomcom='TREMBLAY EN FRANCE CEDEX' THEN 'TREMBLAY EN FRANCE'
				when adresses.nomcom='VAUJOURS' THEN 'VAUJOURS'
				when adresses.nomcom='VILLEMOMBLE' THEN 'VILLEMOMBLE'
				when adresses.nomcom='VILLEPINTE' THEN 'VILLEPINTE'
				when adresses.nomcom='VILLETANEUSE' THEN 'VILLETANEUSE'
				when (adresses.codepos < '93000' or adresses.codepos >= '94000') THEN 'Z_Hors_93'
				END AS "Ville domicile", */
				dossiers.matricule AS "Matricule CAF", 
				dossiers.dtdemrsa AS "Date demande RSA", 
				dossiers.dtdemrmi AS "Date demande RMI",
				
				-- Situation familiale (MAJ après réunion site pilote)
				CASE 
					WHEN foyers.sitfam='ABA' then 'DISPARU (JUGEMENT D ABSENCE)'
					WHEN foyers.sitfam='CEL' then 'CELIBATAIRE'
					WHEN foyers.sitfam='DIV' then 'DIVORCE'
					WHEN foyers.sitfam='ISO' then 'ISOLEMENT APRES VIE MARITALE OU PACS'
					WHEN foyers.sitfam='MAR' then 'MARIAGE'
					WHEN foyers.sitfam='PAC' then 'PACS'
					WHEN foyers.sitfam='RPA' then 'REPRISE VIE COMMUNE SUR PACS'
					WHEN foyers.sitfam='RVC' then 'REPRISE VIE MARITALE'
					WHEN foyers.sitfam='RVM' then 'REPRISE MARIAGE'
					WHEN foyers.sitfam='SEF' then 'SEPARATION DE FAIT'
					WHEN foyers.sitfam='SEL' then 'SEPARATION LEGALE'
					WHEN foyers.sitfam='VEU' then 'VEUVAGE'
					WHEN foyers.sitfam='VIM' then 'VIE MARITALE'
					end as "Situation familiale",
				foyers.ddsitfam AS "Ds cette situation familiale depuis le",
				
				-- Nb d'enfants à charge (MAJ après réunion site pilote)
				detailsdroitsrsa.nbenfautcha AS "Nb enf et autre personnes à charge"
			FROM
			suivi_durant_periode_dd_1
			INNER JOIN personnes ON (personnes.id=suivi_durant_periode_dd_1.personne_id)
			LEFT OUTER JOIN foyers ON (personnes.foyer_id = foyers.id)
			LEFT OUTER JOIN dossiers ON (foyers.dossier_id = dossiers.id)
			LEFT OUTER JOIN detailsdroitsrsa ON (detailsdroitsrsa.dossier_id = dossiers.id) -- MAJ
			
			/*-- Adresses (demande PDV annulée suite à la r° site pilote du 16/11/2015)
			LEFT OUTER JOIN adressesfoyers ON (adressesfoyers.foyer_id = foyers.id AND adressesfoyers.rgadr ='01') -- MAJ
			LEFT OUTER JOIN adresses ON (adressesfoyers.adresse_id = adresses.id) -- MAJ*/
		)
		;
		
		
		-- DROP TABLE suivi_durant_periode_dd_2;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_2 CASCADE;
		CREATE TABLE suivi_durant_periode_dd_2 AS
		(
			SELECT DISTINCT suivi_durant_periode_dd_1bis.*,		
			-- Infos sur le référent (MAJ après réunion site pilote)
			--referents.id AS referent_id,
			referents.qual AS "Civilite referent", 
			referents.nom AS "Nom referent", 
			referents.prenom AS "Prenom referent", 
			referents.fonction AS "Fonction referent"
			--personnes_referents.dddesignation AS "Date debut designation referent", 
			--personnes_referents.dfdesignation AS "Date fin designation referent"
			FROM
			suivi_durant_periode_dd_1bis
			-- avec infos sur le dernier référent de parcours
			LEFT OUTER JOIN referents ON (suivi_durant_periode_dd_1bis.referent_id = referents.id)--MAJ
		)
		;


		-- DROP TABLE suivi_durant_periode_dd_3;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_3 CASCADE;
		CREATE TABLE suivi_durant_periode_dd_3 AS
		(
			SELECT DISTINCT suivi_durant_periode_dd_2.*,
			-- Date du 1er RDV en N-1 (MAJ après réunion site pilote)
			premier_rdv_indiv_honore_annee_prec.daterdv AS "Date 1er RDV indiv honore N-1",
			-- Date du 1er RDV en N (MAJ après réunion site pilote)
			premier_rdv_indiv_honore_periode.daterdv AS "Date 1er RDV indiv honore N",
			-- Date du tout 1er RDV (MAJ après réunion site pilote)
			tout_premier_rdv_indiv_honore.daterdv AS "Date tout 1er RDV indiv honore"
			
			FROM 
			suivi_durant_periode_dd_2
			-- avec infos sur les rdvs
			LEFT OUTER JOIN premier_rdv_indiv_honore_annee_prec ON (premier_rdv_indiv_honore_annee_prec.personne_id=suivi_durant_periode_dd_2.personne_id)-- MAJ
			LEFT OUTER JOIN premier_rdv_indiv_honore_periode ON (premier_rdv_indiv_honore_periode.personne_id=suivi_durant_periode_dd_2.personne_id)-- MAJ
			LEFT OUTER JOIN tout_premier_rdv_indiv_honore ON (tout_premier_rdv_indiv_honore.personne_id=suivi_durant_periode_dd_2.personne_id)-- MAJ
		)
		;
		
		
		-- DROP TABLE suivi_durant_periode_dd_4;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_4 CASCADE;
		CREATE TABLE suivi_durant_periode_dd_4 AS
		(
			SELECT DISTINCT suivi_durant_periode_dd_3.*,
			-- Infos sur le dernier CER validé en N (MAJ après réunion site pilote)
			dernier_cer_valide_N.dd_ci AS "Date debut dernier CER valide en N", 
			dernier_cer_valide_N.df_ci AS "Date fin dernier CER valide en N",
			dernier_cer_valide_N.duree AS "Duree dernier CER valide en N", 
			dernier_cer_valide_N.datesignature AS "Date signature dernier CER valide en N", 
			dernier_cer_valide_N.datevalidation_ci AS "Date validation du dernier CER valide en N", 
			dernier_cer_valide_N.sujetscers93_autres AS "Sujet_autres dernier CER valide en N",
			dernier_cer_valide_N.sujetscers93_formation AS "Sujet_formation dernier CER valide en N",
			dernier_cer_valide_N.sujetscers93_sante AS "Sujet_sante dernier CER valide en N",
			dernier_cer_valide_N.sujetscers93_autonomie_sociale AS "Sujet_aut_soc dernier CER valide en N",
			dernier_cer_valide_N.sujetscers93_logement AS "Sujet_logement dernier CER valide en N",
			dernier_cer_valide_N.sujetscers93_emploi AS "Sujet_emploi dernier CER valide en N"
			FROM suivi_durant_periode_dd_3
			-- Infos sur
			LEFT OUTER JOIN dernier_cer_valide_N ON ( dernier_cer_valide_N.personne_id = suivi_durant_periode_dd_3.personne_id)
		)
		;

		
		-- DROP TABLE suivi_durant_periode_dd_5a;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_5a CASCADE;
		CREATE TABLE suivi_durant_periode_dd_5a AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_4.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_sante."difficultes_exprimees" AS "Difficulte sante?"
			FROM suivi_durant_periode_dd_4
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_sante ON (difficultes_sante.personne_id=suivi_durant_periode_dd_4.personne_id)
		)
		;
		
		
		-- DROP TABLE suivi_durant_periode_dd_5b;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_5b CASCADE;
		CREATE TABLE suivi_durant_periode_dd_5b AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5a.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_logement."difficultes_exprimees" AS "Difficulte logement?"
			FROM suivi_durant_periode_dd_5a
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_logement ON (difficultes_logement.personne_id=suivi_durant_periode_dd_5a.personne_id)
		)
		;

		
		-- DROP TABLE suivi_durant_periode_dd_5c;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_5c CASCADE;
		CREATE TABLE suivi_durant_periode_dd_5c AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5b.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_familial."difficultes_exprimees" AS "Difficulte familiale?"
			FROM suivi_durant_periode_dd_5b
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_familial ON (difficultes_familial.personne_id=suivi_durant_periode_dd_5b.personne_id)
		)
		;
		
			
		-- DROP TABLE suivi_durant_periode_dd_5d;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_5d CASCADE;
		CREATE TABLE suivi_durant_periode_dd_5d AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5c.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_mode_garde."difficultes_exprimees" AS "Difficulte mode de garde?"
			FROM suivi_durant_periode_dd_5c
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_mode_garde ON (difficultes_mode_garde.personne_id=suivi_durant_periode_dd_5c.personne_id)
		)
		;

			
		-- DROP TABLE suivi_durant_periode_dd_5e;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_5e CASCADE;
		CREATE TABLE suivi_durant_periode_dd_5e AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5d.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_surendettement."difficultes_exprimees" AS "Difficulte surrendettement?"
			FROM suivi_durant_periode_dd_5d
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_surendettement ON (difficultes_surendettement.personne_id=suivi_durant_periode_dd_5d.personne_id)
		)
		;
		
			
		-- DROP TABLE suivi_durant_periode_dd_5f;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_5f CASCADE;
		CREATE TABLE suivi_durant_periode_dd_5f AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5e.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_administratives."difficultes_exprimees" AS "Difficulte administrative?"
			FROM suivi_durant_periode_dd_5e
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_administratives ON (difficultes_administratives.personne_id=suivi_durant_periode_dd_5e.personne_id)
		)
		;

			
		-- DROP TABLE suivi_durant_periode_dd_5g;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_5g CASCADE;
		CREATE TABLE suivi_durant_periode_dd_5g AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5f.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_linguistiques."difficultes_exprimees" AS "Difficulte linguistique?"
			FROM suivi_durant_periode_dd_5f
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_linguistiques ON (difficultes_linguistiques.personne_id=suivi_durant_periode_dd_5f.personne_id)
		)
		;
		
			
		-- DROP TABLE suivi_durant_periode_dd_5h;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_5h CASCADE;
		CREATE TABLE suivi_durant_periode_dd_5h AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5g.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_qualification_pro."difficultes_exprimees" AS "Difficulte qualification professionnelle?"
			FROM suivi_durant_periode_dd_5g
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_qualification_pro ON (difficultes_qualification_pro.personne_id=suivi_durant_periode_dd_5g.personne_id)
		)
		;

			
		-- DROP TABLE suivi_durant_periode_dd_5i;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_5i CASCADE;
		CREATE TABLE suivi_durant_periode_dd_5i AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5h.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_acces_emploi."difficultes_exprimees" AS "Difficulte acces emploi?"
			FROM suivi_durant_periode_dd_5h
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_acces_emploi ON (difficultes_acces_emploi.personne_id=suivi_durant_periode_dd_5h.personne_id)
		)
		;
		
			
		-- DROP TABLE suivi_durant_periode_dd_5j;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_5j CASCADE;
		CREATE TABLE suivi_durant_periode_dd_5j AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5i.*,
			-- Infos sur les pb DSP/TDB B3 (MAJ après réunion site pilote)
			difficultes_autres."difficultes_exprimees" AS "Autres difficultes?"
			FROM suivi_durant_periode_dd_5i
			-- Infos sur les difficultés rencontrées
			LEFT OUTER JOIN difficultes_autres ON (difficultes_autres.personne_id=suivi_durant_periode_dd_5i.personne_id)
		)
		;


		-- DROP TABLE suivi_durant_periode_dd_6;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_6 CASCADE;
		CREATE TABLE suivi_durant_periode_dd_6 AS
		(
			SELECT DISTINCT
			suivi_durant_periode_dd_5j.*,
			questionnairesd1pdvs93.id AS questionnairesd1pdvs93_id,
			-- Tous les champs du questionnaire D1 (MAJ après réunion site pilote)
			questionnairesd1pdvs93.marche_travail AS "D1_Ligne2_Marche de l''emploi",
			CASE 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtnai ) ) BETWEEN 0 AND 14 THEN '0_14' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtnai ) ) BETWEEN 15 AND 24 THEN '15_24' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtnai ) ) BETWEEN 25 AND 44 THEN '25_44' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtnai ) ) BETWEEN 45 AND 54 THEN '45_54' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtnai ) ) BETWEEN 55 AND 64 THEN '55_64' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtnai ) ) BETWEEN 65 AND 999 THEN '65_999' 
				ELSE 'NC' 
			END AS "D1_Ligne3_Tranche d'age", 
				 
			questionnairesd1pdvs93.vulnerable AS "D1_Ligne4_Groupes vulnerables", 
				
			CASE
				WHEN questionnairesd1pdvs93.nivetu='1201' THEN 'Niveau_I/II_Enseignement_superieur'
				WHEN questionnairesd1pdvs93.nivetu='1202' THEN 'Niveau_III_Bac+2'
				WHEN questionnairesd1pdvs93.nivetu='1203' THEN 'Niveau_IV_Bac/Equivalent'
				WHEN questionnairesd1pdvs93.nivetu='1204' THEN 'Niveau_V_CAP/BEP'
				WHEN questionnairesd1pdvs93.nivetu='1205' THEN 'Niveau_Vbis_Fin_scolarite_obligatoire'
				WHEN questionnairesd1pdvs93.nivetu='1206' THEN 'Niveau_VI_Pas_de_niveau'
				WHEN questionnairesd1pdvs93.nivetu='1207' THEN 'Niveau_VII_Jamais_scolarise'
			END AS "D1_Ligne5_Niveau d'instruction",
					
			questionnairesd1pdvs93.categorie_sociopro AS "D1_Ligne6_Professions et CSP",
				   
			questionnairesd1pdvs93.autre_caracteristique AS "D1_Ligne7_Autres caracteristiques", 
				
			CASE 
				WHEN ( situationsallocataires.natpf_socle = '1' AND situationsallocataires.natpf_activite = '1' AND situationsallocataires.natpf_majore = '1' ) THEN 'majore' 
				WHEN ( situationsallocataires.natpf_socle = '1' AND situationsallocataires.natpf_activite = '1' AND situationsallocataires.natpf_majore = '0' ) THEN 'socle_activite' 
				WHEN ( situationsallocataires.natpf_socle = '1' AND situationsallocataires.natpf_activite = '0' AND situationsallocataires.natpf_majore = '1' ) THEN 'majore' 
				WHEN ( situationsallocataires.natpf_socle = '1' AND situationsallocataires.natpf_activite = '0' AND situationsallocataires.natpf_majore = '0' ) THEN 'socle' 
				WHEN ( situationsallocataires.natpf_socle = '0' AND situationsallocataires.natpf_activite = '1' AND situationsallocataires.natpf_majore = '1' ) THEN 'majore' 
				WHEN ( situationsallocataires.natpf_socle = '0' AND situationsallocataires.natpf_activite = '1' AND situationsallocataires.natpf_majore = '0' ) THEN 'NC' 
				WHEN ( situationsallocataires.natpf_socle = '0' AND situationsallocataires.natpf_activite = '0' AND situationsallocataires.natpf_majore = '1' ) THEN 'NC' 
				WHEN ( situationsallocataires.natpf_socle = '0' AND situationsallocataires.natpf_activite = '0' AND situationsallocataires.natpf_majore = '0' ) THEN 'NC' 
				ELSE 'NC' 
			END AS "D1_Ligne8_Type de beneficiaires", 
				  
			CASE 
				WHEN situationsallocataires.nati='F' THEN 'Francaise' 
				WHEN situationsallocataires.nati='C' THEN 'Union_Europeenne' 
				WHEN situationsallocataires.nati='A' THEN 'Hors_Union_Europeenne'
				ELSE 'NC' 
			END AS "D1_Ligne9_Nationalite", 
				  
			CASE 
				WHEN situationsallocataires.sitfam IN ('CEL', 'DIV', 'ISO', 'SEF', 'SEL', 'VEU') AND situationsallocataires.nbenfants = 0 THEN 'Isole_sans_enfant' 
				WHEN situationsallocataires.sitfam IN ('CEL', 'DIV', 'ISO', 'SEF', 'SEL', 'VEU') AND situationsallocataires.nbenfants > 0 THEN 'Isole_avec_enfant' 
				WHEN situationsallocataires.sitfam IN ('MAR', 'PAC', 'RPA', 'RVC', 'RVM', 'VIM') AND situationsallocataires.nbenfants = 0 THEN 'En_couple_sans_enfant' 
				WHEN situationsallocataires.sitfam IN ('MAR', 'PAC', 'RPA', 'RVC', 'RVM', 'VIM') AND situationsallocataires.nbenfants > 0 THEN 'En_couple_avec_enfant' 
				ELSE 'NC' 
			END AS "D1_Ligne10_Situation familiale",
				  
			questionnairesd1pdvs93.conditions_logement AS "D1_Ligne11_Condition de logement", 
			
			CASE
				WHEN questionnairesd1pdvs93.inscritpe='1' THEN 'Inscrits'
				WHEN questionnairesd1pdvs93.inscritpe='0' THEN 'Non_inscrits'
				ELSE 'NC' 
			END AS "D1_Ligne12_Inscription Pole Emploi",
				 
			CASE 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtdemrsa ) ) BETWEEN 0 AND 0 THEN '0_0' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtdemrsa ) ) BETWEEN 1 AND 2 THEN '1_2' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtdemrsa ) ) BETWEEN 3 AND 5 THEN '3_5' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtdemrsa ) ) BETWEEN 6 AND 8 THEN '6_8' 
				WHEN EXTRACT( YEAR FROM AGE(df_periode, situationsallocataires.dtdemrsa ) ) BETWEEN 9 AND 999 THEN '9_999' 
				ELSE 'NC' 
			END AS "D1_Ligne13_Anciennete ds le dispositif (en annees)",
				  
			CASE WHEN questionnairesd1pdvs93.nivetu='1207' THEN 'jamais_scolarise' END AS "D1_Ligne14_Non scolarise",
				  
			CASE WHEN questionnairesd1pdvs93.diplomes_etrangers='1' THEN 'oui' END AS "D1_Ligne15_Diplomes etrangers non reconnus en France"

			FROM
			questionnairesd1pdvs93
			INNER JOIN situationsallocataires ON (questionnairesd1pdvs93.situationallocataire_id=situationsallocataires.id)
			INNER JOIN suivi_durant_periode_dd_5j  ON (questionnairesd1pdvs93.personne_id=suivi_durant_periode_dd_5j.personne_id )

			WHERE
			questionnairesd1pdvs93.id IN (SELECT DISTINCT id FROM suivi_durant_periode)
			AND suivi_durant_periode_dd_5j.rdv_id =questionnairesd1pdvs93.rendezvous_id
			ORDER BY personne_id, "Nom", "Prenom"
		)
		;

		
		--DROP TABLE suivi_durant_periode_dd_7;
		DROP TABLE IF EXISTS suivi_durant_periode_dd_7 CASCADE;
		CREATE TABLE suivi_durant_periode_dd_7 AS
		(
			SELECT DISTINCT suivi_durant_periode_dd_6.*,
				-- Tous les champs du questionnaire D2 (MAJ après réunion site pilote)
				questionnairesd2pdvs93.situationaccompagnement AS "D2_Situation accompagnement",
				sortiesaccompagnementsd2pdvs93."name" AS "D2_Type de sortie accompagnement",
				questionnairesd2pdvs93.chgmentsituationadmin AS "D2_Type de changement de situation"
		
			FROM 
			suivi_durant_periode_dd_6
			-- avec ou sans infos D2
			LEFT OUTER JOIN questionnairesd2pdvs93 ON (questionnairesd2pdvs93.questionnaired1pdv93_id=suivi_durant_periode_dd_6.questionnairesd1pdvs93_id)
			LEFT OUTER JOIN sortiesaccompagnementsd2pdvs93 ON (questionnairesd2pdvs93.sortieaccompagnementd2pdv93_id = sortiesaccompagnementsd2pdvs93.id)-- MAJ
		)
		;
		
		
		-- recond_durant_periode : suivi_durant_periode_dd + suivis_annee_prec_dd
		DROP TABLE IF EXISTS recond_durant_periode CASCADE;
		CREATE TABLE recond_durant_periode AS
			SELECT DISTINCT suivi_durant_periode_dd_7.*,
				CASE WHEN suivi_durant_periode_dd_7.personne_id IS NOT NULL THEN 'Reconduit' 
				END AS suivi	
			FROM suivi_durant_periode_dd_7 
				INNER JOIN suivis_annee_prec_dd ON (suivi_durant_periode_dd_7.personne_id=suivis_annee_prec_dd.personne_id)
		;
		
		
		-- nouv_durant_periode : suivi_durant_periode_dd + NOT suivis_annee_prec_dd
		DROP TABLE IF EXISTS nouv_durant_periode CASCADE;
		CREATE TABLE nouv_durant_periode AS
			SELECT DISTINCT suivi_durant_periode_dd_7.*,
				CASE WHEN suivi_durant_periode_dd_7.personne_id IS NOT NULL THEN 'Nouveau' 
				END AS suivi	
			FROM suivi_durant_periode_dd_7 
			WHERE personne_id NOT IN ( 
				SELECT suivis_annee_prec_dd.personne_id 
				FROM suivis_annee_prec_dd 
			)
		;
		
		
		-- suivis_pdv_durant_periode : Reconduits_periode + Nouveau_periode
		DROP TABLE IF EXISTS suivis_pdv_durant_periode CASCADE;
		CREATE TABLE suivis_pdv_durant_periode AS
			SELECT DISTINCT * 
			FROM recond_durant_periode 
			UNION
			SELECT DISTINCT *  
			FROM nouv_durant_periode
		;
		

		-- strucref_type_b1b2 : types de structure referente
		DROP TABLE IF EXISTS strucref_type_b1b2 CASCADE;
		CREATE TABLE strucref_type_b1b2 AS
		(
			SELECT DISTINCT *, 'Le Pôle Emploi' AS type_struc_orient_b1b2
			FROM structuresreferentes
			WHERE (structuresreferentes.lib_struc LIKE 'Pole Emploi%' OR structuresreferentes.lib_struc LIKE 'Pôle Emploi%')
		)
		UNION
		(
			SELECT DISTINCT * , 'Le Service Social' AS type_struc_orient_b1b2
			FROM structuresreferentes
			WHERE (structuresreferentes.lib_struc LIKE 'Service Social%')
		)
		UNION
		(
			SELECT DISTINCT * , 'Une Asso conventionnée' as type_struc_orient_b1b2
			FROM structuresreferentes
			WHERE (structuresreferentes.lib_struc IS NOT NULL
					AND structuresreferentes.lib_struc NOT LIKE 'Service Social%'
					AND structuresreferentes.lib_struc NOT LIKE 'Pole Emploi%'
					AND structuresreferentes.lib_struc NOT LIKE 'Pôle Emploi%'
					AND structuresreferentes.lib_struc NOT LIKE '%Projet de Ville%'
					AND structuresreferentes.lib_struc NOT LIKE 'Centre Communal d Action Sociale Le Raincy')
		)
		UNION
		(
			SELECT DISTINCT * , 'Un PDV' as type_struc_orient_b1b2
			FROM structuresreferentes
			WHERE (structuresreferentes.lib_struc IS NOT NULL
				AND structuresreferentes.lib_struc LIKE '%Projet de Ville%'
			)
		)
		;


		-- partie_3 : suivis_pdv_periode AVEC : RAPATRIER LA DERNIERE ORIENTATION et LA CODER SELON LA TYPO B1/B2 (PARTIE 3.B);
		DROP TABLE IF EXISTS partie_3 CASCADE;
		CREATE TABLE partie_3 AS
			SELECT DISTINCT 
			suivis_pdv_durant_periode.personne_id,
			suivis_pdv_durant_periode.foyer_id,
			suivis_pdv_durant_periode."Nom",
			suivis_pdv_durant_periode."Prenom",
			suivis_pdv_durant_periode."Sexe",
			suivis_pdv_durant_periode."Date de naissance", 
			suivis_pdv_durant_periode."Matricule CAF",
			suivis_pdv_durant_periode."Date demande RSA", 
			suivis_pdv_durant_periode."Date demande RMI", 
			suivis_pdv_durant_periode."Situation familiale", 
			suivis_pdv_durant_periode."Ds cette situation familiale depuis le", 
			suivis_pdv_durant_periode."Nb enf et autre personnes à charge",
			suivis_pdv_durant_periode."Civilite referent", 
			suivis_pdv_durant_periode."Nom referent", 
			suivis_pdv_durant_periode."Prenom referent", 
			suivis_pdv_durant_periode."Fonction referent", 
			suivis_pdv_durant_periode."Date 1er RDV indiv honore N-1", 
			suivis_pdv_durant_periode."Date 1er RDV indiv honore N", 
			suivis_pdv_durant_periode."Date tout 1er RDV indiv honore",
			suivis_pdv_durant_periode."Date debut dernier CER valide en N",
			suivis_pdv_durant_periode."Date fin dernier CER valide en N", 
			suivis_pdv_durant_periode."Duree dernier CER valide en N", 
			suivis_pdv_durant_periode."Date signature dernier CER valide en N", 
			suivis_pdv_durant_periode."Date validation du dernier CER valide en N", 
			suivis_pdv_durant_periode."Sujet_formation dernier CER valide en N", 
			suivis_pdv_durant_periode."Sujet_sante dernier CER valide en N",
			suivis_pdv_durant_periode."Sujet_aut_soc dernier CER valide en N", 
			suivis_pdv_durant_periode."Sujet_logement dernier CER valide en N", 
			suivis_pdv_durant_periode."Sujet_emploi dernier CER valide en N",
			suivis_pdv_durant_periode."Sujet_autres dernier CER valide en N",
			suivis_pdv_durant_periode."Difficulte sante?", 
			suivis_pdv_durant_periode."Difficulte logement?",
			suivis_pdv_durant_periode."Difficulte familiale?", 
			suivis_pdv_durant_periode."Difficulte mode de garde?", 
			suivis_pdv_durant_periode."Difficulte surrendettement?", 
			suivis_pdv_durant_periode."Difficulte administrative?", 
			suivis_pdv_durant_periode."Difficulte linguistique?", 
			suivis_pdv_durant_periode."Difficulte qualification professionnelle?", 
			suivis_pdv_durant_periode."Difficulte acces emploi?", 
			suivis_pdv_durant_periode."Autres difficultes?",
			suivis_pdv_durant_periode.daterdv AS "Date de RDV D1", 
			suivis_pdv_durant_periode.id,
			suivis_pdv_durant_periode.pdv AS "PDV de D1",  
			suivis_pdv_durant_periode.plaine_co,
			suivis_pdv_durant_periode."D1_Ligne2_Marche de l''emploi",
			suivis_pdv_durant_periode."D1_Ligne3_Tranche d'age", 
			suivis_pdv_durant_periode."D1_Ligne4_Groupes vulnerables", 
			suivis_pdv_durant_periode."D1_Ligne5_Niveau d'instruction",
			suivis_pdv_durant_periode."D1_Ligne6_Professions et CSP", 
			suivis_pdv_durant_periode."D1_Ligne7_Autres caracteristiques",
			suivis_pdv_durant_periode."D1_Ligne8_Type de beneficiaires", 
			suivis_pdv_durant_periode."D1_Ligne9_Nationalite", 
			suivis_pdv_durant_periode."D1_Ligne10_Situation familiale", 
			suivis_pdv_durant_periode."D1_Ligne11_Condition de logement", 
			suivis_pdv_durant_periode."D1_Ligne12_Inscription Pole Emploi",
			suivis_pdv_durant_periode."D1_Ligne13_Anciennete ds le dispositif (en annees)", 
			suivis_pdv_durant_periode."D1_Ligne14_Non scolarise", 
			suivis_pdv_durant_periode."D1_Ligne15_Diplomes etrangers non reconnus en France", 
			suivis_pdv_durant_periode."D2_Situation accompagnement",
			suivis_pdv_durant_periode."D2_Type de sortie accompagnement", 
			suivis_pdv_durant_periode."D2_Type de changement de situation", 
			suivi AS "Type de suivi",
			der_valid_orient_av_fin_periode.date_valid AS "Date de validation orientation",--date_valid_orient,
			CASE 
				WHEN (suivis_pdv_durant_periode.id!=der_valid_orient_av_fin_periode.structurereferente_id AND type_struc_orient_b1b2='Un PDV') THEN 'Un autre PDV'
				WHEN suivis_pdv_durant_periode.id!=der_valid_orient_av_fin_periode.structurereferente_id THEN type_struc_orient_b1b2
				WHEN suivis_pdv_durant_periode.id=der_valid_orient_av_fin_periode.structurereferente_id THEN 'Le PDV'
				WHEN der_valid_orient_av_fin_periode.structurereferente_id IS NULL THEN 'Sans Orientation'
			END AS "Service referent (orientation)" --service_ref_orient
			FROM suivis_pdv_durant_periode
				LEFT OUTER JOIN der_valid_orient_av_fin_periode ON (suivis_pdv_durant_periode.personne_id=der_valid_orient_av_fin_periode.personne_id)
				LEFT OUTER JOIN strucref_type_b1b2 ON (strucref_type_b1b2.id=der_valid_orient_av_fin_periode.structurereferente_id)
			ORDER BY "PDV de D1", "Service referent (orientation)"
		;



		/* ***********************************************
		******* LISTE DES TABLES TEMP partie 4 ************
		*************************************************/

		-- rdv_ind_honore_durant_periode : RDV individuel + honore + durant periode 
		DROP TABLE IF EXISTS rdv_ind_honore_durant_periode CASCADE;
		CREATE TABLE rdv_ind_honore_durant_periode AS
			SELECT DISTINCT rendezvous.*
			FROM rendezvous 
				INNER JOIN typesrdv ON (rendezvous.typerdv_id = typesrdv.id)
				INNER JOIN statutsrdvs ON (rendezvous.statutrdv_id = statutsrdvs.id)
			WHERE EXTRACT (YEAR FROM rendezvous.daterdv)=annee_periode
				AND EXTRACT (MONTH FROM rendezvous.daterdv) BETWEEN 1 AND nb_mois
				AND typesrdv.libelle='Individuel' 
				AND statutsrdvs.libelle= 'honoré' 
		;

		-- nb_rdv_par_personne : suivi_periode + DD_periode + rdv_ind_honore_periode + (SR D1==SR RDV)
		DROP TABLE IF EXISTS nb_rdv_par_personne CASCADE;
		CREATE TABLE nb_rdv_par_personne AS 
			SELECT personnes.id AS personne_id,
				structuresreferentes.id,pdv_format_export.pdv, pdv_format_export.plaine_co,
				COUNT( DISTINCT( rdv_ind_honore_durant_periode.id ) ) AS "count rdv_id"
			FROM personnes
				INNER JOIN suivi_durant_periode ON (suivi_durant_periode.personne_id = personnes.id)
				INNER JOIN questionnairesd1pdvs93 ON (questionnairesd1pdvs93.id=suivi_durant_periode.id)
				INNER JOIN situationsallocataires ON (questionnairesd1pdvs93.situationallocataire_id=situationsallocataires.id)
				INNER JOIN rendezvous ON (rendezvous.id = suivi_durant_periode.rendezvous_id )
				LEFT OUTER JOIN foyers ON (personnes.foyer_id = foyers.id)
				LEFT OUTER JOIN dossiers ON (foyers.dossier_id = dossiers.id)
				-- structure référente du RDV_D1
				LEFT OUTER JOIN structuresreferentes ON (rendezvous.structurereferente_id = structuresreferentes.id)
				LEFT OUTER JOIN administration.pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
				LEFT OUTER JOIN dd_durant_periode ON (dd_durant_periode.personne_id=personnes.id)
				INNER JOIN rdv_ind_honore_durant_periode ON (rdv_ind_honore_durant_periode.personne_id = personnes.id)
			WHERE 	-- SR D1 = SR RDV (?)
				rdv_ind_honore_durant_periode.structurereferente_id=structuresreferentes.id
				AND (
					--Soit DD dans histo droit
					dd_durant_periode.personne_id IS NOT NULL
					OR
					-- Soit DD dans D1
					situationsallocataires.toppersdrodevorsa='1'
				)
			GROUP BY personnes.id,structuresreferentes.id,pdv, pdv_format_export.plaine_co;
		
		-- nb_psn_rdv_periode_d1 : 
		DROP TABLE IF EXISTS nb_psn_rdv_periode_d1 CASCADE;
		CREATE TABLE nb_psn_rdv_periode_d1 AS
		(
			SELECT 	DISTINCT id, pdv,plaine_co,
				CASE 
					WHEN "count rdv_id" BETWEEN 1 AND 3 THEN '1 à 3 RDV honorés' 
					WHEN "count rdv_id" BETWEEN 4 AND 6 THEN '4 à 6 RDV honorés' 
					WHEN "count rdv_id" >= 7 THEN  '7 et + RDV honorés' 
					END AS "Nb de RDV honorés",
				COUNT(*) as "Nb personnes"
			FROM 
				nb_rdv_par_personne
			GROUP BY  id, pdv,plaine_co,"Nb de RDV honorés"
		)
		UNION 
		(
			SELECT DISTINCT id, pdv,plaine_co, 'Total'  AS "Nb de RDV honorés", COUNT(*) AS "Nb personnes"
			FROM 
			(
				SELECT 	personnes.id AS personne_id,
					structuresreferentes.id, pdv,plaine_co,
					COUNT( DISTINCT( rdv_ind_honore_durant_periode.id ) ) AS "count rdv_id"
				FROM personnes
					INNER JOIN suivi_durant_periode ON (suivi_durant_periode.personne_id = personnes.id)
					INNER JOIN questionnairesd1pdvs93 ON (questionnairesd1pdvs93.id=suivi_durant_periode.id)
					INNER JOIN situationsallocataires ON (questionnairesd1pdvs93.situationallocataire_id=situationsallocataires.id)
					INNER JOIN rendezvous ON (rendezvous.id = suivi_durant_periode.rendezvous_id )
					LEFT OUTER JOIN foyers ON (personnes.foyer_id = foyers.id)
					LEFT OUTER JOIN dossiers ON (foyers.dossier_id = dossiers.id)
					-- structure référente du RDV_D1
					LEFT OUTER JOIN structuresreferentes ON (rendezvous.structurereferente_id = structuresreferentes.id)
					LEFT OUTER JOIN administration.pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
					LEFT OUTER JOIN dd_durant_periode ON (dd_durant_periode.personne_id=personnes.id)
					INNER JOIN rdv_ind_honore_durant_periode ON (rdv_ind_honore_durant_periode.personne_id = personnes.id)
				WHERE 	-- SR D1 = SR RDV 
					rdv_ind_honore_durant_periode.structurereferente_id=structuresreferentes.id
					AND (
					--Soit DD dans histo droit
					dd_durant_periode.personne_id IS NOT NULL
					OR
					-- Soit DD dans D1
					situationsallocataires.toppersdrodevorsa='1'
				)
				GROUP BY personnes.id,structuresreferentes.id,pdv, plaine_co
			) AS nb_rdv_par_personne
		GROUP BY id, pdv, plaine_co, "Nb de RDV honorés"
		)
		ORDER BY id, pdv,plaine_co, "Nb de RDV honorés"
		;

		-- rdv_hon_total
		DROP TABLE IF EXISTS rdv_hon_total CASCADE;
		CREATE TABLE rdv_hon_total AS
			SELECT id, pdv, plaine_co,
				0 AS nb_pers_1_3_rdv,
				0 AS nb_pers_4_6_rdv,
				0 AS nb_pers_7_plus_rdv,
				nb_psn_rdv_periode_d1."Nb personnes" AS nb_pers_total_rdv,
				0 AS total_RDV, 
				0 AS dt_rdv_psy
			FROM nb_psn_rdv_periode_d1 
			WHERE "Nb de RDV honorés"='Total';

		-- rdv_hon_1_3
		DROP TABLE IF EXISTS rdv_hon_1_3 CASCADE;
		CREATE TABLE rdv_hon_1_3 AS
		SELECT 	id, pdv,  plaine_co,
			nb_psn_rdv_periode_d1."Nb personnes" AS nb_pers_1_3_rdv,
			0 AS nb_pers_4_6_rdv,
			0 AS nb_pers_7_plus_rdv,
			0 AS nb_pers_total_rdv,
			0 AS total_RDV, 
			0 AS dt_rdv_psy
		FROM nb_psn_rdv_periode_d1 
		WHERE "Nb de RDV honorés"='1 à 3 RDV honorés';

		-- rdv_hon_4_6
		DROP TABLE IF EXISTS rdv_hon_4_6 CASCADE;
		CREATE TABLE rdv_hon_4_6 AS
		SELECT 	id, pdv,  plaine_co,
			0 AS nb_pers_1_3_rdv,
			nb_psn_rdv_periode_d1."Nb personnes" AS nb_pers_4_6_rdv ,
			0 AS  nb_pers_7_plus_rdv,
			0 AS nb_pers_total_rdv,
			0 AS total_RDV, 
			0 AS dt_rdv_psy
		FROM nb_psn_rdv_periode_d1 
		WHERE "Nb de RDV honorés"='4 à 6 RDV honorés';

		-- rdv_hon_7_et_plus
		DROP TABLE IF EXISTS rdv_hon_7_et_plus CASCADE;
		CREATE TABLE rdv_hon_7_et_plus AS
		SELECT	id, pdv,  plaine_co,
			0 AS nb_pers_1_3_rdv,
			0 AS nb_pers_4_6_rdv,
			nb_psn_rdv_periode_d1."Nb personnes" AS nb_pers_7_plus_rdv,
			0 AS nb_pers_total_rdv,
			0 AS total_RDV, 
			0 AS dt_rdv_psy
		FROM nb_psn_rdv_periode_d1 
		WHERE "Nb de RDV honorés"='7 et + RDV honorés';

		-- total_rdv_periode_d1 : 
		DROP TABLE IF EXISTS total_rdv_periode_d1 CASCADE;
		CREATE TABLE total_rdv_periode_d1 AS
		(
			SELECT DISTINCT rdv.*, rdv_psy."Dont Total RDV Psy" FROM (
				SELECT 
					DISTINCT structuresreferentes.id, pdv_format_export.pdv, pdv_format_export.plaine_co, 
					COUNT( DISTINCT( rdv_ind_honore_durant_periode.id ) ) AS "Total RDV"
				FROM personnes
					INNER JOIN suivi_durant_periode ON (suivi_durant_periode.personne_id = personnes.id)
					INNER JOIN questionnairesd1pdvs93 ON (questionnairesd1pdvs93.id=suivi_durant_periode.id)
					INNER JOIN situationsallocataires ON (questionnairesd1pdvs93.situationallocataire_id=situationsallocataires.id)
					INNER JOIN rendezvous ON (rendezvous.id = suivi_durant_periode.rendezvous_id )
					LEFT OUTER JOIN foyers ON (personnes.foyer_id = foyers.id)
					LEFT OUTER JOIN dossiers ON (foyers.dossier_id = dossiers.id)
					LEFT OUTER JOIN structuresreferentes ON (rendezvous.structurereferente_id = structuresreferentes.id)
					LEFT OUTER JOIN administration.pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
					INNER JOIN dd_durant_periode ON (dd_durant_periode.personne_id=personnes.id)
					INNER JOIN rdv_ind_honore_durant_periode ON (rdv_ind_honore_durant_periode.personne_id = personnes.id)
					INNER JOIN referents ON ( rdv_ind_honore_durant_periode.referent_id = referents.id)
				WHERE 	-- SR D1 = SR RDV 
					rdv_ind_honore_durant_periode.structurereferente_id=structuresreferentes.id
					AND (
						--Soit DD dans histo droit
						dd_durant_periode.personne_id IS NOT NULL
						OR
						-- Soit DD dans D1
						situationsallocataires.toppersdrodevorsa='1'
					)
				GROUP BY structuresreferentes.id,pdv, plaine_co
			) AS rdv LEFT OUTER JOIN 
		--union
			(
			SELECT 
				DISTINCT structuresreferentes.id, pdv_format_export.pdv,pdv_format_export.plaine_co, 
				COUNT( DISTINCT( rdv_ind_honore_durant_periode.id ) ) AS "Dont Total RDV Psy"

			FROM personnes
				-- D1 durant la periode traité
				INNER JOIN suivi_durant_periode ON (suivi_durant_periode.personne_id = personnes.id)
				INNER JOIN questionnairesd1pdvs93 ON (questionnairesd1pdvs93.id=suivi_durant_periode.id)
				INNER JOIN situationsallocataires ON (questionnairesd1pdvs93.situationallocataire_id=situationsallocataires.id)
				INNER JOIN rendezvous ON (rendezvous.id = suivi_durant_periode.rendezvous_id )
				LEFT OUTER JOIN foyers ON (personnes.foyer_id = foyers.id)
				LEFT OUTER JOIN dossiers ON (foyers.dossier_id = dossiers.id)
				LEFT OUTER JOIN structuresreferentes ON (rendezvous.structurereferente_id = structuresreferentes.id)
				LEFT OUTER JOIN administration.pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
				INNER JOIN dd_durant_periode ON (dd_durant_periode.personne_id=personnes.id)
				INNER JOIN rdv_ind_honore_durant_periode ON (rdv_ind_honore_durant_periode.personne_id = personnes.id)
				INNER JOIN referents ON ( rdv_ind_honore_durant_periode.referent_id = referents.id)
			WHERE 	-- SR D1 = SR RDV 
				rdv_ind_honore_durant_periode.structurereferente_id=structuresreferentes.id
				AND (
					--Soit DD dans histo droit
					dd_durant_periode.personne_id IS NOT NULL
					OR
					-- Soit DD dans D1
					situationsallocataires.toppersdrodevorsa='1'
				)
				AND fonction= 'Psychologue'
			GROUP BY structuresreferentes.id,pdv, plaine_co
			)  AS rdv_psy ON rdv.pdv=rdv_psy.pdv

		);

		-- total_rdv :
		DROP TABLE IF EXISTS total_rdv CASCADE;
		CREATE TABLE total_rdv AS
			SELECT 	id, pdv, plaine_co,
				0 AS nb_pers_1_3_rdv, 
				0 AS nb_pers_4_6_rdv, 
				0 AS nb_pers_7_plus_rdv,
				0 AS nb_pers_total_rdv,
				total_rdv_periode_d1."Total RDV" AS total_RDV, 
				total_rdv_periode_d1."Dont Total RDV Psy" AS dt_rdv_psy
			FROM total_rdv_periode_d1
		;

		/* ***********************************************
		*************** LISTE DES VUES *******************
		*************************************************/

		/**
		-- EXPORT 1 : Listing Partie 2a
		-- ** PARTIE ORIENTATIONS ** --
		**/
		CREATE OR REPLACE VIEW b1b2_liste_2a AS
			SELECT DISTINCT 
				personnes.id AS personne_id, 
				orient_tot.id,
				orient_tot.pdv AS structure_referente_pdv,
				orient_tot.plaine_co,
				personnes.nom, personnes.prenom, 
				CASE 	WHEN personnes.sexe='1' THEN 'homme' 
					WHEN personnes.sexe='2' THEN 'femme' 
				END AS sexe, 
				personnes.dtnai, 
				dossiers.matricule AS num_CAF,
				valid_orient_durant_periode.date_valid AS date_validation_orientation, 
				valid_orient_durant_periode.origine AS methode_orientation
				FROM orient_tot, foyers, dossiers, personnes 
				INNER JOIN valid_orient_durant_periode ON (personnes.id=valid_orient_durant_periode.personne_id)
			WHERE 
				valid_orient_durant_periode.structurereferente_id=orient_tot.id
				AND foyers.id = personnes.foyer_id
				AND dossiers.id = foyers.dossier_id
				AND pdv IS NOT NULL  
			ORDER BY personnes.id
		;

		/**
		-- EXPORT 2 : Listing Partie 3
		-- PARTIE PERSONNES SUIVIES
		**/
		CREATE OR REPLACE VIEW b1b2_liste_3 AS 
			SELECT DISTINCT *
			FROM partie_3
			ORDER BY "PDV de D1", "Service referent (orientation)"
		;

		/**
		-- EXPORT 3 : Listing Partie 4
		**/
		CREATE OR REPLACE VIEW b1b2_stat_4 AS
			SELECT DISTINCT 
				rdv_hon_1_3.id, rdv_hon_1_3.pdv, 
				rdv_hon_1_3.plaine_co,
				rdv_hon_1_3.nb_pers_1_3_rdv,
				rdv_hon_4_6.nb_pers_4_6_rdv,
				rdv_hon_7_et_plus.nb_pers_7_plus_rdv,
				rdv_hon_total.nb_pers_total_rdv,
				total_rdv.total_rdv,
				total_rdv.dt_rdv_psy
			FROM	rdv_hon_1_3  
				LEFT OUTER JOIN rdv_hon_4_6 ON (rdv_hon_1_3.id=rdv_hon_4_6.id)
				LEFT OUTER JOIN rdv_hon_7_et_plus ON (rdv_hon_1_3.id=rdv_hon_7_et_plus.id)
				LEFT OUTER JOIN rdv_hon_total ON (rdv_hon_1_3.id=rdv_hon_total.id)
				LEFT OUTER JOIN total_rdv ON (rdv_hon_1_3.id=total_rdv.id)
			WHERE rdv_hon_1_3.pdv IS NOT NULL
		;
		
		-- Listing partie 4 : liste des personnes avec leur nb de RDV individuels
		CREATE OR REPLACE VIEW b1b2_liste_4_1 AS
			SELECT DISTINCT personne_id, nom, prenom, dtnai AS "date de naissance", matricule, 
			pdv, plaine_co,
			"count rdv_id" AS "nb de RDV individuels"
			FROM nb_rdv_par_personne 
				INNER JOIN personnes ON (personnes.id=nb_rdv_par_personne.personne_id) 
				INNER JOIN foyers ON (foyers.id=personnes.foyer_id)
				INNER JOIN dossiers ON (dossiers.id=foyers.dossier_id)
			WHERE pdv IS NOT NULL
			ORDER BY pdv, personne_id
		;

		-- Listing partie 4 : liste des RDV individuels par personne
		CREATE OR REPLACE VIEW b1b2_liste_4_2 AS
			SELECT nb_rdv_par_personne.personne_id, nom, prenom, dtnai AS "date de naissance", matricule, 
			pdv, plaine_co,
			daterdv, heurerdv
			FROM nb_rdv_par_personne 
				INNER JOIN personnes ON (personnes.id=nb_rdv_par_personne.personne_id) 
				INNER JOIN foyers ON (foyers.id=personnes.foyer_id)
				INNER JOIN dossiers ON (dossiers.id=foyers.dossier_id)
				INNER JOIN rdv_ind_honore_durant_periode ON (rdv_ind_honore_durant_periode.personne_id=nb_rdv_par_personne.personne_id AND structurereferente_id=nb_rdv_par_personne.id)
			WHERE pdv IS NOT NULL
			ORDER BY pdv, personne_id, daterdv, heurerdv
		;

		/**
		-- Export 4 : B1 tableau 
		**/
		CREATE OR REPLACE VIEW b1b2_stat_3 AS
			SELECT id, "PDV de D1", plaine_co,
				SUM(CASE WHEN "Type de suivi" = 'Reconduit' THEN 1 ELSE 0 END) AS "Nb de psnes reconduites", --3aa
				SUM(CASE WHEN "Type de suivi" = 'Nouveau' THEN 1 ELSE 0 END) AS "Nb de nouvelles psnes suivies", --3ab
				SUM(CASE WHEN "Service referent (orientation)" = 'Le PDV' THEN 1 ELSE 0 END) AS "Nb de personnes suivies avec référent PDV", --3ba
				SUM(CASE WHEN "Service referent (orientation)" = 'Le Pôle Emploi' THEN 1 ELSE 0 END) AS "Nb de personnes suivies avec référent Pôle Emploi", --3bb
				SUM(CASE WHEN "Service referent (orientation)" = 'Le Service Social' THEN 1 ELSE 0 END) AS "Nb de personnes suivies avec référent Service Social", --3bc
				SUM(CASE WHEN "Service referent (orientation)" = 'Une Asso conventionnée' THEN 1 ELSE 0 END) AS "Nb de personnes suivies avec référent assoc conv par le Dpt", --3bd
				SUM(CASE WHEN "Service referent (orientation)" = 'Un autre PDV' THEN 1 ELSE 0 END) AS "Nb de personnes suivies avec référent un autre PDV", --3be
				SUM(CASE WHEN "Service referent (orientation)" != 'Sans Orientation' THEN 1 ELSE 0 END) AS "Nb de personnes suivies avec référent", --3b1
				SUM(CASE WHEN "Service referent (orientation)" = 'Sans Orientation' THEN 1 ELSE 0 END) AS "Nb de personnes suivies sans orientation", --3b2
				COUNT(*) AS "Nb de personnes suivies" --3
			FROM partie_3 
			GROUP BY id, "PDV de D1", plaine_co ORDER BY id, "PDV de D1";
		
		---------------------
		-- Requete globale -- liste où au moins un PDV est renseigné parmi les tables temporaires
		---------------------
		CREATE OR REPLACE VIEW b1b2_global AS
			SELECT 
				structuresreferentes.id, pdv_format_export.pdv, 
				pdv_format_export.plaine_co,
				-- PARTIE 2 --
				orient_tot."Nb de psnes avec orientations validées dans la période" AS "2.a Nb de psnes avec orientations validees dans la periode", --2c
				-- PARTIE 3 --
				b1b2_stat_3."Nb de psnes reconduites" AS "3.A.a Nb de psnes reconduites", --3aa
				b1b2_stat_3."Nb de nouvelles psnes suivies" AS "3.A.b Nb de nouvelles psnes suivies", --3ab
				b1b2_stat_3."Nb de personnes suivies" AS "3.A Nb de personnes suivies", --3a
				b1b2_stat_3."Nb de personnes suivies avec référent PDV" AS "3.B.a Nb de personnes suivies avec referent PDV", --3ba
				b1b2_stat_3."Nb de personnes suivies avec référent Pôle Emploi" AS "3.B.b Nb de personnes suivies avec referent Pole Emploi", --3bb
				b1b2_stat_3."Nb de personnes suivies avec référent Service Social" AS "3.B.c Nb de personnes suivies avec referent Service Social", --3bc
				b1b2_stat_3."Nb de personnes suivies avec référent assoc conv par le Dpt" AS "3.Bd Nb de personnes suivies avec referent assoc conv par Dpt", --3bd
				b1b2_stat_3."Nb de personnes suivies avec référent un autre PDV" AS "3.B.e Nb de personnes suivies avec referent un autre PDV", --3be
				b1b2_stat_3."Nb de personnes suivies avec référent" AS "3.B.1 Nb de personnes suivies avec referent", --3b1
				b1b2_stat_3."Nb de personnes suivies sans orientation" AS "3.B.2 Nb de personnes suivies sans orientation", --3b2
				b1b2_stat_3."Nb de personnes suivies" AS "3.B Nb de personnes suivies", --3b
				-- PARTIE 4 --
				b1b2_stat_4.nb_pers_1_3_rdv AS "4.A.a 1 a 3 RDV honores", --4aa
				b1b2_stat_4.nb_pers_4_6_rdv AS "4.A.b 4 a 6 RDV honores", --4ab
				b1b2_stat_4.nb_pers_7_plus_rdv AS "4.A.c 7 et + RDV honores", --4ac
				b1b2_stat_4.nb_pers_total_rdv AS "4.A Total personnes ayant eu 1 ou + RDV", --4a
				b1b2_stat_4.total_RDV AS "4.B Total RDV", --4b
				b1b2_stat_4.dt_rdv_psy AS "4.C Dont Total RDV psy" --4c
			FROM structuresreferentes LEFT OUTER JOIN administration.pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
				-- PARTIE 2
				LEFT OUTER JOIN orient_tot ON (orient_tot.id=structuresreferentes.id)
				-- PARTIE 3
				LEFT OUTER JOIN b1b2_stat_3 ON (b1b2_stat_3.id = structuresreferentes.id)
				--PARTIE 4
				LEFT OUTER JOIN b1b2_stat_4 ON (b1b2_stat_4.id=structuresreferentes.id)
			WHERE orient_tot.pdv IS NOT NULL
				OR b1b2_stat_3."PDV de D1" IS NOT NULL
				OR b1b2_stat_4.pdv IS NOT NULL
			ORDER BY structuresreferentes.id, structuresreferentes.lib_struc;



		CREATE OR REPLACE VIEW b1b2_global_plaine_co AS
			SELECT 
				-- PARTIE 2 --
				SUM(orient_tot."Nb de psnes avec orientations validées dans la période") AS "2.a Nb de psnes avec orientations validees dans la periode", --2c
				-- PARTIE 3 --
				SUM(b1b2_stat_3."Nb de psnes reconduites") AS "3.A.a Nb de psnes reconduites", --3aa
				SUM(b1b2_stat_3."Nb de nouvelles psnes suivies") AS "3.A.b Nb de nouvelles psnes suivies", --3ab
				SUM(b1b2_stat_3."Nb de personnes suivies") AS "3.A Nb de personnes suivies", --3a
				SUM(b1b2_stat_3."Nb de personnes suivies avec référent PDV") AS "3.B.a Nb de personnes suivies avec referent PDV", --3ba
				SUM(b1b2_stat_3."Nb de personnes suivies avec référent Pôle Emploi") AS "3.B.b Nb de personnes suivies avec referent Pole Emploi", --3bb
				SUM(b1b2_stat_3."Nb de personnes suivies avec référent Service Social") AS "3.B.c Nb de personnes suivies avec referent Service Social", --3bc
				SUM(b1b2_stat_3."Nb de personnes suivies avec référent assoc conv par le Dpt") AS "3.Bd Nb de personnes suivies avec referent assoc conv par Dpt", --3bd
				SUM(b1b2_stat_3."Nb de personnes suivies avec référent un autre PDV") AS "3.B.e Nb de personnes suivies avec referent un autre PDV", --3be
				SUM(b1b2_stat_3."Nb de personnes suivies avec référent") AS "3.B.1 Nb de personnes suivies avec referent", --3b1
				SUM(b1b2_stat_3."Nb de personnes suivies sans orientation") AS "3.B.2 Nb de personnes suivies sans orientation", --3b2
				SUM(b1b2_stat_3."Nb de personnes suivies") AS "3.B Nb de personnes suivies", --3b
				-- PARTIE 4 --
				SUM(b1b2_stat_4.nb_pers_1_3_rdv) AS "4.A.a 1 a 3 RDV honores", --4aa
				SUM(b1b2_stat_4.nb_pers_4_6_rdv) AS "4.A.b 4 a 6 RDV honores", --4ab
				SUM(b1b2_stat_4.nb_pers_7_plus_rdv) AS "4.A.c 7 et + RDV honores", --4ac
				SUM(b1b2_stat_4.nb_pers_total_rdv) AS "4.A Total personnes ayant eu 1 ou + RDV", --4a
				SUM(b1b2_stat_4.total_RDV) AS "4.B Total RDV", --4b
				SUM(b1b2_stat_4.dt_rdv_psy) AS "4.C Dont Total RDV psy" --4c
			FROM structuresreferentes LEFT OUTER JOIN administration.pdv_format_export ON (structuresreferentes.id=pdv_format_export.pdv_id)
				-- PARTIE 2
				LEFT OUTER JOIN orient_tot ON (orient_tot.id=structuresreferentes.id)
				-- PARTIE 3
				LEFT OUTER JOIN b1b2_stat_3 ON (b1b2_stat_3.id = structuresreferentes.id)
				--PARTIE 4
				LEFT OUTER JOIN b1b2_stat_4 ON (b1b2_stat_4.id=structuresreferentes.id)
			WHERE (orient_tot.pdv IS NOT NULL
				OR b1b2_stat_3."PDV de D1" IS NOT NULL
				OR b1b2_stat_4.pdv IS NOT NULL)
				AND pdv_format_export.plaine_co='oui'
			;

		
		RETURN 0;
	END;

	
$_$;


ALTER FUNCTION administration.pdv_b1b2_vues(dd_periode_orient text, df_periode_orient text, dd_periode text, df_periode text, nb_mois integer, rdv_individuel integer, rdv_honore integer) OWNER TO webrsa;

--
-- Name: pdv_cer_en_cours_valid_vue(text, text); Type: FUNCTION; Schema: administration; Owner: webrsa
--

CREATE FUNCTION administration.pdv_cer_en_cours_valid_vue(c_dd_ci text, c_df_ci text) RETURNS integer
    LANGUAGE plpgsql STRICT
    AS $_$
	
	/************************************************
	************* Liste des paramètres **************
	****** dates de début et de fin de periode ******
	************************************************/
	DECLARE
		c_dd_ci CONSTANT date NOT NULL := $1;
		c_df_ci CONSTANT date NOT NULL := $2;		
	
	/************************************************
	*** VUE pour la periode passee en parametre *****
	************************************************/
	BEGIN

		
	
		--CREATE OR REPLACE TEMP VIEW cer_en_cours_valid AS
		DROP TABLE IF EXISTS cer_en_cours_valid CASCADE;
		CREATE TABLE cer_en_cours_valid AS
			SELECT DISTINCT pdv, plaine_co,
			personnes.qual AS "Civilite",
			personnes.nom AS "Nom", 
			personnes.prenom AS "Prenom",
			personnes.dtnai AS "Date de naissance", 
			adresses.numvoie AS "Numero de voie", 
			adresses.libtypevoie AS "Type de voie",
			adresses.nomvoie AS "Nom de voie", 
			adresses.complideadr AS "Complement d'adresse", 
			adresses.codepos AS "Code postal",
			case 
			when adresses.nomcom='AUBERVILLIERS' THEN 'AUBERVILLIERS'
			when adresses.nomcom='AULNAY SOUS BOIS' THEN 'AULNAY SOUS BOIS'
			when adresses.nomcom='BAGNOLET' THEN 'BAGNOLET'
			when adresses.nomcom IN ('BOBIGNY', 'BOBIGNY CEDEX') THEN 'BOBIGNY'
			when adresses.nomcom='BONDY' THEN 'BONDY'
			when adresses.nomcom='CLICHY SOUS BOIS' THEN 'CLICHY SOUS BOIS'
			when adresses.nomcom='COUBRON' THEN 'COUBRON'
			when adresses.nomcom IN ('DRANCY','DRANCY CEDEX') THEN 'DRANCY'
			when adresses.nomcom='DUGNY' THEN 'DUGNY'
			when adresses.nomcom='EPINAY SUR SEINE' THEN 'EPINAY SUR SEINE'
			when adresses.nomcom='GAGNY' THEN 'GAGNY'
			when adresses.nomcom='GOURNAY SUR MARNE' THEN 'GOURNAY SUR MARNE'
			when adresses.nomcom='L ILE ST DENIS' THEN 'L ILE ST DENIS'
			when adresses.nomcom='LA COURNEUVE' THEN 'LA COURNEUVE'
			when adresses.nomcom IN ('LA PLAINE ST DENIS','SAINT DENIS','ST DENIS') THEN 'SAINT DENIS'
			when adresses.nomcom='LE BLANC MESNIL' THEN 'LE BLANC MESNIL'
			when adresses.nomcom='LE BOURGET' THEN 'LE BOURGET'
			when adresses.nomcom='LE PRE ST GERVAIS' THEN 'LE PRE ST GERVAIS'
			when adresses.nomcom='LE RAINCY' THEN 'LE RAINCY'
			when adresses.nomcom='LES LILAS' THEN 'LES LILAS'
			when adresses.nomcom IN ('LES PAVILLONS SOUS BOIS','PAVILLONS SOUS BOIS') THEN 'LES PAVILLONS SOUS BOIS'
			when adresses.nomcom='LIVRY GARGAN' THEN 'LIVRY GARGAN'
			when adresses.nomcom='MONTFERMEIL' THEN 'MONFERMEIL'
			when adresses.nomcom='MONTREUIL' THEN 'MONTREUIL'
			when adresses.nomcom='NEUILLY PLAISANCE' THEN 'NEUILLY PLAISANCE'
			when adresses.nomcom='NEUILLY SUR MARNE' THEN 'NEUILLY SUR MARNE'
			when adresses.nomcom='NOISY LE GRAND' THEN 'NOISY LE GRAND'
			when adresses.nomcom='NOISY LE SEC' THEN 'NOISY LE SEC'
			when adresses.nomcom='PANTIN' THEN 'PANTIN'
			when adresses.nomcom IN ('PIERREFITTE','PIERREFITTE SUR SEINE') THEN 'PIERREFITTE SUR SEINE'
			when adresses.nomcom='ROMAINVILLE' THEN 'ROMAINVILLE'
			when adresses.nomcom='ROSNY SOUS BOIS' THEN 'ROSNY SOUS BOIS'
			when adresses.nomcom IN ('SAINT OUEN','ST OUEN') THEN 'SAINT OUEN'
			when adresses.nomcom='SEVRAN' THEN 'SEVRAN'
			when adresses.nomcom='STAINS' THEN 'STAINS'
			when adresses.nomcom='TREMBLAY EN FRANCE' THEN 'TREMBLAY EN FRANCE'
			when adresses.nomcom='VAUJOURS' THEN 'VAUJOURS'
			when adresses.nomcom='VILLEMOMBLE' THEN 'VILLEMOMBLE'
			when adresses.nomcom='VILLEPINTE' THEN 'VILLEPINTE'
			when adresses.nomcom='VILLETANEUSE' THEN 'VILLETANEUSE'
			end AS "Ville de domicile recode",
			dossiers.dtdemrsa AS "Date de demande RSA", 
			dossiers.numdemrsa AS "Numero de demande RSA",
			dossiers.matricule AS "Numero CAF",
			orientsstructs.statut_orient AS "Oriente?", 
			struc_orientation.lib_struc AS "Structure referente",
			orientsstructs.date_valid AS "Date d'orientation", 
			struc_signataire_cer.lib_struc AS "Structure signataire du CER",
			contratsinsertion.dd_ci AS "Date de debut de CER", 
			contratsinsertion.df_ci AS "Date de fin de CER", 
			contratsinsertion.rg_ci AS "Rang de CER",
			CASE
			WHEN cers93.positioncer='00enregistre' THEN 'Enregistre'
			WHEN cers93.positioncer='01signe' THEN 'Signe'
			WHEN cers93.positioncer='02attdecisioncpdv' THEN 'En attente de decision CPDV'
			WHEN cers93.positioncer='03attdecisioncg' THEN 'En attente de decision CG'
			WHEN cers93.positioncer='04premierelecture' THEN 'En premiere lecture'
			WHEN cers93.positioncer='05secondelecture' THEN 'En seconde lecture'
			WHEN cers93.positioncer='07attavisep' THEN 'En attente d''avis EP'
			WHEN cers93.positioncer='99rejete' THEN 'Rejet CG'
			WHEN cers93.positioncer='99rejetecpdv' THEN 'Rejet CPDV'
			WHEN cers93.positioncer='99valide' THEN 'Valide CG'
			END AS "Etat de validation CER", 
			cers93.datesignature AS "Date de signature CER", 
			cers93.created AS "Date de creation du CER", 
			cers93.modified AS "Date de modification du CER",
			referent_parcours.qual AS "Civilite du referent", 
			referent_parcours.nom AS "Nom du referent", 
			referent_parcours.prenom AS "Prenom du referent", 
			referent_parcours.fonction AS "Fonction du referent"
			FROM contratsinsertion
			INNER JOIN cers93  ON (cers93.contratinsertion_id = contratsinsertion.id) 
			INNER JOIN personnes ON (contratsinsertion.personne_id = personnes.id) 
			LEFT OUTER JOIN referents  AS referent_CER ON (contratsinsertion.referent_id = referent_CER.id) 
			INNER JOIN foyers ON (personnes.foyer_id = foyers.id) 
			LEFT OUTER JOIN orientsstructs ON (orientsstructs.personne_id = personnes.id AND orientsstructs.statut_orient = 'Orienté') 
			LEFT OUTER JOIN structuresreferentes AS struc_orientation ON (orientsstructs.structurereferente_id = struc_orientation.id)
			INNER JOIN prestations ON (prestations.personne_id = personnes.id AND prestations.natprest = 'RSA') 
			INNER JOIN dossiers ON (foyers.dossier_id = dossiers.id) 
			LEFT OUTER JOIN adressesfoyers ON (adressesfoyers.foyer_id = foyers.id) 
			LEFT OUTER JOIN adresses ON (adressesfoyers.adresse_id =adresses.id) 
			LEFT OUTER JOIN typesorients ON (orientsstructs.typeorient_id = typesorients.id) 
			LEFT OUTER JOIN structuresreferentes AS struc_signataire_cer ON (contratsinsertion.structurereferente_id = struc_signataire_cer.id) 
			LEFT OUTER JOIN administration.pdv_format_export AS pdv ON (pdv.pdv_id=struc_signataire_cer.id)
			INNER JOIN situationsdossiersrsa ON (situationsdossiersrsa.dossier_id = dossiers.id) 
			LEFT OUTER JOIN detailsdroitsrsa ON (detailsdroitsrsa.dossier_id = dossiers.id) 
			LEFT OUTER JOIN personnes_referents ON 
			(
				personnes_referents.personne_id = personnes.id 
				AND 
				(
					(personnes_referents.id IS NULL) 
					OR 
					(personnes_referents.id IN 
						( 
						SELECT personnes_referents.id 
						FROM personnes_referents 
						WHERE personnes_referents.personne_id = personnes.id 
						AND personnes_referents.dfdesignation IS NULL 
						ORDER BY personnes_referents.dddesignation DESC LIMIT 1 
						)
					)
				)
			) 
			LEFT OUTER JOIN referents AS referent_parcours ON (personnes_referents.referent_id = referent_parcours.id) 
			LEFT OUTER JOIN structuresreferentes AS struc_referent_parcours ON (referent_parcours.structurereferente_id = struc_referent_parcours.id) 
			WHERE 
			(
				(
					(
						(adressesfoyers.id IS NULL) 
						OR 
						(
							adressesfoyers.id IN 
							( 
								SELECT adressesfoyers.id
								FROM adressesfoyers
								WHERE adressesfoyers.foyer_id = foyers.id 
								AND adressesfoyers.rgadr = '01' 
								ORDER BY adressesfoyers.dtemm 
								DESC LIMIT 1 
							)
						)
					)
				) 
				AND 
				(
				prestations.rolepers IN ('DEM', 'CJT')
				)
			) 

			--(critère CESDI pour prendre en compte uniquement les PDV) 
			AND ((struc_signataire_cer.lib_struc LIKE '%Projet de Ville%') OR (struc_signataire_cer.lib_struc LIKE '%Projet Insertion Emploi%') OR (struc_signataire_cer.lib_struc LIKE 'Maison de l%'))
			AND 
			( 
				(contratsinsertion.decision_ci = 'V') 
				AND 
				( 
					( 
						( 
							( 
								( contratsinsertion.dd_ci <= c_dd_ci) 
								AND 
								(contratsinsertion.df_ci >= c_df_ci) 
							) 
						) 
						OR 
						( 
							( 
								(contratsinsertion.dd_ci >= c_dd_ci) 
								AND 
								(contratsinsertion.dd_ci <= c_dd_ci) 
							) 
						) 
					) 
				) 
			) 
			AND 
			( 
				(orientsstructs.id IS NULL) 
				OR 
				(orientsstructs.id IN 
					( 
					SELECT orientsstructs.id
					FROM orientsstructs
					WHERE orientsstructs.personne_id = personnes.id 
					AND orientsstructs.statut_orient = 'Orienté' 
					AND orientsstructs.date_valid IS NOT NULL 
					ORDER BY orientsstructs.date_valid DESC LIMIT 1 
					) 
				) 
			)
		;

		RETURN 0;
	END;
	
$_$;


ALTER FUNCTION administration.pdv_cer_en_cours_valid_vue(c_dd_ci text, c_df_ci text) OWNER TO webrsa;

--
-- Name: reset_sequence(text, text); Type: FUNCTION; Schema: administration; Owner: webrsa
--

CREATE FUNCTION administration.reset_sequence(tablename text, columnname text) RETURNS SETOF bigint
    LANGUAGE plpgsql
    AS $$ 
-- select table_name || '_' || column_name || '_seq', administration.get_sequence(table_name || '_' || column_name || '_seq'),administration.reset_sequence(table_name,column_name) from information_schema.columns where column_default like 'nextval%' and table_schema = 'public';

      DECLARE 
      sequence_name varchar := tablename || '_' || columnname || '_seq' ;
      BEGIN

      EXECUTE 'SELECT setval( ''' || sequence_name  || ''', ' || '(SELECT MAX(' || columnname || ') FROM ' || tablename || ')' || '+1)';
      RETURN QUERY EXECUTE 'SELECT last_value from ' || quote_ident(sequence_name) ;

      END; 

    $$;


ALTER FUNCTION administration.reset_sequence(tablename text, columnname text) OWNER TO webrsa;

--
-- Name: allocatairestransferes; Type: VIEW; Schema: administration; Owner: webrsa
--

CREATE VIEW administration.allocatairestransferes AS
 SELECT o2.id AS vx_orientstruct_id,
    o1.id AS nv_orientstruct_id,
    a2.id AS vx_adressefoyer_id,
    a1.id AS nv_adressefoyer_id,
    o1.user_id,
    o1.date_valid AS created,
    o1.date_valid AS modified
   FROM ((((((public.personnes
     JOIN public.orientsstructs o1 ON (((personnes.id = o1.personne_id) AND ((o1.origine)::text = 'demenagement'::text))))
     JOIN public.orientsstructs o2 ON ((personnes.id = o2.personne_id)))
     JOIN public.adressesfoyers a1 ON (((a1.foyer_id = personnes.foyer_id) AND (a1.rgadr = '01'::bpchar))))
     JOIN public.adressesfoyers a2 ON (((a2.foyer_id = personnes.foyer_id) AND (a2.rgadr = '02'::bpchar))))
     JOIN public.adresses r1 ON ((a1.adresse_id = r1.id)))
     JOIN public.adresses r2 ON ((a2.adresse_id = r2.id)))
  WHERE ((o1.rgorient = (o2.rgorient + 1)) AND ((personnes.id, o1.id) IN ( SELECT orientsstructs.personne_id,
            max(orientsstructs.id) AS max
           FROM public.orientsstructs
          GROUP BY orientsstructs.personne_id)))
  ORDER BY o1.date_valid;


ALTER TABLE administration.allocatairestransferes OWNER TO webrsa;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: av_personnes; Type: TABLE; Schema: administration; Owner: webrsa
--

CREATE TABLE administration.av_personnes (
    foyer_id integer,
    id integer,
    nom character varying(50),
    nomnai character varying(50),
    prenom character varying(50),
    dtnai date,
    sexe character(1),
    nir character(15),
    rolepers character(3),
    nb_adresse bigint
);


ALTER TABLE administration.av_personnes OWNER TO webrsa;

--
-- Name: historiquesdroits; Type: TABLE; Schema: administration; Owner: webrsa
--

CREATE TABLE administration.historiquesdroits (
    id integer NOT NULL,
    numdemrsa character varying(11),
    matricule character varying(15),
    rolepers character varying(3),
    nomnai character varying(40),
    prenom character varying(40),
    dtnai date,
    etatdosrsa character varying(1),
    toppersdrodevorsa character varying(1),
    dtref date,
    dossier_id integer,
    foyer_id integer,
    personne_id integer,
    nomnai_corr character varying(50),
    prenom_corr character varying(50)
);


ALTER TABLE administration.historiquesdroits OWNER TO webrsa;

--
-- Name: historiquesdroits_id_seq; Type: SEQUENCE; Schema: administration; Owner: webrsa
--

CREATE SEQUENCE administration.historiquesdroits_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE administration.historiquesdroits_id_seq OWNER TO webrsa;

--
-- Name: historiquesdroits_id_seq; Type: SEQUENCE OWNED BY; Schema: administration; Owner: webrsa
--

ALTER SEQUENCE administration.historiquesdroits_id_seq OWNED BY administration.historiquesdroits.id;


--
-- Name: pdv_format_export; Type: TABLE; Schema: administration; Owner: webrsa
--

CREATE TABLE administration.pdv_format_export (
    pdv_id integer,
    pdv character varying(100),
    plaine_co character varying(10)
);


ALTER TABLE administration.pdv_format_export OWNER TO webrsa;

--
-- Name: rapportstalendscreances; Type: TABLE; Schema: administration; Owner: webrsa
--

CREATE TABLE administration.rapportstalendscreances (
    id integer NOT NULL,
    flux character varying(15),
    typeflux character varying(1),
    natflux character varying(1),
    dtflux date,
    dtref date,
    dtexec date,
    fichierflux character varying(80),
    nbtotdosrsatransm numeric(8,0),
    nbtotdosrsatransmano numeric(8,0),
    nbrejete numeric(6,0),
    fichierrejet character varying(40),
    nbinser numeric(6,0),
    nbmaj numeric(6,0),
    message character varying(1000)
);


ALTER TABLE administration.rapportstalendscreances OWNER TO webrsa;

--
-- Name: rapportstalendscreances_id_seq; Type: SEQUENCE; Schema: administration; Owner: webrsa
--

CREATE SEQUENCE administration.rapportstalendscreances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE administration.rapportstalendscreances_id_seq OWNER TO webrsa;

--
-- Name: rapportstalendscreances_id_seq; Type: SEQUENCE OWNED BY; Schema: administration; Owner: webrsa
--

ALTER SEQUENCE administration.rapportstalendscreances_id_seq OWNED BY administration.rapportstalendscreances.id;


--
-- Name: rejet_historique; Type: TABLE; Schema: administration; Owner: webrsa
--

CREATE TABLE administration.rejet_historique (
    cleinfodemandersa integer NOT NULL,
    flux character varying(20) DEFAULT NULL::character varying NOT NULL,
    etape integer,
    table_en_erreur character varying(50) DEFAULT NULL::character varying,
    log character varying(1000) DEFAULT NULL::character varying,
    numdemrsa character varying(20) DEFAULT NULL::character varying,
    matricule character varying(20) DEFAULT NULL::character varying,
    "DT_INSERT" timestamp(6) without time zone DEFAULT now() NOT NULL,
    fic character varying(40),
    balisededonnee character varying(100000)
);


ALTER TABLE administration.rejet_historique OWNER TO webrsa;

--
-- Name: rejetstalendscreances; Type: TABLE; Schema: administration; Owner: webrsa
--

CREATE TABLE administration.rejetstalendscreances (
    id integer NOT NULL,
    fusion boolean DEFAULT false,
    flux character varying(15),
    typeflux character varying(1),
    natflux character varying(1),
    dtflux date,
    dtref date,
    dtexec date,
    fichierflux character varying(80),
    matricule character varying(15) DEFAULT NULL::character varying,
    numdemrsa character varying(11) DEFAULT NULL::character varying,
    dtdemrsa date NOT NULL,
    ddratdos date,
    dfratdos date,
    toprespdos boolean,
    nir character(15),
    qual character varying(3) DEFAULT NULL::character varying,
    nom character varying(50) NOT NULL,
    nomnai character varying(50) DEFAULT NULL::character varying,
    prenom character varying(50) NOT NULL,
    prenom2 character varying(50) NOT NULL,
    prenom3 character varying(50) NOT NULL,
    dtnai date NOT NULL,
    nomcomnai character varying(26) DEFAULT NULL::character varying,
    typedtnai character(1),
    typeparte character(4),
    ideparte character(3),
    topvalec boolean,
    sexe character(1),
    rgadr character(2),
    dtemm date,
    typeadr character(1),
    numvoie character varying(6),
    libtypevoie character varying(10),
    nomvoie character varying(32),
    complideadr character varying(50),
    compladr character varying(50),
    lieudist character varying(32),
    numcom character(5),
    codepos character(5),
    dtimplcre date,
    natcre character(3),
    rgcre character(3),
    motiindu character(2),
    oriindu character(2),
    respindu character(2),
    ddregucre date,
    dfregucre date,
    dtdercredcretrans date,
    mtsolreelcretrans numeric(9,2),
    mtinicre numeric(9,2),
    moismoucompta date,
    liblig2adr character varying(38) DEFAULT NULL::character varying,
    liblig3adr character varying(38) DEFAULT NULL::character varying,
    liblig4adr character varying(38) DEFAULT NULL::character varying,
    liblig5adr character varying(38) DEFAULT NULL::character varying,
    liblig6adr character varying(38) DEFAULT NULL::character varying,
    liblig7adr character varying(38) DEFAULT NULL::character varying
);


ALTER TABLE administration.rejetstalendscreances OWNER TO webrsa;

--
-- Name: rejetstalendscreances_id_seq; Type: SEQUENCE; Schema: administration; Owner: webrsa
--

CREATE SEQUENCE administration.rejetstalendscreances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE administration.rejetstalendscreances_id_seq OWNER TO webrsa;

--
-- Name: rejetstalendscreances_id_seq; Type: SEQUENCE OWNED BY; Schema: administration; Owner: webrsa
--

ALTER SEQUENCE administration.rejetstalendscreances_id_seq OWNED BY administration.rejetstalendscreances.id;


--
-- Name: visionneuses; Type: TABLE; Schema: administration; Owner: webrsa
--

CREATE TABLE administration.visionneuses (
    id integer NOT NULL,
    flux character(15),
    nomfic character(40),
    dtdeb timestamp without time zone,
    dtfin timestamp without time zone,
    nbrejete numeric(6,0),
    nbinser numeric(6,0),
    nbmaj numeric(6,0),
    perscree numeric(6,0),
    persmaj numeric(6,0),
    dspcree numeric(6,0),
    dspmaj numeric(6,0)
);


ALTER TABLE administration.visionneuses OWNER TO webrsa;

--
-- Name: visionneuses_id_seq; Type: SEQUENCE; Schema: administration; Owner: webrsa
--

CREATE SEQUENCE administration.visionneuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE administration.visionneuses_id_seq OWNER TO webrsa;

--
-- Name: visionneuses_id_seq; Type: SEQUENCE OWNED BY; Schema: administration; Owner: webrsa
--

ALTER SEQUENCE administration.visionneuses_id_seq OWNED BY administration.visionneuses.id;


--
-- Name: historiquesdroits id; Type: DEFAULT; Schema: administration; Owner: webrsa
--

ALTER TABLE ONLY administration.historiquesdroits ALTER COLUMN id SET DEFAULT nextval('administration.historiquesdroits_id_seq'::regclass);


--
-- Name: rapportstalendscreances id; Type: DEFAULT; Schema: administration; Owner: webrsa
--

ALTER TABLE ONLY administration.rapportstalendscreances ALTER COLUMN id SET DEFAULT nextval('administration.rapportstalendscreances_id_seq'::regclass);


--
-- Name: rejetstalendscreances id; Type: DEFAULT; Schema: administration; Owner: webrsa
--

ALTER TABLE ONLY administration.rejetstalendscreances ALTER COLUMN id SET DEFAULT nextval('administration.rejetstalendscreances_id_seq'::regclass);


--
-- Name: visionneuses id; Type: DEFAULT; Schema: administration; Owner: webrsa
--

ALTER TABLE ONLY administration.visionneuses ALTER COLUMN id SET DEFAULT nextval('administration.visionneuses_id_seq'::regclass);


--
-- Name: historiquesdroits historiquesdroits_pkey; Type: CONSTRAINT; Schema: administration; Owner: webrsa
--

ALTER TABLE ONLY administration.historiquesdroits
    ADD CONSTRAINT historiquesdroits_pkey PRIMARY KEY (id);


--
-- Name: rejet_historique rejet_historique_pkey; Type: CONSTRAINT; Schema: administration; Owner: webrsa
--

ALTER TABLE ONLY administration.rejet_historique
    ADD CONSTRAINT rejet_historique_pkey PRIMARY KEY (cleinfodemandersa, flux, "DT_INSERT");


--
-- Name: foyer_id_index; Type: INDEX; Schema: administration; Owner: webrsa
--

CREATE INDEX foyer_id_index ON administration.av_personnes USING btree (foyer_id);


--
-- Name: historiquesdroits_numdem_rolepers_dtnai_etatdos_toppers_idx; Type: INDEX; Schema: administration; Owner: webrsa
--

CREATE INDEX historiquesdroits_numdem_rolepers_dtnai_etatdos_toppers_idx ON administration.historiquesdroits USING btree (numdemrsa, rolepers, dtnai, etatdosrsa, toppersdrodevorsa);


--
-- Name: historiquesdroits_numdem_rolepers_dtnai_idx; Type: INDEX; Schema: administration; Owner: webrsa
--

CREATE INDEX historiquesdroits_numdem_rolepers_dtnai_idx ON administration.historiquesdroits USING btree (numdemrsa, rolepers, dtnai);


--
-- Name: personne_id_index; Type: INDEX; Schema: administration; Owner: webrsa
--

CREATE INDEX personne_id_index ON administration.av_personnes USING btree (id);


--
-- PostgreSQL database dump complete
--

