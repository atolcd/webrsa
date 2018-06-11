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
-- Correction des colones de la tables des fiches prescription
ALTER TABLE fichesprescriptions93 DROP COLUMN rdvprestataire_personne;
ALTER TABLE fichesprescriptions93 DROP COLUMN date_presente_benef;
ALTER TABLE fichesprescriptions93 DROP COLUMN retour_nom_partenaire;
ALTER TABLE fichesprescriptions93 DROP COLUMN date_signature_partenaire;
ALTER TABLE fichesprescriptions93 DROP COLUMN personne_recue;
ALTER TABLE fichesprescriptions93 DROP COLUMN motifnonreceptionfp93_id;
ALTER TABLE fichesprescriptions93 DROP COLUMN personne_nonrecue_autre;
ALTER TABLE fichesprescriptions93 DROP COLUMN personne_souhaite_integrer;
ALTER TABLE fichesprescriptions93 DROP COLUMN motifnonsouhaitfp93_id;
ALTER TABLE fichesprescriptions93 DROP COLUMN personne_nonsouhaite_autre;

--Suppression des tables retirée de la fiche prescription
DROP TABLE motifsnonreceptionsfps93;
DROP TABLE motifsnonsouhaitsfps93;

--------------------------------------------------------------------------------

--Création de la table de motifs de contacts
DROP TABLE IF EXISTS motifscontactsfps93 CASCADE;
CREATE TABLE motifscontactsfps93 (
    id			SERIAL NOT NULL PRIMARY KEY,
    name		VARCHAR(250) NOT NULL,
    autre 		character varying(1) NOT NULL DEFAULT '0'::character varying,
    created		TIMESTAMP WITHOUT TIME ZONE,
    modified	TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifscontactsfps93 IS 'Paramétrage des motifs de premier contact pour la fiche de prescription - CG 93';

CREATE UNIQUE INDEX motifscontactsfps93_name_idx ON motifscontactsfps93( name );

ALTER TABLE fichesprescriptions93 ADD COLUMN motifcontactfp93_id INTEGER DEFAULT NULL REFERENCES motifscontactsfps93(id) ON DELETE CASCADE ON UPDATE CASCADE;
--------------------------------------------------------------------------------

ALTER TABLE fichesprescriptions93 ADD COLUMN personne_acheve CHARACTER varying(1) DEFAULT NULL::character varying;
ALTER TABLE fichesprescriptions93
  ADD CONSTRAINT fichesprescriptions93_personne_acheve_in_list_chk CHECK (cakephp_validate_in_list(personne_acheve::text, ARRAY['0'::text, '1'::text]));
ALTER TABLE fichesprescriptions93 ADD COLUMN personne_acheve_autre TEXT DEFAULT NULL::TEXT;

--------------------------------------------------------------------------------

--Création de la table de motifs de "Achevé" de l'action

DROP TABLE IF EXISTS motifsactionachevesfps93 CASCADE;
CREATE TABLE motifsactionachevesfps93 (
    id			SERIAL NOT NULL PRIMARY KEY,
    name		VARCHAR(250) NOT NULL,
    autre 		character varying(1) NOT NULL DEFAULT '0'::character varying,
    created		TIMESTAMP WITHOUT TIME ZONE,
    modified	TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifsactionachevesfps93 IS 'Paramétrage des motifs d''action achevé pour la fiche de prescription - CG 93';

CREATE UNIQUE INDEX motifsactionachevesfps93_name_idx ON motifsactionachevesfps93( name );

ALTER TABLE fichesprescriptions93 ADD COLUMN motifactionachevefp93_id INTEGER DEFAULT NULL REFERENCES motifsactionachevesfps93(id) ON DELETE CASCADE ON UPDATE CASCADE;
--------------------------------------------------------------------------------

-- Création de la table de la liste déroulante de motifs de n'avoir pas achevé de l'action
DROP TABLE IF EXISTS motifsnonactionachevesfps93 CASCADE;
CREATE TABLE motifsnonactionachevesfps93 (
    id			SERIAL NOT NULL PRIMARY KEY,
    name		VARCHAR(250) NOT NULL,
    autre 		character varying(1) NOT NULL DEFAULT '0'::character varying,
    created		TIMESTAMP WITHOUT TIME ZONE,
    modified	TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifsnonactionachevesfps93 IS 'Paramétrage des motifs d''action non achevé pour la fiche de prescription - CG 93';

CREATE UNIQUE INDEX motifsnonactionachevesfps93_name_idx ON motifsnonactionachevesfps93( name );

ALTER TABLE fichesprescriptions93 ADD COLUMN motifnonactionachevefp93_id INTEGER DEFAULT NULL REFERENCES motifsnonactionachevesfps93(id) ON DELETE CASCADE ON UPDATE CASCADE;
-- *****************************************************************************
COMMIT;
-- *****************************************************************************