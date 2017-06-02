/*
    TODO:
        - NULL, pas NULL -> voir xls
        - voir si des INT peuvent passer en TINYINT
        - nataccosocfam et diflog: faire les tables associées
        - vérifier types pour dossiers et personnes
*/

CREATE TABLE groups (
    id          SERIAL NOT NULL PRIMARY KEY,
    name        VARCHAR(50),
    parent_id   INT DEFAULT NULL
);
-- -----------------------------------------------------------------------------
--       table : servicesinstructeurs 
-- -----------------------------------------------------------------------------
CREATE TABLE servicesinstructeurs (
    id                  SERIAL NOT NULL PRIMARY KEY,
    lib_service     VARCHAR(30),
    num_rue     VARCHAR(6),
    nom_rue     VARCHAR(25),
    complement_adr  VARCHAR(38),
    code_insee      CHAR(5),
    code_postal     CHAR(5),
    ville       VARCHAR(26)
);


CREATE TABLE users (
    id                      SERIAL NOT NULL PRIMARY KEY,
    group_id                INTEGER NOT NULL REFERENCES groups(id)  DEFAULT 0,
    serviceinstructeur_id   INTEGER NOT NULL REFERENCES servicesinstructeurs(id),
    username                VARCHAR(50) NOT NULL,
    password                VARCHAR(50) NOT NULL,
    nom                     VARCHAR(50),
    prenom                  VARCHAR(50),
    date_naissance          DATE,
    date_deb_hab            DATE,
    date_fin_hab            DATE
);

CREATE TABLE acos (
  id  SERIAL NOT NULL,
  parent_id  INT NOT NULL,
  model VARCHAR(255) DEFAULT '',
  foreign_key INT DEFAULT NULL,
  alias VARCHAR(255) DEFAULT '',
  lft INT  DEFAULT NULL,
  rght INT DEFAULT NULL,
  PRIMARY KEY  (id)
);

CREATE TABLE aros_acos (
  id  SERIAL NOT NULL,
  aro_id INT NOT NULL,
  aco_id INT NOT NULL,
  _create CHAR(2) NOT NULL DEFAULT 0,
  _read CHAR(2) NOT NULL DEFAULT 0,
  _update CHAR(2) NOT NULL DEFAULT 0,
  _delete CHAR(2) NOT NULL DEFAULT 0,
 PRIMARY KEY(id)
);

CREATE TABLE aros (
  id  SERIAL NOT NULL,
  parent_id INT DEFAULT NULL,
  model VARCHAR(255) DEFAULT '',
  foreign_key INT DEFAULT NULL,
  alias VARCHAR(255) DEFAULT '',
  lft INT DEFAULT NULL,
  rght INT DEFAULT NULL,
  PRIMARY KEY(id)
);

CREATE TABLE zonesgeographiques (
    id                  SERIAL NOT NULL PRIMARY KEY,
    codeinsee           CHAR(5) NOT NULL,
    libelle             VARCHAR(50) NOT NULL
);

CREATE TABLE users_zonesgeographiques (
    user_id             INT NOT NULL REFERENCES users (id),
    zonegeographique_id INT NOT NULL REFERENCES zonesgeographiques (id),
    PRIMARY KEY( user_id, zonegeographique_id )
);


CREATE TABLE dossiers_rsa (
    id                      SERIAL NOT NULL PRIMARY KEY,
    numdemrsa               VARCHAR(11),
    dtdemrsa                DATE,
    dtdemrmi                DATE,
    numdepinsrmi            CHAR(3),
    typeinsrmi              CHAR(1),
    numcominsrmi            INTEGER,
    numagrinsrmi            CHAR(2),
    numdosinsrmi            INTEGER,
    numcli                  INTEGER,
    numorg                  CHAR(3),
    fonorg                  CHAR(3),
    matricule               CHAR(15),
    statudemrsa             CHAR(1),
    typeparte               CHAR(4),
    ideparte                CHAR(3),
    fonorgcedmut            CHAR(3),
    numorgcedmut            CHAR(3),
    matriculeorgcedmut      CHAR(15),
    ddarrmut                DATE,
    codeposanchab           CHAR(5),
    fonorgprenmut           CHAR(3),
    numorgprenmut           CHAR(3),
    dddepamut               DATE,
    details_droits_rsa_id   INTEGER,
    avis_pcg_id             INTEGER,
    organisme_id            INTEGER,
    acompte_rsa_id          INTEGER
);


CREATE TABLE foyers (
    id                  SERIAL NOT NULL PRIMARY KEY,
    dossier_rsa_id      INTEGER NOT NULL REFERENCES dossiers_rsa(id),
    sitfam              CHAR(3),
    ddsitfam            DATE,
    typeocclog          CHAR(3),
    mtvallocterr        NUMERIC(9,2),
    mtvalloclog         NUMERIC(9,2),
    contefichliairsa    TEXT
);

