<?php
	/**
	 * Code source de la classe BudgetsapresController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe BudgetsapresController ...
	 *
	 * @package app.Controller
	 */
	class BudgetsapresController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Budgetsapres';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(

		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Theme',
			'Xform',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Actionscandidats:edit',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(

		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'edit' => 'update',
			'index' => 'read',
		);

		/**
		 *
		 */
		public function index() {
			$this->paginate = array(
				$this->modelClass => array(
					'limit' => 5,
					'order' => array( "{$this->modelClass}.exercicebudgetai DESC" ),
				)
			);

			$budgetsapres = $this->paginate( $this->modelClass );
			$this->set( compact( 'budgetsapres' ) );
		}

		/**
		 *
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 */
		protected function _add_edit( $id = null ) {
			if( $this->action == 'edit' ) {
				$qd_budgetapre = array(
					'conditions' => array(
						$this->modelClass.'.id' => $id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$budgetapre = $this->{$this->modelClass}->find( 'first', $qd_budgetapre );
				$this->assert( !empty( $budgetapre ), 'invalidParameter' );
			}

			if( !empty( $this->request->data ) ) {
				$this->{$this->modelClass}->create( $this->request->data );
				if( $this->{$this->modelClass}->save() ) {
					$this->Session->setFlash( __( 'Enregistrement effectué' ), 'flash/success' );
					$this->redirect( array( 'action' => 'index' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $budgetapre;
			}

			$this->render( 'add_edit' );
		}

	}
?>