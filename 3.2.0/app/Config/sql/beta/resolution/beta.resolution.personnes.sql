/*
* Résolution des problèmes et mise en place de vérifications pour les erreurs
* sur les tables ...
*/

/*
* NOTICE:  ALTER TABLE / ADD UNIQUE will create implicit index "prestation_unique_personne_natprest_rolepers" for table "prestations"
*/

BEGIN;

-- Prestations de même nature et de même rôle pour une personne donnée
DELETE FROM prestations
	WHERE prestations.id IN (
		SELECT p1.id
			FROM prestations p1,
				prestations p2
			WHERE p1.id < p2.id
				AND p1.personne_id = p2.personne_id
				AND p1.natprest = p2.natprest
				AND p1.rolepers = p2.rolepers
	);

-- non demandeurs ou non conjoints RSA, seule personne de 18ans ou plus dans son foyers possedant des suivisappuisorientation
UPDATE prestations
	SET rolepers = 'DEM'
	WHERE prestations.natprest = 'RSA'
		AND prestations.personne_id IN (
			SELECT "Personne"."id"
				FROM personnes AS "Personne"
				WHERE "Personne"."id" IN (
					SELECT DISTINCT( suivisappuisorientation.personne_id )
						FROM suivisappuisorientation
					EXCEPT
					SELECT DISTINCT( prestations.personne_id )
						FROM prestations
						WHERE prestations.natprest = 'RSA'
							AND prestations.rolepers IN ( 'DEM', 'CJT' )
				)
				AND AGE( "Personne"."dtnai" ) >= '18 year'
				AND (
					SELECT COUNT(personnes.id)
						FROM personnes
						WHERE personnes.foyer_id = "Personne"."foyer_id"
							AND AGE( personnes.dtnai ) >= '18 year'
				) = 1
		);

-- Prévention: ...
ALTER TABLE prestations ADD CONSTRAINT prestation_unique_personne_natprest_rolepers UNIQUE (personne_id, natprest, rolepers);

COMMIT;