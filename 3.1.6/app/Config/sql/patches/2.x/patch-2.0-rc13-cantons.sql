SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- ****************************************************************************
BEGIN;
-- ****************************************************************************

-- Suppression éventuelle de la colonne, dans le cas où on voudrait faire passer
-- le patch plusieurs fois, suite à des corrections.
-- ALTER TABLE cantons
-- 	DROP COLUMN zonegeographique_id;

-- Suppression éventuelle de la zone géographique "HORS PO", dans le cas où on voudrait faire passer
-- le patch plusieurs fois, suite à des corrections.
-- DELETE FROM zonesgeographiques
-- 	WHERE codeinsee = '00000';

-- Ajout de la colonne
ALTER TABLE cantons
	ADD COLUMN zonegeographique_id
	INTEGER DEFAULT NULL;

-- Ajout de la contrainte de clé étrangère
ALTER TABLE cantons
	ADD CONSTRAINT cantons_zonegeographique_id_fk
	FOREIGN KEY (zonegeographique_id) REFERENCES zonesgeographiques (id)
	ON UPDATE CASCADE
	ON DELETE CASCADE;

-- Ajout de la zone géographique "HORS PO" pour le CG 66 uniquement
CREATE OR REPLACE FUNCTION public.ajout_zonegeographique_cg66() RETURNS bool AS
$body$
	DECLARE
		v_row       record;
	BEGIN
		SELECT 1 INTO v_row FROM zonesgeographiques
			WHERE codeinsee LIKE '66%';
		IF FOUND THEN
			INSERT INTO zonesgeographiques (codeinsee, libelle) VALUES ( '0000', 'HORS PO' );
			RETURN 't';
		ELSE
			RETURN 't';
		END IF;
	END;
$body$
LANGUAGE plpgsql;

SELECT public.ajout_zonegeographique_cg66();
DROP FUNCTION public.ajout_zonegeographique_cg66();

-- Nettoyage des intitulés (suppression des espaces devant et derrière le mot)
UPDATE cantons
	SET canton = TRIM(canton);

UPDATE zonesgeographiques
	SET libelle = TRIM(libelle);

-- ------------------------------------------------------------------------------------------------------------
-- Pour chacune des zones dont le libellé n'est pas le même que la dénomination du canton dans la table cantons
-- ------------------------------------------------------------------------------------------------------------

UPDATE zonesgeographiques
	SET libelle = 'ARGELES'
	WHERE libelle = 'ARGELES-SUR-MER';

UPDATE zonesgeographiques
	SET libelle = 'ARLES SUR TECH'
	WHERE libelle = 'ARLES-SUR-TECH';


UPDATE zonesgeographiques
	SET libelle = 'CANET'
	WHERE libelle = 'CANET-EN-ROUSSILLON';


UPDATE zonesgeographiques
	SET libelle = 'LATOUR DE FRANCE'
	WHERE libelle = 'LATOUR-DE-FRANCE';


UPDATE zonesgeographiques
	SET libelle = 'MONT LOUIS'
	WHERE libelle = 'MONT-LOUIS';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN 1'
	WHERE libelle = 'PERPIGNAN  1ER CANTON';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN 2'
	WHERE libelle = 'PERPIGNAN  2E  CANTON';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN 3'
	WHERE libelle = 'PERPIGNAN  3E  CANTON';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN 4'
	WHERE libelle = 'PERPIGNAN  4E  CANTON';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN 5'
	WHERE libelle = 'PERPIGNAN  5E  CANTON';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN 6'
	WHERE libelle = 'PERPIGNAN  6E  CANTON';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN 7'
	WHERE libelle = 'PERPIGNAN  7E  CANTON';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN 8'
	WHERE libelle = 'PERPIGNAN  8E  CANTON';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN 9'
	WHERE libelle = 'PERPIGNAN  9E  CANTON';


UPDATE zonesgeographiques
	SET libelle = 'PRATS DE MOLLO'
	WHERE libelle = 'PRATS-DE-MOLLO-LA-PRESTE';


