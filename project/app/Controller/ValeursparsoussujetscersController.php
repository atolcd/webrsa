<?php
	/**
	 * Code source de la classe ValeursparsoussujetscersController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe ValeursparsoussujetscersController s'occupe du paramétrage des types d'actions
	 * d'insertion.
	 *
	 * @package app.Controller
	 */
	class ValeursparsoussujetscersController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Valeursparsoussujetscers';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Valeurparsoussujetcer' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Valeursparsoussujetscers:edit'
		);

		/**
		 * Liste des types d'actions d'insertion
		 */
		public function index() {
			$query = array(
				'contain' => array(
					'Soussujetcer.libelle',
				),
				'order' => array(
					'Soussujetcer.libelle ASC'
				),
				'limit' => 1000,
				'maxLimit' => 1001
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Modification d'une valeur par sous-sujet sur lequel le CER porte.
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$options['Valeurparsoussujetcer']['soussujetcer_id'] = $this->Valeurparsoussujetcer->Soussujetcer->find( 'list', ['fields' => ['id', 'libelle']] );

			$this->set( compact( 'options' ) );
		}
	}
?>