SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

/**
* 20140805: Correction: lorsque le référent attaché à une orientation n'appartient
* pas à la structure référente stockée dans l'orientation, on le passe à NULL.
* 0@cg58_20140724, 813@cg66_20140627, 33@cg93_20140710
*/
-- 1. Suppression des PDF
DELETE FROM pdfs WHERE modele = 'Orientstruct' AND fk_value IN (
	SELECT
				o.id
			FROM orientsstructs AS o
				INNER JOIN structuresreferentes AS structuresreferenteso ON ( structuresreferenteso.id = o.structurereferente_id )
				INNER JOIN referents ON ( referents.id = o.referent_id )
				INNER JOIN structuresreferentes AS structuresreferentesreferents ON ( structuresreferentesreferents.id = referents.structurereferente_id )
			WHERE
				o.statut_orient = 'Orienté'
				AND o.structurereferente_id <> referents.structurereferente_id
);
-- 2. Mise à jour des orientsstructs
UPDATE orientsstructs SET referent_id = NULL WHERE id IN (
	SELECT
			o.id
		FROM orientsstructs AS o
			INNER JOIN structuresreferentes AS structuresreferenteso ON ( structuresreferenteso.id = o.structurereferente_id )
			INNER JOIN referents ON ( referents.id = o.referent_id )
			INNER JOIN structuresreferentes AS structuresreferentesreferents ON ( structuresreferentesreferents.id = referents.structurereferente_id )
		WHERE
			o.statut_orient = 'Orienté'
			AND o.structurereferente_id <> referents.structurereferente_id
);

--------------------------------------------------------------------------------
-- 20140811: correction des tickets #5654 et #4949, des dossiers de COV / d'EP
-- avaient été créés par erreur.
--------------------------------------------------------------------------------

-- 1. Dossiers COV (thématique "Proposition d'orientation sociale de fait"), créés via le module rendez-vous, qui n'avaient pas à être créés et qui sont suppressibles.
DELETE FROM dossierscovs58 CASCADE WHERE id IN (
	SELECT
			"Dossiercov58"."id"
		FROM "dossierscovs58" AS "Dossiercov58"
			INNER JOIN "public"."proposorientssocialescovs58" AS "Propoorientsocialecov58" ON ("Propoorientsocialecov58"."dossiercov58_id" = "Dossiercov58"."id")
			INNER JOIN "public"."rendezvous" AS "Rendezvous" ON ("Propoorientsocialecov58"."rendezvous_id" = "Rendezvous"."id")
			INNER JOIN "public"."typesrdv" AS "Typerdv" ON ("Rendezvous"."typerdv_id" = "Typerdv"."id")
			INNER JOIN "public"."statutsrdvs_typesrdv" AS "StatutrdvTyperdv" ON ("StatutrdvTyperdv"."typerdv_id" = "Typerdv"."id")
			LEFT OUTER JOIN "public"."rendezvous" AS "Rendezvouspcd" ON (
				"Rendezvouspcd"."personne_id" = "Rendezvous"."personne_id"
				AND "Rendezvouspcd"."id" IN (
					SELECT "rendezvouspcds"."id"
						FROM "public"."rendezvous" AS "rendezvouspcds"
						WHERE
							"rendezvouspcds"."personne_id" = "Rendezvous"."personne_id"
							AND ( "rendezvouspcds"."daterdv" || ' ' || "rendezvouspcds"."heurerdv" )::TIMESTAMP
								< ( "Rendezvous"."daterdv" || ' ' || "Rendezvous"."heurerdv" )::TIMESTAMP
						ORDER BY
							"rendezvouspcds"."daterdv" DESC,
							"rendezvouspcds"."heurerdv" DESC
						LIMIT 1
				)
			)
			LEFT OUTER JOIN "public"."passagescovs58" AS "Passagecov58" ON ( "Passagecov58"."dossiercov58_id" = "Dossiercov58"."id" )
		WHERE
			"Dossiercov58"."themecov58" = 'proposorientssocialescovs58'
			-- ...qui vient d'un RDV...
			AND "Propoorientsocialecov58"."rendezvous_id" IS NOT NULL
			-- ...et qui est erroné...
			AND (
				(
					"Rendezvous"."typerdv_id" IS NULL
					OR "Rendezvouspcd"."typerdv_id" IS NULL
					OR "Rendezvous"."typerdv_id" <> "Rendezvouspcd"."typerdv_id"
				)
				OR (
					"Rendezvous"."statutrdv_id" IS NULL
					OR "Rendezvouspcd"."statutrdv_id" IS NULL
					OR "Rendezvous"."statutrdv_id" <> "Rendezvouspcd"."statutrdv_id"
				)
			)
			AND "Passagecov58"."etatdossiercov" IS NULL
			AND "StatutrdvTyperdv"."typecommission" = 'cov'
);

