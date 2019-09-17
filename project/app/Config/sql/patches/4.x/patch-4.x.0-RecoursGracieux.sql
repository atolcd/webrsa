SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Création des origine des recours gracieux
CREATE TABLE public.originesrecoursgracieux (
	id serial NOT NULL,
	name varchar(50) NOT NULL,
	actif int2 NOT NULL DEFAULT 0,
	created timestamp NULL,
	modified timestamp NULL,
	CONSTRAINT originesrecoursgracieux_pkey PRIMARY KEY (id)
);

-- Ajout des origine des recours gracieux
INSERT INTO public.originesrecoursgracieux (name, actif)
 VALUES
 ('Transmis par l’allocataire', 1),
 ('Transmis par la CAF', 1),
 ('Transmis par la MSA', 1),
 ('Transmis par l’AS', 1),
 ('Autre', 1);

-- Création des types des recours gracieux
CREATE TABLE public.typesrecoursgracieux (
	id serial NOT NULL,
	name varchar(50) NOT NULL,
	actif int2 NOT NULL DEFAULT 0,
	created timestamp NULL,
	modified timestamp NULL,
    CONSTRAINT typesrecoursgracieux_pkey PRIMARY KEY (id)
);

-- Ajout des origine des recours gracieux
INSERT INTO public.typesrecoursgracieux (name, actif)
 VALUES
 ('Contestation du fond', 1),
 ('Demande de remise de dette', 1);

-- Création de la table de lien gestion -> recours gracieux
CREATE TABLE public.gestionnairesrecoursgracieux (
	id serial NOT NULL,
	user_id serial NOT NULL,
	actif int2 NOT NULL DEFAULT 0,
	created timestamp NULL,
	modified timestamp NULL,
    CONSTRAINT gestionnairesrecoursgracieux_pkey PRIMARY KEY (id)
);

-- Création de la table des recours gracieux
CREATE TABLE public.recoursgracieux (
	id serial NOT NULL,
	foyer_id int4 NOT NULL,
	dtarrivee date  NULL,
	dtbutoire date  NULL,
	dtreception date  NULL,
	originerecoursgracieux_id int4 NULL,
	typerecoursgracieux_id int4 NULL,
	dtaffectation date NULL,
	etat varchar NOT NULL,
	user_id int4 NULL,
	haspiecejointe varchar(1) NOT NULL DEFAULT '0'::character varying,
	created timestamp NULL,
	modified timestamp NULL,
	CONSTRAINT recoursgracieux_pkey PRIMARY KEY (id),
	CONSTRAINT recoursgracieux_haspiecejointe_in_list_chk CHECK (cakephp_validate_in_list((haspiecejointe)::text, ARRAY['0'::text, '1'::text])),
	CONSTRAINT recoursgracieux_foyer_fkey FOREIGN KEY (foyer_id) REFERENCES public.foyers(id) ON DELETE SET NULL ON UPDATE SET NULL,
	CONSTRAINT recoursgracieux_typesrecoursgracieux_fkey FOREIGN KEY (typerecoursgracieux_id) REFERENCES public.typesrecoursgracieux(id) ON DELETE SET NULL ON UPDATE CASCADE,
	CONSTRAINT recoursgracieux_originesrecoursgracieux_fkey FOREIGN KEY (originerecoursgracieux_id) REFERENCES public.originesrecoursgracieux(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
