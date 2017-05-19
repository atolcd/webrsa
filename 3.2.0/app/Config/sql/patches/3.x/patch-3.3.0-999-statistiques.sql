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


--------------------------------------------------------------------------------
-- 20151026: Nouvelle table d'informations dossier/foyer/personne/... qui servira
-- pour garder un historique à utiliser dans les statistiques ministérielles.
-- @todo voir situationsallocataires (utilisé avec les D1 au CD 93)
-- => FIXME: à mettre dans les nouveaux fichiers
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS foos;
CREATE TABLE foos (
    id					SERIAL NOT NULL PRIMARY KEY,
	-- 1°) Partie dossier, foyer, adresse
	dossier_id			INTEGER NOT NULL REFERENCES dossiers(id) ON DELETE SET NULL ON UPDATE CASCADE,
	-- 1.1°) Partie dossier
	numdemrsa			VARCHAR(11) DEFAULT NULL,
	dtdemrsa			DATE DEFAULT NULL,
	matricule			CHAR(15) DEFAULT NULL,
	-- 1.2°) Partie foyer
	sitfam				CHAR(3) DEFAULT NULL,
	nbenfants			SMALLINT DEFAULT NULL,
	nbautcharge			SMALLINT DEFAULT NULL, -- TODO: en a-t-on besoin pour les statistiques ministérielles... vérifier "à charge" ?
	-- 1.3°) Partie situationsdossiersrsa et detailscalculsdroitsrsa
	etatdosrsa			CHAR(1) DEFAULT NULL,
	natpf_socle			CHAR(3) DEFAULT NULL,
	natpf_majore		CHAR(3) DEFAULT NULL,
	natpf_activite		CHAR(3) DEFAULT NULL,
	-- 1.4°) Partie adresse
	numvoie				VARCHAR(6) DEFAULT NULL,
	libtypevoie			VARCHAR(30) DEFAULT NULL,
	nomvoie				VARCHAR(32) DEFAULT NULL,
	codepos				CHAR(5) DEFAULT NULL,
	numcom				CHAR(5) DEFAULT NULL,
	nomcom				VARCHAR(32) DEFAULT NULL,
	pays				VARCHAR(3) DEFAULT NULL,
	-- TODO: Canton en toutes lettres car il risque de changer
	-- 2°) Partie allocataire
	personne_id			INTEGER NOT NULL REFERENCES personnes(id) ON DELETE SET NULL ON UPDATE CASCADE,
	nom					VARCHAR(50) DEFAULT NULL,
	prenom				VARCHAR(50) DEFAULT NULL,
	nomnai				VARCHAR(50) DEFAULT NULL,
	nir					CHAR(15) DEFAULT NULL,
	dtnai				DATE DEFAULT NULL,
	rolepers			CHAR(3) DEFAULT NULL,
	toppersdrodevorsa	CHAR(1) DEFAULT NULL, -- in_list 0, 1
	sexe				CHAR(1) DEFAULT NULL, -- in_list 1, 2
	nivetu				CHAR(4) DEFAULT NULL,
	dernierdossier		CHAR(1) DEFAULT NULL, -- in_list 0, 1
    created				TIMESTAMP WITHOUT TIME ZONE,
    modified			TIMESTAMP WITHOUT TIME ZONE
);

