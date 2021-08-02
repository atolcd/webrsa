SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Ajout la colonne actif_dossier dans la table typesorients
ALTER TABLE public.typesorients ADD IF NOT EXISTS actif_dossier bool NOT NULL DEFAULT true;

-- Ajout de la notion d'actif pour les statuts de rendez-vous
ALTER TABLE public.statutsrdvs ADD actif int2 NOT NULL DEFAULT 1;

-- Ajout de l'extension pg_trgm pour la gestion des doublons
CREATE EXTENSION IF NOT EXISTS pg_trgm;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************