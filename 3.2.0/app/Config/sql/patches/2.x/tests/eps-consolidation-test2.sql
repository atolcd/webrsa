-- =============================================================================
-- Morceaux de requêtes à réemployer
-- =============================================================================

-- Personnes possédant plusieurs orientations validées
SELECT COUNT(DISTINCT(personnes.id))
	FROM personnes
	WHERE (
		SELECT COUNT(orientsstructs.id)
			FROM orientsstructs
			WHERE orientsstructs.personne_id = personnes.id
				AND orientsstructs.date_valid IS NOT NULL
				AND orientsstructs.statut_orient = 'Orienté'
	) > 1;

-- -----------------------------------------------------------------------------

-- Personnes possédant plusieurs CER validés
SELECT COUNT(DISTINCT(personnes.id))
	FROM personnes
	WHERE (
		SELECT COUNT(contratsinsertion.id)
			FROM contratsinsertion
			WHERE contratsinsertion.personne_id = personnes.id
				AND contratsinsertion.datevalidation_ci IS NOT NULL
	) > 1;

-- -----------------------------------------------------------------------------

-- Derniers contrat d'insertion validés et en cours
SELECT COUNT(DISTINCT(contratsinsertion.id))
	FROM contratsinsertion
	WHERE contratsinsertion.id = (
		SELECT dernierci.id
			FROM contratsinsertion AS dernierci
			WHERE dernierci.personne_id = contratsinsertion.personne_id
				AND dernierci.datevalidation_ci IS NOT NULL
				AND dernierci.structurereferente_id IS NOT NULL
				AND dernierci.df_ci >= CURRENT_DATE
			ORDER BY dernierci.dd_ci DESC
			LIMIT 1
	);

-- -----------------------------------------------------------------------------

-- Dernières orientations en cours
SELECT COUNT(DISTINCT(orientsstructs.id))
	FROM orientsstructs
	WHERE orientsstructs.id = (
		SELECT derniereorient.id
			FROM orientsstructs AS derniereorient
			WHERE derniereorient.personne_id = orientsstructs.personne_id
				AND derniereorient.date_valid IS NOT NULL
				AND derniereorient.statut_orient = 'Orienté'
			ORDER BY derniereorient.date_valid DESC
			LIMIT 1
	);

-- -----------------------------------------------------------------------------

-- Combien existe-t'il de personnes distinctes ?
-- 1°) demandeur ou conjoint RSA
-- 2°) qui sont soumises à droits et devoirs
--     ET qui se trouvent dans un dossier dont les droits sont ouverts

SELECT COUNT(DISTINCT(personnes.id))
	FROM personnes
		INNER JOIN prestations ON (
			prestations.personne_id = personnes.id
			AND prestations.natprest = 'RSA'
			AND prestations.rolepers IN ( 'DEM', 'CJT' )
		)
		INNER JOIN calculsdroitsrsa ON (
			personnes.id = calculsdroitsrsa.personne_id
		)
		INNER JOIN foyers ON (
			personnes.foyer_id = foyers.id
		)
		INNER JOIN dossiers ON (
			foyers.dossier_id = dossiers.id
		)
		INNER JOIN situationsdossiersrsa ON (
			situationsdossiersrsa.dossier_id = dossiers.id
		)
	WHERE calculsdroitsrsa.toppersdrodevorsa = '1'
		AND situationsdossiersrsa.etatdosrsa IN ( '2', '3', '4' );

-- =============================================================================
-- Requêtes rapportant des cas généraux (contratsinsertion)
-- =============================================================================

-- Combien existe-t'il de personnes distinctes ?
-- 1°) possédant un CER validé et en cours actuellement
SELECT COUNT(DISTINCT(contratsinsertion.personne_id))
	FROM contratsinsertion
		INNER JOIN personnes ON (
			contratsinsertion.personne_id = personnes.id
			AND contratsinsertion.id = (
					SELECT dernierci.id
						FROM contratsinsertion AS dernierci
						WHERE dernierci.personne_id = contratsinsertion.personne_id
							AND dernierci.datevalidation_ci IS NOT NULL
							AND dernierci.structurereferente_id IS NOT NULL
							AND dernierci.df_ci >= CURRENT_DATE
						ORDER BY dernierci.dd_ci DESC
						LIMIT 1
				)
		);

-- =============================================================================
-- Requêtes rapportant des cas normaux
-- =============================================================================

-- Combien existe-t'il de personnes distinctes ?
-- 1°) demandeur ou conjoint RSA
-- 2°) possédant un CER validé et en cours actuellement
-- 3°) qui sont soumises à droits et devoirs
--     ET qui se trouvent dans un dossier dont les droits sont ouverts

