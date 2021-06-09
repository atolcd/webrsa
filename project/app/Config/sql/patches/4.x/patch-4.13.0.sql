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

-- *****************************************************************************
COMMIT;
-- *****************************************************************************