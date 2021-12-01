<?php
	/**
	 * Code source de la classe UsersController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );
	App::uses( 'CakeEmail', 'Network/Email' );
	App::uses( 'Folder', 'Utility' );
	App::uses( 'File', 'Utility' );
	App::uses( 'Occurences', 'Model/Behavior' );
	App::uses( 'PasswordFactory', 'Password.Utility' );
	App::uses( 'WebrsaEmailConfig', 'Utility' );

	/**
	 * La classe UsersController permet la gestion des utilisateurs.
	 *
	 * @package app.Controller
	 */
	class UsersController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Users';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
				),
			),
			'WebrsaUsers',
			'WebrsaParametrages',
			'WebrsaPermissions'
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Translator',
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'User',
			'Option',
			'WebrsaUser',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Users:edit',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'forgottenpass',
			'login',
			'logout',
			'ajax_get_permissions',
			'ajax_get_permissions_light',
			'errorpass',
			'expiredpass'
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajax_get_permissions' => 'read',
			'changepass' => 'update',
			'delete' => 'delete',
			'delete_jetons' => 'delete',
			'delete_jetonsfonctions' => 'delete',
			'edit' => 'update',
			'force_logout' => 'read',
			'forgottenpass' => 'read',
			'index' => 'read',
			'login' => 'read',
			'logout' => 'read',
			'duplicate' => 'update',
		);

		/**
		 *
		 */
		public function beforeFilter() {
			ini_set( 'max_execution_time', 0 );
			ini_set( 'memory_limit', '1024M' );
			$return = parent::beforeFilter();
			return $return;
		}

		/**
		 *
		 */
		protected function _setOptions() {
			$departement = Configure::read( 'Cg.departement' );

			$options = array(
				'Groups' => $this->User->Group->find( 'list' ),
				'Categoriesutilisateurs' => $this->User->Categorieutilisateur->find( 'list' ),
				'Serviceinstructeur' => $this->User->Serviceinstructeur->listOptions(),
				'communautessrs' => $this->User->Communautesr->find( 'list' ),
				'structuresreferentes' => $this->User->Structurereferente->find( 'list' ),
				'referents' => $this->User->Referent->find(
					'list',
					array(
						'fields' => array(
							'Referent.id',
							'Referent.nom_complet',
							'Structurereferente.lib_struc'
						),
						'recursive' => -1,
						'joins' => array(
							$this->User->Referent->join( 'Structurereferente', array( 'type' => 'INNER' ) )
						),
						'order' => array(
							'Structurereferente.lib_struc ASC',
							'Referent.nom_complet ASC',
						)
					)
				)
			);

			// INFO: pour index()
			if( 'index' === $this->request->params['action'] ) {
				$booleanEnums = array(
					'0' => 'Non',
					'1' => 'Oui'
				);
				$options = Hash::merge(
					$options,
					$this->User->enums(),
					array(
						'User' => array(
							'serviceinstructeur_id' => $this->User->Serviceinstructeur->find( 'list' ),
							'structurereferente_id' => $this->User->Structurereferente->find(
								'list',
								array(
									'fields' => array(
										'Structurereferente.id',
										'Structurereferente.lib_struc',
										'Typeorient.lib_type_orient'
									),
									'recursive' => -1,
									'joins' => array(
										$this->User->Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) )
									),
									'order' => array(
										'Typeorient.lib_type_orient ASC',
										'Structurereferente.lib_struc ASC',
									)
								)
							),
							'referent_id' => Hash::combine(
								$this->User->Referent->find(
									'all',
									array(
										'fields' => array(
											'Referent.id',
											'Referent.structurereferente_id',
											'Referent.nom_complet'
										),
										'recursive' => -1,
										'order' => array(
											'Referent.nom_complet ASC',
										)
									)
								),
								array( '%s_%s', '{n}.Referent.structurereferente_id', '{n}.Referent.id' ),
								'{n}.Referent.nom_complet'
							),
							'has_connections' => $booleanEnums,
							'has_jetons' => $booleanEnums,
							'has_jetonsfonctions' => $booleanEnums
						)
					)
				);

				if( 66 == $departement ) {
					$options['polesdossierspcgs66'] = $this->User->Poledossierpcg66->WebrsaPoledossierpcg66->polesdossierspcgs66( false );
				}
			}

			$this->set( compact( 'options' ) );
		}

		/**
		 * Envoi des options à la vue pour un add ou un edit
		 *
		 * @return void
		 */
		protected function _setOptionsAddEdit() {
			$this->set( 'zglist', $this->User->Zonegeographique->find( 'list' ) );
			$this->set( 'gp', $this->User->Group->find( 'list' ) );
			$this->set( 'si', $this->User->Serviceinstructeur->find( 'list' ) );
			$this->set( 'options', $this->User->enums() );
			$this->set( 'structuresreferentes', $this->User->Structurereferente->find( 'list', array( 'conditions' => array( 'Structurereferente.actif' => 'O' ) ) ) );
			$this->set( 'categories_utilisateurs', $this->User->Categorieutilisateur->find( 'list', array('conditions' => array('actif =' => 'true'))));

			if (Configure::read('Cg.departement') == 66) {
				$internes = $this->User->Service66->find('list',
					array('conditions' => array('Service66.actif' => 1, 'Service66.interne' => 1))
				);
				$externes = $this->User->Service66->find('list',
					array('conditions' => array('Service66.actif' => 1, 'Service66.interne' => 0))
				);

				$this->set('services66', array('DASAD' => $internes, 'Hors DASAD' => $externes));
			}

			$this->set(
				'polesdossierspcgs66',
				$this->User->Poledossierpcg66->WebrsaPoledossierpcg66->polesdossierspcgs66(false)
			);
			// Obtention de la liste des référents auxquels lier l'utilisateur
			$conditions = array(
				'Structurereferente.actif' => 'O',
				'Referent.actif' => 'O',
			);

			// Pour le CG 66, on ne veut que les référents appartenants à une structure OA
			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$conditions['Structurereferente.typestructure'] = 'oa';
			}

			$this->set( 'referents', $this->User->Referent->find(
					'list',
					array(
						'fields' => array(
							'Referent.id',
							'Referent.nom_complet',
							'Structurereferente.lib_struc'
						),
						'recursive' => -1,
						'joins' => array(
							$this->User->Referent->join( 'Structurereferente', array( 'type' => 'INNER' ) )
						),
						'conditions' => $conditions,
						'order' => array(
							'Structurereferente.lib_struc ASC',
							'Referent.nom_complet ASC',
						)
					)
				)
			);

			//On récupère la variable de configuration pour les réferents sectorisation
			$choix_referent_sectorisation_actif = Configure::read('Module.Sectorisation.enabled');
			$this->set( 'choix_referent_sectorisation_actif', $choix_referent_sectorisation_actif);

			if($choix_referent_sectorisation_actif){
				$conditions_referent_sectorisation = $conditions;
				$conditions_referent_sectorisation["NOT"] = array("Referent.nom_complet" => null);
				$this->set(
					'referents_sectorisation',
					$this->User->Referent->find(
						'list',
						array(
							'fields' => array(
								'Referent.id',
								'Referent.nom_complet',
								'Structurereferente.lib_struc'
							),
							'recursive' => -1,
							'joins' => array(
								$this->User->Referent->join( 'Structurereferente', array( 'type' => 'INNER' ) )
							),
							'conditions' => $conditions_referent_sectorisation,
							'order' => array(
								'Structurereferente.lib_struc ASC',
								'Referent.nom_complet ASC',
							)
						)
					)
				);
			}

			$this->set( 'communautessrs', $this->User->Communautesr->find( 'list' ) );

		}

		/**
		 * Supprime les jetons et l'entrée dans la table connections.
		 *
		 * @param integer $user_id
		 * @param integer $session_id
		 */
		protected function _deleteDbEntries( $user_id, $session_id ) {
			$this->WebrsaUsers->clearJetons( $user_id, $session_id );

			$success = $this->User->Jetonfonction->saveResultAsBool(
				$this->User->Connection->deleteAllUnbound(
					array(
						'Connection.user_id' => $user_id,
						'Connection.php_sid' => $session_id
					)
				)
			);

			return $success;
		}

		/**
		 * L'utilisateur est déjà connecté ? On le déconnecte.
		 *
		 * @todo
		 *
		 * @param type $authUser
		 * @return boolean
		 */
		protected function _cleanPreviousConnection( $authUser ) {
			$success = true;

			if( $this->User->Connection->find( 'count', array( 'conditions' => array( 'Connection.user_id' => $authUser['User']['id'] ) ) ) > 0 ) {
				$qd_otherConnections = array(
					'conditions' => array(
						'Connection.user_id' => $authUser['User']['id']
					),
					'contain' => false
				);

				$otherConnections = $this->User->Connection->find( 'all', $qd_otherConnections );
				$connectionIds = (array)Hash::extract( $otherConnections, '{n}.Connection.id' );

				$success = $this->User->Connection->deleteAll( array( 'Connection.id' => $connectionIds ) ) && $success;

				$success = $this->_deleteCachedElements( $authUser ) && $success;

				$sessionIds = (array)Hash::extract( $otherConnections, '{n}.Connection.php_sid' );
				foreach( $sessionIds as $session_id ) {
					$session_id = trim( $session_id );
					$this->_deleteTemporaryFiles( $session_id );
					$this->_deleteDbEntries( $authUser['User']['id'], $session_id );
				}
			}

			return $success;
		}

		/**
		 * Si l'utilisateur a réussi à se connecter ou si il a renouvelé son mot de passe
		 *
		 * @param int id
		 * @return boolean
		 */
		protected function _cleanPreviousErrors( $user_id ) {
			$this->User->save( array(
				'User' => array(
					'id' => $user_id,
					'nb_error_password' => 0
				)
			) );
		}

		/**
		 *
		 */
		public function login() {
			if( $this->Auth->login() ) {
				// Lecture de l'utilisateur authentifié
				// Si CakePHP est en version >= 2.0 on interroge la base de données plutôt que le composant Auth
				$authUser = $this->User->find( 'first', array( 'conditions' => array( 'User.id' => $this->Session->read( 'Auth.User.id' ) ), 'recursive' => -1 ) );

				// Si le mot de passe est expiré on redirige vers mot de passe oublié
				$expirationMonth = Configure::read('Password.expiration_months');
				if($expirationMonth) {
					$dateExpiration = new DateTime($authUser['User']['date_password'] . ' + ' . $expirationMonth . 'months');
					$now = new DateTime();
					if($now > $dateExpiration) {
						$this->redirect( array(	'action' => 'expiredpass') );
					}
				}

				// Suppression des anciennes erreurs de mot de passe
				$this->_cleanPreviousErrors($authUser['User']['id']);

				// Utilisateurs concurrents
				if( Configure::read( 'Utilisateurs.multilogin' ) == false ) {
					$this->User->Connection->begin();
					// Suppression des connections dépassées
					$this->User->Connection->deleteAll(
						array(
							'Connection.modified <' => strftime( '%Y-%m-%d %H:%M:%S', strtotime( '-'.readTimeout().' seconds' ) )
						)
					);

					if( Configure::read( 'Utilisateurs.reconnection' ) === true ) {
						$this->_cleanPreviousConnection( $authUser );
					}

					if( $this->User->Connection->find( 'count', array( 'conditions' => array( 'Connection.user_id' => $authUser['User']['id'] ) ) ) == 0 ) {
						$connection = array(
							'Connection' => array(
								'user_id' => $authUser['User']['id'],
								'php_sid' => session_id()
							)
						);

						$this->User->Connection->set( $connection );
						if( $this->User->Connection->save( $connection , array( 'atomic' => false ) ) ) {
							$this->User->Connection->commit();
						}
						else {
							$this->User->Connection->rollback();
						}
					}
					else {
						$qd_otherConnection = array(
							'conditions' => array(
								'Connection.user_id' => $authUser['User']['id']
							)
						);
						$otherConnection = $this->User->Connection->find( 'first', $qd_otherConnection );

						$this->Session->delete( 'Auth' );
						$this->Flash->error(
								sprintf(
										'Utilisateur déjà connecté jusqu\'au %s (nous sommes actuellement le %s)', strftime( '%d/%m/%Y à %H:%M:%S', ( strtotime( $otherConnection['Connection']['modified'] ) + readTimeout() ) ), strftime( '%d/%m/%Y, il est %H:%M:%S' )
								)
						);

						$this->redirect( $this->Auth->logout() );
					}
				}
				// Fin utilisateurs concurrents

				$this->WebrsaUsers->load();

				// Supprimer la vue cachée du menu
				$this->_deleteCachedElements( $authUser );

				$this->redirect( $this->Auth->redirect() );
			}
			else if( !empty( $this->request->data ) ) {
				// Vérification de la quantité des erreurs de login faites
				$username = $this->request->data['User']['username'];
				$userExist = $this->User->find('count', array('conditions' => array('username' => $username) ) );
				if( !empty($username) && $userExist && Configure::read("Password.failed_allowed") ) {
					$passwordFailedAllowed = Configure::read("Password.failed_allowed");
					$user = $this->User->find('first', array(
						'fields' => array(
							'id',
							'username',
							'nb_error_password'
						),
						'recursive' => -1,
						'conditions' => array(
							'username' => $username
						)
					));

					$passwordFailed = $user['User']['nb_error_password'] +1;
					// Calcul du nombre d'erreur
					if( $passwordFailed > $passwordFailedAllowed ) {
						$this->redirect( array(	'action' => 'errorpass' ) );
					} else {
						$user['User']['nb_error_password'] = $passwordFailed;
						$this->User->save($user);
					}
				}
				$this->Flash->error( __( 'Login failed. Invalid username or password.' ), array( 'key' => 'auth' ) );
			}
		}

		/**
		 *
		 */
		public function logout() {
			if( $user_id = $this->Session->read( 'Auth.User.id' ) ) {
				if( valid_int( $user_id ) ) {
					$this->_deleteCachedElements( array( 'User' => $this->Session->read( 'Auth.User' ) ) );
					$this->_deleteTemporaryFiles( session_id() );
					$this->_deleteDbEntries( $user_id, session_id() );
				}
			}

			foreach( array_keys( $this->Session->read() ) as $key ) {
				if( !in_array( $key, array( 'Config', 'Message' ) ) ) {
					$this->Session->delete( $key );
				}
			}

			$this->redirect( $this->Auth->logout() );
		}

		/**
		 * Suppression des éléments cachés de l'utilisateur.
		 *
		 * @param array $user
		 * @return boolean
		 */
		protected function _deleteCachedElements( $user ) {
			$Folder =  new Folder();
			$dir = TMP.'cache'.DS.'views';
			$Folder->cd( $dir );

			$regexp = '.*element_'.$user['User']['username'];
			$results = $Folder->find( $regexp );

			$success = true;
			if( !empty( $results ) ) {
				foreach( $results as $result ) {
					$File =  new File( $dir.DS.$result, false );
					$success = $File->delete() && $success;
				}
			}

			return $success;
		}

		/**
		 * Suppression des répertoires temporaires de l'utilisateur.
		 *
		 * @param string $session_id
		 * @return boolean
		 */
		protected function _deleteTemporaryFiles( $session_id ) {
			$success = true;

			foreach( array( 'files', 'pdf' ) as $subdir ) {
				$path = trim(TMP.$subdir.DS.$session_id);
				if( file_exists( $path ) ) {
					$oFolder = new Folder( $path, true, 0777 );
					$success = $oFolder->delete() && $success;
				}
			}

			return $success;
		}

		/**
		 * Moteur de recherche des utilisateurs.
		 */
		public function index() {
			$search = (array)Hash::get( $this->request->data, 'Search' );
			if( !empty( $search ) ) {
				$query = $this->WebrsaUser->search( $search );
				$query['limit'] = 10;

				$virtualFields = $query['virtualFields'];
				unset( $query['virtualFields'] );

				// Champs virtuels pour User
				foreach( $virtualFields as $virtualFieldName => $virtualField ) {
					$query['fields'][] = "User.{$virtualFieldName}";
					$this->User->virtualFields[$virtualFieldName] = $virtualField;
				}

				$this->paginate = $query;
				$progressivePaginate = !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' );
				$results = $this->paginate( 'User', array(), array(), $progressivePaginate );

				$this->set( compact( 'results' ) );
			}

			$this->_setOptions();
			$this->render( 'index' );
		}

		/**
		 * Formulaire d'ajout d'un utilisateur.
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Formulaire de duplication d'un utilisateur.
		 */
		public function duplicate($user_id = null) {
			// Au retour du formulaire, on passe l'id de l'utilisateur à null pour forcer l'ajout.
			if (!empty($this->request->data)) {
				$user_id = null;
			}

			// Tout se passe dans la méthode edit.
			$this->edit($user_id);
		}

		/**
		 * Gestion du renouvellement du mot de passe après trop d'erreur
		 */
		public function errorpass() {
			$args = func_get_args();
			call_user_func_array( array( $this, 'sendpass' ), $args );
		}

		/**
		 * Gestion du renouvellement du mot de passe après expiration de celui-ci
		 */
		public function expiredpass() {
			$args = func_get_args();
			call_user_func_array( array( $this, 'sendpass' ), $args );
		}

		/**
		 * Gestion du renouvellement du mot de passe en cas d'oublie de celui-ci
		 */
		public function forgottenpass() {
			$args = func_get_args();
			call_user_func_array( array( $this, 'sendpass' ), $args );
		}

		/**
		 * Formulaire de modification d'un utilisateur.
		 *
		 * @param integer $user_id
		 * @throws NotFoundException
		 */
		public function edit( $user_id = null ) {
			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			if( empty( $this->request->data['User']['passwd'] ) ) {
				unset( $this->User->validate['passwd'] );
			}

			// Suppression du nombre d'erreur du mot de passe
			if( isset( $this->request->data['InitError'] ) ) {
				$this->_cleanPreviousErrors($user_id);
				$this->request->data = array();
				$this->Flash->success( __m('User::Success::InitError') );
			}

			if (!empty($this->request->data)) {
				if(false === empty($user_id)) {
					$this->User->id = $user_id;
				}
				// Ajout de la date de création du mot de passe
				$this->request->data['User']['date_password'] = date("Y-m-d H:i:s");

				$this->request->data = $this->WebrsaPermissions->getCompletedPermissions( $this->request->data );

				$this->User->begin();
				$this->User->create( $this->request->data );
				if ($this->User->save( null, array( 'atomic' => false ) )
					&& $this->WebrsaPermissions->updatePermissions($this->User, $this->User->id, $this->request->data)
				) {
					$this->User->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('controller' => 'users', 'action' => 'index'));
				} else {
					$this->User->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else if( 'edit' === $this->action ) {
				$qd_userDb = array(
					'conditions' => array(
						'User.id' => $user_id
					),
					'contain' => array(
						'Ancienpoledossierpcg66',
						'Zonegeographique',
						'Referent'
					)
				);
				$this->request->data = $this->User->find( 'first', $qd_userDb );

				if(true === empty($this->request->data)) {
					throw new NotFoundException();
				}

				// Certains utilisateurs ne sont pas déclarés comme gestionnaires mais sont liés à un pole
				$poledossierpcg66_id = Hash::get( $this->request->data, 'User.poledossierpcg66_id' );
				$isgestionnaire = Hash::get( $this->request->data, 'User.isgestionnaire' );

				if( false === empty( $poledossierpcg66_id ) && 'O' !== $isgestionnaire ) {
					$message = 'Cet utilisateur n\'était pas gestionnaire mais était lié à un pole PCG. Il est à nouveau renseigné en tant que gestionnaire dans le formulaire.';
					$this->Flash->error( $message );

					$this->request->data['User']['isgestionnaire'] = 'O';
				}
			}
			else if( 'duplicate' === $this->action ) {
				$qd_userDb = array(
					'conditions' => array(
						'User.id' => $user_id
					),
					'contain' => array(
						'Ancienpoledossierpcg66',
						'Zonegeographique'
					)
				);
				$this->request->data = $this->User->find( 'first', $qd_userDb );

				// On ne récupère pas les données personnelles
				// Données communes aux CG
				$this->request->data['User']['password'] = '';
				$this->request->data['User']['id'] = '';
				$this->request->data['User']['username'] = '';
				$this->request->data['User']['nom'] = '';
				$this->request->data['User']['prenom'] = '';
				$this->request->data['User']['date_naissance'] = '';
				$this->request->data['User']['date_deb_hab'] = '';
				$this->request->data['User']['date_fin_hab'] = '';
				$this->request->data['User']['numtel'] = '';
				// Données en plus dans CG66
				if (isset ($this->request->data['User']['numvoie'])) { $this->request->data['User']['numvoie'] = ''; }
				if (isset ($this->request->data['User']['typevoie'])) { $this->request->data['User']['typevoie'] = ''; }
				if (isset ($this->request->data['User']['nomvoie'])) { $this->request->data['User']['nomvoie'] = ''; }
				if (isset ($this->request->data['User']['compladr'])) { $this->request->data['User']['compladr'] = ''; }
				if (isset ($this->request->data['User']['codepos'])) { $this->request->data['User']['codepos'] = ''; }
				if (isset ($this->request->data['User']['ville'])) { $this->request->data['User']['ville'] = ''; }
				if (isset ($this->request->data['User']['email'])) { $this->request->data['User']['email'] = ''; }

				if(true === empty($this->request->data)) {
					throw new NotFoundException();
				}

				// Certains utilisateurs ne sont pas déclarés comme gestionnaires mais sont liés à un pole
				$poledossierpcg66_id = Hash::get( $this->request->data, 'User.poledossierpcg66_id' );
				$isgestionnaire = Hash::get( $this->request->data, 'User.isgestionnaire' );

				if( false === empty( $poledossierpcg66_id ) && 'O' !== $isgestionnaire ) {
					$message = 'Cet utilisateur n\'était pas gestionnaire mais était lié à un pole PCG. Il est à nouveau renseigné en tant que gestionnaire dans le formulaire.';
					$this->Flash->error( $message );

					$this->request->data['User']['isgestionnaire'] = 'O';
				}
			}

			// Vérification: le nombre de champs qui seront renvoyés par le
			// formulaire ne doit pas excéder ce qui est défini dans max_input_vars
			$max_input_vars = ini_get( 'max_input_vars' );
			if( 2500 > $max_input_vars ) {
				$message = 'La valeur de max_input_vars (%d) est trop faible pour permettre l\'enregistrement des droits. Merci de vérifier la valeur recommandée dans la partie "Vérification de l\'application"';
				$this->Flash->error( sprintf( $message, $max_input_vars ) );
			}

			// Permissions actuelles s'il y a lieu
			if( false === isset( $this->request->data['Permission'] ) && false === empty($user_id) ) {
				$this->request->data['Permission'] = $this->WebrsaPermissions->getPermissionsHeritage($this->User, $user_id);
			}

			// Permissions du parent s'il y a lieu
			$group_id = Hash::get( $this->request->data, 'User.group_id' );
			$parentPermissions = array();
			if(false === empty( $group_id )) {
				$parentPermissions = $this->WebrsaPermissions->getPermissionsHeritage($this->User->Group, $group_id);
			}

			$acos = $this->WebrsaPermissions->getAcosTreeByDepartement();

			// Ajout des champs concernant l'expiration du mot de passe
			$str_dateExpiration = '';
			if(Configure::read('Password.expiration_months')) {
				$expirationMonth = Configure::read('Password.expiration_months');
				if(!empty($user_id)) {
					$user = $this->User->find('first', array(
						'fields' => array('User.date_password', 'User.nb_error_password'),
						'recursive' => -1,
						'conditions' => array('User.id' => $user_id )
					));
					$dateExpiration = new DateTime($user['User']['date_password'] . ' + ' . $expirationMonth . ' months');
					$str_dateExpiration = $dateExpiration->format('d/m/Y');
				} else {
					$dateExpiration = new DateTime('today + ' . $expirationMonth . ' months');
					$str_dateExpiration = $dateExpiration->format('d/m/Y');
				}
			}

			$this->set( 'dateExpiration', $str_dateExpiration );

			// Ajout des champs concernant le nombre d'erreur du mot de passe
			$nbPasswordFailed = '';
			if( Configure::read("Password.failed_allowed") ) {
				if(isset($user) && !empty($user)) {
					$nbPasswordFailed = $user['User']['nb_error_password'];
				} else if ( !empty($user_id) ) {
					$user = $this->User->find('first', array(
						'fields' => array('User.nb_error_password'),
						'recursive' => -1,
						'conditions' => array('User.id' => $user_id )
					));
					$nbPasswordFailed = $user['User']['nb_error_password'];
				} else {
					$nbPasswordFailed = 0;
				}
			}
			$this->set( 'nbPasswordFailed', $nbPasswordFailed );

			$this->set( compact( 'parentPermissions', 'acos' ) );
			$this->_setOptionsAddEdit();

			$this->render( 'add_edit' );
		}

		/**
		 * Modification du mot de passe de l'utilisateur connecté.
		 *
		 * @throws Error500Exception
		 */
		public function changepass() {
			if( !empty( $this->request->data ) ) {
				$data = $this->request->data;
				$data['User']['id'] = $this->Session->read( 'Auth.User.id' );

				// Ajout de la date de création du mot de passe
				$data['User']['date_password'] = date("Y-m-d H:i:s");
				if( empty( $data['User']['id'] ) ) {
					throw new error500Exception( 'Auth.User.id vide' );
				}

				$this->User->begin();
				if( $this->User->changePassword( $data ) ) {
					$this->User->commit();
					$this->Flash->success( 'Votre mot de passe a bien été modifié' );
					$this->redirect( '/' );
				}
				else {
					$this->User->rollback();
					$this->Flash->error( 'Erreur lors de la saisie des mots de passe.' );
				}
			}
		}

		/**
		 * Permet l'envoi de mail avec un nouveau mot de passe
		 *
		 * @throws NotFoundException
		 */
		public function sendpass() {
			if( !Configure::read( 'Password.mail_forgotten' ) ) {
				throw new NotFoundException();
			}

			if( $this->action !=  'forgottenpass') {
				if($this->action == 'errorpass') {
					$reason_name = "Failed";
					$reason_nb = array( Configure::read('Password.failed_allowed') );
				} else if($this->action == 'expiredpass') {
					$this->Auth->logout();
					$reason_name = "Expired";
					$reason_nb = array( Configure::read('Password.expiration_months') );
				} else {
					throw new NotFoundException();
				}
				$title = __m("User::Title::" . $reason_name);
				$subtitle = __m("User::Text::" . $reason_name, $reason_nb);
				if( Configure::read( 'Password.administrator_mail' ) ) {
					$mailAdmin = array( Configure::read( 'Password.administrator_mail' ) );
					$subtitle .= __m("User::Mail::Administratormail", $mailAdmin);
				}

				$this->set( compact('title', 'subtitle') );
			}

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'login' ) );
			}

			if( !empty( $this->request->data ) ) {
				$user = $this->User->find(
					'first',
					array(
						'conditions' => array(
							'User.username' => $this->request->data['User']['username'],
							'User.email' => $this->request->data['User']['email'],
						),
						'contain' => false
					)
				);

				if( !empty( $user ) ) {
					$password = PasswordFactory::generator()->generate();
					$isDurationRewenal = false;

					$this->User->begin();

					$fieldsToUpdate = array(
						'User.password' => '\''.Security::hash( $password, null, true ).'\'',
						'User.date_password' => '\'' . date("Y-m-d H:i:s") . '\'',
						'User.nb_error_password' => 0
					);

					// Gestion de la validité du nouveau mot de passe
					if(Configure::read('Password.validated_duration_renewal') && Configure::read('Password.expiration_months')) {
						$isDurationRewenal = true;
						$expirationDays = Configure::read('Password.validated_duration_renewal');
						$expirationMonth = Configure::read('Password.expiration_months');
						$dateFakeExpiration = new DateTime('today - ' . $expirationMonth . ' months + ' . $expirationDays . ' days');
						$fieldsToUpdate['User.date_password'] = '\'' . $dateFakeExpiration->format("Y-m-d H:i:s") . '\'';
					}

					$success = $this->User->updateAllUnBound($fieldsToUpdate,
						array( 'User.id' => $user['User']['id'] )
					);

					$errorMessage = null;

					if( $success ) {
						try {
							$configName = WebrsaEmailConfig::getName( 'user_generation_mdp' );
							$Email = new CakeEmail( $configName );

							// Choix du destinataire suivant l'environnement
							if( !WebrsaEmailConfig::isTestEnvironment() ) {
								$Email->to( $user['User']['email'] );
							}
							else {
								$Email->to( WebrsaEmailConfig::getValue( 'user_generation_mdp', 'to', $Email->from() ) );
							}

							$Email->subject( __m("User::Mail::Subject::" . $this->action ) );
							$mailBody = __m("User::Mail::Body", array( $user['User']['username'], $password ));

							// Message supplémentaire si le mot de passe a une durée de vie
							if($isDurationRewenal) {
								$mailBody = str_replace('Cordialement.', __m("User::Mail::Body::Validduration", $expirationDays), $mailBody);
							}

							if( Configure::read( 'Password.administrator_mail' ) ) {
								$mailAdmin = array( Configure::read( 'Password.administrator_mail' ) );
								$strAdmin = __m("User::Mail::Administratormail", $mailAdmin) . "\n\n" . 'Cordialement.';
								$mailBody = str_replace('Cordialement.', $strAdmin, $mailBody);
							}

							$result = $Email->send( $mailBody );
							$success = !empty( $result ) && $success;
						} catch( Exception $e ) {
							$this->log( $e->getMessage(), LOG_ERROR );
							$success = false;
							$errorMessage = __m("User::Mail::Errormail");
							if( Configure::read( 'Password.administrator_mail' ) ) {
								$mailAdmin = array( Configure::read( 'Password.administrator_mail' ) );
								$errorMessage .= ' ' . __m("User::Mail::Administratormail", $mailAdmin);
							}
						}
					}

					if( $success ) {
						$this->User->commit();
						$this->Flash->success( __m("User::Mail::Success") );
					}
					else {
						$this->User->rollback();
						$this->Flash->error( $errorMessage );
					}
				}
				else {
					$errorMessage = __m("User::Mail::ErrorID");
					if( Configure::read( 'Password.administrator_mail' ) ) {
						$mailAdmin = array( Configure::read( 'Password.administrator_mail' ) );
						$errorMessage .= ' ' . __m("User::Mail::Administratormail", $mailAdmin);
					}
					$this->Flash->error( $errorMessage );
				}
			}
			$this->render( 'forgottenpass' );
		}

		/**
		 * Suppression des jetons d'un utilisateur, si l'utilisation des jetons
		 * est activée dans la configuration.
		 *
		 * @param integer $user_id
		 */
		public function delete_jetons( $user_id ) {
			if( Configure::read( 'Jetons2.disabled' ) ) {
				$this->Flash->error( 'L\'utilisation des jetons (Jetons2.disabled) n\'est pas activée dans la configuration' );
			}

			$this->User->Jeton->begin();

			$success = $this->User->Jeton->saveResultAsBool(
				$this->User->Jeton->deleteAllUnBound(
					array( 'Jeton.user_id' => $user_id )
				)
			);

			if( $success ) {
				$this->User->Jeton->commit();
				$this->Flash->success( 'Jetons de l\'utilisateur supprimés' );
			}
			else {
				$this->User->Jeton->rollback();
				$this->Flash->error( 'Impossible de supprimer les jetons de l\'utilisateurs' );
			}

			$this->redirect( $this->referer() );
		}

		/**
		 * Suppression des jetons d'un utilisateur, si l'utilisation des jetons
		 * est activée dans la configuration.
		 *
		 * @param integer $user_id
		 */
		public function delete_jetonsfonctions( $user_id ) {
			if( Configure::read( 'Jetonfonctionsfonctions2.disabled' ) ) {
				$this->Flash->error( 'L\'utilisation des jetons sur les fonctions (Jetonfonctionsfonctions2.disabled) n\'est pas activée dans la configuration' );
			}

			$this->User->Jetonfonction->begin();

			$success = $this->User->Jetonfonction->saveResultAsBool(
				$this->User->Jetonfonction->deleteAllUnBound(
					array( 'Jetonfonction.user_id' => $user_id )
				)
			);

			if( $success ) {
				$this->User->Jetonfonction->commit();
				$this->Flash->success( 'Jetons sur les fonctions de l\'utilisateur supprimés' );
			}
			else {
				$this->User->Jetonfonction->rollback();
				$this->Flash->error( 'Impossible de supprimer les jetons sur les fonctions de l\'utilisateurs' );
			}

			$this->redirect( $this->referer() );
		}

		/**
		 * Permet de forcer la déconnexion d'un utilisateur.
		 *
		 * @param integer $user_id
		 */
		public function force_logout( $user_id ) {
			if( $user_id == $this->Session->read( 'Auth.User.id' ) ) {
				$this->Flash->error( 'Impossible de forcer la déconnexion de l\'utilisateur qui lance la commande' );
			}
			else {
				$query = array(
					'conditions' => array(
						'Connection.user_id' => $user_id
					),
					'contain' => array(
						'User'
					)
				);
				$connections = $this->User->Connection->find( 'all', $query );

				if( empty( $connections ) ) {
					$this->Flash->error( 'Cet utilisateur n\'est actuellement pas connecté' );
				}
				else {
					$success = true;

					foreach( $connections as $connection ) {
						$success = $this->_deleteCachedElements( $connection ) && $success;
						$success = $this->_deleteTemporaryFiles( $connection['Connection']['php_sid'] ) && $success;
						$success = $this->_deleteDbEntries( $connection['Connection']['user_id'], $connection['Connection']['php_sid'] ) && $success;
					}

					if( $success ) {
						$this->Flash->success( 'Déconnexion de l\'utilisateur effectuée' );
					}
					else {
						$this->Flash->error( 'Impossible de déconnecter l\'utilisateur' );
					}
				}
			}

			$this->redirect( $this->referer() );
		}

		/**
		 * Permet d'obtenir par ajax, les droits d'un groupe (parent)
		 *
		 * @param integer $group_id
		 * @param boolean $light
		 * @return string json
		 */
		public function ajax_get_permissions($group_id, $light = false) {
			if(true === empty($group_id)) {
				$group_id = 0;
			}
			$permissions = $this->WebrsaPermissions->getPermissionsHeritage($this->User->Group, $group_id, $light);

			$this->set('json', json_encode($permissions));
			$this->layout = 'ajax';
			$this->render('/Elements/json');
		}

		/**
		 * Permet d'obtenir par ajax, les droits d'un groupe (parent)
		 *
		 * @param integer $group_id
		 * @return string json
		 */
		public function ajax_get_permissions_light ($group_id) {
			if(true === empty($group_id)) {
				$group_id = 0;
			}
			$permissions = $this->WebrsaPermissions->getPermissionsHeritage($this->User->Group, $group_id, true);

			echo (json_encode($permissions));
			exit ();
		}
	}
?>