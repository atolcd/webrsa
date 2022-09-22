SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.24.1-CG93', CURRENT_TIMESTAMP);

--Création du tag pour le dépassement dans l'algorithme d'orientation
INSERT INTO public.valeurstags(name, categorietag_id)
SELECT 'dépassement', (SELECT id FROM categorietags where name like 'Orientation cohorte')
WHERE NOT EXISTS (SELECT id FROM valeurstags WHERE name LIKE 'dépassement');

-- Ajout de la configuration pour le tag de dépassement dans l'algorithme d'orientation
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.AlgorithmeOrientation.TagDepassement', (SELECT id FROM valeurstags WHERE name LIKE 'dépassement'), 'id du tag pour les dépassements dans l''algorithme d''orientation', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.AlgorithmeOrientation.TagDepassement');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.AlgorithmeOrientation.TagDepassement';

--Modification de la variable de configuration pour prendre en compte le order by et les champs manquants sur la liste des nouveaux orientés
UPDATE public.configurations SET value_variable = '{"filters":{"defaults":{"Situationdossierrsa":{"etatdosrsa_choice":0,"etatdosrsa":[0,2,3,4]},"Orientstruct":{"date_valid":1,"origine":"cohorte"}},"accepted":[],"skip":["PersonneReferent.communautesr_id","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","ByTag.tag_choice","Personne.dtnai","Personne.nom","Personne.nomnai","Personne.prenom","Personne.nir","Personne.sexe","Personne.trancheage","Personne.trancheagesup","Personne.trancheageprec","Calculdroitrsa.toppersdrodevorsa"]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Personne.qual","1":"Personne.nom","2":"Personne.prenom","3":"Personne.nir","4":"Personne.dtnai","5":"Personne.sexe","6":"Personne.age","7":"Adresse.numvoie","8":"Adresse.libtypevoie","9":"Adresse.nomvoie","10":"Adresse.compladr","11":"Adresse.codepos","12":"Adresse.nomcom","13":"Dossier.dtdemrsa","14":"Dossier.numdemrsa","15":"Dossier.matricule","16":"Prestation.rolepers","17":"Orientstruct.date_valid","18":"Orientstruct.date_impression","19":"Structurereferente.ville","20":"Structurereferente.lib_struc","21":"Foyer.sitfam","22":"Orientstruct.origine","23":"Detaildroitrsa.nbenfautcha","24":"Modecontact.numtel","25":"Modecontact.numposte","26":"Modecontact.adrelec","27":"Typeorient.lib_type_orient","/Orientsstructs/index/#Orientstruct.personne_id#":{"disabled":"( ''#Orientstruct.horszone#'' == true )","class":"view external"}},"innerTable":["Situationdossierrsa.etatdosrsa","Personne.nomcomnai","Personne.dtnai","Adresse.numcom","Personne.nir","Historiqueetatpe.identifiantpe","Modecontact.numtel","Prestation.rolepers","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}'
WHERE lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.search';

--Modification de la variable de configuration pour prendre en compte les champs manquants sur l'export de la liste des nouveaux orientés
UPDATE public.configurations SET value_variable = '{"filters":{"defaults":{"Dossier":{"dernier":1},"Situationdossierrsa":{"etatdosrsa_choice":0,"etatdosrsa":[0,2,3,4]}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":false,"auto":false,"results":{"header":[],"fields":["Personne.qual","Personne.nom","Personne.prenom","Personne.nir","Personne.dtnai","Personne.sexe","Personne.age","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Dossier.dtdemrsa","Dossier.numdemrsa","Dossier.matricule","Prestation.rolepers","Orientstruct.date_valid","Orientstruct.date_impression","Structurereferente.ville","Structurereferente.lib_struc","Foyer.sitfam","Orientstruct.origine","Detaildroitrsa.nbenfautcha","Modecontact.numtel","Modecontact.numposte","Modecontact.adrelec","Typeorient.lib_type_orient"],"innerTable":["Situationdossierrsa.etatdosrsa","Personne.nomcomnai","Personne.dtnai","Adresse.numcom","Personne.nir","Historiqueetatpe.identifiantpe","Modecontact.numtel","Prestation.rolepers","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}'
WHERE lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.exportcsv_recherche';


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
