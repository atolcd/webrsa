SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- INFO: http://archives.postgresql.org/pgsql-sql/2005-09/msg00266.php
CREATE OR REPLACE FUNCTION public.add_missing_table_field (text, text, text, text)
RETURNS bool as '
DECLARE
  p_namespace alias for $1;
  p_table     alias for $2;
  p_field     alias for $3;
  p_type      alias for $4;
  v_row       record;
  v_query     text;
BEGIN
  select 1 into v_row from pg_namespace n, pg_class c, pg_attribute a
     where
         --public.slon_quote_brute(n.nspname) = p_namespace and
         n.nspname = p_namespace and
         c.relnamespace = n.oid and
         --public.slon_quote_brute(c.relname) = p_table and
         c.relname = p_table and
         a.attrelid = c.oid and
         --public.slon_quote_brute(a.attname) = p_field;
         a.attname = p_field;
  if not found then
    raise notice ''Upgrade table %.% - add field %'', p_namespace, p_table, p_field;
    v_query := ''alter table '' || p_namespace || ''.'' || p_table || '' add column '';
    v_query := v_query || p_field || '' '' || p_type || '';'';
    execute v_query;
    return ''t'';
  else
    return ''f'';
  end if;
END;' language plpgsql;

COMMENT ON FUNCTION public.add_missing_table_field (text, text, text, text) IS 'Add a column of a given type to a table if it is missing';

-- *****************************************************************************

CREATE OR REPLACE FUNCTION public.alter_table_drop_column_if_exists( text, text, text ) RETURNS bool as
$$
	DECLARE
		p_namespace alias for $1;
		p_table     alias for $2;
		p_field     alias for $3;
		v_row       record;
		v_query     text;
	BEGIN
		SELECT 1 INTO v_row FROM pg_namespace n, pg_class c, pg_attribute a
			WHERE
				n.nspname = p_namespace
				AND c.relnamespace = n.oid
				AND c.relname = p_table
				AND a.attrelid = c.oid
				AND a.attname = p_field;
		IF FOUND THEN
			RAISE NOTICE 'Upgrade table %.% - drop field %', p_namespace, p_table, p_field;
			v_query := 'ALTER TABLE ' || p_namespace || '.' || p_table || ' DROP column ' || p_field || ';';
			EXECUTE v_query;
			RETURN 't';
		ELSE
			RETURN 'f';
		END IF;
	END;
$$
LANGUAGE plpgsql;

COMMENT ON FUNCTION public.add_missing_table_field (text, text, text, text) IS 'Drops a column from a table if it exists.';

SELECT alter_table_drop_column_if_exists( 'public', 'orientsstructs', 'rgorient' );

-- *****************************************************************************

-- A-t'on des vrais doublons ?
-- -> 1322 lignes de doublons pour cg93_20101203_20h46
--    88 avec statut_orient = 'Orienté', 1234 avec statut_orient = 'Non orienté'
-- -> 70 lignes de doublons pour cg66_20101217_eps
--    70 avec statut_orient = 'Orienté'

CREATE OR REPLACE FUNCTION public.dedoublonnage_orientsstructs() RETURNS bool as
$$
	DECLARE
		v_row		RECORD;
		v_tmprow	RECORD;
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
											orientsstructs.referent_id,
											orientsstructs.etatorient
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
											orientsstructs.referent_id,
											orientsstructs.etatorient
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
												orientsstructs.referent_id,
												orientsstructs.etatorient
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
												orientsstructs.referent_id,
												orientsstructs.etatorient
									) AS t
									WHERE t.count > 1
							)
			LOOP
				DELETE FROM pdfs WHERE pdfs.modele = 'Orientstruct' AND pdfs.fk_value = v_doublon.id;
				-- FIXME: pas delete mais UPDATE ?
				DELETE FROM orientsstructs_servicesinstructeurs WHERE orientsstructs_servicesinstructeurs.orientstruct_id = v_doublon.id;
				-- FIXME: pas delete mais UPDATE ?
				SELECT 1 INTO v_tmprow FROM pg_namespace n, pg_class c
					WHERE
						n.nspname = 'public' and
						c.relnamespace = n.oid and
						c.relname = 'parcoursdetectes';
				IF FOUND THEN
					DELETE FROM parcoursdetectes WHERE parcoursdetectes.orientstruct_id = v_doublon.id;
				END IF;
				DELETE FROM orientsstructs WHERE orientsstructs.id = v_doublon.id;
			END LOOP;
		END LOOP;

		RETURN false;-- FIXME: retourne le nombre de suppressions ?
	END;
$$
LANGUAGE plpgsql;

SELECT dedoublonnage_orientsstructs();
DROP FUNCTION public.dedoublonnage_orientsstructs();

ALTER TABLE orientsstructs ADD COLUMN rgorient INTEGER DEFAULT NULL; -- INFO: rgorient SSI Orienté -> sinon, ça n'a pas de sens ? cf. Orientstruct;;beforeSave

UPDATE orientsstructs SET rgorient = NULL;
UPDATE orientsstructs
	SET rgorient = (
		SELECT ( COUNT(orientsstructspcd.id) + 1 )
			FROM orientsstructs AS orientsstructspcd
			WHERE orientsstructspcd.personne_id = orientsstructs.personne_id
				AND orientsstructspcd.id <> orientsstructs.id
				AND orientsstructs.date_valid IS NOT NULL
				AND orientsstructspcd.date_valid IS NOT NULL
				AND (
					orientsstructspcd.date_valid < orientsstructs.date_valid
					OR ( orientsstructspcd.date_valid = orientsstructs.date_valid AND orientsstructspcd.id < orientsstructs.id )
				)
				AND orientsstructs.statut_orient = 'Orienté'
				AND orientsstructspcd.statut_orient = 'Orienté'
	)
	WHERE
		orientsstructs.date_valid IS NOT NULL
		AND orientsstructs.statut_orient = 'Orienté';

CREATE UNIQUE INDEX orientsstructs_personne_id_rgorient_idx ON orientsstructs( personne_id, rgorient ) WHERE rgorient IS NOT NULL;

UPDATE orientsstructs
	SET statut_orient = 'Non orienté'
	WHERE typeorient_id IS NULL
		OR structurereferente_id IS NULL
		OR date_valid IS NULL;

ALTER TABLE orientsstructs ADD CONSTRAINT orientsstructs_statut_orient_oriente_rgorient_not_null_chk CHECK (
	statut_orient <> 'Orienté' OR ( statut_orient = 'Orienté' AND rgorient IS NOT NULL )
);

/*
-- FIXME: si statut_orient Orienté et date_valid -> valid_cg = true ?
-- En fait, il semblerait que l'on puisse supprimer la colonne (en modifiant le PHP)
app/models/critere.php:                    '"Orientstruct"."valid_cg"',
app/tests/fixtures/orientstruct_fixture.php:                            'valid_cg' => null,
app/tests/fixtures/orientstruct_fixture.php:                            'valid_cg' => '1',
app/tests/fixtures/orientstruct_fixture.php:                            'valid_cg' => '1',
app/tests/fixtures/orientstruct_fixture.php:                            'valid_cg' => '1',
app/tests/fixtures/orientstruct_fixture.php:                            'valid_cg' => '1',
app/tests/fixtures/orientstruct_fixture.php:                            'valid_cg' => '1',
app/tests/fixtures/orientstruct_fixture.php:                            'valid_cg' => '1',
app/tests/fixtures/orientstruct_fixture.php:                            'valid_cg' => '1',
app/tests/fixtures/orientstruct_fixture.php:                            'valid_cg' => '1',
app/controllers/dossierssimplifies_controller.php:                                $this->data['Orientstruct'][$key]['valid_cg'] = true;
app/controllers/orientsstructs_controller.php:                                  $this->data['Orientstruct']['valid_cg'] = true;
app/Vendor/shells/refresh.php:                                                         'Orientstruct.valid_cg',
*/

DROP TYPE IF EXISTS type_statutoccupation CASCADE;
CREATE TYPE type_statutoccupation AS ENUM ( 'proprietaire', 'locataire' );
ALTER TABLE dsps ADD COLUMN statutoccupation type_statutoccupation DEFAULT NULL;
ALTER TABLE dsps_revs ADD COLUMN statutoccupation type_statutoccupation DEFAULT NULL;

-- *****************************************************************************
-- Ajout de champs dans la table traitementspdos pour gérer la fiche de calcul
-- *****************************************************************************

DROP TYPE IF EXISTS TYPE_REGIMEFICHECALCUL CASCADE;
CREATE TYPE TYPE_REGIMEFICHECALCUL AS ENUM ( 'fagri', 'ragri', 'reel', 'microbic', 'microbicauto', 'microbnc' );

SELECT add_missing_table_field ('public', 'traitementspdos', 'regime', 'TYPE_REGIMEFICHECALCUL');
SELECT add_missing_table_field ('public', 'traitementspdos', 'saisonnier', 'TYPE_BOOLEANNUMBER');
SELECT add_missing_table_field ('public', 'traitementspdos', 'nrmrcs', 'VARCHAR(20)');
SELECT add_missing_table_field ('public', 'traitementspdos', 'dtdebutactivite', 'DATE');
SELECT add_missing_table_field ('public', 'traitementspdos', 'raisonsocial', 'VARCHAR(100)');
SELECT add_missing_table_field ('public', 'traitementspdos', 'dtdebutperiode', 'DATE');
SELECT add_missing_table_field ('public', 'traitementspdos', 'dtfinperiode', 'DATE');
SELECT add_missing_table_field ('public', 'traitementspdos', 'dtprisecompte', 'DATE');
SELECT add_missing_table_field ('public', 'traitementspdos', 'dtecheance', 'DATE');
SELECT add_missing_table_field ('public', 'traitementspdos', 'forfait', 'FLOAT');
SELECT add_missing_table_field ('public', 'traitementspdos', 'mtaidesub', 'FLOAT');
SELECT add_missing_table_field ('public', 'traitementspdos', 'chaffvnt', 'FLOAT');
SELECT add_missing_table_field ('public', 'traitementspdos', 'chaffsrv', 'FLOAT');
SELECT add_missing_table_field ('public', 'traitementspdos', 'benefoudef', 'FLOAT');
SELECT add_missing_table_field ('public', 'traitementspdos', 'ammortissements', 'FLOAT');
SELECT add_missing_table_field ('public', 'traitementspdos', 'salaireexploitant', 'FLOAT');
SELECT add_missing_table_field ('public', 'traitementspdos', 'provisionsnonded', 'FLOAT');
SELECT add_missing_table_field ('public', 'traitementspdos', 'moinsvaluescession', 'FLOAT');
SELECT add_missing_table_field ('public', 'traitementspdos', 'autrecorrection', 'FLOAT');

SELECT add_missing_table_field ('public', 'traitementspdos', 'nbmoisactivite', 'INTEGER');
SELECT add_missing_table_field ('public', 'traitementspdos', 'mnttotalpriscompte', 'FLOAT');
SELECT add_missing_table_field ('public', 'traitementspdos', 'revenus', 'FLOAT');
SELECT add_missing_table_field ('public', 'traitementspdos', 'benefpriscompte', 'FLOAT');

DROP TYPE IF EXISTS TYPE_AIDESUBVREINT CASCADE;
CREATE TYPE TYPE_AIDESUBVREINT AS ENUM ( 'aide1', 'aide2', 'subv1', 'subv2' );
SELECT add_missing_table_field ('public', 'traitementspdos', 'aidesubvreint', 'TYPE_AIDESUBVREINT');

-- *****************************************************************************
-- Création du nouvel enum pour l'état des dossier de PDO
-- *****************************************************************************

DROP TYPE IF EXISTS TYPE_ETATDOSSIERPDO CASCADE;
CREATE TYPE TYPE_ETATDOSSIERPDO AS ENUM ( 'attaffect', 'attinstr', 'instrencours', 'attval', 'decisionval', 'dossiertraite', 'attpj' );

SELECT add_missing_table_field ('public', 'propospdos', 'etatdossierpdo', 'TYPE_ETATDOSSIERPDO');

-- *****************************************************************************
-- Déplacement des champs de décisions de la PDO dans une autre table
-- *****************************************************************************

DROP TABLE IF EXISTS decisionspropospdos;
CREATE TABLE decisionspropospdos (
	id      				SERIAL NOT NULL PRIMARY KEY,
	datedecisionpdo			DATE,
	decisionpdo_id			INTEGER REFERENCES decisionspdos (id),
	commentairepdo			TEXT,
	isvalidation			type_booleannumber DEFAULT NULL,
	validationdecision		type_no DEFAULT NULL,
	datevalidationdecision	DATE,
	etatdossierpdo			TYPE_ETATDOSSIERPDO DEFAULT NULL,
	propopdo_id				INTEGER REFERENCES propospdos (id)
);

INSERT INTO decisionspropospdos ( isvalidation, validationdecision, datevalidationdecision, datedecisionpdo, commentairepdo, decisionpdo_id, propopdo_id )
    SELECT
        propospdos.isvalidation,
        propospdos.validationdecision,
        propospdos.datevalidationdecision,
        propospdos.datedecisionpdo,
        propospdos.commentairepdo,
        propospdos.decisionpdo_id,
        propospdos.id
    FROM propospdos;