SELECT COUNT(DISTINCT(contratsinsertion.personne_id))
	FROM contratsinsertion
		INNER JOIN personnes ON (
			contratsinsertion.personne_id = personnes.id
			AND contratsinsertion.id = (
					SELECT dernierci.id
						FROM contratsinsertion AS dernierci
						WHERE dernierci.personne_id = contratsinsertion.personne_id
							AND dernierci.datevalidation_ci IS NOT NULL
							AND dernierci.structurereferente_id IS NOT NULL
							AND dernierci.df_ci >= CURRENT_DATE
						ORDER BY dernierci.dd_ci DESC
						LIMIT 1
				)
		)
		INNER JOIN prestations ON (
			prestations.personne_id = personnes.id
			AND prestations.natprest = 'RSA'
			AND prestations.rolepers IN ( 'DEM', 'CJT' )
		)
		INNER JOIN calculsdroitsrsa ON (
			personnes.id = calculsdroitsrsa.personne_id
		)
		INNER JOIN foyers ON (
			personnes.foyer_id = foyers.id
		)
		INNER JOIN dossiers ON (
			foyers.dossier_id = dossiers.id
		)
		INNER JOIN situationsdossiersrsa ON (
			situationsdossiersrsa.dossier_id = dossiers.id
		)
	WHERE calculsdroitsrsa.toppersdrodevorsa = '1'
		AND situationsdossiersrsa.etatdosrsa IN ( '2', '3', '4' );

-- =============================================================================
-- Requêtes rapportant des cas bizzarres
-- =============================================================================

-- Combien existe-t'il de personnes distinctes ?
-- 1°) possédant un CER validé et en cours actuellement
-- 2°) demandeurs ou conjoints RSA
-- 3°) ne possédant pas d'entrée dans la table calculsdroitsrsa (or, cf. XMLRSACGbénéficiaire20100319.xls / VRSB0402-Cristal V32, cette rebrique devait être renseignée dans le flux)

SELECT COUNT(DISTINCT(contratsinsertion.personne_id))
	FROM contratsinsertion
		INNER JOIN personnes ON (
			contratsinsertion.personne_id = personnes.id
			AND contratsinsertion.id = (
					SELECT dernierci.id
						FROM contratsinsertion AS dernierci
						WHERE dernierci.personne_id = contratsinsertion.personne_id
							AND dernierci.datevalidation_ci IS NOT NULL
							AND dernierci.structurereferente_id IS NOT NULL
							AND dernierci.df_ci >= CURRENT_DATE
						ORDER BY dernierci.dd_ci DESC
						LIMIT 1
				)
		)
		INNER JOIN prestations ON (
			prestations.personne_id = contratsinsertion.personne_id
			AND prestations.natprest = 'RSA'
			AND prestations.rolepers IN ( 'DEM', 'CJT' )
		)
	WHERE contratsinsertion.personne_id NOT IN (
			SELECT DISTINCT(calculsdroitsrsa.personne_id)
				FROM calculsdroitsrsa
		);

-- Combien existe-t'il de personnes distinctes ?
-- 1°) demandeur ou conjoint RSA
-- 2°) possédant un CER validé et en cours actuellement
-- 3°) qui NE sont PAS soumises à droits et devoirs
--     OU qui se trouvent dans un dossier dont les droits NE sont PAS ouverts

SELECT COUNT(DISTINCT(contratsinsertion.personne_id))
	FROM contratsinsertion
		INNER JOIN personnes ON (
			contratsinsertion.personne_id = personnes.id
			AND contratsinsertion.id = (
					SELECT dernierci.id
						FROM contratsinsertion AS dernierci
						WHERE dernierci.personne_id = contratsinsertion.personne_id
							AND dernierci.datevalidation_ci IS NOT NULL
							AND dernierci.structurereferente_id IS NOT NULL
							AND dernierci.df_ci >= CURRENT_DATE
						ORDER BY dernierci.dd_ci DESC
						LIMIT 1
				)
		)
		INNER JOIN prestations ON (
			prestations.personne_id = personnes.id
			AND prestations.natprest = 'RSA'
			AND prestations.rolepers IN ( 'DEM', 'CJT' )
		)
		INNER JOIN calculsdroitsrsa ON (
			personnes.id = calculsdroitsrsa.personne_id
		)
		INNER JOIN foyers ON (
			personnes.foyer_id = foyers.id
		)
		INNER JOIN dossiers ON (
			foyers.dossier_id = dossiers.id
		)
		INNER JOIN situationsdossiersrsa ON (
			situationsdossiersrsa.dossier_id = dossiers.id
		)
	WHERE calculsdroitsrsa.toppersdrodevorsa <> '1'
		OR situationsdossiersrsa.etatdosrsa NOT IN ( '2', '3', '4' );

