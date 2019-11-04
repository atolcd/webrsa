<?php
	/**
	 * Code source de la classe FoyerspiecesjointesController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'LocaleHelper', 'View/Helper' );
	App::uses( 'AppController', 'Controller' );
    App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

    /**
	 * La classe FoyerspiecesjointesController ...
	 *
	 * @package app.Controller
	 */
	class FoyerspiecesjointesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
        public $name = 'Foyerspiecesjointes';

        /**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
            'Fileuploader',
            'Gedooo.Gedooo',
			'Default',
            'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses'
        );

        /**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Locale',
			'Paginator',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Cake1xLegacy.Ajax'
        );

        /**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
            'Foyerpiecejointe',
            'Fichiermodule',
            'Foyer'
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
		);

		/**
		 * Sauvegarde d'une nouvelle pièce jointe
		 *
		 * @param int $foyer id
		 * @param array $data
		 */
		protected function _save($foyer_id, $data) {
			$this->Foyerpiecejointe->begin();
			$dataToSave = array();
			$dir = $this->Fileuploader->dirFichiersModule($this->action, $foyer_id);
			$success = $this->Fileuploader->saveFichiers($dir, false, $foyer_id);
			if( $success ) {
				$dataToSave['categorie_id'] = $data['Foyerspiecesjointes']['categorie_id'];
				$dataToSave['foyer_id'] = $foyer_id;
				$dataToSave['archive'] = 0;

				$user = $this->Foyerpiecejointe->User->find(
					'first',
					array(
						'conditions' => array(
							'User.id' => AuthComponent::user('id')
						),
						'contain' => false
					)
				);
				$dataToSave['user_id'] = $user['User']['id'];
				$dataToSave['fichiermodule_id'] = $this->Foyerpiecejointe->Fichiermodule->id;

				$success = $this->Foyerpiecejointe->save($dataToSave);
				if($success) {
					$this->Foyerpiecejointe->commit();
					$this->_mailTo($foyer_id, $this->Foyerpiecejointe->id);
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index', $foyer_id) );
				} else {
					$fichiers = $this->Fileuploader->fichiers( $foyer_id );
					$this->Foyerpiecejointe->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			} else {
				$this->Flash->error( __( 'Save->error' ) );
			}
		}

        /**
		 * Index des pièces jointes
		 * @param integer $foyer_id L'id technique du foyer pour lequel on veut les fichiers liés.
		 *
		 */
		public function index($foyer_id) {
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id )));

			$pjNonArchives = $this->WebrsaAccesses->getIndexRecords(
				$foyer_id,
				array(
					'fields' => array(
						'Foyerpiecejointe.id',
						'Foyerpiecejointe.foyer_id',
						'Foyerpiecejointe.created',
						'User.username',
						'Fichiermodule.name',
						'Categoriepiecejointe.nom',
						'Foyerpiecejointe.id',
						'Foyerpiecejointe.id',
					),
					'conditions' => array(
						'Foyerpiecejointe.foyer_id' => $foyer_id,
						'Foyerpiecejointe.archive' => 0
					),
					'order' => array(
						'Categoriepiecejointe.nom ASC',
						'Fichiermodule.name ASC'
					)
				)
			);

			$pjArchives = $this->Foyerpiecejointe->find('all', array(
				'recursive' => -1,
                'conditions' => array(
                    'Foyerpiecejointe.foyer_id' => $foyer_id,
                    'Foyerpiecejointe.archive' => 1
                    )
                )
			);
			$nbPieceArchives = count($pjArchives);
			$pjArchivesActif = !empty($pjArchives) ? true : false;

			// Assignation à la vue
			$this->set( compact('foyer_id', 'pjNonArchives', 'pjArchivesActif', 'nbPieceArchives' ) );
		}

		/**
		 * Index des fichiers archivés
		 * @param int $foyer_id
		 */
		public function archivage($foyer_id) {
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id )));

			$pjArchives = $this->WebrsaAccesses->getIndexRecords(
				$foyer_id,
				array(
					'fields' => array(
						'Foyerpiecejointe.id',
						'Foyerpiecejointe.foyer_id',
						'Foyerpiecejointe.created',
						'User.username',
						'Fichiermodule.name',
						'Categoriepiecejointe.nom',
						'Foyerpiecejointe.id',
					),
					'conditions' => array(
						'Foyerpiecejointe.foyer_id' => $foyer_id,
						'Foyerpiecejointe.archive' => 1
					),
					'order' => array(
						'Categoriepiecejointe.nom ASC',
						'Fichiermodule.name ASC'
					)
				)
			);

			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'pjArchives', $pjArchives );
		}

		/**
		 * Similaire à download
		 * @param int id du fichier
		 */
		public function view($id) {
			$file = $this->Foyerpiecejointe->find('first', array(
				'fields' => array(
					'Foyerpiecejointe.fichiermodule_id'
				),
				'conditions' => array(
					'Foyerpiecejointe.id' => $id
				)
			) );
			$this->Fileuploader->download($file['Foyerpiecejointe']['fichiermodule_id']);
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
		 * Ajout d'une pièce jointe
		 * @param integer $foyer_id L'id technique du foyer pour lequel on veut ajouter un ou plusieurs fichiers liés.
		 */
		public function add($foyer_id) {

			// Intégration des pièces jointes
			$piecesjointes = array();

			// Suppression des fichiers archivés dans le tableau
			$piecejointeFpj = $this->Foyerpiecejointe->find('all', array(
				'conditions' => array(
                    'Foyerpiecejointe.foyer_id' => $foyer_id,
                    'Foyerpiecejointe.archive' => 0
                    )
                )
			);
			//debug($piecejointeFpj);
			foreach($piecejointeFpj as $key => $piecejointe) {
				$piecesjointes[] = $piecejointe['Fichiermodule'];
			}

			// Récupération de la liste des catégories
			$listeCategorie = $this->Foyerpiecejointe->Categoriepiecejointe->find('list', array(
				'fields' => array(
					'Categoriepiecejointe.id',
					'Categoriepiecejointe.nom'
				),
				'conditions' => array('Categoriepiecejointe.actif' => 1)
			) );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$dir = $this->Fileuploader->dirFichiersModule( $this->action, $foyer_id );
				if(file_exists($dir)) {
					$this->Fileuploader->deleteDir();
				}
				$this->redirect( array( 'action' => 'index', $foyer_id) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->_save($foyer_id, $this->request->data);
			}

			$this->set( compact('foyer_id', 'piecesjointes', 'listeCategorie', 'fichier' ) );
		}

		/**
		 * Mise en archive d'une pièce jointe
		 * @param int
		 */
		public function archive($id) {
			$file = $this->Foyerpiecejointe->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'Foyerpiecejointe.id' => $id
				)
			) );

			$this->Foyerpiecejointe->id = $id;
			$this->Foyerpiecejointe->saveField('archive', 1);
			$this->redirect( array( 'action' => 'index', $file['Foyerpiecejointe']['foyer_id']) );
		}

		public function dearchive($id) {
			$file = $this->Foyerpiecejointe->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'Foyerpiecejointe.id' => $id
				)
			) );

			$this->Foyerpiecejointe->id = $id;
			$this->Foyerpiecejointe->saveField('archive', 0);
			$this->redirect( array( 'action' => 'archivage', $file['Foyerpiecejointe']['foyer_id']) );
		}

		/**
		 * Suppression d'une pièce jointe
		 * @param int
		 */
		public function delete($id) {
			$file = $this->Foyerpiecejointe->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'Foyerpiecejointe.id' => $id
				)
			) );

			if(	$this->Foyerpiecejointe->Fichiermodule->delete($file['Foyerpiecejointe']['fichiermodule_id'], false) &&
				$this->Foyerpiecejointe->delete($file['Foyerpiecejointe']['id'], false)) {
				$this->Flash->success( __( 'Delete->success' ) );
			} else {
				$this->Foyerpiecejointe->Fichiermodule->rollback();
				$this->Foyerpiecejointe->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}
			$this->redirect( array( 'action' => 'index', $file['Foyerpiecejointe']['foyer_id']) );
		}

		/**
		 * Modification d'une catégorie
		 * @param int
		 */
		public function edit($id) {
			// Récupération de l'information du fichier
			$fichier = $this->Foyerpiecejointe->find('first', array(
				'fields' => array(
					'Foyerpiecejointe.id',
					'Foyerpiecejointe.foyer_id',
					'Foyerpiecejointe.created',
					'User.username',
					'Fichiermodule.name',
					'Categoriepiecejointe.nom',
					'Foyerpiecejointe.id',
				),
				'conditions' => array('Foyerpiecejointe.id' => $id)
				)
			);
			// Récupération de la liste des catégories
			$listeCategorie = $this->Foyerpiecejointe->Categoriepiecejointe->find('list', array(
				'fields' => array(
					'Categoriepiecejointe.id',
					'Categoriepiecejointe.nom',
					'Categoriepiecejointe.mailauto'
				),
				'conditions' => array('Categoriepiecejointe.actif' => 1)
			) );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index', $fichier['Foyerpiecejointe']['foyer_id']) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Foyerpiecejointe->begin();
				$data = array();
				$data['categorie_id'] = $this->request->data['Foyerspiecesjointes']['categorie_id'];
				$data['id'] = $id;
				if( $this->Foyerpiecejointe->save($data) ) {
					$this->Foyerpiecejointe->commit();
					$this->_mailTo($fichier['Foyerpiecejointe']['foyer_id'], $id);
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index', $fichier['Foyerpiecejointe']['foyer_id']) );
				} else {
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->set( compact('foyer_id', 'listeCategorie', 'fichier' ) );
		}

		/**
		 * Envoi d'un mail automatique en cas de catégorie de pièce jointe
		 * avec le champs mailauto à 1
		 */
		protected function _mailTo($foyer_id, $id) {
			/* $fichier = $this->Fichierpiecejointe->find('first', array(
				'recursive' => 0,
				'conditions' => array('Fichierpiecejointe.id' => $id)
			));
			debug($fichier); */
		}

    }