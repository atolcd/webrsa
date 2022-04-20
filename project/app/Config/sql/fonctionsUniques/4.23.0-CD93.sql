SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Insertions des critères de l'algorithme d'orientation
insert into criteresalgorithmeorientation (ordre, libelle, type_orient_parent_id, type_orient_enfant_id, valeurtag_id, actif, age_min, age_max, nb_enfants, nb_mois, code) values
(
	1,
	'L''allocataire est domicilié à une adresse correspondant à un hébergement par une association référente ?',
	(select t.parentid from typesorients t where t.lib_type_orient = 'Association référente'),
	(select t.id from typesorients t where t.lib_type_orient = 'Association référente'),
	null,
	true,
	'false',
	'false',
	'false',
	'false',
	'ASSO_REF'
),
(
	2,
	'L''allocataire est actuellement inscrit à Pôle emploi ?',
	(select t.parentid from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),
	(select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),
	null,
	true,
	'false',
	'false',
	'false',
	'false',
	'INSCRIT_PE'
),
(
	3,
	'L''allocataire a été inscrit à Pôle Emploi au moins une fois dans les %d derniers mois',
	(select t.parentid from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),
	(select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),
	null,
	true,
	'false',
	'false',
	'false',
	0,
	'INSCRIT_PE_DERNIERS_MOIS'
),
(
	4,
	'L''allocataire est âgé entre %d et %d ans ?',
	(select t.parentid from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),
	(select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),
	(select v.id from valeurstags v where v.name = 'Dispositif jeune'),
	true,
	0,
	0,
	'false',
	'false',
	'JEUNE'
),
(
	5,
	'L''allocataire est en capacité d’engager rapidement un emploi ?',
	(select t.parentid from typesorients t where t.lib_type_orient = 'Projet Insertion Emploi'),
	(select t.id from typesorients t where t.lib_type_orient = 'Projet Insertion Emploi'),
	null,
	true,
	'false',
	'false',
	'false',
	'false',
	'ENGAGEMENT_RAPIDE_EMPLOI'
),
(
	6,
	'L''allocataire est au RSA Majoré ?',
	(select t.parentid from typesorients t where t.lib_type_orient = 'Service Social'),
	(select t.id from typesorients t where t.lib_type_orient = 'Service Social'),
	null,
	true,
	'false',
	'false',
	'false',
	'false',
	'RSA_MAJORE'
),
(
	7,
	'L''allocataire est dans un foyer monoparental avec 3 enfants ou plus à charge ?',
	(select t.parentid from typesorients t where t.lib_type_orient = 'Service Social'),
	(select t.id from typesorients t where t.lib_type_orient = 'Service Social'),
	null,
	true,
	'false',
	'false',
	'false',
	'false',
	'FOYER_MONOPARENTAL'
),
(
	8,
	'L''allocataire est dans un logement d’urgence, temporaire ou précaire et avec au moins %d enfant à charge ?',
	(select t.parentid from typesorients t where t.lib_type_orient = 'Service Social'),
	(select t.id from typesorients t where t.lib_type_orient = 'Service Social'),
	null,
	true,
	'false',
	'false',
	0,
	'false',
	'LOGEMENT_URGENCE'
),
(
	9,
	'L''allocataire est en difficulté pour la compréhension, lecture ou écriture du Français, et il s’agit de sa seule difficulté sociale repérée ?',
	(select t.parentid from typesorients t where t.lib_type_orient = 'Projet Insertion Emploi'),
	(select t.id from typesorients t where t.lib_type_orient = 'Projet Insertion Emploi'),
	null,
	true,
	'false',
	'false',
	'false',
	'false',
	'DIFFICULTES_FRANCAIS'
),
(
	10,
	'L''allocataire fait face à une ou plusieurs difficultés sociales repérées, combinées ou non à des difficultés de langues ?',
	(select t.parentid from typesorients t where t.lib_type_orient = 'Service Social'),
	(select t.id from typesorients t where t.lib_type_orient = 'Service Social'),
	null,
	true,
	'false',
	'false',
	'false',
	'false',
	'DIFFICULTES_SOC'
),
(
	11,
	'L''allocataire est âgé entre %d et %d ans ?',
	(select t.parentid from typesorients t where t.lib_type_orient = 'Service Social'),
	(select t.id from typesorients t where t.lib_type_orient = 'Service Social'),
	null,
	true,
	1,
	1,
	'false',
	'false',
	'SENIOR'
),
(
	12,
	'L''allocataire n’a jamais travaillé d’après son dossier ?',
	(select t.parentid from typesorients t where t.lib_type_orient = 'Service Social'),
	(select t.id from typesorients t where t.lib_type_orient = 'Service Social'),
	null,
	true,
	'false',
	'false',
	'false',
	'false',
	'JAMAIS_TRAVAILLE'
),
(
	13,
	'L''allocataire en est à sa seconde inscription au RSA ou plus ?',
	(select t.parentid from typesorients t where t.lib_type_orient = 'Projet Insertion Emploi'),
	(select t.id from typesorients t where t.lib_type_orient = 'Projet Insertion Emploi'),
	null,
	true,
	'false',
	'false',
	'false',
	'false',
	'SECONDE_INSCRIPTION_RSA'
),
(
	14,
	'L''allocataire n’a pu se voir proposer un parcours par aucun des critères précédents ?',
	(select t.parentid from typesorients t where t.lib_type_orient = 'Projet Insertion Emploi'),
	(select t.id from typesorients t where t.lib_type_orient = 'Projet Insertion Emploi'),
	(select v.id from valeurstags v where v.name = 'Entretien de diagnostic'),
	true,
	'false',
	'false',
	'false',
	'false',
	'FINAL'
);