-- Combien existe-t'il de personnes distinctes ?
-- 1°) NON demandeur ET NON conjoint RSA
-- 2°) possédant un CER validé et en cours actuellement

SELECT COUNT(DISTINCT(contratsinsertion.personne_id))
	FROM contratsinsertion
		INNER JOIN personnes ON (
			contratsinsertion.personne_id = personnes.id
			AND contratsinsertion.id = (
					SELECT dernierci.id
						FROM contratsinsertion AS dernierci
						WHERE dernierci.personne_id = contratsinsertion.personne_id
							AND dernierci.datevalidation_ci IS NOT NULL
							AND dernierci.structurereferente_id IS NOT NULL
							AND dernierci.df_ci >= CURRENT_DATE
						ORDER BY dernierci.dd_ci DESC
						LIMIT 1
				)
		)
		INNER JOIN prestations ON (
			prestations.personne_id = personnes.id
			AND prestations.natprest = 'RSA'
			AND prestations.rolepers NOT IN ( 'DEM', 'CJT' )
		);

-- =============================================================================
-- Requêtes rapportant des cas généraux (orientsstructs)
-- =============================================================================

-- Combien existe-t'il de personnes distinctes ?
-- 1°) possédant une orientation validée et en cours actuellement
SELECT COUNT(DISTINCT(orientsstructs.personne_id))
	FROM orientsstructs
		INNER JOIN personnes ON (
			orientsstructs.personne_id = personnes.id
			AND orientsstructs.id = (
				SELECT derniereorient.id
					FROM orientsstructs AS derniereorient
					WHERE derniereorient.personne_id = orientsstructs.personne_id
						AND derniereorient.date_valid IS NOT NULL
						AND derniereorient.statut_orient = 'Orienté'
					ORDER BY derniereorient.date_valid DESC
					LIMIT 1
			)
		);

-- Combien existe-t'il de personnes distinctes ?
-- 1°) possédant une orientation validée et en cours actuellement
-- 2°) possédant un CER validé et en cours actuellement
SELECT COUNT(DISTINCT(personnes.id))
	FROM personnes
		INNER JOIN orientsstructs ON (
			orientsstructs.personne_id = personnes.id
			AND orientsstructs.id = (
				SELECT derniereorient.id
					FROM orientsstructs AS derniereorient
					WHERE derniereorient.personne_id = orientsstructs.personne_id
						AND derniereorient.date_valid IS NOT NULL
						AND derniereorient.statut_orient = 'Orienté'
					ORDER BY derniereorient.date_valid DESC
					LIMIT 1
			)
		)
		INNER JOIN contratsinsertion ON (
			contratsinsertion.personne_id = personnes.id
			AND contratsinsertion.id = (
				SELECT dernierci.id
					FROM contratsinsertion AS dernierci
					WHERE dernierci.personne_id = contratsinsertion.personne_id
						AND dernierci.datevalidation_ci IS NOT NULL
						AND dernierci.structurereferente_id IS NOT NULL
						AND dernierci.df_ci >= CURRENT_DATE
					ORDER BY dernierci.dd_ci DESC
					LIMIT 1
			)
		);

-- Combien existe-t'il de personnes distinctes ?
-- 1°) possédant une orientation validée et en cours actuellement
-- 2°) possédant un CER validé et en cours actuellement
-- 3°) dont la structure référente de l'orientation est la même que la structure référente du CER
SELECT COUNT(DISTINCT(personnes.id))
	FROM personnes
		INNER JOIN orientsstructs ON (
			orientsstructs.personne_id = personnes.id
			AND orientsstructs.id = (
				SELECT derniereorient.id
					FROM orientsstructs AS derniereorient
					WHERE derniereorient.personne_id = orientsstructs.personne_id
						AND derniereorient.date_valid IS NOT NULL
						AND derniereorient.statut_orient = 'Orienté'
					ORDER BY derniereorient.date_valid DESC
					LIMIT 1
			)
		)
		INNER JOIN contratsinsertion ON (
			contratsinsertion.personne_id = personnes.id
			AND contratsinsertion.id = (
				SELECT dernierci.id
					FROM contratsinsertion AS dernierci
					WHERE dernierci.personne_id = contratsinsertion.personne_id
						AND dernierci.datevalidation_ci IS NOT NULL
						AND dernierci.structurereferente_id IS NOT NULL
						AND dernierci.df_ci >= CURRENT_DATE
					ORDER BY dernierci.dd_ci DESC
					LIMIT 1
			)
		)
	WHERE orientsstructs.structurereferente_id = contratsinsertion.structurereferente_id;

