--------------------- Ajout du 11/02/2010 - 10h57 --------------------------------------
ALTER TABLE actionscandidats_partenaires DROP COLUMN personne_id;
ALTER TABLE actionscandidats_partenaires ADD COLUMN partenaire_id INTEGER NOT NULL REFERENCES partenaires(id);

CREATE INDEX actionscandidats_partenaires_partenaire_id_idx ON actionscandidats_partenaires (partenaire_id);

ALTER TABLE contactspartenaires ADD COLUMN partenaire_id INTEGER NOT NULL REFERENCES partenaires(id);

--------------------- Ajout du 12/02/2010 - 09h28 --------------------------------------
-- CREATE TABLE contactspartenaires_partenaires (
--     id                          SERIAL NOT NULL PRIMARY KEY,
--     partenaire_id               INTEGER NOT NULL REFERENCES partenaires(id),
--     contactpartenaire_id        INTEGER NOT NULL REFERENCES contactspartenaires(id)
-- );
-- CREATE INDEX contactspartenaires_partenaires_partenaire_id_idx ON contactspartenaires_partenaires (partenaire_id);
-- CREATE INDEX contactspartenaires_partenaires_contactpartenaire_id_idx ON contactspartenaires_partenaires (contactpartenaire_id);
--
-- COMMENT ON TABLE contactspartenaires_partenaires IS 'Table de liaison entre les partenaires et ses contacts';

-- DROP TABLE contactspartenaires_partenaires CASCADE;

ALTER TABLE actionscandidats_personnes ADD COLUMN motifdemande TEXT;

--------------------- Ajout du 18/02/2010 - 09h28 --------------------------------------

CREATE INDEX referents_nom_complet_idx ON referents ( ( qual || ' ' || nom || ' ' || prenom ) );
-- ALTER TABLE actionscandidats_personnes ADD COLUMN rendezvous_id INTEGER REFERENCES rendezvous(id);
ALTER TABLE actionscandidats_personnes ADD COLUMN enattente type_no;
ALTER TABLE actionscandidats_personnes ADD COLUMN datesignature DATE;

CREATE TYPE type_venu AS ENUM ( 'VEN', 'NVE' );
CREATE TYPE type_retenu AS ENUM ( 'RET', 'NRE' );
ALTER TABLE actionscandidats_personnes ADD COLUMN bilanvenu type_venu DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ADD COLUMN bilanretenu type_retenu DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ADD COLUMN infocomplementaire TEXT;
ALTER TABLE actionscandidats_personnes ADD COLUMN datebilan DATE;

--------------------- Ajout du 19/02/2010 - 12h18 --------------------------------------
-- ALTER TABLE actionscandidats_personnes ADD COLUMN referent_id  INTEGER NOT NULL REFERENCES referents(id);
CREATE TYPE type_booleannumber AS ENUM ( '0', '1' );
ALTER TABLE actionscandidats_personnes ADD COLUMN rendezvouspartenaire type_booleannumber;
ALTER TABLE actionscandidats_personnes ADD COLUMN daterdvpartenaire DATE;

ALTER TABLE actionscandidats_personnes ADD COLUMN mobile type_booleannumber;
ALTER TABLE actionscandidats_personnes ADD COLUMN naturemobile TEXT;
ALTER TABLE actionscandidats_personnes ADD COLUMN typemobile TEXT;

--------------------- Ajout du 22/02/2010 - 12h18 --------------------------------------
ALTER TABLE apres DROP COLUMN referentapre_id;
DROP TABLE referentsapre;