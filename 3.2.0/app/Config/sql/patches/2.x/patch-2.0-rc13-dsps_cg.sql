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

CREATE TABLE dsps_revs (
	id						SERIAL NOT NULL PRIMARY KEY,
    dsp_id                  INTEGER NOT NULL,
    personne_id             INTEGER NOT NULL REFERENCES personnes(id),
	-- Généralités
    sitpersdemrsa   		type_sitpersdemrsa DEFAULT NULL,
    topisogroouenf   		type_booleannumber DEFAULT NULL,
    topdrorsarmiant   		type_booleannumber DEFAULT NULL,
    drorsarmianta2   		type_nos DEFAULT NULL,
    topcouvsoc    			type_booleannumber DEFAULT NULL,
	-- SituationSociale - CommunSituationSociale
    accosocfam    			type_nov DEFAULT NULL,
    libcooraccosocfam  		VARCHAR(250) DEFAULT NULL,
    accosocindi    			type_nov DEFAULT NULL,
    libcooraccosocindi  	VARCHAR(250) DEFAULT NULL,
    soutdemarsoc   			type_nov DEFAULT NULL,
	-- NiveauEtude
    nivetu     				type_nivetu DEFAULT NULL,
    nivdipmaxobt   			type_nivdipmaxobt DEFAULT NULL,
    annobtnivdipmax   		CHAR(4) DEFAULT NULL,
    topqualipro    			type_booleannumber DEFAULT NULL,
    libautrqualipro   		VARCHAR(100) DEFAULT NULL,
    topcompeextrapro  		type_booleannumber DEFAULT NULL,
    libcompeextrapro  		VARCHAR(100) DEFAULT NULL,
	-- DisponibiliteEmploi
    topengdemarechemploi	type_booleannumber DEFAULT NULL,
	-- SituationProfessionnelle
    hispro     				type_hispro DEFAULT NULL,
    libderact    			VARCHAR(100) DEFAULT NULL,
    libsecactderact   		VARCHAR(100) DEFAULT NULL,
    cessderact    			type_cessderact DEFAULT NULL,
    topdomideract   		type_booleannumber DEFAULT NULL,
    libactdomi    			VARCHAR(100) DEFAULT NULL,
    libsecactdomi   		VARCHAR(100) DEFAULT NULL,
    duractdomi    			type_duractdomi DEFAULT NULL,
    inscdememploi   		type_inscdememploi DEFAULT NULL,
    topisogrorechemploi  	type_booleannumber DEFAULT NULL,
    accoemploi    			type_accoemploi DEFAULT NULL,
    libcooraccoemploi  		VARCHAR(100) DEFAULT NULL,
    topprojpro    			type_booleannumber DEFAULT NULL,
    libemploirech   		VARCHAR(250) DEFAULT NULL,
    libsecactrech   		VARCHAR(250) DEFAULT NULL,
    topcreareprientre		type_booleannumber DEFAULT NULL,
    concoformqualiemploi 	type_nos DEFAULT NULL,
	-- Mobilite - CommunMobilite
    topmoyloco    			type_booleannumber DEFAULT NULL,
    toppermicondub   		type_booleannumber DEFAULT NULL,
    topautrpermicondu  		type_booleannumber DEFAULT NULL,
    libautrpermicondu  		VARCHAR(100) DEFAULT NULL,
	-- DifficulteLogement - CommunDifficulteLogement
    natlog     				type_natlog DEFAULT NULL,
    demarlog				type_demarlog DEFAULT NULL,
	created					DATE,
	modified				DATE
);
CREATE INDEX dsps_revs_personne_id_idx ON dsps_revs (personne_id);

CREATE TABLE detailsaccosocindis_revs (
	id					SERIAL NOT NULL PRIMARY KEY,
    dsp_rev_id			INTEGER NOT NULL REFERENCES dsps_revs(id),
	nataccosocindi		type_nataccosocindi NOT NULL,
	libautraccosocindi	VARCHAR(100) DEFAULT NULL
);
CREATE INDEX detailsaccosocindis_revs_dsp_rev_id_idx ON detailsaccosocindis_revs (dsp_rev_id);

