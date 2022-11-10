SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Création de la table pour les modes de contact du flux contact CAF
CREATE TABLE IF NOT EXISTS public.infoscontactspersonnecaf (
    id serial4 NOT NULL,
	personne_id int4 NULL,
    nir bpchar(15) NOT NULL,
    numdemrsa varchar(11) NULL,
    matricule bpchar(15) NULL,
    rolepers bpchar(3) NULL,
	telephone varchar(14) NULL DEFAULT NULL::character varying,
    modified_telephone timestamp,
	telephone2 varchar(14) NULL DEFAULT NULL::character varying,
    modified_telephone2 timestamp,
	email varchar(255) NULL DEFAULT NULL::character varying,
    modified_email timestamp,
	created timestamp NULL,
	modified timestamp NULL,
	CONSTRAINT infoscontactspersonnecaf_pkey PRIMARY KEY (id),
    CONSTRAINT infoscontactspersonnecaf_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES public.personnes(id) ON DELETE CASCADE ON UPDATE CASCADE
);

--Création des champs de date de modification pour chacune des coordonnées
ALTER TABLE public.infoscontactspersonne ADD COLUMN IF NOT EXISTS modified_fixe timestamp;
ALTER TABLE public.infoscontactspersonne ADD COLUMN IF NOT EXISTS modified_mobile timestamp;
ALTER TABLE public.infoscontactspersonne ADD COLUMN IF NOT EXISTS modified_email timestamp;

--Création des champs created et modified pour les modes de contacts du flux instruction
ALTER TABLE public.modescontact ADD COLUMN IF NOT EXISTS created timestamp;
ALTER TABLE public.modescontact ADD COLUMN IF NOT EXISTS modified timestamp;

--Peuplement des champs modified
update infoscontactspersonne
set modified_fixe = modified
where fixe is not null;

update infoscontactspersonne
set modified_mobile = modified
where mobile is not null;

update infoscontactspersonne
set modified_email = modified
where email is not null;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
