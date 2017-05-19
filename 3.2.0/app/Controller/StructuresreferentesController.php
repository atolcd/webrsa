<?php
	/**
	 * Code source de la classe StructuresreferentesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe StructuresreferentesController s'occupe du paramétrage des
	 * structures référentes.
	 *
	 * @package app.Controller
	 */
	class StructuresreferentesController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Structuresreferentes';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'InsertionsBeneficiaires',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
				)
			),
			'WebrsaParametrages'
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Structurereferente',
			'WebrsaStructurereferente'
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Structuresreferentes:edit'
		);

		/**
		 * Moteur de recherche par structures référentes
		 */
		public function index() {
			$search = (array)Hash::get( $this->request->data, 'Search' );
			if( !empty( $search ) ) {
				$query = $this->WebrsaStructurereferente->search( $search );
				$query['limit'] = 20;
				$this->paginate = $query;
				$results = $this->paginate( 'Structurereferente', array(), array(), !Hash::get($search, 'Pagination.nombre_total') );
				$this->set( compact( 'results' ) );
			}

			$departement = (int)Configure::read( 'Cg.departement' );
			$options = $this->Structurereferente->enums();
			$options['Structurereferente']['typeorient_id'] = $this->InsertionsBeneficiaires->typesorients( array( 'conditions' => array() ) );
			if( 93 === $departement ) {
				$options['Structurereferente']['communautesr_id'] = $this->Structurereferente->Communautesr->find( 'list' );
			}
			$this->set( compact( 'options' ) );
		}

		/**
		 * Formulaire de modification d'une structure référente
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$params = array(
				'query' => array(
					'contain' => 'Zonegeographique'
				),
				'view' => 'add_edit'
			);
			$this->WebrsaParametrages->edit( $id, $params );

			$options = $this->viewVars['options'];
			$options['Structurereferente']['typeorient_id'] = $this->InsertionsBeneficiaires->typesorients( array( 'conditions' => array() ) );
			$options['Zonegeographique']['Zonegeographique'] = $this->Structurereferente->Zonegeographique->find( 'list' );
			$this->set( compact( 'options' ) );
		}
	}

?>