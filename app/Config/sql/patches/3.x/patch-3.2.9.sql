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

/*
*
* Creation de la table des titres emmis
*
*/
CREATE TABLE titrescreanciers (
  id serial NOT NULL,
  creance_id integer NOT NULL,
  dtemissiontitre date,
  dtvalidation date,
  etatranstitr character varying(3) NOT NULL,
  numtitr character varying(30) NOT NULL,
  mnttitr numeric(9,2) NOT NULL,
  typetitre character varying(3) NOT NULL,
  mention character varying(255),
  qual character varying(3),
  nom character varying(50),
  nir character(15),
  numtel character varying(14)
  haspiecejointe character varying(1) DEFAULT '0'::character varying,
);

/*
*
* Creation de la table des rapport crée par les talends d'intégration des flux CNAF
*
*/
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
