SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Mise à jour de la variable de configuration ConfigurableQuery.Orientsstructs.search
UPDATE public.configurations SET value_variable = '{"filters":{"defaults":{"Dossier":{"dernier":0,"dtdemrsa":0,"dtdemrsa_from":"TAB::-1WEEK","dtdemrsa_to":"TAB::NOW"}},"accepted":[],"skip":["Orientstruct.statut_orient"]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":10,"auto":false,"results":{"header":[],"fields":{"0":"Dossier.numdemrsa","1":"Personne.nom_complet","2":"Adresse.nomcom","3":"Dossier.dtdemrsa","4":"Orientstruct.date_valid","5":"Typeorient.lib_type_orient","6":"Structurereferente.lib_struc","7":"Orientstruct.statut_orient","Calculdroitrsa.toppersdrodevorsa":{"type":"boolean"},"/Orientsstructs/index/#Orientstruct.personne_id#":{"class":"view"}},"innerTable":["Situationdossierrsa.etatdosrsa","Personne.nomcomnai","Personne.dtnai","Adresse.numcom","Personne.nir","Historiqueetatpe.identifiantpe","Modecontact.numtel","Prestation.rolepers","Structurereferenteparcours.lib_struc","Referentparcours.nom_complet","Activite.act"]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}'
WHERE lib_variable = 'ConfigurableQuery.Orientsstructs.search';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************