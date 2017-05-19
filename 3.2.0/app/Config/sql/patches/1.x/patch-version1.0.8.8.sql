SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- -----------------------------------------------------------------------------

-- CREATE TYPE type_booleannumber AS ENUM ( '0', '1' );
CREATE TYPE type_no AS ENUM ( 'N', 'O' );
-- CREATE TYPE type_nos AS ENUM ( 'N', 'O', 'S' );
-- CREATE TYPE type_nov AS ENUM ( 'N', 'O', 'V' );
-- CREATE TYPE type_sitpersdemrsa AS ENUM ( '0101', '0102', '0103', '0104', '0105', '0106', '0107', '0108', '0109' );
-- CREATE TYPE type_nivetu AS ENUM ( '1201', '1202', '1203', '1204', '1205', '1206', '1207' );
-- CREATE TYPE type_nivdipmaxobt AS ENUM ( '2601', '2602', '2603', '2604', '2605', '2606' );
-- CREATE TYPE type_hispro AS ENUM ( '1901', '1902', '1903', '1904' );
-- CREATE TYPE type_cessderact AS ENUM ( '2701', '2702' );
-- CREATE TYPE type_duractdomi AS ENUM ( '2104', '2105', '2106', '2107'  );
-- CREATE TYPE type_inscdememploi AS ENUM ( '4301', '4302', '4303', '4304'  );
-- CREATE TYPE type_accoemploi AS ENUM ( '1801', '1802', '1803'  );
-- CREATE TYPE type_natlog AS ENUM ( '0901', '0902', '0903', '0904', '0905', '0906', '0907', '0908', '0909', '0910', '0911', '0912', '0913'  );
-- CREATE TYPE type_demarlog AS ENUM ( '1101', '1102', '1103' );

-- -----------------------------------------------------------------------------
-- Stockage
-- -----------------------------------------------------------------------------

CREATE TABLE integrationfichiersapre (
    id      			SERIAL NOT NULL PRIMARY KEY,
	date_integration	TIMESTAMP NOT NULL,
	nbr_atraiter		INTEGER NOT NULL,
	nbr_succes			INTEGER NOT NULL,
	nbr_erreurs			INTEGER NOT NULL,
	fichier_in			VARCHAR(250) NOT NULL,
	erreurs				TEXT
);

-- --------------------------------------------------------------------------------------------------------
-- Ajout de la table "referentsapre" liée à 'apres'
-- --------------------------------------------------------------------------------------------------------

CREATE TABLE referentsapre (
    id                          SERIAL NOT NULL PRIMARY KEY,
    qual                        VARCHAR(3),
    nom                         VARCHAR(28),
    prenom                      VARCHAR(32),
    adresse                     TEXT,
    numtel                      VARCHAR(10),
    email                       VARCHAR(78),
    fonction                    VARCHAR(50),
    organismeref                VARCHAR(50)
);
-- CREATE INDEX referentsapre_apre_id_idx ON referentsapre (apre_id);

-- --------------------------------------------------------------------------------------------------------
-- Ajout de la table "apre" liée à 'personnes'
-- --------------------------------------------------------------------------------------------------------
CREATE TYPE type_typedemandeapre AS ENUM ( 'FO', 'AU' );
CREATE TYPE type_naturelogement AS ENUM ( 'P', 'L', 'H', 'S', 'A' );
-- CREATE TYPE type_activitebeneficiaire AS ENUM ( 'E', 'F', 'C' );
CREATE TYPE type_activitebeneficiaire AS ENUM ( 'E', 'F', 'C', 'P' );

CREATE TYPE type_typecontrat AS ENUM ( 'CDI', 'CDD', 'CON', 'AUT' );

CREATE TABLE apres (
    id                              SERIAL NOT NULL PRIMARY KEY,
    personne_id                     INTEGER NOT NULL REFERENCES personnes(id),
    referentapre_id                 INTEGER REFERENCES referentsapre(id),
    numeroapre                      VARCHAR(16),
    typedemandeapre                 type_typedemandeapre DEFAULT NULL,
    datedemandeapre                 DATE,
    naturelogement                  type_naturelogement DEFAULT NULL,
    precisionsautrelogement         VARCHAR(20),
    anciennetepoleemploi            VARCHAR(20),
    projetprofessionnel             TEXT,
    secteurprofessionnel            TEXT,
    activitebeneficiaire            type_activitebeneficiaire DEFAULT NULL,
    dateentreeemploi                DATE,
    typecontrat                     type_typecontrat DEFAULT NULL,
    precisionsautrecontrat          VARCHAR(50),
    nbheurestravaillees             NUMERIC(4),
    nomemployeur                    VARCHAR(50),
    adresseemployeur                TEXT,
    quota                           NUMERIC(10,2),
    derogation                      NUMERIC(10,2),
    avistechreferent                TEXT
);
CREATE INDEX apres_personne_id_idx ON apres (personne_id);

