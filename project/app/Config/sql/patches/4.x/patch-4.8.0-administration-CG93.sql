SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Schéma administration (à appliquer sur la base staging)
-- Changement de type sur la table administration.alerte
ALTER TABLE administration.alerte
ALTER COLUMN etape TYPE int4 USING etape::integer,
ALTER COLUMN code TYPE int4 USING etape::integer;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************