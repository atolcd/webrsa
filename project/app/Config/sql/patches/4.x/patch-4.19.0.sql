SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.19.0', CURRENT_TIMESTAMP);

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

-- Création de la table stockant les liens entre les utilisateurs et les réferents pour la sectorisation
CREATE TABLE IF NOT EXISTS public.referents_users (
	user_id int4 NOT NULL,
	referent_id int4 NOT NULL,
	id serial4 NOT NULL,
	CONSTRAINT referents_users_pkey PRIMARY KEY (id),
	CONSTRAINT referents_users_referent_id_fk FOREIGN KEY (referent_id) REFERENCES public.referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT referents_users_user_id_fk FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Création de la variable de configuration permettant d'afficher ou non le bloc de choix pour le réferent sectorisation
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Sectorisation.enabled', 'false', 'Permet à l''administrateur d''ajouter un ou des réferent(s) sectorisation à un utilisateur. @default false',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Sectorisation.enabled');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.Sectorisation.enabled';


-- Ajout d'une colonne pour activer ou non la sectorisation sur chaque structure référente
ALTER TABLE structuresreferentes
ADD COLUMN IF NOT EXISTS actif_sectorisation bool NULL DEFAULT true;


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
