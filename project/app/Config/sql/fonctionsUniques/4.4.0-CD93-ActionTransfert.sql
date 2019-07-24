SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

CREATE TABLE tempCorrespondanceFiliereAction (
  id serial NOT NULL,
  old_filiere_id integer NOT NULL,
  old_action_id integer NULL,
  new_filiere_id integer NOT NULL,
  new_action_id integer  NULL
);

/*
 * List des actions et filières a remplacer dans les fiches prescription
 *  */
INSERT INTO
tempCorrespondanceFiliereAction(id, old_filiere_id, old_action_id, new_filiere_id, new_action_id)
VALUES
('1','339','1244','379','1495')
;

/*
 * Cette fonction change l'action et la filière à la quelle sont liée les fiches de prescription d'une série d'actions spécifiques. 
 *
 */
CREATE OR REPLACE FUNCTION reset_FiliereAction() RETURNS void AS
$$
DECLARE
	--Declaration des variables
	-- des ids a remplacer
	replace RECORD;

	--Identifiant de la FP
	ID_FP RECORD;

	--Identifiant de la FP
	adresseprestatairefp93 RECORD;

BEGIN
-- Pour chaque ID de reference
	FOR replace IN (
		SELECT
		id,old_filiere_id,old_action_id,new_filiere_id, new_action_id
		FROM tempCorrespondanceFiliereAction
	 ) LOOP

		SELECT adresseprestatairefp93_id FROM actionsfps93 A WHERE replace.old_action_id = A.id LIMIT 1 INTO adresseprestatairefp93 ;

		-- pour chaque FP qui utilise le couple d'ID
		FOR ID_FP IN (
			SELECT id FROM fichesprescriptions93
			WHERE  filierefp93_id = replace.old_filiere_id
			AND actionfp93_id = replace.old_action_id
			AND created between '01-01-2019' and NOW()
		) LOOP

			-- on Update les FPs qui y sont liée avec les nouvelle valeurs
			UPDATE fichesprescriptions93 AS FP SET
				filierefp93_id = replace.new_filiere_id,
				actionfp93_id = replace.new_action_id,
				adresseprestatairefp93_id = adresseprestatairefp93.adresseprestatairefp93_id
			WHERE FP.id = ID_FP.id ;
		END LOOP;
	END LOOP;
END;
$$
LANGUAGE 'plpgsql';

SELECT reset_FiliereAction();

DROP FUNCTION reset_FiliereAction();
DROP TABLE IF EXISTS tempCorrespondanceFiliereAction CASCADE;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
