SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.24.3', CURRENT_TIMESTAMP);

--Modification de la contrainte sur les users pour enregistrer un utilisateur de type prestataire
ALTER TABLE public.users DROP CONSTRAINT users_type_structurereferente_idreferent_id_chk;

alter table public.users add constraint users_type_structurereferente_idreferent_id_chk check (((((type)::text = 'cg'::text)
and (structurereferente_id is null)
and (referent_id is null)
and (communautesr_id is null))
or (((type)::text = 'externe_cpdvcom'::text)
and (structurereferente_id is null)
and (referent_id is null)
and (communautesr_id is not null))
or (((type)::text = any (array[('externe_cpdv'::character varying)::text,
('externe_secretaire'::character varying)::text]))
and (structurereferente_id is not null)
and (referent_id is null)
and (communautesr_id is null))
or (((type)::text = 'externe_ci'::text)
and (structurereferente_id is null)
and (referent_id is not null)
and (communautesr_id is null))
or (((type)::text = 'prestataire'::text))
));

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
