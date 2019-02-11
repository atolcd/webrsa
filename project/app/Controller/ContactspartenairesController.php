<?php
	/**
	 * Code source de la classe ContactspartenairesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'OccurencesBehavior', 'Model/Behavior' );
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe ContactspartenairesController ...
	 *
	 * @package app.Controller
	 */
	class ContactspartenairesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Contactspartenaires';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
				),
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default',
			'Default2',
			'Theme',
			'Xform',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Contactpartenaire',
			'Option',
			'Partenaire',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Contactspartenaires:edit',
			'view' => 'Contactspartenaires:index',
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
		 *
		 * @return type
		 */
		public function _setOptions() {
			$options = array();
			foreach( array( 'Partenaire' ) as $linkedModel ) {
				$field = Inflector::singularize( Inflector::tableize( $linkedModel ) ).'_id';
                $options = Hash::insert( $options, "{$this->modelClass}.{$field}", $this->{$this->modelClass}->{$linkedModel}->find( 'list', array( 'order' => array( "{$linkedModel}.libstruc ASC" ) ) ) );
			}
			$this->set( 'qual', $this->Option->qual() );

			$this->set( compact( 'options', 'qual' ) );
		}


		/**
		*   Ajout à la suite de l'utilisation des nouveaux helpers
		*   - default.php
		*   - theme.php
		*/

		public function index() {
			$messages = array();
			if( 0 === $this->Partenaire->find( 'count' ) ) {
				$msg = 'Merci de renseigner au moins un partenaire / prestataire avant de renseigner un contact.';
				$messages[$msg] = 'error';
			}
			$this->set( compact( 'messages' ) );

            if( !empty( $this->request->data ) ) {
                $this->Contactpartenaire->Behaviors->attach( 'Occurences' );
                $querydata = $this->Contactpartenaire->search( $this->request->data );
                $querydata = $this->Contactpartenaire->qdOccurencesExists( $querydata );
                $this->paginate = $querydata;
                $contactspartenaires = $this->paginate( 'Contactpartenaire' );
                $this->set( compact('contactspartenaires'));
			}
			$this->_setOptions();
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
			$this->Default->delete( $id );
		}

		/**
		*
		*/

		public function view( $id ) {
			$this->Default->view( $id );
		}
	}
?>