-- Combien existe-t'il de personnes distinctes ?
-- 1°) possédant une orientation validée et en cours actuellement
-- 2°) possédant un CER validé et en cours actuellement
-- 3°) dont la structure référente de l'orientation est la même que la structure référente du CER
-- 4°) dont la date de validation du CER est au minimum égale à la date de saisie du CER
SELECT COUNT(DISTINCT(personnes.id))
	FROM personnes
		INNER JOIN orientsstructs ON (
			orientsstructs.personne_id = personnes.id
			AND orientsstructs.id = (
				SELECT derniereorient.id
					FROM orientsstructs AS derniereorient
					WHERE derniereorient.personne_id = orientsstructs.personne_id
						AND derniereorient.date_valid IS NOT NULL
						AND derniereorient.statut_orient = 'Orienté'
					ORDER BY derniereorient.date_valid DESC
					LIMIT 1
			)
		)
		INNER JOIN contratsinsertion ON (
			contratsinsertion.personne_id = personnes.id
			AND contratsinsertion.id = (
				SELECT dernierci.id
					FROM contratsinsertion AS dernierci
					WHERE dernierci.personne_id = contratsinsertion.personne_id
						AND dernierci.datevalidation_ci IS NOT NULL
						AND dernierci.structurereferente_id IS NOT NULL
						AND dernierci.df_ci >= CURRENT_DATE
					ORDER BY dernierci.dd_ci DESC
					LIMIT 1
			)
		)
	WHERE orientsstructs.structurereferente_id = contratsinsertion.structurereferente_id
            AND orientsstructs.date_valid <= contratsinsertion.date_saisi_ci;

-- =============================================================================
-- Combien existe-t'il de personnes distinctes ?
-- 1°) possédant des CER qui se chevauchent au niveau des dates
-- =============================================================================

SELECT COUNT(DISTINCT(c1.personne_id))
	FROM contratsinsertion AS c1,
		contratsinsertion AS c2
	WHERE c1.id <> c2.id
		AND c1.personne_id = c2.personne_id
		AND (c1.dd_ci, c1.df_ci) OVERLAPS ( c2.dd_ci, c2.df_ci );

-- =============================================================================
-- Combien existe-t'il de personnes distinctes ?
-- 1°) possédant une orientation validée et en cours actuellement
-- 2°) qui sont soumises à droits et devoirs
--     ET qui se trouvent dans un dossier dont les droits sont ouverts
-- 3°) NE possédant PAS un CER validé et en cours actuellement
-- =============================================================================

SELECT COUNT(DISTINCT(personnes.id))
	FROM personnes
		INNER JOIN orientsstructs ON (
			orientsstructs.personne_id = personnes.id
			AND orientsstructs.id = (
				SELECT derniereorient.id
					FROM orientsstructs AS derniereorient
					WHERE derniereorient.personne_id = orientsstructs.personne_id
						AND derniereorient.date_valid IS NOT NULL
						AND derniereorient.statut_orient = 'Orienté'
					ORDER BY derniereorient.date_valid DESC
					LIMIT 1
			)
		)
		INNER JOIN prestations ON (
			prestations.personne_id = personnes.id
			AND prestations.natprest = 'RSA'
			AND prestations.rolepers IN ( 'DEM', 'CJT' )
		)
		INNER JOIN calculsdroitsrsa ON (
			personnes.id = calculsdroitsrsa.personne_id
		)
		INNER JOIN foyers ON (
			personnes.foyer_id = foyers.id
		)
		INNER JOIN dossiers ON (
			foyers.dossier_id = dossiers.id
		)
		INNER JOIN situationsdossiersrsa ON (
			situationsdossiersrsa.dossier_id = dossiers.id
		)
	WHERE personnes.id NOT IN (
		SELECT dernierci.personne_id
			FROM contratsinsertion AS dernierci
			WHERE dernierci.personne_id = personnes.id
				AND dernierci.datevalidation_ci IS NOT NULL
				AND dernierci.structurereferente_id IS NOT NULL
				AND dernierci.df_ci >= CURRENT_DATE
			ORDER BY dernierci.dd_ci DESC
			LIMIT 1
	)
		AND calculsdroitsrsa.toppersdrodevorsa = '1'
		AND situationsdossiersrsa.etatdosrsa IN ( '2', '3', '4' );