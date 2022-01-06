SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.20.0', CURRENT_TIMESTAMP);

-- Ajout d'une variable de configuration pour le cloisonnement
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Cloisonnement.enabled', 'false', 'Permet de restreindre certains choix en fonction de la structure référente de l''utilisateur. @default false',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Cloisonnement.enabled');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Cloisonnement.enabled';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************