SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Ajout de la colonne email pour les structures référentes
ALTER TABLE public.structuresreferentes ADD COLUMN email varchar(250);


-- *****************************************************************************
COMMIT;
-- *****************************************************************************