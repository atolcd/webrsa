<?php
	/**
	 * Code source de la classe DevelopmentCheckModulePdoShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppShell', 'Console/Command' );
	App::uses( 'ConnectionManager', 'Model' );

	/**
	 * La classe DevelopmentCheckModulePdoShell permet de trouver des tables du
	 * module PCO/PCG66 qui ne sont remplies dans aucune des bases de données
	 * des départements.
	 *
	 * Le shell fournit une expression régulière permetttant de trouver toutes
	 * les occurences (nom de table, de modèle, etc...) dans le code de
	 * l'application.
	 *
	 * @package app.Console.Command
	 */
	class DevelopmentCheckModulePdoShell extends AppShell
	{

		/**
		 * La constante à utiliser dans la méthode _stop() en cas de succès.
		 */
		const SUCCESS = 0;

		/**
		 * La constante à utiliser dans la méthode _stop() en cas d'erreur.
		 */
		const ERROR = 1;

		/**
		 * Méthode principale.
		 */
		public function main() {
			$bdds = array(
				'cg58_20161010_v32x',
				'cg66_20170328_v32x',
				'cg93_20170302_v32x',
				'cg976_20160330_v32x'
			);

			$defaults = array(
				'datasource' => 'Postgres.Database/PostgresPostgres',
				'persistent' => false,
				'host' => 'localhost',
				'login' => 'webrsa',
				'password' => 'webrsa',
				'database' => 'webrsa',
				'prefix' => '',
				'encoding' => 'utf8',
			);

			$results = array();

			foreach( $bdds as $bdd ) {
				$config = array( 'database' => $bdd ) + $defaults;
				ConnectionManager::drop( 'default' );
				$connection = ConnectionManager::create( 'default', $config );

				$tables = $connection->listSources();
				foreach($tables as $index => $table) {
					if( 1 !== preg_match( '/(pdo|pcg)/', $table ) ) {
						unset($tables[$index]);
					}
				}

				sort( $tables );

				foreach( $tables as $table ) {
					if( false === isset( $results[$table] ) ) {
						$results[$table] = array();
					}

					$sql = "SELECT COUNT(*) AS \"count\" FROM {$table};";
					$results[$table][$bdd] = Hash::get( $connection->query( $sql ), '0.0.count' );
				}
			}

			foreach( $results as $index => $result ) {
				$result = array_filter( $result );
				if( false === empty( $result ) ) {
					unset( $results[$index] );
				}
			}

			$results = array_keys( $results );
			$remove = array( 'decisionssaisinespdoseps66', 'saisinespdoseps66' );
			$results = array_values( array_diff( $results, $remove ) );
			sort( $results );

			$regexp = array();
			foreach( $results as $result ) {
				$regexp[] = $result;
				$regexp[] = Inflector::camelize( $result );
				$regexp[] = Inflector::singularize( $result );
				$regexp[] = Inflector::singularize( Inflector::camelize( $result ) );
			}

			$this->out( '\W('.implode( '|', $regexp ).')\W' );

			$this->_stop( self::SUCCESS );
		}
	}
?>