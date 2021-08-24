<?php
	/**
	 * Code source de la classe TutorielsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe TutorielsController ...
	 *
	 * @package app.Controller
	 */
	class TutorielsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Tutoriels';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Tutoriel', 'Option', 'Fichiermodule' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Tutoriels:edit'
		);

		/**
		 * Liste des tables à ne pas prendre en compte dans les enregistrements
		 * vérifiés pour éviter les suppressions en cascade intempestives.
		 *
		 * @var array
		 */
		public $blacklist = array();

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Fileuploader',
			'Default',
			'WebrsaParametrages'
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Locale',
			'Default2',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Fileuploader',
			'Cake1xLegacy.Ajax'
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajaxfileupload',
			'ajaxfiledelete',
			'fileview',
			'download',
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
		 * Requête à utiliser pour lister les enregistrements par parent puis par
		 * ordre alphabétique.
		 *
		 * @return array
		 */
		protected function _query() {
			$query = array(
				'fields' => array_merge(
					$this->Tutoriel->fields(),
					array( 'Parent.titre' )
				),
				'recursive' => -1,
				'joins' => array(
					$this->Tutoriel->join( 'Parent', array( 'type' => 'LEFT OUTER' ) )
				),
				'order' => array(
					'( CASE WHEN Tutoriel.parentid IS NULL THEN Tutoriel.id ELSE Tutoriel.parentid END) ASC',
					'( CASE WHEN Tutoriel.parentid IS NULL THEN 0 ELSE 1 END) ASC',
					'Tutoriel.rg ASC'
				)
			);

			return $query;
		}

		/**
		 * Liste des tutoriels
		 *
		 */
		public function index() {
			$query = $this->_query();
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire d'ajout d'un tutoriel.
		 *
		 * @see TutorielsController::_add_edit()
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Formulaire de modification d'un tutoriel.
		 *
		 * @see TutorielsController::_add_edit()
		 */
		public function edit($id) {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		protected function _add_edit($id = null) {
			$redirectUrl = array( 'action' => 'index');

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( $redirectUrl );
			}

			// Tentative de sauvegarde
			if( !empty( $this->request->data ) ) {
				// Récupération du fichier sur disque
				$idFile = '';
				if ($this->action == 'edit' ) {
					$idFile = $id;
				}

				$hasFichier = !empty($this->Fileuploader->_fichiersSurDisque($idFile));

				if( $hasFichier == false ) {
					$this->Tutoriel->begin();
					$datas = $this->request->data['Tutoriel'];
					if ($this->action == 'edit' ) {
						$datas['id'] = $id;
					}
					$success = $this->Tutoriel->save($datas);
				} else {
					$this->Tutoriel->begin();
					$datas = $this->request->data['Tutoriel'];

					if ($this->action == 'edit' ) {
						$datas['id'] = $id;
					}
					$this->Tutoriel->save($datas);
					$idTuto = $this->Tutoriel->id;
					$dir = $this->Fileuploader->dirFichiersModule($this->action, $idFile);
					$success = $this->Fileuploader->saveFichiers($dir, false, $idTuto);
					$datas['id'] = $idTuto;
					$datas['fichiermodule_id'] = $this->Tutoriel->Fichiermodule->id;
					$success = $success && $this->Tutoriel->save($datas);
				}

				if($success) {
					$this->Tutoriel->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( $redirectUrl );
				} else {
					if($hasFichier) {
						$this->Fileuploader->fichiers( $id );
					}
					$this->Tutoriel->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$tutoriel = array();
			if ($this->action == 'edit' && empty($this->request->data)) {
				// Ajout des informations existantes
				$tutoriel = $this->Tutoriel->findById($id);
				$this->request->data = $tutoriel;

				if( !empty($tutoriel['Tutoriel']['fichiermodule_id']) ) {
					$fichierPresent = array(
						array(
							'name' => $tutoriel['Fichiermodule']['name'],
							'id' => $tutoriel['Fichiermodule']['id'],
							'created' => $tutoriel['Fichiermodule']['created'],
							'modified' => $tutoriel['Fichiermodule']['modified']
						)
					);
				}
			}

			$options['Tutoriel']['parentid'] = $this->Tutoriel->find('list', array(
				'fields' => array(
					'Tutoriel.id',
					'Tutoriel.titre'
				),
				'conditions' => array(
					'Tutoriel.actif' => 1,
					'Tutoriel.parentid IS NULL'
				)
			));

			$this->set( compact( 'fichierPresent', 'tutoriel', 'options') );
			$this->render( 'add_edit' );
		}

		public function delete($id) {
			$redirectUrl = array( 'action' => 'index');

			// Récupération de l'id du fichier lié si exitantant
			$tutoriel = $this->Tutoriel->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'Tutoriel.id' => $id
				)
			));

			$success = $this->Tutoriel->delete($id);
			if( !empty($tutoriel['Tutoriel']['fichiermodule_id']) ) {
				$success = $this->Tutoriel->Fichiermodule->delete($tutoriel['Tutoriel']['fichiermodule_id'], false) && $success;
			}


			if ($success) {
				$this->Flash->success( __( 'Delete->success' ) );
			} else {
				$this->Flash->error( __( 'Delete->error' ) );
			}
			$this->redirect( $redirectUrl );
		}

		public function view() {
			$query = $this->_query();

			// Ajout de la condition pour n'avoir que les tutoriels actifs
			$query['conditions'] = array('Tutoriel.actif' => true);

			$tutoriels = $this->Tutoriel->find('all', $query);

			$this->set('tutoriels', $tutoriels);
		}
	}