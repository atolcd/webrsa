<?php
	/**
	 * Code source de la classe AbstractWebrsaParametragesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe AbstractWebrsaParametragesController ...
	 *
	 * @package app.Controller
	 */
	abstract class AbstractWebrsaParametragesController extends AppController
	{
		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array( 'WebrsaParametrages' );

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array();

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'delete' => 'delete',
			'edit' => 'update',
			'index' => 'read'
		);

		/**
		 * Liste des tables à ne pas prendre en compte dans les enregistrements
		 * vérifiés pour éviter les suppressions en cascade intempestives.
		 *
		 * @var array
		 */
		public $blacklist = array();

		/**
		 * Liste des éléments.
		 *
		 * @todo final
		 */
		public function index() {
			$this->WebrsaParametrages->index( array(), array( 'blacklist' => $this->blacklist ) );
		}

		/**
		 * Formulaire d'ajout d'un élément.
		 *
		 * @todo final
		 */
		public function add() {
			$this->edit();
		}

		/**
		 * Formulaire de modification d'un élément.
		 *
		 * @todo final
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );
		}

		/**
		 * Suppression d'un élément.
		 *
		 * @todo final
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->WebrsaParametrages->delete( $id, array( 'blacklist' => $this->blacklist ) );
		}
	}
?>
