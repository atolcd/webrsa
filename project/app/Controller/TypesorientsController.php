<?php
	/**
	 * Code source de la classe TypesorientsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe TypesorientsController permet de réaliser le paramétrage des
	 * types d'orientation.
	 *
	 * @package app.Controller
	 */
	class TypesorientsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Typesorients';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Typeorient', 'Exceptionimpressiontypeorient' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Typesorients:edit',
		);

		/**
		 * Requête à utiliser pour lister les enregistrements par parent puis par
		 * ordre alphabétique.
		 *
		 * @return array
		 */
		protected function _query() {
			$query = array(
				'fields' => array_merge(
					$this->Typeorient->fields(),
					array( 'Parent.lib_type_orient' )
				),
				'recursive' => -1,
				'joins' => array(
					$this->Typeorient->join( 'Parent', array( 'type' => 'LEFT OUTER' ) )
				),
				'order' => array(
					'( CASE WHEN Typeorient.parentid IS NULL THEN Typeorient.id ELSE Typeorient.parentid END) ASC',
					'( CASE WHEN Typeorient.parentid IS NULL THEN 0 ELSE 1 END) ASC',
					'Typeorient.lib_type_orient ASC'
				)
			);

			return $query;
		}

		/**
		 * Liste des types d'orientations.
		 */
		public function index() {
			if( false === $this->Typeorient->Behaviors->attached( 'Occurences' ) ) {
				$this->Typeorient->Behaviors->attach( 'Occurences' );
			}

			$query = $this->_query();
			$query['fields'] = array_merge(
				$query['fields'],
				array( $this->Typeorient->sqHasLinkedRecords() ),
				$this->Typeorient->sqHasException()
			);

			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'un type d'orientation
		 *
		 * @param integer $id L'id de l'enregistrement à modifier
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$options['Typeorient']['parentid'] = $this->Typeorient->find(
				'list',
				array(
					'fields' => array(
						'Typeorient.id',
						'Typeorient.lib_type_orient',
					),
					'conditions' => array(
						'Typeorient.parentid' => null,
						'Typeorient.id <>' => $id
					),
					'recursive' => -1
				)
			);

			$query = $this->_query();
			$query['fields'] = array_merge(
				$query['fields'],
				$this->Typeorient->sqHasException()
			);
			$typesorients = $this->Typeorient->find( 'all', $query );
			$exceptions = $this->Exceptionimpressiontypeorient->getByTypeOrient($id);
			$dernier_id = $this->Exceptionimpressiontypeorient->getDernierId($exceptions);
			$premier_id = $this->Exceptionimpressiontypeorient->getPremierId($exceptions);
			$this->set( compact( 'options', 'typesorients', 'exceptions', 'id', 'dernier_id', 'premier_id' ) );
		}
	}
?>