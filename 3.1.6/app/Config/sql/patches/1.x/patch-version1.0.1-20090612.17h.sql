/* TODO: Enlever php_sid de jetons */

/* Patch: on peut insérer plusieurs fois le même utilisateur -> à faire deans le modèle */
ALTER TABLE users ADD CONSTRAINT users_username_key UNIQUE (username);

CREATE TABLE connections (
    id          SERIAL NOT NULL PRIMARY KEY,
    user_id     INT NOT NULL references users(id),
    php_sid     CHAR(32) DEFAULT NULL,
    created     TIMESTAMP DEFAULT NULL,
    modified    TIMESTAMP DEFAULT NULL
);

CREATE TABLE jetons (
    id          SERIAL NOT NULL PRIMARY KEY,
    dossier_id  INT NOT NULL references dossiers_rsa(id),
    php_sid     CHAR(32) DEFAULT NULL,
    user_id     INT NOT NULL references users(id),
    created     TIMESTAMP DEFAULT NULL,
    modified    TIMESTAMP DEFAULT NULL
);
CREATE TABLE users_contratsinsertion (
    user_id             INT NOT NULL REFERENCES users (id),
    contratinsertion_id INT NOT NULL REFERENCES contratsinsertion (id),
    PRIMARY KEY( user_id, contratinsertion_id )
);


ALTER TABLE contratsinsertion ALTER COLUMN expr_prof TYPE TEXT;
ALTER TABLE contratsinsertion ALTER COLUMN diplomes TYPE TEXT;
ALTER TABLE contratsinsertion ALTER COLUMN objectifs_fixes TYPE TEXT;
ALTER TABLE contratsinsertion ALTER COLUMN engag_object TYPE TEXT;
ALTER TABLE contratsinsertion ALTER COLUMN nature_projet TYPE TEXT;
ALTER TABLE contratsinsertion ALTER COLUMN observ_ci TYPE TEXT;
ALTER TABLE contratsinsertion ALTER COLUMN nat_cont_trav TYPE CHAR(4);
ALTER TABLE orientsstructs ALTER COLUMN typeorient_id DROP NOT NULL;
ALTER TABLE orientsstructs ALTER COLUMN structurereferente_id DROP NOT NULL;


ALTER TABLE rattachements ALTER COLUMN typepar TYPE CHAR(3);

ALTER TABLE referents ALTER COLUMN numero_poste TYPE VARCHAR(14);
ALTER TABLE referents ADD COLUMN qual VARCHAR(3);

ALTER TABLE servicesinstructeurs ALTER COLUMN nom_rue TYPE VARCHAR(100);
ALTER TABLE servicesinstructeurs ALTER COLUMN lib_service TYPE VARCHAR(100);
ALTER TABLE servicesinstructeurs ADD COLUMN numdepins CHAR(3);
ALTER TABLE servicesinstructeurs ADD COLUMN typeserins CHAR(1);
ALTER TABLE servicesinstructeurs ADD COLUMN numcomins CHAR(3);
ALTER TABLE servicesinstructeurs ADD COLUMN numagrins INTEGER;
ALTER TABLE servicesinstructeurs ADD COLUMN type_voie VARCHAR(6);

ALTER TABLE structuresreferentes ALTER COLUMN lib_struc TYPE VARCHAR(100);

ALTER TABLE typesorients ALTER COLUMN modele_notif TYPE VARCHAR(40);
