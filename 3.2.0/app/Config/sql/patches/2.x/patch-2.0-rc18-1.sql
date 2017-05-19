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

DROP TABLE IF EXISTS courrierspdos CASCADE;
DROP TABLE IF EXISTS courrierspdos_traitementspdos CASCADE;
DROP TABLE IF EXISTS textareascourrierspdos CASCADE;
DROP TABLE IF EXISTS contenustextareascourrierspdos CASCADE;


DROP INDEX IF EXISTS courrierspdos_name_idx ;
DROP INDEX IF EXISTS courrierspdos_modeleodt_idx;

DROP INDEX IF EXISTS courrierspdos_traitementspdos_traitementpdo_id_idx;
DROP INDEX IF EXISTS courrierspdos_traitementspdos_courrierpdo_id_idx;

DROP INDEX IF EXISTS textareascourrierspdos_courrierpdo_id_idx;
DROP INDEX IF EXISTS textareascourrierspdos_ordre_idx;

DROP INDEX IF EXISTS contenustextareascourrierspdos_courrierpdo_id_idx;
DROP INDEX IF EXISTS contenustextareascourrierspdos_courrierpdo_traitementpdo_id_idx;


CREATE TABLE courrierspdos (
    id                      SERIAL NOT NULL PRIMARY KEY,
    name                    VARCHAR(255) NOT NULL,
    modeleodt               VARCHAR(255) NOT NULL
);
COMMENT ON TABLE courrierspdos IS 'Liste des courriers liés à un traitement de PDO (CG66)';
CREATE INDEX courrierspdos_name_idx ON courrierspdos( name );
CREATE INDEX courrierspdos_modeleodt_idx ON courrierspdos( modeleodt );


--************************************************************************************************
CREATE TABLE courrierspdos_traitementspdos (
    id              SERIAL NOT NULL PRIMARY KEY,
    courrierpdo_id       INTEGER NOT NULL REFERENCES courrierspdos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    traitementpdo_id          INTEGER NOT NULL REFERENCES traitementspdos(id) ON DELETE CASCADE ON UPDATE CASCADE

);
COMMENT ON TABLE courrierspdos_traitementspdos IS 'Table de liaison entre les courriers et le traitement d''une PDO (CG66)';
CREATE INDEX courrierspdos_traitementspdos_courrierpdo_id_idx ON courrierspdos_traitementspdos (courrierpdo_id);
CREATE INDEX courrierspdos_traitementspdos_traitementpdo_id_idx ON courrierspdos_traitementspdos (traitementpdo_id);

-- *****************************************************************************

CREATE TABLE textareascourrierspdos (
    id                      SERIAL NOT NULL PRIMARY KEY,
    courrierpdo_id       INTEGER NOT NULL REFERENCES courrierspdos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    nomchampodt             VARCHAR(250) NOT NULL,
    name                 TEXT NOT NULL,
    ordre           INTEGER NOT NULL
);
COMMENT ON TABLE textareascourrierspdos IS 'Table permettant de lier les zones de commentaires à un courrier de PDO (CG66)';
CREATE INDEX textareascourrierspdos_courrierpdo_id_idx ON textareascourrierspdos( courrierpdo_id );
CREATE INDEX textareascourrierspdos_ordre_idx ON textareascourrierspdos( ordre );


-- ************************************
CREATE TABLE contenustextareascourrierspdos (
    id                                      SERIAL NOT NULL PRIMARY KEY,
    textareacourrierpdo_id                  INTEGER NOT NULL REFERENCES textareascourrierspdos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    courrierpdo_traitementpdo_id            INTEGER NOT NULL REFERENCES courrierspdos_traitementspdos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    contenu                                 TEXT NOT NULL
);
COMMENT ON TABLE contenustextareascourrierspdos IS 'Table de liaison entre les courriers PDOs et le nombre de textarea à ajouter (CG66)';
CREATE INDEX contenustextareascourrierspdos_courrierpdo_id_idx ON contenustextareascourrierspdos( textareacourrierpdo_id );
CREATE INDEX contenustextareascourrierspdos_courrierpdo_traitementpdo_id_idx ON contenustextareascourrierspdos( courrierpdo_traitementpdo_id );
-- ************************************

