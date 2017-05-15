<?php    
    /**
     * Code source de la classe Modelestypescourrierspcgs66Controller.
     *
     * PHP 5.3
     *
     * @package app.Controller
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */
    App::import( 'Behaviors', 'Occurences' );

    /**
     * La classe Modelestypescourrierspcgs66Controller ...
     *
     * @package app.Controller
     */
    class Modelestypescourrierspcgs66Controller extends AppController
    {
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Modelestypescourrierspcgs66';

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
			'add' => 'Modelestypescourrierspcgs66:edit',
			'view' => 'Modelestypescourrierspcgs66:index',
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

		protected function _setOptions() {
			$options = array();
			$options[$this->modelClass]['typecourrierpcg66_id'] = $this->Modeletypecourrierpcg66->Typecourrierpcg66->find( 'list', array( 'fields' => array( 'id', 'name' ) ) );

            $options = Set::merge( $this->Modeletypecourrierpcg66->enums(), $options);
			$this->set( compact( 'options' ) );

		}
        
        public function index() {
            $this->Modeletypecourrierpcg66->Behaviors->attach( 'Occurences' );
            $querydata = $this->Modeletypecourrierpcg66->qdOccurencesExists(
                array(
                    'fields' => array_merge(
                        $this->Modeletypecourrierpcg66->Typecourrierpcg66->fields(),
                        $this->Modeletypecourrierpcg66->fields()
                    ),
                    'order' => array( 'Typecourrierpcg66.name ASC' ),
                )
            );
            $this->paginate = $querydata;
            $modelestypescourrierspcgs66 = $this->paginate( 'Modeletypecourrierpcg66' );

            $this->_setOptions();
            $this->set( compact('modelestypescourrierspcgs66'));
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
            $this->Default->view( $id );
        }
    }
?>
