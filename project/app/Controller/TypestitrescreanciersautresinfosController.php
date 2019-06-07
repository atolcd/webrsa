<?php
	/**
	 * Code source de la classe Typestitrescreanciersautresinfos.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe TypestitrescreanciersautresinfosController s'occupe du paramétrage des types de titres de recettes autres infos.
	 *
	 * @package app.Controller
	 */
	class TypestitrescreanciersautresinfosController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Typestitrescreanciersautresinfos';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Typetitrecreancierautreinfo' );

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
