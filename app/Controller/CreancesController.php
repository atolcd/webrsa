<?php
	/**
	 * Code source de la classe CreancesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'LocaleHelper', 'View/Helper' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'WebrsaAccessCreances', 'Utility' );

	/**
	 * La classe CreancesController ...
	 *
	 * @package app.Controller
	 */
	class CreancesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Creances';

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
			'Fileuploader',
			'Search.SearchPrg' => array(
				'actions' => array(
					'dossierEntrantsCreanciers',
				),
			)
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
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Fileuploader',
			'Cake1xLegacy.Ajax',
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
			'dossierEntrantsCreanciers' => 'read',
			'exportcsv' => 'read'
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Creance',
			'WebrsaCreance',
			'Dossier',
			'Foyer',
			'Personne',
			'Situationdossierrsa',
			'Calculdroitrsa',
			'Option'
			);

        protected function _setOptions() {
			$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa') );
			$this->set( 'droitdevoirs', ClassRegistry::init('Calculdroitrsa')->enum('toppersdrodevorsa') );
			$this->set( 'orgcre', ClassRegistry::init('Creance')->enum('orgcre') );
			$this->set( 'motiindu', ClassRegistry::init('Creance')->enum('motiindu') );
			$this->set( 'natcre', ClassRegistry::init('Creance')->enum('natcre') );
			$this->set( 'oriindu', ClassRegistry::init('Creance')->enum('oriindu') );
			$this->set( 'respindu', ClassRegistry::init('Creance')->enum('respindu') );
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
		 * @param integer $foyer_id L'id technique du Foyer pour lequel on veut les Creances.
		 *
		 */
		public function index($foyer_id) {
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id )));

			$this->set( 'options', $this->Creance->enums() );

			$creances = $this->WebrsaAccesses->getIndexRecords(
				$foyer_id,
				array(
					'fields' => array_merge(
						$this->Creance->fields(),
						array(
							$this->Creance->Fichiermodule->sqNbFichiersLies( $this->Creance, 'nb_fichiers_lies' )
						)
					),
					'conditions' => array(
						'Creance.foyer_id' => $foyer_id
					),
					'order' => array(
						'Creance.dtimplcre DESC',
					),
					'contain' => FALSE
				)
			);

			// Assignations à la vue
			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'creances', $creances );
			$this->set( 'urlmenu', '/creances/index/'.$foyer_id );
		}

		/**
		*
		*/
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		*
		*/
		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Ajouter une creances à un foyer
		 *
		 * @param integer $foyer_id L'id technique du foyer auquel ajouter la créance
		 * @return void
		 */
		protected function _add_edit($id = null) {
			if($this->action == 'add' ) {
				$foyer_id = $id;
				$id = null;
				$dossier_id = $this->Creance->Foyer->dossierId( $foyer_id );
			}elseif($this->action == 'edit' ){
				$this->WebrsaAccesses->check($id);
				$this->Creance->id = $id;
				$foyer_id = $this->Creance->field( 'foyer_id' );
				$dossier_id = $this->Creance->dossierId( $id );
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
				$this->Creance->begin();
				$data = $this->request->data;
				
				if ( $data['Creance']['mtsolreelcretrans'] == '' ||  $data['Creance']['mtinicre'] =='' ) {
					$this->Creance->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}else{
					if($this->action == 'add' ) {
						$data['Creance']['foyer_id'] = $foyer_id;
					}
					if( $this->Creance->saveAll( $data, array( 'validate' => 'only' ) ) ) {
						if( $this->Creance->save( $data ) ) {
							$this->Creance->commit();
							$this->Jetons2->release( $dossier_id );
							$this->Flash->success( __( 'Save->success' ) );
							$this->redirect( array( 'controller' => 'Creances', 'action' => 'index', $foyer_id ) );
						}
						else {
							$this->Creance->rollback();
							$this->Flash->error( __( 'Save->error' ) );
						}
					}
				}
			}
			// Affichage des données
			elseif($this->action == 'edit' ) {
				$creance = $this->Creance->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Creance->fields()
						),
						'conditions' => array(
							'Creance.id' => $id
						),
						'contain' => FALSE
					)
				);
				if (!empty( $creance ) ){
					// Assignation au formulaire
					$this->request->data = $creance;		
				}
			}

			// Assignation à la vue
			$this->set( 'options', $this->Creance->enums() );
			$this->set( 'urlmenu', '/creances/index/'.$foyer_id );
			$this->set( 'foyer_id', $foyer_id );
			$this->render( 'add_edit' );
			
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
			$dossier_id = $this->Creance->dossierId( $id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$fichiers = array();

			$creances = $this->Creance->find(
				'first',
				array(
					'conditions' => array(
						'Creance.id' => $id
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
				$this->Creance->id = $id;
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $this->Creance->field( 'foyer_id' )) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Creance->begin();

				$saved = $this->Creance->updateAllUnBound(
					array( 'Creance.haspiecejointe' => '\''.$this->request->data['Creances']['haspiecejointe'].'\'' ),
					array( '"Creance"."id"' => $id)
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, Set::classicExtract( $this->request->data, "Creance.haspiecejointe" ), $id ) && $saved;
				}

				if( $saved ) {
					$this->Creance->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('action' => 'filelink', $id));
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Creance->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->set( 'options', (array)Hash::get( $this->Creance->enums(), 'Creance' ) );
			$this->set( compact( 'dossier_id', 'personne_id', 'fichiers', 'creances' ) );
		}

		/**
		 *
		 */
		public function dossierEntrantsCreanciers() {
			$options = array(
				'annees' => array( 'minYear' => date( 'Y', strtotime('01-01-2009') ), 'maxYear' => date( 'Y' ) ),
			);
			$this->set( compact( 'options' ) );

			if( !empty( $this->request->data ) ) {
				$this->Dossier->begin(); // Pour les jetons

				$paginate = $this->Creance->search( $this->request->data );
				$paginate['limit'] = 15;

				$this->paginate = $paginate;
				$dossierEntrantsCreanciers = $this->paginate( 'Creance' );

				$this->set( 'dossierEntrantsCreanciers', $dossierEntrantsCreanciers );

				$this->Dossier->commit();
			}
            $this->_setOptions();
		}

		/**
		 *
		 */
		public function exportcsv() {
			$options = $this->Creance->search( Hash::expand( $this->request->params['named'], '__' ) );

			unset( $options['limit'] );
			$dossierEntrantsCreanciers = $this->Creance->find( 'all', $options );

            $this->_setOptions();
			$this->layout = '';
			$this->set( compact( 'headers', 'dossierEntrantsCreanciers' ) );
		}

	}
?>
