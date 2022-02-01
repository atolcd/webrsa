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

-- Update de la variable de configuration pour le paramétrage des seuils de l'algorithme d'orientation
UPDATE public.configurations SET value_variable = '{"agemin" : [18,20],"agemax" : [55,60],"nbenfants" : [2,3,4],"nbmois" : [6,8,12]}',
comments_variable = 'Seuils disponibles dans les critères de l''algorithme d''orientation
{
	//Age minimum
	"agemin" : [
		18,
		62
	],
	//age maximum
	"agemax" : [
		30,
		140
	],
	//nombre d''enfants
	"nbenfants" : [
		1
	],
	//nombre de mois
	"nbmois" : [
		6
	]
}'
WHERE lib_variable LIKE 'Module.AlgorithmeOrientation.seuils';

-- Update de la variable de configuration permettant de stocker l'id du typeorient Association référente
UPDATE public.configurations
SET value_variable = (select t.id from typesorients t where t.lib_type_orient = 'Association référente')
WHERE configurations.lib_variable LIKE 'Typeorient.asso_referente_id';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
