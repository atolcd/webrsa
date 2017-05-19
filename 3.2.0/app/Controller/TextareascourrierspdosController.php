<?php
	/**
	 * Code source de la classe TextareascourrierspdosController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe TextareascourrierspdosController s'occupe du paramétrage des
	 * zones de commentaires supplémentaires pour les courriers d'un traitement
	 * d'une PDO
	 *
	 * @package app.Controller
	 */
	class TextareascourrierspdosController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Textareascourrierspdos';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Textareacourrierpdo' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Textareascourrierspdos:edit'
		);

		/**
		 * Liste des zones de commentaires supplémentaires.
		 */
		public function index() {
			$messages = array();
			if( 0 === $this->Textareacourrierpdo->Courrierpdo->find( 'count' ) ) {
				$msg = 'Merci de renseigner au moins un courrier pour les traitements PDO avant de renseigner une zone de commentaires supplémentaire pour les courriers de traitements PDO.';
				$messages[$msg] = 'error';
			}
			$this->set( compact( 'messages' ) );

			$query = array(
				'contain' => array(
					'Courrierpdo.name'
				)
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'une zone de commentaires supplémentaire.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$options['Textareacourrierpdo']['courrierpdo_id'] = $this->Textareacourrierpdo->Courrierpdo->find( 'list' );
			$this->set( compact( 'options' ) );
		}
	}
?>