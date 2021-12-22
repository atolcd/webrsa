SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.19.1', CURRENT_TIMESTAMP);

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
