--------------- Ajout du 18/09/2009 à 14h48 ------------------
ALTER TABLE propospdos ADD COLUMN motifpdo CHAR(1);

ALTER TABLE contratsinsertion ADD COLUMN commentaire_action TEXT;

--------------- Ajout du 23/09/2009 à 11h48 ------------------

CREATE TABLE typesrdv(
    id                      SERIAL NOT NULL PRIMARY KEY,
    libelle                 VARCHAR(255),
    modelenotifrdv          VARCHAR (50)
);

INSERT INTO typesrdv VALUES ( 1, 'Pour Contrat d''insertion', 'modele_convoc_ci' );
INSERT INTO typesrdv VALUES ( 2, 'Pour l''orientation', 'modele_convoc_orient' );
SELECT pg_catalog.setval('typesrdv_id_seq', ( SELECT max(typesrdv.id) + 1 FROM typesrdv ), false);

ALTER TABLE rendezvous ADD COLUMN typerdv_id INTEGER REFERENCES typesrdv(id);

CREATE INDEX rendezvous_daterdv_idx ON rendezvous (daterdv);
CREATE INDEX rendezvous_statutrdv_idx ON rendezvous (statutrdv);
CREATE INDEX typesrdv_libelle_idx ON typesrdv (libelle);

--------------- Ajout du 28/09/2009 à 11h48 ------------------
ALTER TABLE orientsstructs ADD COLUMN date_impression_relance DATE;