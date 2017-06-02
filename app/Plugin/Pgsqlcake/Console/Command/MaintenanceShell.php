<?php
	/**
	 * Code source de la classe MaintenanceShell.
	 *
	 * PHP 5.3
	 *
	 * @package Pgsqlcake
	 * @subpackage Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::import( 'ConnectionManager', 'Model' );
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe MaintenanceShell fournit des méthodes de maintenance de base
	 * de données PostgreSQL.
	 *
	 * @package Pgsqlcake
	 * @subpackage Console.Command
	 * @see http://docs.postgresqlfr.org/8.2/maintenance.html
	 */
	class MaintenanceShell extends XShell
	{

		/**
		 *
		 * @var type
		 */
		public $commandDescriptions = array(
			'reindex' => 'Reconstruction des indexes',
			'sequences' => 'Mise à jour des compteurs des champs auto-incrémentés',
			'vacuum' => 'Nettoyage de la base de données et mise à jour des statistiques du planificateur'
		);

		/**
		 *
		 * @var type
		 */
		public $operations = array(
			'vacuum',
			'sequences',
			'reindex'
		);

		/**
		 *
		 * @var type
		 */
		public $verbose;

		/**
		 * Initialisation: lecture des paramètres, on s'assure d'avoir une connexion
		 * PostgreSQL valide
		 */
		public function startup() {
			parent::startup();
			$this->connection = @ConnectionManager::getDataSource( $this->params['connection'] );

			if( !$this->connection || !$this->connection->connected ) {
				$this->err( "Impossible de se connecter avec la connexion {$this->params['connection']}" );
				$this->_stop( 1 );
			}

			if( !( $this->connection instanceof Postgres ) ) {
				$this->err( "La connexion {$this->params['connection']} n'utilise pas le driver postgres" );
				$this->_stop( 1 );
			}
		}

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description( 'Script de maintenance de base de données PostgreSQL' );
			$subCommands = array(
				'all' => array( 'help' => "Effectue toutes les opérations de maintenance ( ".implode( ', ', $this->operations )." )." ),
				'reindex' => array( 'help' => $this->commandDescriptions['reindex'] ),
				'sequences' => array( 'help' => $this->commandDescriptions['sequences'] ),
				'vacuum' => array( 'help' => $this->commandDescriptions['vacuum'] )
			);
			$parser->addSubcommands( $subCommands );
			return $parser;
		}

		/**
		 *
		 */
		protected function _showParams() {
			parent::_showParams();
			$this->out( '<info>Version de psql : </info><important>'.Set::classicExtract( $this->connection->query( 'SELECT version();' ), '0.0.version' ).'</important>' );
		}

		/**
		 * Effectue une requête SQL simple et affiche ou retourne si la requête
		 * s'est déroulée sans erreur.
		 */
		protected function _singleQuery( $sql ) {
			$this->connection->query( $sql );

			if( $this->verbose ) {
				$this->out(
						sprintf(
								"$sql\t-- terminé avec %s en %s ms", ( empty( $this->connection->error ) ? 'succès' : 'erreur' ), $this->connection->took
						)
				);
			}

			if( $this->command == 'all' ) {
				return empty( $this->connection->error );
			}
			else {
				$this->out();
				return $this->_stop( !empty( $this->connection->error ) );
			}
		}

		/**
		 * Reconstruction des indexes
		 */
		public function reindex() {
			$this->out( "\n".date( 'H:i:s' )." - {$this->commandDescriptions['reindex']} (reindex)" );
			return $this->_singleQuery( "REINDEX DATABASE {$this->connection->config['database']};" );
		}

		/**
		 * Mise à jour des compteurs des champs auto-incrémentés
		 */
		public function sequences() {
			$this->out( "\n".date( 'H:i:s' )." - {$this->commandDescriptions['sequences']} (sequences)" );

			if( $this->verbose ) {
				$this->out( 'BEGIN;' );
			}
			$this->connection->query( 'BEGIN;' );

			$took = 0;
			$success = true;

			$sql = "SELECT table_name AS \"Model__table\",
						column_name	AS \"Model__column\",
						column_default AS \"Model__sequence\"
						FROM information_schema.columns
						WHERE table_schema = 'public'
							AND column_default LIKE 'nextval(%::regclass)'
						ORDER BY table_name, column_name";

			foreach( $this->connection->query( $sql ) as $model ) {
				$sequence = preg_replace( '/^nextval\(\'(.*)\'.*\)$/', '\1', $model['Model']['sequence'] );

				$sql = "SELECT setval('{$sequence}', COALESCE(MAX({$model['Model']['column']}),0)+1, false) FROM {$model['Model']['table']};";
				$result = $this->connection->query( $sql );

				$tmpSuccess = empty( $this->connection->error );
				$success = $success && $tmpSuccess;

				if( $this->verbose ) {
					$this->out(
							sprintf(
									"$sql\t-- terminé avec %s en %s ms - nouvelle valeur: %s", ( empty( $this->connection->error ) ? 'succès' : 'erreur' ), $this->connection->took, $result[0][0]['setval']
							)
					);
				}
			}

			if( $success ) {
				if( $this->verbose ) {
					$this->out( 'COMMIT;' );
				}
				$this->connection->query( 'COMMIT;' );
			}
			else {
				if( $this->verbose ) {
					$this->err( 'ROLLBACK;' );
				}
				$this->connection->query( 'ROLLBACK;' );
			}

			if( $this->command == 'all' ) {
				return $success;
			}
			else {
				$this->out();
				return $this->_stop( !$success );
			}
		}

		/**
		 * Nettoyage de la base de données et mise à jour des statistiques du planificateur
		 * INFO: pas FULL -> http://docs.postgresqlfr.org/8.2/maintenance.html
		 */
		public function vacuum() {
			$this->out( "\n".date( 'H:i:s' )." - {$this->commandDescriptions['vacuum']} (vacuum)" );
			return $this->_singleQuery( "VACUUM ANALYZE;" );
		}

		/**
		 * Réalisation de toutes les opérations
		 */
		public function all() {
			$error = false;


			foreach( $this->operations as $operation ) {
				$error = !$this->{$operation}() && $error;
			}

			$this->out();
			$this->_stop( $error );
		}

	}
?>