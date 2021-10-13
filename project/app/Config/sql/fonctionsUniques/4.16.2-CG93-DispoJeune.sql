SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Reprise des données des anciens types d'orientation vers leurs enfants
-- public.structuresreferentes CHAMP typeorient_id
-- Type d'orientation Social
UPDATE public.structuresreferentes
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Service Social')
WHERE id IN (
	SELECT
		s.id
	FROM structuresreferentes s
	INNER JOIN typesorients t2 ON t2.id = s.typeorient_id
	WHERE t2.lib_type_orient = 'Social'
);

-- Type d'orientation Socioprofessionnelle
UPDATE public.structuresreferentes
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Projet Insertion Emploi')
WHERE id IN (
	SELECT
		s.id
	FROM structuresreferentes s
	INNER JOIN typesorients t2 ON t2.id = s.typeorient_id
	WHERE t2.lib_type_orient = 'Socioprofessionnelle'
);

-- Type d'orientation Emploi
UPDATE public.structuresreferentes
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Pole Emploi')
WHERE id IN (
	SELECT
		s.id
	FROM structuresreferentes s
	INNER JOIN typesorients t2 ON t2.id = s.typeorient_id
	WHERE t2.lib_type_orient = 'Emploi'
);

-- public.orientsstructs CHAMP typeorient_id
-- Type d'orientation Social
UPDATE public.orientsstructs
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Service Social')
WHERE id IN (
	SELECT
		o.id
	FROM orientsstructs o
	INNER JOIN typesorients t2 ON t2.id = o.typeorient_id
	WHERE t2.lib_type_orient = 'Social'
);

-- Type d'orientation Socioprofessionnelle
UPDATE public.orientsstructs
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Projet Insertion Emploi')
WHERE id IN (
	SELECT
		o.id
	FROM orientsstructs o
	INNER JOIN typesorients t2 ON t2.id = o.typeorient_id
	WHERE t2.lib_type_orient = 'Socioprofessionnelle'
);

-- Type d'orientation Emploi
UPDATE public.orientsstructs
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Pole Emploi')
WHERE id IN (
	SELECT
		o.id
	FROM orientsstructs o
	INNER JOIN typesorients t2 ON t2.id = o.typeorient_id
	WHERE t2.lib_type_orient = 'Emploi'
);

-- public.reorientationseps93 CHAMP typeorient_id
-- Type d'orientation Social
UPDATE public.reorientationseps93
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Service Social')
WHERE id IN (
	SELECT
		r.id
	FROM reorientationseps93 r
	INNER JOIN typesorients t2 ON t2.id = r.typeorient_id
	WHERE t2.lib_type_orient = 'Social'
);

-- Type d'orientation Socioprofessionnelle
UPDATE public.reorientationseps93
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Projet Insertion Emploi')
WHERE id IN (
	SELECT
		r.id
	FROM reorientationseps93 r
	INNER JOIN typesorients t2 ON t2.id = r.typeorient_id
	WHERE t2.lib_type_orient = 'Socioprofessionnelle'
);

-- Type d'orientation Emploi
UPDATE public.reorientationseps93
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Pole Emploi')
WHERE id IN (
	SELECT
		r.id
	FROM reorientationseps93 r
	INNER JOIN typesorients t2 ON t2.id = r.typeorient_id
	WHERE t2.lib_type_orient = 'Emploi'
);

-- public.decisionsreorientationseps93 CHAMP typeorient_id
-- Type d'orientation Social
UPDATE public.decisionsreorientationseps93
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Service Social')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsreorientationseps93 d
	INNER JOIN typesorients t2 ON t2.id = d.typeorient_id
	WHERE t2.lib_type_orient = 'Social'
);

-- Type d'orientation Socioprofessionnelle
UPDATE public.decisionsreorientationseps93
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Projet Insertion Emploi')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsreorientationseps93 d
	INNER JOIN typesorients t2 ON t2.id = d.typeorient_id
	WHERE t2.lib_type_orient = 'Socioprofessionnelle'
);

-- Type d'orientation Emploi
UPDATE public.decisionsreorientationseps93
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Pole Emploi')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsreorientationseps93 d
	INNER JOIN typesorients t2 ON t2.id = d.typeorient_id
	WHERE t2.lib_type_orient = 'Emploi'
);

-- public.decisionsnonorientationsproseps93 CHAMP typeorient_id
-- Type d'orientation Social
UPDATE public.decisionsnonorientationsproseps93
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Service Social')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsnonorientationsproseps93 d
	INNER JOIN typesorients t2 ON t2.id = d.typeorient_id
	WHERE t2.lib_type_orient = 'Social'
);

-- Type d'orientation Socioprofessionnelle
UPDATE public.decisionsnonorientationsproseps93
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Projet Insertion Emploi')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsnonorientationsproseps93 d
	INNER JOIN typesorients t2 ON t2.id = d.typeorient_id
	WHERE t2.lib_type_orient = 'Socioprofessionnelle'
);

-- Type d'orientation Emploi
UPDATE public.decisionsnonorientationsproseps93
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Pole Emploi')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsnonorientationsproseps93 d
	INNER JOIN typesorients t2 ON t2.id = d.typeorient_id
	WHERE t2.lib_type_orient = 'Emploi'
);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************