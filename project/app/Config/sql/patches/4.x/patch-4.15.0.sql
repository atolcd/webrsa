SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Ajout la colonne actif_dossier dans la table typesorients
ALTER TABLE public.typesorients ADD IF NOT EXISTS actif_dossier bool NOT NULL DEFAULT true;


-- *****************************************************************************
COMMIT;
-- *****************************************************************************