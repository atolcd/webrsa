SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************


-- Update de la variable de configuration pour l'activation du module algorithme d'orientation
UPDATE public.configurations SET value_variable = 'true' WHERE lib_variable LIKE 'Module.AlgorithmeOrientation.enabled';


-- Ajout du type d'orientation Association référente
INSERT INTO public.typesorients (lib_type_orient)
SELECT 'Association référente'
WHERE NOT EXISTS (SELECT id FROM typesorients WHERE lib_type_orient LIKE 'Association référente');

UPDATE public.typesorients t
SET parentid = t1.id
FROM typesorients t1
WHERE t1.lib_type_orient = 'Social' AND t.lib_type_orient LIKE 'Association référente';

-- Rattrapage des orientés association référente
UPDATE orientsstructs
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Association référente')
WHERE structurereferente_id IN (
	SELECT id
	FROM structuresreferentes s
	WHERE s.lib_struc = 'Emmaüs Alternatives'
	OR s.lib_struc = 'Association FAIRE'
	OR s.lib_struc = 'ADEPT'
);

-- Update de la variable de configuration pour le paramétrage des seuils de l'algorithme d'orientation
UPDATE public.configurations SET value_variable = '{"agemin" : [18,62],"agemax" : [30,140],"nbenfants" : [1],"nbmois" : [6]}',
comments_variable = 'Seuils disponibles dans les critères de l''algorithme d''orientation
{
	//Age minimum
	"agemin" : [
		18,
		62
	],
	//age maximum
	"agemax" : [
		30,
		140
	],
	//nombre d''enfants
	"nbenfants" : [
		1
	],
	//nombre de mois
	"nbmois" : [
		6
	]
}'
WHERE lib_variable LIKE 'Module.AlgorithmeOrientation.seuils';


-- Création de la variable de configuration pour le formulaire de recherche de l'algorithme d'orientation
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Algorithmeorientation.orientation',
'{"filters":{"defaults":{"Dossier":{"dernier":1,"dtdemrsa":1,"dtdemrsa_from":{"year":2012,"month":1,"day":1}},"Situationdossierrsa":{"etatdosrsa_choice":1,"etatdosrsa":[2]},"Detaildroitrsa":{"oridemrsa_choice":1,"oridemrsa":["DEM","RMI","API"]},"Calculdroitrsa":{"toppersdrodevorsa":1},"Orientstruct":{"statut_orient":"Non orienté"}},"accepted":[],"skip":["PersonneReferent.communautesr_id","PersonneReferent.structurereferente_id","PersonneReferent.referent_id"]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom","2":"Personne.prenom","3":"Dossier.dtdemrsa","4":"Dossier.matricule","5":"Adresse.numvoie","6":"Adresse.libtypevoie","7":"Adresse.nomvoie","8":"Adresse.nomcom","9":"Adresse.codepos","10":"Adresse.compladr","/Adressesfoyers/index/#Foyer.id#":{"class":"view external"}},"innerTable":["Situationdossierrsa.etatdosrsa","Personne.nomcomnai","Personne.dtnai","Adresse.numcom","Personne.nir","Historiqueetatpe.identifiantpe","Modecontact.numtel","Prestation.rolepers","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}',
'Menu "Cohortes" > "Orientation" > "Algorithme et Transferts" > "Algorithme d''orientation"

		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					),
					''Detailcalculdroitrsa'' => array(
						''natpf_choice'' => ''1'',
						''natpf'' => array( ''RSD'', ''RSI'' )
					),
					''Detaildroitrsa'' => array(
						''oridemrsa_choice'' => ''1'',
						''oridemrsa'' => array( ''DEM'',"RMI","API" )
					),
					''Situationdossierrsa'' => array(
						''etatdosrsa_choice'' => ''1'',
						''etatdosrsa'' => array( 2)
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
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
						"0": "Dossier.numdemrsa",
			                        "1": "Personne.nom",
			                        "2": "Personne.prenom",
			                        "3": "Dossier.dtdemrsa",
			                        "4": "Dossier.matricule",
			                        "5": "Adresse.numvoie",
			                        "6": "Adresse.libtypevoie",
			                        "7": "Adresse.nomvoie",
			                        "8": "Adresse.nomcom",
			                        "9": "Adresse.codepos",
			                       "10": "Adresse.compladr",
			                       "/Adressesfoyers/index/#Foyer.id#": {
				                       "class": "view"
			                       }
					)
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.dtnai'',
					''Dossier.matricule'',
					''Personne.nir'',
					''Adresse.codepos'',
					''Situationdossierrsa.dtclorsa'',
					''Situationdossierrsa.moticlorsa'',
					''Prestation.rolepers'',
					''Situationdossierrsa.etatdosrsa'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''1024M''
			)',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.orientation');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.orientation';


