SET client_encoding = 'UTF8';
-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.24.1-CG93', CURRENT_TIMESTAMP);


--Création du tag pour le dépassement dans l'algorithme d'orientation
INSERT INTO public.valeurstags(name, categorietag_id)
SELECT 'dépassement', (SELECT id FROM categorietags where name like 'Orientation cohorte')
WHERE NOT EXISTS (SELECT id FROM valeurstags WHERE name LIKE 'dépassement');

-- Ajout de la configuration pour le tag de dépassement dans l'algorithme d'orientation
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.AlgorithmeOrientation.TagDepassement', (SELECT id FROM valeurstags WHERE name LIKE 'dépassement'), 'id du tag pour les dépassements dans l''algorithme d''orientation', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.AlgorithmeOrientation.TagDepassement');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.AlgorithmeOrientation.TagDepassement';




-- *****************************************************************************
COMMIT;
-- *****************************************************************************
