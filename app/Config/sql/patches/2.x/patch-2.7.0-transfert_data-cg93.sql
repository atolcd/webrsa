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

/*
SELECT
		actionscandidats.name,
		'93XXX' || REGEXP_REPLACE( EXTRACT( 'YEAR' FROM actionscandidats_personnes.ddaction )::TEXT, '^..(..)$', E'\\1' ) || REGEXP_REPLACE( actionscandidats.name, '^(..).*$', E'000\\1' ) AS "Numero Convention Action",
		EXTRACT( 'YEAR' FROM actionscandidats_personnes.ddaction ) AS "Annee"
	FROM actionscandidats_personnes
		INNER JOIN actionscandidats ON ( actionscandidats_personnes.actioncandidat_id = actionscandidats.id )
	GROUP BY EXTRACT( 'YEAR' FROM actionscandidats_personnes.ddaction ), actionscandidats.name, '93XXX' || REGEXP_REPLACE( EXTRACT( 'YEAR' FROM actionscandidats_personnes.ddaction )::TEXT, '^..(..)$', E'\\1' ) || REGEXP_REPLACE( actionscandidats.name, '^(..).*$', E'000\\1' )
	ORDER BY EXTRACT( 'YEAR' FROM actionscandidats_personnes.ddaction ), actionscandidats.name;
*/

INSERT INTO prestatairesfps93 ( name, created, modified ) VALUES
	( 'Non définie', NOW(), NOW() );

-- Fonction utilitaire.
CREATE OR REPLACE FUNCTION select_adresseprestatairefp93_id( TEXT ) RETURNS INT AS
$$
	SELECT
			adressesprestatairesfps93.id
		FROM adressesprestatairesfps93
		WHERE
			NOACCENTS_UPPER( adressesprestatairesfps93.adresse ) = NOACCENTS_UPPER( $1 )
		LIMIT 1
$$
LANGUAGE 'sql';

INSERT INTO adressesprestatairesfps93 ( prestatairefp93_id, adresse, codepos, localite, tel, fax, email, created, modified ) VALUES
	( ( SELECT id FROM prestatairesfps93 WHERE name = 'Non définie' LIMIT 1 ), 'Non définie', '00000', 'Non définie', NULL, NULL, NULL, NOW(), NOW() );

INSERT INTO thematiquesfps93 ( type, name, created, modified ) VALUES
	( 'pdi', 'Prescription professionnelle', NOW(), NOW() ),
	( 'horspdi', 'Prescription professionnelle', NOW(), NOW() ),
	( 'pdi', 'Prescription socioprofessionnelle', NOW(), NOW() ),
	( 'horspdi', 'Prescription socioprofessionnelle', NOW(), NOW() ),
	( 'horspdi', 'Prescription Pôle Emploi', NOW(), NOW() ),
	( 'horspdi', 'Prescription sociale', NOW(), NOW() ),
	( 'pdi', 'Prescription vers les acteurs de la santé', NOW(), NOW() ),
	( 'pdi', 'Prescription culture loisirs vacances', NOW(), NOW() ),
	( 'horspdi', 'Autres', NOW(), NOW() );

-- Fonction utilitaire.
CREATE OR REPLACE FUNCTION select_thematiquefp93_id( TEXT, TEXT ) RETURNS INT AS
$$
	SELECT
			thematiquesfps93.id
		FROM thematiquesfps93
		WHERE
			NOACCENTS_UPPER( thematiquesfps93.type ) = NOACCENTS_UPPER( $1 )
			AND NOACCENTS_UPPER( thematiquesfps93.name ) = NOACCENTS_UPPER( $2 )
		LIMIT 1
$$
LANGUAGE 'sql';

INSERT INTO categoriesfps93 ( thematiquefp93_id, name, created, modified ) VALUES
	( select_thematiquefp93_id( 'pdi', 'Prescription professionnelle' ), 'Non définie', NOW(), NOW() ),
	( select_thematiquefp93_id( 'pdi', 'Prescription professionnelle' ), 'formation pré-qualifiante', NOW(), NOW() ),
	( select_thematiquefp93_id( 'pdi', 'Prescription professionnelle' ), 'formation qualifiante', NOW(), NOW() ),
	( select_thematiquefp93_id( 'pdi', 'Prescription professionnelle' ), 'SIAE / Entreprise d insertion', NOW(), NOW() ),
	( select_thematiquefp93_id( 'pdi', 'Prescription professionnelle' ), 'Action du CUCS', NOW(), NOW() ),
	( select_thematiquefp93_id( 'pdi', 'Prescription professionnelle' ), 'Accompagnement à la création d''activité', NOW(), NOW() ),
	( select_thematiquefp93_id( 'pdi', 'Prescription professionnelle' ), 'FDIF/APRE', NOW(), NOW() ),
	( select_thematiquefp93_id( 'horspdi', 'Prescription professionnelle' ), 'Non définie', NOW(), NOW() ),
	( select_thematiquefp93_id( 'horspdi', 'Prescription professionnelle' ), 'maison de l’emploi ou SE autre que PE', NOW(), NOW() ),
	( select_thematiquefp93_id( 'horspdi', 'Prescription professionnelle' ), 'Plie', NOW(), NOW() ),
	( select_thematiquefp93_id( 'horspdi', 'Prescription professionnelle' ), 'Formation de Droit Commun Région, AFPA, …', NOW(), NOW() ),
	( select_thematiquefp93_id( 'pdi', 'Prescription socioprofessionnelle' ), 'Non définie', NOW(), NOW() ),
	( select_thematiquefp93_id( 'pdi', 'Prescription socioprofessionnelle' ), 'Action du CUCS', NOW(), NOW() ),
	( select_thematiquefp93_id( 'pdi', 'Prescription socioprofessionnelle' ), 'Remise à niveau', NOW(), NOW() ),
	( select_thematiquefp93_id( 'pdi', 'Prescription socioprofessionnelle' ), 'linguistique', NOW(), NOW() ),
	( select_thematiquefp93_id( 'horspdi', 'Prescription socioprofessionnelle' ), 'Non définie', NOW(), NOW() ),
	( select_thematiquefp93_id( 'horspdi', 'Prescription Pôle Emploi' ), 'Prestation pôle emploi', NOW(), NOW() ),
	( select_thematiquefp93_id( 'horspdi', 'Prescription sociale' ), 'Prestation sociale', NOW(), NOW() ),
	( select_thematiquefp93_id( 'pdi', 'Prescription vers les acteurs de la santé' ), 'Accompagnement santé', NOW(), NOW() ),
	( select_thematiquefp93_id( 'pdi', 'Prescription culture loisirs vacances' ), 'Projet loisirs vacances', NOW(), NOW() ),
	( select_thematiquefp93_id( 'horspdi', 'Autres' ), 'Non définie', NOW(), NOW() );

