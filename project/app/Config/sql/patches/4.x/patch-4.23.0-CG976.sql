SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

UPDATE public.configurations SET value_variable  = false WHERE lib_variable = 'Orientation.impression_auto';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************