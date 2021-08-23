SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Ajout la colonne actif_dossier dans la table typesorients
ALTER TABLE public.typesorients ADD IF NOT EXISTS actif_dossier bool NOT NULL DEFAULT true;

-- Ajout de la notion d'actif pour les statuts de rendez-vous
ALTER TABLE public.statutsrdvs ADD actif int2 NOT NULL DEFAULT 1;

-- Ajout de l'extension pg_trgm pour la gestion des doublons
CREATE EXTENSION IF NOT EXISTS pg_trgm;

-- Insertion de la variable de configuration
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'PlanPauvrete.Cohorte.Activite.Skip', '[]', 'Listes des codes d''activités qui ne sont pas pris en compte dans les cohortes plan pauvreté (nouveaux entrants & stock) suivantes :
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