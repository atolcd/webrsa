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

-- Mise à jour de la table nonorientes66 pour supprimer le lien avec l'enregistrement
-- de la table orientsstructs que l'on voudrait supprimer (cf. requête suivante).
-- En lien avec le ticket #41544 (Atol CD)
UPDATE nonorientes66
	SET orientstruct_id = NULL
	WHERE orientstruct_id IN (
		SELECT
				orientsstructs.id
			FROM orientsstructs
			WHERE
				(
					statut_orient = 'Non orienté'
					OR statut_orient IS NULL
				)
				AND (
					( SELECT COUNT(*) FROM bilansparcours66 WHERE bilansparcours66.orientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM defautsinsertionseps66 WHERE defautsinsertionseps66.nvorientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM defautsinsertionseps66 WHERE defautsinsertionseps66.orientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM nonorientationsproseps58 WHERE nonorientationsproseps58.orientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM nonorientationsproseps93 WHERE nonorientationsproseps93.orientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM nonorientationsproscovs58 WHERE nonorientationsproscovs58.nvorientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM nonorientationsproscovs58 WHERE nonorientationsproscovs58.orientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM nonorientationsproseps58 WHERE nonorientationsproseps58.nvorientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM nonorientationsproseps66 WHERE nonorientationsproseps66.orientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM nonorientationsproseps93 WHERE nonorientationsproseps93.nvorientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM nonrespectssanctionseps93 WHERE nonrespectssanctionseps93.orientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM orientsstructs_servicesinstructeurs WHERE orientsstructs_servicesinstructeurs.orientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM proposnonorientationsproscovs58 WHERE proposnonorientationsproscovs58.nvorientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM proposnonorientationsproscovs58 WHERE proposnonorientationsproscovs58.orientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM proposorientationscovs58 WHERE proposorientationscovs58.nvorientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM proposorientssocialescovs58 WHERE proposorientssocialescovs58.nvorientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM regressionsorientationscovs58 WHERE regressionsorientationscovs58.nvorientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM regressionsorientationscovs58 WHERE regressionsorientationscovs58.orientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM regressionsorientationseps58 WHERE regressionsorientationseps58.nvorientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM reorientationseps93 WHERE reorientationseps93.nvorientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM saisinesbilansparcourseps66 WHERE saisinesbilansparcourseps66.nvorientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM reorientationseps93 WHERE reorientationseps93.orientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM sanctionseps58 WHERE sanctionseps58.orientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM transfertspdvs93 WHERE transfertspdvs93.nv_orientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM transfertspdvs93 WHERE transfertspdvs93.vx_orientstruct_id = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM pdfs WHERE pdfs.modele = 'Orientstruct' AND pdfs.fk_value = orientsstructs.id)
					+ ( SELECT COUNT(*) FROM fichiersmodules WHERE fichiersmodules.modele = 'Orientstruct' AND fichiersmodules.fk_value = orientsstructs.id)
				) = 0
	);

--------------------------------------------------------------------------------

-- Suppression des enregistrements de la table orientsstructs qui ne sont pas
-- "Orienté" et qui ne sont liés à aucun autre enregistrement de l'application.
-- En lien avec le ticket #41544 (Atol CD)
DELETE
	FROM orientsstructs
	WHERE
		(
			statut_orient = 'Non orienté'
			OR statut_orient IS NULL
		)
		AND (
			( SELECT COUNT(*) FROM bilansparcours66 WHERE bilansparcours66.orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM defautsinsertionseps66 WHERE defautsinsertionseps66.nvorientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM defautsinsertionseps66 WHERE defautsinsertionseps66.orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM nonorientationsproseps58 WHERE nonorientationsproseps58.orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM nonorientationsproseps93 WHERE nonorientationsproseps93.orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM nonorientationsproscovs58 WHERE nonorientationsproscovs58.nvorientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM nonorientationsproscovs58 WHERE nonorientationsproscovs58.orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM nonorientationsproseps58 WHERE nonorientationsproseps58.nvorientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM nonorientationsproseps66 WHERE nonorientationsproseps66.orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM nonorientationsproseps93 WHERE nonorientationsproseps93.nvorientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM nonorientes66 WHERE nonorientes66.orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM nonrespectssanctionseps93 WHERE nonrespectssanctionseps93.orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM orientsstructs_servicesinstructeurs WHERE orientsstructs_servicesinstructeurs.orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM proposnonorientationsproscovs58 WHERE proposnonorientationsproscovs58.nvorientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM proposnonorientationsproscovs58 WHERE proposnonorientationsproscovs58.orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM proposorientationscovs58 WHERE proposorientationscovs58.nvorientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM proposorientssocialescovs58 WHERE proposorientssocialescovs58.nvorientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM regressionsorientationscovs58 WHERE regressionsorientationscovs58.nvorientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM regressionsorientationscovs58 WHERE regressionsorientationscovs58.orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM regressionsorientationseps58 WHERE regressionsorientationseps58.nvorientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM reorientationseps93 WHERE reorientationseps93.nvorientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM saisinesbilansparcourseps66 WHERE saisinesbilansparcourseps66.nvorientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM reorientationseps93 WHERE reorientationseps93.orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM sanctionseps58 WHERE sanctionseps58.orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM transfertspdvs93 WHERE transfertspdvs93.nv_orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM transfertspdvs93 WHERE transfertspdvs93.vx_orientstruct_id = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM pdfs WHERE pdfs.modele = 'Orientstruct' AND pdfs.fk_value = orientsstructs.id)
			+ ( SELECT COUNT(*) FROM fichiersmodules WHERE fichiersmodules.modele = 'Orientstruct' AND fichiersmodules.fk_value = orientsstructs.id)
		) = 0;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************