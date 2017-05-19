<?php
	/**
	 * Code source de la classe IndexesShell.
	 *
	 * PHP 5.3
	 *
	 * @package Pgsqlcake
	 * @subpackage Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe IndexesShell vérifie si toutes les colonnes se terminant par _id
	 * possèdent bien un index.
	 *
	 * @package Pgsqlcake
	 * @subpackage Console.Command
	 */
	class IndexesShell extends XShell
	{

		/**
		 *
		 * @var type
		 */
		public $output = array();

		/**
		 *
		 */
		protected function _listTableConstraints( $schema, $table ) {
			$name = Inflector::classify( $table );
			$Model = new AppModel( array( 'name' => $name, 'table' => $table ) );

			$fields = $this->connection->query( "SELECT column_name FROM information_schema.columns WHERE table_schema = '{$schema}' AND table_name = '{$table}' AND column_name ~ '_id$';" );
			$fields = Set::extract( $fields, '/0/column_name' );

			$indexes = $this->connection->index( $Model );
			$indexes = Set::classicExtract( $indexes, '{s}.column' );

			/* $Model->Behaviors->attach( 'Pgsqlcake.PgsqlSchema' );
			  $fkPresentes = $Model->foreignKeysFrom();
			  $offsets = Set::extract( $fkPresentes, '/From/column' ); */

			$hr = str_pad( '-- ', 80, '-' );

			$output = array( $hr, "-- Ajout des indexes pour la table {$table}.", $hr );
			foreach( $fields as $fieldName ) {
				if( empty( $indexes ) || !in_array( $fieldName, $indexes ) ) {
					$output[] = "DROP INDEX IF EXISTS {$table}_{$fieldName}_idx;";
					$output[] = "CREATE INDEX {$table}_{$fieldName}_idx ON {$table}( {$fieldName} );";
				}
			}

			if( count( $output ) > 3 ) {
				$this->output = array_merge( $this->output, $output );
			}
		}

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->addArgument( 'table', array( 'help' => 'Table à analyser (all: analyse toutes les tables)', 'required' => true ) );
			return $parser;
		}

		/**
		 *
		 */
		public function main() {
			$tables = array( );
			if( $this->args[0] != 'all' ) {
				$tables[] = $this->args[0];
			}
			else {
				$tables = $this->connection->listSources();
			}
			sort( $tables );


			$this->XProgressBar->start( count( $tables ) );
			foreach( $tables as $table ) {
				$this->XProgressBar->next( 1, '<info>Table en cours d\'analyse : </info><important>'.$table.'</important>' );
				$this->_listTableConstraints( $this->connection->config['schema'], $table );
			}

//			if( count( $this->output ) > 3 ) {
			$this->out();
			$this->out( $this->output );
//			}
		}

	}
?>