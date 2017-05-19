--cf. patch-2.0rc15.sql

-- Statistiques sur les personnes non demandeurs ou non conjoints RSA possédant une entrée dans orientsstructs
/*SELECT
	COUNT(orientsstructs.id), orientsstructs.statut_orient, prestations.rolepers
	FROM orientsstructs
	INNER JOIN personnes ON personnes.id = orientsstructs.personne_id
	INNER JOIN prestations ON prestations.personne_id = personnes.id
	WHERE prestations.natprest = 'RSA' AND prestations.rolepers NOT IN ('DEM', 'CJT')
	GROUP BY orientsstructs.statut_orient, prestations.rolepers;*/

-- *****************************************************************************

-- A-t'on des orientsstructs qui ont été relancées ?
/*SELECT
		COUNT(orientsstructs.id)
	FROM orientsstructs
	WHERE ( orientsstructs.statutrelance <> 'E' OR orientsstructs.statutrelance IS NULL )
		OR orientsstructs.daterelance IS NOT NULL
		OR orientsstructs.date_impression_relance IS NOT NULL;*/


-- actuellement relancesdetectionscontrats93
/*CREATE TABLE relancesxxx (
	id					SERIAL NOT NULL,
	personne_id			INTEGER DEFAULT NULL REFERENCES personnes(id),
	propopdo_id			INTEGER DEFAULT NULL REFERENCES propospdos(id),
	tempradiation_id	INTEGER DEFAULT NULL REFERENCES tempradiations(id), -- FIXME à l'avenir ?
	--saisine_id 			-- saisine -> FIXME
	orientstruct_id		INTEGER DEFAULT NULL REFERENCES orientsstructs(id),
	contratinsertion_id	INTEGER DEFAULT NULL REFERENCES contratsinsertion(id),
	cui_id				INTEGER DEFAULT NULL REFERENCES cuis(id)
	-- ppae -- bool
);*/

-- Combien de dernières orientsstructs qui n'ont pas signé de contrat lié à cette orientation
-- TODO: when au lieu du count (pour les performances) ?
/*SELECT
		orientsstructs.personne_id,
		( DATE( NOW() ) - orientsstructs.date_valid ) AS nbjours
	FROM orientsstructs
	WHERE
		-- la dernière orientation
		orientsstructs.id IN (
			SELECT dernierorientsstructs.id
				FROM orientsstructs AS dernierorientsstructs
				WHERE dernierorientsstructs.personne_id = orientsstructs.personne_id
					AND dernierorientsstructs.statut_orient = 'Orienté'
					AND dernierorientsstructs.date_valid IS NOT NULL
				ORDER BY dernierorientsstructs.date_valid DESC
				LIMIT 1
		)
		-- Ne possédant pas de contratsinsertion "lié à cette orientation"
		AND (
			SELECT COUNT(id) FROM (
				SELECT
						contratsinsertion.id AS id,
						contratsinsertion.dd_ci,
						contratsinsertion.personne_id
					FROM contratsinsertion
					WHERE
						contratsinsertion.personne_id = orientsstructs.personne_id
						AND (
							contratsinsertion.dd_ci >= orientsstructs.date_valid
							OR contratsinsertion.datevalidation_ci >= orientsstructs.date_valid
						)
					ORDER BY contratsinsertion.dd_ci DESC
					LIMIT 1
			) AS dernierscontratsinsertion
		) = 0
		-- Ne possédant pas de cuis "lié à cette orientation"
		AND (
			SELECT COUNT(id) FROM (
				SELECT
						cuis.id AS id,
						cuis.datecontrat,
						cuis.personne_id
					FROM cuis
					WHERE
						cuis.personne_id = orientsstructs.personne_id
						AND (
							cuis.datecontrat >= orientsstructs.date_valid
							OR cuis.datevalidationcui >= orientsstructs.date_valid
						)
					ORDER BY cuis.datecontrat DESC
					LIMIT 1
			) AS dernierscuis
		) = 0
	LIMIT 10;*/

-- FIXME: problèmes de minuscules et d'accents dans la table personnes --> mettre une contrainte ?
-- FIXME: problèmes de nom / prenom vides (pas NULL mais vides) dans la table personnes -> contrainte ?

