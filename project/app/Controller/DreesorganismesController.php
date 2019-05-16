<?php
	/**
	 * Code source de la classe DreesorganismesController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe DreesDreesorganismesController s'occupe du paramétrage des Dreesorganismes DREES liés au Statistiques DREES.
	 *
	 * @package app.Controller
	 */
	class DreesorganismesController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Dreesorganismes';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Dreesorganisme' );

		/**
		 * Requête à utiliser pour lister les enregistrements par parent puis par
		 * ordre alphabétique.
		 *
		 * @return array
		 */
		protected function _query() {
			$query = array(
				'fields' => array_merge(
					$this->Dreesorganisme->fields(),
					array( 'Parent.lib_dreesorganisme' )
				),
				'recursive' => -1,
				'joins' => array(
					$this->Dreesorganisme->join( 'Parent', array( 'type' => 'LEFT OUTER' ) )
				),
				'order' => array(
					'( CASE WHEN Dreesorganisme.parentid IS NULL THEN Dreesorganisme.id ELSE Dreesorganisme.parentid END) ASC',
					'( CASE WHEN Dreesorganisme.parentid IS NULL THEN 0 ELSE 1 END) ASC',
					'Dreesorganisme.lib_dreesorganisme ASC'
				)
			);

			return $query;
		}

		/**
		 * Liste des Dreesorganismes
		 *
		 */
		public function index() {
			if( false === $this->Dreesorganisme->Behaviors->attached( 'Occurences' ) ) {
				$this->Dreesorganisme->Behaviors->attach( 'Occurences' );
			}

			$query = $this->_query();

			$query['fields'] = array_merge(
				$query['fields'],
				 array( $this->Dreesorganisme->sqHasLinkedRecords() )
			);

			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'un Dreesorganisme
		 *
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );
			$options = $this->viewVars['options'];

			$options['Dreesorganisme']['parentid'] = $this->Dreesorganisme->find(
				'list',
				array(
					'fields' => array(
						'Dreesorganisme.id',
						'Dreesorganisme.lib_dreesorganisme',
					),
					'conditions' => array(
						'Dreesorganisme.parentid' => null,
						'Dreesorganisme.id <>' => $id
					),
					'recursive' => -1
				)
			);

			$this->set( compact( 'options' ) );
		}
	}
?>