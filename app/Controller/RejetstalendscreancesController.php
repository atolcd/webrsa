<?php
	/**
	 * Code source de la classe RejetstalendscreancesController.
	 *
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe RejetstalendscreancesController ...
	 *
	 * @package app.Controller
	 */
	class RejetstalendscreancesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Rejetstalendscreances';

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
			'index' => 'read',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Rapporttalendcreance',
			'Rejettalendcreance',
		);

		/**
		 * Pagination sur les <éléments> de la table.
		 */
		public function index($rapport_id) {
			$this->set( 'optionsRapport', $this->Rapporttalendcreance->enums() );
			$this->set( 'options', $this->Rejettalendcreance->enums() );

			$this->set( 'natpf', ClassRegistry::init('Detailcalculdroitrsa')->enum('natpf') );
			$raportQuery =  array(
					'fields' => $this->Rapporttalendcreance->fields(),
					'order' => array(
						'Rapporttalendcreance.dtexec DESC',
					),
					'conditions' => array( 'Rapporttalendcreance.id' => $rapport_id ),
					'contain' => FALSE
				);
			$Rapportstalendscreances = $this->Rapporttalendcreance->find('all',$raportQuery);

			$rejetQuery =  array(
					'fields' => $this->Rejettalendcreance->fields(),
					'order' => array(
						'Rejettalendcreance.dtimplcre DESC',
					),
					'conditions' => array(
						//'Rejettalendcreance.fichierflux' => $Rapportstalendscreances[0]['Rapporttalendcreance']['fichierflux'],
						'Rejettalendcreance.typeflux' => $Rapportstalendscreances[0]['Rapporttalendcreance']['typeflux'],
						'Rejettalendcreance.natflux' => $Rapportstalendscreances[0]['Rapporttalendcreance']['natflux'],
						'Rejettalendcreance.dtexec' => $Rapportstalendscreances[0]['Rapporttalendcreance']['dtexec'],
						'Rejettalendcreance.dtref' => $Rapportstalendscreances[0]['Rapporttalendcreance']['dtref'],
					),
					'contain' => FALSE
				);
			$Rejetstalendscreances = $this->Rejettalendcreance->find('all',$rejetQuery);

				//$this->paginate = $Rapportstalendscreances;
				//$Rapportstalendscreances = $this->paginate( 'Rapporttalendcreance' );
				/**/

			$this->set( 'Rapportstalendscreances', $Rapportstalendscreances );
			$this->set( 'Rejetstalendscreances', $Rejetstalendscreances );
		}


	}
?>
