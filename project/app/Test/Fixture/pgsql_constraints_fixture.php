<?php
	/**
	 * Code source de la classe PgsqlConstraintsFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Cette classe permet d'ajouter des contraintes de CHECK aux tables créées
	 * pour les fixtures à partir des contraintes de CHECK faisant appel à des
	 * fonctions cakephp_validate_ trouvées dans les tables de reférence.
	 *
	 * Cette classe est uniquement destinée à être sous-classée et ne fonctionne
	 * qu'avec le driver PostgreSQL.
	 *
	 * @package app.Test.Fixture
	 */
	abstract class PgsqlConstraintsFixture extends CakeTestFixture
	{
		/**
		 * Source de données "maître"
		 *
		 * @var DataSource
		 */
		public $masterDb = null;

		/**
		 * Source de données utilisée pour les tests unitaires
		 *
		 * @var DataSource
		 */
		public $testDb = null;

		/**
		 * Permet l'exécution protégée d'une requête sur une base de données,
		 * sans ou avec mise en cache.
		 *
		 * @param object $Db La base de données
		 * @param string $sql La requête SQL
		 * @param boolean $cache Permettre la mise en cache ?
		 * @return boolean|array
		 */
		protected function _query( &$Db, $sql, $cache = false ) {
			try {
				return $Db->query( $sql, array(), $cache );
			} catch (Exception $e) {
				debug( $e );
				return false;
			}
		}

		/**
		 * Retourne la liste des contraintes de CHECK fisant appel à des fonctions
		 * cakephp_validate_
		 *
		 * TODO: prefixes
		 *
		 * @param DataSource $conn
		 * @param string $tableName
		 * @return array
		 */
		protected function _getTableContraints( &$conn, $tableName ) {
			$sql = "SELECT
					istc.table_catalog,
					istc.table_schema,
					istc.table_name,
					istc.constraint_name,
					iscc.check_clause
				FROM information_schema.check_constraints AS iscc
					INNER JOIN information_schema.table_constraints AS istc ON (
						istc.constraint_name = iscc.constraint_name
					)
				WHERE
					istc.table_catalog = '{$conn->config['database']}'
					AND istc.table_schema = '{$conn->config['schema']}'
					AND istc.table_name = '".$tableName."'
					AND istc.constraint_type = 'CHECK'
					AND iscc.check_clause ~ 'cakephp_validate_.*(.*)';";

			return $this->_query( $conn, $sql );
		}

		/**
		 * Création du langage PlPgsql ainsi que des fonctions qui seront utilisées
		 * dans les CHECK.
		 *
		 * TODO: plus fin -> les fonctions existent déjà, on en a d'autres dans master, ...
		 *
		 * @param DataSource $conn
		 */
		protected function _createContraintFunctions( &$conn ) {
			$functions = array(
				"CREATE OR REPLACE FUNCTION public.create_plpgsql_language ()
					RETURNS TEXT
					AS $$
						CREATE LANGUAGE plpgsql;
						SELECT 'language plpgsql created'::TEXT;
					$$
				LANGUAGE 'sql';",

				"SELECT CASE WHEN
					( SELECT true::BOOLEAN FROM pg_language WHERE lanname='plpgsql')
				THEN
					(SELECT 'language already installed'::TEXT)
				ELSE
					(SELECT public.create_plpgsql_language())
				END;",
				"CREATE OR REPLACE FUNCTION cakephp_validate_in_list( text, text[] ) RETURNS boolean AS
				$$
					SELECT $1 IS NULL OR ( ARRAY[CAST($1 AS TEXT)] <@ CAST($2 AS TEXT[]) );
				$$
				LANGUAGE sql IMMUTABLE;",
				"CREATE OR REPLACE FUNCTION cakephp_validate_in_list( integer, integer[] ) RETURNS boolean AS
				$$
					SELECT $1 IS NULL OR ( ARRAY[CAST($1 AS TEXT)] <@ CAST($2 AS TEXT[]) );
				$$
				LANGUAGE sql IMMUTABLE;",
				"CREATE OR REPLACE FUNCTION cakephp_validate_range( p_check float, p_lower float, p_upper float ) RETURNS boolean AS
				$$
					BEGIN
						RETURN p_check IS NULL
							OR p_lower IS NULL
							OR p_upper IS NULL
							OR(
								p_check > p_lower
								AND p_check < p_upper
							);
					END;
				$$
				LANGUAGE plpgsql IMMUTABLE;",
				"CREATE OR REPLACE FUNCTION cakephp_validate_inclusive_range( p_check float, p_lower float, p_upper float ) RETURNS boolean AS
				$$
					BEGIN
						RETURN p_check IS NULL
							OR p_lower IS NULL
							OR p_upper IS NULL
							OR(
								p_check >= p_lower
								AND p_check <= p_upper
							);
					END;
				$$
				LANGUAGE plpgsql IMMUTABLE;",

				'CREATE OR REPLACE FUNCTION cakephp_validate_ssn( p_ssn text, p_regex text, p_country text ) RETURNS boolean AS
				$$
					BEGIN
						RETURN ( p_ssn IS NULL )
							OR(
				-- 				(
				-- 					( p_country IS NULL OR p_country IN ( \'all\', \'can\', \'us\' ) )
				-- 					AND p_ssn ~ E\'^(?:\\+?1)?[-. ]?\\(?[2-9][0-8][0-9]\\)?[-. ]?[2-9][0-9]{2}[-. ]?[0-9]{4}$\'
				-- 				)
				-- 				OR
								(
									( p_country = \'fr\' )
									AND UPPER( p_ssn ) ~ E\'^(1|2|7|8)[0-9]{2}(0[1-9]|10|11|12|[2-9][0-9])((0[1-9]|[1-8][0-9]|9[0-5]|2A|2B)(00[1-9]|0[1-9][0-9]|[1-8][0-9][0-9]|9[0-8][0-9]|990)|(9[7-8][0-9])(0[1-9]|0[1-9]|[1-8][0-9]|90)|99(00[1-9]|0[1-9][0-9]|[1-8][0-9][0-9]|9[0-8][0-9]|990))(00[1-9]|0[1-9][0-9]|[1-9][0-9][0-9]|)(0[1-9]|[1-8][0-9]|9[0-7])$\'
								)
								OR
								(
									( p_regex IS NOT NULL )
									AND p_ssn ~ p_regex
								)
							);
					END;
				$$ LANGUAGE plpgsql;',

				'CREATE OR REPLACE FUNCTION "public"."calcul_cle_nir" (text) RETURNS text AS
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

						IF p_nir ~ \'^.{6}(A|B)\' THEN
							IF p_nir ~ \'^.{6}A\' THEN
								correction:=1000000;
							ELSE
								correction:=2000000;
							END IF;
							p_nir:=regexp_replace( p_nir, \'(A|B)\', \'0\' );
						END IF;

						cle:=LPAD( CAST( 97 - ( ( CAST( p_nir AS BIGINT ) - correction ) % 97 ) AS VARCHAR(13)), 2, \'0\' );
						RETURN cle;
					END;
				$body$
				LANGUAGE \'plpgsql\' VOLATILE RETURNS NULL ON NULL INPUT SECURITY INVOKER;',

				'CREATE OR REPLACE FUNCTION "public"."nir_correct" (TEXT) RETURNS BOOLEAN AS
				$body$
					DECLARE
						p_nir text;

					BEGIN
						p_nir:=$1;

						RETURN (
							CHAR_LENGTH( TRIM( BOTH \' \' FROM p_nir ) ) = 15
							AND (
								cakephp_validate_ssn( p_nir, null, \'fr\' )
								AND calcul_cle_nir( SUBSTRING( p_nir FROM 1 FOR 13 ) ) = SUBSTRING( p_nir FROM 14 FOR 2 )
							)
						);
					END;
				$body$
				LANGUAGE \'plpgsql\';',

				'CREATE OR REPLACE FUNCTION public.nir_correct13( TEXT ) RETURNS BOOLEAN AS
				$body$
					DECLARE
						p_nir text;
					BEGIN
						p_nir:=$1;

						IF p_nir IS NULL THEN
							RETURN false;
						END IF;

						RETURN (
							CHAR_LENGTH( TRIM( BOTH \' \' FROM p_nir ) ) >= 13
							AND (
								cakephp_validate_ssn( SUBSTRING( p_nir FROM 1 FOR 13 ) || calcul_cle_nir( SUBSTRING( p_nir FROM 1 FOR 13 ) ), null, \'fr\' )
							)
						);
					END;
				$body$
				LANGUAGE \'plpgsql\' IMMUTABLE;',
			);

			foreach( $functions as $sql ) {
				$this->_query( $conn, $sql );
			}
		}

		/**
		 * Création de la table.
		 *
		 * @param object $db
		 * @return boolean
		 */
		public function create( $db ) {
			$return = parent::create( $db );

			$this->testDb = $db;
			$this->masterDb = ConnectionManager::getDataSource( 'default' );

			$masterContraints = $this->_getTableContraints( $this->masterDb, $this->table );
			$testContraints = $this->_getTableContraints( $this->testDb, $this->table );
			if( !empty( $masterContraints ) && empty( $testContraints ) ) {
				$this->_createContraintFunctions( $this->testDb );
				foreach( $masterContraints as $constraint ) {
					$constraint = $constraint[0];
					// TODO: prefixes
					$sql = "ALTER TABLE {$this->table} ADD CONSTRAINT {$constraint['constraint_name']} CHECK ( {$constraint['check_clause']} );";
					$this->_query( $this->testDb, $sql );
				}
			}

			return $return;
		}
	}
?>
