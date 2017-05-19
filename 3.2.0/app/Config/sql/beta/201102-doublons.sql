CREATE OR REPLACE FUNCTION "public"."calcul_cle_nir" (text) RETURNS text AS
$body$
	DECLARE
		p_nir text;
		cle text;
		correction BIGINT;

	BEGIN
		correction:=0;
		p_nir:=$1;

		IF NOT p_nir ~ E'[0-9]{6}(A|B|[0-9])[0-9]{6}' THEN
			RETURN NULL;
		END IF;

		IF p_nir ~ '^.{6}(A|B)' THEN
			IF p_nir ~ '^.{6}A' THEN
				correction:=1000000;
			ELSE
				correction:=2000000;
			END IF;
			p_nir:=regexp_replace( p_nir, '(A|B)', '0' );
		END IF;

		cle:=LPAD( CAST( 97 - ( ( CAST( p_nir AS BIGINT ) - correction ) % 97 ) AS VARCHAR(13)), 2, '0' );
		RETURN cle;
	END;
$body$
LANGUAGE 'plpgsql' VOLATILE RETURNS NULL ON NULL INPUT SECURITY INVOKER;

-- -----------------------------------------------------------------------------
-- Ex. diffèrent: certification, nom de naissance
-- 3426 (3433 rolepers confondus)
-- -----------------------------------------------------------------------------

SELECT
		COUNT( p1.id )
-- 		p1.id, p1.foyer_id,
-- 		p2.id, p2.foyer_id
	FROM
		personnes AS p1
			INNER JOIN prestations AS pr1 ON (
				pr1.personne_id = p1.id
				AND pr1.natprest = 'RSA'
			),
		personnes AS p2
			INNER JOIN prestations AS pr2 ON (
				pr2.personne_id = p2.id
				AND pr2.natprest = 'RSA'
			)
	WHERE
		p1.id < p2.id
		AND p1.nom = p2.nom
		AND p1.prenom = p2.prenom
		AND p1.dtnai = p2.dtnai
		AND p1.foyer_id = p2.foyer_id
		AND nir_correct( p1.nir )
		AND nir_correct( p2.nir )
		AND p1.nir = p2.nir
		AND pr1.rolepers = pr2.rolepers
-- 	LIMIT 10
;

-- -----------------------------------------------------------------------------
-- FIXME: - des foyers on changé d'état et on a une copie plus récente d'un foyer
--          avec un nouveau numéro
--        - ce n'est pas toujours le cas
-- Pas dans les mêmes foyers, mais les foyers en droits ouverts et versables
-- --> voir avec dossierscaf.numdemrsaprece et dossierscaf.personne_id
-- 189
-- -----------------------------------------------------------------------------

SELECT
-- 		COUNT( p1.id )
		p1.id, p1.foyer_id,
		p2.id, p2.foyer_id
	FROM
		personnes AS p1
			INNER JOIN prestations AS pr1 ON (
				pr1.personne_id = p1.id
				AND pr1.natprest = 'RSA'
			)
			INNER JOIN foyers AS f1 ON (
				p1.foyer_id = f1.id
			)
			INNER JOIN situationsdossiersrsa AS s1 ON (
				s1.dossier_id = f1.dossier_id
			),
		personnes AS p2
			INNER JOIN prestations AS pr2 ON (
				pr2.personne_id = p2.id
				AND pr2.natprest = 'RSA'
			)
			INNER JOIN foyers AS f2 ON (
				p2.foyer_id = f2.id
			)
			INNER JOIN situationsdossiersrsa AS s2 ON (
				s2.dossier_id = f2.dossier_id
			)
	WHERE
		p1.id < p2.id
		AND p1.nom = p2.nom
		AND p1.prenom = p2.prenom
		AND p1.dtnai = p2.dtnai
		AND p1.foyer_id <> p2.foyer_id
		AND nir_correct( p1.nir )
		AND nir_correct( p2.nir )
		AND p1.nir = p2.nir
		AND pr1.rolepers = pr2.rolepers
		AND s1.etatdosrsa IN ( 'Z', '2', '3', '4' )
		AND s2.etatdosrsa IN ( 'Z', '2', '3', '4' )
	LIMIT 10;