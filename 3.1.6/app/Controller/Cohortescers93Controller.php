<?php
	/**
	 * Code source de la classe Cohortescers93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Cohortescers93Controller permet ...
	 *
	 * @package app.Controller
	 */
	class Cohortescers93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cohortescers93';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'Cohortes' => array(
				'saisie',
				'avalidercpdv',
				'premierelecture',
				'validationcs',
				'validationcadre'
			),
			'Gedooo.Gedooo',
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Search.Filtresdefaut' => array(
				'saisie',
				'avalidercpdv',
				'premierelecture',
				'validationcs',
				'validationcadre',
				'visualisation'
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'saisie' => array(
						'filter' => 'Search'
					),
					'avalidercpdv' => array(
						'filter' => 'Search'
					),
					'premierelecture' => array(
						'filter' => 'Search'
					),
					'validationcs' => array(
						'filter' => 'Search'
					),
					'validationcadre' => array(
						'filter' => 'Search'
					),
					'visualisation' => array(
						'filter' => 'Search'
					)
				)
			),
			'WebrsaUsers',
			'Workflowscers93',
			
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Allocataires',
			'Cake1xLegacy.Ajax',
			'Checkboxes',
			'Csv',
			'Default2',
			'Search',
			'Xform',
			'Xhtml',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cohortecer93',
			'Contratinsertion',
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
			'avalidercpdv' => 'update',
			'exportcsv' => 'read',
			'impressionsDecisions' => 'update',
			'premierelecture' => 'read',
			'saisie' => 'read',
			'validationcadre' => 'update',
			'validationcs' => 'update',
			'visualisation' => 'read',
		);

		/**
		 * Moteur de recherche et traitement des enregistrements ligne par ligne
		 * (Ajax) pour la partie "2. Saisie d'un CER".
		 *
		 * @return void
		 */
		public function saisie() {
			$this->Workflowscers93->assertUserExterne();
			$structuresreferentes_ids = $this->Workflowscers93->getUserStructurereferenteId();

			$this->_traitercohorteajax( $structuresreferentes_ids, '01signe' );
		}

		/**
		 * Moteur de recherche pour la partie "3. Validation Responsable".
		 *
		 * @return void
		 */
		public function avalidercpdv() {
			$this->Workflowscers93->assertUserCpdv();

			$structuresreferentes_ids = $this->Workflowscers93->getUserStructurereferenteId();
			$this->_traitercohorteajax( $structuresreferentes_ids, '02attdecisioncpdv' );
		}

		/**
		 * Moteur de recherche et de traitement Ajax commun pour les étapes
		 * "2. Saisie d'un CER" et "3. Validation Responsable".
		 *
		 * @param integer $structuresreferentes_ids
		 * @param string $position
		 */
		protected function _traitercohorteajax( $structuresreferentes_ids, $position ) {
			$options = $this->_indexOptions( $structuresreferentes_ids );

			if( $this->action == 'saisie' ) {
				$histochoixcer93key = 'Histochoixcer93etape02';
			}
			else {
				$histochoixcer93key = 'Histochoixcer93';
			}

			if( !$this->request->is( 'ajax' ) ) {
				// On doit pouvoir obtenir les résultats dès le premier accès à la page
				if( !isset( $this->request->data['Search'] ) ) {
					$this->request->data = Set::merge(
						$this->Filtresdefaut->values(),
						array( 'Search' => array( 'active' => true ) )
					);
				}

				$querydata = $this->_qd( $this->request->data['Search'] );

                if( !empty( $structuresreferentes_ids ) ) {
                    $querydata['conditions']['Referent.structurereferente_id'] = $structuresreferentes_ids;
                }


				$this->paginate = $querydata;
				$cers93 = $this->paginate(
					$this->Contratinsertion->Personne,
					array(),
					array(),
					!Hash::get( $this->request->data, 'Search.Pagination.nombre_total' )
				);
				$cers93 = $this->_addCommentairenormecer93( $cers93, $histochoixcer93key );

				$dossiers_ids = Set::extract( $cers93, "/Cer93[positioncer={$position}]/../Dossier/id" );
				$this->Cohortes->get( $dossiers_ids );

				$this->set( compact( 'cers93', 'options' ) );

				if( $this->action == 'avalidercpdv' ) {
					// INFO: l'équivalent d'une fonction prepareFormDataAvaliderCpdv()
					$commentairesnormescers93_indexes_ids = array_keys( $options['commentairesnormescers93_list'] );
					$requestData = $this->request->data;
					$nbCommentairesnormescers93 = count( $commentairesnormescers93_indexes_ids );

					foreach( $cers93 as $index => $cer93 ) {
						$requestData['Histochoixcer93'][$index]['formeci'] = $cer93['Histochoixcer93']['formeci'];
						if( isset( $cer93['Histochoixcer93']['Commentairenormecer93Histochoixcer93'] ) && !empty( $cer93['Histochoixcer93']['Commentairenormecer93Histochoixcer93'] ) ) {
							foreach( $cer93['Histochoixcer93']['Commentairenormecer93Histochoixcer93'] as $commentaire ) {
								$j = ( $index * $nbCommentairesnormescers93 ) + array_search( $commentaire['commentairenormecer93_id'], $commentairesnormescers93_indexes_ids );
								$requestData['Commentairenormecer93']['Commentairenormecer93'][$j] = array(
									'commentairenormecer93_id' => $commentaire['commentairenormecer93_id'],
									'commentaireautre' => $commentaire['commentaireautre'],
								);
							}
						}
					}

					$this->request->data = $requestData;
				}
			}
			else {
				// TODO: avec les $keys etc ...
				$formData = Set::extract( $this->request->data, '/Histochoixcer93' );

				$key = array_keys( $this->request->data['Histochoixcer93'] );
				$key = $key[0];

				// TODO: array_move_key( array( 0 => 'Test' ), 0, 2 )
				if( $key != 0 ) {
					$formData[$key] = $formData[0];
					unset( $formData[0] );
				}

				if( isset( $this->request->data['Commentairenormecer93'] ) ) {
					$formData[$key]['Commentairenormecer93'] = $this->request->data['Commentairenormecer93'];
				}

				$this->Contratinsertion->begin();
					foreach( $formData as $key => $value ) {
						if( isset( $value['Commentairenormecer93']['Commentairenormecer93'] ) ) {
							$formData[$key]['Commentairenormecer93']['Commentairenormecer93'] = array_values($value['Commentairenormecer93']['Commentairenormecer93']);
						}
					}

				if( $this->action == 'saisie' ) {
					$success = $this->Contratinsertion->Cer93->Histochoixcer93->saveAll( $formData, array( 'validate' => 'first', 'atomic' => false ) );
					$success = $success && $this->Contratinsertion->Cer93->updateAllUnBound(
						array( '"Cer93"."positioncer"' => "'02attdecisioncpdv'" ),
						array( '"Cer93"."id"' => $formData[$key]['Histochoixcer93']['cer93_id'] )
					);
				}
				else if( $this->action == 'avalidercpdv' ) {
					// Ne pas oublier d'indiquer s'il s'agit ou non d'un rejet lorsqu'on est cohorte
					$formData[$key]['Histochoixcer93']['isrejet'] = ( ( $this->request->data['decision'] == '99rejetecpdv' ) ? '1' : '0' );

					$success = $this->Contratinsertion->Cer93->Histochoixcer93->saveAll( $formData, array( 'validate' => 'first', 'atomic' => false ) );
					$success = $success && $this->Contratinsertion->Cer93->updateAllUnBound(
						array( '"Cer93"."positioncer"' => "'".$this->request->data['decision']."'" ),
						array( '"Cer93"."id"' => $formData[$key]['Histochoixcer93']['cer93_id'] )
					);

					if( $this->request->data['decision'] == '99rejetecpdv' ) {
						$this->Contratinsertion->Cer93->id = $formData[$key]['Histochoixcer93']['cer93_id'];
						$contratinsertion_id = $this->Contratinsertion->Cer93->field( 'contratinsertion_id' );
						$success = $success && $this->Contratinsertion->updateAllUnBound(
							array(
								'"Contratinsertion"."rg_ci"' => null, //FIXME
								'"Contratinsertion"."decision_ci"' => "'R'",
								'"Contratinsertion"."datedecision"' => "'".date( 'Y-m-d' )."'",
							),
							array( '"Contratinsertion"."id"' => $contratinsertion_id )
						);
					}
				}

				if( $success ) {
					$this->Contratinsertion->commit();
					$dossier_id = $this->Contratinsertion->Cer93->dossierId( $formData[$key]['Histochoixcer93']['cer93_id'] );
					$this->Cohortes->release( $dossier_id );
				}
				else {
					$this->Contratinsertion->rollback();
				}

				$querydata = $this->_qd();
				$querydata['conditions']['Cer93.id'] = $formData[$key]['Histochoixcer93']['cer93_id'];
				$querydata['limit'] = 1;

				$cers93 = $this->Contratinsertion->Personne->find( 'all', $querydata );
				$cers93 = $this->_addCommentairenormecer93( $cers93, $histochoixcer93key );

				if( $this->action == 'saisie' ) {
					if( $key != 0 ) {
						$cers93[$key] = $cers93[0];
						unset( $cers93[0] );
					}
				}

				$this->set( compact( 'cers93', 'options' ) );

				$this->layout = null;
				$this->render( "{$this->action}_tbody_trs" );
			}
		}

		// TODO: à mettre dans le modèle
		protected function _qd( $search = array() ) {
			$querydata = $this->Cohortecer93->search(
				$this->action,
				$search
			);

			$querydata = $this->Gestionzonesgeos->qdConditions( $querydata );
			$querydata = $this->Cohortes->qdConditions( $querydata );

			// TODO: différencier CER / CER précédent ?
			if( $this->action == 'saisie' ) {
				$querydata['conditions'][] = array(
					array(
						'OR' => array(
							'Cer93.positioncer' => array( '00enregistre', '01signe', '02attdecisioncpdv', '99rejete', '99rejetecpdv' ),
							'Contratinsertion.id IS NULL',
							'Contratinsertion.df_ci <= DATE_TRUNC( \'day\', NOW() )',
							'Contratinsertion.df_ci - INTERVAL \''.Configure::read( 'Cohortescers93.saisie.periodeRenouvellement' ).'\' <= DATE_TRUNC( \'day\', NOW() )'
						)
					),
					array(
						'OR' => array(
							'PersonneReferent.referent_id' => $this->Session->read( 'Auth.User.referent_id' ),
							'PersonneReferent.structurereferente_id' => $this->WebrsaUsers->structuresreferentes()
						)
					)
				);
			}
			else if( $this->action == 'avalidercpdv' ) {
				$querydata['conditions'][] = array(
					'Cer93.positioncer' => '02attdecisioncpdv',
				);
			}

			return $querydata;
		}

		/**
		 *
		 * @param integer $structuresreferentes_ids
		 * @return array
		 */
		protected function _indexOptions( $structuresreferentes_ids ) {
			$options = array(
				'actions' => array( 'Activer' => 'Activer', 'Desactiver' => 'Désactiver' ),
				'cantons' => $this->Gestionzonesgeos->listeCantons(),
				'etatdosrsa' => ClassRegistry::init('Dossier')->enum('etatdosrsa'),
				'moticlorsa' => ClassRegistry::init('Situationdossierrsa')->enum('moticlorsa'),
				'exists' => array( '1' => 'Oui', '0' => 'Non' ),
				'mesCodesInsee' => $this->Gestionzonesgeos->listeCodesInsee(),
				'referents' => $this->InsertionsBeneficiaires->referents(
					array(
						'type' => 'optgroup',
						'conditions' => $this->InsertionsBeneficiaires->conditions['referents']
							+ (
								empty( $structuresreferentes_ids )
								? array()
								: array( 'Referent.structurereferente_id' => $structuresreferentes_ids )
							)
					)
				),
				'toppersdrodevorsa' => $this->Option->toppersdrodevorsa( true ),
				'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers'),
				'formeci' => ClassRegistry::init('Contratinsertion')->enum('forme_ci'),
				'gestionnaire' => ClassRegistry::init( 'User' )->find( 'list', array( 'fields' => array( 'User.nom_complet' ) ) )
			);
			$options['Adresse']['numcom'] = $options['mesCodesInsee'];
			$options['Adresse']['canton'] = $options['cantons'];
			$options = Set::merge(
				$options,
				$this->Contratinsertion->Cer93->enums(),
				$this->Contratinsertion->Cer93->Histochoixcer93->enums()
			);

			$options['Cer93']['positioncer'] = array_filter_keys( $options['Cer93']['positioncer'], array( '00enregistre', '01signe', '02attdecisioncpdv', '03attdecisioncg', '99rejetecpdv', '99rejete'  ), false );

			// TODO: à factoriser
			$commentairesnormescers93 = $this->Contratinsertion->Cer93->Histochoixcer93->Commentairenormecer93->find(
				'all',
				array(
					'order' => array( 'Commentairenormecer93.isautre ASC', 'Commentairenormecer93.name ASC' )
				)
			);

			$options['commentairesnormescers93_list'] = Hash::combine( $commentairesnormescers93, '{n}.Commentairenormecer93.id', '{n}.Commentairenormecer93.name' );
			$options['commentairesnormescers93_autres_ids'] = Hash::extract( $commentairesnormescers93, '{n}.Commentairenormecer93[isautre=1].id' );

			return $options;
		}

		/**
		 *
		 * TODO: dans le modèle ?
		 *
		 * @param array $results
		 * @param string $histochoixcer93key
		 * @return array
		 */
		/*protected function _addCommentairenormecer93( $results, $histochoixcer93key ) {
			if( !empty( $results ) ) {
				foreach( $results as $i => $result ) {
					if( !empty( $result[$histochoixcer93key]['id'] ) ) {
						$conditions = array(
							'Commentairenormecer93Histochoixcer93.histochoixcer93_id' => $result[$histochoixcer93key]['id']
						);

						$etape = (int)preg_replace( '/^([0-9]+).*$/', '\1', $result[$histochoixcer93key]['etape'] );
						if( $etape > 3 ) {
							$conditions = array(
								'OR' => array(
									$conditions,
									array(
										'Commentairenormecer93Histochoixcer93.histochoixcer93_id IN ('.$this->Contratinsertion->Cer93->Histochoixcer93->sq(
											array_words_replace(
												array(
													'fields' => array(
														'Histochoixcer93.id'
													),
													'alias' => 'histoschoixcers93',
													'conditions' => array(
														'Histochoixcer93.cer93_id' => $result['Histochoixcer93']['cer93_id'],
														'Histochoixcer93.etape' => '03attdecisioncg'
													),
													'order' => array( 'Histochoixcer93.created DESC' ),
													'limit' => 1
												),
												array(
													'Cer93' => 'cers93',
													'Histochoixcer93' => 'histoschoixcers93',
												)
											)
										).')'
									)
								)
							);
						}

						$commentaires = $this->Contratinsertion->Cer93->Histochoixcer93->Commentairenormecer93Histochoixcer93->find(
							'all',
							array(
								'conditions' => $conditions,
								'contain' => array(
									'Commentairenormecer93',
									'Histochoixcer93' => array(
//										'fields' => array(
//											'Histochoixcer93.user_id'
//										),
										'User'
									)
								),
								'order' => array( 'Commentairenormecer93.isautre ASC', 'Commentairenormecer93.name ASC' )
							)
						);

						$usersIds = array_unique( Hash::extract( $commentaires, '{n}.Histochoixcer93.user_id' ) );
						$user_id = ( !empty( $usersIds ) ? $usersIds[0] : null );
						$user = array( 'User' => array() );
						if( !empty( $user_id ) ) {
							$user = $this->Contratinsertion->User->find( 'first', array( 'conditions' => array( 'User.id' => $user_id ), 'contain' => false, 'fields' => array( 'User.nom_complet' ) ) );
						}
						$results[$i]['User'] = $user['User'];

						$results[$i]['Commentairenormecer93'] = Hash::extract( $commentaires, '{n}.Commentairenormecer93' );
						$results[$i]['Commentairenormecer93Histochoixcer93'] = Hash::extract( $commentaires, '{n}.Commentairenormecer93Histochoixcer93' );
					}
				}
			}

			return $results;
		}*/

		/**
		 *
		 * TODO: dans le modèle ?
		 *
		 * @param array $results
		 * @param string $histochoixcer93key
		 * @return array
		 */
		protected function _addCommentairenormecer93( $results ) {
			if( !empty( $results ) ) {
				foreach( $results as $i => $result ) {
                    // 1.
                    foreach( array( '', 'etape02', 'etape03', 'etape04', 'etape05', 'etape06' ) as $etape ) {
                        $histochoixcer93key = "Histochoixcer93{$etape}";

                        if( !empty( $result[$histochoixcer93key]['id'] ) ) {
                            $user_id = Hash::get( $result, "{$histochoixcer93key}.user_id" );

                            $user = array( 'User' => array() );
							if( !empty( $user_id ) ) {
								$user = $this->Contratinsertion->User->find( 'first', array( 'conditions' => array( 'User.id' => $user_id ), 'contain' => false, 'fields' => array( 'User.nom_complet' ) ) );
							}

							$results[$i][$histochoixcer93key] = Hash::merge(
								$results[$i][$histochoixcer93key],
								$user
							);
                        }
                    }

                    // 2.
					foreach( array( '', 'etape02', 'etape03' ) as $etape ) {
						$histochoixcer93key = "Histochoixcer93{$etape}";

						if( !empty( $result[$histochoixcer93key]['id'] ) ) {
							$conditions = array(
								'Commentairenormecer93Histochoixcer93.histochoixcer93_id' => $result[$histochoixcer93key]['id']
							);

							$etape = (int)preg_replace( '/^([0-9]+).*$/', '\1', $result[$histochoixcer93key]['etape'] );
							if( $etape > 3 ) {
								$conditions = array(
									'OR' => array(
										$conditions,
										array(
											'Commentairenormecer93Histochoixcer93.histochoixcer93_id IN ('.$this->Contratinsertion->Cer93->Histochoixcer93->sq(
												array_words_replace(
													array(
														'fields' => array(
															'Histochoixcer93.id'
														),
														'alias' => 'histoschoixcers93',
														'conditions' => array(
															'Histochoixcer93.cer93_id' => $result['Histochoixcer93']['cer93_id'],
															'Histochoixcer93.etape' => '03attdecisioncg'
														),
														'order' => array( 'Histochoixcer93.created DESC' ),
														'limit' => 1
													),
													array(
														'Cer93' => 'cers93',
														'Histochoixcer93' => 'histoschoixcers93',
													)
												)
											).')'
										)
									)
								);
							}

							$commentaires = $this->Contratinsertion->Cer93->Histochoixcer93->Commentairenormecer93Histochoixcer93->find(
								'all',
								array(
									'conditions' => $conditions,
									'contain' => array(
										'Commentairenormecer93',
										'Histochoixcer93' => array(
											'User'
										)
									),
									'order' => array( 'Commentairenormecer93.isautre ASC', 'Commentairenormecer93.name ASC' )
								)
							);

//							$usersIds = array_unique( Hash::extract( $commentaires, '{n}.Histochoixcer93.user_id' ) );
//							$user_id = ( !empty( $usersIds ) ? $usersIds[0] : null );
//							$user = array( 'User' => array() );
//							if( !empty( $user_id ) ) {
//								$user = $this->Contratinsertion->User->find( 'first', array( 'conditions' => array( 'User.id' => $user_id ), 'contain' => false, 'fields' => array( 'User.nom_complet' ) ) );
//							}

							$ajout = array(
								//'User' => $user['User'],
								'Commentairenormecer93' => Hash::extract( $commentaires, '{n}.Commentairenormecer93' ),
								'Commentairenormecer93Histochoixcer93' => Hash::extract( $commentaires, '{n}.Commentairenormecer93Histochoixcer93' )
							);

							$results[$i][$histochoixcer93key] = Hash::merge(
								$results[$i][$histochoixcer93key],
								$ajout
							);
						}
					}
				}
			}

			return $results;
		}

		/**
		 * Moteur de recherche pour la partie "4. Décision CG - 4.1 Première lecture".
		 *
		 * TODO: traitement ligne par ligne également
		 *
		 * @return void
		 */
		public function premierelecture() {
			$this->Workflowscers93->assertUserCg();
			$this->_validations( false );
		}

		/**
		 * Moteur de recherche pour la partie "4. Décision CG - 4.2 Validation CS".
		 *
		 * TODO: traitement ligne par ligne également
		 *
		 * @return void
		 */
		public function validationcs() {
			$this->Workflowscers93->assertUserCg();
			$this->_validations( false );
		}

		/**
		 * Moteur de recherche pour la partie "4. Décision CG - 4.3 Validation Cadre".
		 *
		 * TODO: traitement ligne par ligne également
		 *
		 * @return void
		 */
		public function validationcadre() {
			$this->Workflowscers93->assertUserCg();
			$this->_validations( false );
		}

		/**
		 * Moteur de recherche pour la partie "5. Tableau de suivi".
		 *
		 * @return void
		 */
		public function visualisation() {
			$structuresreferentes_ids = $this->WebrsaUsers->structuresreferentes();

			$this->_index( $structuresreferentes_ids );
		}

		/**
		 * Si l'utilisateur n'est pas attaché à une structure référente, alors on envoit une erreur.
		 *
		 * TODO: renommer cette fonction en _validationcg, du coup, on n'a plus la SR
		 *
		 * @param boolean $checkStructurereferente L'utilisateur doit-il être attaché à une structure référente ?
		 * @return void
		 */
		protected function _validations( $checkStructurereferente ) {
			$structuresreferentes_ids = $this->Workflowscers93->getUserStructurereferenteId( $checkStructurereferente );

			$this->_index( $structuresreferentes_ids );
		}

		/**
		 * Méthode de recherche générique.
		 *
		 * @param integer $structuresreferentes_ids L'id de la structure référente à laquelle l'utilisateur est lié.
		 */
		protected function _index( $structuresreferentes_ids ) {
			if( !empty( $this->request->data ) ) {
				// Traitement du formulaire d'affectation
				if( ( $this->action != 'saisie' ) && isset( $this->request->data['Histochoixcer93'] ) ) {
					$dossiers_ids = array_unique( Set::extract( '/Histochoixcer93/dossier_id', $this->request->data ) );
					$this->Cohortes->get( $dossiers_ids );

					// On change les règles de validation du modèle PersonneReferent avec celles qui sont spécfiques à la cohorte
					$histochoixcer93Validate = $this->Contratinsertion->Cer93->Histochoixcer93->validate;

					$datas = array();
					foreach( $this->request->data['Histochoixcer93'] as $i => $tmp ) {
						if( $tmp['action'] === 'Activer' ) {
							$item = array(
								'Histochoixcer93' => $tmp
							);

							if( isset( $this->request->data['Commentairenormecer93'][$i] ) ) {
								$item['Commentairenormecer93'] = $this->request->data['Commentairenormecer93'][$i];
							}

							$datas[$i] = $item;
						}
					}

					if( $this->Contratinsertion->Cer93->Histochoixcer93->saveAll( $datas, array( 'validate' => 'only', 'atomic' => false ) ) ) {
						if( !empty( $datas ) ) {
							$this->Contratinsertion->Cer93->Histochoixcer93->begin();
							$success = true;

							foreach( $datas as $key => $data ) {
								$success = $this->Contratinsertion->Cer93->Histochoixcer93->saveDecision( $data ) && $success;
							}

							if( $success ) {
								$this->Contratinsertion->Cer93->Histochoixcer93->commit();
								$this->Cohortes->release( $dossiers_ids );
								$this->Session->setFlash( 'Enregistrement effectué.', 'flash/success' );
								unset( $this->request->data['Histochoixcer93'] );
							}
							else {
								$this->Contratinsertion->Cer93->Histochoixcer93->rollback();
								$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
							}

						}
						else {
							$this->Session->setFlash( 'Aucun élément à enregistrer', 'flash/notice' );
						}
					}
					else {
						$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
					}

					$this->Contratinsertion->Cer93->Histochoixcer93->validate = $histochoixcer93Validate;
				}

				// Traitement du formulaire de recherche
				$querydata = $this->Cohortecer93->search(
					$this->action,
					$this->request->data['Search']
				);

				$querydata = $this->Gestionzonesgeos->qdConditions( $querydata );
				$querydata = $this->Cohortes->qdConditions( $querydata );

				if( !empty( $structuresreferentes_ids ) ) {
					$querydata['conditions']['Referent.structurereferente_id'] = $structuresreferentes_ids;
				}

				// TODO: plus proprement, pour toute l'application, avec Configure
//				$this->Paginator->settings = array( 'limit' => 10, 'maxLimit' => 1001 );
				$querydata['maxLimit'] = 1001;

				$this->paginate = $querydata;
				$cers93 = $this->paginate(
					$this->Contratinsertion->Personne,
					array(),
					array(),
					!Hash::get( $this->request->data, 'Search.Pagination.nombre_total' )
				);

				// Ajout des commentaires fournis par le CPDV bug #6251
				$cers93 = $this->_addCommentairenormecer93( $cers93, 'Histochoixcer93' );
				$this->set( 'cers93', $cers93 );

				if( !in_array( $this->action, array( 'saisie', 'visualisation' ) ) ) {
					$position = null;
					if( $this->action == 'premierelecture' ) { // FIXME
						$position = '03attdecisioncg';
					}
					else if( $this->action == 'validationcs' ) {
						$position = '04premierelecture';
					}
					else if( $this->action == 'validationcadre' ) {
						$position = '05secondelecture';
					}

					if( !is_null( $position ) ) {
						$dossiers_ids = Set::extract( $cers93, "/Cer93[positioncer={$position}]/../Dossier/id" );
						$this->Cohortes->get( $dossiers_ids );
					}

					// Par défaut, on récupère les informations déjà saisies en individuel
					if( !isset( $this->request->data['Histochoixcer93'] ) ) {
						if( $this->action == 'avalidercpdv' ) {
							$etape = '03attdecisioncg';
						}
						else if( $this->action == 'premierelecture' ) {
							$etape = '04premierelecture';
						}
						else if( $this->action == 'validationcs' ) {
							$etape = '05secondelecture';
						}
						else if( $this->action == 'validationcadre' ) {
							$etape = '06attaviscadre';
						}
						$datas = $this->Cohortecer93->prepareFormData( $cers93, $etape, $this->Session->read( 'Auth.User.id' ) );
						if( !empty( $datas ) ) {
							$this->request->data['Histochoixcer93'] = $datas['Histochoixcer93'];
						}
					}
				}
				else if( $this->action == 'visualisation' ) {

					// Valeur pour déterminer si on peut voir ou non les commentaires CG
					// On masque les informations si l'utilisateur est <> CG
					$authUser = $this->Session->read( 'Auth.User.id' );
					$user = ClassRegistry::init( 'User' )->find(
						'first',
						array(
							'conditions' => array(
								'User.id' => $authUser
							),
							'contain' => false
						)
					);
					$authViewCommentaireCg = false;
					if( $user['User']['type'] == 'cg' ) {
						$authViewCommentaireCg = true;
					}


					foreach( $cers93 as $i => $cer93 ){

						// Colonne Non orienté PDV
						$typeOrientationId = $cer93['Orientstruct']['typeorient_id'];
						$typeOrientSociopro = Configure::read( 'Orientstruct.typeorientprincipale.Socioprofessionnelle' );
						if( !empty( $typeOrientationId ) ) {
							//	Nous ne sommes pas en sociopro (PDV)
							if( !in_array( $typeOrientationId, $typeOrientSociopro ) ) {
								$labelTypeOrientation = 'Réorientation';
							}
							else {
								$labelTypeOrientation = '';
							}
						}
						else {
							$labelTypeOrientation = 'Orientation';
						}
						$cers93[$i]['Cer93']['nonorientepdv'] = $labelTypeOrientation;

						// Colonne Saisie du CER
						if( in_array( $cer93['Cer93']['positioncer'], array( '', '00enregistre', '01signe', '02attdecisioncpdv' ) ) ) {
							$positionAvantCG = $cer93['Cer93']['positioncer'];
						}
						else {
							$positionAvantCG = '';
						}
						$cers93[$i]['Cer93']['positioncer_avantcg'] = $positionAvantCG;

						// Colonne Etape du Responsable
						$validationcpdv = '';
						if( !empty( $cer93['Histochoixcer93etape03']['etape'] ) ) {
							if( $cer93['Histochoixcer93etape03']['isrejet'] != '1' ) {
								$validationcpdv = '03attdecisioncg';
								if( $cer93['Cer93']['positioncer'] == '99rejetecpdv' ) {
									$validationcpdv = '99rejetecpdv';
								}
							}
							else {
								$validationcpdv = '99rejetecpdv';
							}
						}
						$cers93[$i]['Cer93']['validationcpdv'] = $validationcpdv;

						// On masque les commentaires du CG si l'utilisateur n'est pas du CG
						$commentaireCg = '';
						$etapes = array( /*'0' => 'etape04',*/ '0' => 'etape05', '1' => 'etape06' );
						foreach( $etapes as $key => $etape ) {
							if( !empty( $cer93["Histochoixcer93{$etape}"]['etape'] ) ) {
								if( $authViewCommentaireCg ) {
									$commentaireCg = $cer93["Histochoixcer93{$etape}"]['commentaire'];
								}
								else {
									$commentaireCg = '';
								}
							}
							$cers93[$i]['Histochoixcer93']['commentaire'] = $commentaireCg;
						}

						if( in_array( $cer93['Cer93']['positioncer'], array( '99valide', '99rejete' ) ) ) {
							$commentaireCpdv = '';
						}
						else {
							$commentaireCpdv = $cer93['Histochoixcer93etape03']['commentaire'];
						}
						$cers93[$i]['Histochoixcer93etape03']['commentaire'] = $commentaireCpdv;
					}
					$this->set( 'cers93', $cers93 );
				}
			}

			$this->set( 'structurereferente_id', $structuresreferentes_ids );

			// Options
			$options = array(
				'actions' => array( 'Activer' => 'Activer', 'Desactiver' => 'Désactiver' ),
				'cantons' => $this->Gestionzonesgeos->listeCantons(),
				'etatdosrsa' => ClassRegistry::init('Dossier')->enum('etatdosrsa'),
				'moticlorsa' => ClassRegistry::init('Situationdossierrsa')->enum('moticlorsa'),
				'exists' => array( '1' => 'Oui', '0' => 'Non' ),
				'mesCodesInsee' => $this->Gestionzonesgeos->listeCodesInsee(),
				'referents' => $this->InsertionsBeneficiaires->referents(
					array(
						'type' => 'optgroup',
						'conditions' => $this->InsertionsBeneficiaires->conditions['referents']
							+ (
								empty( $structuresreferentes_ids )
								? array()
								: array( 'Referent.structurereferente_id' => $structuresreferentes_ids )
							)
					)
				),
				'toppersdrodevorsa' => $this->Option->toppersdrodevorsa( true ),
				'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers'),
				'formeci' => ClassRegistry::init('Contratinsertion')->enum('forme_ci'),
				'gestionnaire' => ClassRegistry::init( 'User' )->find( 'list', array( 'fields' => array( 'User.nom_complet' ) ) )
			);
			$options['Adresse']['numcom'] = $options['mesCodesInsee'];
			$options['Adresse']['canton'] = $options['cantons'];
			$options = Set::merge(
				$options,
				$this->Contratinsertion->Cer93->enums(),
				$this->Contratinsertion->Cer93->Histochoixcer93->enums(),
				$this->Allocataires->optionsSessionCommunautesr( 'PersonneReferent' )
			);
			$options['Search']['Cer93']['hasdateimpression'] = array( 'I' => 'Imprimé', 'N' => 'Non imprimé' );
			$options['Search']['Cer93']['limit'] = array_combine( array_range( 100, 1000, 100 ), array_range( 100, 1000, 100 ) );
			$options['Search']['Cer93']['positioncer'] = array( '99decisioncg' => 'Décision CG', '99valide' => 'Validé CG', '99rejete' => 'Rejeté CG' );

			$this->set( compact( 'options' ) );

			if( $this->action == 'saisie' ) {
				$this->render( 'saisie' );
			}
			else if( $this->action == 'avalidercpdv' ) {
				$this->render( 'avalidercpdv' );
			}
			else if( $this->action == 'premierelecture' ) {
				$this->render( 'premierelecture' );
			}
			else if( $this->action == 'visualisation' ) {
				$this->render( 'visualisation' );
			}
		}

		/**
		 * TODO
		 *
		 * @return void
		 */
		public function exportcsv( $etape ) {
			if( $etape == 'saisie' ) {
				$this->Workflowscers93->assertUserExterne();
			}
			else if( $etape == 'avalidercpdv' ) {
				$this->Workflowscers93->assertUserCpdv();
			}
			else if( in_array( $etape, array( 'premierelecture', 'validationcs', 'validationcadre' ) ) ) {
				$this->Workflowscers93->assertUserCg();
			}

			$data = Hash::expand( $this->request->params['named'], '__' );
			$querydata = $this->Cohortecer93->search(
				$etape,
				$data['Search']
			);

			$querydata = $this->Gestionzonesgeos->qdConditions( $querydata );

			unset( $querydata['limit'] );

			$structuresreferentes_ids = $this->Workflowscers93->getUserStructurereferenteId( false );
            if( !empty( $structuresreferentes_ids ) ) {
                $querydata['conditions']['Referent.structurereferente_id'] = $structuresreferentes_ids;
            }

			// TODO: factoriser
			if( $etape == 'saisie' ) {
				$querydata['conditions'][] = array(
					array(
						'OR' => array(
							'Cer93.positioncer' => array( '00enregistre', '01signe', '02attdecisioncpdv' ),
							'Contratinsertion.id IS NULL',
							'Contratinsertion.df_ci <= DATE_TRUNC( \'day\', NOW() )',
							'Contratinsertion.df_ci - INTERVAL \''.Configure::read( 'Cohortescers93.saisie.periodeRenouvellement' ).'\' <= DATE_TRUNC( \'day\', NOW() )'
						)
					),
					array(
						'OR' => array(
							'PersonneReferent.referent_id' => $this->Session->read( 'Auth.User.referent_id' ),
							'PersonneReferent.structurereferente_id' => $this->WebrsaUsers->structuresreferentes()
						)
					)
				);
			}

			$cers93 = $this->Contratinsertion->Personne->find( 'all', $querydata );

			// TODO: factoriser ?
			$options = array(
				'actions' => array( 'Activer' => 'Activer', 'Desactiver' => 'Désactiver' ),
				'cantons' => $this->Gestionzonesgeos->listeCantons(),
				'etatdosrsa' => ClassRegistry::init('Dossier')->enum('etatdosrsa'),
				'moticlorsa' => ClassRegistry::init('Situationdossierrsa')->enum('moticlorsa'),
				'exists' => array( '1' => 'Oui', '0' => 'Non' ),
				'mesCodesInsee' => $this->Gestionzonesgeos->listeCodesInsee(),
				'referents' => $this->InsertionsBeneficiaires->referents(
					array(
						'type' => 'optgroup',
						'conditions' => $this->InsertionsBeneficiaires->conditions['referents']
							+ (
								empty( $structuresreferentes_ids )
								? array()
								: array( 'Referent.structurereferente_id' => $structuresreferentes_ids )
							)
					)
				),
				'toppersdrodevorsa' => $this->Option->toppersdrodevorsa( true ),
				'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers'),
				'formeci' => ClassRegistry::init('Contratinsertion')->enum('forme_ci')
			);

			$options = Set::merge(
				$options,
				$this->Contratinsertion->Cer93->enums(),
				$this->Contratinsertion->Cer93->Histochoixcer93->enums()
			);

			$this->set( compact( 'options', 'etape', 'cers93' ) );
			$this->layout = '';
		}



		/**
		 * Imprime en cohorte des décisions sur le CER 93.
		 * INFO: http://localhost/webrsa/trunk/cers93/printdecision/44327
		 *
		 * @param integer $contratinsertion_id
		 * @return void
		 */
		public function impressionsDecisions( $statut = null ) {
			// La page sur laquelle nous sommes
			$page = Set::classicExtract( $this->request->params, 'named.page' );
			if( ( intval( $page ) != $page ) || $page < 0 ) {
				$page = 1;
			}

			$this->Cohortecer93->begin();

			$pdfs = $this->Cohortecer93->getDefaultCohortePdf(
				$statut,
				$this->Session->read( 'Auth.User.id' ),
				Hash::get( Hash::expand( $this->request->params['named'], '__' ), 'Search' ),
				$page
			);

			if( !empty( $pdfs ) ) {
				$this->Cohortecer93->commit();
				$pdfs = $this->Gedooo->concatPdfs( $pdfs, 'Cer93' );
				$this->Gedooo->sendPdfContentToClient( $pdfs, sprintf( 'contratinsertion_decision-%s.pdf', date( 'Y-m-d' ) ) );
			}
			else {
				$this->Cohortecer93->rollback();
				$this->Session->setFlash( 'Impossible de générer le courrier.', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}

		}
	}
?>