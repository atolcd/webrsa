<?php
	/**
	 * Code source de la classe MembresepsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe MembresepsController s'occupe du paramétrage et de la gestion des
	 * membres des équipes pluridisciplinaires.
	 *
	 * @package app.Controller
	 */
	class MembresepsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Membreseps';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
				)
			),
			'WebrsaParametrages'
		);

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
			'add' => 'Membreseps:edit'
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
			'editliste' => 'update',
			'editpresence' => 'update',
			'index' => 'read'
		);

		/**
		 * Liste des tables à ne pas prendre en compte dans les enregistrements
		 * vérifiés pour éviter les suppressions en cascade intempestives.
		 *
		 * @var array
		 */
		public $blacklist = array( 'commissionseps_membreseps', 'eps_membreseps' );

		protected function _setOptions() {
			$options = $this->Membreep->enums();
			$options['Membreep']['fonctionmembreep_id'] = $this->Membreep->Fonctionmembreep->find( 'list' );
			$options['Membreep']['ep_id'] = $this->Membreep->Ep->find( 'list' );

			$enums = $this->Membreep->CommissionepMembreep->enums();
			$options['CommissionepMembreep'] = $enums['CommissionepMembreep'];

			$this->set( compact( 'options' ) );
		}

		/**
		 * Liste des membres des équipes pluridisciplinaires.
		 */
		public function index() {
			if( false === empty( $this->request->data  ) ) {
				if( false === $this->Membreep->Behaviors->attached( 'Occurences' ) ) {
					$this->Membreep->Behaviors->attach( 'Occurences' );
				}

				$query = $this->Membreep->search( $this->request->data );

				$query['fields'][] = 'Membreep.nomcomplet';
				$query['fields'][] = 'Membreep.adresse';
				$query['fields'][] = $this->Membreep->sqHasLinkedRecords( true, $this->blacklist );
				$query['limit'] = 100;
				$query['maxLimit'] = 101;

				$this->paginate = $query;
				$results = $this->paginate( 'Membreep', array(), array(), true );

				$this->set( compact( 'results' ) );
			}

			$options = $this->Membreep->enums();
			$options['Membreep']['fonctionmembreep_id'] = $this->Membreep->Fonctionmembreep->find( 'list' );

			$messages = array();
			if ( 0 === $this->Membreep->Fonctionmembreep->find( 'count' ) ) {
				$messages['Merci d\'ajouter au moins une fonction pour les membres avant d\'ajouter un membre.'] = 'error';
			}
			$this->set( compact( 'options', 'messages' ) );
		}

		/**
		 * Formulaire de modification d'un membre des équipes pluridisciplinaires.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			$options['Membreep']['fonctionmembreep_id'] = $this->Membreep->Fonctionmembreep->find( 'list' );
			$this->set( compact( 'options' ) );
		}

		/**
		* Dresse la liste de tous les membres de l'EP pour enregistrer ceux, parmis-eux, qui participeront à la séance.
		* @param integer $ep_id Index de l'EP dont on veut récupérer tous les membres.
		*/
		public function editliste( $commissionep_id ) {
			$commissionep = $this->Membreep->CommissionepMembreep->Commissionep->find(
				'first',
				array(
					'conditions' => array(
						'Commissionep.id' => $commissionep_id
					),
					'contain' => false
				)
			);
			$ep_id = $commissionep['Commissionep']['ep_id'];

			$membres = $this->Membreep->find(
				'all',
				array(
					'fields' => array(
						'Membreep.id',
						'Membreep.qual',
						'Membreep.nom',
						'Membreep.prenom',
						'Membreep.tel',
						'Membreep.mail',
						'Membreep.fonctionmembreep_id',
						'CommissionepMembreep.reponse',
						// Ajout de l'affichage des fonctions des remplaçants
						'CommissionepMembreep.fonctionreponsesuppleant_id',
						'CommissionepMembreep.reponsesuppleant_id'
					),
					'joins' => array(
						array(
							'table' => 'commissionseps_membreseps',
							'alias' => 'CommissionepMembreep',
							'type' => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array(
								'Membreep.id = CommissionepMembreep.membreep_id',
								'CommissionepMembreep.commissionep_id' => $commissionep_id
							)
						),
						array(
							'table' => 'commissionseps',
							'alias' => 'Commissionep',
							'type' => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array(
								'Commissionep.id = CommissionepMembreep.commissionep_id'
							)
						),
						array(
							'table' => 'eps_membreseps',
							'alias' => 'EpMembreep',
							'type' => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Membreep.id = EpMembreep.membreep_id',
								'EpMembreep.ep_id' => $ep_id
							)
						),
						array(
							'table' => 'eps',
							'alias' => 'Ep',
							'type' => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Ep.id = EpMembreep.ep_id'
							)
						)
					),
					'contain'=>false
				)
			);
			$this->set('membres', $membres);

			if( !empty( $this->request->data ) ) {
				$success = true;
				if ( !$this->Membreep->CommissionepMembreep->checkDoublon( $this->request->data['CommissionepMembreep'] ) ) {
					$this->Membreep->CommissionepMembreep->begin();
					$reponsesMembres = array();
					foreach( $this->request->data['CommissionepMembreep'] as $membreep_id => $reponse ) {
						$existeEnBase = $this->Membreep->CommissionepMembreep->find(
							'first',
							array(
								'conditions'=>array(
									'CommissionepMembreep.membreep_id'=>$membreep_id,
									'CommissionepMembreep.commissionep_id'=>$commissionep_id
								),
								'contain' => false
							)
						);

						if (!empty($existeEnBase)) {
							$existeEnBase['CommissionepMembreep']['reponse'] = $reponse['reponse'];
							$existeEnBase['CommissionepMembreep']['reponsesuppleant_id'] = null;
							if ( $reponse['reponse'] == 'remplacepar' ) {
								if ( isset( $reponse['reponsesuppleant_id'] ) && !empty( $reponse['reponsesuppleant_id'] ) ) {
									// Ajout de l'affichage des fonctions des remplaçants
									$existeEnBase['CommissionepMembreep']['fonctionreponsesuppleant_id'] = $reponse['fonctionreponsesuppleant_id'];
									$existeEnBase['CommissionepMembreep']['reponsesuppleant_id'] = $reponse['reponsesuppleant_id'];
								}
							}
							$reponsesMembres[$membreep_id] = $existeEnBase;

						}
						else {
							$nouvelleEntree['CommissionepMembreep']['commissionep_id'] = $commissionep_id;
							$nouvelleEntree['CommissionepMembreep']['membreep_id'] = $membreep_id;
							$nouvelleEntree['CommissionepMembreep']['reponse'] = $reponse['reponse'];
							$nouvelleEntree['CommissionepMembreep']['reponsesuppleant_id'] = null;
							if ( $reponse['reponse'] == 'remplacepar' ) {
								if ( isset( $reponse['reponsesuppleant_id'] ) && !empty( $reponse['reponsesuppleant_id'] ) ) {
									// Ajout de l'affichage des fonctions des remplaçants
									$nouvelleEntree['CommissionepMembreep']['fonctionreponsesuppleant_id'] = $reponse['fonctionreponsesuppleant_id'];
									$nouvelleEntree['CommissionepMembreep']['reponsesuppleant_id'] = $reponse['reponsesuppleant_id'];
								}
							}
							$reponsesMembres[$membreep_id] = $nouvelleEntree;
						}
					}
					$success = $this->Membreep->CommissionepMembreep->saveAll( $reponsesMembres, array( 'validate' => 'first', 'atomic' => false ) ) && $success;
					$success = $this->Membreep->CommissionepMembreep->Commissionep->WebrsaCommissionep->changeEtatCreeAssocie( $commissionep_id ) && $success;
				}
				else {
					$success = false;
				}

				if ( $success ) {
					$this->Membreep->CommissionepMembreep->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('controller'=>'commissionseps', 'action'=>'view', $commissionep_id));
				}
				else {
					$this->Membreep->CommissionepMembreep->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				$this->request->data = array();
				foreach( $membres as $membre ) {
					$this->request->data['CommissionepMembreep'][$membre['Membreep']['id']]['reponse'] = $membre['CommissionepMembreep']['reponse'];
					// Ajout de l'affichage des fonctions des remplaçants
					$this->request->data['CommissionepMembreep'][$membre['Membreep']['id']]['fonctionreponsesuppleant_id'] = $membre['CommissionepMembreep']['fonctionreponsesuppleant_id'];
					$this->request->data['CommissionepMembreep'][$membre['Membreep']['id']]['reponsesuppleant_id'] = $membre['CommissionepMembreep']['fonctionreponsesuppleant_id'].'_'.$membre['CommissionepMembreep']['reponsesuppleant_id'];
				}
			}

			$fonctionsmembres = $this->Membreep->Fonctionmembreep->find(
				'all',
				array(
					'fields' => array(
						'Fonctionmembreep.id',
						'Fonctionmembreep.name'
					),
					'joins' => array(
						array(
							'table' => 'membreseps',
							'alias' => 'Membreep',
							'type' => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Fonctionmembreep.id = Membreep.fonctionmembreep_id'
							)
						),
						array(
							'table' => 'eps_membreseps',
							'alias' => 'EpMembreep',
							'type' => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Membreep.id = EpMembreep.membreep_id',
								'EpMembreep.ep_id' => $ep_id
							)
						),
						array(
							'table' => 'eps',
							'alias' => 'Ep',
							'type' => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Ep.id = EpMembreep.ep_id'
							)
						)
					),
					'contain'=>false,
					'group' => array(
						'Fonctionmembreep.id',
						'Fonctionmembreep.name'
					)
				)
			);
			$this->set('fonctionsmembres', $fonctionsmembres);

			$listemembres = $this->Membreep->find(
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
						'Membreep.id NOT IN ( '.$this->Membreep->EpMembreep->sq(
							array(
								'fields' => array(
									'eps_membreseps.membreep_id'
								),
								'alias' => 'eps_membreseps',
								'conditions' => array(
									'eps_membreseps.ep_id' => $ep_id
								)
							)
						).' )'
					),
					'contain' => false,
					'order' => array( 'Membreep.nom ASC', 'Membreep.prenom ASC' )
				)
			);

			$membres_fonction = array();
			foreach( $listemembres as $membreep ) {
				// Modification de l'affichage des remplaçants
				$membres_fonction[$membreep['Membreep']['fonctionmembreep_id'].'_'.$membreep['Membreep']['id']] = $membreep['Membreep']['qual'].' '.$membreep['Membreep']['nom'].' '.$membreep['Membreep']['prenom'];
			}
			$this->set( 'membres_fonction', $membres_fonction );
