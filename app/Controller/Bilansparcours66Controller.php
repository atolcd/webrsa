<?php
	/**
	 * Code source de la classe CohortesciController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAccessBilansparcours66', 'Utility');

	/**
	 * La classe CohortesciController permet de gérer les bilans de parcours (CG 66).
	 *
	 * @package app.Controller
	 */
	class Bilansparcours66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Bilansparcours66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Fileuploader',
			'Gedooo.Gedooo',
			'InsertionsBeneficiaires',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'search'
				)
			),
			'WebrsaAccesses',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Bilanparcours66',
			'Dossierep',
			'Option',
			'Typeorient',
			'WebrsaBilanparcours66',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Bilansparcours66:edit',
			'exportcsv' => 'Criteresbilansparcours66:exportcsv',
			'search' => 'Criteresbilansparcours66:index',
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajaxfiledelete',
			'ajaxfileupload',
			'choixformulaire',
			'download',
			'fileview',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'cancel' => 'update',
			'download' => 'read',
			'edit' => 'update',
			'exportcsv' => 'read',
			'filelink' => 'read',
			'fileview' => 'read',
			'impression' => 'read',
			'index' => 'read',
			'search' => 'read',
			'view' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions( $options = array(), $params = array() ) {
			$params += array( 'find' => true );

			$options = Hash::merge( $options, $this->Bilanparcours66->enums() );
			$typevoie = $this->Option->typevoie();
			$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );
			$this->set( 'qual', $this->Option->qual() );
			$this->set( 'nationalite', ClassRegistry::init('Personne')->enum('nati') );

			$options = Hash::insert( $options, 'typevoie', $typevoie );

			if( $params['find'] === true ) {
				$options[$this->modelClass]['structurereferente_id'] = $this->{$this->modelClass}->Structurereferente->listOptions();
	// 			$options[$this->modelClass]['referent_id'] = $this->{$this->modelClass}->Referent->find( 'list' );
				$options[$this->modelClass]['referent_id'] = $this->Bilanparcours66->Referent->WebrsaReferent->listOptions();
				$options[$this->modelClass]['nvsansep_referent_id'] = $this->{$this->modelClass}->Referent->find( 'list' );
				$options[$this->modelClass]['nvparcours_referent_id'] = $this->{$this->modelClass}->Referent->find( 'list' );
			}

			$this->set( 'rsaSocle', ClassRegistry::init('Detailcalculdroitrsa')->enum('natpf') );

			$options['Bilanparcours66']['duree_engag'] = $this->Option->duree_engag();

			if( $params['find'] === true ) {
				$typesorients = $this->Typeorient->find('list');
				$this->set(compact('typesorients'));
				$structuresreferentes = $this->Bilanparcours66->Structurereferente->find('list');
				$this->set(compact('structuresreferentes'));
				$autresstructuresreferentes = $this->{$this->modelClass}->Structurereferente->listOptions();
				$this->set(compact('autresstructuresreferentes'));
			}

			$options = Set::merge( $options, $this->Dossierep->Passagecommissionep->Decisionsaisinebilanparcoursep66->enums() );
			$options = Set::merge( $options, $this->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->enums() );

			$options = Set::merge( $options, $this->Bilanparcours66->Dossierpcg66->Decisiondossierpcg66->enums() );

			if( $params['find'] === true ) {
				$typeorientprincipale = Configure::read( 'Orientstruct.typeorientprincipale' );
				$options['Bilanparcours66']['typeorientprincipale_id'] = $this->Typeorient->listRadiosOptionsPrincipales( $typeorientprincipale['SOCIAL'] );
				$options['Bilanparcours66']['orientationpro_id'] = $this->Typeorient->listRadiosOptionsPrincipales( $typeorientprincipale['Emploi'] );

				$options['Bilanparcours66']['nvtypeorient_id'] = $this->Typeorient->listOptionsUnderParent();
				$options['Bilanparcours66']['nvstructurereferente_id'] = $this->Bilanparcours66->Structurereferente->list1Options( array( 'orientation' => 'O' ) );
				$options['Saisinebilanparcoursep66']['typeorient_id'] = $options['Bilanparcours66']['nvtypeorient_id'];
				$options['Saisinebilanparcoursep66']['structurereferente_id'] = $options['Bilanparcours66']['nvstructurereferente_id'];

				$options[$this->modelClass]['serviceinstructeur_id'] = $this->{$this->modelClass}->Serviceinstructeur->listOptions( array( 'Serviceinstructeur.typeserins <>' => 'C' ) ); // Liste des services instructeurs en lien avec un Service Social
			}

			$Entretien = ClassRegistry::init( 'Entretien' );
			$Contratinsertion = ClassRegistry::init( 'Contratinsertion' );
			$options = array_merge($options, $Entretien->options(), $Contratinsertion->WebrsaContratinsertion->options());
			$options['Entretien']['structurereferente_id'] = $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) );

			$this->set( compact( 'options' ) );
		}

		/**
		 * http://valums.com/ajax-upload/
		 * http://doc.ubuntu-fr.org/modules_php
		 * increase post_max_size and upload_max_filesize to 10M
		 * debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
		 */
		public function ajaxfileupload() {
			$this->Fileuploader->ajaxfileupload();
		}

		/**
		 * http://valums.com/ajax-upload/
		 * http://doc.ubuntu-fr.org/modules_php
		 * increase post_max_size and upload_max_filesize to 10M
		 * debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
		 * FIXME: traiter les valeurs de retour
		 */
		public function ajaxfiledelete() {
			$this->Fileuploader->ajaxfiledelete();
		}

		/**
		 * Fonction permettant de visualiser les fichiers chargés dans la vue avant leur envoi sur le serveur
		 *
		 * @param integer $id
		 */
		public function fileview( $id ) {
			$this->Fileuploader->fileview( $id );
		}

		/**
		 * Téléchargement des fichiers préalablement associés à un traitement donné
		 *
		 * @param integer $fichiermodule_id
		 */
		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}

		/**
		 * Fonction permettant d'accéder à la page pour lier les fichiers à l'Orientation
		 *
		 * @param integer $id
		 */
		public function filelink( $id ){
			$this->WebrsaAccesses->check($id);
			$this->assert( valid_int( $id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Bilanparcours66->personneId( $id ) ) ) );

			$fichiers = array();
			$bilanparcours66 = $this->Bilanparcours66->find(
				'first',
				array(
					'conditions' => array(
						'Bilanparcours66.id' => $id
					),
					'contain' => array(
						'Fichiermodule' => array(
							'fields' => array( 'name', 'id', 'created', 'modified' )
						),
						'Personne',
						'Orientstruct' => array(
							'fields' => array(
								'personne_id'
							)
						)
					)
				)
			);

			$personne_id = Set::classicExtract( $bilanparcours66, 'Bilanparcours66.personne_id' );

			$dossier_id = $this->Bilanparcours66->Personne->dossierId( $personne_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {

				$this->Bilanparcours66->begin();

				$saved = $this->Bilanparcours66->updateAllUnBound(
					array( 'Bilanparcours66.haspiecejointe' => '\''.$this->request->data['Bilanparcours66']['haspiecejointe'].'\'' ),
					array(
						'"Bilanparcours66"."orientstruct_id"' => Set::classicExtract( $bilanparcours66, 'Bilanparcours66.orientstruct_id' ),
						'"Bilanparcours66"."id"' => $id
					)
				);

				if( $saved ){
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, !Set::classicExtract( $this->request->data, "Bilanparcours66.haspiecejointe" ), $id ) && $saved;
				}

				if( $saved ) {
					$this->Bilanparcours66->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
// 					$this->redirect( array(  'controller' => 'bilansparcours66','action' => 'index', $personne_id ) );
					$this->redirect( $this->referer() );
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Bilanparcours66->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}

			$this->_setOptions();
			$this->set( 'urlmenu', '/bilanspourcours66/index/'.$personne_id );
			$this->set( compact( 'dossier_id', 'personne_id', 'fichiers', 'bilanparcours66' ) );
		}

		/**
		 *
		 * @param integer $personne_id
		 */
		public function index($personne_id) {
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id)));
			$this->_setEntriesAncienDossier($personne_id, 'Bilanparcours66');

			$cacheKey = 'Bilanparcours66_'.Inflector::underscore(__METHOD__);
			$query = Cache::read($cacheKey);

			if ($query === false) {
				Cache::write($cacheKey, $query = $this->Bilanparcours66->WebrsaBilanparcours66->getIndexQuery());
			}

			$query['conditions']['Bilanparcours66.personne_id'] = $personne_id;
			
			$bilansparcours66 = $this->WebrsaAccesses->getIndexRecords($personne_id, $query);
			
			$this->_setOptions(array(), array('find' => false));
			$this->set(compact('bilansparcours66', 'nborientstruct', 'struct', 'ajoutPossible'));
		}

		/**
		 *
		 */
		public function add($personne_id) {
			$this->WebrsaAccesses->check(null, $personne_id);
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * TODO: que modifie-t'on ? Dans quel cas peut-on supprimer ?
		 *
		 */
		public function edit($id) {
			$this->WebrsaAccesses->check($id);
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		* Ajout ou modification du bilan de parcours d'un allocataire.
		*
		* Le bilan de parcours entraîne:
		*	- pour le thème réorientation/saisinesbilansparcourseps66
		*		* soit un maintien de l'orientation, avec reconduction du CER, sans passage en EP
		*		* soit une saisine de l'EP locale, commission parcours
		*
		* FIXME: modification du bilan
		*
		* @param integer $id Pour un ajout, l'id technique de la personne; pour une
		*	modification, l'id technique du bilan de parcours.
		* @return void
		* @precondition L'allocataire existe et possède une orientation
		* @access protected
		*/
		protected function _add_edit( $id = null ) {
			if( $this->action == 'add' ) {
				$personne_id = $id;
			}
			// TODO
			else if( $this->action == 'edit' ) {
				$bilanparcours66 = $this->Bilanparcours66->find(
					'first',
					array(
						'contain' => array(
							'Personne',
							'Saisinebilanparcoursep66' => array(
								'Dossierep' => array(
									'Passagecommissionep' => array(
										'conditions' => array(
											'Passagecommissionep.etatdossierep' => 'traite'
										),
										'Commissionep',
										'Decisionsaisinebilanparcoursep66' => array(
											'order' => 'Decisionsaisinebilanparcoursep66.etape ASC'
										)
									)
								)
							),
							'Defautinsertionep66' => array(
								'Dossierep' => array(
									'Passagecommissionep' => array(
										'conditions' => array(
											'Passagecommissionep.etatdossierep' => 'traite'
										),
										'Commissionep',
										'Decisiondefautinsertionep66' => array(
											'order' => 'Decisiondefautinsertionep66.etape ASC'
										)
									)
								)
							),
							'Contratinsertion',
							'Orientstruct' => array(
								'Typeorient',
								'Structurereferente'
							)
						),
						'conditions' => array( 'Bilanparcours66.id' => $id )
					)
				);
				$this->assert( !empty( $bilanparcours66 ), 'error404' );

				$personne_id = $bilanparcours66['Bilanparcours66']['personne_id'];

				if( in_array( $bilanparcours66['Bilanparcours66']['proposition'], array( 'parcours', 'parcourspe' ) ) ) {
					$bilanparcours66['Saisinebilanparcoursep66']['structurereferente_id'] = implode( '_', array( $bilanparcours66['Saisinebilanparcoursep66']['typeorient_id'], $bilanparcours66['Saisinebilanparcoursep66']['structurereferente_id']) );
					$passagecommissionep = $this->Dossierep->Passagecommissionep->find(
						'first',
						array(
							'conditions' => array(
								'Passagecommissionep.dossierep_id IN ( '.$this->Dossierep->sq(
									array(
										'fields' => array(
											'dossierseps.id'
										),
										'alias' => 'dossierseps',
										'conditions' => array(
											'dossierseps.themeep' => 'saisinesbilansparcourseps66'
										),
										'joins' => array(
											array(
												'table' => 'saisinesbilansparcourseps66',
												'alias' => 'saisinesbilansparcourseps66',
												'type' => 'INNER',
												'conditions' => array(
													'saisinesbilansparcourseps66.dossierep_id = dossierseps.id',
													'saisinesbilansparcourseps66.bilanparcours66_id' => $id
												)
											)
										)
									)
								).' )',
                                'Passagecommissionep.id IN ('.$this->Dossierep->Passagecommissionep->sqDernier().' )'
							),
							'contain' => array(
								'Commissionep',
								'Decisionsaisinebilanparcoursep66' => array(
									'order' => array( 'Decisionsaisinebilanparcoursep66.etape ASC' ),
                                    'Typeorient',
                                    'Structurereferente',
                                    'Referent'
								),
                                'Dossierep'
							)
						)
					);
					$this->set( compact( 'passagecommissionep' ) );
				}
				elseif ( in_array( $bilanparcours66['Bilanparcours66']['proposition'], array( 'audition', 'auditionpe' ) ) ) {
					$passagecommissionep = $this->Dossierep->Passagecommissionep->find(
						'first',
						array(
							'conditions' => array(
								'Passagecommissionep.dossierep_id IN ( '.$this->Dossierep->sq(
									array(
										'fields' => array(
											'dossierseps.id'
										),
										'alias' => 'dossierseps',
										'conditions' => array(
											'dossierseps.themeep' => 'defautsinsertionseps66'
										),
										'joins' => array(
											array(
												'table' => 'defautsinsertionseps66',
												'alias' => 'defautsinsertionseps66',
												'type' => 'INNER',
												'conditions' => array(
													'defautsinsertionseps66.dossierep_id = dossierseps.id',
													'defautsinsertionseps66.bilanparcours66_id' => $id
												)
											)
										)
									)
								).' )',
                                'Passagecommissionep.id IN ('.$this->Dossierep->Passagecommissionep->sqDernier().' )'
							),
							'contain' => array(
								'Commissionep',
								'Decisiondefautinsertionep66' => array(
									'order' => array( 'Decisiondefautinsertionep66.etape ASC' )
								),
                                'Dossierep'
							)
						)
					);

					$dossierpcg66 = $this->Bilanparcours66->Dossierpcg66->find(
						'first',
						array(
							'conditions' => array(
								'Dossierpcg66.bilanparcours66_id' => $id
							),
							'contain' => array(
								'Decisiondossierpcg66' => array(
                                    'order' => array( 'Decisiondossierpcg66.created DESC' ),
                                    'conditions' => array(
                                        'Decisiondossierpcg66.validationproposition' => 'O',
                                        'Decisiondossierpcg66.etatop' => 'transmis'
                                    ),
									'Decisionpdo'
								)
							)
						)
					);

					$this->set( compact( 'passagecommissionep', 'dossierpcg66' ) );
				}
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

            $dossier_id = $this->Bilanparcours66->Personne->dossierId( $personne_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

            $this->Jetons2->get( $dossier_id );

            // Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
                $this->Jetons2->release( $dossier_id );
                if( $this->action == 'edit' ) {
                    $bilanparcours66 = $this->Bilanparcours66->find(
                        'first',
                        array(
                            'contain' => false,
                            'conditions' => array( 'Bilanparcours66.id' => $id )
                        )
                    );
                    $personne_id = Set::classicExtract( $bilanparcours66, 'Bilanparcours66.personne_id' );
                    $id = $personne_id;
                }
                $this->redirect( array( 'action' => 'index', $id ) );
			}

			// INFO: pour passer de 74 à 29 modèles utilisés lors du find count
			$this->Bilanparcours66->Personne->unbindModelAll();

			// On s'assure que la personne existe bien
			$nPersonnes = $this->Bilanparcours66->Personne->find(
				'count',
				array(
					'contain' => false,
					'conditions' => array( 'Personne.id' => $personne_id )
				)
			);
			$this->assert( ( $nPersonnes == 1 ), 'error404' );



            // Nombre de mois cumulés pour la contractualisation
            $nbCumulDureeCER66 = $this->Bilanparcours66->Contratinsertion->WebrsaContratinsertion->limiteCumulDureeCER( $personne_id );
            $this->set('nbCumulDureeCER66', $nbCumulDureeCER66);

            // On récupère l'utilisateur connecté et qui exécute l'action
			$userConnected = $this->Session->read( 'Auth.User.id' );
			$this->set( compact( 'userConnected' ) );
			// On récupère le service instructeur de l'utilisateur connecté
			$user = $this->Bilanparcours66->User->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => $userConnected
					),
					'contain' => array(
						'Serviceinstructeur'
					),
					'recursive' => -1
				)
			);
			$serviceinstruceteurUser = Set::classicExtract( $user, 'User.serviceinstructeur_id' );
			$this->set( 'serviceinstruceteurUser', $serviceinstruceteurUser );

			// Si le formulaire a été renvoyé
			if( !empty( $this->request->data ) ) {
				// Si le bilan est traité, on s'assure que l'information soit bien dans $this->request->data
				if ( isset($bilanparcours66['Bilanparcours66']['positionbilan']) && $bilanparcours66['Bilanparcours66']['positionbilan'] === 'traite' ) {
					$this->request->data['Bilanparcours66']['positionbilan'] = 'traite';
				}

				//On regarde si un CER est lié au bilan sans passage en EP
				if( $this->action == 'edit' ) {
					$this->Bilanparcours66->id = $this->request->data['Bilanparcours66']['id'];
					$nvcontratinsertionId = $this->Bilanparcours66->field( 'nvcontratinsertion_id' );
					$proposition = $this->Bilanparcours66->field( 'proposition' );
				}

				//On regarde si un dossier EP pour passage en EP Parcours existe pour ce bilan
				$dossierepParcours = $this->Dossierep->Saisinebilanparcoursep66->find(
					'first',
					array(
						'conditions' => array(
							'Saisinebilanparcoursep66.bilanparcours66_id' => $id
						)
					)
				);
				//On regarde si un dossier EP pour passage en EP Audition existe pour ce bilan
				$dossierepAudition = $this->Dossierep->Defautinsertionep66->find(
					'first',
					array(
						'conditions' => array(
							'Defautinsertionep66.bilanparcours66_id' => $id
						)
					)
				);

                $this->Bilanparcours66->begin();

				//Avec passage en EP
                if ( ( !isset( $passagecommissionep ) || empty( $passagecommissionep ) ) && ( !empty( $dossierepAudition ) || !empty( $dossierepParcours ) ) && ( $this->action == 'edit' ) && empty( $nvcontratinsertionId ) ) {

					if ( !empty( $dossierepParcours ) ) {
						$this->Dossierep->Saisinebilanparcoursep66->deleteAll( array( 'Saisinebilanparcoursep66.bilanparcours66_id' => $id ) );
						$this->Dossierep->delete( $dossierepParcours['Saisinebilanparcoursep66']['dossierep_id'] );
					}
					else {
						$this->Dossierep->Defautinsertionep66->deleteAll( array( 'Defautinsertionep66.bilanparcours66_id' => $id ) );
						$this->Dossierep->delete( $dossierepAudition['Defautinsertionep66']['dossierep_id'] );
					}

					$success = $this->Bilanparcours66->WebrsaBilanparcours66->sauvegardeBilan( $this->request->data );
// debug($success);
				}
				// Sans passage en EP
				elseif ( ( $this->action == 'edit' ) && !empty( $nvcontratinsertionId ) ) {
					//Suppression du CER tacitement reconduit si on modifie et que l'on passe finalement l'allocataire en EP
					$success = true;

					$propositionModifie = $this->request->data['Bilanparcours66']['proposition'];
					if( $proposition != $propositionModifie ) {
						$success = $this->Bilanparcours66->Contratinsertion->delete( $nvcontratinsertionId, true ) && $success;
						foreach( array( 'typeorientprincipale_id', 'nvcontratinsertion_id', 'duree_engag', 'ddreconductoncontrat', 'dfreconductoncontrat', 'nvtypeorient_id', 'nvstructurereferente_id' ) as $field ) {
							$this->request->data['Bilanparcours66'][$field] = null;
						}

						$success = $this->Bilanparcours66->WebrsaBilanparcours66->sauvegardeBilan( $this->request->data ) && $success;
					}
					else {
						$success = $this->Bilanparcours66->save( $this->request->data ) && $success;
					}
				}
				elseif ( ( $this->action == 'edit' ) && empty( $nvcontratinsertionId ) ) {
					if( isset( $this->request->data['Pe']['Bilanparcours66']['proposition'] ) ) {
						$propositionModifie = $this->request->data['Pe']['Bilanparcours66']['proposition'];
					}
					else {
						$propositionModifie = $this->request->data['Bilanparcours66']['proposition'];
					}

					if( !in_array( $propositionModifie, array( 'aucun' ) ) && ( $proposition != $propositionModifie) ) {
						$success = $this->Bilanparcours66->WebrsaBilanparcours66->sauvegardeBilan( $this->request->data );
					}
					else {
						$success = $this->Bilanparcours66->save( $this->request->data );
					}
				}
				elseif ( $this->action == 'add' ) {
					$success = $this->Bilanparcours66->WebrsaBilanparcours66->sauvegardeBilan( $this->request->data );
				}

				$this->_setFlashResult( 'Save', $success );
				if( $success ) {
					$this->Bilanparcours66->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );

					if ( isset( $this->request->data['Bilanparcours66']['proposition'] ) && $this->request->data['Bilanparcours66']['proposition'] == 'traitement' && isset( $this->request->data['Bilanparcours66']['maintienorientation'] ) && $this->request->data['Bilanparcours66']['maintienorientation'] == 1 ) {
						$this->redirect( array( 'controller' => 'contratsinsertion', 'action' => 'index', $personne_id ) );
					}
					else {
						$this->redirect( array( 'controller' => 'bilansparcours66', 'action' => 'index', $personne_id ) );
					}

				}
				else {
					$this->Bilanparcours66->rollback();
                    $this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			// Premier accès à la page
			else {
				if( $this->action == 'edit' ) {
					$bilanparcours66['Bilanparcours66']['referent_id'] = $bilanparcours66['Bilanparcours66']['structurereferente_id'].'_'.$bilanparcours66['Bilanparcours66']['referent_id'];

					$this->request->data = $bilanparcours66;
				}
				else {
					$orientstruct = $this->Bilanparcours66->Orientstruct->find(
						'first',
						array(
							'fields' => array(
								'Orientstruct.id',
								'Orientstruct.personne_id',
								'Orientstruct.structurereferente_id',//ajout arnaud
							),
							'conditions' => array(
								'Orientstruct.personne_id' => $personne_id,
								'Orientstruct.date_valid IS NOT NULL'
							),
							'contain' => array(
								'Structurereferente',
								'Referent'
							),
							'order' => array( 'Orientstruct.date_valid DESC' )
						)
					);

					if( !empty(  $orientstruct ) ){
						$this->request->data['Bilanparcours66']['orientstruct_id'] = $orientstruct['Orientstruct']['id'];
						//ajout arnaud
						$this->request->data['Bilanparcours66']['structurereferente_id'] = $orientstruct['Orientstruct']['structurereferente_id'];
						if( !empty( $orientstruct['Orientstruct']['referent_id'] ) ) {
							$this->request->data['Bilanparcours66']['referent_id'] = $orientstruct['Orientstruct']['referent_id'];
						}
					}
				}

				$this->request->data = Hash::insert($this->request->data, 'Pe', $this->request->data);
			}

			if (!isset($this->request->data['Bilanparcours66']['sitfam']) || empty($this->request->data['Bilanparcours66']['sitfam'])) {
				$sitfam = $this->Bilanparcours66->Personne->Foyer->find(
					'first',
					array(
						'fields' => array(
							'Foyer.id',
							'Foyer.sitfam'
						),
						'joins' => array(
							array(
								'table' => 'personnes',
								'alias' => 'Personne',
								'type' => 'INNER',
								'foreignKey' => false,
								'conditions' => array(
									'Personne.foyer_id = Foyer.id',
									'Personne.id' => $personne_id
								)
							)
						),
						'contain'=>false
					)
				);
				$nbenfant = $this->Bilanparcours66->Personne->Foyer->nbEnfants($sitfam['Foyer']['id']);
				///FIXME: voir si isolement correspond à l'isolement prévu dans la table foyer
				//if ($sitfam['Foyer']['sitfam'] == 'ISO') {
				if (in_array($sitfam['Foyer']['sitfam'], array('CEL', 'DIV', 'ISO', 'SEF', 'SEL', 'VEU'))) {
					if ($nbenfant==0) {
						$this->request->data['Bilanparcours66']['sitfam']='isole';
					}
					else {
						$this->request->data['Bilanparcours66']['sitfam']='isoleenfant';
					}
				}
				elseif (in_array($sitfam['Foyer']['sitfam'], array('MAR', 'PAC', 'RPA', 'RVC', 'VIM'))) {
					if ($nbenfant==0) {
						$this->request->data['Bilanparcours66']['sitfam']='couple';
					}
					else {
						$this->request->data['Bilanparcours66']['sitfam']='coupleenfant';
					}
				}
			}

			// INFO: si on utilise fields pour un modèle (le modèle principal ?), on n'a pas la relation belongsTo (genre Foyer belongsTo Dossier)
			// INFO: http://stackoverflow.com/questions/3865349/containable-fails-to-join-in-belongsto-relationships-when-fields-are-used-in-ca
			// http://cakephp.lighthouseapp.com/projects/42648/tickets/1174-containable-fails-to-join-in-belongsto-relationships-when-fields-are-used
			// Recherche des informations de la personne
			$personne = $this->Bilanparcours66->Personne->find(
				'first',
				array(
					'conditions' => array( 'Personne.id' => $personne_id ),
					'contain' => array(
						'Orientstruct' => array(
							'fields' => array( 'typeorient_id', 'structurereferente_id', 'date_valid' ),
							'Typeorient' => array(
								'fields' => array(
									'lib_type_orient'
								)
							),
							'order' => "Orientstruct.date_valid DESC",
							'conditions' => array( 'Orientstruct.statut_orient' => 'Orienté' ),
						),
						'Foyer' => array(
							'fields' => array(
								'id'
							),
							'Adressefoyer' => array(
								'conditions' => array(
									'Adressefoyer.rgadr' => '01'
								),
								'Adresse' => array(
									'fields' => array(
										'numvoie',
										'libtypevoie',
										'nomvoie',
										'codepos',
										'nomcom'
									)
								)
							),
							'Dossier' => array(
								'fields' => array(
									'numdemrsa',
									'matricule',
								)
							),
							'Modecontact' => array(
								'fields' => array(
									'autorutitel',
									'numtel',
									'autorutiadrelec',
									'adrelec'
								)
							)
						),
						'Prestation' => array(
							'fields' => array(
								'rolepers'
							)
						)
					)
				)
			);

			//Précochage du bouton radio selon l'origine du bilan de parcours ou le type d'orientation de l'allocataire
			if( $this->action == 'add' ) {
				if( isset( $bilanparcours66 ) && in_array( $bilanparcours66['Bilanparcours66']['examenauditionpe'], array( 'noninscriptionpe', 'radiationpe' ) ) ) {
					$typeformulaire = 'cg';
				}
				else {
					$typeformulaire = 'cg';
					$orientation = $this->Bilanparcours66->Orientstruct->find(
						'first',
						array(
							'conditions' => array(
								'Orientstruct.personne_id' => $personne_id,
								'Orientstruct.statut_orient' => 'Orienté'
							),
							'contain' => array(
								'Typeorient'
							),
							'order' => array( 'Orientstruct.date_valid DESC' )
						)
					);
					$typeorient_id = Set::classicExtract( $orientation, 'Typeorient.id' );
					if( !empty( $typeorient_id ) ) {
						if( $this->Bilanparcours66->Orientstruct->Typeorient->isProOrientation($typeorient_id) && ( !isset( $this->request->params['named'] ) || empty( $this->request->params['named'] ) ) ){
							$typeformulaire = 'pe';
						}
					}
				}
			}
			else {
				$typeformulaire = $bilanparcours66['Bilanparcours66']['typeformulaire'];
			}

			$entretiens = $this->Bilanparcours66->Personne->Entretien->find( 'all', $this->Bilanparcours66->Personne->Entretien->queryEntretiens( $personne_id ) );

			$contratsinsertion = $this->Bilanparcours66->Personne->Contratinsertion->find(
				'first',
				array(
					'fields' => array(
						'("Contratinsertion".structurereferente_id || \'_\' || "Contratinsertion".referent_id) AS "Contratinsertion__referent_id"',
						'Contratinsertion.sitfam_ci',
						'Contratinsertion.sitpro_ci',
						'Contratinsertion.observ_benef',
						'Contratinsertion.nature_projet',
						'Contratinsertion.duree_engag',
						'Contratinsertion.dd_ci',
						'Contratinsertion.df_ci',
					),
					'contain' => false,
					'conditions' => array(
						'Contratinsertion.personne_id' => $personne_id
					),
					'order' => array(
						'Contratinsertion.dd_ci' => 'DESC',
						'Contratinsertion.id' => 'DESC',
					)
				)
			);
			$this->set( compact( 'contratsinsertion', 'entretiens' ) );

			/// Si le nombre de dossiers d'EP en cours est > 0,
			/// alors on ne peut pas créer de bilan pour la thématique concernée par le dossier EP
			$dossiersepsencours = array(
				'defautsinsertionseps66' => !$this->Bilanparcours66->WebrsaBilanparcours66->ajoutPossibleThematique66( 'defautsinsertionseps66', $personne_id ),
				'saisinesbilansparcourseps66' => !$this->Bilanparcours66->WebrsaBilanparcours66->ajoutPossibleThematique66( 'saisinesbilansparcourseps66', $personne_id )
			);

			$this->set( 'typevoie', $this->Option->typevoie() );
			$this->set( 'rolepers', ClassRegistry::init('Prestation')->enum('rolepers') );
			$this->set( 'qual', $this->Option->qual() );
			$this->set( 'nationalite', ClassRegistry::init('Personne')->enum('nati') );
			$this->set( 'typeformulaire', $typeformulaire );
			$this->set( compact( 'dossiersepsencours' ) );
			$this->set( 'urlmenu', '/bilanspourcours66/index/'.$personne_id );

			$this->set( compact( 'personne' ) );
			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		/**
		 * Fonction pour annuler le Bilan de parcours pour le CG66
		 *
		 * @param integer $id
		 */
		public function cancel( $id ) {
			$this->WebrsaAccesses->check($id);
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Bilanparcours66->personneId( $id ) ) ) );

			$qd_bilan = array(
				'conditions' => array(
					$this->modelClass.'.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$bilan = $this->{$this->modelClass}->find( 'first', $qd_bilan );
			$personne_id = Set::classicExtract( $bilan, 'Bilanparcours66.personne_id' );
			$this->set( 'personne_id', $personne_id );

			$dossier_id = $this->Bilanparcours66->dossierId( $id );
			$this->Jetons2->get( $dossier_id );

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Bilanparcours66->begin();

				// On cherche le dossier EP créé par le bilan de parcours lors de l'enregistrement
				// Dans un premier temps, on regarde si le dossier EP est lié à une demande de réorientation (saisinebilanparcoursep66)
				$dossierep = $this->Dossierep->Saisinebilanparcoursep66->find(
					'first',
					array(
						'conditions' => array(
							'Saisinebilanparcoursep66.bilanparcours66_id' => $id
						)
					)
				);
				// Si c'est le cas, on supprime le dossier d'EP dans la thématique en question
				if ( !empty( $dossierep ) ) {
					$this->Dossierep->Saisinebilanparcoursep66->deleteAll( array( 'Saisinebilanparcoursep66.bilanparcours66_id' => $id ) );
					$this->Dossierep->delete( $dossierep['Saisinebilanparcoursep66']['dossierep_id'] );
				}
				// Sinon on cherche dans l'autre thématique (defautinsertionep66) et on supprime le dossier d'EP
				else {
					$dossierep = $this->Dossierep->Defautinsertionep66->find(
						'first',
						array(
							'conditions' => array(
								'Defautinsertionep66.bilanparcours66_id' => $id
							)
						)
					);
					if( !empty( $dossierep ) ) {
						$this->Dossierep->Defautinsertionep66->deleteAll( array( 'Defautinsertionep66.bilanparcours66_id' => $id ) );
						$this->Dossierep->delete( $dossierep['Defautinsertionep66']['dossierep_id'] );
					}
				}


				$saved = $this->Bilanparcours66->save( $this->request->data );
				$saved = $this->{$this->modelClass}->updateAllUnBound(
					array( 'Bilanparcours66.positionbilan' => '\'annule\'' ),
					array(
						'"Bilanparcours66"."personne_id"' => $bilan['Bilanparcours66']['personne_id'],
						'"Bilanparcours66"."id"' => $bilan['Bilanparcours66']['id']
					)
				) && $saved;

				if( $saved ) {
					$this->Bilanparcours66->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'action' => 'index', $personne_id ) );
				}
				else {
					$this->Bilanparcours66->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement.', 'flash/erreur' );
				}
			}
			else {
				$this->request->data = $bilan;
			}
			$this->set( 'urlmenu', '/bilansparcours66/index/'.$personne_id );
		}

		/**
		 *
		 * @param integer $id
		 */
		public function impression( $id ) {
			$this->WebrsaAccesses->check($id);
			$this->assert( !empty( $id ), 'error404' );

			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Bilanparcours66->personneId( $id ) ) );

			$pdf = $this->Bilanparcours66->WebrsaBilanparcours66->getDefaultPdf( $id );

			$this->Gedooo->sendPdfContentToClient( $pdf, "Bilanparcours-{$id}.pdf" );
		}

		/**
		 * Visualisation du Bilan de parcours 66
		 *
		 * @param integer $bilanparcours66_id
		 * @return void
		 */
		public function view( $bilanparcours66_id ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Bilanparcours66->personneId( $bilanparcours66_id ) ) ) );

			$this->Bilanparcours66->id = $bilanparcours66_id;
			$personne_id = $this->Bilanparcours66->field( 'personne_id' );
			$this->set( 'personne_id', $personne_id );

            $this->_setOptions($this->Bilanparcours66->WebrsaBilanparcours66->optionsView());
//            $this->set( 'options',$this->Bilanparcours66->WebrsaBilanparcours66->optionsView() );
			$this->set( 'bilanparcours66', $this->Bilanparcours66->WebrsaBilanparcours66->dataView( $bilanparcours66_id ) );
			$this->set( 'urlmenu', "/bilansparcours66/index/{$personne_id}" );
		}

		/**
		 * Moteur de recherche
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesBilansparcours66' );
			$Recherches->search();
			$this->Bilanparcours66->validate = array();
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesBilansparcours66' );
			$Recherches->exportcsv();
		}
	}
?>