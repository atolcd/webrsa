<?php
	/**
	* Code source de la classe DecisionspropospdosController.
	*
	* PHP 5.3
	*
	* @package app.Controller
	* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	*/
	App::uses( 'AppController', 'Controller' );

	/**
	* La classe DecisionspropospdosController ...
	*
	* @package app.Controller
	*/
	class DecisionspropospdosController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Decisionspropospdos';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'Gedooo.Gedooo',
			'Jetons2',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default2',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Decisionpropopdo',
			'Option',
			'Pdf',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Decisionspropospdos:edit',
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
			'decisionproposition' => 'read',
			'delete' => 'delete',
			'edit' => 'update',
			'view' => 'read',
		);

		/**
		 *
		 */
		protected function _options() {
			$options = (array)Hash::get( $this->Decisionpropopdo->enums(), 'Decisionpropopdo' );

			$this->set( 'decisionpdo', $this->Decisionpropopdo->Decisionpdo->find( 'list' ) );

			return $options;
		}

		/**		 * *******************************************************************
		 *
		 * ** ****************************************************************** */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**		 * *******************************************************************
		 *
		 * ** ****************************************************************** */
		protected function _add_edit( $id = null ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$this->set( 'options', $this->_options() );

			// Récupération des id afférents
			if( $this->action == 'add' ) {
				$propopdo_id = $id;

				$qd_propopdo = array(
					'conditions' => array(
						'Propopdo.id' => $id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$propopdo = $this->Decisionpropopdo->Propopdo->find( 'first', $qd_propopdo );

				$this->set( 'propopdo', $propopdo );
				$personne_id = Set::classicExtract( $propopdo, 'Propopdo.personne_id' );
				$dossier_id = $this->Decisionpropopdo->Propopdo->Personne->dossierId( $personne_id );
			}
			else if( $this->action == 'edit' ) {
				$decisionpropopdo_id = $id;
				$qd_decisionpropopdo = array(
					'conditions' => array(
						'Decisionpropopdo.id' => $decisionpropopdo_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$decisionpropopdo = $this->Decisionpropopdo->find( 'first', $qd_decisionpropopdo );
				$this->assert( !empty( $decisionpropopdo ), 'invalidParameter' );
				$propopdo_id = Set::classicExtract( $decisionpropopdo, 'Decisionpropopdo.propopdo_id' );
				$personne_id = Set::classicExtract( $decisionpropopdo, 'Propopdo.personne_id' );
				$dossier_id = $this->Decisionpropopdo->Propopdo->Personne->dossierId( $personne_id );
			}

			$this->assert( !empty( $dossier_id ), 'invalidParameter' );
			$this->set( 'dossier_id', $dossier_id );
			$this->set( 'propopdo_id', $propopdo_id );
			$this->set( 'personne_id', $personne_id );

			$this->Jetons2->get( $dossier_id );

			if( !empty( $this->request->data ) ) {
				$this->Decisionpropopdo->begin();

				if( $this->Decisionpropopdo->saveAll( $this->request->data, array( 'validate' => 'only', 'atomic' => false ) ) ) {
					$saved = true;

					$saved = $this->Decisionpropopdo->save( $this->request->data , array( 'atomic' => false ) );

					if( $saved ) {
						$saved = $this->Decisionpropopdo->Propopdo->updateEtat( $this->Decisionpropopdo->id );
					}

					if( $saved ) {
						$this->Decisionpropopdo->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'controller' => 'propospdos', 'action' => 'edit', $propopdo_id ) );
					}
					else {
						$this->Decisionpropopdo->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
				else {
					$this->Decisionpropopdo->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $decisionpropopdo;
			}

			$this->set( 'urlmenu', '/propospdos/index/'.$personne_id );
			$this->render( 'add_edit' );
		}

		/**
		 *   Enregistrement du courrier de proposition lors de l'enregistrement de la proposition
		 */
		public function decisionproposition( $id ) {
			$this->assert( !empty( $id ), 'error404' );

			$pdf = $this->Decisionpropopdo->getStoredPdf( $id );

			$this->assert( !empty( $pdf ), 'error404' );
			$this->assert( !empty( $pdf['Pdf']['document'] ), 'error500' ); // FIXME: ou en faire l'impression ?

			$this->Gedooo->sendPdfContentToClient( $pdf['Pdf']['document'], "Proposition_decision.pdf" );
		}

		/**
		 *
		 */
		public function view( $id ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$decisionpropopdo = $this->Decisionpropopdo->find(
					'first', array(
				'conditions' => array(
					'Decisionpropopdo.id' => $id,
				),
				'contain' => array(
					'Propopdo' => array(
						'fields' => array( 'personne_id' )
					),
					'Decisionpdo' => array(
						'fields' => array( 'libelle' )
					)
				)
					)
			);

			$this->assert( !empty( $decisionpropopdo ), 'invalidParameter' );

			$this->set( 'dossier_id', $this->Decisionpropopdo->dossierId( $id ) );

			// Retour à la page d'édition de la PDO
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'propospdos', 'action' => 'edit', Set::classicExtract( $decisionpropopdo, 'Decisionpropopdo.propopdo_id' ) ) );
			}

			$options = $this->Decisionpropopdo->enums();
			$this->set( compact( 'decisionpropopdo', 'options' ) );
			$this->set( 'urlmenu', '/propospdos/index/'.$decisionpropopdo['Propopdo']['personne_id'] );
		}

		/**
		 * Suppression de la proposition de décision
		 */
		public function delete( $id ) {
			$qd_decisionpropopdo = array(
				'conditions' => array(
					'Decisionpropopdo.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$decisionpropopdo = $this->Decisionpropopdo->find( 'first', $qd_decisionpropopdo );

			$pdo_id = Set::classicExtract( $decisionpropopdo, 'Decisionpropopdo.propopdo_id' );

			$success = $this->Decisionpropopdo->delete( $id );
			if( $success ) {
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Flash->error( __( 'Delete->error' ) );
			}
			$this->redirect( array( 'controller' => 'propospdos', 'action' => 'edit', $pdo_id ) );
		}

	}
?>