-- Création de la variable de configuration pour l'export csv des adresses problématiques de l'algorithme d'orientation
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Algorithmeorientation.exportcsv_adresses',
'{"filters":{"defaults":{"Dossier":{"dernier":1}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Personne.nom":{"sort":false},"Personne.prenom":{"sort":false},"Personne.dtnai":{"sort":false},"Personne.nir":{"sort":false},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Adresse.numvoie":{"sort":false},"Adresse.libtypevoie":{"sort":false},"Adresse.nomvoie":{"sort":false},"Adresse.nomcom":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.numcom":{"sort":false}}},"ini_set":[]}',
'Export CSV, Algorithme d''orientation - Adresses problématiques

		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Prestation.natprest'',
					''Calculdroitrsa.toppersdrodevorsa'',
					''Personne.nom_complet_prenoms'',
					''Personne.qual'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Personne.age'' => array( ''label'' => ''Age'' ),
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Personne.email'',
					''Personne.numfixe'',
					''Typeorient.lib_type_orient'',
					''Personne.idassedic'',
					''Dsp.inscdememploi'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Personne.sexe'',
					''Dsp.inscdememploi'',
					''Dsp.natlog'' ,
					''Dsp.nivetu''
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.ini_set'' ),
		)',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.exportcsv_adresses');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.exportcsv_adresses';


-- Création de la variable de configuration pour l'export csv des orientables
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Algorithmeorientation.exportcsv_orientables',
'{"filters":{"defaults":{"Dossier":{"dernier":1}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Personne.qual":{"sort":false},"Personne.nom":{"sort":false},"Personne.prenom":{"sort":false},"Personne.dtnai":{"sort":false},"Personne.nir":{"sort":false},"Personne.sexe":{"sort":false},"Personne.age":{"sort":false},"Adresse.numvoie":{"sort":false},"Adresse.libtypevoie":{"sort":false},"Adresse.nomvoie":{"sort":false},"Adresse.compladr":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.nomcom":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Prestation.rolepers":{"sort":false},"Foyer.sitfam":{"sort":false},"Detaildroitrsa.nbenfautcha":{"sort":false},"Modecontact.numtel":{"sort":false},"Modecontact.numposte":{"sort":false},"Modecontact.adrelec":{"sort":false}}},"ini_set":[]}',
'Export CSV, Algorithme d''orientation - Orientables

		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Prestation.natprest'',
					''Calculdroitrsa.toppersdrodevorsa'',
					''Personne.nom_complet_prenoms'',
					''Personne.qual'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Personne.age'' => array( ''label'' => ''Age'' ),
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Personne.email'',
					''Personne.numfixe'',
					''Typeorient.lib_type_orient'',
					''Personne.idassedic'',
					''Dsp.inscdememploi'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Personne.sexe'',
					''Dsp.inscdememploi'',
					''Dsp.natlog'' ,
					''Dsp.nivetu''
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.ini_set'' ),
		)',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.exportcsv_orientables');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.exportcsv_orientables';

