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

-- 20120514: suppression des dossiers d'EP n'ayant pas d'entrée dans les tables de thématiques
DELETE FROM dossierseps WHERE themeep = 'reorientationseps93' AND dossierseps.id NOT IN (SELECT reorientationseps93.dossierep_id FROM reorientationseps93);
DELETE FROM dossierseps WHERE themeep = 'saisinesbilansparcourseps66' AND dossierseps.id NOT IN (SELECT saisinesbilansparcourseps66.dossierep_id FROM saisinesbilansparcourseps66);
DELETE FROM dossierseps WHERE themeep = 'saisinespdoseps66' AND dossierseps.id NOT IN (SELECT saisinespdoseps66.dossierep_id FROM saisinespdoseps66);
DELETE FROM dossierseps WHERE themeep = 'nonrespectssanctionseps93' AND dossierseps.id NOT IN (SELECT nonrespectssanctionseps93.dossierep_id FROM nonrespectssanctionseps93);
DELETE FROM dossierseps WHERE themeep = 'defautsinsertionseps66' AND dossierseps.id NOT IN (SELECT defautsinsertionseps66.dossierep_id FROM defautsinsertionseps66);
DELETE FROM dossierseps WHERE themeep = 'nonorientationsproseps58' AND dossierseps.id NOT IN (SELECT nonorientationsproseps58.dossierep_id FROM nonorientationsproseps58);
DELETE FROM dossierseps WHERE themeep = 'nonorientationsproseps93' AND dossierseps.id NOT IN (SELECT nonorientationsproseps93.dossierep_id FROM nonorientationsproseps93);
DELETE FROM dossierseps WHERE themeep = 'regressionsorientationseps58' AND dossierseps.id NOT IN (SELECT regressionsorientationseps58.dossierep_id FROM regressionsorientationseps58);
DELETE FROM dossierseps WHERE themeep = 'sanctionseps58' AND dossierseps.id NOT IN (SELECT sanctionseps58.dossierep_id FROM sanctionseps58);
DELETE FROM dossierseps WHERE themeep = 'signalementseps93' AND dossierseps.id NOT IN (SELECT signalementseps93.dossierep_id FROM signalementseps93);
DELETE FROM dossierseps WHERE themeep = 'sanctionsrendezvouseps58' AND dossierseps.id NOT IN (SELECT sanctionsrendezvouseps58.dossierep_id FROM sanctionsrendezvouseps58);
DELETE FROM dossierseps WHERE themeep = 'contratscomplexeseps93' AND dossierseps.id NOT IN (SELECT contratscomplexeseps93.dossierep_id FROM contratscomplexeseps93);
DELETE FROM dossierseps WHERE themeep = 'nonorientationsproseps66' AND dossierseps.id NOT IN (SELECT nonorientationsproseps66.dossierep_id FROM nonorientationsproseps66);

-- 20120514: suppression des dossiers de COV n'ayant pas d'entrée dans les tables de thématiques
DELETE FROM dossierscovs58 WHERE themecov58 = 'proposorientationscovs58' AND dossierscovs58.id NOT IN (SELECT proposorientationscovs58.dossiercov58_id FROM proposorientationscovs58);
DELETE FROM dossierscovs58 WHERE themecov58 = 'proposcontratsinsertioncovs58' AND dossierscovs58.id NOT IN (SELECT proposcontratsinsertioncovs58.dossiercov58_id FROM proposcontratsinsertioncovs58);
DELETE FROM dossierscovs58 WHERE themecov58 = 'proposnonorientationsproscovs58' AND dossierscovs58.id NOT IN (SELECT proposnonorientationsproscovs58.dossiercov58_id FROM proposnonorientationsproscovs58);

-- 20120516: création d'indexes uniques pour les décisions COV

