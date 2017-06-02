<?php
	/**
	 * Code source de la classe Questionspcgs66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Questionspcgs66Controller s'occupe du paramétrage des questions PCG.
	 *
	 * @package app.Controller
	 */
	class Questionspcgs66Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Questionspcgs66';

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Questionspcgs66:edit'
		);

		/**
		 * Liste des questions PCG
		 */
		public function index() {
			$query = array(
				'contain' => array(
					'Compofoyerpcg66.name',
					'Decisionpcg66.name'
				)
			);
			$this->WebrsaParametrages->index( $query );

			$erreurs = array(
				'Merci d\'ajouter au moins une décision avant d\'ajouter une EP.' => 0 == $this->Questionpcg66->Decisionpcg66->find( 'count' ),
				'Merci d\'ajouter au moins une composition de foyer avant d\'ajouter une EP.' => 0 == $this->Questionpcg66->Compofoyerpcg66->find( 'count' )
			);
			$this->set( compact( 'erreurs' ) );
		}

		/**
		 * Formulaire de modification d'une question PCG.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$query = array(
				'contain' => array(
					'Compofoyerpcg66.name',
					'Decisionpcg66.name'
				)
			);
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit', 'query' => $query ) );

			$options = $this->viewVars['options'];
			$options['Questionpcg66']['compofoyerpcg66_id'] = $this->Questionpcg66->Compofoyerpcg66->find( 'list' );
			$options['Questionpcg66']['decisionpcg66_id'] = $this->Questionpcg66->Decisionpcg66->find( 'list' );
			$this->set( compact( 'options' ) );
		}
	}
?>