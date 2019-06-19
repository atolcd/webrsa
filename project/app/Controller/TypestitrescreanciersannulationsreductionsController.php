<?php
	/**
	 * Code source de la classe Typestitrescreanciersannulationsreductions.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe TypestitrescreanciersannulationsreductionsController s'occupe du paramétrage des types de titres de recettes pour la réduction et l'annulation.
	 *
	 * @package app.Controller
	 */
	class TypestitrescreanciersannulationsreductionsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Typestitrescreanciersannulationsreductions';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Typetitrecreancierannulationreduction' );

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