-- 1°) Partie dossier, foyer, adresse
-- 1.1°) Partie dossier
COMMENT ON COLUMN foos.numdemrsa IS 'Source: dossiers.numdemrsa';
COMMENT ON COLUMN foos.dtdemrsa IS 'Source: dossiers.dtdemrsa';
COMMENT ON COLUMN foos.matricule IS 'Source: dossiers.matricule';
-- 1.2°) Partie foyer
COMMENT ON COLUMN foos.sitfam IS 'Source: foyers.sitfam';
COMMENT ON COLUMN foos.nbenfants IS 'Source: nombre de personnes du foyer ayant une prestation RSA ENF';
COMMENT ON COLUMN foos.nbautcharge IS 'Source: nombre de personnes à charge dans le foyer'; -- ?? FIXME
-- 1.3°) Partie situationsdossiersrsa et detailscalculsdroitsrsa
COMMENT ON COLUMN foos.etatdosrsa IS 'Source: situationsdossiersrsa.etatdosrsa';
COMMENT ON COLUMN foos.natpf_socle IS 'Source: detailscalculsdroitsrsa.natpf (prestation socle)';
COMMENT ON COLUMN foos.natpf_majore IS 'Source: detailscalculsdroitsrsa.natpf (prestation majorée)';
COMMENT ON COLUMN foos.natpf_activite IS 'Source: detailscalculsdroitsrsa.natpf (prestation activité)';
-- 1.4°) Partie adresse
COMMENT ON COLUMN foos.numvoie IS 'Source: adresses.numvoie';
COMMENT ON COLUMN foos.libtypevoie IS 'Source: adresses.libtypevoie';
COMMENT ON COLUMN foos.nomvoie IS 'Source: adresses.nomvoie';
COMMENT ON COLUMN foos.codepos IS 'Source: adresses.codepos';
COMMENT ON COLUMN foos.numcom IS 'Source: adresses.numcom';
COMMENT ON COLUMN foos.nomcom IS 'Source: adresses.nomcom';
COMMENT ON COLUMN foos.pays IS 'Source: adresses.pays';
-- 2°) Partie allocataire
COMMENT ON COLUMN foos.nom IS 'Source: personnes.nom';
COMMENT ON COLUMN foos.prenom IS 'Source: personnes.prenom';
COMMENT ON COLUMN foos.nomnai IS 'Source: personnes.nomnai';
COMMENT ON COLUMN foos.nir IS 'Source: personnes.nir';
COMMENT ON COLUMN foos.dtnai IS 'Source: personnes.dtnai';
COMMENT ON COLUMN foos.rolepers IS 'Source: prestations.rolepers';
COMMENT ON COLUMN foos.toppersdrodevorsa IS 'Source: calculsdroitsrsa.toppersdrodevorsa';
COMMENT ON COLUMN foos.sexe IS 'Source: personnes.sexe';
COMMENT ON COLUMN foos.nivetu IS 'Source: dsps.nivetu, dsps_revs.nivetu';
COMMENT ON COLUMN foos.dernierdossier IS 'Source: table derniersdossiersallocataires';

-- TODO: create INDEX

-- Vérification: est-il possible d'avoir plusieurs prestations socle|majore|activite ?
-- Faire des booléens comme dans instantanesdonneesfps93 (socle, socle majoré)
-- detailscalculsdroitsrsa, detaildroitrsa_id
-- Ex. dans /statistiquesministerielles/indicateurs_orientations, on ne veut pas comptabiliser les majorés
-- TODO: vérifier si on peut se contenter de socle|majore|activite|jeune ?
--	-> INFO: on perdra l'information (socle|activite) état / local, mais elle
-- n'est pas utilisée (de même que RSA jeune) dans les statistiques

/*
	'RSD' => 'RSA Socle (Financement sur fonds Conseil général)',
	'RSI' => 'RSA Socle majoré (Financement sur fonds Conseil général)',
	'RSU' => 'RSA Socle Etat Contrat aidé  (Financement sur fonds Etat)',
	'RSB' => 'RSA Socle Local (Financement sur fonds Conseil général)',
	'RCD' => 'RSA Activité (Financement sur fonds Etat)',
	'RCI' => 'RSA Activité majoré (Financement sur fonds Etat)',
	'RCU' => 'RSA Activité Etat Contrat aidé (Financement sur fonds Etat)',
	'RCB' => 'RSA Activité Local (Financement sur fonds Conseil général)',
	//ajout suite à l'arrivée du RSAJeune
	'RSJ' => 'RSA socle Jeune (Financement sur fonds Etat)',
	'RCJ' => 'RSA activité Jeune (Financement sur fonds Etat)',
	// TODO: dans la configuration ?
	'RSD,RCD' => 'RSA Socle et activité',
	//'RSJ,RCJ' => 'RSA Jeune Socle et activité',
	'RSD-RCD' => 'RSA Socle uniquement',
	'RCD-RSD' => 'RSA Activité uniquement',
*/

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
