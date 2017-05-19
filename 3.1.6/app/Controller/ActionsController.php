<?php
	/**
	 * Code source de la classe ActionsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ActionsController ...
	 *
	 * @package app.Controller
	 */
	class ActionsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Actions';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			
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
			'Actioninsertion',
			'Action',
			'Aidedirecte',
			'Option',
			'Prestform',
			'Refpresta',
			'Typeaction',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Actions:edit',
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
			parent::beforeFilter();
			$libtypaction = $this->Typeaction->find( 'list', array( 'fields' => array( 'libelle' ) ) );
			$this->set( 'libtypaction', $libtypaction );
		}

		/**
		*
		*/

		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'parametrages', 'action' => 'index' ) );
			}

			$actions = $this->Action->find(
				'all',
				array(
					'fields' => array_merge(
						$this->Action->fields(),
						$this->Action->Typeaction->fields(),
						array(
							'EXISTS( SELECT "contratsinsertion"."id" FROM contratsinsertion WHERE "contratsinsertion"."engag_object" = "Action"."code" ) AS "Action__occurences"'
						)
					),
					'joins' => array(
						$this->Action->join( 'Typeaction' )
					),
					'contain' => false
				)
			);

			$this->set( 'actions', $actions );
		}

		/**
		*
		*/

		public function add() {
			if( !empty( $this->request->data ) ) {
				$this->Action->begin();
				if( $this->Action->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) ) ) {
					$this->Action->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'actions', 'action' => 'index' ) );
				}
				else {
					$this->Action->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}

			$this->render( 'add_edit' );
		}

		/**
		*
		*/

		public function edit( $action_id = null ){
			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $action_id ), 'invalidParameter' );

			$action = $this->Action->find(
				'first',
				array(
					'conditions' => array(
						'Action.id' => $action_id
					),
					'recursive' => -1
				)
			);

			// Si action n'existe pas -> 404
			if( empty( $action ) ) {
				$this->cakeError( 'error404' );
			}

			if( !empty( $this->request->data ) ) {
				if( $this->Action->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'actions', 'action' => 'index', $action['Action']['id']) );
				}
			}
			else {
				$this->request->data = $action;
			}
			$this->render( 'add_edit' );
		}

		/**
		*
		*/

		public function delete( $action_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $action_id ) ) {
				throw new NotFoundException();
			}

			// Recherche de la personne
			$action = $this->Action->find(
				'first',
				array(
					'conditions' => array( 'Action.id' => $action_id ),
					'contain' => false
				)
			);

			// Mauvais paramètre
			if( empty( $action ) ) {
				throw new NotFoundException();
			}

			// Tentative de suppression
			$this->Action->begin();
			if( $this->Action->delete( $action_id ) ) {
				$this->Action->commit();
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			}
			else {
				$this->Action->rollback();
				$this->Session->setFlash( 'Erreur lors de la tentative de suppression', 'flash/error' );
			}

			$this->redirect( $this->referer() );
		}
	}
?>
