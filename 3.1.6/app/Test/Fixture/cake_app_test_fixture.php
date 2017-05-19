<?php
	/**
	 * Code source de la classe CakeAppTestFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Cette classe sert de classe parente à toutes les fixtures de app, en tous
	 * cas pour les tables qui contiennent des types enum en PostgreSQL.
	 *
	 * @package app.Test.Fixture
	 */
	abstract class CakeAppTestFixture extends CakeTestFixture
	{
		public $masterDb = null;
		public $masterDbPrefix = null;
		public $testDb = null;
		public $testDbPrefix = null;
		public $_pgsqlEnumTypes = false;
		public $_masterTableTypes = array();

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
		 * À partir du nom du type, on vérifie s'il existe déjà dans le base de test ou non.
		 * S'il existe ou s'il n'a aucunes valeurs la fonction se termine.
		 * S'il n'existe pas, on va rechercher ses valeurs et on le crée.
		 *
		 * @param string $typeName
		 */
		protected function _createTypeIfNotExists( $typeName ) {
			$sql = "SELECT count(*) FROM pg_catalog.pg_type where typname = '{$this->testDbPrefix}{$typeName}';";
			$count = Hash::get( $this->_query( $this->testDb, $sql ), '0.0.count' );

			if( $count == 0 ) {
				$sql = "SELECT enum_range(null::{$this->masterDbPrefix}{$typeName});";
				$values = $this->_query( $this->masterDb, $sql, true );
				if( !empty( $values ) ) {
					$patterns = array( '{', '}' );
					$values = str_replace( $patterns, '', Set::extract( $values, '0.0.enum_range' ) );
					$values = explode( ',', $values );

					$sql = "CREATE TYPE {$this->testDbPrefix}{$typeName} AS ENUM ( '".implode( "', '", $values )."' );";
					$this->_query( $this->testDb, $sql );
				}
			}
		}

		/**
		 * Met à null la valeur par défaut du champ passé en paramètre et le cast avec ce type.
		 *
		 * @param string $typeName
		 * @param string $columnName
		 * @return boolean Toutes les requêtes ont été exécutées sans erreur
		 */
		protected function _alterColumns( $typeName, $columnName ) {
			$queries = array();

			if ( !$this->fields[$columnName]['null'] ) {
				$queries[] = "ALTER TABLE {$this->testDbPrefix}{$this->table} ALTER COLUMN {$columnName} DROP NOT NULL;";
			}

			$queries[] = "ALTER TABLE {$this->testDbPrefix}{$this->table} ALTER COLUMN {$columnName} SET DEFAULT NULL;";
			$queries[] = "ALTER TABLE {$this->testDbPrefix}{$this->table} ALTER COLUMN {$columnName} TYPE {$this->testDbPrefix}{$typeName} USING CAST({$columnName} AS {$this->testDbPrefix}{$typeName});";

			if ( !empty( $this->fields[$columnName]['default'] ) ) {
				$queries[] = "ALTER TABLE {$this->testDbPrefix}{$this->table} ALTER COLUMN {$columnName} SET DEFAULT '{$this->fields[$columnName]['default']}'::{$this->testDbPrefix}{$typeName};";
			}

			if ( !$this->fields[$columnName]['null'] ) {
				$queries[] = "ALTER TABLE {$this->testDbPrefix}{$this->table} ALTER COLUMN {$columnName} SET NOT NULL;";
			}

			$success = true;
			foreach( $queries as $sql ) {
				$success && $this->_query($this->testDb, $sql );
			}
			return $success;
		}

		/**
		 * Si la table est al dernière de la base à utiliser le type passé en paramètre, on supprime le type
		 *
		 * @param string $typeName
		 */
		protected function _dropTypeIfLastTable( $typeName ) {
			$sql = "SELECT COUNT( DISTINCT(table_name) ) FROM information_schema.columns WHERE data_type = 'USER-DEFINED' AND udt_name = '{$this->testDbPrefix}{$typeName}' AND table_name <> '{$this->testDbPrefix}{$this->table}';";
			$count = Hash::get( (array)$this->_query( $this->testDb, $sql ), '0.0.count' );

			if ( $count === 0 ) {
				$sql = "DROP TYPE {$this->testDbPrefix}{$typeName} CASCADE";
				$this->_query( $this->testDb, $sql );
			}
		}

		/**
		 * Retourne les types ainsi que les champs pour une table particulière. + valeurs par défaut
		 * ex. pour la table users: array( 'type_no' => array( 'isgestionnaire', 'sensibilite' ) )
		 *
		 * @param string $tableName
		 * @return array
		 */
		protected function _masterTableTypes( $tableName ) {
			$sql = "SELECT column_name, udt_name FROM information_schema.columns WHERE table_name = '{$this->masterDbPrefix}{$this->table}' AND data_type = 'USER-DEFINED';";
			$results = $this->_query( $this->masterDb, $sql, true );
			$return = array();

			if( !empty( $results ) ) {
				foreach( $results as $key => $fields ) {
					$column_name = $fields[0]['column_name'];
					$udt_name = $fields[0]['udt_name'];
					$return[$udt_name][] = $column_name;
				}
			}

			return $return;
		}

		/**
		 * Création de la table.
		 *
		 * @see http://www.tig12.net/downloads/apidocs/cakephp/cake/tests/lib/CakeTestFixture.class.html
		 *
		 * @param object $db
		 */
		public function create( $db ) {
			$return = parent::create( $db );

			if( $db instanceof Postgres ) {
				$this->testDb = $db;
				$this->testDbPrefix = $db->config['prefix'];

				$this->masterDb = ConnectionManager::getDataSource( 'default' );
				$this->masterDbPrefix = $this->masterDb->config['prefix'];

				$this->_initPostgresqlEnums( $db );
			}

			return $return;
		}

		/**
		 * Initialisation des enums
		 *
		 * @param object $db
		 */
		protected function _initPostgresqlEnums( $db ) {
			$masterTableTypes = $this->_masterTableTypes( $this->table );
			if ( !empty( $masterTableTypes ) ) {
				foreach( $masterTableTypes as $type => $fields) {
					$this->_createTypeIfNotExists( $type );
					foreach( $fields as $field ) {
						$this->_alterColumns( $type, $field );
					}
				}
			}
		}

		/**
		 * Destruction de la table.
		 *
		 * @param object $db
		 */
		public function drop( $db ) {
			$return = parent::drop( $db );

			if( $db instanceof Postgres ) {
				$this->testDb = $db;
				$this->testDbPrefix = $db->config['prefix'];

				$this->masterDb = ConnectionManager::getDataSource( 'default' );
				$this->masterDbPrefix = $this->masterDb->config['prefix'];

				$this->_dropPostgresqlEnums( $db );
			}

			return $return;
		}

		/**
		 * Suppression des enums
		 *
		 * @param object $db
		 */
		protected function _dropPostgresqlEnums( $db ) {
			$masterTableTypes = $this->_masterTableTypes( $this->table );
			if ( !empty( $masterTableTypes ) ) {
				foreach( $masterTableTypes as $type => $fields) {
					$this->_dropTypeIfLastTable( $type );
				}
			}
		}
	}

?>
