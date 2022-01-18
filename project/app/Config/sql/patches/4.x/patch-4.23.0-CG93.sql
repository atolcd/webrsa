SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************


-- Update de la variable de configuration pour l'activation du module algorithme d'orientation
UPDATE public.configurations SET value_variable = 'true' WHERE lib_variable LIKE 'Module.AlgorithmeOrientation.enabled';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
