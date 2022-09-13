SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.24.1-CG93', CURRENT_TIMESTAMP);

--Modification de la variable de configuration pour prendre en compte le order by sur la liste des nouveaux orientés
UPDATE public.configurations SET value_variable = '{"filters":{"defaults":{"Situationdossierrsa":{"etatdosrsa_choice":0,"etatdosrsa":[0,2,3,4]},"Orientstruct":{"date_valid":1,"origine":"cohorte"}},"accepted":[],"skip":["PersonneReferent.communautesr_id","PersonneReferent.structurereferente_id","PersonneReferent.referent_id","ByTag.tag_choice","Personne.dtnai","Personne.nom","Personne.nomnai","Personne.prenom","Personne.nir","Personne.sexe","Personne.trancheage","Personne.trancheagesup","Personne.trancheageprec","Calculdroitrsa.toppersdrodevorsa"]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Personne.qual","1":"Personne.nom","2":"Personne.prenom","3":"Personne.nir","4":"Personne.dtnai","5":"Personne.sexe","6":"Personne.age","7":"Adresse.numvoie","8":"Adresse.libtypevoie","9":"Adresse.compladr","10":"Adresse.codepos","11":"Adresse.nomcom","12":"Dossier.dtdemrsa","13":"Dossier.numdemrsa","14":"Prestation.rolepers","15":"Orientstruct.date_valid","16":"Orientstruct.date_impression","17":"Structurereferente.ville","18":"Structurereferente.lib_struc","19":"Foyer.sitfam","20":"Orientstruct.origine","21":"Detaildroitrsa.nbenfautcha","22":"Modecontact.numtel","23":"Modecontact.numposte","24":"Modecontact.adrelec","/Orientsstructs/index/#Orientstruct.personne_id#":{"disabled":"( ''#Orientstruct.horszone#'' == true )","class":"view external"}},"innerTable":["Situationdossierrsa.etatdosrsa","Personne.nomcomnai","Personne.dtnai","Adresse.numcom","Personne.nir","Historiqueetatpe.identifiantpe","Modecontact.numtel","Prestation.rolepers","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}'
WHERE lib_variable LIKE 'ConfigurableQuery.Algorithmeorientation.search';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