-- Création de la variable de configuration pour l'export csv des orientations
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Algorithmeorientation.exportcsv_orientations',
'{"filters":{"defaults":{"Dossier":{"dernier":1}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Personne.qual":{"sort":false},"Personne.nom":{"sort":false},"Personne.prenom":{"sort":false},"Personne.dtnai":{"sort":false},"Personne.nir":{"sort":false},"Personne.sexe":{"sort":false},"Personne.age":{"sort":false},"Adresse.numvoie":{"sort":false},"Adresse.libtypevoie":{"sort":false},"Adresse.nomvoie":{"sort":false},"Adresse.compladr":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.nomcom":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Prestation.rolepers":{"sort":false},"Propositionorientation.structure_libelle":{"sort":false},"Propositionorientation.structure_ville":{"sort":false},"Propositionorientation.lib_type_orient":{"sort":false},"Foyer.sitfam":{"sort":false},"Detaildroitrsa.nbenfautcha":{"sort":false},"Modecontact.numtel":{"sort":false},"Modecontact.numposte":{"sort":false},"Modecontact.adrelec":{"sort":false}}},"ini_set":[]}',
'Export CSV, Algorithme d''orientation - Orientations

		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Prestation.natprest'',
					''Calculdroitrsa.toppersdrodevorsa'',
					''Personne.nom_complet_prenoms'',
					''Personne.qual'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Personne.age'' => array( ''label'' => ''Age'' ),
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Personne.email'',
					''Personne.numfixe'',
					''Typeorient.lib_type_orient'',
					''Personne.idassedic'',
					''Dsp.inscdememploi'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Personne.sexe'',
					''Dsp.inscdememploi'',
					''Dsp.natlog'' ,
					''Dsp.nivetu''
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.ini_set'' ),
		)',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.exportcsv_orientations');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.exportcsv_orientations';


-- Création de la variable de configuration pour l'export csv des depassements
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Algorithmeorientation.exportcsv_depassement',
'{"filters":{"defaults":{"Dossier":{"dernier":1}},"accepted":[],"skip":["Calculdroitrsa.toppersdrodevorsa","Dossier.dtdemrsa","Detaildroitrsa.oridemrsa","Foyer.sitfam","Adresse.nomvoie","Personne.dtnai","Personne.dtnai_month","Personne.dtnai_year","Personne.nir","Personne.sexe","Personne.trancheage","Situationdossierrsa.etatdosrsa","Serviceinstructeur.id","Suiviinstruction.typeserins","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","Prestation.rolepers","ByTag.tag_choice"],"has":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"results":{"fields":{"Personne.id":{"hidden":true},"Personne.qual":{"sort":false},"Personne.nom":{"sort":false},"Personne.prenom":{"sort":false},"Personne.dtnai":{"sort":false},"Personne.nir":{"sort":false},"Personne.sexe":{"sort":false},"Personne.age":{"sort":false},"Adresse.numvoie":{"sort":false},"Adresse.libtypevoie":{"sort":false},"Adresse.nomvoie":{"sort":false},"Adresse.compladr":{"sort":false},"Adresse.codepos":{"sort":false},"Adresse.nomcom":{"sort":false},"Dossier.dtdemrsa":{"sort":false},"Dossier.numdemrsa":{"sort":false},"Dossier.matricule":{"sort":false},"Prestation.rolepers":{"sort":false},"Foyer.sitfam":{"sort":false},"Detaildroitrsa.nbenfautcha":{"sort":false},"Modecontact.numtel":{"sort":false},"Modecontact.numposte":{"sort":false},"Modecontact.adrelec":{"sort":false}}},"ini_set":[]}',
'Export CSV, Algorithme d''orientation - Dépassements de capacité maximale

		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Prestation.natprest'',
					''Calculdroitrsa.toppersdrodevorsa'',
					''Personne.nom_complet_prenoms'',
					''Personne.qual'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Personne.age'' => array( ''label'' => ''Age'' ),
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Personne.email'',
					''Personne.numfixe'',
					''Typeorient.lib_type_orient'',
					''Personne.idassedic'',
					''Dsp.inscdememploi'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Personne.sexe'',
					''Dsp.inscdememploi'',
					''Dsp.natlog'' ,
					''Dsp.nivetu''
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.ini_set'' ),
		)',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.exportcsv_depassement');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.exportcsv_depassement';


