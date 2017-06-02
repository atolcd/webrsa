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
-- Paramétrage des zones géographiques
-- @url http://www.insee.fr/fr/methodes/nomenclatures/zonages/zone.asp?zonage=DEP&zone=976
--------------------------------------------------------------------------------
DELETE FROM zonesgeographiques;
-- Remise à zéro du compteur
SELECT setval('zonesgeographiques_id_seq', COALESCE(MAX(id),1), false) FROM zonesgeographiques;
-- Insertion des 17 codes INSEE (communes) de Mayotte
INSERT INTO zonesgeographiques (codeinsee, libelle) VALUES
	( '97601', 'Acoua' ),
	( '97602', 'Bandraboua' ),
	( '97603', 'Bandrele' ),
	( '97604', 'Bouéni' ),
	( '97605', 'Chiconi' ),
	( '97606', 'Chirongui' ),
	( '97607', 'Dembeni' ),
	( '97608', 'Dzaoudzi' ),
	( '97609', 'Kani-Kéli' ),
	( '97610', 'Koungou' ),
	( '97611', 'Mamoudzou' ),
	( '97612', 'Mtsamboro' ),
	( '97613', 'M''Tsangamouji' ),
	( '97614', 'Ouangani' ),
	( '97615', 'Pamandzi' ),
	( '97616', 'Sada' ),
	( '97617', 'Tsingoni' );

UPDATE users SET filtre_zone_geo = false;

--------------------------------------------------------------------------------
-- Mise à jour des informations de l'administrateur
--------------------------------------------------------------------------------
UPDATE users SET nom = 'webrsa', prenom = 'webrsa' WHERE username = 'webrsa';

--------------------------------------------------------------------------------
-- Mise à jour des types d'orientation, sur un seul niveau, si besoin.
--------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION public.orientsstructs_noparent() RETURNS VOID AS
$$
	DECLARE
		v_query TEXT;
		v_row RECORD;
	BEGIN
		v_query := 'SELECT COUNT(*) AS count FROM typesorients WHERE parentid IS NOT NULL;';
		EXECUTE v_query INTO v_row;

		IF v_row.count > 0 THEN
 			v_query := 'UPDATE typesorients SET actif = ''N'' WHERE parentid IS NULL;';
			EXECUTE v_query;

 			v_query := 'UPDATE typesorients SET parentid = NULL WHERE actif = ''O'';';
			EXECUTE v_query;

 			v_query := 'DELETE FROM typesorients WHERE actif = ''N'';';
			EXECUTE v_query;

		END IF;
	END;
$$
LANGUAGE plpgsql VOLATILE;

SELECT public.orientsstructs_noparent();
DROP FUNCTION public.orientsstructs_noparent();

-- *****************************************************************************
COMMIT;
-- *****************************************************************************