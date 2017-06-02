<?php	
	/**
	 * Code source de la classe ParametresfinanciersController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ParametresfinanciersController ...
	 *
	 * @package app.Controller
	 */
	class ParametresfinanciersController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Parametresfinanciers';

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
			'Xform',
			'Xhtml',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			
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
			'edit' => 'update',
			'index' => 'read',
		);

		/**
		*
		*/

		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'apres', 'action' => 'indexparams' ) );
			}
			$this->set( 'parametrefinancier',  $this->{$this->modelClass}->find( 'first' ) );
		}

		/**
		*
		*/

		public function edit() {
			$parametrefinancier = $this->{$this->modelClass}->find( 'first' );
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'parametresfinanciers', 'action' => 'index' ) );
			}
			if( !empty( $this->request->data ) ) {
				$this->{$this->modelClass}->create( $this->request->data );
				if( $this->{$this->modelClass}->save() ) {
					$this->Session->setFlash( __( 'Enregistrement effectué' ), 'flash/success' );
					$this->redirect( array( 'controller' => 'parametresfinanciers', 'action' => 'index' ) );
				}
			}
			else {
				$this->request->data = $parametrefinancier;
			}
		}
	}
?>