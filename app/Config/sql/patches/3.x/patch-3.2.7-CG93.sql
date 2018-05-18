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


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
