<?php
    /**
     * Code source de la classe SecteurscuisController.
     *
     * PHP 5.3
     *
     * @package app.Controller
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */
     App::uses( 'OccurencesBehavior', 'Model/Behavior' );
	 App::uses( 'AppController', 'Controller' );

    /**
     * La classe SecteurscuisController ...
     *
     * @package app.Controller
     */
    class SecteurscuisController extends AppController
    {
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Secteurscuis';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default2',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Secteurscuis:edit',
			'view' => 'Secteurscuis:index',
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
			'delete' => 'delete',
			'edit' => 'update',
			'index' => 'read',
			'view' => 'read',
		);

		/**
		 * Envoi des options communes dans les vues.
		 *
		 * @return void
		 */
		protected function _setOptions() {
			$options = array(
				'exists' => array( '0' => 'Non', '1' => 'Oui' )
			);
			$this->set( compact( 'options' ) );
		}

        /**
         *
         */
        public function index() {
			$this->Secteurcui->Behaviors->attach( 'Occurences' );

            $querydata = $this->Secteurcui->qdOccurencesExists(
                array(
                    'fields' => $this->Secteurcui->fields(),
                    'order' => array( 'Secteurcui.name ASC' )
                )
            );

            $this->paginate = $querydata;
            $secteurscuis = $this->paginate('Secteurcui');
            $this->set( compact('secteurscuis'));
        }

        /**
        *
        */
        public function add() {
            $args = func_get_args();
            call_user_func_array( array( $this, '_add_edit' ), $args );
        }

        /**
        *
        */
        public function edit() {
            $args = func_get_args();
            call_user_func_array( array( $this, '_add_edit' ), $args );
        }

        /**
        *
        */

        protected function _add_edit(){
            $args = func_get_args();

            // Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'secteurscuis', 'action' => 'index' ) );
			}
            $this->_setOptions();
            $this->Default->{$this->action}( $args );
        }

        /**
         *
         * @param integer $id
         */
        public function delete( $id ) {
            $this->Default->delete( $id, true );
        }

        /**
        *
        */

        public function view( $id ) {
            $this->Default->view( $id );
        }
    }
?>
