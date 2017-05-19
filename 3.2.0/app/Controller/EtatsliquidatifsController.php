<?php
	/**
	 * Code source de la classe EtatsliquidatifsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe EtatsliquidatifsController ...
	 *
	 * @package app.Controller
	 */
	class EtatsliquidatifsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Etatsliquidatifs';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Gedooo.Gedooo',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Apreversement',
			'Cake1xLegacy.Ajax',
			'Locale',
			'Paginator',
			'Theme',
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Etatliquidatif',
			'Option',
			'Parametrefinancier',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Etatsliquidatifs:edit',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajaxmontant',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajaxmontant' => 'read',
			'edit' => 'update',
			'hopeyra' => 'read',
			'impression' => 'update',
			'impressions' => 'update',
			'index' => 'read',
			'pdf' => 'read',
			'selectionapres' => 'read',
			'validation' => 'update',
			'versementapres' => 'read',
			'visualisationapres' => 'read',
		);

		/**
		 *
		 */
		public function beforeFilter() {
			ini_set( 'max_execution_time', 0 );
			ini_set( 'memory_limit', '1024M' );
			ini_set( 'default_socket_timeout', 3660 );
			parent::beforeFilter();
		}

		/**
		 *
		 */
		public function index() {
			$conditions = array( );

			$budgetapre_id = Set::classicExtract( $this->request->params, 'named.budgetapre_id' );
			if( !empty( $budgetapre_id ) ) {
				$conditions["Etatliquidatif.budgetapre_id"] = $budgetapre_id;
			}

			$this->paginate = array(
				$this->modelClass => array(
					'limit' => 10,
					'conditions' => $conditions,
					'contain' => array(
						'Budgetapre'
					),
					'order' => array(
						'Etatliquidatif.datecloture DESC',
						'Etatliquidatif.id DESC'
					)
				)
			);

			$etatsliquidatifs = $this->paginate( $this->modelClass );
// debug($etatsliquidatifs);
			if( !empty( $etatsliquidatifs ) ) {
				$apres_etatsliquidatifs = $this->Etatliquidatif->ApreEtatliquidatif->find(
					'all',
					array(
						'conditions' => array(
							'ApreEtatliquidatif.etatliquidatif_id' => Set::extract( $etatsliquidatifs, '/Etatliquidatif/id' )
						),
						'recursive' => -1
					)
				);
				$this->set( 'apres_etatsliquidatifs', $apres_etatsliquidatifs );
			}

			$this->set( compact( 'etatsliquidatifs' ) );
		}

		/**
		 *
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 */
		protected function _add_edit( $id = null ) {
			$parametrefinancier = $this->Parametrefinancier->find( 'first' );
			if( empty( $parametrefinancier ) ) {
				$this->Flash->error( __( 'Impossible de créer ou de modifier un état liquidatif si les paramètres financiers ne sont pas enregistrés.' ) );
				$this->redirect( array( 'action' => 'index', max( 1, Set::classicExtract( $this->request->params, 'named.page' ) ) ) );
			}

			$budgetsapres = $this->Etatliquidatif->Budgetapre->find( 'list' );
			if( empty( $budgetsapres ) ) {
				$this->Flash->error( __( 'Impossible de créer ou de modifier un état liquidatif s\'il n\'existe pas de budget APRE.' ) );
				$this->redirect( array( 'action' => 'index', max( 1, Set::classicExtract( $this->request->params, 'named.page' ) ) ) );
			}

			if( $this->action == 'edit' ) {
				$qd_etatliquidatif = array(
					'conditions' => array(
						'Etatliquidatif.id' => $id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$etatliquidatif = $this->Etatliquidatif->find( 'first', $qd_etatliquidatif );

				$this->assert( !empty( $etatliquidatif ), 'invalidParameter' );
			}
			else {
				// Aucun autre état liquidatif ouvert
				$nEtatsliquidatifs = $this->Etatliquidatif->find( 'count', array( 'conditions' => array( 'Etatliquidatif.datecloture IS NULL' ) ) );
				if( $nEtatsliquidatifs > 0 ) {
					$this->Flash->error( __( 'Impossible de créer un état liquidatif lorsqu\'il existe un autre état liquidatif non validé.' ) );
					$this->redirect( array( 'action' => 'index', max( 1, Set::classicExtract( $this->request->params, 'named.page' ) ) ) );
				}
			}

			if( !empty( $this->request->data ) ) {
				$parametrefinancier = $this->Parametrefinancier->find( 'first' );

				// Copie
				$etatliquidatifFields = array_keys( $this->Etatliquidatif->schema() );
				foreach( $parametrefinancier['Parametrefinancier'] as $field => $value ) {
					if( ( $field != 'id' ) && in_array( $field, $etatliquidatifFields ) ) {
						$this->request->data[$this->modelClass][$field] = $value;
					}
				}
				$this->request->data[$this->modelClass]['operation'] = ( ( $this->request->data[$this->modelClass]['typeapre'] == 'forfaitaire' ) ? $this->request->data[$this->modelClass]['apreforfait'] : $this->request->data[$this->modelClass]['aprecomplem'] );

				$this->Etatliquidatif->create( $this->request->data );
				if( $this->Etatliquidatif->save( null, array( 'atomic' => false ) ) ) {
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index', max( 1, Set::classicExtract( $this->request->params, 'named.page' ) ) ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $etatliquidatif;
			}

			$this->set( 'typesapres', array( 'forfaitaire' => 'APREs forfaitaires', 'complementaire' => 'APREs complémentaires' ) ); // TODO: enum
			$this->set( 'budgetsapres', $budgetsapres );
			$this->render( 'add_edit' );
		}

		/**
		 *
		 */
		public function selectionapres( $id = null ) {
			$qd_etatliquidatif = array(
				'conditions' => array(
					'Etatliquidatif.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$etatliquidatif = $this->Etatliquidatif->find( 'first', $qd_etatliquidatif );
			$this->assert( !empty( $etatliquidatif ), 'invalidParameter' );

			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			// État liquidatif pas encore validé
			if( !empty( $etatliquidatif['Etatliquidatif']['datecloture'] ) ) {
				$this->Flash->error( __( 'Impossible de sélectionner des APREs pour un état liquidatif validé.' ) );
				$this->redirect( array( 'action' => 'index' ) );
			}


			if( !empty( $this->request->data ) ) {
				foreach( $this->request->data['Apre']['Apre'] as $i => $value ) {
					if( empty( $value ) ) {
						unset( $this->request->data['Apre']['Apre'][$i] );
					}
				}

				if( $this->Etatliquidatif->saveAll( $this->request->data ) ) {
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index', max( 1, Set::classicExtract( $this->request->params, 'named.page' ) ) ) );
				}
			}

			$typeapre = ( ( Set::classicExtract( $etatliquidatif, 'Etatliquidatif.typeapre' ) == 'forfaitaire' ) ? 'F' : 'C' );

			$queryData = $this->Etatliquidatif->listeApresPourEtatLiquidatif( $id, array( 'Apre.statutapre' => $typeapre ) );

			$etatliquidatifLimit = Configure::read( 'Etatliquidatif.limit' );
			if( !empty( $etatliquidatifLimit ) ) {
				$queryData['limit'] = $etatliquidatifLimit;
			}

			$deepAfterFind = $this->Etatliquidatif->Apre->deepAfterFind;
			$this->Etatliquidatif->Apre->deepAfterFind = false;

			$querydata = array(
				'fields' => $queryData['fields'],
				'joins' => array(
					$this->Etatliquidatif->Apre->join( 'Personne' ),
					$this->Etatliquidatif->Apre->Personne->join( 'Foyer' ),
					$this->Etatliquidatif->Apre->Personne->Foyer->join( 'Dossier' ),
					$this->Etatliquidatif->Apre->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Etatliquidatif->Apre->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$this->Etatliquidatif->Apre->join( 'ApreComiteapre', array( 'type' => 'LEFT OUTER' ) )
				),
				'conditions' => array(
					'Adressefoyer.id IS NULL OR Adressefoyer.id IN ('
					.$this->Etatliquidatif->Apre->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' )
					.')',
					'Apre.eligibiliteapre' => 'O',
					'AND' => array(
						'(Apre.statutapre = \'F\') OR Apre.montantaverser IS NOT NULL', // FIXME: Apre.statutapre F -> pas de montantaverser ?
						'OR' => array(
							'Apre.montantdejaverse IS NULL',
							'Apre.montantaverser > Apre.montantdejaverse'
						),
					// Nb. paiements ?
					),
					'Apre.statutapre' => $typeapre,
					'OR' => array(
						'Apre.statutapre' => 'F',
						'AND' => array(
							'Apre.statutapre' => 'C',
							'ApreComiteapre.id IN ('
							.$this->Etatliquidatif->Apre->ApreComiteapre->sqDernierComiteApre()
							.')',
							'ApreComiteapre.decisioncomite' => 'ACC'
						)
					),
					array(
						'OR' => array(
							// L'APRE n'est pas dans un etatliquidatif non clôturé
							'Apre.id NOT IN ('
							.$this->Etatliquidatif->ApreEtatliquidatif->sq(
									array(
										'alias' => 'apres_etatsliquidatifs',
										'fields' => 'apres_etatsliquidatifs.apre_id',
										'joins' => array(
											array(
												'table' => '"etatsliquidatifs"', // FIXME
												'alias' => 'etatsliquidatifs',
												'type' => 'INNER',
												'conditions' => array(
													'apres_etatsliquidatifs.etatliquidatif_id = etatsliquidatifs.id'
												)
											)
										),
										'conditions' => array(
											'etatsliquidatifs.datecloture IS NOT NULL'
										),
										'contain' => false
									)
							)
							.')',
							// L'APRE doit encore recevoir des paiement
							// FIXME: à présent, on prend tant que la totalité n'a pas été payée OU
							//        tant que le montant déjà versé est inférieur au montant à verser
							// FIXME: on a une partie de ces conditions en haut, ligne 207: Apre.montantaverser > Apre.montantdejaverse
							'Apre.id IN ('
							.$this->Etatliquidatif->ApreEtatliquidatif->sq(
									array(
										'alias' => 'apres_etatsliquidatifs',
										'fields' => 'apres_etatsliquidatifs.apre_id',
										'joins' => array(
											array(
												'table' => '"etatsliquidatifs"', // FIXME
												'alias' => 'etatsliquidatifs',
												'type' => 'INNER',
												'conditions' => array(
													'apres_etatsliquidatifs.etatliquidatif_id = etatsliquidatifs.id'
												)
											)
										),
										'conditions' => array(
											'OR' => array(
												$this->Etatliquidatif->sousRequeteApreNbpaiementeff.' <> Apre.nbpaiementsouhait',
												'Apre.montantdejaverse < Apre.montantaverser'
											)
										),
										'contain' => false
									)
							)
							.')'
						)
					)
				),
				'contain' => false,
// 	'limit' => 1000
			);

			$queryData = $querydata;

			$apres = $this->Etatliquidatif->Apre->find( 'all', $queryData );
//
			$this->Etatliquidatif->Apre->deepAfterFind = $deepAfterFind;

			$apres_etatsliquidatifs = $this->Etatliquidatif->ApreEtatliquidatif->find(
					'all', array(
				'conditions' => array( 'ApreEtatliquidatif.etatliquidatif_id' => $id ),
				'recursive' => -1
					)
			);
			$this->request->data['Apre']['Apre'] = Set::extract( $apres_etatsliquidatifs, '/ApreEtatliquidatif/apre_id' );

			$this->set( compact( 'apres', 'typeapre' ) );
		}

		/**
		 *
		 */
		public function visualisationapres( $id = null ) {
			$typeapre = $this->Etatliquidatif->getTypeapre( $id );
			$this->assert( !empty( $typeapre ), 'invalidParameter' );

			$method = 'qdDonneesApre'.Inflector::camelize( $typeapre );
			$querydata = $this->Etatliquidatif->{$method}();
			$querydata = Set::merge(
							$querydata, array(
						'conditions' => array(
							'Etatliquidatif.id' => $id
						),
						'limit' => 100
							)
			);

			$this->paginate = $querydata;
			$deepAfterFind = $this->Etatliquidatif->Apre->deepAfterFind;
			$this->Etatliquidatif->Apre->deepAfterFind = false;
			$apres = $this->paginate( $this->Etatliquidatif );
			$this->Etatliquidatif->Apre->deepAfterFind = $deepAfterFind;

			$this->set( 'natureAidesApres', $this->Option->natureAidesApres() );
			$this->set( 'typeapre', ( $typeapre == 'forfaitaire' ? 'F' : 'C' ) );
			$this->set( compact( 'apres' ) );
		}

		/**
		 * Impression d'un état de liquidation pour une APRE, avec en destinataire le bénéficiaire, pour les
		 * APREs forfaitaires; le bénéficiaire ou le tiers prestataire pour les APREs complémentaires.
		 *
		 * @param integer $apre_id
		 * @param integer $etatliquidatif_id
		 * @return void
		 */
		public function impression( $apre_id, $etatliquidatif_id ) {
			$dest = Set::classicExtract( $this->request->params, 'named.dest' );

			$typeapre = $this->Etatliquidatif->getTypeapre( $etatliquidatif_id );
			$this->assert( !empty( $typeapre ), 'invalidParameter' );

			$pdf = $this->Etatliquidatif->ApreEtatliquidatif->getDefaultPdf(
					$typeapre, $apre_id, $etatliquidatif_id, $dest, $this->Session->read( 'Auth.User.id' )
			);

			if( !empty( $pdf ) ) {
				if( $typeapre == 'forfaitaire' ) {
					$nomfichier = sprintf( 'apreforfaitaire-%s.pdf', date( 'Y-m-d' ) );
				}
				else if( $typeapre == 'complementaire' && $dest == 'tiersprestataire' ) {
					$nomfichier = sprintf( 'paiement_tiersprestataire-%s.pdf', date( 'Y-m-d' ) );
				}
				else if( $typeapre == 'complementaire' && $dest == 'beneficiaire' ) {
					$nomfichier = sprintf( 'paiement_beneficiaire-%s.pdf', date( 'Y-m-d' ) );
				}

				$this->Gedooo->sendPdfContentToClient( $pdf, $nomfichier );
			}
			else {
				$this->Flash->error( 'Impossible de générer l\'impression de l\'état liquidatif de l\'APRE.' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Impression d'une page d'APREs en état de liquidation, ayant comme destinataire le bénéficiaire et
		 * ne concernant que les APREs forfaitaires.
		 *
		 * @param integer $id L'id de l'état liquidatif.
		 */
		public function impressions( $id ) {
			$typeapre = $this->Etatliquidatif->getTypeapre( $id );
			$this->assert( !empty( $typeapre ), 'invalidParameter' );

			// La page sur laquelle nous sommes
			$page = Set::classicExtract( $this->request->params, 'named.page' );
			if( ( intval( $page ) != $page ) || $page < 0 ) {
				$page = 1;
			}

			$pdf = $this->Etatliquidatif->ApreEtatliquidatif->getDefaultCohortePdf(
					$typeapre, $id, 'beneficiaire', $this->Session->read( 'Auth.User.id' ), $page, 100, Set::classicExtract( $this->request->params, 'named.sort' ), Set::classicExtract( $this->request->params, 'named.direction' )
			);

			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'apresforfaitaires-%d-%s.pdf', $id, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Flash->error( 'Impossible de générer l\'impression de la page de l\'état liquidatif des APREs.' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 *
		 */
		public function validation( $id = null ) {
			$qd_etatliquidatif = array(
				'conditions' => array(
					'Etatliquidatif.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$etatliquidatif = $this->Etatliquidatif->find( 'first', $qd_etatliquidatif );
			$this->assert( !empty( $etatliquidatif ), 'invalidParameter' );

			// État liquidatif pas encore validé
			if( !empty( $etatliquidatif['Etatliquidatif']['datecloture'] ) ) {
				$this->Flash->error( __( 'Impossible de valider un état liquidatif déjà validé.' ) );
				$this->redirect( array( 'action' => 'index' ) );
			}

			// État liquidatif sans APRE ?
			// FIXME: doit-il y avoir obligatoirement des apres dans un état liquidatif
			$nApres = $this->Etatliquidatif->ApreEtatliquidatif->find( 'count', array( 'conditions' => array( 'ApreEtatliquidatif.etatliquidatif_id' => $id ) ) );
			if( $nApres == 0 ) {
				$this->Flash->error( __( 'Impossible de valider un état liquidatif n\'étant associé à aucune APRE.' ) );
				$this->redirect( array( 'action' => 'index' ) );
			}

			// TODO -> dans le modèle
			$this->Etatliquidatif->Apre->deepAfterFind = false;
			$montanttotalapre = $this->Etatliquidatif->Apre->find(
					'all', array(
				'fields' => array(
					'Apre.mtforfait',
					'ApreEtatliquidatif.montantattribue',
				),
				'joins' => array(
					array(
						'table' => 'apres_etatsliquidatifs',
						'alias' => 'ApreEtatliquidatif',
						'type' => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Apre.id = ApreEtatliquidatif.apre_id' )
					),
				),
				'recursive' => 1,
				'conditions' => array( 'ApreEtatliquidatif.etatliquidatif_id' => $id ),
					)
			);

			$etatliquidatif['Etatliquidatif']['datecloture'] = date( 'Y-m-d' );

			if( $etatliquidatif['Etatliquidatif']['typeapre'] == 'forfaitaire' ) {
				$etatliquidatif['Etatliquidatif']['montanttotalapre'] = array_sum( Set::extract( $montanttotalapre, '/Apre/mtforfait' ) );
			}
			else if( $etatliquidatif['Etatliquidatif']['typeapre'] == 'complementaire' ) {
				$etatliquidatif['Etatliquidatif']['montanttotalapre'] = array_sum( Set::extract( $montanttotalapre, '/ApreEtatliquidatif/montantattribue' ) );
			}
			else {
				$this->cakeError( 'error500' );
			}

			$this->Etatliquidatif->create( $etatliquidatif );
			if( $this->Etatliquidatif->save( null, array( 'atomic' => false ) ) ) {
				$this->Flash->success( __( 'Save->success' ) );
				$this->redirect( array( 'action' => 'index', max( 1, Set::classicExtract( $this->request->params, 'named.page' ) ) ) );
			}
		}

		/**
		 *
		 */
		public function hopeyra( $id = null ) {
			$qd_etatliquidatif = array(
				'conditions' => array(
					'Etatliquidatif.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$etatliquidatif = $this->Etatliquidatif->find( 'first', $qd_etatliquidatif );
			$this->assert( !empty( $etatliquidatif ), 'invalidParameter' );

			// État liquidatif pas encore validé
			if( empty( $etatliquidatif['Etatliquidatif']['datecloture'] ) ) {
				$this->Flash->error( __( 'Impossible de générer le fichier HOPEYRA pour un état liquidatif pas encore validé.' ) );
				$this->redirect( array( 'action' => 'index' ) );
			}

			$apres = $this->Etatliquidatif->hopeyra( $id, $etatliquidatif['Etatliquidatif']['typeapre'] );

			$this->set( compact( 'apres' ) );

			$this->render( null, 'ajax' );
		}

		/**
		 *   PDF pour les APREs Forfaitaires
		 */
		public function pdf( $id = null ) {
			$qd_etatliquidatif = array(
				'conditions' => array(
					'Etatliquidatif.id' => $id
				),
				'fields' => null,
				'order' => null,
				'contain' => array(
					'Budgetapre'
				)
			);
			$etatliquidatif = $this->Etatliquidatif->find( 'first', $qd_etatliquidatif );
			$this->assert( !empty( $etatliquidatif ), 'invalidParameter' );

			// État liquidatif pas encore validé
			if( empty( $etatliquidatif['Etatliquidatif']['datecloture'] ) ) {
				$this->Flash->error( __( 'Impossible de générer le fichier PDF pour un état liquidatif pas encore validé.' ) );
				$this->redirect( array( 'action' => 'index' ) );
			}

			$elements = $this->Etatliquidatif->pdf( $id, $etatliquidatif['Etatliquidatif']['typeapre'], true );

			$qual = $this->Option->qual();

			$this->set( compact( 'elements', 'etatliquidatif', 'qual' ) );

			Configure::write( 'debug', 0 );
		}

		/**
		 *
		 */
		public function ajaxmontant( $etatliquidatif_id, $apre_id, $index ) { // FIXME
			Configure::write( 'debug', 0 );
			$nbpaiementsouhait = $this->request->data['Apre'][$index]['nbpaiementsouhait'];

			$queryData = $this->Etatliquidatif->listeApresEtatLiquidatifNonTermine( array( 'Apre.statutapre' => 'C', 'Apre.id' => $apre_id ), $etatliquidatif_id );
			$queryData['recursive'] = -1;

			$apre = $this->Etatliquidatif->Apre->find( 'first', $queryData );

			// Calcul -> FIXME: dans le modèle
			$montanttotal = Set::classicExtract( $apre, 'Apre.montantaverser' );
			if( $nbpaiementsouhait == 1 ) {
				$montantattribue = $montanttotal;
			}
			else if( $nbpaiementsouhait == 2 ) {
				// INFO: remplacement du pourcentage de 40 -> 60 % pour les versements en 2 fois (du coup ajout d'un paramétrage)
				$montantattribue = Configure::read( 'Apre.pourcentage.montantversement' ) * ( Set::classicExtract( $apre, 'Apre.montantaverser' ) ) / 100;
			}

			$this->set( 'json', array( 'montantattribue' => $montantattribue ) );

			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}

		/**
		 *
		 */
		public function versementapres( $id = null ) {
			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}

			$qd_etatliquidatif = array(
				'conditions' => array(
					'Etatliquidatif.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$etatliquidatif = $this->Etatliquidatif->find( 'first', $qd_etatliquidatif );
			$this->assert( !empty( $etatliquidatif ), 'invalidParameter' );

			$nbpaiementsouhait = array( '1' => 1, '2' => 2 );
			$this->set( 'nbpaiementsouhait', $nbpaiementsouhait );


			$queryData = $this->Etatliquidatif->listeApresEtatLiquidatifNonTerminePourVersement( array( 'Apre.statutapre' => 'C' ), $id );
			$queryData['limit'] = 100;

			$this->Etatliquidatif->Apre->deepAfterFind = false;
			$this->paginate = array( 'Apre' => $queryData );
			$apres = $this->paginate( 'Apre' );

			$this->set( compact( 'apres', 'queryData' ) );


			if( !empty( $this->request->data ) ) {
				$this->Etatliquidatif->ApreEtatliquidatif->begin();

				$apre_ids = Set::extract( $this->request->data, '/ApreEtatliquidatif/apre_id' );
				$apres = Set::extract( $this->request->data, '/Apre' );
				$apres_etatsliquidatifs = Set::extract( $this->request->data, '/ApreEtatliquidatif' );

				// INFO: il faut d'abord sauver les APREs pour connaître le nombre de montants désirés
				$return = $this->Etatliquidatif->Apre->saveAll( $apres, array( 'atomic' => false ) );
				$return = $this->Etatliquidatif->ApreEtatliquidatif->saveAll( $apres_etatsliquidatifs, array( 'atomic' => false ) ) && $return;
				if( $return ) {
					$this->Etatliquidatif->Apre->WebrsaApre->calculMontantsDejaVerses( $apre_ids );
					$this->Etatliquidatif->ApreEtatliquidatif->commit();
					$this->redirect( array( 'action' => 'index', max( 1, Set::classicExtract( $this->request->params, 'named.page' ) ) ) );
				}
				else {
					$this->Etatliquidatif->ApreEtatliquidatif->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
		}

	}
?>