-- Création de la variable de configuration permettant de configurer les variables dans la requête
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Algorithmeorientation.Parametragerequete',
'{"natlog":"''0903'',''0904'', ''0905'',''0906'',''0907'',''0908'',''0909'',''0910'',''0911'',''0912'', ''0913''","sitfam":"''CEL'', ''DIV'', ''ISO'', ''SEF'', ''SEL'', ''VEU''","natpf":"''RSI'', ''RCI''","sousnatpf":"''RSIN1''"}',
'Algorithme d''orientation

"natlog": Codes des logements correspondant à des logements d''urgence ou logements précaires (critère est dans un logement d’urgence, temporaire ou précaire et avec au moins un enfant à charge ?  )
"sitfam": Codes des situations familiales correpondant à un foyer monoparental
"natpf" et "sousnatpf": Codes des natures de prestations correspondant à un RSA Majoré
}',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Algorithmeorientation.Parametragerequete');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Algorithmeorientation.Parametragerequete';

-- Création de la variable de configuration permettant de configurer les variables pour les graphiques
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Algorithmeorientation.Parametragegraphiques',
'{"tranches_ages":[18,30,50,65]}',
'Algorithme d''orientation - Tranches d''âge pour les graphiques

{
	"tranches_ages": [
		18,
		30,
		50,
		65
	]
}',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Algorithmeorientation.Parametragegraphiques');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Algorithmeorientation.Parametragegraphiques';

-- Création de la variable de configuration stockant l'id du typeorient Service Social
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Typeorient.service_social_id', (select t.id from typesorients t where t.lib_type_orient ilike 'Service social'), 'id du typeorient Service Social',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Typeorient.service_social_id');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Typeorient.service_social_id';

-- Création de la variable de configuration stockant l'id du typeorient Association référente
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Typeorient.asso_referente_id', (select t.id from typesorients t where t.lib_type_orient = 'Association référente'), 'id du typeorient Association référente',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Typeorient.asso_referente_id');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Typeorient.asso_referente_id';