-- --------------------------------------------------------------------------------------------------------
-- Ajout de la table "montantsconsommes" liée à 'apres'
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE montantsconsommes (
    id                          SERIAL NOT NULL PRIMARY KEY,
    apre_id                     INTEGER NOT NULL REFERENCES apres(id),
    montantconso                NUMERIC(10,2),
    dateconso                   DATE,
    justifconso                 VARCHAR(50)
);
CREATE INDEX montantsconsommes_apre_id_idx ON montantsconsommes (apre_id);

-- --------------------------------------------------------------------------------------------------------
--  ....
-- --------------------------------------------------------------------------------------------------------

CREATE TABLE piecesapre (
    id                          SERIAL NOT NULL PRIMARY KEY,
    libelle                     TEXT NOT NULL
);

INSERT INTO piecesapre ( libelle ) VALUES
    ( 'Justificatif d''entrée en formation ou de création d''entreprise *' ),
    ( 'Contrat de travail *' ),
    ( 'Contrat d''insertion validé par le Président du Conseil Général (sauf pour SIAE et titulaires de contrats aidés) *' ),
    ( 'Attestation CAF datant du dernier mois de prestation versée' ),
    ( 'Curriculum vitae' ),
    ( 'Lettre motivée de l''allocataire détaillant les besoins' ),
    ( 'RIB de l''allocataire ou de l''organisme' );


CREATE TABLE apres_piecesapre (
    id                          SERIAL NOT NULL PRIMARY KEY,
    apre_id                     INTEGER NOT NULL REFERENCES apres(id),
    pieceapre_id                INTEGER NOT NULL REFERENCES piecesapre(id)
);
CREATE INDEX apres_piecesapre_apre_id_idx ON apres_piecesapre (apre_id);
CREATE INDEX apres_piecesapre_pieceapre_id_idx ON apres_piecesapre (pieceapre_id);

-- --------------------------------------------------------------------------------------------------------
-- --------------------------------------------------------------------------------------------------------
--  ....Données nécessaire pour la table Formqualif
-- --------------------------------------------------------------------------------------------------------

CREATE TABLE formsqualifs (
    id                          SERIAL NOT NULL PRIMARY KEY,
    apre_id                     INTEGER NOT NULL REFERENCES apres(id),
    intituleform                VARCHAR(100) NOT NULL,
    organismeform               VARCHAR(100) NOT NULL,
    ddform                      DATE,
    dfform                      DATE,
    dureeform                   INT4,
    modevalidation              VARCHAR(30),
    coutform                    DECIMAL(10,2),
    cofinanceurs                VARCHAR(30),
    montantaide                 DECIMAL(10,2)
);
CREATE INDEX formsqualifs_apre_id_idx ON formsqualifs (apre_id);
-- --------------------------------------------------------------------------------------------------------
--  ....Table des pièces liées à formqualif
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE piecesformsqualifs (
    id                          SERIAL NOT NULL PRIMARY KEY,
    libelle                     TEXT NOT NULL
);

INSERT INTO piecesformsqualifs ( libelle ) VALUES
    ( 'Attestation d''entrée en formation' ),
    ( 'Devis nominatif détaillé précisant l''intitulé de la formation, son lieu, dates prévisionnelles de début et fin d''action, durée en heure, jours et mois, contenu (heures et modules), l''organisation de la formation, le coût global ainsi que la participation éventuelle du stagiaire' ),
    ( 'Facture ou devis' );

-- --------------------------------------------------------------------------------------------------------
--  ....Table liée Formqualif avec ses pièces
-- --------------------------------------------------------------------------------------------------------

CREATE TABLE formsqualifs_piecesformsqualifs (
    id                          SERIAL NOT NULL PRIMARY KEY,
    formqualif_id               INTEGER NOT NULL REFERENCES formsqualifs(id),
    pieceformqualif_id          INTEGER NOT NULL REFERENCES piecesformsqualifs(id)
);
CREATE INDEX formsqualifs_piecesformsqualifs_formqualif_id_idx ON formsqualifs_piecesformsqualifs (formqualif_id);
CREATE INDEX formsqualifs_piecesformsqualifs_pieceformqualif_id_idx ON formsqualifs_piecesformsqualifs (pieceformqualif_id);