CREATE TABLE adresses (
    id          SERIAL NOT NULL PRIMARY KEY,
    numvoie     VARCHAR(6),
    typevoie    VARCHAR(4),
    nomvoie     VARCHAR(25),
    complideadr VARCHAR(38),
    compladr    VARCHAR(26),
    lieudist    VARCHAR(32),
    numcomrat   CHAR(5),
    numcomptt   CHAR(5),
    codepos     CHAR(5),
    locaadr     VARCHAR(26),
    pays        VARCHAR(3),
    canton      VARCHAR(20)
);

CREATE TABLE adresses_foyers (
    id          SERIAL NOT NULL PRIMARY KEY,
    adresse_id  INTEGER NOT NULL REFERENCES adresses(id),
    foyer_id    INTEGER NOT NULL REFERENCES foyers(id),
    rgadr       CHAR(2),
    dtemm       DATE,
    typeadr     CHAR(1)
);

CREATE TABLE paiementsfoyers (
    id                  SERIAL NOT NULL PRIMARY KEY,
    foyer_id            INTEGER NOT NULL REFERENCES foyers(id),
    topverstie          BOOLEAN,
    modepai             CHAR(2),
    topribconj          BOOLEAN,
    titurib             CHAR(3),
    nomprenomtiturib    VARCHAR(24),
    etaban              CHAR(5),
    guiban              CHAR(5),
    numcomptban         CHAR(11),
    clerib              SMALLINT,
    comban              VARCHAR(24)
);

CREATE TABLE personnes (
    id                      SERIAL NOT NULL PRIMARY KEY,
    foyer_id                INTEGER NOT NULL REFERENCES foyers(id),
    qual                    VARCHAR(3),
    nom                     VARCHAR(20),
    prenom                  VARCHAR(15),
    nomnai                  VARCHAR(20),
    prenom2                 VARCHAR(15),
    prenom3                 VARCHAR(15),
    nomcomnai               VARCHAR(26),
    dtnai                   DATE,
    rgnai                   INTEGER,
    typedtnai               CHAR(1),
    nir                     CHAR(15),
    topvalec                BOOLEAN,
    sexe                    CHAR(1),
    nati                    CHAR(1),
    dtnati                  DATE,
    pieecpres               CHAR(1),
    natprest                CHAR(3),
    rolepers                CHAR(3),
    topchapers              BOOLEAN, -- FIXME: pas dans l'édition de personne ?
    toppersdrodevorsa       BOOLEAN,
    idassedic               VARCHAR(8)
);

CREATE TABLE modes_contact (
    id              SERIAL NOT NULL PRIMARY KEY,
    foyer_id        INTEGER NOT NULL REFERENCES foyers(id),
    numtel          VARCHAR(11),
    numposte        INTEGER,
    nattel          CHAR(1),
    matetel         CHAR(3),
    autorutitel     CHAR(1),
    adrelec         VARCHAR(78),
    autorutiadrelec CHAR(1)
);

CREATE TABLE titres_sejour (
    id              SERIAL NOT NULL PRIMARY KEY,
    personne_id     INTEGER NOT NULL REFERENCES personnes(id),
    dtentfra        DATE,
    nattitsej       CHAR(3),
    menttitsej      CHAR(2),
    ddtitsej        DATE,
    dftitsej        DATE,
    numtitsej       VARCHAR(10),
    numduptitsej    INTEGER
);

CREATE TABLE rattachements (
    personne_id INTEGER NOT NULL REFERENCES personnes(id),
    rattache_id INTEGER NOT NULL REFERENCES personnes(id),
    typepar     CHAR(2),
    PRIMARY KEY( personne_id, rattache_id )
);

/* Suivis d'instruction */
CREATE TABLE suivisinstruction (
    id                      SERIAL NOT NULL PRIMARY KEY,
    dossier_rsa_id          INTEGER NOT NULL REFERENCES dossiers_rsa(id),
    etatirsa                CHAR(2),
    date_etat_instruction   DATE,
    nomins                  VARCHAR(28),
    prenomins               VARCHAR(32),
    numdepins               CHAR(3),
    typeserins              CHAR(1),
    numcomins               CHAR(3),
    numagrins               INTEGER
);

/* Situations du dossier rsa */
CREATE TABLE situationsdossiersrsa (
    id                          SERIAL NOT NULL PRIMARY KEY,
    dossier_rsa_id              INTEGER NOT NULL REFERENCES dossiers_rsa(id),
    etatdosrsa                  CHAR(1),
    dtrefursa                   DATE,
    moticlorsa                  CHAR(3),
    dtclorsa                    DATE
);

