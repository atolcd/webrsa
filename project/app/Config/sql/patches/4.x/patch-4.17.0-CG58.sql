SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Accès aux données PE depuis le menu allocataire
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.fluxpoleemploi.enabled', 'false', 'Accès aux données PE depuis le menu allocataire',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.fluxpoleemploi.enabled');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.fluxpoleemploi.enabled';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************