SELECT alter_table_drop_column_if_exists( 'public', 'propospdos', 'datedecisionpdo' );
SELECT alter_table_drop_column_if_exists( 'public', 'propospdos', 'decisionpdo_id' );
SELECT alter_table_drop_column_if_exists( 'public', 'propospdos', 'commentairepdo' );
SELECT alter_table_drop_column_if_exists( 'public', 'propospdos', 'isvalidation' );
SELECT alter_table_drop_column_if_exists( 'public', 'propospdos', 'validationdecision' );
SELECT alter_table_drop_column_if_exists( 'public', 'propospdos', 'datevalidationdecision' );

DROP INDEX IF EXISTS decisionspropospdos_decisionpdo_id_idx;
CREATE INDEX decisionspropospdos_decisionpdo_id_idx ON decisionspropospdos (decisionpdo_id);

DROP INDEX IF EXISTS decisionspropospdos_propopdo_id_idx;
CREATE INDEX decisionspropospdos_propopdo_id_idx ON decisionspropospdos (propopdo_id);

-- *****************************************************************************
-- Nouvelle structure pour les informations venant de Pôle Emploi
-- *****************************************************************************

-- INFO: voir http://postgresql.developpez.com/sources/?page=chaines
CREATE OR REPLACE FUNCTION "public"."noaccents_upper" (text) RETURNS text AS
$body$
	DECLARE
		st text;

	BEGIN
		-- On transforme les caractèes accentués et on passe en majuscule
		st:=translate($1,'aàäâeéèêëiïîoôöuùûücçñAÀÄÂEÉÈÊËIÏÎOÔÖUÙÛÜCÇÑ','AAAAEEEEEIIIOOOUUUUCCNAAAAEEEEEIIIOOOUUUUCCN');
		st:=upper(st);

		return st;
	END;
$body$
LANGUAGE 'plpgsql' VOLATILE RETURNS NULL ON NULL INPUT SECURITY INVOKER;

-- FIXME: problèmes de minuscules et d'accents dans la table personnes --> mettre une contrainte ?
-- FIXME: problèmes de nom / prenom vides (pas NULL mais vides) dans la table personnes -> contrainte ?

-- Mise à jour sur la table personnes (nomnai, ... à NULL si une chaîne vide)
UPDATE personnes SET nomnai = NULL WHERE CHAR_LENGTH( TRIM( BOTH ' ' FROM nomnai ) ) = 0;
UPDATE personnes SET prenom2 = NULL WHERE CHAR_LENGTH( TRIM( BOTH ' ' FROM prenom2 ) ) = 0;
UPDATE personnes SET prenom3 = NULL WHERE CHAR_LENGTH( TRIM( BOTH ' ' FROM prenom3 ) ) = 0;

-- Mise à jour sur la table personnes (nom, ... -> en majuscules)
UPDATE personnes SET nom = public.noaccents_upper(nom) WHERE nom !~ '^([A-Z]|\-| |'')+$';
UPDATE personnes SET prenom = public.noaccents_upper(prenom) WHERE prenom !~ '^([A-Z]|\-| |'')+$';
UPDATE personnes SET nomnai = public.noaccents_upper(nomnai) WHERE ( nomnai IS NOT NULL AND nomnai !~ '^([A-Z]|\-| |'')+$' );
UPDATE personnes SET prenom2 = public.noaccents_upper(prenom2) WHERE ( prenom2 IS NOT NULL AND prenom2 !~ '^([A-Z]|\-| |'')+$' );
UPDATE personnes SET prenom3 = public.noaccents_upper(prenom3) WHERE ( prenom3 IS NOT NULL AND prenom3 !~ '^([A-Z]|\-| |'')+$' );

-- Mise à jour des anciennes tables tables concernant les inscriptions/cessations/radiations Pôle Emploi
UPDATE tempcessations SET nom = public.noaccents_upper(nom) WHERE nom !~ '^([A-Z]|\-| |'')+$';
UPDATE tempcessations SET prenom = public.noaccents_upper(nom) WHERE prenom !~ '^([A-Z]|\-| |'')+$';
UPDATE tempinscriptions SET nom = public.noaccents_upper(nom) WHERE nom !~ '^([A-Z]|\-| |'')+$';
UPDATE tempinscriptions SET prenom = public.noaccents_upper(nom) WHERE prenom !~ '^([A-Z]|\-| |'')+$';
UPDATE tempradiations SET nom = public.noaccents_upper(nom) WHERE nom !~ '^([A-Z]|\-| |'')+$';
UPDATE tempradiations SET prenom = public.noaccents_upper(nom) WHERE prenom !~ '^([A-Z]|\-| |'')+$';

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

-- Correction: certains NIRs ont 15 caractères avec les deux derniers à 0 -> recalcul de la clé
/*UPDATE personnes
	SET nir = ( SUBSTRING( nir FROM 1 FOR 13 ) || calcul_cle_nir( SUBSTRING( nir FROM 1 FOR 13 ) ) )
	WHERE
		nir IS NOT NULL
		AND LENGTH( TRIM( BOTH ' ' FROM nir ) ) = 15
		AND nir ~ '00$';*/

-- SELECT COUNT(id) FROM personnes WHERE NOT nir_correct( nir );
-- SELECT COUNT(nir), nir FROM personnes WHERE NOT nir_correct( nir ) GROUP BY nir ORDER BY nir ASC;
-- SELECT COUNT(nir), nir FROM personnes WHERE NOT nir_correct( nir ) AND TRIM( BOTH ' ' FROM nir ) = '' GROUP BY nir ORDER BY nir ASC;
-- SELECT COUNT(nir), nir FROM personnes WHERE NOT nir_correct( nir ) AND TRIM( BOTH ' ' FROM nir ) <> '' AND nir !~ '[0-9]' GROUP BY nir ORDER BY nir ASC;
-- SELECT COUNT(nir), nir, LENGTH(nir), LENGTH(TRIM( BOTH ' ' FROM nir )) FROM personnes WHERE NOT nir_correct( nir ) AND LENGTH(TRIM( BOTH ' ' FROM nir )) <> 15 GROUP BY nir ORDER BY nir ASC;
-- SELECT COUNT(nir), nir, LENGTH(TRIM( BOTH ' ' FROM nir )) FROM personnes WHERE NOT nir_correct( nir ) AND LENGTH(TRIM( BOTH ' ' FROM nir )) = 15 GROUP BY nir ORDER BY nir ASC;

-- SELECT noaccents_upper(nom), LENGTH(noaccents_upper(nom)), TRIM( BOTH ' ' FROM noaccents_upper(nom) ), LENGTH(TRIM( BOTH ' ' FROM noaccents_upper(nom) )) FROM personnes WHERE noaccents_upper(nom) !~ '^[A-Z]([A-Z]|-| |'')*[A-Z]$';
-- SELECT noaccents_upper(prenom), LENGTH(noaccents_upper(prenom)), TRIM( BOTH ' ' FROM noaccents_upper(prenom) ), LENGTH(TRIM( BOTH ' ' FROM noaccents_upper(prenom) )) FROM personnes WHERE noaccents_upper(prenom) !~ '^[A-Z]([A-Z]|-| |'')*[A-Z]$';
-- SELECT noaccents_upper(nomnai), LENGTH(noaccents_upper(nomnai)), TRIM( BOTH ' ' FROM noaccents_upper(nomnai) ), LENGTH(TRIM( BOTH ' ' FROM noaccents_upper(nomnai) )) FROM personnes WHERE nomnai IS NOT NULL AND LENGTH(noaccents_upper(nomnai)) > 0 AND noaccents_upper(nomnai) !~ '^[A-Z]([A-Z]|-| |'')*[A-Z]$';

-- 0°) Nettoyage ---------------------------------------------------------------
DROP TABLE IF EXISTS historiqueetatspe CASCADE;
DROP TABLE IF EXISTS informationspe CASCADE;

DROP TYPE IF EXISTS TYPE_ETATPE CASCADE;

--

SELECT public.add_missing_table_field ( 'public', 'tempinscriptions', 'nir15', 'VARCHAR(15)');
UPDATE tempinscriptions SET nir15 = CASE WHEN ( nir_correct( nir || calcul_cle_nir( nir ) ) ) THEN nir || calcul_cle_nir( nir ) ELSE NULL END;
SELECT public.add_missing_table_field ( 'public', 'tempcessations', 'nir15', 'VARCHAR(15)');
UPDATE tempcessations SET nir15 = CASE WHEN ( nir_correct( nir || calcul_cle_nir( nir ) ) ) THEN nir || calcul_cle_nir( nir ) ELSE NULL END;
SELECT public.add_missing_table_field ( 'public', 'tempradiations', 'nir15', 'VARCHAR(15)');
UPDATE tempradiations SET nir15 = CASE WHEN ( nir_correct( nir || calcul_cle_nir( nir ) ) ) THEN nir || calcul_cle_nir( nir ) ELSE NULL END;

-- 1°) -------------------------------------------------------------------------
-- TODO: pourquoi une erreur avec les REFERENCES ?
CREATE TABLE informationspe (
	id				SERIAL NOT NULL PRIMARY KEY,
	nir				VARCHAR(15) DEFAULT NULL,
	nom				VARCHAR(50) DEFAULT NULL, -- FIXME: une personne a un nom NULL (id 50946) dans la table personnes (CG 66, 20101217_dump_webrsaCG66_rc9.sql.gz)
	prenom			VARCHAR(50) NOT NULL,
	dtnai			DATE NOT NULL
);

-- Contrainte sur le NIR qui doit être bien formé ou être NULL -- FIXME avec les valeurs réelles possibles, cf. fonction nir_correct
ALTER TABLE informationspe ADD CONSTRAINT informationspe_nir_correct_chk CHECK( nir IS NULL OR nir_correct( nir ) );
-- -- Test: doivent passer
-- INSERT INTO informationspe ( nir, nom, prenom, dtnai ) VALUES
-- 	( NULL, 'Foo', 'Bar', '2010-10-28' ),
-- 	( '123456789012345', 'Foo', 'Bar', '2010-10-28' );
-- -- Test: ne doit pas passer
-- INSERT INTO informationspe ( nir, nom, prenom, dtnai ) VALUES
-- 	( '123456 89012345', 'Foo', 'Bar', '2010-10-28' );

-- Indexes
CREATE INDEX informationspe_nir_idx ON informationspe ( nir varchar_pattern_ops );
-- FIXME: majuscules ?
CREATE INDEX informationspe_nom_idx ON informationspe ( nom varchar_pattern_ops );
CREATE INDEX informationspe_prenom_idx ON informationspe ( prenom varchar_pattern_ops );
CREATE INDEX informationspe_dtnai_idx ON informationspe ( dtnai );
CREATE UNIQUE INDEX informationspe_unique_tuple_idx ON informationspe ( nir, nom, prenom, dtnai );

COMMENT ON TABLE informationspe IS 'Liens entre Pôle Emploi et de supposés allocataires.';

-- 2°) Population de la table avec les valeurs des anciennes tables ------------

-- A partir des personnes déjà trouvées
INSERT INTO informationspe ( nir, nom, prenom, dtnai )
SELECT
		CASE WHEN ( nir_correct( personnes.nir ) ) THEN personnes.nir
			ELSE NULL
		END AS nir,
		personnes.nom,
		personnes.prenom,
		personnes.dtnai
	FROM infospoleemploi
	INNER JOIN personnes ON (
		infospoleemploi.personne_id = personnes.id
	)
	GROUP BY
		personnes.nir,
		personnes.nom,
		personnes.prenom,
		personnes.dtnai
	ORDER BY
		personnes.nir,
		personnes.nom,
		personnes.prenom,
		personnes.dtnai;

-- A partir des personnes pas encore trouvées (tables tempXXX)
INSERT INTO informationspe ( nir, nom, prenom, dtnai )
	SELECT
			nir15 AS nir,
			temp.nom,
			temp.prenom,
			temp.dtnai
		FROM (
			SELECT *
				FROM(
					SELECT
							nir15,
							nom,
							prenom,
							dtnai
						FROM tempcessations
					UNION
					SELECT
							nir15,
							nom,
							prenom,
							dtnai
						FROM tempradiations
					UNION
					SELECT
							nir15,
							nom,
							prenom,
							dtnai
						FROM tempinscriptions
				) AS tmptables
		) AS temp
		WHERE (
			SELECT
					COUNT(*)
				FROM informationspe
				WHERE (
						(
							informationspe.nir IS NOT NULL
							AND temp.nir15 IS NOT NULL
							AND informationspe.nir = temp.nir15
						)
						OR (
							informationspe.nom = temp.nom
							AND informationspe.prenom = temp.prenom
							AND informationspe.dtnai = temp.dtnai
						)
					)
		) = 0
		GROUP BY
			temp.nir15,
			temp.nom,
			temp.prenom,
			temp.dtnai;

