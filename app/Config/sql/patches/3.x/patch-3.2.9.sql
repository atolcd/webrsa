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
  nir character varying(15),
  numtel character varying(14),
  haspiecejointe character varying(1) DEFAULT '0'::character varying,
);

/*
* Creation de la table des rapport crée par les talends d'intégration des flux CNAF
*/
CREATE TABLE administration.rapportstalendscreances (
  id serial NOT NULL,
  flux character varying(15),
  typeflux character varying(1),
  natflux character varying(1),
  dtflux date,
  dtref date,
  dtexec date,
  fichierflux character varying(80),
  nbtotdosrsatransm numeric(8,0),
  nbtotdosrsatransmano numeric(8,0),
  nbrejete numeric(6,0),
  fichierrejet character varying(40),
  nbinser numeric(6,0),
  nbmaj numeric(6,0),
  message character varying(1000)
);

/*
* Creation de la table des personnes rejetées par les talends d'intégration des flux CNAF
*/
CREATE TABLE administration.rejetstalendscreances (
	id 					serial NOT NULL,
	fusion 				BOOLEAN DEFAULT FALSE,
	flux 				character varying(15),
	typeflux 			character varying(1),
	natflux 			character varying(1),
	dtflux 				date,
	dtref 				date,
	dtexec 				date,
	fichierflux 		character varying(80),
	matricule			VARCHAR(15) DEFAULT NULL,
	numdemrsa		  	VARCHAR(11) DEFAULT NULL,
	dtdemrsa			DATE NOT NULL,
	ddratdos	   		DATE,
	dfratdos			DATE,
	toprespdos	  		BOOLEAN,
	nir					CHAR(15),
	qual				VARCHAR(3) DEFAULT NULL,
	nom					VARCHAR(50) NOT NULL,
	nomnai				VARCHAR(50) DEFAULT NULL,
	prenom				VARCHAR(50) NOT NULL,
	prenom2				VARCHAR(50) NOT NULL,
	prenom3				VARCHAR(50) NOT NULL,
	dtnai				DATE NOT NULL,
	nomcomnai			VARCHAR(26) DEFAULT NULL,
	typedtnai			CHAR(1),
	typeparte			CHAR(4),
	ideparte			CHAR(3),
	topvalec			BOOLEAN,
	sexe				CHAR(1),
	rgadr	   			CHAR(2),
	dtemm	   			DATE,
	typeadr	 			CHAR(1),
	numvoie	 			VARCHAR(6),
	libtypevoie			VARCHAR(10),
	nomvoie	 			VARCHAR(25),
	complideadr 		VARCHAR(50),
	compladr			VARCHAR(50),
	lieudist			VARCHAR(32),
	numcom   			CHAR(5),
	codepos				CHAR(5),
	dtimplcre			DATE,
	natcre	  			CHAR(3),
	rgcre	   			CHAR(3),
	motiindu			CHAR(2),
	oriindu	 			CHAR(2),
	respindu			CHAR(2),
	ddregucre	 		DATE,
	dfregucre	 		DATE,
	dtdercredcretrans   DATE,
	mtsolreelcretrans   NUMERIC(9,2),
	mtinicre			NUMERIC(9,2),
	moismoucompta 		DATE,
	liblig2adr			VARCHAR(38) DEFAULT NULL,
	liblig3adr			VARCHAR(38) DEFAULT NULL,
	liblig4adr 			VARCHAR(38) DEFAULT NULL,
	liblig5adr 			VARCHAR(38) DEFAULT NULL,
	liblig6adr 			VARCHAR(38) DEFAULT NULL,
	liblig7adr 			VARCHAR(38) DEFAULT NULL
);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
