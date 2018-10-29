<?php
	/**
	 * Code source de la classe EtatstitrescreanciersController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
    App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe EtatstitrescreanciersController s'occupe du paramétrage des etats des
	 * titres creanciers
	 *
	 * @package app.Controller
	 */
	class EtatstitrescreanciersController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Etatstitrescreanciers';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Etattitrecreancier' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Etatstitrescreanciers:edit'
		);

		/**
		 * Surcharge du formulaire de modification d'un motif de refus pour que
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