<?php
	/**
	 * Code source de la classe Propositionscuis66.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Propositionscuis66 ...
	 *
	 * @package app.Controller
	 */
	class Propositionscuis66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Propositionscuis66';

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
			'Propositioncui66',
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
			'ajaxfiledelete',
			'ajaxfileupload',
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
			'add' => 'create',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'create',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'filelink' => 'read',
			'fileview' => 'read',
			'impression' => 'read',
			'impression_aviselu' => 'update',
			'index' => 'read',
			'view' => 'read',
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
					$this->Propositioncui66->join( 'Cui66' ),
					$this->Propositioncui66->Cui66->join( 'Cui' ),
				),
				'conditions' => array( 'Propositioncui66.id' => $id )
			);
			$result = $this->Propositioncui66->find( 'first', $query );
			$personne_id = $result['Cui']['personne_id'];
			$cui_id = $result['Cui']['id'];
			$this->WebrsaModelesLiesCuis66->initAccess('Propositioncui66');
			$this->WebrsaModelesLiesCuis66->WebrsaAccesses->check($id, $personne_id);

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );

			$this->Fileuploader->filelink( $id, array( 'action' => 'index', $cui_id ) );
			$urlmenu = "/cuis/index/{$personne_id}";

			$options = $this->Propositioncui66->enums();
			$this->set( compact( 'options', 'dossierMenu', 'urlmenu' ) );
		}

		/**
		 * Liste des avis techniques
		 *
		 * @param integer $cui_id
		 */
		public function index( $cui_id ) {
			$params = array(
				'modelClass' => 'Propositioncui66',
				'urlmenu' => "/cuis/index/#0.Cui.personne_id#"
			);
			$customQuery['fields'][] = $this->Propositioncui66->Fichiermodule->sqNbFichiersLies( $this->Propositioncui66, 'nombre' );

			$this->WebrsaModelesLiesCuis66->index( $cui_id, $params, $customQuery );
		}

		/**
		 * Visualisation d'un avis technique
		 *
		 * @param integer $id
		 */
		public function view( $id ) {
			$params = array(
				'modelClass' => 'Propositioncui66',
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
				'modelClass' => 'Propositioncui66',
				'view' => 'edit',
				'redirect' => "/Propositionscuis66/index/#Cui.id#",
				'urlmenu' => "/cuis/index/#Cui.personne_id#"
			);
			$result = $this->WebrsaModelesLiesCuis66->addEdit( $id, $params );

			// L'avis "En attente de décision" ne devrait pas être là, donc on ne le met pas dans la liste si ce n'est pas la valeur courante
			$options = Hash::get( $this->viewVars, 'options' );
			$avis = Hash::get( $this->request->data, 'Propositioncui66.avis' );
			if( 'attentedecision' !== $avis ) {
				unset( $options['Propositioncui66']['avis']['attentedecision'] );
				$this->set( compact( 'options' ) );
			}

			return $result;
		}

		/**
		 * Suppression d'un avis technique
		 *
		 * @param integer $id
		 * @return boolean
		 */
		public function delete( $id ){
			return $this->WebrsaModelesLiesCuis66->delete( $id );
		}

		/**
		 * Impression générique
		 *
		 * @param integer $id
		 * @return boolean
		 */
		public function impression( $id ){
			return $this->WebrsaModelesLiesCuis66->impression( $id );
		}

		/**
		 * Impression avis élu
		 *
		 * @param integer $id
		 * @return boolean
		 */
		public function impression_aviselu( $id ){
			return $this->WebrsaModelesLiesCuis66->impression( $id, 'aviselu' );
		}
	}
?>
