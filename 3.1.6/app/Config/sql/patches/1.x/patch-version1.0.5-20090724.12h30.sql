--------------- Ajout du 24/07/2009 à 12h30 ------------------
ALTER TABLE prestations ADD CONSTRAINT personneidfk FOREIGN KEY (personne_id) REFERENCES personnes (id) MATCH FULL;
