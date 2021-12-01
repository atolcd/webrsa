SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Ajout de la notion d'actif pour les passages en comissions des rendez-vous
ALTER TABLE public.statutsrdvs_typesrdv ADD IF NOT EXISTS actif bool NOT NULL DEFAULT true;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************