-- Il faut d'abord supprimer les vrais doublons éventuels.
DELETE FROM decisionsproposorientationscovs58
	WHERE decisionsproposorientationscovs58.id IN (
		SELECT
				d1.id
			FROM decisionsproposorientationscovs58 AS d1, decisionsproposorientationscovs58 AS d2
			WHERE
				d1.id <> d2.id
				AND d1.passagecov58_id = d2.passagecov58_id
				AND d1.etapecov = d2.etapecov
				AND d1.decisioncov = d2.decisioncov
				AND d1.typeorient_id = d2.typeorient_id
				AND d1.structurereferente_id = d2.structurereferente_id
				AND d1.referent_id = d2.referent_id
				AND d1.datevalidation IS NULL
				AND d1.commentaire IS NULL
				AND d1.modified < d2.modified
	);

DROP INDEX IF EXISTS decisionsproposcontratsinsertioncovs58_passagecov58_id_idx;
CREATE UNIQUE INDEX decisionsproposcontratsinsertioncovs58_passagecov58_id_idx ON decisionsproposcontratsinsertioncovs58 (passagecov58_id);

DROP INDEX IF EXISTS decisionsproposnonorientationsproscovs58_passagecov58_id_idx;
CREATE UNIQUE INDEX decisionsproposnonorientationsproscovs58_passagecov58_id_idx ON decisionsproposnonorientationsproscovs58 (passagecov58_id);

DROP INDEX IF EXISTS decisionsproposorientationscovs58_passagecov58_id_idx;
CREATE UNIQUE INDEX decisionsproposorientationscovs58_passagecov58_id_idx ON decisionsproposorientationscovs58 (passagecov58_id);

-- -----------------------------------------------------------------------------------------------------------
-- 20120607: correction: une décision de maintienref pour la thématique nonorientationsproseps58
-- entraîne tout de même la création d'une nouvelle orientation
-- -----------------------------------------------------------------------------------------------------------

DROP INDEX orientsstructs_personne_id_rgorient_idx;
ALTER TABLE orientsstructs DROP CONSTRAINT orientsstructs_statut_orient_oriente_rgorient_not_null_chk;
ALTER TABLE orientsstructs DROP CONSTRAINT orientsstructs_origine_check;

UPDATE orientsstructs
	SET statut_orient = 'Orienté'
	WHERE
		typeorient_id IS NOT NULL
		AND structurereferente_id IS NOT NULL
		AND date_valid IS NOT NULL;

INSERT INTO orientsstructs ( personne_id, typeorient_id, structurereferente_id, referent_id, date_propo, date_valid, statut_orient, rgorient, etatorient, user_id, origine )
	SELECT
			dossierseps.personne_id AS personne_id,
			decisionsnonorientationsproseps58.typeorient_id AS typeorient_id,
			decisionsnonorientationsproseps58.structurereferente_id AS structurereferente_id,
			decisionsnonorientationsproseps58.referent_id AS referent_id,
			DATE_TRUNC( 'day', nonorientationsproseps58.created ) AS date_propo,
			DATE_TRUNC( 'day', commissionseps.dateseance ) AS date_valid,
			'Orienté' AS statut_orient,
			NULL AS rgorient,
			'decision' AS etatorient,
			nonorientationsproseps58.user_id AS user_id,
			'reorientation' AS origine
		FROM decisionsnonorientationsproseps58
			INNER JOIN passagescommissionseps ON ( decisionsnonorientationsproseps58.passagecommissionep_id  =passagescommissionseps.id )
			INNER JOIN dossierseps ON ( passagescommissionseps.dossierep_id = dossierseps.id )
			INNER JOIN nonorientationsproseps58 ON ( nonorientationsproseps58.dossierep_id = dossierseps.id )
			INNER JOIN commissionseps ON ( passagescommissionseps.commissionep_id = commissionseps.id )
		WHERE
			commissionseps.etatcommissionep = 'traite'
			AND passagescommissionseps.etatdossierep = 'traite'
			AND decisionsnonorientationsproseps58.decision = 'maintienref';

