SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Activation de l'accès aux nouvelles cohortes du plan pauvreté
UPDATE public.configurations SET value_variable = true
WHERE lib_variable IN (
	'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_stock',
	'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_nouveaux',
	'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_imprime_stock',
	'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_imprime_nouveaux',
	'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_venu_nonvenu_stock',
	'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_venu_nonvenu_nouveaux'
);

-- Activation de l'ajout d'oriantation sociale de fait
UPDATE public.configurations SET value_variable = 'true'
WHERE lib_variable = 'PlanPauvrete.Cohorte.OrientationrdvSocialeDeFait.enabled';

-- Ajout de l'id d'orientation sociale de fait
UPDATE public.configurations SET value_variable = '{"typeorient_id": "3"}'
WHERE lib_variable = 'PlanPauvrete.Cohorte.OrientationrdvSocialeDeFait';
-- *****************************************************************************
COMMIT;
-- *****************************************************************************