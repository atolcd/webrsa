SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Configuration du cache de WebRSA
-- Mode debug
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, created, modified)
select 'Configuration.cache.debug', '"+10 seconds"', 'Valeur du cache en mode debug.', current_timestamp, current_timestamp
where not exists (select id from configurations where lib_variable LIKE 'Configuration.cache.debug');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Configuration.cache.debug';

-- Mode production
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, created, modified)
select 'Configuration.cache.production', '"+999 days"', 'Valeur du cache en mode production.', current_timestamp, current_timestamp
where not exists (select id from configurations where lib_variable LIKE 'Configuration.cache.production');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Configuration.cache.production';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************