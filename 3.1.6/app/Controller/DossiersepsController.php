<?php
	/**
	 * Code source de la classe DossiersepsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::import( 'Core', 'Sanitize' );

	/**
	 * La classe DossiersepsController ...
	 *
	 * @package app.Controller
	 */
	class DossiersepsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Dossierseps';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Gedooo.Gedooo',
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Jetons2',
			'Search.Filtresdefaut' => array(
				'administration',
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'administration' => array(
						'filter' => 'Search'
					),
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
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Type2',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Option',
			'Dossierep',
			'Decisionpdo',
			'Propopdo',
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
			'administration' => 'read',
			'choose' => 'read',
			'courrierInformation' => 'update',
			'courriersInformations' => 'update',
			'decisioncg' => 'read',
			'decisions' => 'read',
			'delete' => 'delete',
			'deletepassage' => 'delete',
			'exportcsv' => 'read',
			'index' => 'read',
		);

		/**
		 * FIXME: evite les droits
		 */
		protected function _setOptions() {
			$this->set( 'motifpdo', ClassRegistry::init('Propopdo')->enum('motifpdo') );
			$this->set( 'decisionpdo', $this->Decisionpdo->find( 'list' ) );

			$options = (array)Hash::get( $this->Propopdo->enums(), 'Propopdo' );

			// Ajout des enums pour les thématiques du CG uniquement
			foreach( $this->Dossierep->Passagecommissionep->Commissionep->Ep->Regroupementep->themes() as $theme ) {
				$modeleDecision = Inflector::classify( "decision{$theme}" );
				if( in_array( 'Enumerable', $this->Dossierep->Passagecommissionep->Commissionep->Passagecommissionep->{$modeleDecision}->Behaviors->attached() ) ) {
					$options = Set::merge( $options, $this->Dossierep->Passagecommissionep->Commissionep->Passagecommissionep->{$modeleDecision}->enums() );
				}
			}

			$this->set( compact( 'options' ) );
		}

		/**
		*
		*/

		public function index() {
			$this->paginate = array(
				'fields' => array(
					'Dossierep.id',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Dossierep.created',
					'Dossierep.themeep',
				),
				'contain' => array(
					'Personne',
				),
				'limit' => 10
			);

			$this->set( 'options', $this->Dossierep->enums() );
			$this->set( 'dossierseps', $this->paginate( $this->Dossierep ) );
		}

		/**
		 * Envoi à la vue pour chacune des thématiques la liste des dossiers sélectionnables pour
		 * passage en commission d'une commission d'ep donnée.
		 *
		 * Set les variables $themeEmpty, $dossiers, $themesChoose, $countDossiers,
		 * $options, $dossierseps, $commissionep, $commissionep_id dans la vue.
		 *
		 * @param array $commissionep L'enregistrement de la commission d'EP
		 * @param boolean $paginate Soit on pagine (pour le choose), soit on find tout, pour l'export CSV
		 */
		protected function _setListeDossiersSelectionnables( $commissionep, $paginate ) {
			$commissionep_id = $commissionep['Commissionep']['id'];

			$conditionsAdresses = array( 'OR' => array() );
			// Début conditions zones géographiques CG 58 et CG 93
			if( Configure::read( 'CG.cantons' ) == false ) {
				$zonesgeographiques = Set::extract(
					$commissionep,
					'Ep.Zonegeographique.{n}.codeinsee'
				);
				if( !empty( $zonesgeographiques ) ) {
					foreach( $zonesgeographiques as $zonegeographique ) {
							$conditionsAdresses['OR'][] = "Adresse.numcom ILIKE '%".Sanitize::paranoid( $zonegeographique )."%'";
					}
				}
			}
			// Fin conditions zones géographiques CG 58 et CG 93
			// Début conditions zones géographiques CG 66
			else {
			/// Critères sur l'adresse - canton
				$zonesgeographiques = Set::extract(
					$commissionep,
					'Ep.Zonegeographique.{n}.id'
				);

				$this->Canton = ClassRegistry::init( 'Canton' );
				$conditionsAdresses = array();
				if( count( $zonesgeographiques ) != $this->Canton->Zonegeographique->find( 'count' ) ) {
					$conditionsAdresses = $this->Canton->queryConditionsByZonesgeographiques( $zonesgeographiques );
				}
			}
			// Fin conditions zones géographiques CG 66

			$themes = $this->Dossierep->Passagecommissionep->Commissionep->WebrsaCommissionep->themesTraites( $commissionep_id );
			$listeThemes = null;
			if( !empty( $themes ) ) {
				$listeThemes['OR'] = array();
				foreach($themes as $theme => $niveauDecision) {
					$listeThemes['OR'][] = array( 'Dossierep.themeep' => Inflector::tableize( $theme ) );
				}
				$this->set( 'themeEmpty', false );

				if( empty( $conditionsAdresses['OR'] ) ) {
					$conditionsAdresses = array();
				}

				$queryData = array(
					'conditions' => array(
						(array)Configure::read( 'Dossierseps.conditionsSelection' ),
						$conditionsAdresses,
						$listeThemes,
						'Dossierep.id NOT IN ('.
							$this->Dossierep->Passagecommissionep->sq(
								array(
									'fields' => array( 'passagescommissionseps.dossierep_id' ),
									'alias' => 'passagescommissionseps',
									'joins' => array(
										array(
											'table'      => 'commissionseps',
											'alias'      => 'commissionseps',
											'type'       => 'INNER',
											'foreignKey' => false,
											'conditions' => array( 'commissionseps.id = passagescommissionseps.commissionep_id' ),
											'order'      => array( 'commissionseps.dateseance DESC' ),
											'limit'      => 1,

										),
									),
									'conditions' => array(
										'commissionseps.id <> ' => $commissionep_id,
										'passagescommissionseps.etatdossierep <>' => 'reporte'
									)
								)
							)
						.' )',
					),
					'limit' => 50,
				);
				$configuredOrder = Configure::read( $this->name.'.'.$this->action.'.order' );
				$queryData['order'] = $configuredOrder ? $configuredOrder : array( 'Personne.nom', 'Personne.prenom' );

				$queryData = $this->Dossierep->queryDossiersSelectionnables( $queryData );

				$options = $this->Dossierep->enums();
				$options['Dossierep']['commissionep_id'] = $this->Dossierep->Passagecommissionep->Commissionep->find(
					'list',
					array(
						'conditions' => array(
							'Commissionep.etatcommissionep' => array( 'cree', 'associe', 'valide', 'presence', 'decisionep' )
						)
					)
				);
			}
			else {
				$this->set( 'themeEmpty', true );
			}

			$themesChoose = array_keys( $this->Dossierep->Passagecommissionep->Commissionep->WebrsaCommissionep->themesTraites( $commissionep_id ) );

			$dossiers = array();
			$countDossiers = 0;
			$originalPaginate = $this->paginate;
			foreach( $themesChoose as $theme ) {
				$qd = array();
				$class = Inflector::classify( $theme );
				$qdListeDossier = $this->Dossierep->{$class}->qdListeDossierChoose( $commissionep_id );

				if ( isset( $qdListeDossier['fields'] ) ) {
					$qd['fields'] = array_merge( $qdListeDossier['fields'], $queryData['fields'] );
				}

				// TODO: une sorte de mergeConditions (plus propre) ?
				$qd['conditions'] = array_merge(
					array( 'Dossierep.themeep' => Inflector::tableize( $class ) ),
					$queryData['conditions'],
					$qdListeDossier['conditions']
				);

				$qd['joins'] = array_merge( $qdListeDossier['joins'], $queryData['joins'] );
				$qd['contain'] = false;
				$qd['limit'] = $queryData['limit'];
				$qd['order'] = $queryData['order'];

				$this->Dossierep->{$class}->forceVirtualFields = true;

				// Si l'allocataire a déménagé, si l'état de son dossier a changé, ... mais qu'il a déjà été sélectionné, il faut qu'on le retrouve quand même
				$qd['conditions'] = array(
					'OR' => array(
						array(
							'Passagecommissionep.dossierep_id = Dossierep.id',
							'Passagecommissionep.commissionep_id = Commissionep.id',
						),
						$qd['conditions']
					)
				);

				if( $paginate ) {
					$this->paginate = $qd;
					$dossiers[$theme] = $this->paginate( $this->Dossierep->{$class} );
					$this->refreshPaginator();
				}
				else {
					$dossiers[$theme] = $this->Dossierep->{$class}->find( 'all', $qd );
				}

				// INFO: pour avoir le formulaire pré-rempli ... à mettre dans le modèle également ?
				if( empty( $this->request->data ) ) {
					foreach( $dossiers[$theme] as $key => $dossierep ) {
						$dossiers[$theme][$key]['Passagecommissionep']['chosen'] = ( ( $dossierep['Passagecommissionep']['commissionep_id'] == $commissionep_id ) );
					}
				}
				$countDossiers += count($dossiers[$theme]);
			}
			$this->paginate = $originalPaginate;
			$this->set( compact( 'dossiers', 'themesChoose' ) );
			$this->set( compact( 'countDossiers' ) );

			if ( Configure::read( 'Cg.departement' ) == 93 ) {
				$options = Hash::merge(
					$options,
					$this->Dossierep->Nonrespectsanctionep93->enums(),
					$this->Dossierep->Signalementep93->Contratinsertion->enums(),
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

			if( Configure::read( 'Cg.departement' ) == 58 ){
				$options = Set::merge( $options, $this->Dossierep->Sanctionep58->enums() );
			}

			$this->set( compact( 'options', 'dossierseps', 'commissionep' ) );
			$this->set( 'commissionep_id', $commissionep_id);
		}

		/**
		*
		*/

		public function choose( $commissionep_id ) {
			$commissionep = $this->Dossierep->Passagecommissionep->Commissionep->find(
				'first',
				array(
					'conditions' => array(
						'Commissionep.id' => $commissionep_id
					),
					'contain' => array(
						'Ep' => array(
							'Zonegeographique'
						)
					)
				)
			);

			// Peut-on travailler à cette étape avec cette commission ?
			if( in_array( $commissionep['Commissionep']['etatcommissionep'], array( 'decisionep', 'decisioncg', 'annulee' ) ) ) {
				$this->Session->setFlash( 'Impossible d\'attribuer des dossiers à une commission d\'EP lorsque celle-ci comporte déjà des avis ou des décisions.', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				/*$this->Jetons2->release( Set::extract( '/Foyer/dossier_id', $this->request->data ) );
				$this->Jetonsfonctions2->release( $cov58_id );*/
				$this->redirect( array( 'controller' => 'commissionseps', 'action' => 'view', $commissionep_id, '#' => "dossiers,{$this->request->data['Choose']['theme']}" ) );
			}

			// Enregistrement des cases cochées / décochées
			if( !empty( $this->request->data ) ) {
				$ajouts = array();
				$suppressions = array();
				foreach( $this->request->data['Dossierep'] as $key => $dossierep ) {
					if( empty( $this->request->data['Passagecommissionep'][$key]['chosen'] ) && !empty( $this->request->data['Passagecommissionep'][$key]['id'] ) ) {
						$suppressions[] = $this->request->data['Passagecommissionep'][$key]['id'];
					}
					else if( !empty( $this->request->data['Passagecommissionep'][$key]['chosen'] ) && empty( $this->request->data['Passagecommissionep'][$key]['id'] ) ) {
						$ajouts[] = array(
							'commissionep_id' => $commissionep_id,
							'dossierep_id' => $this->request->data['Dossierep'][$key]['id'],
							'user_id' => $this->Session->read( 'Auth.User.id' )
						);
					}
				}

				$this->Dossierep->begin();

				$success = true;

				if( !empty( $ajouts ) ) {
					$success = $this->Dossierep->Passagecommissionep->saveAll( $ajouts, array( 'atomic' => false ) ) && $success;
				}

				if( !empty( $suppressions ) ) {
					$success = $this->Dossierep->Passagecommissionep->delete( $suppressions ) && $success;
				}

				// Changer l'état de la séance
				$success = $this->Dossierep->Passagecommissionep->Commissionep->WebrsaCommissionep->changeEtatCreeAssocie( $commissionep_id ) && $success;

				$this->_setFlashResult( 'Save', $success );

				if( $success ) {
					$this->Dossierep->commit();
					$this->redirect( array( 'controller'=>'commissionseps', 'action'=>'view', $commissionep_id, '#' => 'dossiers,'.$this->request->data['Choose']['theme'] ) );
				}
				else {
					$this->Dossierep->rollback();
				}
			}

			$this->_setListeDossiersSelectionnables( $commissionep, true );
		}

		/**
		 * Exporte la liste de dossier sélectionnables pour une commission d'EP donnée.
		 *
		 * @param @integer $commissionep_id L'id de la commission
		 */
		public function exportcsv( $commissionep_id ) {
			$commissionep = $this->Dossierep->Passagecommissionep->Commissionep->find(
				'first',
				array(
					'conditions' => array(
						'Commissionep.id' => $commissionep_id
					),
					'contain' => array(
						'Ep' => array(
							'Zonegeographique'
						)
					)
				)
			);

			$this->_setListeDossiersSelectionnables( $commissionep, false );

			$this->layout = '';
		}

		/**
		*
		*/

		public function decisioncg ( $dossierep_id ) {
			$this->_decision( $dossierep_id, 'cg' );
		}

		/**
		*
		*/

		public function _decision ( $dossierep_id, $niveauDecision ) {
			$themeTraite = $this->Dossierep->themeTraite($dossierep_id);
			$dossierep = array();

			$dossierep = $this->Dossierep->find(
				'first',
				array(
					'conditions' => array(
						'Dossierep.id' => $dossierep_id
					)
				)
			);

			$classThemeName = Inflector::classify( $dossierep['Dossierep']['themeep'] );
			$containQueryData = $this->Dossierep->{$classThemeName}->containQueryData();
			$dossierep = $this->Dossierep->find(
				'first',
				array(
					'conditions' => array(
						'Dossierep.id' => $dossierep_id
					),
					'contain' => $containQueryData
				)
			);

			$this->set( 'dossier', $dossierep );
			$this->set( 'themeName', Inflector::underscore( $classThemeName ) );

			if (!empty($this->request->data)) {
				$this->Dossierep->begin();
				if ($this->Dossierep->sauvegardeUnique( $dossierep_id, $this->request->data, $niveauDecision )) {
					$this->_setFlashResult( 'Save', true );
					$this->Dossierep->commit();
					$this->redirect(array('controller'=>'commissionseps', 'action'=>'traitercg', $dossierep['Passagecommissionep'][0]['commissionep_id']));
				}
				else {
					$this->_setFlashResult( 'Save', false );
					$this->Dossierep->rollback();
				}
			}
			else {
				$this->request->data = $this->Dossierep->prepareFormDataUnique($dossierep_id, $dossierep, $niveauDecision);
			}
			$this->_setOptions();
			$this->set('dossierep_id', $dossierep_id);
			$this->set( 'commissionep_id', $dossierep['Passagecommissionep'][0]['commissionep_id'] );
		}

		/**
		*
		*/

		public function decisions() {
			$this->paginate = array(
				'Dossierep' => array(
					'contain' => array(
						'Commissionep',
						'Personne' => array(
							'Foyer' => array(
								'Adressefoyer' => array(
									'conditions' => array(
										'Adressefoyer.rgadr' => '01'
									),
									'Adresse'
								)
							)
						),
						// Thèmes 66
						'Defautinsertionep66' => array(
							'Decisiondefautinsertionep66' => array(
								'order' => 'created DESC',
								'limit' => 1
							)
						),
						'Saisinebilanparcoursep66' => array(
							'Decisionsaisinebilanparcoursep66' => array(
								'order' => 'created DESC',
								'limit' => 1
							)
						),
						'Saisinepdoep66' => array(
							'Decisionsaisinepdoep66' => array(
								'order' => 'created DESC',
								'limit' => 1,
								'Decisionpdo'
							)
						),
						// Thèmes 93
						'Reorientationep93' => array(
							'Decisionreorientationep93' => array(
								'order' => 'created DESC',
								'limit' => 1
							)
						),
						'Nonrespectsanctionep93' => array(
							'Decisionnonrespectsanctionep93' => array(
								'order' => 'created DESC',
								'limit' => 1
							)
						),
					),
					'conditions' => array(
						'Dossierep.commissionep_id IS NOT NULL',
						'Dossierep.etatdossierep' => 'traite',
					),
					'limit' => 10
				)
			);

			// FIXME: plus générique
			$decisions = array(
				// CG 66
				'Defautinsertionep66' => $this->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->enum( 'decision' ),
				'Saisinebilanparcoursep66' => $this->Dossierep->Passagecommissionep->Decisionsaisinebilanparcoursep66->enum( 'decision' ),
				// CG 93
				'Nonrespectsanctionep93' => $this->Dossierep->Passagecommissionep->Decisionnonrespectsanctionep93->enum( 'decision' ),
				'Reorientationep93' => $this->Dossierep->Passagecommissionep->Decisionreorientationep93->enum( 'decision' )
			);
			$this->set( compact( 'decisions' ) );
			$this->set( 'options', $this->Dossierep->enums() );
			$this->set( 'dossierseps', $this->paginate( $this->Dossierep ) );
		}


		/**
		* Génération et envoi du courrier d'information avant passage en EP pour
		* la thématique defautinsertionep66.
		*/

		public function courrierInformation( $dossierep_id ) {
			$dossierep = $this->Dossierep->find(
				'first',
				array(
					'conditions' => array(
						'Dossierep.id' => $dossierep_id
					)
				)
			);

			$classThemeName = Inflector::classify( $dossierep['Dossierep']['themeep'] );
			$pdf = $this->Dossierep->{$classThemeName}->getCourrierInformationPdf( $dossierep['Dossierep']['id'], $this->Session->read( 'Auth.User.id' ) );

			if( $pdf ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, 'Courrier_Information.pdf' );
			}
			else {
				$this->Session->setFlash( 'Impossible de générer le courrier d\'information', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}

		/**
		* Génération des courriers d'information avant passage en EP pour
		* la thématique defautinsertionep66.
		*/

		public function courriersInformations( $commissionep_id ) {
			$liste = $this->Dossierep->Passagecommissionep->find(
				'all',
				array(
					'fields' => array(
						'Passagecommissionep.id',
						'Passagecommissionep.dossierep_id',
					),
					'conditions' => array(
						'Passagecommissionep.commissionep_id' => $commissionep_id,
						'Dossierep.themeep' => 'defautsinsertionseps66'
					),
					'contain' => array(
						'Dossierep'
					)
				)
			);

			$pdfs = array();
			foreach( Set::extract( '/Passagecommissionep/dossierep_id', $liste ) as $dossierep_id ) {
				$pdfs[] = $this->Dossierep->Defautinsertionep66->getCourrierInformationPdf( $dossierep_id );
			}

			$pdfs = $this->Gedooo->concatPdfs( $pdfs, 'CourriersInformation' );

			if( $pdfs ) {
				$this->Gedooo->sendPdfContentToClient( $pdfs, 'CourriersInformation.pdf' );
			}
			else {
				$this->Session->setFlash( 'Impossible de générer les courriers d\'information pour cette commission.', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Permet de lister les dossiers d'EP pouvant être supprimés.
		 *
		 * @see Dossierep::searchAdministration()
		 */
		public function administration() {
			if( !empty( $this->request->data ) ) {
				$query = $this->Dossierep->searchAdministration( $this->request->data['Search'] );
				$lockedField = $this->Jetons2->sqLocked( 'Dossier' );
				$this->Dossierep->Personne->Foyer->Dossier->virtualFields['locked'] = $lockedField;
				$query['fields'][] = "( {$lockedField} ) AS \"Dossier__locked\"";

				$this->paginate = array( 'Dossierep' => $query );

				$results = $this->paginate(
					$this->Dossierep,
					array(),
					array(),
					!Hash::get( $this->request->data, 'Search.Pagination.nombre_total' )
				);

				$this->set( compact( 'results' ) );
			}

			// -----------------------------------------------------------------

			$this->Gestionzonesgeos->setCantonsIfConfigured();
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );

			$options = Hash::merge(
				$this->Dossierep->enums(),
				$this->Dossierep->Passagecommissionep->Commissionep->enums()
			);

			$options['Dossierep']['themeep'] = $this->Dossierep->themesCg();
			$options['Ep']['regroupementep_id'] = $this->Dossierep->Passagecommissionep->Commissionep->Ep->Regroupementep->find( 'list' );

			$themes = array_keys( $options['Dossierep']['themeep'] );
			foreach( $themes as $theme ) {
				$tableNameDecision = "decisions{$theme}";
				$modelNameDecision = Inflector::classify( $tableNameDecision );

				$optionsDecision = $this->Dossierep->Passagecommissionep->{$modelNameDecision}->enums();
				$options = Hash::merge( $options, array( 'Decisionthematique' => $optionsDecision[$modelNameDecision] ) );
			}

			$this->set( compact( 'options' ) );
		}

		/**
		 * Suppression d'un dossier d'EP lorsque c'est possible.
		 *
		 * @see Dossierep::searchAdministration()
		 *
		 * @param integer $id Clé primaire de l'enregistrement de dossiers à supprimer.
		 * @throws LogicException
		 */
		public function delete( $id ) {
			// On vérifie que le dossier remplisse bien les critères pour être supprimé
			$query = $this->Dossierep->searchAdministration( array() );
			$query['conditions']['Dossierep.id'] = $id;
			$result = $this->Dossierep->find( 'all', $query );

			if( count( $result ) != 1 ) {
				$message = sprintf( "Erreur lors de la tentative de suppression du dossier d'EP d'id %d", $id );
				throw new LogicException( $message );
			}

			// Tentative d'acquisition du jeton sur le dossier
			$dossier_id = $this->Dossierep->dossierId( $id );
			$this->DossiersMenus->checkDossierMenu( array( 'id' => $dossier_id ) );

			$this->Jetons2->get( $dossier_id );

			// Tentative de suppression du dossier d'EP et des enregistrements liés
			$this->Dossierep->begin();
			$success = $this->Dossierep->delete( $id );

			if( $success ) {
				$this->Dossierep->commit();
				$this->Jetons2->release( $dossier_id );
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			}
			else {
				$this->Dossierep->rollback();
				$this->Session->setFlash( 'Suppression impossible', 'flash/error' );
			}

			$this->redirect( $this->referer() );
		}

		/**
		 * Suppression d'un passage en commission d'un dossier d'EP lorsque
		 * c'est possible.
		 *
		 * @see Dossierep::searchAdministration()
		 *
		 * @param integer $id Clé primaire de l'enregistrement du passage à supprimer.
		 * @throws LogicException
		 */
		public function deletepassage( $id ) {
			// On vérifie que le dossier remplisse bien les critères pour être supprimé
			$query = $this->Dossierep->searchAdministration( array() );
			$query['conditions']['Passagecommissionep.id'] = $id;
			$result = $this->Dossierep->find( 'all', $query );

			if( count( $result ) != 1 ) {
				$message = sprintf( "Erreur lors de la tentative de suppression du dossier d'EP d'id %d", $id );
				throw new LogicException( $message );
			}

			// Tentative d'acquisition du jeton sur le dossier
			$dossier_id = $this->Dossierep->dossierId( $result[0]['Dossierep']['id'] );
			$this->DossiersMenus->checkDossierMenu( array( 'id' => $dossier_id ) );

			$this->Jetons2->get( $dossier_id );

			// Tentative de suppression du dossier d'EP et des enregistrements liés
			$this->Dossierep->begin();
			$success = $this->Dossierep->Passagecommissionep->delete( $id );

			if( $success ) {
				$this->Dossierep->commit();
				$this->Jetons2->release( $dossier_id );
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			}
			else {
				$this->Dossierep->rollback();
				$this->Session->setFlash( 'Suppression impossible', 'flash/error' );
			}

			$this->redirect( $this->referer() );
		}
		
		/**
		 * Permet de passer un dossier EP en actif = false
		 * 
		 * @param integer $id
		 */
		public function disable($id) {
			$result = $this->Dossierep->find('first', 
				array(
					'fields' => 'Dossierep.id',
					'contain' => false,
					'conditions' => array('Dossierep.id' => $id)
				)
			);
			
			$this->assert(!empty($result), 'error404');
			
			$data = array(
				'Dossierep' => array(
					'id' => $id,
					'actif' => 0
				)
			);
			
			$this->Dossierep->begin();
			$this->Dossierep->create($data);
			
			if ($this->Dossierep->save()) {
				$this->Dossierep->commit();
				$this->Session->setFlash('Désactivation effectuée', 'flash/success');
			}
			else {
				$this->Dossierep->rollback();
				$this->Session->setFlash('Désactivation impossible', 'flash/error');
			}
			
			$this->redirect($this->referer());
		}
	}
?>