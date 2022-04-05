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
	(select t.parentid from typesorients t where t.lib_type_orient = 'Expérimentation RSA Jeune'),
	(select t.id from typesorients t where t.lib_type_orient = 'Expérimentation RSA Jeune'),
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

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