UPDATE zonesgeographiques
	SET libelle = 'SAINT ESTEVE'
	WHERE libelle = 'SAINT-ESTEVE';


UPDATE zonesgeographiques
	SET libelle = 'SAINT LAURENT DE LA SALANQUE'
	WHERE libelle = 'SAINT-LAURENT-DE-LA-SALANQUE';


UPDATE zonesgeographiques
	SET libelle = 'SAINT PAUL DE FENOUILLET'
	WHERE libelle = 'SAINT-PAUL-DE-FENOUILLET';

-- ------------------------------------------------------------------------------------------------------------
-- Association entre les cantons et les zonesgeographiques sur le libellé et le nom du canton,
-- afin de remplir l'entier de la clé étrangère
-- ------------------------------------------------------------------------------------------------------------

UPDATE cantons
	SET zonegeographique_id = (
		SELECT zonesgeographiques.id
			FROM zonesgeographiques
			WHERE zonesgeographiques.libelle = cantons.canton
			LIMIT 1
	);

-- ------------------------------------------------------------------------------------------------------------
-- On peut remettre les libellés des zonesgeographiques comme avant si besoin
-- ------------------------------------------------------------------------------------------------------------

UPDATE zonesgeographiques
	SET libelle = 'ARGELES-SUR-MER'
	WHERE libelle = 'ARGELES';


UPDATE zonesgeographiques
	SET libelle = 'ARLES-SUR-TECH'
	WHERE libelle = 'ARLES SUR TECH';


UPDATE zonesgeographiques
	SET libelle = 'CANET-EN-ROUSSILLON'
	WHERE libelle = 'CANET';


UPDATE zonesgeographiques
	SET libelle = 'LATOUR-DE-FRANCE'
	WHERE libelle = 'LATOUR DE FRANCE';


UPDATE zonesgeographiques
	SET libelle = 'MONT-LOUIS'
	WHERE libelle = 'MONT LOUIS';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN  1ER CANTON'
	WHERE libelle = 'PERPIGNAN 1';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN  2E  CANTON'
	WHERE libelle = 'PERPIGNAN 2';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN  3E  CANTON'
	WHERE libelle = 'PERPIGNAN 3';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN  4E  CANTON'
	WHERE libelle = 'PERPIGNAN 4';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN  5E  CANTON'
	WHERE libelle = 'PERPIGNAN 5';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN  6E  CANTON'
	WHERE libelle = 'PERPIGNAN 6';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN  7E  CANTON'
	WHERE libelle = 'PERPIGNAN 7';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN  8E  CANTON'
	WHERE libelle = 'PERPIGNAN 8';


UPDATE zonesgeographiques
	SET libelle = 'PERPIGNAN  9E  CANTON'
	WHERE libelle = 'PERPIGNAN 9';


UPDATE zonesgeographiques
	SET libelle = 'PRATS-DE-MOLLO-LA-PRESTE'
	WHERE libelle = 'PRATS DE MOLLO';


UPDATE zonesgeographiques
	SET libelle = 'SAINT-ESTEVE'
	WHERE libelle = 'SAINT ESTEVE';


UPDATE zonesgeographiques
	SET libelle = 'SAINT-LAURENT-DE-LA-SALANQUE'
	WHERE libelle = 'SAINT LAURENT DE LA SALANQUE';


UPDATE zonesgeographiques
	SET libelle = 'SAINT-PAUL-DE-FENOUILLET'
	WHERE libelle = 'SAINT PAUL DE FENOUILLET';

-- Maintenant que tout a été complété, on peut mettre une contrainte sur la colonne zonegeographique_id de la table cantons
UPDATE cantons
	SET zonegeographique_id = (
		SELECT zonesgeographiques.id
			FROM zonesgeographiques
			WHERE zonesgeographiques.codeinsee = cantons.numcomptt
			LIMIT 1
	)
	WHERE zonegeographique_id IS NULL;

ALTER TABLE cantons ALTER COLUMN zonegeographique_id DROP DEFAULT;
ALTER TABLE cantons ALTER COLUMN zonegeographique_id SET NOT NULL;

-- ****************************************************************************
COMMIT;
-- ****************************************************************************