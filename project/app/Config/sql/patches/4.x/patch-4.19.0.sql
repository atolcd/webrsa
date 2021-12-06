SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Ajout de la notion d'actif pour les actions de rendez-vous
ALTER TABLE public.statutsrdvs_typesrdv ADD IF NOT EXISTS actif bool NOT NULL DEFAULT true;

-- Augmentation de la taille de la colonne typecommission
ALTER TABLE public.statutsrdvs_typesrdv ALTER COLUMN typecommission TYPE varchar(20) USING typecommission::varchar;

-- Modification des contraintes de la table statutsrdvs_typesrdvs pour permettre l'utilisation du type de comission 'ORIENTATION'
ALTER TABLE public.statutsrdvs_typesrdv DROP CONSTRAINT IF EXISTS statutsrdvs_typesrdv_typecommission_in_list_chk;
ALTER TABLE public.statutsrdvs_typesrdv ADD CONSTRAINT statutsrdvs_typesrdv_typecommission_in_list_chk CHECK (cakephp_validate_in_list((typecommission)::text, ARRAY['cov'::text, 'ep'::text, 'orientation'::text]));

-- Assouplissement de la contrainte d'enregistrement des actions de rendez-vous
DROP INDEX IF EXISTS public.statutsrdvs_typesrdv_statutrdv_id_typerdv_id_idx;
CREATE UNIQUE INDEX IF NOT EXISTS statutsrdvs_typesrdv_statutrdv_id_typerdv_id_typecommission_idx ON public.statutsrdvs_typesrdv (statutrdv_id,typerdv_id,typecommission);


-- *****************************************************************************
COMMIT;
-- *****************************************************************************