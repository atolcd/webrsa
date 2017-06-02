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

DROP INDEX IF EXISTS prestations_allocataire_rsa_idx;
CREATE INDEX prestations_allocataire_rsa_idx
	ON prestations (personne_id, natprest, rolepers)
	WHERE natprest = 'RSA' AND rolepers IN ( 'DEM', 'CJT' );

DROP INDEX IF EXISTS adressesfoyers_actuelle_rsa_idx;
CREATE INDEX adressesfoyers_actuelle_rsa_idx
	ON adressesfoyers (foyer_id, rgadr)
	WHERE rgadr = '01';

DROP INDEX IF EXISTS situationsdossiersrsa_etatdosrsa_ouvert_idx;
CREATE INDEX situationsdossiersrsa_etatdosrsa_ouvert_idx
	ON situationsdossiersrsa (dossier_id, etatdosrsa)
	WHERE etatdosrsa IN ( '2', '3', '4' );


-------------------------- Ajout du 05/10/2010 -----------------------------

UPDATE apres
	SET eligibiliteapre = 'O'
	WHERE eligibiliteapre = 'N';

-------------------------- Ajout du 08/10/2010 -----------------------------

-- Il est possible que vous ayez à commenter la ou les commande(s) suivante:
CREATE OR REPLACE FUNCTION create_field_controlesadministratifs_foyer_id() RETURNS VOID AS
$$
BEGIN
	IF NOT EXISTS(
		SELECT *
			FROM pg_namespace n, pg_class c, pg_attribute a
			WHERE
				nspname = 'public'
				AND c.relnamespace = n.oid
				AND a.attrelid = c.oid
				AND relname = 'controlesadministratifs'
				AND attname = 'foyer_id'
	)
	THEN
		ALTER TABLE controlesadministratifs ADD COLUMN foyer_id INTEGER NOT NULL;
	END IF;
END;
$$
LANGUAGE 'plpgsql';

SELECT create_field_controlesadministratifs_foyer_id();
DROP FUNCTION create_field_controlesadministratifs_foyer_id();

-- -----------------------------------------------------------------------------

ALTER TABLE controlesadministratifs
	ADD CONSTRAINT controlesadministratifs_foyer_id_fk
	FOREIGN KEY (foyer_id) REFERENCES foyers (id)
	ON UPDATE CASCADE
	ON DELETE CASCADE;

CREATE OR REPLACE FUNCTION create_index_controlesadministratifs_foyer_id_idx() RETURNS VOID AS
$$
BEGIN
	IF NOT EXISTS(
		SELECT *
			FROM pg_catalog.pg_class c
			WHERE
				c.relkind IN ('i','')
				AND c.relname = 'controlesadministratifs_foyer_id_idx'
	)
	THEN
		CREATE INDEX controlesadministratifs_foyer_id_idx ON controlesadministratifs (foyer_id);
	END IF;
END;
$$
LANGUAGE 'plpgsql';

SELECT create_index_controlesadministratifs_foyer_id_idx();
DROP FUNCTION create_index_controlesadministratifs_foyer_id_idx();


-- Corrections mauvaises valeurs par défaut pour le type time -> voir avec Gaétan
-- ALTER TABLE comitesapres ALTER COLUMN heurecomite SET DEFAULT now();


-- *****************************************************************************
COMMIT;
-- *****************************************************************************