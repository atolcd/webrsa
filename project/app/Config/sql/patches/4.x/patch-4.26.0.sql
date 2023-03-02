SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.26.0', CURRENT_TIMESTAMP);

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

-- Création de la table pour les rapports talend du flux modes de contact
CREATE TABLE IF NOT EXISTS administration.rapportstalendmodescontacts (
    id serial4 NOT NULL,
    fichier varchar(255) NOT NULL,
	personne_id int4 NULL,
    nir bpchar(15) NULL,
    matricule bpchar(15) NULL,
    rolepers bpchar(3) NULL,
    motif varchar(255) NOT NULL,
	created timestamp NOT NULL,
	CONSTRAINT rapportstalendmodescontacts_pkey PRIMARY KEY (id),
    CONSTRAINT rapportstalendmodescontacts_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES public.personnes(id) ON DELETE CASCADE ON UPDATE CASCADE
);

--Création des champs de date de modification pour chacune des coordonnées dans la table personne
ALTER TABLE public.personnes ADD COLUMN IF NOT EXISTS modified_numfixe timestamp;
ALTER TABLE public.personnes ADD COLUMN IF NOT EXISTS modified_numport timestamp;
ALTER TABLE public.personnes ADD COLUMN IF NOT EXISTS modified_email timestamp;

--Remplissage des dates de modification vides dans la table personne
with date_modif as (
	select distinct on (p.id) p.id as pid, coalesce(i.modified_fixe, d.dtdemrsa) as date_m
	from personnes p
		join foyers f on f.id = p.foyer_id
		join dossiers d on d.id = f.dossier_id
		left join infoscontactspersonne i on p.id = i.personne_id
	where p.numfixe is not null
	order by p.id, i.modified_fixe desc, d.dtdemrsa desc
)
update personnes
set modified_numfixe = date_modif.date_m
from date_modif
where personnes.id = date_modif.pid and personnes.numfixe is not null and personnes.modified_numfixe is null;


with date_modif as (
	select distinct on (p.id) p.id as pid, coalesce(i.modified_mobile , d.dtdemrsa) as date_m
	from personnes p
		join foyers f on f.id = p.foyer_id
		join dossiers d on d.id = f.dossier_id
		left join infoscontactspersonne i on p.id = i.personne_id
	where p.numport is not null
	order by p.id, i.modified_mobile desc, d.dtdemrsa desc
)
update personnes
set modified_numport = date_modif.date_m
from date_modif
where personnes.id = date_modif.pid and personnes.numport is not null and personnes.modified_numport is null;


with date_modif as (
	select distinct on (p.id) p.id as pid, coalesce(i.modified_email, d.dtdemrsa) as date_m
	from personnes p
		join foyers f on f.id = p.foyer_id
		join dossiers d on d.id = f.dossier_id
		left join infoscontactspersonne i on p.id = i.personne_id
	where p.email is not null
	order by p.id, i.modified_email desc, d.dtdemrsa desc
)
update personnes
set modified_email  = date_modif.date_m
from date_modif
where personnes.id = date_modif.pid and personnes.email is not null and personnes.modified_email is null;


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
