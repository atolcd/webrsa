<?php
	/**
	 * Code source de la classe Cohortecer93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Cohortecer93 permet de rechercher le allocataires ne possédant pas de référent de parcours
	 * en cours.
	 *
	 * @package app.Model
	 */
	class Cohortecer93 extends AppModel
	{
		/**
		 * @var string
		 */
		public $name = 'Cohortecer93';

		/**
		 * @var boolean
		 */
		public $useTable = false;

		/**
		 * @var array
		 */
		public $actsAs = array( 'Conditionnable' );

		/**
		 * Retourne un querydata résultant du traitement du formulaire de recherche des cohortes de référent
		 * du parcours.
		 *
		 * @param string $statut
		 * @param array $search Critères du formulaire de recherche
		 * @return array
		 */
		public function search( $statut, $search ) {
			$Personne = ClassRegistry::init( 'Personne' );

			$sqDerniereRgadr01 = $Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' );
			$sqDerniereOrientstruct = $Personne->Orientstruct->WebrsaOrientstruct->sqDerniere();
			$sqDernierContratinsertion = array();

			if( isset( $search['Contratinsertion']['dernier'] ) && $search['Contratinsertion']['dernier'] == '1' ) {
				$sqDernierContratinsertion = $Personne->sqLatest( 'Contratinsertion', 'id' );
			}

			// Par défaut on affiche le dernier CER dans le tableau des saisies CER
			if( in_array( $statut, array( 'saisie', 'avalidercpdv' ) ) ) {
				$sqDernierContratinsertion = $Personne->sqLatest( 'Contratinsertion', 'dd_ci' );
			}

			$sqDernierReferent = $Personne->PersonneReferent->sqDerniere( 'Personne.id', Configure::read('Filtresdefaut.Cohortescers93_avalidercpdv.dfdesignation') );
			$sqDernierRdv = $Personne->Rendezvous->sqDernier( 'Personne.id' );

			$sqDspId = 'SELECT dsps.id FROM dsps WHERE dsps.personne_id = "Personne"."id" LIMIT 1';
			$sqDspExists = "( {$sqDspId} ) IS NOT NULL";


			$conditions = array(
				'Prestation.rolepers' => array( 'DEM', 'CJT' ),
				array(
					'OR' => array(
						'Adressefoyer.id IS NULL',
						"Adressefoyer.id IN ( {$sqDerniereRgadr01} )"
					)
				),
				$sqDernierContratinsertion,
				"PersonneReferent.id IN ( {$sqDernierReferent} )",
				array(
					'OR' => array(
						'Rendezvous.id IS NULL',
						"Rendezvous.id IN ( {$sqDernierRdv} )"
					)
				)
			);

			// Choix du référent affecté ?
			$referent_id = suffix( Hash::get( $search, 'PersonneReferent.referent_id' ) );
			if( !empty( $referent_id ) ) {
				$conditions['PersonneReferent.referent_id'] = $referent_id;
			}
			$conditions = $this->conditionsDates( $conditions, $search, 'PersonneReferent.dddesignation' );

			// Présence DSP ?
			if( isset( $search['Dsp']['exists'] ) && ( $search['Dsp']['exists'] != '' ) ) {
				if( $search['Dsp']['exists'] ) {
					$conditions[] = "( {$sqDspExists} )";
				}
				else {
					$conditions[] = "( ( {$sqDspId} ) IS NULL )";
				}
			}

			// Présence CER ?
			if( isset( $search['Contratinsertion']['exists'] ) && ( $search['Contratinsertion']['exists'] != '' ) ) {
				 $sqDernierContratinsertionExists = $Personne->sqLatest( 'Contratinsertion', 'rg_ci', array(), false );

				if( $search['Contratinsertion']['exists'] ) {
					$conditions[] = "( ( {$sqDernierContratinsertionExists} ) IS NOT NULL )";
				}
				else {
					$conditions[] = "( ( {$sqDernierContratinsertionExists} ) IS NULL )";
				}
			}

			$conditions = $this->conditionsAdresse( $conditions, $search );
			$conditions = $this->conditionsPersonneFoyerDossier( $conditions, $search );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $search );
			$conditions = $this->conditionsDates( $conditions, $search, 'Orientstruct.date_valid' );
			// Filtre sur la date d'envoi du CER
			$conditions = $this->conditionsDates( $conditions, $search, 'Contratinsertion.created' );
			// Filtre sur la date de signature
			$conditions = $this->conditionsDates( $conditions, $search, 'Cer93.datesignature' );

			// Mode d'opération
			$mode_operation = Hash::get( $search, 'Cer93.mode_operation' );

			//Filtre sur la position du CER
			if( $mode_operation == 'traitement' ) {
				$positioncer = Set::extract( $search, 'Cer93.positioncer' );
				if( isset( $search['Cer93']['positioncer'] ) && !empty( $search['Cer93']['positioncer'] ) ) {
					$conditions[] = '( Cer93.positioncer IN ( \''.implode( '\', \'', $positioncer ).'\' ) )';
				}
			}
			else {
				$positioncer = Set::extract( $search, 'Cer93.positioncer' );
				if( isset( $search['Cer93']['positioncer'] ) && !empty( $search['Cer93']['positioncer'] ) ) {
					if( $positioncer == '99decisioncg' ) {
						$positioncer = array( '99valide', '99rejete' );
					}
					else {
						$positioncer = (array)$positioncer;
					}
					$conditions[] = '( Cer93.positioncer IN ( \''.implode( '\', \'', $positioncer ).'\' ) )';
				}
			}

			if( !in_array( $statut, array( 'saisie', 'visualisation' ) ) ) {
				if( $statut == 'avalidercpdv' ) {
					$position = '02attdecisioncpdv';
				}
				else if( $statut == 'premierelecture' ) {
					$position = '03attdecisioncg';
				}
				else if( $statut == 'validationcs' ) {
					$position = array( '04premierelecture' );
				}
				else if( $statut == 'validationcadre' ) {
					$position = '05secondelecture';
				}

				$condition = array(
					'Contratinsertion.id IN ('.$Personne->Contratinsertion->sq(
						array_words_replace(
							array(
								'fields' => array(
									'Contratinsertion.id'
								),
								'alias' => 'contratsinsertion',
								'conditions' => array(
									'Contratinsertion.personne_id = Personne.id',
									'Cer93.positioncer <>' => '99rejete',
									'Cer93.positioncer' => $position,
									'Histochoixcer93.etape' => $position
								),
								'joins' => array(
									$Personne->Contratinsertion->join( 'Cer93' ),
									$Personne->Contratinsertion->Cer93->join( 'Histochoixcer93' )
								),
								'order' => array( 'Contratinsertion.dd_ci DESC' ),
								'limit' => 1
							),
							array(
								'Contratinsertion' => 'contratsinsertion',
								'Cer93' => 'cers93',
								'Histochoixcer93' => 'histoschoixcers93',
							)
						)
					).')'
				);

				if( in_array( $statut, array( 'validationcs', 'validationcadre' ) ) ) {
					if( $mode_operation != 'impression' ) {
						$positionsuivante = null;
						if( $statut == 'validationcs' ) {
							$positioncer = '99valide';
							$etape = '05secondelecture';
						}
						else if( $statut == 'validationcadre' ) {
							$positioncer = array( '99valide', '99rejete' );
							$etape = '06attaviscadre';
						}
					}
					else {
						$etape = array( '05secondelecture', '06attaviscadre' );
					}

					$conditionDateImpression = 'Cer93.dateimpressiondecision IS NULL';
					$conditionDateDecision = '1 = 1';
					if( $mode_operation == 'impression' ) {
						// Conditions sur la présence d'une date d'impression
						$hasDateImpression = Hash::get( $search, 'Cer93.hasdateimpression' );
						if( empty( $hasDateImpression ) ) {
							$conditionDateImpression = '1 = 1';
						}
						else if( $hasDateImpression == 'I' ) {
							$conditionDateImpression = 'Cer93.dateimpressiondecision IS NOT NULL';
						}
						else {
							$conditionDateImpression = 'Cer93.dateimpressiondecision IS NULL';
						}

						// Conditions sur la date d'impression
						$conditionDateImpression = $this->conditionsDates( (array)$conditionDateImpression, $search, 'Cer93.dateimpressiondecision' );

						// Conditions sur la date de décision
						if(Hash::get($search, 'Contratinsertion.datedecision')) {
							$conditionDateDecision = $this->conditionsDates( array(), $search, 'Contratinsertion.datedecision' );
						}
					}

					$conditions[] = array(
						'OR' => array(
							$condition,
							array(
								'Contratinsertion.id IN ('.$Personne->Contratinsertion->sq(
									array_words_replace(
										array(
											'fields' => array(
												'Contratinsertion.id'
											),
											'alias' => 'contratsinsertion',
											'conditions' => array(
												'Contratinsertion.personne_id = Personne.id',
												'Cer93.positioncer' => $positioncer,
												$conditionDateImpression,
												$conditionDateDecision,
												'Histochoixcer93.etape' => $etape,
												'Histochoixcer93.id IN ( '.$Personne->Contratinsertion->Cer93->sqLatest( 'Histochoixcer93', 'etape', array(), false ).' )'
											),
											'joins' => array(
												$Personne->Contratinsertion->join( 'Cer93', array( 'type' => 'INNER' )  ),
												$Personne->Contratinsertion->Cer93->join( 'Histochoixcer93', array( 'type' => 'INNER' ) )
											),
											'order' => array( 'Contratinsertion.dd_ci DESC' ),
											'limit' => 1
										),
										array(
											'Contratinsertion' => 'contratsinsertion',
											'Cer93' => 'cers93',
											'Histochoixcer93' => 'histoschoixcers93',
										)
									)
								).')'
							)
						)
					);
				}
				else {
					$conditions[] = $condition;
				}

			}
			else if( $statut == 'saisie' ) {
				$position = array( '00enregistre', '01signe', '02attdecisioncpdv', '99rejete', '99rejetecpdv' );
				$conditions[] = array(
					'OR' => array(
						'Contratinsertion.id IN ('.$Personne->Contratinsertion->sq(
							array_words_replace(
								array(
									'fields' => array(
										'Contratinsertion.id'
									),
									'alias' => 'contratsinsertion',
									'conditions' => array(
										'Contratinsertion.personne_id = Personne.id',
										'Cer93.positioncer' => $position
									),
									'joins' => array(
										$Personne->Contratinsertion->join( 'Cer93' )
									),
									'order' => array( 'Contratinsertion.dd_ci DESC' ),
									'limit' => 1
								),
								array(
									'Contratinsertion' => 'contratsinsertion',
									'Cer93' => 'cers93'
								)
							)
						).')',
						'Contratinsertion.id IS NULL',
						'AND' => array(
							// FIXME modifier la date de détection avec 1 mois avant son terme ou 1 fois dépassée
							'Contratinsertion.df_ci <' => date( 'Y-m-d' ),
							'Contratinsertion.decision_ci' => 'V'
						)
					)
				);
			}

			/// Dossiers lockés
			if( !empty( $lockedDossiers ) && ( $statut != 'visualisation' && ( $mode_operation != 'impression' ) ) ) {
				if( is_array( $lockedDossiers ) ) {
					$lockedDossiers = implode( ', ', $lockedDossiers );
				}
				if( $statut == 'saisie' ) {
					$conditions[] = array(
						'OR' => array(
							'Cer93.id IS NULL',
							'NOT' => array( 'Cer93.positioncer' => array( '00enregistre', '01signe' ) ),
							array(
								'Cer93.positioncer' => array( '00enregistre', '01signe' ),
								"NOT {$lockedDossiers}"
							),
						)
					);
				}
				else { // FIXME
					if( $statut == 'validationcs' ) {
						$conditions[] = array(
							'OR' => array(
								'NOT' => array( 'Cer93.positioncer' => array( '04premierelecture' ) ),
								array(
									'Cer93.positioncer' => array( '04premierelecture' ),
									"NOT {$lockedDossiers}"
								),
							)
						);
					}
					else {
						$conditions[] = "NOT {$lockedDossiers}";
					}
				}
			}

			$limit = isset ($search['limit']) ? $search['limit'] : Configure::read('ResultatsParPage.nombre_par_defaut');

			$querydata = array(
				'fields' => array_merge(
					$Personne->fields(),
					$Personne->Calculdroitrsa->fields(),
					$Personne->Contratinsertion->fields(),
					$Personne->Contratinsertion->Cer93->fields(),
					$Personne->Orientstruct->fields(),
					$Personne->Prestation->fields(),
					$Personne->Foyer->Dossier->fields(),
					$Personne->Foyer->Adressefoyer->Adresse->fields(),
					$Personne->Rendezvous->fields(),
					$Personne->PersonneReferent->fields(),
					$Personne->Foyer->Dossier->Situationdossierrsa->fields(),
					// Présence DSP
					array(
						$Personne->sqVirtualField( 'nom_complet_court', true ),
						'Structurereferente.lib_struc',
						"( {$sqDspExists} ) AS \"Dsp__exists\"" // TODO: mettre dans le modèle
					),
					array(
						$Personne->Foyer->Adressefoyer->NvTransfertpdv93->vfDateAnterieureTransfert( 'NvTransfertpdv93.created', 'Contratinsertion.date_saisi_ci', 'NvTransfertpdv93.encoursvalidation' )
					)
				),
				'contain' => false,
				'joins' => array(
					$Personne->join( 'Calculdroitrsa', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Personne->join(
						'Orientstruct',
						array(
							'conditions' => array(
								'OR' => array(
									'Orientstruct.id IS NULL',
									"Orientstruct.id IN ( {$sqDerniereOrientstruct} )"
								)
							),
							'type' => 'LEFT OUTER'
						)
					),
					$Personne->join( 'Contratinsertion', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Contratinsertion->join( 'Cer93', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->join( 'PersonneReferent', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Rendezvous', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->Adressefoyer->join(
						'NvTransfertpdv93', array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'NvTransfertpdv93.nv_orientstruct_id = Orientstruct.id'
							)
						)
					),
					$Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
				),
				'conditions' => $conditions,
				'order' => array(
					'Orientstruct.date_valid ASC',
					'Personne.nom ASC',
					'Personne.prenom ASC',
				),
				'limit' => ( ( $mode_operation != 'impression' ) ? $limit : Hash::get( $search, 'Cer93.limit' ) )
			);

			//
			if( !in_array( $statut, array( 'saisie', 'visualisation' ) ) ) {
				if( $mode_operation != 'impression' ) {
					$decision_ci = 'E';
					if( $statut == 'validationcs' ) {
						$decision_ci = array( 'E', 'V' );
					}
					else if( $statut == 'validationcadre' ) {
						$decision_ci = array( 'E', 'V', 'R' );
					}
				}
				else {
					$querydata['conditions']['Histochoixcer93.etape'] = ( $statut == 'validationcs' ? '05secondelecture' : '06attaviscadre' );
					$decision_ci = array( 'V', 'R' );
				}

				$sqDerniereHistochoixcer93Etape = $Personne->Contratinsertion->Cer93->sqLatest(
					'Histochoixcer93',
					'modified',
					array( 'Contratinsertion.decision_ci' => $decision_ci )
				);
				$querydata['conditions'][] = $sqDerniereHistochoixcer93Etape;

				$querydata['fields'] = array_merge( $querydata['fields'], $Personne->Contratinsertion->Cer93->Histochoixcer93->fields() );
				$querydata['joins'][] = $Personne->Contratinsertion->Cer93->join( 'Histochoixcer93', array( 'type' => 'LEFT OUTER' ) );
			}
			else {
				$fields = $Personne->Contratinsertion->Cer93->Histochoixcer93->fields();
				$etapes = array( '02attdecisioncpdv', '03attdecisioncg', '04premierelecture', '05secondelecture', '06attaviscadre' );
				foreach( $etapes as $e ) {
					$alias = 'Histochoixcer93etape'.preg_replace( '/^([0-9]+).*$/', '\1', $e );

					$querydata['fields'] = array_merge( $querydata['fields'], array_words_replace( $fields, array( 'Histochoixcer93' => $alias ) ) );
					$querydata['joins'][] = array_words_replace(
						$Personne->Contratinsertion->Cer93->join( 'Histochoixcer93', array( 'type' => 'LEFT OUTER', 'conditions' => array( 'Histochoixcer93.etape' => $e ) ) ),
						array( 'Histochoixcer93' => $alias )
					);
				}
			}

			// Lorsqu'on recherche les référents affecté, on doit ajouter des champs et une jointure
			$querydata['fields'] = Set::merge(
				$querydata['fields'],
				array(
					'PersonneReferent.dddesignation',
					$Personne->PersonneReferent->Referent->sqVirtualField( 'nom_complet', true )
				)
			);
			$querydata['joins'][] = $Personne->PersonneReferent->join( 'Referent', array( 'type' => 'INNER' ) );

			// Condition sur le projet insertion emploi territorial de la structure du référent
			$querydata['conditions'] = $this->conditionCommunautesr(
				$querydata['conditions'],
				$search,
				array( 'PersonneReferent.communautesr_id' => 'Referent.structurereferente_id' )
			);

			return $querydata;
		}


		/**
		 *	Fonction permettant de précharger le formulaire de cohorte avec les informations
		 *	du dernier historique lié aux cers93
		 *	@param $datas
		 *	@return array()
		 */
		public function prepareFormData( $datas, $etape, $user_id ) {
			$formData = array();

			foreach( $datas as $index => $data ) {
				$duree = ( !empty( $data['Histochoixcer93']['duree'] ) ) ? ( $data['Histochoixcer93']['duree'] ) : $data['Cer93']['duree'];
				$formData['Histochoixcer93'][$index] = array(
					'cer93_id' => $data['Histochoixcer93']['cer93_id'],
					'user_id' => $user_id,
					'formeci' => $data['Histochoixcer93']['formeci'],
					'duree' => $duree,
					'etape' => $etape,
					'datechoix' => date( 'Y-m-d' ),
					'prevalide' => $data['Histochoixcer93']['prevalide'],
					'decisioncs' => $data['Histochoixcer93']['decisioncs'],
					'decisioncadre' => $data['Histochoixcer93']['decisioncadre'],
					'isrejet' => $data['Histochoixcer93']['isrejet'],
					'action' => 'Desactiver',
					'dossier_id' => $data['Dossier']['id'],
					'commentaire' => $data['Histochoixcer93']['commentaire'],
				);
				$formData['Cer93'][$index] = array(
					'id' => $data['Histochoixcer93']['cer93_id'],
				);
			}

			return $formData;
		}

		/**
		 * Retourne la liste des clés de configuration pour lesquelles il faut
		 * vérifier la syntaxe de l'intervalle PostgreSQL.
		 *
		 * @return array
		 */
		public function checkPostgresqlIntervals() {
			$keys = array(
				'Cohortescers93.saisie.periodeRenouvellement'
			);

			return $this->_checkPostgresqlIntervals( $keys );
		}

		/**
		 * Retourne le PDF concernant les Décisions CG
		 *
		 * @param string $search
		 * @param integer $user_id
		 * @return string
		 */
		public function getDefaultCohortePdf( $statut, $user_id, $search, $page ) {
			$querydata = $this->search( $statut, $search );

			$querydata['offset'] = ( ( (int)$page ) <= 1 ? 0 : ( $querydata['limit'] * ( $page - 1 ) ) );
			$querydata['maxLimit'] = 1001;

			$Personne = ClassRegistry::init( 'Personne' );
			$cers93 = $Personne->find( 'all', $querydata );

			$pdfs = array();
			foreach( $cers93 as $cer93 ) {
				$pdfs[] = $Personne->Contratinsertion->Cer93->getDecisionPdf( $cer93['Cer93']['contratinsertion_id'], $user_id );
			}

			return $pdfs;
		}
	}
?>