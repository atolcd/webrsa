SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.29.0', CURRENT_TIMESTAMP);

--Table pour la liste déroulante des décisions EP 
create table if not exists listedecisionssuspensionseps93 (
	id serial4 NOT NULL,
	code varchar(255)[] not null,
	libelle varchar(255) not null,
	nom_courrier varchar(255) null,
	premier_niveau bool not null default false,
	deuxieme_niveau bool not null default false,
	actif bool not null default true,
	created timestamp NOT NULL,
	modified timestamp NOT NULL
);

alter table administration.rapportstalendmodescontacts add column if not exists count integer default null;

-- Variable de configuration de la durée de vie des jetons
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT
'Jetons.duree',
'1800',
'Nombre de secondes pendant lequel le jeton reste actif',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Jetons.duree');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable IN ('Jetons.duree');

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
