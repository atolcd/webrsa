<?php
	/**
	 * Code source de la classe ValeurstagsController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe ValeurstagsController s'occupe du paramétrage des tags.
	 *
	 * @package app.Controller
	 */
	class ValeurstagsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Valeurstags';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Valeurtag' );

		/**
		 * Liste des tags.
		 *
		 * @todo final
		 */
		public function index() {
			$query = array(
				'contain' => array(
					'Categorietag.name'
				)
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'un tag.
		 *
		 * @todo final
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$options['Valeurtag']['categorietag_id'] = $this->Valeurtag->Categorietag->find('list');
			$this->set( compact( 'options' ) );
		}
	}
?>