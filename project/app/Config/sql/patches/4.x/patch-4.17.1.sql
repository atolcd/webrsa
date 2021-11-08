SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.17.1', CURRENT_TIMESTAMP);

-- MAJ de configuration
UPDATE configurations SET value_variable = '{"filters":{"defaults":{"Dossier":{"dernier":1},"Detailcalculdroitrsa":{"natpf_choice":1,"natpf":["RSD","RSI"]},"Detaildroitrsa":{"oridemrsa_choice":1,"oridemrsa":["DEM"]},"Situationdossierrsa":{"etatdosrsa_choice":1,"etatdosrsa":[2,3,4]}},"accepted":{"Situationdossierrsa.etatdosrsa":[2,3,4],"Detailcalculdroitrsa.natpf":["RSD","RSI","RSU","RSJ"]},"skip":["Dossier.numdemrsa","Dossier.matricule","Dossier.anciennete_dispositif","Serviceinstructeur.id","Dossier.fonorg","Foyer.sitfam","Personne.dtnai","Personne.nomnai","Personne.nir","Personne.sexe","Personne.trancheage"]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":1,"Situationdossierrsa.etatdosrsa":[2,3,4],"Detailcalculdroitrsa.natpf_choice":1,"Detailcalculdroitrsa.natpf":["RSD","RSI","RSU","RSJ"]},"conditions":[],"order":["Dossier.dtdemrsa"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.nom_complet_court":[],"Adresse.nomcom":[],"Structureorientante.lib_struc":[],"Referentorientant.nom_complet":[],"Orientstruct.origine":[],"Typeorient.lib_type_orient":[],"Structurereferente.lib_struc":[],"Referent.nom_complet":[],"Calculdroitrsa.toppersdrodevorsa":{"type":"boolean"},"Orientstruct.date_propo":[],"Orientstruct.date_valid":[],"/Orientsstructs/impression/#Orientstruct.id#":{"class":"external"},"/Dossiers/view/#Dossier.id#":{"class":"external"}},"innerTable":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Situationdossierrsa.dtclorsa","Situationdossierrsa.moticlorsa","Prestation.rolepers","Situationdossierrsa.etatdosrsa","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}' WHERE lib_variable = 'ConfigurableQuery.Orientsstructs.cohorte_orientees_validees';
UPDATE configurations SET value_variable = '{"filters":{"defaults":{"Dossier":{"dernier":1},"Detailcalculdroitrsa":{"natpf_choice":1,"natpf":["RSD","RSI"]},"Detaildroitrsa":{"oridemrsa_choice":1,"oridemrsa":["DEM"]},"Situationdossierrsa":{"etatdosrsa_choice":1,"etatdosrsa":[2,3,4]}},"accepted":{"Situationdossierrsa.etatdosrsa":[2,3,4],"Detailcalculdroitrsa.natpf":["RSD","RSI","RSU","RSJ"]},"skip":["Dossier.numdemrsa","Dossier.matricule","Dossier.anciennete_dispositif","Serviceinstructeur.id","Dossier.fonorg","Foyer.sitfam","Personne.dtnai","Personne.nomnai","Personne.nir","Personne.sexe","Personne.trancheage"]},"query":{"restrict":{"Situationdossierrsa.etatdosrsa_choice":1,"Situationdossierrsa.etatdosrsa":[2,3,4],"Detailcalculdroitrsa.natpf_choice":1,"Detailcalculdroitrsa.natpf":["RSD","RSI","RSU","RSJ"]},"conditions":[],"order":["Dossier.dtdemrsa"]},"limit":10,"auto":false,"results":{"header":[],"fields":{"Personne.nom_complet_court":[],"Adresse.nomcom":[],"Structureorientante.lib_struc":[],"Referentorientant.nom_complet":[],"Orientstruct.origine":[],"Typeorient.lib_type_orient":[],"Structurereferente.lib_struc":[],"Referent.nom_complet":[],"Calculdroitrsa.toppersdrodevorsa":{"type":"boolean"},"Orientstruct.date_propo":[],"/Dossiers/view/#Dossier.id#":{"class":"external"}},"innerTable":["Dossier.numdemrsa","Dossier.dtdemrsa","Personne.dtnai","Dossier.matricule","Personne.nir","Adresse.codepos","Situationdossierrsa.dtclorsa","Situationdossierrsa.moticlorsa","Prestation.rolepers","Situationdossierrsa.etatdosrsa","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}' WHERE lib_variable = 'ConfigurableQuery.Orientsstructs.cohorte_validation';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************