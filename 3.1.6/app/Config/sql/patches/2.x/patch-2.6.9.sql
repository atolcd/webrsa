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

-- Il faut scinder le niveau 1201 des DSP pour les CER.
SELECT public.alter_table_drop_constraint_if_exists( 'public', 'cers93', 'cers93_nivetu_in_list_chk' );
ALTER TABLE cers93 ALTER COLUMN nivetu TYPE VARCHAR(5);
-- On corrige les CER existants avec la valeur exacte suivant l'intitulé qui était visible
UPDATE cers93 SET nivetu = '1201a' WHERE nivetu = '1201';
UPDATE cers93 SET nivetu = '1201b' WHERE nivetu = '1202';
UPDATE cers93 SET nivetu = '1202' WHERE nivetu = '1203';
UPDATE cers93 SET nivetu = '1203' WHERE nivetu = '1204';
UPDATE cers93 SET nivetu = '1204' WHERE nivetu = '1205';
UPDATE cers93 SET nivetu = '1205' WHERE nivetu = '1206';
UPDATE cers93 SET nivetu = '1206' WHERE nivetu = '1207';
UPDATE cers93 SET nivetu = '1207' WHERE nivetu = '1208';
ALTER TABLE cers93 ADD CONSTRAINT cers93_nivetu_in_list_chk CHECK ( cakephp_validate_in_list( nivetu, ARRAY['1201a', '1201b', '1202', '1203', '1204', '1205', '1206', '1207'] ) );

-- 20140526: correction, à présent, la structure référente du questionnaire D2 sera celle stockée dans le RDV lié au D1 (lui-même lié au D2)
SELECT alter_table_drop_column_if_exists( 'public', 'questionnairesd2pdvs93', 'structurereferente_id' );

-- 20140528: correction, certaines decisions de saisines de bilans de parcours au niveau cg sont sans décision au niveau ep.
INSERT INTO decisionssaisinesbilansparcourseps66 ( etape, decision, typeorient_id, structurereferente_id, commentaire, created, modified, referent_id, passagecommissionep_id, raisonnonpassage, maintienorientparcours, changementrefparcours, reorientation, typeorientprincipale_id, user_id )
	SELECT
			'ep' AS etape,
			decisioncg.decision,
			decisioncg.typeorient_id,
			decisioncg.structurereferente_id,
			decisioncg.commentaire,
			( decisioncg.created - interval '10 minutes' ) AS created,
			( decisioncg.modified - interval '10 minutes' ) AS modified,
			decisioncg.referent_id,
			decisioncg.passagecommissionep_id,
			decisioncg.raisonnonpassage,
			decisioncg.maintienorientparcours,
			decisioncg.changementrefparcours,
			decisioncg.reorientation,
			decisioncg.typeorientprincipale_id,
			decisioncg.user_id
		FROM decisionssaisinesbilansparcourseps66 AS decisioncg
		WHERE
			decisioncg.etape = 'cg'
			AND decisioncg.passagecommissionep_id NOT IN (
				SELECT decisionep.passagecommissionep_id
					FROM decisionssaisinesbilansparcourseps66 AS decisionep
					WHERE
						decisionep.etape = 'ep'
						AND decisioncg.passagecommissionep_id = decisionep.passagecommissionep_id
			);

-- 20140528: correction: mise à jour des positions du bilan en 'traite' lorsque le dossier d'EP associé a été traité au niveau CG
UPDATE bilansparcours66
	SET positionbilan = 'traite'
	WHERE id IN (
		SELECT
				bilansparcours66.id
			FROM
				saisinesbilansparcourseps66
				INNER JOIN dossierseps ON ( saisinesbilansparcourseps66.dossierep_id = dossierseps.id )
				INNER JOIN passagescommissionseps ON (
					dossierseps.id = passagescommissionseps.dossierep_id
					AND passagescommissionseps.etatdossierep IN ( 'traite', 'annule', 'reporte' )
				)
				INNER JOIN decisionssaisinesbilansparcourseps66 ON (
					decisionssaisinesbilansparcourseps66.passagecommissionep_id = passagescommissionseps.id
					AND decisionssaisinesbilansparcourseps66.etape = 'cg'
				)
				INNER JOIN bilansparcours66 ON ( bilansparcours66.id = saisinesbilansparcourseps66.bilanparcours66_id )
			WHERE
				( decisionssaisinesbilansparcourseps66.decision IN ( 'maintien', 'reorientation' ) AND bilansparcours66.positionbilan <> 'traite' )
		);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************