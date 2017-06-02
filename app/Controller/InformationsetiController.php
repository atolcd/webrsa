<?php
	/**
	 * Code source de la classe InformationsetiController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe InformationsetiController ...
	 *
	 * @package app.Controller
	 */
	class InformationsetiController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Informationseti';

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
			'Informationeti',
			'Option',
			'Personne',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'view' => 'Informationseti:index',
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
			$this->set( 'topcreaentre', ClassRegistry::init('Informationeti')->enum('topcreaentre') );
			$this->set( 'topaccre', ClassRegistry::init('Informationeti')->enum('topaccre') );
			$this->set( 'acteti', ClassRegistry::init('Informationeti')->enum('acteti') );
			$this->set( 'topempl1ax', ClassRegistry::init('Informationeti')->enum('topempl1ax') );
			$this->set( 'topstag1ax', ClassRegistry::init('Informationeti')->enum('topstag1ax') );
			$this->set( 'topsansempl', ClassRegistry::init('Informationeti')->enum('topsansempl') );
			$this->set( 'regfiseti', ClassRegistry::init('Informationeti')->enum('regfiseti') );
			$this->set( 'topbeneti', ClassRegistry::init('Informationeti')->enum('topbeneti') );
			$this->set( 'regfisetia1', ClassRegistry::init('Informationeti')->enum('regfisetia1') );
			$this->set( 'topevoreveti', ClassRegistry::init('Informationeti')->enum('topevoreveti') );
			$this->set( 'topressevaeti', ClassRegistry::init('Informationeti')->enum('topressevaeti') );
		}

		/**
		 *
		 * @param integer $personne_id
		 */
		public function index( $personne_id = null ){
			$this->assert( valid_int( $personne_id ), 'error404' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$informationeti = $this->Informationeti->find(
				'first',
				array(
					'conditions' => array(
						'Informationeti.personne_id' => $personne_id
					),
					'recursive' => -1
				)
			) ;

			// Assignations à la vue
			$this->set( 'personne_id', $personne_id );
			$this->set( 'informationeti', $informationeti );
		}

		/**
		 *
		 * @param integer $informationeti_id
		 */
		public function view( $informationeti_id = null ) {
			$this->assert( valid_int( $informationeti_id ), 'error404' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Informationeti->personneId( $informationeti_id ) ) ) );

			$informationeti = $this->Informationeti->find(
				'first',
				array(
					'conditions' => array(
						'Informationeti.id' => $informationeti_id
					),
				'recursive' => -1
				)
			);
			$this->assert( !empty( $informationeti ), 'error404' );

			// Assignations à la vue
			$this->set( 'personne_id', $informationeti['Informationeti']['personne_id'] );
			$this->set( 'informationeti', $informationeti );
		}
	}

?>