<?php
	/**
	 * Code source de la classe Soussujetscers93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Soussujetscers93Controller s'occupe du paramétrage des sous-sujets
	 * pour le CER du CD 93.
	 *
	 * @package app.Controller
	 */
	class Soussujetscers93Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Soussujetscers93';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Soussujetcer93' );

		/**
		 * Liste des sous-sujets sur lequel le CER porte
		 */
		public function index() {
			$query = array(
				'contain' => array(
					'Sujetcer93.name'
				),
				'order' => array(
					'Sujetcer93.name ASC'
				)
			);
			$this->WebrsaParametrages->index( $query, array( 'blacklist' => $this->blacklist ) );
		}

		/**
		 * Modification d'un sous-sujet sur lequel le CER porte.
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$query = array(
				'fields' => array( 'id', 'name' ),
				'conditions' => array( 'Sujetcer93.isautre' => '0' )
			);
			$options['Soussujetcer93']['sujetcer93_id'] = $this->Soussujetcer93->Sujetcer93->find( 'list', $query );
			$this->set( compact( 'options' ) );
		}
	}
?>
