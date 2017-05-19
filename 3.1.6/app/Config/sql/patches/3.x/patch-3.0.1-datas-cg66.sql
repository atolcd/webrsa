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

-- Ajoute une ligne pour chaque nom / prenom identique
CREATE OR REPLACE FUNCTION public.garnissagederniersreferents() RETURNS VOID AS
$$
	DECLARE
		v_row record;
	BEGIN
		FOR v_row IN
			SELECT id, nom, prenom FROM referents ORDER BY id ASC
		LOOP
			INSERT INTO derniersreferents (referent_id, prevreferent_id, dernierreferent_id) VALUES
			(
				v_row.id,
				(
					SELECT referents.id FROM referents 
					WHERE referents.id < v_row.id
					AND referents.nom = v_row.nom
					AND referents.prenom = v_row.prenom
					ORDER BY referents.id DESC
					LIMIT 1
				),
				v_row.id
			);

			UPDATE derniersreferents SET dernierreferent_id = v_row.id 
			WHERE dernierreferent_id IN (SELECT a.prevreferent_id FROM derniersreferents AS a WHERE a.dernierreferent_id = v_row.id);
			
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.garnissagederniersreferents();
DROP FUNCTION public.garnissagederniersreferents();

--------------------------------------------------------------------------------
-- Correction des erreurs causés par la modification de la structure referente d'un referent
-- Si un enregistrement porte un structurereferente_id != de celui du référent, 
-- un nouveau referent est crée avec cette ancienne structure référente
-- Toutes les relations avec le couple referent/structurereferente sont ajouté/modifié avec le nouveau referent
--------------------------------------------------------------------------------

-- Ajout de champs created de façon temporaire pour faciliter le traitement de corrige_referents()
ALTER TABLE entretiens ADD COLUMN created DATE;
UPDATE entretiens SET created = dateentretien;

ALTER TABLE personnes_referents ADD COLUMN created DATE;
UPDATE personnes_referents SET created = dddesignation;

ALTER TABLE rendezvous ADD COLUMN created_old DATE;
UPDATE rendezvous SET created_old = created;
UPDATE rendezvous SET created = daterdv WHERE created IS NULL;

ALTER TABLE apres ADD COLUMN created DATE;
UPDATE apres SET created = (SELECT datemontantpropose FROM aidesapres66 WHERE aidesapres66.apre_id = apres.id LIMIT 1);

ALTER TABLE orientsstructs ADD COLUMN created DATE;
UPDATE orientsstructs SET created = COALESCE(date_propo, date_valid);

CREATE TABLE tmp_table (
	id SERIAL NOT NULL PRIMARY KEY,
	referent_id	INTEGER NOT NULL REFERENCES referents(id),
	structurereferente_id INTEGER NOT NULL REFERENCES structuresreferentes(id),
	structureactuelle_id INTEGER NOT NULL REFERENCES structuresreferentes(id),
	created TIMESTAMP WITHOUT TIME ZONE
);

CREATE TABLE tmp_table2 (
	id SERIAL NOT NULL PRIMARY KEY,
	referent_id	INTEGER NOT NULL REFERENCES referents(id),
	structurereferente_id INTEGER NOT NULL REFERENCES structuresreferentes(id),
	structureactuelle_id INTEGER NOT NULL REFERENCES structuresreferentes(id),
	created TIMESTAMP WITHOUT TIME ZONE
);

CREATE OR REPLACE FUNCTION corrige_referents() RETURNS void AS
$$
DECLARE
		liste_table record;
		tmp_table_record record;
		tmp_table2_record record;