-- --------------------------------------------------------------------------------------------------------
-- --------------------------------------------------------------------------------------------------------
--  ....Données nécessaire pour la table Actprof
-- --------------------------------------------------------------------------------------------------------
CREATE TYPE type_typecontratact AS ENUM ( 'CI', 'CA', 'SA' );
CREATE TABLE actsprofs (
    id                          SERIAL NOT NULL PRIMARY KEY,
    apre_id                     INTEGER NOT NULL REFERENCES apres(id),
    nomemployeur                VARCHAR (50),
    adresseemployeur            VARCHAR (100),
    typecontratact              type_typecontratact DEFAULT NULL,
    ddconvention                DATE,
    dfconvention                DATE,
    intituleformation           VARCHAR (200),
    ddform                      DATE,
    dfform                      DATE,
    dureeform                   INT4,
    modevalidation              VARCHAR (30),
    coutform                    DECIMAL (10, 2),
    cofinanceurs                VARCHAR (30),
    montantaide                 DECIMAL (10, 2)
);
CREATE INDEX actsprofs_apre_id_idx ON actsprofs (apre_id);
-- --------------------------------------------------------------------------------------------------------
--  ....Table des pièces liées à Actprof
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE piecesactsprofs (
    id                          SERIAL NOT NULL PRIMARY KEY,
    libelle                     TEXT NOT NULL
);

INSERT INTO piecesactsprofs ( libelle ) VALUES
    ( 'Convention individuelle (pour les contrats aidés) *' ),
    ( 'Contrat de travail (pour les contrats SIAE) *' ),
    ( 'Facture ou devis' );

-- --------------------------------------------------------------------------------------------------------
--  ....Table liée Actprof avec ses pièces
-- --------------------------------------------------------------------------------------------------------

CREATE TABLE actsprofs_piecesactsprofs (
    id                          SERIAL NOT NULL PRIMARY KEY,
    actprof_id                  INTEGER NOT NULL REFERENCES actsprofs(id),
    pieceactprof_id             INTEGER NOT NULL REFERENCES piecesactsprofs(id)
);
CREATE INDEX actsprofs_piecesactsprofs_actprof_id_idx ON actsprofs_piecesactsprofs (actprof_id);
CREATE INDEX actsprofs_piecesactsprofs_pieceactprof_id_idx ON actsprofs_piecesactsprofs (pieceactprof_id);

-- --------------------------------------------------------------------------------------------------------
-- --------------------------------------------------------------------------------------------------------
--  ....Données nécessaire pour la table Permisb
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE permisb (
    id                          SERIAL NOT NULL PRIMARY KEY,
    apre_id                     INTEGER NOT NULL REFERENCES apres(id),
    nomautoecole                VARCHAR (50),
    adresseautoecole            VARCHAR (100),
    code                        CHAR (1),
    conduite                    CHAR (1),
    dureeform                   INT4,
    coutform                    DECIMAL (10, 2)

);
CREATE INDEX permisb_apre_id_idx ON permisb (apre_id);
-- --------------------------------------------------------------------------------------------------------
--  ....Table des pièces liées à permisb
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE piecespermisb (
    id                          SERIAL NOT NULL PRIMARY KEY,
    libelle                     TEXT NOT NULL
);

INSERT INTO piecespermisb ( libelle ) VALUES
    ( 'Attestation sur l’honneur ou Cerfa 02' ),
    ( 'Devis ou facture' );

-- --------------------------------------------------------------------------------------------------------
--  ....Table liée Permisb avec ses pièces
-- --------------------------------------------------------------------------------------------------------

CREATE TABLE permisb_piecespermisb (
    id                          SERIAL NOT NULL PRIMARY KEY,
    permisb_id                  INTEGER NOT NULL REFERENCES permisb(id),
    piecepermisb_id             INTEGER NOT NULL REFERENCES piecespermisb(id)
);
CREATE INDEX permisb_piecespermisb_permisb_id_idx ON permisb_piecespermisb (permisb_id);
CREATE INDEX permisb_piecespermisb_piecepermisb_id_idx ON permisb_piecespermisb (piecepermisb_id);