-- Fonction utilitaire.
CREATE OR REPLACE FUNCTION select_categoriefp93_id( TEXT, TEXT, TEXT ) RETURNS INT AS
$$
	SELECT
			categoriesfps93.id
		FROM thematiquesfps93
			INNER JOIN categoriesfps93 ON ( categoriesfps93.thematiquefp93_id = thematiquesfps93.id )
		WHERE
			NOACCENTS_UPPER( thematiquesfps93.type ) = NOACCENTS_UPPER( $1 )
			AND NOACCENTS_UPPER( thematiquesfps93.name ) = NOACCENTS_UPPER( $2 )
			AND NOACCENTS_UPPER( categoriesfps93.name ) = NOACCENTS_UPPER( $3 )
		LIMIT 1
$$
LANGUAGE 'sql';

INSERT INTO filieresfps93 ( categoriefp93_id, name, created, modified ) VALUES
	( select_categoriefp93_id( 'pdi', 'Prescription professionnelle', 'Non définie' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'pdi', 'Prescription professionnelle', 'formation pré-qualifiante' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'pdi', 'Prescription professionnelle', 'formation qualifiante' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'pdi', 'Prescription professionnelle', 'SIAE / Entreprise d insertion' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'pdi', 'Prescription professionnelle', 'Action du CUCS' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'pdi', 'Prescription professionnelle', 'Accompagnement à la création d''activité' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'pdi', 'Prescription professionnelle', 'FDIF/APRE' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'horspdi', 'Prescription professionnelle', 'Non définie' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'horspdi', 'Prescription professionnelle', 'maison de l’emploi ou SE autre que PE' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'horspdi', 'Prescription professionnelle', 'Plie' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'horspdi', 'Prescription professionnelle', 'Formation de Droit Commun Région, AFPA, …' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Non définie' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Action du CUCS' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Remise à niveau' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'linguistique' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'horspdi', 'Prescription socioprofessionnelle', 'Non définie' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'horspdi', 'Prescription Pôle Emploi', 'Prestation pôle emploi' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'horspdi', 'Prescription sociale', 'Prestation sociale' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'pdi', 'Prescription vers les acteurs de la santé', 'Accompagnement santé' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'pdi', 'Prescription culture loisirs vacances', 'Projet loisirs vacances' ), 'Non définie', NOW(), NOW() ),
	( select_categoriefp93_id( 'horspdi', 'Autres', 'Non définie' ), 'Non définie', NOW(), NOW() );

-- Fonction utilitaire.
CREATE OR REPLACE FUNCTION select_filierefp93_id( TEXT, TEXT, TEXT, TEXT ) RETURNS INT AS
$$
	SELECT
			filieresfps93.id
		FROM filieresfps93
			INNER JOIN categoriesfps93 ON ( categoriesfps93.id = filieresfps93.categoriefp93_id )
			INNER JOIN thematiquesfps93 ON ( thematiquesfps93.id = categoriesfps93.thematiquefp93_id )
		WHERE
			NOACCENTS_UPPER( thematiquesfps93.type ) = NOACCENTS_UPPER( $1 )
			AND NOACCENTS_UPPER( thematiquesfps93.name ) = NOACCENTS_UPPER( $2 )
			AND NOACCENTS_UPPER( categoriesfps93.name ) = NOACCENTS_UPPER( $3 )
			AND NOACCENTS_UPPER( filieresfps93.name ) = NOACCENTS_UPPER( $4 )
		LIMIT 1
$$
LANGUAGE 'sql';

INSERT INTO actionsfps93 ( filierefp93_id, adresseprestatairefp93_id, name, numconvention, annee, duree, actif, created, modified ) VALUES
	-- 2012
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'Non définie', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1200010', 2012, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'formation pré-qualifiante', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1200011', 2012, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'formation qualifiante', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1200012', 2012, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'SIAE / Entreprise d insertion', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1200013', 2012, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'Action du CUCS', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1200014', 2012, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'Accompagnement à la création d''activité', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1200015', 2012, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'FDIF/APRE', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1200016', 2012, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Non définie', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1200030', 2012, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Action du CUCS', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1200031', 2012, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Remise à niveau', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1200032', 2012, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'linguistique', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1200033', 2012, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription vers les acteurs de la santé', 'Accompagnement santé', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1200070', 2012, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription culture loisirs vacances', 'Projet loisirs vacances', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1200080', 2012, 'Non définie', '0', NOW(), NOW() ),
	-- 2013
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'Non définie', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1300010', 2013, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'formation pré-qualifiante', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1300011', 2013, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'formation qualifiante', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1300012', 2013, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'SIAE / Entreprise d insertion', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1300013', 2013, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'Action du CUCS', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1300014', 2013, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'Accompagnement à la création d''activité', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1300015', 2013, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'FDIF/APRE', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1300016', 2013, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Non définie', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1300030', 2013, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Action du CUCS', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1300031', 2013, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Remise à niveau', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1300032', 2013, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'linguistique', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1300033', 2013, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription vers les acteurs de la santé', 'Accompagnement santé', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1300070', 2013, 'Non définie', '0', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription culture loisirs vacances', 'Projet loisirs vacances', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1300080', 2013, 'Non définie', '0', NOW(), NOW() ),
	-- 2014
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'Non définie', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1400010', 2014, 'Non définie', '1', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'formation pré-qualifiante', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1400011', 2014, 'Non définie', '1', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'formation qualifiante', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1400012', 2014, 'Non définie', '1', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'SIAE / Entreprise d insertion', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1400013', 2014, 'Non définie', '1', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'Action du CUCS', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1400014', 2014, 'Non définie', '1', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'Accompagnement à la création d''activité', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1400015', 2014, 'Non définie', '1', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'FDIF/APRE', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1400016', 2014, 'Non définie', '1', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Non définie', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1400030', 2014, 'Non définie', '1', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Action du CUCS', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1400031', 2014, 'Non définie', '1', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Remise à niveau', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1400032', 2014, 'Non définie', '1', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'linguistique', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1400033', 2014, 'Non définie', '1', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription vers les acteurs de la santé', 'Accompagnement santé', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1400070', 2014, 'Non définie', '1', NOW(), NOW() ),
	( select_filierefp93_id( 'pdi', 'Prescription culture loisirs vacances', 'Projet loisirs vacances', 'Non définie' ), select_adresseprestatairefp93_id( 'Non définie' ), 'Non définie', '93XXX1400080', 2014, 'Non définie', '1', NOW(), NOW() );

