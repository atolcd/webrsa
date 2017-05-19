<?php
	/**
	 * Code source de la classe SituationsdossiersrsaController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe SituationsdossiersrsaController ...
	 *
	 * @package app.Controller
	 */
	class SituationsdossiersrsaController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Situationsdossiersrsa';

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
			'Situationdossierrsa',
			'Dossier',
			'Option',
			'Suspensiondroit',
			'Suspensionversement',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'view' => 'Situationsdossiersrsa:index',
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
			$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa') );
			$this->set( 'moticlorsa', ClassRegistry::init('Situationdossierrsa')->enum('moticlorsa') );
			$this->set( 'motisusdrorsa', ClassRegistry::init('Suspensiondroit')->enum('motisusdrorsa') );
			$this->set( 'motisusversrsa', ClassRegistry::init('Suspensionversement')->enum('motisusversrsa') );
		}

		/**
		 *
		 * @param integer $dossier_id
		 */
		public function index( $dossier_id = null ){
			// Vérification du format de la variable
			$this->assert( valid_int( $dossier_id ), 'error404' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$situationdossierrsa = $this->Situationdossierrsa->find(
				'first',
				array(
					'conditions' => array(
						'Situationdossierrsa.dossier_id' => $dossier_id
					),
					'contain' => array(
						'Dossier',
						'Suspensiondroit',
						'Suspensionversement'
					)
				)
			) ;
			// Assignations à la vue
			$this->set( 'dossier_id', $dossier_id );
			$this->set( 'situationdossierrsa', $situationdossierrsa );
		}

		/**
		 *
		 * @param integer $situationdossierrsa_id
		 */
		public function view( $situationdossierrsa_id = null ) {
			// Vérification du format de la variable
			$this->assert( valid_int( $situationdossierrsa_id ), 'error404' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $this->Situationdossierrsa->dossierId( $situationdossierrsa_id ) ) ) );

			$situationdossierrsa = $this->Situationdossierrsa->find(
				'first',
				array(
					'conditions' => array(
						'Situationdossierrsa.id' => $situationdossierrsa_id
					),
				'recursive' => -1
				)
			);
			$this->assert( !empty( $situationdossierrsa ), 'error404' );

			// Assignations à la vue
			$this->set( 'dossier_id', $situationdossierrsa['Situationdossierrsa']['dossier_id'] );
			$this->set( 'situationdossierrsa', $situationdossierrsa );
		}
	}

?>