// debug($membres_fonction );
			$this->set('seance_id', $commissionep_id);
			$this->set('ep_id', $ep_id);
			$this->_setOptions();
		}

		/**
		 *
		 * @param integer $commissionep_id
		 */
		public function editpresence( $commissionep_id ) {
			if( !empty( $this->request->data ) ) {
				$success = true;
				$this->Membreep->CommissionepMembreep->begin();
				$reponsesMembres = array();
				foreach($this->request->data['CommissionepMembreep'] as $membreep_id => $reponse) {
					$existeEnBase = $this->Membreep->CommissionepMembreep->find(
						'first',
						array(
							'conditions'=>array(
								'CommissionepMembreep.membreep_id' => $membreep_id,
								'CommissionepMembreep.commissionep_id' => $commissionep_id
							),
							'contain' => false
						)
					);
					if (!empty($existeEnBase)) {
						$existeEnBase['CommissionepMembreep']['presence'] = $reponse['presence'];
						if ( $reponse['presence'] == 'remplacepar' ) {
							$existeEnBase['CommissionepMembreep']['presencesuppleant_id'] = null;
							if ( isset( $reponse['presencesuppleant_id'] ) && !empty( $reponse['presencesuppleant_id'] ) ) {
								$existeEnBase['CommissionepMembreep']['fonctionpresencesuppleant_id'] = $reponse['fonctionpresencesuppleant_id'];
								$existeEnBase['CommissionepMembreep']['presencesuppleant_id'] = $reponse['presencesuppleant_id'];
							}
						}
						$reponsesMembres[$membreep_id] = $existeEnBase;
					}
					else {
						$nouvelleEntree['CommissionepMembreep']['commissionep_id'] = $commissionep_id;
						$nouvelleEntree['CommissionepMembreep']['membreep_id'] = $membreep_id;
						$nouvelleEntree['CommissionepMembreep']['presence'] = $reponse['presence'];
						if ( $reponse['presence'] == 'remplacepar' ) {
							$existeEnBase['CommissionepMembreep']['presencesuppleant_id'] = null;
							if ( isset( $reponse['presencesuppleant_id'] ) && !empty( $reponse['presencesuppleant_id'] ) ) {
								$nouvelleEntree['CommissionepMembreep']['fonctionpresencesuppleant_id'] = $reponse['fonctionpresencesuppleant_id'];
								$nouvelleEntree['CommissionepMembreep']['presencesuppleant_id'] = $reponse['presencesuppleant_id'];
							}
						}
						$reponsesMembres[$membreep_id] = $nouvelleEntree;
					}
				}

				$success = $this->Membreep->CommissionepMembreep->saveAll( $reponsesMembres, array( 'validate' => 'first', 'atomic' => false ) ) && $success;
				$success = $this->Membreep->CommissionepMembreep->Commissionep->WebrsaCommissionep->changeEtatAssociePresence( $commissionep_id ) && $success;

				if ($success) {
					$this->Membreep->CommissionepMembreep->commit();
					$this->Flash->success( __( 'Save->success' ) );
					if( $this->Membreep->CommissionepMembreep->Commissionep->WebrsaCommissionep->checkEtat( $commissionep_id ) != 'quorum' ) {
						$this->redirect( array( 'controller' => 'commissionseps', 'action' => 'traiterep', $commissionep_id ) );
					}
				}
				else {
					$this->Membreep->CommissionepMembreep->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$commissionep = $this->Membreep->CommissionepMembreep->Commissionep->find(
				'first', array(
					'conditions' => array( 'Commissionep.id' => $commissionep_id ),
					'contain' => array(
						'Ep' => array( 'Regroupementep'),
						'CommissionepMembreep'
					)
				)
			);
			if ( Configure::read( 'Cg.departement' ) == 66 ) {
				$listeMembrePresentRemplace = array();
				foreach( $commissionep['CommissionepMembreep'] as $membre ) {
					if ( $membre['presence'] == 'present' || $membre['presence'] == 'remplacepar' ) {
						$listeMembrePresentRemplace[] = $membre['membreep_id'];
					}
				}

				$compositionValide = $this->Membreep->CommissionepMembreep->Commissionep->Ep->Regroupementep->Compositionregroupementep->compositionValide( $commissionep['Ep']['regroupementep_id'], $listeMembrePresentRemplace );
				if( !$compositionValide['check'] && isset( $compositionValide['error'] ) && !empty( $compositionValide['error'] ) ) {
					$message = null;
					if ( $compositionValide['error'] == 'obligatoire' ) {
						$message = "Pour une commission de ce regroupement, il faut au moins un membre occupant la fonction : ".implode( ' ou ', $this->Membreep->CommissionepMembreep->Commissionep->Ep->Regroupementep->Compositionregroupementep->listeFonctionsObligatoires( $commissionep['Ep']['regroupementep_id'] ) ).".";
					}
					elseif ( $compositionValide['error'] == 'nbminmembre' ) {
						$message = "Il n'y a pas assez de membres prioritaires qui ont accepté de venir ou qui se font remplacer pour que la commission puisse avoir lieu.";
					}
					elseif ( $compositionValide['error'] == 'nbmaxmembre' ) {
						$message = "Il y a trop de membres qui ont accepté de venir ou qui se font remplacer pour que la commission puisse avoir lieu.";
					}
					$this->set( 'messageQuorum', $message );
				}
			}
			$this->set( compact( 'commissionep' ) );
			$ep_id = $commissionep['Commissionep']['ep_id'];

			$membres = $this->Membreep->Commissionep->CommissionepMembreep->find(
				'all',
				array(
					'fields' => array_merge(
						$this->Membreep->Commissionep->CommissionepMembreep->fields(),
						$this->Membreep->Commissionep->CommissionepMembreep->Membreep->fields()
					),
					'joins' => array(
						$this->Membreep->Commissionep->CommissionepMembreep->join( 'Membreep' )

					),
					'conditions' => array(
						'CommissionepMembreep.commissionep_id' => $commissionep_id
					),
					'contain'=>false
				)
			);

			// Ajout de la fonction des membres pour les suppléants
			foreach( $membres as $membre ) {
				$this->request->data['CommissionepMembreep'][$membre['Membreep']['id']]['reponse'] = $membre['CommissionepMembreep']['reponse'];
				$this->request->data['CommissionepMembreep'][$membre['Membreep']['id']]['fonctionpresencesuppleant_id'] = $membre['CommissionepMembreep']['fonctionreponsesuppleant_id'];
				$this->request->data['CommissionepMembreep'][$membre['Membreep']['id']]['presencesuppleant_id'] = $membre['CommissionepMembreep']['fonctionreponsesuppleant_id'].'_'.$membre['CommissionepMembreep']['reponsesuppleant_id'];
			}
			$this->set( 'membres', $membres );

			$fonctionsmembres = $this->Membreep->Fonctionmembreep->find(
				'all',
				array(
					'fields' => array(
						'Fonctionmembreep.id',
						'Fonctionmembreep.name'
					),
					'joins' => array(
						array(
							'table' => 'membreseps',
							'alias' => 'Membreep',
							'type' => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Fonctionmembreep.id = Membreep.fonctionmembreep_id'
							)
						),
						$this->Membreep->join( 'CommissionepMembreep', array( 'type' => 'INNER' ) ),
						$this->Membreep->CommissionepMembreep->join( 'Commissionep', array( 'type' => 'INNER' ) ),
						$this->Membreep->CommissionepMembreep->Commissionep->join( 'Ep', array( 'type' => 'INNER' ) ),
					),
					'contain'=>false,
					'group' => array(
						'Fonctionmembreep.id',
						'Fonctionmembreep.name'
					)
				)
			);
			$this->set('fonctionsmembres', $fonctionsmembres);

			$listemembres = $this->Membreep->find(
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
						'Membreep.id NOT IN ( '.$this->Membreep->EpMembreep->sq(
							array(
								'fields' => array(
									'eps_membreseps.membreep_id'
								),
								'alias' => 'eps_membreseps',
								'conditions' => array(
									'eps_membreseps.ep_id' => $ep_id
								)
							)
						).' )'
					),
					'contain' => false
				)
			);

			$membres_fonction = array();
			foreach( $listemembres as $membreep ) {
				// Modification de l'affichage des suppléants
				$membres_fonction[$membreep['Membreep']['fonctionmembreep_id'].'_'.$membreep['Membreep']['id']] = $membreep['Membreep']['qual'].' '.$membreep['Membreep']['nom'].' '.$membreep['Membreep']['prenom'];
			}
			$this->set( 'membres_fonction', $membres_fonction );

			$this->set('seance_id', $commissionep_id);
			$this->set('ep_id', $ep_id);
			$this->_setOptions();
		}
	}
?>