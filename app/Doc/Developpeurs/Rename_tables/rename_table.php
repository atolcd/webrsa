<?php
	App::uses( 'AppModel', 'Model' );
	App::uses( 'Model', 'Model' );
	App::uses( 'File', 'Utility' );

	class RenameTableShell extends Shell
	{

		/**
		*
		*/

		public function main() {
			if( count( $this->args ) != 4 ) {
				$this->err( "4 arguments: nom existant(pl), nom existant(sing), nom désiré(pl), nom désiré(sing)" );
			}
			else {
				$fromP = trim( $this->args[0] );
				$fromS = trim( $this->args[1] );
				$toP = trim( $this->args[2] );
				$toS = trim( $this->args[3] );

				$this->out( "regexes=( \\" );

				$this->out( "\t\"s/(?<!\w)".Inflector::camelize( $fromP )."(?<!W)/".Inflector::camelize( $toP )."/g\" \\" );
				$this->out( "\t\"s/(?<!\w){$fromP}(?<!W)/{$toP}/g\" \\" );

				$this->out( "\t\"s/(?<!\w)".Inflector::camelize( $fromS )."(?<!W)/".Inflector::camelize( $toS )."/g\" \\" );
				$this->out( "\t\"s/(?<!\w){$fromS}(?<!W)/{$toS}/g\" \\" );

				$this->out( "\t\"s/(?<!\w)".strtoupper( Inflector::singularize( $fromS ) )."(?<!W)/".strtoupper( Inflector::singularize( $toS ) )."/g\" \\" );

				$this->out( ")\nrename_in_files \"\${regexes[@]}\"" );

				$this->out( "" );

				$this->out( "rename_files \"{$fromP}\" \"{$toP}\"" );
				$this->out( "rename_files \"{$fromS}\" \"{$toS}\"" );

				$db = ConnectionManager::getDataSource('default');

				$exist = $db->query( "SELECT COUNT(table_name) FROM information_schema.columns WHERE table_name = '".$fromP."'" );

				if ( $exist[0][0]['count'] > 0 ) {
					$this->out( "" );

					$results = $db->query( "SELECT table_name, column_name FROM information_schema.columns WHERE column_name LIKE '".$fromS."_id'" );

					foreach( $results as $result ) {
						$this->out( "ALTER TABLE ".$result[0]['table_name']." RENAME COLUMN ".$fromS."_id TO ".$toS."_id;" );
					}
					$this->out( "ALTER TABLE ".$fromP." RENAME TO ".$toP.";" );
				}
			}
		}
	}
?>
