SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.28.9', CURRENT_TIMESTAMP);

--Variable de configuration pour l'export des rapports
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'ConfigurableQuery.Rapportsechangesali.exportcsv',
'{"filters":{"defaults":[],"accepted":[],"skip":[]},"query":{"restrict":[],"conditions":[],"order":[]},"limit":false,"auto":false,"results":{"header":[],"fields":["Personne.qual","Personne.nom","Personne.prenom","Personne.nir","Personne.dtnai","Erreur.referentparcours","Erreur.rendezvous","Erreur.dsp","Erreur.cer","Erreur.orient","Erreur.d1","Erreur.d2","Erreur.b7"],"innerTable":[]},"ini_set":{"max_execution_time":0,"memory_limit":"1024M"}}',
'Export CSV, Rapports d''échange ALI

		array(
			 1. Filtres de recherche, on reprend la configuration de la recherche
			''filters'' => Configure::read( ''ConfigurableQuery.Rapportsechangesali.exportcsv.filters'' ),
			 2. Recherche, on reprend la configuration de la recherche
			''query'' => Configure::read( ''ConfigurableQuery.Rapportsechangesali.exportcsv.query'' ),
			 3. Résultats de la recherche
			''results'' => array(
				''fields'' => array(
					''Personne.qual'',
					''Personne.nom'',
					''Personne.prenom'',
					''Personne.dtnai'',
					''Personne.age'' => array( ''label'' => ''Age'' ),
					...
				)
			),
			 4. Temps d''exécution, mémoire maximum, ...
			''ini_set'' => Configure::read( ''ConfigurableQuery.Rapportsechangesali.exportcsv.ini_set'' ),
		)',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'ConfigurableQuery.Rapportsechangesali.exportcsv');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'ConfigurableQuery.Rapportsechangesali.exportcsv';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