-- 3°) -------------------------------------------------------------------------

CREATE TYPE TYPE_ETATPE AS ENUM ( 'cessation', 'inscription', 'radiation' );

CREATE TABLE historiqueetatspe (
	id					SERIAL NOT NULL PRIMARY KEY,
	informationpe_id	INTEGER NOT NULL REFERENCES informationspe(id) ON UPDATE CASCADE ON DELETE CASCADE,
	identifiantpe		VARCHAR(11) NOT NULL, -- FIXME: 11 ou 8 et 3 pour la structure ?
	date				DATE NOT NULL,
	etat				TYPE_ETATPE NOT NULL,
	code				VARCHAR(2) DEFAULT NULL,
	motif				VARCHAR(250) DEFAULT NULL
);

COMMENT ON TABLE historiqueetatspe IS 'Historique des états par lesquels passe un supposé allocataire à Pôle Emploi, avec l''identifiant PE associé.';

CREATE INDEX historiqueetatspe_informationpe_id_idx ON historiqueetatspe ( informationpe_id );
CREATE INDEX historiqueetatspe_identifiantpe_idx ON historiqueetatspe ( identifiantpe varchar_pattern_ops );
CREATE INDEX historiqueetatspe_date_idx ON historiqueetatspe ( date );
CREATE INDEX historiqueetatspe_etat_idx ON historiqueetatspe ( etat );
CREATE INDEX historiqueetatspe_code_idx ON historiqueetatspe ( code varchar_pattern_ops );
CREATE INDEX historiqueetatspe_motif_idx ON historiqueetatspe ( motif varchar_pattern_ops );
CREATE UNIQUE INDEX historiqueetatspe_unique_tuple_idx ON historiqueetatspe ( informationpe_id, identifiantpe, date, etat, code, motif );

-- 4°) Population de la table avec les valeurs des anciennes tables ------------
-- A partir des personnes déjà trouvées
-- FIXME: ici, les inscriptions, c'est ceux qui n'ont rien dans les autres dates
-- -> faut il rajouter les inscriptions de ceux qui ont quelque chose dans ces autres dates ?
INSERT INTO historiqueetatspe ( informationpe_id, identifiantpe, date, etat, code, motif )
	SELECT
			informationspe.id,
			infospoleemploi.identifiantpe,
			CASE
				WHEN infospoleemploi.datecessation IS NOT NULL THEN infospoleemploi.datecessation
				WHEN infospoleemploi.dateradiation IS NOT NULL THEN infospoleemploi.dateradiation
				WHEN infospoleemploi.dateinscription IS NOT NULL THEN infospoleemploi.dateinscription
			END AS date,
			CASE
				WHEN infospoleemploi.datecessation IS NOT NULL THEN CAST( 'cessation' AS TYPE_ETATPE )
				WHEN infospoleemploi.dateradiation IS NOT NULL THEN CAST( 'radiation' AS TYPE_ETATPE )
				WHEN infospoleemploi.dateinscription IS NOT NULL THEN CAST( 'inscription' AS TYPE_ETATPE )
			END AS etat,
			CASE
				WHEN infospoleemploi.datecessation IS NOT NULL THEN NULL
				WHEN infospoleemploi.dateradiation IS NOT NULL THEN NULL
				WHEN infospoleemploi.dateinscription IS NOT NULL THEN infospoleemploi.categoriepe
			END AS code,
			CASE
				WHEN infospoleemploi.datecessation IS NOT NULL THEN infospoleemploi.motifcessation
				WHEN infospoleemploi.dateradiation IS NOT NULL THEN infospoleemploi.motifradiation
				WHEN infospoleemploi.dateinscription IS NOT NULL THEN NULL
			END AS motif
		FROM infospoleemploi
			INNER JOIN personnes ON (
				personnes.id = infospoleemploi.personne_id
			)
			INNER JOIN informationspe ON (
				(
					informationspe.nir IS NOT NULL
					AND personnes.nir IS NOT NULL
					AND informationspe.nir = personnes.nir
				)
				OR
				(
					informationspe.nom = personnes.nom
					AND informationspe.prenom = personnes.prenom
					AND informationspe.dtnai = personnes.dtnai
				)
			)
		GROUP BY
			informationspe.id,
			infospoleemploi.identifiantpe,
			date,
			etat,
			code,
			motif;

-- A partir des personnes pas encore trouvées (tables tempXXX)
INSERT INTO historiqueetatspe ( informationpe_id, identifiantpe, date, etat, code, motif )
	SELECT
			informationspe.id,
			identifiantpe,
			date,
			etat,
			code,
			motif
		FROM(
			SELECT
					nir15,
					identifiantpe,
					nom,
					prenom,
					dtnai,
					CAST( 'cessation' AS TYPE_ETATPE ) AS etat,
					datecessation AS date,
					NULL AS code,
					motifcessation as motif
				FROM tempcessations
			UNION
			SELECT
					nir15,
					identifiantpe,
					nom,
					prenom,
					dtnai,
					CAST( 'radiation' AS TYPE_ETATPE ) AS etat,
					dateradiation AS date,
					NULL AS code,
					motifradiation as motif
				FROM tempradiations
			UNION
			SELECT
					nir15,
					identifiantpe,
					nom,
					prenom,
					dtnai,
					CAST( 'inscription' AS TYPE_ETATPE ) AS etat,
					dateinscription AS date,
					categoriepe AS code,
					NULL as motif
				FROM tempinscriptions
		) AS temp
			INNER JOIN informationspe ON (
				(
					informationspe.nir IS NOT NULL
					AND temp.nir15 IS NOT NULL
					AND informationspe.nir = temp.nir15
				)
				OR (
					informationspe.nom = temp.nom
					AND informationspe.prenom = temp.prenom
					AND informationspe.dtnai = temp.dtnai
				)
			)
		GROUP BY
			informationspe.id,
			identifiantpe,
			date,
			etat,
			code,
			motif;

-- 5°) Mise à jour des codes -- FIXME: tous les codes
UPDATE
	historiqueetatspe
	SET code = '90'
	WHERE code IS NULL
		AND etat = 'cessation'
		AND motif = 'ABSENCE AU CONTROLE (NON REPONSE A DAM)';

UPDATE
	historiqueetatspe
	SET code = 'CX'
	WHERE code IS NULL
		AND etat = 'radiation'
		AND motif = 'REFUS ACTION INSERTION SUSPENSION DE QUINZE JOURS';

UPDATE
	historiqueetatspe
	SET code = '92'
	WHERE code IS NULL
		AND etat = 'radiation'
		AND motif = 'NON REPONSE A CONVOCATION SUSPENSION DE DEUX MOIS';

UPDATE
	historiqueetatspe
	SET code = '8X'
	WHERE code IS NULL
		AND etat = 'radiation'
		AND motif = 'INSUFFISANCE DE RECHERCHE D''EMPLOI SUSPENSION DE QUINZE JOURS';

--

DROP INDEX IF EXISTS decisionspropospdos_datedecisionpdo_idx;
DROP INDEX IF EXISTS decisionspropospdos_datevalidationdecision_idx;
DROP INDEX IF EXISTS decisionspropospdos_etatdossierpdo_idx;
DROP INDEX IF EXISTS decisionspropospdos_isvalidation_idx;
DROP INDEX IF EXISTS decisionspropospdos_validationdecision_idx;
DROP INDEX IF EXISTS detailsressourcesmensuelles_ressourcesmensuelles_detailressourcemensuelle_id_idx;
DROP INDEX IF EXISTS detailsressourcesmensuelles_ressourcesmensuelles_ressourcemensuelle_id_idx;
DROP INDEX IF EXISTS dsps_statutoccupation_idx;
DROP INDEX IF EXISTS dsps_revs_statutoccupation_idx;
DROP INDEX IF EXISTS locsvehicinsert_pieceslocsvehicinsert_piecelocvehicinsert_id_idx;
DROP INDEX IF EXISTS regroupementszonesgeo_zonesgeographiques_regroupementzonegeo_id_idx;
DROP INDEX IF EXISTS regroupementszonesgeo_zonesgeographiques_zonegeographique_id_idx;
DROP INDEX IF EXISTS structuresreferentes_zonesgeographiques_structurereferente_id_idx;
DROP INDEX IF EXISTS traitementspdos_aidesubvreint_idx;
DROP INDEX IF EXISTS traitementspdos_dtdebutactivite_idx;
DROP INDEX IF EXISTS traitementspdos_dtdebutperiode_idx;
DROP INDEX IF EXISTS traitementspdos_dtecheance_idx;
DROP INDEX IF EXISTS traitementspdos_dtfinperiode_idx;
DROP INDEX IF EXISTS traitementspdos_dtprisecompte_idx;
DROP INDEX IF EXISTS traitementspdos_regime_idx;
DROP INDEX IF EXISTS traitementspdos_saisonnier_idx;

-- -----------------------------------------------------------------------------

CREATE INDEX decisionspropospdos_datedecisionpdo_idx ON decisionspropospdos (datedecisionpdo);
CREATE INDEX decisionspropospdos_datevalidationdecision_idx ON decisionspropospdos (datevalidationdecision);
CREATE INDEX decisionspropospdos_etatdossierpdo_idx ON decisionspropospdos (etatdossierpdo);
CREATE INDEX decisionspropospdos_isvalidation_idx ON decisionspropospdos (isvalidation);
CREATE INDEX decisionspropospdos_validationdecision_idx ON decisionspropospdos (validationdecision);
CREATE INDEX detailsressourcesmensuelles_ressourcesmensuelles_detailressourcemensuelle_id_idx ON detailsressourcesmensuelles_ressourcesmensuelles (detailressourcemensuelle_id);
CREATE INDEX detailsressourcesmensuelles_ressourcesmensuelles_ressourcemensuelle_id_idx ON detailsressourcesmensuelles_ressourcesmensuelles (ressourcemensuelle_id);
CREATE INDEX dsps_statutoccupation_idx ON dsps (statutoccupation);
CREATE INDEX dsps_revs_statutoccupation_idx ON dsps_revs (statutoccupation);
CREATE INDEX locsvehicinsert_pieceslocsvehicinsert_piecelocvehicinsert_id_idx ON locsvehicinsert_pieceslocsvehicinsert (piecelocvehicinsert_id);
CREATE INDEX regroupementszonesgeo_zonesgeographiques_regroupementzonegeo_id_idx ON regroupementszonesgeo_zonesgeographiques (regroupementzonegeo_id);
CREATE INDEX regroupementszonesgeo_zonesgeographiques_zonegeographique_id_idx ON regroupementszonesgeo_zonesgeographiques (zonegeographique_id);
CREATE INDEX structuresreferentes_zonesgeographiques_structurereferente_id_idx ON structuresreferentes_zonesgeographiques (structurereferente_id);
CREATE INDEX traitementspdos_aidesubvreint_idx ON traitementspdos (aidesubvreint);
CREATE INDEX traitementspdos_dtdebutactivite_idx ON traitementspdos (dtdebutactivite);
CREATE INDEX traitementspdos_dtdebutperiode_idx ON traitementspdos (dtdebutperiode);
CREATE INDEX traitementspdos_dtecheance_idx ON traitementspdos (dtecheance);
CREATE INDEX traitementspdos_dtfinperiode_idx ON traitementspdos (dtfinperiode);
CREATE INDEX traitementspdos_dtprisecompte_idx ON traitementspdos (dtprisecompte);
CREATE INDEX traitementspdos_regime_idx ON traitementspdos (regime);
CREATE INDEX traitementspdos_saisonnier_idx ON traitementspdos (saisonnier);

-- -----------------------------------------------------------------------------

SELECT public.add_missing_table_field ( 'public', 'aidesapres66', 'motifrejetequipe', 'TEXT');

-- -----------------------------------------------------------------------------
-- 20110120
-- -----------------------------------------------------------------------------

DROP INDEX IF EXISTS calculsdroitsrsa_personne_id_idx;
CREATE UNIQUE INDEX calculsdroitsrsa_personne_id_idx ON calculsdroitsrsa( personne_id );

-- -----------------------------------------------------------------------------
-- 20110207
-- -----------------------------------------------------------------------------
SELECT alter_table_drop_column_if_exists( 'public', 'decisionspropospdos', 'commentairedecision' );
ALTER TABLE decisionspropospdos ADD COLUMN commentairedecision TEXT DEFAULT NULL;

SELECT alter_table_drop_column_if_exists( 'public', 'decisionspropospdos', 'avistechnique' );
ALTER TABLE decisionspropospdos ADD COLUMN avistechnique type_no DEFAULT NULL;

SELECT alter_table_drop_column_if_exists( 'public', 'decisionspropospdos', 'dateavistechnique' );
ALTER TABLE decisionspropospdos ADD COLUMN dateavistechnique DATE;

SELECT alter_table_drop_column_if_exists( 'public', 'decisionspropospdos', 'commentaireavistechnique' );
ALTER TABLE decisionspropospdos ADD COLUMN commentaireavistechnique TEXT DEFAULT NULL;

