<?php
	/**
	 * Code source de la classe EpsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe EpsController ...
	 *
	 * @package app.Controller
	 */
	class EpsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Eps';

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
			'Default',
			'Default2',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Eps:edit',
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
			'addparticipant' => 'create',
			'delete' => 'delete',
			'deleteparticipant' => 'delete',
			'edit' => 'update',
			'index' => 'read',
		);

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
		*
		*/

		public function index() {
			$fields = array(
				'Ep.id',
				'Ep.name',
				'Ep.adressemail',
				'Ep.identifiant',
				'Regroupementep.name'
			);

			$this->paginate = array(
				'fields' => $fields,
				'contain' => array(
					'Regroupementep'
				),
				'conditions' =>  $this->Ep->sqRestrictionsZonesGeographiques(
					'Ep.id',
					$this->Session->read( 'Auth.User.filtre_zone_geo' ),
					$this->Session->read( 'Auth.Zonegeographique' )
				),
				'limit' => 10
			);

			$this->_setOptions();
			$this->set( 'eps', $this->paginate( $this->Ep ) );
			$compteurs = array(
				'Regroupementep' => $this->Ep->Regroupementep->find( 'count' ),
				'Membreep' => $this->Ep->Membreep->find( 'count' )
			);
			$this->set( compact( 'compteurs' ) );
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
		*/

		protected function _add_edit( $id = null ) {
			if ( !empty( $this->request->data ) ) {
				$success = true;
				$this->Ep->begin();

				if ( !empty( $this->request->data['Ep']['regroupementep_id'] ) && Configure::read( 'Cg.departement' ) == 66 ) {
					$compositionValide = $this->Ep->Regroupementep->Compositionregroupementep->compositionValide( $this->request->data['Ep']['regroupementep_id'], $this->request->data['Membreep']['Membreep'] );
					$success = $compositionValide['check'];
					if ( !$success && isset( $compositionValide['error'] ) && !empty( $compositionValide['error'] ) ) {
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

				if ( empty( $this->request->data['Membreep']['Membreep'] ) ) {
					$success = false;
					$this->Ep->invalidate( 'Membreep.Membreep', 'Il est obligatoire de saisir au moins un membre pour participer à une commission d\'EP.' );
				}
// debug( $this->request->data );
				if ( $success ) {
					$this->Ep->create( $this->request->data );
					$success = $this->Ep->save() && $success;
				}

				$this->_setFlashResult( 'Save', $success );
				if( $success ) {
					$this->Ep->commit();
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->Ep->rollback();
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
			$this->set(compact('listeFonctionsMembres'));


			/**
			*
			*/
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

			$this->set( compact( 'fonctionsParticipants' ) );
			$this->_setOptions();

			$this->render( 'add_edit' );
		}

		/**
		*
		*/

		public function delete( $id ) {
			$success = $this->Ep->delete( $id );
			$this->_setFlashResult( 'Delete', $success );
			$this->redirect( array( 'action' => 'index' ) );
		}

		/**
		*
		*/

		public function addparticipant( $ep_id, $fonction_id ) {
			if ( !empty( $this->request->data ) ) {
				$this->Ep->EpMembreep->begin();
				$this->Ep->EpMembreep->create( $this->request->data );
				$success = $this->Ep->EpMembreep->save();
				$this->_setFlashResult( 'Save', $success );
				if ( $success ) {
					$this->Ep->EpMembreep->commit();
				}
				else {
					$this->Ep->EpMembreep->rollback();
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
		*/

		public function deleteparticipant($ep_id, $participant_id) {
			$success = $this->Ep->EpMembreep->deleteAll(
				array(
					'EpMembreep.ep_id'=>$ep_id,
					'EpMembreep.membreep_id'=>$participant_id
				)
			);
			$this->_setFlashResult( 'Delete', $success );
			$this->redirect( array( 'action' => 'edit', $ep_id ) );
		}
	}
?>