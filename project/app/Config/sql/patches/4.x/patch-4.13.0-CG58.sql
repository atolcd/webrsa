SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Activation de l'option de l'orientation directe
UPDATE public.configurations
SET value_variable = 'true'
WHERE lib_variable LIKE 'Module.ModifEtatPE.enabled';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************