/*
	-- Entrées non en majuscules sans accents dans personnes
	SELECT
		nom,
		prenom,
		nomnai,
		prenom2,
		prenom3
	FROM personnes
	WHERE
		nom !~ '^([A-Z]|\-| |'')+$'
		OR prenom !~ '^([A-Z]|\-| |'')+$'
		OR ( nomnai IS NOT NULL AND CHAR_LENGTH( TRIM( BOTH ' ' FROM nomnai ) ) > 0 AND nomnai !~ '^([A-Z]|\-| |'')+$' )
		OR ( prenom2 IS NOT NULL AND CHAR_LENGTH( TRIM( BOTH ' ' FROM prenom2 ) ) > 0 AND prenom2 !~ '^([A-Z]|\-| |'')+$' )
		OR ( prenom3 IS NOT NULL AND CHAR_LENGTH( TRIM( BOTH ' ' FROM prenom3 ) ) > 0 AND prenom3 !~ '^([A-Z]|\-| |'')+$' );
*/


-- "Doublons" --> 672
/*SELECT
		i.*
	FROM (
		SELECT
				COUNT(informationspe.id) AS count,
		-- 		informationspe.personne_id,
				informationspe.nir,
				informationspe.nom,
				informationspe.prenom,
				informationspe.dtnai
			FROM informationspe
			GROUP BY
		-- 		informationspe.personne_id,
				informationspe.nir,
				informationspe.nom,
				informationspe.prenom,
				informationspe.dtnai
	) AS i
	WHERE i.count > 1
	ORDER BY i.count DESC

-- FIXME: 3 x avec le rôle DEM RSA
-- FIXME: 2 dossiers différents: 1 en droit 6 (clos/FIXME), 2 personnes DEM pour l'autre dossier
SELECT
		informationspe.*,
		prestations.*,
		situationsdossiersrsa.*
	FROM informationspe
		INNER JOIN prestations ON (
			prestations.personne_id = informationspe.personne_id
			AND prestations.natprest = 'RSA'
		)
		INNER JOIN personnes ON (
			personnes.id = informationspe.personne_id
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
	WHERE
		informationspe.nom = ( SELECT nom FROM personnes WHERE personnes.id = 49646 )
		AND informationspe.prenom = ( SELECT prenom FROM personnes WHERE personnes.id = 49646 )
		AND informationspe.dtnai = ( SELECT dtnai FROM personnes WHERE personnes.id = 49646 )

-- EXEMPLE: dernière information du parcours PE d'un allocataire
SELECT
		historiqueetatspe.identifiantpe,
		historiqueetatspe.date,
		historiqueetatspe.etat,
		historiqueetatspe.code,
		historiqueetatspe.motif
	FROM historiqueetatspe
	WHERE historiqueetatspe.informationpe_id IN (
		SELECT
				informationspe.id
			FROM informationspe
			WHERE
				informationspe.nom = ( SELECT nom FROM personnes WHERE personnes.id = 49646 )
				AND informationspe.prenom = ( SELECT prenom FROM personnes WHERE personnes.id = 49646 )
				AND informationspe.dtnai = ( SELECT dtnai FROM personnes WHERE personnes.id = 49646 )
	)
	GROUP BY
		historiqueetatspe.identifiantpe,
		historiqueetatspe.date,
		historiqueetatspe.etat,
		historiqueetatspe.code,
		historiqueetatspe.motif
	ORDER BY historiqueetatspe.date DESC
	LIMIT 1

-- EXEMPLE: dernière information venant de Pôle Emploi pour les allocataires
SELECT
-- 		COUNT(*),
		informationspe.nir,
		informationspe.nom,
		informationspe.prenom,
		informationspe.dtnai,
		historiqueetatspe.date,
		historiqueetatspe.etat
	FROM informationspe
		INNER JOIN historiqueetatspe ON (
			historiqueetatspe.informationpe_id = informationspe.id
		)
		INNER JOIN personnes ON (
			(
				informationspe.nir IS NOT NULL
				AND personnes.nir IS NOT NULL
				AND informationspe.nir ~* '^[0-9]{15}$'
				AND personnes.nir ~* '^[0-9]{13}$'
				AND informationspe.nir = personnes.nir || calcul_cle_nir( personnes.nir )
			)
			OR (
				informationspe.nom = personnes.nom
				AND informationspe.prenom = personnes.prenom
				AND informationspe.dtnai = personnes.dtnai
			)
		)
		INNER JOIN prestations ON (
			personnes.id = prestations.personne_id
			AND prestations.natprest = 'RSA'
			AND prestations.rolepers IN ( 'DEM', 'CJT' )
		)
	WHERE
		historiqueetatspe.id IN (
			SELECT h.id
				FROM historiqueetatspe AS h
				WHERE h.informationpe_id = informationspe.id
				ORDER BY h.date DESC
				LIMIT 1
		)
	GROUP BY
		informationspe.nir,
		informationspe.nom,
		informationspe.prenom,
		informationspe.dtnai,
		historiqueetatspe.date,
		historiqueetatspe.etat
	ORDER BY
		historiqueetatspe.date DESC
	LIMIT 10
*/

	/*
	Attention:
		les personnes trouvées par le script importcsvinfope et pour lesquelles on
		rajoute une entrée dans infospoleemploi ne sont pas toutes les personnes
		"concernées" (une même personne pouvant appartenir à plusieurs foyers, il
		faudrait refléter cette réalité, et voir si la personne est DEM ou CJT RSA,
		car pour l'instant, c'est simplement la première personne qui vient à
		PostgreSQL).
	*/

	/*
	Questions / remarques:
		1°) Pourquoi passait-on par les tables temporaires (parce que la personne
			n'était peut-être pas encore dans la liste des allocataires) ?
		2°) Quand les entrées de la table tempcessations (etc) sont-elles supprimées
			(lorsqu'on trouve la personne dans la liste des allocataires dans le script traitementcsvinfope) ?
		3°) A quels endroits se sert-on actuellement d'Infopoleemploi (infospoleemploi) dans WebRSA ?
			grep -lR "\(Infopoleemploi\|infospoleemploi\)" app | grep -v "\/\(fixtures\|sql\|tests\|\.svn\)\/"
				* app/models/personne.php
				* app/models/critere.php
				* app/models/infopoleemploi.php
				* app/config/inflections.php
				* app/views/criteres/exportcsv.ctp
				* app/views/criteres/index.ctp
				* app/views/dossiers/view.ctp
				* app/controllers/dossiers_controller.php
				* app/vendors/shells/anomalies.php
				* app/vendors/shells/traitementcsvinfope.php
				* app/vendors/shells/anomaliesr.php
				* app/vendors/shells/integrationfluxpe.php
		4°) Même question que pour le point 2, mais pour tempcessations, tempinscriptions, tempradiations
			grep -lR "\(tempcessations\|Tempcessation\|tempinscriptions\|Tempinscription\|tempradiations\|Tempradiation\)" app | grep -v "\/\(fixtures\|sql\|tests\|\.svn\)\/"
				* app/models/tempinscription.php
				* app/models/tempcessation.php
				* app/models/tempradiation.php
				* app/vendors/shells/traitementcsvinfope.php
				* app/vendors/shells/integrationfluxpe.php
				* app/vendors/shells/importcsvinfope.php
		5°) Inscription, radiation, réinscription, ... -> même identifiant PE ? -> comment structurer les tables ?
		6°) Garde-t'on l'identifiant PE à vie ?
		7°) Format de l'identifiant: 6666666S 046 (8 chiffres ou 7 chiffres + 1 lettre, puis identifiant bureau du PE ?)
		8°) Peut-on avoir la liste des codes/libellés (pour en faire une table de paramétrage éventuellement, et mettre à jour les codes pour les entrées que l'on a déjà) ?
	*/

	/*
		-- Quelles sont les personnes présentes dans la table informationspe et qui ne sont ni DEM ni CJT RSA ?
		-- (CG 66, 20101217_dump_webrsaCG66_rc9.sql.gz -> 55)
		SELECT
				*
			FROM informationspe
				INNER JOIN prestations ON (
					prestations.personne_id = informationspe.personne_id
					AND prestations.natprest = 'RSA'
				)
			WHERE prestations.rolepers NOT IN ( 'DEM', 'CJT' );
	*/

	/*
	-- Vrais doublons dans la table tempcessations (CG 66, 20101217_dump_webrsaCG66_rc9.sql.gz -> 0)
	SELECT t.* FROM (
		SELECT
				nir,
				identifiantpe,
				nom,
				prenom,
				dtnai,
				datecessation,
				motifcessation,
				COUNT(id) AS rows
			FROM tempcessations
			GROUP BY identifiantpe, nir, nom, prenom, dtnai, datecessation, motifcessation
			ORDER BY COUNT(identifiantpe) DESC
		) AS t
		WHERE t.rows > 1
	*/

	/*
	-- Vrais doublons dans la table infospoleemploi (CG 66, 20101217_dump_webrsaCG66_rc9.sql.gz -> 3713 personnes)
	SELECT i.* FROM (
		SELECT
				personne_id,
				identifiantpe,
				dateinscription,
				categoriepe,
				datecessation,
				motifcessation,
				dateradiation,
				motifradiation,
				COUNT(id) AS rows
			FROM infospoleemploi
			GROUP BY personne_id, identifiantpe, dateinscription, categoriepe, datecessation, motifcessation, dateradiation, motifradiation
			ORDER BY COUNT(personne_id) DESC
		) AS i
		WHERE i.rows > 1
	*/

	/*
	-- Infos Pôle Emploi pour des personnes non demandeurs ou non conjoints RSA (doublons en base):
	--	* dans le même dossier (cf. personne_id 39460  - CG 66, 20101217_dump_webrsaCG66_rc9.sql.gz)
	--	* dans des dossiers différents (cf. personne_id 35670 - CG 66, 20101217_dump_webrsaCG66_rc9.sql.gz)
	SELECT
			infospoleemploi.personne_id,
			infospoleemploi.identifiantpe,
			infospoleemploi.dateinscription,
			infospoleemploi.categoriepe,
			infospoleemploi.datecessation,
			infospoleemploi.motifcessation,
			infospoleemploi.dateradiation,
			infospoleemploi.motifradiation,
			COUNT(infospoleemploi.id) AS rows
		FROM infospoleemploi
		WHERE infospoleemploi.personne_id NOT IN (
			SELECT
					prestations.personne_id
				FROM prestations
				WHERE prestations.personne_id = infospoleemploi.personne_id
					AND prestations.natprest = 'RSA'
					AND prestations.rolepers IN ( 'DEM', 'CJT' )
		)
		GROUP BY infospoleemploi.personne_id, infospoleemploi.identifiantpe, infospoleemploi.dateinscription, infospoleemploi.categoriepe, infospoleemploi.datecessation, infospoleemploi.motifcessation, infospoleemploi.dateradiation, infospoleemploi.motifradiation
		ORDER BY COUNT(infospoleemploi.personne_id) DESC
	*/

	/*
	SELECT personnes.foyer_id, personnes.id, prestations.rolepers, ipe.*
		FROM(
			SELECT
					nir,
					nom,
					prenom,
					dtnai
				FROM tempcessations
			UNION
			SELECT
					nir,
					nom,
					prenom,
					dtnai
				FROM tempradiations
			UNION
			SELECT
					nir,
					nom,
					prenom,
					dtnai
				FROM tempinscriptions
			) AS ipe
			INNER JOIN personnes ON (
	--			( ipe.nir || '%' ) = personnes.nir
	--			OR (
					ipe.nom = personnes.nom
					AND ipe.prenom = personnes.prenom
					AND ipe.dtnai = personnes.dtnai
	--			)
			)
			INNER JOIN prestations ON (
				personnes.id = prestations.personne_id
				AND prestations.natprest = 'RSA'
			)
		GROUP BY ipe.nir, ipe.nom, ipe.dtnai, ipe.prenom, personnes.id, personnes.foyer_id, prestations.rolepers
		ORDER BY personnes.foyer_id, personnes.id
	*/

	/*
	-- Dans les informations PE, a-t'on des NIR / identifiants PE différents pour une personne donnée ?
	-- (CG 66, 20101217_dump_webrsaCG66_rc9.sql.gz -> 70 personnes)
	SELECT *
		FROM (
			SELECT ipe.nom, ipe.prenom, ipe.dtnai, COUNT(DISTINCT(ipe.nir)) AS nbnirs, COUNT(DISTINCT(ipe.identifiantpe)) AS nbidentifiants
				FROM(
					SELECT
							nir,
							identifiantpe,
							nom,
							prenom,
							dtnai
						FROM tempcessations
					UNION
					SELECT
							nir,
							identifiantpe,
							nom,
							prenom,
							dtnai
						FROM tempradiations
					UNION
					SELECT
							nir,
							identifiantpe,
							nom,
							prenom,
							dtnai
						FROM tempinscriptions
					) AS ipe
				GROUP BY ipe.nom, ipe.prenom, ipe.dtnai
		) AS liste
		WHERE liste.nbnirs > 1 OR liste.nbidentifiants > 1
		ORDER BY nbnirs DESC, nbidentifiants DESC
	*/

	/*
	-- Exemples concernant les informations PE avec des NIR / identifiants PE différents pour une personne donnée.
	-- (CG 66, 20101217_dump_webrsaCG66_rc9.sql.gz)
	SELECT *
		FROM(
			SELECT
					nir,
					identifiantpe,
					nom,
					prenom,
					dtnai
				FROM tempcessations
			UNION
			SELECT
					nir,
					identifiantpe,
					nom,
					prenom,
					dtnai
				FROM tempradiations
			UNION
			SELECT
					nir,
					identifiantpe,
					nom,
					prenom,
					dtnai
				FROM tempinscriptions
			) AS ipe
		WHERE ipe.nom = ( SELECT nom FROM personnes WHERE personnes.id = 34057 )
			OR ipe.nom = ( SELECT nom FROM personnes WHERE personnes.id = 87505 )
		ORDER BY ipe.nom, ipe.prenom
	*/

	/*
	-- A quoi ressemble le parcours PE pour les personnes ayant de multiples entrées
	-- (inscription, cessation, radiation)
	SELECT
			ipe.nir,
			ipe.identifiantpe,
			ipe.nom,
			ipe.prenom,
			ipe.dtnai,
			tmpipe2.nb,
			ipe.date,
			ipe.action
		FROM(
			SELECT
					nir,
					identifiantpe,
					nom,
					prenom,
					dtnai,
					'cessation' AS action,
					datecessation AS date
				FROM tempcessations
			UNION
			SELECT
					nir,
					identifiantpe,
					nom,
					prenom,
					dtnai,
					'radiation' AS action,
					dateradiation AS date
				FROM tempradiations
			UNION
			SELECT
					nir,
					identifiantpe,
					nom,
					prenom,
					dtnai,
					'inscription' AS action,
					dateinscription AS date
				FROM tempinscriptions
			) AS ipe
		INNER JOIN (
			SELECT tmpipe.nom, tmpipe.prenom, tmpipe.dtnai, COUNT(tmpipe.id) AS nb
				FROM(
					SELECT
							id,
							nom,
							prenom,
							dtnai
						FROM tempcessations
					UNION
					SELECT
							id,
							nom,
							prenom,
							dtnai
						FROM tempradiations
					UNION
					SELECT
							id,
							nom,
							prenom,
							dtnai
						FROM tempinscriptions
					) AS tmpipe
				GROUP BY tmpipe.nom, tmpipe.prenom, tmpipe.dtnai
				ORDER BY COUNT(tmpipe.id) DESC
		) AS tmpipe2
		ON (
			tmpipe2.nom = ipe.nom
			AND tmpipe2.prenom = ipe.prenom
			AND tmpipe2.dtnai = ipe.dtnai
			AND tmpipe2.nb > 1
		)
		ORDER BY tmpipe2.nb DESC, ipe.nom, ipe.prenom, ipe.date ASC
	*/