-- --------------------------------------------------------------------------------------------------------
-- --------------------------------------------------------------------------------------------------------
--  ....Données nécessaire pour la table Amenaglogt
-- --------------------------------------------------------------------------------------------------------
CREATE TYPE type_typeaidelogement AS ENUM ( 'AEL', 'AML' );
CREATE TABLE amenagslogts (
    id                          SERIAL NOT NULL PRIMARY KEY,
    apre_id                     INTEGER NOT NULL REFERENCES apres(id),
    typeaidelogement            type_typeaidelogement DEFAULT NULL,
    besoins                     VARCHAR (250),
    montantaide                 DECIMAL (10, 2)
);
CREATE INDEX amenagslogts_apre_id_idx ON amenagslogts (apre_id);
-- --------------------------------------------------------------------------------------------------------
--  ....Table des pièces liées à Amenaglogt
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE piecesamenagslogts (
    id                          SERIAL NOT NULL PRIMARY KEY,
    libelle                     TEXT NOT NULL
);

INSERT INTO piecesamenagslogts ( libelle ) VALUES
    ( 'Bail ou contrat de location' ),
    ( 'Devis pour les frais d''agence' ),
    ( 'Devis ou facture frais de déménagement' ),
    ( 'Contrat ou devis assurance habitation' ),
    ( 'Facture ouverture compteurs EDF/GDF' ),
    ( 'Versement caution logement' ),
    ( 'Facture' );

-- --------------------------------------------------------------------------------------------------------
--  ....Table liée Amenaglogt avec ses pièces
-- --------------------------------------------------------------------------------------------------------

CREATE TABLE amenagslogts_piecesamenagslogts (
    id                          SERIAL NOT NULL PRIMARY KEY,
    amenaglogt_id                  INTEGER NOT NULL REFERENCES amenagslogts(id),
    pieceamenaglogt_id             INTEGER NOT NULL REFERENCES piecesamenagslogts(id)
);
CREATE INDEX amenagslogts_piecesamenagslogts_permisb_id_idx ON amenagslogts_piecesamenagslogts (amenaglogt_id);
CREATE INDEX amenagslogts_piecesamenagslogts_piecepermisb_id_idx ON amenagslogts_piecesamenagslogts (pieceamenaglogt_id);


-- --------------------------------------------------------------------------------------------------------
-- --------------------------------------------------------------------------------------------------------
--  ....Données nécessaire pour la table Acccreaentr
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE accscreaentr (
    id                          SERIAL NOT NULL PRIMARY KEY,
    apre_id                     INTEGER NOT NULL REFERENCES apres(id),
    nacre                       type_no DEFAULT NULL,
    microcredit                 type_no DEFAULT NULL,
    projet                      VARCHAR (250),
    montantaide                 DECIMAL (10, 2)
);
CREATE INDEX accscreaentr_apre_id_idx ON accscreaentr (apre_id);
-- --------------------------------------------------------------------------------------------------------
--  ....Table des pièces liées à Acccreaentr
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE piecesaccscreaentr (
    id                          SERIAL NOT NULL PRIMARY KEY,
    libelle                     TEXT NOT NULL
);

INSERT INTO piecesaccscreaentr ( libelle ) VALUES
    ( 'Extrait du Kbis du registre du commerce' ),
    ( 'Facture' );

-- --------------------------------------------------------------------------------------------------------
--  ....Table liée Acccreaentr avec ses pièces
-- --------------------------------------------------------------------------------------------------------

CREATE TABLE accscreaentr_piecesaccscreaentr (
    id                          SERIAL NOT NULL PRIMARY KEY,
    acccreaentr_id                  INTEGER NOT NULL REFERENCES accscreaentr(id),
    pieceacccreaentr_id             INTEGER NOT NULL REFERENCES piecesaccscreaentr(id)
);
CREATE INDEX accscreaentr_piecesaccscreaentr_acccreaentr_id_idx ON accscreaentr_piecesaccscreaentr (acccreaentr_id);
CREATE INDEX accscreaentr_piecesaccscreaentr_pieceacccreaentr_id_idx ON accscreaentr_piecesaccscreaentr (pieceacccreaentr_id);


