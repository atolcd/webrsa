<?php
	/**
	 * Code source de la classe GroupsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );
	App::uses( 'Occurences', 'Model/Behavior' );
	App::uses( 'WebrsaSessionAclUtility', 'Utility' );

	/**
	 * La classe GroupsController ...
	 *
	 * @package app.Controller
	 */
	class GroupsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Groups';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array( 'WebrsaParametrages', 'WebrsaPermissions' );

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'WebrsaPermissions',
			'Xform'
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Group', 'User' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Groups:edit',
			'resetDroitGroupUsers' => 'Groups:edit',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajax_get_permissions',
			'ajax_get_permissions_light',
		);

		/**
		*
		*/
		public function beforeFilter() {
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', '1024M');
			parent::beforeFilter();
		}

		/**
		 * Liste des groupes.
		 */
		public function index() {
			if( false === $this->Group->Behaviors->attached( 'Occurences' ) ) {
				$this->Group->Behaviors->attach( 'Occurences' );
			}

			$query = array(
				'fields' => array_merge(
					$this->Group->fields(),
					array( $this->Group->sqHasLinkedRecords( true ) ),
					$this->Group->ParentGroup->fields()
				),
				'joins' => array(
					$this->Group->join( 'ParentGroup', array( 'type' => 'LEFT OUTER' ) )
				)
			);

			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire d'ajout d'un groupe.
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Formulaire de modification d'un groupe.
		 *
		 * @param integer $group_id
		 * @throws NotFoundException
		 */
		public function edit( $group_id = null ) {
			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			if (!empty($this->request->data)) {
				if(false === empty($group_id)) {
					$this->Group->id = $group_id;
				}
				$this->request->data = $this->WebrsaPermissions->getCompletedPermissions( $this->request->data );

				$this->Group->begin();
				if ($this->Group->save( $this->request->data, array( 'atomic' => false ) )
					&& $this->WebrsaPermissions->updatePermissions($this->Group, $this->Group->id, $this->request->data)
				) {
					$this->Group->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('controller' => 'groups', 'action' => 'index'));
				} else {
					$this->Group->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			} else if( 'edit' === $this->action ) {
				$this->request->data = $this->Group->find(
					'first',
					array(
						'contain' => false,
						'conditions' => array(
							'Group.id' => $group_id,
						)
					)
				);

				if(true === empty($this->request->data)) {
					throw new NotFoundException();
				}
			}

			// Permissions actuelles s'il y a lieu
			if( false === isset( $this->request->data['Permission'] ) && false === empty($group_id) ) {
				$this->request->data['Permission'] = $this->WebrsaPermissions->getPermissionsHeritage($this->Group, $group_id);
			}

			// Permissions du parent s'il y a lieu
			$parent_id = Hash::get( $this->request->data, 'Group.parent_id' );
			$parentPermissions = array();
			if(false === empty( $parent_id )) {
				$parentPermissions = $this->WebrsaPermissions->getPermissionsHeritage($this->Group, $parent_id);
			}

			$acos = $this->WebrsaPermissions->getAcosTreeByDepartement();

			// Vérification: le nombre de champs qui seront renvoyés par le
			// formulaire ne doit pas excéder ce qui est défini dans max_input_vars
			$max_input_vars = ini_get( 'max_input_vars' );
			if( 2500 > $max_input_vars ) {
				$message = 'La valeur de max_input_vars (%d) est trop faible pour permettre l\'enregistrement des droits. Merci de vérifier la valeur recommandée dans la partie "Vérification de l\'application"';
				$this->Flash->error( sprintf( $message, $max_input_vars ) );
			}

			// Liste des groupes parents pour le menu déroulant
			$querydata = array(
				'fields' => array( 'Group.id', 'Group.name' ),
				'contain' => false,
				'conditions' => array(),
				'order' => array( 'Group.name ASC' )
			);

			// On ne peut hériter ni de soi-meme, ni d'un de ses enfants
			if( false === empty( $group_id ) ) {
				$children = $this->Group->getChildren( $group_id );
				$children[] = $group_id;
				if( false === empty( $children ) ) {
					$querydata['conditions'] = array(
						'NOT' => array( 'Group.id' => $children )
					);
				}
			}

			$groups = $this->Group->find( 'list', $querydata );

			$this->set( compact( 'groups', 'parentPermissions', 'acos' ) );
			$this->render( 'add_edit' );
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
			$permissions = $this->WebrsaPermissions->getPermissionsHeritage($this->Group, $group_id, $light);

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
			$this->ajax_get_permissions($group_id, true);
		}

		/**
		 * Réinitialise tous les droits de tous les utilisateurs de ce groupe à 'hérité'.
		 *
		 * @param integer $group_id
		 */
		public function resetDroitGroupUsers($group_id) {
			$permissions['Permission'] = $this->WebrsaPermissions->getPermissionsHeritage($this->Group, $group_id);
			$permissions['Permission'] = array_fill_keys(array_keys ($permissions['Permission']), 0);
			$users = $this->User->find( 'all', array ('contain' => false, 'conditions' => array ('group_id' => $group_id)) );

			foreach ($users as $user) {
				$this->User->id = $user['User']['id'];
				$this->User->begin();
				$success = $this->WebrsaPermissions->updatePermissions($this->User, $user['User']['id'], $permissions);

				if ($success) {
					$this->User->commit();
				} else {
					$this->User->rollback();
					break;
				}
			}

			if ($success) {
				$this->Flash->success( __( 'Save->success' ) );
			} else {
				$this->Flash->error( __( 'Save->error' ) );
			}

			$this->redirect(array('controller' => 'groups', 'action' => 'index'));
		}
	}
?>