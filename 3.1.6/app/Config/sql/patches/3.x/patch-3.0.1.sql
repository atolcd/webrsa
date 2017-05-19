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
-- 20160713: correction des entrées de la table personnes_referents pour
-- lesquelles la structure de la personne chargée du suivi ne correspond pas à
-- la structure de suivi enregistrée.
--------------------------------------------------------------------------------

UPDATE personnes_referents
	SET structurereferente_id = (
		SELECT referents.structurereferente_id
			FROM referents
			WHERE referents.id = referent_id
	)
	WHERE
		personnes_referents.id IN (
			SELECT
					"PersonneReferent"."id"
				FROM "public"."personnes_referents" AS "PersonneReferent"
					INNER JOIN "public"."personnes" AS "Personne" ON ( "PersonneReferent"."personne_id" = "Personne"."id" )
					INNER JOIN "public"."referents" AS "Referent" ON ( "PersonneReferent"."referent_id" = "Referent"."id" )
					INNER JOIN "public"."structuresreferentes" AS "Structurereferente" ON ( "PersonneReferent"."structurereferente_id" = "Structurereferente"."id" )
					INNER JOIN "public"."structuresreferentes" AS "Structurereferentereferent" ON ( "Referent"."structurereferente_id" = "Structurereferentereferent"."id" )
				WHERE
					"PersonneReferent"."structurereferente_id" <> "Referent"."structurereferente_id"
		);


--------------------------------------------------------------------------------
-- Liens entre referents
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS derniersreferents;

CREATE TABLE derniersreferents (
	id SERIAL NOT NULL PRIMARY KEY,
	referent_id	INTEGER NOT NULL REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
	prevreferent_id INTEGER REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
	dernierreferent_id INTEGER REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************