-- --------------------------------------------------------------------------------------------------------
-- --------------------------------------------------------------------------------------------------------
--  ....Données nécessaire pour la table Acqmatprof
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE acqsmatsprofs (
    id                          SERIAL NOT NULL PRIMARY KEY,
    apre_id                     INTEGER NOT NULL REFERENCES apres(id),
    besoins                     VARCHAR(250),
    montantaide                 DECIMAL (10, 2)
);
CREATE INDEX acqsmatsprofs_apre_id_idx ON acqsmatsprofs (apre_id);
-- --------------------------------------------------------------------------------------------------------
--  ....Table des pièces liées à Acqmatprof
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE piecesacqsmatsprofs (
    id                          SERIAL NOT NULL PRIMARY KEY,
    libelle                     TEXT NOT NULL
);

INSERT INTO piecesacqsmatsprofs ( libelle ) VALUES
    ( 'Attestation d''entrée en formation ou contrat de travail' ),
    ( 'Facture ou devis (en rapport avec le poste de travail' ),
    ( 'Justificatif de la liste de matériel nécessaire' );

-- --------------------------------------------------------------------------------------------------------
--  ....Table liée Acqmatprof avec ses pièces
-- --------------------------------------------------------------------------------------------------------

CREATE TABLE acqsmatsprofs_piecesacqsmatsprofs (
    id                          SERIAL NOT NULL PRIMARY KEY,
    acqmatprof_id                  INTEGER NOT NULL REFERENCES acqsmatsprofs(id),
    pieceacqmatprof_id             INTEGER NOT NULL REFERENCES piecesacqsmatsprofs(id)
);
CREATE INDEX acqsmatsprofs_piecesacqsmatsprofs_acqmatprof_id_idx ON acqsmatsprofs_piecesacqsmatsprofs (acqmatprof_id);
CREATE INDEX acqsmatsprofs_piecesacqsmatsprofs_pieceacqmatprof_id_idx ON acqsmatsprofs_piecesacqsmatsprofs (pieceacqmatprof_id);


-- --------------------------------------------------------------------------------------------------------
-- --------------------------------------------------------------------------------------------------------
--  ....Données nécessaire pour la table Acqmatprof
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE locsvehicinsert (
    id                          SERIAL NOT NULL PRIMARY KEY,
    apre_id                     INTEGER NOT NULL REFERENCES apres(id),
    societelocation             VARCHAR(250),
    dureelocation               VARCHAR(250),
    montantaide                 DECIMAL (10, 2)
);
CREATE INDEX locsvehicinsert_apre_id_idx ON locsvehicinsert (apre_id);
-- --------------------------------------------------------------------------------------------------------
--  ....Table des pièces liées à Acqmatprof
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE pieceslocsvehicinsert (
    id                          SERIAL NOT NULL PRIMARY KEY,
    libelle                     TEXT NOT NULL
);

INSERT INTO pieceslocsvehicinsert ( libelle ) VALUES
    ( 'Facture' ),
    ( 'Photocopie du permis de conduire B' );

-- --------------------------------------------------------------------------------------------------------
--  ....Table liée Acqmatprof avec ses pièces
-- --------------------------------------------------------------------------------------------------------

CREATE TABLE locsvehicinsert_pieceslocsvehicinsert (
    id                          SERIAL NOT NULL PRIMARY KEY,
    locvehicinsert_id                  INTEGER NOT NULL REFERENCES locsvehicinsert(id),
    piecelocvehicinsert_id             INTEGER NOT NULL REFERENCES pieceslocsvehicinsert(id)
);
CREATE INDEX locsvehicinsert_pieceslocsvehicinsert_locvehicinsert_id_idx ON locsvehicinsert_pieceslocsvehicinsert (locvehicinsert_id);
CREATE INDEX locsvehicinsert_pieceslocsvehicinsert_piecelocvehicinsert_id_idx ON locsvehicinsert_pieceslocsvehicinsert (piecelocvehicinsert_id);

--------------- Ajout du 13/11/2009 à 14h00 ------------------
ALTER TABLE referentsapre ADD COLUMN spe type_no DEFAULT NULL;

CREATE TYPE type_decisioncomite AS ENUM ( 'REF', 'ACC', 'AJ' );


CREATE TYPE type_etatdossierapre AS ENUM ( 'COM', 'INC' );
ALTER TABLE apres ADD COLUMN etatdossierapre type_etatdossierapre DEFAULT NULL;

-- --------------------------------------------------------------------------------------------------------
--  ....Table liée Relancesapres liée à l'APRE
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE relancesapres (
    id                          SERIAL NOT NULL PRIMARY KEY,
    apre_id                     INTEGER NOT NULL REFERENCES apres(id),
    daterelance                 DATE,
    etatdossierapre             type_etatdossierapre DEFAULT NULL,
    commentairerelance          TEXT
);
CREATE INDEX relancesapres_apre_id_idx ON relancesapres (apre_id);

