-- *****************************************************************************
-- Nécessite la correction de la fonction plpgsql public.calcul_cle_nir( TEXT ) du patch 2.0-rc16
-- *****************************************************************************

-- *****************************************************************************
-- Initialisation: personnes
-- *****************************************************************************

-- -----------------------------------------------------------------------------
-- Combien a-t'on de personnes ?
--
-- cg58_20101124_eps:		18067
-- cg66_20101217_eps:		93407
-- cg93_20110215_2203_eps:	454546
-- -----------------------------------------------------------------------------

SELECT COUNT(*) FROM personnes;

-- -----------------------------------------------------------------------------
-- Combien a-t'on de personnes demandeurs ou conjoints RSA ?
--
-- cg58_20101124_eps:		9928
-- cg66_20101217_eps:		53450
-- cg93_20110215_2203_eps:	230663
-- -----------------------------------------------------------------------------

SELECT
		COUNT(DISTINCT(personnes.id))
	FROM personnes
		INNER JOIN prestations ON (
			personnes.id = prestations.personne_id
			AND prestations.natprest = 'RSA'
		)
	WHERE prestations.rolepers IN ( 'DEM', 'CJT' );

-- -----------------------------------------------------------------------------
-- Combien a-t'on de personnes non demandeurs ou conjoints RSA ?
--
-- cg58_20101124_eps:		8139
-- cg66_20101217_eps:		39889
-- cg93_20110215_2203_eps:	223873
-- -----------------------------------------------------------------------------

SELECT
		COUNT(DISTINCT(personnes.id))
	FROM personnes
		INNER JOIN prestations ON (
			personnes.id = prestations.personne_id
			AND prestations.natprest = 'RSA'
		)
	WHERE prestations.rolepers NOT IN ( 'DEM', 'CJT' );

-- -----------------------------------------------------------------------------
-- Combien a-t'on de personnes sans prestation RSA ?
--
-- cg58_20101124_eps:		0
-- cg66_20101217_eps:		111
-- cg93_20110215_2203_eps:	10
-- -----------------------------------------------------------------------------

SELECT
		COUNT(DISTINCT(personnes.id))
	FROM personnes
	WHERE (
		SELECT
				COUNT(prestations.*)
			FROM prestations
			WHERE prestations.personne_id = personnes.id
				AND prestations.natprest = 'RSA'
	) = 0;

-- -----------------------------------------------------------------------------
-- Combien a-t'on de personnes avec plus d'une prestation RSA ?
--
-- cg58_20101124_eps:		0
-- cg66_20101217_eps:		56
-- cg93_20110215_2203_eps:	0
-- -----------------------------------------------------------------------------

SELECT
		COUNT(DISTINCT(personnes.id))
	FROM personnes
	WHERE (
		SELECT
				COUNT(prestations.*)
			FROM prestations
			WHERE prestations.personne_id = personnes.id
				AND prestations.natprest = 'RSA'
	) > 1;

-- -----------------------------------------------------------------------------
-- Vérification du comptage des différents cas:
--
-- cg58_20101124_eps:		18067 - ( 9928 + 8139 + 0 - 0 ) = 0
-- cg66_20101217_eps:		93407 - ( 53450 + 39889 + 111 - 56 ) = 13 -- FIXME -> à nettoyer
-- cg93_20110215_2203_eps:	454546 - ( 230663 + 223873 + 10 - 0 ) = 0
-- -----------------------------------------------------------------------------

-- *****************************************************************************
-- Requêtes de déctection de doublons pour les personnes
-- *****************************************************************************

-- FIXME: scinder les requêtes (nir/dtnai, nom/prenom/dtnai)
--	- pas les 2 NIRs corrects, prénom avec une lettre qui change -> Dossier: 10748007093

-- -----------------------------------------------------------------------------
-- Combien a-t'on de demandeurs ou conjoints RSA en doublons dans le même foyer ?
--
-- On considère qu'il y a doublon lorsque:
--     * les NIR sont corrects, sont identiques et que les dates de naissance sont identiques
--     * les noms, les prénoms et les dates de naissance sont identiques
--
-- cg58_20101124_eps:		8		8 / 9928		-> 0.08 % des demandeurs ou conjoints
-- cg66_20101217_eps:		3478	3478 / 53450	-> 6.51 % des demandeurs ou conjoints
-- cg93_20110215_2203_eps:	7978	7978 / 230663	-> 3.46 % des demandeurs ou conjoints
-- -----------------------------------------------------------------------------

