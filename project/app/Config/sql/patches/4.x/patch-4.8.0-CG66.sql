SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Adaptation du PPAE pour le 66
UPDATE public.regroupementseps SET sanctionep58 = 'decisionep';

-- Adaptation du module Apres66 pour les recherches
UPDATE public.configurations SET configurationscategorie_id = configurationscategories.id FROM configurationscategories WHERE configurationscategories.lib_categorie = 'Apres' AND configurations.lib_variable LIKE 'ConfigurableQuery.Apres.search';
UPDATE public.configurations SET configurationscategorie_id = configurationscategories.id FROM configurationscategories WHERE configurationscategories.lib_categorie = 'Apres' AND configurations.lib_variable LIKE 'ConfigurableQuery.Apres.exportcsv';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************