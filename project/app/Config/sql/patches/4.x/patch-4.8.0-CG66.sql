SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Adaptation du PPAE pour le 66
UPDATE public.regroupementseps SET sanctionep58 = 'decisionep';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************