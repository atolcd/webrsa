SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Insertion de la variable de configuration
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'PlanPauvrete.Cohorte.Activite.Skip', '["EXP", "ETI"]', 'Listes des codes d''activités qui ne sont pas pris en compte dans les cohortes plan pauvreté (nouveaux entrants & stock) suivantes :
- Inscrite PE > Inscrits PE
- Non inscrits PE > Information collective

Elle ne seront pas non plus dans les options d''activités de la recherche.
', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'PlanPauvrete.Cohorte.Activite.Skip');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'PlanPauvrete.Cohorte.Activite.Skip';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************