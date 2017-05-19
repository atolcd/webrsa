<?php
	/**
	 * Code source de la classe SuivisinstructionController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe SuivisinstructionController ...
	 *
	 * @package app.Controller
	 */
	class SuivisinstructionController  extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Suivisinstruction';

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
			'Suiviinstruction',
			'Dossier',
			'Option',
			'Serviceinstructeur',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'view' => 'Suivisinstruction:index',
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
			$this->set( 'suiirsa', ClassRegistry::init('Suiviinstruction')->enum('suiirsa') );
			$this->set( 'typeserins', $this->Option->typeserins() );
		}

		/**
		 *
		 * @param integer $dossier_id
		 */
		public function index( $dossier_id = null ) {
			// Vérification du format de la variable
			$this->assert( valid_int( $dossier_id ), 'error404' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			// Recherche des adresses du foyer
			$suivisinstruction = $this->Suiviinstruction->find(
				'all',
				array(
					'conditions' => array( 'Suiviinstruction.dossier_id' => $dossier_id ),
					'recursive' => -1
				)
			);

			// Assignations à la vue
			$this->set( 'dossier_id', $dossier_id );
			$this->set( 'suivisinstruction', $suivisinstruction );
		}

		/**
		 *
		 * @param integer $suiviinstruction_id
		 */
		public function view( $suiviinstruction_id = null ) {
			// Vérification du format de la variable
			$this->assert( valid_int( $suiviinstruction_id ), 'error404' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $this->Suiviinstruction->dossierId( $suiviinstruction_id ) ) ) );

			$suiviinstruction = $this->Suiviinstruction->find(
				'first',
				array(
					'conditions' => array(
						'Suiviinstruction.id' => $suiviinstruction_id
					),
				'recursive' => -1
				)

			);
			$this->assert( !empty( $suiviinstruction ), 'error404' );

			// Assignations à la vue
			$this->set( 'dossier_id', $suiviinstruction['Suiviinstruction']['dossier_id'] );
			$this->set( 'suiviinstruction', $suiviinstruction );
		}
	}

?>