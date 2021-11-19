SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************


-- Update de la variable de configuration pour l'affichage des referents sectorisation

UPDATE public.configurations SET value_variable = 'true' WHERE lib_variable LIKE 'Module.Sectorisation.enabled';


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