-- Update de la variable de configuration stockant l'id du typeorient Pole emploi
UPDATE public.configurations
SET value_variable = (select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'), comments_variable = 'Id du typeorient Pole Emploi'
WHERE configurations.lib_variable LIKE 'Typeorient.emploi_id';

--Création de la variable de configuration pour la recherche des nouveaux orientés
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Algorithmeorientation.search',
'{"filters":{"defaults":{"Situationdossierrsa":{"etatdosrsa_choice":0,"etatdosrsa":[0,2,3,4]},"Orientstruct":{"date_valid":1,"origine":"cohorte"}},"accepted":[],"skip":["PersonneReferent.communautesr_id","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","ByTag.tag_choice","Personne.dtnai","Personne.nom","Personne.nomnai","Personne.prenom","Personne.nir","Personne.sexe","Personne.trancheage","Personne.trancheagesup","Personne.trancheageprec","Calculdroitrsa.toppersdrodevorsa"]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Personne.qual","1":"Personne.nom","2":"Personne.prenom","3":"Personne.nir","4":"Personne.dtnai","5":"Personne.sexe","6":"Personne.age","7":"Adresse.numvoie","8":"Adresse.libtypevoie","9":"Adresse.compladr","10":"Adresse.codepos","11":"Adresse.nomcom","12":"Dossier.dtdemrsa","13":"Dossier.numdemrsa","14":"Prestation.rolepers","15":"Orientstruct.date_valid","16":"Orientstruct.date_impression","17":"Structurereferente.ville","18":"Structurereferente.lib_struc","19":"Foyer.sitfam","20":"Orientstruct.origine","21":"Detaildroitrsa.nbenfautcha","22":"Modecontact.numtel","23":"Modecontact.numposte","24":"Modecontact.adrelec","/Orientsstructs/index/#Orientstruct.personne_id#":{"disabled":"( ''#Orientstruct.horszone#'' == true )","class":"view external"}},"innerTable":["Situationdossierrsa.etatdosrsa","Personne.nomcomnai","Personne.dtnai","Adresse.numcom","Personne.nir","Historiqueetatpe.identifiantpe","Modecontact.numtel","Prestation.rolepers","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}',
'Menu "Cohortes" > "Orientation" > "Algorithme et Transferts" > "Liste des nouveaux orientés"

		array(
			 1. Filtres de recherche
			''filters'' => array(
				 1.1 Valeurs par défaut des filtres de recherche
				''defaults'' => array(
					''Dossier'' => array(
						 Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						''dernier'' => ''1''
					),
					''Detailcalculdroitrsa'' => array(
						''natpf_choice'' => ''1'',
						''natpf'' => array( ''RSD'', ''RSI'' )
					),
					''Detaildroitrsa'' => array(
						''oridemrsa_choice'' => ''1'',
						''oridemrsa'' => array( ''DEM'',"RMI","API" )
					),
					''Situationdossierrsa'' => array(
						''etatdosrsa_choice'' => ''1'',
						''etatdosrsa'' => array( 2)
					)
				),
				 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				''accepted'' => array(),
				 1.3 Ne pas afficher ni traiter certains filtres de recherche
				''skip'' => array()
			),
			 2. Recherche
			''query'' => array(
				 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				''restrict'' => array(),
				 2.2 Conditions supplémentaires optionnelles
				''conditions'' => array(),
				 2.3 Tri par défaut
				''order'' => array()
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
						"0": "Dossier.numdemrsa",
			                        "1": "Personne.nom",
			                        "2": "Personne.prenom",
			                        "3": "Dossier.dtdemrsa",
			                        "4": "Dossier.matricule",
			                        "5": "Adresse.numvoie",
			                        "6": "Adresse.libtypevoie",
			                        "7": "Adresse.nomvoie",
			                        "8": "Adresse.nomcom",
			                        "9": "Adresse.codepos",
			                       "10": "Adresse.compladr",
			                       "/Adressesfoyers/index/#Foyer.id#": {
				                       "class": "view"
			                       }
					)
				),
				 5.3 Infobulle optionnelle du tableau de résultats
				''innerTable'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.dtnai'',
					''Dossier.matricule'',
					''Personne.nir'',
					''Adresse.codepos'',
					''Situationdossierrsa.dtclorsa'',
					''Situationdossierrsa.moticlorsa'',
					''Prestation.rolepers'',
					''Situationdossierrsa.etatdosrsa'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
				)
			),
			 6. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => array(
				''max_execution_time'' => 0,
				''memory_limit'' => ''1024M''
			)
		)',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.search');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.search';


