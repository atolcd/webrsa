<?php
	/**
	 * Code source de la classe ActionrolesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe ActionrolesController s'occupe du paramétrage des actions de
	 * rôles.
	 *
	 * @package app.Controller
	 */
	class ActionrolesController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Actionroles';

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default',
			'Default2',
			'Theme',
			'Xform',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Actionrole' );

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Search.SearchPrg' => array(
				'actions' => array( 'index' )
			),
			'WebrsaParametrages'
		);

		/**
		 * Moteur de recherche par actions de rôles.
		 */
		public function index() {
			if( !empty( $this->request->data ) ) {
				$search = $this->request->data['Search'];
				$query = array(
					'contain' => array(
						'Role.name',
						'Role.actif',
						'Categorieactionrole.name'
					),
					'conditions' => array(),
					'order' => array(
						'Categorieactionrole.name',
						'Role.name',
						'Actionrole.name'
					)
				);
				foreach( array_keys( $search ) as $modelName ) {
					if( 'Pagination' !== $modelName ) {
						foreach( (array)Hash::get( $search, $modelName ) as $fieldName => $value ) {
							$value = trim( (string)$value );
							if( '' !== $value ) {
								if( false !== strpos( $fieldName, '_id' ) ) {
									if( 'RoleUser' === $modelName ) {
										$subQuery = array(
											'alias' => 'roles_users',
											'fields' => array( 'roles_users.id' ),
											'conditions' => array(
												'roles_users.user_id' => $value,
												'roles_users.role_id = Role.id'
											),
											'contain' => false
										);
										$sql = $this->Actionrole->Role->RoleUser->sq( $subQuery );
										$query['conditions'][] = "EXISTS( {$sql} )";
									}
									else {
										$query['conditions']["{$modelName}.{$fieldName}"] = suffix( $value );
									}
								}
								else {
									if('actif' === $fieldName) {
										$query['conditions']["{$modelName}.{$fieldName}"] = $value;
									}
									else {
										$query['conditions']["{$modelName}.{$fieldName} ILIKE"] = "%{$value}%";
									}
								}
							}
						}
					}
				}
				$query['limit'] = 100;
				$this->WebrsaParametrages->index( $query, array( 'progressivePaginate' => !Hash::get($search, 'Pagination.nombre_total') ) );
			}

			$options = array_merge(
				$this->Actionrole->Role->enums(),
				array(
					'Actionrole' => array(
						'categorieactionrole_id' => $this->Actionrole->Categorieactionrole->find( 'list' ),
						'role_id' => $this->Actionrole->Role->find( 'list' )
					)
				),
				array(
					'RoleUser' => array(
						'user_id' => Hash::combine(
							$this->Actionrole->Role->RoleUser->User->find(
								'all',
								array(
									'fields' => array(
										'User.id',
										'User.nom',
										'User.prenom',
										'User.username'
									),
									'contain' => false,
									'order' => array(
										'User.nom',
										'User.prenom',
										'User.username'
									)
								)
							),
							'{n}.User.id',
							array( '%s %s (%s)', '{n}.User.nom', '{n}.User.prenom', '{n}.User.username' )
						)
					)
				)
			);
			$this->set( compact( 'options' ) );
		}

		/**
		 * Modification d'une action de rôles.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
            // Retour à la liste en cas d'annulation
            if( isset( $this->request->data['Cancel'] ) ) {
                $this->redirect( array( 'controller' => 'actionroles', 'action' => 'index' ) );
            }

			if( !empty( $this->request->data ) ) {
				$this->Actionrole->create( $this->request->data );
				$success = $this->Actionrole->save( null, array( 'atomic' => false ) );

				if( $success ) {
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->Actionrole->find(
					'first',
					array(
						'contain' => false,
						'conditions' => array( 'Actionrole.id' => $id )
					)
				);
				$this->assert( !empty( $this->request->data ), 'error404' );
			}
			else{
				$this->request->data['Actionrole']['actif'] = true;
			}

			$options = $this->_options();

			$this->set( compact( 'options' ) );

			$this->view = 'add_edit';
		}

		/**
		 * Options pour la vue
		 *
		 * @return array
		 */
		protected function _options() {
			$options['Actionrole']['role_id'] = $this->Actionrole->Role->find('list', array('order' => 'name', 'conditions' => array('actif' => 1)));
			$options['Actionrole']['categorieactionrole_id'] = $this->Actionrole->Categorieactionrole->find('list', array('order' => 'name'));

			return $options;
		}
	}
?>