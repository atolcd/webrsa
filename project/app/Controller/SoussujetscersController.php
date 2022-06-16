<?php
	/**
	 * Code source de la classe SoussujetscersController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe SoussujetscersController s'occupe du paramétrage des types d'actions
	 * d'insertion.
	 *
	 * @package app.Controller
	 */
	class SoussujetscersController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Soussujetscers';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Soussujetcer' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Soussujetscers:edit'
		);

		/**
		 * Liste des types d'actions d'insertion
		 */
		public function index() {
			$query = array(
				'contain' => array(
					'Sujetcer.libelle',
				),
				'order' => array(
					'Sujetcer.libelle ASC'
				),
				'limit' => 1000,
				'maxLimit' => 1001
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Modification d'un sous-sujet sur lequel le CER porte.
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$options['Soussujetcer']['sujetcer_id'] = $this->Soussujetcer->Sujetcer->find( 'list', ['fields' => ['id', 'libelle']] );

			$this->set( compact( 'options' ) );
		}
	}