-- --------------------------------------------------------------------------------------------------------
--  ....Table liée Comitesapres liée à l'APRE
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE comitesapres (
    id                          SERIAL NOT NULL PRIMARY KEY,
    datecomite                  DATE,
    heurecomite                 TIME,
    lieucomite                  TEXT,
    intitulecomite              TEXT,
    observationcomite           TEXT
);

-- --------------------------------------------------------------------------------------------------------
--  ....Table liée Acqmatprof avec ses pièces
-- --------------------------------------------------------------------------------------------------------

CREATE TABLE apres_comitesapres (
    id                          SERIAL NOT NULL PRIMARY KEY,
    apre_id                     INTEGER NOT NULL REFERENCES apres(id),
    comiteapre_id         INTEGER NOT NULL REFERENCES comitesapres(id)
);
CREATE INDEX apres_comitesapres_apre_id_idx ON apres_comitesapres (apre_id);
CREATE INDEX apres_comitesapres_comiteapre_id_idx ON apres_comitesapres (comiteapre_id);
-- --------------------------------------------------------------------------------------------------------
--  ....Table liée Participantscomitesexamen liée au Comite d''examen
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE participantscomites (
    id                          SERIAL NOT NULL PRIMARY KEY,
    qual                        VARCHAR(3),
    nom                         VARCHAR(50),
    prenom                      VARCHAR(50),
    fonction                    TEXT,
    organisme                   TEXT,
    numtel                      VARCHAR(10),
    mail                        VARCHAR(78)
);
-- --------------------------------------------------------------------------------------------------------
--  ....Table liée Comites avec ses Participants
-- --------------------------------------------------------------------------------------------------------

CREATE TABLE comitesapres_participantscomites (
    id                          SERIAL NOT NULL PRIMARY KEY,
    comiteapre_id                  INTEGER NOT NULL REFERENCES comitesapres(id),
    participantcomite_id             INTEGER NOT NULL REFERENCES participantscomites(id)
);
CREATE INDEX comitesapres_participantscomites_comiteapre_id_idx ON comitesapres_participantscomites (comiteapre_id);
CREATE INDEX comitesapres_participantscomites_participantcomite_id_idx ON comitesapres_participantscomites (participantcomite_id);

-- --------------------------------------------------------------------------------------------------------
--------------- Ajout du 26/11/2009 à 10h02 ------------------
--------------------------------------------------------------------------------------------------------
ALTER TABLE apres ADD COLUMN eligibiliteapre type_no;

ALTER TABLE apres ADD COLUMN mtforfait DECIMAL (10, 2) DEFAULT NULL;
ALTER TABLE apres ADD COLUMN nenfants INTEGER DEFAULT NULL;

-- --------------------------------------------------------------------------------------------------------
--------------- Ajout du 27/11/2009 à 10h02 ------------------
--------------------------------------------------------------------------------------------------------
ALTER TABLE apres_comitesapres ADD COLUMN montantattribue DECIMAL (10, 2) DEFAULT NULL;
ALTER TABLE apres_comitesapres ADD COLUMN observationcomite TEXT;
ALTER TABLE apres_comitesapres ADD COLUMN decisioncomite type_decisioncomite;

--------------- Ajout du 27/11/2009 à 16h46 ------------------
ALTER TABLE apres_comitesapres ADD COLUMN recoursapre type_no;
ALTER TABLE apres_comitesapres ADD COLUMN decisionrecours type_decisioncomite;
ALTER TABLE apres_comitesapres ADD COLUMN observationrecours TEXT;
ALTER TABLE apres_comitesapres ADD COLUMN daterecours DATE;


-- -------------------------- Ajout du 30/11/2009 à 16h46 ------------------
---------------------------------------------------------------------------------------------
-- --------------------------------------------------------------------------------------------------------
--  ....Données nécessaire pour la table Formqualif
-- --------------------------------------------------------------------------------------------------------

