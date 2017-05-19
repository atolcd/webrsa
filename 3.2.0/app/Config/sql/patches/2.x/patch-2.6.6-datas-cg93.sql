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

-- 20140214: Suite à la correction du ticket #4195, il faut supprimer les courriers
-- d'orientation suite à déménagement stockés dans la table pdfs
DELETE FROM pdfs
	WHERE pdfs.modele = 'Orientstruct'
	AND fk_value IN (
		SELECT orientsstructs.id
			FROM orientsstructs
			WHERE orientsstructs.origine = 'demenagement'
	);

-- 20140214: Suite à la correction du ticket #4196, il faut supprimer les courriers
-- de relance pour non respect sanction suite à (nouveau) CER non renouvelé stockés
-- dans la table pdfs
DELETE FROM pdfs
	WHERE pdfs.modele = 'Relancenonrespectsanctionep93'
	AND fk_value IN (
		SELECT relancesnonrespectssanctionseps93.id
			FROM relancesnonrespectssanctionseps93
				INNER JOIN nonrespectssanctionseps93 ON (
					nonrespectssanctionseps93.id = relancesnonrespectssanctionseps93.nonrespectsanctionep93_id
				)
				INNER JOIN contratsinsertion ON (
					nonrespectssanctionseps93.contratinsertion_id = contratsinsertion.id
				)
			WHERE
				nonrespectssanctionseps93.origine = 'contratinsertion'
				AND contratsinsertion.duree_engag IS NULL
	);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
