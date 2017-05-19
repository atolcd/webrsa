SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

BEGIN;

ALTER TABLE users ADD COLUMN numvoie VARCHAR(6);
ALTER TABLE users ADD COLUMN typevoie VARCHAR(4);
ALTER TABLE users ADD COLUMN nomvoie VARCHAR(25);
ALTER TABLE users ADD COLUMN compladr VARCHAR(40);
ALTER TABLE users ADD COLUMN codepos VARCHAR(5);
ALTER TABLE users ADD COLUMN ville VARCHAR(50);


ALTER TABLE structuresreferentes ADD COLUMN orientation type_no DEFAULT 'O';
ALTER TABLE structuresreferentes ADD COLUMN pdo type_no DEFAULT 'O';

CREATE TYPE type_type_demande AS ENUM ( 'DOD', 'DRD' );
ALTER TABLE contratsinsertion ADD COLUMN type_demande type_type_demande;

CREATE TYPE type_num_contrat AS ENUM ( 'PRE', 'REN' );
ALTER TABLE contratsinsertion ADD COLUMN num_contrat type_num_contrat;

UPDATE contratsinsertion
    SET num_contrat = 'REN'
    WHERE numcontrat = 'Renouvellement';

UPDATE contratsinsertion
    SET num_contrat = 'PRE'
    WHERE numcontrat = 'Premier contrat';

ALTER TABLE contratsinsertion DROP COLUMN numcontrat;
-- *****************************************************************************
--      La partie suivante est à utiliser uniquement en cas de doublons
--      de pièces liées au niveau des aides de l'APRE pour le CG93
-- Ces doublons ont été constatés lors de la récupération de certaines bases
-- *****************************************************************************

-- DELETE FROM piecesaccscreaentr WHERE id > '2';
-- DELETE FROM piecesactsprofs WHERE id > '3';
-- DELETE FROM piecesamenagslogts WHERE id > '7';
-- DELETE FROM piecesformsqualifs WHERE id > '3';
-- DELETE FROM piecespermisb WHERE id > '2';
-- --------------------------------------------------------------------------------

ALTER TABLE orientsstructs ADD COLUMN referent_id INTEGER REFERENCES referents(id) DEFAULT NULL;

-- --------------------------------------------------------------------------------
-- Ajout dans la table calculsdroitsrsa des entrées pour les personnes dont les ressources sont inexistantes
-- --------------------------------------------------------------------------------

INSERT INTO calculsdroitsrsa ( personne_id, mtpersressmenrsa, mtpersabaneursa, toppersdrodevorsa )
	SELECT DISTINCT(ressources.personne_id) AS personne_id,
			0 AS mtpersressmenrsa,
			0 AS mtpersabaneursa,
			CAST ( '1' AS type_booleannumber ) AS toppersdrodevorsa
		FROM ressources
			WHERE ressources.topressnul = true
				AND ressources.personne_id NOT IN(
					SELECT calculsdroitsrsa.personne_id
						FROM calculsdroitsrsa
				)
				AND ressources.id IN (
					SELECT tmpressources.id FROM (
						SELECT ressources.id, MAX(ressources.dfress)
							FROM ressources
							GROUP BY ressources.personne_id, ressources.id
					) AS tmpressources
				);

-- --------------------------------------------------------------------------------
-- Ajout dans la table calculsdroitsrsa des entrées pour les personnes dont les ressources sont existantes
-- mais dont les ressources mensuelles sont inexistantes et qui ne sont pas encore rentrées
-- --------------------------------------------------------------------------------

--> 166239
-- SELECT COUNT(DISTINCT(personne_id))
-- 	FROM prestations
-- 		INNER JOIN personnes ON (
-- 			prestations.personne_id = personnes.id
-- 			AND prestations.natprest = 'RSA'
-- 			AND prestations.rolepers IN ( 'DEM', 'CJT' )
-- 		);

--> 160032
-- SELECT COUNT(DISTINCT(personne_id))
-- 	FROM calculsdroitsrsa;

--> 34255
-- SELECT COUNT(DISTINCT(personne_id))
-- 	FROM prestations
-- 		INNER JOIN personnes ON (
-- 			prestations.personne_id = personnes.id
-- 			AND prestations.natprest = 'RSA'
-- 			AND prestations.rolepers IN ( 'DEM', 'CJT' )
-- 		)
-- 	WHERE prestations.personne_id NOT IN (
-- 		SELECT calculsdroitsrsa.personne_id
-- 			FROM calculsdroitsrsa
-- 	);

-- FIXME: 34255
INSERT INTO calculsdroitsrsa (personne_id, toppersdrodevorsa, mtpersressmenrsa, mtpersabaneursa)
	SELECT prestations.personne_id AS personne_id,
			'1' AS toppersdrodevorsa,
			0 AS mtpersressmenrsa,
			0 AS mtpersabaneursa
		FROM prestations
			INNER JOIN personnes ON (
				prestations.personne_id = personnes.id
				AND prestations.natprest = 'RSA'
				AND prestations.rolepers IN ( 'DEM', 'CJT' )
			)
			LEFT OUTER JOIN ressources ON ( ressources.personne_id = personnes.id )
		WHERE (
			(
				ressources.id IN (
					SELECT tmpressources.id FROM (
						SELECT ressources.id, MAX(ressources.dfress)
							FROM ressources
							GROUP BY ressources.personne_id, ressources.id
					) AS tmpressources
				)
				AND ressources.id NOT IN (
					SELECT ressourcesmensuelles.ressource_id
						FROM ressourcesmensuelles
				)
			)
			OR (
				prestations.personne_id NOT IN (
					SELECT tmpressources.personne_id FROM (
						SELECT ressources.personne_id, MAX(ressources.dfress)
							FROM ressources
							GROUP BY ressources.personne_id, ressources.id
					) AS tmpressources
				)
			)
		)
		AND prestations.personne_id NOT IN (
			SELECT calculsdroitsrsa.personne_id
				FROM calculsdroitsrsa
		);

COMMIT;