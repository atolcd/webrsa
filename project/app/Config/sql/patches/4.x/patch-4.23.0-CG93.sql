SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************


-- Update de la variable de configuration pour l'activation du module algorithme d'orientation
UPDATE public.configurations SET value_variable = 'true' WHERE lib_variable LIKE 'Module.AlgorithmeOrientation.enabled';


-- Ajout du type d'orientation Association référente
INSERT INTO public.typesorients (lib_type_orient)
SELECT 'Association référente'
WHERE NOT EXISTS (SELECT id FROM typesorients WHERE lib_type_orient LIKE 'Association référente');

UPDATE public.typesorients t
SET parentid = t1.id
FROM typesorients t1
WHERE t1.lib_type_orient = 'Social' AND t.lib_type_orient LIKE 'Association référente';

-- Rattrapage des orientés association référente
UPDATE orientsstructs
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Association référente')
WHERE structurereferente_id IN (
	SELECT id
	FROM structuresreferentes s
	WHERE s.lib_struc = 'Emmaüs Alternatives'
	OR s.lib_struc = 'Association FAIRE'
	OR s.lib_struc = 'ADEPT'
);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
