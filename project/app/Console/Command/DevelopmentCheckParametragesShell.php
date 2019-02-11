<?php
	/**
	 * Code source de la classe DevelopmentCheckParametragesShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppShell', 'Console/Command' );

	/**
	 * La classe DevelopmentCheckParametragesShell permet de parcourir les modèles
	 * de l'application et de vérifier si le champ correspondant au displayField
	 * est NOT NULL en base de données et s'il possède un index unique.
	 *
	 * @package app.Console.Command
	 */
	class DevelopmentCheckParametragesShell extends AppShell
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
		 * Description courte du shell
		 *
		 * @var string
		 */
		public $description = 'Ce shell parcourt les modèles de l\'application et vérifie si le champ correspondant au displayField est NOT NULL en base de données et s\'il possède un index unique.';

		/**
		 * Liste des sous-commandes et de leur description.
		 *
		 * @var array
		 */
		public $commands = array();

		/**
		 * Méthode principale.
		 */
		public function main() {
			$modelNames = App::objects( 'model' );
			sort( $modelNames );

			foreach($modelNames as $modelName) {
				if( 'AppModel' !== $modelName ) {
					try {
						$model = ClassRegistry::init( $modelName );
					} catch( Exception $e ) {
						$model = null;
					}

					if( false === empty( $model ) ) {

						if( false === $model->useTable ) {
							$this->out( sprintf( 'Le modèle <info>%s</info> n\'utilise pas de <info>table</info>', $modelName ) );
						}
						else if( $model->displayField === $model->primaryKey ) {
							$this->out( sprintf( 'Le modèle <info>%s</info> n\'utilise pas <info>displayField</info>', $modelName ) );
						}
						else {
							$this->out( sprintf( 'Analyse de la table <info>%s</info>', $model->useTable ) );

							$displayField = $model->displayField;
							$schema = $model->schema();

							if( true === isset( $schema[$displayField] ) ) {
								if( true === $schema[$displayField]['null'] ) {
									$this->err( sprintf( "\tle champ <error>%s</error> de la table <error>%s</error> n'est pas NOT NULL", $displayField, $model->useTable ) );
									$this->err( sprintf( "\t<info>ALTER TABLE %s ALTER COLUMN %s SET NOT NULL;</info>", $model->useTable, $displayField ) );
								}
							}
							else {
								$displayField = false;
							}

							// Index unique sur la colonne displayField ?
							if( false !== $displayField ) {
								$found = array();
								$indexes = $model->getDataSource()->index( $model );
								foreach( $indexes as $name => $index ) {
									if( 'PRIMARY' !== $name && true === $index['unique'] && true === in_array( $displayField, (array)$index['column'] ) ) {
										$found[] = (array)$index['column'];
									}
								}

								if( true === empty( $found ) ) {
									$this->err( sprintf( "\tla table <error>%s</error> ne possède pas d'index unique sur le champ <error>%s</error>", $model->useTable, $displayField ) );
									$this->err( sprintf( "\t<info>DROP INDEX IF EXISTS %s_%s_idx;</info>", $model->useTable, $displayField ) );
									$this->err( sprintf( "\t<info>CREATE UNIQUE INDEX %s_%s_idx ON %s(%s);</info>", $model->useTable, $displayField, $model->useTable, $displayField ) );
								}
							}
						}
					}
				}
			}

			$this->_stop( self::SUCCESS );
		}
	}
?>