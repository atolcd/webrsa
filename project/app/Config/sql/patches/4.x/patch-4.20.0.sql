SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Suppression des variables de configuration obsolètes
DELETE
FROM public.configurations
WHERE lib_variable = 'PlanPauvrete.Cohorte.OrientationrdvSocialeDeFait';

DELETE
FROM public.configurations
WHERE lib_variable = 'Module.OrientationrdvSocialeDeFait.enabled';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
