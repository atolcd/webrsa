SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.25.4', CURRENT_TIMESTAMP);

-- Variable de configuration permettant l'impression automatique des orientations validées
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Impression.memory_limit', '"2048M"', 'Détermine la limite de mémoire pour les impressions',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Impression.memory_limit');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable IN ('Impression.memory_limit');

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
