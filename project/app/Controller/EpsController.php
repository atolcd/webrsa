<?php
	/**
	 * Code source de la classe EpsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe EpsController s'occupe du paramétrage et de la gestion des
	 * équipes pluridisciplinaires.
	 *
	 * @package app.Controller
	 */
	class EpsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Eps';

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Eps:edit'
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'addparticipant' => 'create',
			'delete' => 'delete',
			'deleteparticipant' => 'delete',
			'edit' => 'update',
			'index' => 'read'
		);

		/**
		 * Liste des tables à ne pas prendre en compte dans les enregistrements
		 * vérifiés pour éviter les suppressions en cascade intempestives.
		 *
		 * @var array
		 */
		public $blacklist = array( 'eps_membreseps', 'eps_zonesgeographiques' );

		/**
		 *
		 */
		protected function _setOptions() {
			$options = array();
			if( $this->action != 'index' ) {
				$options['Ep']['regroupementep_id'] = $this->Ep->Regroupementep->find( 'list' );
				$options['Zonegeographique']['Zonegeographique'] = $this->Ep->Zonegeographique->find( 'list' );
			}
			$this->set( compact( 'options' ) );
		}

		/**
		 * Liste des équipes pluridisciplinaires.
		 */
		public function index() {
			if( false === $this->Ep->Behaviors->attached( 'Occurences' ) ) {
				$this->Ep->Behaviors->attach( 'Occurences' );
			}

			$this->paginate = array(
				'fields' => array(
					'Ep.id',
					'Ep.name',
					'Ep.adressemail',
					'Ep.identifiant',
					'Ep.actif',
					'Regroupementep.name',
					$this->Ep->sqHasLinkedRecords( true, $this->blacklist )
				),
				'contain' => array(
					'Regroupementep'
				),
				'conditions' => $this->Ep->sqRestrictionsZonesGeographiques(
					'Ep.id',
					$this->Session->read( 'Auth.User.filtre_zone_geo' ),
					$this->Session->read( 'Auth.Zonegeographique' )
				),
				'limit' => 50,
				'order' => array( 'Ep.identifiant DESC' )
			);
			$results = $this->paginate( $this->Ep );

			// Messages
			$messages = array();
			if( 0 === $this->Ep->Regroupementep->find( 'count' ) ) {
				$messages["Merci d'ajouter au moins un regroupement avant d'ajouter une EP."] = 'error';
			}
			if( 0 === $this->Ep->Membreep->find( 'count' ) ) {
				$messages["Merci d'ajouter au moins un membre avant d'ajouter une EP."] = 'error';
			}

			$options = $this->Ep->enums();
			$this->set( compact( 'messages', 'results', 'options' ) );
		}

		/**
		 * Formulaire d'ajout d'une équipe pluridisciplinaire.
		 */
		public function add() {
			$this->edit();
		}

		/**
		 * Formulaire de modification d'une équipe pluridisciplinaire.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$departement = Configure::read( 'Cg.departement' );

			if ( !empty( $this->request->data ) ) {
				// Retour à la liste en cas d'annulation
				if( isset( $this->request->data['Cancel'] ) ) {
					$this->redirect( array( 'action' => 'index' ) );
				}

				$this->Ep->begin();

				$this->Ep->create( $this->request->data );
				$success = $this->Ep->save( null, array( 'atomic' => false ) );

				if ( 66 == $departement && false === empty( $this->request->data['Ep']['regroupementep_id'] ) ) {
					$compositionValide = $this->Ep->Regroupementep->Compositionregroupementep->compositionValide( $this->request->data['Ep']['regroupementep_id'], $this->request->data['Membreep']['Membreep'] );
					$success = $compositionValide['check'] && $success;
					if ( false === $compositionValide['check'] && isset( $compositionValide['error'] ) && !empty( $compositionValide['error'] ) ) {
						$message = null;
						if ( $compositionValide['error'] == 'obligatoire' ) {
							$message = "Pour le regroupement sélectionné il faut au moins un membre : ".implode( ', ', $this->Ep->Regroupementep->Compositionregroupementep->listeFonctionsObligatoires( $this->request->data['Ep']['regroupementep_id'] ) ).".";
						}
						elseif ( $compositionValide['error'] == 'nbminmembre' ) {
							$message = "Il n'y a pas assez de membres prioritaires assignés pour le regroupement sélectionné.";
						}
						elseif ( $compositionValide['error'] == 'nbmaxmembre' ) {
							$message = "Il y a trop de membres assignés pour le regroupement sélectionné.";
						}
						$this->Ep->invalidate( 'Membreep.Membreep', $message );
					}
				}

				if ( true === empty( $this->request->data['Membreep']['Membreep'] ) ) {
					$success = false;
					$this->Ep->invalidate( 'Membreep.Membreep', 'Il est obligatoire de saisir au moins un membre pour participer à une commission d\'EP.' );
				}

				if( $success ) {
					$this->Ep->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->Ep->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			elseif ( $this->action == 'edit' ) {
				$this->request->data = $this->Ep->find(
					'first',
					array(
						'contain' => array(
							'Zonegeographique' => array(
								'fields' => array( 'id', 'libelle' )
							),
							'Membreep'
						),
						'conditions' => array( 'Ep.id' => $id )
					)
				);
				$this->assert( !empty( $this->request->data ), 'error404' );
				$this->set('ep_id', $id);
			}

			$listeFonctionsMembres = $this->Ep->Membreep->Fonctionmembreep->find( 'list' );

			$fonctionsParticipants = $this->Ep->Membreep->Fonctionmembreep->find(
				'all',
				array(
					'contain' => array(
						'Membreep' => array(
							'fields' => array(
								'id',
								'( "Membreep"."qual" || \' \' || "Membreep"."nom" || \' \' || "Membreep"."prenom" ) AS "Membreep__name"'
							)
						)
					)
				)
			);

			$this->set( compact( 'listeFonctionsMembres', 'fonctionsParticipants' ) );
			$this->_setOptions();

			$this->render( 'add_edit' );
		}

		/**
		 *
		 * @param integer $ep_id
		 * @param integer $fonction_id
		 */
		public function addparticipant( $ep_id, $fonction_id ) {
			if ( !empty( $this->request->data ) ) {
				$this->Ep->EpMembreep->begin();
				$this->Ep->EpMembreep->create( $this->request->data );
				$success = $this->Ep->EpMembreep->save( null, array( 'atomic' => false ) );

				if ( $success ) {
					$this->Ep->EpMembreep->commit();
					$this->Flash->success( __( 'Save->success' ) );
				}
				else {
					$this->Ep->EpMembreep->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$participants = $this->Ep->Membreep->find(
				'all',
				array(
					'conditions'=>array(
						'Membreep.fonctionmembreep_id' => $fonction_id
					),
					'contain' => false
				)
			);

			foreach( $participants as $key => $participant ) {
				$count = $this->Ep->EpMembreep->find(
					'count',
					array(
						'conditions'=>array(
							'EpMembreep.membreep_id' => $participant['Membreep']['id'],
							'EpMembreep.ep_id' => $ep_id
						)
					)
				);
				if ( $count > 0 ) {
					unset( $participants[$key] );
				}
			}

			$listeParticipants = array();
			foreach( $participants as $participant ) {
				$fontionsmembres = $this->Ep->Membreep->Fonctionmembreep->find( 'list', array( 'fields' => array( 'name' ) ) );
				$fonctionMembre = Set::enum( $participant['Membreep']['fonctionmembreep_id'], $fontionsmembres );
				$listeParticipants[$participant['Membreep']['id']] = implode( ' ', array( $participant['Membreep']['qual'], $participant['Membreep']['nom'], $participant['Membreep']['prenom'], ': ', $fonctionMembre ) );
			}
			$this->set( compact( 'listeParticipants' ) );
			$this->set( 'ep_id', $ep_id );
		}

		/**
		 *
		 * @param integer $ep_id
		 * @param integer $participant_id
		 */
		public function deleteparticipant($ep_id, $participant_id) {
			$success = $this->Ep->EpMembreep->deleteAll(
				array(
					'EpMembreep.ep_id'=>$ep_id,
					'EpMembreep.membreep_id'=>$participant_id
				)
			);
			if( $success ) {
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Flash->error( __( 'Delete->error' ) );
			}
			$this->redirect( array( 'action' => 'edit', $ep_id ) );
		}
	}
?>