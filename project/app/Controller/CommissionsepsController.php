<?php
	/**
	 * Fichier source de la classe CommissionsepsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'WebrsaAccessCommissionseps', 'Utility' );

	/**
	 * Gestion des séances d'équipes pluridisciplinaires.
	 *
	 * @package app.Controller
	 */
	class CommissionsepsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Commissionseps';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Gedooo.Gedooo',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
					'creationmodification',
					'attributiondossiers',
					'arbitrageep',
					'arbitragecg',
					'recherche',
					'decisions',
				),
			),
			'WebrsaAccesses' => array(
				'parentModelName' => 'Commissionep'
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Default',
			'Default2',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Commissionep',
			'Option',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'edit' => 'Commissionseps:add',
			'view' => 'Commissionseps:recherche',
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
			'arbitragecg' => 'read',
			'arbitrageep' => 'read',
			'attributiondossiers' => 'read',
			'creationmodification' => 'read',
			'decisioncg' => 'read',
			'decisionep' => 'read',
			'decisions' => 'read',
			'delete' => 'delete',
			'edit' => 'update',
			'exportcsv' => 'read',
			'fichessynthese' => 'read',
			'fichesynthese' => 'read',
			'impressionDecision' => 'update',
			'impressionpv' => 'update',
			'impressionpvcohorte' => 'update',
			'impressionsDecisions' => 'update',
			'ordredujour' => 'read',
			'printConvocationBeneficiaire' => 'update',
			'printConvocationParticipant' => 'update',
			'printConvocationsBeneficiaires' => 'update',
			'printConvocationsParticipants' => 'update',
			'printOrdreDuJour' => 'update',
			'printOrdresDuJour' => 'update',
			'recherche' => 'read',
			'traitercg' => 'update',
			'traiterep' => 'update',
			'validecommission' => 'update',
			'view' => 'read',
			'annulervalidation' => 'update',
		);

		/**
		 *
		 */
		public $etatsActions = array(
			'cree' => array(
				'dossierseps::choose',
				'membreseps::editliste',
				'commissionseps::edit',
				'commissionseps::delete',
				'membreseps::printConvocationParticipant',
				'membreseps::printConvocationsParticipants',
			),
			'associe' => array(
				'commissionseps::ordredujour',
				'dossierseps::choose',
				'commissionseps::printConvocationBeneficiaire',
				'commissionseps::printConvocationsBeneficiaires',
				'membreseps::printConvocationParticipant',
				'membreseps::printConvocationsParticipants',
				'commissionseps::printOrdreDuJour',
				'membreseps::editliste',
				'membreseps::editpresence',
				'commissionseps::edit',
				'commissionseps::delete',
			),
			'valide' => array(
				'commissionseps::ordredujour',
				'membreseps::editpresence',
				'commissionseps::printConvocationBeneficiaire',
				'commissionseps::printConvocationsBeneficiaires',
				'commissionseps::printOrdreDuJour',
				'commissionseps::delete',
			),
			'quorum' => array(
				'membreseps::editpresence',
				'commissionseps::delete',
				'membreseps::printConvocationParticipant',
				'membreseps::printConvocationsParticipants',
				'commissionseps::printConvocationBeneficiaire',
				'commissionseps::printConvocationsBeneficiaires',
			),
			'presence' => array(
				'membreseps::editpresence',
				'commissionseps::traiterep',
				'commissionseps::delete',
				'commissionseps::printOrdreDuJour',
				'membreseps::printConvocationParticipant',
				'membreseps::printConvocationsParticipants',
				'commissionseps::printConvocationBeneficiaire',
				'commissionseps::printConvocationsBeneficiaires',
			),
			'decisionep' => array(
				'commissionseps::printOrdreDuJour',
				'commissionseps::edit',
				'commissionseps::traiterep',
				'commissionseps::finaliserep',
				'commissionseps::delete',
				'membreseps::printConvocationParticipant',
				'membreseps::printConvocationsParticipants',
				'commissionseps::printConvocationBeneficiaire',
				'commissionseps::printConvocationsBeneficiaires',
			),
			'traiteep' => array(
				'membreseps::printConvocationParticipant',
				'membreseps::printConvocationsParticipants',
				'commissionseps::printOrdreDuJour',
				'commissionseps::impressionpv',
				'commissionseps::traitercg',
				'commissionseps::printConvocationBeneficiaire',
				'commissionseps::printConvocationsBeneficiaires',
				'commissionseps::annulervalidation',
			),
			'decisioncg' => array(
				'membreseps::printConvocationParticipant',
				'membreseps::printConvocationsParticipants',
				'commissionseps::printOrdreDuJour',
				'commissionseps::impressionpv',
				'commissionseps::traitercg',
				'commissionseps::finalisercg',
				'commissionseps::printConvocationBeneficiaire',
				'commissionseps::printConvocationsBeneficiaires',
			),
			'traite' => array(
				'membreseps::printConvocationParticipant',
				'membreseps::printConvocationsParticipants',
				'commissionseps::printOrdreDuJour',
				'commissionseps::impressionpv',
				'commissionseps::printDecision',
				'commissionseps::printConvocationBeneficiaire',
				'commissionseps::printConvocationsBeneficiaires',
			),
			'annule' => array( )
		);

		/**
		 * TODO:
		 * 	- plus générique - scinder les CG
		 * 	- est-ce que ça a  du sens de mettre typeorient/structurereferente/referent dans $options['Commissionep']['xxxx']
		 */
		protected function _setOptions() {
			$options = Set::merge(
				$this->Commissionep->Passagecommissionep->Dossierep->enums(),
				$this->Commissionep->enums(),
				$this->Commissionep->CommissionepMembreep->enums(),
				$this->Commissionep->Passagecommissionep->enums(),
				array(
					'Foyer' => array(
						'sitfam' => $this->Option->sitfam()
					)
				),
				array(
					'Orientstruct' => array(
						'structurereferente_id' => $this->Commissionep->Passagecommissionep->Dossierep->Personne->Orientstruct->Structurereferente->find( 'list', array( 'fields' => array( 'lib_struc' ) ) )
					)
				)
			);

			$options['Decisiondossierpcg66']['decisionpdo_id'] = $this->Commissionep->Passagecommissionep->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->Dossierpcg66->Decisiondossierpcg66->Decisionpdo->find('list');

			$options[$this->modelClass]['ep_id'] = $this->{$this->modelClass}->Ep->listOptions(
					$this->Session->read( 'Auth.User.filtre_zone_geo' ), $this->Session->read( 'Auth.Zonegeographique' )
			);
			$options['Ep']['regroupementep_id'] = $this->{$this->modelClass}->Ep->Regroupementep->find( 'list' );

			// Ajout des enums pour les thématiques du CG uniquement
			foreach( $this->Commissionep->Ep->Regroupementep->themes() as $theme ) {
				$model = Inflector::classify( $theme );
				$options = Set::merge( $options, $this->Commissionep->Passagecommissionep->Dossierep->{$model}->enums() );

				$modeleDecision = Inflector::classify( "decision{$theme}" );
				$options = Set::merge( $options, $this->Commissionep->Passagecommissionep->{$modeleDecision}->enums() );
			}

			// Suivant l'action demandée
			if( !in_array( $this->action, array( 'add', 'edit', 'index' ) ) ) {
				$typesorients = $this->Commissionep->Passagecommissionep->Dossierep->Personne->Orientstruct->Typeorient->listOptions();
				$structuresreferentes = $this->Commissionep->Passagecommissionep->Dossierep->Personne->Orientstruct->Structurereferente->list1Options();
				$referents = $this->Commissionep->Passagecommissionep->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->Referent->WebrsaReferent->listOptions();
				if( Configure::read( 'Cg.departement' ) == 66 ) {
					$options['Decisionsaisinepdoep66']['decisionpdo_id'] = $this->Commissionep->Passagecommissionep->Decisionsaisinepdoep66->Decisionpdo->find( 'list' );
					$options = Set::merge( $options, $this->Commissionep->Passagecommissionep->Dossierep->Personne->Bilanparcours66->enums() );
				}
			}

			$liste_typesorients = $this->Commissionep->Passagecommissionep->Dossierep->Personne->Orientstruct->Typeorient->find( 'list' );
			$liste_structuresreferentes = $this->Commissionep->Passagecommissionep->Dossierep->Personne->Orientstruct->Structurereferente->find( 'list' );
			$liste_referents = $this->Commissionep->Passagecommissionep->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->Referent->find( 'list' );

			$this->set( 'liste_typesorients', $liste_typesorients );
			$this->set( 'liste_structuresreferentes', $liste_structuresreferentes );
			$this->set( 'liste_referents', $liste_referents );

			// Suivant le CG
			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$listeTypesorients = $this->Commissionep->Passagecommissionep->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->Typeorient->find( 'list' );
				$listeStructuresreferentes = $this->Commissionep->Passagecommissionep->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->Structurereferente->find( 'list' );
				$listeReferents = $this->Commissionep->Passagecommissionep->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->Referent->find( 'list' );
				$this->set( compact( 'listeTypesorients' ) );
				$this->set( compact( 'listeStructuresreferentes' ) );
				$this->set( compact( 'listeReferents' ) );
				$options = Set::merge(
								$options, $this->Commissionep->Passagecommissionep->Dossierep->Defautinsertionep66->enums()
				);
				$options = Set::merge(
								$options, $this->Commissionep->Passagecommissionep->Dossierep->Saisinebilanparcoursep66->enums()
				);
				$options['Saisinebilanparcoursep66']['typeorientprincipale_id'] = $this->Commissionep->Passagecommissionep->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->Typeorient->listRadiosOptionsPrincipales( array_values( Hash::flatten( Configure::read( 'Orientstruct.typeorientprincipale' ) ) ) );
				$options['Saisinebilanparcoursep66']['typeorient_id'] = $this->Commissionep->Passagecommissionep->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->Typeorient->list1Options();
				$options['Saisinebilanparcoursep66']['structurereferente_id'] = $this->Commissionep->Passagecommissionep->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->Structurereferente->list1Options( array( 'orientation' => 'O' ) );
			}
			else if( Configure::read( 'Cg.departement' ) == 93 ) {
				$options = Hash::merge(
					$options,
					$this->Commissionep->Passagecommissionep->Dossierep->Nonrespectsanctionep93->enums(),
					$this->Commissionep->Passagecommissionep->Dossierep->Signalementep93->Contratinsertion->enums(),
					array(
						'Contratinsertion' => array(
							'duree_engag' => $this->Option->duree_engag()
						),
						'Cer93' => array(
							'duree' => $this->Option->duree_engag()
						)
					)
				);
			}
			else if( Configure::read( 'Cg.departement' ) == 58 ) {
				$this->set( 'listesanctionseps58', $this->Commissionep->Passagecommissionep->Decisionsanctionep58->Listesanctionep58->find( 'list' ) );
				$regularisationlistesanctionseps58 = Set::merge(
								$this->Commissionep->Passagecommissionep->Decisionsanctionep58->enums(), $this->Commissionep->Passagecommissionep->Decisionsanctionrendezvousep58->enums()
				);
				$this->set( compact( 'regularisationlistesanctionseps58' ) );
				$this->set( 'typesrdv', $this->Commissionep->Passagecommissionep->Dossierep->Sanctionrendezvousep58->Rendezvous->Typerdv->find( 'list' ) );
			}

			$this->set( compact( 'options' ) );
			$this->set( compact( 'typesorients' ) );
			$this->set( compact( 'structuresreferentes' ) );
			$this->set( compact( 'referents' ) );
		}

		/**
		 *
		 */
		protected function _index( $etape = null ) {
			if( !empty( $this->request->data ) ) {
				$paginate['Commissionep'] = $this->Commissionep->WebrsaCommissionep->search(
						$this->request->data, $this->Session->read( 'Auth.User.filtre_zone_geo' ), $this->Session->read( 'Auth.Zonegeographique' )
				);

				$paginate['Commissionep']['limit'] = 10;
				$paginate['Commissionep']['order'] = array( 'Commissionep.dateseance DESC' );

				switch( $etape ) {
					case 'recherche':
						$paginate['Commissionep']['conditions']['etatcommissionep'] = array( 'cree', 'associe' );
						break;
					case 'creationmodification':
						$paginate['Commissionep']['conditions']['etatcommissionep'] = array( 'cree', 'associe' );
						break;
					case 'attributiondossiers':
						$paginate['Commissionep']['conditions']['etatcommissionep'] = array( 'cree', 'associe' );
						break;
					case 'arbitrageep':
						$paginate['Commissionep']['conditions']['etatcommissionep'] = array( 'associe', 'valide', 'quorum', 'presence', 'decisionep', 'traiteep' );
						break;
					case 'arbitragecg':
						$paginate['Commissionep']['conditions']['etatcommissionep'] = array( 'traiteep', 'decisioncg' );
						break;
					case 'decisions':
						$paginate['Commissionep']['conditions']['etatcommissionep'] = array( 'traite', 'annule' );
						break;
				}

				$this->paginate = $paginate;
				$commissionseps = $this->paginate( $this->Commissionep );
				$themeseps = $this->Commissionep->Ep->themes();

				foreach( $commissionseps as $key => $commissionep ) {
					//Calcul du nombre de participants
					$nbparticipants = $this->Commissionep->Membreep->CommissionepMembreep->find(
							'count', array(
						'conditions' => array(
							'CommissionepMembreep.commissionep_id' => Set::classicExtract( $commissionep, 'Commissionep.id' ),
							'CommissionepMembreep.membreep_id IS NOT NULL'
						),
						'contain' => false
							)
					);
					$commissionseps[$key]['Commissionep']['nbparticipants'] = $nbparticipants;

					//Calcul du nombre d'absents parmi les participants
					$nbabsents = $this->Commissionep->Membreep->CommissionepMembreep->find(
							'count', array(
						'conditions' => array(
							'CommissionepMembreep.commissionep_id' => Set::classicExtract( $commissionep, 'Commissionep.id' ),
							'CommissionepMembreep.membreep_id IS NOT NULL',
							'CommissionepMembreep.presence <> \'present\''
						),
						'contain' => false
							)
					);
					$commissionseps[$key]['Commissionep']['nbabsents'] = $nbabsents;

					// Niveau de décision maximum, par commission
					$regroupementep = $commissionep['Ep']['Regroupementep'];
					$niveaudecisionmax = 'nontraite';
					foreach( $themeseps as $themeep ) {
						if( $regroupementep[$themeep] == 'decisioncg' ) {
							$niveaudecisionmax = 'decisioncg';
						}
						else if( $niveaudecisionmax != 'decisioncg' && $regroupementep[$themeep] == 'decisionep' ) {
							$niveaudecisionmax = 'decisionep';
						}
					}
					// Libellé décision max
					$libelledecisionmax = 'Non traité';
					if( $niveaudecisionmax == 'decisioncg' ) {
						$libelledecisionmax = 'Voir les décisions';
					}
					else if( $niveaudecisionmax == 'decisionep' ) {
						if( Configure::read( 'Cg.departement' ) == 58 ) {
							$libelledecisionmax = 'Voir les décisions';
						}
						else {
							$libelledecisionmax = 'Voir les avis';
						}
					}
					$commissionseps[$key]['Commissionep']['niveaudecisionmax'] = $niveaudecisionmax;
					$commissionseps[$key]['Commissionep']['libelledecisionmax'] = $libelledecisionmax;
				}
				$this->set( 'commissionseps', $commissionseps );
			}

			$this->_setOptions();
			$compteurs = array( 'Ep' => $this->Commissionep->Ep->find( 'count' ) );
			$this->set( compact( 'compteurs' ) );
			$this->render( 'index' );
		}

		/**
		 *
		 */
		public function creationmodification() {
			$this->_index( $this->action );
		}

		/**
		 *
		 */
		public function attributiondossiers() {
			$this->_index( $this->action );
		}

		/**
		 *
		 */
		public function arbitrageep() {
			$this->_index( $this->action );
		}

		/**
		 *
		 */
		public function arbitragecg() {
			$this->_index( $this->action );
		}

		/**
		 *
		 */
		public function decisions() {
			$this->_index( $this->action );
		}

		/**
		 *
		 */
		public function recherche() {
			$this->_index( $this->action );
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
			if( !empty( $this->request->data ) ) {
				$this->Commissionep->create( $this->request->data );
				$success = $this->Commissionep->save( null, array( 'atomic' => false ) );

				if( $success ) {
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'view', $this->Commissionep->id ) );
				}
				else {
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->WebrsaAccesses->check( $id );

				$this->request->data = $this->Commissionep->find(
					'first',
					array(
						'contain' => false,
						'conditions' => array( 'Commissionep.id' => $id )
					)
				);
				$this->assert( !empty( $this->request->data ), 'error404' );

				if( in_array( $this->request->data['Commissionep']['etatcommissionep'], array( 'decisionep', 'decisioncg', 'annulee' ) ) ) {
					$this->Flash->error( 'Impossible de modifier une commission d\'EP lorsque celle-ci comporte déjà des avis ou des décisions.' );
					$this->redirect( $this->referer() );
				}
			}
			else if( $this->action == 'add' ) {
				$this->request->data['Commissionep']['etatcommissionep'] = 'cree';
			}

			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		/**
		 * Fonction de suppression de la commission d'ep
		 * Passe tous ses dossiers liés dans l'état reporté et son état à annulé
		 */
		public function delete( $commissionep_id ) {
			$this->WebrsaAccesses->check( $commissionep_id );

			if( !empty( $this->request->data ) ) {
				$success = true;
				$this->Commissionep->begin();
				$this->Commissionep->id = $commissionep_id;

				$commissionep = array(
					'Commissionep' => array(
						'id' => $commissionep_id,
						'etatcommissionep' => 'annule',
						'raisonannulation' => $this->request->data['Commissionep']['raisonannulation']
					)
				);
				$this->Commissionep->create( $commissionep );
				$success = $this->Commissionep->save( null, array( 'atomic' => false ) ) && $success;

				$this->Commissionep->Passagecommissionep->updateAllUnBound(
						array( 'Passagecommissionep.etatdossierep' => '\'reporte\'' ), array(
					'"Passagecommissionep"."commissionep_id"' => $commissionep_id
						)
				);

				if( $success ) {
					$this->Commissionep->commit();
					$this->Flash->success( __( 'Delete->success' ) );
					$this->redirect( array( 'controller' => 'commissionseps', 'action' => 'view', $commissionep_id ) );
				}
				else {
					$this->Commissionep->rollback();
					$this->Flash->error( __( 'Delete->error' ) );
				}
			}
			$this->set( 'commissionep_id', $commissionep_id );
		}

		/**
		 * Traitement d'une séance à un certain niveau de décision.
		 */
		protected function _traiter( $commissionep_id, $niveauDecision ) {
			if( isset( $this->request->data['Valider'] ) ) {
				$this->_finaliser( $commissionep_id, $niveauDecision );
			}

			$commissionep = $this->Commissionep->find(
					'first', array(
				'conditions' => array(
					'Commissionep.id' => $commissionep_id,
				),
				'contain' => array(
					'Ep'
				)
					)
			);

			$this->assert( !empty( $commissionep ), 'error404' );
			// On s'assure que le commission ne soit pas dans un état final
			$this->assert( !in_array( $commissionep['Commissionep']['etatcommissionep'], array( 'traite', 'annule', 'reporte' ) ) );

			// Etape OK ?
			$etapePossible = (
					( ( $niveauDecision == 'ep' ) && empty( $commissionep['Commissionep']['etatcommissionep'] ) ) // OK
					|| ( ( $niveauDecision == 'cg' ) && ( $commissionep['Commissionep']['etatcommissionep'] == 'ep' ) ) // OK
					|| ( $commissionep['Commissionep']['etatcommissionep'] != 'cg' ) // OK
					);

			if( !$etapePossible ) {
				$this->Flash->error( 'Impossible de traiter les dossiers d\'une commission d\'EP à une étape antérieure.' );
				$this->redirect( $this->referer() );
			}

			if( !empty( $this->request->data ) && !isset( $this->request->data['Valider'] ) ) {
				$this->Commissionep->begin();
				$success = $this->Commissionep->WebrsaCommissionep->saveDecisions( $commissionep_id, $this->request->data, $niveauDecision );

				if( $success ) {
					$this->Commissionep->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'commissionseps', 'action' => 'traiter'.$niveauDecision, $commissionep_id ) );
				}
				else {
					$this->Commissionep->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$dossiers = $this->Commissionep->WebrsaCommissionep->dossiersParListe( $commissionep_id, $niveauDecision, $this->name.'.'.$this->action.'.order' );

			if( empty( $this->request->data ) ) {
				$this->request->data = $this->Commissionep->WebrsaCommissionep->prepareFormData( $commissionep_id, $dossiers, $niveauDecision );
			}

			$this->set( compact( 'commissionep', 'dossiers' ) );
			$this->set( 'commissionep_id', $commissionep_id );
			$this->_setOptions();
		}

		/**
		 * Traitement d'une séance au niveau de décision EP
		 */
		public function traiterep( $commissionep_id ) {
			$this->WebrsaAccesses->check( $commissionep_id );
			$this->_traiter( $commissionep_id, 'ep' );
		}

		/**
		 *
		 */
		protected function _finaliser( $commissionep_id, $niveauDecision ) {
			$commissionep = $this->Commissionep->find(
					'first', array(
				'conditions' => array(
					'Commissionep.id' => $commissionep_id,
				),
				'contain' => false
					)
			);

			$this->assert( !empty( $commissionep ), 'error404' );

			// Etape OK ?
			$etapePossible = (
					( ( $niveauDecision == 'ep' ) && empty( $commissionep['Commissionep']['etatcommissionep'] ) ) // OK
					|| ( ( $niveauDecision == 'cg' ) && ( $commissionep['Commissionep']['etatcommissionep'] == 'ep' ) ) // OK
					|| ( $commissionep['Commissionep']['etatcommissionep'] != 'cg' ) // OK
					);

			if( !$etapePossible ) {
				$this->Flash->error( 'Impossible de finaliser les décisions des dossiers d\'une commission d\'EP à une étape antérieure.' );
				$this->redirect( $this->referer() );
			}

			if( !$this->Gedooo->check( true, false ) ) {
				$this->Flash->error( 'Le serveur d\'impression n\'est pas disponible ou ne fonctionne pas correctement.' );
			}
			else {
				$this->Commissionep->begin();
				$success = $this->Commissionep->WebrsaCommissionep->finaliser( $commissionep_id, $this->request->data, $niveauDecision, $this->Session->read( 'Auth.User.id' ) );

				if( $success ) {
					$this->Flash->success( __( 'Save->success' ) );
					$this->Commissionep->commit();
					$this->redirect( array( 'action' => "decision{$niveauDecision}", $commissionep_id ) );
				}
				else {
					$this->Commissionep->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
		}

		/**
		 * Traitement d'une séance au niveau de décision CG
		 * TODO: les dossiers qui ne doivent pas être traités par le CG ne doivent pas apparaître ici
		 * TODO: si tous les thèmes se décident niveau EP, plus besoin de passer par ici.
		 */
		public function traitercg( $commissionep_id ) {
			$this->WebrsaAccesses->check( $commissionep_id );
			$this->_traiter( $commissionep_id, 'cg' );
		}

		/**
		 * Affiche la séance EP avec la liste de ses membres.
		 * @param integer $commissionep_id
		 */
		public function view( $commissionep_id = null ) {
			$this->WebrsaAccesses->check( $commissionep_id );

			// Enregistrement des heures de passage
			if (isset ($this->request->data['Passagecommissionep'])) {
				$passagecommissioneps = $this->Commissionep->Passagecommissionep->find(
					'all',
					array(
						'conditions' => array(
							'Passagecommissionep.commissionep_id' => $commissionep_id
						),
					)
				);

				$dataPassagecommissioneps = $this->request->data['Passagecommissionep'];
				$nbPassagecommissionep = count ($passagecommissioneps);

				for ($i = 0; $i < $nbPassagecommissionep; $i++) {
					foreach ($dataPassagecommissioneps as $dataPassagecommissionep) {
						if ($dataPassagecommissionep['id'] == $passagecommissioneps[$i]['Passagecommissionep']['id']) {
							$temp = new DateTime ();
							$heure = explode(':', $dataPassagecommissionep['heureseance']);
							if (is_array($heure) && count($heure) >= 2) {
								$temp->setTime($heure[0], $heure[1]);
								$dataPassagecommissionep['heureseance'] = $temp->format('H:i:s');
								$passagecommissioneps[$i]['Passagecommissionep']['heureseance'] = $dataPassagecommissionep['heureseance'];
							}
							else {
								$passagecommissioneps[$i]['Passagecommissionep']['heureseance'] = '';
							}
							break;
						}
					}
				}

				$success = true;

				if( !empty( $passagecommissioneps ) ) {
					$success = $this->Commissionep->Passagecommissionep->saveAll( $passagecommissioneps, array( 'atomic' => false ) ) && $success;
				}

				if( $success ) {
					$this->Commissionep->commit();
					$this->Flash->success( __( 'Save->success' ) );
				}
				else {
					$this->Commissionep->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$commissionep = $this->Commissionep->find(
					'first', array(
				'conditions' => array( 'Commissionep.id' => $commissionep_id ),
				'contain' => array(
					'Ep' => array( 'Regroupementep' ),
					'CommissionepMembreep'
				)
					)
			);
			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$listeMembrePresentRemplace = array( );
				foreach( $commissionep['CommissionepMembreep'] as $membre ) {
					if( $membre['reponse'] == 'confirme' || $membre['reponse'] == 'remplacepar' ) {
						$listeMembrePresentRemplace[] = $membre['membreep_id'];
					}
				}

				$compositionValide = $this->Commissionep->Ep->Regroupementep->Compositionregroupementep->compositionValide( $commissionep['Ep']['regroupementep_id'], $listeMembrePresentRemplace );
				if( !$compositionValide['check'] && isset( $compositionValide['error'] ) && !empty( $compositionValide['error'] ) ) {
					$message = null;
					if( $compositionValide['error'] == 'obligatoire' ) {
						$message = "Pour une commission de ce regroupement, il faut au moins un membre occupant la fonction : ".implode( ' ou ', $this->Commissionep->Ep->Regroupementep->Compositionregroupementep->listeFonctionsObligatoires( $commissionep['Ep']['regroupementep_id'] ) ).".";
					}
					elseif( $compositionValide['error'] == 'nbminmembre' ) {
						$message = "Il n'y a pas assez de membres qui ont accepté de venir ou qui se font remplacer pour que la commission puisse avoir lieu.";
					}
					elseif( $compositionValide['error'] == 'nbmaxmembre' ) {
						$message = "Il y a trop de membres qui ont accepté de venir ou qui se font remplacer pour que la commission puisse avoir lieu.";
					}
					$this->set( 'messageQuorum', $message );
				}
			}



			list( $jourCommission, $heureCommission ) = explode( ' ', $commissionep['Commissionep']['dateseance'] );
			$presencesPossible = ( date( 'Y-m-d' ) >= $jourCommission );
			$this->set( compact( 'presencesPossible' ) );

			$this->set( 'commissionep', $commissionep );
			$this->_setOptions();


			// Dossiers à passer en séance, par thème traité
			$themes = array_keys( $this->Commissionep->WebrsaCommissionep->themesTraites( $commissionep_id ) );

			$this->set( compact( 'themes' ) );
			$dossiers = array( );
			$countDossiers = 0;
			$sort = isset($this->request->params['named']['sort']) ? $this->request->params['named']['sort'] : '';
			$direction = isset($this->request->params['named']['direction']) ? $this->request->params['named']['direction'] : '';

			foreach( $themes as $theme ) {

				$class = Inflector::classify( $theme );

				$qdListeDossier = $this->Commissionep->Passagecommissionep->Dossierep->{$class}->qdListeDossier();

				if( isset( $qdListeDossier['fields'] ) ) {
					$qd['fields'] = $qdListeDossier['fields'];
				}
				$qd['conditions'] = array( 'Passagecommissionep.commissionep_id' => $commissionep_id, 'Dossierep.themeep' => Inflector::tableize( $class ) );
				$qd['joins'] = $qdListeDossier['joins'];
				$qd['contain'] = false;

				$qd['fields'][] = $this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->sqVirtualField( 'enerreur' );

				$qd['limit'] = 50;

				$qd['order'] = array(
					'Personne.nom' => 'asc',
					'Personne.prenom' => 'asc',
					'Dossierep.id' => 'asc'
				);

				// Ajout des tris sur les colonnes des dossiers affectés à une EP
				if(array_search(substr($sort, 0, strpos($sort, '.')), array_column($qd['joins'], 'alias')) === false) {
					unset($this->request->params['named']['sort']);
				} else {
					$this->request->params['named']['sort'] = $sort;
				}

				$this->paginate = $qd;
				$dossiers[$theme] = $this->paginate( $this->Commissionep->Passagecommissionep->Dossierep);
				$this->refreshPaginator();

				$this->request->params['paging']['Dossierep']['order'] = [$sort => $direction];
				$this->request->params['paging']['Dossierep']['options']['order'] = [$sort => $direction];

				$countDossiers += count( $dossiers[$theme] );
			}

			
			$querydata = $this->Commissionep->WebrsaCommissionep->qdSynthese( $commissionep_id );
			if(array_search(substr($sort, 0, strpos($sort, '.')), array_column($querydata['joins'], 'alias')) === false) {
				unset($this->request->params['named']['sort']);
			} else {
				$this->request->params['named']['sort'] = $sort;
			}
			$querydata = $this->Components->load( 'Search.SearchPaginator' )->setPaginationOrder( $querydata );

			$dossierseps = $this->Commissionep->Passagecommissionep->find( 'all', $querydata );


			$this->set( compact( 'dossierseps' ) );
			$this->set( compact( 'dossiers' ) );
			$this->set( compact( 'countDossiers' ) );

			$fields = array(
				'CommissionepMembreep.id',
				'CommissionepMembreep.commissionep_id',
				'CommissionepMembreep.membreep_id',
				'CommissionepMembreep.reponse',
				'CommissionepMembreep.presence',
				'CommissionepMembreep.reponsesuppleant_id',
				'CommissionepMembreep.presencesuppleant_id',
				'Membreep.id',
				'Membreep.qual',
				'Membreep.nom',
				'Membreep.prenom',
				'Membreep.tel',
				'Membreep.mail',
				'Membreep.organisme',
				'Membreep.fonctionmembreep_id',
				'Fonctionmembreep.name'
			);

			// Doit-on aller chercher les membres pour cette commission via eps_membreseps ou commissionseps_membreseps ?
			$isMembreepInCommissionepMembreep = false;

			$count = $this->Commissionep->CommissionepMembreep->find( 'count', array( 'contain' => false, 'conditions' => array( 'CommissionepMembreep.commissionep_id' => $commissionep_id ) ) );
			$isMembreepInCommissionepMembreep = ( $count != 0 );

			if( $isMembreepInCommissionepMembreep ) {
				$membresepsseanceseps = $this->Commissionep->find(
					'all',
					array(
						'fields' => $fields,
						'conditions' => array(
							'Commissionep.id' => $commissionep_id
						),
						'joins' => array(
							array(
								'alias' => 'Ep',
								'table' => 'eps',
								'type' => 'INNER',
								'conditions' => array(
									'Commissionep.ep_id = Ep.id'
								)
							),
							array(
								'alias' => 'CommissionepMembreep',
								'table' => 'commissionseps_membreseps',
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'CommissionepMembreep.commissionep_id = Commissionep.id'
								)
							),
							array(
								'alias' => 'Membreep',
								'table' => 'membreseps',
								'type' => 'INNER',
								'conditions' => array(
									'CommissionepMembreep.membreep_id = Membreep.id'
								)
							),
							array(
								'alias' => 'Fonctionmembreep',
								'table' => 'fonctionsmembreseps',
								'type' => 'INNER',
								'conditions' => array(
									'Membreep.fonctionmembreep_id = Fonctionmembreep.id'
								)
							),
						),
						'contain' => false
					)
				);
			}
			else {
				$membresepsseanceseps = $this->Commissionep->find(
					'all',
					array(
						'fields' => $fields,
						'conditions' => array(
							'Commissionep.id' => $commissionep_id
						),
						'joins' => array(
							array(
								'alias' => 'Ep',
								'table' => 'eps',
								'type' => 'INNER',
								'conditions' => array(
									'Commissionep.ep_id = Ep.id'
								)
							),
							array(
								'alias' => 'EpMembreep',
								'table' => 'eps_membreseps',
								'type' => 'INNER',
								'conditions' => array(
									'EpMembreep.ep_id = Ep.id'
								)
							),
							array(
								'alias' => 'Membreep',
								'table' => 'membreseps',
								'type' => 'INNER',
								'conditions' => array(
									'Membreep.id = EpMembreep.membreep_id'
								)
							),
							array(
								'alias' => 'Fonctionmembreep',
								'table' => 'fonctionsmembreseps',
								'type' => 'INNER',
								'conditions' => array(
									'Membreep.fonctionmembreep_id = Fonctionmembreep.id'
								)
							),
							array(
								'alias' => 'CommissionepMembreep',
								'table' => 'commissionseps_membreseps',
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'CommissionepMembreep.commissionep_id = Commissionep.id',
									'CommissionepMembreep.membreep_id = Membreep.id'
								)
							)
						),
						'contain' => false
					)
				);
			}

			$this->set( 'membresepsseanceseps', $membresepsseanceseps );

			$membreseps = $this->Commissionep->CommissionepMembreep->Membreep->find(
				'all',
				array(
					'fields' => array(
						'Membreep.id',
						'Membreep.qual',
						'Membreep.nom',
						'Membreep.prenom',
						'Membreep.fonctionmembreep_id'
					),
					'conditions' => array(
						'Membreep.id NOT IN ( '.$this->Commissionep->CommissionepMembreep->Membreep->EpMembreep->sq(
							array(
								'fields' => array(
									'eps_membreseps.membreep_id'
								),
								'alias' => 'eps_membreseps',
								'conditions' => array(
									'eps_membreseps.ep_id' => $commissionep['Commissionep']['ep_id']
								)
							)
						).' )'
					),
					'contain' => false
				)
			);

			$listemembreseps = array( );
			foreach( $membreseps as $membreep ) {
				$listemembreseps[$membreep['Membreep']['id']] = implode( ' ', array( $membreep['Membreep']['qual'], $membreep['Membreep']['nom'], $membreep['Membreep']['prenom'] ) );
			}
			$this->set( compact( 'listemembreseps' ) );

			$this->set( 'controller', 'commissionseps' );
			$this->set( 'etatsActions', $this->etatsActions );
		}

		/**
		 * Passe une commission dont l'id est passé en paramètre en validé
		 */
		public function validecommission( $commissionep_id ) {
			$this->Commissionep->id = $commissionep_id;
			$success = $this->Commissionep->saveField( 'etatcommissionep', 'valide' );
			if( $success ) {
				$this->Flash->success( __( 'Save->success' ) );
			}
			else {
				$this->Flash->error( __( 'Save->error' ) );
			}
			$this->redirect( $this->referer() );
		}

		/**
		 *
		 */
		public function impressionpv( $commissionep_id ) {
			$this->WebrsaAccesses->check( $commissionep_id );

			$commissionep = $this->Commissionep->find(
					'first', array(
				'fields' => array(
					'Commissionep.etatcommissionep'
				),
				'conditions' => array(
					'Commissionep.id' => $commissionep_id
				)
					)
			);

			$pdf = $this->Commissionep->WebrsaCommissionep->getPdfPv( $commissionep_id, null, $this->Session->read( 'Auth.User.id' ) );

			if( $pdf ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, 'pv.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer le PV de la commission d\'EP' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Impression du PV en cohorte pour l'ensemble des participants
		 */
		public function impressionpvcohorte( $commissionep_id ) {
			$commissionep = $this->Commissionep->find(
				'first',
				array(
					'fields' => array(
						'Commissionep.etatcommissionep'
					),
					'conditions' => array(
						'Commissionep.id' => $commissionep_id
					),
					'contain' => array(
						'Membreep' => array(
							'fields' => array(
								'Membreep.id'
							)
						)
					)
				)
			);

			$pdfs = array();
			foreach( Set::extract( '/Membreep/id', $commissionep ) as $participant_id ) {
				$pdfs[] = $this->Commissionep->WebrsaCommissionep->getPdfPv( $commissionep_id, $participant_id, $this->Session->read( 'Auth.User.id' ) );
			}
			$pdf = $this->Gedooo->concatPdfs( $pdfs, 'PV' );

			if( $pdf ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, 'pv.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer le PV de la commission d\'EP' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 *
		 */
		public function ordredujour( $commissionep_id ) {
			$reponsesNonIndiquees = $this->Commissionep->CommissionepMembreep->find(
					'count', array(
				'conditions' => array(
					'CommissionepMembreep.commissionep_id' => $commissionep_id,
					'CommissionepMembreep.reponse' => 'nonrenseigne'
				)
					)
			);

			$nombreDossierseps = $this->Commissionep->Passagecommissionep->find(
					'count', array(
				'contain' => array(
					'Dossierep'
				),
				'conditions' => array(
					'Passagecommissionep.commissionep_id' => $commissionep_id
				)
					)
			);

			if( ( $reponsesNonIndiquees > 0 ) || ( $nombreDossierseps == 0 ) ) {
				if( $reponsesNonIndiquees > 0 ) {
					$this->Flash->error( 'Impossible d\'imprimer l\'ordre du jour avant d\'avoir indiqué la réponse des participants.' );
				}
				if( $nombreDossierseps == 0 ) {
					$this->Flash->error( 'Impossible d\'imprimer l\'ordre du jour avant d\'avoir attribué des dossiers.' );
				}
				$this->redirect( $this->referer() );
			}

			$pdf = $this->Commissionep->getPdfOrdreDuJour( $commissionep_id, $this->Session->read( 'Auth.User.id' ) );

			if( $pdf ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, 'OJ.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer l\'ordre du jour de la commission d\'EP' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Génération du document d'invitation à une  EP au participant.
		 * Courrier contenant le lieu, date et heure de la commission EP
		 *
		 * @param type $commissionep_id
		 * @param type $membreep_id
		 * @return void
		 */
		public function printConvocationParticipant( $commissionep_id, $membreep_id ) {
			$membreep = $this->Commissionep->CommissionepMembreep->find(
					'first', array(
				'conditions' => array(
					'CommissionepMembreep.commissionep_id' => $commissionep_id,
					'CommissionepMembreep.membreep_id' => $membreep_id
				),
				'contain' => false
					)
			);
			if( $membreep['CommissionepMembreep']['reponse'] == 'remplacepar' ) {
				$membreep_id = $membreep['CommissionepMembreep']['reponsesuppleant_id'];
			}

			$pdf = $this->Commissionep->WebrsaCommissionep->getPdfConvocationParticipant( $commissionep_id, $membreep_id, $this->Session->read( 'Auth.User.id' ) );

			if( $pdf ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, 'ConvocationEPParticipant.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer le courrier d\'information' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Génération des documents d'invitation à une commission d'EP pour les participants.
		 * Courrier contenant le lieu, date et heure de la commission EP
		 *
		 * @param integer $ep_id
		 * @param integer $commissionep_id
		 * @return void
		 */
		public function printConvocationsParticipants( $ep_id, $commissionep_id ) {
			$idsMembresEffectifs = $this->Commissionep->CommissionepMembreep->idsMembresPrevus( $commissionep_id );

			foreach( $idsMembresEffectifs as $membreep_id ) {
				if( !empty( $membreep_id ) ) {
					$pdfs[] = $this->Commissionep->WebrsaCommissionep->getPdfConvocationParticipant( $commissionep_id, $membreep_id, $this->Session->read( 'Auth.User.id' ) );
				}
			}

			$pdfs = $this->Gedooo->concatPdfs( $pdfs, 'ConvocationEPParticipant' );

			if( $pdfs ) {
				$this->Gedooo->sendPdfContentToClient( $pdfs, 'ConvocationEPParticipant.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer les invitations pour les participants de cette commission.' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Génération du document de convocation du passage en EP à l'allocataire.
		 * Courrier contenant le lieu, date et heure de la commission EP
		 */
		public function printConvocationBeneficiaire( $passagecommissionep_id ) {
			$pdf = $this->Commissionep->Passagecommissionep->Dossierep->getConvocationBeneficiaireEpPdf( $passagecommissionep_id );

			if( $pdf ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, 'ConvocationEPBeneficiaire.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer le courrier d\'information' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Génération de la cohorte des convocations de passage en commission d'EP aux allocataires.
		 */
		public function printConvocationsBeneficiaires( $commissionep_id ) {
			$liste = $this->Commissionep->Passagecommissionep->find(
					'list', array(
				'conditions' => array(
					'Passagecommissionep.commissionep_id' => $commissionep_id,
				),
				'recursive' => -1
					)
			);

			$pdfs = array( );
			foreach( array_keys( $liste ) as $passagecommissionep_id ) {
				$pdfs[] = $this->Commissionep->Passagecommissionep->Dossierep->getConvocationBeneficiaireEpPdf( $passagecommissionep_id );
			}

			$pdfs = $this->Gedooo->concatPdfs( $pdfs, 'Passagecommissionep' );

			if( $pdfs ) {
				$this->Gedooo->sendPdfContentToClient( $pdfs, 'ConvocationsEPsBeneficiaire.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer les convocations aux bénéficiaires pour cette commission.' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Vérification, pour une commission donnée, si on peut imprimer l'ordre du jour.
		 * Sinon, ajout d'un message d'erreur et redirection vers la page précédente.
		 */
		protected function _checkPrintOrdreDuJour( $commissionep_id ) {
			$this->assert( !empty( $commissionep_id ), 'invalidParameter' );

			// Réponses prévisionnelles de participation
			$reponsesNonIndiquees = $this->Commissionep->CommissionepMembreep->find(
					'count', array(
				'conditions' => array(
					'CommissionepMembreep.commissionep_id' => $commissionep_id,
					'CommissionepMembreep.reponse' => 'nonrenseigne'
				),
				'contain' => false
					)
			);

			// Dossiers devant passer dans cette commission
			$nombreDossierseps = $this->Commissionep->Passagecommissionep->find(
					'count', array(
				'conditions' => array(
					'Passagecommissionep.commissionep_id' => $commissionep_id
				),
				'contain' => false
					)
			);

			if( ( $reponsesNonIndiquees > 0 ) || ( $nombreDossierseps == 0 ) ) {
				if( $reponsesNonIndiquees > 0 ) {
					$this->Flash->error( 'Impossible d\'imprimer l\'ordre du jour avant d\'avoir indiqué la réponse des participants.' );
				}
				if( $nombreDossierseps == 0 ) {
					$this->Flash->error( 'Impossible d\'imprimer l\'ordre du jour avant d\'avoir attribué des dossiers.' );
				}
				$this->redirect( $this->referer() );
			}
		}

		/**
		 *   Impression des convocations pour les participants à la commission d'EP
		 */
		public function printOrdreDuJour( $commissionep_membreep_id ) {
			$commissionep_id = $this->Commissionep->CommissionepMembreep->field( 'commissionep_id', array( 'CommissionepMembreep.id' => $commissionep_membreep_id ) );

			$this->_checkPrintOrdreDuJour( $commissionep_id );

			$pdf = $this->Commissionep->WebrsaCommissionep->getPdfOrdredujour( $commissionep_membreep_id, $this->Session->read( 'Auth.User.id' ) );

			if( $pdf ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, 'ConvocationepParticipant.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer les convocations du participant à la commission d\'EP' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 *   Impression des convocations pour les participants à la commission d'EP
		 */
		public function printOrdresDuJour( $commissionep_id ) {
			$this->_checkPrintOrdreDuJour( $commissionep_id );

			$liste = $this->Commissionep->CommissionepMembreep->find(
					'list', array(
				'fields' => array(
					'CommissionepMembreep.id',
					'CommissionepMembreep.id'
				),
				'conditions' => array(
					'CommissionepMembreep.commissionep_id' => $commissionep_id,
					'CommissionepMembreep.reponse <>' => 'decline'
				),
				'recursive' => -1
					)
			);
			$pdfs = array( );
			foreach( $liste as $commissionep_membreep_id ) {
				$pdf = $this->Commissionep->WebrsaCommissionep->getPdfOrdredujour( $commissionep_membreep_id, $this->Session->read( 'Auth.User.id' ) );
				$pdfs[] = $pdf;
			}

			$pdfs = $this->Gedooo->concatPdfs( $pdfs, 'ConvocationepParticipant' );

			if( !empty( $pdfs ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdfs, 'ConvocationepParticipant.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer les convocations du participant à la commission d\'EP' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Affichage des décisions de la commission d'EP niveau EP
		 */
		public function decisionep( $commissionep_id ) {
			$this->WebrsaAccesses->check( $commissionep_id );
			$this->_decision( $commissionep_id, 'ep' );
		}

		/**
		 * Affichage des décisions de la commission d'EP niveau CG
		 */
		public function decisioncg( $commissionep_id ) {
			$this->WebrsaAccesses->check( $commissionep_id );
			$this->_decision( $commissionep_id, 'cg' );
		}

		/**
		 * Affichage des décisions de la commission d'EP
		 */
		protected function _decision( $commissionep_id, $niveauDecision ) {
			$commissionep = $this->Commissionep->find(
					'first', array(
				'conditions' => array(
					'Commissionep.id' => $commissionep_id,
				),
				'contain' => array(
					'Ep'
				)
					)
			);

			$this->assert( !empty( $commissionep ), 'error404' );

			$dossiers = $this->Commissionep->WebrsaCommissionep->dossiersParListe( $commissionep_id, $niveauDecision, $this->name.'.'.$this->action.'.order' );

			if( in_array( Configure::read( 'Cg.departement' ), array( 58, 93 ) ) ) {

				$querydata = array(
					'conditions' => array(
						'Passagecommissionep.commissionep_id' => $commissionep_id
					),
					'contain' => array(
						'Dossierep' => array(
							'Personne' => array(
								'Foyer' => array(
									'Adressefoyer' => array(
										'conditions' => array(
											'Adressefoyer.rgadr' => '01'
										),
										'Adresse'
									)
								)
							)
						)
					)
				);

				if( Configure::read( 'Cg.departement' ) == 58 ) {
					$querydata['contain']['Dossierep'] = array_merge(
							$querydata['contain']['Dossierep'], array(
						'Nonorientationproep58' => array(
							'Decisionpropononorientationprocov58' => array(
								'Passagecov58' => array(
									'Cov58'
								)
							)
						)
							)
					);
				}

				$syntheses = $this->Commissionep->Passagecommissionep->find( 'all', $querydata );


				$this->set( compact( 'syntheses' ) );
				$this->set( 'etatsActions', $this->etatsActions );
			}

			$this->set( compact( 'commissionep', 'dossiers' ) );
			$this->set( 'commissionep_id', $commissionep_id );
			$this->_setOptions();
		}

		/**
		 * Génération du PDF concernant la décision suite au passage en commission
		 * d'un dossier d'EP pour un certain niveau de décision.
		 */
		public function impressionDecision( $passagecommissionep_id ) {
			$pdf = $this->Commissionep->Passagecommissionep->Dossierep->getDecisionPdf( $passagecommissionep_id, $this->Session->read( 'Auth.User.id' ) );

			if( $pdf ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, "CourrierDecision-{$passagecommissionep_id}.pdf" );
			}
			else {
				$this->Flash->error( 'Impossible de générer le courrier de décision' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Génération des décisions pour tous les dossiers d'EP d'une commission.
		 */
		public function impressionsDecisions( $commissionep_id ) {
			$this->WebrsaAccesses->check( $commissionep_id );

			$liste = $this->Commissionep->Passagecommissionep->find(
					'list', array(
				'fields' => array(
					'Passagecommissionep.id',
					'Passagecommissionep.dossierep_id',
				),
				'conditions' => array(
					'Passagecommissionep.commissionep_id' => $commissionep_id,
				),
				'recursive' => -1
					)
			);

			$pdfs = array( );
			foreach( array_keys( $liste ) as $passagecommissionep_id ) {
				$pdfs[] = $this->Commissionep->Passagecommissionep->Dossierep->getDecisionPdf( $passagecommissionep_id, $this->Session->read( 'Auth.User.id' ) );
			}

			// INFO: pour le CG 66, on n'a pas de PDF de décision pour toutes les thématiques
			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$pdfs = Hash::filter( (array)$pdfs );
			}

			$pdfs = $this->Gedooo->concatPdfs( $pdfs, 'DecisionsEPsBeneficiaire' );

			if( $pdfs ) {
				$this->Gedooo->sendPdfContentToClient( $pdfs, 'DecisionsEPsBeneficiaire.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer les courriers de décision pour cette commission.' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Génération de la fiche de synthèse des différents dossiers d'EP
		 */
		public function fichesynthese( $commissionep_id, $dossierep_id, $anonymiser = false ) {
			$this->WebrsaAccesses->check( $commissionep_id );

			$pdf = $this->Commissionep->WebrsaCommissionep->getFicheSynthese( $commissionep_id, $dossierep_id, $anonymiser );

			if( $pdf ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, 'FicheSynthetique.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer le courrier d\'information' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Génération de la cohorte des convocations de passage en commission d'EP aux allocataires.
		 */
		public function fichessynthese( $commissionep_id, $anonymiser = false ) {
			$this->WebrsaAccesses->check( $commissionep_id );

			$liste = $this->Commissionep->Passagecommissionep->find(
					'list', array(
				'fields' => array(
					'Passagecommissionep.id',
					'Passagecommissionep.dossierep_id',
				),
				'conditions' => array(
					'Passagecommissionep.commissionep_id' => $commissionep_id,
				),
				'recursive' => -1
					)
			);

			$pdfs = array( );
			foreach( array_values( $liste ) as $dossierep_id ) {
				$pdfs[] = $this->Commissionep->WebrsaCommissionep->getFicheSynthese( $commissionep_id, $dossierep_id, $anonymiser );
			}

			$pdfs = $this->Gedooo->concatPdfs( $pdfs, 'Fichessynthese' );

			if( $pdfs ) {
				$this->Gedooo->sendPdfContentToClient( $pdfs, 'Fichessynthese.pdf' );
			}
			else {
				$this->Flash->error( 'Impossible de générer les fiches de synthèse pour cette commission.' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Export du tableau en CSV de l'écran de synthèse des commissions d'EP CG58
		 *
		 * @param integer $commissionep_id
		 */
		public function exportcsv( $commissionep_id = null ) {
			$querydata = $this->Commissionep->WebrsaCommissionep->qdSynthese( $commissionep_id );
			$querydata = $this->Components->load( 'Search.SearchPaginator' )->setPaginationOrder( $querydata );
			$dossierseps = $this->Commissionep->Passagecommissionep->find( 'all', $querydata );

			$this->_setOptions();

			$this->layout = '';
			$this->set( compact( 'dossierseps' ) );
		}

		/**
		 * Annule la validation de la commission EP
		 *
		 * @param integer $commissionep_id
		 */
		public function annulervalidation( $commissionep_id = null ) {
			$success = $this->WebrsaCommissionep->annulervalidation($commissionep_id);

			if ($success) {
				$this->Flash->success( __( 'L\'annulation de l\'arbitrage est effectuée.' ) );
				$this->redirect(
					array (
						'controller' => 'Commissionseps',
						'action' => 'traiterep',
						$commissionep_id
					)
				);
			}
			else {
				$this->Flash->error( 'Impossible de dévalider cette commission.' );
				$this->redirect( $this->referer() );
			}
		}
	}
?>