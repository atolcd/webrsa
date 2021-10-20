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

-- Insertion de la variable de configuration paramétrant l'orientation sociale de fait
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'PlanPauvrete.Cohorte.OrientationrdvSocialeDeFait', '{}', 'Paramétrage du type d''orientation lorsqu''une personne ne viens par à rendez-vous 3 en 1.
	La configuration doit s''écrire comme cela :
	{
		"typeorient_id": "id"
	} ', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'PlanPauvrete.Cohorte.OrientationrdvSocialeDeFait');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'PlanPauvrete.Cohorte.OrientationrdvSocialeDeFait';

-- Insertion de la variable de configuration paramétrant l'orientation sociale de fait
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.OrientationrdvSocialeDeFait.enabled', 'false', 'Activation de la création d''une orientation sociale de fait lorsqu''une personne ne viens par à rendez-vous 3 en 1', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.OrientationrdvSocialeDeFait.enabled');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'Module.OrientationrdvSocialeDeFait.enabled';

UPDATE configurations SET value_variable = '{"filters":{"defaults":{"Dossier":{"dernier":1},"Rendezvous":{"daterdv":0,"daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet_prenoms":{"sort":false},"Adresse.complete":{"sort":false},"Canton.canton":{"sort":false},"Modecontact.numtel":{"class":"numtelCAF","sort":false},"Personne.numport":{"class":"numtelCD","sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"/Dossiers/view/#Dossier.id#":{"class":"view external"},"/Personnes/coordonnees/#Personne.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_SECOND_RDV_NOUVEAUX","Statutrdv.code_statut":"PREVU"},"save":{"Typerdv.code_type":"INFO_COLL_SECOND_RDV_NOUVEAUX","Statutrdv.code_statut":""}}},"ini_set":[]}'
WHERE lib_variable = 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_second_rdv_nouveaux';

UPDATE configurations SET value_variable = '{"filters":{"defaults":{"Dossier":{"dernier":1},"Rendezvous":{"daterdv":0,"daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet_prenoms":{"sort":false},"Adresse.complete":{"sort":false},"Canton.canton":{"sort":false},"Modecontact.numtel":{"class":"numtelCAF","sort":false},"Personne.numport":{"class":"numtelCD","sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"/Dossiers/view/#Dossier.id#":{"class":"view external"},"/Personnes/coordonnees/#Personne.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_SECOND_RDV_STOCK","Statutrdv.code_statut":"PREVU"},"save":{"Typerdv.code_type":"INFO_COLL_SECOND_RDV_STOCK","Statutrdv.code_statut":""}}},"ini_set":[]}'
WHERE lib_variable = 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_second_rdv_stock';

