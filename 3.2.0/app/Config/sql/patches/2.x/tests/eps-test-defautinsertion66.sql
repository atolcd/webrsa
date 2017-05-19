
-- TODO: ajouter les champs concernant l'audition
CREATE TABLE defautsinsertionseps66 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	dossierep_id			INTEGER NOT NULL REFERENCES dossierseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	bilanparcours66_id		INTEGER DEFAULT NULL REFERENCES bilansparcours66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	contratinsertion_id		INTEGER DEFAULT NULL REFERENCES contratsinsertion(id) ON DELETE CASCADE ON UPDATE CASCADE,
	orientstruct_id			INTEGER DEFAULT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	origine					TYPE_ORIGINEDEFAULTINSERTIONEP66 NOT NULL,
	type					TYPE_TYPE_DEMANDE DEFAULT NULL,-- DOD (défaut de conclusion), DRD (non respect)
	historiqueetatpe_id		INTEGER DEFAULT NULL REFERENCES historiqueetatspe(id) ON DELETE CASCADE ON UPDATE CASCADE, -- Pour les radiations PE
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);

--
SELECT
		personnes.id,
		contratsinsertion.id,
		orientsstructs.id,
		historiqueetatspe.id
	FROM informationspe
		INNER JOIN historiqueetatspe ON (
			informationspe.id = historiqueetatspe.informationpe_id
		)
		INNER JOIN personnes ON (
			(
				personnes.nir IS NOT NULL
				AND informationspe.nir IS NOT NULL
				AND personnes.nir = informationspe.nir
				-- FIXME: longueur
			)
			OR
			(
				personnes.nom = informationspe.nom
				AND personnes.prenom = informationspe.prenom
				AND personnes.dtnai = informationspe.dtnai
			)
		)
		INNER JOIN prestations ON (
			personnes.id = prestations.personne_id
			AND prestations.natprest = 'RSA'
			AND prestations.rolepers IN ( 'DEM', 'CJT' )
		)
		INNER JOIN foyers ON (
			personnes.foyer_id = foyers.id
		)
		INNER JOIN dossiers ON (
			foyers.dossier_id = dossiers.id
		)
		INNER JOIN situationsdossiersrsa ON (
			situationsdossiersrsa.dossier_id = dossiers.id
			AND situationsdossiersrsa.etatdosrsa IN ( 'Z', '2', '3', '4' )
		)
		INNER JOIN orientsstructs ON (
			orientsstructs.personne_id = personnes.id
			AND orientsstructs.id IN (
				SELECT o.id
					FROM orientsstructs AS o
					WHERE
						o.personne_id = personnes.id
						AND o.date_valid IS NOT NULL
						AND o.statut_orient = 'Orienté'
					ORDER BY o.date_valid DESC
					LIMIT 1
			)
		)
		INNER JOIN contratsinsertion ON (
			contratsinsertion.personne_id = personnes.id
			AND contratsinsertion.id IN (
				SELECT c.id
					FROM contratsinsertion AS c
					WHERE
						c.personne_id = personnes.id
						AND c.datevalidation_ci IS NOT NULL
						AND c.datevalidation_ci >= orientsstructs.date_valid
						AND c.decision_ci = 'V'
					ORDER BY c.datevalidation_ci DESC
					LIMIT 1
			)
		)
	WHERE
		/*orientsstructs.typeorient_id IN ( SELECT id FROM typesorients WHERE parentid IS NOT NULL AND lib_type_orient LIKE 'Emploi%' )
		AND */historiqueetatspe.id IN (
			SELECT
					h.id
				FROM historiqueetatspe AS h
				WHERE informationspe.id = h.informationpe_id
				ORDER BY h.date DESC
				LIMIT 1
		)
		AND historiqueetatspe.etat = 'radiation'
		AND historiqueetatspe.date >= orientsstructs.date_valid
	LIMIT 10;

--

INSERT INTO dossierseps ( personne_id, seanceep_id, themeep, created, modified ) VALUES
	( 59383, NULL, 'defautsinsertionseps66', '2010-12-30 16:05:00', NULL );

INSERT INTO defautsinsertionseps66 ( dossierep_id, bilanparcours66_id, contratinsertion_id, orientstruct_id, origine, type, historiqueetatpe_id, created, modified ) VALUES
	( 1, NULL, 3399, 28584, 'radiationpe', NULL, 372, '2010-12-30 16:05:00', NULL );

