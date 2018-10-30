<?php
	/**
	 * Code source de la classe CreancesController.
	 *
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe CreancesController ...
	 *
	 * @package app.Controller
	 */
	class RapportsTalendsCreancesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'RapportsTalendsCreances';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			//'WebrsaAccesses'
			);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read'
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'RapportTalendCreance'
		);

		/**
		 * Pagination sur les <éléments> de la table.
		 */
		public function index() {

			$RapportsTalendsCreances = $this->RapportTalendCreance->find(
			    'all',
			    array(
					//'fields' => '',
					'order' => array(
						'RapportTalendCreance.dtexec DESC',
					),
					'contain' => FALSE
				)
			);

			$this->set( 'RapportsTalendsCreances', $RapportsTalendsCreances );

		}

	}
?>
