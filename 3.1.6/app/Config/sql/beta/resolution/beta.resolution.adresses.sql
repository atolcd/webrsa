/*
* Résolution des problèmes pour les erreurs sur les tables adresses_foyers et adresses
*/

BEGIN;

-- Réparation des rgadr mal formattés
UPDATE adresses_foyers SET rgadr = '01' WHERE rgadr = '1';
UPDATE adresses_foyers SET rgadr = '02' WHERE rgadr = '2';
UPDATE adresses_foyers SET rgadr = '03' WHERE rgadr = '3';

-- Suppression des vrais doublons des adresses_foyers en doublons
DELETE FROM adresses_foyers
	WHERE adresses_foyers.id IN (
-- 		SELECT af1.*, a1.*
		SELECT af1.id
			FROM adresses_foyers AS af1
					INNER JOIN adresses AS a1 ON af1.adresse_id = a1.id,
				adresses_foyers AS af2
					INNER JOIN adresses AS a2 ON af2.adresse_id = a2.id
			WHERE
				-- adresses_foyers
				af1.id < af2.id
				AND af1.foyer_id = af2.foyer_id
				AND af1.rgadr = af2.rgadr
				AND af1.dtemm = af2.dtemm
				AND af1.typeadr = af2.typeadr
				AND af1.adresse_id <> af2.adresse_id
				-- adresses
				AND a1.numvoie = a2.numvoie
				AND a1.typevoie = a2.typevoie
				AND a1.nomvoie = a2.nomvoie
				AND a1.complideadr = a2.complideadr
				AND TRIM(a1.compladr) = TRIM(a2.compladr)
				AND a1.lieudist = a2.lieudist
				AND a1.numcomrat = a2.numcomrat
				AND a1.numcomptt = a2.numcomptt
				AND a1.codepos = a2.codepos
				AND a1.locaadr = a2.locaadr
				AND a1.pays = a2.pays
				AND ( ( a1.canton = a2.canton ) OR ( a1.canton IS NULL AND a2.canton IS NULL ) )
				AND ( ( a1.typeres = a2.typeres ) OR ( a1.typeres IS NULL AND a2.typeres IS NULL ) )
				AND ( ( a1.topresetr = a2.topresetr ) OR ( a1.topresetr IS NULL AND a2.topresetr IS NULL ) )
	);

/**
* adresses_foyers en doublons -> suppression des anciennes adressees de même rang (faux doublons)
*/

DELETE FROM adresses_foyers
	WHERE adresses_foyers.id IN (
-- 		SELECT af1.*, a1.*
		SELECT af1.id
			FROM adresses_foyers AS af1
					INNER JOIN adresses AS a1 ON af1.adresse_id = a1.id,
				adresses_foyers AS af2
					INNER JOIN adresses AS a2 ON af2.adresse_id = a2.id
			WHERE
				-- adresses_foyers
				af1.id <> af2.id
				AND af1.foyer_id = af2.foyer_id
				AND af1.rgadr = af2.rgadr
				AND af1.dtemm < af2.dtemm
-- 				AND af1.typeadr = af2.typeadr
				AND af1.adresse_id <> af2.adresse_id
	);


/**
* adresses sans adresses_foyers -> FIXME: ON DELETE CASCADE
*/

DELETE FROM adresses
	WHERE adresses.id IN (
			SELECT DISTINCT( adresses.id )
				FROM adresses
		EXCEPT
			SELECT DISTINCT( adresses_foyers.adresse_id )
				FROM adresses_foyers
	);

-- Pour s'assurer qu'une adresse n'est liée qu'à une seule adresses_foyers
-- FIXME: raisonnement correct (on supprime celle dont le rgadr est le plus élevé) ?
DELETE FROM adresses_foyers
	WHERE adresses_foyers.id IN (
		SELECT a1.id
			FROM adresses_foyers AS a1,
				adresses_foyers AS a2
			WHERE
				a1.id <> a2.id
				AND a1.adresse_id = a2.adresse_id
				AND a1.foyer_id = a2.foyer_id
				AND a1.rgadr > a2.rgadr
			ORDER BY a1.adresse_id ASC, a1.foyer_id ASC
	);

COMMIT;