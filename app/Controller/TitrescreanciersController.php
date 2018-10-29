<?php
	/**
	 * Code source de la classe TitrescreanciersController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'LocaleHelper', 'View/Helper' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'WebrsaAccessTitrescreanciers', 'Utility' );

	/**
	 * La classe TitrescreanciersController ...
	 *
	 * @package app.Controller
	 */
	class TitrescreanciersController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Titrescreanciers';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
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
				'className' => 'Default.DefaultDefault'
			),
			'Fileuploader',
			'Cake1xLegacy.Ajax'
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
			'filelink' => 'read',
			'fileview' => 'read',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'ajaxreffonct' => 'read',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Titrecreancier',
			'WebrsaTitrecreancier',
			'Creance',
			'WebrsaCreance',
			'Dossier',
			'Foyer',
			'Personne',
			'Option'
			);

        protected function _setOptions() {
			$this->set( 'qual', ClassRegistry::init('Titrecreancier')->enum('qual') );
		}

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
			'fileview',
		);

		/**
		 * Pagination sur les <éléments> de la table. *
		 * @param integer $creance_id L'id technique du Foyer pour lequel on veut les Creances.
		 *
		 */
		public function index($creance_id) {
			$foyer_id = $this->Titrecreancier->foyerId( $creance_id );
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id )));

			$this->set( 'options', $this->Titrecreancier->options() );

			$titresCreanciers = $this->WebrsaAccesses->getIndexRecords(
				$foyer_id,
				array(
					'fields' => array_merge(
						$this->Titrecreancier->fields()
						,array(
							$this->Titrecreancier->Fichiermodule->sqNbFichiersLies( $this->Titrecreancier, 'nb_fichiers_lies' )
						)
					),
					'conditions' => array(
						'Titrecreancier.creance_id' => $creance_id
					),
					'contain' => false,
					'order' => array(
						'Titrecreancier.dtemissiontitre DESC',
					)
				)
			);

			$creances = $this->Creance->find('all',
				array(
					'conditions' => array(
						'Creance.id ' => $creance_id
					),
					'contain' => false
				)
			);

			// Assignations à la vue
			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'creances', $creances );
			$this->set( 'titresCreanciers', $titresCreanciers );
			$this->set( 'urlmenu', '/creances/index/'.$foyer_id );
		}

		/**
		 * Ajouter une Titrecreancier à une Créance
		 *
		 * @param integer $foyer_id L'id technique de la creance auquel ajouter le Titrecreancier
		 * @return void
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Modification d'un Titrecreancier d'une creance.
		 *
		 * @param integer $id L'id technique dans la table titrescreanciers.
		 * @return void
		 */
		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Validation d'une Titrecreancier d'une Créance
		 *
		 * @param integer $id L'id technique dans la table titrescreanciers
		 * @return void
		 */
		public function valider() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Fonction commune d'ajout/modification d'un titrecreancier
		 *
		 * @param integer $id
		 * 		Soit l'id technique de la creance auquel ajouter le Titrecreancier
		 * 		Soit l'id technique dans la table titrescreanciers
		 * @return void
		 */
		public function _add_edit( $id = null ) {
			if($this->action == 'add' ) {
				$creance_id = $id;
				$foyer_id = $this->Titrecreancier->foyerId( $creance_id );
				$dossier_id = $this->Titrecreancier->dossierId( $creance_id );
			}elseif($this->action == 'edit' || $this->action == 'valider'){
				$this->WebrsaAccesses->check($id);
				$creance_id = $this->Titrecreancier->creanceId( $id );
				$foyer_id = $this->Titrecreancier->foyerId( $creance_id );
				$dossier_id = $this->Titrecreancier->dossierId( $creance_id );
			}
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Titrecreancier->id = $id;
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $creance_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Titrecreancier->begin();
				$data = $this->request->data;		
				if ( $data['Titrecreancier']['mnttitr'] == '' ) {
					$this->Titrecreancier->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}else{
					if( $this->Titrecreancier->saveAll( $data, array( 'validate' => 'only' ) ) ) {
						if( $this->Titrecreancier->saveAll( $data, array( 'atomic' => false ) ) ) {
							$this->Titrecreancier->commit();
							$this->Jetons2->release( $dossier_id );
							$this->Flash->success( __( 'Save->success' ) );
							if ( $this->action == 'add' ) {
								$this->Titrecreancier->query('UPDATE creances SET hastitrecreancier = hastitrecreancier+1 WHERE creances.id ='. $creance_id);
							}
							$this->redirect( array( 'action' => 'index', $creance_id ) );
						}
						else {
							$this->Titrecreancier->rollback();
							$this->Flash->error( __( 'Save->error' ) );
						}
					}
					else {
						$this->Titrecreancier->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
			}
			// Affichage des données
			elseif ($this->action != 'add' ) {
				$titrecreancier = $this->Titrecreancier->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Titrecreancier->fields()
						),
						'conditions' => array(
							'Titrecreancier.id' => $id
						),
						'contain' => FALSE
					)
				);

				// Mauvais paramètre
				$this->assert( !empty( $titrecreancier ), 'invalidParameter' );

				// Assignation au formulaire
				$this->request->data = $titrecreancier;
			}

			$this->set( 'options', $this->Titrecreancier->options() );
			$this->set( 'creance_id', $creance_id );
			$this->set( 'foyer_id', $foyer_id );

			$this->set( 'urlmenu', '/titrescreanciers/index/'.$creance_id );

			if($this->action == 'valider' ) {
				$this->render( 'valider' );
			}elseif($this->action == 'edit' || $this->action == 'add'){
				$this->render( 'add_edit' );
			}
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

			$creance_id = $this->Titrecreancier->creanceId( $id );
			$foyer_id = $this->Titrecreancier->foyerId( $creance_id );
			$dossier_id = $this->Titrecreancier->dossierId( $creance_id );

			$this->assert( !empty( $dossier_id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$fichiers = array();

			$titrescreanciers = $this->Titrecreancier->find(
				'first',
				array(
					'conditions' => array(
						'Titrecreancier.id' => $id
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
				$this->Titrecreancier->id = $id;
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $creance_id) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Titrecreancier->begin();

				$saved = $this->Titrecreancier->updateAllUnBound(
					array( 'Titrecreancier.haspiecejointe' => '\''.$this->request->data['Titrescreanciers']['haspiecejointe'].'\'' ),
					array( '"Titrecreancier"."id"' => $id)
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, Set::classicExtract( $this->request->data, "Titrecreancier.haspiecejointe" ), $id ) && $saved;
					//$saved = $this->Fileuploader->saveFichiers( $dir, false, $id ) && $saved;
				}

				if( $saved ) {
					$this->Titrecreancier->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('action' => 'filelink', $id));
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Titrecreancier->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->set( 'options', (array)Hash::get( $this->Titrecreancier->options(), 'Titrecreancier' ) );
			$this->set( compact( 'dossier_id', 'personne_id', 'fichiers', 'titrescreanciers' ) );
		}
	}
?>
