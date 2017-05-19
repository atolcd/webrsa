-- #############################################################################
-- Les requêtes ci-dessous permettent de lister certaines incohérences dans les
-- données se trouvant dans WebRSA.
--
-- Le but est d'assainir les données présentes, pour ensuite pouvoir mettre des
-- contraintes d'intégrité sur ces valeurs, afin de s'assurer d'une qualité de
-- données minimum.
--
-- Les contraintes d'intégrité empêcheront l'enregistrement en base de données
-- lorsque celles-ci ne seront pas remplies.
--
-- Une partie des corrections se trouve déjà dans le patch patch-2.0-rc15.sql
--
-- -----------------------------------------------------------------------------
-- Par exemple:
-- On considère qu'un nom de famille doit être composé de 2 lettres majuscules au
-- minimum, sachant que le premier et le dernier caractère doivent être une lettre
-- majuscule, et que les caractères intermédiaires peuvent être soit un tiret,
-- un espace ou une apostrophe.
-- ALTER TABLE personnes ADD CONSTRAINT ADD CONSTRAINT personnes_nom_chk CHECK( nom ~ '^[A-Z]([A-Z]|-| |'')*[A-Z]$' );
-- -----------------------------------------------------------------------------
--
-- A VOIR -> je ne suis pas sûr, mais les données me semblent bizzarres
-- INCOHERENCE -> il faut nettoyer ces données, mais à priori la réparation automatique est possible
-- ANOMALIE -> il faut nettoyer ces données, mais une intervention humaine est certainement nécessaire
-- #############################################################################


-- *****************************************************************************
-- I. Table orientsstructs
-- *****************************************************************************

-- -----------------------------------------------------------------------------
-- I.1. Personnes non demandeurs ou non conjoints RSA orientées -> règle métier
-- A VOIR si c'est à empêcher ou si c'est possible dans certans cas.
-- -----------------------------------------------------------------------------

-- Nombre de personnes non demandeurs ou conjoints RSA possédant une entrée dans orientsstructs, par statut d'orientation et rôle.
SELECT
		COUNT(orientsstructs.id),
		orientsstructs.statut_orient,
		prestations.rolepers
	FROM orientsstructs
		INNER JOIN personnes ON (
			personnes.id = orientsstructs.personne_id
		)
		INNER JOIN prestations ON (
			prestations.personne_id = personnes.id
		)
	WHERE
		prestations.natprest = 'RSA'
		AND prestations.rolepers NOT IN ('DEM', 'CJT')
	GROUP BY
		orientsstructs.statut_orient,
		prestations.rolepers;

-- Détails des personnes non demandeurs ou conjoints RSA possédant une entrée dans orientsstructs, par statut d'orientation et rôle.
SELECT
		orientsstructs.id,
		orientsstructs.personne_id,
		orientsstructs.statut_orient,
		prestations.rolepers
	FROM orientsstructs
		INNER JOIN personnes ON (
			personnes.id = orientsstructs.personne_id
		)
		INNER JOIN prestations ON (
			prestations.personne_id = personnes.id
		)
	WHERE
		prestations.natprest = 'RSA'
		AND prestations.rolepers NOT IN ('DEM', 'CJT')
	ORDER BY
		orientsstructs.statut_orient,
		prestations.rolepers;

-- -----------------------------------------------------------------------------
-- I.2. Vrais doublons d'orientation
-- ANOMALIE -> correction ci-dessous: dédoublonnage
-- -----------------------------------------------------------------------------