DROP TYPE IF EXISTS type_duree CASCADE;
CREATE TYPE type_duree AS ENUM ( '1', '2', '3', '4', '5', '6', '7', '8' );

SELECT alter_table_drop_column_if_exists( 'public', 'traitementspdos', 'dureedepart' );
ALTER TABLE traitementspdos ADD COLUMN dureedepart type_duree DEFAULT NULL;

SELECT alter_table_drop_column_if_exists( 'public', 'traitementspdos', 'dureeecheance' );
ALTER TABLE traitementspdos ADD COLUMN dureeecheance type_duree DEFAULT NULL;


DROP TYPE IF EXISTS type_autreavisradiation CASCADE;
CREATE TYPE type_autreavisradiation AS ENUM ( 'END', 'RDC', 'MOA' );
SELECT alter_table_drop_column_if_exists( 'public', 'contratsinsertion', 'autreavisradiation' );
ALTER TABLE contratsinsertion ADD COLUMN autreavisradiation type_autreavisradiation DEFAULT NULL;

DROP TYPE IF EXISTS type_autreavissuspension CASCADE;
CREATE TYPE type_autreavissuspension AS ENUM ( 'END', 'RDC', 'STE', 'MOA' );
SELECT alter_table_drop_column_if_exists( 'public', 'contratsinsertion', 'autreavissuspension' );
ALTER TABLE contratsinsertion ADD COLUMN autreavissuspension type_autreavissuspension DEFAULT NULL;


DROP TABLE IF EXISTS autresavissuspension;
SELECT alter_table_drop_column_if_exists( 'public', 'autresavissuspension', 'contratinsertion_id' );
SELECT alter_table_drop_column_if_exists( 'public', 'autresavissuspension', 'autreavissuspension' );
CREATE TABLE autresavissuspension (
    id                      SERIAL NOT NULL PRIMARY KEY,
    contratinsertion_id     INTEGER REFERENCES contratsinsertion (id),
    autreavissuspension     type_autreavissuspension DEFAULT NULL
);


DROP INDEX IF EXISTS autresavissuspension_contratinsertion_id_idx;
CREATE INDEX autresavissuspension_contratinsertion_id_idx ON autresavissuspension (contratinsertion_id);

DROP INDEX IF EXISTS autresavissuspension_autreavissuspension_idx;
CREATE INDEX autresavissuspension_autreavissuspension_idx ON autresavissuspension (autreavissuspension);


DROP TABLE IF EXISTS autresavisradiation;
SELECT alter_table_drop_column_if_exists( 'public', 'autresavisradiation', 'contratinsertion_id' );
SELECT alter_table_drop_column_if_exists( 'public', 'autresavisradiation', 'autreavisradiation' );
CREATE TABLE autresavisradiation (
    id                      SERIAL NOT NULL PRIMARY KEY,
    contratinsertion_id     INTEGER REFERENCES contratsinsertion (id),
    autreavisradiation     type_autreavisradiation DEFAULT NULL
);

DROP INDEX IF EXISTS autresavisradiation_contratinsertion_id_idx;
CREATE INDEX autresavisradiation_contratinsertion_id_idx ON autresavisradiation (contratinsertion_id);

DROP INDEX IF EXISTS autresavisradiation_autreavisradiation_idx;
CREATE INDEX autresavisradiation_autreavisradiation_idx ON autresavisradiation (autreavisradiation);


-- -----------------------------------------------------------------------------
-- 20110208
-- -----------------------------------------------------------------------------
SELECT alter_table_drop_column_if_exists( 'public', 'orientsstructs', 'structureorientante_id' );
ALTER TABLE orientsstructs ADD COLUMN structureorientante_id INTEGER REFERENCES structuresreferentes (id) DEFAULT NULL;

SELECT alter_table_drop_column_if_exists( 'public', 'orientsstructs', 'referentorientant_id' );
ALTER TABLE orientsstructs ADD COLUMN referentorientant_id INTEGER REFERENCES referents (id) DEFAULT NULL;

-- -----------------------------------------------------------------------------
-- 20110214 - eps-schema-current.sql
-- -----------------------------------------------------------------------------

-- INFO: http://archives.postgresql.org/pgsql-sql/2005-09/msg00266.php
CREATE OR REPLACE FUNCTION public.add_missing_table_field (text, text, text, text)
returns bool as '
DECLARE
  p_namespace alias for $1;
  p_table     alias for $2;
  p_field     alias for $3;
  p_type      alias for $4;
  v_row       record;
  v_query     text;
BEGIN
  select 1 into v_row from pg_namespace n, pg_class c, pg_attribute a
     where
         --public.slon_quote_brute(n.nspname) = p_namespace and
         n.nspname = p_namespace and
         c.relnamespace = n.oid and
         --public.slon_quote_brute(c.relname) = p_table and
         c.relname = p_table and
         a.attrelid = c.oid and
         --public.slon_quote_brute(a.attname) = p_field;
         a.attname = p_field;
  if not found then
    raise notice ''Upgrade table %.% - add field %'', p_namespace, p_table, p_field;
    v_query := ''alter table '' || p_namespace || ''.'' || p_table || '' add column '';
    v_query := v_query || p_field || '' '' || p_type || '';'';
    execute v_query;
    return ''t'';
  else
    return ''f'';
  end if;
END;' language plpgsql;

COMMENT ON FUNCTION public.add_missing_table_field (text, text, text, text) IS 'Add a column of a given type to a table if it is missing';

-- *****************************************************************************

-- Anciennes tables
DROP TABLE IF EXISTS parcoursdetectes CASCADE;

-- Nouvelles tables
DROP TABLE IF EXISTS decisionsdefautsinsertionseps66 CASCADE;
DROP TABLE IF EXISTS defautsinsertionseps66 CASCADE;
DROP TABLE IF EXISTS nvsrsepsreorient66 CASCADE;
DROP TABLE IF EXISTS nvsrsepsreorientsrs93 CASCADE;
DROP TABLE IF EXISTS saisinesepssignalementsnrscers93 CASCADE;
DROP TABLE IF EXISTS relancesdetectionscontrats93 CASCADE;
DROP TABLE IF EXISTS avissrmreps93 CASCADE;
DROP TABLE IF EXISTS saisineseps66 CASCADE;
DROP TABLE IF EXISTS bilansparcours66 CASCADE;
DROP TABLE IF EXISTS nvsrsepsreorientsrs93 CASCADE;
DROP TABLE IF EXISTS saisinesepsreorientsrs93 CASCADE;
DROP TABLE IF EXISTS saisinesepsbilansparcours66 CASCADE;
DROP TABLE IF EXISTS maintiensreorientseps CASCADE;
DROP TABLE IF EXISTS dossierseps CASCADE;
DROP TABLE IF EXISTS eps_zonesgeographiques CASCADE;
DROP TABLE IF EXISTS eps_membreseps CASCADE;
DROP TABLE IF EXISTS membreseps CASCADE;
DROP TABLE IF EXISTS fonctionsmembreseps CASCADE;
DROP TABLE IF EXISTS seanceseps CASCADE;
DROP TABLE IF EXISTS eps CASCADE;
DROP TABLE IF EXISTS regroupementseps CASCADE;
DROP TABLE IF EXISTS motifsreorients CASCADE;
DROP TABLE IF EXISTS saisinesepdspdos66 CASCADE;
DROP TABLE IF EXISTS nvsepdspdos66 CASCADE;
DROP TABLE IF EXISTS nonrespectssanctionseps93 CASCADE;
DROP TABLE IF EXISTS relancesnonrespectssanctionseps93 CASCADE;
DROP TABLE IF EXISTS decisionsnonrespectssanctionseps93 CASCADE;
DROP TABLE IF EXISTS nonorientationspros58 CASCADE;
DROP TABLE IF EXISTS decisionsnonorientationspros58 CASCADE;
DROP TABLE IF EXISTS nonorientationspros66 CASCADE;
DROP TABLE IF EXISTS decisionsnonorientationspros66 CASCADE;
DROP TABLE IF EXISTS nonorientationspros93 CASCADE;
DROP TABLE IF EXISTS decisionsnonorientationspros93 CASCADE;

DROP TYPE IF EXISTS TYPE_THEMEEP CASCADE;
DROP TYPE IF EXISTS TYPE_DECISIONEP CASCADE;
DROP TYPE IF EXISTS TYPE_ETAPEDECISIONEP CASCADE;
DROP TYPE IF EXISTS TYPE_NIVEAUDECISIONEP CASCADE;
DROP TYPE IF EXISTS TYPE_QUAL CASCADE;
DROP TYPE IF EXISTS TYPE_ETAPEDOSSIEREP CASCADE;
DROP TYPE IF EXISTS TYPE_TYPEREORIENTATION66 CASCADE;
DROP TYPE IF EXISTS TYPE_ETAPERELANCECONTENTIEUSE CASCADE;
DROP TYPE IF EXISTS TYPE_TYPERELANCECONTENTIEUSE CASCADE;
DROP TYPE IF EXISTS type_orgpayeur CASCADE;
DROP TYPE IF EXISTS type_dateactive CASCADE;
DROP TYPE IF EXISTS TYPE_DECISIONSANCTIONEP93 CASCADE;
DROP TYPE IF EXISTS TYPE_ORIGINESANCTIONEP93 CASCADE;
DROP TYPE IF EXISTS TYPE_ORIGINEDEFAULTINSERTIONEP66 CASCADE;
DROP TYPE IF EXISTS TYPE_DECISIONDEFAUTEP66 CASCADE;
DROP TYPE IF EXISTS TYPE_PROPOSITIONBILANPARCOURS CASCADE;
DROP TYPE IF EXISTS TYPE_SITFAMBILANPARCOURS CASCADE;
DROP TYPE IF EXISTS TYPE_DECISIONNONORIENTATIONPRO58 CASCADE;
DROP TYPE IF EXISTS TYPE_DECISIONNONORIENTATIONPRO66 CASCADE;
DROP TYPE IF EXISTS TYPE_DECISIONNONORIENTATIONPRO93 CASCADE;

-- -----------------------------------------------------------------------------

CREATE TABLE regroupementseps (
	id      SERIAL NOT NULL PRIMARY KEY,
	name	VARCHAR(255) NOT NULL
);

CREATE UNIQUE INDEX regroupementseps_name_idx ON regroupementseps(name);
ALTER TABLE regroupementseps OWNER TO webrsa;
-- -----------------------------------------------------------------------------

CREATE TYPE TYPE_NIVEAUDECISIONEP AS ENUM ( 'nontraite', 'ep', 'cg' );

CREATE TABLE eps (
	id      					SERIAL NOT NULL PRIMARY KEY,
	name						VARCHAR(255) NOT NULL,
	identifiant					VARCHAR(255) NOT NULL,
	regroupementep_id			INTEGER NOT NULL REFERENCES regroupementseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	-- CG 93
	defautinsertionep66			TYPE_NIVEAUDECISIONEP NOT NULL DEFAULT 'nontraite',
	saisineepbilanparcours66	TYPE_NIVEAUDECISIONEP NOT NULL DEFAULT 'nontraite',
	saisineepdpdo66				TYPE_NIVEAUDECISIONEP NOT NULL DEFAULT 'nontraite',
	-- CG 66
	nonrespectsanctionep93		TYPE_NIVEAUDECISIONEP NOT NULL DEFAULT 'nontraite',
	saisineepreorientsr93		TYPE_NIVEAUDECISIONEP NOT NULL DEFAULT 'nontraite',
	-- CG 58
	nonorientationpro58		TYPE_NIVEAUDECISIONEP NOT NULL DEFAULT 'nontraite'
);

CREATE UNIQUE INDEX eps_name_idx ON eps(name);
CREATE INDEX eps_regroupementep_id_idx ON eps(regroupementep_id);
ALTER TABLE eps OWNER TO webrsa;
-- -----------------------------------------------------------------------------

CREATE TABLE fonctionsmembreseps (
	id      SERIAL NOT NULL PRIMARY KEY,
	name	VARCHAR(255) NOT NULL
);

CREATE UNIQUE INDEX fonctionsmembreseps_name_idx ON fonctionsmembreseps(name);
ALTER TABLE fonctionsmembreseps OWNER TO webrsa;
-- -----------------------------------------------------------------------------

CREATE TYPE TYPE_QUAL AS ENUM ( 'M.', 'Mlle.', 'Mme.' );

CREATE TABLE membreseps (
	id      			SERIAL NOT NULL PRIMARY KEY,
	fonctionmembreep_id	INTEGER NOT NULL REFERENCES fonctionsmembreseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	qual				TYPE_QUAL NOT NULL,
	nom					VARCHAR(255) NOT NULL,
	prenom				VARCHAR(255) NOT NULL,
	tel					VARCHAR(10),
	mail				VARCHAR(50),
	suppleant_id		INTEGER REFERENCES membreseps (id) ON DELETE SET NULL
);

CREATE INDEX membreseps_fonctionmembreep_id_idx ON membreseps(fonctionmembreep_id);
ALTER TABLE membreseps OWNER TO webrsa;
-- -----------------------------------------------------------------------------

