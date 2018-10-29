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

ALTER TABLE creances ADD COLUMN moismoucompta date;
ALTER TABLE creances ADD COLUMN orgcre character(3);
ALTER TABLE creances ADD COLUMN haspiecejointe character varying(1) NOT NULL DEFAULT '0'::character varying;

CREATE TABLE administration.rapportstalendscreances (
  id serial NOT NULL,
  flux character(15),
  dtexec date,
  fichierflux character(40),
  nbrejete numeric(6,0),
  fichierrejet character(40),
  nbinser numeric(6,0),
  nbmaj numeric(6,0),
  message character(1000)
);


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
