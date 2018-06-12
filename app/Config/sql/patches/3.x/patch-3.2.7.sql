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


-- *****************************************************************************
-- TAG
ALTER TABLE valeurstags ADD COLUMN actif smallint NOT NULL DEFAULT 1;


-- *****************************************************************************
-- Questionnaire B7
CREATE TABLE dureeemplois
(
  id serial NOT NULL,
  name character varying(255) NOT NULL,
  created timestamp without time zone,
  modified timestamp without time zone,

  CONSTRAINT dureeemplois_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dureeemplois
  OWNER TO webrsa;

CREATE TABLE typeemplois
(
  id serial NOT NULL,
  name character varying(255) NOT NULL,
  created timestamp without time zone,
  modified timestamp without time zone,

  CONSTRAINT typeemplois_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE typeemplois
  OWNER TO webrsa;

CREATE TABLE questionnairesb7pdvs93
(
  id serial NOT NULL,
  personne_id integer NOT NULL,
  dateemploi date,
  typeemploi_id integer NOT NULL,
  dureeemploi_id integer NOT NULL,
  expproromev3_id integer,
  created timestamp without time zone,
  modified timestamp without time zone,
  CONSTRAINT questionnairesb7pdvs93_pkey PRIMARY KEY (id),
  CONSTRAINT questionnairesb7pdvs93_expproromev3_id_fkey FOREIGN KEY (expproromev3_id)
      REFERENCES entreesromesv3 (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT questionnairesb7pdvs93_personne_id_fkey FOREIGN KEY (personne_id)
      REFERENCES personnes (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT questionnairesb7pdvs93_typeemploi_id_fkey FOREIGN KEY (typeemploi_id)
      REFERENCES typeemplois (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT questionnairesb7pdvs93_dureeemploi_id_fkey FOREIGN KEY (dureeemploi_id)
      REFERENCES dureeemplois (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE questionnairesb7pdvs93
  OWNER TO postgres;


INSERT INTO dureeemplois (name, created, modified) VALUES
('Temps complet', now(), now()),
('Temps partiel', now(), now());

INSERT INTO typeemplois (name, created, modified) VALUES
('Accès à un emploi temporaire (CDD de moins de 6 mois, intérim)', now(), now()),
('Accès à un emploi CDD de plus de 6 mois', now(), now()),
('Accès à un emploi CDI', now(), now()),
('Accès à un emploi aidé', now(), now()),
('Accès à un emploi salarié SIAE', now(), now()),
('Accès à une activité d\'indépendant, création d\'entreprise', now(), now());

ALTER TABLE questionnairesd2pdvs93 ADD COLUMN emploiromev3_id integer;
ALTER TABLE questionnairesd2pdvs93 ADD CONSTRAINT questionnairesd2pdvs93_emploiromev3_id_fkey FOREIGN KEY (emploiromev3_id)
	REFERENCES entreesromesv3 (id) MATCH SIMPLE
	ON UPDATE CASCADE ON DELETE SET NULL;

ALTER TABLE questionnairesd2pdvs93 ADD COLUMN dureeemploi_id integer;
ALTER TABLE questionnairesd2pdvs93 ADD CONSTRAINT questionnairesb7pdvs93_dureeemploi_id_fkey FOREIGN KEY (dureeemploi_id)
	REFERENCES dureeemplois (id) MATCH SIMPLE
	ON UPDATE CASCADE ON DELETE SET NULL;

ALTER TABLE sortiesaccompagnementsd2pdvs93 ADD COLUMN actif smallint NOT NULL DEFAULT 1;

INSERT INTO sortiesaccompagnementsd2pdvs93 (name, parent_id, created, modified, actif) VALUES
('Accès à un emploi CDI', '1', '2018-06-08 11:30:00', '2018-06-08 11:30:00', 1),
('Accès à un emploi CDD de + de 6 mois', '1', '2018-06-08 11:30:00', '2018-06-08 11:30:00', 1),
('Accès à un emploi temporaire (CDD de - de 6 mois, intérim)', '1', '2018-06-08 11:30:00', '2018-06-08 11:30:00', 1),
('Accès à une activité d''indépendant, création d''entreprise', '1', '2018-06-08 11:30:00', '2018-06-08 11:30:00', 1),
('Accès à un emploi aidé', '1', '2018-06-08 11:30:00', '2018-06-08 11:30:00', 1),
('Accès à un emploi salarié SIAE', '1', '2018-06-08 11:30:00', '2018-06-08 11:30:00', 1);

UPDATE sortiesaccompagnementsd2pdvs93 SET actif = 0 WHERE id = 5;
UPDATE sortiesaccompagnementsd2pdvs93 SET actif = 0 WHERE id = 6;
UPDATE sortiesaccompagnementsd2pdvs93 SET actif = 0 WHERE id = 4;
UPDATE sortiesaccompagnementsd2pdvs93 SET actif = 0 WHERE id = 11;

ALTER TABLE questionnairesd2pdvs93 ADD COLUMN toujoursenemploi smallint NOT NULL DEFAULT 0;


-- *****************************************************************************
-- Fiches de positionnement

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
