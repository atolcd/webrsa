<?php
	/**
	 * Code source de la classe PersonnesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'WebrsaAccessPersonnes', 'Utility' );

	/**
	 * La classe PersonnesController permet de gérer les personnes au sein d'un foyer RSA.
	 *
	 * @package app.Controller
	 */
	class PersonnesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Personnes';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
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
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Personne',
			'Foyer',
			'Grossesse',
			'Infocontactpersonne',
			'Option',
			'WebrsaPersonne',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Personnes:edit',
			'view' => 'Personnes:index',
			'histoinfocontactpersonne' => 'Personnes:coordonnees',
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
			'coordonnees' => 'update',
			'download' => 'read',
			'edit' => 'update',
			'filelink' => 'read',
			'fileview' => 'read',
			'index' => 'read',
			'view' => 'read',
			'histoinfocontactpersonne' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );
			$this->set( 'qual', $this->Option->qual() );
			$this->set( 'nationalite', ClassRegistry::init('Personne')->enum('nati') );
			$this->set( 'typedtnai', ClassRegistry::init('Personne')->enum('typedtnai') );
			$this->set( 'pieecpres', ClassRegistry::init('Personne')->enum('pieecpres') );
			$this->set( 'sexe', $this->Option->sexe() );
			$this->set( 'sitfam', $this->Option->sitfam() );
			$this->set( 'natfingro', ClassRegistry::init('Grossesse')->enum('natfingro') );
			$this->set( 'options', (array)Hash::get( $this->Personne->enums(), 'Personne' ) );
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
		 * Fonction permettant de visualiser les fichiers chargés dans la vue avant leur envoi sur le serveur
		 */
		public function fileview( $id ) {
			$this->Fileuploader->fileview( $id );
		}

		/**
		 * Téléchargement des fichiers préalablement associés à un traitement donné
		 */
		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}

		/**
		 *   Fonction permettant d'accéder à la page pour lier les fichiers à l'Orientation
		 */
		public function filelink( $id ) {
			$this->WebrsaAccesses->check($id);

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $id ) ) );

			$fichiers = array( );
			$personne = $this->Personne->find(
				'first',
				array(
					'conditions' => array(
						'Personne.id' => $id
					),
					'contain' => array(
						'Fichiermodule' => array(
							'fields' => array( 'name', 'id', 'created', 'modified' )
						)
					)
				)
			);

			$dossier_id = $this->Personne->dossierId( $id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );
			$foyer_id = Set::classicExtract( $personne, 'Personne.foyer_id' );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Personne->begin();

				$saved = $this->Personne->updateAllUnBound(
					array( 'Personne.haspiecejointe' => '\''.$this->request->data['Personne']['haspiecejointe'].'\'' ),
					array( '"Personne"."id"' => $id )
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, !Set::classicExtract( $this->request->data, "Personne.haspiecejointe" ), $id ) && $saved;
				}

				if( $saved ) {
					$this->Personne->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( $this->referer() );
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Personne->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->_setOptions();
			$this->set( 'urlmenu', '/personnes/view/'.$id );
			$this->set( compact( 'dossier_id', 'id', 'fichiers', 'personne' ) );
		}

		/**
		 *   Voir les personnes d'un foyer
		 */
		public function index( $foyer_id = null ) {
			// Vérification du format de la variable
			$this->assert( valid_int( $foyer_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );

			$personnes = $this->WebrsaAccesses->getIndexRecords(
				$foyer_id, array(
					'fields' => array(
						'Personne.id',
						'Personne.qual',
						'Personne.nom',
						'Personne.prenom',
						'Personne.prenom2',
						'Personne.prenom3',
						'Personne.dtnai',
						'Prestation.rolepers',
						'Calculdroitrsa.toppersdrodevorsa',
						$this->Personne->Fichiermodule->sqNbFichiersLies( $this->Personne, 'nb_fichiers_lies' )
					),
					'conditions' => array( 'Personne.foyer_id' => $foyer_id ),
					'contain' => array(
						'Prestation',
						'Calculdroitrsa'
					)
				)
			);

			// Assignations à la vue
//			$this->_setOptions();
			$options = Hash::merge(
				$this->Personne->enums(),
				$this->Personne->Prestation->enums(),
				$this->Personne->Calculdroitrsa->enums(),
				array(
					'Calculdroitrsa' => array(
						'toppersdrodevorsa' => array(
							'' => 'Non défini',
						)
					)
				)
			);
			$this->set(compact('foyer_id', 'personnes', 'options'));
		}

		/**
		 *
		 *   Voir une personne en particulier
		 *
		 */
		public function view( $id = null ) {
			$this->WebrsaAccesses->check($id);

			$queryData = $this->WebrsaPersonne->completeVirtualFieldsForAccess(
				array(
					'fields' => array(
						'Personne.id',
						'Personne.foyer_id',
						'Personne.qual',
						'Personne.nom',
						'Personne.prenom',
						'Personne.nomnai',
						'Personne.prenom2',
						'Personne.prenom3',
						'Personne.nomcomnai',
						'Personne.dtnai',
						'Personne.rgnai',
						'Personne.typedtnai',
						'Personne.nir',
						'Personne.topvalec',
						'Personne.sexe',
						'Personne.nati',
						'Personne.dtnati',
						'Personne.pieecpres',
						'Personne.idassedic',
						'Personne.numagenpoleemploi',
						'Personne.dtinscpoleemploi',
						'Personne.numfixe',
						'Personne.numport',
						'Personne.email',
						'Prestation.rolepers',
					),
					'conditions' => array( 'Personne.id' => $id ),
					'contain' => array(
						'Prestation',
						'Grossesse' => array(
							'order' => array( 'Grossesse.ddgro DESC' ),
							'limit' => 1
						)
					)
				)
			);

			if( Configure::read( 'nom_form_ci_cg' ) == 'cg58' ) {
				$queryData['fields'][] = 'Foyer.sitfam';
				$queryData['contain'][] = 'Foyer';
			}

			$personne = $this->Personne->find('first', $queryData);
			$foyer_id = Hash::get($personne, 'Personne.foyer_id');
			$actionsParams = WebrsaAccessPersonnes::getActionParamsList($this->action);
			$paramsAccess = $this->WebrsaPersonne->getParamsForAccess($id, $actionsParams);

			$personne = WebrsaAccessPersonnes::access($personne, $paramsAccess);

			// Mauvais paramètre ?
			$this->assert( !empty( $personne ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $id ) ) );

			// Assignation à la vue
			$this->_setOptions();
			$this->set( 'personne', $personne );
		}

		/**
		 *   Ajout d'une personne au foyer
		 */
		public function add( $foyer_id = null ) {
			$this->WebrsaAccesses->check(null, $foyer_id);

			$dossier_id = $this->Foyer->dossierId( $foyer_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );

			$personne = $this->Personne->Foyer->find(
					'first', array(
				'fields' => array(
					'Foyer.sitfam'
				),
				'conditions' => array(
					'Foyer.id' => $foyer_id
				),
				'recursive' => -1
					)
			);

			$this->Jetons2->get( $dossier_id );

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'controller' => 'personnes', 'action' => 'index', $foyer_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Personne->begin();

				if( ( $this->request->data['Prestation']['rolepers'] == 'DEM' ) || ( $this->request->data['Prestation']['rolepers'] == 'CJT' ) ) {
					$this->request->data['Calculdroitrsa']['toppersdrodevorsa'] = true;
				}

				if( $this->Personne->saveAll( $this->request->data, array( 'validate' => 'only', 'atomic' => false ) ) ) {
					if( $this->Personne->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) ) ) {

						// FIXME: mettre dans un afterSave (mais ça pose des problèmes)
						// FIXME: valeur de retour
						$qd_thisPersonne = array(
							'conditions' => array(
								'Personne.id' => $this->Personne->id
							),
							'fields' => null,
							'order' => null,
							'recursive' => -1
						);
						$thisPersonne = $this->Personne->find( 'first', $qd_thisPersonne );


						$this->Personne->Foyer->refreshSoumisADroitsEtDevoirs( $thisPersonne['Personne']['foyer_id'] );

						$this->Personne->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'controller' => 'personnes', 'action' => 'index', $foyer_id ) );
					}
					else {
						$this->Personne->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
				else {
					$this->Personne->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				$roles = $this->Personne->find(
					'all',
					array(
						'fields' => array(
							'Personne.id',
							'Prestation.rolepers',
						),
						'conditions' => array(
							'Personne.foyer_id' => $foyer_id,
							'Prestation.rolepers' => array( 'DEM', 'CJT' )
						),
						'contain' => array(
							'Prestation'
						)
					)
				);
				$roles = Set::extract( '/Prestation/rolepers', $roles );

				// On ne fait apparaître les roles de demandeur et de conjoint que
				// si ceux-ci n'existent pas encore dans le foyer
				$rolepersPermis = ClassRegistry::init('Prestation')->enum('rolepers');
				foreach( $rolepersPermis as $key => $rPP ) {
					if( in_array( $key, $roles ) ) {
						unset( $rolepersPermis[$key] );
					}
				}
				$this->set( 'rolepers', $rolepersPermis );
			}

			$this->set( 'foyer_id', $foyer_id );
			$this->request->data['Personne']['foyer_id'] = $foyer_id;
			$this->set( 'personne', $personne );
			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		/**
		 *   Éditer une personne spécifique d'un foyer
		 */
		public function edit( $id = null ) {
			$this->WebrsaAccesses->check($id);

			$personne = $this->Personne->find(
				'first',
				array(
					'conditions' => array( 'Personne.id' => $id ),
					'contain' => array(
						'Foyer',
						'Prestation'
					)
				)
			);
			$this->assert(!empty($personne), 'invalidParameter');
			$foyer_id = Hash::get($personne, 'Personne.foyer_id');

			$dossier_id = $this->Personne->dossierId( $id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $id ) ) );

			$this->Jetons2->get( $dossier_id );

			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'controller' => 'personnes', 'action' => 'index', $foyer_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Personne->begin();

				if ($this->Personne->saveAll($this->request->data)) {
					$this->Personne->Foyer->refreshSoumisADroitsEtDevoirs($foyer_id);
					$this->Personne->commit();
					$this->Jetons2->release($dossier_id);
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(
						array('controller' => 'personnes', 'action' => 'index', $this->request->data['Personne']['foyer_id'])
					);
				} else {
					$this->Personne->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			} else {
				$this->request->data = $personne;
			}

			$sitfam = $this->Option->sitfam();
			$situationfamiliale = Set::enum( Set::classicExtract( $personne, 'Foyer.sitfam' ), $sitfam );
			$this->set( 'situationfamiliale', $situationfamiliale );
			$this->set( 'personne', $personne );
			$this->_setOptions();
			$this->view = 'add_edit';
		}

		/**
         * Voir l'historique des coordonnées spécifique d'une personne.
         *
         * @param integer $id L'id de la personne
         * @throws NotFoundException
         */
		public function histoinfocontactpersonne ( $id = null ) {
			//$this->WebrsaAccesses->check($id);

			/*Historique donnée contact*/
			$infocontactpersonne = $this->Personne->getHistoinfocontactpersonne($id);

			$actionsParams = WebrsaAccessPersonnes::getActionParamsList($this->action);
			$paramsAccess = $this->WebrsaPersonne->getParamsForAccess($id, $actionsParams);

			foreach ($infocontactpersonne as $key => $value) {
				$infocontactpersonne[$key] = WebrsaAccessPersonnes::access($infocontactpersonne[$key], $paramsAccess);
			}

			// Mauvais paramètre ?
			$this->assert( !empty( $infocontactpersonne ), 'invalidParameter' );

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $id ) );

			$urlmenu = "/personnes/view/{$id}";
			$this->_setOptions();
			$this->set( compact('infocontactpersonne', 'dossierMenu', 'urlmenu' ) );
		}

        /**
         * Éditer les coordonnées spécifique d'une personne.
         *
         * @param integer $id L'id de la personne
         * @throws NotFoundException
         */
		public function coordonnees( $id = null ) {
			$this->WebrsaAccesses->check($id);

			$query = array(
				'conditions' => array( 'Personne.id' => $id ),
				'contain' => 'Prestation'
			);
			$personne = $this->Personne->find('first',$query);

			if (empty($personne)) {
				throw new NotFoundException();
			}

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $id ) );
			$this->Jetons2->get( $dossierMenu['Dossier']['id'] );
			$redirectUrl = array( 'controller' => 'modescontact', 'index' => 'view', $personne['Personne']['foyer_id'], $personne['Prestation']['rolepers'] );

			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
				$this->redirect( $redirectUrl );
			}

			if( $this->request->is('post') || $this->request->is('put') ) {
				$this->Personne->begin();
				$data = Hash::merge( $personne, $this->request->data );

				// Ajout de la clé au NIR si celle-ci manque.
				( strlen ( trim ( $data['Personne']['nir'] )) == 13 ) ? $data['Personne']['nir'] = trim ( $data['Personne']['nir'] ).( 97 - trim ( $data['Personne']['nir'] ) % 97 ) : '' ;

				//on récupère la dernière version des coordonnées
				$contact = $this->Infocontactpersonne->findByPersonneId($data['Personne']['id'], [], ['Infocontactpersonne.modified' => 'desc']);
				$now = date('Y-m-d H:i:s');

					//Historiser le contact
					$this->Infocontactpersonne->begin();
					$infocontactdata['Infocontactpersonne']['personne_id'] = $data['Personne']['id'];
				if(!empty($data['Personne']['numfixe'])){
					$infocontactdata['Infocontactpersonne']['fixe'] = $data['Personne']['numfixe'];
					$infocontactdata['Infocontactpersonne']['modified_fixe'] = $now;
					$datapers['Personne']['numfixe'] = $data['Personne']['numfixe'];
					$datapers['Personne']['modified_numfixe'] = $now;
				} else if ($contact != null){
					$infocontactdata['Infocontactpersonne']['fixe'] = $contact['Infocontactpersonne']['fixe'];
					$infocontactdata['Infocontactpersonne']['modified_fixe'] = $contact['Infocontactpersonne']['modified_fixe'];
				}
				if(!empty($data['Personne']['numport'])){
					$infocontactdata['Infocontactpersonne']['mobile'] = $data['Personne']['numport'];
					$infocontactdata['Infocontactpersonne']['modified_mobile'] = $now;
					$datapers['Personne']['numport'] = $data['Personne']['numport'];
					$datapers['Personne']['modified_numport'] = $now;
				} else if ($contact != null){
					$infocontactdata['Infocontactpersonne']['mobile'] = $contact['Infocontactpersonne']['mobile'];
					$infocontactdata['Infocontactpersonne']['modified_mobile'] = $contact['Infocontactpersonne']['modified_mobile'];
				}
				if(!empty($data['Personne']['email'])){
					$infocontactdata['Infocontactpersonne']['email'] = $data['Personne']['email'];
					$infocontactdata['Infocontactpersonne']['modified_email'] = $now;
					$datapers['Personne']['email'] = $data['Personne']['email'];
					$datapers['Personne']['modified_email'] = $now;
				} else if ($contact != null) {
					$infocontactdata['Infocontactpersonne']['email'] = $contact['Infocontactpersonne']['email'];
					$infocontactdata['Infocontactpersonne']['modified_email'] = $contact['Infocontactpersonne']['modified_email'];
				}

				$datapers['Personne']['id'] = $data['Personne']['id'];
				$this->Infocontactpersonne->create( $infocontactdata );
				$this->Personne->create( $datapers );
				if (empty($data['Personne']['email']) && empty($data['Personne']['numport']) && empty($data['Personne']['numfixe'])) {
					//Le formulaire est vide
					$this->Flash->error( __( 'Save->empty' ) );
				}else if (!$this->Infocontactpersonne->validates()) {
					//Il y a des erreurs dans le formulaire
					$errors = $this->Infocontactpersonne->validationErrors;
					$this->set( compact( 'errors') );
					$this->Flash->error( __( 'Save->error' ) );
				} else {
					if ( $this->Infocontactpersonne->save( null, array( 'atomic' => false ) ) && $this->Personne->save( null, array( 'atomic' => false ) )) {
						$this->Infocontactpersonne->commit();
						$this->Personne->commit();
						$this->Flash->success( __( 'Save->success' ) );
					}else{
						$this->Personne->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
					$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
					return $this->redirect( $redirectUrl );
				}
			}

			$this->set( compact( 'personne', 'dossierMenu') );
		}
	}
?>