-- Fonction utilitaire
CREATE OR REPLACE FUNCTION select_actionfp93_id( TEXT ) RETURNS INT AS
$$
	SELECT
			actionsfps93.id
		FROM actionsfps93
		WHERE
			NOACCENTS_UPPER( actionsfps93.numconvention ) = NOACCENTS_UPPER( $1 )
		LIMIT 1
$$
LANGUAGE 'sql';

-- Fonction utilitaire
CREATE OR REPLACE FUNCTION select_actionfp93_name_pdi( TEXT ) RETURNS BOOLEAN AS
$$
	SELECT SUBSTRING( $1 FROM 1 FOR 2 ) IN ( '10', '11', '12', '13', '14', '15', '16', '30', '31', '32', '33', '70', '80' )
$$
LANGUAGE 'sql';

-- Fonction utilitaire
CREATE OR REPLACE FUNCTION select_actionfp93_name_horspdi( TEXT ) RETURNS BOOLEAN AS
$$
	SELECT SUBSTRING( $1 FROM 1 FOR 2 ) IN ( '20', '21', '22', '23', '40', '50', '60', '90' )
$$
LANGUAGE 'sql';

-- Insertion des prestataires hors PDI
SELECT add_missing_table_field( 'public', 'prestataireshorspdifps93', 'actioncandidat_personne_id', 'INTEGER' );
INSERT INTO prestataireshorspdifps93 ( name, adresse, codepos, localite, created, modified, actioncandidat_personne_id )
	SELECT
			'Non définie' AS name,
			'Non définie' AS adresse,
			'00000' AS codepos,
			'Non définie' AS localite,
			LEAST( actionscandidats_personnes.ddaction, actionscandidats_personnes.dfaction, actionscandidats_personnes.datesignature, actionscandidats_personnes.datebilan, actionscandidats_personnes.daterecu, actionscandidats_personnes.sortiele ) AS created,
			GREATEST( actionscandidats_personnes.ddaction, actionscandidats_personnes.dfaction, actionscandidats_personnes.datesignature, actionscandidats_personnes.datebilan, actionscandidats_personnes.daterecu, actionscandidats_personnes.sortiele ) AS modified,
			actionscandidats_personnes.id AS actioncandidat_personne_id
		FROM actionscandidats_personnes
			INNER JOIN actionscandidats ON ( actionscandidats_personnes.actioncandidat_id = actionscandidats.id )
		WHERE select_actionfp93_name_horspdi( actionscandidats.name );

-- INSERT 0 4969 (?)
SELECT add_missing_table_field( 'public', 'fichesprescriptions93', 'actioncandidat_personne_id', 'INTEGER' );

