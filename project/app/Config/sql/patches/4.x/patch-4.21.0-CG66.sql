SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Mise à jour des nir non correct dans la table informationspe
UPDATE informationspe SET nir = NULL WHERE nir_correct(nir) IS FALSE;

-- Suppression de la contrainte existante au cas où
ALTER TABLE public.informationspe DROP CONSTRAINT IF EXISTS informationspe_nir_correct_chk;

-- Ajout de la contrainte dans la table informationspe pour n'avoir que des bons NIR, ou NULL (idem aux autres département)
ALTER TABLE public.informationspe ADD CONSTRAINT informationspe_nir_correct_chk CHECK (((nir IS NULL) OR nir_correct((nir)::text)));

-- *****************************************************************************
COMMIT;
-- *****************************************************************************