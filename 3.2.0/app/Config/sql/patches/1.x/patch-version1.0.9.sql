------------- 05/01/2010 - 17h00 -------------------
ALTER TABLE dspps ALTER COLUMN rappemploiquali TYPE CHAR(1) USING CASE WHEN rappemploiquali IS TRUE THEN '1' WHEN rappemploiquali IS FALSE THEN '0' ELSE NULL END;
ALTER TABLE dspps ALTER COLUMN rappemploiform TYPE CHAR(1) USING CASE WHEN rappemploiform IS TRUE THEN '1' WHEN rappemploiform IS FALSE THEN '0' ELSE NULL END;
ALTER TABLE dspps ALTER COLUMN permicondub TYPE CHAR(1) USING CASE WHEN permicondub IS TRUE THEN '1' WHEN permicondub IS FALSE THEN '0' ELSE NULL END;
ALTER TABLE dspps ALTER COLUMN moyloco TYPE CHAR(1) USING CASE WHEN moyloco IS TRUE THEN '1' WHEN moyloco IS FALSE THEN '0' ELSE NULL END;
ALTER TABLE dspps ALTER COLUMN persisogrorechemploi TYPE CHAR(1) USING CASE WHEN persisogrorechemploi IS TRUE THEN '1' WHEN persisogrorechemploi IS FALSE THEN '0' ELSE NULL END;

/* APRES/pièces/TODO
    1°) tout supprimer de apres_piecesapre et piecesapre
    2°) remettre les auto incrémentés (id) à 1
*/

------------- 06/01/2010 - 18h00  ------------------
INSERT INTO piecesapre ( libelle ) VALUES
    ( 'Attestation CAF datant du dernier mois de prestation versée' ),
    ( 'Curriculum vitae' ),
    ( 'Lettre motivée de l''allocataire détaillant les besoins' ),
    ( 'RIB de l''allocataire ou de l''organisme' );

------------- 07/01/2010 - 18h00  ------------------
ALTER TABLE decisionspdos ALTER COLUMN libelle TYPE VARCHAR(150);


------------- 11/01/2010 - 11h00  ------------------
CREATE TABLE jetonsfonctions (
    id          SERIAL NOT NULL PRIMARY KEY,
    controller  VARCHAR(250) NOT NULL,
    action      VARCHAR(250) NOT NULL,
    php_sid     CHARACTER(32) DEFAULT NULL::BPCHAR,
    user_id     INTEGER NOT NULL,
    created     TIMESTAMP WITHOUT TIME ZONE,
    modified    TIMESTAMP WITHOUT TIME ZONE
);
CREATE INDEX jetonsfonctions_controller_idx ON jetonsfonctions (controller);
CREATE INDEX jetonsfonctions_action_idx ON jetonsfonctions (action);
CREATE INDEX jetonsfonctions_php_sid_idx ON jetonsfonctions (php_sid);
CREATE INDEX jetonsfonctions_user_id_idx ON jetonsfonctions (user_id);

------------- 11/01/2010 - 17h00  ------------------
ALTER TABLE apres ADD COLUMN structurereferente_id INTEGER REFERENCES structuresreferentes(id);
CREATE INDEX apres_structurereferente_id_idx ON apres (structurereferente_id);

ALTER TABLE structuresreferentes ADD COLUMN contratengagement type_no DEFAULT NULL;
ALTER TABLE structuresreferentes ADD COLUMN apre type_no DEFAULT NULL;

ALTER TABLE apres ADD COLUMN referent_id INTEGER REFERENCES referents(id);
CREATE INDEX apres_referent_id_idx ON apres (referent_id);

