<?php
	/**
	 * Code source de la classe UsersController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Folder', 'Utility' );
	App::uses( 'File', 'Utility' );
	App::uses( 'CakeEmail', 'Network/Email' );
	App::uses( 'WebrsaEmailConfig', 'Utility' );
	App::uses( 'Occurences', 'Model/Behavior' );

	/**
	 * La classe UsersController permet la gestion des utilisateurs.
	 *
	 * @package app.Controller
	 */
	class UsersController extends AppController
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
			'Dbdroits',
			'Jetons2',
			'Menu',
			'Password',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
				),
			),
			'WebrsaUsers',
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
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
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
//                            $this->User->Referent->sqVirtualField( 'nom_complet' ),
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
			$this->set( 'typevoie', $this->Option->typevoie() );
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
                $this->User->Poledossierpcg66->find(
                    'list',
                    array(
                        'fields' => array(
                            'Poledossierpcg66.id',
                            'Poledossierpcg66.name'
                        ),
                        'conditions' => array( 'Poledossierpcg66.isactif' => '1' ),
                        'order' => array( 'Poledossierpcg66.name ASC' )
                    )
                )
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
			// FIXME:à bouger dans un composant ?
			if( $this->Session->check( 'Auth.User' ) && !$this->Session->check( 'Auth.Permissions' ) ) {
				$aro = $this->Acl->Aro->find(
						'first', array(
					'conditions' => array(
						'Aro.model' => 'Utilisateur',
						'Aro.foreign_key' => $this->Session->read( 'Auth.User.id' )
					)
						)
				);

				// Recherche des droits pour les sous-groupes
				$parent_id = Set::extract( $aro, 'Aro.parent_id' );
				$parentAros = array( );
				while( !empty( $parent_id ) && ( $parent_id != 0 ) ) {
					$parentAro = $this->Acl->Aro->find(
							'first', array(
						'conditions' => array(
							'Aro.id' => $parent_id
						)
							)
					);
					$parentAros[] = $parentAro;
					$parent_id = Set::extract( $parentAro, 'Aro.parent_id' );
				}

				$permissions = array( );
				if( !empty( $parentAros ) && !empty( $parentAros['Aro'] ) && !empty( $parentAros['Aco'] ) ) {
					$permissions = Set::combine( $parentAros, '/Aco/alias', '/Aco/Permission/_create' );
				}
				if( !empty( $aro ) ) {
					$qd_permissions = array(
						'contain' => array(
							'Aco'
						),
						'fields' => array(
							'Aco.alias',
							'Permission._create'
						),
						'conditions' => array(
							'Permission.aro_id' => array( $aro['Aro']['id'], $aro['Aro']['parent_id'] )
						)
					);
					$data = $this->Acl->Aro->Permission->find( 'all', $qd_permissions );
					$permissions = Set::merge( $permissions, Set::combine( $data, '{n}.Aco.alias', '{n}.Permission._create' ) );

					foreach( $permissions as $key => $permission ) {
						$permissions[$key] = ( $permission != -1 );
					}
					$this->Session->write( 'Auth.Permissions', $permissions );
				}
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
		 * Chargement des données de la structure référente et du chargé d'insertion
		 * auquel l'utilisateur est attaché (CG 93).
		 *
		 * Ici, on écrase les zones géographiques de l'utilisateur connecté avec
		 * celles de la structurereferente s'il y a lieu.
		 *
		 * Pour le CG 66, si on est référent d'un OA, on va lire et mettre en
		 * session la structure référente à laquelle le référent est lié.
		 *
		 * @deprecated since 3.1.0
		 * @see WebrsaUsersComponent::loadStructurereferente()
		 *
		 * @return void
		 */
		protected function _loadStructurereferente() {
			if( Configure::read( 'Cg.departement' ) == 93 ) {
				$referent_id = $this->Session->read( 'Auth.User.referent_id' );
				$structurereferente_id = $this->Session->read( 'Auth.User.structurereferente_id' );

				// Si l'utilisateur est lié à une structure référente via un CI
				// FIXME: méthode en commun avec le 66, voir plus bas
				if( !$this->Session->check( 'Auth.Referent' ) ) {
					if( !empty( $referent_id ) ) {
						$querydata = array(
							'conditions' => array(
								'Referent.id' => $referent_id
							),
							'fields' => null,
							'order' => null,
							'contain' => false
						);
						$referent = $this->User->Referent->find( 'first', $querydata );
						$this->Session->write( 'Auth.Referent', empty( $referent ) ? false : $referent['Referent'] );

						if( !empty( $referent ) ) {
							$structurereferente_id = $referent['Referent']['structurereferente_id'];
						}
					}
					else {
						$this->Session->write( 'Auth.Referent', false );
					}
				}

				// Si l'utilisateur est lié (directement) à une structure référente
				// TODO: début à factoriser avec le CG 66 ci-dessous
				if( !$this->Session->check( 'Auth.Structurereferente' ) ) {
					if( !empty( $structurereferente_id ) ) {
						$querydata = array(
							'conditions' => array(
								'Structurereferente.id' => $structurereferente_id
							),
							'fields' => null,
							'order' => null,
							'contain' => array(
								'Zonegeographique'
							)
						);
						$structurereferente = $this->User->Structurereferente->find( 'first', $querydata );

						// Si la structure référente est limitée au niveau des zones géographiques, on fait de même avec l'utilisateur
						if( $structurereferente['Structurereferente']['filtre_zone_geo'] ) {
							$this->Session->write(
								'Auth.Zonegeographique',
								Set::combine( $structurereferente, 'Zonegeographique.{n}.id', 'Zonegeographique.{n}.codeinsee' )
							);
							$this->Session->write( 'Auth.User.filtre_zone_geo', true );
						}
						$this->Session->write( 'Auth.Structurereferente', empty( $structurereferente ) ? false : $structurereferente['Structurereferente'] );
					}
				}
				else {
					$this->Session->write( 'Auth.Structurereferente', false );
				}
				// TODO: fin à factoriser avec le CG 66 ci-dessous
			}
			else if( Configure::read( 'Cg.departement' ) == 66 ) {
				if( $this->Session->read( 'Auth.User.type' ) == 'externe_ci' ) {
					$querydata = array(
						'fields' => array( 'Referent.structurereferente_id' ),
						'conditions' => array(
							'Referent.id' => $this->Session->read( 'Auth.User.referent_id' )
						)
					);
					$referent = $this->User->Referent->find( 'first', $querydata );
					$this->Session->write( 'Auth.User.structurereferente_id', $referent['Referent']['structurereferente_id'] );

					$structurereferente_id = $referent['Referent']['structurereferente_id'];
					// Si l'utilisateur est lié (directement) à une structure référente
					// TODO: début à factoriser avec le CG 93 ci-dessus
					if( !$this->Session->check( 'Auth.Structurereferente' ) ) {
						if( !empty( $structurereferente_id ) ) {
							$querydata = array(
								'conditions' => array(
									'Structurereferente.id' => $structurereferente_id
								),
								'fields' => null,
								'order' => null,
								'contain' => array(
									'Zonegeographique'
								)
							);
							$structurereferente = $this->User->Structurereferente->find( 'first', $querydata );

							// Si la structure référente est limitée au niveau des zones géographiques, on fait de même avec l'utilisateur
							if( $structurereferente['Structurereferente']['filtre_zone_geo'] ) {
								$this->Session->write(
									'Auth.Zonegeographique',
									Set::combine( $structurereferente, 'Zonegeographique.{n}.id', 'Zonegeographique.{n}.codeinsee' )
								);
								$this->Session->write( 'Auth.User.filtre_zone_geo', true );
							}
							$this->Session->write( 'Auth.Structurereferente', empty( $structurereferente ) ? false : $structurereferente['Structurereferente'] );
						}
					}
					else {
						$this->Session->write( 'Auth.Structurereferente', false );
					}
					// TODO: fin à factoriser avec le CG 93 ci-dessus
				}
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
						if( $this->User->Connection->save( $connection ) ) {
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
						$this->Session->setFlash(
								sprintf(
										'Utilisateur déjà connecté jusqu\'au %s (nous sommes actuellement le %s)', strftime( '%d/%m/%Y à %H:%M:%S', ( strtotime( $otherConnection['Connection']['modified'] ) + readTimeout() ) ), strftime( '%d/%m/%Y, il est %H:%M:%S' )
								), 'flash/error'
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
				$this->Session->setFlash( __( 'Login failed. Invalid username or password.' ), 'default', array( ), 'auth' );
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
				$results = $this->paginate( 'User' );

				$this->set( compact( 'results' ) );
			}

			$this->_setOptions();
			$this->render( 'index' );
		}

		/**
		 *
		 */
		public function add() {
			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->User->begin();
				if( $this->User->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) ) ) {
					// Définition des nouvelles permissions

					$this->request->data['Droits'] = $this->Dbdroits->litCruDroits( array( 'model' => 'Group', 'foreign_key' => $this->request->data['User']['group_id'] ) );
					$this->Dbdroits->MajCruDroits(
						array(
							'model' => 'Utilisateur',
							'foreign_key' => $this->User->id,
							'alias' => $this->request->data['User']['username']
						),
						array(
							'model' => 'Group',
							'foreign_key' => $this->request->data['User']['group_id']
						),
						$this->request->data['Droits']
					);
					$this->User->commit();
					$this->Session->setFlash( 'Enregistrement effectué. Veuillez-vous déconnecter et vous reconnecter afin de prendre en compte tous les changements.', 'flash/success' );
					$this->redirect( array( 'controller' => 'users', 'action' => 'index' ) );
				}
				else {
					$this->User->rollback();
				}
			}

			$this->_setOptionsAddEdit();
			$this->render( 'add_edit' );
		}

		/**
		 *
		 */
		public function edit( $user_id = null ) {
			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $user_id ), 'error404' );

			$qd_userDb = array(
				'conditions' => array(
					'User.id' => $user_id
				),
				'contain' => array(
					'Zonegeographique'
				)
			);
			$userDb = $this->User->find( 'first', $qd_userDb );

			$this->assert( !empty( $userDb ), 'error404' );

			if( empty( $this->request->data['User']['passwd'] ) ) {
				unset( $this->User->validate['passwd'] );
			}

			if( !empty( $this->request->data ) ) {
				$this->User->begin();
				// Permet de supprimer les zones associées si on ne filtre pas sur les zones
				$filtre_zone_geo = Set::classicExtract( $this->request->data, 'User.filtre_zone_geo' );
				if( empty( $filtre_zone_geo ) ) {
					$this->request->data['Zonegeographique']['Zonegeographique'] = array( );
				}

				if( $this->User->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) ) ) {
					if( $userDb['User']['group_id'] != $this->request->data['User']['group_id'] ) {
						$this->request->data['Droits'] = $this->Dbdroits->litCruDroits( array( 'model' => 'Group', 'foreign_key' => $this->request->data['User']['group_id'] ) );
					}

					$this->Dbdroits->MajCruDroits(
						array(
							'model' => 'Utilisateur',
							'foreign_key' => $this->request->data['User']['id'],
							'alias' => $this->request->data['User']['username']
						),
						array(
							'model' => 'Group',
							'foreign_key' => $this->request->data['User']['group_id']
						),
						$this->request->data['Droits']
					);
					$this->User->commit();

					// Suppression du cache du menu pour la personne concernée
					Cache::delete('element_'.Hash::get($this->request->data, 'User.username'), 'views');

					$this->Session->setFlash( 'Enregistrement effectué. Veuillez-vous déconnecter et vous reconnecter afin de prendre en compte tous les changements.', 'flash/success' );
					$this->redirect( array( 'controller' => 'users', 'action' => 'index' ) );
				}
				else {
					$this->User->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else {
				$this->request->data = $userDb;
				$this->request->data['Droits'] = $this->Dbdroits->litCruDroits( array( 'model' => 'Utilisateur', 'foreign_key' => $user_id ) );

				// Vérification: le nombre de champs qui seront renvoyés par le
				// formulaire ne doit pas excéder ce qui est défini dans max_input_vars
				$max_input_vars = ini_get( 'max_input_vars' );
				if( 2500 > $max_input_vars ) {
					$message = 'La valeur de max_input_vars (%d) est trop faible pour permettre l\'enregistrement des droits. Merci de vérifier la valeur recommandée dans la partie "Vérification de l\'application"';
					$this->Session->setFlash( sprintf( $message, $max_input_vars ), 'flash/error' );
				}
			}

			$this->_setOptionsAddEdit();
			$this->set( 'listeCtrlAction', $this->Menu->menuCtrlActionAffichage() );
			$this->render( 'add_edit' );
		}

		/**
		 * Suppression d'un utilisateur.
		 *
		 * @param integer $id L'id de l'utilisateur à supprimer.
		 * @throws NotFoundException
		 * @throws RuntimeException
		 */
		public function delete( $id = null ) {
			if( !valid_int( $id ) ) {
				$message = "L'entrée d'id {$id} pour le modèle {$this->User->alias} n'existe pas";
				throw new NotFoundException( $message );
			}

			if( false === $this->User->Behaviors->attached( 'Occurences' ) ) {
				$this->User->Behaviors->attach( 'Occurences' );
			}

			$blacklist = array(
				'connections',
				// 'jetons', // INFO: pas de foreignkey
				// 'jetonsfonctions', // INFO: pas de foreignkey
				'users_zonesgeographiques'
			);

			$query = array(
				'fields' => array_merge(
					$this->User->fields(),
					array(
						$this->User->sqHasLinkedRecords( true, $blacklist )
					)
				),
				'contain' => false,
				'conditions' => array( 'User.id' => $id )
			);

			$user = $this->User->find( 'first', $query );

			if( empty( $user ) ) {
				$message = "L'entrée d'id {$id} pour le modèle {$this->User->alias} n'existe pas";
				throw new NotFoundException( $message );
			}

			if( true == $user['User']['has_linkedrecords'] ) {
				$message = "Erreur lors de la tentative de suppression de l'entrée d'id {$id} pour le modèle {$this->User->alias}: cette entrée possède des enregistrements liés.";
				throw new RuntimeException( $message );
			}

			// Tentative de suppression
			$this->User->begin();
			$success = $this->User->delete( $id );

			$query = array(
				'fields' => array( 'id' ),
				'conditions' => array(
					'model' => 'Utilisateur',
					'foreign_key' => $id
				)
			);
			$aro = $this->Acl->Aro->find( 'first', $query );

			if( !empty( $aro ) ) {
				$success = $success && $this->Acl->Aro->delete( $aro['Aro']['id'] );
				$permissions_ids = Hash::extract( $aro, 'Aco.{n}.Permission.id' );
				if( !empty( $permissions_ids ) ) {
					$success = $success && $this->Acl->Aro->Permission->deleteAll(
						array( 'Permission.id' => $permissions_ids )
					);
				}
			}

			$success = $success && $this->Acl->Aro->recover( 'parent', null );

			if( $success ) {
				$this->User->commit();
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			}
			else {
				$this->User->rollback();
				$this->Session->setFlash( 'Erreur lors de la suppression', 'flash/error' );
			}

			$this->redirect( array( 'controller' => 'users', 'action' => 'index' ) );
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
					$this->Session->setFlash( 'Votre mot de passe a bien été modifié', 'flash/success' );
					$this->redirect( '/' );
				}
				else {
					$this->User->rollback();
					$this->Session->setFlash( 'Erreur lors de la saisie des mots de passe.', 'flash/error' );
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
					$this->User->begin();

					$password = $this->Password->generate();

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
                            $mailBody = "Bonjour,\nsuite à votre demande, veuillez trouver ci-dessous vos nouveaux identifiants:\nRappel de votre identifiant : {$user['User']['username']}\nVotre nouveau mot de passe : {$password}\n";

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
						$this->Session->setFlash( 'Un courriel contenant votre nouveau mot de passe vient de vous être envoyé.', 'flash/success' );
					}
					else {
						$this->User->rollback();
						$this->Session->setFlash( $errorMessage, 'flash/error' );
					}
				}
				else {
					$this->Session->setFlash( 'Impossible de trouver ce couple identifiant/adresse de courriel, veuillez contacter votre administrateur.', 'flash/error' );
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
				$this->Session->setFlash( 'L\'utilisation des jetons (Jetons2.disabled) n\'est pas activée dans la configuration', 'flash/error' );
			}

			$this->User->Jeton->begin();

			$success = $this->User->Jeton->saveResultAsBool(
				$this->User->Jeton->deleteAllUnBound(
					array( 'Jeton.user_id' => $user_id )
				)
			);

			if( $success ) {
				$this->User->Jeton->commit();
				$this->Session->setFlash( 'Jetons de l\'utilisateur supprimés', 'flash/success' );
			}
			else {
				$this->User->Jeton->rollback();
				$this->Session->setFlash( 'Impossible de supprimer les jetons de l\'utilisateurs', 'flash/error' );
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
				$this->Session->setFlash( 'L\'utilisation des jetons sur les fonctions (Jetonfonctionsfonctions2.disabled) n\'est pas activée dans la configuration', 'flash/error' );
			}

			$this->User->Jetonfonction->begin();

			$success = $this->User->Jetonfonction->saveResultAsBool(
				$this->User->Jetonfonction->deleteAllUnBound(
					array( 'Jetonfonction.user_id' => $user_id )
				)
			);

			if( $success ) {
				$this->User->Jetonfonction->commit();
				$this->Session->setFlash( 'Jetons sur les fonctions de l\'utilisateur supprimés', 'flash/success' );
			}
			else {
				$this->User->Jetonfonction->rollback();
				$this->Session->setFlash( 'Impossible de supprimer les jetons sur les fonctions de l\'utilisateurs', 'flash/error' );
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
				$this->Session->setFlash( 'Impossible de forcer la déconnexion de l\'utilisateur qui lance la commande', 'flash/error' );
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
					$this->Session->setFlash( 'Cet utilisateur n\'est actuellement pas connecté', 'flash/error' );
				}
				else {
					$success = true;

					foreach( $connections as $connection ) {
						$success = $this->_deleteCachedElements( $connection ) && $success;
						$success = $this->_deleteTemporaryFiles( $connection['Connection']['php_sid'] ) && $success;
						$success = $this->_deleteDbEntries( $connection['Connection']['user_id'], $connection['Connection']['php_sid'] ) && $success;
					}

					if( $success ) {
						$this->Session->setFlash( 'Déconnexion de l\'utilisateur effectuée', 'flash/success' );
					}
					else {
						$this->Session->setFlash( 'Impossible de déconnecter l\'utilisateur', 'flash/error' );
					}
				}
			}

			$this->redirect( $this->referer() );
		}
	}
?>