<?php
	/**
	 * Code source de la classe RecoursgracieuxController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'LocaleHelper', 'View/Helper' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'WebrsaAccessRecoursgracieux', 'Utility' );

	/**
	 * La classe RecoursgracieuxController ...
	 *
	 * @package app.Controller
	 */
	class RecoursgracieuxController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Recoursgracieux';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes',
			'Gedooo.Gedooo',
			'Default',
			'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses',
			'Fileuploader'
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Locale',
			'Paginator',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Cake1xLegacy.Ajax',
			'Search',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read',
			'view' => 'read',
			'add' => 'create',
			'edit' => 'update',
			'delete' => 'delete',
			'search' => 'read',
			'filelink' => 'read',
			'fileview' => 'read',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'ajaxreffonct' => 'read',
			'exportcsv' => 'read'
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Recourgracieux',
			'WebrsaRecourgracieux',
			'Originerecoursgracieux',
			'Typerecoursgracieux',
			'Dossier',
			'Foyer',
			'Personne',
			'Option',
			'Historiqueetat'
			);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		*/
		public $aucunDroit = array(
			'ajaxfiledelete',
			'ajaxfileupload',
			'ajaxreffonct',
			'download',
			'fileview'
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'delete' => 'Recoursgracieux:add',
			'view' => 'Recoursgracieux:index'
		);

		/**
		 * Moteur de recherche par Recoursgracieux
		 *
		 * @return void
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesRecoursgracieux' );
			$Recherches->search();
		}

		/**
		 * Pagination sur les <éléments> de la table. *
		 * @param integer $foyer_id L'id technique du Foyer pour lequel on veut les Recoursgracieux.
		 *
		 */
		public function index($foyer_id) {
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id )));
			$this->set( 'options', $this->Recourgracieux->enums() );

			$recoursgracieux = $this->WebrsaAccesses->getIndexRecords(
				$foyer_id,
				array(
					'fields' => array_merge(
						$this->Recourgracieux->fields(),
						 array(
							$this->Recourgracieux->Fichiermodule->sqNbFichiersLies( $this->Recourgracieux, 'nb_fichiers_lies' )
						)
					),
					'conditions' => array(
						'Recourgracieux.foyer_id' => $foyer_id
					),
					'contain' => false,
					'order' => array(
						'Recourgracieux.dtarrivee DESC',
					)
				)
			);

			// Assignations à la vue
			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'recoursgracieux', $recoursgracieux );
			$this->set( 'urlmenu', '/recoursgracieux/index/'.$foyer_id );
		}

		/**
		 * Pagination sur les <éléments> de la table. *
		 * @param integer $recourgracieux_id L'id technique du recours à affiché
		 *
		 */
		public function view($recourgracieux_id) {
			$this->WebrsaAccesses->check($recourgracieux_id);
			$this->Recourgracieux->id = $recourgracieux_id;
			$foyer_id = $this->Recourgracieux->field( 'foyer_id' );

			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id )));

			$recoursgracieux = $this->WebrsaAccesses->getIndexRecords(
				$recourgracieux_id,
				array(
					'fields' => array_merge(
						$this->Recourgracieux->fields(),
						array(
							$this->Recourgracieux->Fichiermodule->sqNbFichiersLies( $this->Recourgracieux, 'nb_fichiers_lies' )
						)
					),
					'conditions' => array(
						'Recourgracieux.id' => $recourgracieux_id
					),
					'contain' => false,
					'order' => array(
						'Recourgracieux.created DESC',
					)
				)
			);

			// Assignation à la vue
			$this->set( 'options', array_merge(
					$this->Recourgracieux->options(),
					$this->Recourgracieux->enums()
				)
			);
			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'recoursgracieux', $recoursgracieux );
			$this->set( 'urlmenu', '/recoursgracieux /view/'.$recourgracieux_id );
		}

		/**
		 * Ajouter un recours gracieux à un foyer
		 *
		 * @param integer $foyer_id L'id technique du foyer auquel ajouter le recours gracieux
		 * @return void
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Modification d'un recours gracieux du foyer.
		 *
		 * @param integer $id L'id technique dans la table recoursgracieux.
		 * @return void
		 */
		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Fonction commune d'ajout/modification d'un recours gracieux
		 *
		 * @param integer $id
		 * 		Soit l'id technique du foyer auquel ajouter le recours gracieux
		 * 		Soit l'id technique dans la table recoursgracieux a éditer.
		 * @return void
		 */
		protected function _add_edit($id = null) {
			if($this->action == 'add' ) {
				$foyer_id = $id;
				$id = null;
				$dossier_id = $this->Recourgracieux->Foyer->dossierId( $foyer_id );
			}elseif($this->action == 'edit' ){
				$this->WebrsaAccesses->check($id);
				$this->Recourgracieux->id = $id;
				$foyer_id = $this->Recourgracieux->field( 'foyer_id' );
				$dossier_id = $this->Recourgracieux->dossierId( $id );
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Recourgracieux->begin();
				$data = $this->request->data;

					if($this->action == 'add' ) {
						$data['Recourgracieux']['foyer_id'] = $foyer_id;
					}
					if( $this->Recourgracieux->saveAll( $data, array( 'validate' => 'only' ) ) &&
						$this->Recourgracieux->save( $data ) ) {
						$this->Recourgracieux->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'controller' => 'Recoursgracieux', 'action' => 'index', $foyer_id ) );
					} else {
						$this->Recourgracieux->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
			}
			// Affichage des données
			elseif( $this->action == 'edit' ) {
				$recoursgracieux = $this->Recourgracieux->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Recourgracieux->fields()
						),
						'conditions' => array(
							'Recourgracieux.id' => $id
						),
						'contain' => FALSE
					)
				);
				if (!empty( $recoursgracieux ) ){
					// Assignation au formulaire
					$this->request->data = $recoursgracieux;
				}
			}elseif( $this->action == 'add' ){
					// Assignation au formulaire
					$recoursgracieux['Recourgracieux']['dtbutoire'] = date('Y-m-d', strtotime( '+1 Month' )) ;
					$this->request->data = $recoursgracieux;
			}

			// Assignation à la vue
			$this->set( 'options', array_merge(
					$this->Recourgracieux->options(),
					$this->Recourgracieux->enums()
				)
			);
			$this->set( 'urlmenu', '/recoursgracieux/index/'.$foyer_id );
			$this->set( 'foyer_id', $foyer_id );
			$this->render( 'add_edit' );

		}

		/**
		 * Supprime un recours gracieux d'un foyer
		 *
		 * @param integer $id L'id technique du recours gracieux a supprimé
		 * @return void
		 */
		public function delete($id) {
				$this->WebrsaAccesses->check($id);
				$foyer_id = $this->Recourgracieux->field( 'foyer_id' );
				$success = $this->Recourgracieux->delete( $id );
				if( $success ) {
					$this->Flash->success( __( 'Delete->success' ) );
				}
				else {
					$this->Flash->error( __( 'Delete->error' ) );
				}
				$this->redirect( array( 'controller' => 'recoursgracieux', 'action' => 'index', $foyer_id ) );
		}

		/**
		 * http://valums.com/ajax-upload/
		 * http://doc.ubuntu-fr.org/modules_php
		 * increase post_max_size and upload_max_filesize to 10M
		 * debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
		 */
		public function ajaxfileupload() {
			$this->Fileuploader->ajaxfileupload();
		}

		/**
		 * http://valums.com/ajax-upload/
		 * http://doc.ubuntu-fr.org/modules_php
		 * increase post_max_size and upload_max_filesize to 10M
		 * debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
		 * FIXME: traiter les valeurs de retour
		 */
		public function ajaxfiledelete() {
			$this->Fileuploader->ajaxfiledelete();
		}

		/**
		 *   Fonction permettant de visualiser les fichiers chargés dans la vue avant leur envoi sur le serveur
		 */
		public function fileview( $id ) {
			$this->Fileuploader->fileview( $id );
		}

		/**
		 *   Téléchargement des fichiers préalablement associés à un traitement donné
		 */
		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}

		/**
		 * Fonction permettant d'accéder à la page pour lier les fichiers.
		 *
		 * @param type $id
		 */
		public function filelink( $id ) {
			$this->WebrsaAccesses->check($id);
			$dossier_id = $this->Recourgracieux->dossierId( $id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$fichiers = array();

			$recoursgracieux = $this->Recourgracieux->find(
				'first',
				array(
					'conditions' => array(
						'Recourgracieux.id' => $id
					),
					'contain' => array(
						'Fichiermodule' => array(
							'fields' => array( 'name', 'id', 'created', 'modified' )
						)
					)
				)
			);

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Recourgracieux->id = $id;
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $this->Recourgracieux->field( 'foyer_id' )) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Recourgracieux->begin();

				$saved = $this->Recourgracieux->updateAllUnBound(
					array( 'Recourgracieux.haspiecejointe' => '\''.$this->request->data['Recourgracieux']['haspiecejointe'].'\'' ),
					array( '"Recourgracieux"."id"' => $id)
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, Set::classicExtract( $this->request->data, "Recourgracieux.haspiecejointe" ), $id ) && $saved;
				}

				if( $saved ) {
					$this->Recourgracieux->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('action' => 'filelink', $id));
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Recourgracieux->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->set( 'options', array_merge(
					$this->Recourgracieux->options(),
					$this->Recourgracieux->enums()
				)
			);
			$this->set( compact( 'dossier_id', 'personne_id', 'fichiers', 'recoursgracieux' ) );
		}

	}
?>
