<?php
	/**
	 * Code source de la classe Manifestationsbilansparcours66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Manifestationsbilansparcours66Controller ... (CG 66).
	 *
	 * @package app.Controller
	 */
	class Manifestationsbilansparcours66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Manifestationsbilansparcours66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Fileuploader',
			'Jetons2',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default2',
			'Fileuploader',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Manifestationbilanparcours66',
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
			'ajaxfileupload' => 'update',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'filelink' => 'read',
			'fileview' => 'read',
			'index' => 'read',
		);

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
		 * Fonction permettant de visualiser les fichiers chargés dans la vue avant leur envoi sur le serveur
		 *
		 * @param integer $id
		 */
		public function fileview( $id ) {
			$this->Fileuploader->fileview( $id );
		}

		/**
		 * Téléchargement des fichiers préalablement associés
		 *
		 * @param integer $fichiermodule_id
		 */
		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}

		/**
		 *
		 */
		protected function _setOptions() {
			$options = $this->Manifestationbilanparcours66->enums();

			$options = array_merge(
				$this->Manifestationbilanparcours66->Bilanparcours66->enums(),
				$options
			);

			$this->set( 'options', $options );
		}


		/**
		 * Fonction permettant d'accéder à la page pour lier les fichiers à une manifestation d'allocataire
		 * (CG 66).
		 *
		 * @param integer $id
		 */
		public function filelink( $id ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$manifestationbilanparcours66 = $this->Manifestationbilanparcours66->find(
				'first',
				array(
					'fields' => array(
						'Personne.id',
						'Foyer.dossier_id',
						'Manifestationbilanparcours66.id',
						'Manifestationbilanparcours66.haspiecejointe',
						'Manifestationbilanparcours66.bilanparcours66_id',
					),
					'joins' => array(
						$this->Manifestationbilanparcours66->join('Bilanparcours66'),
						$this->Manifestationbilanparcours66->Bilanparcours66->join('Personne'),
						$this->Manifestationbilanparcours66->Bilanparcours66->Personne->join('Foyer'),
					),
					'conditions' => array(
						'Manifestationbilanparcours66.id' => $id
					),
					'contain' => array(
						'Fichiermodule' => array(
							'fields' => array( 'name', 'id', 'created', 'modified' )
						)
					)
				)
			);

			$personne_id = Hash::get($manifestationbilanparcours66, 'Personne.id');
			$dossier_id = Hash::get($manifestationbilanparcours66, 'Foyer.dossier_id');
			$bilanparcours66_id = Hash::get($manifestationbilanparcours66, 'Manifestationbilanparcours66.bilanparcours66_id');

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );
			$this->set( 'urlmenu', '/bilansparcours66/index/'.$personne_id );

			$fichiers = array();
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $bilanparcours66_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Manifestationbilanparcours66->begin();

				$saved = $this->Manifestationbilanparcours66->updateAllUnBound(
					array( 'Manifestationbilanparcours66.haspiecejointe' => '\''.$this->request->data['Manifestationbilanparcours66']['haspiecejointe'].'\'' ),
					array(
						'"Manifestationbilanparcours66"."bilanparcours66_id"' => $bilanparcours66_id,
						'"Manifestationbilanparcours66"."id"' => $id
					)
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, !Set::classicExtract( $this->request->data, "Manifestationbilanparcours66.haspiecejointe" ), $id ) && $saved;
				}

				if( $saved ) {
					$this->Manifestationbilanparcours66->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( $this->referer() );
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Manifestationbilanparcours66->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->_setOptions();
			$this->set( compact( 'dossier_id', 'personne_id', 'fichiers', 'manifestationbilanparcours66' ) );
		}

		/**
		 *
		 * @param integer $bilanparcours66_id
		 */
		public function index( $bilanparcours66_id ) {
			$this->assert( valid_int( $bilanparcours66_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Manifestationbilanparcours66->Bilanparcours66->personneId( $bilanparcours66_id ) ) ) );

			$bilanparcours66 = $this->Manifestationbilanparcours66->Bilanparcours66->find(
				'first',
				array(
					'fields' => array(
						'Bilanparcours66.personne_id'
					),
					'conditions' => array(
						'Bilanparcours66.id' => $bilanparcours66_id
					),
					'contain' => false
				)
			);
			$this->set( 'personne_id', $bilanparcours66['Bilanparcours66']['personne_id']  );
			$this->set( 'bilanparcours66_id', $bilanparcours66_id );

			$manifestationsbilansparcours66 = $this->Manifestationbilanparcours66->find(
				'all',
				array(
					'fields' => array_merge(
						$this->Manifestationbilanparcours66->fields(),
						array(
							$this->Manifestationbilanparcours66->Fichiermodule->sqNbFichiersLies( $this->Manifestationbilanparcours66, 'nb_fichiers_lies', 'Manifestationbilanparcours66' )
						)
					),
					'conditions' => array(
						'Manifestationbilanparcours66.bilanparcours66_id' => $bilanparcours66_id
					),
					'contain' => false
				)
			);
			$this->set( 'manifestationsbilansparcours66', $manifestationsbilansparcours66 );
			$this->_setOptions();
			$this->set( 'urlmenu', '/bilansparcours66/index/'.$bilanparcours66['Bilanparcours66']['personne_id']  );
		}


		/**
		 * Formulaire d'ajout d'un élémént.
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Formulaire de modification d'un <élément>.
		 *
		 * @param integer $id
		 * @throws NotFoundException
		 */
		public function edit( $id = null ) {
			if( $this->action == 'add' ) {
				$bilanparcours66_id = $id;
			}
			else {
				$this->Manifestationbilanparcours66->id = $id;
				$bilanparcours66_id = $this->Manifestationbilanparcours66->field( 'bilanparcours66_id' );
			}
			$personne_id = $this->Manifestationbilanparcours66->Bilanparcours66->personneId( $bilanparcours66_id );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			// Le dossier auquel appartient la personne
			$dossier_id = $this->Manifestationbilanparcours66->Bilanparcours66->Personne->dossierId( $personne_id );

			// On s'assure que l'id passé en paramètre et le dossier lié existent bien
			if( empty( $personne_id ) || empty( $dossier_id ) ) {
				throw new NotFoundException();
			}

			// Tentative d'acquisition du jeton sur le dossier
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $bilanparcours66_id ) );
			}

			// Tentative de sauvegarde du formulaire
			if( !empty( $this->request->data ) ) {
				$this->Manifestationbilanparcours66->begin();

				if( $this->Manifestationbilanparcours66->save( $this->request->data , array( 'atomic' => false ) ) ) {
					$this->Manifestationbilanparcours66->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index', $bilanparcours66_id ) );
				}
				else {
					$this->Manifestationbilanparcours66->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			if( empty( $this->request->data ) && $this->action === 'edit' ) {
				$this->request->data = $this->Manifestationbilanparcours66->find(
					'first',
					array(
						'conditions' => array(
							'Manifestationbilanparcours66.id' => $id
						),
						'contain' => false
					)
				);
			}
			$this->set('personne_id', $personne_id);
			$this->set('bilanparcours66_id', $bilanparcours66_id);
			$this->render( 'edit' );
			$this->set( 'urlmenu', '/bilansparcours66/index/'.$personne_id );
		}

		/**
		 * Suppression d'un enregistrement.
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$dossier_id = $this->Manifestationbilanparcours66->Bilanparcours66->dossierId( $id );

			$this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) );

			$this->Jetons2->get( $dossier_id );

			$this->Manifestationbilanparcours66->begin();

			$success = $this->Manifestationbilanparcours66->delete( $id );

			if( $success ) {
				$this->Manifestationbilanparcours66->commit();
				$this->Jetons2->release( $dossier_id );
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Manifestationbilanparcours66->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->redirect( $this->referer() );
		}
	}
?>