-- 20110411
DROP INDEX IF EXISTS traitementspdos_dtfinperiode_idx;
SELECT public.alter_columnname_ifexists( 'public', 'traitementspdos', 'dtfinperiode', 'datefinperiode' );
SELECT public.alter_columnname_ifexists( 'public', 'traitementspdos', 'dureeecheance', 'dureefinperiode' );
DROP INDEX IF EXISTS traitementspdos_datefinperiode_idx;
CREATE INDEX traitementspdos_datefinperiode_idx ON traitementspdos (datefinperiode);

-- -----------------------------------------------------------------------------
-- 20110411: renommage des séquences des tables renommées en 2.0rc12
-- -----------------------------------------------------------------------------

SELECT rename_sequence_ifexists( 'dossiers_rsa', 'dossiers' );
SELECT rename_sequence_ifexists( 'adresses_foyers', 'adressesfoyers' );
SELECT rename_sequence_ifexists( 'titres_sejour', 'titressejour' );
SELECT rename_sequence_ifexists( 'avispcgdroitrsa', 'avispcgdroitsrsa' );
SELECT rename_sequence_ifexists( 'ressourcesmensuelles_detailsressourcesmensuelles', 'detailsressourcesmensuelles_ressourcesmensuelles' );
SELECT rename_sequence_ifexists( 'typesaidesapres66_piecesaides66', 'piecesaides66_typesaidesapres66' );
SELECT rename_sequence_ifexists( 'typesaidesapres66_piecescomptables66', 'piecescomptables66_typesaidesapres66' );
SELECT rename_sequence_ifexists( 'users_contratsinsertion', 'contratsinsertion_users' );
SELECT rename_sequence_ifexists( 'zonesgeographiques_regroupementszonesgeo', 'regroupementszonesgeo_zonesgeographiques' );

-- -----------------------------------------------------------------------------------------------
-- 20110412: ajout de nouveaux champs sur la table decisionspropospdos suite à l'avis de l'EP
-- -----------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'decisionspropospdos', 'hasreponseep', 'type_booleannumber');
SELECT add_missing_table_field ('public', 'decisionspropospdos', 'accordepaudition', 'type_booleannumber');
SELECT add_missing_table_field ('public', 'decisionspropospdos', 'commentairereponseep', 'TEXT');
SELECT add_missing_table_field ('public', 'decisionspropospdos', 'datereponseep', 'date');
SELECT add_missing_table_field ('public', 'decisionspropospdos', 'decisionreponseep', 'type_decisiondefautep66' );

-- -----------------------------------------------------------------------------------------------
-- 20110413: ajout de la structure référente pour les bilans de parcours provenant des sites partenaires
-- -----------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'bilansparcours66', 'autrestructurereferente_id', 'integer' );
SELECT add_missing_constraint ('public', 'bilansparcours66', 'bilansparcours66_autrestructurereferente_id_fkey', 'structuresreferentes', 'autrestructurereferente_id');


-- -----------------------------------------------------------------------------------------------
-- 20110415: Ajout d'uhne table générique pour le stockage des fichiers scannés
-- -----------------------------------------------------------------------------------------------
DROP TABLE IF EXISTS fichiersmodules;

CREATE TABLE fichiersmodules (
    id                      SERIAL NOT NULL PRIMARY KEY,
    name                    VARCHAR(255) NOT NULL,
    fk_value                INTEGER NOT NULL,
    document                BYTEA DEFAULT NULL,
    modele                  VARCHAR(255) NOT NULL,
    cmspath                 VARCHAR(255) DEFAULT NULL,
    mime                    VARCHAR(255) NOT NULL,
    created                 TIMESTAMP WITHOUT TIME ZONE,
    modified                TIMESTAMP WITHOUT TIME ZONE
);

CREATE INDEX fichiersmodules_name_idx ON fichiersmodules( name );
CREATE INDEX fichiersmodules_fk_value_idx ON fichiersmodules( fk_value );
CREATE INDEX fichiersmodules_mime_idx ON fichiersmodules( mime );
CREATE UNIQUE INDEX fichiersmodules_cmspath_idx ON fichiersmodules( cmspath );


