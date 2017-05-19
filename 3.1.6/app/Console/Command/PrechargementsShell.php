<?php
	/**
	 * Code source de la classe PrechargementsShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe PrechargementsShell se charge du préchargement du cache de
	 * l'application.
	 *
	 * @package app.Console.Command
	 */
	class PrechargementsShell extends AppShell
	{
		/**
		 * Tâches utilisées par ce shell.
		 *
		 * @var array
		 */
		public $tasks = array( 'XProgressBar' );

		/**
		 * Modèle utilisé par ce shell.
		 *
		 * @var array
		 */
		public $uses = array( 'Prechargement' );

		/**
		 *
		 * @param CakeEvent $Event
		 */
		public function eventListener( CakeEvent $Event ) {
			$eventName = $Event->name();

			if( $eventName == 'Model.Prechargement.preloadTables' ) {
				$this->out( 'Préchargement des tables' );
			}
			else if( $eventName == 'Model.Prechargement.preloadModels' ) {
				$this->out( 'Préchargement des modèles' );
				$this->XProgressBar->start( $Event->data[0] );
			}
			else if( $eventName == 'Model.Prechargement.preloadModel.begin' ) {
				$message = sprintf( "modèle %s", $Event->data[0] );
				$this->XProgressBar->next( 1, $message );
			}
		}

		/**
		 * Préchargement de l'application.
		 */
		public function main() {
			Configure::write( 'Cache.disable', false );

			$events = array(
				'Model.Prechargement.preloadTables',
				'Model.Prechargement.preloadModels',
				'Model.Prechargement.preloadModel.begin',
			);
			foreach( $events as $eventName ) {
				CakeEventManager::instance()->attach( array( $this, 'eventListener' ), $eventName );
			}

			$cache = $this->Prechargement->preloadCache();

			$this->out( 'Modèles préchargés' );
			$models = Hash::extract( $cache, 'Prechargement.{n}[type=model]' );
			foreach( $models as $model ) {
				$count = count( $model['entries'] );
				$msg = "\t{$model['title']}: {$count}";

				if( !$model['error'] ) {
					$this->out( $msg );
				}
				else {
					$this->err( $msg );
				}
			}

			$this->out();

			$this->out( 'Locales préchargées' );
			$locales = Hash::extract( $cache, 'Prechargement.{n}[type=locale]' );
			foreach( $locales as $locale ) {
				$count = count( $locale['entries'] );
				$msg = "\t{$locale['title']}: {$count}";

				if( !$locale['error'] ) {
					$this->out( $msg );
				}
				else {
					$this->err( $msg );
				}
			}

			$this->_stop( self::SUCCESS );
		}

		/**
		 * Paramétrages et aides du shell.
		 */
		/*public function getOptionParser() {
			$Parser = parent::getOptionParser();
			return $Parser;
		}*/
	}
?>