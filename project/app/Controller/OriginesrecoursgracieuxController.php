<?php
	/**
	 * Code source de la classe Originesrecoursgracieux.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe OriginesrecoursgracieuxController s'occupe du paramétrage des origines des recours gracieux.
	 *
	 * @package app.Controller
	 */
	class OriginesrecoursgracieuxController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Originesrecoursgracieux';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Originerecoursgracieux' );

		/**
		 * Liste des articles
		 *
		 */
		public function index() {
			$query = array();
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'un article
		 *
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );
			$options = $this->viewVars['options'];

			$this->set( compact( 'options' ) );
		}
	}
