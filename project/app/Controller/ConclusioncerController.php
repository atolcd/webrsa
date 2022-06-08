<?php
	/**
	 * Code source de la classe ConclusioncerController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe ConclusioncerController s'occupe du paramétrage des types d'actions
	 * d'insertion.
	 *
	 * @package app.Controller
	 */
	class ConclusioncerController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Conclusioncer';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Conclusioncer' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Conclusioncer:edit'
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