CREATE TABLE formspermsfimo (
    id                          SERIAL NOT NULL PRIMARY KEY,
    apre_id                     INTEGER NOT NULL REFERENCES apres(id),
    intituleform                VARCHAR(100) NOT NULL,
    organismeform               VARCHAR(100) NOT NULL,
    ddform                      DATE,
    dfform                      DATE,
    dureeform                   INT4,
    modevalidation              VARCHAR(30),
    coutform                    DECIMAL(10,2),
    cofinanceurs                VARCHAR(30),
    montantaide                 DECIMAL(10,2)
);
CREATE INDEX formspermsfimo_apre_id_idx ON formspermsfimo (apre_id);
-- --------------------------------------------------------------------------------------------------------
--  ....Table des pièces liées à formpermfimo
-- --------------------------------------------------------------------------------------------------------
CREATE TABLE piecesformspermsfimo (
    id                          SERIAL NOT NULL PRIMARY KEY,
    libelle                     TEXT NOT NULL
);

INSERT INTO piecesformspermsfimo ( libelle ) VALUES
    ( 'Photocopie du permis de conduire' ),
    ( 'Devis nominatif détaillé précisant l''intitulé de la formation, son lieu, dates prévisionnelles de début et fin d''action, durée en heure jours et mois, contenu (heures et modules), l''organisation de la formation, le coût global ainsi que la participation éventuelle du stagiaire.' ),
    ( 'Evaluation des connaissances et compétences professionnelles (ECCP)' ),
    ( 'Facture ou devis' );

-- --------------------------------------------------------------------------------------------------------
--  ....Table liée Formqualif avec ses pièces
-- --------------------------------------------------------------------------------------------------------

CREATE TABLE formspermsfimo_piecesformspermsfimo (
    id                          SERIAL NOT NULL PRIMARY KEY,
    formpermfimo_id               INTEGER NOT NULL REFERENCES formspermsfimo(id),
    pieceformpermfimo_id          INTEGER NOT NULL REFERENCES piecesformspermsfimo(id)
);
CREATE INDEX formspermsfimo_piecesformspermsfimo_formpermfimo_id_idx ON formspermsfimo_piecesformspermsfimo (formpermfimo_id);
CREATE INDEX formspermsfimo_piecesformspermsfimo_pieceformpermfimo_id_idx ON formspermsfimo_piecesformspermsfimo (pieceformpermfimo_id);


-- -------------------------- Ajout du 01/12/2009 à 9h10 ------------------
ALTER TABLE apres ADD COLUMN secteuractivite CHAR(1);
ALTER TABLE apres DROP COLUMN nenfants;
ALTER TABLE apres ADD COLUMN nbenf12 INTEGER;

-- ALTER TABLE apres DROP TYPE type_activitebeneficiaire CASCADE;
-- CREATE TYPE type_activitebeneficiaire AS ENUM ( 'E', 'F', 'C', 'P' );

-- -------------------------- Ajout du 02/12/2009 à 17h10 ------------------
ALTER TABLE apres_comitesapres DROP COLUMN decisionrecours;
ALTER TABLE apres_comitesapres ADD COLUMN comite_pcd_id INTEGER NULL;

-- -------------------------- Ajout du 04/12/2009 à 15h40 ------------------
CREATE TYPE type_presence AS ENUM ( 'PRE', 'ABS', 'EXC' );
ALTER TABLE comitesapres_participantscomites ADD COLUMN presence type_presence;

-- -------------------------- Ajout du 08/12/2009 à 9h10 ------------------
CREATE TYPE type_statutapre AS ENUM ( 'C', 'F' );
ALTER TABLE apres ADD COLUMN statutapre type_statutapre DEFAULT 'F';

-- -------------------------- Ajout du 08/12/2009 à 15h10 ------------------

CREATE TABLE parametresfinanciers (
    id					SERIAL NOT NULL PRIMARY KEY,
	entitefi			VARCHAR(250) NOT NULL,
	engagement			VARCHAR(250),
	tiers				VARCHAR(250) NOT NULL,
	codecdr				VARCHAR(250) NOT NULL,
	libellecdr			VARCHAR(250) NOT NULL,
	natureanalytique	VARCHAR(250) NOT NULL,
	programme			VARCHAR(250) NOT NULL,
	lib_programme		VARCHAR(250) NOT NULL,
	apreforfait			VARCHAR(250) NOT NULL,
	aprecomplem			VARCHAR(250),
	natureimput			VARCHAR(250) NOT NULL
);

CREATE TABLE budgetsapres (
    id					SERIAL NOT NULL PRIMARY KEY,
	exercicebudgetai	INTEGER NOT NULL,
	montantattretat		NUMERIC(10,2) NOT NULL,
	ddexecutionbudge	DATE NOT NULL,
	dfexecutionbudge	DATE NOT NULL
);

