--------------- Ajout du 13/07/2009 à 15h45 ------------------
ALTER TABLE dossierscaf ALTER COLUMN numdemrsaprece TYPE VARCHAR(11);
ALTER TABLE dossierscaf ALTER COLUMN numdemrsaprece SET DEFAULT NULL;

--------------- Ajout du 17/07/2009 à 17h50 ------------------
ALTER TABLE contratsinsertion ALTER COLUMN decision_ci SET DEFAULT 'E';

--------------- Ajout du 23/07/2009 à 10h50 ------------------
ALTER TABLE personnes ALTER COLUMN nom TYPE VARCHAR(50);
ALTER TABLE personnes ALTER COLUMN prenom TYPE VARCHAR(50);
ALTER TABLE personnes ALTER COLUMN nomnai TYPE VARCHAR(50);
ALTER TABLE personnes ALTER COLUMN prenom2 TYPE VARCHAR(50);
ALTER TABLE personnes ALTER COLUMN prenom3 TYPE VARCHAR(50);

ALTER TABLE creancesalimentaires ADD COLUMN personne_id INTEGER;
ALTER TABLE creancesalimentaires ADD CONSTRAINT distfk FOREIGN KEY (personne_id) REFERENCES personnes (id) MATCH FULL;
