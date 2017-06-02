<?php
	/**
	* Code source de la classe Decisionspersonnespcgs66Controller.
	*
	* PHP 5.3
	*
	* @package app.Controller
	* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	*/
	App::uses( 'AppController', 'Controller' );

	/**
	* La classe Decisionspersonnespcgs66Controller permet de gérer les décisions
	* au niveau des personnes d'un dossier PCG 66
	*
	* @package app.Controller
	*/
	class Decisionspersonnespcgs66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Decisionspersonnespcgs66';

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
			'Decisionpersonnepcg66',
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
			'add' => 'Decisionspersonnespcgs66:edit',
			'view' => 'Decisionspersonnespcgs66:index',
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
			'index' => 'read',
			'view' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			$options = array( );
			$options = $this->Decisionpersonnepcg66->Decisionpdo->find( 'list' );
			$this->set( compact( 'options' ) );
		}

		/**
		 *
		 * @param integer $personnepcg66_id
		 */
		public function index( $personnepcg66_id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Decisionpersonnepcg66->Personnepcg66Situationpdo->Personnepcg66->personneId( $personnepcg66_id ) ) ) );

			//Récupération des informations de la personne concernée par le dossier
			$personnepcg66 = $this->Decisionpersonnepcg66->Personnepcg66Situationpdo->Personnepcg66->find(
					'first', array(
				'conditions' => array(
					'Personnepcg66.id' => $personnepcg66_id
				),
				'contain' => array(
					'Personnepcg66Situationpdo' => array(
						'Situationpdo',
						'Decisionpersonnepcg66'
					),
					'Dossierpcg66'
				)
					)
			);
			$dossierpcg66_id = $personnepcg66['Personnepcg66']['dossierpcg66_id'];
			$personne_id = $personnepcg66['Personnepcg66']['personne_id'];
			$this->set( 'etatdossierpcg', $personnepcg66['Dossierpcg66']['etatdossierpcg'] );

			// Récupération du nom de l'allocataire
			$personne = $this->Decisionpersonnepcg66->Personnepcg66Situationpdo->Personnepcg66->Personne->find(
                'first', array(
				'fields' => array( $this->Decisionpersonnepcg66->Personnepcg66Situationpdo->Personnepcg66->Personne->sqVirtualField( 'nom_complet' ) ),
				'conditions' => array(
					'Personne.id' => $personnepcg66['Personnepcg66']['personne_id']
				),
				'contain' => false
					)
			);
			$nompersonne = Set::classicExtract( $personne, 'Personne.nom_complet' );
			$this->set( compact( 'nompersonne' ) );

			//Récuipération des propositions de décisions
			$listeDecisions = $this->Decisionpersonnepcg66->listeDecisionsParPersonnepcg66( $personnepcg66_id, $dossierpcg66_id );
			$this->set( compact( 'listeDecisions' ) );

			$this->set( 'personne_id', $personne_id );
			$this->set( 'personnepcg66_id', $personnepcg66_id );
			$this->set( 'dossierpcg66_id', $dossierpcg66_id );

			$this->set( 'urlmenu', '/traitementspcgs66/index/'.$personne_id );
			$this->_setOptions();
			$this->set( 'personne_id', $personne_id );
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
				$personnepcg66_id = $id;

				$personnepcg66 = $this->Decisionpersonnepcg66->Personnepcg66Situationpdo->Personnepcg66->find(
						'first', array(
					'conditions' => array(
						'Personnepcg66.id' => $personnepcg66_id
					),
					'contain' => array( 'Personne' )
						)
				);

				$this->set( 'personnepcg66', $personnepcg66 );
				$personne_id = Set::classicExtract( $personnepcg66, 'Personnepcg66.personne_id' );
				$dossierpcg66_id = Set::classicExtract( $personnepcg66, 'Personnepcg66.dossierpcg66_id' );
				$dossier_id = $this->Decisionpersonnepcg66->Personnepcg66Situationpdo->Personnepcg66->Personne->dossierId( $personne_id );
			}
			else if( $this->action == 'edit' ) {
				$decisionpersonnepcg66_id = $id;
				$qd_decisionpersonnepcg66 = array(
					'conditions' => array(
						'Decisionpersonnepcg66.id' => $decisionpersonnepcg66_id
					),
					'fields' => null,
					'order' => null,
					'contain' => array( 'Personnepcg66Situationpdo' )
				);
				$decisionpersonnepcg66 = $this->Decisionpersonnepcg66->find( 'first', $qd_decisionpersonnepcg66 );
				$this->assert( !empty( $decisionpersonnepcg66 ), 'invalidParameter' );

				$personnepcg66_id = Set::classicExtract( $decisionpersonnepcg66, 'Personnepcg66Situationpdo.personnepcg66_id' );
				$personnepcg66 = $this->Decisionpersonnepcg66->Personnepcg66Situationpdo->Personnepcg66->find(
					'first',
					array(
						'conditions' => array(
							'Personnepcg66.id' => $personnepcg66_id
						),
						'contain' => array( 'Personne' )
					)
				);
				$this->set( 'personnepcg66', $personnepcg66 );
				$personne_id = Set::classicExtract( $personnepcg66, 'Personnepcg66.personne_id' );
				$dossierpcg66_id = Set::classicExtract( $personnepcg66, 'Personnepcg66.dossierpcg66_id' );
				$dossier_id = $this->Decisionpersonnepcg66->Personnepcg66Situationpdo->Personnepcg66->Personne->dossierId( $personne_id );
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$this->assert( !empty( $dossier_id ), 'invalidParameter' );
			$this->set( 'dossier_id', $dossier_id );
			$this->set( 'personnepcg66_id', $personnepcg66_id );
			$this->set( 'dossierpcg66_id', $dossierpcg66_id );
			$this->set( 'personne_id', $personne_id );

			//Récupération de la liste des motifs de l'allocataire concerné
			$personnespcgs66Situationspdos = $this->Decisionpersonnepcg66->Personnepcg66Situationpdo->listeMotifsPourDecisions( $personnepcg66_id );
			$this->set( compact( 'personnespcgs66Situationspdos' ) );

			$this->Jetons2->get( $dossier_id );

			if( !empty( $this->request->data ) ) {
				$this->Decisionpersonnepcg66->begin();

				if( $this->Decisionpersonnepcg66->saveAll( $this->request->data, array( 'validate' => 'only', 'atomic' => false ) ) ) {
					$saved = true;

					$saved = $this->Decisionpersonnepcg66->save( $this->request->data , array( 'atomic' => false ) );

					if( $saved ) {
						$this->Decisionpersonnepcg66->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'controller' => 'decisionspersonnespcgs66', 'action' => 'index', $personnepcg66_id ) );
					}
					else {
						$this->Decisionpersonnepcg66->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
				else {
					$this->Decisionpersonnepcg66->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			elseif( $this->action == 'edit' ) {
				$this->request->data = $decisionpersonnepcg66;
			}

			$this->_setOptions();
			$this->set( 'urlmenu', '/traitementspcgs66/index/'.$personne_id );
			$this->render( 'add_edit' );
		}

		/**
		 * Enregistrement du courrier de proposition lors de l'enregistrement de la proposition
		 *
		 * @param integer $id
		 */
		public function decisionproposition( $id ) {
			$this->assert( !empty( $id ), 'error404' );
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Decisionpersonnepcg66->personneId( $id ) ) );


			$pdf = $this->Decisionpersonnepcg66->getStoredPdf( $id );

			$this->assert( !empty( $pdf ), 'error404' );
			$this->assert( !empty( $pdf['Pdf']['document'] ), 'error500' ); // FIXME: ou en faire l'impression ?

			$this->Gedooo->sendPdfContentToClient( $pdf['Pdf']['document'], "Proposition_decision.pdf" );
		}

		/**
		 *
		 * @param integer $id
		 */
		public function view( $id ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$decisionpersonnepcg66 = $this->Decisionpersonnepcg66->find(
					'first', array(
				'conditions' => array(
					'Decisionpersonnepcg66.id' => $id,
				),
				'contain' => array(
					'Personnepcg66Situationpdo' => array(
						'fields' => array( 'personnepcg66_id' )
					),
					'Decisionpdo' => array(
						'fields' => array( 'libelle' )
					)
				)
					)
			);

			$this->assert( !empty( $decisionpersonnepcg66 ), 'invalidParameter' );

			$this->set( 'dossier_id', $this->Decisionpersonnepcg66->Personnepcg66Situationpdo->Personnepcg66->Personne->dossierId( $id ) );
			$personnepcg66_id = Set::classicExtract( $decisionpersonnepcg66, 'Personnepcg66Situationpdo.personnepcg66_id' );
			$personnepcg66 = $this->Decisionpersonnepcg66->Personnepcg66Situationpdo->Personnepcg66->find(
					'first', array(
				'conditions' => array(
					'Personnepcg66.id' => $personnepcg66_id
				),
				'contain' => false
					)
			);

			//Récupération de la liste des motifs de l'allocataire concerné
			$personnespcgs66Situationspdos = $this->Decisionpersonnepcg66->Personnepcg66Situationpdo->listeMotifsPourDecisions( $personnepcg66_id );
			$this->set( compact( 'personnespcgs66Situationspdos' ) );

			$dossierpcg66_id = Set::classicExtract( $personnepcg66, 'Personnepcg66.dossierpcg66_id' );
			$this->set( 'personnepcg66_id', $personnepcg66_id );
			$personne_id = Set::classicExtract( $personnepcg66, 'Personnepcg66.personne_id' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );
			$this->set( 'personne_id', $personne_id );

			// Retour à la page d'édition de la PDO
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'decisionspersonnespcgs66', 'action' => 'index', $personnepcg66_id ) );
			}

			$this->set( compact( 'decisionpersonnepcg66' ) );
			$this->_setOptions();
			$this->set( 'urlmenu', '/traitementspcgs66/index/'.$personne_id );
		}

		/**
		 * Suppression de la proposition de décision
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Decisionpersonnepcg66->personneId( $id ) ) );

			$qd_decisionpersonnepcg66 = array(
				'conditions' => array(
					'Decisionpersonnepcg66.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$decisionpersonnepcg66 = $this->Decisionpersonnepcg66->find( 'first', $qd_decisionpersonnepcg66 );

			$personnepcg66_situationpdo_id = Set::classicExtract( $decisionpersonnepcg66, 'Decisionpersonnepcg66.personnepcg66_situationpdo_id' );

			$qd_personnepcg66_situationpdo_id = array(
				'conditions' => array(
					'Personnepcg66Situationpdo.id' => $personnepcg66_situationpdo_id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$personnepcg66_situationpdo_id = $this->Decisionpersonnepcg66->Personnepcg66Situationpdo->find( 'first', $qd_personnepcg66_situationpdo_id );

			$personnepcg66_id = Set::classicExtract( $personnepcg66_situationpdo, 'Personnepcg66Situationpdo.personnepcg66_id' );


			$success = $this->Decisionpersonnepcg66->delete( $id );
			if( $success ) {
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Flash->error( __( 'Delete->error' ) );
			}
			$this->redirect( array( 'controller' => 'decisionspersonnespcgs66', 'action' => 'index', $personnepcg66_id ) );
		}
	}
?>