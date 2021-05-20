SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Configuration de la recherche par données Pôle Emploi
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, created, modified)
select 'Module.RecherchePoleEmploi.enabled', 'true', 'Accès à la recherche par données Pôle Emploi', current_timestamp, current_timestamp
where not exists (select id from configurations where lib_variable LIKE 'Module.RecherchePoleEmploi.enabled');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.RecherchePoleEmploi.enabled';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************