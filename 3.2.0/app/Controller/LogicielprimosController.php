<?php
	/**
	 * Code source de la classe LogicielprimosController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe LogicielprimosController s'occupe du paramétrage des
	 * logiciels utilisés pour la primo analyse des fiches de liaison.
	 * de fiches de liaison.
	 *
	 * @package app.Controller
	 */
	class LogicielprimosController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Logicielprimos';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Logicielprimo' );

		/**
		 * Surcharge du formulaire de modification d'un logiciel de primo analyse
		 * pour que l'enregistrement soit actif par défaut lors d'un ajout.
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
