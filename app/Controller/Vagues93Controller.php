<?php
	/**
	 * Code source de la classe ZonesgeographiquesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe ZonesgeographiquesController s'occupe du paramétrage des zones
	 * géographiques.
	 *
	 * @package app.Controller
	 */
	class Vagues93Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Vagues93';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Vague93' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Vagues93:edit'
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