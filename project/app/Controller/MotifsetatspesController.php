<?php
	/**
	 * Code source de la classe MotifsetatspesController.
	 * La classe MotifsetatspesController s'occupe du paramétrage des Types de
	 * motifs permettant la modification de l'état Pôle emploi d'une personne
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
    App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	class MotifsetatspesController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Motifsetatspes';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Motifetatpe' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Motifsetatspes:edit'
		);

		/**
		 * Liste des articles
		 *
		 */
		public function index() {
			$query = array();
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Surcharge du formulaire de modification d'un motif de modification pour que
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