-- -----------------------------------------------------------------------------------------------
-- 20110418: Ajout d'un champ pour sélectionner si on ajoute des fichiers ou non aux PDOs
-- -----------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'propospdos', 'haspiece', 'type_booleannumber');
ALTER TABLE propospdos ALTER COLUMN haspiece SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE propospdos SET haspiece = '0'::TYPE_BOOLEANNUMBER WHERE haspiece IS NULL;
ALTER TABLE propospdos ALTER COLUMN haspiece SET NOT NULL;

-- -----------------------------------------------------------------------------------------------
-- 20110418: Ajout d'une table de paramétrage pour les COVs du CG58
-- -----------------------------------------------------------------------------------------------
DROP TABLE IF EXISTS sitescovs58 CASCADE;
CREATE TABLE sitescovs58(
    id                      SERIAL NOT NULL PRIMARY KEY,
    name                    VARCHAR(255) NOT NULL
);
CREATE INDEX sitescovs58_name_idx ON sitescovs58( name );

SELECT add_missing_table_field ('public', 'covs58', 'sitecov58_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'covs58', 'covs58_sitecov58_id_fkey', 'sitescovs58', 'sitecov58_id');

-- Récupération des anciens noms de sites pré-saisis
-- FIXME; ne peut être passé qu'une fois pour le moment
INSERT INTO sitescovs58 ( name )
    SELECT
            covs58.name AS name
        FROM covs58
        WHERE
            covs58.name IS NOT NULL;

UPDATE covs58
    SET sitecov58_id = sitescovs58.id
            FROM sitescovs58
            WHERE sitescovs58.name = covs58.name;

ALTER TABLE covs58 ALTER COLUMN sitecov58_id SET NOT NULL;
ALTER TABLE covs58 ALTER COLUMN name DROP NOT NULL;


-- -----------------------------------------------------------------------------------------------
-- 20110419: Ajout d'un champ pour sélectionner si on ajoute des fichiers ou non aux Orientations
-- -----------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'orientsstructs', 'haspiecejointe', 'type_booleannumber');
ALTER TABLE orientsstructs ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE orientsstructs SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE orientsstructs ALTER COLUMN haspiecejointe SET NOT NULL;
-- -----------------------------------------------------------------------------------------------
-- 20110419: Ajout d'un champ pour sélectionner si on ajoute des fichiers ou non aux Rendezvous
-- -----------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'rendezvous', 'haspiecejointe', 'type_booleannumber');
ALTER TABLE rendezvous ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE rendezvous SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE rendezvous ALTER COLUMN haspiecejointe SET NOT NULL;
-- -----------------------------------------------------------------------------------------------
-- 20110419: Ajout d'un champ pour sélectionner si on ajoute des fichiers ou non aux Bilans de Parcours 66
-- -----------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'bilansparcours66', 'haspiecejointe', 'type_booleannumber');
ALTER TABLE bilansparcours66 ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE bilansparcours66 SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN haspiecejointe SET NOT NULL;
-- -----------------------------------------------------------------------------------------------
-- 20110419: Ajout d'un champ pour sélectionner si on ajoute des fichiers ou non aux CER
-- -----------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'contratsinsertion', 'haspiecejointe', 'type_booleannumber');
ALTER TABLE contratsinsertion ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE contratsinsertion SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE contratsinsertion ALTER COLUMN haspiecejointe SET NOT NULL;
-- -----------------------------------------------------------------------------------------------
-- 20110420: Ajout d'un champ pour sélectionner si on ajoute des fichiers ou non aux DSPs
-- -----------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'dsps_revs', 'haspiecejointe', 'type_booleannumber');
ALTER TABLE dsps_revs ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE dsps_revs SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE dsps_revs ALTER COLUMN haspiecejointe SET NOT NULL;
-- -------------------------------------------------------------------------------------------------------------
-- 20110420: Ajout d'un champ pour sélectionner si on ajoute des fichiers ou non aux Référent du parcours
-- -------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'personnes_referents', 'haspiecejointe', 'type_booleannumber');
ALTER TABLE personnes_referents ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE personnes_referents SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE personnes_referents ALTER COLUMN haspiecejointe SET NOT NULL;

-- -------------------------------------------------------------------------------------------------------------
-- 20110420: Ajout d'un champ pour sélectionner si on ajoute des fichiers ou non aux Entretiens
-- -------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'entretiens', 'haspiecejointe', 'type_booleannumber');
ALTER TABLE entretiens ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE entretiens SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE entretiens ALTER COLUMN haspiecejointe SET NOT NULL;

