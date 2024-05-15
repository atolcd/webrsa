SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.28.10', CURRENT_TIMESTAMP);

--Ajout d'option dans le questionnaire d2
alter table public.questionnairesd2pdvs93 drop constraint if exists questionnairesd2pdvs93_situationaccompagnement_in_list_chk;
ALTER TABLE public.questionnairesd2pdvs93 ADD CONSTRAINT questionnairesd2pdvs93_situationaccompagnement_in_list_chk CHECK (cakephp_validate_in_list((situationaccompagnement)::text, ARRAY['maintien'::text, 'sortie_obligation'::text, 'abandon'::text, 'reorientation'::text, 'changement_situation'::text, 'reorientation_ent_diag'::text]));

-- Suppression de l'index sur le nom des sorties d'accompagnement
DROP INDEX if exists public.sortiesaccompagnementsd2pdvs93_name_idx;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
