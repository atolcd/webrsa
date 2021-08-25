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

-- Ajout la table tutoriel
CREATE TABLE IF NOT EXISTS public.tutoriels (
	id serial NOT NULL,
	titre varchar(50) NOT NULL,
	parentid int4 NULL,
	rg int4 NOT NULL,
	actif int2 NOT NULL DEFAULT 0,
	fichiermodule_id int4 NULL,
	created timestamp NULL,
	modified timestamp NULL,
	CONSTRAINT tutoriels_pkey PRIMARY KEY (id),
	CONSTRAINT tutoriels_fichiermodule_fkey FOREIGN KEY (fichiermodule_id) REFERENCES public.fichiersmodules(id) ON DELETE SET NULL,
	CONSTRAINT tutoriels_parentid_fkey FOREIGN KEY (parentid) REFERENCES public.tutoriels(id)
);

-- Ajout du module du tutoriel
INSERT INTO configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Tutoriel', 'false', 'Activation du module de tutoriel, accès et paramétrage', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Tutoriel');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Tutoriel';

-- Ajout des adresses dans les SAMS
ALTER TABLE public.sitescovs58 ADD IF NOT EXISTS lib_adresse varchar(150) NULL;
ALTER TABLE public.sitescovs58 ADD IF NOT EXISTS num_voie varchar(15) NULL;
ALTER TABLE public.sitescovs58 ADD IF NOT EXISTS type_voie varchar(30) NULL;
ALTER TABLE public.sitescovs58 ADD IF NOT EXISTS nom_voie varchar(50) NULL;
ALTER TABLE public.sitescovs58 ADD IF NOT EXISTS code_postal bpchar(5) NULL;
ALTER TABLE public.sitescovs58 ADD IF NOT EXISTS ville varchar(45) NULL;
ALTER TABLE public.sitescovs58 ADD IF NOT EXISTS code_insee bpchar(5) NULL;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************