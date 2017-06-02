<?php
	/**
	* Code source de la classe Decisionstraitementspcgs66Controller.
	*
	* PHP 5.3
	*
	* @package app.Controller
	* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	*/
	App::uses( 'AppController', 'Controller' );

	/**
	* La classe Decisionstraitementspcgs66Controller ...
	*
	* @package app.Controller
	*/
	class Decisionstraitementspcgs66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Decisionstraitementspcgs66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'DossiersMenus',
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
			'Decisiontraitementpcg66',
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
			'add' => 'Decisionstraitementspcgs66:edit',
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
			'edit' => 'update',
			'index' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			$options = $this->Decisiontraitementpcg66->enums();
			$this->set( compact( 'options' ) );
		}

		/**
		 *
		 * @param integer $traitementpcg66_id
		 */
		public function index( $traitementpcg66_id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Decisiontraitementpcg66->Traitementpcg66->personneId( $traitementpcg66_id ) ) ) );

			//Récupération des informations de la personne concernée par le dossier
			$traitementpcg66 = $this->Decisiontraitementpcg66->Traitementpcg66->find(
				'first',
				array(
					'conditions' => array(
						'Traitementpcg66.id' => $traitementpcg66_id
					),
					'contain' => array(
						'Personnepcg66',
						'Descriptionpdo'
					)
				)
			);

			$listeDecisions = $this->Decisiontraitementpcg66->find(
				'all',
				array(
					'conditions' => array(
						'Decisiontraitementpcg66.traitementpcg66_id' => $traitementpcg66_id
					),
					'contain' => false,
					'order' => array(
						'Decisiontraitementpcg66.created DESC'
					)
				)
			);
			$this->set( compact( 'listeDecisions', 'traitementpcg66' ) );

			$this->set( 'urlmenu', '/traitementspcgs66/index/'.$traitementpcg66['Personnepcg66']['personne_id'] );
			$this->_setOptions();
		}

		/**
		 *
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 */
		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 * @param integer $id
		 */
		protected function _add_edit( $id = null ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			// Récupération des id afférents
			if( $this->action == 'add' ) {
				$traitementpcg66_id = $id;

				$traitementpcg66 = $this->Decisiontraitementpcg66->Traitementpcg66->find(
					'first',
					array(
						'conditions' => array(
							'Traitementpcg66.id' => $traitementpcg66_id
						),
						'contain' => array(
							'Descriptionpdo',
							'Personnepcg66'
						)
					)
				);
				$this->set( compact( 'traitementpcg66' ) );
				$personne_id = Set::classicExtract( $traitementpcg66_id, 'Personnepcg66.personne_id' );
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $traitementpcg66['Personnepcg66']['personne_id'] ) ) );

			$this->set( 'personnepcg66_id', $traitementpcg66['Personnepcg66']['id'] );
			$this->set( 'dossierpcg66_id', $traitementpcg66['Personnepcg66']['dossierpcg66_id'] );
			$this->set( 'personne_id', $traitementpcg66['Personnepcg66']['personne_id'] );

			$dossier_id = $this->Decisiontraitementpcg66->Traitementpcg66->Personnepcg66->Personne->dossierId( $traitementpcg66['Personnepcg66']['personne_id'] );

			$this->Jetons2->get( $dossier_id );

			if( !empty( $this->request->data ) ) {
				$this->Decisiontraitementpcg66->begin();

				$saved = $this->Decisiontraitementpcg66->save( $this->request->data , array( 'atomic' => false ) );

				///FIXME: à remettre pour gérer les états du dossierpcg66
				if ( $saved ) {
					$saved = $this->Decisiontraitementpcg66->Traitementpcg66->Personnepcg66->Dossierpcg66->updateEtatViaDecisionTraitement( $traitementpcg66['Personnepcg66']['dossierpcg66_id'] ) && $saved;
				}

				if( $saved ) {
					$this->Decisiontraitementpcg66->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'decisionstraitementspcgs66', 'action' => 'index', $traitementpcg66['Traitementpcg66']['id'] ) );
				}
				else {
					$this->Decisiontraitementpcg66->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			elseif( $this->action == 'edit' ){
				$this->request->data = $decisionpersonnepcg66;
			}

			$this->_setOptions();
			$this->set( 'urlmenu', '/traitementspcgs66/index/'.$personne_id );
			$this->render( 'add_edit' );
		}
	}
?>