----------- 04/02/2010 - 09h30 ----------------------
ALTER TABLE typesorients ADD COLUMN modele_notif_cohorte VARCHAR(50);


----------- 09/02/2010 - 15h03 ----------------------
-- DROP TABLE partenaires CASCADE;
-- DROP TABLE actionscandidats CASCADE;
-- DROP TABLE actionscandidats_personnes CASCADE;
-- DROP TABLE actionscandidats_partenaires CASCADE;
-- DROP TABLE contactspartenaires CASCADE;
-- DROP TABLE contactspartenaires_partenaires CASCADE;

CREATE TABLE partenaires (
    id                     SERIAL NOT NULL PRIMARY KEY,
    libstruc               VARCHAR(32) NOT NULL,
    numvoie                VARCHAR(6) NOT NULL,
    typevoie               VARCHAR(6) NOT NULL,
    nomvoie                VARCHAR(30) NOT NULL,
    compladr               VARCHAR(32) NOT NULL,
    numtel                 VARCHAR(14),
    numfax                 VARCHAR(14),
    email                  VARCHAR(78),
    codepostal             CHAR(5) NOT NULL,
    ville                  VARCHAR(45) NOT NULL
);
COMMENT ON TABLE partenaires IS 'Partenaires pour les fiches de candidatures';

--
CREATE TABLE actionscandidats (
    id                     SERIAL NOT NULL PRIMARY KEY,
    intitule                VARCHAR(250) NOT NULL,
    code                    VARCHAR(6)
);
COMMENT ON TABLE actionscandidats IS 'Actions pour les fiches de candidatures';
--

CREATE TABLE actionscandidats_personnes (
    id                  SERIAL NOT NULL PRIMARY KEY,
    personne_id     INTEGER NOT NULL REFERENCES personnes(id),
    actioncandidat_id   INTEGER NOT NULL REFERENCES actionscandidats(id),
    referent_id         INTEGER NOT NULL REFERENCES referents(id),
    ddaction            DATE,
    dfaction            DATE
);
CREATE INDEX actionscandidats_personnes_personne_id_idx ON actionscandidats_personnes (personne_id);
CREATE INDEX actionscandidats_personnes_actioncandidat_id_idx ON actionscandidats_personnes (actioncandidat_id);
CREATE INDEX actionscandidats_personnes_referent_id_idx ON actionscandidats_personnes (referent_id);

COMMENT ON TABLE actionscandidats_personnes IS 'Table de liaison entre une personne, un référent et les actions de candidature';
--

CREATE TABLE actionscandidats_partenaires (
    id                          SERIAL NOT NULL PRIMARY KEY,
    personne_id     INTEGER NOT NULL REFERENCES personnes(id),
    actioncandidat_id   INTEGER NOT NULL REFERENCES actionscandidats(id)
);
CREATE INDEX actionscandidats_partenaires_personne_id_idx ON actionscandidats_partenaires (personne_id);
CREATE INDEX actionscandidats_partenaires_actioncandidat_id_idx ON actionscandidats_partenaires (actioncandidat_id);
COMMENT ON TABLE actionscandidats_partenaires IS 'Table de liaison entre les partenaires et les actions de candidature';

--
CREATE TABLE contactspartenaires (
    id                          SERIAL NOT NULL PRIMARY KEY,
    qual                        VARCHAR(3) NOT NULL,
    nom                         VARCHAR(28) NOT NULL,
    prenom                      VARCHAR(32) NOT NULL,
    numtel                      VARCHAR(10),
    email                       VARCHAR(78)
);
COMMENT ON TABLE contactspartenaires IS 'Contact lié à un partenaire';


CREATE TABLE contactspartenaires_partenaires (
    id                          SERIAL NOT NULL PRIMARY KEY,
    partenaire_id               INTEGER NOT NULL REFERENCES partenaires(id),
    contactpartenaire_id        INTEGER NOT NULL REFERENCES contactspartenaires(id)
);
CREATE INDEX contactspartenaires_partenaires_partenaire_id_idx ON contactspartenaires_partenaires (partenaire_id);
CREATE INDEX contactspartenaires_partenaires_contactpartenaire_id_idx ON contactspartenaires_partenaires (contactpartenaire_id);

COMMENT ON TABLE contactspartenaires_partenaires IS 'Table de liaison entre les partenaires et ses contacts';

ALTER TABLE contratsinsertion ALTER COLUMN  lieu_saisi_ci TYPE VARCHAR(50);