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
			'calculrejetes',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read',
			'calculrejetes' => 'read',
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

		public function calculrejetes () {
			$this->loadModel ('Visionneuse');
			$visionneuses = $this->Visionneuse->query ('SELECT * FROM administration.visionneuses;');

			foreach ($visionneuses as $visionneuse) {
				$query = '
					SELECT COUNT(*) AS nombre
					FROM administration.rejet_historique
					WHERE administration.rejet_historique.fic = \''.$visionneuse['0']['nomfic'].'\';';
				$nbRejete = $this->Visionneuse->query ($query);

				$update = '
					UPDATE administration.visionneuses
					SET nbrejete = '.$nbRejete[0][0]['nombre'].'
					WHERE id = '.$visionneuse['0']['id'];

				$this->Visionneuse->query ($update);
			}

			$this->redirect( array( 'controller' => 'visionneuses', 'action' => 'index' ) );
		}
	}
?>