<?php
	/**
	 * Code source de la classe TypesactionsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe TypesactionsController ...
	 *
	 * @package app.Controller
	 */
	class TypesactionsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Typesactions';

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
			'add' => 'Typesactions:edit',
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

		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'parametrages', 'action' => 'index' ) );
			}

			$typesactions = $this->Typeaction->find(
				'all',
				array(
					'recursive' => -1,
					'order' => 'Typeaction.libelle ASC'
				)
			);
			$this->set( 'typesactions', $typesactions );
		}

		public function add() {
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}
			if( !empty( $this->request->data ) ) {
				$this->Typeaction->begin();
				if( $this->Typeaction->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) ) ) {
					$this->Typeaction->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'typesactions', 'action' => 'index' ) );
				}
				else {
					$this->Typeaction->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			$this->render( 'add_edit' );
		}

		public function edit( $typeaction_id = null ){
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}
			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $typeaction_id ), 'invalidParameter' );

			$typeaction = $this->Typeaction->find(
				'first',
				array(
					'conditions' => array(
						'Typeaction.id' => $typeaction_id
					),
					'recursive' => -1
				)
			);
			// Si action n'existe pas -> 404
			if( empty( $typeaction ) ) {
				$this->cakeError( 'error404' );
			}

			if( !empty( $this->request->data ) ) {
				if( $this->Typeaction->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'typesactions', 'action' => 'index', $typeaction['Typeaction']['id']) );
				}
			}
			else {
				$this->request->data = $typeaction;
			}
			$this->render( 'add_edit' );
		}


		/** ********************************************************************
		*
		*** *******************************************************************/

		public function delete( $typeaction_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $typeaction_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de la personne
			$typeaction = $this->Typeaction->find(
				'first',
				array( 'conditions' => array( 'Typeaction.id' => $typeaction_id )
				)
			);

			// Mauvais paramètre
			if( empty( $typeaction ) ) {
				$this->cakeError( 'error404' );
			}

			// Tentative de suppression ... FIXME
			if( $this->Typeaction->deleteAll( array( 'Typeaction.id' => $typeaction_id ), true ) ) {
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
				$this->redirect( array( 'controller' => 'typesactions', 'action' => 'index' ) );
			}
		}
	}
?>