-- Liste des personnes possédant au moins deux tuples dont tous les champs (sauf l'id technique) sont identiques dans la table orientsstructs.
SELECT
		orientsstructs.personne_id
	FROM orientsstructs
	WHERE
		orientsstructs.personne_id IN (
			SELECT
						DISTINCT( t.personne_id )
					FROM (
						SELECT
								COUNT(id) AS count,
								orientsstructs.personne_id,
								orientsstructs.typeorient_id,
								orientsstructs.structurereferente_id,
								orientsstructs.propo_algo,
								orientsstructs.valid_cg,
								orientsstructs.date_propo,
								orientsstructs.date_valid,
								orientsstructs.statut_orient,
								orientsstructs.date_impression,
								orientsstructs.daterelance,
								orientsstructs.statutrelance,
								orientsstructs.date_impression_relance,
								orientsstructs.referent_id
							FROM orientsstructs
							GROUP BY
								orientsstructs.personne_id,
								orientsstructs.typeorient_id,
								orientsstructs.structurereferente_id,
								orientsstructs.propo_algo,
								orientsstructs.valid_cg,
								orientsstructs.date_propo,
								orientsstructs.date_valid,
								orientsstructs.statut_orient,
								orientsstructs.date_impression,
								orientsstructs.daterelance,
								orientsstructs.statutrelance,
								orientsstructs.date_impression_relance,
								orientsstructs.referent_id
					) AS t
					WHERE t.count > 1
			);

-- Correction : suppression des lignes en doublons (requête présente dans le patch-2.0rc15.sql).
CREATE OR REPLACE FUNCTION dedoublonnage_orientsstructs() RETURNS bool as
$$
	DECLARE
		v_row		RECORD;
		v_doublon	RECORD;
		v_first_id	INTEGER;
	BEGIN
		FOR v_row IN
			SELECT
					orientsstructs.personne_id
				FROM orientsstructs
				WHERE
					orientsstructs.personne_id IN (
						SELECT
									DISTINCT( t.personne_id )
								FROM (
									SELECT
											COUNT(id) AS count,
											orientsstructs.personne_id,
											orientsstructs.typeorient_id,
											orientsstructs.structurereferente_id,
											orientsstructs.propo_algo,
											orientsstructs.valid_cg,
											orientsstructs.date_propo,
											orientsstructs.date_valid,
											orientsstructs.statut_orient,
											orientsstructs.date_impression,
											orientsstructs.daterelance,
											orientsstructs.statutrelance,
											orientsstructs.date_impression_relance,
											orientsstructs.referent_id
										FROM orientsstructs
										GROUP BY
											orientsstructs.personne_id,
											orientsstructs.typeorient_id,
											orientsstructs.structurereferente_id,
											orientsstructs.propo_algo,
											orientsstructs.valid_cg,
											orientsstructs.date_propo,
											orientsstructs.date_valid,
											orientsstructs.statut_orient,
											orientsstructs.date_impression,
											orientsstructs.daterelance,
											orientsstructs.statutrelance,
											orientsstructs.date_impression_relance,
											orientsstructs.referent_id
								) AS t
								WHERE t.count > 1
						)
		LOOP
			v_first_id := ( SELECT id FROM orientsstructs WHERE orientsstructs.personne_id = v_row.personne_id ORDER BY id LIMIT 1 );

			FOR v_doublon IN
				SELECT
						orientsstructs.id
					FROM orientsstructs
					WHERE
						orientsstructs.personne_id = v_row.personne_id
						AND orientsstructs.id <> v_first_id
						AND orientsstructs.id IN (
							SELECT
								DISTINCT( orientsstructs.id )
									FROM (
										SELECT
												COUNT(id) AS count,
												orientsstructs.personne_id,
												orientsstructs.typeorient_id,
												orientsstructs.structurereferente_id,
												orientsstructs.propo_algo,
												orientsstructs.valid_cg,
												orientsstructs.date_propo,
												orientsstructs.date_valid,
												orientsstructs.statut_orient,
												orientsstructs.date_impression,
												orientsstructs.daterelance,
												orientsstructs.statutrelance,
												orientsstructs.date_impression_relance,
												orientsstructs.referent_id
											FROM orientsstructs
											WHERE orientsstructs.personne_id = v_row.personne_id
											GROUP BY
												orientsstructs.personne_id,
												orientsstructs.typeorient_id,
												orientsstructs.structurereferente_id,
												orientsstructs.propo_algo,
												orientsstructs.valid_cg,
												orientsstructs.date_propo,
												orientsstructs.date_valid,
												orientsstructs.statut_orient,
												orientsstructs.date_impression,
												orientsstructs.daterelance,
												orientsstructs.statutrelance,
												orientsstructs.date_impression_relance,
												orientsstructs.referent_id
									) AS t
									WHERE t.count > 1
							)
			LOOP
				DELETE FROM pdfs WHERE pdfs.modele = 'Orientstruct' AND pdfs.fk_value = v_doublon.id;
				DELETE FROM orientsstructs_servicesinstructeurs WHERE orientsstructs_servicesinstructeurs.orientstruct_id = v_doublon.id;
				DELETE FROM parcoursdetectes WHERE parcoursdetectes.orientstruct_id = v_doublon.id;
				DELETE FROM orientsstructs WHERE orientsstructs.id = v_doublon.id;
			END LOOP;
		END LOOP;

		RETURN false;
	END;
$$
LANGUAGE plpgsql;

SELECT dedoublonnage_orientsstructs();
DROP FUNCTION dedoublonnage_orientsstructs();

-- *****************************************************************************
-- II. Table personnes
-- *****************************************************************************

-- -----------------------------------------------------------------------------
-- Minuscules, caractères accentués -> règle WebRSA
-- Les champs "nom", "prénom", "nomnai", "prenom2", "prenom3" devraient être en majuscules et sans accents.
-- ANOMALIE: voir correction ci-dessous
-- -----------------------------------------------------------------------------

-- FIXME:
CREATE OR REPLACE FUNCTION noaccents_upper( text ) RETURNS text AS
$$
	DECLARE
		st text;

	BEGIN
		st:=translate($1,'aàäâeéèêëiïîoôöuùûücçñAÀÄÂEÉÈÊËIÏÎOÔÖUÙÛÜCÇÑ','AAAAEEEEEIIIOOOUUUUCCNAAAAEEEEEIIIOOOUUUUCCN');
		st:=upper(st);

		return st;
	END;
$$
LANGUAGE 'plpgsql' VOLATILE RETURNS NULL ON NULL INPUT SECURITY INVOKER;

-- Exemple : recherche des noms contenant des minuscules ou des caractères accentués.
SELECT
		DISTINCT(nom)
	FROM personnes
	WHERE noaccents_upper( nom ) <> nom;

-- Correction possible (requête présente dans le patch-2.0rc15.sql).
UPDATE personnes
	SET nom = noaccents_upper(nom)
	WHERE noaccents_upper(nom) <> nom;

-- -----------------------------------------------------------------------------
-- Champs vides (nomnai, prenom2, prenom3)
-- INCOHERENCE: à transformer en NULL - voir correction ci-dessous
-- -----------------------------------------------------------------------------
SELECT *
	FROM personnes
	WHERE
		nomnai IS NOT NULL
		AND CHAR_LENGTH( TRIM( BOTH ' ' FROM nomnai ) ) = 0;

-- Correction possible
UPDATE personnes
	SET nomnai = NULL
	WHERE CHAR_LENGTH( TRIM( BOTH ' ' FROM nomnai ) ) = 0;

-- -----------------------------------------------------------------------------
-- Champs vides (nom, prenom).
-- ANOMALIE: à corriger manuellement
-- -----------------------------------------------------------------------------
SELECT *
	FROM personnes
	WHERE
		prenom IS NOT NULL
		AND CHAR_LENGTH( TRIM( BOTH ' ' FROM prenom ) ) = 0;

-- -----------------------------------------------------------------------------
-- Champs commençant ou terminant par des espaces (nom, prenom, nomnai, ...)
-- ANOMALIE: enlever les espaces devant et derrière - voir correction ci-dessous
-- -----------------------------------------------------------------------------
SELECT *
	FROM personnes
	WHERE CHAR_LENGTH( TRIM( BOTH ' ' FROM nom ) ) <> CHAR_LENGTH( nom );

-- Correction possible
UPDATE personnes
	SET nom = TRIM( BOTH ' ' FROM nom )
	WHERE CHAR_LENGTH( TRIM( BOTH ' ' FROM nom ) ) <> CHAR_LENGTH( nom );

-- -----------------------------------------------------------------------------
-- Champs commençant ou terminant par autre chose qu'une lettre
-- ANOMALIE/A VOIR
-- -----------------------------------------------------------------------------
SELECT *
	FROM personnes
	WHERE nom !~ '^[A-Z]' OR nom !~ '[A-Z]$';

-- -----------------------------------------------------------------------------
-- Un nom (prénom, nomnai, ...) doit être composé de 2 lettres au minimum, en ayant
-- le premier et le dernier caractère qui soient des lettres (ou un apostrophe ?)
-- et dont les autres caractères sont des lettres, tiret, espace, apostrophe.
-- A VOIR
-- -----------------------------------------------------------------------------
SELECT
		DISTINCT( noaccents_upper( TRIM( BOTH ' ' FROM nom ) ) )
	FROM personnes
	WHERE noaccents_upper( TRIM( BOTH ' ' FROM nom ) ) !~ '^[A-Z]([A-Z]|-| |'')*[A-Z]$'
	ORDER BY noaccents_upper( TRIM( BOTH ' ' FROM nom ) );

-- -----------------------------------------------------------------------------
-- Commune de  naissance -> on s'assure qu'il y ait au moins une lettre
-- A VOIR car le format (alphanumérique) semble être très libre.
-- -----------------------------------------------------------------------------

SELECT
		DISTINCT( noaccents_upper( TRIM( BOTH ' ' FROM nomcomnai ) ) )
	FROM personnes
	WHERE noaccents_upper( TRIM( BOTH ' ' FROM nomcomnai ) ) !~ '[A-Z]'
	ORDER BY noaccents_upper( TRIM( BOTH ' ' FROM nomcomnai ) );

-- -----------------------------------------------------------------------------
-- Personnes ayant le nom = au prénom
-- A VOIR
-- -----------------------------------------------------------------------------

SELECT personnes.*
	FROM personnes
	WHERE TRIM( BOTH ' ' FROM nom ) = TRIM( BOTH ' ' FROM prenom )
	ORDER BY nom, prenom, dtnai;

-- -----------------------------------------------------------------------------
-- NIR incorrect -> règle WebRSA: soit le NIR est présent et correct, soit il est NULL
-- ANOMALIE
-- -----------------------------------------------------------------------------

-- Calcul de la clé du NIR (13 caractères) avec gestion des départements 2A et 2B
-- http://fr.wikipedia.org/wiki/Num%C3%A9ro_de_s%C3%A9curit%C3%A9_sociale_en_France#ancrage_E
CREATE OR REPLACE FUNCTION "public"."calcul_cle_nir" (text) RETURNS text AS
$body$
	DECLARE
		p_nir text;
		cle text;
		correction BIGINT;

	BEGIN
		correction:=0;
		p_nir:=$1;

		IF NOT nir_correct( p_nir ) THEN
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

/*
	Vérification du NIR sur 15 caractères
	INFO: http://fr.wikipedia.org/wiki/Num%C3%A9ro_de_s%C3%A9curit%C3%A9_sociale_en_France#Signification_des_chiffres_du_NIR
*/

CREATE OR REPLACE FUNCTION cakephp_validate_ssn( p_ssn text, p_regex text, p_country text ) RETURNS boolean AS
$$
	BEGIN
		RETURN ( p_ssn IS NULL )
			OR(
-- 				(
-- 					( p_country IS NULL OR p_country IN ( 'all', 'can', 'us' ) )
-- 					AND p_ssn ~ E'^(?:\\+?1)?[-. ]?\\(?[2-9][0-8][0-9]\\)?[-. ]?[2-9][0-9]{2}[-. ]?[0-9]{4}$'
-- 				)
-- 				OR
				(
					( p_country = 'fr' )
					AND UPPER( p_ssn ) ~ E'^(1|2|7|8)[0-9]{2}(0[1-9]|10|11|12|[2-9][0-9])((0[1-9]|[1-8][0-9]|9[0-5]|2A|2B)(00[1-9]|0[1-9][0-9]|[1-8][0-9][0-9]|9[0-8][0-9]|990)|(9[7-8][0-9])(0[1-9]|0[1-9]|[1-8][0-9]|90)|99(00[1-9]|0[1-9][0-9]|[1-8][0-9][0-9]|9[0-8][0-9]|990))(00[1-9]|0[1-9][0-9]|[1-9][0-9][0-9]|)(0[1-9]|[1-8][0-9]|9[0-7])$'
				)
				OR
				(
					( p_regex IS NOT NULL )
					AND p_ssn ~ p_regex
				)
			);
	END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION cakephp_validate_ssn( p_ssn text, p_regex text, p_country text ) IS
	'@see http://api.cakephp.org/class/validation#method-Validationssn\nCustom country France (fr) added.';

-- -----------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "public"."nir_correct" (TEXT) RETURNS BOOLEAN AS
$body$
	DECLARE
		p_nir text;

	BEGIN
		p_nir:=$1;

		RETURN (
			CHAR_LENGTH( TRIM( BOTH ' ' FROM p_nir ) ) = 15
			AND (
				cakephp_validate_ssn( p_nir, null, 'fr' )
				AND calcul_cle_nir( SUBSTRING( p_nir FROM 1 FOR 13 ) ) = SUBSTRING( p_nir FROM 14 FOR 2 )
			)
		);
	END;
$body$
LANGUAGE 'plpgsql';

-- -----------------------------------------------------------------------------

-- NIR ne faisant pas 15 caractères
-- Possibilité de correction: mise à NULL
SELECT DISTINCT( nir )
	FROM personnes
	WHERE
		nir IS NOT NULL
		AND LENGTH( TRIM( BOTH ' ' FROM nir ) ) < 15
	ORDER BY nir ASC;

-- NIR faisant 15 caractères, mais dont la clé n'est pas bien formatée (elle vaut 00)
-- Possibilité de correction: recalcul et modification de la clé, mise à NULL si le NIR n'est toujours pas bien formaté
SELECT DISTINCT( nir )
	FROM personnes
	WHERE
		nir IS NOT NULL
		AND LENGTH( TRIM( BOTH ' ' FROM nir ) ) = 15
		AND nir ~ '00$'
	ORDER BY nir ASC;

/*UPDATE personnes
	SET nir = ( SUBSTRING( nir FROM 1 FOR 13 ) || calcul_cle_nir( SUBSTRING( nir FROM 1 FOR 13 ) ) )
	WHERE
		nir IS NOT NULL
		AND LENGTH( TRIM( BOTH ' ' FROM nir ) ) = 15
		AND nir ~ '00$';*/

-- NIR de 15 caractères, avec une clé bien formatée (différente de 00), mais erronés
-- Possibilité de correction: mise à NULL
SELECT DISTINCT( nir )
	FROM personnes
	WHERE
		nir IS NOT NULL
		AND LENGTH( TRIM( BOTH ' ' FROM nir ) ) = 15
		AND nir !~ '00$'
		AND NOT nir_correct( nir )
	ORDER BY nir ASC;

-- *****************************************************************************
-- III. Table ressources
-- Pour la thématique non déclaration de DTR pour tous les dossiers < 6 mois
-- *****************************************************************************

-- -----------------------------------------------------------------------------
-- Nombre de personnes, demandeurs ou conjoints RSA qui n'ont jamais eu de
-- ressources renseignées dans l'application
-- A VOIR, car les données ne sont pas obligatoires dans les flux instruction et
-- bénéficiaire, mais Sammy nous a confirmé qu'il était illogique d'ouvrir un droit
-- RSA sans au moins la DTR correspondant à la période précédent l'ouverture.
-- -----------------------------------------------------------------------------

SELECT
		COUNT(personnes.*)
	FROM personnes
		INNER JOIN prestations ON (
			personnes.id = prestations.personne_id
			AND prestations.natprest = 'RSA'
			AND prestations.rolepers IN ( 'DEM', 'CJT' )
		)
	WHERE
		personnes.id NOT IN (
			SELECT
					personne_id
				FROM ressources
		);