-- -------------------------------------------------------------------------------------------------------------
-- 20110420: Ajout d'un champ pour sélectionner si on ajoute des fichiers ou non aux APREs
-- -------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'apres', 'haspiecejointe', 'type_booleannumber');
ALTER TABLE apres ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE apres SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE apres ALTER COLUMN haspiecejointe SET NOT NULL;

SELECT alter_table_drop_column_if_exists ('public', 'dsps', 'haspiecejointe');

-- -------------------------------------------------------------------------------------------------------------
-- 20110420: Ajout d'un champ pour sélectionner si on ajoute des fichiers ou non aux Personnes
-- -------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'personnes', 'haspiecejointe', 'type_booleannumber');
ALTER TABLE personnes ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
-- UPDATE personnes SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
-- ALTER TABLE personnes ALTER COLUMN haspiecejointe SET NOT NULL;

-- -------------------------------------------------------------------------------------------------------------
-- 20110426: optimisations ?
-- -------------------------------------------------------------------------------------------------------------

CREATE INDEX informationspe_nom_prenom_dtnai_idx ON informationspe USING btree (nom, prenom, dtnai);
CREATE INDEX personnes_nom_prenom_dtnai_idx ON personnes USING btree (nom, prenom, dtnai);

-- -------------------------------------------------------------------------------------------------------------
-- 20110506: Fiche candidature pour CG66
-- -------------------------------------------------------------------------------------------------------------

-- ACTIONSCANDIDATS & SES RELATIONS
DROP TABLE IF EXISTS actionscandidats_partenaires;
DROP TABLE IF EXISTS actionscandidats_personnes;
DROP TABLE IF EXISTS actionscandidats_zonesgeographiques;
DROP TABLE IF EXISTS actionscandidats;

-- ACTIONSCANDIDATS
DROP TYPE IF EXISTS type_contractualisation;
CREATE TYPE type_contractualisation AS ENUM ('marche', 'subvention');

CREATE TABLE actionscandidats
(
	id					SERIAL NOT NULL PRIMARY KEY,
	name				character varying(250) NOT NULL,
	themecode			INTEGER NOT NULL,
	codefamille			character varying(1) NOT NULL,
	numcodefamille		character varying(5) NOT NULL,
	contractualisation	type_contractualisation DEFAULT NULL,
	lieuaction			character varying(250) NOT NULL,
	cantonaction		character varying(250) NOT NULL,
	ddaction			DATE NOT NULL,
	dfaction			DATE NOT NULL,
	nbpostedispo		INTEGER NOT NULL,
	nbposterestant		INTEGER,
	correspondantaction	type_booleannumber DEFAULT '0'::type_booleannumber,
	referent_id 		INTEGER REFERENCES referents(id),
	hasfichecandidature	type_booleannumber NOT NULL DEFAULT '1'::type_booleannumber,
	filtre_zone_geo 	type_booleannumber DEFAULT '0'::type_booleannumber
);

COMMENT ON TABLE actionscandidats IS 'Table pour les actions d''insertion liées à la fiche de candidature (CG66)';
COMMENT ON COLUMN actionscandidats.id IS '[PK] Identifiant';
COMMENT ON COLUMN actionscandidats.name	IS 'Intitulé de l''action';
COMMENT ON COLUMN actionscandidats.themecode IS 'Code de l''action : partie thème';
COMMENT ON COLUMN actionscandidats.codefamille IS 'Code de l''action : partie code famille';
COMMENT ON COLUMN actionscandidats.numcodefamille IS 'Code de l''action : numéro du code famille';
COMMENT ON COLUMN actionscandidats.contractualisation IS '[ENUM type_contractualisation] ex : Marché public, Subvention,...';
COMMENT ON COLUMN actionscandidats.lieuaction IS 'Lieu de l''action';
COMMENT ON COLUMN actionscandidats.cantonaction IS 'Canton de l''action';
COMMENT ON COLUMN actionscandidats.ddaction IS 'Date de début de l''action';
COMMENT ON COLUMN actionscandidats.dfaction IS 'Date de fin de l''action';
COMMENT ON COLUMN actionscandidats.nbpostedispo IS 'Nombre de postes disponibles';
COMMENT ON COLUMN actionscandidats.nbposterestant IS 'Nombre de postes restants (calculé)';
COMMENT ON COLUMN actionscandidats.correspondantaction IS 'Présence d''un correspondant de l''action';
COMMENT ON COLUMN actionscandidats.referent_id IS 'Référent de l''action (si correspondantaction est 1)';
COMMENT ON COLUMN actionscandidats.hasfichecandidature IS 'Présence d''une fiche de candidature';

