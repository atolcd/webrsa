<?php
	/**
	 * Code source de la classe SuivisaidesapresController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe SuivisaidesapresController ...
	 *
	 * @package app.Controller
	 */
	class SuivisaidesapresController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Suivisaidesapres';

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
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Suiviaideapre',
			'Option',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Suivisaidesapres:edit',
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
		);

		public function beforeFilter() {
			$return = parent::beforeFilter();
			$this->set( 'qual', $this->Option->qual() );

			return $return;
		}

		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'apres', 'action' => 'indexparams' ) );
			}

			$suivisaidesapres = $this->Suiviaideapre->find(
				'all',
				array(
					'recursive' => -1,
					'conditions' => array( 'Suiviaideapre.deleted' => '0' )
				)
			);
			$this->set('suivisaidesapres', $suivisaidesapres );
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

		protected function _add_edit() {
			$args = func_get_args();
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}
			call_user_func_array( array( $this->Default, $this->action ), $args );
		}

		/**
		*
		*/

		public function delete( $suiviaideapre_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $suiviaideapre_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de la personne
			$suiviaideapre = $this->Suiviaideapre->find(
				'first',
				array( 'conditions' => array( 'Suiviaideapre.id' => $suiviaideapre_id )
				)
			);

			// Mauvais paramètre
			if( empty( $suiviaideapre_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Tentative de suppression ... FIXME
			$this->Suiviaideapre->delete( array( 'Suiviaideapre.id' => $suiviaideapre_id ) );
			$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			$this->redirect( array( 'controller' => 'suivisaidesapres', 'action' => 'index' ) );
		}
	}

?>