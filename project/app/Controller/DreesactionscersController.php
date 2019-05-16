<?php
	/**
	 * Code source de la classe DreesactionscersController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe DreesDreesactionscersController s'occupe du paramétrage des Dreesactionscers DREES liés au Statistiques DREES.
	 *
	 * @package app.Controller
	 */
	class DreesactionscersController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Dreesactionscers';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Dreesactionscer' );

		/**
		 * Liste des Dreesactionscers
		 *
		 */
		public function index() {
			$query = array();
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'un Dreesactionscers
		 *
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );
			$options = $this->viewVars['options'];
			$this->set( compact( 'options' ) );
		}
	}
?>