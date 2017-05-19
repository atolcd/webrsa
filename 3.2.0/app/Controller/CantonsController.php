<?php
	/**
	 * Code source de la classe CantonsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe CantonsController s'occupe du paramétrage des cantons.
	 *
	 * @package app.Controller
	 */
	class CantonsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cantons';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Search.SearchPrg' => array(
				'actions' => array(
					'index' => array( 'filter' => 'Search' )
				)
			),
			'WebrsaParametrages'
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Canton' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Cantons:edit'
		);

		/**
		 * Liste des tables à ne pas prendre en compte dans les enregistrements
		 * vérifiés pour éviter les suppressions en cascade intempestives.
		 *
		 * @var array
		 */
		public $blacklist = array( 'adresses_cantons' );

		/**
		 * Liste des cantons
		 */
		public function index() {
			if( false === $this->Canton->Behaviors->attached( 'Occurences' ) ) {
				$this->Canton->Behaviors->attach( 'Occurences' );
			}

			// Ajout d'un message lors d'un enregistrement réussi
			$notice = null;
			if( 'success' === $this->Session->read( 'Message.flash.params.class' ) ) {
				$notice = 'Attention, en cas de modifications sur les cantons, il peut être utile de lancer AdresseCantonShell en console pour recalculer les relations entre Adresses et Cantons';
			}

			if( false === empty( $this->request->data ) ) {
				$query = $this->Canton->search( $this->request->data['Search'] );
				$query['fields'][] = $this->Canton->sqHasLinkedRecords( true, $this->blacklist );
				$query['limit'] = 100;
				$this->paginate = $query;
				$results = $this->paginate( 'Canton' );
				$this->set( compact( 'results' ) );
			}

			$options = $this->Canton->enums();
			$options['Canton']['zonegeographique_id'] = $this->Canton->Zonegeographique->find( 'list' );
			$this->set( compact( 'options', 'notice' ) );
		}

		/**
		 * Formulaire de modification d'un canton
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$options['Canton']['zonegeographique_id'] = $this->Canton->Zonegeographique->find( 'list' );
			$this->set( compact( 'options' ) );
		}
	}
?>