CREATE TABLE detailsdifdisps_revs (
	id					SERIAL NOT NULL PRIMARY KEY,
    dsp_rev_id			INTEGER NOT NULL REFERENCES dsps_revs(id),
	difdisp				type_difdisp NOT NULL
);
CREATE INDEX detailsdifdisps_revs_dsp_rev_id_idx ON detailsdifdisps_revs (dsp_rev_id);

CREATE TABLE detailsnatmobs_revs (
	id					SERIAL NOT NULL PRIMARY KEY,
    dsp_rev_id			INTEGER NOT NULL REFERENCES dsps_revs(id),
	natmob				type_natmob NOT NULL
);
CREATE INDEX detailsnatmobs_revs_dsp_rev_id_idx ON detailsnatmobs_revs (dsp_rev_id);

CREATE TABLE detailsdiflogs_revs (
	id					SERIAL NOT NULL PRIMARY KEY,
    dsp_rev_id			INTEGER NOT NULL REFERENCES dsps_revs(id),
	diflog				type_diflog NOT NULL,
	libautrdiflog		VARCHAR(100) DEFAULT NULL
);
CREATE INDEX detailsdiflogs_revs_dsp_rev_id_idx ON detailsdiflogs_revs (dsp_rev_id);

CREATE TABLE detailsdifsocs_revs (
	id					SERIAL NOT NULL PRIMARY KEY,
    dsp_rev_id			INTEGER NOT NULL REFERENCES dsps_revs(id),
	difsoc				type_difsoc NOT NULL,
	libautrdifsoc		VARCHAR(100) DEFAULT NULL
);
CREATE INDEX detailsdifsocs_revs_dsp_rev_id_idx ON detailsdifsocs_revs (dsp_rev_id);

CREATE TABLE detailsaccosocfams_revs (
	id					SERIAL NOT NULL PRIMARY KEY,
    dsp_rev_id			INTEGER NOT NULL REFERENCES dsps_revs(id),
	nataccosocfam		type_nataccosocfam NOT NULL,
	libautraccosocfam	VARCHAR(100) DEFAULT NULL
);
CREATE INDEX detailsaccosocfams_revs_dsp_rev_id_idx ON detailsaccosocfams_revs (dsp_rev_id);

------------------------------------------------------------------------------------------------------

CREATE TYPE type_moytrans AS ENUM ( '2001', '2002', '2003', '2004', '2005', '2006', '2007', '2008' );
CREATE TABLE detailsmoytrans (
    id      		SERIAL NOT NULL PRIMARY KEY,
    dsp_id			INTEGER NOT NULL REFERENCES dsps(id),
	moytrans		type_moytrans NOT NULL,
	libautrmoytrans	VARCHAR(100) DEFAULT NULL
);
CREATE INDEX detailsmoytrans_dsp_id_idx ON detailsmoytrans (dsp_id);

CREATE TABLE detailsmoytrans_revs (
    id      		SERIAL NOT NULL PRIMARY KEY,
    dsp_rev_id		INTEGER NOT NULL REFERENCES dsps_revs(id),
	moytrans		type_moytrans NOT NULL,
	libautrmoytrans	VARCHAR(100) DEFAULT NULL
);
CREATE INDEX detailsmoytrans_revs_dsp_id_idx ON detailsmoytrans_revs (dsp_rev_id);

CREATE TYPE type_difsocpro AS ENUM ( '2101', '2102', '2103', '2104', '2105', '2106', '2107', '2108', '2109', '2110' );
CREATE TABLE detailsdifsocpros (
    id      			SERIAL NOT NULL PRIMARY KEY,
    dsp_id				INTEGER NOT NULL REFERENCES dsps(id),
	difsocpro			type_difsocpro NOT NULL,
	libautrdifsocpro	VARCHAR(100) DEFAULT NULL
);
CREATE INDEX detailsdifsocpros_dsp_id_idx ON detailsdifsocpros (dsp_id);

