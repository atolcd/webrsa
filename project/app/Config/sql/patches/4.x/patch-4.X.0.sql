SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.X.0', CURRENT_TIMESTAMP);

-- Ajout d'une colonne pour le non respect du cer
ALTER TABLE contratsinsertion
ADD COLUMN IF NOT EXISTS non_respect bool NULL;

-- Ajout d'une colonne pour la cause du non respect du cer
ALTER TABLE contratsinsertion
ADD COLUMN IF NOT EXISTS cause_non_respect varchar NULL;

-- Ajout d'une colonne pour le nombre d'heures du contrat de travail
ALTER TABLE contratsinsertion
ADD COLUMN IF NOT EXISTS nb_heures_contrat_travail varchar NULL;

-- Ajout d'une colonne pour la date de début du contrat de travail
ALTER TABLE contratsinsertion
ADD COLUMN IF NOT EXISTS dd_contrat_travail date NULL;

-- Ajout d'une colonne pour  la date de fin du contrat de travail
ALTER TABLE contratsinsertion
ADD COLUMN IF NOT EXISTS df_contrat_travail date NULL;

-- Ajout d'une colonne pour  la date de fin du contrat de travail
ALTER TABLE contratsinsertion
ADD COLUMN IF NOT EXISTS descriptionaction varchar NULL;

--Création de la table pour les types de contrats
CREATE TABLE IF NOT EXISTS public.typescontrats (
	id serial4 NOT NULL,
	libelle varchar(50) NOT NULL,
	actif bool NOT NULL DEFAULT true,
	CONSTRAINT typescontrats_pk PRIMARY KEY (id)
);

--Création de la table pour les temps de travail
CREATE TABLE IF NOT EXISTS public.tempstravail (
	id serial4 NOT NULL,
	libelle varchar(50) NOT NULL,
	CONSTRAINT tempstravail_pk PRIMARY KEY (id)
);

--Création de la table pour les conclusions de cer
CREATE TABLE IF NOT EXISTS public.conclusioncer (
	id serial4 NOT NULL,
	libelle varchar(50) NOT NULL,
	CONSTRAINT conclusioncer_pk PRIMARY KEY (id)
);

-- Ajout d'une colonne pour le type de contrat déjà bénéficié
ALTER TABLE contratsinsertion
ADD COLUMN IF NOT EXISTS type_contrat_travail int4 NULL;
ALTER TABLE contratsinsertion DROP CONSTRAINT IF EXISTS contratsinsertion_type_contrat_travail_fk;
ALTER TABLE contratsinsertion ADD CONSTRAINT contratsinsertion_type_contrat_travail_fk FOREIGN KEY (type_contrat_travail) REFERENCES public.typescontrats(id);

-- Ajout d'une colonne pour le temps de travail déjà bénéficié
ALTER TABLE contratsinsertion
ADD COLUMN IF NOT EXISTS temps_contrat_travail int4 NULL;
ALTER TABLE contratsinsertion DROP CONSTRAINT IF EXISTS contratsinsertion_temps_contrat_travail_fk;
ALTER TABLE contratsinsertion ADD CONSTRAINT contratsinsertion_temps_contrat_travail_fk FOREIGN KEY (temps_contrat_travail) REFERENCES public.tempstravail(id);

-- Ajout d'une colonne pour la conclusion
ALTER TABLE contratsinsertion
ADD COLUMN IF NOT EXISTS action_conclusion int4 NULL;
ALTER TABLE contratsinsertion DROP CONSTRAINT IF EXISTS contratsinsertion_action_conclusion_fk;
ALTER TABLE contratsinsertion ADD CONSTRAINT contratsinsertion_action_conclusion_fk FOREIGN KEY (action_conclusion) REFERENCES public.conclusioncer(id);

--Ajout de la table pour les sujets de CER
CREATE TABLE IF NOT EXISTS public.sujetscers (
	id serial4 NOT NULL,
	libelle varchar(250) NOT NULL,
	champtexte bool NOT NULL DEFAULT false,
	created timestamp NULL,
	modified timestamp NULL,
	CONSTRAINT sujetscers_pkey PRIMARY KEY (id)
);

--Ajout de la table pour les sous-sujets de CER
CREATE TABLE IF NOT EXISTS public.soussujetscers (
	id serial4 NOT NULL,
	libelle varchar(250) NOT NULL,
	sujetcer_id int4 NOT NULL,
	created timestamp NULL,
	modified timestamp NULL,
	CONSTRAINT soussujetscers_pkey PRIMARY KEY (id),
	CONSTRAINT soussujetscers_sujetcer_id_fkey FOREIGN KEY (sujetcer_id) REFERENCES public.sujetscers(id) ON DELETE CASCADE ON UPDATE CASCADE
);

--Ajout de la table pour les valeurs par sous sujet de CER
CREATE TABLE IF NOT EXISTS public.valeursparsoussujetscers (
	id serial4 NOT NULL,
	libelle varchar(250) NOT NULL,
	soussujetcer_id int4 NOT NULL,
	created timestamp NULL,
	modified timestamp NULL,
	CONSTRAINT valeursparsoussujetscers_pkey PRIMARY KEY (id),
	CONSTRAINT valeursparsoussujetscers_soussujetcer_id_fkey FOREIGN KEY (soussujetcer_id) REFERENCES public.soussujetscers(id) ON DELETE CASCADE ON UPDATE CASCADE
);

--Ajout de la table de jointure entre les cer et les sujets
CREATE TABLE IF NOT EXISTS public.contratsinsertion_sujetscers (
	id serial4 NOT NULL,
	contratinsertion_id int4 NOT NULL,
	sujetcer_id int4 NULL,
	soussujetcer_id int4 NULL,
	valeurparsoussujetcer_id int4 NULL,
	commentaire varchar(250) NULL,
	created timestamp NULL,
	modified timestamp NULL,
	CONSTRAINT contratsinsertion_sujetscers_pkey PRIMARY KEY (id),
	CONSTRAINT contratsinsertion_sujetscers_contratinsertion_id_fkey FOREIGN KEY (contratinsertion_id) REFERENCES public.contratsinsertion(id) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT contratsinsertion_sujetscers_soussujetcer_id_fkey FOREIGN KEY (soussujetcer_id) REFERENCES public.soussujetscers(id) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT contratsinsertion_sujetscers_sujetcer_id_fkey FOREIGN KEY (sujetcer_id) REFERENCES public.sujetscers(id) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT contratsinsertion_sujetscers_valeurparsoussujetcer_id_fkey FOREIGN KEY (valeurparsoussujetcer_id) REFERENCES public.valeursparsoussujetscers(id) ON DELETE CASCADE ON UPDATE cascade
);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