SELECT
		COUNT(*)
--		p1.id, p1.nir, p1.dtnai, p1.foyer_id, p2.id, p2.nir, p2.dtnai, p2.foyer_id
	FROM personnes p1, personnes p2
	WHERE
		p1.id < p2.id
		AND p1.foyer_id = p2.foyer_id
		AND ( SELECT COUNT(*) FROM prestations WHERE prestations.natprest = 'RSA' AND prestations.personne_id = p1.id AND prestations.rolepers IN ( 'DEM', 'CJT' ) ) > 0
		AND ( SELECT COUNT(*) FROM prestations WHERE prestations.natprest = 'RSA' AND prestations.personne_id = p2.id AND prestations.rolepers IN ( 'DEM', 'CJT' ) ) > 0
		AND (
			( nir_correct( p1.nir ) AND p1.nir = p2.nir AND p1.dtnai = p2.dtnai )
			OR (
				TRIM( BOTH ' ' FROM p1.nom ) = TRIM( BOTH ' ' FROM p2.nom )
				AND TRIM( BOTH ' ' FROM p1.prenom ) = TRIM( BOTH ' ' FROM p2.prenom )
				AND p1.dtnai = p2.dtnai
			)
		);

-- -----------------------------------------------------------------------------
-- Combien a-t'on de non demandeurs et non conjoints RSA en doublons dans le même foyer ?
--
-- On considère qu'il y a doublon lorsque:
--     * les NIR sont corrects, sont identiques et que les dates de naissance sont identiques
--     * les noms, les prénoms et les dates de naissance sont identiques
--
-- cg58_20101124_eps:		4		4 / 8139		0.05 % des personnes ...
-- cg66_20101217_eps:		1011	1011 / 39889	2.53 % des personnes ...
-- cg93_20110215_2203_eps:	6846	6846 / 223873	3.06 % des personnes ...
-- -----------------------------------------------------------------------------

SELECT
		COUNT(*)
--		p1.id, p1.nir, p1.dtnai, p1.foyer_id, p2.id, p2.nir, p2.dtnai, p2.foyer_id
	FROM personnes p1, personnes p2
	WHERE
		p1.id < p2.id
		AND p1.foyer_id = p2.foyer_id
		AND ( SELECT COUNT(*) FROM prestations WHERE prestations.natprest = 'RSA' AND prestations.personne_id = p1.id AND prestations.rolepers NOT IN ( 'DEM', 'CJT' ) ) > 0
		AND ( SELECT COUNT(*) FROM prestations WHERE prestations.natprest = 'RSA' AND prestations.personne_id = p2.id AND prestations.rolepers NOT IN ( 'DEM', 'CJT' ) ) > 0
		AND (
			(
				nir_correct( p1.nir )
				AND p1.nir = p2.nir
				AND p1.dtnai = p2.dtnai
			)
			OR (
				TRIM( BOTH ' ' FROM p1.nom ) = TRIM( BOTH ' ' FROM p2.nom )
				AND TRIM( BOTH ' ' FROM p1.prenom ) = TRIM( BOTH ' ' FROM p2.prenom )
				AND p1.dtnai = p2.dtnai )
		);

-- TODO: même foyer, natprest <> (1 x dem/cjt, 1 x enfant)

-- cg58_20101124_eps:		8		8 / 9928		-> 0.08 % des personnes ...
-- cg66_20101217_eps:		3478	3478 / 53450	-> 6.51 % des personnes ...
-- cg93_20110215_2203_eps:	7978	7978 / 230663	-> 3.46 % des personnes ...

-- -----------------------------------------------------------------------------
-- Combien a-t'on de demandeurs ou conjoints RSA en doublons dans des foyers différents dont les droits sont ouverts ?
--
-- On considère qu'il y a doublon lorsque:
--     * les NIR sont corrects, sont identiques et que les dates de naissance sont identiques
--     * les noms, les prénoms et les dates de naissance sont identiques
--
-- cg58_20101124_eps:		5		5 / 9928		-> 0.05 % des demandeurs ou conjoints
-- cg66_20101217_eps:		2395	2395 / 53450	-> 4.48 % des demandeurs ou conjoints
-- cg93_20110215_2203_eps:	4253	4253 / 230663	-> 1.84 % des demandeurs ou conjoints
-- -----------------------------------------------------------------------------

