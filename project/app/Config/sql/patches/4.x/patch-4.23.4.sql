SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.23.4', CURRENT_TIMESTAMP);

--Modification de la taille du champ libelle
ALTER TABLE public.criteresalgorithmeorientation ALTER COLUMN libelle TYPE varchar(500) USING libelle::varchar;

--Création du champ libelle_initial non modifiable
ALTER TABLE public.criteresalgorithmeorientation ADD COLUMN IF NOT EXISTS libelle_initial varchar(500);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
