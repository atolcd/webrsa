<?php
	/**
	 * Code source de la classe CachesController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe CachesController ...
	 *
	 * @package app.Controller
	 */
	class CachesController extends AppController
	{
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array();

		/**
		 * Vide le cache de l'application
		 */
		public function reinitializeCache()
		{
			$nbFichiers = exec ('ls '. APP . 'tmp/cache/ -1 | wc -l');
			$this->deleteCache();

			$this->view = null;
			$this->Flash->success( sprintf(__d('cache', 'Cache.success'), $nbFichiers ) );
			$this->redirect( array( 'controller' => 'accueils', 'action' => 'index' ) );
		}

	}