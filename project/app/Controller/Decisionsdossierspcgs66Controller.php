<?php
	/**
	 * Code source de la classe Decisionsdossierspcgs66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Decisionsdossierspcgs66Controller permet de gérer les décisions
	 * d'un dossier PCG 66
	 *
	 * @package app.Controller
	 */
	class Decisionsdossierspcgs66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Decisionsdossierspcgs66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'DossiersMenus',
			'Fileuploader',
			'Gedooo.Gedooo',
			'Jetons2',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Default2',
			'Fileuploader',
			'Locale',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			)
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Decisiondossierpcg66',
			'Option',
			'Pdf',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'view' => 'Dossierspcgs66:index'
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajaxfiledelete',
			'ajaxfileupload',
			'ajaxproposition',
			'download',
			'fileview'
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
			'ajaxproposition' => 'read',
			'avistechnique' => 'update',
			'cancel' => 'update',
			'decisionproposition' => 'update',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'filelink' => 'read',
			'fileview' => 'read',
			'transmitop' => 'update',
			'validation' => 'update',
			'view' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			$options = $this->Decisiondossierpcg66->enums();
			$options = array_merge( $options, $this->Decisiondossierpcg66->Dossierpcg66->enums() );
			$options = array_merge(
				$options, $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Traitementpcg66->Decisiontraitementpcg66->enums(), $this->Decisiondossierpcg66->Dossierpcg66->Decisiondefautinsertionep66->enums(), $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Traitementpcg66->enums(), $this->Decisiondossierpcg66->Dossierpcg66->Contratinsertion->enums()
			);
			$typersapcg66 = $this->Decisiondossierpcg66->Typersapcg66->find( 'list' );
			$orgtransmisdossierpcg66 = $this->Decisiondossierpcg66->Orgtransmisdossierpcg66->findForTraitement( 'list' );

			$forme_ci = array( 'S' => 'Simple', 'C' => 'Particulier' );
			$compofoyerpcg66 = $this->Decisiondossierpcg66->Compofoyerpcg66->find( 'list' );

			$query = array(
				'fields' => array(
					'Decisionpdo.id',
					'Decisionpdo.libelle',
					'Decisionpdo.decisioncerparticulier',
					'Decisionpdo.isactif'
				),
				'conditions' => array(
					'Decisionpdo.cerparticulier' => 'O'
				),
				'contain' => false
			);
			$decisionspcgsCer = $this->Decisiondossierpcg66->Decisionpdo->findForTraitement( 'all', $query );
			$listdecisionpcgCer = Hash::combine( $decisionspcgsCer, '{n}.Decisionpdo.id', '{n}.Decisionpdo.libelle' );

			// Récupération des IDs de décisions PDO qui correspondent à une non validation du CER Particulier
			$idsDecisionNonValidCer = array( );
			foreach( $decisionspcgsCer as $decisionpcgCer ) {
				if( $decisionpcgCer['Decisionpdo']['decisioncerparticulier'] == 'N' ) {
					$idsDecisionNonValidCer[] = $decisionpcgCer['Decisionpdo']['id'];
				}
			}

			$listMotifs = $this->Decisiondossierpcg66->Dossierpcg66->Contratinsertion->Propodecisioncer66->Motifcernonvalid66->find( 'list' );
			$this->set( compact( 'listMotifs' ) );

			$this->set( compact( 'options', 'typersapcg66', 'compofoyerpcg66', 'forme_ci', 'listdecisionpcgCer', 'idsDecisionNonValidCer', 'orgtransmisdossierpcg66') );
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
		 * @param type $id
		 */
		public function fileview( $id ) {
			$this->Fileuploader->fileview( $id );
		}

		/**
		 * Téléchargement des fichiers préalablement associés à un traitement donné
		 *
		 * @param type $fichiermodule_id
		 */
		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}

		/**
		 * Fonction permettant d'accéder à la page pour lier les fichiers au CER
		 *
		 * @param type $id
		 */
		public function filelink( $id ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $this->Decisiondossierpcg66->dossierId( $id ) ) ) );

			$fichiers = array( );
			$decisiondossierpcg66 = $this->Decisiondossierpcg66->find(
				'first',
				array(
					'conditions' => array(
						'Decisiondossierpcg66.id' => $id
					),
					'contain' => array(
						'Fichiermodule' => array(
							'fields' => array( 'name', 'id', 'created', 'modified' )
						),
						'Dossierpcg66'
					)
				)
			);

			$dossierpcg66_id = $decisiondossierpcg66['Decisiondossierpcg66']['dossierpcg66_id'];
			$dossier_id = $this->Decisiondossierpcg66->Dossierpcg66->dossierId( $dossierpcg66_id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'controller' => 'dossierspcgs66', 'action' => 'edit', $dossierpcg66_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Decisiondossierpcg66->begin();

				$saved = $this->Decisiondossierpcg66->updateAllUnBound(
					array( 'Decisiondossierpcg66.haspiecejointe' => '\''.$this->request->data['Decisiondossierpcg66']['haspiecejointe'].'\'' ),
					array(
						'"Decisiondossierpcg66"."dossierpcg66_id"' => $dossierpcg66_id,
						'"Decisiondossierpcg66"."id"' => $id
					)
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, !Set::classicExtract( $this->request->data, "Decisiondossierpcg66.haspiecejointe" ), $id ) && $saved;
				}

				if( $saved ) {
					$this->Decisiondossierpcg66->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( $this->referer() );
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Decisiondossierpcg66->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			$this->_setOptions();
			$this->set( 'dossier_id', $dossier_id);
			$this->set( compact( 'personne_id', 'fichiers', 'decisiondossierpcg66' ) );
			$this->set( 'urlmenu', '/dossierspcgs66/edit/'.$dossierpcg66_id );
		}

		/**
		 * Affichage de la proposition du
		 */
		public function ajaxproposition() {
			Configure::write( 'debug', 0 );

			$decisionpcg66_id = Set::extract( $this->request->params, 'form.decisionpcg66_id' );

			$data = array(
				'defautinsertion' => Set::extract( $this->request->params, 'form.defautinsertion' ),
				'compofoyerpcg66_id' => Set::extract( $this->request->params, 'form.compofoyerpcg66_id' ),
				'recidive' => Set::extract( $this->request->params, 'form.recidive' ),
				'phase' => Set::extract( $this->request->params, 'form.phase' )
			);

			$questionspcg = array( );

			$calculpossible = true;
			// Nous manque-t'il au moins une valeur permettant de faire le calcul ?
			foreach( $data as $key => $value ) {
				if( is_null( $value ) || $value == '' ) {
					$calculpossible = false;
				}
			}

			// On a toutes les valeurs nécessaires pour faire la calcul
			if( $calculpossible ) {
				$questionspcg = $this->Decisiondossierpcg66->Decisionpcg66->Questionpcg66->find(
						'list', array(
					'fields' => array( 'Decisionpcg66.id', 'Decisionpcg66.name' ),
					'conditions' => array(
						'Questionpcg66.defautinsertion' => $data['defautinsertion'],
						'Questionpcg66.compofoyerpcg66_id' => $data['compofoyerpcg66_id'],
						'Questionpcg66.recidive' => $data['recidive'],
						'Questionpcg66.phase' => $data['phase']
					),
					'contain' => false,
					'joins' => array(
						$this->Decisiondossierpcg66->Decisionpcg66->Questionpcg66->join( 'Decisionpcg66' )
					)
						)
				);
			}

			if( !empty( $decisionpcg66_id ) ) {
				$this->request->data['Decisiondossierpcg66']['decisionpcg66_id'] = $decisionpcg66_id;
			}

			$this->set( compact( 'questionspcg', 'calculpossible' ) );
			$this->render( 'ajaxproposition', 'ajax' );
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
		 * @param integer $id
		 */
		protected function _add_edit( $id = null ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );



			// Récupération des id afférents
			if( $this->action == 'add' ) {
				$dossierpcg66_id = $id;

				$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $this->Decisiondossierpcg66->Dossierpcg66->dossierId( $id ) ) ) );

				$dossierpcg66 = $this->Decisiondossierpcg66->Dossierpcg66->find(
					'first',
					array(
						'conditions' => array(
							'Dossierpcg66.id' => $id
						),
						'contain' => array(
							'Decisiondossierpcg66' => array(
								'Decisionpdo',
								'order' => array( 'Decisiondossierpcg66.created DESC' ),
							),
                            'Foyer' => array(
                                'Personne'
                            ),
							'Decisiondefautinsertionep66' => array(
								'Passagecommissionep' => array(
									'Dossierep' => array(
										'Defautinsertionep66' => array(
											'Bilanparcours66'
										)
									)
								)
							),
							'Fichiermodule',
							'Contratinsertion'
						)
					)
				);
				$this->set( 'dossierpcg66', $dossierpcg66 );

				$foyer_id = Set::classicExtract( $dossierpcg66, 'Dossierpcg66.foyer_id' );
				$dossier_id = $this->Decisiondossierpcg66->Dossierpcg66->Foyer->dossierId( $foyer_id );
			}
			else {
				$decisiondossierpcg66_id = $id;
				$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $this->Decisiondossierpcg66->dossierId( $id ) ) ) );

				$query = array(
					'contain' => array(
						'Typersapcg66',
						'Orgtransmisdossierpcg66',
						'Dossierpcg66' => array(
							'Contratinsertion' => array(
								'Propodecisioncer66' => array(
									'Motifcernonvalid66'
								)
							)
						),
						'Useravistechnique.nom_complet',
						'Userproposition.nom_complet'
					),
					'conditions' => array(
						'Decisiondossierpcg66.id' => $decisiondossierpcg66_id
					),
					'order' => array( 'Decisiondossierpcg66.created DESC' )
				);
				$decisiondossierpcg66 = $this->Decisiondossierpcg66->find( 'first', $query );
				$this->assert( !empty( $decisiondossierpcg66 ), 'invalidParameter' );

				$isvalidcer = Set::classicExtract( $decisiondossierpcg66, 'Propodecisioncer66.isvalidcer' );
				$this->set( compact( 'isvalidcer' ) );

				if( !empty( $isvalidcer ) && $isvalidcer == 'N' ) {
					$motifs = $this->Decisiondossierpcg66->Dossierpcg66->Contratinsertion->Propodecisioncer66->Motifcernonvalid66Propodecisioncer66->find(
							'all', array(
						'fields' => array(
							'Motifcernonvalid66Propodecisioncer66.motifcernonvalid66_id'
						),
						'conditions' => array(
							'Motifcernonvalid66Propodecisioncer66.propodecisioncer66_id' => $decisiondossierpcg66['Propodecisioncer66']['id']
						),
						'contain' => false
							)
					);

					$motifceronvalid66 = array( );
					foreach( $motifs as $key => $value ) {
						$motifceronvalid66[] = $value['Motifcernonvalid66Propodecisioncer66']['motifcernonvalid66_id'];
					}
					$decisiondossierpcg66['Motifcernonvalid66']['Motifcernonvalid66'] = $motifceronvalid66;
				}

				$dossierpcg66_id = Set::classicExtract( $decisiondossierpcg66, 'Decisiondossierpcg66.dossierpcg66_id' );
				// FIXME: une fonction avec la partie du add ci-dessus
				$dossierpcg66 = $this->Decisiondossierpcg66->Dossierpcg66->find(
					'first',
					array(
						'conditions' => array(
							'Dossierpcg66.id' => $dossierpcg66_id
						),
						'contain' => array(
							'Decisiondossierpcg66' => array(
								'conditions' => array( 'Decisiondossierpcg66.id <>' => $id ),
								'order' => array( 'Decisiondossierpcg66.created DESC' ),
								'Decisionpdo'
							),
                            'Foyer' => array(
                                'Personne'
                            ),
							'Decisiondefautinsertionep66' => array(
								'Passagecommissionep' => array(
									'Dossierep' => array(
										'Defautinsertionep66' => array(
											'Bilanparcours66'
										)
									)
								)
							),
							'Fichiermodule',
							'Contratinsertion'
						)
					)
				);


				$foyer_id = Set::classicExtract( $dossierpcg66, 'Dossierpcg66.foyer_id' );
				$dossier_id = $this->Decisiondossierpcg66->Dossierpcg66->Foyer->dossierId( $foyer_id );
			}

			$this->assert( !empty( $dossier_id ), 'invalidParameter' );

			$contratinsertion_id = null;
			if( !empty( $dossierpcg66['Dossierpcg66']['contratinsertion_id'] ) ) {
				$contratinsertion_id = $dossierpcg66['Dossierpcg66']['contratinsertion_id'];
			}
			$this->set( 'contratinsertion_id', $contratinsertion_id );

			if( !empty( $dossierpcg66['Decisiondefautinsertionep66']['decision'] ) ) {
				if( $dossierpcg66['Decisiondefautinsertionep66']['decision'] != 'maintien' ) {
					$decisiondossierpcg66_decision = $dossierpcg66['Decisiondefautinsertionep66']['decision'].'_'.$dossierpcg66['Decisiondefautinsertionep66']['Passagecommissionep']['Dossierep']['Defautinsertionep66']['Bilanparcours66']['proposition'];
				}
				else {
					$decisiondossierpcg66_decision = $dossierpcg66['Decisiondefautinsertionep66']['Passagecommissionep']['Dossierep']['Defautinsertionep66']['Bilanparcours66']['examenaudition'];
					$proposition = $dossierpcg66['Decisiondefautinsertionep66']['Passagecommissionep']['Dossierep']['Defautinsertionep66']['Bilanparcours66']['proposition'];
					if( $decisiondossierpcg66_decision == 'DOD' ) {
						$decisiondossierpcg66_decision = 'suspensiondefaut';
					}
					else {
						$decisiondossierpcg66_decision = 'suspensionnonrespect';
					}
					$decisiondossierpcg66_decision = $decisiondossierpcg66_decision.'_'.$proposition;
				}
				if( $decisiondossierpcg66_decision == 'suspensiondefaut_audition' ) {
					if( empty( $dossierpcg66['Decisiondefautinsertionep66']['Passagecommissionep']['Dossierep']['Defautinsertionep66']['Bilanparcours66']['orientstruct_id'] ) ) {
						$decisiondossierpcg66_decision = "{$decisiondossierpcg66_decision}_nonorientation";
					}
					else {
						$decisiondossierpcg66_decision = "{$decisiondossierpcg66_decision}_orientation";
					}
				}

				$this->set( 'decisiondossierpcg66_decision', $decisiondossierpcg66_decision ); // FIXME: pour le add
			}

			$this->set( 'dossier_id', $dossier_id );
			$this->set( 'dossierpcg66_id', $dossierpcg66_id );
			$this->set( 'foyer_id', $foyer_id );

			// On récupère l'utilisateur connecté et qui exécute l'action
			$userConnected = $this->Session->read( 'Auth.User.id' );
			$this->set( compact( 'userConnected' ) );

			$this->Jetons2->get( $dossier_id );




            // Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$dossier_id = $this->Decisiondossierpcg66->Dossierpcg66->Foyer->dossierId( $foyer_id );
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'controller' => 'dossierspcgs66', 'action' => 'edit', $dossierpcg66_id ) );
			}

			if( !empty( $this->request->data ) ) {
				// Lorsqu'on modifie l'enregistrement, on ne change pas l'utilisateur qui a fait la proposition
				if( $this->request->action !== 'add' && Hash::get( $decisiondossierpcg66, 'Decisiondossierpcg66.userproposition_id' ) !== null ) {
					unset($this->request->data['Decisiondossierpcg66']['userproposition_id']);
				}

				$this->Decisiondossierpcg66->begin();

				if( $this->Decisiondossierpcg66->saveAll( $this->request->data, array( 'validate' => 'only', 'atomic' => false ) ) ) {
					$saved = $this->Decisiondossierpcg66->save( $this->request->data , array( 'atomic' => false ) );
					if( !empty( $this->request->data['Decisiondossierpcg66Decisionpersonnepcg66'][0]['decisionpersonnepcg66_id'] ) ) {
						foreach( $this->request->data['Decisiondossierpcg66Decisionpersonnepcg66'] as $joinTable ) {
							if( isset( $this->request->data['Decisiondossierpcg66']['validationproposition'] ) && $this->request->data['Decisiondossierpcg66']['validationproposition'] == 'O' ) {
								$this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Personnepcg66Situationpdo->Decisionpersonnepcg66->id = $joinTable['decisionpersonnepcg66_id'] && $saved;
							}
							if( $this->action == 'add' ) {
								$joinTable['decisiondossierpcg66_id'] = $this->Decisiondossierpcg66->id;
								$this->Decisiondossierpcg66->Decisiondossierpcg66Decisionpersonnepcg66->create( $joinTable );
								$saved = $this->Decisiondossierpcg66->Decisiondossierpcg66Decisionpersonnepcg66->save( null, array( 'atomic' => false ) );
							}
						}
					}

					if( !empty( $dossierpcg66['Dossierpcg66']['contratinsertion_id'] ) ) {
						// Proposition de non validation
						if( $this->Decisiondossierpcg66->Dossierpcg66->Contratinsertion->Propodecisioncer66->saveAll( $this->request->data, array( 'validate' => 'only', 'atomic' => false ) ) ) {
							$saved = $this->Decisiondossierpcg66->Dossierpcg66->Contratinsertion->Propodecisioncer66->save( $this->request->data , array( 'atomic' => false ) );

							if( !isset( $this->request->data['Motifcernonvalid66'] ) && !empty( $propodecisioncer66 ) ) {
								$saved = $this->Decisiondossierpcg66->Dossierpcg66->Contratinsertion->Propodecisioncer66->Motifcernonvalid66Propodecisioncer66->deleteAll(
												array(
													'Motifcernonvalid66Propodecisioncer66.propodecisioncer66_id' => $this->Decisiondossierpcg66->Dossierpcg66->Contratinsertion->Propodecisioncer66->id
												)
										) && $saved;

								$saved = $this->Decisiondossierpcg66->Dossierpcg66->Contratinsertion->Propodecisioncer66->updateAllUnBound(
												array(
											'Propodecisioncer66.motifficheliaison' => null,
											'Propodecisioncer66.motifnotifnonvalid' => null
												), array(
											'Propodecisioncer66.id' => $this->Decisiondossierpcg66->Dossierpcg66->Contratinsertion->Propodecisioncer66->id
												)
										) && $saved;
							}
						}
					}
					//


					if( $saved ) {
						$saved = $this->Decisiondossierpcg66->Dossierpcg66->WebrsaDossierpcg66->updatePositionsPcgsById($dossierpcg66_id);
					}

					/**
					 * Si un traitement de type courrier a été crée le même jour, on le met dans la liste d'impression de la décision
					 */
					if ( $saved && Hash::get( $this->request->data, 'Decisiondossierpcg66.validationproposition' ) === 'O' ) {
						$listeDecisions = $this->Decisiondossierpcg66->Dossierpcg66->find( 'all',
							array(
								'fields' => array(
									'Decisiondossierpcg66.id',
									'Dossierpcg66.foyer_id',
									'Decisiondossierpcg66.created'
								),
								'contain' =>false,
								'joins' => array(
									$this->Decisiondossierpcg66->Dossierpcg66->join('Decisiondossierpcg66')
								),
								'conditions' => array(
									'Dossierpcg66.id' => Hash::get($decisiondossierpcg66, 'Dossierpcg66.id')
								)
							)
						);

						foreach ( Hash::extract( $listeDecisions, '{n}.Decisiondossierpcg66.id' ) as $key => $idsDossierspcgs66 ) {
							$traitementsCourrierMemeJour = $this->Decisiondossierpcg66->Dossierpcg66->Foyer->find( 'all',
								array(
									'fields' => 'Traitementpcg66.id',
									'contain' => false,
									'joins' => array(
										$this->Decisiondossierpcg66->Dossierpcg66->Foyer->join('Dossierpcg66', array('type' => 'INNER')),
										$this->Decisiondossierpcg66->Dossierpcg66->join('Decisiondossierpcg66', array('type' => 'LEFT')),
										$this->Decisiondossierpcg66->Dossierpcg66->join('Personnepcg66', array('type' => 'INNER')),
										$this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->join('Traitementpcg66', array('type' => 'INNER')),
									),
									'conditions' => array(
										'Foyer.id' => Hash::get($listeDecisions, $key.'.Dossierpcg66.foyer_id'),
										'OR' => array(
											'Decisiondossierpcg66.id IS NULL',
											'Decisiondossierpcg66.id' => $idsDossierspcgs66,
										),
										'Dossierpcg66.poledossierpcg66_id' => Hash::get($decisiondossierpcg66, 'Dossierpcg66.poledossierpcg66_id'),
										'Traitementpcg66.annule' => 'N',
										'Traitementpcg66.typetraitement' => 'courrier',
										'Traitementpcg66.etattraitementpcg' => 'contrôler',
									)
								)
							);

							if ( !empty($traitementsCourrierMemeJour) ) {
								$traitements_ids = array();
								foreach ( $traitementsCourrierMemeJour as $datas ) {
									$traitements_ids[] = Hash::get($datas, 'Traitementpcg66.id');
								}

								$saved = $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Traitementpcg66->updateAllUnbound(
									array( 'imprimer' => 1, 'etattraitementpcg' => "'imprimer'" ),
									array( 'Traitementpcg66.id' => $traitements_ids  )
								);
							}
						}
					}

                    // Clôture des traitements PCGs non clôturés, appartenant même à un autre dossier
                    // que celui auquel je suis lié
                    if( $saved && !empty( $this->request->data['Traitementpcg66']['Traitementpcg66'] ) ) {
                         $saved = $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Traitementpcg66->updateAllUnBound(
                            array( 'Traitementpcg66.clos' => '\'O\'' ),
                            array(
                                'Traitementpcg66.id' => $this->request->data['Traitementpcg66']['Traitementpcg66']
                            )
                        ) && $saved;
                    }

					if( $saved ) {
						$this->Decisiondossierpcg66->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'controller' => 'dossierspcgs66', 'action' => 'edit', $dossierpcg66_id ) );
					}
					else {
						$this->Decisiondossierpcg66->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
				else {
					$this->Decisiondossierpcg66->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else if( $this->action != 'add' ) {
				$this->request->data = $decisiondossierpcg66;

				// Récupération des types de RSA sélectionnés
				$typesrsapcg = $this->Decisiondossierpcg66->Decisiondossierpcg66Typersapcg66->find(
					'list',
					array(
						'fields' => array( "Decisiondossierpcg66Typersapcg66.id", "Decisiondossierpcg66Typersapcg66.typersapcg66_id" ),
						'conditions' => array(
							"Decisiondossierpcg66Typersapcg66.decisiondossierpcg66_id" => $decisiondossierpcg66_id
						)
					)
				);
				$this->request->data['Typersapcg66']['Typersapcg66'] = $typesrsapcg;

                if( !empty( $decisiondossierpcg66['Decisiondossierpcg66']['orgtransmisdossierpcg66_id'] ) ) {
                    $this->request->data['Decisiondossierpcg66']['orgtransmisdossierpcg66_id'] = $decisiondossierpcg66['Orgtransmisdossierpcg66']['poledossierpcg66_id'].'_'.$decisiondossierpcg66['Orgtransmisdossierpcg66']['id'];
                }
			}

            //Liste des personne sliées au traitement
			$personnespcgs66 = $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->find(
                'all', array(
                    'conditions' => array(
                        'Personnepcg66.dossierpcg66_id' => $dossierpcg66_id
                    ),
                    'contain' => array(
                        'Statutpdo',
                        'Situationpdo',
                        'Personne',
                        'Traitementpcg66' => array(
							'Personnepcg66' => array(
								'Personne'
							)
						)
                    )
                )
			);

			//Liste des traitements avec une fiche de calcul devant être reportée dans la décision
			$listeFicheAReporter = array( );
			foreach( $personnespcgs66 as $i => $personnepcg66 ) {
				if( !empty( $personnepcg66['Traitementpcg66'] ) ) {
					foreach( $personnepcg66['Traitementpcg66'] as $j => $traitementpcg66 ) {
						if( $traitementpcg66['reversedo'] == 1 && $traitementpcg66['annule'] != 'O' && $traitementpcg66['typetraitement'] == 'revenu'  ) {
							$listeFicheAReporter[] = $traitementpcg66;
						}
					}
				}
			}
			$this->set( compact( 'listeFicheAReporter' ) );


            //Liste des traitements non clos appartenant aux dossiers liés à mon Foyer
            $listeTraitementsNonClos = array();

            $personnesFoyerIds = Hash::extract( $dossierpcg66, 'Foyer.Personne.{n}.id' );
            $listeTraitementsNonClos = $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->listeTraitementpcg66NonClos( array_values( $personnesFoyerIds ), $this->action, $this->request->data );

			if (!empty($listeTraitementsNonClos)) {
				$listeTraitementsNonClos['Traitementpcg66']['autorisations'] = array(
					'printFicheCalcul' => array(),
					'printModeleCourrier' => array(),
				);
				$authorize =& $listeTraitementsNonClos['Traitementpcg66']['autorisations'];
				foreach (array_keys($listeTraitementsNonClos['Traitementpcg66']['traitementnonclosdecision']) as $traitement_id) {
					$traitementpcg66s = $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Traitementpcg66->find('first',
						array(
							'fields' => array(
								'Traitementpcg66.typetraitement'
							),
							'conditions' => array(
								'Traitementpcg66.id' => $traitement_id,
								'Traitementpcg66.annule !=' => 'O',
							),
							'contain' => false
						)
					);
					$authorize['printFicheCalcul'][$traitement_id] = Hash::get($traitementpcg66s, 'Traitementpcg66.typetraitement') === 'revenu';
					$authorize['printModeleCourrier'][$traitement_id] = Hash::get($traitementpcg66s, 'Traitementpcg66.typetraitement') === 'courrier';
				}
			}

            $this->set( 'listeTraitementsNonClos', $listeTraitementsNonClos );

			// avistechniquemodifiable, validationmodifiable
			$avistechniquemodifiable = $validationmodifiable = false;
			switch( $dossierpcg66['Dossierpcg66']['etatdossierpcg'] ) {
				case 'attval':
				case 'decisionvalid':
				case 'decisionnonvalid':
				case 'decisionnonvalidretouravis':
				case 'decisionvalidretouravis':
				case 'attpj':
				case 'atttransmisop':
                case 'transmisop':
					$validationmodifiable = ( $this->action != 'add' );
				case 'attavistech':
					$avistechniquemodifiable = ( $this->action != 'add' );
					break;
			}


			// Fichiers liés aux traitements de type document arrivé
			$fichiermoduleJoin = $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Traitementpcg66->join('Fichiermodule', array('type' => 'INNER'));
			$fichiermoduleJoin['conditions'] = array(
				'Fichiermodule.modele' => 'Traitementpcg66',
				'Fichiermodule.fk_value = Traitementpcg66.id',
			);

			$fichiersDocument = $this->Decisiondossierpcg66->Dossierpcg66->find('all',
				array(
					'fields' => array(
						'Fichiermodule.id',
						'Fichiermodule.name',
						'Fichiermodule.created',
					),
					'contain' => false,
					'joins' => array(
						$this->Decisiondossierpcg66->Dossierpcg66->join('Personnepcg66', array('type' => 'INNER')),
						$this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->join('Traitementpcg66',
							array(
								'type' => 'INNER',
								'conditions' => array(
									'Traitementpcg66.typetraitement' => 'documentarrive',
								)
							)
						),
						$fichiermoduleJoin
					),
					'conditions' => array(
						'Dossierpcg66.id' => $dossierpcg66_id
					)
				)
			);

			$this->set( compact( 'personnespcgs66', 'dossierpcg66', 'decisiondossierpcg66', 'avistechniquemodifiable', 'validationmodifiable', 'fichiersDocument' ) );

			// Options à envoyer à la vue
			$this->_setOptions();
			$options = $this->viewVars['options'];
			// Information transmise à...
			$query = array(
				'fields' => array(
					'Orgtransmisdossierpcg66.id',
					'Orgtransmisdossierpcg66.name',
					'Orgtransmisdossierpcg66.poledossierpcg66_id'
				),
				'conditions' => array(
					'Orgtransmisdossierpcg66.poledossierpcg66_id IS NOT NULL',
				)
			);
			$options['Decisiondossierpcg66']['orgtransmisdossierpcg66_id'] = Hash::combine(
				$this->Decisiondossierpcg66->Orgtransmisdossierpcg66->findForTraitement( 'all', $query ),
				array( '%d_%d', '{n}.Orgtransmisdossierpcg66.poledossierpcg66_id', '{n}.Orgtransmisdossierpcg66.id' ),
				'{n}.Orgtransmisdossierpcg66.name'
			);

			// Type de proposition
			$options['Decisiondossierpcg66']['decisionpdo_id'] = $this->Decisiondossierpcg66->Decisionpdo->findForTraitement( 'list' );

			// On complète les options le cas échéant
			// Information transmise à...
			$options = $this->Decisiondossierpcg66->Orgtransmisdossierpcg66->completeOptions(
				$options,
				$this->request->data,
				array(
					'Decisiondossierpcg66.orgtransmisdossierpcg66_id' => array(
						'prefix' => 'Orgtransmisdossierpcg66.poledossierpcg66_id'
					)
				)
			);

			// Type de proposition
			$options = $this->Decisiondossierpcg66->Decisionpdo->completeOptions(
				$options,
				$this->request->data,
				array( 'Decisiondossierpcg66.decisionpdo_id' )
			);

			$this->set( compact( 'options' ) );

			$this->set( 'urlmenu', '/dossierspcgs66/index/'.$foyer_id );
			$this->render( 'add_edit' );
		}

		/**
		 * Enregistrement du courrier de proposition lors de l'enregistrement de la proposition
		 *
		 * @param integer $id
		 * @deprecated since version 2.10
		 * @see Dossierspcgs66::imprimer()
		 */
		public function decisionproposition( $id ) {
			$this->assert( !empty( $id ), 'error404' );

			$this->DossiersMenus->checkDossierMenu( array( 'id' => $this->Decisiondossierpcg66->dossierId( $id ) ) );

			$pdf = $this->Decisiondossierpcg66->WebrsaDecisiondossierpcg66->getPdfDecision( $id );

			if( $pdf ) {
				$success = true;

				$query = array(
					'fields' => array(
						'Dossierpcg66.id',
						'Dossierpcg66.etatdossierpcg'
					),
					'conditions' => array(
						'Decisiondossierpcg66.id' => $id
					),
					'contain' => false,
					'joins' => array(
						$this->Decisiondossierpcg66->Dossierpcg66->join( 'Decisiondossierpcg66', array( 'type' => 'INNER' ) )
					)
				);
				$results = $this->Decisiondossierpcg66->Dossierpcg66->find( 'first', $query );

				$this->Decisiondossierpcg66->begin();

				// Si l'etat du dossier est decisionvalid on le passe en atttransmiop avec une date d'impression
				if ( Hash::get( $results, 'Dossierpcg66.etatdossierpcg' ) === 'decisionvalid' ) {
					$results['Dossierpcg66']['dateimpression'] = date('Y-m-d');
					$results['Dossierpcg66']['etatdossierpcg'] = 'atttransmisop';
					$success = $this->Decisiondossierpcg66->Dossierpcg66->save( $results['Dossierpcg66'], array( 'atomic' => false ) );
				}

				if( $success ) {
					$this->Decisiondossierpcg66->commit();
					$this->Gedooo->sendPdfContentToClient( $pdf, 'Décision.pdf' );
				}
				else {
					$this->Decisiondossierpcg66->rollback();
				}
			}

			$this->Flash->error( 'Impossible de générer la décision' );
			$this->redirect( $this->referer() );
		}

		/**
		 *
		 * @param integer $id
		 */
		public function view( $id ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $this->Decisiondossierpcg66->dossierId( $id ) ) ) );

			$query = array(
				'contain' => array(
					'Decisionpdo',
					'Dossierpcg66' => array(
						'Personnepcg66',
						'Foyer'
					),
					'Fichiermodule',
					'Orgtransmisdossierpcg66',
					'Notificationdecisiondossierpcg66' => array(
						'name',
						'order' => 'Notificationdecisiondossierpcg66.modified DESC',
						'limit' => 1
					),
					'Useravistechnique.nom_complet',
					'Userproposition.nom_complet'
				),
				'conditions' => array(
					'Decisiondossierpcg66.id' => $id
				)
			);
			$decisiondossierpcg66 = $this->Decisiondossierpcg66->find( 'first', $query );

			$this->assert( !empty( $decisiondossierpcg66 ), 'invalidParameter' );

			$this->set( 'dossier_id', $this->Decisiondossierpcg66->dossierId( $id ) );

			// Retour à la page d'édition de la PDO
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'dossierspcgs66', 'action' => 'edit', Set::classicExtract( $decisiondossierpcg66, 'Decisiondossierpcg66.dossierpcg66_id' ) ) );
			}

             // Liste des organismes auxquels on transmet le dossier
            if( !empty( $decisiondossierpcg66['Notificationdecisiondossierpcg66'] ) ) {
                $listOrgs = Hash::extract( $decisiondossierpcg66, 'Notificationdecisiondossierpcg66.{n}.name' );
                $orgs = implode( ', ',  $listOrgs );
            }

			$options = $this->Decisiondossierpcg66->enums();
			$this->set( compact( 'decisiondossierpcg66', 'orgs' ) );
			$this->_setOptions();

			$this->set( 'urlmenu', '/dossierspcgs66/index/'.$decisiondossierpcg66['Dossierpcg66']['foyer_id'] );
		}

		/**
		 * Suppression de la proposition de décision
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->DossiersMenus->checkDossierMenu( array( 'id' => $this->Decisiondossierpcg66->dossierId( $id ) ) );

			$decisiondossierpcg66 = $this->Decisiondossierpcg66->find(
					'first', array(
				'conditions' => array(
					'Decisiondossierpcg66.id' => $id
				),
				'contain' => array(
					'Dossierpcg66'
				)
					)
			);
			$dossierpcg66_id = Set::classicExtract( $decisiondossierpcg66, 'Dossierpcg66.id' );
			$etatdossierpcg = Set::classicExtract( $decisiondossierpcg66, 'Dossierpcg66.etatdossierpcg' );

			$success = $this->Decisiondossierpcg66->delete( $id );
			if( $success ) {
				$success = $this->Decisiondossierpcg66->Dossierpcg66->WebrsaDossierpcg66->updatePositionsPcgsById($dossierpcg66_id);
			}

			if( $success ) {
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Flash->error( __( 'Delete->error' ) );
			}
			$this->redirect( $this->referer() );
		}

		/**
		 * Gestion de la transmission à l'organisme payeur
		 *
		 * @param integer $id
		 */
		public function transmitop( $id ) {
			$this->assert( !empty( $id ), 'error404' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $this->Decisiondossierpcg66->dossierId( $id ) ) ) );

			$qd_decisiondossierpcg66 = array(
				'conditions' => array(
					'Decisiondossierpcg66.id' => $id
				),
				'contain' => array(
					'Notificationdecisiondossierpcg66'
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$decisiondossierpcg66 = $this->Decisiondossierpcg66->find( 'first', $qd_decisiondossierpcg66 );
			$this->set( 'decisiondossierpcg66', $decisiondossierpcg66 );
			$dossierpcg66_id = Set::classicExtract( $decisiondossierpcg66, 'Decisiondossierpcg66.dossierpcg66_id' );
			$this->set( 'dossierpcg66_id', $dossierpcg66_id );

			$dossierpcg66 = $this->Decisiondossierpcg66->Dossierpcg66->find(
				'first',
				array(
					'conditions' => array(
						'Dossierpcg66.id' => $dossierpcg66_id
					),
					'contain' => false
				)
			);

			$foyer_id = Set::classicExtract( $dossierpcg66, 'Dossierpcg66.foyer_id' );
			$this->set( 'foyer_id', $foyer_id );


			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'dossierspcgs66', 'action' => 'edit', $decisiondossierpcg66['Decisiondossierpcg66']['dossierpcg66_id'] ) );
			}

			$messages = array();
			if( !empty( $this->request->data ) ) {

				$this->Decisiondossierpcg66->begin();

				$saved = $this->Decisiondossierpcg66->save( $this->request->data , array( 'atomic' => false ) );
				$saved = $this->Decisiondossierpcg66->Dossierpcg66->WebrsaDossierpcg66->updatePositionsPcgsById($dossierpcg66_id) && $saved;
				$savedDossierGenere = $this->Decisiondossierpcg66->Dossierpcg66->WebrsaDossierpcg66->generateDossierPCG66Transmis( $dossierpcg66_id );
				if( false === $savedDossierGenere && false === empty( $this->Decisiondossierpcg66->Dossierpcg66->validationErrors ) ) {
					$query = array(
						'fields' => $this->Decisiondossierpcg66->Dossierpcg66->Poledossierpcg66->fields(),
						'joins' => array(
							$this->Decisiondossierpcg66->Dossierpcg66->join( 'Poledossierpcg66', array( 'type' => 'LEFT OUTER' ) )
						),
						'contain' => false,
						'conditions' => array(
							'Dossierpcg66.id' => $dossierpcg66_id
						)
					);
					$record = $this->Decisiondossierpcg66->Dossierpcg66->find( 'first', $query );

					// Vide dans Poledossierpcg66 ?
					$fieldNames = Hash::normalize( array_keys( $this->Decisiondossierpcg66->Dossierpcg66->validationErrors ) );
					foreach( array_keys( $fieldNames ) as $fieldName ) {
						if( true === empty( $record['Poledossierpcg66'][$fieldName] ) ) {
							$fieldNames[$fieldName] = sprintf( '« %s »', __d( 'poledossierpcg66', "Poledossierpcg66.{$fieldName}" ) );
						}
						else {
							unset( $fieldNames[$fieldName] );
						}
					}

					if( false === empty( $fieldNames ) ) {
						$msgid = 'Le(s) champ(s) suivant(s) sont vides dans le paramétrage du pôle « %s », impossible de créer automatiquement un nouveau dossier PCG : %s';
						$message = sprintf( $msgid, $record['Poledossierpcg66']['name'], implode( ', ', $fieldNames ) );
					} else {
						$message = 'Erreur inconnue lors de la tentative de création automatique d\'un dossier PCG';
					}
					$messages[$message] = 'error';
				}
				$saved = $savedDossierGenere && $saved;

				if( $saved ) {
					$this->Decisiondossierpcg66->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'dossierspcgs66', 'action' => 'edit', $decisiondossierpcg66['Decisiondossierpcg66']['dossierpcg66_id'] ) );
				}
				else {
					$this->Decisiondossierpcg66->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				$this->request->data = $decisiondossierpcg66;

				// Récupération des organismes sélectionnés
				$orgstransmisdossierspcgs66 = $this->Decisiondossierpcg66->Decdospcg66Orgdospcg66->find(
					'list',
					array(
						'fields' => array(
							"Decdospcg66Orgdospcg66.id",
							"Decdospcg66Orgdospcg66.orgtransmisdossierpcg66_id"
						),
						'conditions' => array(
                            "Decdospcg66Orgdospcg66.decisiondossierpcg66_id" => $id
						)
					)
				);
//
				$this->request->data['Notificationdecisiondossierpcg66']['Notificationdecisiondossierpcg66'] = $orgstransmisdossierspcgs66;
			}

            // Liste des Ids d'organisme enregistrés en lien avec la décision avant la désactivation de cet organisme
            $orgsIds = Hash::extract( $this->request->data, 'Notificationdecisiondossierpcg66.Notificationdecisiondossierpcg66' );

            $conditions = array();
            if( !empty( $orgsIds ) ) {
                $conditions = array(
                    'OR' => array(
                        $conditions,
                       array(
                           'Orgtransmisdossierpcg66.id' => $orgsIds
                       )
                    )
                );
            }

            $listeOrgstransmisdossierspcgs66 = $this->Decisiondossierpcg66->Orgtransmisdossierpcg66->findForTraitement(
                'list',
                array(
                    'conditions' => $conditions,
                    'order' => array( 'Orgtransmisdossierpcg66.name ASC' )
                )
            );

			$this->_setOptions();
            $this->set( compact( 'listeOrgstransmisdossierspcgs66', 'messages' ) );
			$this->set( 'urlmenu', '/dossierspcgs66/index/'.$foyer_id );
		}

		/**
		 * Affiche le formulaire d'ajout/modification selon l'état du dossier
		 * et selon le profil de l'utilisateur (avis technique)
		 * @param integer $id ID d'une décision liée au dossier PCG
		 *
		 */
		public function avistechnique( $id ) {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Affiche le formulaire d'ajout/modification selon l'état du dossier
		 * et selon le profil de l'utilisateur (validation après avis technique)
		 * @param integer $id ID d'une décision liée au dossier PCG
		 */
		public function validation( $id ) {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}


		/**
		 *
		 * @param integer $id
		 */
		public function cancel( $id ) {
			$qd_decisiondossierpcg66 = array(
				'conditions' => array(
					'Decisiondossierpcg66.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$decisiondossierpcg66 = $this->Decisiondossierpcg66->find( 'first', $qd_decisiondossierpcg66 );
			$dossierpcg66_id = Hash::get($decisiondossierpcg66, 'Decisiondossierpcg66.dossierpcg66_id');

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $this->Decisiondossierpcg66->dossierId( $id ) ) ) );

			$dossier_id = $this->Decisiondossierpcg66->dossierId( $id );
			$this->Jetons2->get( $dossier_id );

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'controller' => 'dossierspcgs66', 'action' => 'edit', $decisiondossierpcg66['Decisiondossierpcg66']['dossierpcg66_id'] ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Decisiondossierpcg66->begin();

				$saved = $this->Decisiondossierpcg66->save( $this->request->data , array( 'atomic' => false ) );
				$saved = $this->Decisiondossierpcg66->updateAllUnBound(
					array( 'Decisiondossierpcg66.etatdossierpcg' => '\'annule\'' ),
					array(
						'"Decisiondossierpcg66"."dossierpcg66_id"' => $decisiondossierpcg66['Decisiondossierpcg66']['dossierpcg66_id'],
						'"Decisiondossierpcg66"."id"' => $decisiondossierpcg66['Decisiondossierpcg66']['id']
					)
				) && $saved;

				if( $saved && $this->Decisiondossierpcg66->Dossierpcg66->WebrsaDossierpcg66->updatePositionsPcgsById($dossierpcg66_id) ) {
					$this->Decisiondossierpcg66->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'dossierspcgs66', 'action' => 'edit', $decisiondossierpcg66['Decisiondossierpcg66']['dossierpcg66_id'] ) );
				}
				else {
					$this->Decisiondossierpcg66->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				$this->request->data = $decisiondossierpcg66;
			}
		}
	}
?>