-- Insertion des configurations des cohortes
-- Rendez-vous élaboration CER
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_stock','{"filters":{"defaults":{"Dossier":{"dernier":1},"Rendezvous":{"daterdv":0,"daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet_prenoms":{"sort":false},"Adresse.complete":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Permanence.libpermanence":{"sort":false},"Referent.nom_complet":{"sort":false},"Canton.canton":{"sort":false},"Modecontact.numtel":{"class":"numtelCAF","sort":false},"Personne.numport":{"class":"numtelCD","sort":false},"/Dossiers/view/#Dossier.id#":{"class":"view external"},"/Personnes/coordonnees/#Personne.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_SECOND_RDV_STOCK","Statutrdv.code_statut":"NONVENU"},"save":{"Typerdv.code_type":"INFO_COLL_TROISIEME_RDV_STOCK","Statutrdv.code_statut":"PREVU"}}},"ini_set":[]}','Cohorte Plan Pauvreté > Stock > Non inscrits PE > Rendez-vous élaboration CER

		array(
			 1. Filtres de recherche
			''filters'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.filters''),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			''results'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.results''),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_SECOND_RDV_STOCK'',
						''Statutrdv.code_statut'' => ''NONVENU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_TROISIEME_RDV_STOCK'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_stock');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_stock';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_nouveaux','{"filters":{"defaults":{"Dossier":{"dernier":1},"Rendezvous":{"daterdv":0,"daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet_prenoms":{"sort":false},"Adresse.complete":{"sort":false},"Canton.canton":{"sort":false},"Modecontact.numtel":{"class":"numtelCAF","sort":false},"Personne.numport":{"class":"numtelCD","sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"/Dossiers/view/#Dossier.id#":{"class":"view external"},"/Personnes/coordonnees/#Personne.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_SECOND_RDV_NOUVEAUX","Statutrdv.code_statut":"NONVENU"},"save":{"Typerdv.code_type":"INFO_COLL_TROISIEME_RDV_NOUVEAUX","Statutrdv.code_statut":"PREVU"}}},"ini_set":[]}','Cohorte Plan Pauvreté > Stock > Non inscrits PE > Rendez-vous élaboration CER

		array(
			 1. Filtres de recherche
			''filters'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.filters''),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			''results'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.results''),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_SECOND_RDV_NOUVEAUX'',
						''Statutrdv.code_statut'' => ''NONVENU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_TROISIEME_RDV_NOUVEAUX'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_nouveaux');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_nouveaux';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_stock','{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}','Export de la cohorte Plan Pauvreté > Stock > Non inscrits PE > Rendez-vous élaboration CER', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_stock');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_stock';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_nouveaux','{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}','Export CSV de la cohorte Plan Pauvreté > Stock > Non inscrits PE > Rendez-vous élaboration CER', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_nouveaux');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_nouveaux';

-- Impression rendez-vous élaboration CER
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_imprime_stock','{"filters":{"defaults":{"Dossier":{"dernier":1},"Rendezvous":{"daterdv":0,"daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Referent.nom_complet":{"sort":false},"Typerdv.libelle":{"sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"Statutrdv.libelle":{"sort":false},"Canton.canton":{"sort":false},"/Dossiers/view/#Dossier.id#":{"class":"view external"},"/Rendezvous/impression/#Rendezvous.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_TROISIEME_RDV_STOCK","Statutrdv.code_statut":"PREVU"},"save":[]}},"ini_set":[]}','Cohorte Plan Pauvreté > Stock > Non inscrits PE > Impression rendez-vous élaboration CER

		array(
			 1. Filtres de recherche
			''filters'' => Configure::read (''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.filters''),
			 2. Recherche
			''query'' => Configure::read (''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.query''),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => Configure::read (''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.results''),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Valeurs à utiliser pour le préremplissage de la cohorte
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_TROISIEME_RDV_STOCK'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_imprime_stock');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_imprime_stock';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_imprime_nouveaux','{"filters":{"defaults":{"Dossier":{"dernier":1},"Rendezvous":{"daterdv":0,"daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Structurereferente.lib_struc":{"sort":false},"Referent.nom_complet":{"sort":false},"Typerdv.libelle":{"sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"Statutrdv.libelle":{"sort":false},"Canton.canton":{"sort":false},"/Dossiers/view/#Dossier.id#":{"class":"view external"},"/Rendezvous/impression/#Rendezvous.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_TROISIEME_RDV_NOUVEAUX","Statutrdv.code_statut":"PREVU"},"save":[]}},"ini_set":[]}','Cohorte Plan Pauvreté > Stock > Non inscrits PE > Impression rendez-vous élaboration CER

		array(
			 1. Filtres de recherche
			''filters'' => Configure::read (''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.filters''),
			 2. Recherche
			''query'' => Configure::read (''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.query''),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => Configure::read (''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.results''),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Valeurs à utiliser pour le préremplissage de la cohorte
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_TROISIEME_RDV_NOUVEAUX'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_imprime_nouveaux');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_imprime_nouveaux';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_imprime_stock','{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}','Export de la cohorte Plan Pauvreté > Stock > Non inscrits PE > Rendez-vous élaboration CER', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_imprime_stock');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_imprime_stock';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_imprime_nouveaux','{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}','Export CSV de la cohorte Plan Pauvreté > Stock > Non inscrits PE > Rendez-vous élaboration CER', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_imprime_nouveaux');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_imprime_nouveaux';

-- Convoqués rendez-vous élaboration CER
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_venu_nonvenu_stock','{"filters":{"defaults":{"Dossier":{"dernier":1},"Rendezvous":{"daterdv":0,"daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet_prenoms":{"sort":false},"Adresse.complete":{"sort":false},"Canton.canton":{"sort":false},"Modecontact.numtel":{"class":"numtelCAF","sort":false},"Personne.numport":{"class":"numtelCD","sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"/Dossiers/view/#Dossier.id#":{"class":"view external"},"/Personnes/coordonnees/#Personne.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_TROISIEME_RDV_STOCK","Statutrdv.code_statut":"PREVU"},"save":{"Typerdv.code_type":"INFO_COLL_TROISIEME_RDV_STOCK","Statutrdv.code_statut":""}}},"ini_set":[]}','Cohorte Plan Pauvreté > Stock > Non inscrits PE > Convoqués rendez-vous élaboration CER

		array(
			 1. Filtres de recherche
			''filters'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.filters''),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			''results'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.results''),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_TROISIEME_RDV_STOCK'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_TROISIEME_RDV_STOCK'',
						''Statutrdv.code_statut'' => ''''
					),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_venu_nonvenu_stock');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_venu_nonvenu_stock';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_venu_nonvenu_nouveaux','{"filters":{"defaults":{"Dossier":{"dernier":1},"Rendezvous":{"daterdv":0,"daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet_prenoms":{"sort":false},"Adresse.complete":{"sort":false},"Canton.canton":{"sort":false},"Modecontact.numtel":{"class":"numtelCAF","sort":false},"Personne.numport":{"class":"numtelCD","sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"/Dossiers/view/#Dossier.id#":{"class":"view external"},"/Personnes/coordonnees/#Personne.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_TROISIEME_RDV_NOUVEAUX","Statutrdv.code_statut":"PREVU"},"save":{"Typerdv.code_type":"INFO_COLL_TROISIEME_RDV_NOUVEAUX","Statutrdv.code_statut":""}}},"ini_set":[]}','Cohorte Plan Pauvreté > Stock > Non inscrits PE > Convoqués rendez-vous élaboration CER

		array(
			 1. Filtres de recherche
			''filters'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.filters''),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			''results'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.results''),
			 Configuration du formulaire de cohorte
			''cohorte'' => array(
				 Remplacement des options dans la cohorte
				''options'' => array(),
				 Valeurs à remplir dans les champs de la cohorte avant de les cacher
				''values'' => array(
				),
				 Configuration des paramètres
				''config'' => array(
					 Valeurs utilisées pour la recherche de la cohorte
					''recherche'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_TROISIEME_RDV_NOUVEAUX'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(
						''Typerdv.code_type'' => ''INFO_COLL_TROISIEME_RDV_NOUVEAUX'',
						''Statutrdv.code_statut'' => ''''
					),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_venu_nonvenu_nouveaux');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_rdv_cer_venu_nonvenu_nouveaux';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_venu_nonvenu_stock','{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}','Export de la cohorte Plan Pauvreté > Stock > Non inscrits PE > Rendez-vous élaboration CER', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_venu_nonvenu_stock');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_venu_nonvenu_stock';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_venu_nonvenu_nouveaux','{"filters":{"defaults":{"Dossier":{"dernier":"1"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}','Export CSV de la cohorte Plan Pauvreté > Stock > Non inscrits PE > Rendez-vous élaboration CER', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_venu_nonvenu_nouveaux');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_rdv_cer_venu_nonvenu_nouveaux';

-- Ajout de la table versionpatchsql
CREATE TABLE IF NOT EXISTS public.versionpatchsql (
	id serial NOT NULL,
	"version" varchar NOT NULL,
	created timestamp NOT NULL,
	CONSTRAINT versionpatchsql_pkey PRIMARY KEY (id)
);

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.17.0', CURRENT_TIMESTAMP);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************