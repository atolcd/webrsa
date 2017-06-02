<?php
	/**
	 * Code source de la classe Motifsrupturescuis66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
    App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Motifsrupturescuis66Controller s'occupe du paramétrage des
	 * motifs de rupture du CUI pour le CD 66.
	 *
	 * @package app.Controller
	 */
	class Motifsrupturescuis66Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Motifsrupturescuis66';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Motifrupturecui66' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Motifsrupturescuis66:edit'
		);

		/**
		 * Surcharge du formulaire de modification d'un motif de rupture pour que
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