BEGIN;

----------- 24/02/2010 - 14h50 ----------------------
ALTER TABLE propospdos ADD COLUMN datereceptionpdo DATE;
CREATE TYPE type_statutdecision AS ENUM ( 'DEF', 'UND' );
ALTER TABLE propospdos ADD COLUMN statutdecision type_statutdecision;

CREATE TABLE originespdos (
    id                          SERIAL NOT NULL PRIMARY KEY,
    libelle                     VARCHAR(30)
);
CREATE INDEX originespdos_libelle_idx ON originespdos (libelle);
COMMENT ON TABLE originespdos IS 'Table des origines des demandes de PDOs';

ALTER TABLE propospdos ADD COLUMN originepdo_id INTEGER REFERENCES originespdos(id);


----------- 01/03/2010 - 10h10 ----------------------
CREATE TABLE situationspdos (
    id                          SERIAL NOT NULL PRIMARY KEY,
    libelle                     VARCHAR(50)
);
CREATE INDEX situationspdos_libelle_idx ON situationspdos (libelle);
COMMENT ON TABLE situationspdos IS 'Table des situations des demandes de PDOs';

-----------------------------------------------------
CREATE TABLE statutspdos (
    id                          SERIAL NOT NULL PRIMARY KEY,
    libelle                     VARCHAR(50)
);
CREATE INDEX statutspdos_libelle_idx ON statutspdos (libelle);
COMMENT ON TABLE statutspdos IS 'Table des statuts des demandes de PDOs';

-----------------------------------------------------
CREATE TABLE propospdos_situationspdos (
    id              SERIAL NOT NULL PRIMARY KEY,
    propopdo_id           INTEGER NOT NULL REFERENCES propospdos(id),
    situationpdo_id       INTEGER NOT NULL REFERENCES situationspdos(id)
);

CREATE INDEX propospdos_situationspdos_propopdo_id_idx ON propospdos_situationspdos (propopdo_id);
CREATE INDEX propospdos_situationspdos_situationpdo_id_idx ON propospdos_situationspdos (situationpdo_id);

COMMENT ON TABLE propospdos_situationspdos IS 'Situations des allocataires liés aux PDOs';

-----------------------------------------------------
CREATE TABLE propospdos_statutspdos (
    id              SERIAL NOT NULL PRIMARY KEY,
    propopdo_id           INTEGER NOT NULL REFERENCES propospdos(id),
    statutpdo_id       INTEGER NOT NULL REFERENCES statutspdos(id)
);

CREATE INDEX propospdos_statutspdos_propopdo_id_idx ON propospdos_statutspdos (propopdo_id);
CREATE INDEX propospdos_statutspdos_statutpdo_id_idx ON propospdos_statutspdos (statutpdo_id);

COMMENT ON TABLE propospdos_statutspdos IS 'Statuts des allocataires liés aux PDOs';

--------------------------- Ajout du 04/03/2010 ---------------

-- ALTER TABLE evenements ADD COLUMN foyer_id INTEGER NOT NULL REFERENCES foyers(id);
-- ALTER TABLE creances ADD COLUMN foyer_id INTEGER NOT NULL REFERENCES foyers(id);
-- DROP TABLE foyers_evenements;
-- DROP TABLE foyers_anomalies;
-- DROP TABLE foyers_creances;

COMMIT;

-- *****************************************************************************
-- Demandes de réorientation
-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- DROP TABLE precosreorients;
-- DROP TABLE eps_partseps;
-- DROP TABLE demandesreorient;
-- DROP TABLE eps;
-- DROP TABLE partseps;
-- DROP TABLE rolespartseps;
-- DROP TABLE motifsdemsreorients;
-- DROP TYPE type_presenceep;
-- DROP TYPE type_rolereorient;

-- *****************************************************************************

CREATE TABLE eps (
	id				SERIAL NOT NULL PRIMARY KEY,
	name			VARCHAR(255) NOT NULL,
	date			TIMESTAMP WITHOUT TIME ZONE,
	localisation	VARCHAR(255) NOT NULL,
	traitee			type_booleannumber DEFAULT '0', -- virtual field ou aftersave /afterdelete ?
	traiteecg		type_booleannumber DEFAULT '0' -- virtual field ou aftersave /afterdelete ?
);

COMMENT ON TABLE eps IS 'Équipes pluridisciplinaires';
COMMENT ON COLUMN eps.traitee IS 'Décision prise pour tous les dossiers par l''EP ?';
COMMENT ON COLUMN eps.traiteecg IS 'Décision prise pour tous les dossiers par le CG ?';

