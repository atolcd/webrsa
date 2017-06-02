<?php    
    /**
     * Code source de la classe Progsfichescandidatures66Controller.
     *
     * PHP 5.3
     *
     * @package app.Controller
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */
    App::import('Behaviors', 'Occurences');

    /**
     * La classe Progsfichescandidatures66Controller ...
     *
     * @package app.Controller
     */
    class Progsfichescandidatures66Controller extends AppController
    {
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Progsfichescandidatures66';

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
			'add' => 'Progsfichescandidatures66:edit',
			'view' => 'Progsfichescandidatures66:index',
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
		
        protected function _setOptions(){
            $options = $this->Progfichecandidature66->enums();
            $this->set( compact( 'options' ) );
        }
        
        
        public function index() {
            $this->Progfichecandidature66->Behaviors->attach( 'Occurences' );
            $querydata = $this->Progfichecandidature66->qdOccurencesExists(
                array(
                    'fields' => array_merge(
                        $this->Progfichecandidature66->fields()
                    ),
                    'order' => array( 'Progfichecandidature66.name ASC' )
                )
            );
            $this->paginate = $querydata;
            $progsfichescandidatures66 = $this->paginate( 'Progfichecandidature66' );

            $this->_setOptions();
            $this->set( compact('progsfichescandidatures66'));
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

            $this->_setOptions();
            $this->Default->{$this->action}( $args );
        }

        /**
        *
        */

        public function delete( $id ) {
            $this->Default->delete( $id, true );
        }

        /**
        *
        */

        public function view( $id ) {
            $this->_setOptions();
            $this->Default->view( $id );
        }
    }
?>