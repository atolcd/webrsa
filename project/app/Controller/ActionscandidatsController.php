<?php
	/**
	 * Code source de la classe ActionscandidatsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
     App::uses( 'OccurencesBehavior', 'Model/Behavior' );
	 App::uses( 'AppController', 'Controller' );

	/**
	 * La classe ActionscandidatsController ...
	 *
	 * @package app.Controller
	 */
	class ActionscandidatsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Actionscandidats';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'Fileuploader',
			'Search.SearchPrg' => array(
				'actions' => array('index')
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Xform',
			'Default',
			'Theme',
			'Default2',
			'Fileuploader',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Actioncandidat',
			'Option',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Actionscandidats:edit',
			'view' => 'Actionscandidats:index',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajax_getLastNumcodefamille',
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
			'ajax_getLastNumcodefamille' => 'read',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'create',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'fileview' => 'read',
			'index' => 'read',
			'view' => 'read',
		);

		protected function _setOptions() {
			$options = $this->Actioncandidat->enums();

			$options['Actioncandidat']['dreesactionscer_id'] = $this->Actioncandidat->Dreesactionscer->find(
				 'list',
				  array(
				  		'fields' => array( 'id', 'lib_dreesactioncer' ),
						'conditions' => array( 'Dreesactionscer.actif' => '1' )
				  )
				 );

			$options['Actioncandidat']['eligiblefse'] = array(0=>'Non', 1=>'Oui');

			if( $this->action != 'index' ) {
				$options['Actioncandidat']['referent_id'] = $this->Actioncandidat->ActioncandidatPersonne->Referent->find('list');

				$options['Zonegeographique'] = $this->Actioncandidat->Zonegeographique->find( 'list' );
				$zonesselected = $this->Actioncandidat->Zonegeographique->find( 'list', array( 'fields' => array( 'id' ) ) );
				$this->set( compact( 'zonesselected' ) );


				$conditionsChargeinsertionSecretaire = Configure::read( 'Chargeinsertion.Secretaire.group_id' );
				if( $conditionsChargeinsertionSecretaire != NULL ) {
					$conditionsChargeinsertion = array(
						'Chargeinsertion.nom IS NOT NULL',
						"Chargeinsertion.group_id" => $conditionsChargeinsertionSecretaire
					);

					$conditionsSecretaire = array(
						'Secretaire.nom IS NOT NULL',
						"Secretaire.group_id" => $conditionsChargeinsertionSecretaire
					);
				}
				else {
					$conditionsChargeinsertion = $conditionsSecretaire = array();
				}
				$options['Actioncandidat']['chargeinsertion_id'] = $this->Actioncandidat->Chargeinsertion->find(
					'list',
					array(
						'fields' => array( 'id', 'nom_complet' ),
						'conditions' => $conditionsChargeinsertion,
						'order' => 'Chargeinsertion.nom ASC'
					)
				);
				$options['Actioncandidat']['secretaire_id'] = $this->Actioncandidat->Secretaire->find(
					'list',
					array(
						'fields' => array( 'id', 'nom_complet' ),
						'conditions' => $conditionsSecretaire,
						'order' => 'Secretaire.nom ASC'
					)
				);
			}
            $this->set( 'cantons', ClassRegistry::init( 'Canton' )->selectList() );

            $motifssortie = $this->Actioncandidat->Motifsortie->find( 'list', array( 'fields' => array( 'Motifsortie.name'  ) ) );
            $this->set( 'motifssortie', $motifssortie );
			foreach( array( 'Contactpartenaire') as $linkedModel ) {
				$field = Inflector::singularize( Inflector::tableize( $linkedModel ) ).'_id';
				$options = Hash::insert( $options, "{$this->modelClass}.{$field}", $this->{$this->modelClass}->{$linkedModel}->find( 'list' ) );
			}
			$this->set( compact( 'options' ) );
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
		*   Ajout à la suite de l'utilisation des nouveaux helpers
		*   - default.php
		*   - theme.php
		*/

		public function index() {
			$this->Actioncandidat->recursive = -1;

			$messages = array();
			if( 0 === $this->Actioncandidat->Partenaire->find( 'count' ) ) {
				$msg = 'Merci de renseigner au moins un partenaire / prestataire avant de renseigner une action d\'insertion.';
				$messages[$msg] = 'error';
			}
			if( 0 === $this->Actioncandidat->Partenaire->Contactpartenaire->find( 'count' ) ) {
				$msg = 'Merci de renseigner au moins un contact pour le partenaire / prestataire avant de renseigner une action d\'insertion.';
				$messages[$msg] = 'error';
			}
			$this->set( compact( 'messages' ) );

            if( !empty( $this->request->data ) ) {
                $querydata = $this->Actioncandidat->search( $this->request->data );
                $this->paginate = $querydata;
                $actionscandidats = $this->paginate( 'Actioncandidat' );
                $this->set( compact('actionscandidats'));
            }

			$this->_setOptions();
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
		 * Formulaire d'ajout et de modification d'une action d'insertion
		 *
		 * @param integer $id
		 */
		protected function _add_edit($id = null){
			$actioncandidat = array();
			$fichiers = array();

			if( !empty( $this->request->data ) ) {
				$this->Actioncandidat->begin();
				$saved = $this->Actioncandidat->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) );
				if( $saved ) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers(
						$dir,
						!Set::classicExtract( $this->request->data, "Actioncandidat.haspiecejointe" ),
						( ( $this->action == 'add' ) ? $this->Actioncandidat->id : $id )
					) && $saved;
				}

				if( $saved ) {
					$this->Actioncandidat->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array(  'controller' => 'actionscandidats','action' => 'index' ) );
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id, false );
					$this->Actioncandidat->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			elseif( $this->action == 'edit' ) {
				$actioncandidat = $this->Actioncandidat->find(
					'first',
					array(
						'fields' => $this->Actioncandidat->fields(),
						'conditions' => array(
							'Actioncandidat.id' => $id
						),
						'contain' => array(
							'Fichiermodule',
							'Zonegeographique',
							'Motifsortie'
						)
					)
				);

				if(true === empty($actioncandidat)) {
					throw new NotFoundException();
				}

				$this->request->data = $actioncandidat;
			}

			$fichiersEnBase = array();
			if(false === empty($id)) {
				$fichiersEnBase = Hash::extract(
					$this->Fileuploader->fichiersEnBase( $id ),
					'{n}.Fichiermodule'
				);
			}

			$this->set( compact( 'actioncandidat', 'fichiers', 'fichiersEnBase' ) );
			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		/**
		*
		*/

		public function delete( $id ) {
			$this->Default->delete( $id );
		}

		/**
		*
		*/

		public function view( $id ) {
			$this->Default->view( $id );
		}

		/**
		 * Permet à partir du themecode et de codefamille de trouver le dernier
		 * numéro du code famille
		 */
		public function ajax_getLastNumcodefamille() {
			$themecode = Hash::get($this->request->data, 'themecode');

			if ( true === valid_int( $themecode ) ) {
				$query = array(
					'fields' => 'Actioncandidat.numcodefamille',
					'contain' => false,
					'conditions' => array(
						'Actioncandidat.themecode' => $themecode,
						'Actioncandidat.codefamille' => Hash::get($this->request->data, 'codefamille'),
						'Actioncandidat.numcodefamille ~ \'^[0-9]+$\''
					),
					'order' => array(
						'Actioncandidat.numcodefamille' => 'DESC'
					)
				);

				$result = Hash::get($this->Actioncandidat->find('first', $query), 'Actioncandidat.numcodefamille');
				echo $result ? $result : 'Aucun numéro du code famille n\'a été trouvé';
			}
			else {
				echo 'Erreur: le thème de l\'action doit etre un nombre entier';
			}

			exit;
		}
	}
?>