-- Création de la variable de configuration pour l'export CSV de la recherche desnouveaux orientés
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Algorithmeorientation.exportcsv_recherche',
'{"filters":{"defaults":{"Dossier":{"dernier":1},"Situationdossierrsa":{"etatdosrsa_choice":0,"etatdosrsa":[0,2,3,4]}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":false,"auto":false,"results":{"header":[],"fields":["Personne.qual","Personne.nom","Personne.prenom","Personne.nir","Personne.dtnai","Personne.sexe","Personne.age","Adresse.numvoie","Adresse.libtypevoie","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Dossier.dtdemrsa","Dossier.numdemrsa","Prestation.rolepers","Orientstruct.date_valid","Orientstruct.date_impression","Structurereferente.ville","Structurereferente.lib_struc","Foyer.sitfam","Orientstruct.origine","Detaildroitrsa.nbenfautcha","Modecontact.numtel","Modecontact.numposte","Modecontact.adrelec"],"innerTable":["Situationdossierrsa.etatdosrsa","Personne.nomcomnai","Personne.dtnai","Adresse.numcom","Personne.nir","Historiqueetatpe.identifiantpe","Modecontact.numtel","Prestation.rolepers","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}',
'Export CSV, Algorithme d''orientation - Liste des nouveaux orientés

		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Dossier.numdemrsa'',
					''Dossier.dtdemrsa'',
					''Personne.nir'',
					''Situationdossierrsa.etatdosrsa'',
					''Prestation.natprest'',
					''Calculdroitrsa.toppersdrodevorsa'',
					''Personne.nom_complet_prenoms'',
					''Personne.qual'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Personne.age'' => array( ''label'' => ''Age'' ),
					''Adresse.numvoie'',
					''Adresse.libtypevoie'',
					''Adresse.nomvoie'',
					''Adresse.complideadr'',
					''Adresse.compladr'',
					''Adresse.codepos'',
					''Adresse.nomcom'',
					''Personne.email'',
					''Personne.numfixe'',
					''Typeorient.lib_type_orient'',
					''Personne.idassedic'',
					''Dsp.inscdememploi'',
					''Dossier.matricule'',
					''Structurereferenteparcours.lib_struc'',
					''Referentparcours.nom_complet'',
					''Personne.sexe'',
					''Dsp.inscdememploi'',
					''Dsp.natlog'' ,
					''Dsp.nivetu''
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Algorithmeorientation.orientation.ini_set'' ),
		)',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.exportcsv_recherche');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.exportcsv_recherche';


--Mise à jour de la variable pour le tri des pdf lors de l'impression des orientations validées
UPDATE public.configurations
SET value_variable = '{"filters":{"defaults":{"Detailcalculdroitrsa":{"natpf_choice":1,"natpf":["RSD","RSI"]},"Detaildroitrsa":{"oridemrsa_choice":1,"oridemrsa":["DEM"]},"Situationdossierrsa":{"etatdosrsa_choice":1,"etatdosrsa":[2,3,4]}},"accepted":{"Situationdossierrsa.etatdosrsa":[2,3,4],"Detailcalculdroitrsa.natpf":["RSD","RSI","RSU","RSJ"]},"skip":["Dossier.numdemrsa","Dossier.matricule","Dossier.anciennete_dispositif","Serviceinstructeur.id","Dossier.fonorg","Foyer.sitfam","Personne.dtnai","Personne.nomnai","Personne.nir","Personne.sexe","Personne.trancheage"],"has":["Dsp"]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":1,"Situationdossierrsa.etatdosrsa":[2,3,4],"Detailcalculdroitrsa.natpf_choice":1,"Detailcalculdroitrsa.natpf":["RSD","RSI","RSU","RSJ"]},"conditions":[],"order":{"Adresse.nomcom":"ASC","Typeorient.id":"ASC","Personne.nom":"ASC","Personne.prenom":"ASC"}},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Adresse.nomcom","1":"Personne.nom_complet_court","2":"Dossier.dtdemrsa","3":"Suiviinstruction.typeserins","4":"Orientstruct.origine","5":"Orientstruct.propo_algo","6":"Typeorient.lib_type_orient","7":"Structurereferente.lib_struc","8":"Orientstruct.statut_orient","9":"Orientstruct.date_propo","10":"Orientstruct.date_valid","Personne.has_dsp":{"type":"boolean"},"/Dossiers/view/#Dossier.id#":{"class":"external view"},"/Orientsstructs/impression/#Orientstruct.id#":{"class":"external"}},"innerTable":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Situationdossierrsa.dtclorsa","Situationdossierrsa.moticlorsa","Prestation.rolepers","Situationdossierrsa.etatdosrsa","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}'
WHERE configurations.lib_variable LIKE 'ConfigurableQuery.Orientsstructs.cohorte_orientees';


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
