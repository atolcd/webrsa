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
			$departement = (int)Configure::read( 'Cg.departement' );

			$options = array(
				'Groups' => $this->User->Group->find( 'list' ),
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

				if( 66 === $departement ) {
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

			if (Configure::read('Cg.departement') === 66) {
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

			$this->set( 'communautessrs', $this->User->Communautesr->find( 'list' ) );
		}

		/**
		 * Chargement et mise en cache (session) des permissions de l'utilisateur
		 * INFO:
		 * 	- n'est réellement exécuté que la première fois
		 * 	- http://dsi.vozibrale.com/articles/view/all-cakephp-acl-permissions-for-your-views
		 * 	- http://www.neilcrookes.com/2009/02/26/get-all-acl-permissions/
		 */
		protected function _loadPermissions() {
			if ($this->Session->check('Auth.User') && !$this->Session->check('Auth.Permissions')) {
				$permissions = $this->WebrsaPermissions->getPermissions($this->User, $this->Session->read('Auth.User.id'));
				$this->Session->write('Auth.Permissions', $permissions);
			}
		}

		/**
		 * Chargement et mise en cache (session) des zones géographiques associées à l'utilisateur
		 * INFO: n'est réellement exécuté que la première fois
		 */
		protected function _loadZonesgeographiques() {
			if( $this->Session->check( 'Auth.User' ) && $this->Session->read( 'Auth.User.filtre_zone_geo' ) && !$this->Session->check( 'Auth.Zonegeographique' ) ) {
				$qd_users_zonegeographiques = array(
					'fields' => array(
						'Zonegeographique.id',
						'Zonegeographique.codeinsee'
					),
					'contain' => array(
						'Zonegeographique'
					),
					'conditions' => array(
						'UserZonegeographique.user_id' => $this->Session->read( 'Auth.User.id' )
					)
				);
				$results = $this->User->UserZonegeographique->find( 'all', $qd_users_zonegeographiques );

				if( count( $results ) > 0 ) {
					$zones = array( );
					foreach( $results as $result ) {
						$zones[$result['Zonegeographique']['id']] = $result['Zonegeographique']['codeinsee'];
					}
					$this->Session->write( 'Auth.Zonegeographique', $zones ); // FIXME: vide -> rééxécute ?
				}
			}
		}

		/**
		 * Chargement du service instructeur de l'utilisateur connecté, lancement
		 * d'une erreur 500 si aucun service instructeur n'est associé à l'utilisateur
		 *
		 * @return void
		 */
		protected function _loadServiceInstructeur() {
			if( !$this->Session->check( 'Auth.Serviceinstructeur' ) ) {
				$qd_service = array(
					'conditions' => array(
						'Serviceinstructeur.id' => $this->Session->read( 'Auth.User.serviceinstructeur_id' )
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$service = $this->User->Serviceinstructeur->find( 'first', $qd_service );
				$this->assert( !empty( $service ), 'error500' );
				$this->Session->write( 'Auth.Serviceinstructeur', $service['Serviceinstructeur'] );
			}
		}

		/**
		 * Chargement du groupe de l'utilisateur connecté, lancement
		 * d'une erreur 500 si aucun groupe n'est associé à l'utilisateur
		 *
		 * @return void
		 */
		protected function _loadGroup() {
			if( !$this->Session->check( 'Auth.Group' ) ) {
				$qd_group = array(
					'conditions' => array(
						'Group.id' => $this->Session->read( 'Auth.User.group_id' )
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$group = $this->User->Group->find( 'first', $qd_group );
				$this->assert( !empty( $group ), 'error500' );
				$this->Session->write( 'Auth.Group', $group['Group'] );
			}
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
		 *
		 */
		public function login() {
			if( $this->Auth->login() ) {
				// Lecture de l'utilisateur authentifié
				// Si CakePHP est en version >= 2.0 on interroge la base de données plutôt que le composant Auth
				$authUser = $this->User->find( 'first', array( 'conditions' => array( 'User.id' => $this->Session->read( 'Auth.User.id' ) ), 'recursive' => -1 ) );

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

				// lecture du service de l'utilisateur authentifié
				$group = $this->User->Group->find(
					'first',
					array(
						'conditions' => array(
							'Group.id' => $authUser['User']['group_id']
						),
						'contain' => false
					)
				);
				$authUser['User']['aroAlias'] = $authUser['User']['username'];
				/* lecture de la collectivite de l'utilisateur authentifié */
				$this->Session->write( 'Auth', $authUser );

				// chargements des informations complémentaires
				$this->_loadPermissions();
				$this->_loadZonesgeographiques();
				$this->_loadGroup();
				$this->_loadServiceInstructeur();
				$this->WebrsaUsers->loadStructurereferente();

				// Supprimer la vue cachée du menu
				$this->_deleteCachedElements( $authUser );

				$this->redirect( $this->Auth->redirect() );
			}
			else if( !empty( $this->request->data ) ) {
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

			if (!empty($this->request->data)) {
				if(false === empty($user_id)) {
					$this->User->id = $user_id;
				}
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
						'Zonegeographique'
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
		 *
		 * @throws NotFoundException
		 */
		public function forgottenpass() {
			if( !Configure::read( 'Password.mail_forgotten' ) ) {
				throw new NotFoundException();
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

					$this->User->begin();

					$success = $this->User->updateAllUnBound(
						array( 'User.password' => '\''.Security::hash( $password, null, true ).'\'' ),
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

							$Email->subject( WebrsaEmailConfig::getValue( 'user_generation_mdp', 'subject', 'WebRSA: changement de mot de passe' ) );
                            $mailBody = "Bonjour,\n\nsuite à votre demande, veuillez trouver ci-dessous un rappel de votre identifiant ainsi qu'un mot de passe temporaire que nous vous invitons à modifier après vous être connecté(e).\n\nRappel de votre identifiant : {$user['User']['username']}\nMot de passe : {$password}\n\nCordialement.";

                            $result = $Email->send( $mailBody );
                            $success = !empty( $result ) && $success;
                        } catch( Exception $e ) {
                            $this->log( $e->getMessage(), LOG_ERROR );
                            $success = false;
                            $errorMessage = 'Impossible d\'envoyer le courriel contenant votre nouveau mot de passe, veuillez contacter votre administrateur.';
                        }
                    }

					if( $success ) {
						$this->User->commit();
						$this->Flash->success( 'Un courriel contenant votre nouveau mot de passe vient de vous être envoyé.' );
					}
					else {
						$this->User->rollback();
						$this->Flash->error( $errorMessage );
					}
				}
				else {
					$this->Flash->error( 'Impossible de trouver ce couple identifiant/adresse de courriel, veuillez contacter votre administrateur.' );
				}
			}
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
		 * @return string json
		 */
		public function ajax_get_permissions($group_id) {
			if(true === empty($group_id)) {
				$group_id = 0;
			}
			$permissions = $this->WebrsaPermissions->getPermissionsHeritage($this->User->Group, $group_id);

			$this->set('json', json_encode($permissions));
			$this->layout = 'ajax';
			$this->render('/Elements/json');
		}
	}
?>