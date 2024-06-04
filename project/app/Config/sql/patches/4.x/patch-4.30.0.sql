SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.30.0', CURRENT_TIMESTAMP);

-- Variable de configuration de l'export csv des données du tableau 2'
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT
'ConfigurableQuery.Tableauxbords93.exportcsv_tableau2_donnees',
'{"filters":{"defaults":{"Dossier":{"dernier":1},"Situationdossierrsa":{"etatdosrsa_choice":0,"etatdosrsa":[0,2,3,4]}},"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":["Personne.id"]},"limit":false,"auto":false,"results":{"header":[],"fields":["Personne.qual","Personne.nom","Personne.prenom","Personne.nir","Personne.dtnai","Personne.sexe","Personne.age","Adresse.numvoie","Adresse.libtypevoie","Adresse.nomvoie","Adresse.compladr","Adresse.codepos","Adresse.nomcom","Dossier.dtdemrsa","Dossier.numdemrsa","Dossier.matricule","Prestation.rolepers","Orientstruct.date_valid","Orientstruct.date_impression","Structurereferente.ville","Structurereferente.lib_struc","Foyer.sitfam","Orientstruct.origine","Detaildroitrsa.nbenfautcha","Personne.numfixe","Personne.numport","Personne.email","Typeorient.lib_type_orient"],"innerTable":["Situationdossierrsa.etatdosrsa","Personne.nomcomnai","Personne.dtnai","Adresse.numcom","Personne.nir","Historiqueetatpe.identifiantpe","Modecontact.numtel","Prestation.rolepers","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}',
'',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Tableauxbords93.exportcsv_tableau2_donnees');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable IN ('ConfigurableQuery.Tableauxbords93.exportcsv_tableau2_donnees');

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
