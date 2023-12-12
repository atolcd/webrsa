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
-- *****************************************************************************
COMMIT;
-- *****************************************************************************
