SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Insertions des variables de configuration pour l'accès aux nouvelles cohortes du plan pauvreté
-- Rendez-vous élaboration CER
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_stock', 'false', 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrits PE > Rendez-vous élaboration CER', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_stock');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_stock';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_nouveaux', 'false', 'Accès à la cohorte Plan Pauvreté > Nouveaux entrants > Non inscrits PE > Rendez-vous élaboration CER', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_nouveaux');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_nouveaux';

-- Impression rendez-vous élaboration CER
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_imprime_stock', 'false', 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrits PE > Impression rendez-vous élaboration CER', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_imprime_stock');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_imprime_stock';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_imprime_nouveaux', 'false', 'Accès à la cohorte Plan Pauvreté > Nouveaux entrants > Non inscrits PE > Impression rendez-vous élaboration CER', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_imprime_nouveaux');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_imprime_nouveaux';

-- Convoqués rendez-vous élaboration CER
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_venu_nonvenu_stock', 'false', 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrits PE > Convoqués rendez-vous élaboration CER', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_venu_nonvenu_stock');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_venu_nonvenu_stock';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_venu_nonvenu_nouveaux', 'false', 'Accès à la cohorte Plan Pauvreté > Nouveaux entrants > Non inscrits PE > Convoqués rendez-vous élaboration CER', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_venu_nonvenu_nouveaux');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_venu_nonvenu_nouveaux';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************