SELECT
		COUNT(*)
--		p1.id, p1.nir, p1.dtnai, p1.foyer_id, p2.id, p2.nir, p2.dtnai, p2.foyer_id
	FROM
		personnes p1
			INNER JOIN foyers AS f1 ON p1.foyer_id = f1.id
			INNER JOIN situationsdossiersrsa AS s1 ON ( f1.dossier_id = s1.dossier_id ),
		personnes p2
			INNER JOIN foyers AS f2 ON p2.foyer_id = f2.id
			INNER JOIN situationsdossiersrsa AS s2 ON ( f2.dossier_id = s2.dossier_id )
	WHERE
		p1.id < p2.id
		AND p1.foyer_id = p2.foyer_id
		AND ( SELECT COUNT(*) FROM prestations WHERE prestations.natprest = 'RSA' AND prestations.personne_id = p1.id AND prestations.rolepers IN ( 'DEM', 'CJT' ) ) > 0
		AND ( SELECT COUNT(*) FROM prestations WHERE prestations.natprest = 'RSA' AND prestations.personne_id = p2.id AND prestations.rolepers IN ( 'DEM', 'CJT' ) ) > 0
		AND (
			( nir_correct( p1.nir ) AND p1.nir = p2.nir AND p1.dtnai = p2.dtnai )
			OR (
				TRIM( BOTH ' ' FROM p1.nom ) = TRIM( BOTH ' ' FROM p2.nom )
				AND TRIM( BOTH ' ' FROM p1.prenom ) = TRIM( BOTH ' ' FROM p2.prenom )
				AND p1.dtnai = p2.dtnai
			)
		)
		AND s1.etatdosrsa IN ( 'Z', '2', '3', '4' )
		AND s2.etatdosrsa IN ( 'Z', '2', '3', '4' );

-- -----------------------------------------------------------------------------
-- FIXME: continuer ici
-- DEM OU CJT dans le même foyer avec des NIR erronés ou ne correspondant pas et soit un nom et une date de naissance identiques, soit un prénom et une date de naissance identiques.

-- cg58_20101124_eps:		4
-- cg66_20101217_eps:		165
-- cg93_20110215_2203_eps:	1003
-- -----------------------------------------------------------------------------

SELECT
		COUNT(*)
--		p1.id, p1.nir, p1.dtnai, p1.foyer_id, p2.id, p2.nir, p2.dtnai, p2.foyer_id
	FROM personnes p1, personnes p2
	WHERE
		p1.id < p2.id
		AND p1.foyer_id = p2.foyer_id
		AND ( SELECT COUNT(*) FROM prestations WHERE prestations.natprest = 'RSA' AND prestations.personne_id = p1.id AND prestations.rolepers IN ( 'DEM', 'CJT' ) ) > 0
		AND ( SELECT COUNT(*) FROM prestations WHERE prestations.natprest = 'RSA' AND prestations.personne_id = p2.id AND prestations.rolepers IN ( 'DEM', 'CJT' ) ) > 0
		AND (
			NOT ( nir_correct( p1.nir ) AND p1.nir = p2.nir AND p1.dtnai = p2.dtnai )
			AND (
				(
					TRIM( BOTH ' ' FROM p1.nom ) <> TRIM( BOTH ' ' FROM p2.nom )
					AND TRIM( BOTH ' ' FROM p1.prenom ) = TRIM( BOTH ' ' FROM p2.prenom )
					AND p1.dtnai = p2.dtnai
				)
				OR (
					TRIM( BOTH ' ' FROM p1.nom ) = TRIM( BOTH ' ' FROM p2.nom )
					AND TRIM( BOTH ' ' FROM p1.prenom ) <> TRIM( BOTH ' ' FROM p2.prenom )
					AND p1.dtnai = p2.dtnai
				)
			)
		);

-- *****************************************************************************
-- Initialisation: foyers
-- *****************************************************************************

-- -----------------------------------------------------------------------------
-- Combien a-t'on de foyers ?
--
-- cg58_20101124_eps:		7933
-- cg66_20101217_eps:		39592
-- cg93_20110215_2203_eps:	160353
-- -----------------------------------------------------------------------------

SELECT COUNT(*) FROM foyers;

