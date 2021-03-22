SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Insertion automatique des cantons
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, created, modified)
select 'Canton.InsertionAuto.enabled', 'false', 'Ajout des adresses sans canton dans la table adresse depuis le script d''import des cantons.', current_timestamp, current_timestamp
where not exists (select id from configurations where lib_variable LIKE 'Canton.InsertionAuto.enabled');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Canton.InsertionAuto.enabled';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************