CREATE TABLE suspensionsversements (
    id                          SERIAL NOT NULL PRIMARY KEY,
    situationdossierrsa_id      INTEGER NOT NULL REFERENCES situationsdossiersrsa(id),
    motisusversrsa              CHAR(2),
    ddsusversrsa                DATE
);

CREATE TABLE suspensionsdroits (
    id                          SERIAL NOT NULL PRIMARY KEY,
    situationdossierrsa_id      INTEGER NOT NULL REFERENCES situationsdossiersrsa(id),
    motisusdrorsa               CHAR(2),
    ddsusdrorsa                 DATE
);

/* Avis PCG droit rsa */
CREATE TABLE avispcgdroitrsa (
    id                          SERIAL NOT NULL PRIMARY KEY,
    dossier_rsa_id              INTEGER NOT NULL REFERENCES dossiers_rsa(id),
    avisdestpairsa              CHAR(1),
    dtavisdestpairsa            DATE,
    nomtie                      VARCHAR(64),
    typeperstie                 CHAR(1)
);

CREATE TABLE reducsrsa (
    id                          SERIAL NOT NULL PRIMARY KEY,
    avispcgdroitrsa_id          INTEGER NOT NULL REFERENCES avispcgdroitrsa(id),
    mtredrsa                    NUMERIC(9,2),
    ddredrsa                    DATE,
    dfredrsa                    DATE
);

CREATE TABLE condsadmins (
    id                          SERIAL NOT NULL PRIMARY KEY,
    avispcgdroitrsa_id          INTEGER NOT NULL REFERENCES avispcgdroitrsa(id),
    aviscondadmrsa              CHAR(1),
    moticondadmrsa              CHAR(2),
    comm1condadmrsa             VARCHAR(60),
    comm2condadmrsa             VARCHAR(60),
    dteffaviscondadmrsa         DATE
);

CREATE TABLE activites (
    id              SERIAL NOT NULL PRIMARY KEY,
    personne_id     INTEGER NOT NULL REFERENCES personnes(id),
    reg             CHAR(2),
    act             CHAR(3),
    paysact         CHAR(3),
    ddact           DATE,
    dfact           DATE,
    natcontrtra     CHAR(3),
    topcondadmeti   BOOLEAN,
    hauremuscmic    CHAR(1) -- FIXME : hauremusmic cf flux instruction
);

CREATE TABLE dspfs (
    id                  SERIAL NOT NULL PRIMARY KEY,
    foyer_id            INTEGER NOT NULL REFERENCES foyers(id),
    motidemrsa          CHAR(4),
    accosocfam          BOOLEAN,
    libautraccosocfam   VARCHAR(100),
    libcooraccosocfam   VARCHAR(250),
    natlog              CHAR(4),
    libautrdiflog       VARCHAR(100),
    demarlog            CHAR(4)
);

-- -----------------------------------------------------------------------------
--       table : nataccosocfams
-- -----------------------------------------------------------------------------
CREATE TABLE nataccosocfams (
    id          SERIAL NOT NULL PRIMARY KEY,
    code    CHAR(4),
    name    VARCHAR(100) -- FIXME
);

CREATE TABLE dspfs_nataccosocfams (
    nataccosocfam_id        INTEGER NOT NULL REFERENCES nataccosocfams(id),
    dspf_id                 INTEGER NOT NULL REFERENCES dspfs(id)
);

-- -----------------------------------------------------------------------------
--       table : diflogs
-- -----------------------------------------------------------------------------
CREATE TABLE diflogs (
    id      SERIAL NOT NULL PRIMARY KEY,
    code        CHAR(4),
    name        VARCHAR(100) -- FIXME
);

CREATE TABLE dspfs_diflogs (
    diflog_id       INTEGER NOT NULL REFERENCES diflogs(id),
    dspf_id         INTEGER NOT NULL REFERENCES dspfs(id)
);

/* Données socio-professionnelles */

