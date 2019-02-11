<?php
	/**
	 * Code source de la classe FichiersmodulesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe FichiersmodulesController ...
	 *
	 * @package app.Controller
	 */
	class FichiersmodulesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Fichiersmodules';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(

		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(

		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Fichiermodule'
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(

		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(

		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'delete' => 'delete',
		);

		/**
		 * Suppression du fichiers préalablement associés à un traitement donné
		 *
		 * @param integer $fichiermodule_id
		 */
		public function delete( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );

			if( $this->Fichiermodule->delete( $fichiermodule_id ) ) {
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->redirect( $this->referer() );
		}
	}
?>