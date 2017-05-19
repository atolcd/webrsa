<?php
	/**
	 * Code source de la classe PermanencesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe PermanencesController ...
	 *
	 * @package app.Controller
	 */
	class PermanencesController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Permanences';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Permanence' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Permanences:edit'
		);

		/**
		 * Liste des permanences
		 */
		public function index() {
			if( false === $this->Permanence->Behaviors->attached( 'Occurences' ) ) {
				$this->Permanence->Behaviors->attach( 'Occurences' );
			}

			$query = array(
				'fields' => array_merge(
					$this->Permanence->fields(),
					array( $this->Permanence->sqHasLinkedRecords() ),
					$this->Permanence->Structurereferente->fields()
				),
				'joins' => array(
					$this->Permanence->join( 'Structurereferente', array( 'type' => 'INNER' ) )
				),
				'limit' => 1000,
				'maxLimit' => 1001
			);
			$this->WebrsaParametrages->index( $query );
			$options = $this->Permanence->enums();
			$this->set( compact( 'options' ) );
		}

		/**
		 * Formulaire de modification d'une permanence.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$options['Permanence']['structurereferente_id'] = $this->Permanence->Structurereferente->find( 'list' );
			$this->set( compact( 'options' ) );
		}
	}
?>