<?php
	/**
	 * Code source de la classe AccompagnementsbeneficiairesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe AccompagnementsbeneficiairesController ...
	 *
	 * @package app.Controller
	 */
	class AccompagnementsbeneficiairesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Accompagnementsbeneficiaires';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'InsertionsBeneficiaires',
			'Jetons2',
			'Workflowscers93',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Search.SearchForm',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Personne',
			'WebrsaAccompagnementbeneficiaire',
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
		 * ...
		 *
		 * @see /accompagnementsbeneficiaires/index/372005
		 * @todo séparer en différentes méthodes (avec $commeDroit) pour les appels Ajax
		 */
		public function index( $personne_id ) {
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$options = $this->WebrsaAccompagnementbeneficiaire->options();

			// 1. Accompagnement
			$query = $this->WebrsaAccompagnementbeneficiaire->qdDetails( array( 'Personne.id' => $personne_id ) );
			$this->Personne->forceVirtualFields = true;
			$details = $this->Personne->find( 'first', $query );

			// 2. Tableaux
			$params = array();
			$userType = $this->Session->read( 'Auth.User.type' );
			if( 0 === strpos( $userType, 'externe_' ) ) {
				$params['structurereferente_id'] = $this->Workflowscers93->getUserStructurereferenteId( false );
			}

			// 2.1 Tableau d'actions
			$actions = $this->WebrsaAccompagnementbeneficiaire->actions( $personne_id, $params );

			// 2.2 Tableau de courriers
			$impressions = $this->WebrsaAccompagnementbeneficiaire->impressions( $personne_id, $params );

			// 2.3 Tableau de fichiers liés
			$fichiersmodules = $this->WebrsaAccompagnementbeneficiaire->fichiersmodules( $personne_id, $params );

			$this->set( compact( 'dossierMenu', 'options', 'details', 'actions', 'impressions', 'fichiersmodules' ) );
		}
	}
?>
