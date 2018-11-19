<?php
	/**
	 * Code source de la classe VaguesdorientationsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe VaguesdorientationsController s'occupe du paramétrage des zones
	 * géographiques.
	 *
	 * @package app.Controller
	 */
	class VaguesdorientationsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Vaguesdorientations';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Vaguedorientation' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Vaguesdorientations:edit'
		);

		/**
		 * Liste des vagues
		 */
		public function index() {
			$query = array(
				'limit' => 1000,
				'maxLimit' => 1001,
				'order by'=>'datedebut asc'
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'une vague.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );
		}

		/**
		 * Formulaire de modification d'une vague.
		 *
		 * @param integer $id
		 */
		public function add( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );
		}
	}
?>