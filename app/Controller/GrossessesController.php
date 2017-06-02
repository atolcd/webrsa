<?php
	/**
	 * Code source de la classe GrossessesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe GrossessesController ...
	 *
	 * @package app.Controller
	 */
	class GrossessesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Grossesses';

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
			'Grossesse',
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
			'view' => 'Grossesses:index',
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
			$this->set( 'topressevaeti', ClassRegistry::init('Informationeti')->enum('topressevaeti') );
			$this->set( 'natfingro', ClassRegistry::init('Grossesse')->enum('natfingro') );
		}

		/**
		 *
		 * @param integer $personne_id
		 */
		public function index( $personne_id = null ){
			$this->assert( valid_int( $personne_id ), 'error404' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$grossesse = $this->Grossesse->find(
				'first',
				array(
					'conditions' => array(
						'Grossesse.personne_id' => $personne_id
					),
					'recursive' => -1
				)
			) ;

			// Assignations à la vue
			$this->set( 'personne_id', $personne_id );
			$this->set( 'grossesse', $grossesse );
		}

		/**
		 *
		 * @param integer $grossesse_id
		 */
		public function view( $grossesse_id = null ) {
			$this->assert( valid_int( $grossesse_id ), 'error404' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Grossesse->personneId( $grossesse_id ) ) ) );

			$grossesse = $this->Grossesse->find(
				'first',
				array(
					'conditions' => array(
						'Grossesse.id' => $grossesse_id
					),
				'recursive' => -1
				)
			);

			$this->assert( !empty( $grossesse ), 'error404' );

			// Assignations à la vue
			$this->set( 'personne_id', $grossesse['Grossesse']['personne_id'] );
			$this->set( 'grossesse', $grossesse );
		}
	}
?>