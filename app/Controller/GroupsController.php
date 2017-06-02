<?php
	/**
	 * Code source de la classe GroupsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Occurences', 'Model/Behavior' );

	/**
	 * La classe GroupsController ...
	 *
	 * @package app.Controller
	 */
	class GroupsController extends AppController
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
		public $components = array(
			'Dbdroits',
			'Menu',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Group',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Groups:edit',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(

		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'delete' => 'delete',
			'edit' => 'update',
			'index' => 'read',
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
		 *
		 */
		public function index() {
			$this->Group->Behaviors->attach( 'Occurences' );

			$querydata = array(
				'fields' => array_merge(
					$this->Group->fields(),
					$this->Group->ParentGroup->fields()
				),
				'joins' => array(
					$this->Group->join( 'ParentGroup', array( 'type' => 'LEFT OUTER' ) )
				),
				'contain' => false,
				'order' => array(
					'Group.name asc'
				)
			);
			$querydata = $this->Group->qdOccurencesExists( $querydata );

			$groups = $this->Group->find( 'all', $querydata );
			$this->set( 'groups', $groups );
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
				if( $this->Group->saveAll( $this->request->data ) ) {
					if ($this->request->data['Group']['parent_id']!=0) {
						$this->request->data['Droits'] = $this->Dbdroits->litCruDroits(array('model'=>'Group','foreign_key'=>$this->request->data['Group']['parent_id']));
						$this->Dbdroits->MajCruDroits(
							array(
								'model'=>'Group',
								'foreign_key'=>$this->Group->id,
								'alias'=>$this->request->data['Group']['name']
							),
							array (
								'model'=>'Group',
								'foreign_key'=>$this->request->data['Group']['parent_id']
							),
							$this->request->data['Droits']
						);
					}
					else {
						$this->Dbdroits->AddCru(
							array(
								'model'=>'Group',
								'foreign_key'=>$this->Group->id,
								'alias'=>$this->request->data['Group']['name']
							),
							null
						);
					}
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'groups', 'action' => 'index' ) );
				}
			}

			// Liste des groupes parents pour le menu déroulant
			$querydata = array(
				'fields' => array( 'Group.id', 'Group.name' ),
				'contain' => false,
				'order' => array( 'Group.name ASC' )
			);
			$groups = $this->Group->find( 'list', $querydata );
			$groups = array( 0 => null ) + $groups;
			$this->set( compact( 'groups' ) );

			$this->render( 'add_edit' );
		}

		/**
		*
		*/

		public function edit( $group_id = null ) {
			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $group_id ), 'error404' );


			if( !empty( $this->request->data ) ) {
				$group=$this->Group->read(null,$group_id);
				if( $this->Group->saveAll( $this->request->data ) ) {
					$new_droit=array();
					if ($group['Group']['parent_id']!=$this->request->data['Group']['parent_id']) {
						$new_droit = Set::diff(
							$this->Dbdroits->litCruDroits(
								array(
									'model'=>'Group',
									'foreign_key'=>$this->request->data['Group']['parent_id']
								)
							),
							$this->Dbdroits->litCruDroits(
								array(
									'model'=>'Group',
									'foreign_key'=>$group_id
								)
							)
						);
						$this->Dbdroits->MajCruDroits(
							array('model'=>'Group','foreign_key'=>$group_id,'alias'=>$this->request->data['Group']['name']),
							array('model'=>'Group','foreign_key'=>$this->request->data['Group']['parent_id']),
							$new_droit
						);
					}
					elseif ($this->request->data['Group']['parent_id']==0) {
						$new_droit = Set::diff($this->request->data['Droits'],$this->Dbdroits->litCruDroits(array('model'=>'Group', 'foreign_key'=>$group_id)));
							$this->Dbdroits->MajCruDroits(
							array('model'=>'Group','foreign_key'=>$group_id,'alias'=>$this->request->data['Group']['name']),
							null,
							$new_droit
						);
					}
					else {
						$new_droit = Set::diff($this->request->data['Droits'],$this->Dbdroits->litCruDroits(array('model'=>'Group', 'foreign_key'=>$group_id)));
							$this->Dbdroits->MajCruDroits(
							array('model'=>'Group','foreign_key'=>$group_id,'alias'=>$this->request->data['Group']['name']),
							array('model'=>'Group','foreign_key'=>$this->request->data['Group']['parent_id']),
							$new_droit
						);
					}
					$this->Dbdroits->restreintCruEnfantsDroits(
						array('model'=>'Group','foreign_key'=>$group_id),
						$new_droit
					);

					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'groups', 'action' => 'index' ) );
				}
				else {
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			$group = $this->Group->find(
				'first',
				array(
					'conditions' => array(
						'Group.id' => $group_id,
					)
				)
			);
			$this->request->data = $group;

			$this->set('listeCtrlAction', $this->Menu->menuCtrlActionAffichage());
			$this->request->data['Droits'] = $this->Dbdroits->litCruDroits(array('model'=>'Group','foreign_key'=>$group_id));

			// Vérification: le nombre de champs qui seront renvoyés par le
			// formulaire ne doit pas excéder ce qui est défini dans max_input_vars
			$max_input_vars = ini_get( 'max_input_vars' );
			if( 2500 > $max_input_vars ) {
				$message = 'La valeur de max_input_vars (%d) est trop faible pour permettre l\'enregistrement des droits. Merci de vérifier la valeur recommandée dans la partie "Vérification de l\'application"';
				$this->Session->setFlash( sprintf( $message, $max_input_vars ), 'flash/error' );
			}

			// Liste des groupes parents pour le menu déroulant
			$querydata = array(
				'fields' => array( 'Group.id', 'Group.name' ),
				'contain' => false,
				'conditions' => array(
					'NOT' => array(
						'Group.id' => $group_id
					)
				),
				'order' => array( 'Group.name ASC' )
			);
			$groups = $this->Group->find( 'list', $querydata );
			$groups = array( 0 => null ) + $groups;
			$this->set( compact( 'groups' ) );

			$this->render( 'add_edit' );
		}

		/**
		 * Suppression d'un groupe d'utilisateur.
		 *
		 * @param integer $id L'id du groupe à supprimer.
		 * @throws error404Exception
		 * @throws error500Exception
		 */
		public function delete( $id = null ) {
			if( !valid_int( $id ) ) {
				throw new error404Exception();
			}

			$querydata = array(
				'fields' => $this->Group->fields(),
				'conditions' => array( 'Group.id' => $id ),
				'contain' => false
			);
			$this->Group->Behaviors->attach( 'Occurences' );
			$querydata = $this->Group->qdOccurencesExists( $querydata );
			$group = $this->Group->find( 'first', $querydata );

			if( empty( $group ) ) {
				throw new error404Exception();
			}

			if( $group['Group']['occurences'] ) {
				$message = "Erreur lors de la tentative de suppression de l'entrée d'id {$id} pour le modèle {$this->Group->alias}: cette entrée possède des enregistrements liés.";
				throw new error500Exception( $message );
			}

			// Tentative de suppression
			$this->Group->begin();
			$success = $this->Group->delete( $id );

			$querydata = array(
				'fields' => array( 'id' ),
				'conditions' => array(
					'model' => 'Group',
					'foreign_key' => $id
				)
			);
			$aro = $this->Acl->Aro->find( 'first', $querydata );
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
				$this->Group->commit();
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			}
			else {
				$this->Group->rollback();
				$this->Session->setFlash( 'Erreur lors de la suppression', 'flash/error' );
			}

			$this->redirect( array( 'controller' => 'groups', 'action' => 'index' ) );
		}
	}
?>