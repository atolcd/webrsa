SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.32.2', CURRENT_TIMESTAMP);

-- Ajout de la variable UseDernierePersonne dans Module.Francetravail.Flux
UPDATE configurations SET value_variable = '{"Situationdossierrsa.etatdosrsa":["2","3","4"],"Calculdroitrsa.toppersdrodevorsa":"1","NbJoursDerniereRecuperationDonnees":"30","NbJoursDerniereMAJOrientations":"30","UseDernierePersonne":"true"}'
                        , comments_variable = 'Paramétrage des données à utiliser pour le flux de France Travail. Pour ne pas prendre en compte un des paramètres, laisser vide.

Situationdossierrsa.etatdosrsa : liste des états dossiers RSA à prendre en compte (obligatoire)
Calculdroitrsa.toppersdrodevorsa : Prends les personnes SDD. Si peu importe le statut, laisser vide
NbJoursDerniereRecuperationDonnees: Nombre de jour depuis la dernières récupérations des données
NbJoursDerniereMAJOrientations: Nombre de jour depuis l''envoie de l''orientation à France Travail
UseDernierePersonne: Utiliser uniquement la dernière personne créée avec le dernier dossier présent dans derniersdossiersallocataires'
WHERE lib_variable LIKE 'Module.Francetravail.Flux';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
