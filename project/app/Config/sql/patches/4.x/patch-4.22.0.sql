SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.22.0', CURRENT_TIMESTAMP);

-- Mise à jour des commentaires des variables de configuration Module.Cohorte.Plan.Pauvrete*
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Nouveaux Entrants (hors CD58)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Nouveaux.Menu';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Nouveaux Entrants > Inscrit PE > Inscrit PE ou Nouveaux entrants du Plan pauvreté > Inscrit PE > Inscrits PE avec PPAE (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_isemploi';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Nouveaux Entrants > Inscrit PE > Impression inscrits PE ou Nouveaux entrants du Plan pauvreté > Inscrit PE > Impression Orientation PE (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_isemploi_imprime';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Nouveaux Entrants > Non inscrit PE > Information collective ou Nouveaux entrants du Plan pauvreté > RDV > Info coll / indiv > Création des RDV (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Nouveaux Entrants > Non inscrit PE > Impression information collective ou Nouveaux entrants du Plan pauvreté > RDV > Info coll / indiv > Impression des convocations (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_imprime';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Nouveaux Entrants > Inscrits PE ou Nouveaux entrants du Plan pauvreté > Inscrit PE (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Nouveaux.MenuInscritPE';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Nouveaux Entrants > Non inscrits PE ou Nouveaux entrants du Plan pauvreté > RDV (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Nouveaux.MenuNonInscritPE';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Stock > Inscrits PE ou File active des Non orientés > Inscrits PE (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Stock.MenuInscritPE';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté (hors CD58)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Menu';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Nouveaux Entrants > Non inscrits PE > Convoqués informations collectives ou Nouveaux entrants du Plan pauvreté > RDV > Info coll / indiv > MAJ du statut des RDV (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_venu_nonvenu_nouveaux';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Nouveaux Entrants > Non inscrits PE > 3 en 1 ou Nouveaux entrants du Plan pauvreté > RDV > 3 en 1 > Création des RDV (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_second_rdv_nouveaux';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Nouveaux Entrants > Non inscrits PE > Impression 3 en 1 ou Nouveaux entrants du Plan pauvreté > RDV > 3 en 1 > Impression des convocations (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_imprime_second_rdv_nouveaux';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Stock (hors CD58)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Stock.Menu';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Stock > Inscrit PE > Inscrit PE ou File active des Non orientés > Inscrit PE > Inscrits PE avec PPAE (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_isemploi_stock';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Stock > Inscrit PE > Impression inscrits PE ou File active des Non orientés > Inscrit PE > Impression Orientation PE (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_isemploi_stock_imprime';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrit PE > Information collective ou File active des Non orientés > RDV > Info coll / indiv > Création des RDV (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_stock';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrit PE > Impression information collective ou File active des Non orientés > RDV > Info coll / indiv > Impression des convocations (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_imprime_stock';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrits PE > Convoqués informations collectives ou File active des Non orientés > RDV > Info coll / indiv > MAJ du statut des RDV (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_venu_nonvenu_stock';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrits PE > 3 en 1 ou File active des Non orientés > RDV > 3 en 1 > Création des RDV (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_second_rdv_stock';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrits PE > Impression 3 en 1 ou File active des Non orientés > RDV > 3 en 1 > Impression des convocations (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_imprime_second_rdv_stock';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrits PE ou File active des Non orientés > RDV (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Stock.MenuNonInscritPE';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrits PE > Convoqués rendez-vous 3 en 1 ou File active des Non orientés > 3 en 1 > MAJ du statut des RDV (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_venu_nonvenu_second_rdv_stock';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Nouveaux Entrants > Non inscrits PE > Convoqués rendez-vous 3 en 1 ou Nouveaux entrants du Plan pauvreté > 3 en 1 > MAJ du statut des RDV (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_venu_nonvenu_second_rdv_nouveaux';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrits PE > Rendez-vous élaboration CER ou File active des Non orientés > Élaboration du CER > Création du RDV (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_stock';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Nouveaux entrants > Non inscrits PE > Rendez-vous élaboration CER ou Nouveaux entrants du Plan pauvreté > Élaboration du CER > Création du RDV (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_nouveaux';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrits PE > Impression rendez-vous élaboration CER ou File active des Non orientés > Élaboration du CER > Impression des convocations (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_imprime_stock';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Nouveaux entrants > Non inscrits PE > Impression rendez-vous élaboration CER ou Nouveaux entrants du Plan pauvreté > Élaboration du CER > Impression des convocations (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_imprime_nouveaux';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrits PE > Convoqués rendez-vous élaboration CER ou File active des Non orientés > Élaboration du CER > MAJ du statut des RDV (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_venu_nonvenu_stock';
UPDATE public.configurations SET comments_variable = 'Accès à la cohorte Plan Pauvreté > Nouveaux entrants > Non inscrits PE > Convoqués rendez-vous élaboration CER ou Nouveaux entrants du Plan pauvreté > Élaboration du CER > MAJ du statut des RDV (selon département)' WHERE lib_variable = 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_venu_nonvenu_nouveaux';


-- *****************************************************************************
COMMIT;
-- *****************************************************************************