CREATE TABLE detailsdifsocpros_revs (
    id      			SERIAL NOT NULL PRIMARY KEY,
    dsp_rev_id			INTEGER NOT NULL REFERENCES dsps_revs(id),
	difsocpro			type_difsocpro NOT NULL,
	libautrdifsocpro	VARCHAR(100) DEFAULT NULL
);
CREATE INDEX detailsdifsocpros_revs_dsp_id_idx ON detailsdifsocpros_revs (dsp_rev_id);

------------------------------------------------------------------------------------------------------

ALTER TABLE detailsdifdisps ALTER COLUMN difdisp TYPE VARCHAR(4);
ALTER TABLE detailsdifdisps_revs ALTER COLUMN difdisp TYPE VARCHAR(4);

DROP TYPE type_difdisp;
CREATE TYPE type_difdisp AS ENUM ( '0501', '0502', '0503', '0504', '0505', '0506', '0507', '0508', '0509', '0510', '0511', '0512', '0513' );

ALTER TABLE detailsdifdisps ALTER COLUMN difdisp TYPE type_difdisp USING CAST(difdisp AS type_difdisp);
ALTER TABLE detailsdifdisps_revs ALTER COLUMN difdisp TYPE type_difdisp USING CAST(difdisp AS type_difdisp);

------------------------------------------------------------------------------------------------------

CREATE TYPE type_projpro AS ENUM ( '2201', '2202', '2203', '2204', '2205', '2206', '2207', '2208', '2209', '2210', '2211', '2212', '2213' );
CREATE TABLE detailsprojpros (
    id      			SERIAL NOT NULL PRIMARY KEY,
    dsp_id				INTEGER NOT NULL REFERENCES dsps(id),
	projpro				type_projpro NOT NULL,
	libautrprojpro		VARCHAR(100) DEFAULT NULL
);
CREATE INDEX detailsprojpros_dsp_id_idx ON detailsprojpros (dsp_id);

CREATE TABLE detailsprojpros_revs (
    id      			SERIAL NOT NULL PRIMARY KEY,
    dsp_rev_id			INTEGER NOT NULL REFERENCES dsps_revs(id),
	projpro				type_projpro NOT NULL,
	libautrprojpro		VARCHAR(100) DEFAULT NULL
);
CREATE INDEX detailsprojpros_revs_dsp_id_idx ON detailsprojpros_revs (dsp_rev_id);

------------------------------------------------------------------------------------------------------

ALTER TABLE dsps ADD COLUMN libformenv VARCHAR(250) DEFAULT NULL;

ALTER TABLE dsps_revs ADD COLUMN libformenv VARCHAR(250) DEFAULT NULL;

CREATE TYPE type_freinform AS ENUM ( '2301', '2302', '2303', '2304', '2305', '2306', '2307', '2308' );
CREATE TABLE detailsfreinforms (
    id      			SERIAL NOT NULL PRIMARY KEY,
    dsp_id				INTEGER NOT NULL REFERENCES dsps(id),
	freinform			type_freinform NOT NULL
);
CREATE INDEX detailsfreinforms_dsp_id_idx ON detailsfreinforms (dsp_id);

CREATE TABLE detailsfreinforms_revs (
    id      			SERIAL NOT NULL PRIMARY KEY,
    dsp_rev_id			INTEGER NOT NULL REFERENCES dsps_revs(id),
	freinform			type_freinform NOT NULL
);
CREATE INDEX detailsfreinforms_revs_dsp_id_idx ON detailsfreinforms_revs (dsp_rev_id);

------------------------------------------------------------------------------------------------------

CREATE TYPE type_confort AS ENUM ( '2401', '2402', '2403', '2404' );
CREATE TABLE detailsconforts (
	id      		SERIAL NOT NULL PRIMARY KEY,
	dsp_id			INTEGER NOT NULL REFERENCES dsps(id),
	confort			type_confort NOT NULL
);
CREATE INDEX detailsconforts_dsp_id_idx ON detailsconforts (dsp_id);

CREATE TABLE detailsconforts_revs (
    id      			SERIAL NOT NULL PRIMARY KEY,
    dsp_rev_id			INTEGER NOT NULL REFERENCES dsps_revs(id),
	confort				type_confort NOT NULL
);
CREATE INDEX detailsconforts_revs_dsp_id_idx ON detailsconforts_revs (dsp_rev_id);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************