INSERT INTO fichesprescriptions93 (
	actioncandidat_personne_id,
	personne_id,
	statut,
	referent_id,
	objet,
	rdvprestataire_date,
	filierefp93_id,
	actionfp93_id,
	actionfp93,
	adresseprestatairefp93_id,
	prestatairehorspdifp93_id,
	documentbeneffp93_autre,
	date_signature,
	date_transmission,
	date_retour,
	benef_retour_presente,
	retour_nom_partenaire,
	personne_recue,
	motifnonreceptionfp93_id,
	personne_nonrecue_autre,
	personne_retenue,
	motifnonretenuefp93_id,
	personne_nonretenue_autre,
	personne_a_integre,
	motifnonintegrationfp93_id,
	personne_nonintegre_autre,
	motif_annulation,
	created,
	modified
)
SELECT
		actionscandidats_personnes.id,
		actionscandidats_personnes.personne_id,
		(
			CASE
				WHEN (
					actionscandidats_personnes.positionfiche = 'enattente'
					AND actionscandidats_personnes.bilanretenu IS NULL
					AND actionscandidats_personnes.datesignature IS NULL
				) THEN '05suivi_renseigne'
				WHEN (
					actionscandidats_personnes.positionfiche = 'enattente'
					AND actionscandidats_personnes.bilanretenu IS NULL
					AND actionscandidats_personnes.datesignature IS NOT NULL
				) THEN '02signee'
				WHEN (
					actionscandidats_personnes.positionfiche = 'enattente'
					AND actionscandidats_personnes.bilanretenu IS NOT NULL
					AND actionscandidats_personnes.datesignature IS NOT NULL
				) THEN '05suivi_renseigne'
				WHEN (
					actionscandidats_personnes.positionfiche = 'encours'
					AND actionscandidats_personnes.bilanretenu = 'RET'
				) THEN '05suivi_renseigne'
				WHEN (
					actionscandidats_personnes.positionfiche = 'nonretenue'
					AND actionscandidats_personnes.bilanretenu = 'NRE'
				) THEN '05suivi_renseigne'
				WHEN (
					actionscandidats_personnes.positionfiche = 'annule'
				) THEN '99annulee'
			END
		) AS statut,
		actionscandidats_personnes.referent_id,
		actionscandidats_personnes.motifdemande AS objet,
		actionscandidats_personnes.horairerdvpartenaire AS rdvprestataire_date,
		(
			CASE
				-- Prescriptions PDI
				WHEN actionscandidats.name LIKE '10 %' THEN (
					select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'Non définie', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '11 %' THEN (
					select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'formation pré-qualifiante', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '12 %' THEN (
					select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'formation qualifiante', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '13 %' THEN (
					select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'SIAE / Entreprise d insertion', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '14 %' THEN (
					select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'Action du CUCS', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '15 %' THEN (
					select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'Accompagnement à la création d''activité', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '16 %' THEN (
					select_filierefp93_id( 'pdi', 'Prescription professionnelle', 'FDIF/APRE', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '30 %' THEN (
					select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Non définie', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '31 %' THEN (
					select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Action du CUCS', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '32 %' THEN (
					select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'Remise à niveau', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '33 %' THEN (
					select_filierefp93_id( 'pdi', 'Prescription socioprofessionnelle', 'linguistique', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '70 %' THEN (
					select_filierefp93_id( 'pdi', 'Prescription vers les acteurs de la santé', 'Accompagnement santé', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '80 %' THEN (
					select_filierefp93_id( 'pdi', 'Prescription culture loisirs vacances', 'Projet loisirs vacances', 'Non définie' )
				)
				-- Prescriptions hors PDI
				WHEN actionscandidats.name LIKE '20 %' THEN (
					select_filierefp93_id( 'horspdi', 'Prescription professionnelle', 'Non définie', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '21 %' THEN (
					select_filierefp93_id( 'horspdi', 'Prescription professionnelle', 'maison de l’emploi ou SE autre que PE', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '22 %' THEN (
					select_filierefp93_id( 'horspdi', 'Prescription professionnelle', 'Plie', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '23 %' THEN (
					select_filierefp93_id( 'horspdi', 'Prescription professionnelle', 'Formation de Droit Commun Région, AFPA, …', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '40 %' THEN (
					select_filierefp93_id( 'horspdi', 'Prescription socioprofessionnelle', 'Non définie', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '50 %' THEN (
					select_filierefp93_id( 'horspdi', 'Prescription Pôle Emploi', 'Prestation pôle emploi', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '60 %' THEN (
					select_filierefp93_id( 'horspdi', 'Prescription sociale', 'Prestation sociale', 'Non définie' )
				)
				WHEN actionscandidats.name LIKE '90 %' THEN (
					select_filierefp93_id( 'horspdi', 'Autres', 'Non définie', 'Non définie' )
				)
				ELSE NULL
			END
		) AS filierefp93_id,
		(
			CASE
				WHEN select_actionfp93_name_pdi( actionscandidats.name ) THEN (
					select_actionfp93_id( '93XXX' || REGEXP_REPLACE( EXTRACT( 'YEAR' FROM actionscandidats_personnes.ddaction )::TEXT, '^..(..)$', E'\\1' ) || '000' || SUBSTRING( actionscandidats.name FROM 1 FOR 2 ) )
				)
				-- Prescriptions hors PDI
				ELSE NULL
			END
		) AS actionfp93_id,
		(
			CASE
				-- Prescriptions hors PDI
				WHEN select_actionfp93_name_horspdi( actionscandidats.name ) THEN 'Non définie'
				ELSE NULL
			END
		) AS actionfp93,
		(
			CASE
				WHEN select_actionfp93_name_pdi( actionscandidats.name ) THEN ( SELECT adressesprestatairesfps93.id FROM adressesprestatairesfps93 WHERE adressesprestatairesfps93.adresse = 'Non définie' LIMIT 1 )
				ELSE NULL
			END
		) AS adresseprestatairefp93_id,
		(
			CASE
				WHEN select_actionfp93_name_horspdi( actionscandidats.name ) THEN ( SELECT prestataireshorspdifps93.id FROM prestataireshorspdifps93 WHERE prestataireshorspdifps93.actioncandidat_personne_id = actionscandidats_personnes.id LIMIT 1 )
				ELSE NULL
			END
		) AS prestatairehorspdifp93_id,
		actionscandidats_personnes.autrepiece AS documentbeneffp93_autre,
		actionscandidats_personnes.datesignature AS date_signature,
		actionscandidats_personnes.ddaction AS date_transmission,
		actionscandidats_personnes.dfaction AS date_retour,
		( CASE WHEN actionscandidats_personnes.bilanvenu = 'VEN' THEN 'oui' WHEN actionscandidats_personnes.bilanvenu = 'NVE' THEN 'non' ELSE NULL END ),
		actionscandidats_personnes.personnerecu,
		( CASE WHEN actionscandidats_personnes.bilanrecu = 'O' THEN '1' WHEN actionscandidats_personnes.bilanrecu = 'N' THEN '0' ELSE NULL END ),
		( CASE WHEN actionscandidats_personnes.bilanrecu = 'N' THEN ( SELECT id FROM motifsnonreceptionsfps93 WHERE autre = '1' LIMIT 1 ) ELSE NULL END ),
		( CASE WHEN actionscandidats_personnes.bilanrecu = 'N' THEN actionscandidats_personnes.precisionmotif ELSE NULL END ),
		( CASE WHEN actionscandidats_personnes.bilanretenu = 'RET' THEN '1' WHEN actionscandidats_personnes.bilanretenu = 'NRE' THEN '0' ELSE NULL END ),
		( CASE WHEN actionscandidats_personnes.bilanretenu = 'NRE' THEN ( SELECT id FROM motifsnonretenuesfps93 WHERE autre = '1' LIMIT 1 ) ELSE NULL END ),
		( CASE WHEN actionscandidats_personnes.bilanretenu = 'NRE' THEN actionscandidats_personnes.precisionmotif ELSE NULL END ),
		( CASE WHEN actionscandidats_personnes.integrationaction = 'O' THEN '1' WHEN actionscandidats_personnes.integrationaction = 'N' THEN '0' ELSE NULL END ),
		( CASE WHEN actionscandidats_personnes.integrationaction = 'N' THEN ( SELECT id FROM motifsnonintegrationsfps93 WHERE autre = '1' LIMIT 1 ) ELSE NULL END ),
		( CASE WHEN actionscandidats_personnes.integrationaction = 'N' THEN actionscandidats_personnes.precisionmotif ELSE NULL END ),
		actionscandidats_personnes.motifannulation,
		LEAST( actionscandidats_personnes.ddaction, actionscandidats_personnes.dfaction, actionscandidats_personnes.datesignature, actionscandidats_personnes.datebilan, actionscandidats_personnes.daterecu, actionscandidats_personnes.sortiele ),
		GREATEST( actionscandidats_personnes.ddaction, actionscandidats_personnes.dfaction, actionscandidats_personnes.datesignature, actionscandidats_personnes.datebilan, actionscandidats_personnes.daterecu, actionscandidats_personnes.sortiele )
	FROM actionscandidats_personnes
		INNER JOIN actionscandidats ON ( actionscandidats_personnes.actioncandidat_id = actionscandidats.id );

DROP FUNCTION select_adresseprestatairefp93_id( TEXT );
DROP FUNCTION select_thematiquefp93_id( TEXT, TEXT );
DROP FUNCTION select_categoriefp93_id( TEXT, TEXT, TEXT );
DROP FUNCTION select_filierefp93_id( TEXT, TEXT, TEXT, TEXT );
DROP FUNCTION select_actionfp93_id( TEXT );
DROP FUNCTION select_actionfp93_name_pdi( TEXT );
DROP FUNCTION select_actionfp93_name_horspdi( TEXT );

INSERT INTO instantanesdonneesfps93 (
	ficheprescription93_id,
	referent_fonction,
	structure_name,
	structure_num_voie,
	structure_type_voie,
	structure_nom_voie,
	structure_code_postal,
	structure_ville,
	structure_tel,
	structure_fax,
	referent_email,
	benef_qual,
	benef_nom,
	benef_prenom,
	benef_dtnai,
	benef_numvoie,
	benef_libtypevoie,
	benef_nomvoie,
	benef_complideadr,
	benef_compladr,
	benef_numcom,
	benef_codepos,
	benef_nomcom,
	benef_tel_fixe,
	benef_tel_port,
	benef_email,
	benef_identifiantpe,
	benef_inscritpe,
	benef_matricule,
	benef_natpf_socle,
	benef_natpf_majore,
	benef_natpf_activite,
	benef_nivetu,
	benef_etatdosrsa,
	benef_toppersdrodevorsa,
	benef_dd_ci,
	benef_df_ci,
	benef_positioncer,
	created,
	modified
)
SELECT
		fichesprescriptions93.id,
		referents.fonction,
		structuresreferentes.lib_struc,
		structuresreferentes.num_voie,
		structuresreferentes.type_voie,
		structuresreferentes.nom_voie,
		structuresreferentes.code_postal,
		structuresreferentes.ville,
		structuresreferentes.numtel,
		structuresreferentes.numfax,
		referents.email,
		personnes.qual,
		personnes.nom,
		personnes.prenom,
		personnes.dtnai,
		(
			CASE
				WHEN situationsallocataires.id IS NOT NULL THEN situationsallocataires.numvoie
				ELSE adresses.numvoie
			END
		) AS benef_numvoie,
		(
			CASE
				WHEN situationsallocataires.id IS NOT NULL THEN situationsallocataires.libtypevoie
				ELSE adresses.libtypevoie
			END
		) AS benef_libtypevoie,
		(
			CASE
				WHEN situationsallocataires.id IS NOT NULL THEN situationsallocataires.nomvoie
				ELSE adresses.nomvoie
			END
		) AS benef_nomvoie,
		(
			CASE
				WHEN situationsallocataires.id IS NOT NULL THEN situationsallocataires.complideadr
				ELSE adresses.complideadr
			END
		) AS benef_complideadr,
		(
			CASE
				WHEN situationsallocataires.id IS NOT NULL THEN situationsallocataires.compladr
				ELSE adresses.compladr
			END
		) AS benef_compladr,
		(
			CASE
				WHEN situationsallocataires.id IS NOT NULL THEN situationsallocataires.numcom
				ELSE adresses.numcom
			END
		) AS benef_numcom,
		(
			CASE
				WHEN situationsallocataires.id IS NOT NULL THEN situationsallocataires.codepos
				ELSE adresses.codepos
			END
		) AS benef_codepos,
		(
			CASE
				WHEN situationsallocataires.id IS NOT NULL THEN situationsallocataires.nomcom
				ELSE adresses.nomcom
			END
		) AS benef_nomcom,
		personnes.numfixe,
		personnes.numport,
		personnes.email,
		historiqueetatspe.identifiantpe AS benef_identifiantpe,
		( CASE WHEN historiqueetatspe.etat = 'inscription' THEN '1' ELSE '0' END ) AS benef_inscritpe,
		(
			CASE
				WHEN situationsallocataires.id IS NOT NULL THEN situationsallocataires.matricule
				ELSE dossiers.matricule
			END
		) AS benef_matricule,
		(
			CASE
				WHEN situationsallocataires.id IS NOT NULL THEN situationsallocataires.natpf_socle
				ELSE ( CASE WHEN EXISTS( SELECT detailscalculsdroitsrsa.id FROM detailscalculsdroitsrsa WHERE detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id AND detailscalculsdroitsrsa.natpf IN ('RSD', 'RSI', 'RSU', 'RSB', 'RSJ') ) THEN '1' ELSE '0' END )
			END
		) AS benef_natpf_socle,
		(
			CASE
				WHEN situationsallocataires.id IS NOT NULL THEN situationsallocataires.natpf_majore
				ELSE ( CASE WHEN EXISTS( SELECT detailscalculsdroitsrsa.id FROM detailscalculsdroitsrsa WHERE detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id AND detailscalculsdroitsrsa.natpf IN ('RSI', 'RCI') ) THEN '1' ELSE '0' END )
			END
		) AS benef_natpf_majore,
		(
			CASE
				WHEN situationsallocataires.id IS NOT NULL THEN situationsallocataires.natpf_activite
				ELSE ( CASE WHEN EXISTS( SELECT detailscalculsdroitsrsa.id FROM detailscalculsdroitsrsa WHERE detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id AND detailscalculsdroitsrsa.natpf IN ('RCD', 'RCI', 'RCU', 'RCB', 'RCJ') ) THEN '1' ELSE '0' END )
			END
		) AS benef_natpf_activite,
		(
			CASE
				WHEN dsps_revs.id IS NOT NULL THEN dsps_revs.nivetu
				WHEN dsps.id IS NOT NULL THEN dsps.nivetu
				else null
			END
		) AS benef_nivetu,
		(
			CASE
				WHEN historiquesdroits.id IS NOT NULL THEN historiquesdroits.etatdosrsa
				ELSE situationsdossiersrsa.etatdosrsa
			END
		) AS benef_etatdosrsa,
		(
			CASE
				WHEN historiquesdroits.id IS NOT NULL THEN historiquesdroits.toppersdrodevorsa
				ELSE calculsdroitsrsa.toppersdrodevorsa::TEXT
			END
		) AS benef_toppersdrodevorsa,
		contratsinsertion.dd_ci,
		contratsinsertion.df_ci,
		(
			CASE
				WHEN contratsinsertion.decision_ci = 'V' THEN 'valide'
				WHEN cers93.positioncer IN ( '00enregistre', '01signe', '02attdecisioncpdv' ) THEN 'validationpdv'
				WHEN cers93.positioncer IN ( '03attdecisioncg', '04premierelecture', '05secondelecture', '07attavisep' ) THEN 'validationcg'
				ELSE 'aucun'
			END
		) AS benef_positioncer,
		fichesprescriptions93.created,
		fichesprescriptions93.created
	FROM fichesprescriptions93
		INNER JOIN actionscandidats_personnes ON ( fichesprescriptions93.actioncandidat_personne_id = actionscandidats_personnes.id )
		INNER JOIN referents ON ( fichesprescriptions93.referent_id = referents.id )
		INNER JOIN structuresreferentes ON ( referents.structurereferente_id = structuresreferentes.id )
		INNER JOIN personnes ON ( fichesprescriptions93.personne_id = personnes.id )
		INNER JOIN foyers ON ( personnes.foyer_id = foyers.id )
		INNER JOIN dossiers ON ( foyers.dossier_id = dossiers.id )
		LEFT OUTER JOIN detailsdroitsrsa ON ( dossiers.id = detailsdroitsrsa.dossier_id )
		LEFT OUTER JOIN situationsallocataires ON (
			situationsallocataires.personne_id = personnes.id
			AND situationsallocataires.id IN (
				SELECT dernierssituationsallocataires.id
					FROM situationsallocataires AS dernierssituationsallocataires
					WHERE
						dernierssituationsallocataires.personne_id = personnes.id
						-- Avant la date de création de la fiche de prescription
						AND dernierssituationsallocataires.modified <= fichesprescriptions93.created
					ORDER BY dernierssituationsallocataires.modified DESC
					LIMIT 1
			)
		)
		LEFT OUTER JOIN contratsinsertion ON (
			contratsinsertion.personne_id = personnes.id
			AND contratsinsertion.id IN (
				SELECT
					dernierscontratsinsertion.id
				FROM contratsinsertion AS dernierscontratsinsertion
				WHERE
					dernierscontratsinsertion.personne_id = personnes.id
					AND dernierscontratsinsertion.decision_ci IN ('E', 'V')
					AND dernierscontratsinsertion.dd_ci <= LEAST( actionscandidats_personnes.ddaction, actionscandidats_personnes.dfaction, actionscandidats_personnes.datesignature, actionscandidats_personnes.datebilan, actionscandidats_personnes.daterecu, actionscandidats_personnes.sortiele )
				ORDER BY dernierscontratsinsertion.dd_ci DESC
				LIMIT 1
			)
		)
		LEFT OUTER JOIN cers93 ON ( cers93.contratinsertion_id = contratsinsertion.id )
		LEFT OUTER JOIN informationspe ON (
			(
				(
					informationspe.nir IS NOT NULL
					AND  SUBSTRING( informationspe.nir FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH ' ' FROM personnes.nir ) FROM 1 FOR 13 )
					AND  informationspe.dtnai = personnes.dtnai
				)
				OR
				(
					personnes.nom IS NOT NULL
					AND  personnes.prenom IS NOT NULL
					AND  personnes.dtnai IS NOT NULL
					AND  TRIM( BOTH ' ' FROM informationspe.nom ) = TRIM( BOTH ' ' FROM personnes.nom )
					AND  TRIM( BOTH ' ' FROM informationspe.prenom ) = TRIM( BOTH ' ' FROM personnes.prenom )
					AND  informationspe.dtnai = personnes.dtnai
				)
			)
			AND informationspe.id IN (
				SELECT derniereinformationspe.i__id FROM (
					SELECT i.id AS i__id, h.date AS h__date
						FROM informationspe AS i
							INNER JOIN historiqueetatspe AS h ON (h.informationpe_id = i.id)
						WHERE
						(
							(
								(
									(i.nir IS NOT NULL)
									AND  (personnes.nir IS NOT NULL)
									AND  (TRIM( BOTH ' ' FROM i.nir ) <> '')
									AND  (TRIM( BOTH ' ' FROM personnes.nir ) <> '')
									AND  (SUBSTRING( i.nir FROM 1 FOR 13 ) = SUBSTRING( personnes.nir FROM 1 FOR 13 ))
									AND  (i.dtnai = personnes.dtnai)
								)
							)
							OR
							(
								(
									(i.nom IS NOT NULL)
									AND  (personnes.nom IS NOT NULL)
									AND  (i.prenom IS NOT NULL)
									AND  (personnes.prenom IS NOT NULL)
									AND  (TRIM( BOTH ' ' FROM i.nom ) <> '')
									AND  (TRIM( BOTH ' ' FROM i.prenom ) <> '')
									AND  (TRIM( BOTH ' ' FROM personnes.nom ) <> '')
									AND  (TRIM( BOTH ' ' FROM personnes.prenom ) <> '')
									AND  (TRIM( BOTH ' ' FROM i.nom ) = personnes.nom)
									AND  (TRIM( BOTH ' ' FROM i.prenom ) = personnes.prenom)
									AND  (i.dtnai = personnes.dtnai)
								)
							)
						)
						AND h.id IN (
							SELECT dernierhistoriqueetatspe.id AS dernierhistoriqueetatspe__id
							FROM historiqueetatspe AS dernierhistoriqueetatspe
							WHERE
								dernierhistoriqueetatspe.informationpe_id = i.id
								-- Avant la date de création de la fiche de prescription
								AND dernierhistoriqueetatspe.date <= fichesprescriptions93.created
							ORDER BY dernierhistoriqueetatspe.date DESC, dernierhistoriqueetatspe.id DESC
							LIMIT 1
						)
				) AS derniereinformationspe
				ORDER BY derniereinformationspe.h__date DESC
				LIMIT 1
			)
		)
		LEFT OUTER JOIN historiqueetatspe ON (
			historiqueetatspe.informationpe_id = informationspe.id
			AND historiqueetatspe.id IN (
				SELECT h.id
					FROM historiqueetatspe AS h
					WHERE h.informationpe_id = informationspe.id
					ORDER BY h.date DESC
					LIMIT 1
			)
		)
		LEFT OUTER JOIN adressesfoyers ON (
			adressesfoyers.foyer_id = foyers.id
			AND adressesfoyers.id IN (
				SELECT af.id
					FROM adressesfoyers AS af
					WHERE
						af.foyer_id = foyers.id
						-- Avant la date de création de la fiche de prescription
						AND af.dtemm <= fichesprescriptions93.created
						ORDER BY af.dtemm DESC
					LIMIT 1
			)
		)
		LEFT OUTER JOIN adresses ON ( adressesfoyers.adresse_id = adresses.id )
		LEFT OUTER JOIN dsps ON (
			dsps.personne_id = personnes.id
			AND dsps.id IN (
				SELECT dernieresdsps.id
					FROM dsps AS dernieresdsps
					WHERE dernieresdsps.personne_id = personnes.id
					ORDER BY dernieresdsps.id DESC
					LIMIT 1
			)
		)
		LEFT OUTER JOIN dsps_revs ON (
			dsps_revs.personne_id = personnes.id
			AND dsps_revs.id IN (
				SELECT dernieresdsps_revs.id
					FROM dsps_revs AS dernieresdsps_revs
					WHERE
						dernieresdsps_revs.personne_id = personnes.id
						-- Avant la date de création de la fiche de prescription
						AND dernieresdsps_revs.modified <= fichesprescriptions93.created
					ORDER BY dernieresdsps_revs.modified DESC
					LIMIT 1
			)
		)
		LEFT OUTER JOIN situationsdossiersrsa ON ( situationsdossiersrsa.dossier_id = dossiers.id )
		LEFT OUTER JOIN calculsdroitsrsa ON ( calculsdroitsrsa.personne_id = personnes.id )
		LEFT OUTER JOIN historiquesdroits ON (
			historiquesdroits.personne_id = personnes.id
			AND historiquesdroits.id IN (
				SELECT derniershistoriquesdroits.id
					FROM historiquesdroits AS derniershistoriquesdroits
					WHERE
						derniershistoriquesdroits.personne_id = personnes.id
						-- Avant la date de création de la fiche de prescription
						AND derniershistoriquesdroits.created <= fichesprescriptions93.created
					ORDER BY derniershistoriquesdroits.modified DESC
					LIMIT 1
			)
		)
	WHERE
		fichesprescriptions93.actioncandidat_personne_id IS NOT NULL
;

INSERT INTO documentsbenefsfps93_fichesprescriptions93
(
	documentbeneffp93_id,
	ficheprescription93_id,
	created,
	modified
)
SELECT
		(
			CASE
				WHEN actionscandidats_personnes.pieceallocataire = 'CER' THEN (
					SELECT documentsbenefsfps93.id
						FROM documentsbenefsfps93
						WHERE documentsbenefsfps93.name = 'CER'
						LIMIT 1
				)
				WHEN actionscandidats_personnes.pieceallocataire = 'NCA' THEN (
					SELECT documentsbenefsfps93.id
						FROM documentsbenefsfps93
						WHERE documentsbenefsfps93.name = 'Notification CAF'
						LIMIT 1
				)
				WHEN actionscandidats_personnes.pieceallocataire = 'CV' THEN (
					SELECT documentsbenefsfps93.id
						FROM documentsbenefsfps93
						WHERE documentsbenefsfps93.name = 'CV'
						LIMIT 1
				)
				WHEN actionscandidats_personnes.pieceallocataire = 'AUT' THEN (
					SELECT documentsbenefsfps93.id
						FROM documentsbenefsfps93
						WHERE documentsbenefsfps93.name = 'Autre'
						LIMIT 1
				)
				ELSE NULL
			END
		) AS documentbeneffp93_id,
		fichesprescriptions93.id AS ficheprescription93_id,
		fichesprescriptions93.created AS created,
		fichesprescriptions93.modified AS modified
	FROM fichesprescriptions93
		INNER JOIN actionscandidats_personnes ON ( fichesprescriptions93.actioncandidat_personne_id = actionscandidats_personnes.id )
WHERE
	fichesprescriptions93.actioncandidat_personne_id IS NOT NULL
	AND actionscandidats_personnes.pieceallocataire IS NOT NULL;

-- Les 2 requêtes ci-dessous ne doivent rien retourner
/*SELECT * FROM fichesprescriptions93 WHERE actioncandidat_personne_id IS NULL;
SELECT * FROM actionscandidats_personnes WHERE id NOT IN (
	SELECT fichesprescriptions93.actioncandidat_personne_id
		FROM fichesprescriptions93
		WHERE fichesprescriptions93.actioncandidat_personne_id = actionscandidats_personnes.id
);*/

 -- FIXME: décommenter
-- ALTER TABLE fichesprescriptions93 DROP COLUMN actioncandidat_personne_id;
-- ALTER TABLE prestataireshorspdifps93 DROP COLUMN actioncandidat_personne_id;
-- *****************************************************************************
COMMIT;
-- *****************************************************************************