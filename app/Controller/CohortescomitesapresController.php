<?php
	/**
	 * Code source de la classe CohortescomitesapresController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe CohortescomitesapresController permet la gestion de comités
	 * d'examen APRE en cohorte (CG 93).
	 *
	 * @package app.Controller
	 */
	class CohortescomitesapresController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cohortescomitesapres';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Gedooo.Gedooo',
			'Search.SearchPrg' => array(
				'actions' => array(
					'aviscomite',
					'notificationscomite',
				),
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Locale',
			'Xform',
			'Xhtml',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cohortecomiteapre',
			'Comiteapre',
			'Option',
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
			'aviscomite' => 'read',
			'editdecision' => 'update',
			'exportcsv' => 'read',
			'impression' => 'update',
			'notificationscomite' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			$this->set( 'referent', $this->Comiteapre->Apre->Personne->Referent->find( 'list' ) );
			$options = array(
				'decisioncomite' => array(
					'ACC' => __d( 'apre', 'ENUM::DECISIONCOMITE::ACC' ),
					'AJ' => __d( 'apre', 'ENUM::DECISIONCOMITE::AJ' ),
					'REF' => __d( 'apre', 'ENUM::DECISIONCOMITE::REF' ),
				)
			);
			$this->set( 'options', $options );
		}

		/**
		 * Prise de décision du comité.
		 */
		public function aviscomite() {
			$this->_index( 'Cohortecomiteapre::aviscomite' );
		}

		/**
		 * Visualisation des décisions prises par les comités.
		 */
		public function notificationscomite() {
			$this->_index( 'Cohortecomiteapre::notificationscomite' );
		}

		/**
		 *
		 * @param string $avisComite
		 */
		protected function _index( $avisComite = null ) {
			$this->set( 'comitesapre', $this->Comiteapre->find( 'list' ) );
			$this->Comiteapre->Apre->deepAfterFind = true;

			$isRapport = ( Set::classicExtract( $this->request->params, 'named.rapport' ) == 1 );
			$idRapport = Set::classicExtract( $this->request->params, 'named.Cohortecomiteapre__id' );
			$idComite = Set::classicExtract( $this->request->data, 'Cohortecomiteapre.id' );

			$this->Comiteapre->begin(); // Pour les jetons
			if( !empty( $this->request->data ) ) {
				// Sauvegarde
				if( !empty( $this->request->data['ApreComiteapre'] ) ) {
					$data = Set::extract( $this->request->data, '/ApreComiteapre' );
					$dataApre = Set::combine( $this->request->data, 'ApreComiteapre.{n}.apre_id', 'ApreComiteapre.{n}.montantattribue' );

					// On oblige le comité à prendre une décision
					$this->Comiteapre->ApreComiteapre->validate['decisioncomite'][] = array(
						'rule' => array( NOT_BLANK_RULE_NAME ),
						'required' => true,
						'message' => 'Champ obligatoire',
					);

					$return = $this->Comiteapre->ApreComiteapre->saveAll( $data, array( 'validate' => 'only', 'atomic' => false ) );

					if( $return ) {
						$return = $this->Comiteapre->ApreComiteapre->saveAll( $data, array( 'validate' => 'first', 'atomic' => false ) );

						$saved = $return;
						$this->Comiteapre->Apre->deepAfterFind = false;
						foreach( $dataApre as $apre_id => $montantattribue ) {
							$qd_apre = array(
								'conditions' => array(
									'Apre.id' => $apre_id
								),
								'fields' => null,
								'order' => null,
								'recursive' => -1
							);
							$apre = $this->Comiteapre->Apre->find( 'first', $qd_apre );

							$apre['Apre']['montantaverser'] = (!empty( $montantattribue ) ? $montantattribue : 0 );
							$this->Comiteapre->Apre->create( $apre );
							$saved = $this->Comiteapre->Apre->save( $apre , array( 'atomic' => false ) ) && $saved;
						}

						if( $saved ) {
							$this->Comiteapre->ApreComiteapre->commit();
							if( !$isRapport ) {
								$this->Flash->success( __( 'Save->success' ) );
								$this->redirect( array( 'controller' => 'comitesapres', 'action' => 'rapport', $idComite ) );
							}
							else if( $isRapport ) {
								$this->Flash->success( __( 'Save->success' ) );
								$this->redirect( array( 'controller' => 'comitesapres', 'action' => 'rapport', $idRapport ) );
							}
						}
						else {
							$this->Comiteapre->ApreComiteapre->rollback();
						}
					}
				}

				$comitesapres = $this->Cohortecomiteapre->search( $avisComite, $this->request->data );

				$comitesapres['limit'] = Configure::read( 'ResultatsParPage.nombre_par_defaut' );
				if (isset ($this->request->data['Search']['limit'])) {
					$comitesapres['limit'] = $this->request->data['Search']['limit'];
				}
				$this->paginate = $comitesapres;
				$comitesapres = $this->paginate( 'Comiteapre' );

				$this->set( 'comitesapres', $comitesapres );
			}
			$this->_setOptions();
			switch( $avisComite ) {
				case 'Cohortecomiteapre::aviscomite':
					$this->set( 'pageTitle', 'Décisions des comités' );
					$this->render( 'formulaire' );
					break;
				case 'Cohortecomiteapre::notificationscomite':
					$this->set( 'pageTitle', 'Notifications décisions comités' );
					$this->render( 'visualisation' );
					break;
			}

			$this->Comiteapre->commit();
		}

		/**
		 *
		 */
		public function exportcsv() {
			$querydata = $this->Cohortecomiteapre->search( null, Hash::expand( $this->request->params['named'], '__' ) );
			unset( $querydata['limit'] );
			$decisionscomites = $this->Comiteapre->find( 'all', $querydata );
			$this->_setOptions();
			$this->layout = '';
			$this->set( compact( 'decisionscomites' ) );
		}

		/**
		 * Modifications du Comité d'examen
		 *
		 * @param integer $apre_id
		 */
		public function editdecision( $apre_id = null ) {
			$this->Comiteapre->ApreComiteapre->Apre->deepAfterFind = false;
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'comitesapres', 'action' => 'rapport', Set::classicExtract( $this->request->data, 'ApreComiteapre.comiteapre_id' ) ) );
			}

			// TODO: error404/error500 si on ne trouve pas les données
			$qual = $this->Option->qual();
			$qd_aprecomiteapre = array(
				'conditions' => array(
					'ApreComiteapre.apre_id' => $apre_id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$aprecomiteapre = $this->Comiteapre->ApreComiteapre->find( 'first', $qd_aprecomiteapre );
			$this->set( compact( 'aprecomiteapre' ) );

			$qd_comiteapre = array(
				'conditions' => array(
					'Comiteapre.id' => Set::classicExtract( $aprecomiteapre, 'ApreComiteapre.comiteapre_id' )
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$comiteapre = $this->Comiteapre->find( 'first', $qd_comiteapre );
			$this->set( compact( 'comiteapre' ) );

			$apre = $this->Comiteapre->Apre->find(
					'first', array(
				'conditions' => array(
					'Apre.id' => $apre_id
				)
					)
			);

			unset( $apre['Apre']['Piecemanquante'] );
			unset( $apre['Apre']['Piecepresente'] );
			unset( $apre['Apre']['Piece'] );
			unset( $apre['Apre']['Natureaide'] );
			unset( $apre['Pieceapre'] );
			unset( $apre['Montantconsomme'] );
			foreach( $this->Comiteapre->Apre->WebrsaApre->aidesApre as $model ) {
				unset( $apre[$model] );
			}
			unset( $apre['Relanceapre'] );

			// Foyer
			$qd_foyer = array(
				'conditions' => array(
					'Foyer.id' => $apre['Personne']['foyer_id']
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$foyer = $this->Comiteapre->Apre->Personne->Foyer->find( 'first', $qd_foyer );

			$apre['Foyer'] = $foyer['Foyer'];

			// Dossier
			$qd_dossier = array(
				'conditions' => array(
					'Dossier.id' => $foyer['Foyer']['dossier_id']
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$dossier = $this->Comiteapre->Apre->Personne->Foyer->Dossier->find( 'first', $qd_dossier );
			$apre['Dossier'] = $dossier['Dossier'];

			// Adresse
			$qd_adresse = array(
				'conditions' => array(
					'Adresse.id' => $foyer['Foyer']['id']
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$adresse = $this->Comiteapre->Apre->Personne->Foyer->Adressefoyer->Adresse->find( 'first', $qd_adresse );
			$apre['Adresse'] = $adresse['Adresse'];

			$this->Comiteapre->begin(); // Pour les jetons
			if( !empty( $this->request->data ) ) {

				$data = Set::extract( $this->request->data, '/ApreComiteapre' );

				if( $this->Comiteapre->ApreComiteapre->saveAll( $this->request->data, array( 'validate' => 'only', 'atomic' => false ) ) ) {
					$saved = $this->Comiteapre->ApreComiteapre->saveAll( $this->request->data, array( 'validate' => 'first', 'atomic' => false ) );
					if( $saved && empty( $this->Comiteapre->Apre->ApreComiteapre->validationErrors ) ) {
						$this->Comiteapre->ApreComiteapre->commit();
						$this->redirect( array( 'controller' => 'comitesapres', 'action' => 'rapport', Set::classicExtract( $this->request->data, 'ApreComiteapre.comiteapre_id' ) ) );
					}
					else {
						$this->Comiteapre->ApreComiteapre->rollback();
					}
				}
			}
			else {
				$this->request->data = $apre;
			}
			$this->_setOptions();
			$this->Comiteapre->commit(); // Pour les jetons
			$this->set( 'apre', $apre );
		}

		/**
		 * Génère l'impression de la décision d'un passage en comité d'examen APRE.
		 * On prend la décision de ne pas le stocker.
		 *
		 * @param integer $apre_comiteapre_id L'id de l'entrée de décision d'une APRE en comité APRE
		 * @return void
		 */
		public function impression( $apre_comiteapre_id = null ) {
			$dest = Set::classicExtract( $this->request->params, 'named.dest' );

			$pdf = $this->Comiteapre->ApreComiteapre->getNotificationPdf(
					$apre_comiteapre_id, $dest, $this->Session->read( 'Auth.User.id' )
			);

			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'decision_comite_apre_%d-%s-%s.pdf', $apre_comiteapre_id, $dest, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Flash->error( 'Impossible de générer l\'impression de la décision du comité APRE.' );
				$this->redirect( $this->referer() );
			}
		}

	}
?>