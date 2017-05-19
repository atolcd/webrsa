<?php
	/**
	 * Fichier source de la classe PermissionsDeveloppementShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );
	App::uses( 'Controller', 'Controller' );
	App::uses( 'AclComponent', 'Controller/Component' );
	App::uses( 'DbdroitsComponent', 'Controller/Component' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ComponentCollection', 'Controller' );

	/**
	 * La classe PermissionsDeveloppementShell ...
	 *
	 * @package app.Console.Command
	 */
	class PermissionsDeveloppementShell extends XShell
	{

		/**
		 *
		 * @var type
		 */
		public $User;

		/**
		 *
		 * @var type
		 */
		public $Controller;

		/**
		 *
		 * @var type
		 */
		public $ComponentCollection = null;

		/**
		 *
		 * @var type
		 */
		public $Acl = null;

		/**
		 *
		 * @var type
		 */
		public $Dbdroits = null;

		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description( array(
				'Ce script va mettre à jour les droits des utilisateurs.',
				'Il supprime tous les droits et, pour tous les membres du groupe Administrateurs, va donner les droits à tous pour toutes les fonctionnalités.',
				'Très utilisé lors des développements afin de ne pas à avoir à repasser tous les droits pour vérifier le bon fonctionnement du code mis en place.'
			) );
			return $parser;
		}

		/**
		 * Initialisation: lecture des paramètres, on s'assure d'avoir une connexion
		 * valide
		 */
		public function initialize() {
			parent::initialize();
//			$this->Controller = & new Controller();
//			$this->components = array( 'Acl', 'Dbdroits' );
//			$this->uses = array( );
//			$this->constructClasses();
//			$this->Component->initialize( new ComponentCollection );
			$this->ComponentCollection = new ComponentCollection();
			$this->Acl = new AclComponent( $this->ComponentCollection );
			$this->Dbdroits = new DbdroitsComponent( $this->ComponentCollection );
			$this->User = ClassRegistry::init( 'User' );
		}

		/**
		 *
		 */
		protected function _clean() {
			$clean_actions = array(
				'aros' => array(
					'out' => 'Suppression des données de la table aros.',
					'query' => 'DELETE FROM aros;'
				),
				'acos' => array(
					'out' => 'Suppression des données de la table acos.',
					'query' => 'DELETE FROM acos;'
				),
				'aros_acos' => array(
					'out' => 'Suppression des données de la table aros_acos.',
					'query' => 'DELETE FROM aros_acos;'
				),
			);

			$this->XProgressBar->start( count( $clean_actions ) * 2 );
			foreach( $clean_actions as $action ) {
				$this->XProgressBar->next( 1, $action['out'] );
				$this->connection->query( $action['query'] );
			}

			foreach( array_keys( $clean_actions ) as $table ) {
				$this->XProgressBar->next( 1, $table );
				$sql = "SELECT pg_catalog.setval( '{$table}_id_seq', ( CASE WHEN ( SELECT max({$table}.id) FROM {$table} ) IS NOT NULL THEN ( SELECT max({$table}.id) + 1 FROM {$table} ) ELSE 1 END ), false);";
				$this->connection->query( $sql );
			}
			return true;
		}

		/**
		 *
		 */
		protected function _addGroups() {
			$success = true;

			$groups = $this->User->Group->find( 'all', array( 'order' => 'Group.parent_id ASC' ) );


			$this->XProgressBar->start( count( $groups ) );
			$msg = array( );
			foreach( $groups as $group ) {
				$parent_id = 0;
				if( !empty( $group['Group']['parent_id'] ) ) {
					$parent_id = $this->Acl->Aro->field( 'id', array( 'model' => 'Group', 'foreign_key' => $group['Group']['parent_id'] ) );
				}

				$this->Acl->Aro->create(
						array(
							'Aro' => array(
								'parent_id' => $parent_id,
								'model' => 'Group',
								'foreign_key' => $group['Group']['id'],
								'alias' => $group['Group']['name'],
							)
						)
				);
				if( $success = $this->Acl->Aro->save() && $success ) {
					$msg[] = "<success>Le groupe {$group['Group']['name']} a été ajouté aux Aros</success>";
				}
				else {
					$msg[] = "<important>Le groupe {$group['Group']['name']} n'a pas été ajouté aux Aros</important>";
				}
				$this->XProgressBar->next();
			}
			$this->out( $msg );
		}

		/**
		 *
		 */
		protected function _addUsers() {
			$success = true;

			$users = $this->User->find(
					'all', array(
				'fields' => array(
					'User.id',
					'User.username',
					'User.group_id',
					'Group.id',
					'Group.name'
				),
				'recursive' => 0
					)
			);

			$this->XProgressBar->start( count( $users ) );
			$msg = array( );
			foreach( $users as $id => $user ) {
				$qd_userAro = array(
					'conditions' => array(
						'Aro.alias' => $user['User']['username']
					)
				);
				$userAro = $this->Acl->Aro->find( 'first', $qd_userAro );

				$qd_groupAro = array(
					'conditions' => array(
						'Aro.alias' => $user['Group']['name']
					)
				);
				$groupAro = $this->Acl->Aro->find( 'first', $qd_groupAro );

				if( !empty( $groupAro ) ) {
					if( empty( $userAro ) ) {
						$qd_user = array(
							'conditions' => array(
								'User.username' => $user['User']['username']
							)
						);
						$user = $this->User->find( 'first', $qd_user );

						$this->Acl->Aro->create(
								array(
									'Aro' => array(
										'parent_id' => $groupAro['Aro']['id'],
										'model' => 'Utilisateur',
										'foreign_key' => $user['User']['id'],
										'alias' => $user['User']['username'],
									)
								)
						);
						if( $tmp = $this->Acl->Aro->save() ) {
							$msg[] = "<success>L'utilisateur {$user['User']['username']} a été ajouté aux Aros</success>";
						}
						else {
							$msg[] = "<error>Impossible d'ajouter l'utilisateur {$user['User']['username']} aux Aros</error>";
						}
						$success = $tmp && $success;
					}
					else {
						$msg[] = "<important>L'utilisateur {$user['User']['username']} figurait déjà dans les Aros</important>";
					}
				}
				else {
					$msg[] = "<important>Le groupe {$user['Group']['name']} ne figure pas dans les Aros</important>";
				}
				$this->XProgressBar->next();
			}
			$this->out( $msg );
			return $success;
		}

		/**
		 *
		 */
		protected function _addAcos() {
			$this->Dbdroits->majActions();
			return true;
		}

		/**
		 * Par défaut, on affiche l'aide
		 */
		public function main() {
			$success = true;
			$this->_wait( 'Nettoyage des Acl' );
			$success = $this->_clean();
			$this->_wait( 'Traitement des groupes' );
			$success = $this->_addGroups() && $success;
			$this->_wait( 'Traitement des utilisateurs' );
			$success = $this->_addUsers() && $success;
			$this->_wait( 'Traitement des controllers et des actions' );
			$success = $this->_addAcos() && $success;

			$acos = $this->Acl->Aco->find(
				'list',
				array(
					'fields' => array( 'id', 'alias' ),
					'conditions' => array( 'Aco.alias LIKE' => 'Module:%' )
				)
			);

			$this->_wait( 'Paramétrage des permissions des Administrateurs' );
			$this->XProgressBar->start( count( $acos ) + 1 );
			foreach( $acos as $id => $moduleAlias ) {
				$this->Acl->allow( 'Administrateurs', $moduleAlias );
				$this->XProgressBar->next( 1, 'Administrateur '.$id );
			}

			// Soyons certains de donner l'accès à la page d'accueil
			$slash = Router::parse( '/' );
//			$slashAlias = Inflector::camelize( $slash['controller'] ).':'.$slash['action'];
			$slashAlias = Inflector::camelize( 'dossiers' ).':'.'index';
			$this->Acl->allow( 'Administrateurs', $slashAlias );
			$this->XProgressBar->next( 1, 'Administrateurs : accès à la page d\'accueil' );
		}

	}
?>