UPDATE orientsstructs SET rgorient = NULL;
UPDATE orientsstructs
	SET rgorient = (
		SELECT ( COUNT(orientsstructspcd.id) + 1 )
			FROM orientsstructs AS orientsstructspcd
			WHERE orientsstructspcd.personne_id = orientsstructs.personne_id
				AND orientsstructspcd.id <> orientsstructs.id
				AND orientsstructs.date_valid IS NOT NULL
				AND orientsstructspcd.date_valid IS NOT NULL
				AND (
					orientsstructspcd.date_valid < orientsstructs.date_valid
					OR ( orientsstructspcd.date_valid = orientsstructs.date_valid AND orientsstructspcd.id < orientsstructs.id )
				)
				AND orientsstructs.statut_orient = 'Orienté'
				AND orientsstructspcd.statut_orient = 'Orienté'
	)
	WHERE
		orientsstructs.date_valid IS NOT NULL
		AND orientsstructs.statut_orient = 'Orienté';

UPDATE orientsstructs
	SET origine = 'manuelle'
	WHERE rgorient = 1 AND origine = 'reorientation';

UPDATE orientsstructs
	SET origine = 'reorientation'
	WHERE rgorient > 1 AND origine <> 'reorientation';

CREATE UNIQUE INDEX orientsstructs_personne_id_rgorient_idx ON orientsstructs( personne_id, rgorient ) WHERE rgorient IS NOT NULL;

ALTER TABLE orientsstructs ADD CONSTRAINT orientsstructs_statut_orient_oriente_rgorient_not_null_chk CHECK (
	( statut_orient <> 'Orienté' AND rgorient IS NULL )
	OR ( statut_orient = 'Orienté' AND rgorient IS NOT NULL )
);

ALTER TABLE orientsstructs ADD CONSTRAINT orientsstructs_origine_check CHECK(
	( origine IS NULL AND date_valid IS NULL )
	OR (
		( origine IS NOT NULL AND date_valid IS NOT NULL )
		AND (
			( rgorient = 1 AND origine IN ( 'manuelle', 'cohorte' ) )
			OR ( rgorient > 1 AND origine = 'reorientation' )
		)
	)
);

-- On n'enregistrait pas le referent_id dans les orientsstructs
CREATE OR REPLACE FUNCTION public.update_orientsstructs_decisionsnonorientationsproseps58() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					decisionsnonorientationsproseps58.referent_id AS referent_id,
					orientsstructs.id AS orientstruct_id
				FROM decisionsnonorientationsproseps58
					INNER JOIN passagescommissionseps ON ( decisionsnonorientationsproseps58.passagecommissionep_id  =passagescommissionseps.id )
					INNER JOIN dossierseps ON ( passagescommissionseps.dossierep_id = dossierseps.id )
					INNER JOIN nonorientationsproseps58 ON ( nonorientationsproseps58.dossierep_id = dossierseps.id )
					INNER JOIN commissionseps ON ( passagescommissionseps.commissionep_id = commissionseps.id )
					INNER JOIN orientsstructs ON (
						dossierseps.personne_id = orientsstructs.personne_id
						AND decisionsnonorientationsproseps58.typeorient_id = orientsstructs.typeorient_id
						AND decisionsnonorientationsproseps58.structurereferente_id = orientsstructs.structurereferente_id
						AND decisionsnonorientationsproseps58.referent_id = orientsstructs.referent_id
						AND DATE_TRUNC( 'day', nonorientationsproseps58.created ) = orientsstructs.date_propo
						AND DATE_TRUNC( 'day', commissionseps.dateseance ) = orientsstructs.date_valid
						AND orientsstructs.statut_orient = 'Orienté'
						AND orientsstructs.etatorient = 'decision'
					)
				WHERE
					commissionseps.etatcommissionep = 'traite'
					AND passagescommissionseps.etatdossierep = 'traite'
					AND decisionsnonorientationsproseps58.decision IN ( 'maintienref', 'reorientation' )
					AND orientsstructs.referent_id IS NULL
		LOOP
			-- Mise à jour dans la table orientsstructs
			v_query := 'UPDATE orientsstructs SET referent_id = ' || v_row.referent_id || ' WHERE id = ' || v_row.orientstruct_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_orientsstructs_decisionsnonorientationsproseps58();
DROP FUNCTION public.update_orientsstructs_decisionsnonorientationsproseps58();

-- *****************************************************************************
COMMIT;
-- *****************************************************************************