CREATE INDEX actionscandidats_name_idx ON actionscandidats( name );
CREATE INDEX actionscandidats_themecode_idx ON actionscandidats( themecode );
CREATE INDEX actionscandidats_codefamille_idx ON actionscandidats( codefamille );
CREATE INDEX actionscandidats_numcodefamille_idx ON actionscandidats( numcodefamille );
CREATE INDEX actionscandidats_contractualisation_idx ON actionscandidats( contractualisation );
CREATE INDEX actionscandidats_lieuaction_idx ON actionscandidats( lieuaction );
CREATE INDEX actionscandidats_cantonaction_idx ON actionscandidats( cantonaction );
CREATE INDEX actionscandidats_nbpostedispo_idx ON actionscandidats( nbpostedispo );
CREATE INDEX actionscandidats_correspondantaction_idx ON actionscandidats( correspondantaction );
CREATE INDEX actionscandidats_referent_id_idx ON actionscandidats( referent_id );


-- MOTIFSSORTIE
DROP TABLE IF EXISTS motifssortie;
CREATE TABLE motifssortie
(
    id  SERIAL NOT NULL PRIMARY KEY,
    name    TEXT
);

COMMENT ON TABLE motifssortie IS 'Table pour les motifs de sortie d''une action d''insertion pour la fiche de candidature (CG66)';
COMMENT ON COLUMN motifssortie.id IS '[PK] Identifiant';
COMMENT ON COLUMN motifssortie.name IS 'Intitulé du motif de sortie';

CREATE INDEX motifssortie_name_idx ON motifssortie( name );



-- ACTIONSCANDIDATS_PERSONNES
CREATE TABLE actionscandidats_personnes
(
  id serial NOT NULL PRIMARY KEY,
  personne_id integer NOT NULL REFERENCES personnes (id) ON UPDATE NO ACTION ON DELETE NO ACTION,
  actioncandidat_id integer NOT NULL REFERENCES actionscandidats (id) ON UPDATE NO ACTION ON DELETE NO ACTION,
  referent_id integer NOT NULL REFERENCES referents (id) ON UPDATE NO ACTION ON DELETE NO ACTION,
  ddaction date,
  dfaction date,
  motifdemande text,
  enattente type_no,
  datesignature date,
  bilanvenu type_venu,
  bilanretenu type_retenu,
  infocomplementaire text,
  datebilan date,
  rendezvouspartenaire type_booleannumber,
  mobile type_booleannumber,
  naturemobile text,
  typemobile text,
  bilanrecu type_no,
  daterecu date,
  personnerecu character varying(50),
  pieceallocataire type_munir,
  autrepiece character varying(50),
  precisionmotif text,
  presencecontrat type_no,
  integrationaction type_no,
  horairerdvpartenaire timestamp without time zone,
  sortiele date,
  motifsortie_id integer REFERENCES motifssortie (id) ON UPDATE NO ACTION ON DELETE NO ACTION
);

COMMENT ON TABLE actionscandidats_personnes IS 'Table de liaison entre une personne, un référent et les actions de candidature';

