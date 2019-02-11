<?php
	/**
	 * Code source de la classe MemosController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'WebrsaAccessMemos', 'Utility' );

	/**
	 * La classe MemosController permet de gérer les mémos attachés à un allocataire.
	 *
	 * @package app.Controller
	 */
	class MemosController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Memos';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'DossiersMenus',
			'Fileuploader',
			'Jetons2',
			'WebrsaAccesses',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default2',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Fileuploader',
			'Locale',
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Memo',
			'Option',
			'Personne',
			'WebrsaMemo',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Memos:edit',
			'view' => 'Memos:index',
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
			'ajaxfileupload' => 'update',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'filelink' => 'read',
			'fileview' => 'read',
			'index' => 'read',
			'view' => 'read',
		);

		/**
		*
		*/
		protected function _setOptions() {
			$options = $this->Memo->enums();
			$this->set( 'options', $options );
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
		 * Fonction permettant d'accéder à la page pour lier les fichiers à une manifestation d'allocataire
		 * (CG 66).
		 *
		 * @param type $id
		 */
		public function filelink( $id ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Memo->personneId( $id ) ) ) );

			$fichiers = array();
			$memo = $this->Memo->find(
				'first',
				array(
					'conditions' => array(
						'Memo.id' => $id
					),
					'contain' => array(
						'Fichiermodule' => array(
							'fields' => array( 'name', 'id', 'created', 'modified' )
						)
					)
				)
			);

			$personne_id = $memo['Memo']['personne_id'];
			$this->set( 'personne_id', $personne_id );

			$dossier_id = $this->Memo->Personne->dossierId( $personne_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Memo->begin();

				$saved = $this->Memo->updateAllUnBound(
					array( 'Memo.haspiecejointe' => '\''.$this->request->data['Memo']['haspiecejointe'].'\'' ),
					array(
						'"Memo"."personne_id"' => $personne_id,
						'"Memo"."id"' => $id
					)
				);

				if( $saved ) {
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, !Set::classicExtract( $this->request->data, "Memo.haspiecejointe" ), $id ) && $saved;
				}

				if( $saved ) {
					$this->Memo->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( $this->referer() );
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Memo->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->_setOptions();
			$this->set( compact( 'dossier_id', 'fichiers', 'memo' ) );
		}

		/**
		 *
		 * @param integer $personne_id
		 */
		public function index( $personne_id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$nbrPersonnes = $this->Memo->Personne->find( 'count', array( 'conditions' => array( 'Personne.id' => $personne_id ), 'contain' => false ) );
			$this->assert( ( $nbrPersonnes == 1 ), 'invalidParameter' );

			$this->_setEntriesAncienDossier( $personne_id, 'Memo' );

			$memos = $this->WebrsaAccesses->getIndexRecords(
				$personne_id, array(
					'fields' => array_merge(
						$this->Memo->fields(),
						array(
							$this->Memo->Fichiermodule->sqNbFichiersLies( $this->Memo, 'nb_fichiers_lies', 'Memo' )
						)
					),
					'conditions' => array(
						'Memo.personne_id' => $personne_id
					),
					'recursive' => -1
				)
			);

			$this->set(compact('memos', 'personne_id', 'ajoutPossible'));
		}

		/**		 * *******************************************************************
		 *
		 * ** ****************************************************************** */
		public function add($personne_id) {
			$this->WebrsaAccesses->check(null, $personne_id);
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		public function edit($id) {
			$this->WebrsaAccesses->check($id);
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 * @param integer $id
		 */
		protected function _add_edit( $id = null ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			// Récupération des id afférents
			if( $this->action == 'add' ) {
				$personne_id = $id;
				$dossier_id = $this->Personne->dossierId( $personne_id );
			}
			else if( $this->action == 'edit' ) {
				$memo_id = $id;
				$qd_memo = array(
					'conditions' => array(
						'Memo.id' => $memo_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$memo = $this->Memo->find( 'first', $qd_memo);
				$this->assert( !empty( $memo ), 'invalidParameter' );

				$personne_id = $memo['Memo']['personne_id'];
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$dossier_id = $this->Personne->dossierId( $personne_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->Jetons2->get( $dossier_id );

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Memo->begin();
				if( $this->Memo->saveAll( $this->request->data, array( 'validate' => 'only', 'atomic' => false ) ) ) {
					if( $this->Memo->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) ) ) {
						$this->Memo->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'controller' => 'memos', 'action' => 'index', $personne_id ) );
					}
					else {
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
			}
			elseif( $this->action == 'edit' ) {
				$this->request->data = $memo;
			}

			$this->set( 'personne_id', $personne_id );
			$this->set( 'urlmenu', '/memos/index/'.$personne_id );
			$this->render( 'add_edit' );
		}

		/**
		 * Visualisation d'un mémo.
		 *
		 * @param integer $id
		 * @throws NotFoundException
		 */
		public function view( $id ) {
			$this->WebrsaAccesses->check($id);
			$personne_id = $this->Memo->personneId( $id );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$query = array(
				'fields' => array_merge(
					$this->Memo->fields(),
					array(
						'Personne.nom_complet',
						'Dossier.matricule'
					)
				),
				'joins' => array(
					$this->Memo->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Memo->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Memo->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
				),
				'contain' => false,
				'conditions' => array(
					'Memo.id' => $id,
				),
			);

			$this->Memo->forceVirtualFields = true;
			$memo = $this->Memo->find( 'first', $query );

			if( empty( $memo ) ) {
				throw new NotFoundException();
			}

			$this->set( 'urlmenu', '/memos/index/'.$personne_id );

			$this->set( compact( 'memo' ) );
		}

		/**
		 *
		 */
		public function delete( $id ) {
			$this->WebrsaAccesses->check($id);
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Memo->personneId( $id ) ) );

			$this->Default->delete( $id );
		}

	}
?>