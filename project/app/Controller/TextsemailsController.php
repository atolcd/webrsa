<?php
	/**
	 * Code source de la classe TextsemailsController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe TextsemailsController s'occupe du paramétrage des
	 * modèles de mails.
	 *
	 * @package app.Controller
	 */
	class TextsemailsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Textsemails';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Textemail' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'view' => 'Textsemails:index',
		);

		/**
		 * Surcharge du formulaire de modification d'un modèle de mail pour que
		 * l'enregistrement soit actif par défaut lors d'un ajout.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			if( empty( $this->request->data ) ) {
				$this->request->data[$this->modelClass]['actif'] = true;
			}
		}
	}
?>