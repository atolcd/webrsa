SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.23.0', CURRENT_TIMESTAMP);

-- Création de la variable de configuration permettant d'activer ou non l'algorithme d'orientation
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.AlgorithmeOrientation.enabled', 'false', 'Active le module ''algorithme d''orientation'' spécifique au CD93. @default false',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.AlgorithmeOrientation.enabled');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.AlgorithmeOrientation.enabled';


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
