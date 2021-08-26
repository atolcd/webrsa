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

-- *****************************************************************************
COMMIT;
-- *****************************************************************************