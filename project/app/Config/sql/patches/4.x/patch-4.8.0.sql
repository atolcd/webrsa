SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Ajout du module de modification de l'état de dossier
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
	VALUES
	('Module.ModifEtatDossier.enabled', 'true', 'Activation de la modification de l''état du dossier', current_timestamp, current_timestamp),
	('Module.ModifEtatDossier.etatdos', '{"5":"Droit clos","6":"Droit clos sur mois antérieur ayant eu des créances transferées ou une régularisation dans le mois de référence pour une période antérieure."}', 'Liste des états possibles pour la modification de l''état des dossiers.
"Z" : "Non défini"
"0" : "Nouvelle demande en attente de décision CD pour ouverture du droit"
"1" : "Droit refusé"
"2" : "Droit ouvert et versable"
"3" : "Droit ouvert et suspendu (le montant du droit est calculable, mais l''existence du droit est remis en cause)"
"4" : "Droit ouvert mais versement suspendu (le montant du droit n''est pas calculable)"
"5" : "Droit clos"
"6" : "Droit clos sur mois antérieur ayant eu des créances transferées ou une régularisation dans le mois de référence pour une période antérieure."', current_timestamp, current_timestamp);


UPDATE public.configurations SET configurationscategorie_id = configurationscategories.id FROM configurationscategories WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.ModifEtatDossier.enabled';
UPDATE public.configurations SET configurationscategorie_id = configurationscategories.id FROM configurationscategories WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.ModifEtatDossier.etatdos';

-- Ajout du paramétrage pour la modification de l'état de dossier
CREATE TABLE motifsetatsdossiers (
	id serial NOT NULL,
	lib_motif varchar(250) NOT NULL,
	actif int2 NOT NULL DEFAULT 0,
	created timestamp NOT NULL,
	modified timestamp NOT NULL,
	CONSTRAINT motifsetatsdossiers_pkey PRIMARY KEY (id),
	CONSTRAINT motifsetatsdossiers_un UNIQUE (lib_motif)
);

-- Modification de la table historiquesdroits
ALTER TABLE public.historiquesdroits ADD nom varchar(50);
ALTER TABLE public.historiquesdroits ADD prenom varchar(50);
ALTER TABLE public.historiquesdroits ADD motif varchar(255);

-- Cohorte modification de l'état de dossier
INSERT INTO public.configurationscategories (lib_categorie) VALUES('Modifsetatsdossiers');

INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
	VALUES
	('ConfigurableQuery.Modifsetatsdossiers.cohorte_modifetatdos', '{"filters":{"defaults":{"Calculdroitrsa":{"toppersdrodevorsa":""},"Dossier":{"dernier":"0","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":[],"has":{"0":"Dsp","Contratinsertion":{"Contratinsertion.decision_ci":"V"},"Orientstruct":{"Orientstruct.statut_orient":"Orienté"}}},"query":{"restrict":[],"conditions":[],"order":["Personne.nom"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Dossier.dtdemrsa","2":"Personne.nir","3":"Situationdossierrsa.etatdosrsa","4":"Personne.nom_complet_prenoms","5":"Adresse.nomcom","Dossier.locked":{"type":"boolean","class":"dossier_locked"},"\/Dossiers\/view\/#Dossier.id#":{"class":"view external"}},"innerTable":{"0":"Dossier.matricule","1":"Personne.dtnai","2":"Prestation.rolepers","3":"Structurereferenteparcours.lib_struc","4":"Referentparcours.nom_complet","5":"Activite.act","6":"Personne.etat_dossier_orientation","Adresse.numcom":{"options":[]}}},"cohorte":{"options":[],"values":[],"config":{"recherche":[],"save":[]}},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Menu "Gestion de liste / Cohorte" > "Modification d''état des dossiers"

		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Calculdroitrsa'' => array(
						''toppersdrodevorsa'' => ''
					),
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''0'',
						 Case à cocher "Filtrer par date de demande RSA"
						''dtdemrsa'' => ''0'',
						 Du (inclus)
						''dtdemrsa_from'' => ''TAB::-1WEEK'',
						 Au (inclus)
						''dtdemrsa_to'' => ''TAB::NOW'',
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array(),
				 1.4 Filtres additionnels : La personne possède un(e)...
				''has'' => array(
					''Dsp'',
					''Contratinsertion'' => array(
						''Contratinsertion.decision_ci'' => ''V''
					),
					''Orientstruct'' => array(
						''Orientstruct.statut_orient'' => ''Orienté'',
					),
				)
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array( ''Personne.nom'' )
			),
			 3. Nombre d''enregistrements par page
			''limit'' => 10,
			 4. Lancer la recherche au premier accès à la page ?
			''auto'' => false,
			 5. Résultats de la recherche
			''results'' => array(
				 5.1 Ligne optionnelle supplémentaire d''en-tête du tableau de résultats
				''header'' => array(),
				 5.2 Colonnes du tableau de résultats
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nom_complet_prenoms'',
					''Adresse.nomcom'',
					''Dossier.locked'' => array(
						''type'' => ''boolean'',
						''class'' => ''dossier_locked''
					),
					''Dossiersview#Dossier.id#''
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.matricule'',
					''Personne.dtnai'',
					''Adresse.numcom'' => array(
						''options'' => array()
					),
					''Prestation.rolepers'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Activite.act'',  CG 58
					''Personne.etat_dossier_orientation'',  CG 58
				)
			),
			// Options de la cohorte
			''cohorte'' => array(
				''options'' => array()
				''values'' => array()
				''config'' => array(
					''recherche'' => array(),
					''save'' => array()
				}
			},
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''512M''
			)
		)', current_timestamp, current_timestamp),
	('ConfigurableQuery.Modifsetatsdossiers.exportcsv_modifetatdos', '{"filters":{"defaults":{"Calculdroitrsa":{"toppersdrodevorsa":""},"Dossier":{"dernier":"0","dtdemrsa":"0","dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":[],"has":{"0":"Dsp","Contratinsertion":{"Contratinsertion.decision_ci":"V"},"Orientstruct":{"Orientstruct.statut_orient":"Orienté"}}},"query":{"restrict":[],"conditions":[],"order":["Personne.nom"]},"results":{"fields":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.nir","Situationdossierrsa.etatdosrsa","Personne.nom_complet_prenoms","Personne.dtnai","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.complideadr","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Typeorient.lib_type_orient","Personne.idassedic","Dossier.matricule","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Personne.sexe","Dsp.natlog","Activite.act","Personne.etat_dossier_orientation"]},"ini_set":{"max_execution_time":0,"memory_limit":"512M"}}', 'Export CSV, menu "Gestion de liste / Cohorte" > "Modification d''état des dossiers"

		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Dossiers.search.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Dossiers.search.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Personne.nom_complet_prenoms'',
					''Personne.dtnai'',
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Typeorient.lib_type_orient'',
					''Personne.idassedic'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Personne.sexe'',
					''Dsp.natlog'',
					''Activite.act'',  CG 58
					''Personne.etat_dossier_orientation''  CG 58
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Dossiers.search.ini_set'' ),
		)', current_timestamp, current_timestamp);