CREATE TABLE dspps (
    id                  SERIAL NOT NULL PRIMARY KEY,
    personne_id         INTEGER NOT NULL REFERENCES personnes(id),
    drorsarmiant        BOOLEAN,
    drorsarmianta2      BOOLEAN,
    couvsoc             BOOLEAN,
    libautrdifsoc       VARCHAR(100),
    elopersdifdisp      BOOLEAN,
    obstemploidifdisp   BOOLEAN,
    soutdemarsoc        BOOLEAN,
    libautraccosocindi  VARCHAR(100),
    libcooraccosocindi  VARCHAR(250),
    annderdipobt        DATE,
    rappemploiquali     BOOLEAN,
    rappemploiform      BOOLEAN,
    libautrqualipro     VARCHAR(100),
    permicondub         BOOLEAN,
    libautrpermicondu   VARCHAR(100),
    libcompeextrapro    VARCHAR(100),
    persisogrorechemploi    BOOLEAN,
   -- accoemploi          CHAR(4),
    libcooraccoemploi   VARCHAR(100),
    hispro              CHAR(4),
    libderact           VARCHAR(100),
    libsecactderact     VARCHAR(100),
    dfderact            DATE,
    domideract          BOOLEAN,
    libactdomi          VARCHAR(100),
    libsecactdomi       VARCHAR(100),
    duractdomi          CHAR(4),
    libemploirech       VARCHAR(100),
    libsecactrech       VARCHAR(100),
    creareprisentrrech  BOOLEAN,
    moyloco             BOOLEAN
);

-- -----------------------------------------------------------------------------
--       table : nivetus --nouvelle table à ajouter pour le formulaire des DSP Personne car il y a plusieurs niveaux d'étude possibles
-- -----------------------------------------------------------------------------
-- CREATE TABLE nivetus (
--    id        SERIAL NOT NULL PRIMARY KEY,
--    nivetu    CHAR(4)
--);

CREATE TABLE accoemplois (
    id      SERIAL NOT NULL PRIMARY KEY,
    code    CHAR(4),
    name    VARCHAR(100)
);


CREATE TABLE dspps_accoemplois (
    accoemploi_id   INTEGER NOT NULL REFERENCES accoemplois(id),
    dspp_id     INTEGER NOT NULL REFERENCES dspps(id)
);

CREATE TABLE nivetus (
    id                         SERIAL NOT NULL PRIMARY KEY,
    code                       CHAR(4),
    name                       VARCHAR(100) -- FIXME
);

CREATE TABLE dspps_nivetus (
    nivetu_id   INTEGER NOT NULL REFERENCES nivetus(id),
    dspp_id     INTEGER NOT NULL REFERENCES dspps(id)
);

-- -----------------------------------------------------------------------------
--       table : difsocs
-- -----------------------------------------------------------------------------
CREATE TABLE difsocs (
    id      SERIAL NOT NULL PRIMARY KEY,
    code    CHAR(4),
    name    VARCHAR(100)
);


CREATE TABLE dspps_difsocs (
    difsoc_id   INTEGER NOT NULL REFERENCES difsocs(id),
    dspp_id     INTEGER NOT NULL REFERENCES dspps(id)
);

-- -----------------------------------------------------------------------------
--       table : nataccosocindis
-- -----------------------------------------------------------------------------
CREATE TABLE nataccosocindis (
    id      SERIAL NOT NULL PRIMARY KEY,
    code    CHAR(4),
    name    VARCHAR(100)
);

CREATE TABLE dspps_nataccosocindis (
    nataccosocindi_id   INTEGER NOT NULL REFERENCES nataccosocindis(id),
    dspp_id         INTEGER NOT NULL REFERENCES dspps(id)
);

-- -----------------------------------------------------------------------------
--       table : difdisps
-- -----------------------------------------------------------------------------
CREATE TABLE difdisps (
    id          SERIAL NOT NULL PRIMARY KEY,
    code        CHAR(4),
    name        VARCHAR(100)
);

CREATE TABLE dspps_difdisps (
    difdisp_id  INTEGER NOT NULL REFERENCES difdisps(id),
    dspp_id     INTEGER NOT NULL REFERENCES dspps(id)
);

-- -----------------------------------------------------------------------------
--       table : natmobs
-- -----------------------------------------------------------------------------
CREATE TABLE natmobs (
    id          SERIAL NOT NULL PRIMARY KEY,
    code        CHAR(4),
    name        VARCHAR(100)
);

CREATE TABLE dspps_natmobs (
    natmob_id   INTEGER NOT NULL REFERENCES natmobs(id),
    dspp_id     INTEGER NOT NULL REFERENCES dspps(id)
);

-- -----------------------------------------------------------------------------
--       table : typesorients
-- -----------------------------------------------------------------------------
CREATE TABLE typesorients (
    id                  SERIAL NOT NULL PRIMARY KEY,
    parentid            INTEGER,
    lib_type_orient     VARCHAR(30),
    modele_notif        VARCHAR(40)
);

-- -----------------------------------------------------------------------------
--       table : structuresreferentes
-- -----------------------------------------------------------------------------

