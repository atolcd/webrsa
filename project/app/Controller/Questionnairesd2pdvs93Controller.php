<?php
	/**
	 * Code source de la classe Questionnairesd2pdvs93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Questionnairesd2pdvs93Controller ...
	 *
	 * @package app.Controller
	 */
	class Questionnairesd2pdvs93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Questionnairesd2pdvs93';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Questionnaired2pdv93',
			'WebrsaQuestionnaired2pdv93',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(

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
		 * Liste des questionnaires D2 de l'allocataire.
		 */
		public function index( $personne_id ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$this->_setEntriesAncienDossier( $personne_id, 'Questionnaired2pdv93' );

			// Remplit-on les conditions initiales ? / Messages à envoyer à l'utilisateur
			$status = $this->Questionnaired2pdv93->statusQuestionnaireD2( $personne_id );
			$messages = $this->Questionnaired2pdv93->messages( $personne_id );
			$ajoutPossible = $status['button'];
			$options = $this->Questionnaired2pdv93->enums();
			$this->set( compact( 'messages', 'ajoutPossible', 'options' ) );

			// Recherche de l'allocataire
			$personne = $this->Questionnaired2pdv93->Personne->find(
				'first',
				array(
					'conditions' => array(
						'Personne.id' => $personne_id
					),
					'contain' => false
				)
			);

			$querydata = array (
				'contain' => array(
					'Personne',
					'Emploiromev3' => array (
						'Appellationromev3',
					),
					'Sortieaccompagnementd2pdv93',
					'Questionnaired1pdv93' => array (
						'Rendezvous' => array (
							'Structurereferente'
						),
					),
					'Dureeemploi',
				),
				'conditions' => array(
					'Questionnaired2pdv93.personne_id' => $personne_id,
				),
				'order' => array(
					'Questionnaired2pdv93.date_validation DESC'
				)
			);

			$questionnairesd2pdvs93 = $this->WebrsaAccesses->getIndexRecords( $personne_id, $querydata );

			$this->set( compact( 'questionnairesd2pdvs93', 'personne' ) );
		}

		/**
		 * Formulaire d'ajout d'un questionnaire D2.
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Formulaire de modification d'un questionnaire D2.
		 *
		 * @throws NotFoundException
		 */
		public function edit( $id = null ) {
			if( $this->action == 'add' ) {
				$personne_id = $id;
				$this->WebrsaAccesses->check( null, $personne_id );
			}
			else {
				$this->WebrsaAccesses->check( $id );
				$personne_id = $this->Questionnaired2pdv93->personneId( $id );
			}

			if( $this->action == 'add' ) {
				$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			}
			else {
				$dossierMenu = $this->DossiersMenus->getDossierMenu( array( 'personne_id' => $personne_id ) );

				$questionnaired2pdv93 = $this->Questionnaired2pdv93->find(
					'first',
					array(
						'fields' => array( 'Rendezvous.structurereferente_id' ),
						'conditions' => array(
							'Questionnaired2pdv93.id' => $id
						),
						'contain' => false,
						'joins' => array(
							$this->Questionnaired2pdv93->join( 'Questionnaired1pdv93', array( 'type' => 'INNER' ) ),
							$this->Questionnaired2pdv93->Questionnaired1pdv93->join( 'Rendezvous', array( 'type' => 'INNER' ) )
						)
					)
				);
				// @deprecated
				$permission = WebrsaPermissions::checkD1D2( Hash::get( (array)$questionnaired2pdv93, 'Rendezvous.structurereferente_id' ) );

				if( !$permission ) {
					throw new Error403Exception( null );
				}
			}

			$this->Jetons2->get( $dossierMenu['Dossier']['id'] );

			$isAjax = ( $this->request->is( 'ajax' ) || Hash::get( $this->request->data, 'Questionnaired2pdv93.isajax' ) );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossierMenu['Dossier']['id'] );

				if( !$isAjax ) {
					$this->redirect( array( 'action' => 'index', $personne_id ) );
				}
				else {
					Configure::write ( 'debug', 0 );
					header( 'Content-Type: application/json' );
					echo htmlspecialchars( json_encode( array( 'success' => true, 'cancel' => true ) ), ENT_NOQUOTES );
					die();
				}
			}

			if( !empty( $this->request->data ) ) {
				$this->Questionnaired2pdv93->begin();

				// On désactive des champs dans le formulaire, on ne veut pas garder leurs anciennes valeurs
				$fields = array_keys( $this->Questionnaired2pdv93->schema(false) );
				$empty = array_combine( $fields, array_pad( array(), count( $fields ), null ) );
				$data = Hash::merge( array( 'Questionnaired2pdv93' => $empty ), $this->request->data );

				// Rome V3
				$data['Questionnaired2pdv93']['emploiromev3_id'] = $this->Questionnaired2pdv93->getEmploiromev3Id( $data );

				$this->Questionnaired2pdv93->create( $data );

				if( $this->Questionnaired2pdv93->save( null, array( 'atomic' => false ) ) ) {
					$this->Questionnaired2pdv93->commit();
					$this->Jetons2->release( $dossierMenu['Dossier']['id'] );

					if( !$isAjax ) {
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'action' => 'index', $personne_id ) );
					}
					else {
						Configure::write ( 'debug', 0 );
						header( 'Content-Type: application/json' );
						echo htmlspecialchars( json_encode( array( 'success' => true, 'save' => true ) ), ENT_NOQUOTES );
						die();
					}
				}
				else {
					$this->Questionnaired2pdv93->rollback();
					if( !$isAjax ) {
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->Questionnaired2pdv93->prepareFormData( $personne_id, $id );

				if( empty( $this->request->data  ) ) {
					throw new NotFoundException();
				}
			}
			else if( $this->action == 'add' ) {
				$this->request->data = $this->Questionnaired2pdv93->prepareFormData( $personne_id );
			}

			// Allocataire
			$personne = $this->Questionnaired2pdv93->Personne->find(
				'first',
				array(
					'conditions' => array(
						'Personne.id' => $personne_id
					),
					'contain' => false
				)
			);

			// Gestion de l'affichage de la question "Toujours en Emploi"
			//On n'affiche pas le boutton "Toujours en Emploi" sauf si :
			$allowToujoursEmploi = false;

			// Si l'allocataire as un B7
			$questionnaireB7 = $this->Questionnaired2pdv93->Personne->Questionnaireb7pdv93->find(
				'first',
				array(
					'conditions' => array(
						'Questionnaireb7pdv93.personne_id' => $personne_id,
						'Questionnaireb7pdv93.created <=' => $this->request->data['Questionnaired2pdv93']['date_validation'],						
					),
					'contain' => false,
					'order' => array(
						'Questionnaireb7pdv93.created' => 'DESC'
					)
				)
			);
			if ( ! empty ($questionnaireB7) ) {
				// et qu'aucun D2 ne clos l'emploi ce qui se traduit par :
				// - soit qu'aucun D2 n'existe entre le B7 et le D2 actuel
				// - soit le D2 précédent valide toujours en emploi
				$ancienQuestionnaireD2 = $this->Questionnaired2pdv93->find(
					'first',
					array(
						'conditions' => array(
							'Questionnaired2pdv93.personne_id' => $personne_id,
							'Questionnaired2pdv93.date_validation <' => $this->request->data['Questionnaired2pdv93']['date_validation'],
							'Questionnaired2pdv93.created <=' => $questionnaireB7['Questionnaireb7pdv93']['created'],
						),
						'contain' => false,
						'order' => array(
							'Questionnaired2pdv93.created' => 'DESC'
						)
					)
				);
				if (
					empty ($ancienQuestionnaireD2)
					|| ( $ancienQuestionnaireD2['Questionnaired2pdv93']['toujoursenemploi'] == 1 )
				) {
					// Alors on active le boutton
					$allowToujoursEmploi = true;
				}
			}


			// Options
			$options = array_merge(
				$this->Questionnaired2pdv93->options( array( 'find' => true ) ),
				$this->WebrsaQuestionnaired2pdv93->options()
			);

			$urlmenu = "/questionnairesd2pdvs93/index/{$personne_id}";

			$this->set( compact( 'personne_id', 'personne', 'options', 'dossierMenu', 'isAjax', 'urlmenu', 'allowToujoursEmploi' ) );

			if( $isAjax ) {
				$this->layout = null;
				$data = $this->request->data;
				$data['Questionnaired2pdv93']['isajax'] = true;
				$this->request->data = $data;
			}

			$this->render( 'edit' );
		}

		/**
		 * Suppression d'un questionnaire D2 et redirection vers l'index.
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->WebrsaAccesses->check( $id );

			$personne_id = $this->Questionnaired2pdv93->personneId( $id );

			$querydata = array(
				'fields' => array(
					'Questionnaired2pdv93.id',
					'Rendezvous.structurereferente_id',
				),
				'conditions' => array(
					'Questionnaired2pdv93.id' => $id
				),
				'contain' => false,
				'joins' => array(
					$this->Questionnaired2pdv93->join( 'Questionnaired1pdv93', array( 'type' => 'INNER' ) ),
					$this->Questionnaired2pdv93->Questionnaired1pdv93->join( 'Rendezvous', array( 'type' => 'INNER' ) )
				)
			);

			$questionnaired2pdv93 = $this->Questionnaired2pdv93->find( 'first', $querydata );
			if( empty( $questionnaired2pdv93 ) ) {
				throw new NotFoundException();
			}

			// @deprecated
			$permission = WebrsaPermissions::checkD1D2( Hash::get( $questionnaired2pdv93, 'Rendezvous.structurereferente_id' ) );
			if( !$permission ) {
				throw new Error403Exception( null );
			}

			$this->Questionnaired2pdv93->begin();

			if( $this->Questionnaired2pdv93->delete( $id ) ) {
				$this->Questionnaired2pdv93->commit();
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Questionnaired2pdv93->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->redirect( array( 'action' => 'index', $personne_id ) );
		}
	}
?>
