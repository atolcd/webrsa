<?php
	/**
	 * Code source de la classe Suspensionscuis66.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Suspensionscuis66 ...
	 *
	 * @package app.Controller
	 */
	class Suspensionscuis66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Suspensionscuis66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'DossiersMenus',
			'Fileuploader',
			'Gestionzonesgeos',
			'Jetons2',
			'WebrsaModelesLiesCuis66',
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
			'Suspensioncui66',
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
			'ajaxfileupload',
			'ajaxfiledelete',
			'download',
			'fileview',
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
			'view' => 'read',
			'filelink' => 'read',
			'ajaxfileupload' => 'create',
			'ajaxfiledelete' => 'delete',
			'fileview' => 'read',
			'download' => 'read',
		);

		/**
		 * Envoi d'un fichier temporaire depuis le formualaire.
		 */
		public function ajaxfileupload() {
			$this->Fileuploader->ajaxfileupload();
		}

		/**
		 * Suppression d'un fichier temporaire.
		 */
		public function ajaxfiledelete() {
			$this->Fileuploader->ajaxfiledelete();
		}

		/**
		 * Visualisation d'un fichier temporaire.
		 *
		 * @param integer $id
		 */
		public function fileview( $id ) {
			$this->Fileuploader->fileview( $id );
		}

		/**
		 * Visualisation d'un fichier stocké.
		 *
		 * @param integer $id
		 */
		public function download( $id ) {
			$this->Fileuploader->download( $id );
		}

		/**
		 * Liste des fichiers liés à une orientation.
		 *
		 * @param integer $id
		 */
		public function filelink( $id ) {
			$query = array(
				'fields' => array(
					'Cui.personne_id',
					'Cui.id'
				),
				'joins' => array(
					$this->Suspensioncui66->join( 'Cui66' ),
					$this->Suspensioncui66->Cui66->join( 'Cui' ),
				),
				'conditions' => array( 'Suspensioncui66.id' => $id )
			);
			$result = $this->Suspensioncui66->find( 'first', $query );
			$personne_id = $result['Cui']['personne_id'];
			$cui_id = $result['Cui']['id'];

			$this->WebrsaModelesLiesCuis66->initAccess('Suspensioncui66');
			$this->WebrsaModelesLiesCuis66->WebrsaAccesses->check($id, $personne_id );

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );

			$this->Fileuploader->filelink( $id, array( 'action' => 'index', $cui_id ) );
			$urlmenu = "/cuis/index/{$personne_id}";

			$options = $this->Suspensioncui66->enums();
			$this->set( compact( 'options', 'dossierMenu', 'urlmenu' ) );
		}

		/**
		 * Liste des suspensions du CUI d'un allocataire
		 *
		 * @param integer $cui_id
		 */
		public function index( $cui_id ) {
			$params = array(
				'modelClass' => 'Suspensioncui66',
				'urlmenu' => "/cuis/index/#0.Cui.personne_id#"
			);
			$customQuery['fields'][] = $this->Suspensioncui66->Fichiermodule->sqNbFichiersLies( $this->Suspensioncui66, 'nombre' );

			$this->WebrsaModelesLiesCuis66->index( $cui_id, $params, $customQuery );
		}

		/**
		 * Visualisation
		 *
		 * @param integer $id
		 */
		public function view( $id ) {
			$params = array(
				'modelClass' => 'Suspensioncui66',
				'urlmenu' => "/cuis/index/#Cui.personne_id#"
			);
			return $this->WebrsaModelesLiesCuis66->view( $id, $params );
		}

		/**
		 * Formulaire d'ajout d'avis technique CUI
		 *
		 * @param integer $cui_id L'id du CUI
		 */
		public function add( $cui_id ) {
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Méthode générique d'ajout et de modification d'avis technique
		 *
		 * @param integer $id L'id du CUI (add) ou de la proposition (edit)
		 */
		public function edit( $id = null ) {
			$params = array(
				'modelClass' => 'Suspensioncui66',
				'view' => 'edit',
				'redirect' => "/Suspensionscuis66/index/#Cui.id#",
				'urlmenu' => "/cuis/index/#Cui.personne_id#"
			);
			return $this->WebrsaModelesLiesCuis66->addEdit( $id, $params );
		}

		/**
		 * Suppression
		 *
		 * @param integer $id
		 * @return type
		 */
		public function delete( $id ){
			return $this->WebrsaModelesLiesCuis66->delete( $id );
		}
	}
?>
