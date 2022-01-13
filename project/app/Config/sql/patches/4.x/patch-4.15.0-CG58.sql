SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Ajout des activités à exclure lors des cohortes PP
UPDATE configurations SET value_variable = '["EXP", "ETI"]' WHERE lib_variable LIKE 'PlanPauvrete.Cohorte.Activite.Skip';

-- Activation du module du tutoriel
UPDATE configurations SET value_variable = 'true' WHERE lib_variable LIKE 'Module.Tutoriel';

-- Suppression du parent de l'organisme DREES "SPE"
UPDATE dreesorganismes SET parentid = NULL WHERE lib_dreesorganisme = 'SPE';

-- ********************** Nouveaux type d'orientation **************************
-- Mise à jour de l aconfiguration pour avoir la liste des types d'orientation par parent
UPDATE configurations SET value_variable = 'true' WHERE lib_variable LIKE 'with_parentid';

-- ***************** Création des nouveaux type d'orientation ******************

-- Modification des noms des types d'orientations actuels
UPDATE typesorients SET lib_type_orient = 'Sociale - Sociale' WHERE id = 3;
UPDATE typesorients SET lib_type_orient = 'Professionnelle - Professionnelle' WHERE id = 1;

-- Création des nouveaux parents
INSERT INTO typesorients(lib_type_orient, code_type_orient )
SELECT 'Professionnelle', 'EMPLOI' WHERE NOT EXISTS (SELECT id FROM typesorients WHERE lib_type_orient = 'Professionnelle');

INSERT INTO typesorients(lib_type_orient, code_type_orient )
SELECT 'Sociale', 'SOCIAL' WHERE NOT EXISTS (SELECT id FROM typesorients WHERE lib_type_orient = 'Sociale');

-- Mise à jour des anciens types pour être des enfants
UPDATE typesorients
	SET parentid = to2.id, code_type_orient = ''
	FROM typesorients to2
	WHERE to2.lib_type_orient = 'Professionnelle' AND typesorients.lib_type_orient = 'Professionnelle - Professionnelle';

UPDATE typesorients
	SET parentid = to2.id, code_type_orient = ''
	FROM typesorients to2
	WHERE to2.lib_type_orient = 'Sociale' AND typesorients.lib_type_orient = 'Sociale - Sociale';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************