<?php
	/**
     * Code source de la classe VisionneusesController.
	 * Fait par le CG93
     *
     * PHP 5.3
     *
	 * @author Harry ZARKA <hzarka@cg93.fr>, 2010.
     * @package app.Controller
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe VisionneusesController ...
	 *
	 * @package app.Controller
	 */
	class VisionneusesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Visionneuses';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
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
			'Visionneuse',
			'RejetHistorique',
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
			'index' => 'read',
		);

		public $paginate = array(
			'limit'=>10,
			'order'=>'Visionneuse.dtdeb DESC'
		);

		public function index() {
			$this->Visionneuse->recursive = 0;
			if( empty( $this->request->data ) ) {
				$this->set('visionneuses', $this->paginate());
			}
			else {
				$this->Default->search(
					array(
						'Visionneuse.dtint',
						'Visionneuse.flux'
					)
				);
			}
		}
	}
?>