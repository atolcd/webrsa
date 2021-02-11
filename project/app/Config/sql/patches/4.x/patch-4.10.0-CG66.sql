SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Adaptation du PPAE pour le 66 - Rollback
UPDATE public.regroupementseps SET sanctionep58 = 'nontraite';

UPDATE public.configurations SET value_variable = false where lib_variable like 'Commissionseps.sanctionep.nonrespectppae';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************