create table structuresreferentes (
    id                      SERIAL NOT NULL PRIMARY KEY,
    typeorient_id           INTEGER NOT NULL REFERENCES typesorients(id),
    lib_struc               VARCHAR(32) NOT NULL,
    num_voie                VARCHAR(6) NOT NULL, 
    type_voie               VARCHAR(6) NOT NULL,
    nom_voie                VARCHAR(30) NOT NULL,
    code_postal             CHAR(5) NOT NULL,
    ville                   VARCHAR(45) NOT NULL,
    code_insee              CHAR(5)
);

CREATE TABLE structuresreferentes_zonesgeographiques (
    structurereferente_id               INT NOT NULL REFERENCES structuresreferentes (id),
    zonegeographique_id                 INT NOT NULL REFERENCES zonesgeographiques (id),
    PRIMARY KEY( structurereferente_id, zonegeographique_id )
);

-- -----------------------------------------------------------------------------
--       table : orientsstructs
-- -----------------------------------------------------------------------------
create table orientsstructs (
    id                              SERIAL NOT NULL PRIMARY KEY,
    personne_id                     INTEGER NOT NULL REFERENCES personnes(id),
    typeorient_id                   INTEGER REFERENCES typesorients(id),
    structurereferente_id           INTEGER REFERENCES structuresreferentes(id),
    propo_algo                      INTEGER  REFERENCES typesorients(id),
--     propo_cg                        INTEGER  REFERENCES typesorients(id),
    valid_cg                        BOOLEAN,
    date_propo                      DATE,
    date_valid                      DATE,
    statut_orient                   VARCHAR(15)
);

-- -----------------------------------------------------------------------------
--       table : referents
-- -----------------------------------------------------------------------------
create table referents (
    id                          SERIAL NOT NULL PRIMARY KEY,
    structurereferente_id       INTEGER NOT NULL REFERENCES structuresreferentes(id),
    nom                         VARCHAR(28),
    prenom                      VARCHAR(32),
    numero_poste                CHAR(4),
    email                       VARCHAR(78)
);

-- -----------------------------------------------------------------------------
--       table : typoscontrats
-- -----------------------------------------------------------------------------
CREATE TABLE typoscontrats (-- Ajout du 25052009
  id  SERIAL NOT NULL PRIMARY KEY,
  lib_typo VARCHAR(20)
);
-- -----------------------------------------------------------------------------
--       table : contratsinsertion
-- -----------------------------------------------------------------------------
create table contratsinsertion (
    id                              SERIAL NOT NULL PRIMARY KEY,
    personne_id                     INTEGER NOT NULL REFERENCES personnes(id),
    structurereferente_id           INTEGER NOT NULL REFERENCES structuresreferentes(id),
    typocontrat_id                  INTEGER NOT NULL REFERENCES typoscontrats(id), -- Ajout du 25052009
    dd_ci                           DATE,
    df_ci                           DATE,
    --  niv_etude                       VARCHAR(30),
    diplomes                        TEXT,
    form_compl                      VARCHAR(60),
    expr_prof                       TEXT,
    aut_expr_prof                   VARCHAR(120),
    -- type_ci                         CHAR(3) null  check (type_ci in ('pre', 'ren', 'red')),
    rg_ci                           INTEGER,
    actions_prev                    VARCHAR(120),
    obsta_renc                      VARCHAR(120),
    service_soutien                 VARCHAR(120),
    pers_charg_suivi                VARCHAR(50),
    objectifs_fixes                 TEXT,
    engag_object                    TEXT,
    sect_acti_emp                   VARCHAR(20),
    emp_occupe                      VARCHAR(30),
    duree_hebdo_emp                 VARCHAR(20),
    nat_cont_trav                   CHAR(3),
    duree_cdd                       VARCHAR(20),
    duree_engag                     INTEGER,
    nature_projet                   TEXT,
    observ_ci                       TEXT,
    decision_ci                     CHAR(1),
    datevalidation_ci               DATE
);


-- -----------------------------------------------------------------------------
--       table : actionsinsertion
-- -----------------------------------------------------------------------------

create table actionsinsertion (
    id                          SERIAL NOT NULL PRIMARY KEY,
    contratinsertion_id         INTEGER NOT NULL REFERENCES contratsinsertion(id),
    dd_action                   DATE,
    df_action                   DATE
);

-- -----------------------------------------------------------------------------
--       table : aidesdirectes
-- -----------------------------------------------------------------------------
create table aidesdirectes (
    id                          SERIAL NOT NULL PRIMARY KEY,
    actioninsertion_id          INTEGER NOT NULL REFERENCES actionsinsertion(id),
    lib_aide                    VARCHAR(32),
    typo_aide                   VARCHAR(32),
    date_aide                   DATE
);