BEGIN
		FOR liste_table IN
			SELECT UNNEST(
				ARRAY['bilansparcours66',
					'decisionsdefautsinsertionseps66',
					'decisionssaisinesbilansparcourseps66',
					'entretiens',
					'personnes_referents',
					'rendezvous',
					'apres',
					'contratsinsertion',
					'orientsstructs']
			) AS tablename

		LOOP

			-- Création de la liste des changements de structures d'un referent
			EXECUTE 'INSERT INTO tmp_table (referent_id, structurereferente_id, structureactuelle_id, created)
				SELECT 
					a.referent_id AS referent_id,
					a.structurereferente_id AS structurereferente_id,
					b.structurereferente_id AS structureactuelle_id,
					a.created AS created

				FROM ' || liste_table.tablename || ' AS a
				INNER JOIN referents AS b ON a.referent_id = b.id
				WHERE a.structurereferente_id != b.structurereferente_id';

		END LOOP;

		-- On rempli la table tmp_table2 sans doublons (referent_id et structurereferent_id identique)
		FOR tmp_table_record IN
			SELECT * FROM tmp_table ORDER BY created ASC

		LOOP
			IF (NOT EXISTS(
				SELECT id FROM tmp_table2
				WHERE referent_id = tmp_table_record.referent_id
				AND structurereferente_id = tmp_table_record.structurereferente_id
			)) THEN 
				INSERT INTO tmp_table2 (referent_id, structurereferente_id, structureactuelle_id)
				VALUES (tmp_table_record.referent_id, tmp_table_record.structurereferente_id, tmp_table_record.structureactuelle_id);
			END IF;
		END LOOP;


		FOR tmp_table2_record IN
			SELECT * FROM tmp_table2 ORDER BY created DESC NULLS LAST

		LOOP

			-- On copie les référents affecté aux anciennes structures
			EXECUTE 'INSERT INTO referents (structurereferente_id, nom, prenom, numero_poste, email, qual, fonction, actif, datecloture)
				(SELECT ' || tmp_table2_record.structurereferente_id || ', nom, prenom, numero_poste, email, qual, fonction, ''N'', NOW()::date
				FROM referents WHERE id = ' || tmp_table2_record.referent_id || ' LIMIT 1);';

			-- On ajoute la nouvelle entrée dans derniersreferents et on met à jour la plus ancienne
			UPDATE derniersreferents
			SET prevreferent_id = currval(pg_get_serial_sequence('referents', 'id')) 
			WHERE derniersreferents.referent_id = tmp_table2_record.referent_id;

			INSERT INTO derniersreferents (referent_id, prevreferent_id, dernierreferent_id)
			VALUES (currval(pg_get_serial_sequence('referents', 'id')), null, (
				SELECT a.dernierreferent_id FROM derniersreferents AS a
				WHERE a.referent_id = tmp_table2_record.referent_id
				LIMIT 1
			));

			-- On applique pour chaque couples referent_id / anciennestructurereferente_id le nouveau referent_id
			FOR liste_table IN
				SELECT UNNEST(
					ARRAY['bilansparcours66',
					'decisionsdefautsinsertionseps66',
					'decisionssaisinesbilansparcourseps66',
					'entretiens',
					'personnes_referents',
					'rendezvous',
					'apres',
					'contratsinsertion',
					'orientsstructs']
				) AS tablename

			LOOP

				-- Mise à jour des enregistrements avec le nouveau referent_id
				EXECUTE 'UPDATE ' || liste_table.tablename || ' 
					SET referent_id = ' || currval(pg_get_serial_sequence('referents', 'id')) || '
					WHERE referent_id = ' || tmp_table2_record.referent_id || '
					AND structurereferente_id = ' || tmp_table2_record.structurereferente_id || ';';

			END LOOP;

		END LOOP;

END;
$$
LANGUAGE plpgsql;

SELECT corrige_referents();

-- On restaure le schema de la base
DROP FUNCTION corrige_referents();
ALTER TABLE entretiens DROP COLUMN created;
ALTER TABLE personnes_referents DROP COLUMN created;
UPDATE rendezvous SET created = created_old;
ALTER TABLE rendezvous DROP COLUMN created_old;
ALTER TABLE apres DROP COLUMN created;
ALTER TABLE orientsstructs DROP COLUMN created;
DROP TABLE tmp_table;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************