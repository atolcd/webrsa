SET client_encoding = 'UTF8';
-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Ajout du module de changement d'état PE
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.ModifEtatPE.enabled', 'false', 'Activation de la possibilité de modifier l''état Pôle Emploi d''une personne', current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.ModifEtatPE.enabled');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.ModifEtatPE.enabled';

-- Ajout de la table permettant d'ajouter des motifs de changement d'état PE
CREATE TABLE IF NOT EXISTS public.motifsetatspe (
	id serial NOT NULL,
	lib_motif varchar(250) NOT NULL,
	actif int2 NOT NULL DEFAULT 0,
	created timestamp(0) NOT NULL,
	modified timestamp(0) NOT NULL,
	CONSTRAINT motifsetatspe_pkey PRIMARY KEY (id),
	CONSTRAINT motifsetatspe_unique UNIQUE (lib_motif)
);

-- Configuration des accès aux cohortes du Plan Pauvreté

-- Nouveaux entrants > Inscrits PE
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, created, modified)
select 'Module.Cohorte.Plan.Pauvrete.Nouveaux.MenuInscritPE', 'true', 'Accès à la cohorte Plan Pauvreté > Nouveaux Entrants > Inscrits PE', current_timestamp, current_timestamp
where not exists (select id from configurations where lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.MenuInscritPE');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.MenuInscritPE';

-- Nouveaux entrants > Non inscrits PE
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, created, modified)
select 'Module.Cohorte.Plan.Pauvrete.Nouveaux.MenuNonInscritPE', 'true', 'Accès à la cohorte Plan Pauvreté > Nouveaux Entrants > Non inscrits PE', current_timestamp, current_timestamp
where not exists (select id from configurations where lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.MenuNonInscritPE');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Nouveaux.MenuNonInscritPE';

-- Stock > Inscrits PE
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, created, modified)
select 'Module.Cohorte.Plan.Pauvrete.Stock.MenuInscritPE', 'true', 'Accès à la cohorte Plan Pauvreté > Stock > Inscrits PE', current_timestamp, current_timestamp
where not exists (select id from configurations where lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.MenuInscritPE');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.MenuInscritPE';

-- Stock > Non inscrits PE
INSERT INTO public.configurations (lib_variable, value_variable, comments_variable, created, modified)
select 'Module.Cohorte.Plan.Pauvrete.Stock.MenuNonInscritPE', 'true', 'Accès à la cohorte Plan Pauvreté > Stock > Non inscrits PE', current_timestamp, current_timestamp
where not exists (select id from configurations where lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.MenuNonInscritPE');
UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Cohorte.Plan.Pauvrete.Stock.MenuNonInscritPE';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************