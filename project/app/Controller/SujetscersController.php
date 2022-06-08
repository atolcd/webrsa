<?php
	/**
	 * Code source de la classe SujetscersController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe SujetscersController s'occupe du paramétrage des types d'actions
	 * d'insertion.
	 *
	 * @package app.Controller
	 */
	class SujetscersController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Sujetscers';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Sujetcer' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Sujetscers:edit'
		);

		/**
		 * Liste des types d'actions d'insertion
		 */
		public function index() {
			$query = array(
				'limit' => 1000,
				'maxLimit' => 1001
			);
			$this->WebrsaParametrages->index( $query );
		}
	}
?>