-- -----------------------------------------------------------------------------
--       table : refsprestas
-- -----------------------------------------------------------------------------
create table refsprestas (
    id                          SERIAL NOT NULL PRIMARY KEY,
    nomrefpresta                    VARCHAR(28),
    prenomrefpresta                 VARCHAR(32),
    emailrefpresta                  VARCHAR(78),
    numero_posterefpresta           VARCHAR(4)
);

-- -----------------------------------------------------------------------------
--       table : prestsform
-- -----------------------------------------------------------------------------
create table prestsform (
    id                  SERIAL NOT NULL PRIMARY KEY,
    actioninsertion_id  INTEGER NOT NULL REFERENCES actionsinsertion(id),
    refpresta_id        INTEGER NOT NULL REFERENCES refsprestas(id),
    lib_presta          VARCHAR(32),
    date_presta         DATE
);


-- -----------------------------------------------------------------------------
--       table Action: pour les prestations et aides
-- -----------------------------------------------------------------------------

-- à quoi servent ces deux tables? je ne les ai pas dans mon MCD. Elles ont une utilité ou pas ?

create table typesactions (
        id                  SERIAL NOT NULL PRIMARY KEY,
        libelle             VARCHAR(250)
);

create table actions (
        id                  SERIAL NOT NULL PRIMARY KEY,
        typeaction_id       INTEGER NOT NULL REFERENCES typesactions(id),
        code                CHAR(2),
        libelle             VARCHAR(250)
);

-- -----------------------------------------------------------------------------
--       table : ressources
-- -----------------------------------------------------------------------------
create table ressources (
        id                                          SERIAL NOT NULL PRIMARY KEY,
        personne_id                                 INTEGER NOT NULL REFERENCES personnes(id),
        topressnul                                  BOOLEAN,
        mtpersressmenrsa                            NUMERIC(10,2),
        ddress                                      DATE,
        dfress                                      DATE 
);

-- -----------------------------------------------------------------------------
--       table : ressources_mensuelles
-- -----------------------------------------------------------------------------

create table ressourcesmensuelles (
        id                                          SERIAL NOT NULL PRIMARY KEY,
        ressource_id                                INTEGER NOT NULL REFERENCES ressources(id),
        moisress                                    DATE,
        nbheumentra                                 INTEGER,
        mtabaneu                                    NUMERIC(9,2)
);

CREATE TABLE ressources_ressourcesmensuelles (
    ressourcemensuelle_id       INTEGER NOT NULL REFERENCES ressourcesmensuelles(id),
    ressource_id        INTEGER NOT NULL REFERENCES ressources(id)
);
-- -----------------------------------------------------------------------------
--       table ressources_mensuelles
-- -----------------------------------------------------------------------------

create table detailsressourcesmensuelles (
        id                                          SERIAL NOT NULL PRIMARY KEY,
        ressourcemensuelle_id                       INTEGER NOT NULL REFERENCES ressourcesmensuelles(id),
        natress                                     CHAR(3),
        mtnatressmen                                NUMERIC(10,2),
        abaneu                                      CHAR(1),
        dfpercress                                  DATE,
        topprevsubsress                             BOOLEAN
);

create table ressourcesmensuelles_detailsressourcesmensuelles (
        detailressourcemensuelle_id                 INTEGER NOT NULL REFERENCES detailsressourcesmensuelles(id),
        ressourcemensuelle_id                       INTEGER NOT NULL REFERENCES ressourcesmensuelles(id)
);

-- -----------------------------------------------------------------------------
--       table : infosfinancieres (Volet Allocation)
-- -----------------------------------------------------------------------------
create table infosfinancieres (
        id                                          SERIAL NOT NULL PRIMARY KEY,
        dossier_rsa_id                              INTEGER NOT NULL REFERENCES dossiers_rsa(id),
        moismoucompta                               DATE,
        type_allocation                             VARCHAR(25),
        natpfcre                                    CHAR(3),
        rgcre                                       INTEGER,
        numintmoucompta                             INTEGER,
        typeopecompta                               CHAR(3),
        sensopecompta                               CHAR(2),
        mtmoucompta                                 NUMERIC(11,2),
        ddregu                                      DATE,
        dttraimoucompta                             DATE,
        heutraimoucompta                            DATE
);

-- -----------------------------------------------------------------------------
--       table : Identification Flux 
-- -----------------------------------------------------------------------------
create table identificationsflux (
        id                                          SERIAL NOT NULL PRIMARY KEY,
        applieme                                    CHAR(3),
        numversionapplieme                          CHAR(4),
        typeflux                                    CHAR(1),
        natflux                                     CHAR(1),
        dtcreaflux                                  DATE,
        heucreaflux                                 DATE,
        dtref                                       DATE
);

