SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.27.0-CG93', CURRENT_TIMESTAMP);

--Insertions des exceptions d'impression
-- Prestaorient PIE
with ins as (
	insert into exceptionsimpressionstypesorients
		(ordre, typeorient_id, modele_notif)
	values
	(
		(select coalesce(MAX(ordre), 0)+1 from exceptionsimpressionstypesorients where typeorient_id = (select id from typesorients where lib_type_orient = 'Projet Insertion Emploi')),
		(select id from typesorients where lib_type_orient = 'Projet Insertion Emploi'),
		'Orientation/PIE_SSD_prestaorient'
	)
	returning id
)
insert into exceptionsimpressionstypesorients_origines
	(excepimprtypeorient_id, origine)
values
(
	(select id from ins),
	'prestaorient'
);

-- Prestaorient Service social
with ins as (
	insert into exceptionsimpressionstypesorients
		(ordre, typeorient_id, modele_notif)
	values
	(
		(select coalesce(MAX(ordre), 0)+1 from exceptionsimpressionstypesorients where typeorient_id = (select id from typesorients where lib_type_orient = 'Service Social')),
		(select id from typesorients where lib_type_orient = 'Service Social'),
		'Orientation/PIE_SSD_prestaorient'
	)
	returning id
)
insert into exceptionsimpressionstypesorients_origines
	(excepimprtypeorient_id, origine)
values
(
	(select id from ins),
	'prestaorient'
);


-- Transfert Service social
with ins as (
	insert into exceptionsimpressionstypesorients
		(ordre, typeorient_id, modele_notif)
	values
	(
		(select coalesce(MAX(ordre), 0)+1 from exceptionsimpressionstypesorients where typeorient_id = (select id from typesorients where lib_type_orient = 'Service Social')),
		(select id from typesorients where lib_type_orient = 'Service Social'),
		'Transfertpdv93/mutation_social'
	)
	returning id
)
insert into exceptionsimpressionstypesorients_origines
	(excepimprtypeorient_id, origine)
values
(
	(select id from ins),
	'demenagement'
);

-- Transfert ALI
with ins as (
	insert into exceptionsimpressionstypesorients
		(ordre, typeorient_id, modele_notif)
	values
	(
		(select coalesce(MAX(ordre), 0)+1 from exceptionsimpressionstypesorients where typeorient_id = (select id from typesorients where lib_type_orient = 'Agence Locale d''Insertion')),
		(select id from typesorients where lib_type_orient = 'Agence Locale d''Insertion'),
		'Transfertpdv93/mutation_social'
	)
	returning id
)
insert into exceptionsimpressionstypesorients_origines
	(excepimprtypeorient_id, origine)
values
(
	(select id from ins),
	'demenagement'
);

-- Transfert PIE
with ins as (
	insert into exceptionsimpressionstypesorients
		(ordre, typeorient_id, modele_notif)
	values
	(
		(select coalesce(MAX(ordre), 0)+1 from exceptionsimpressionstypesorients where typeorient_id = (select id from typesorients where lib_type_orient = 'Projet Insertion Emploi')),
		(select id from typesorients where lib_type_orient = 'Projet Insertion Emploi'),
		'Transfertpdv93/mutation_social'
	)
	returning id
)
insert into exceptionsimpressionstypesorients_origines
	(excepimprtypeorient_id, origine)
values
(
	(select id from ins),
	'demenagement'
);

-- Transfert Association référente
with ins as (
	insert into exceptionsimpressionstypesorients
		(ordre, typeorient_id, modele_notif)
	values
	(
		(select coalesce(MAX(ordre), 0)+1 from exceptionsimpressionstypesorients where typeorient_id = (select id from typesorients where lib_type_orient = 'Association référente')),
		(select id from typesorients where lib_type_orient = 'Association référente'),
		'Transfertpdv93/mutation_social'
	)
	returning id
)
insert into exceptionsimpressionstypesorients_origines
	(excepimprtypeorient_id, origine)
values
(
	(select id from ins),
	'demenagement'
);

-- Transfert PE
with ins as (
	insert into exceptionsimpressionstypesorients
		(ordre, typeorient_id, modele_notif)
	values
	(
		(select coalesce(MAX(ordre), 0)+1 from exceptionsimpressionstypesorients where typeorient_id = (select id from typesorients where lib_type_orient = 'Pole Emploi')),
		(select id from typesorients where lib_type_orient = 'Pole Emploi'),
		'Transfertpdv93/mutation_emploi'
	)
	returning id
)
insert into exceptionsimpressionstypesorients_origines
	(excepimprtypeorient_id, origine)
values
(
	(select id from ins),
	'demenagement'
);

-- Transfert Mission locale
with ins as (
	insert into exceptionsimpressionstypesorients
		(ordre, typeorient_id, modele_notif)
	values
	(
		(select coalesce(MAX(ordre), 0)+1 from exceptionsimpressionstypesorients where typeorient_id = (select id from typesorients where lib_type_orient = 'Mission locale')),
		(select id from typesorients where lib_type_orient = 'Mission locale'),
		'Transfertpdv93/mutation_emploi'
	)
	returning id
)
insert into exceptionsimpressionstypesorients_origines
	(excepimprtypeorient_id, origine)
values
(
	(select id from ins),
	'demenagement'
);

--Modification de la variable de configuration pour les exceptions d'impression des types d'orientation
UPDATE public.configurations SET value_variable = '["Exceptionimpressiontypeorient.structurereferente_libelle","Exceptionimpressiontypeorient.zonesgeo"]'
WHERE lib_variable LIKE 'Exceptionsimpressiontypesorient.affichageprincipal';



-- *****************************************************************************
COMMIT;
-- *****************************************************************************
