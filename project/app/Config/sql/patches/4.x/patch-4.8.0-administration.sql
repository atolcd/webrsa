SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Schéma administration (Au CD93, à appliquer sur la base staging)
-- Ajout d'identificationflux_id dans les tables visionneuse et rejet_historique
ALTER TABLE administration.visionneuses ADD COLUMN identificationflux_id INT4;
ALTER TABLE administration.rejet_historique ADD COLUMN identificationflux_id INT4;

-- Création de la table mettant à jour les dates d'insertion de flux
CREATE TABLE IF NOT EXISTS administration.majdossier (
	dossier_id INT4 NOT NULL,
	dtmajfluxbenef TIMESTAMP,
	dtmajfluxinst TIMESTAMP,
	dtmajfluxfinanc TIMESTAMP,
	CONSTRAINT majdossier_pkey PRIMARY KEY (dossier_id)
);

-- Création de la table mettant à jour les dates d'insertion de flux
CREATE TABLE IF NOT EXISTS administration.talendsynt (
	id serial NOT NULL,
	identificationflux_id INT4 NOT NULL,
	qual varchar(3) NULL,
	nom varchar(50) NULL,
	prenom varchar(50) NULL,
	nomnai varchar(50) NULL,
	dtnai date NULL,
	nir bpchar(15) NULL,
	sexe bpchar(1) NULL,
	cree boolean NOT NULL DEFAULT FALSE,
	maj boolean NOT NULL DEFAULT FALSE,
	rejet boolean NOT NULL DEFAULT FALSE,
	CONSTRAINT talendsynt_pkey PRIMARY KEY (id)
);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************