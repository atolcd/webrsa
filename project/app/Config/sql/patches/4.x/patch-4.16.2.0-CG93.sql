SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Update des types emplois pour avoir le bon affichage du tableau 7b
UPDATE public.typeemplois SET codeTypeEmploi = 'ACT_IND' WHERE codeTypeEmploi IS NULL;
UPDATE public.typeemplois SET ordre_affichage = 1 WHERE ordre_affichage IS NULL;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************