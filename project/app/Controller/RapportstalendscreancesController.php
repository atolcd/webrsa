<?php
	/**
	 * Code source de la classe RapportstalendscreancesController.
	 *
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe RapportstalendscreancesController ...
	 *
	 * @package app.Controller
	 */
	class RapportstalendscreancesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Rapportstalendscreances';

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
			'Paginator',
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
			'Rapporttalendcreance',
			'Rejettalendcreance'
		);

		/**
		 * Pagination sur les <éléments> de la table.
		 */
		public function index() {
			$this->set( 'options', $this->Rapporttalendcreance->enums() );

			$query =  array(
					'fields' => $this->Rapporttalendcreance->fields(),
					'order' => array(
						'Rapporttalendcreance.dtexec DESC',
					),
					'contain' => FALSE,
					'limit' => 20,
				) ;
			$this->paginate = $query;
			$Rapportstalendscreances = $this->paginate( 'Rapporttalendcreance' );
			
			$this->set( 'Rapportstalendscreances', $Rapportstalendscreances );
		}
	}
?>
