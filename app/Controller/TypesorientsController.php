<?php
	/**
	 * Code source de la classe TypesorientsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe TypesorientsController ...
	 *
	 * @package app.Controller
	 */
	class TypesorientsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Typesorients';

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
			'Typeorient',
			'Structurereferente',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Typesorients:edit',
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

		public function _setOptions() {
			$options = (array)Hash::get( $this->Typeorient->enums(), 'Typeorient' );
			$this->set( compact( 'options' ) );
		}


		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'parametrages', 'action' => 'index' ) );
			}

			if( false === $this->Typeorient->Behaviors->attached( 'Occurences' ) ) {
				$this->Typeorient->Behaviors->attach( 'Occurences' );
			}

			$query = array(
				'fields' => array_merge(
					$this->Typeorient->fields(),
					array(
						$this->Typeorient->sqHasLinkedRecords()
					)
				),
				'recursive' => -1
			);
			$typesorients = $this->Typeorient->find( 'all', $query );

			$this->_setOptions();

			$this->set( 'typesorients', $typesorients );
		}

		public function add() {
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			$this->set( 'options', $this->Typeorient->listOptions() );

			$typesorients = $this->Typeorient->find(
				'all',
				array(
					'recursive' => -1
				)

			);
			$this->set('typesorients', $typesorients);

			$parentid = $this->Typeorient->find(
				'list',
				array(
					'fields' => array(
						'Typeorient.id',
						'Typeorient.lib_type_orient',
					),
					'conditions' => array( 'Typeorient.parentid' => null ),
					'recursive' => -1
				)
			);
			$this->set( 'parentid', $parentid );


			if( !empty( $this->request->data ) ) {
				if( $this->Typeorient->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'typesorients', 'action' => 'index' ) );
				}
			}
			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		public function edit( $typeorient_id = null ) {
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $typeorient_id ), 'error404' );

			$typesorients = $this->Typeorient->find(
				'all',
				array(
					'recursive' => -1
				)

			);
			$this->set('typesorients', $typesorients);

			$parentid = $this->Typeorient->find(
				'list',
				array(
					'fields' => array(
						'Typeorient.id',
						'Typeorient.lib_type_orient',
					),
					'conditions' => array(
						'Typeorient.parentid' => null,
						'Typeorient.id <>' => $typeorient_id,
					),
					'recursive' => -1
				)
			);
			$this->set( 'parentid', $parentid );

			$notif = $this->Typeorient->find(
				'list',
				array(
					'fields' => array(
						'Typeorient.modele_notif'
					),
					'recursive' => -1
				)
			);
			$this->set( 'notif', $notif );

			if( !empty( $this->request->data ) ) {
				if( $this->Typeorient->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'typesorients', 'action' => 'index' ) );
				}
			}
			else {
				$typeorient = $this->Typeorient->find(
					'first',
					array(
						'conditions' => array(
							'Typeorient.id' => $typeorient_id,
						),
						'recursive' => -1
					)
				);
				$this->request->data = $typeorient;
			}
			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		public function delete( $typeorient_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $typeorient_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de l'enregistrement
			if( false === $this->Typeorient->Behaviors->attached( 'Occurences' ) ) {
				$this->Typeorient->Behaviors->attach( 'Occurences' );
			}

			$query = array(
				'fields' => array_merge(
					$this->Typeorient->fields(),
					array(
						$this->Typeorient->sqHasLinkedRecords()
					)
				),
				'contain' => false,
				'conditions' => array(
					'Typeorient.id' => $typeorient_id
				)
			);
			$typeorient = $this->Typeorient->find( 'first', $query );

			// Mauvais paramètre
			if( empty( $typeorient ) ) {
				$this->cakeError( 'error404' );
			}

			// Structure référente encore liée à d'autres enregistrements ?
			if( true === $typeorient['Typeorient']['has_linkedrecords'] ) {
				$msgid = 'Tentative de suppression du type d\'orientation d\'id %d par l\'utilisateur %s alors que celui-ci est encore lié à des enregistrements';
				$msgstr = sprintf( $msgid, $typeorient_id, $this->Session->read( 'Auth.User.username' ) );
				throw new RuntimeException( $msgstr, 500 );
			}

			// Tentative de suppression
			$this->Typeorient->begin();
			if( $this->Typeorient->delete( array( 'Typeorient.id' => $typeorient_id ) ) ) {
				$this->Typeorient->commit();
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			}
			else {
				$this->Typeorient->rollback();
				$this->Session->setFlash( 'Impossible de supprimer l\'enregistrement', 'flash/error' );
			}

			$this->redirect( array( 'controller' => 'typesorients', 'action' => 'index' ) );
		}
	}
?>