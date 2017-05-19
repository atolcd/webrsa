-- Nombre d'allocataires figurant dans un dossier dont les droits sont ouverts et
-- possédant un contrat d'insertion (FIXME: contrats en cours seulement ?)

SELECT COUNT(DISTINCT(contratsinsertion.personne_id))
	FROM contratsinsertion
		INNER JOIN personnes ON (
			contratsinsertion.personne_id = personnes.id
		)
		INNER JOIN prestations ON (
			prestations.personne_id = personnes.id
			AND prestations.natprest = 'RSA'
			AND prestations.rolepers IN ( 'DEM', 'CJT' )
		)
		INNER JOIN calculsdroitsrsa ON (
			personnes.id = calculsdroitsrsa.personne_id
			AND calculsdroitsrsa.toppersdrodevorsa = '1'
		)
		INNER JOIN foyers ON (
			personnes.foyer_id = foyers.id
		)
		INNER JOIN dossiers ON (
			foyers.dossier_id = dossiers.id
		)
		INNER JOIN situationsdossiersrsa ON (
			situationsdossiersrsa.dossier_id = dossiers.id
			AND situationsdossiersrsa.etatdosrsa IN ( '2', '3', '4' )
		);

-- -----------------------------------------------------------------------------

-- Nombre d'allocataires figurant dans un dossier dont les droits sont ouverts et
-- possédant une orientation (FIXME: orientsstructs en cours seulement ?)

SELECT COUNT(DISTINCT(orientsstructs.personne_id))
	FROM orientsstructs
		INNER JOIN personnes ON (
			orientsstructs.personne_id = personnes.id
		)
		INNER JOIN prestations ON (
			prestations.personne_id = personnes.id
			AND prestations.natprest = 'RSA'
			AND prestations.rolepers IN ( 'DEM', 'CJT' )
		)
		INNER JOIN calculsdroitsrsa ON (
			personnes.id = calculsdroitsrsa.personne_id
			AND calculsdroitsrsa.toppersdrodevorsa = '1'
		)
		INNER JOIN foyers ON (
			personnes.foyer_id = foyers.id
		)
		INNER JOIN dossiers ON (
			foyers.dossier_id = dossiers.id
		)
		INNER JOIN situationsdossiersrsa ON (
			situationsdossiersrsa.dossier_id = dossiers.id
			AND situationsdossiersrsa.etatdosrsa IN ( '2', '3', '4' )
		)
	WHERE
		orientsstructs.date_valid IS NOT NULL
		AND orientsstructs.statut_orient = 'Orienté'
		AND orientsstructs.structurereferente_id IS NOT NULL;

-- -----------------------------------------------------------------------------

-- Quelles sont les orientations manifestement liées à un contrat d'insertion

SELECT COUNT(*)
	FROM orientsstructs
		INNER JOIN contratsinsertion ON (
			contratsinsertion.personne_id = orientsstructs.personne_id
			AND contratsinsertion.structurereferente_id = orientsstructs.structurereferente_id
			AND orientsstructs.date_valid <= date_saisi_ci
		)
		INNER JOIN personnes ON (
			orientsstructs.personne_id = personnes.id
		)
		INNER JOIN prestations ON (
			prestations.personne_id = personnes.id
			AND prestations.natprest = 'RSA'
			AND prestations.rolepers IN ( 'DEM', 'CJT' )
		)
		INNER JOIN calculsdroitsrsa ON (
			personnes.id = calculsdroitsrsa.personne_id
			AND calculsdroitsrsa.toppersdrodevorsa = '1'
		)
		INNER JOIN foyers ON (
			personnes.foyer_id = foyers.id
		)
		INNER JOIN dossiers ON (
			foyers.dossier_id = dossiers.id
		)
		INNER JOIN situationsdossiersrsa ON (
			situationsdossiersrsa.dossier_id = dossiers.id
			AND situationsdossiersrsa.etatdosrsa IN ( '2', '3', '4' )
		)
	WHERE
		orientsstructs.date_valid IS NOT NULL
		AND orientsstructs.statut_orient = 'Orienté'
		AND orientsstructs.structurereferente_id IS NOT NULL
		AND contratsinsertion.datevalidation_ci IS NOT NULL;

-- -----------------------------------------------------------------------------

-- Dernières orientations validées par personne
SELECT COUNT(DISTINCT(orientsstructs.id))
	FROM orientsstructs
	WHERE orientsstructs.id IN (
		SELECT o.id
			FROM orientsstructs AS o
			WHERE o.personne_id = orientsstructs.personne_id
				AND o.date_valid IS NOT NULL
				AND o.statut_orient = 'Orienté'
				AND o.structurereferente_id IS NOT NULL
			GROUP BY o.personne_id, o.id, o.date_valid
			ORDER BY o.date_valid DESC
	);

/*SELECT COUNT(DISTINCT(orientsstructs.id))
	FROM orientsstructs
	WHERE
		orientsstructs.date_valid IS NOT NULL
		AND orientsstructs.statut_orient = 'Orienté'
		AND orientsstructs.structurereferente_id IS NOT NULL
	GROUP BY orientsstructs.personne_id, orientsstructs.id, orientsstructs.date_valid
	ORDER BY orientsstructs.date_valid DESC;*/

-- -----------------------------------------------------------------------------

-- Derniers contratsinsertion validés et en cours par personne
SELECT COUNT(DISTINCT(contratsinsertion.id))
	FROM contratsinsertion
	WHERE contratsinsertion.id IN (
		SELECT c.id
			FROM contratsinsertion AS c
			WHERE c.personne_id = contratsinsertion.personne_id
				AND c.datevalidation_ci IS NOT NULL
				AND c.structurereferente_id IS NOT NULL
				AND c.df_ci >= CURRENT_DATE
			GROUP BY c.personne_id, c.id, c.datevalidation_ci
			ORDER BY c.datevalidation_ci DESC
	);

-- -----------------------------------------------------------------------------

-- Nombre de contrats d'insertion par personne, triés du plus grand au plus petit
-- nombre de contrats par personne.
SELECT contratsinsertion.personne_id, COUNT(contratsinsertion.*)
	FROM contratsinsertion
	GROUP BY contratsinsertion.personne_id
	ORDER BY COUNT(contratsinsertion.*) DESC;

-- -----------------------------------------------------------------------------

-- Nombre de personnes possédant des contrats d'insertion se chevauchant au niveau des dates
SELECT COUNT(DISTINCT(c1.personne_id))
	FROM contratsinsertion AS c1,
		contratsinsertion AS c2
	WHERE c1.id <> c2.id
		AND c1.personne_id = c2.personne_id
		AND (c1.dd_ci, c1.df_ci) OVERLAPS ( c2.dd_ci, c2.df_ci );