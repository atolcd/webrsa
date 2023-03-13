SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.24.5', CURRENT_TIMESTAMP);

-- Mise à jour de la variable de configuration de memory_limit
update public.configurations set value_variable = '"2048M"'  WHERE lib_variable LIKE 'Impression.memory_limit';

insert into configurationshistoriques (configurations_id, value_variable_old, value_variable_new, user_id, username, created, modified)
values (
	(select id from configurations where lib_variable like 'Impression.memory_limit'),
	(select value_variable from configurations where lib_variable like 'Impression.memory_limit'),
	'"2048M"',
	(select id from users where username like 'webrsa'),
	'WebRSA patch',
	current_date,
	current_date
);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
