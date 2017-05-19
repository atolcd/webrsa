<?php
	/**
	 * Code source de la classe DevelopmentCheckAutovalidateShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppShell', 'Console/Command' );
	App::uses( 'ConnectionManager', 'Model' );

	/**
	 * La classe DevelopmentCheckAutovalidateShell ...
	 *
	 * @package app.Console.Command
	 */
	class DevelopmentCheckAutovalidateShell extends AppShell
	{

		/**
		 * La constante à utiliser dans la méthode _stop() en cas de succès.
		 */
		const SUCCESS = 0;

		/**
		 * La constante à utiliser dans la méthode _stop() en cas d'erreur.
		 */
		const ERROR = 1;

		protected function _data( Model $Model, $value ) {
			$fieldNames = array_keys( $Model->schema() );
			array_remove( $fieldNames, $Model->primaryKey );

			return array(
				$Model->alias => array_combine(
					$fieldNames,
					array_fill( 0, count( $fieldNames ), $value )
				)
			);
		}

		protected function _traitement($modelName) {
			App::uses( $modelName, 'Model' );
			if( 'AppModel' === $modelName ) {
				return true;
			}

			$Reflection = new ReflectionClass( $modelName );

			if( true === $Reflection->isAbstract() ) {
				return true;
			}

			// @fixme: vérifier le datasource
			$Model = ClassRegistry::init( $modelName );

			if( false === $Model->useTable ) {
				return true;
			}

			$success = true;
			$values = array( null, 1, 'X' );

			foreach($values as $value) {
				$data = $this->_data( $Model, $value );

				$Model->begin();
				try {
					$Model->create($data);
					$Model->save( null, array( 'atomic' => false ) );
					$this->out( sprintf( 'OK: %s', $Model->alias ), 1, Shell::VERBOSE );
				} catch( Exception $e ) {
					$success = false;

					$message = $e->getMessage();
					$queryString = isset( $e->queryString ) ? $e->queryString : null;

					$debug = (
						0 !== strpos( $message, 'SQLSTATE[23503]: Foreign key violation' )
						&& 0 !== strpos( $queryString, 'SELECT ' )
					);

					if( $debug ) {
						$message = sprintf(
							'Erreur: %s: %s %s',
							$Model->alias,
							var_export( $Model->data, true ),
							var_export( $message, true )
						);
						$this->err( $message );
						debug($Model->validate);
						$this->out();
					}
				}
				$Model->rollback();
			}

			return $success;
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			$success = true;

			$modelNames = App::objects( 'Model' );
			foreach( $modelNames as $modelName ) {
				$success = $this->_traitement( $modelName ) && $success;
			}

			$this->_stop( true === $success ? self::SUCCESS : self::ERROR );
		}
	}
?>