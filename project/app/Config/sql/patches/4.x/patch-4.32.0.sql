SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.32.0', CURRENT_TIMESTAMP);

-- Création de la table des DSP France travail
CREATE TABLE IF NOT EXISTS orientations_francetravail (
    id                                      SERIAL4 NOT NULL,
    personne_id                             INT4 NOT NULL,
    code_parcours                           VARCHAR,
    organisme                               VARCHAR,
    struct_libelle                          VARCHAR,
    struct_decision_libelle                 VARCHAR,
    statut                                  VARCHAR,
    etat                                    VARCHAR,
    date_entree_parcours                    DATE,
    date_modification                       TIMESTAMP,
    crit_origine_calcul                     VARCHAR,
    crit_situation_professionnelle          VARCHAR,
    crit_type_emploi                        VARCHAR,
    crit_niveau_etude                       VARCHAR,
    crit_capacite_a_travailler              BOOLEAN NOT NULL DEFAULT FALSE,
    crit_projet_pro                         VARCHAR,
    crit_contrainte_sante                   VARCHAR,
    crit_contrainte_logement                VARCHAR,
    crit_contrainte_mobilite                VARCHAR,
    crit_contrainte_familiale               VARCHAR,
    crit_contrainte_financiere              VARCHAR,
    crit_contrainte_numerique               VARCHAR,
    crit_contrainte_admin_jur               VARCHAR,
    crit_contrainte_francais_calcul         VARCHAR,
    crit_boe                                BOOLEAN NOT NULL DEFAULT FALSE,
    crit_baeeh                              BOOLEAN NOT NULL DEFAULT FALSE,
    crit_scolarite_etab_spec                BOOLEAN NOT NULL DEFAULT FALSE,
    crit_esat                               BOOLEAN NOT NULL DEFAULT FALSE,
    crit_boe_souhait_accompagnement         BOOLEAN NOT NULL DEFAULT FALSE,
    crit_msa_autonomie_recherche_emploi     INT,
    crit_msa_demarches_professionnelles     VARCHAR,
    decision_date_sortie_parcours           DATE,
    decision_motif_sortie_parcours          TEXT,
    decision_etat                           VARCHAR,
    decision_date                           DATE,
    decision_organisme                      VARCHAR,
    decision_motif_refus                    VARCHAR,
    decision_commentaire_refus              TEXT,
    decision_structure_libelle              VARCHAR,
    created                                 TIMESTAMP NOT NULL,
    modified                                TIMESTAMP NOT NULL,
    CONSTRAINT orientations_francetravail_pkey PRIMARY KEY (id),
    CONSTRAINT orientations_francetravail_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Ajout des champs pour les orientations envoyés à France Travail
ALTER TABLE orientsstructs
    ADD COLUMN IF NOT EXISTS is_envoye_francetravail BOOLEAN DEFAULT FALSE,
    ADD COLUMN IF NOT EXISTS date_envoi_francetravail TIMESTAMP;

-- Ajout de la table de rapport dans l'administration
CREATE TABLE IF NOT EXISTS administration.rapport_flux_francetravail(
    id SERIAL4              NOT NULL,
    date_debut              TIMESTAMP NOT NULL,
    date_fin                TIMESTAMP,
    nb_pers_prevus          INTEGER,
    nb_pers_traitees        INTEGER,
    nb_pers_non_traitees    INTEGER,
    liste_pers_traitee      TEXT,
    liste_pers_non_traite   TEXT,
    erreurs                 TEXT,
    created                 TIMESTAMP NOT NULL,
    modified                TIMESTAMP NOT NULL,
    CONSTRAINT rapport_flux_francetravail_pkey PRIMARY KEY (id)
);

-- Ajout de la configuration concernant les conditions de flux de France Travail
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
    SELECT
        'Module.Francetravail.Flux',
        '{"Situationdossierrsa.etatdosrsa":["2","3","4"],"Calculdroitrsa.toppersdrodevorsa":"1","NbJoursDerniereRecuperationDonnees":"30","NbJoursDerniereMAJOrientations":"30"}',
        'Paramétrage des données à utiliser pour le flux de France Travail. Pour ne pas prendre en compte un des paramètres, laisser vide.

Situationdossierrsa.etatdosrsa : liste des états dossiers RSA à prendre en compte (obligatoire)
Calculdroitrsa.toppersdrodevorsa : Prends les personnes SDD. Si peu importe le statut, laisser vide
NbJoursDerniereRecuperationDonnees: Nombre de jour depuis la dernières récupérations des données
NbJoursDerniereMAJOrientations: Nombre de jour depuis l''envoie de l''orientation à France Travail',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP
    WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Francetravail.Flux');

UPDATE configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable IN ('Module.Francetravail.Flux');

-- Ajout de la configuration des URL liés au flux de France Travail
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
    SELECT
        'Module.Francetravail.APIURL',
        '{"BaseURL":"https://api-r.ft-qvr.io","ListeScope":"api_rechercher-usagerv2 rechercheusager api_orientationusagerv1 orientationusager profil_accedant","RechercheUsager_parDateNaissance-NIR":"/partenaire/rechercher-usager/v2/usagers/par-datenaissance-et-nir","LectureOrientation":"/partenaire/orientationusager/v1/lectureOrientation","OrientationUsager":"/partenaire/orientationusager/v1/orientation"}',
        'Paramétrage de l''API France Travail',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP
    WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Francetravail.APIURL');

-- Ajout de la configuration d'activation de l'envoi des orientations à France Travail
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
    SELECT
        'Module.Francetravail.EnvoiOrientation',
        'false',
        'Active / désactive l''envoi des orientations à France travail (par défaut à false)',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP
    WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Francetravail.EnvoiOrientation');

-- Ajout de la configuration de date de dernier envoi des orientations à France Travail
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
    SELECT
        'Module.Francetravail.DateEnvoi',
        '',
        'Détermine la date exacte du dernier envoi des orientations à France travail (par défaut à vide). À mettre au format AAAA-MM-DD',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP
    WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Francetravail.DateEnvoi');


UPDATE configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE
    configurationscategories.lib_categorie = 'webrsa'
    AND configurations.lib_variable IN ('Module.Francetravail.APIURL', 'Module.Francetravail.EnvoiOrientation', 'Module.Francetravail.DateEnvoi');


-- Mise à jour de l'ordre du dernier critère
UPDATE criteresalgorithmeorientation SET ordre = 18 WHERE code = 'FINAL';

-- Ajout des 4 nouveaux critères de l'algo d'orientation
INSERT INTO criteresalgorithmeorientation
(ordre, libelle, type_orient_parent_id, type_orient_enfant_id, code, libelle_initial)
VALUES
(14, 'France travail préconise-t-il un parcours emploi ?', 3, 7, 'FT_PRECO_EMPLOI', 'France travail préconise-t-il un parcours emploi ?'),
(15, 'France travail préconise-t-il un parcours social ?', 2, 6, 'FT_PRECO_SOCIAL', 'France travail préconise-t-il un parcours social ?'),
(16, 'France travail préconise-t-il un parcours socio professionnel ?', 1, 8, 'FT_PRECO_SOCIOPRO', 'France travail préconise-t-il un parcours socio professionnel ?'),
(17, 'Critère balai France Travail', 3, 7, 'FT_BALAI', 'Critère balai France Travail');

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