CREATE TABLE eps_membreseps (
	id      			SERIAL NOT NULL PRIMARY KEY,
	ep_id				INTEGER NOT NULL REFERENCES eps(id),
	membreep_id			INTEGER NOT NULL REFERENCES membreseps(id)
);
-- -----------------------------------------------------------------------------

CREATE TABLE eps_zonesgeographiques (
	id      			SERIAL NOT NULL PRIMARY KEY,
	ep_id				INTEGER NOT NULL REFERENCES eps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	zonegeographique_id	INTEGER NOT NULL REFERENCES zonesgeographiques(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE UNIQUE INDEX eps_zonesgeographiques_unique_idx ON eps_zonesgeographiques(ep_id,zonegeographique_id);
CREATE INDEX eps_zonesgeographiques_ep_id_idx ON eps_zonesgeographiques(ep_id);
CREATE INDEX eps_zonesgeographiques_zonegeographique_id_idx ON eps_zonesgeographiques(zonegeographique_id);
ALTER TABLE eps_zonesgeographiques OWNER TO webrsa;
-- -----------------------------------------------------------------------------

CREATE TYPE TYPE_ETAPEDECISIONEP AS ENUM ( 'ep', 'cg' );

CREATE TABLE seanceseps (
	id      				SERIAL NOT NULL PRIMARY KEY,
	identifiant				VARCHAR(255) NOT NULL,
	name					VARCHAR(255) NOT NULL,
	ep_id					INTEGER NOT NULL REFERENCES eps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	structurereferente_id	INTEGER REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	dateseance				TIMESTAMP WITHOUT TIME ZONE NOT NULL,
	salle					VARCHAR(255),
	observations			VARCHAR(255),
	finalisee				TYPE_ETAPEDECISIONEP DEFAULT NULL
);

CREATE INDEX seanceseps_ep_id_idx ON seanceseps(ep_id);
CREATE INDEX seanceseps_structurereferente_id_idx ON seanceseps(structurereferente_id);
ALTER TABLE seanceseps OWNER TO webrsa;

-- -----------------------------------------------------------------------------

CREATE TYPE TYPE_THEMEEP AS ENUM ( 'saisinesepsreorientsrs93', 'saisinesepsbilansparcours66', /*'suspensionsreductionsallocations93',*/ 'saisinesepdspdos66', 'nonrespectssanctionseps93', 'defautsinsertionseps66', 'nonorientationspros58' );
CREATE TYPE TYPE_ETAPEDOSSIEREP AS ENUM ( 'cree', '...', 'seance', 'decisionep', 'decisioncg', 'traite' );

CREATE TABLE dossierseps (
	id      			SERIAL NOT NULL PRIMARY KEY,
	personne_id			INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	seanceep_id			INTEGER DEFAULT NULL REFERENCES seanceseps(id) ON DELETE SET NULL ON UPDATE CASCADE,
	etapedossierep		TYPE_ETAPEDOSSIEREP NOT NULL DEFAULT 'cree',
	themeep				TYPE_THEMEEP NOT NULL,
	-- urgent ?
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);

COMMENT ON TABLE dossierseps IS 'Dossiers de passage en commission d''EPs';

CREATE INDEX dossierseps_personne_id_idx ON dossierseps(personne_id);
CREATE INDEX dossierseps_ep_id_idx ON dossierseps(seanceep_id);
CREATE INDEX dossierseps_etapedossierep_idx ON dossierseps(etapedossierep);
ALTER TABLE dossierseps OWNER TO webrsa;

-- =============================================================================

CREATE TABLE motifsreorients (
	id      SERIAL NOT NULL PRIMARY KEY,
	name	VARCHAR(255) NOT NULL
);

CREATE UNIQUE INDEX motifsreorients_name_idx ON motifsreorients(name);
ALTER TABLE motifsreorients OWNER TO webrsa;

-- -----------------------------------------------------------------------------

CREATE TABLE saisinesepsreorientsrs93 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	dossierep_id			INTEGER DEFAULT NULL REFERENCES dossierseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	orientstruct_id			INTEGER NOT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typeorient_id			INTEGER NOT NULL REFERENCES typesorients(id) ON DELETE SET NULL ON UPDATE CASCADE,
	structurereferente_id	INTEGER NOT NULL REFERENCES structuresreferentes(id) ON DELETE SET NULL ON UPDATE CASCADE,
	datedemande				DATE NOT NULL,
	referent_id				INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE SET NULL ON UPDATE CASCADE,
	motifreorient_id		INTEGER NOT NULL REFERENCES motifsreorients(id) ON DELETE SET NULL ON UPDATE CASCADE,
	commentaire				TEXT DEFAULT NULL,
	accordaccueil			TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0',
	desaccordaccueil		TEXT DEFAULT NULL,
	accordallocataire		TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0',
	urgent					TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0',
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);

COMMENT ON TABLE saisinesepsreorientsrs93 IS 'Saisines d''EPs créées par les structures référentes (CG93)';
ALTER TABLE saisinesepsreorientsrs93 OWNER TO webrsa;

-- -----------------------------------------------------------------------------

CREATE TYPE TYPE_DECISIONEP AS ENUM ( 'accepte', 'refuse' );

CREATE TABLE nvsrsepsreorientsrs93 (
	id      					SERIAL NOT NULL PRIMARY KEY,
	saisineepreorientsr93_id	INTEGER NOT NULL REFERENCES saisinesepsreorientsrs93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etape						TYPE_ETAPEDECISIONEP NOT NULL,
	decision					TYPE_DECISIONEP NOT NULL,
	typeorient_id				INTEGER DEFAULT NULL REFERENCES typesorients(id) ON DELETE SET NULL ON UPDATE CASCADE,
	structurereferente_id		INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON DELETE SET NULL ON UPDATE CASCADE,
	referent_id					INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE SET NULL ON UPDATE CASCADE,
	commentaire					TEXT DEFAULT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);

COMMENT ON TABLE nvsrsepsreorientsrs93 IS 'Décisions des nouvelles structures referentes concernant les saisines d''EPs créées par les structures référentes (CG93)';
CREATE UNIQUE INDEX nvsrsepsreorientsrs93_saisineepreorientsr93_id_etape_unique_idx ON nvsrsepsreorientsrs93(saisineepreorientsr93_id,etape);

-- =============================================================================

CREATE TABLE bilansparcours66 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	referent_id				INTEGER NOT NULL REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
	orientstruct_id			INTEGER NOT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	contratinsertion_id		INTEGER NOT NULL REFERENCES contratsinsertion(id) ON DELETE CASCADE ON UPDATE CASCADE,
	presenceallocataire		TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0',
	situationallocataire	TEXT NOT NULL,
	bilanparcours			TEXT NOT NULL, -- Plus précis ? (diviser en sous-questions)
	infoscomplementaires	TEXT DEFAULT NULL, -- bp "normal"
	preconisationpe			TEXT DEFAULT NULL, -- bp "PE"
	observationsallocataire	TEXT DEFAULT NULL,
	saisineepparcours		TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0',
	maintienorientation		TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0',
	changereferent			TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0',
	ddreconductoncontrat	DATE DEFAULT NULL,
	dfreconductoncontrat	DATE DEFAULT NULL,
	duree_engag				INTEGER DEFAULT NULL,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);
ALTER TABLE bilansparcours66 OWNER TO webrsa;

-- -----------------------------------------------------------------------------

CREATE TABLE saisinesepsbilansparcours66 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	bilanparcours66_id		INTEGER NOT NULL REFERENCES bilansparcours66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	dossierep_id			INTEGER DEFAULT NULL REFERENCES dossierseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typeorient_id			INTEGER DEFAULT NULL REFERENCES typesorients(id) ON DELETE SET NULL ON UPDATE CASCADE,
	structurereferente_id	INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON DELETE SET NULL ON UPDATE CASCADE,
	/*motifreorient_id		INTEGER NOT NULL REFERENCES motifsreorients(id) ON DELETE SET NULL ON UPDATE CASCADE,
	commentaire				TEXT DEFAULT NULL,
	accordaccueil			TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0',
	desaccordaccueil		TEXT DEFAULT NULL,
	accordallocataire		TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0',
	urgent					TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0',*/
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);

COMMENT ON TABLE saisinesepsbilansparcours66 IS 'Saisines d''EPs créées lors du bilan de parcours (CG66)';


-- -----------------------------------------------------------------------------

CREATE TABLE nvsrsepsreorient66 (
	id      					SERIAL NOT NULL PRIMARY KEY,
	saisineepbilanparcours66_id	INTEGER NOT NULL REFERENCES saisinesepsbilansparcours66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etape						TYPE_ETAPEDECISIONEP NOT NULL,
	decision					TYPE_DECISIONEP,
	typeorient_id				INTEGER DEFAULT NULL REFERENCES typesorients(id) ON DELETE SET NULL ON UPDATE CASCADE,
	structurereferente_id		INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON DELETE SET NULL ON UPDATE CASCADE,
	commentaire					TEXT DEFAULT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);

COMMENT ON TABLE nvsrsepsreorient66 IS 'Décisions des nouvelles structures referentes concernant les saisines d''EPs suite au bilan de parcours (CG66)';
CREATE UNIQUE INDEX nvsrsepsreorient66_saisineepbilanparcours66_id_etape_unique_idx ON nvsrsepsreorient66(saisineepbilanparcours66_id,etape);

-- *****************************************************************************

SELECT add_missing_table_field ('public', 'propospdos', 'serviceinstructeur_id', 'integer');
ALTER TABLE propospdos ADD FOREIGN KEY (serviceinstructeur_id) REFERENCES servicesinstructeurs (id);

DROP INDEX IF EXISTS propospdos_serviceinstructeur_id_idx;
CREATE INDEX propospdos_serviceinstructeur_id_idx ON propospdos (serviceinstructeur_id);

SELECT add_missing_table_field ('public', 'propospdos', 'created', 'TIMESTAMP WITHOUT TIME ZONE');
SELECT add_missing_table_field ('public', 'propospdos', 'modified', 'TIMESTAMP WITHOUT TIME ZONE');

CREATE TYPE type_orgpayeur AS ENUM ( 'CAF', 'MSA' );
SELECT add_missing_table_field ('public', 'propospdos', 'orgpayeur', 'type_orgpayeur');

CREATE TYPE type_dateactive AS ENUM ( 'datedepart', 'datereception' );
SELECT add_missing_table_field ('public', 'descriptionspdos', 'dateactive', 'type_dateactive');
UPDATE descriptionspdos SET dateactive = 'datedepart' WHERE dateactive IS NULL;
ALTER TABLE descriptionspdos ALTER COLUMN dateactive SET DEFAULT 'datedepart';
ALTER TABLE descriptionspdos ALTER COLUMN dateactive SET NOT NULL;
SELECT add_missing_table_field ('public', 'descriptionspdos', 'declencheep', 'type_booleannumber');
UPDATE descriptionspdos SET declencheep = '0' WHERE declencheep IS NULL;
ALTER TABLE descriptionspdos ALTER COLUMN declencheep SET DEFAULT '0';
ALTER TABLE descriptionspdos ALTER COLUMN declencheep SET NOT NULL;

SELECT add_missing_table_field ('public', 'traitementspdos', 'dateecheance', 'DATE');
SELECT add_missing_table_field ('public', 'traitementspdos', 'daterevision', 'DATE');
SELECT add_missing_table_field ('public', 'traitementspdos', 'personne_id', 'integer');
ALTER TABLE propospdos ADD FOREIGN KEY (personne_id) REFERENCES personnes (id);

DROP INDEX IF EXISTS traitementspdos_personne_id_idx;
CREATE INDEX traitementspdos_personne_id_idx ON traitementspdos (personne_id);

SELECT add_missing_table_field ('public', 'traitementspdos', 'ficheanalyse', 'TEXT');
SELECT add_missing_table_field ('public', 'traitementspdos', 'clos', 'INTEGER');
UPDATE traitementspdos SET clos = '0' WHERE clos IS NULL;
ALTER TABLE traitementspdos ALTER COLUMN clos SET DEFAULT '0';
ALTER TABLE traitementspdos ALTER COLUMN clos SET NOT NULL;

CREATE TABLE saisinesepdspdos66 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	dossierep_id			INTEGER DEFAULT NULL REFERENCES dossierseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	traitementpdo_id		INTEGER NOT NULL REFERENCES traitementspdos (id),
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);

CREATE TABLE nvsepdspdos66 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	saisineepdpdo66_id		INTEGER NOT NULL REFERENCES saisinesepdspdos66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etape					TYPE_ETAPEDECISIONEP NOT NULL,
	decisionpdo_id			INTEGER REFERENCES decisionspdos (id),
	commentaire				TEXT DEFAULT NULL,
	nonadmis				type_nonadmis,
	motifpdo				VARCHAR(1),
	datedecisionpdo			DATE,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);

CREATE UNIQUE INDEX nvsepdspdos66_saisineepdpdo66_id_etape_unique_idx ON nvsepdspdos66(saisineepdpdo66_id,etape);

