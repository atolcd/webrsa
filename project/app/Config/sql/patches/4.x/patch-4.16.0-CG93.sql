SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- ---------- Dispositif Jeune ----------

-- Mise à jour de l'utilisation des orientations avec parent
UPDATE public.configurations SET value_variable = 'true' WHERE lib_variable LIKE 'with_parentid';

-- Activation du workflow de validation
UPDATE public.configurations SET value_variable = 'true' WHERE lib_variable LIKE 'Orientation.validation.enabled';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************