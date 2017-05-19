<?php
	/**
	 * Code source de la classe PgsqlSchemaBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package Pgsqlcake
	 * @subpackage Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe PgsqlSchemaBehavior fournit des méthodes pour interroger des
	 * propriétés propres à Postgresql à partir des classes de modèles.
	 *
	 * @package Pgsqlcake
	 * @subpackage Model.Behavior
	 */
	class PgsqlSchemaBehavior extends ModelBehavior
	{
		/**
		* Setup this behavior with the specified configuration settings.
		* Ne fonctionne qu'avec PostgreSQL
		*
		* @param object $model Model using this behavior
		* @param array $settings Configuration settings for $model
		* @access public
		*/
		public function setup( Model $model, $settings = array() ) {
			if( !( $model->getDataSource() instanceof Postgres ) ) {
				trigger_error( sprintf( __( '%s: driver (%s) non supporté pour le modèle (%s).' ), __CLASS__, $driver, $model->alias ), E_USER_WARNING );
			}
		}

		/**
		* Permet de savoir si une colonne d'un modèle donné a un index unique,
		* éventuellement avec un nom d'index donné.
		*
		* Fonctionne avec tous les SGBD supportés par CakePHP.
		*
		* @param AppModel $model La classe du modèle lié à la table sur laquelle
		* 	l'index s'applique.
		* @param mixed $column La colonne (ou un array contenant les colonnes)
		* 	sur laquelle l'index s'applique.
		* @param string $expectedName Le nom de l'index (null pour ne pas vérifier)
		* @return boolean
		*/

		public function hasUniqueIndex( Model $model, $column, $expectedName = null ) {
			$indexes = $model->getDataSource( $model->useDbConfig )->index( $model );

			foreach( $indexes as $name => $index ) {
				if( $index['unique'] && $index['column'] == $column ) {
					if( is_null( $expectedName ) || ( !is_null( $expectedName ) && ( $name == $expectedName ) ) ) {
						return true;
					}
				}
			}

			return false;
		}

		/**
		* Retrouver une contraine nommée
		*/

		public function hasCheck( Model $model, $constraintName ) {
			$ds = $model->getDataSource( $model->useDbConfig );

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
							istc.table_catalog = '{$ds->config['database']}'
							AND istc.table_schema = '{$ds->config['schema']}'
							AND istc.table_name = '".$ds->fullTableName( $model, false, false )."'
							AND istc.constraint_type = 'CHECK'
							AND istc.constraint_name = '{$constraintName}';";
			$checks = $model->query( $sql );
			return !empty( $checks );
		}

		/**
		 * Liste les contraintes de check d'une table (pas les clés étrangères ni les indexes uniques, ni les
		 * not null).
		 *
		 * @param Model $model Le modèle pour lequel on veut la liste des contraintes de la table liée
		 * @return array
		 */
		public function pgCheckConstraints( Model $model ) {
			$ds = $model->getDataSource( $model->useDbConfig );

			// FIXME: '{$ds->config['database']}'

			$sql = "SELECT
						pc.conname AS \"Check__name\",
						pg_catalog.pg_get_constraintdef(pc.oid, true) AS \"Check__clause\"
					FROM
						pg_catalog.pg_constraint pc
					WHERE
						pc.conrelid = (
							SELECT oid FROM pg_catalog.pg_class
								WHERE
									relname='".$ds->fullTableName( $model, false, false )."'
									AND relnamespace = (
										SELECT oid
											FROM pg_catalog.pg_namespace
											WHERE nspname='{$ds->config['schema']}'
									)
						)
						AND pc.contype = 'c'
					ORDER BY 1";

			/*$sql = "SELECT
						istc.constraint_name AS \"Check__name\",
						iscc.check_clause AS \"Check__clause\"
					FROM information_schema.check_constraints AS iscc
						INNER JOIN information_schema.table_constraints AS istc ON (
							istc.constraint_name = iscc.constraint_name
						)
					WHERE
						istc.table_catalog = '{$ds->config['database']}'
						AND istc.table_schema = '{$ds->config['schema']}'
						AND istc.table_name = '".$ds->fullTableName( $model, false, false )."'
						AND istc.constraint_type = 'CHECK'
						AND (
							istc.constraint_name NOT LIKE '%_not_null'
							AND iscc.check_clause NOT LIKE '% IS NOT NULL'
						);";*/

			return $model->query( $sql );
		}

		/**
		*
		*/

		protected function _foreignKeys( Model $model, $acceptedTables = true, $direction = 'to' ) {
			$ds = $model->getDataSource( $model->useDbConfig );

			if( $direction == 'to' ) {
				$cu = 'ccu';
				$otherCu = 'kcu';
			}
			else {
				$cu = 'kcu';
				$otherCu = 'ccu';
			}

			$conditionsTables = '';
			if( !empty( $acceptedTables ) && !is_bool( $acceptedTables ) ) {
				$conditionsTables = "AND ( {$otherCu}.table_name IN ( '".implode( "', '", $acceptedTables )."' ) ) AND {$cu}.table_name IN ( '".implode( "', '", $acceptedTables )."' )";
			}

			$table = $ds->fullTableName( $model, false, false );

			$sql = "SELECT
						tc.constraint_name AS \"Foreignkey__name\",
						rc.update_rule AS \"Foreignkey__onupdate\",
						rc.delete_rule AS \"Foreignkey__ondelete\",
						kcu.table_schema AS \"From__schema\",
						kcu.table_name AS \"From__table\",
						kcu.column_name AS \"From__column\",
						( CASE WHEN kcc.is_nullable = 'NO' THEN false ELSE true END ) AS \"From__nullable\",
						EXISTS(
							SELECT
									*
								FROM pg_catalog.pg_class c, pg_catalog.pg_class c2, pg_catalog.pg_index i
								WHERE
									c.oid = (
										SELECT
												c.oid
											FROM pg_catalog.pg_class c
											LEFT JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
											WHERE
												c.relname = kcu.table_name
												AND pg_catalog.pg_table_is_visible(c.oid)
												AND n.nspname = kcu.table_schema
									)
									AND c.oid = i.indrelid
									AND i.indexrelid = c2.oid
									AND i.indisunique
									AND regexp_replace( pg_catalog.pg_get_indexdef(i.indexrelid, 0, true), E'^.*\\((.*)\\)$', E'\\1', 'g') = kcu.column_name
						) AS \"From__unique\",
						ccu.table_schema AS \"To__schema\",
						ccu.table_name AS \"To__table\",
						ccu.column_name AS \"To__column\",
						( CASE WHEN ccc.is_nullable = 'NO' THEN false ELSE true END ) AS \"To__nullable\",
						EXISTS(
							SELECT
									*
								FROM pg_catalog.pg_class c, pg_catalog.pg_class c2, pg_catalog.pg_index i
								WHERE
									c.oid = (
										SELECT
												c.oid
											FROM pg_catalog.pg_class c
											LEFT JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
											WHERE
												c.relname = ccu.table_name
												AND pg_catalog.pg_table_is_visible(c.oid)
												AND n.nspname = ccu.table_schema
									)
									AND c.oid = i.indrelid
									AND i.indexrelid = c2.oid
									AND i.indisunique
									AND regexp_replace( pg_catalog.pg_get_indexdef(i.indexrelid, 0, true), E'^.*\\((.*)\\)$', E'\\1', 'g') = ccu.column_name
						) AS \"To__unique\"
					FROM information_schema.table_constraints tc
						LEFT JOIN information_schema.key_column_usage kcu ON (
							tc.constraint_catalog = kcu.constraint_catalog
							AND tc.constraint_schema = kcu.constraint_schema
							AND tc.constraint_name = kcu.constraint_name
						)
						LEFT JOIN information_schema.referential_constraints rc ON (
							tc.constraint_catalog = rc.constraint_catalog
							AND tc.constraint_schema = rc.constraint_schema
							AND tc.constraint_name = rc.constraint_name
						)
						LEFT JOIN information_schema.constraint_column_usage ccu ON (
							rc.unique_constraint_catalog = ccu.constraint_catalog
							AND rc.unique_constraint_schema = ccu.constraint_schema
							AND rc.unique_constraint_name = ccu.constraint_name
						)
						LEFT JOIN information_schema.columns kcc ON (
							kcu.table_schema = kcc.table_schema
							AND kcu.table_name = kcc.table_name
							AND kcu.column_name = kcc.column_name
						)
						LEFT JOIN information_schema.columns ccc ON (
							ccu.table_schema = ccc.table_schema
							AND ccu.table_name = ccc.table_name
							AND ccu.column_name = ccc.column_name
						)
					WHERE
						{$cu}.table_name = '{$table}'
						{$conditionsTables}
						AND tc.constraint_type = 'FOREIGN KEY';";
			return $ds->query( $sql );
		}

		/**
		*
		*/

		public function foreignKeysFrom( Model $model, $acceptedTables = true ) {
			return $this->_foreignKeys( $model, $acceptedTables, 'from' );
		}

		/**
		*
		*/

		public function foreignKeysTo( Model $model, $acceptedTables = true ) {
			return $this->_foreignKeys( $model, $acceptedTables, 'to' );
		}

		/**
		 * Retourne la liste des fonctions PostgreSQL disponibles.
		 *
		 * @param type $model
		 * @param array $names Liste des noms de fonctions que l'on veut trouver.
		 *	Ne pas passer de paramètre pour récupérer toutes les fonctions.
		 * @param array $conditions Les conditions de base (ex. array( 'namespace.nspname = \'public\'' ) )
		 * @return array
		 */
		public function pgFunctions( Model $model, $names = array(), $conditions = array() ) {
			$ds = $model->getDataSource( $model->useDbConfig );

			if( !is_array( $names ) ) {
				$names = (array)$names;
			}

			if( !empty( $names ) ) {
				$conditions[] = 'function.proname IN ( \''.implode( '\', \'', $names ).'\' )';
			}

			$sql = "SELECT
						function.proname as \"Function__name\",
						format_type(function.prorettype, NULL) as \"Function__result\",
						oidvectortypes(function.proargtypes) as \"Function__arguments\"
					FROM pg_proc AS function
						INNER JOIN pg_namespace AS namespace ON ( function.pronamespace = namespace.oid )
					WHERE
						function.prorettype <> 0
						AND (
							pronargs = 0
							OR oidvectortypes(function.proargtypes) <> ''
						)
						".( !empty( $conditions ) ? ' AND '.implode( ' AND ', $conditions ) : '' )."
					ORDER BY
						\"Function__name\",
						\"Function__result\",
						\"Function__arguments\";";

			return $ds->query( $sql );
		}

		/**
		 * Retourne la version de PostgreSQL utilisée.
		 *
		 * @param AppModel $model Modèle utilisant ce behavior
		 * @param boolean $full true: renvoie la chaîne complète, false: renvoie le numéro de version.
		 * @return string
		 */
		public function pgVersion( Model $model, $full = false ) {
			$psqlVersion = $model->getDataSource( $model->useDbConfig )->query( 'SELECT version();' );
			$psqlVersion = Set::classicExtract( $psqlVersion, '0.0.version' );

			if( !$full ) {
				$psqlVersion = preg_replace( '/.*PostgreSQL ([^ ]+) .*$/', '\1', $psqlVersion );
			}

			return $psqlVersion;
		}

		/**
		 * Vérifie si une liste de fonctions PostgreSQL est présente en base.
		 *
		 * @param AppModel $model Modèle utilisant ce behavior
		 * @param array $expected La liste des fonctions PostgreSQL dont on veut vérifier la présence.
		 * @param string $message Le message d'erreur lorsque des fonctions ne sont pas trouvées.
		 * @return array
		 */
		public function pgHasFunctions( Model $model, array $expected, $message = 'Les fonctions PostgreSQL suivantes sont manquantes: %s.' ) {
			$pg_functions = $this->pgFunctions( $model, $expected );
			$pg_functions = Set::extract( $pg_functions, '/Function/name' );
			$pg_functions = array_unique( $pg_functions );

			if( count( $pg_functions ) < count( $expected ) ) {
				$diff = implode( ', ', array_diff( $expected, $pg_functions ) );
				return array(
					'success' => false,
					'message' => sprintf( $message, $diff )
				);
			}
			else {
				return array(
					'success' => true,
					'message' => null
				);
			}
		}

		/**
		 * Vérifie si la date du serveur PostgreSQL correspond à la date du serveur Web.
		 * La tolérance est de moins d'une minute.
		 *
		 * @param AppModel $model Modèle utilisant ce behavior
		 * @param string $message Le message d'erreur la tolérance est dépassée.
		 * @return array
		 */
		public function pgCheckTimeDifference( Model $model, $message = 'Différence de date entre le serveur Web et le serveur de base de données trop importante.' ) {
			$sqlAge = 'AGE( DATE_TRUNC( \'second\', localtimestamp ), \''.date( 'Y-m-d H:i:s' ).'\' )';
			$sqlAgeSuccess = "{$sqlAge} < '1 min'";
			$sql = "SELECT
						{$sqlAge} as value,
						$sqlAgeSuccess AS success,
						( CASE WHEN {$sqlAgeSuccess} THEN NULL ELSE '{$message}' END ) AS message;";
			$result = $model->query( $sql );
			return $result[0][0];
		}

		/**
		 * Permet de vérifier la syntaxe d'un intervalle au sens PostgreSQL.
		 *
		 * @param AppModel $model Modèle utilisant ce behavior
		 * @param string $interval L'intervalle à tester.
		 * @return mixed true si la syntaxe est correcte, sinon une chaîne de
		 *         caractères contenant l'erreur.
		 */
		public function pgCheckIntervalSyntax( Model $model, $interval ) {
			$sql = "EXPLAIN SELECT NOW() + interval '{$interval}'";

			$success = false;
			$message = null;
			try {
				$success = ( @$model->query( $sql ) !== false );
			} catch( Exception $e ) {
			}

			if( $success == false ) {
				$ds = $model->getDataSource();
				$message = $ds->lastError();
			}

			return array(
				'value' => $interval,
				'success' => $success,
				'message' => $message,
			);
		}
	}
?>