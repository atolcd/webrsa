SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Update de la variable de configuration pour l'affichage des referents sectorisation
UPDATE public.configurations SET value_variable = 'true' WHERE lib_variable LIKE 'PlanPauvrete.Nouveauxentrants.PPAE';

-- Update des variables de configuration pour ne pas afficher le rdv 3 en 1 dans le menu du plan pauvreté
UPDATE public.configurations SET value_variable = 'false' WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_second_rdv_nouveaux';
UPDATE public.configurations SET value_variable = 'false' WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_imprime_second_rdv_nouveaux';
UPDATE public.configurations SET value_variable = 'false' WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_venu_nonvenu_second_rdv_nouveaux';
UPDATE public.configurations SET value_variable = 'false' WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_second_rdv_stock';
UPDATE public.configurations SET value_variable = 'false' WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_imprime_second_rdv_stock';
UPDATE public.configurations SET value_variable = 'false' WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_venu_nonvenu_second_rdv_stock';

-- Update de la variable de configuration permettant de n'activer la modification que pour la dernière version des dsp
UPDATE public.configurations SET value_variable = 'false' WHERE lib_variable LIKE 'Dsp.modification.all.enabled';


-- *****************************************************************************
COMMIT;
-- *****************************************************************************