-- *****************************************************************************
-- Modification pour reprendre l'ancien bilan de parcours du 66
-- *****************************************************************************

SELECT add_missing_table_field ('public', 'bilansparcours66', 'accordprojet', 'type_booleannumber');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'maintienorientsansep', 'type_orient');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'choixparcours', 'type_choixparcours');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'changementrefsansep', 'type_no');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'maintienorientparcours', 'type_orient');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'changementrefparcours', 'type_no');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'reorientation', 'type_reorientation');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'examenaudition', 'type_type_demande');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'maintienorientavisep', 'type_orient');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'changementrefeplocale', 'type_no');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'reorientationeplocale', 'type_reorientation');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'typeeplocale', 'type_typeeplocale');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'decisioncommission', 'type_aviscommission');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'decisioncoordonnateur', 'type_aviscoordonnateur');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'decisioncga', 'type_aviscoordonnateur');

SELECT add_missing_table_field ('public', 'bilansparcours66', 'datebilan', 'DATE');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'observbenef', 'TEXT');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'objinit', 'TEXT');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'objatteint', 'TEXT');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'objnew', 'TEXT');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'motifsaisine', 'TEXT');

ALTER TABLE bilansparcours66 ALTER COLUMN situationallocataire DROP NOT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN bilanparcours DROP NOT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN contratinsertion_id DROP NOT NULL;

CREATE TYPE TYPE_SITFAMBILANPARCOURS AS ENUM ( 'couple', 'coupleenfant', 'isole', 'isoleenfant' );
SELECT add_missing_table_field ('public', 'bilansparcours66', 'sitfam', 'TYPE_SITFAMBILANPARCOURS');

SELECT add_missing_table_field ('public', 'bilansparcours66', 'observbenefrealisationbilan', 'TEXT');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'observbenefcompterendu', 'TEXT');

CREATE TYPE TYPE_PROPOSITIONBILANPARCOURS AS ENUM ( 'audition', 'parcours', 'traitement' );
SELECT add_missing_table_field ('public', 'bilansparcours66', 'proposition', 'TYPE_PROPOSITIONBILANPARCOURS');
ALTER TABLE bilansparcours66 ALTER COLUMN proposition SET NOT NULL;

-- -----------------------------------------------------------------------------
-- Développement Thierry :
-- -----------------------------------------------------------------------------

-- Liste des participants aux séances EPs.

-- Suppresion de tables et des types
DROP TABLE IF EXISTS membreseps_seanceseps CASCADE;
DROP TYPE IF EXISTS type_reponseseanceep CASCADE;
DROP TYPE IF EXISTS type_presenceseanceep CASCADE;

-- -----------------------------------------------------------------------------
-- Création des types :
CREATE TYPE type_reponseseanceep AS ENUM ( 'confirme', 'decline', 'nonrenseigne', 'remplacepar' );
CREATE TYPE type_presenceseanceep AS ENUM ( 'present', 'excuse', 'remplacepar' );