CREATE TYPE type_typeapre AS ENUM ( 'forfaitaire', 'complementaire' ); -- <-- type_statutapre ?

CREATE TABLE etatsliquidatifs (
    id					SERIAL NOT NULL PRIMARY KEY,
	budgetapre_id       INTEGER NOT NULL REFERENCES budgetsapres(id),
	entitefi			VARCHAR(250) NOT NULL,
	engagement			VARCHAR(250),
	tiers				VARCHAR(250) NOT NULL,
	codecdr				VARCHAR(250) NOT NULL,
	libellecdr			VARCHAR(250) NOT NULL, -- <-- cdr dans l'affichage
	natureanalytique	VARCHAR(250) NOT NULL,
	programme			VARCHAR(250) NOT NULL,
	lib_programme		VARCHAR(250) NOT NULL,
	apreforfait			VARCHAR(250) NOT NULL,
	aprecomplem			VARCHAR(250),
	natureimput			VARCHAR(250) NOT NULL,
	typeapre			type_typeapre NOT NULL,
	operation			VARCHAR(250) NOT NULL, -- <-- Le code apreforfait ou aprecomplem
	objet				VARCHAR(250) NOT NULL,
	datecloture			DATE,
	montanttotalapre	NUMERIC(10,2)
);
CREATE INDEX etatsliquidatifs_budgetapre_id_idx ON etatsliquidatifs(budgetapre_id);

CREATE TABLE apres_etatsliquidatifs (
    id                  SERIAL NOT NULL PRIMARY KEY,
    apre_id             INTEGER NOT NULL REFERENCES apres(id),
    etatliquidatif_id	INTEGER NOT NULL REFERENCES etatsliquidatifs(id)
);
CREATE INDEX apres_etatsliquidatifs_apre_id_idx ON apres_etatsliquidatifs (apre_id);
CREATE INDEX apres_etatsliquidatifs_etatliquidatif_id_idx ON apres_etatsliquidatifs (etatliquidatif_id);

--------------- Ajout du 09/12/2009 à 18h12 ------------------
CREATE TABLE domiciliationsbancaires (
   id                    SERIAL NOT NULL PRIMARY KEY,
   codebanque            CHAR(5),
   codeagence            CHAR(5),
   libelledomiciliation  VARCHAR(50)
);

CREATE INDEX domiciliationsbancaires_codebanque_idx ON domiciliationsbancaires (codebanque);
CREATE INDEX domiciliationsbancaires_codeagence_idx ON domiciliationsbancaires (codeagence);

-- -------------------------- Ajout du 10/12/2009 à 11h30 ------------------

CREATE TABLE tiersprestatairesapres (
    id                      SERIAL NOT NULL PRIMARY KEY,
    nomtiers                VARCHAR(50),
    siret                   CHAR(14),
    numvoie                 VARCHAR(6),
    typevoie                VARCHAR(4),
    nomvoie                 VARCHAR(25),
    compladr                VARCHAR(40),
    codepos                 CHAR(5),
    ville                   VARCHAR(30),
    canton                  VARCHAR(20),
    numtel                  VARCHAR(14),
    adrelec                 VARCHAR(78),
    nomtiturib              VARCHAR(24),
    etaban                  CHAR(5),
    guiban                  CHAR(5),
    numcomptban             CHAR(11),
    clerib                  smallint
);

ALTER TABLE formsqualifs ADD COLUMN tiersprestataireapre_id INTEGER REFERENCES tiersprestatairesapres(id);
ALTER TABLE formspermsfimo ADD COLUMN tiersprestataireapre_id INTEGER REFERENCES tiersprestatairesapres(id);
ALTER TABLE actsprofs ADD COLUMN tiersprestataireapre_id INTEGER REFERENCES tiersprestatairesapres(id);
ALTER TABLE permisb ADD COLUMN tiersprestataireapre_id INTEGER REFERENCES tiersprestatairesapres(id);

ALTER TABLE formsqualifs DROP COLUMN organismeform;
ALTER TABLE formspermsfimo DROP COLUMN organismeform;
ALTER TABLE actsprofs DROP COLUMN nomemployeur;
ALTER TABLE actsprofs DROP COLUMN adresseemployeur;
ALTER TABLE permisb DROP COLUMN nomautoecole;
ALTER TABLE permisb DROP COLUMN adresseautoecole;
