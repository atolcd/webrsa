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
			// Suppression du cache et calcul du nombre de fichiers supprimés
			$nbFichiersAvant = exec ('ls '. APP . 'tmp/cache/ -1 | wc -l');
			$this->deleteCache();
			$nbFichiersApres = exec ('ls '. APP . 'tmp/cache/ -1 | wc -l');
			$nbFichiersSupprimes = $nbFichiersAvant - $nbFichiersApres;

			// Gestion du message de sortie
			$message = __d('cache', 'Cache.noFile');
			if($nbFichiersSupprimes > 0) {
				$message = sprintf(__d('cache', 'Cache.success'), $nbFichiersSupprimes);
			}

			$this->view = null;
			$this->Flash->notice( $message );

			$this->redirect( array( 'controller' => 'accueils', 'action' => 'index' ) );
		}

	}