CREATE TABLE membreseps_seanceseps
(
  id serial NOT NULL,
  seanceep_id integer NOT NULL,
  membreep_id integer NOT NULL,
  suppleant type_booleannumber DEFAULT '0' NOT NULL,
  suppleant_id integer DEFAULT NULL,
  reponse type_reponseseanceep NOT NULL DEFAULT 'nonrenseigne',
  presence type_presenceseanceep DEFAULT NULL,
  CONSTRAINT membreseps_seanceseps_pkey PRIMARY KEY (id),
  CONSTRAINT membreseps_seanceseps_seanceep_id_fkey FOREIGN KEY (seanceep_id)
      REFERENCES seanceseps (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT membreseps_seanceseps_membreep_id_fkey FOREIGN KEY (membreep_id)
      REFERENCES membreseps (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE membreseps_seanceseps OWNER TO webrsa;

COMMENT ON COLUMN membreseps_seanceseps.suppleant IS 'booléen pour désigner si le membre est un suppléant';
COMMENT ON COLUMN membreseps_seanceseps.suppleant_id IS 'clé étrangère sur la table membresepsseanceseps';
COMMENT ON COLUMN membreseps_seanceseps.reponse IS 'À confirmé, À décliné, Non renseigné';
COMMENT ON COLUMN membreseps_seanceseps.presence IS 'Présent, Excusé, Remplacé par <suppléant>';


-- Index: membreseps_seanceseps_seanceep_id_idx

-- DROP INDEX membreseps_seanceseps_seanceep_id_idx;

CREATE INDEX membreseps_seanceseps_seanceep_id_idx
  ON membreseps_seanceseps
  USING btree
  (seanceep_id);


-- Index: membreseps_seanceseps_membresep_id_idx

-- DROP INDEX membreseps_seanceseps_membresep_id_idx;

CREATE INDEX membreseps_seanceseps_membresep_id_idx
  ON membreseps_seanceseps
  USING btree
  (membreep_id);

-- *****************************************************************************
-- Non respect / sanctions 93
-- *****************************************************************************

CREATE TYPE TYPE_DECISIONSANCTIONEP93 AS ENUM ( '1reduction', '1maintien', '1sursis', '2suspensiontotale', '2suspensionpartielle', '2maintien' );
CREATE TYPE TYPE_ORIGINESANCTIONEP93 AS ENUM ( 'orientstruct', 'contratinsertion', 'pdo' );

CREATE TABLE nonrespectssanctionseps93 (
	id						SERIAL NOT NULL PRIMARY KEY,
	dossierep_id			INTEGER DEFAULT NULL REFERENCES dossierseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	propopdo_id				INTEGER DEFAULT NULL REFERENCES propospdos(id) ON DELETE CASCADE ON UPDATE CASCADE,
	orientstruct_id			INTEGER DEFAULT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	contratinsertion_id		INTEGER DEFAULT NULL REFERENCES contratsinsertion(id) ON DELETE CASCADE ON UPDATE CASCADE,
	origine					TYPE_ORIGINESANCTIONEP93 NOT NULL,
	decision				TYPE_DECISIONSANCTIONEP93 DEFAULT NULL,
	rgpassage				INTEGER NOT NULL,
	montantreduction		FLOAT DEFAULT NULL,
	dureesursis				INTEGER DEFAULT NULL,
	sortienvcontrat			TYPE_BOOLEANNUMBER NOT NULL DEFAULT '0',
	active					TYPE_BOOLEANNUMBER NOT NULL DEFAULT '1',
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);

CREATE INDEX nonrespectssanctionseps93_dossierep_id_idx
	ON nonrespectssanctionseps93
	(dossierep_id);

CREATE INDEX nonrespectssanctionseps93_propopdo_id_idx
	ON nonrespectssanctionseps93
	(propopdo_id);

CREATE INDEX nonrespectssanctionseps93_orientstruct_id_idx
	ON nonrespectssanctionseps93
	(orientstruct_id);

CREATE INDEX nonrespectssanctionseps93_contratinsertion_id_idx
	ON nonrespectssanctionseps93
	(contratinsertion_id);

CREATE INDEX nonrespectssanctionseps93_active_idx
	ON nonrespectssanctionseps93
	(active);

--DROP CONSTRAINT nonrespectssanctionseps_valid_entry_chk;
ALTER TABLE nonrespectssanctionseps93 ADD CONSTRAINT nonrespectssanctionseps93_valid_entry_chk CHECK (
	( propopdo_id IS NOT NULL ) OR ( orientstruct_id IS NOT NULL ) OR ( contratinsertion_id IS NOT NULL )
);

CREATE TABLE relancesnonrespectssanctionseps93 (
	id      					SERIAL NOT NULL PRIMARY KEY,
	nonrespectsanctionep93_id	INTEGER NOT NULL REFERENCES nonrespectssanctionseps93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	numrelance					INTEGER NOT NULL DEFAULT 1,
	dateimpression				DATE DEFAULT NULL,
	daterelance					DATE NOT NULL
);

CREATE INDEX relancesnonrespectssanctionseps93_nonrespectsanctionep93_id_idx
	ON relancesnonrespectssanctionseps93
	(nonrespectsanctionep93_id);

CREATE INDEX relancesnonrespectssanctionseps93_numrelance_idx
	ON relancesnonrespectssanctionseps93
	(numrelance);

CREATE UNIQUE INDEX relancesnonrespectssanctionseps93_nonrespectsanctionep93_id_numrelance_idx
	ON relancesnonrespectssanctionseps93
	(nonrespectsanctionep93_id, numrelance);

-- Import des relances de la table orientsstructs dans les tables ...
-- Population ... en cours
INSERT INTO nonrespectssanctionseps93 ( orientstruct_id, origine, active, rgpassage, created, modified )
	SELECT
			orientsstructs.id AS orientstruct_id,
			'orientstruct' AS origine,
			CAST( CASE
				WHEN (
					contratsinsertion.datevalidation_ci >= orientsstructs.daterelance
					OR contratsinsertion.dd_ci >= orientsstructs.daterelance
				) THEN '0'
				ELSE '1'
				END
				AS TYPE_BOOLEANNUMBER
			) AS active,
			1 AS rgpassage,
			orientsstructs.daterelance,
			orientsstructs.daterelance
-- 			orientsstructs.date_impression_relance,
		FROM orientsstructs
			LEFT OUTER JOIN contratsinsertion ON (
				orientsstructs.personne_id = contratsinsertion.personne_id
				AND contratsinsertion.id IN (
					SELECT "tmpcontratsinsertion"."id" FROM (
						SELECT
								"contratsinsertion"."id" AS id,
								"contratsinsertion"."personne_id"
							FROM contratsinsertion
							WHERE
								"contratsinsertion"."personne_id" = orientsstructs.personne_id
							ORDER BY "contratsinsertion"."dd_ci" DESC
							LIMIT 1
					) AS tmpcontratsinsertion
				)
			)
		WHERE
			orientsstructs.daterelance IS NOT NULL
			AND orientsstructs.statutrelance = 'R';

INSERT INTO relancesnonrespectssanctionseps93 ( nonrespectsanctionep93_id, numrelance, dateimpression, daterelance )
	SELECT
			nonrespectssanctionseps93.id AS nonrespectsanctionep93_id,
			1 AS numrelance,
			orientsstructs.date_impression_relance AS dateimpression,
			orientsstructs.daterelance AS daterelance
		FROM orientsstructs
			INNER JOIN nonrespectssanctionseps93 ON (
				nonrespectssanctionseps93.orientstruct_id = orientsstructs.id
				AND nonrespectssanctionseps93.active = '1'
			)
			LEFT OUTER JOIN contratsinsertion ON (
				orientsstructs.personne_id = contratsinsertion.personne_id
				AND contratsinsertion.id IN (
					SELECT "tmpcontratsinsertion"."id" FROM (
						SELECT
								"contratsinsertion"."id" AS id,
								"contratsinsertion"."personne_id"
							FROM contratsinsertion
							WHERE
								"contratsinsertion"."personne_id" = orientsstructs.personne_id
							ORDER BY "contratsinsertion"."dd_ci" DESC
							LIMIT 1
					) AS tmpcontratsinsertion
				)
			)
		WHERE
			orientsstructs.daterelance IS NOT NULL
			AND orientsstructs.statutrelance = 'R';

-- Avis et décisions ep/cg pour le thème non respect / sanctions (CG 93)
CREATE TABLE decisionsnonrespectssanctionseps93 (
	id      					SERIAL NOT NULL PRIMARY KEY,
	nonrespectsanctionep93_id	INTEGER NOT NULL REFERENCES nonrespectssanctionseps93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etape						TYPE_ETAPEDECISIONEP NOT NULL,
	decision					TYPE_DECISIONSANCTIONEP93 DEFAULT NULL,
	montantreduction			FLOAT DEFAULT NULL,
	dureesursis					INTEGER DEFAULT NULL,
	commentaire					TEXT DEFAULT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);

COMMENT ON TABLE decisionsnonrespectssanctionseps93 IS 'Avis et décisions ep/cg pour le thème non respect / sanctions (CG 93)';
CREATE UNIQUE INDEX decisionsnonrespectssanctionseps93_nonrespectsanctionep93_id_etape_unique_idx ON decisionsnonrespectssanctionseps93(nonrespectsanctionep93_id,etape);

-- *****************************************************************************
-- Thématique défaut d'insertion (CG 66)
-- *****************************************************************************

CREATE TYPE TYPE_ORIGINEDEFAULTINSERTIONEP66 AS ENUM ( 'bilanparcours', 'noninscriptionpe', 'radiationpe' );
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
COMMENT ON TABLE defautsinsertionseps66 IS 'Saisines d''EPs créées lors du bilan de parcours ou suite aux flux Pôle Emploi pour la thématique Défaut d''insertion (CG66)';

-- -----------------------------------------------------------------------------

/*
	Suspension suite à défaut de conclusion
	Suspension suite à non respect du contrat
	Maintien du versement de l'allocation
	Réorientation du PROFESSIONNEL vers le SOCIAL
	Réorientation du SOCIAL vers le PROFESSIONNEL
*/
CREATE TYPE TYPE_DECISIONDEFAUTEP66 AS ENUM ( 'suspensionnonrespect', 'suspensiondefaut', 'maintien', 'reorientationprofverssoc', 'reorientationsocversprof' );

CREATE TABLE decisionsdefautsinsertionseps66 (
	id      					SERIAL NOT NULL PRIMARY KEY,
	defautinsertionep66_id		INTEGER NOT NULL REFERENCES defautsinsertionseps66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typeorient_id				INTEGER DEFAULT NULL REFERENCES typesorients(id) ON UPDATE CASCADE ON DELETE SET NULL,
	structurereferente_id		INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON UPDATE CASCADE ON DELETE SET NULL,
	etape						TYPE_ETAPEDECISIONEP NOT NULL,
	decision					TYPE_DECISIONDEFAUTEP66 NOT NULL,
	commentaire					TEXT DEFAULT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);

CREATE UNIQUE INDEX decisionsdefautsinsertionseps66_defautinsertionep66_id_etape_unique_idx ON decisionsdefautsinsertionseps66(defautinsertionep66_id,etape);

-- *****************************************************************************
-- Ajout dans le bilansparcours66
-- *****************************************************************************

DROP TYPE IF EXISTS TYPE_ACCOMPAGNEMENT CASCADE;
CREATE TYPE TYPE_ACCOMPAGNEMENT AS ENUM ( 'prepro', 'social' );

SELECT add_missing_table_field ('public', 'bilansparcours66', 'accompagnement', 'TYPE_ACCOMPAGNEMENT');

DROP TYPE IF EXISTS TYPE_TYPEFORMULAIRE CASCADE;
CREATE TYPE TYPE_TYPEFORMULAIRE AS ENUM ( 'cg', 'pe' );

SELECT add_missing_table_field ('public', 'bilansparcours66', 'typeformulaire', 'TYPE_TYPEFORMULAIRE');
SELECT add_missing_table_field ('public', 'bilansparcours66', 'textbilanparcours', 'TEXT');

DROP INDEX IF EXISTS bilansparcours66_contratinsertion_id_idx;
CREATE INDEX bilansparcours66_contratinsertion_id_idx ON bilansparcours66 (contratinsertion_id);

DROP INDEX IF EXISTS bilansparcours66_orientstruct_id_idx;
CREATE INDEX bilansparcours66_orientstruct_id_idx ON bilansparcours66 (orientstruct_id);

DROP INDEX IF EXISTS bilansparcours66_referent_id_idx;
CREATE INDEX bilansparcours66_referent_id_idx ON bilansparcours66 (referent_id);

-- -----------------------------------------------------------------------------
-- 20110204
-- -----------------------------------------------------------------------------

SELECT alter_table_drop_column_if_exists( 'public', 'bilansparcours66', 'situationperso' );
ALTER TABLE bilansparcours66 ADD COLUMN situationperso TEXT DEFAULT NULL;

SELECT alter_table_drop_column_if_exists( 'public', 'bilansparcours66', 'situationpro' );
ALTER TABLE bilansparcours66 ADD COLUMN situationpro TEXT DEFAULT NULL;

SELECT alter_table_drop_column_if_exists( 'public', 'bilansparcours66', 'bilanparcoursinsertion' );
ALTER TABLE bilansparcours66 ADD COLUMN bilanparcoursinsertion type_booleannumber DEFAULT NULL;

SELECT alter_table_drop_column_if_exists( 'public', 'bilansparcours66', 'motifep' );
ALTER TABLE bilansparcours66 ADD COLUMN motifep type_booleannumber DEFAULT NULL;

-- -----------------------------------------------------------------------------
-- Ajout de la thématique des non-orientations pro pour les cg 58 66 et 93
-- -----------------------------------------------------------------------------

CREATE TABLE nonorientationspros58 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	dossierep_id			INTEGER NOT NULL REFERENCES dossierseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	orientstruct_id			INTEGER DEFAULT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE nonorientationspros58 IS 'Saisines d''EPs créées lors d''une non orientation du social vers le professionel dans une délai de 6, 12 ou 24 mois (CG58)';

DROP INDEX IF EXISTS nonorientationspros58_dossierep_id_idx;
CREATE INDEX nonorientationspros58_dossierep_id_idx ON nonorientationspros58 (dossierep_id);

DROP INDEX IF EXISTS nonorientationspros58_orientstruct_id_idx;
CREATE INDEX nonorientationspros58_orientstruct_id_idx ON nonorientationspros58 (orientstruct_id);

CREATE TYPE TYPE_DECISIONNONORIENTATIONPRO58 AS ENUM ( 'reorientation', 'maintienref' );
COMMENT ON TYPE TYPE_DECISIONNONORIENTATIONPRO58 IS 'Type de décision pour la non orientation professionnelle dans les délais (CG58)';

CREATE TABLE decisionsnonorientationspros58 (
	id      					SERIAL NOT NULL PRIMARY KEY,
	etape						TYPE_ETAPEDECISIONEP NOT NULL,
	decision					TYPE_DECISIONNONORIENTATIONPRO58 DEFAULT NULL,
	nonorientationpro58_id		INTEGER NOT NULL REFERENCES nonorientationspros58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typeorient_id				INTEGER DEFAULT NULL REFERENCES typesorients(id) ON UPDATE CASCADE ON DELETE SET NULL,
	structurereferente_id		INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON UPDATE CASCADE ON DELETE SET NULL,
	commentaire					TEXT DEFAULT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE decisionsnonorientationspros58 IS 'Décisions de la saisine d''EP lors d''une non orientation du social vers le professionel dans une délai de 6, 12 ou 24 mois (CG58)';

DROP INDEX IF EXISTS decisionsnonorientationspros58_nonorientationpro58_id_etape_unique_idx;
CREATE UNIQUE INDEX decisionsnonorientationspros58_nonorientationpro58_id_etape_unique_idx ON decisionsnonorientationspros58(nonorientationpro58_id,etape);

DROP INDEX IF EXISTS decisionsnonorientationspros58_nonorientationpro58_id_idx;
CREATE INDEX decisionsnonorientationspros58_nonorientationpro58_id_idx ON decisionsnonorientationspros58 (nonorientationpro58_id);

DROP INDEX IF EXISTS decisionsnonorientationspros58_typeorient_id_idx;
CREATE INDEX decisionsnonorientationspros58_typeorient_id_idx ON decisionsnonorientationspros58 (typeorient_id);

DROP INDEX IF EXISTS decisionsnonorientationspros58_structurereferente_id_idx;
CREATE INDEX decisionsnonorientationspros58_structurereferente_id_idx ON decisionsnonorientationspros58 (structurereferente_id);

-- -----------------------------------------------------------------------------

CREATE TABLE nonorientationspros66 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	dossierep_id			INTEGER NOT NULL REFERENCES dossierseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	orientstruct_id			INTEGER DEFAULT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE nonorientationspros66 IS 'Saisines d''EPs créées lors d''une non orientation du social vers le professionel dans une délai de 6, 12 ou 24 mois (CG66)';

DROP INDEX IF EXISTS nonorientationspros66_dossierep_id_idx;
CREATE INDEX nonorientationspros66_dossierep_id_idx ON nonorientationspros66 (dossierep_id);

DROP INDEX IF EXISTS nonorientationspros66_orientstruct_id_idx;
CREATE INDEX nonorientationspros66_orientstruct_id_idx ON nonorientationspros66 (orientstruct_id);

CREATE TYPE TYPE_DECISIONNONORIENTATIONPRO66 AS ENUM ( 'reorientation', 'maintienref' );
COMMENT ON TYPE TYPE_DECISIONNONORIENTATIONPRO66 IS 'Type de décision pour la non orientation professionnelle dans les délais (CG66)';

CREATE TABLE decisionsnonorientationspros66 (
	id      					SERIAL NOT NULL PRIMARY KEY,
	etape						TYPE_ETAPEDECISIONEP NOT NULL,
	decision					TYPE_DECISIONNONORIENTATIONPRO66 DEFAULT NULL,
	nonorientationpro66_id		INTEGER NOT NULL REFERENCES nonorientationspros66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typeorient_id				INTEGER DEFAULT NULL REFERENCES typesorients(id) ON UPDATE CASCADE ON DELETE SET NULL,
	structurereferente_id		INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON UPDATE CASCADE ON DELETE SET NULL,
	commentaire					TEXT DEFAULT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE decisionsnonorientationspros66 IS 'Décisions de la saisine d''EP lors d''une non orientation du social vers le professionel dans une délai de 6, 12 ou 24 mois (CG66)';

DROP INDEX IF EXISTS decisionsnonorientationspros66_nonorientationpro66_id_etape_unique_idx;
CREATE UNIQUE INDEX decisionsnonorientationspros66_nonorientationpro66_id_etape_unique_idx ON decisionsnonorientationspros66(nonorientationpro66_id,etape);

DROP INDEX IF EXISTS decisionsnonorientationspros66_nonorientationpro66_id_idx;
CREATE INDEX decisionsnonorientationspros66_nonorientationpro66_id_idx ON decisionsnonorientationspros66 (nonorientationpro66_id);

DROP INDEX IF EXISTS decisionsnonorientationspros66_typeorient_id_idx;
CREATE INDEX decisionsnonorientationspros66_typeorient_id_idx ON decisionsnonorientationspros66 (typeorient_id);

DROP INDEX IF EXISTS decisionsnonorientationspros66_structurereferente_id_idx;
CREATE INDEX decisionsnonorientationspros66_structurereferente_id_idx ON decisionsnonorientationspros66 (structurereferente_id);

-- -----------------------------------------------------------------------------

CREATE TABLE nonorientationspros93 (
	id      				SERIAL NOT NULL PRIMARY KEY,
	dossierep_id			INTEGER NOT NULL REFERENCES dossierseps(id) ON DELETE CASCADE ON UPDATE CASCADE,
	orientstruct_id			INTEGER DEFAULT NULL REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE nonorientationspros93 IS 'Saisines d''EPs créées lors d''une non orientation du social vers le professionel dans une délai de 6, 12 ou 24 mois (CG93)';

DROP INDEX IF EXISTS nonorientationspros93_dossierep_id_idx;
CREATE INDEX nonorientationspros93_dossierep_id_idx ON nonorientationspros93 (dossierep_id);

DROP INDEX IF EXISTS nonorientationspros93_orientstruct_id_idx;
CREATE INDEX nonorientationspros93_orientstruct_id_idx ON nonorientationspros93 (orientstruct_id);

CREATE TYPE TYPE_DECISIONNONORIENTATIONPRO93 AS ENUM ( 'reorientation', 'maintienref' );
COMMENT ON TYPE TYPE_DECISIONNONORIENTATIONPRO93 IS 'Type de décision pour la non orientation professionnelle dans les délais (CG93)';

CREATE TABLE decisionsnonorientationspros93 (
	id      					SERIAL NOT NULL PRIMARY KEY,
	etape						TYPE_ETAPEDECISIONEP NOT NULL,
	decision					TYPE_DECISIONNONORIENTATIONPRO93 DEFAULT NULL,
	nonorientationpro93_id		INTEGER NOT NULL REFERENCES nonorientationspros93(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typeorient_id				INTEGER DEFAULT NULL REFERENCES typesorients(id) ON UPDATE CASCADE ON DELETE SET NULL,
	structurereferente_id		INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON UPDATE CASCADE ON DELETE SET NULL,
	commentaire					TEXT DEFAULT NULL,
	created						TIMESTAMP WITHOUT TIME ZONE,
	modified					TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE decisionsnonorientationspros93 IS 'Décisions de la saisine d''EP lors d''une non orientation du social vers le professionel dans une délai de 6, 12 ou 24 mois (CG93)';

DROP INDEX IF EXISTS decisionsnonorientationspros93_nonorientationpro93_id_etape_unique_idx;
CREATE UNIQUE INDEX decisionsnonorientationspros93_nonorientationpro93_id_etape_unique_idx ON decisionsnonorientationspros93(nonorientationpro93_id,etape);

DROP INDEX IF EXISTS decisionsnonorientationspros93_nonorientationpro93_id_idx;
CREATE INDEX decisionsnonorientationspros93_nonorientationpro93_id_idx ON decisionsnonorientationspros93 (nonorientationpro93_id);

DROP INDEX IF EXISTS decisionsnonorientationspros93_typeorient_id_idx;
CREATE INDEX decisionsnonorientationspros93_typeorient_id_idx ON decisionsnonorientationspros93 (typeorient_id);

DROP INDEX IF EXISTS decisionsnonorientationspros93_structurereferente_id_idx;
CREATE INDEX decisionsnonorientationspros93_structurereferente_id_idx ON decisionsnonorientationspros93 (structurereferente_id);

-- *****************************************************************************
-- Indexes liés aux EPs
-- *****************************************************************************

DROP INDEX IF EXISTS decisionsnonrespectssanctionseps93_nonrespectsanctionep93_id_idx;
CREATE INDEX decisionsnonrespectssanctionseps93_nonrespectsanctionep93_id_idx ON decisionsnonrespectssanctionseps93 (nonrespectsanctionep93_id);

DROP INDEX IF EXISTS eps_membreseps_ep_id_idx;
CREATE INDEX eps_membreseps_ep_id_idx ON eps_membreseps (ep_id);

DROP INDEX IF EXISTS eps_membreseps_membreep_id_idx;
CREATE INDEX eps_membreseps_membreep_id_idx ON eps_membreseps (membreep_id);

DROP INDEX IF EXISTS nvsepdspdos66_decisionpdo_id_idx;
CREATE INDEX nvsepdspdos66_decisionpdo_id_idx ON nvsepdspdos66 (decisionpdo_id);

DROP INDEX IF EXISTS nvsepdspdos66_saisineepdpdo66_id_idx;
CREATE INDEX nvsepdspdos66_saisineepdpdo66_id_idx ON nvsepdspdos66 (saisineepdpdo66_id);

DROP INDEX IF EXISTS nvsrsepsreorient66_saisineepbilanparcours66_id_idx;
CREATE INDEX nvsrsepsreorient66_saisineepbilanparcours66_id_idx ON nvsrsepsreorient66 (saisineepbilanparcours66_id);

DROP INDEX IF EXISTS nvsrsepsreorient66_structurereferente_id_idx;
CREATE INDEX nvsrsepsreorient66_structurereferente_id_idx ON nvsrsepsreorient66 (structurereferente_id);

DROP INDEX IF EXISTS nvsrsepsreorient66_typeorient_id_idx;
CREATE INDEX nvsrsepsreorient66_typeorient_id_idx ON nvsrsepsreorient66 (typeorient_id);

DROP INDEX IF EXISTS nvsrsepsreorientsrs93_saisineepreorientsr93_id_idx;
CREATE INDEX nvsrsepsreorientsrs93_saisineepreorientsr93_id_idx ON nvsrsepsreorientsrs93 (saisineepreorientsr93_id);

DROP INDEX IF EXISTS nvsrsepsreorientsrs93_structurereferente_id_idx;
CREATE INDEX nvsrsepsreorientsrs93_structurereferente_id_idx ON nvsrsepsreorientsrs93 (structurereferente_id);

DROP INDEX IF EXISTS nvsrsepsreorientsrs93_typeorient_id_idx;
CREATE INDEX nvsrsepsreorientsrs93_typeorient_id_idx ON nvsrsepsreorientsrs93 (typeorient_id);

DROP INDEX IF EXISTS saisinesepdspdos66_dossierep_id_idx;
CREATE INDEX saisinesepdspdos66_dossierep_id_idx ON saisinesepdspdos66 (dossierep_id);

DROP INDEX IF EXISTS saisinesepdspdos66_traitementpdo_id_idx;
CREATE INDEX saisinesepdspdos66_traitementpdo_id_idx ON saisinesepdspdos66 (traitementpdo_id);

DROP INDEX IF EXISTS saisinesepsbilansparcours66_bilanparcours66_id_idx;
CREATE INDEX saisinesepsbilansparcours66_bilanparcours66_id_idx ON saisinesepsbilansparcours66 (bilanparcours66_id);

DROP INDEX IF EXISTS saisinesepsbilansparcours66_dossierep_id_idx;
CREATE INDEX saisinesepsbilansparcours66_dossierep_id_idx ON saisinesepsbilansparcours66 (dossierep_id);

DROP INDEX IF EXISTS saisinesepsbilansparcours66_structurereferente_id_idx;
CREATE INDEX saisinesepsbilansparcours66_structurereferente_id_idx ON saisinesepsbilansparcours66 (structurereferente_id);

DROP INDEX IF EXISTS saisinesepsbilansparcours66_typeorient_id_idx;
CREATE INDEX saisinesepsbilansparcours66_typeorient_id_idx ON saisinesepsbilansparcours66 (typeorient_id);

DROP INDEX IF EXISTS saisinesepsreorientsrs93_dossierep_id_idx;
CREATE INDEX saisinesepsreorientsrs93_dossierep_id_idx ON saisinesepsreorientsrs93 (dossierep_id);

DROP INDEX IF EXISTS saisinesepsreorientsrs93_motifreorient_id_idx;
CREATE INDEX saisinesepsreorientsrs93_motifreorient_id_idx ON saisinesepsreorientsrs93 (motifreorient_id);

DROP INDEX IF EXISTS saisinesepsreorientsrs93_orientstruct_id_idx;
CREATE INDEX saisinesepsreorientsrs93_orientstruct_id_idx ON saisinesepsreorientsrs93 (orientstruct_id);

DROP INDEX IF EXISTS saisinesepsreorientsrs93_structurereferente_id_idx;
CREATE INDEX saisinesepsreorientsrs93_structurereferente_id_idx ON saisinesepsreorientsrs93 (structurereferente_id);

DROP INDEX IF EXISTS saisinesepsreorientsrs93_typeorient_id_idx;
CREATE INDEX saisinesepsreorientsrs93_typeorient_id_idx ON saisinesepsreorientsrs93 (typeorient_id);

DROP INDEX IF EXISTS defautsinsertionseps66_dossierep_id_idx;
CREATE INDEX defautsinsertionseps66_dossierep_id_idx ON defautsinsertionseps66 (dossierep_id);

DROP INDEX IF EXISTS defautsinsertionseps66_bilanparcours66_id_idx;
CREATE INDEX defautsinsertionseps66_bilanparcours66_id_idx ON defautsinsertionseps66 (bilanparcours66_id);

DROP INDEX IF EXISTS defautsinsertionseps66_contratinsertion_id_idx;
CREATE INDEX defautsinsertionseps66_contratinsertion_id_idx ON defautsinsertionseps66 (contratinsertion_id);

DROP INDEX IF EXISTS defautsinsertionseps66_orientstruct_id_idx;
CREATE INDEX defautsinsertionseps66_orientstruct_id_idx ON defautsinsertionseps66 (orientstruct_id);

DROP INDEX IF EXISTS defautsinsertionseps66_origine_idx;
CREATE INDEX defautsinsertionseps66_origine_idx ON defautsinsertionseps66 (origine);

DROP INDEX IF EXISTS defautsinsertionseps66_type_idx;
CREATE INDEX defautsinsertionseps66_type_idx ON defautsinsertionseps66 (type);

DROP INDEX IF EXISTS defautsinsertionseps66_historiqueetatpe_id_idx;
CREATE INDEX defautsinsertionseps66_historiqueetatpe_id_idx ON defautsinsertionseps66 (historiqueetatpe_id);


DROP INDEX IF EXISTS decisionsdefautsinsertionseps66_defautinsertionep66_id_idx;
CREATE INDEX decisionsdefautsinsertionseps66_defautinsertionep66_id_idx ON decisionsdefautsinsertionseps66 (defautinsertionep66_id);

DROP INDEX IF EXISTS decisionsdefautsinsertionseps66_etape_idx;
CREATE INDEX decisionsdefautsinsertionseps66_etape_idx ON decisionsdefautsinsertionseps66 (etape);

DROP INDEX IF EXISTS decisionsdefautsinsertionseps66_decision_idx;
CREATE INDEX decisionsdefautsinsertionseps66_decision_idx ON decisionsdefautsinsertionseps66 (decision);

-- -----------------------------------------------------------------------------
-- 20110214 - covs-schema-current.sql
-- -----------------------------------------------------------------------------

DROP TABLE IF EXISTS themescovs58 CASCADE;
DROP TABLE IF EXISTS covs58 CASCADE;
DROP TABLE IF EXISTS dossierscovs58 CASCADE;
DROP TABLE IF EXISTS proposorientationscovs58 CASCADE;
DROP TABLE IF EXISTS proposcontratsinsertioncovs58 CASCADE;

-- *****************************************************************************

DROP TYPE IF EXISTS TYPE_ETATCOV CASCADE;
DROP TYPE IF EXISTS TYPE_ETAPECOV CASCADE;

-- *****************************************************************************

CREATE TABLE themescovs58 (
	id					SERIAL NOT NULL PRIMARY KEY,
	name				VARCHAR(50) NOT NULL
);
COMMENT ON TABLE themescovs58 IS 'Liste des différents thèmes traités par la COV (cg58)';

CREATE TYPE TYPE_ETATCOV AS ENUM ('cree', 'traitement', 'finalise');

CREATE TABLE covs58 (
	id					SERIAL NOT NULL PRIMARY KEY,
	name				VARCHAR(50) NOT NULL,
	lieu				VARCHAR(100) DEFAULT NULL,
	datecommission		TIMESTAMP WITHOUT TIME ZONE NOT NULL,
	observation			TEXT DEFAULT NULL,
	etatcov				TYPE_ETATCOV NOT NULL DEFAULT 'cree'
);
COMMENT ON TABLE covs58 IS 'Commissions de la COV (cg58)';

CREATE TYPE TYPE_ETAPECOV AS ENUM ('cree', 'traitement', 'ajourne', 'finalise');

CREATE TABLE dossierscovs58 (
	id 							SERIAL NOT NULL PRIMARY KEY,
	personne_id 				INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	themecov58_id				INTEGER NOT NULL REFERENCES themescovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	etapecov					TYPE_ETAPECOV NOT NULL DEFAULT 'cree',
	cov58_id					INTEGER DEFAULT NULL REFERENCES covs58(id) ON DELETE CASCADE ON UPDATE CASCADE
);
COMMENT ON TABLE dossierscovs58 IS 'Dossiers en attente de validation par la COV (cg58)';

CREATE INDEX dossierscovs58_personne_id_idx ON dossierscovs58(personne_id);
CREATE INDEX dossierscovs58_themecov58_id_idx ON dossierscovs58(themecov58_id);
CREATE INDEX dossierscovs58_cov58_id_idx ON dossierscovs58(cov58_id);

CREATE TABLE proposorientationscovs58 (
	id 							SERIAL NOT NULL PRIMARY KEY,
	dossiercov58_id				INTEGER NOT NULL REFERENCES dossierscovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	typeorient_id 				INTEGER NOT NULL REFERENCES typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE,
	structurereferente_id 		INTEGER NOT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	referent_id 				INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datedemande 				DATE NOT NULL,
	rgorient 					INTEGER NOT NULL,
	covtypeorient_id 			INTEGER DEFAULT NULL REFERENCES typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE,
	covstructurereferente_id	INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datevalidation				DATE DEFAULT NULL,
	commentaire 				TEXT DEFAULT NULL
);
COMMENT ON TABLE proposorientationscovs58 IS 'Orientations en attente de validation par la COV (cg58)';
-- ajouter contrainte que pour une personne que l'on n'ait qu'une seule proposition non traitée

CREATE INDEX proposorientationscovs58_dossiercov58_id_idx ON proposorientationscovs58(dossiercov58_id);
CREATE INDEX proposorientationscovs58_typeorient_id_idx ON proposorientationscovs58(typeorient_id);
CREATE INDEX proposorientationscovs58_structurereferente_id_idx ON proposorientationscovs58(structurereferente_id);
CREATE INDEX proposorientationscovs58_referent_id_idx ON proposorientationscovs58(referent_id);
CREATE INDEX proposorientationscovs58_covtypeorient_id_idx ON proposorientationscovs58(covtypeorient_id);
CREATE INDEX proposorientationscovs58_covstructurereferente_id_idx ON proposorientationscovs58(covstructurereferente_id);

CREATE TABLE proposcontratsinsertioncovs58 (
	id 							SERIAL NOT NULL PRIMARY KEY,
	dossiercov58_id				INTEGER NOT NULL REFERENCES dossierscovs58(id) ON DELETE CASCADE ON UPDATE CASCADE,
	structurereferente_id 		INTEGER NOT NULL REFERENCES structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	referent_id 				INTEGER DEFAULT NULL REFERENCES referents(id) ON DELETE CASCADE ON UPDATE CASCADE,
	datedemande 				DATE NOT NULL,
	num_contrat					TYPE_NUM_CONTRAT NOT NULL,
	dd_ci						DATE NOT NULL,
	duree_engag					INTEGER NOT NULL,
	df_ci						DATE NOT NULL,
	forme_ci					VARCHAR(1) NOT NULL,
	avisraison_ci				VARCHAR(1) DEFAULT NULL,
	rg_ci						INTEGER DEFAULT NULL,
	datevalidation				DATE DEFAULT NULL,
	commentaire 				TEXT DEFAULT NULL
);
COMMENT ON TABLE proposcontratsinsertioncovs58 IS 'Contrats d''insertion en attente de validation par la COV (cg58)';
-- ajouter contrainte que pour une personne que l'on n'ait qu'une seule proposition non traitée

CREATE INDEX proposcontratsinsertioncovs58_dossiercov58_id_idx ON proposcontratsinsertioncovs58(dossiercov58_id);
CREATE INDEX proposcontratsinsertioncovs58_structurereferente_id_idx ON proposcontratsinsertioncovs58(structurereferente_id);
CREATE INDEX proposcontratsinsertioncovs58_referent_id_idx ON proposcontratsinsertioncovs58(referent_id);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************