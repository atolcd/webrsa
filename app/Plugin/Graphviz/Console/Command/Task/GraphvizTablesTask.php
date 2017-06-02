<?php
	/**
	 * Code source de la classe TablesTask.
	 *
	 * PHP 5.3
	 *
	 * @package Graphviz
	 * @subpackage Console.Command.Task
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppShell', 'Console/Command' );
	App::uses( 'ConnectionManager', 'Model' );

	/**
	 * La classe TablesTask ...
	 *
	 * @package Graphviz
	 * @subpackage Console.Command.Task
	 */
	class GraphvizTablesTask extends AppShell
	{
		/**
		 *
		 * @var DataSource
		 */
		public $Dbo = null;

//		public function execute() {}

		public function initialize() {
			parent::initialize();
			$this->Dbo = ConnectionManager::getDataSource( 'default' );
		}

		public function getNames() {
			$tables = $this->Dbo->listSources();
			sort( $tables );

			$regex = Hash::get( $this->params, 'tables' );
			if( !empty( $regex ) ) {
				foreach( $tables as $key => $table ) {
					if( !preg_match( $regex, $table ) ) {
						unset( $tables[$key] );
					}
				}
			}

			return $tables;
		}

		protected $_typemap = array(
			'character varying' => 'varchar',
			'USER-DEFINED' => 'text'
		);

		// INFO: ne fonctionne qu'avec PostgreSQL
		public function getFields( $tableName ) {
			$result = array();
			$sql = "SELECT
							column_name AS \"name\",
							data_type AS \"type\",
							is_nullable AS \"null\",
							column_default AS \"default\",
							character_maximum_length AS \"length\"
						FROM information_schema.columns
						WHERE
							table_name = '{$tableName}'
							AND table_schema = 'public'
						ORDER BY ordinal_position;";
			foreach( $this->Dbo->query( $sql ) as $row ) {
				$result[$row[0]['name']] = array(
					'type' => strtoupper( isset( $this->_typemap[$row[0]['type']] ) ? $this->_typemap[$row[0]['type']] : $row[0]['type'] ),
					'null' => ( $row[0]['null'] === 'YES' ? true : false ),
					'default' => $row[0]['default'],
					'length' => $row[0]['length']
				);

				if( strpos( $result[$row[0]['name']]['default'], 'nextval(' ) !== false ) {
					$result[$row[0]['name']]['default'] = null;
				}
				else if( $result[$row[0]['name']]['default'] !== null ) {
					$result[$row[0]['name']]['default'] = preg_replace( '/^\'([^\']+)\'.*$/', '\1', $result[$row[0]['name']]['default'] );
					$result[$row[0]['name']]['default'] = preg_replace( '/^([^\:]+).*$/', '\1', $result[$row[0]['name']]['default'] );
					if( strtolower( $result[$row[0]['name']]['default'] ) === 'null' ) {
						$result[$row[0]['name']]['default'] = null;
					}
				}
			}

			return $result;
		}

		public function getRelations( $tableName ) {
			$foreignKeys = $this->Dbo->getPostgresForeignKeys(
				array(
					"\"From\".\"table_name\" = '{$tableName}'",
					"\"From\".table_schema = '{$this->Dbo->config['schema']}'",
					"\"To\".\"table_name\" IN ( '".implode("', '", $this->getNames())."' )",
					"\"To\".table_schema = '{$this->Dbo->config['schema']}'",
				)
			);

			return $foreignKeys;
		}

		public function getIndexes( $tableName ) {
			return $this->Dbo->index( $tableName );
		}

		public function getSummary( $tableName ) {
			$summary = array(
				'Table' => array(
					'name' => $tableName,
					'fields' => $this->getFields( $tableName ),
					'relations' => $this->getRelations( $tableName ),
					'indexes' => $this->getIndexes( $tableName )
				)
			);

			return $summary;
		}

		public function getSummaries() {
			$summaries = array();

			$tableNames = $this->getNames();

			foreach( $tableNames as $tableName ) {
				$summaries[] = $this->getSummary( $tableName );
			}

			return $summaries;
		}
	}
?>