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

-- INFO: pour les développeurs seulement
-- TRUNCATE informationspe CASCADE;
-- TRUNCATE historiqueetatspe CASCADE;
-- 
-- ALTER SEQUENCE informationspe_id_seq RESTART WITH 1;
-- ALTER SEQUENCE historiqueetatspe_id_seq RESTART WITH 1;
-- 
-- -- *****************************************************************************
-- 
-- INSERT INTO informationspe ( nir, nom, prenom, dtnai )
-- 	SELECT
-- 		NULL AS nir,
-- 		nom,
-- 		prenom,
-- 		dtnai
-- 	FROM personnes
-- 	WHERE personnes.id IN ( 15, 92, 48, 74, 117 )
-- 	ORDER BY personnes.id;
-- 
-- INSERT INTO historiqueetatspe ( informationpe_id, identifiantpe, date, etat ) VALUES
-- 	( 1, '0611044290Y', '2011-01-01', 'radiation' ),
-- 	( 2, '0611717975P', '2011-01-01', 'radiation' ),
-- 	( 3, '0613061080L', '2011-01-01', 'radiation' ),
-- 	( 4, '0611309465G', '2011-01-01', 'radiation' ),
-- 	( 5, '0610905944X', '2011-01-01', 'radiation' )
-- ;

TRUNCATE listesanctionseps58 CASCADE;
ALTER SEQUENCE listesanctionseps58_id_seq RESTART WITH 1;
INSERT INTO listesanctionseps58 (rang, sanction, duree) VALUES
    ('1', '-100€', '1'),
    ('2', '-50%', '4'),
    ('3', 'Suspension totale', '4')
;

-- *****************************************************************************
-- 20110317
-- *****************************************************************************

-- 2. Les utilisateurs du CCAS de Nevers ne doivent avoir accès qu'aux dossiers :
--     - des couples sans enfants
--     - des bénéficiaires seuls sans enfants

UPDATE servicesinstructeurs
	SET sqrecherche = '(
		-- Couples sans enfants
		(
			(
				SELECT COUNT(personnes.id)
					FROM personnes
						INNER JOIN prestations ON (
							personnes.id = prestations.personne_id
							AND prestations.natprest = ''RSA''
							AND prestations.rolepers IN ( ''DEM'', ''CJT'' )
						)
					WHERE personnes.foyer_id = "Foyer"."id"
			) = 2
			AND
			(
				SELECT COUNT(personnes.id)
					FROM personnes
						INNER JOIN prestations ON (
							personnes.id = prestations.personne_id
							AND prestations.natprest = ''RSA''
							AND prestations.rolepers NOT IN ( ''DEM'', ''CJT'' )
						)
					WHERE personnes.foyer_id = "Foyer"."id"
			) = 0
		)
		OR
		-- Bénéficiaires seuls sans enfants
		(
			(
				SELECT COUNT(personnes.id)
					FROM personnes
						INNER JOIN prestations ON (
							personnes.id = prestations.personne_id
							AND prestations.natprest = ''RSA''
							AND prestations.rolepers IN ( ''DEM'' )
						)
					WHERE personnes.foyer_id = Foyer.id
			) = 1
			AND
			(
				SELECT COUNT(personnes.id)
					FROM personnes
						INNER JOIN prestations ON (
							personnes.id = prestations.personne_id
							AND prestations.natprest = ''RSA''
						)
					WHERE personnes.foyer_id = Foyer.id
			) = 1
		)
	)'
	WHERE servicesinstructeurs.id = 13;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************