-- -----------------------------------------------------------------------------

CREATE TABLE partseps (-- FIXME: cli, etc ...
	id				SERIAL NOT NULL PRIMARY KEY,
	qual			VARCHAR(3) NOT NULL,
	nom				VARCHAR(255) NOT NULL,
	prenom			VARCHAR(255) NOT NULL,
	tel				VARCHAR(14) DEFAULT NULL,
	email			VARCHAR(255) DEFAULT NULL
);

COMMENT ON TABLE partseps IS 'Participants aux équipes pluridisciplinaires';

-- -----------------------------------------------------------------------------

CREATE TABLE rolespartseps (
	id		SERIAL NOT NULL PRIMARY KEY,
	name	VARCHAR(255) NOT NULL
);

COMMENT ON TABLE rolespartseps IS 'Rôles de participants aux équipes pluridisciplinaires';

-- -----------------------------------------------------------------------------

CREATE TYPE type_presenceep AS ENUM ( 'absent', 'present', 'remplace', 'excuse' );

-- -----------------------------------------------------------------------------

CREATE TABLE eps_partseps (
	id				SERIAL NOT NULL PRIMARY KEY,
	ep_id			INTEGER NOT NULL REFERENCES eps(id),
	partep_id		INTEGER NOT NULL REFERENCES partseps(id),
	rolepartep_id	INTEGER NOT NULL REFERENCES rolespartseps(id),
	presencepre		type_booleannumber DEFAULT NULL,
	presenceeff		type_presenceep DEFAULT NULL,
	parteprempl_id	INTEGER DEFAULT NULL REFERENCES partseps(id)
);

CREATE INDEX eps_partseps_ep_id_idx ON eps_partseps (ep_id);
CREATE INDEX eps_partseps_partep_id_idx ON eps_partseps (partep_id);
CREATE INDEX eps_partseps_rolepartep_id_idx ON eps_partseps (rolepartep_id);
CREATE INDEX eps_partseps_parteprempl_id_idx ON eps_partseps (parteprempl_id);

COMMENT ON TABLE eps_partseps IS 'Rôle et présences des participants liés aux EPs';

-- *****************************************************************************

CREATE TABLE motifsdemsreorients (
	id		SERIAL NOT NULL PRIMARY KEY,
	name	VARCHAR(255) NOT NULL
);

-- -----------------------------------------------------------------------------

CREATE TABLE demandesreorient (
	id						SERIAL NOT NULL PRIMARY KEY,
	personne_id			INTEGER NOT NULL REFERENCES personnes(id),
	-- Référent
	reforigine_id			INTEGER NOT NULL REFERENCES referents(id),-- FIXME: par orientations.xxx ?
	motifdemreorient_id		INTEGER NOT NULL REFERENCES motifsdemsreorients(id),
	commentaire				TEXT DEFAULT NULL,
	urgent					type_booleannumber DEFAULT '0',
	ep_id					INTEGER DEFAULT NULL REFERENCES eps(id),
	created					TIMESTAMP WITHOUT TIME ZONE,
	accordbenef				type_booleannumber DEFAULT NULL,
	orientstruct_id			INTEGER DEFAULT NULL REFERENCES orientsstructs(id) -- UNIQUE
	/*refaccueil_id			INTEGER NOT NULL REFERENCES referents(id),-- FIXME: mêmes champs que dans orientations.xxx ?
	accordrefaccueil		type_booleannumber DEFAULT NULL,
	commentairerefaccueil	TEXT DEFAULT NULL,
	accordbenef				type_booleannumber DEFAULT NULL,
	-- EP
	decisionep				type_booleannumber DEFAULT NULL,
	motifdecisionep			TEXT DEFAULT NULL,
	refaccueilep_id			INTEGER DEFAULT NULL REFERENCES referents(id),-- FIXME: mêmes champs que dans orientations.xxx ?
	-- CG
	decisioncg				type_booleannumber DEFAULT NULL,
	motifdecisioncg			TEXT DEFAULT NULL,
	refaccueilcg_id			INTEGER DEFAULT NULL REFERENCES referents(id),-- FIXME: mêmes champs que dans orientations.xxx ?
	--
	dateimpression			TIMESTAMP WITHOUT TIME ZONE DEFAULT NULL*/
);

CREATE INDEX demandesreorient_orientstruct_id_idx ON demandesreorient (orientstruct_id);
CREATE INDEX demandesreorient_personne_id_idx ON demandesreorient (personne_id);
CREATE INDEX demandesreorient_motifdemreorient_id_idx ON demandesreorient (motifdemreorient_id);
CREATE INDEX demandesreorient_reforigine_id_idx ON demandesreorient (reforigine_id);
CREATE INDEX demandesreorient_ep_id_idx ON demandesreorient (ep_id);

