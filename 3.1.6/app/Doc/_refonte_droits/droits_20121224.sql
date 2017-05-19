/*
lib/Cake/Console/cake derniersdossiersallocataires
lib/Cake/Console/cake permissions
lib/Cake/Console/cake preorientation (? FIXME ?)
lib/Cake/Console/cake pgsqlcake.maintenance all

/checks -> max_input_vars 2000
*/

-- Mon code INSEE: Bobigny, en tant que CI/Referent du PDV de Bobigny

-- Foyers qui sont dans ma zone et qui n'y étaient pas avant
-- Ex.: 164071, 162545, 162714, 144244, 143035, 126546, 122368, 136406, 174032, 115661
SELECT
		*
	FROM
		adressesfoyers AS af1
			INNER JOIN adresses AS a1 ON ( af1.rgadr = '01' AND af1.adresse_id = a1.id ),
		adressesfoyers AS af2
			INNER JOIN adresses AS a2 ON ( af2.rgadr = '02' AND af2.adresse_id = a2.id ),
		adressesfoyers AS af3
			INNER JOIN adresses AS a3 ON ( af3.rgadr = '03' AND af3.adresse_id = a3.id )
	WHERE
		af1.foyer_id = af2.foyer_id
		AND af2.foyer_id = af3.foyer_id
		AND a1.numcomptt = '93008'
		AND a2.numcomptt <> '93008'
		AND a3.numcomptt <> '93008'
	LIMIT 10;

-- Foyers qui ne sont pas dans ma zone et qui y étaient avant
-- Ex.: 133540, 231641, 117230, 222502, 173458, 72840,158833, 124379, 204938, 231774
SELECT
		*
	FROM
		adressesfoyers AS af1
			INNER JOIN adresses AS a1 ON ( af1.rgadr = '01' AND af1.adresse_id = a1.id ),
		adressesfoyers AS af2
			INNER JOIN adresses AS a2 ON ( af2.rgadr = '02' AND af2.adresse_id = a2.id ),
		adressesfoyers AS af3
			INNER JOIN adresses AS a3 ON ( af3.rgadr = '03' AND af3.adresse_id = a3.id )
	WHERE
		af1.foyer_id = af2.foyer_id
		AND af2.foyer_id = af3.foyer_id
		AND a1.numcomptt <> '93008'
		AND (
			a2.numcomptt = '93008'
			OR a3.numcomptt = '93008'
		)
		-- et qui possèdent des dsps_revs
		/*AND af1.foyer_id IN (
			SELECT personnes.foyer_id
				FROM personnes
					INNER JOIN dsps ON ( personnes.id = dsps.personne_id )
					INNER JOIN dsps_revs ON (
						dsps.id = dsps_revs.dsp_id
						AND personnes.id = dsps_revs.personne_id
					)
				WHERE personnes.foyer_id = af1.foyer_id
		)*/
	LIMIT 10;

-- Foyers qui ne sont pas dans ma zone et qui n'y étaient pas avant
-- Ex.: 77672, 192918, 214762, 117193, 117195, 193801, 117196, 276422, 133528, 141535
SELECT
		*
	FROM
		adressesfoyers AS af1
			INNER JOIN adresses AS a1 ON ( af1.rgadr = '01' AND af1.adresse_id = a1.id ),
		adressesfoyers AS af2
			INNER JOIN adresses AS a2 ON ( af2.rgadr = '02' AND af2.adresse_id = a2.id ),
		adressesfoyers AS af3
			INNER JOIN adresses AS a3 ON ( af3.rgadr = '03' AND af3.adresse_id = a3.id )
	WHERE
		af1.foyer_id = af2.foyer_id
		AND af2.foyer_id = af3.foyer_id
		AND a1.numcomptt <> '93008'
		AND a2.numcomptt <> '93008'
		AND a3.numcomptt <> '93008'
	LIMIT 10;