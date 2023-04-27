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
		 * Ajout des options proposées à la vue selon l'action mise en paramètre
		 *
		 * @param string
		 * @return array
		*/
		protected function _getViewOptions($action) {
			$departement = Configure::read( 'Cg.departement' );
			$options = array();

			if($action == 'index') {
				$options = $this->Structurereferente->enums();
				if($departement == 93) {
					$options['Structurereferente']['communautesr_id'] = $this->Structurereferente->Communautesr->find( 'list' );
				}
			}elseif( in_array($action, array('edit', 'add') ) ) {
				$options = $this->viewVars['options'];
				$options['Zonegeographique']['Zonegeographique'] = $this->Structurereferente->Zonegeographique->find( 'list' );
			}

			if( Configure::read('Module.Sectorisation.enabled') == true ) {
				$options['Structurereferente']['actif_sectorisation'] = array(
					__m("Structurereferente.actif_sectorisation.false"),
					__m("Structurereferente.actif_sectorisation.true")
				);
			}

			if( Configure::read('Orientation.validation.enabled') == true ) {
				$options['Structurereferente']['workflow_valid'] = array(
					__m("Structurereferente.workflow_valid.false"),
					__m("Structurereferente.workflow_valid.true")
				);
			}

			$options['Structurereferente']['export_donnees'] = array(
				__m("Structurereferente.export_donnees.false"),
				__m("Structurereferente.export_donnees.true")
			);

			$options['Structurereferente']['typeorient_id'] = $this->InsertionsBeneficiaires->typesorients( array( 'conditions' => array() ) );
			$options['Structurereferente']['dreesorganisme_id'] = $this->InsertionsBeneficiaires->dreesorganismes( array( 'conditions' => array() ) );

			return $options;
		}

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

			$options = $this->_getViewOptions($this->action);

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

			$options = $this->_getViewOptions($this->action);

			$this->set( compact( 'options' ) );
		}
	}

?>