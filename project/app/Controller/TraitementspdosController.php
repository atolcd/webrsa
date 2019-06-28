<?php
	/**
	 * Code source de la classe TraitementspdosController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe TraitementspdosController ...
	 *
	 * @package app.Controller
	 */
	class TraitementspdosController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Traitementspdos';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'Fileuploader',
			'Gedooo.Gedooo',
			'Jetons2',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Default2',
			'Fileuploader',
			'Locale',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Traitementpdo',
			'Descriptionpdo',
			'Dossier',
			'Fichiermodule',
			'Personne',
			'Propopdo',
			'Traitementtypepdo',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'view' => 'Traitementspdos:index',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajaxfiledelete',
			'ajaxfileupload',
			'ajaxstatutpersonne',
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
			'ajaxstatutpersonne' => 'read',
			'clore' => 'read',
			'delete' => 'delete',
			'download' => 'read',
			'fileview' => 'read',
			'index' => 'read',
			'printCourrier' => 'update',
			'view' => 'read',
		);

		/**
		 *
		 */
		protected function _options() {
			$options = $this->{$this->modelClass}->enums();

			$this->set(
					'listcourrier', $this->Traitementpdo->Courrierpdo->find(
							'all', array(
						'contain' => array(
							'Textareacourrierpdo' => array(
								'order' => 'Textareacourrierpdo.ordre ASC'
							)
						)
							)
					)
			);

			$options[$this->modelClass]['descriptionpdo_id'] = $this->Descriptionpdo->find( 'list' );
			$options[$this->modelClass]['traitementtypepdo_id'] = $this->Traitementtypepdo->find( 'list' );
			$this->set( 'gestionnaire', $this->User->find(
							'list', array(
						'fields' => array(
							'User.nom_complet'
						),
						'conditions' => array(
							'User.isgestionnaire' => 'O'
						)
							)
					)
			);
			$options[$this->modelClass]['listeDescription'] = $this->Descriptionpdo->find( 'all', array( 'contain' => false ) );
			$this->set( 'cloture', array( 0 => 'Non', 1 => 'Oui' ) );
			return $options;
		}

		/**
		 *
		 */
		public function index( $id = null ) {
			$traitementspdos = $this->{$this->modelClass}->find(
					'all', array(
				'conditions' => array(
					'propopdo_id' => $id
				),
				'contain' => false
					)
			);
			$this->set( compact( 'traitementspdos' ) );

			// Dossier
			$qd_pdo = array(
				'conditions' => array(
					'Propopdo.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$pdo = $this->{$this->modelClass}->Propopdo->find( 'first', $qd_pdo );

			$this->set( 'pdo', $pdo );

			$personne_id = Set::classicExtract( $pdo, 'Propopdo.personne_id' );
			$pdo_id = Set::classicExtract( $pdo, 'Propopdo.id' );

			$this->set( 'personne_id', $personne_id );
			$this->set( 'pdo_id', $pdo_id );
			$this->set( 'options', $this->_options() );
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
		 *
		 */
		public function view( $id ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$traitementpdo = $this->Traitementpdo->find(
					'first', array(
				'conditions' => array(
					'Traitementpdo.id' => $id,
				),
				'contain' => array(
					'Propopdo' => array(
						'fields' => array(
							'Propopdo.personne_id'
						)
					),
					'Descriptionpdo' => array(
						'fields' => array(
							'Descriptionpdo.name'
						)
					),
					'Traitementtypepdo' => array(
						'fields' => array(
							'Traitementtypepdo.name'
						)
					),
					'Personne' => array(
						'fields' => array(
							'Personne.nom',
							'Personne.prenom',
						)
					),
					'Fichiertraitementpdo' => array(
						'fields' => array(
							'Fichiertraitementpdo.name',
							'Fichiertraitementpdo.type',
							'Fichiertraitementpdo.created',
							'Fichiertraitementpdo.traitementpdo_id',
						)
					),
					'Courrierpdo' => array(
						'fields' => array(
							'Courrierpdo.id',
							'Courrierpdo.name'
						)
					),
					'Fichiermodule'
				)
					)
			);
			$this->assert( !empty( $traitementpdo ), 'invalidParameter' );
			// Retour à la page d'édition de la PDO
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'propospdos', 'action' => 'edit', Set::classicExtract( $traitementpdo, 'Traitementpdo.propopdo_id' ) ) );
			}

			$this->set( 'dossier_id', $this->Traitementpdo->dossierId( $id ) );

			$options = $this->Traitementpdo->enums();
			$this->set( compact( 'traitementpdo', 'options' ) );
			$this->set( 'urlmenu', '/propospdos/index/'.$traitementpdo['Propopdo']['personne_id'] );
		}

		/**
		 *
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * FIXME: traiter le bouton "Retour"
		 */
		protected function _add_edit( $id = null ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$fichiers = array( );
			$this->set( 'options', $this->_options() );

			// Récupération des id afférents
			if( $this->action == 'add' ) {
				$propopdo_id = $id;

				$qd_propopdo = array(
					'conditions' => array(
						'Propopdo.id' => $id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$propopdo = $this->Propopdo->find( 'first', $qd_propopdo );


				$this->set( 'propopdo', $propopdo );
				$personne_id = Set::classicExtract( $propopdo, 'Propopdo.personne_id' );
				$dossier_id = $this->Personne->dossierId( $personne_id );
			}
			else if( $this->action == 'edit' ) {
				$traitement_id = $id;
				$traitement = $this->Traitementpdo->find(
						'first', array(
					'conditions' => array(
						'Traitementpdo.id' => $traitement_id
					),
					'contain' => array(
						'Propopdo',
						'Descriptionpdo',
						'Traitementtypepdo',
						'Saisinepdoep66',
						'Courrierpdo'
					)
						)
				);

				$this->assert( !empty( $traitement ), 'invalidParameter' );

				$propopdo_id = Set::classicExtract( $traitement, 'Traitementpdo.propopdo_id' );
				$personne_id = Set::classicExtract( $traitement, 'Propopdo.personne_id' );
				$dossier_id = $this->Personne->dossierId( $personne_id );
			}

			$personnes = $this->Personne->Foyer->Dossier->find(
					'all', array(
				'fields' => array(
					'Personne.id',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom'
				),
				'conditions' => array(
					'Dossier.id' => $dossier_id
				),
				'recursive' => -1,
				'joins' => array(
					array(
						'table' => 'foyers',
						'alias' => 'Foyer',
						'type' => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Dossier.id = Foyer.dossier_id' )
					),
					array(
						'table' => 'personnes',
						'alias' => 'Personne',
						'type' => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Personne.foyer_id = Foyer.id' )
					)
				)
					)
			);
			$listepersonnes = array( );
			foreach( $personnes as $personne ) {
				$listepersonnes[$personne['Personne']['id']] = implode(
						' ', array(
					$personne['Personne']['qual'],
					$personne['Personne']['nom'],
					$personne['Personne']['prenom']
						)
				);
			}
			$this->set( compact( 'listepersonnes' ) );

			$this->assert( !empty( $dossier_id ), 'invalidParameter' );
			$this->set( 'personne_id', $personne_id );
			$this->set( 'dossier_id', $dossier_id );
			$this->set( 'propopdo_id', $propopdo_id );

			$this->Jetons2->get( $dossier_id );

			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->Fileuploader->deleteDir();
				$this->redirect( array( 'controller' => 'propospdos', 'action' => 'edit', $id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Traitementpdo->begin();

				if( $this->Traitementpdo->saveAll( $this->request->data, array( 'validate' => 'only', 'atomic' => false ) ) ) {
					$saved = $this->Traitementpdo->sauvegardeTraitement( $this->request->data );

					if( $saved ) {
						// Début sauvegarde des fichiers attachés, en utilisant le Component Fileuploader
						$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
						$saved = $this->Fileuploader->saveFichiers(
										$dir, !Set::classicExtract( $this->request->data, "Traitementpdo.haspiecejointe" ), ( ( $this->action == 'add' ) ? $this->Traitementpdo->id : $id )
								) && $saved;
					}

					if( $saved ) {
						$this->Traitementpdo->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'controller' => 'propospdos', 'action' => 'edit', $propopdo_id ) );
					}
					else {
						$this->Traitementpdo->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );

					$this->Traitementpdo->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $traitement;
				$fichiers = $this->Fileuploader->fichiers( $id );
			}

			$traitementspdosouverts = $this->{$this->modelClass}->find(
					'all', array(
				'conditions' => array(
					'Traitementpdo.propopdo_id' => $id,
					'Traitementpdo.clos' => 0
				)
					)
			);
			$this->set( compact( 'traitementspdosouverts', 'fichiers' ) );
			$this->set( 'urlmenu', '/propospdos/index/'.$personne_id );

			$this->render( 'add_edit' );
		}

		/**
		 *
		 */
		public function ajaxstatutpersonne( $personne_id = null ) {
			$dataTraitementpdo_id = Set::extract( $this->request->data, 'Traitementpdo.personne_id' );
			$personne_id = ( empty( $personne_id ) && !empty( $dataTraitementpdo_id ) ? $dataTraitementpdo_id : $personne_id );
			$personne = $this->Traitementpdo->Propopdo->find(
					'first', array(
				'conditions' => array(
					'Propopdo.personne_id' => $personne_id
				),
				'contain' => array(
					'Statutpdo'
				),
				'order' => array(
					'Propopdo.datereceptionpdo DESC'
				)
					)
			);
			$this->set( 'values', $personne );
			Configure::write( 'debug', 0 );
			$this->render( 'statutpersonne', 'ajax' );
		}

		/**
		 *
		 */
		public function clore( $id = null ) {
			$traitementpdo = $this->Traitementpdo->find(
					'first', array(
				'conditions' => array(
					'Traitementpdo.id' => $id
				)
					)
			);
			$this->assert( !empty( $traitementpdo ), 'invalidParameter' );

			$this->Traitementpdo->id = $id;
			$this->Traitementpdo->saveField( 'clos', Configure::read( 'traitementClosId' ) );
			$this->redirect( array( 'controller' => 'propospdos', 'action' => 'edit', $traitementpdo['Traitementpdo']['propopdo_id'] ) );
		}

		/**
		 *
		 */
		public function delete( $id = null ) {
			$traitementpdo = $this->Traitementpdo->find(
					'first', array(
				'conditions' => array(
					'Traitementpdo.id' => $id
				)
					)
			);
			$this->assert( !empty( $traitementpdo ), 'invalidParameter' );

			$this->Traitementpdo->delete( $id );
			$this->redirect( array( 'controller' => 'propospdos', 'action' => 'edit', $traitementpdo['Traitementpdo']['propopdo_id'] ) );
		}

		/**
		 *   Enregistrement du courrier de proposition lors de l'enregistrement de la proposition
		 */
		public function printCourrier( $courrierpdo_traitementpdo_id ) {

			$this->assert( !empty( $courrierpdo_traitementpdo_id ), 'error404' );

			$courrierpdotraitementpdo = $this->Traitementpdo->CourrierpdoTraitementpdo->find(
					'first', array(
				'conditions' => array(
					'CourrierpdoTraitementpdo.id' => $courrierpdo_traitementpdo_id
				)
					)
			);
			$name = Set::classicExtract( $courrierpdotraitementpdo, 'Courrierpdo.modeleodt' );

			$pdf = $this->Traitementpdo->CourrierpdoTraitementpdo->getStoredPdf( $courrierpdo_traitementpdo_id );

			$this->assert( !empty( $pdf ), 'error404' );
			$this->assert( !empty( $pdf['Pdf']['document'] ), 'error500' ); // FIXME: ou en faire l'impression ?

			$this->Gedooo->sendPdfContentToClient( $pdf['Pdf']['document'], $name.".pdf" );
		}

		/**
		 *   Téléchargement des fichiers préalablement associés à un traitement donné
		 */
		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}

	}
?>