create table totalisationsacomptes (
        id                                          SERIAL NOT NULL PRIMARY KEY,
        identificationflux_id                       INTEGER NOT NULL REFERENCES identificationsflux(id),
        type_totalisation                           VARCHAR(30),
        mttotsoclrsa                    NUMERIC(12,2),
        mttotsoclmajorsa                            NUMERIC(12,2),
        mttotlocalrsa                               NUMERIC(12,2),
        mttotrsa                                    NUMERIC(12,2)
);


/* Details des droit rsa liés au Dossier rsa  */
CREATE TABLE detailsdroitsrsa ( ---- l.176 - 205 Beneficiaires
    id                          SERIAL NOT NULL PRIMARY KEY,
    dossier_rsa_id              INTEGER NOT NULL REFERENCES dossiers_rsa(id),
    topsansdomfixe              BOOLEAN,
    nbenfautcha                 INTEGER,
    oridemrsa                   CHAR(3),
    dtoridemrsa                 DATE,
    topfoydrodevorsa            BOOLEAN,
    ddelecal                    DATE,
    dfelecal                    DATE,
    mtrevminigararsa            NUMERIC(9,2),
    mtpentrsa                   NUMERIC(9,2),
    mtlocalrsa                  NUMERIC(9,2),
    mtrevgararsa                NUMERIC(9,2),
    mtpfrsa                     NUMERIC(9,2),
    mtalrsa                     NUMERIC(9,2),
    mtressmenrsa                NUMERIC(9,2),
    mtsanoblalimrsa             NUMERIC(9,2),
    mtredhosrsa                 NUMERIC(9,2),
    mtredcgrsa                  NUMERIC(9,2),
    mtcumintegrsa               NUMERIC(9,2),
    mtabaneursa                 NUMERIC(9,2),
    mttotdrorsa                 NUMERIC(9,2)
);

CREATE TABLE detailscalculsdroitsrsa (
    id                      SERIAL NOT NULL PRIMARY KEY,
    detaildroitrsa_id       INTEGER NOT NULL REFERENCES detailsdroitsrsa(id),
    natpf                   CHAR(3),
    sousnatpf               CHAR(5),
    ddnatdro                DATE,
    dfnatdro                DATE,
    mtrsavers               NUMERIC(9,2),
    dtderrsavers            DATE
);


/* Infos agricoles Liées à la Personne  */
CREATE TABLE infosagricoles ( --l.120 - 128 Instructions
    id                  SERIAL NOT NULL PRIMARY KEY,
    personne_id         INTEGER NOT NULL REFERENCES personnes(id),
    mtbenagri           NUMERIC(10,2),
    dtbenagri           DATE,
    regfisagri          CHAR(1)
);

CREATE TABLE aidesagricoles (
    id                  SERIAL NOT NULL PRIMARY KEY,
    infoagricole_id     INTEGER NOT NULL REFERENCES infosagricoles(id),
    annrefaideagri      CHAR(4),
    libnataideagri      VARCHAR(30),
    mtaideagri          NUMERIC(9,2)
);

/* Informations ETI Liées à la Personne */

CREATE TABLE informationseti ( --l.131 - 151 Instructions
    id                  SERIAL NOT NULL PRIMARY KEY,
    personne_id         INTEGER NOT NULL REFERENCES personnes(id),
    topcreaentre        BOOLEAN,
    topaccre            BOOLEAN,
    acteti              CHAR(1),
    topempl1ax          BOOLEAN,
    topstag1ax          BOOLEAN,
    topsansempl         BOOLEAN,
    ddchiaffaeti        DATE,
    dfchiaffaeti        DATE,
    mtchiaffaeti        NUMERIC(9,2),
    regfiseti           CHAR(1),
    topbeneti           BOOLEAN,
    regfisetia1         CHAR(1),
    mtbenetia1          NUMERIC(9,2),
    mtamoeti            NUMERIC(9,2),
    mtplusvalueti       NUMERIC(9,2),
    topevoreveti        BOOLEAN,
    libevoreveti        VARCHAR(30),
    topressevaeti       BOOLEAN
);

/* Grossesse liée à Personne */
CREATE TABLE grossesses ( --l.112 - 116 Beneficiaires
    id                  SERIAL NOT NULL PRIMARY KEY,
    personne_id         INTEGER NOT NULL REFERENCES personnes(id),
    ddgro               DATE,
    dfgro               DATE,
    dtdeclgro           DATE,
    natfingro           CHAR(1)    
);

/* NOUVELLES TABLES DU 21 MAI */
-- -----------------------------------------------------------------------------
--       table : evenements 
-- -----------------------------------------------------------------------------
CREATE TABLE evenements (
    id                  SERIAL NOT NULL PRIMARY KEY,
    dtliq               DATE,
    heuliq              DATE,
    fg              VARCHAR(30)
);

