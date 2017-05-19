<?php
	/**
	 * Code source de la classe Tauxcgscuis66.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');
	App::uses('CakeEmail', 'Network/Email');
	App::uses( 'WebrsaEmailConfig', 'Utility' );

	/**
	 * La classe Tauxcgscuis66 ...
	 *
	 * @package app.Controller
	 */
	class Tauxcgscuis66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Tauxcgscuis66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'DossiersMenus',
			'Fileuploader',
			'Gedooo.Gedooo',
			'Gestionzonesgeos',
			'Jetons2',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Ajax2' => array(
				'className' => 'Prototype.PrototypeAjax',
				'useBuffer' => false
			),
			'Cake1xLegacy.Ajax',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Fileuploader',
			'Observer' => array(
				'className' => 'Prototype.PrototypeObserver',
				'useBuffer' => true
			),
			'Romev3',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Tauxcgcui66',
			'Option',
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
			'index' => 'read',
			'add' => 'create',
			'edit' => 'update',
			'delete' => 'delete',
		);
		
		/**
		 * Liste des CUI du bénéficiaire.
		 * 
		 * @param integer $personne_id
		 */
		public function index() {
			$results = $this->Tauxcgcui66->find( 'all' );
			
			// Options
			$options = $this->Tauxcgcui66->options();

			$this->set( compact( 'results', 'options' ) );
		}
		
		/**
		 * Formulaire d'ajout
		 *
		 * @param integer $personne_id L'id de la Personne à laquelle on veut ajouter un CUI
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Méthode générique d'ajout et de modification
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			// On tente la sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Tauxcgcui66->begin();
				if( $this->Tauxcgcui66->saveAddEdit( $this->request->data, $this->Session->read( 'Auth.User.id' ) ) ) {
					$this->Tauxcgcui66->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->Tauxcgcui66->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else {
				$this->request->data = $this->Tauxcgcui66->prepareFormDataAddEdit( $id );
			}
			
			// Options
			$options = $this->Tauxcgcui66->options();

			$this->set( compact( 'options' ) );
			$this->render( 'edit' );
		}
		
		/**
		 * Supprime un tauxcg
		 * 
		 * @param type $id
		 */
		public function delete( $id ){
			$this->Tauxcgcui66->begin();
			$success = $this->Tauxcgcui66->delete($id);
			$this->_setFlashResult('Delete', $success);

			if ($success) {
				$this->Tauxcgcui66->commit();
			} else {
				$this->Tauxcgcui66->rollback();
			}
			$this->redirect($this->referer());
		}
	}
?>
