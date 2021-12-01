SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.19.0', CURRENT_TIMESTAMP);

-- Ajout de la notion d'actif pour les passages en comissions des rendez-vous
ALTER TABLE public.statutsrdvs_typesrdv ADD IF NOT EXISTS actif bool NOT NULL DEFAULT true;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************