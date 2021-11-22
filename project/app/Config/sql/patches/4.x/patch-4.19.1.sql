SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.19.1', CURRENT_TIMESTAMP);

-- Ajout d'une colonne pour activer ou non la sectorisation sur chauqe structure référente
ALTER TABLE structuresreferentes
ADD COLUMN IF NOT EXISTS actif_sectorisation bool NULL DEFAULT true;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************