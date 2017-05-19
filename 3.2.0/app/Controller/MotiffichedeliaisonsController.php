<?php
	/**
	 * Code source de la classe MotiffichedeliaisonsController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe MotiffichedeliaisonsController s'occupe du paramétrage des motifs
	 * de fiches de liaison.
	 *
	 * @package app.Controller
	 */
	class MotiffichedeliaisonsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Motiffichedeliaisons';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Motiffichedeliaison' );

		/**
		 * Surcharge du formulaire de modification d'un motif de fiche de liaison
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