CREATE INDEX actionscandidats_personnes_actioncandidat_id_idx ON actionscandidats_personnes (actioncandidat_id);
CREATE INDEX actionscandidats_personnes_bilanrecu_idx ON actionscandidats_personnes (bilanrecu);
CREATE INDEX actionscandidats_personnes_bilanretenu_idx ON actionscandidats_personnes (bilanretenu);
CREATE INDEX actionscandidats_personnes_bilanvenu_idx ON actionscandidats_personnes (bilanvenu);
CREATE INDEX actionscandidats_personnes_datebilan_idx ON actionscandidats_personnes (datebilan);
CREATE INDEX actionscandidats_personnes_daterecu_idx ON actionscandidats_personnes (daterecu);
CREATE INDEX actionscandidats_personnes_datesignature_idx ON actionscandidats_personnes (datesignature);
CREATE INDEX actionscandidats_personnes_ddaction_idx ON actionscandidats_personnes (ddaction);
CREATE INDEX actionscandidats_personnes_dfaction_idx ON actionscandidats_personnes (dfaction);
CREATE INDEX actionscandidats_personnes_enattente_idx ON actionscandidats_personnes (enattente);
CREATE INDEX actionscandidats_personnes_integrationaction_idx ON actionscandidats_personnes (integrationaction);
CREATE INDEX actionscandidats_personnes_mobile_idx ON actionscandidats_personnes (mobile);
CREATE INDEX actionscandidats_personnes_personne_id_idx ON actionscandidats_personnes (personne_id);
CREATE INDEX actionscandidats_personnes_pieceallocataire_idx ON actionscandidats_personnes (pieceallocataire);
CREATE INDEX actionscandidats_personnes_presencecontrat_idx ON actionscandidats_personnes (presencecontrat);
CREATE INDEX actionscandidats_personnes_referent_id_idx ON actionscandidats_personnes (referent_id);
CREATE INDEX actionscandidats_personnes_rendezvouspartenaire_idx ON actionscandidats_personnes (rendezvouspartenaire);
CREATE INDEX actionscandidats_personnes_motifsortie_id_idx ON actionscandidats_personnes( motifsortie_id );

-- ACTIONSCANDIDATS_PARTENAIRES
CREATE TABLE actionscandidats_partenaires
(
  id 				SERIAL NOT NULL PRIMARY KEY,
  actioncandidat_id	INTEGER NOT NULL REFERENCES actionscandidats(id) ON DELETE CASCADE ON UPDATE CASCADE,
  partenaire_id		INTEGER NOT NULL REFERENCES partenaires(id) ON DELETE CASCADE ON UPDATE CASCADE
);

COMMENT ON TABLE actionscandidats_partenaires IS 'Table de liaison entre les partenaires et les actions de candidature';

-- ACTIONSCANDIDATS_ZONESGEOGRAPHIQUES
CREATE TABLE actionscandidats_zonesgeographiques
(
	id					SERIAL NOT NULL PRIMARY KEY,
	actioncandidat_id	INTEGER NOT NULL REFERENCES actionscandidats(id) ON DELETE CASCADE ON UPDATE CASCADE,
	zonegeographique_id	INTEGER NOT NULL REFERENCES zonesgeographiques(id) ON DELETE CASCADE ON UPDATE CASCADE
);

COMMENT ON TABLE actionscandidats_zonesgeographiques IS 'Table de liaison entre les actions d''insertion pour fiches de candidatures et les zones géographiques (CG66)';
COMMENT ON COLUMN actionscandidats_zonesgeographiques.id IS '[PK] Identifiant';
COMMENT ON COLUMN actionscandidats_zonesgeographiques.actioncandidat_id IS '[FK] actionscandidats(id)';
COMMENT ON COLUMN actionscandidats_zonesgeographiques.zonegeographique_id IS '[FK] zonesgeographiques(id)';

-- PARTENAIRES
SELECT alter_table_drop_column_if_exists('public', 'partenaires', 'codepartenaire');
ALTER TABLE partenaires ADD COLUMN codepartenaire character varying(10);
COMMENT ON COLUMN partenaires.codepartenaire IS 'Code partenaire (en lien avec le code action)';

-- -----------------------------------------------------------------------------
-- 20110509
-- -----------------------------------------------------------------------------

-- Correction d'une commande du patch 2.0rc15 (ligne 241) qui ne nettoyait pas
-- correctement les orientsstructs non orientées.
UPDATE orientsstructs
	SET
		typeorient_id = NULL,
		structurereferente_id = NULL,
		referent_id = NULL,
		valid_cg = NULL,
		date_valid = NULL,
		date_impression = NULL,
		etatorient = NULL,
		rgorient = NULL,
		structureorientante_id = NULL,
		referentorientant_id = NULL,
		user_id = NULL
	WHERE statut_orient <> 'Orienté';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************