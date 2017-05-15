<?php

	/**
	 * Code source de la classe Cohortesdossierspcgs66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('ZipUtility', 'Utility');

	/**
	 * La classe Cohortesdossierspcgs66Controller permet de traiter les dossiers PCGs en cohorte
	 * (CG 66).
	 *
	 * @package app.Controller
	 * @deprecated since version 3.0
	 */
	class Cohortesdossierspcgs66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cohortesdossierspcgs66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes' => array(
				'enattenteaffectation',
				'atransmettre',
				'aimprimer'
			),
			'Gedooo.Gedooo',
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Search.SearchPrg' => array(
				'actions' => array(
					'enattenteaffectation' => array('filter' => 'Search'),
					'affectes' => array('filter' => 'Search'),
					'aimprimer' => array('filter' => 'Search'),
					'atransmettre' => array('filter' => 'Search')
				)
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Default2',
			'Locale',
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cohortedossierpcg66',
			'Canton',
			'Dossier',
			'Dossierpcg66',
			'Option',
			'Zonegeographique',
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
			'affectes' => 'read',
			'aimprimer' => 'read',
			'atransmettre' => 'read',
			'enattenteaffectation' => 'read',
			'exportcsv' => 'read',
			'imprimer_cohorte' => 'update',
			'notificationsCohorte' => 'read',
		);

		/**
		 *
		 */
		public function _setOptions() {
			$options = $this->Dossierpcg66->enums();
			$this->set('typepdo', $this->Dossierpcg66->Typepdo->find('list'));
			$this->set('originepdo', $this->Dossierpcg66->Originepdo->find('list'));
			$this->set('serviceinstructeur', $this->Dossierpcg66->Serviceinstructeur->listOptions());
			$this->set('orgpayeur', array('CAF' => 'CAF', 'MSA' => 'MSA'));


			$this->set('qual', $this->Option->qual());
			$this->set('etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa'));

			$gestionnaires = $this->User->find(
					'all', array(
				'fields' => array(
					'User.nom_complet',
					'( "Poledossierpcg66"."id" || \'_\'|| "User"."id" ) AS "User__gestionnaire"',
				),
				'conditions' => array(
					'User.isgestionnaire' => 'O'
				),
				'joins' => array(
					$this->User->join('Poledossierpcg66', array('type' => 'INNER')),
				),
				'order' => array('User.nom ASC', 'User.prenom ASC'),
				'contain' => false
					)
			);
			$gestionnaires = Hash::combine($gestionnaires, '{n}.User.gestionnaire', '{n}.User.nom_complet');
			$this->set(compact('gestionnaires'));


			$this->set('gestionnaire', $this->User->find(
							'list', array(
						'fields' => array(
							'User.nom_complet'
						),
						'conditions' => array(
							'User.isgestionnaire' => 'O'
						),
						'order' => array('User.nom ASC', 'User.prenom ASC')
							)
					)
			);

			$this->set('polesdossierspcgs66', $this->User->Poledossierpcg66->find(
							'list', array(
						'fields' => array(
							'Poledossierpcg66.name'
						),
						'conditions' => array(
							'Poledossierpcg66.isactif' => '1'
						),
						'order' => array('Poledossierpcg66.name ASC', 'Poledossierpcg66.id ASC')
							)
					)
			);

			$etatdossierpcg = $options['Dossierpcg66']['etatdossierpcg'];
			$this->set(compact('options', 'etatdossierpcg'));
		}

		/**
		 *
		 */
		public function enattenteaffectation() {
			$this->_index('Affectationdossierpcg66::enattenteaffectation');
		}

		/**
		 *
		 */
		public function affectes() {
			$this->_index('Affectationdossierpcg66::affectes');
		}

		/**
		 *
		 */
		public function aimprimer() {
			$this->_index('Affectationdossierpcg66::aimprimer');
		}

		/**
		 *
		 */
		public function atransmettre() {
			$this->_index('Affectationdossierpcg66::atransmettre');
		}

		/**
		 *
		 */
		protected function _index($statutAffectation = null) {
			$this->assert(!empty($statutAffectation), 'invalidParameter');

			$this->set('cantons', $this->Gestionzonesgeos->listeCantons());
			$this->set('mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee());

			if (!empty($this->request->data)) {
				/**
				 *
				 * Sauvegarde
				 *
				 */
				// On a renvoyé  le formulaire de la cohorte
				if (!empty($this->request->data['Dossierpcg66'])) {
					$this->Cohortes->get(array_unique(Set::extract($this->request->data, 'Dossierpcg66.{n}.dossier_id')));

					$this->Dossierpcg66->begin();
					$success = true;

					if ($this->action == 'attentetransmission' || $this->action == 'atransmettre') {
						foreach ($this->request->data['Dossierpcg66'] as $index => $dossierpcg66) {
							if (Hash::get($dossierpcg66, 'istransmis')) {
								$notificationsdecisionsdossierspcgs66 = array(
									'Decisiondossierpcg66' => array('id' => Hash::get($this->request->data, "Decisiondossierpcg66.{$index}.id")),
									'Notificationdecisiondossierpcg66' => (array) Hash::get($this->request->data, "Notificationdecisiondossierpcg66.{$index}")
								);
	//debug($notificationsdecisionsdossierspcgs66);
								$this->Dossierpcg66->create(array('Dossierpcg66' => $dossierpcg66));
								$success = $this->Dossierpcg66->save() && $success;

								$success = $this->Dossierpcg66->Decisiondossierpcg66->saveAssociated($notificationsdecisionsdossierspcgs66, array('atomic' => false, 'validate' => 'first')) && $success;

								$success = $this->Dossierpcg66->Decisiondossierpcg66->updateAllUnbound(
									array('Decisiondossierpcg66.etatop' => "'" . Hash::get($this->request->data, "Decisiondossierpcg66.{$index}.etatop") . "'"),
									array('Decisiondossierpcg66.id' => Hash::get($this->request->data, "Decisiondossierpcg66.{$index}.id"))
								) && $success;

								if( $this->action == 'atransmettre' ) {
									$datetransmissionop = $this->request->data['Decisiondossierpcg66'][$index]['datetransmissionop'];
									$date = "{$datetransmissionop['year']}-{$datetransmissionop['month']}-{$datetransmissionop['day']}";
									if( Validation::date( $date  ) ) {
										$success = $this->Dossierpcg66->Decisiondossierpcg66->updateAllUnbound(
											array( 'Decisiondossierpcg66.datetransmissionop' => "'{$date}'" ),
											array('Decisiondossierpcg66.id' => Hash::get($this->request->data, "Decisiondossierpcg66.{$index}.id"))
										) && $success;
									}
									else {
										$success = false; // FIXME: message d'erreur
	//                                    $this->Dossierpcg66->Decisiondossierpcg66->invalidate( "{$index}.datetransmissionop", 'erreur de merde');
										$this->Dossierpcg66->Decisiondossierpcg66->validationErrors[$index]['datetransmissionop'] = __( 'Validate::date' );
									}
								}
							}
						}

						if ($success) {
							$this->Dossierpcg66->commit(); //FIXME
							$this->Session->setFlash('Enregistrement effectué.', 'flash/success');
							$this->Cohortes->release(array_unique(Hash::extract($this->request->data, 'Dossierpcg66.{n}.dossier_id')));

							unset($this->request->data['Dossierpcg66']);
							unset($this->request->data['Decisiondossierpcg66']);
							unset($this->request->data['Notificationdecisiondossierpcg66']);
						} else {
							$this->Dossierpcg66->rollback();
							$this->Session->setFlash('Erreur lors de l\'enregistrement.', 'flash/error');
						}
					} else {
						$valid = $this->Dossierpcg66->saveAll($this->request->data['Dossierpcg66'], array('validate' => 'only', 'atomic' => false));

						if ($valid) {
							$saved = $this->Dossierpcg66->saveAll($this->request->data['Dossierpcg66'], array('validate' => 'first', 'atomic' => false));

							if ($saved) {
								$this->Dossierpcg66->commit();
								$this->Session->setFlash('Enregistrement effectué.', 'flash/success');
								$this->Cohortes->release(array_unique(Set::extract($this->request->data, 'Dossierpcg66.{n}.dossier_id')));

								unset($this->request->data['Dossierpcg66']);
							} else {
								$this->Dossierpcg66->rollback();
								$this->Session->setFlash('Erreur lors de l\'enregistrement.', 'flash/error');
							}
						}
					}
				}


	//debug($this->request->data);
				/**
				 *
				 * Filtrage
				 *
				 */
				if (( $statutAffectation == 'Affectationdossierpcg66::enattenteaffectation' ) || ( $statutAffectation == 'Affectationdossierpcg66::affectes' ) || ( $statutAffectation == 'Affectationdossierpcg66::aimprimer' ) || ( $statutAffectation == 'Affectationdossierpcg66::atransmettre' ) || ( $statutAffectation == 'Affectationdossierpcg66::attentetransmission' ) && !empty($this->request->data)) {
					$paginate = $this->Cohortedossierpcg66->search(
							$statutAffectation, (array) $this->Session->read('Auth.Zonegeographique'), $this->Session->read('Auth.User.filtre_zone_geo'), $this->request->data, $this->Cohortes->sqLocked('Dossier')
					);
					$paginate['conditions'][] = WebrsaPermissions::conditionsDossier();
					$paginate['limit'] = 10;

					$this->paginate = $paginate;
					$progressivePaginate = !Hash::get($this->request->data, 'Search.Pagination.nombre_total');
					$cohortedossierpcg66 = $this->paginate('Dossierpcg66', array(), array(), $progressivePaginate);

					if (empty($this->request->data['Dossierpcg66'])) {
						// Si un précédent dossier existe, on récupère le gestionnaire précédent par défaut
						foreach ($cohortedossierpcg66 as $i => $dossierpcg66) {
							$foyer = $this->Dossierpcg66->Foyer->find(
								'first',
								array(
									'conditions' => array(
										'Foyer.id' => $dossierpcg66['Dossierpcg66']['foyer_id']
									),
									'contain' => array(
										'Dossierpcg66' => array(
											'limit' => 1,
											'fields' => array('Dossierpcg66.user_id', 'Dossierpcg66.poledossierpcg66_id'),
											'order' => 'Dossierpcg66.created DESC',
											'conditions' => array(
												'Dossierpcg66.user_id IS NOT NULL'
											)
										)
									)
								)
							);
							$polePcdId = Hash::get($foyer, 'Dossierpcg66.0.poledossierpcg66_id');
							$poleActuelId = Hash::get($dossierpcg66, 'Dossierpcg66.poledossierpcg66_id');
							if (empty($poleActuelId) || $polePcdId == $poleActuelId) {
								$this->request->data['Dossierpcg66'][$i]['poledossierpcg66_id'] = $polePcdId;
								$this->request->data['Dossierpcg66'][$i]['user_id'] = $polePcdId . '_' . Hash::get($foyer, 'Dossierpcg66.0.user_id');
							} else {
								$this->request->data['Dossierpcg66'][$i]['poledossierpcg66_id'] = $poleActuelId;
							}


							// Préchargement de la liste des organismes à qui la décision est transmise
							if (in_array($statutAffectation, array('Affectationdossierpcg66::atransmettre'))) {
								$listeOrgstransmisdossierspcgs66 = $this->Dossierpcg66->Decisiondossierpcg66->Decdospcg66Orgdospcg66->find(
									'all',
									array(
										'fields' => array(
											'Decdospcg66Orgdospcg66.decisiondossierpcg66_id',
											'Decdospcg66Orgdospcg66.orgtransmisdossierpcg66_id'
										),
										'conditions' => array(
											'Decdospcg66Orgdospcg66.decisiondossierpcg66_id' => $dossierpcg66['Decisiondossierpcg66']['id']
										),
										'contain' => false
									)
								);
								$this->request->data['Notificationdecisiondossierpcg66'][$i]['Notificationdecisiondossierpcg66'] = Hash::extract($listeOrgstransmisdossierspcgs66, "{n}.Decdospcg66Orgdospcg66.orgtransmisdossierpcg66_id");
							}
						}
					} else {
						$progressivePaginate = SearchProgressivePagination::enabled($this->name, $this->action);
						if (!is_null($progressivePaginate)) {
							$this->request->data['Search']['Dossierpcg66']['paginationNombreTotal'] = !$progressivePaginate;
						}
					}

					if (!in_array($statutAffectation, array('Affectationdossierpcg66::affectes', 'Affectationdossierpcg66::aimprimer'))) {
						$this->Cohortes->get(Set::extract($cohortedossierpcg66, '{n}.Dossier.id'));
					}

					$this->set('cohortedossierpcg66', $cohortedossierpcg66);
				}
			}



			$orgsIds = Hash::extract($this->request->data, 'Orgtransmisdossierpcg66.Orgtransmisdossierpcg66');
			$conditions = array(
				'Orgtransmisdossierpcg66.isactif' => '1'
			);
			if (!empty($orgsIds)) {
				$conditions = array(
					'OR' => array(
						$conditions,
						array(
							'Orgtransmisdossierpcg66.id' => $orgsIds
						)
					)
				);
			}
			$listeOrgstransmisdossierspcgs66 = $this->Dossierpcg66->Decisiondossierpcg66->Orgtransmisdossierpcg66->find(
					'list', array(
				'conditions' => $conditions,
				'order' => array('Orgtransmisdossierpcg66.name ASC')
					)
			);
			$this->set(compact('listeOrgstransmisdossierspcgs66'));

			$this->_setOptions();

			$this->set('structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ));
			$this->set('referentsparcours', $this->InsertionsBeneficiaires->referents() );

			switch ($statutAffectation) {
				case 'Affectationdossierpcg66::enattenteaffectation':
					$this->render('formulaire');
					break;
				case 'Affectationdossierpcg66::affectes':
					$this->render('visualisation');
					break;
				case 'Affectationdossierpcg66::aimprimer':
					$this->render('aimprimer');
					break;
				case 'Affectationdossierpcg66::attentetransmission':
					$this->render('attentetransmission');
					break;
				case 'Affectationdossierpcg66::atransmettre':
					$this->render('atransmettre');
					break;
			}
		}

		/**
		 * Export du tableau en CSV
		 */
		public function exportcsv($action) {

			$querydata = $this->Cohortedossierpcg66->search(
					"Affectationdossierpcg66::{$action}", (array) $this->Session->read('Auth.Zonegeographique'), $this->Session->read('Auth.User.filtre_zone_geo'), Hash::expand($this->request->params['named'], '__')
			);
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();
			unset($querydata['limit']);
			$dossierspcgs66 = $this->Dossierpcg66->find('all', $querydata);

			$this->_setOptions();
			$this->layout = '';
			$this->set(compact('dossierspcgs66'));
		}

		/**
		 * Génération de la cohorte des convocations de passage en commission d'EP aux allocataires.
		 */
		public function notificationsCohorte() {
			$this->Dossierpcg66->begin();

			$querydata = $this->Cohortedossierpcg66->search(
					'Affectationdossierpcg66::aimprimer', (array) $this->Session->read('Auth.Zonegeographique'), $this->Session->read('Auth.User.filtre_zone_geo'), Hash::expand($this->request->params['named'], '__')
			);
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();
			unset($querydata['limit']);

			$dossierspcgs66 = $this->Dossierpcg66->find('all', $querydata);

			$pdfs = array();
			$decisionsdossierspcgs66_ids = Set::extract('/Decisiondossierpcg66/id', $dossierspcgs66);

			foreach ($decisionsdossierspcgs66_ids as $decisiondossierpcg66_id) {
				$pdfs[] = $this->Dossierpcg66->Decisiondossierpcg66->WebrsaDecisiondossierpcg66->getPdfDecision($decisiondossierpcg66_id);
			}

			$pdfs = $this->Gedooo->concatPdfs($pdfs, 'NotificationsDecisions');
			if ($pdfs) {
				$success = $this->Dossierpcg66->Decisiondossierpcg66->updateDossierpcg66Dateimpression($decisionsdossierspcgs66_ids);
				if (!$success) {
					$pdfs = null;
				}
			}

			if ($pdfs) {
				$this->Dossierpcg66->commit();
				$this->Gedooo->sendPdfContentToClient($pdfs, 'NotificationsDecisions.pdf');
			} else {
				$this->Dossierpcg66->rollback();
				$this->Session->setFlash('Impossible de générer les décisions des dossiers PCGs.', 'default', array('class' => 'error'));
				$this->redirect($this->referer());
			}
		}

		/**
		 * Créer un fichier zip avec la page entière d'impression en PDF
		 *
		 * @todo Jetons
		 */
		public function imprimer_cohorte() {
			$this->assert( !empty( $this->request->params['pass'] ), 'error404' );
			$this->Dossierpcg66 = ClassRegistry::init( 'Dossierpcg66' );
			$dossier_idList = array();
			$datas = array();
			$success = true;

			/**
			 * On recherche tout les éléments dont on a besoin
			 */
			foreach( $this->request->params['pass'] as $key => $dossierpcg66_id ) {
				$query = $this->Dossierpcg66->WebrsaDossierpcg66->getImpressionBaseQuery( $dossierpcg66_id );
				unset($query['order']); // Gain de temps vu qu'on a un id dans cette action
				$datas[$key] = $this->Dossierpcg66->find( 'first', $query );

				$dossier_idList[] = Hash::get( $datas[$key], 'Foyer.dossier_id' );

				if ( empty($datas[$key]) ) {
					$success = false;
					break;
				}
			}

			if ( $success ) {
				$this->Cohortes->get( $dossier_idList );

				$prefix = 'Dossier_PCG';
				$datetime = date('Y-m-d_His');
				$PdfUtility = new WebrsaPdfUtility();
				$pdfList = array();

				$this->Dossierpcg66->Decisiondossierpcg66->begin();

				foreach ( $datas as $key => $value ) {
					$pdfs = array();

					// Si l'etat du dossier est decisionvalid on le passe en atttransmiop avec une date d'impression
					if ( Hash::get( $value, 'Dossierpcg66.etatdossierpcg' ) === 'decisionvalid' ) {
						$value['Dossierpcg66']['dateimpression'] = date('Y-m-d');
						$value['Dossierpcg66']['etatdossierpcg'] = 'atttransmisop';
						$success = $this->Dossierpcg66->Decisiondossierpcg66->Dossierpcg66->save($value['Dossierpcg66']);
					}
					if ( !$success ) {
						break;
					}

					/**
					 * On récupère les PDFs
					 */
					$decisionsdossierspcgs66_id = Hash::get($value, 'Decisiondossierpcg66.id');
					$dossierpcg_id = Hash::get($value, 'Dossierpcg66.id');

					$decisionPdf = $decisionsdossierspcgs66_id !== null
						? $this->Dossierpcg66->Decisiondossierpcg66->WebrsaDecisiondossierpcg66->getPdfDecision( $decisionsdossierspcgs66_id )
						: null
					;

					$courriers = $this->Dossierpcg66->Decisiondossierpcg66->Dossierpcg66->Personnepcg66
						->Traitementpcg66->getPdfsByConditions( $dossierpcg_id, $decisionsdossierspcgs66_id, $this->Session->read('Auth.User.id') )
					;

					// Il faut au moins 1 PDF sinon il y a un problême
					if ( $decisionPdf === null && empty($courriers) ) {
						$success = false;
						break;
					}

					if ( $decisionPdf !== null ) {
						$pdfs[] = $decisionPdf;
					}

					foreach ( $courriers as $i => $courrier ) {
						$pdfs[] = $courrier['pdf'];
					}

					if ( Configure::read('Dossierspcgs66.imprimer_cohorte.Impression.RectoVerso') ) {
						$pdfs = $PdfUtility->preparePdfListForRectoVerso($pdfs);
					}

					$pdfList[] = $this->Gedooo->concatPdfs($pdfs, 'Dossierpcg66');
				}

				if ( $success ) {
					$this->Dossierpcg66->commit();
					$this->Cohortes->release( $dossier_idList );

					if ( Configure::read('Dossierspcgs66.imprimer_cohorte.Impression.RectoVerso') ) {
						$pdfList = $PdfUtility->preparePdfListForRectoVerso( $pdfList, WebrsaPdfUtility::ADD_BLANK_PAGES_BETWEEN_PDFS );
					}

					$concatPdf = $this->Gedooo->concatPdfs($pdfList, 'Dossierpcg66');
					$this->Gedooo->sendPdfContentToClient($concatPdf, "{$datetime}_{$prefix}_Cohorte_impression.pdf");
				}
				else {
					$this->Dossierpcg66->Decisiondossierpcg66->rollback();
				}

				$this->Session->setFlash( 'Impossible de générer les fichiers PDF', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}
	}
?>