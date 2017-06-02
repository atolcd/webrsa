/* création de la table de relation orientsstructs_servicesinstructeurs */
CREATE TABLE orientsstructs_servicesinstructeurs (
    orientstruct_id             INT NOT NULL REFERENCES orientsstructs (id),
    serviceinstructeur_id INT NOT NULL REFERENCES servicesinstructeurs (id),
    PRIMARY KEY( orientstruct_id, serviceinstructeur_id )
);

/* mise à jour table referents */
ALTER TABLE contratsinsertion ADD COLUMN date_saisie_ci DATE;
ALTER TABLE contratsinsertion ADD COLUMN lieu_saisie_ci VARCHAR(30);


ALTER TABLE contratsinsertion RENAME COLUMN date_saisie_ci TO date_saisi_ci;
ALTER TABLE contratsinsertion RENAME COLUMN lieu_saisie_ci TO lieu_saisi_ci;

ALTER TABLE users ADD COLUMN numtel VARCHAR(15);

--------------- Ajout du 17 06 09 ------------------
ALTER TABLE contratsinsertion ADD COLUMN emp_trouv BOOLEAN;
ALTER TABLE contratsinsertion ALTER COLUMN actions_prev TYPE CHAR(1);

------------------------------------------------------------

/* création de la table regroupementszonesgeo */
--------------- Ajout du 18 06 09 - 10h00 ------------------

CREATE TABLE regroupementszonesgeo (
    id          SERIAL NOT NULL PRIMARY KEY,
    lib_rgpt    VARCHAR(50)
);

CREATE TABLE zonesgeographiques_regroupementszonesgeo (
    zonegeographique_id             INT NOT NULL REFERENCES zonesgeographiques(id),
    regroupementzonegeo_id          INT NOT NULL REFERENCES regroupementszonesgeo(id),
    PRIMARY KEY( zonegeographique_id, regroupementzonegeo_id )
);

--------------- Ajout du 19 06 09 - 14h10 ------------------
ALTER TABLE dspps ALTER COLUMN couvsoc TYPE CHAR(1) USING CASE WHEN couvsoc IS TRUE THEN '1' ELSE '0' END;
ALTER TABLE dspps ALTER COLUMN creareprisentrrech TYPE CHAR(1) USING CASE WHEN creareprisentrrech IS TRUE THEN '1' ELSE '0' END;
ALTER TABLE dspfs ALTER COLUMN accosocfam TYPE CHAR(1) USING CASE WHEN accosocfam IS TRUE THEN '1' ELSE '0' END;
ALTER TABLE dspfs ALTER COLUMN accosocfam TYPE CHAR(1);
ALTER TABLE dspps ALTER COLUMN couvsoc TYPE CHAR(1) ;
ALTER TABLE dspps ALTER COLUMN creareprisentrrech TYPE CHAR(1);


--------------- Ajout du 22 06 09 - 9h00 ------------------
ALTER TABLE dspps ALTER COLUMN domideract TYPE CHAR(1) USING CASE WHEN domideract IS TRUE THEN '1' ELSE '0' END;
ALTER TABLE dspps ALTER COLUMN drorsarmiant TYPE CHAR(1) USING CASE WHEN drorsarmiant IS TRUE THEN '1' ELSE '0' END;
ALTER TABLE dspps ALTER COLUMN drorsarmianta2 TYPE CHAR(1) USING CASE WHEN drorsarmianta2 IS TRUE THEN '1' ELSE '0' END;
ALTER TABLE dspps ALTER COLUMN elopersdifdisp TYPE CHAR(1) USING CASE WHEN elopersdifdisp IS TRUE THEN '1' ELSE '0' END;
ALTER TABLE dspps ALTER COLUMN obstemploidifdisp TYPE CHAR(1) USING CASE WHEN obstemploidifdisp IS TRUE THEN '1' ELSE '0' END;
ALTER TABLE dspps ALTER COLUMN soutdemarsoc TYPE CHAR(1) USING CASE WHEN soutdemarsoc IS TRUE THEN '1' ELSE '0' END;

--------------- Ajout du 23 06 09 - 9h13 ------------------
DROP TABLE modes_contact;
CREATE TABLE modescontact (
    id              SERIAL NOT NULL PRIMARY KEY,
    foyer_id        INTEGER NOT NULL REFERENCES foyers(id),
    numtel          VARCHAR(11),
    numposte        INTEGER,
    nattel          CHAR(1),
    matetel         CHAR(3),
    autorutitel     CHAR(1),
    adrelec         VARCHAR(78),
    autorutiadrelec CHAR(1)
);