-- -----------------------------------------------------------------------------

CREATE TYPE type_rolereorient AS ENUM ( 'referent', 'equipe', 'conseil' );

CREATE TABLE precosreorients (
	id						SERIAL NOT NULL PRIMARY KEY,
	-- FIXME
	-- orientstruct_id
	demandereorient_id		INTEGER NOT NULL REFERENCES demandesreorient(id),
	rolereorient			type_rolereorient NOT NULL,
	typeorient_id			INTEGER NOT NULL REFERENCES typesorients(id),
	structurereferente_id	INTEGER NOT NULL REFERENCES structuresreferentes(id),
	referent_id				INTEGER NOT NULL REFERENCES referents(id),
	accord					type_booleannumber DEFAULT NULL,-- null, 0, 1 ou boolean ? référent accueil, équipe, conseil
	commentaire				TEXT DEFAULT NULL,
	created					TIMESTAMP WITHOUT TIME ZONE
);

CREATE INDEX precosreorients_demandereorient_id_idx ON precosreorients (demandereorient_id);
CREATE INDEX precosreorients_rolereorient_idx ON precosreorients (rolereorient);
CREATE INDEX precosreorients_typeorient_id_idx ON precosreorients (typeorient_id);
CREATE INDEX precosreorients_structurereferente_id_idx ON precosreorients (structurereferente_id);
CREATE INDEX precosreorients_referent_id_idx ON precosreorients (referent_id);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************

-- *****************************************************************************
-- Parcours bénéficiaires
-- *****************************************************************************

BEGIN;
-- DROP TABLE decisionsparcours;
-- DROP TABLE parcoursdetectes;
-- DROP TYPE type_roleparcours;
COMMIT;

-- *****************************************************************************

BEGIN;

-- DROP INDEX decisionsparcours_structuresreferentes_idx; -- FIXME ??

-- -----------------------------------------------------------------------------

CREATE TABLE parcoursdetectes (
	id				SERIAL NOT NULL PRIMARY KEY,
	orientstruct_id	INTEGER NOT NULL REFERENCES orientsstructs(id),
	signale			type_booleannumber DEFAULT NULL,
	commentaire		TEXT DEFAULT NULL, -- Commentaire du référent
	created			DATE NOT NULL, -- Date de détection
	datetransref	DATE DEFAULT NULL, -- Date de transmission au référent
	ep_id			INTEGER DEFAULT NULL REFERENCES eps(id),
	osnv_id			INTEGER DEFAULT NULL REFERENCES orientsstructs(id) -- Nv orientstruct
);

CREATE UNIQUE INDEX parcoursdetectes_orientstruct_id_idx ON parcoursdetectes (orientstruct_id);
CREATE UNIQUE INDEX parcoursdetectes_osnv_id_idx ON parcoursdetectes (osnv_id);
CREATE INDEX parcoursdetectes_ep_id_idx ON parcoursdetectes (ep_id);

-- -----------------------------------------------------------------------------

CREATE TYPE type_roleparcours AS ENUM ( 'equipe', 'conseil' );

CREATE TABLE decisionsparcours (
	id						SERIAL NOT NULL PRIMARY KEY,
	parcoursdetecte_id		INTEGER NOT NULL REFERENCES parcoursdetectes(id),
	roleparcours			type_rolereorient NOT NULL,
	maintien				type_booleannumber DEFAULT NULL,
	typeorient_id			INTEGER DEFAULT NULL REFERENCES typesorients(id),
	structurereferente_id	INTEGER DEFAULT NULL REFERENCES structuresreferentes(id),
	referent_id				INTEGER DEFAULT NULL REFERENCES referents(id),
	commentaire				TEXT DEFAULT NULL,
	created					DATE NOT NULL
);

CREATE INDEX decisionsparcours_parcoursdetecte_id_idx ON decisionsparcours (parcoursdetecte_id);
CREATE INDEX decisionsparcours_typeorient_id_idx ON decisionsparcours (typeorient_id);
CREATE INDEX decisionsparcours_structuresreferentes_idx ON structuresreferentes (typeorient_id);
CREATE INDEX decisionsparcours_referent_id_idx ON decisionsparcours (referent_id);

--------------------------- Ajout du 09/03/2010 ---------------
ALTER TABLE eps ADD COLUMN validordre type_booleannumber DEFAULT '0';

COMMIT;