UPDATE public.configurations SET configurationscategorie_id = configurationscategories.id FROM configurationscategories WHERE configurationscategories.lib_categorie = 'Modifsetatsdossiers' AND configurations.lib_variable LIKE 'ConfigurableQuery.Modifsetatsdossiers.cohorte_modifetatdos';
UPDATE public.configurations SET configurationscategorie_id = configurationscategories.id FROM configurationscategories WHERE configurationscategories.lib_categorie = 'Modifsetatsdossiers' AND configurations.lib_variable LIKE 'Module.ModifEtatDossier.etatdos';

-- PPAE
ALTER TABLE public.sanctionseps58 DROP CONSTRAINT sanctionseps58_origine_in_list_chk;
ALTER TABLE public.sanctionseps58 ADD CONSTRAINT sanctionseps58_origine_in_list_chk CHECK (cakephp_validate_in_list((origine)::text, ARRAY['radiepe'::text, 'noninscritpe'::text, 'nonrespectcer'::text, 'nonrespectppae'::text]));
ALTER TABLE public.sanctionseps58 ALTER COLUMN origine TYPE varchar(15) USING origine::varchar;
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, created, modified)
VALUES('Commissionseps.sanctionep.nonrespectppae', 'true', 'Permet les sanctions pour non respect du PPAE', current_timestamp, current_timestamp );
UPDATE public.configurations SET configurationscategorie_id = configurationscategories.id FROM configurationscategories WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Commissionseps.sanctionep.nonrespectppae';
Commissionseps.ppae
-- *****************************************************************************
COMMIT;
-- *****************************************************************************