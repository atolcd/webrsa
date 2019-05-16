<?php
	/**
	 * Code source de la classe Sujetscers93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Sujetscers93Controller s'occupe du paramétrage des sujets pour
	 * le CER du CD 93.
	 *
	 * @package app.Controller
	 */
	class Sujetscers93Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Sujetscers93';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Sujetcer93' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Sujetscers93:edit'
		);

		/**
		 * Liste des sujets sur lequel le CER porte
		 */
		public function index() {
			$query = array(
				'contain' => array(
					'Dreesactionscer.lib_dreesactioncer'
				),
			);
			$this->WebrsaParametrages->index( $query, array( 'blacklist' => $this->blacklist ) );
		}


		/**
		 * Modification d'un sujet sur lequel le CER porte.
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$query = array(
				'fields' => array( 'id', 'lib_dreesactioncer' ),
				'conditions' => array( 'Dreesactionscer.actif' => '1' )
			);

			$options['Sujetcer93']['dreesactionscer_id'] = $this->Sujetcer93->Dreesactionscer->find( 'list', $query );

			$this->set( compact( 'options' ) );
		}
	}
?>
