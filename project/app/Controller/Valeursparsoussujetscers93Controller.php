<?php
	/**
	 * Code source de la classe Valeursparsoussujetscers93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Valeursparsoussujetscers93Controller s'occupe du paramétrage des
	 * valeurs par sous-sujets pour le CER du CD 93.
	 *
	 * @package app.Controller
	 */
	class Valeursparsoussujetscers93Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Valeursparsoussujetscers93';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Valeurparsoussujetcer93' );

		/**
		 * Liste des valeurs par sous-sujet sur lequel le CER porte.
		 */
		public function index() {
			$query = array(
				'contain' => array(
					'Soussujetcer93.name',
					'Dreesactionscer.lib_dreesactioncer'
				),
				'order' => array(
					'Soussujetcer93.name ASC'
				)
			);
			$this->WebrsaParametrages->index( $query, array( 'blacklist' => $this->blacklist ) );
		}

		/**
		 * Modification d'une valeur par sous-sujet sur lequel le CER porte.
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$query = array(
				'fields' => array( 'id', 'name' ),
				'conditions' => array( 'Soussujetcer93.isautre' => '0' )
			);
			$options['Valeurparsoussujetcer93']['soussujetcer93_id'] = $this->Valeurparsoussujetcer93->Soussujetcer93->find( 'list', $query );

			$query = array(
				'fields' => array( 'id', 'lib_dreesactioncer' ),
				'conditions' => array( 'Dreesactionscer.actif' => '1' )
			);

			$options['Valeurparsoussujetcer93']['dreesactionscer_id'] = $this->Valeurparsoussujetcer93->Dreesactionscer->find( 'list', $query );

			$this->set( compact( 'options' ) );
		}
	}
?>