-- -----------------------------------------------------------------------------
--       table : foyers_evenements (relation entre foyers et evenements)
-- -----------------------------------------------------------------------------
create table foyers_evenements (
        foyer_id                 INTEGER NOT NULL REFERENCES foyers(id),
        evenement_id             INTEGER NOT NULL REFERENCES evenements(id)
);

-- -----------------------------------------------------------------------------
--       table : creances 
-- -----------------------------------------------------------------------------
CREATE TABLE creances (
    id                  SERIAL NOT NULL PRIMARY KEY,
    dtimplcre       DATE,
    natcre      CHAR(3),
    rgcre       CHAR(3),
    motiindu        CHAR(2),
    oriindu     CHAR(2),
    respindu        CHAR(2),
    ddregucre       DATE,
    dfregucre       DATE,
    dtdercredcretrans   DATE,
    mtsolreelcretrans   NUMERIC(9,2),
    mtinicre        NUMERIC(9,2)
);

-- -----------------------------------------------------------------------------
--       table : foyers_creances (relation entre foyers et creances)
-- -----------------------------------------------------------------------------
create table foyers_creances (
        foyer_id                INTEGER NOT NULL REFERENCES foyers(id),
        creance_id              INTEGER NOT NULL REFERENCES creances(id)
);

-- -----------------------------------------------------------------------------
--       table : dossierscaf 
-- -----------------------------------------------------------------------------
CREATE TABLE dossierscaf (
    id                  SERIAL NOT NULL PRIMARY KEY,
    personne_id         INTEGER NOT NULL REFERENCES personnes(id),
    ddratdos        DATE,
    dfratdos        DATE,
    toprespdos      BOOLEAN,
    numdemrsaprece  CHAR(11)
);

-- -----------------------------------------------------------------------------
--       table : creancesalimentaires 
-- -----------------------------------------------------------------------------
CREATE TABLE creancesalimentaires (
    id                  SERIAL NOT NULL PRIMARY KEY,
    etatcrealim     CHAR(2),
    ddcrealim       DATE,
    dfcrealim       DATE,
    orioblalim      CHAR(3),
    motidiscrealim  CHAR(3),
    commcrealim     VARCHAR(50),
    mtsancrealim    NUMERIC(9,2),
    topdemdisproccrealim BOOLEAN,
    engproccrealim CHAR(1),
    verspa CHAR(1),
    topjugpa BOOLEAN
);

-- -----------------------------------------------------------------------------
--       table : creancesalimentaires_personnes (relation entre creancesalimentaires et personnes)
-- -----------------------------------------------------------------------------
create table creancesalimentaires_personnes (
        personne_id                INTEGER NOT NULL REFERENCES personnes(id),
        creancealimentaire_id      INTEGER NOT NULL REFERENCES creancesalimentaires(id)
);

-- -----------------------------------------------------------------------------
--       table : allocationssoutienfamilial 
-- -----------------------------------------------------------------------------
CREATE TABLE allocationssoutienfamilial (
    id                  SERIAL NOT NULL PRIMARY KEY,
    personne_id         INTEGER NOT NULL REFERENCES personnes(id),
    sitasf      CHAR(2),
    parassoasf      CHAR(1),
    ddasf       DATE,
    dfasf       DATE
);

-- -----------------------------------------------------------------------------
--       table : avispcgpersonnes 
-- -----------------------------------------------------------------------------
CREATE TABLE avispcgpersonnes (
    id                      SERIAL NOT NULL PRIMARY KEY,
    personne_id             INTEGER NOT NULL REFERENCES personnes(id),
    avisevaressnonsal       CHAR(1),
    dtsouressnonsal     DATE,
    dtevaressnonsal     DATE,
    mtevalressnonsal        NUMERIC(9,2),
    excl            CHAR(1),
    ddexcl          DATE,
    dfexcl          DATE
);

-- -----------------------------------------------------------------------------
--       table : liberalites 
-- -----------------------------------------------------------------------------
CREATE TABLE liberalites (
    id                      SERIAL NOT NULL PRIMARY KEY,
    avispcgpersonne_id          INTEGER NOT NULL REFERENCES avispcgpersonnes(id),
    mtlibernondecl      NUMERIC(9,2),
    dtabsdeclliber      DATE
);


-- -----------------------------------------------------------------------------
--       table : derogations 
-- -----------------------------------------------------------------------------
CREATE TABLE derogations (
    id                  SERIAL NOT NULL PRIMARY KEY,
    avispcgpersonne_id  INTEGER NOT NULL REFERENCES avispcgpersonnes(id),
    typdero     CHAR(3),
    avisdero        CHAR(1),
    ddavisdero      DATE,
    dfavisdero      DATE
);