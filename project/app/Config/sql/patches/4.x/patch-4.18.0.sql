SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.18.0', CURRENT_TIMESTAMP);

-- Ajout de la notion d'actif pour les catégories FP93 pour l'utilisation des tableau 4 et 5
ALTER TABLE public.categoriesfps93 ADD IF NOT EXISTS tableau4_actif bool NOT NULL DEFAULT false;
ALTER TABLE public.categoriesfps93 ADD IF NOT EXISTS tableau5_actif bool NOT NULL DEFAULT false;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************