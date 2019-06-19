<?php
	/**
	 * Code source de la classe Motifsemissionstitrescreanciers.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe MotifsemissionstitrescreanciersController s'occupe du paramétrage des motifs d'émission des titres de recettes.
	 *
	 * @package app.Controller
	 */
	class MotifsemissionstitrescreanciersController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Motifsemissionstitrescreanciers';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Motifemissiontitrecreancier' );

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