-- -----------------------------------------------------------------------------
-- Combien a-t'on de foyers dont les droits sont ouverts ?
--
-- cg58_20101124_eps:		7380	( 7380 / 7933 = 93.03 % )
-- cg66_20101217_eps:		26695	( 26695 / 39592 = 67.43 % )
-- cg93_20110215_2203_eps:	88293	( 88293 / 160353 = 55.06 % )
-- -----------------------------------------------------------------------------

SELECT
		COUNT(*)
	FROM foyers
		INNER JOIN situationsdossiersrsa ON ( foyers.dossier_id = situationsdossiersrsa.dossier_id )
	WHERE situationsdossiersrsa.etatdosrsa IN ( 'Z', '2', '3', '4' );

-- *****************************************************************************
-- Foyers
-- *****************************************************************************

-- -----------------------------------------------------------------------------
-- Combien a-t-on de foyers possédant plus d'un demandeur ou conjoint RSA
--
-- cg58_20101124_eps:		27		-> 27 / 7933		 0.34 %
-- cg66_20101217_eps:		3986	-> 3986 / 39592		10.07 %
-- cg93_20110215_2203_eps:	11910	-> 11910 / 160353	 7.43 %
-- -----------------------------------------------------------------------------

SELECT
		COUNT(foyers.*)
	FROM foyers
	WHERE (
		( SELECT COUNT(*) FROM prestations INNER JOIN personnes ON prestations.personne_id = personnes.id WHERE prestations.natprest = 'RSA' AND personnes.foyer_id = foyers.id AND prestations.rolepers = 'DEM' ) > 1
		OR ( SELECT COUNT(*) FROM prestations INNER JOIN personnes ON prestations.personne_id = personnes.id WHERE prestations.natprest = 'RSA' AND personnes.foyer_id = foyers.id AND prestations.rolepers = 'CJT' ) > 1
	);

-- -----------------------------------------------------------------------------
-- Combien a-t-on de foyers dont les droits sont ouverts possédant plus d'un demandeur ou conjoint RSA
--
-- cg58_20101124_eps:		22		-> 22 / 7380		 0.30 % des foyers ouverts
-- cg66_20101217_eps:		2779	-> 2779 / 26695		10.41 % des foyers ouverts
-- cg93_20110215_2203_eps:	6435	-> 6435 / 88293		 7.29 % des foyers ouverts
-- -----------------------------------------------------------------------------

SELECT
		COUNT(foyers.*)
	FROM foyers
		INNER JOIN situationsdossiersrsa ON ( foyers.dossier_id = situationsdossiersrsa.dossier_id )
	WHERE
		situationsdossiersrsa.etatdosrsa IN ( 'Z', '2', '3', '4' )
		AND (
		( SELECT COUNT(*) FROM prestations INNER JOIN personnes ON prestations.personne_id = personnes.id WHERE prestations.natprest = 'RSA' AND personnes.foyer_id = foyers.id AND prestations.rolepers = 'DEM' ) > 1
		OR ( SELECT COUNT(*) FROM prestations INNER JOIN personnes ON prestations.personne_id = personnes.id WHERE prestations.natprest = 'RSA' AND personnes.foyer_id = foyers.id AND prestations.rolepers = 'CJT' ) > 1
	);

-- -----------------------------------------------------------------------------
-- Combien a-t-on de foyers sans demandeur RSA contenant au plus un conjoint FIXME: et au moins une personne
--
-- cg58_20101124_eps:		33		-> 33 / 7933	 0.42 %
-- cg66_20101217_eps:		46		-> 46 / 39592	 0.12 %
-- cg93_20110215_2203_eps:	43		-> 43 / 160353	 0.03 %
-- -----------------------------------------------------------------------------

SELECT
		COUNT(foyers.*)
	FROM foyers
	WHERE (
		( SELECT COUNT(*) FROM prestations INNER JOIN personnes ON prestations.personne_id = personnes.id WHERE prestations.natprest = 'RSA' AND personnes.foyer_id = foyers.id AND prestations.rolepers = 'DEM' ) = 0
		AND ( SELECT COUNT(*) FROM prestations INNER JOIN personnes ON prestations.personne_id = personnes.id WHERE prestations.natprest = 'RSA' AND personnes.foyer_id = foyers.id AND prestations.rolepers = 'CJT' ) <= 1
	);

-- FIXME: Combien a-t-on de foyers sans personne RSAs