-- 2. Dossiers d'EP (thématique "Sanction pour rendez-vous non honoré(s)"), créés via le module rendez-vous, qui n'avaient pas à être créés et qui sont suppressibles.
DELETE FROM dossierseps CASCADE WHERE id IN (
	SELECT
			"Dossierep"."id"
		FROM "dossierseps" AS "Dossierep"
			INNER JOIN "public"."sanctionsrendezvouseps58" AS "Sanctionrendezvousep58" ON ("Sanctionrendezvousep58"."dossierep_id" = "Dossierep"."id")
			INNER JOIN "public"."rendezvous" AS "Rendezvous" ON ("Sanctionrendezvousep58"."rendezvous_id" = "Rendezvous"."id")
			INNER JOIN "public"."typesrdv" AS "Typerdv" ON ("Rendezvous"."typerdv_id" = "Typerdv"."id")
			INNER JOIN "public"."statutsrdvs_typesrdv" AS "StatutrdvTyperdv" ON ("StatutrdvTyperdv"."typerdv_id" = "Typerdv"."id")
			LEFT OUTER JOIN "public"."rendezvous" AS "Rendezvouspcd" ON (
				"Rendezvouspcd"."personne_id" = "Rendezvous"."personne_id"
				AND "Rendezvouspcd"."id" IN (
					SELECT "rendezvouspcds"."id"
						FROM "public"."rendezvous" AS "rendezvouspcds"
						WHERE
							"rendezvouspcds"."personne_id" = "Rendezvous"."personne_id"
							AND ( "rendezvouspcds"."daterdv" || ' ' || "rendezvouspcds"."heurerdv" )::TIMESTAMP
								< ( "Rendezvous"."daterdv" || ' ' || "Rendezvous"."heurerdv" )::TIMESTAMP
						ORDER BY
							"rendezvouspcds"."daterdv" DESC,
							"rendezvouspcds"."heurerdv" DESC
						LIMIT 1
				)
			)
			LEFT OUTER JOIN "public"."passagescommissionseps" AS "Passagecommissionep" ON ( "Passagecommissionep"."dossierep_id" = "Dossierep"."id" )
		WHERE
			"Dossierep"."themeep" = 'sanctionsrendezvouseps58'
			-- ...qui vient d'un RDV...
			AND "Sanctionrendezvousep58"."rendezvous_id" IS NOT NULL
			-- ...et qui est erroné...
			AND (
				(
					"Rendezvous"."typerdv_id" IS NULL
					OR "Rendezvouspcd"."typerdv_id" IS NULL
					OR "Rendezvous"."typerdv_id" <> "Rendezvouspcd"."typerdv_id"
				)
				OR (
					"Rendezvous"."statutrdv_id" IS NULL
					OR "Rendezvouspcd"."statutrdv_id" IS NULL
					OR "Rendezvous"."statutrdv_id" <> "Rendezvouspcd"."statutrdv_id"
				)
			)
			AND "Passagecommissionep"."etatdossierep" IS NULL
			AND "StatutrdvTyperdv"."typecommission" = 'ep'
);

--------------------------------------------------------------------------------
-- 20140814: Mise à jour des positions des CER (CG 66) à NULL lorsque les positions
-- avaient été changées par le bilan de parcours de manière erronée. Les positions
-- seront correctement recalculées lors du passage du shell Positionscer66.
--------------------------------------------------------------------------------

UPDATE contratsinsertion SET positioncer = NULL WHERE id IN (
	SELECT
			contratsinsertion.id
		FROM contratsinsertion
			INNER JOIN personnes ON ( contratsinsertion.personne_id = personnes.id )
			INNER JOIN bilansparcours66 ON ( bilansparcours66.personne_id = personnes.id )
		WHERE
			contratsinsertion.positioncer = 'attvalid'
			AND contratsinsertion.created < bilansparcours66.modified
			AND contratsinsertion.decision_ci = 'V'
			AND bilansparcours66.proposition <> 'aucun'
			-- Ceux pour qui le traitement devait être fait sur le CER reconduit
			AND NOT (
				bilansparcours66.proposition = 'traitement'
				AND bilansparcours66.choixsanspassageep = 'maintien'
				AND bilansparcours66.changementref = 'N'
				AND bilansparcours66.nvcontratinsertion_id IS NOT NULL
			)
);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************