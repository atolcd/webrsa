--------------- Ajout du 07/09/2009 à 11h30 ------------------
ALTER TABLE derogations RENAME COLUMN typdero TO typedero;

--------------- Ajout du 08/09/2009 à 09h13 ------------------

CREATE TABLE propospdos (
    id                    SERIAL NOT NULL PRIMARY KEY,
    dossier_rsa_id        INTEGER NOT NULL REFERENCES dossiers_rsa(id),
    typepdo               CHAR(1),
    decisionpdo           CHAR(1),
    datedecisionpdo       DATE,
    commentairepdo        TEXT
);

CREATE TABLE rendezvous (
    id                      SERIAL NOT NULL PRIMARY KEY,
    personne_id             INTEGER NOT NULL REFERENCES personnes(id),
    structurereferente_id   INT NOT NULL REFERENCES structuresreferentes(id),
    daterdv                 DATE,
    objetrdv                VARCHAR(256),
    commentairerdv          TEXT,
    statutrdv               CHAR(1)
);

-- -----------------------------------------------------------------------------
--       création table : calculsdroitsrsa 
-- -----------------------------------------------------------------------------

CREATE TABLE calculsdroitsrsa (
    id                    SERIAL NOT NULL PRIMARY KEY,
    personne_id           INTEGER NOT NULL REFERENCES personnes(id),
    toppersdrodevorsa     BOOLEAN,
    mtpersressmenrsa      NUMERIC(9,2),
    mtpersabaneursa       NUMERIC(9,2)
);
-- -----------------------------------------------------------------------------
--       modifications tables : prestations, ressources, ressourcesmensuelles
-- -----------------------------------------------------------------------------

/* INSERT INTO calculsdroitsrsa (personne_id, toppersdrodevorsa, mtpersressmenrsa, mtabaneu)

SELECT *
    FROM personnes
        INNER JOIN prestations ON (
        personnes.id = prestations.personne_id
            AND prestations.natprest = 'RSA'
            AND prestations.rolepers IN ( 'DEM', 'CJT' )
    )
        INNER JOIN ressources ON ( personnes.id = ressources.personne_id )
    INNER JOIN ressourcesmensuelles ON ( ressources.id = ressourcesmensuelles.id );


ALTER TABLE prestations DROP COLUMN toppersdrodevorsa;
ALTER TABLE ressources DROP COLUMN mtpersressmenrsa;
ALTER TABLE ressourcesmensuelles DROP COLUMN mtabaneu;
*/

--------------- Ajout du 08/09/2009 à 09h13 ------------------

ALTER TABLE orientsstructs ADD COLUMN daterelance DATE;
ALTER TABLE orientsstructs ADD COLUMN statutrelance CHAR(1);
