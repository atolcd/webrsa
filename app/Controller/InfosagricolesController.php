<?php
	/**
	 * Code source de la classe InfosagricolesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe InfosagricolesController ...
	 *
	 * @package app.Controller
	 */
	class InfosagricolesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Infosagricoles';

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
			'Infoagricole',
			'Option',
			'Personne',
			'Aideagricole',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'view' => 'Infosagricoles:index',
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
			'view' => 'read',
		);

		/**
		 *
		 */
		public function beforeFilter() {
			parent::beforeFilter();
			$this->set( 'regfisagri', ClassRegistry::init('Infoagricole')->enum('regfisagri') );
		}

		/**
		 *
		 * @param integer $personne_id
		 */
		public function index( $personne_id = null ){
			$this->assert( valid_int( $personne_id ), 'error404' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$infoagricole = $this->Infoagricole->find(
				'first',
				array(
					'conditions' => array(
						'Infoagricole.personne_id' => $personne_id
					),
					'contain' => array(
						'Personne',
						'Aideagricole'
					)
				)
			);

			// Assignations à la vue
			$this->set( 'personne_id', $personne_id );
			$this->set( 'infoagricole', $infoagricole );
		}

		/**
		 *
		 * @param integer $infoagricole_id
		 */
		public function view( $infoagricole_id = null ) {
			$this->assert( valid_int( $infoagricole_id ), 'error404' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Infoagricole->personneId( $infoagricole_id ) ) ) );

			$infoagricole = $this->Infoagricole->find(
				'first',
				array(
					'conditions' => array(
						'Infoagricole.id' => $infoagricole_id
					),
				'recursive' => -1
				)

			);
			$this->assert( !empty( $infoagricole ), 'error404' );

			// Assignations à la vue
			$this->set( 'personne_id', $infoagricole['Infoagricole']['personne_id'] );
			$this->set( 'infoagricole', $infoagricole );
		}
	}
?>