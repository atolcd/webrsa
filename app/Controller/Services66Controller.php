<?php
	/**
	 * Code source de la classe Services66ControllerController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Services66ControllerController s'occupe du paramétrage des
	 * services du CD 66.
	 *
	 * @package app.Controller
	 */
	class Services66Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Services66';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Service66' );

		/**
		 * Surcharge du formulaire de modification d'un service pour que
		 * l'enregistrement ne soit pas interne et soit actif par défaut lors
		 * d'un ajout.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			if( empty( $this->request->data ) ) {
				$this->request->data[$this->modelClass]['interne'] = 0;
				$this->request->data[$this->modelClass]['actif'] = true;
			}
		}
	}
?>