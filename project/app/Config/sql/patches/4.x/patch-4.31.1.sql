SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.31.1', CURRENT_TIMESTAMP);

--Ajout de la colonne pour le nombre de rdv individuels de l'année
ALTER TABLE tdb2_histo_corpus 
ADD IF NOT EXISTS nb_rdv_indiv_annee integer null;

-- Modification du nom d'une colonne pour harmonisation
ALTER TABLE tdb1_a_corpus
RENAME COLUMN tagdiag TO tag_diag;

ALTER TABLE tdb1_b_corpus
RENAME COLUMN tagdiag TO tag_diag;

ALTER TABLE tdb1_c_corpus
RENAME COLUMN tagdiag TO tag_diag;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
