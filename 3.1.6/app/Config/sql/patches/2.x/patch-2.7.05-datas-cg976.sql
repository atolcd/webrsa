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
-- Utilisation de typesorients avec parentid
--------------------------------------------------------------------------------
ALTER TABLE typesorients ALTER COLUMN lib_type_orient TYPE VARCHAR(60);

INSERT INTO typesorients ( parentid, lib_type_orient, modele_notif, modele_notif_cohorte, actif )
	SELECT
			typesorients.id,
			structuresreferentes.lib_struc,
			typesorients.modele_notif,
			typesorients.modele_notif_cohorte,
			( CASE WHEN ( typesorients.actif = 'O' AND structuresreferentes.actif = 'O' ) THEN 'O'::TYPE_NO ELSE 'N'::TYPE_NO END )
		FROM typesorients
			INNER JOIN structuresreferentes ON ( structuresreferentes.typeorient_id = typesorients.id )
		ORDER BY lib_struc, lib_type_orient;

UPDATE typesorients SET modele_notif = NULL, modele_notif_cohorte = NULL WHERE parentid IS NULL;

--------------------------------------------------------------------------------

UPDATE structuresreferentes
	SET typeorient_id = typesorients.id
	FROM typesorients
	WHERE
		structuresreferentes.typeorient_id = typesorients.parentid
		AND structuresreferentes.lib_struc = typesorients.lib_type_orient;

--------------------------------------------------------------------------------

-- On double les référents
INSERT INTO referents ( structurereferente_id, nom, prenom, numero_poste, email, qual, fonction, actif, datecloture )
	SELECT
			(
				SELECT autresstructuresreferentes.id
					FROM structuresreferentes AS autresstructuresreferentes
						INNER JOIN typesorients AS autrestypesorients ON ( autresstructuresreferentes.typeorient_id = autrestypesorients.id )
					WHERE
						RTRIM( structuresreferentes.lib_struc, ' 2' ) = RTRIM( autresstructuresreferentes.lib_struc, ' 2' )
						AND typesorients.parentid <> autrestypesorients.parentid
			) AS structurereferente_id,
			referents.nom, referents.prenom, referents.numero_poste, referents.email, referents.qual, referents.fonction, referents.actif, referents.datecloture
		FROM referents
			INNER JOIN structuresreferentes ON ( referents.structurereferente_id = structuresreferentes.id )
			INNER JOIN typesorients ON ( structuresreferentes.typeorient_id = typesorients.id )
		WHERE (
				SELECT autresstructuresreferentes.id
					FROM structuresreferentes AS autresstructuresreferentes
						INNER JOIN typesorients AS autrestypesorients ON ( autresstructuresreferentes.typeorient_id = autrestypesorients.id )
					WHERE
						RTRIM( structuresreferentes.lib_struc, ' 2' ) = RTRIM( autresstructuresreferentes.lib_struc, ' 2' )
						AND typesorients.parentid <> autrestypesorients.parentid
			) IS NOT NULL;

--------------------------------------------------------------------------------
-- Mise à jour des orientations
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION public.update_typeorient_id_from_structurereferente_id( p_table TEXT, p_typeorient_column TEXT, p_structurereferente_column TEXT ) RETURNS VOID AS
$$
	DECLARE
		v_query text;
	BEGIN
		v_query := 'UPDATE ' || p_table || '
						SET ' || p_typeorient_column || ' = structuresreferentes.typeorient_id
						FROM structuresreferentes
						WHERE ' || p_table || '.' || p_structurereferente_column || ' IS NOT NULL
						AND ' || p_table || '.' || p_structurereferente_column || ' = structuresreferentes.id;';
		RAISE NOTICE  '%', v_query;
		EXECUTE v_query;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_typeorient_id_from_structurereferente_id( 'orientsstructs', 'typeorient_id', 'structurereferente_id' );

--------------------------------------------------------------------------------
-- Mise à jour des intitulés dans typesorients et dans structuresreferentes
--------------------------------------------------------------------------------

UPDATE typesorients
	SET lib_type_orient = ( parentstypesorients.lib_type_orient || ' - ' || RTRIM( typesorients.lib_type_orient, ' 2' ) )
	FROM typesorients AS parentstypesorients
	WHERE
		typesorients.parentid IS NOT NULL
		AND typesorients.parentid = parentstypesorients.id;

UPDATE structuresreferentes
	SET lib_struc = ( RTRIM( structuresreferentes.lib_struc, ' 2' ) || ' (' || parentstypesorients.lib_type_orient || ')' )
	FROM typesorients, typesorients AS parentstypesorients
	WHERE
		structuresreferentes.typeorient_id = typesorients.id
		AND typesorients.parentid = parentstypesorients.id;

--------------------------------------------------------------------------------

DROP FUNCTION public.update_typeorient_id_from_structurereferente_id( p_table TEXT, p_typeorient_column TEXT, p_structurereferente_column TEXT );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************