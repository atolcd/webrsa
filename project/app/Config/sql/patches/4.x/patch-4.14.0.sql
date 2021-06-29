SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Ajout des dates de CER (CD93)
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Cer93.dateCER', '{"dtdebutMin":"2009-06-01","dtdebutMax":"+ 3 months"}', 'Définit la tranche de date à vérifier lors de l''enregistrement d''un CER
dtdebutMin : début minimum (date fixe)
dtdebutMax : début maximum par rapport à la date du jour',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Cer93.dateCER');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Cer93.dateCER';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************