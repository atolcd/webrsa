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
-- Parties de patch concernant la structure de la base de données qui n'étaient
-- pas passés
-- *****************************************************************************

-- Ajout du champ foyerid dans la table adresses
-- @see app/Config/sql/patches/2.x/patch-2.0-rc08.sql
SELECT add_missing_table_field( 'public', 'adresses', 'foyerid', 'INTEGER' );

-- Suppression des anciennes colonnes de la table adresses
-- @see app/Config/sql/patches/2.x/patch-2.6.10.sql

-- Suppression de la vue
DROP VIEW IF EXISTS public.exportcloud;

-- Suppression des colonnes des anciens flux

SELECT alter_table_drop_column_if_exists( 'public', 'parcours', 'typevoie' );
SELECT alter_table_drop_column_if_exists( 'public', 'parcours', 'locaadr' );

SELECT alter_table_drop_column_if_exists( 'public', 'orientations', 'typevoie' );
SELECT alter_table_drop_column_if_exists( 'public', 'orientations', 'locaadr' );

SELECT alter_table_drop_column_if_exists( 'public', 'adresses', 'typevoie' );
SELECT alter_table_drop_column_if_exists( 'public', 'adresses', 'numcomrat' );
SELECT alter_table_drop_column_if_exists( 'public', 'adresses', 'numcomptt' );
SELECT alter_table_drop_column_if_exists( 'public', 'adresses', 'locaadr' );

SELECT alter_table_drop_column_if_exists( 'public', 'cantons', 'typevoie' );
SELECT alter_table_drop_column_if_exists( 'public', 'cantons', 'locaadr' );
SELECT alter_table_drop_column_if_exists( 'public', 'cantons', 'numcomptt' );

SELECT alter_table_drop_column_if_exists( 'public', 'situationsallocataires', 'typevoie' );
SELECT alter_table_drop_column_if_exists( 'public', 'situationsallocataires', 'numcomrat' );
SELECT alter_table_drop_column_if_exists( 'public', 'situationsallocataires', 'numcomptt' );
SELECT alter_table_drop_column_if_exists( 'public', 'situationsallocataires', 'locaadr' );

SELECT alter_table_drop_column_if_exists( 'public', 'cers93', 'locaadr' );

-- Re-création de la vue, la colonne adresses.typevoie contient à présent la valeur NULL

CREATE VIEW exportcloud AS
    SELECT
			DISTINCT personnes.id,
			dossiers.matricule,
			personnes.nir,
			personnes.qual,
			personnes.nom,
			personnes.prenom,
			adresses.numvoie,
			NULL AS typevoie,
			adresses.libtypevoie,
			adresses.nomvoie,
			adresses.compladr,
			adresses.lieudist,
			adresses.codepos,
			adresses.nomcom,
			personnes.email,
			personnes.dtnai,
			personnes.nomcomnai,
			dossiers.numdemrsa,
			dossiers.dtdemrsa,
			prestations.rolepers
	FROM personnes,
		foyers,
		dossiers,
		prestations,
		adressesfoyers,
		adresses,
		situationsdossiersrsa
	WHERE (
		(
			(
				(
					(
						(
							(
								(
									(
										(personnes.foyer_id = foyers.id)
										AND (foyers.dossier_id = dossiers.id)
									)
									AND (prestations.personne_id = personnes.id)
								)
								AND (prestations.natprest = 'RSA')
							)
							AND (prestations.rolepers = ANY (ARRAY['DEM', 'CJT']))
						)
						AND (adressesfoyers.rgadr = '01')
					)
					AND (adressesfoyers.foyer_id = foyers.id)
				)
				AND (adressesfoyers.adresse_id = adresses.id)
			)
			AND (situationsdossiersrsa.dossier_id = dossiers.id)
		)
		AND (situationsdossiersrsa.etatdosrsa = ANY (ARRAY['2', '3', '4']))
	)
	ORDER BY personnes.id;

-- Augmentation de la longueur de l'intitulé des types d'orientations
-- @see app/Config/sql/patches/2.x/patch-2.7.05.sql
ALTER TABLE typesorients ALTER COLUMN lib_type_orient TYPE VARCHAR(75);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
