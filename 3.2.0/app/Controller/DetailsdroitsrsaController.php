<?php
	/**
	 * Code source de la classe DetailsdroitsrsaController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe DetailsdroitsrsaController ...
	 *
	 * @package app.Controller
	 */
	class DetailsdroitsrsaController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Detailsdroitsrsa';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Jetons2',
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
			'Detailcalculdroitrsa',
			'Detaildroitrsa',
			'Dossier',
			'Option',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array();

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

		/**
		 *
		 */
		public function beforeFilter() {
			parent::beforeFilter();
			$this->set( 'topsansdomfixe', ClassRegistry::init('Detaildroitrsa')->enum('topsansdomfixe') );
			$this->set( 'oridemrsa', ClassRegistry::init('Detaildroitrsa')->enum('oridemrsa') );
			$this->set( 'topfoydrodevorsa', ClassRegistry::init('Detaildroitrsa')->enum('topfoydrodevorsa') );
			$this->set( 'natpf', ClassRegistry::init('Detailcalculdroitrsa')->enum('natpf') );
			$this->set( 'sousnatpf', ClassRegistry::init('Detailcalculdroitrsa')->enum('sousnatpf') );
		}

		/**
		 *
		 * @param integer $dossier_id
		 */
		public function index( $dossier_id = null ){
			// Vérification du format de la variable
			$this->assert( valid_int( $dossier_id ), 'error404' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$detaildroitrsa = $this->Detaildroitrsa->find(
				'first',
				array(
					'conditions' => array(
						'Detaildroitrsa.dossier_id' => $dossier_id
					),
					'contain' => array( 'Detailcalculdroitrsa' )
				)
			) ;

			// Assignations à la vue
			$this->set( 'dossier_id', $dossier_id );
			$this->set( 'detaildroitrsa', $detaildroitrsa );
		}
	}

?>