-- Insertion des adresses des associations référentes
insert into permanences (structurereferente_id, libpermanence, numvoie, typevoie, nomvoie, codepos, ville, actif) values
(
	(select s.id from structuresreferentes s where s.lib_struc = 'Emmaüs Alternatives'),
	'Emmaüs',
	'260',
	'RUE',
	'DE ROSNY',
	'93100',
	'MONTREUIL',
	'O'
),
(
	(select s.id from structuresreferentes s where s.lib_struc = 'ADEPT'),
	'ADEPT',
	'37',
	'RUE',
	'VOLTAIRE',
	'93700',
	'DRANCY',
	'O'
);


insert into public.structuresreferentes_typesorients_zonesgeographiques
(typeorient_id, zonegeographique_id, structurereferente_id)
values
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93001'),3),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93005'),2),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93006'),8),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93008'),9),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93010'),14),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93014'),23),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93015'),18),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93029'),22),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93030'),16),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93031'),26),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93032'),28),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93033'),44),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93039'),117),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93027'),20),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93007'),46),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93013'),16),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93061'),51),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93062'),59),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93045'),51),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93046'),34),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93047'),18),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93048'),39),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93049'),42),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93050'),42),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93051'),44),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93053'),47),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93055'),49),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93057'),48),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93059'),50),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93063'),52),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93064'),53),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93066'),54),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93070'),56),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93071'),55),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93072'),57),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93073'),58),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93074'),34),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93077'),59),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93078'),60),
((select t.id from typesorients t where t.lib_type_orient ilike '%Service Social%'),(select id from zonesgeographiques z where z.codeinsee = '93079'),30),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93001'),74),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93005'),75),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93006'),84),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93008'),77),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93010'),78),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93014'),133),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93015'),83),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93029'),99),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93030'),80),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93031'),79),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93032'),133),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93033'),85),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93039'),94),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93027'),80),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93007'),81),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93013'),80),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93061'),88),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93062'),83),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93045'),87),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93046'),83),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93047'),133),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93048'),84),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93049'),85),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93050'),85),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93051'),86),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93053'),87),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93055'),88),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93057'),113),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93059'),96),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93063'),89),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93064'),90),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93066'),91),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93070'),94),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93071'),95),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93072'),96),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93073'),97),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93074'),83),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93077'),90),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93078'),97),
((select t.id from typesorients t where t.lib_type_orient ilike '%Pole Emploi%'),(select id from zonesgeographiques z where z.codeinsee = '93079'),79),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93001'),1),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93005'),114),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93006'),7),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93008'),10),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93010'),13),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93014'),17),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93029'),21),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93031'),24),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93032'),149),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93039'),29),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93027'),19),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93007'),11),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93013'),15),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93061'),64),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93045'),31),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93046'),33),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93047'),17),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93048'),38),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93049'),41),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93050'),40),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93051'),43),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93053'),45),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93055'),61),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93057'),62),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93063'),65),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93064'),66),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93066'),67),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93070'),68),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93071'),69),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93073'),71),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93078'),73),
((select t.id from typesorients t where t.lib_type_orient ilike '%Projet Insertion Emploi'),(select id from zonesgeographiques z where z.codeinsee = '93079'),32),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93005'),151),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93008'),148),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93014'),145),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93015'),145),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93032'),152),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93061'),148),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93062'),145),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93045'),148),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93046'),145),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93047'),145),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93049'),150),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93050'),150),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93055'),148),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93057'),152),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93059'),139),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93064'),150),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93066'),139),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93071'),141),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93073'),141),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93074'),145),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93077'),152),
((select t.id from typesorients t where t.lib_type_orient ilike '%RSA Jeune'),(select id from zonesgeographiques z where z.codeinsee = '93078'),141),
((select t.id from typesorients t where t.lib_type_orient ilike '%Association%'),(select id from zonesgeographiques z where z.codeinsee = '93029'),106),
((select t.id from typesorients t where t.lib_type_orient ilike '%Association%'),(select id from zonesgeographiques z where z.codeinsee = '93048'),104);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
