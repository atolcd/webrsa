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

-- Insertion des nouvelles configurations pour la nouvelle cohorte du plan pauvreté / convoqués 3 en 1
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_venu_nonvenu_second_rdv_stock', 'true', 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrits PE > Convoqués rendez-vous 3 en 1', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_venu_nonvenu_second_rdv_stock');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_venu_nonvenu_second_rdv_stock';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_venu_nonvenu_second_rdv_nouveaux', 'true', 'Accès à la cohorte Plan Pauvreté > Nouveaux Entrants > Non inscrits PE > Convoqués rendez-vous 3 en 1', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_venu_nonvenu_second_rdv_nouveaux');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_venu_nonvenu_second_rdv_nouveaux';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_second_rdv_stock','{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet_prenoms":{"sort":false},"Adresse.complete":{"sort":false},"Canton.canton":{"sort":false},"Modecontact.numtel":{"class":"numtelCAF","sort":false},"Personne.numport":{"class":"numtelCD","sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Personnes\/coordonnees\/#Personne.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_SECOND_RDV_STOCK","Statutrdv.code_statut":"PREVU"},"save":{"Typerdv.code_type":"","Statutrdv.code_statut":""}}},"ini_set":[]}','Cohorte Stock - Convoqués 3 en 1 - Venu Non venu

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
						''Statutrdv.code_statut'' => ''PREVU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (si il y a)
					''save'' => array(
						''Typerdv.code_type'' => '''',
						''Statutrdv.code_statut'' => ''''
					),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_second_rdv_stock');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_second_rdv_stock';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_second_rdv_nouveaux','{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"header":[],"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Personne.nom_complet_prenoms":{"sort":false},"Adresse.complete":{"sort":false},"Canton.canton":{"sort":false},"Modecontact.numtel":{"class":"numtelCAF","sort":false},"Personne.numport":{"class":"numtelCD","sort":false},"Rendezvous.daterdv":{"sort":false},"Rendezvous.heurerdv":{"sort":false},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"},"\/Personnes\/coordonnees\/#Personne.id#":{"class":"view external"}},"innerTable":[]},"cohorte":{"options":[],"values":[],"config":{"recherche":{"Typerdv.code_type":"INFO_COLL_SECOND_RDV_NOUVEAU","Statutrdv.code_statut":"PREVU"},"save":{"Typerdv.code_type":"","Statutrdv.code_statut":""}}},"ini_set":[]}','Cohorte Nouveaux entrants - Convoqué 3 en 1 - Venu  Non venu


		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					),
					''Rendezvous'' => array(
						 Case à cocher "Filtrer par date de RDV"
						''daterdv'' => ''0'',
						 Du (inclus)
						''daterdv_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''daterdv_to'' => ''TAB::NOW'',
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => Configure::read(''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_imprime.filters.skip''),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array(''Personne.id'')
			),
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array (
					''Personne.id'' => array (''hidden'' => true),
					''Dossier.numdemrsa'' => array ( ''sort'' => false ),
					''Dossier.matricule'' => array ( ''sort'' => false ),
					''Dossier.dtdemrsa'' => array ( ''sort'' => false ),
					''Personne.nom_complet_prenoms'' => array ( ''sort'' => false ),
					''Adresse.complete'' => array ( ''sort'' => false ),
					''Canton.canton'' => array ( ''sort'' => false ),
					''Modecontact.numtel'' => array(''class'' => ''numtelCAF'', ''sort'' => false ),
					''Personne.numport'' => array(''class'' => ''numtelCD'', ''sort'' => false ),
					''Rendezvous.daterdv'' => array ( ''sort'' => false ),
					''Rendezvous.heurerdv'' => array ( ''sort'' => false ),
					''Dossiersview#Dossier.id#'' => array( ''class'' => ''view external'' ),
					''Personnescoordonnees#Personne.id#'' => array( ''class'' => ''view external'' ),
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
				)
			),
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
						''Typerdv.code_type'' => ''INFO_COLL_SECOND_RDV_NOUVEAU'',
						''Statutrdv.code_statut'' => ''PREVU''
					),
					 Valeurs utilisées pour la sauvegarde de la cohorte & préremplissage de la cohorte (s''il y a)
					''save'' => array(
						''Typerdv.code_type'' => '''',
						''Statutrdv.code_statut'' => ''''
					),
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array()
		)', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_second_rdv_nouveaux');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_second_rdv_nouveaux';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_venu_nonvenu_second_rdv_stock','{"filters":{"defaults":{"Dossier":{"dernier":1},"Rendezvous":{"daterdv":0,"daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}','Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_stock.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_stock.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
					''fields'' => array(
						''Personne.id'' => array (''hidden'' => true),
						''Dossier.numdemrsa'' => array ( ''sort'' => false ),
						''Personne.nom_complet_court'' => array ( ''sort'' => false ),
						''Adresse.nomcom'' => array ( ''sort'' => false ),
						''Personne.dtnai'' => array ( ''sort'' => false ),
						''Dossier.matricule'' => array ( ''sort'' => false ),
						''Personne.nir'' => array ( ''sort'' => false ),
						''Adresse.codepos'' => array ( ''sort'' => false ),
						''Adresse.numcom'' => array ( ''sort'' => false ),
						''Canton.canton'' => array ( ''sort'' => false ),
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_stock.ini_set'' ),
		)', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_venu_nonvenu_second_rdv_stock');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_venu_nonvenu_second_rdv_stock';

INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_venu_nonvenu_second_rdv_nouveaux','{"filters":{"defaults":{"Dossier":{"dernier":"1"},"Rendezvous":{"daterdv":"0","daterdv_from":"TAB::-1WEEK","daterdv_to":"TAB::NOW"}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Dossier.numdemrsa","Dossier.matricule","Dossier.dernier","Detailcalculdroitrsa.natpf","Dossier.anciennete_dispositif","Dossier.fonorg","Adresse.nomvoie","Adresse.nomcom","Adresse.numcom","Canton.canton","Personne.nom","Personne.nomnai","Personne.prenom","Prestation.rolepers","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Dossier.numdemrsa":{"sort":false},"Personne.nom_complet_court":{"sort":false},"Adresse.nomcom":{"sort":false},"Personne.dtnai":{"sort":false},"Dossier.matricule":{"sort":false},"Personne.nir":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false},"Canton.canton":{"sort":false}}},"ini_set":[]}','Export CSV


		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
					''fields'' => array(
						''Personne.id'' => array (''hidden'' => true),
						''Dossier.numdemrsa'' => array ( ''sort'' => false ),
						''Personne.nom_complet_court'' => array ( ''sort'' => false ),
						''Adresse.nomcom'' => array ( ''sort'' => false ),
						''Personne.dtnai'' => array ( ''sort'' => false ),
						''Dossier.matricule'' => array ( ''sort'' => false ),
						''Personne.nir'' => array ( ''sort'' => false ),
						''Adresse.codepos'' => array ( ''sort'' => false ),
						''Adresse.numcom'' => array ( ''sort'' => false ),
						''Canton.canton'' => array ( ''sort'' => false ),
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Planpauvreterendezvous.cohorte_infocol_venu_nonvenu_nouveaux.ini_set'' ),
		)', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_venu_nonvenu_second_rdv_nouveaux');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'Planpauvreterendezvous' AND configurations.lib_variable LIKE 'ConfigurableQuery.Planpauvreterendezvous.exportcsv_infocol_venu_nonvenu_second_rdv_nouveaux';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************