
SET client_encoding = 'UTF8';
-- *****************************************************************************
BEGIN;
-- *****************************************************************************

--Insertion de valeurs dans le champ libelle_initial
update public.criteresalgorithmeorientation set libelle_initial = libelle;

-- Update du critère 7 pour pouvoir modifier le nombre d'enfants à charge
UPDATE criteresalgorithmeorientation
SET libelle = 'L''allocataire est dans un foyer monoparental avec %d enfants ou plus à charge ?', nb_enfants = 1
WHERE code = 'FOYER_MONOPARENTAL';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
