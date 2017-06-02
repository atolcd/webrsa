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

INSERT INTO typesrdv (libelle, modelenotifrdv, nbabsencesavpassageep, nbabsaveplaudition, motifpassageep) VALUES
	( 'Collectif', 'collectif', 0, 0, NULL ),
	( 'Individuel', 'individuel', 0, 0, NULL );

INSERT INTO thematiquesrdvs ( name, typerdv_id, created, modified )
	SELECT
			libelle,
			( SELECT id FROM typesrdv AS t WHERE t.libelle = 'Individuel' ),
			NOW(),
			NOW()
		FROM typesrdv
		WHERE
			libelle NOT IN ( 'Collectif', 'Individuel' )
			AND libelle NOT LIKE 'action collective%'
	UNION
	SELECT
			libelle,
			( SELECT id FROM typesrdv AS t WHERE t.libelle = 'Collectif' ),
			NOW(),
			NOW()
		FROM typesrdv
		WHERE
			libelle NOT IN ( 'Collectif', 'Individuel' )
			AND libelle LIKE 'action collective%';

INSERT INTO rendezvous_thematiquesrdvs ( rendezvous_id, thematiquerdv_id )
	SELECT
			rendezvous.id AS rendezvous_id,
			thematiquesrdvs.id AS thematiquerdv_id
		FROM rendezvous
			INNER JOIN typesrdv ON ( rendezvous.typerdv_id = typesrdv.id )
			INNER JOIN thematiquesrdvs ON ( thematiquesrdvs.name = typesrdv.libelle );

UPDATE rendezvous
	SET typerdv_id = (
		SELECT
			thematiquesrdvs.typerdv_id
			FROM thematiquesrdvs
			WHERE thematiquesrdvs.id IN (
				SELECT rendezvous_thematiquesrdvs.thematiquerdv_id
					FROM rendezvous_thematiquesrdvs
					WHERE rendezvous_thematiquesrdvs.rendezvous_id = rendezvous.id
					LIMIT 1
			)
	);

DELETE FROM typesrdv WHERE libelle NOT IN ( 'Collectif', 'Individuel' );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************