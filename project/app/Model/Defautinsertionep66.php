<?php
	/**
	 * Code source de la classe Defautinsertionep66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Thematiqueep', 'Model/Abstractclass' );

	/**
	 * Saisines d'EP pour les bilans de parcours pour le conseil départemental du
	 * département 66.
	 *
	 * Une saisine regoupe plusieurs thèmes des EPs pour le CG 66.
	 *
	 * @package app.Model
	 */
	class Defautinsertionep66 extends Thematiqueep
	{
		public $name = 'Defautinsertionep66';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
			'Gedooo.Gedooo',
			'Conditionnable',
			'ModelesodtConditionnables' => array(
				66 => array(
					// Courrier d'information
					'%s/nonconclusionorientation_courrierinformationavantep.odt',
                    '%s/nonconclusioncer_courrierinformationavantep.odt',
                    '%s/nonrespect_courrierinformationavantep.odt',
					'%s/noninscriptionpe_courrierinformationavantep.odt',
					'%s/radiationpe_courrierinformationavantep.odt',
					// Convocation EP
					'Commissionep/convocationep_beneficiaire.odt',
				)
			)
		);

		public $belongsTo = array(
			'Bilanparcours66' => array(
				'className' => 'Bilanparcours66',
				'foreignKey' => 'bilanparcours66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Dossierep' => array(
				'className' => 'Dossierep',
				'foreignKey' => 'dossierep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'contratinsertion_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Cui' => array(
				'className' => 'Cui',
				'foreignKey' => 'cui_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Orientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'orientstruct_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Historiqueetatpe' => array(
				'className' => 'Historiqueetatpe',
				'foreignKey' => 'historiqueetatpe_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		/**
		 * Liste des décisions qui nécessitent un passage en EPL parcours.
		 *
		 * @var array
		 */
		public $decisionsEplParcours = array(
			'reorientationsocversprof',
			'reorientationprofverssoc',
			'maintienorientsoc'
		);

		/**
		*
		*/
		public function containQueryData() {
			return array(
				'Defautinsertionep66' => array(
					'Decisiondefautinsertionep66'=>array(
						'Typeorient',
						'Structurereferente'
					),
				)
			);
		}

		/**
		* Querydata permettant d'obtenir les dossiers qui doivent être traités
		* par liste pour la thématique de ce modèle.
		*
		* TODO: une autre liste pour avoir un tableau permettant d'accéder à la fiche
		* TODO: que ceux avec accord, les autres en individuel
		*
		* @param integer $commissionep_id L'id technique de la séance d'EP
		* @param string $niveauDecision Le niveau de décision ('ep' ou 'cg') pour
		*	lequel il faut les dossiers à passer par liste.
		* @return array
		* @access public
		*/

		public function qdDossiersParListe( $commissionep_id, $niveauDecision ) {
			// Doit-on prendre une décision à ce niveau ?
			$themes = $this->Dossierep->Passagecommissionep->Commissionep->WebrsaCommissionep->themesTraites( $commissionep_id );
			$niveauFinal = Hash::get( $themes, Inflector::underscore($this->alias) );
			if( ( $niveauFinal == 'ep' ) && ( $niveauDecision == 'cg' ) ) {
				return array();
			}

			$query = array(
				'conditions' => array(
					'Dossierep.themeep' => Inflector::tableize( $this->alias ),
					'Dossierep.id IN ( '.$this->Dossierep->Passagecommissionep->sq(
						array(
							'fields' => array( 'passagescommissionseps.dossierep_id' ),
							'alias' => 'passagescommissionseps',
							'conditions' => array(
								'passagescommissionseps.commissionep_id' => $commissionep_id
							)
						)
					).' )'
				),
				'contain' => array(
					'Personne' => array(
						'Foyer' => array(
							'Adressefoyer' => array(
								'conditions' => array(
									'Adressefoyer.rgadr' => '01'
								),
								'Adresse'
							),
							'Dossierpcg66' => array(
								'Decisiondefautinsertionep66',
								'Decisiondossierpcg66' => array(
									'conditions' => array(
										'Decisiondossierpcg66.validationproposition' => 'O'
									),
									'order' => array( 'Decisiondossierpcg66.datevalidation DESC' ),
									'Decisionpdo'
								)
							)
						)
					),
					$this->alias => array(
						'Bilanparcours66',
						'Contratinsertion',
						'Orientstruct' => array(
							'Typeorient',
							'Structurereferente',
						),
						'Historiqueetatpe'
					),
					'Passagecommissionep' => array(
						'conditions' => array(
							'Passagecommissionep.commissionep_id' => $commissionep_id
						),
						'Decisiondefautinsertionep66' => array(
							'order' => array( 'Decisiondefautinsertionep66.etape DESC' )
						)
					)
				),
				// On trie pour avoir les dossiers non cachés (suite à la transformation en EPL parcours) en premier lieu
				'order' => array(
					'( CASE
						WHEN (
							SELECT decisionsdefautsinsertionseps66.decision
								FROM decisionsdefautsinsertionseps66
									INNER JOIN passagescommissionseps ON (
										passagescommissionseps.id = decisionsdefautsinsertionseps66.passagecommissionep_id
										AND decisionsdefautsinsertionseps66.etape = \'ep\'
										AND passagescommissionseps.dossierep_id = Dossierep.id
										AND passagescommissionseps.commissionep_id = \''.$commissionep_id.'\'
									)
							) NOT IN ( \''.implode( "', '", $this->decisionsEplParcours ).'\' ) THEN 1
						ELSE 0
					END )'
				)
			);

			return $query;
		}

		/**
		 * Sauvegarde des décisions de la commission (avant validation )
		 * @param array $data données de la commission EP à sauvegarder
		 * @param string $niveauDecision ep ou cg, selon le type de commission
		 * @return boolean
		 * FIXME: type_positionbilan -> {eplaudit,eplparc,attcga,attct,ajourne,annule} => ajouter traite
		 */

		 public function saveDecisions( $data, $niveauDecision ) {
			// Filtrage des données
			$themeData = Set::extract( $data, '/Decisiondefautinsertionep66' );
			if( empty( $themeData ) ) {
				return true;
			}
			else {
				$count = count ($themeData);
				for ($i = 0; $i < $count; $i++) {
					$query = "SELECT id FROM decisionsdefautsinsertionseps66 AS Decisiondefautinsertionep66 WHERE passagecommissionep_id='".$themeData[$i]['Decisiondefautinsertionep66']['passagecommissionep_id']."' AND etape ='".$themeData[$i]['Decisiondefautinsertionep66']['etape']."'";
					$exist = $this->query ($query);

					if (isset ($exist['0']['0']['id'])) {
						$themeData[$i]['Decisiondefautinsertionep66']['id'] = $exist['0']['0']['id'];
					}
				}

				$success = $this->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->saveAll( $themeData, array( 'atomic' => false ) );

				$passagescommissionseps_ids = Set::extract( $themeData, '/Decision'.Inflector::underscore( $this->alias ).'/passagecommissionep_id' );

				// Mise à jour de l'état du passage en commission EP
				$success = $this->Dossierep->Passagecommissionep->updateAllUnBound(
					array( 'Passagecommissionep.etatdossierep' => '\'decision'.$niveauDecision.'\'' ),
					array( '"Passagecommissionep"."id"' => $passagescommissionseps_ids )
				) && $success;

				return $success;
			}
		}

		/**
		 * Récupère l'id de la commission d'EP à partir d'une liste d'ids
		 * d'entrées de passages en commissions EP.
		 * @param array $passagescommissionseps_ids Ids des passages commissions des dossiers eps
		 *
		 */
		protected function _commissionepIdParPassagecommissionId( $passagescommissionseps_ids ) {

			return $this->Dossierep->Passagecommissionep->Commissionep->find(
				'first',
				array(
					'fields' => array( 'Commissionep.id', 'Commissionep.dateseance' ),
					'conditions' => array(
						'Commissionep.id IN ('
							.$this->Dossierep->Passagecommissionep->sq(
								array(
									'alias' => 'passagescommissionseps',
									'fields' => array( 'passagescommissionseps.commissionep_id' ),
									'conditions' => array(
										'passagescommissionseps.id' => $passagescommissionseps_ids
									),
									'contain' => false
								)
							)
						.')'
					),
					'contain' => false
				)
			);
		}


		/**
		 *	Génération d'un dossier PCG une fois l'avis de l'EP validé
		 * @param integer $commissionep_id L'id technique de la séance d'EP
		 * @param date $dateseanceCommission La date de la séance pour la date
		 *  de création de la PDO (dossierpcg.datereceptionpdo
		 * @param string $etape Etape à laquelle cette opération a lieu = ep
		 * @return array
		 * @access protected
		 */
		public function generateDossierpcg( $commissionep_id, $dateseanceCommission, $etape ) {
			$dateseanceCommission = preg_replace( '/^(.*) (.*)$/', '\1', $dateseanceCommission );

			$dossierseps = $this->find(
				'all',
				array(
					'conditions' => array(
						'Dossierep.id IN ( '.
							$this->Dossierep->Passagecommissionep->sq(
								array(
									'fields' => array(
										'passagescommissionseps.dossierep_id'
									),
									'alias' => 'passagescommissionseps',
									'conditions' => array(
										'passagescommissionseps.commissionep_id' => $commissionep_id
									)
								)
							)
						.' )',
						'Dossierep.themeep' => Inflector::tableize( $this->alias ),//FIXME: ailleurs aussi
					),
					'contain' => array(
						'Dossierep' => array(
							'Passagecommissionep' => array(
                                'conditions' => array(
                                    'Passagecommissionep.commissionep_id' => $commissionep_id
                                ),
								'Decisiondefautinsertionep66' => array(
									'conditions' => array(
										'Decisiondefautinsertionep66.etape' => $etape
									)
								)
							)
						)
					)
				)
			);

// 			debug($dossierseps);
// 			die();
			$success = true;
			foreach( $dossierseps as $i => $dossierep ) {

				$defautinsertionep66 = array( 'Defautinsertionep66' => $dossierep['Defautinsertionep66'] );
				$foyer = $this->Bilanparcours66->Orientstruct->Personne->Foyer->find(
					'first',
					array(
						'fields' => array(
							'Foyer.id'
						),
						'conditions' => array(
							'Bilanparcours66.id' => $defautinsertionep66['Defautinsertionep66']['bilanparcours66_id']
						),
						'joins' => array(
							array(
								'table' => 'personnes',
								'alias' => 'Personne',
								'type' => 'INNER',
								'conditions' => array( 'Personne.foyer_id = Foyer.id' )
							),
							array(
								'table' => 'orientsstructs',
								'alias' => 'Orientstruct',
								'type' => 'LEFT OUTER',
								'conditions' => array( 'Orientstruct.personne_id = Personne.id' )
							),
							array(
								'table' => 'bilansparcours66',
								'alias' => 'Bilanparcours66',
								'type' => 'INNER',
								'conditions' => array( 'Bilanparcours66.personne_id = Personne.id' )
							)
						),
						'contain' => false
					)
				);

				$originepdo = $this->Bilanparcours66->Dossierpcg66->Originepdo->find(
					'first',
					array(
						'fields' => array(
							'Originepdo.id'
						),
						'conditions' => array(
							'Originepdo.originepcg' => 'O'
						),
						'contain' => false
					)
				);

				$typepdo = $this->Bilanparcours66->Dossierpcg66->Typepdo->find(
					'first',
					array(
						'fields' => array(
							'Typepdo.id'
						),
						'conditions' => array(
							'Typepdo.originepcg' => 'O'
						),
						'contain' => false
					)
				);

				// Paramétrage incorrect
				if( empty( $originepdo ) || empty( $typepdo ) ) {
					$validationErrors = array();
					$originePdoMessage = 'aucune origine PDO n\'est origine par défaut d\'un dossier PDO venant d\'une EP';
					$typePdoMessage = 'aucune type de dossier PDO n\'est origine par défaut d\'un dossier PDO venant d\'une EP';

					if( empty( $originepdo ) && empty( $typepdo ) ) {
						$validationErrors[$i] = array( 'decision' => "Problème de paramétrage: {$originePdoMessage} et {$typePdoMessage}." );
					}
					else if( empty( $originepdo ) ) {
						$validationErrors[$i] = array( 'decision' => "Problème de paramétrage: {$originePdoMessage}." );
					}
					else if( empty( $typepdo ) ) {
						$validationErrors[$i] = array( 'decision' => "Problème de paramétrage: {$typePdoMessage}." );
					}

					$this->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->validationErrors = Set::merge(
						$this->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->validationErrors,
						$validationErrors
					);

					$success = false;
				}

				$dossier = $this->Bilanparcours66->Orientstruct->Personne->Foyer->Dossier->find(
					'first',
					array(
						'fields' => array(
							'Dossier.fonorg'
						),
						'conditions' => array(
							'Bilanparcours66.id' => $defautinsertionep66['Defautinsertionep66']['bilanparcours66_id']
						),
						'joins' => array(
							array(
								'table' => 'foyers',
								'alias' => 'Foyer',
								'type' => 'INNER',
								'conditions' => array( 'Foyer.dossier_id = Dossier.id' )
							),
							array(
								'table' => 'personnes',
								'alias' => 'Personne',
								'type' => 'INNER',
								'conditions' => array( 'Personne.foyer_id = Foyer.id' )
							),
							array(
								'table' => 'orientsstructs',
								'alias' => 'Orientstruct',
								'type' => 'LEFT OUTER',
								'conditions' => array( 'Orientstruct.personne_id = Personne.id' )
							),
							array(
								'table' => 'bilansparcours66',
								'alias' => 'Bilanparcours66',
								'type' => 'INNER',
								'conditions' => array( 'Bilanparcours66.personne_id = Personne.id' )
							)
						),
						'contain' => false
					)
				);

                $decisiondefautinsertion_id = Hash::get( $dossierep, 'Dossierep.Passagecommissionep.0.Decisiondefautinsertionep66.0.id' );
                $etatdossierep = Hash::get( $dossierep, 'Dossierep.Passagecommissionep.0.Decisiondefautinsertionep66.0.decision' );

				$dossierpcg66 = array(
					'Dossierpcg66' => array(
						'foyer_id' => $foyer['Foyer']['id'],
						'originepdo_id' => $originepdo['Originepdo']['id'],
						'typepdo_id' => $typepdo['Typepdo']['id'],
						'orgpayeur' => $dossier['Dossier']['fonorg'],
						'datereceptionpdo' => $dateseanceCommission,
						'haspiecejointe' => 0,
						'bilanparcours66_id' => $defautinsertionep66['Defautinsertionep66']['bilanparcours66_id'],
						'decisiondefautinsertionep66_id' => $decisiondefautinsertion_id
					)
				);

				$nbDossierPCG66PourDecisiondefautinsertion66 = $this->Bilanparcours66->Dossierpcg66->find(
					'count',
					array(
						'conditions' => array(
							'Dossierpcg66.decisiondefautinsertionep66_id' => $decisiondefautinsertion_id
						),
						'recursive' => -1
					)
				);

				// Le dossier PCG du foyer de l'allocataire n'existe pas encore
				// pour ce foyer et cette décision de thématique EP donc on peut le créer
//				if( $nbDossierPCG66PourDecisiondefautinsertion66 == 0 && $etatdossierep != 'reporte' ) {
				if( $nbDossierPCG66PourDecisiondefautinsertion66 == 0 && !in_array( $etatdossierep, array( 'reporte', 'annule' ) ) ) {
					$this->Bilanparcours66->Dossierpcg66->create( $dossierpcg66 );
					$success = $this->Bilanparcours66->Dossierpcg66->save( null, array( 'atomic' => false ) ) && $success;
				}
				// OU la commission a été annulée et le dossier PCG est à l'état annulationep
				if( $nbDossierPCG66PourDecisiondefautinsertion66 == 1 && !in_array( $etatdossierep, array( 'annulationep' ) ) ) {
					$succes = $this->query ('UPDATE dossierspcgs66 SET etatdossierpcg = \'attaffect\' WHERE decisiondefautinsertionep66_id = '.$decisiondefautinsertion_id) && $success;
				}
			}

			return $success;
		}



		/**
		* Prépare les données du formulaire d'un niveau de décision
		* en prenant en compte les données du bilan ou du niveau de décision
		* précédent.
		*
		* @param integer $commissionep_id L'id technique de la séance d'EP
		* @param array $datas Les données des dossiers
		* @param string $niveauDecision Le niveau de décision pour lequel il
		* 	faut préparer les données du formulaire
		* @return array
		* @access public
		*/

		public function prepareFormData( $commissionep_id, $datas, $niveauDecision ) {
			// Doit-on prendre une décision à ce niveau ?
			$themes = $this->Dossierep->Passagecommissionep->Commissionep->WebrsaCommissionep->themesTraites( $commissionep_id );
			$niveauFinal = Hash::get( $themes, Inflector::underscore($this->alias) );
			if( ( $niveauFinal == 'ep' ) && ( $niveauDecision == 'cg' ) ) {
				return array();
			}

			$formData = array();
			foreach( $datas as $key => $dossierep ) {
				$formData['Decisiondefautinsertionep66'][$key]['passagecommissionep_id'] = @$datas[$key]['Passagecommissionep'][0]['id'];

//				$formData['Decisiondefautinsertionep66'][$key]['id'] = $this->_prepareFormDataDecisionId( $dossierep );

				// On modifie les enregistrements de cette étape
				if( @$dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['etape'] == $niveauDecision ) {
					$formData['Decisiondefautinsertionep66'][$key] = @$dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0];
					$formData['Decisiondefautinsertionep66'][$key]['referent_id'] = implode( '_', array( $formData['Decisiondefautinsertionep66'][$key]['structurereferente_id'], $formData['Decisiondefautinsertionep66'][$key]['referent_id'] ) );
					$formData['Decisiondefautinsertionep66'][$key]['structurereferente_id'] = implode( '_', array( $formData['Decisiondefautinsertionep66'][$key]['typeorient_id'], $formData['Decisiondefautinsertionep66'][$key]['structurereferente_id'] ) );

					if( $niveauDecision == 'cg' && !in_array( $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][1]['decision'], $this->decisionsEplParcours ) ) {
						$formData['Decisiondefautinsertionep66'][$key]['field_type'] = 'hidden';
					}
				}
				// On ajoute les enregistrements de cette étape
				else {
					if( $niveauDecision == 'cg' ) {
						$formData['Decisiondefautinsertionep66'][$key]['decision'] = $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['decision'];
						$formData['Decisiondefautinsertionep66'][$key]['raisonnonpassage'] = $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['raisonnonpassage'];
						$formData['Decisiondefautinsertionep66'][$key]['commentaire'] = $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['commentaire'];
						$formData['Decisiondefautinsertionep66'][$key]['decisionsup'] = $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['decisionsup'];
						$formData['Decisiondefautinsertionep66'][$key]['typeorient_id'] = $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['typeorient_id'];
						$formData['Decisiondefautinsertionep66'][$key]['referent_id'] = implode( '_', array( $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['structurereferente_id'], $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['referent_id'] ) );
						$formData['Decisiondefautinsertionep66'][$key]['structurereferente_id'] = implode( '_', array( $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['typeorient_id'], $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['structurereferente_id'] ) );

						if( !in_array( $formData['Decisiondefautinsertionep66'][$key]['decision'], $this->decisionsEplParcours ) ) {
							$formData['Decisiondefautinsertionep66'][$key]['field_type'] = 'hidden';
						}

					}
				}

				// On nettoie les enregistements ne contenant que l'underscore
				foreach( array( 'referent_id', 'structurereferente_id' ) as $fieldName ) {
					$fieldValue = Hash::get( $formData, "Decisiondefautinsertionep66.{$key}.{$fieldName}" );
					if( $fieldValue === '_' ) {
						$formData['Decisiondefautinsertionep66'][$key][$fieldName] = null;
					}
				}

			}
			return $formData;
		}

		public function prepareFormDataUnique( $dossierep_id, $dossierep, $niveauDecision ) {
			$formData = array();
			return $formData;
		}

		/**
		* TODO: docs
		*/

		public function finaliser( $commissionep_id, $etape, $user_id ) {
			$commissionep = $this->Dossierep->Passagecommissionep->Commissionep->find(
				'first',
				array(
					'conditions' => array( 'Commissionep.id' => $commissionep_id ),
					'contain' => array(
						'Ep' => array(
							'Regroupementep'
						)
					)
				)
			);

			$niveauDecisionFinale = $commissionep['Ep']['Regroupementep'][Inflector::underscore( $this->alias )];

			$dossierseps = $this->find(
				'all',
				array(
					'conditions' => array(
						'Dossierep.id IN ( '.
							$this->Dossierep->Passagecommissionep->sq(
								array(
									'fields' => array(
										'passagescommissionseps.dossierep_id'
									),
									'alias' => 'passagescommissionseps',
									'conditions' => array(
										'passagescommissionseps.commissionep_id' => $commissionep_id
									)
								)
							)
						.' )',
						'Dossierep.themeep' => Inflector::tableize( $this->alias ),//FIXME: ailleurs aussi
					),
					'contain' => array(
						'Dossierep' => array(
							'Passagecommissionep' => array(
								'Decisiondefautinsertionep66' => array(
									'conditions' => array(
										'Decisiondefautinsertionep66.etape' => $etape
									)
								)
							)
						)
					)
				)
			);

			$success = true;
			$themeData = array();

			foreach( $dossierseps as $i => $dossierep ) {
				if( $niveauDecisionFinale == "decision{$etape}" ) {
					$defautinsertionep66 = array( 'Defautinsertionep66' => $dossierep['Defautinsertionep66'] );

					$defautinsertionep66['Defautinsertionep66']['decision'] = @$dossierep['Dossierep']['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['decision'];

					// Si réorientation, alors passage en EP Parcours "Réorientation ou maintien d'orientation"
					if( in_array( $defautinsertionep66['Defautinsertionep66']['decision'], $this->decisionsEplParcours ) ) {
						// En cas de demande de réorientation, l'EPL Audition va statuer et générer l'orientation
						$rgorient = $this->Bilanparcours66->Orientstruct->WebrsaOrientstruct->rgorientMax( $dossierep['Dossierep']['personne_id'] ) + 1;
						$origine = ( $rgorient > 1 ? 'reorientation' : 'cohorte' );

						$orientstruct = array(
							'Orientstruct' => array(
								'personne_id' => $dossierep['Dossierep']['personne_id'],
								'typeorient_id' => $dossierep['Dossierep']['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['typeorient_id'],
								'structurereferente_id' => suffix( $dossierep['Dossierep']['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['structurereferente_id'] ),
								'referent_id' => suffix( $dossierep['Dossierep']['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['referent_id'] ),
								'date_propo' => date( 'Y-m-d' ),
								'date_valid' => date( 'Y-m-d' ),
								'statut_orient' => 'Orienté',
								'user_id' => $user_id,
								'rgorient' => $rgorient,
								'origine' => $origine
							)
						);
						$this->Bilanparcours66->Orientstruct->create( $orientstruct );
						$success = $this->Bilanparcours66->Orientstruct->save( null, array( 'atomic' => false ) ) && $success;

						// Mise à jour de l'enregistrement de la thématique avec l'id de la nouvelle orientation
						$success = $success && $this->updateAllUnBound(
							array( "\"{$this->alias}\".\"nvorientstruct_id\"" => $this->Bilanparcours66->Orientstruct->id ),
							array( "\"{$this->alias}\".\"id\"" => $dossierep[$this->alias]['id'] )
						);

						// Clôture du référent du parcours actuel
						$this->Bilanparcours66->Orientstruct->Personne->PersonneReferent->updateAllUnBound(
							array( 'PersonneReferent.dfdesignation' => "'".date( 'Y-m-d' )."'" ),
							array(
								'"PersonneReferent"."personne_id"' => $dossierep['Dossierep']['personne_id'],
								'"PersonneReferent"."dfdesignation" IS NULL'
							)
						);

						// Création du nouveau référent du parcours s'il a été désigné
						if( !empty( $dossierep['Dossierep']['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['referent_id'] ) ) {
							$referent = array(
								'PersonneReferent' => array(
									'personne_id' => $dossierep['Dossierep']['personne_id'],
									'referent_id' => $dossierep['Dossierep']['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['referent_id'],
									'dddesignation' => date( 'Y-m-d' ),
									'structurereferente_id' => $dossierep['Dossierep']['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]['structurereferente_id'],
									'user_id' => $user_id
								)
							);

							$this->Bilanparcours66->Orientstruct->Personne->PersonneReferent->create( $referent );
							$success = $this->Bilanparcours66->Orientstruct->Personne->PersonneReferent->save( null, array( 'atomic' => false ) ) && $success;
						}
					}

					//	Ancien emplacement de la génération du dossierpcg66
					$this->create( $defautinsertionep66 );
					$success = $this->save( null, array( 'atomic' => false ) ) && $success;
				}

				$themeData[] = isset($dossierep['Dossierep']['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0]) ? array( 'Decisiondefautinsertionep66' => $dossierep['Dossierep']['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0] ) : null;
			}

			// Mise à jour de la position du bilan de parcours
			if( !empty( $themeData ) ) {
				$passagescommissionseps_ids = Hash::extract( $themeData, '{n}.Decisiondefautinsertionep66.passagecommissionep_id' );
				$success = $this->Bilanparcours66->WebrsaBilanparcours66->updatePositionBilanDecisionsEp( $this->name, $themeData, $etape, $passagescommissionseps_ids ) && $success;
			}

			return $success;
		}

		/**
		* FIXME: et qui ne sont pas passés en EP pour ce motif dans un délai de moins de 1 mois (paramétrable)
		*/

		protected function _qdSelection( $datas, $mesCodesInsee, $filtre_zone_geo ) {
			// Récupération des types d'orientation de type EMPLOI
			$typeOrientEmploi = implode(',', $this->Orientstruct->Typeorient->listIdTypeOrient('EMPLOI'));

			$Situationdossierrsa = ClassRegistry::init( 'Situationdossierrsa' );
			$queryData = array(
				'fields' => array(
					'Personne.id',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.nir',
					$this->Dossierep->Personne->Foyer->sqVirtualField( 'enerreur', true ),
					'Orientstruct.id',
					'Orientstruct.personne_id',
					'Orientstruct.date_valid',
					'"Situationdossierrsa"."etatdosrsa"',
				),
				'contain' => false,
				'joins' => array(
					array(
						'table'      => 'prestations', // FIXME:
						'alias'      => 'Prestation',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'Personne.id = Prestation.personne_id',
							'Prestation.natprest' => 'RSA',
							'Prestation.rolepers' => array( 'DEM', 'CJT' ),
						)
					),
					array(
						'table'      => 'foyers',
						'alias'      => 'Foyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Personne.foyer_id = Foyer.id' )
					),
					array(
						'table'      => 'adressesfoyers',
						'alias'      => 'Adressefoyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'Adressefoyer.foyer_id = Foyer.id',
							'Adressefoyer.rgadr' => '01'
						)
					),
					array(
						'table'      => 'adresses',
						'alias'      => 'Adresse',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Adressefoyer.adresse_id = Adresse.id' )
					),
					array(
						'table'      => 'dossiers',
						'alias'      => 'Dossier',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Dossier.id = Foyer.dossier_id' )
					),
					array(
						'table'      => 'situationsdossiersrsa',
						'alias'      => 'Situationdossierrsa',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Situationdossierrsa.dossier_id = Dossier.id /*AND ( Situationdossierrsa.etatdosrsa IN ( \''.implode( '\', \'', $Situationdossierrsa->etatOuvert() ).'\' ) )*/' )
					),
					array(
						'table'      => 'calculsdroitsrsa', // FIXME:
						'alias'      => 'Calculdroitrsa',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'Personne.id = Calculdroitrsa.personne_id',
							'Calculdroitrsa.toppersdrodevorsa' => '1',
						)
					),
					array(
						'table'      => 'orientsstructs', // FIXME:
						'alias'      => 'Orientstruct',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'Personne.id = Orientstruct.personne_id',
							// La dernière
							'Orientstruct.id IN (
										SELECT o.id
											FROM orientsstructs AS o
											WHERE
												o.personne_id = Personne.id
												AND o.date_valid IS NOT NULL
											ORDER BY o.date_valid DESC
											LIMIT 1
							)',
							// en emploi
							'Orientstruct.typeorient_id IN (
								SELECT t.id
									FROM typesorients AS t
									WHERE t.id in ('. $typeOrientEmploi . ')
							)'// FIXME
						)
					),
				),
				'conditions' => array(
					// On ne veut pas les personnes ayant un dossier d'EP en cours de traitement
					'Personne.id NOT IN ('.$this->Dossierep->sq(
						array(
							'fields' => array( 'dossierseps1.personne_id' ),
							'alias' => 'dossierseps1',
							'conditions' => array(
								'dossierseps1.actif' => '1',
								'dossierseps1.personne_id = Personne.id',
								'dossierseps1.themeep' => 'defautsinsertionseps66',
								'dossierseps1.id IN ('.$this->Dossierep->Passagecommissionep->sq(
									array(
										'fields' => array( 'passagescommissionseps1.dossierep_id' ),
										'alias' => 'passagescommissionseps1',
										'conditions' => array(
											'passagescommissionseps1.dossierep_id = dossierseps1.id',
											'passagescommissionseps1.etatdossierep' => array( 'associe', 'decisionep', 'decisioncg', 'reporte' )
										)
									)
								).')'
							)
						)
					).')',
					// Ni celles qui ont un dossier d'EP pour la thématique ayant été traité en commission plus récemment que 2 mois
					'Personne.id NOT IN ('.$this->Dossierep->sq(
						array(
							'fields' => array( 'dossierseps2.personne_id' ),
							'alias' => 'dossierseps2',
							'conditions' => array(
								'dossierseps2.personne_id = Personne.id',
								'dossierseps2.themeep' => 'defautsinsertionseps66',
								'dossierseps2.id IN ('.$this->Dossierep->Passagecommissionep->sq(
									array(
										'fields' => array( 'passagescommissionseps2.dossierep_id' ),
										'alias' => 'passagescommissionseps2',
										'conditions' => array(
											'passagescommissionseps2.dossierep_id = dossierseps2.id',
											'passagescommissionseps2.etatdossierep' => array( 'traite', 'annule' )
										),
										'joins' => array(
											array(
												'table' => 'commissionseps',
												'alias' => 'commissionseps',
												'type' => 'INNER',
												'conditions' => array(
													'commissionseps.id = passagescommissionseps2.commissionep_id',
													'commissionseps.dateseance >=' => date( 'Y-m-d', strtotime( '-2 mons' ) ) // FIXME: paramétrage
												)
											)
										)
									)
								).')'
							)
						)
					).')',
					// Ni celles dont le dossier n'a pas encore été associé à une commission
					'Personne.id NOT IN ('.$this->Dossierep->sq(
						array(
							'fields' => array( 'dossierseps3.personne_id' ),
							'alias' => 'dossierseps3',
							'conditions' => array(
								'dossierseps3.actif' => '1',
								'dossierseps3.personne_id = Personne.id',
								'dossierseps3.themeep' => 'defautsinsertionseps66',
								'dossierseps3.id NOT IN ('.$this->Dossierep->Passagecommissionep->sq(
									array(
										'fields' => array( 'passagescommissionseps3.dossierep_id' ),
										'alias' => 'passagescommissionseps3',
										'conditions' => array(
											'passagescommissionseps3.dossierep_id = dossierseps3.id',
										)
									)
								).')'
							)
						)
					).')',
				)
			);


			// On a un filtre par défaut sur l'état du dossier si celui-ci n'est pas renseigné dans le formulaire.
			$Situationdossierrsa = ClassRegistry::init( 'Situationdossierrsa' );
			$etatdossier = Set::extract( $datas, 'Situationdossierrsa.etatdosrsa' );
			if( !isset( $datas['Situationdossierrsa']['etatdosrsa'] ) || empty( $datas['Situationdossierrsa']['etatdosrsa'] ) ) {
				$datas['Situationdossierrsa']['etatdosrsa']  = $Situationdossierrsa->etatOuvert();
			}

			/// Filtre zone géographique
			$queryData['conditions'] = $this->conditionsAdresse( $queryData['conditions'], $datas, $filtre_zone_geo, $mesCodesInsee );
			$queryData['conditions'] = $this->conditionsPersonneFoyerDossier( $queryData['conditions'], $datas );
			$queryData['conditions'] = $this->conditionsDernierDossierAllocataire( $queryData['conditions'], $datas );

			if( isset( $datas['Orientstruct']['date_valid'] ) && !empty( $datas['Orientstruct']['date_valid'] ) ) {
				if( valid_int( $datas['Orientstruct']['date_valid']['year'] ) ) {
				$queryData['conditions'][] = 'EXTRACT(YEAR FROM Orientstruct.date_valid) = '.$datas['Orientstruct']['date_valid']['year'];
				}
				if( valid_int( $datas['Orientstruct']['date_valid']['month'] ) ) {
					$queryData['conditions'][] = 'EXTRACT(MONTH FROM Orientstruct.date_valid) = '.$datas['Orientstruct']['date_valid']['month'];
				}
			}

			$identifiantpe = Set::classicExtract( $datas, 'Historiqueetatpe.identifiantpe' );

			if ( !empty( $identifiantpe ) ) {
				$queryData['conditions'][] = ClassRegistry::init( 'Historiqueetatpe' )->conditionIdentifiantpe( $identifiantpe );
			}

			$queryData = $this->Dossierep->Personne->PersonneReferent->completeQdReferentParcours( $queryData, $datas );


			return $queryData;
		}

		/**
		*
		*/

		public function qdNonInscrits( $datas, $mesCodesInsee, $filtre_zone_geo ) {
			$queryData = $this->_qdSelection( $datas, $mesCodesInsee, $filtre_zone_geo );
			$qdNonInscrits = $this->Historiqueetatpe->Informationpe->qdNonInscrits();

			$queryData['fields'] = array_merge( $queryData['fields'] ,$qdNonInscrits['fields'] );
			$queryData['joins'] = array_merge( $queryData['joins'] ,$qdNonInscrits['joins'] );
			$queryData['conditions'] = array_merge( $queryData['conditions'] ,$qdNonInscrits['conditions'] );
			$queryData['order'] = $qdNonInscrits['order'];

			return $queryData;
		}

		/**
		*
		*/

		public function qdRadies( $datas, $mesCodesInsee, $filtre_zone_geo ) {
			// FIXME: et qui ne sont pas passés dans une EP pour ce motif depuis au moins 1 mois (?)
			$queryData = $this->_qdSelection( $datas, $mesCodesInsee, $filtre_zone_geo );
			$qdRadies = $this->Historiqueetatpe->Informationpe->qdRadies();
			$queryData['fields'] = array_merge( $queryData['fields'] ,$qdRadies['fields'] );
			$queryData['joins'] = array_merge( $queryData['joins'] ,$qdRadies['joins'] );
			$queryData['conditions'] = array_merge( $queryData['conditions'] ,$qdRadies['conditions'] );
			$queryData['order'] = $qdRadies['order'];

			return $queryData;
		}

		/**
		 * Retourne une partie de querydata concernant la thématique pour le PV d'EP.
		 *
		 * @return array
		 */
		public function qdProcesVerbal() {
			$querydata = array(
				'fields' => array_merge(
					$this->fields(),
					$this->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->fields(),
					$this->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->Typeorient->fields(),
					$this->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->Structurereferente->fields(),
					$this->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->Referent->fields(),
					$this->Dossierep->Defautinsertionep66->Bilanparcours66->fields(),
					array_words_replace(
						$this->Dossierep->Defautinsertionep66->Bilanparcours66->Structurereferente->fields(),
						array( 'Structurereferente' => 'Structurereferentebilan' )
					),
					array_words_replace(
						$this->Dossierep->Defautinsertionep66->Bilanparcours66->Referent->fields(),
						array( 'Referent' => 'Referentbilan' )
					)
				),
				'joins' => array(
					array(
						'table'      => 'defautsinsertionseps66',
						'alias'      => 'Defautinsertionep66',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Defautinsertionep66.dossierep_id = Dossierep.id' ),
					),
					array(
						'table'      => 'decisionsdefautsinsertionseps66',
						'alias'      => 'Decisiondefautinsertionep66',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'Decisiondefautinsertionep66.passagecommissionep_id = Passagecommissionep.id',
							'Decisiondefautinsertionep66.etape' => 'ep'
						),
					),
					$this->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossierep->Defautinsertionep66->join( 'Bilanparcours66', array( 'type' => 'INNER' ) ),
					array_words_replace(
						$this->Dossierep->Defautinsertionep66->Bilanparcours66->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						array( 'Structurereferente' => 'Structurereferentebilan' )
					),
					array_words_replace(
						$this->Dossierep->Defautinsertionep66->Bilanparcours66->join( 'Referent', array( 'type' => 'INNER' ) ),
						array( 'Referent' => 'Referentbilan' )
					)
				)
			);


			$modeleDecisionPart = 'decdefins'.Configure::read( 'Cg.departement' );
			return array_words_replace(
				$querydata,
				array(
					'Typeorient' => "Typeorient{$modeleDecisionPart}",
					'Structurereferente' => "Structurereferente{$modeleDecisionPart}",
					'Referent' => "Referent{$modeleDecisionPart}",
				)
			);
		}

		/**
		* Récupération des informations propres au dossier devant passer en EP
		* avant liaison avec la commission d'EP
		*/

		public function getCourrierInformationPdf( $dossierep_id, $user_id ) {
			$gedooo_data = $this->find(
				'first',
				array(
					'fields' => array_merge(
						$this->fields(),
						$this->Dossierep->fields(),
						$this->Dossierep->Personne->fields(),
						$this->Dossierep->Personne->Foyer->fields(),
						$this->Dossierep->Personne->Foyer->Dossier->fields(),
						$this->Dossierep->Personne->Foyer->Adressefoyer->fields(),
						$this->Dossierep->Personne->Foyer->Adressefoyer->Adresse->fields(),
						$this->Bilanparcours66->fields(),
						$this->Bilanparcours66->Structurereferente->fields(),
						$this->Contratinsertion->fields(),
						$this->Orientstruct->fields()
					),
					'joins' => array(
						$this->join( 'Dossierep', array( 'type' => 'INNER' ) ),
						$this->Dossierep->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Dossierep->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Dossierep->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Dossierep->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$this->Dossierep->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Bilanparcours66', array( 'type' => 'INNER' ) ),
						$this->join( 'Contratinsertion', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Orientstruct', array( 'type' => 'LEFT OUTER' ) ),
						$this->Bilanparcours66->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
					),
					'recursive' => -1,
					'conditions' => array(
						'Dossierep.id' => $dossierep_id,
						$this->Dossierep->Personne->Foyer->sqLatest( 'Adressefoyer', 'dtemm', array( 'Adressefoyer.rgadr' => '01' ) )
					)
				)
			);

			$Option = ClassRegistry::init( 'Option' );
			$options =  Set::merge(
				array(
					'Personne' => array(
						'qual' => $Option->qual()
					)
				),
				$this->enums()
			);

			$user = $this->Bilanparcours66->User->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => $user_id
					),
					'contain' => array('Serviceinstructeur')
				)
			);
			$gedooo_data = Set::merge( $gedooo_data, $user );

            // Non inscription PE
                //  Bilanparcours66.examenauditionpe = noninscriptionpe --> personne_id 35253
                // Defautinsertionep66.origine = noninscriptionpe
            //
            // Radiation PE
                // Bilanparcours66.examenauditionpe = radidationpe --> personne_id 33507
                // Defautinsertionep66.origine = radidationpe
            //
            // Audition pour Non conclusion (pas de CER)
                // Bilanparcours66.examenaudition = DOD --> personne_id 42
                // Bilanparcours66.proposition = audition
                // Defautinsertionep66.origine = bilanparcours
                // Bilanparcours66.orientstruct_id IS NOT NULL
            //
            // Audition pour Non Conclusion Sans orientation
                // Bilanparcours66.examenaudition = DOD --> personne_id 120261
                // Bilanparcours66.proposition = audition
                // Defautinsertionep66.origine = bilanparcours
                // Bilanparcours66.orientstruct_id IS NULL
            //
            // Audition pour Non Respect
                // Bilanparcours66.examenaudition = DRD --> personne_id 74
                // Bilanparcours66.proposition = audition
                // Defautinsertionep66.origine = bilanparcours

            $origineAudition = $gedooo_data['Defautinsertionep66']['origine'];
            $modele = null;

            if( $origineAudition == 'noninscriptionpe' ) {
                $modele = 'noninscriptionpe_courrierinformationavantep.odt';
            }
            else if($origineAudition == 'radiationpe' ) {
                $modele = 'radiationpe_courrierinformationavantep.odt';
            }
            else if($origineAudition == 'bilanparcours' ) {
                $examenaudition = $gedooo_data['Bilanparcours66']['examenaudition'];
                $proposition = $gedooo_data['Bilanparcours66']['proposition'];
                $orientstruct_id = $gedooo_data['Bilanparcours66']['orientstruct_id'];

                if( $examenaudition == 'DOD' && $proposition == 'audition' && !empty( $orientstruct_id ) ){
                    $modele = 'nonconclusioncer_courrierinformationavantep.odt';
                }
                else if( $examenaudition == 'DOD' && $proposition == 'audition' && empty( $orientstruct_id ) ){
                    $modele = 'nonconclusionorientation_courrierinformationavantep.odt';
                }
                else if( $examenaudition == 'DRD' && $proposition == 'audition' ){
                    $modele = 'nonrespect_courrierinformationavantep.odt';
                }
            }

            if( is_null( $modele ) ) {
                $this->cakeError( 'error500' );
            }

			$this->id = $gedooo_data['Defautinsertionep66']['id'];
			$this->saveField( 'dateimpressionconvoc', date( 'Y-m-d' ) );

//			return $this->ged( $gedooo_data, "{$this->alias}/{$gedooo_data[$this->alias]['origine']}_courrierinformationavantep.odt" );
            return $this->ged( $gedooo_data, "{$this->alias}/{$modele}", false, $options  );
		}

		/**
		* Récupération du courrier de convocation à l'allocataire pour un passage
		* en commission donné.
		*/
		public function getConvocationBeneficiaireEpPdf( $passagecommissionep_id ) {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$datas = Cache::read( $cacheKey );

			if( $datas === false ) {
				$datas = $this->_qdConvocationBeneficiaireEpPdf();

				Cache::write( $cacheKey, $datas );
			}

			$datas['querydata']['conditions']['Passagecommissionep.id'] = $passagecommissionep_id;

			$datas['querydata']['joins'] = array_merge(
				$datas['querydata']['joins'],
				array(
					$this->join( 'Bilanparcours66' ),
					$this->Bilanparcours66->join( 'Structurereferente' ),
					array_words_replace(
						$this->Bilanparcours66->join( 'User', array( 'type' => 'LEFT OUTER' ) ),
						array(
							'User' => 'Userbilan'
						)
					)
				)
			);

			$datas['querydata']['fields'] = Set::merge(
				$datas['querydata']['fields'],
				array_merge(
					$this->Bilanparcours66->fields(),
					$this->Bilanparcours66->Structurereferente->fields(),
					array_words_replace(
						$this->Bilanparcours66->User->fields(),
						array(
							'User' => 'Userbilan'
						)
					)
				)
			);

			$virtualFields = $this->Dossierep->Passagecommissionep->virtualFields;
			$this->Dossierep->Passagecommissionep->virtualFields = array();
			$gedooo_data = $this->Dossierep->Passagecommissionep->find( 'first', $datas['querydata'] );
			$this->Dossierep->Passagecommissionep->virtualFields = $virtualFields;

			$modeleOdt = 'Commissionep/convocationep_beneficiaire.odt';

			if( empty( $gedooo_data ) ) {
				return false;
			}

			return $this->ged(
				$gedooo_data,
				$modeleOdt,
				false,
				$datas['options']
			);
		}

		/**
		* Fonction retournant un querydata qui va permettre de retrouver des dossiers d'EP
		*/
		public function qdListeDossier( $commissionep_id = null ) {
			$return = array(
				'fields' => array(
					'Dossierep.id',
					'Personne.id',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Adresse.nomcom',
					'Dossierep.created',
					'Dossierep.themeep',
					'Passagecommissionep.id',
					'Passagecommissionep.commissionep_id',
					'Passagecommissionep.etatdossierep',
					'Passagecommissionep.heureseance',
				)
			);

			if( !empty( $commissionep_id ) ) {
				$join = array(
					'alias' => 'Dossierep',
					'table' => 'dossierseps',
					'type' => 'INNER',
					'conditions' => array(
						'Dossierep.id = '.$this->alias.'.dossierep_id'
					)
				);
			}
			else {
				$join = array(
					'alias' => $this->alias,
					'table' => Inflector::tableize( $this->alias ),
					'type' => 'INNER',
					'conditions' => array(
						'Dossierep.id = '.$this->alias.'.dossierep_id'
					)
				);
			}

			$return['joins'] = array(
				$join,
				array(
					'alias' => 'Personne',
					'table' => 'personnes',
					'type' => 'INNER',
					'conditions' => array(
						'Dossierep.personne_id = Personne.id'
					)
				),
				array(
					'alias' => 'Foyer',
					'table' => 'foyers',
					'type' => 'INNER',
					'conditions' => array(
						'Personne.foyer_id = Foyer.id'
					)
				),
				array(
					'alias' => 'Dossier',
					'table' => 'dossiers',
					'type' => 'INNER',
					'conditions' => array(
						'Foyer.dossier_id = Dossier.id'
					)
				),
				array(
					'alias' => 'Adressefoyer',
					'table' => 'adressesfoyers',
					'type' => 'INNER',
					'conditions' => array(
						'Adressefoyer.foyer_id = Foyer.id',
						'Adressefoyer.rgadr' => '01'
					)
				),
				array(
					'alias' => 'Adresse',
					'table' => 'adresses',
					'type' => 'INNER',
					'conditions' => array(
						'Adressefoyer.adresse_id = Adresse.id'
					)
				),
				array(
					'alias' => 'Passagecommissionep',
					'table' => 'passagescommissionseps',
					'type' => 'LEFT OUTER',
					'conditions' => Set::merge(
						array( 'Passagecommissionep.dossierep_id = Dossierep.id' ),
						empty( $commissionep_id ) ? array() : array(
							'OR' => array(
								'Passagecommissionep.commissionep_id IS NULL',
								'Passagecommissionep.commissionep_id' => $commissionep_id
							)
						)
					)
				)
			);

			return $return;
		}

		/**
		* Récupération de la décision suite au passage en commission d'un dossier
		* d'EP pour un certain niveau de décision. On revoie la chaîne vide car on
		* n'est pas sensés imprimer de décision pour la commission.
		*/
		public function getDecisionPdf( $passagecommissionep_id, $user_id = null  ) {
			$modele = $this->alias;
			$modeleDecisions = 'Decision'.Inflector::underscore( $this->alias );

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$datas = Cache::read( $cacheKey );

			if( $datas === false ) {
				$datas['querydata'] = $this->_qdDecisionPdf();

				// Bilan de parcours
				$datas['querydata']['fields'] = array_merge( $datas['querydata']['fields'], $this->Bilanparcours66->fields() );
				$datas['querydata']['joins'][] = $this->join( 'Bilanparcours66' );

				// Orientation liée au bilan de parcours
				$datas['querydata']['fields'] = array_merge( $datas['querydata']['fields'], $this->Bilanparcours66->Orientstruct->fields() );
				$datas['querydata']['joins'][] = $this->Bilanparcours66->join( 'Orientstruct' );

				// Structure référente liée àl'orientation
				$datas['querydata']['fields'] = array_merge( $datas['querydata']['fields'], $this->Bilanparcours66->Orientstruct->Structurereferente->fields() );
				$datas['querydata']['joins'][] = $this->Bilanparcours66->Orientstruct->join( 'Structurereferente' );

				// Référent orientant lié à l'orientation
				$datas['querydata']['fields'] = array_merge( $datas['querydata']['fields'], $this->Bilanparcours66->Orientstruct->Referentorientant->fields() );
				$datas['querydata']['joins'][] = $this->Bilanparcours66->Orientstruct->join( 'Referentorientant' );

                // Référent lié au bilan
                $datas['querydata']['fields'] = array_merge( $datas['querydata']['fields'], $this->Bilanparcours66->Referent->fields() );
                $datas['querydata']['joins'][] = $this->Bilanparcours66->join( 'Referent' );

				// Jointures et champs décisions
				$modelesProposes = array(
					'Typeorient' => "{$modeleDecisions}typeorient",
					'Structurereferente' => "{$modeleDecisions}structurereferente",
					'Referent' => "{$modeleDecisions}referent"
				);

				foreach( $modelesProposes as $modelePropose => $modeleProposeAliase ) {
					$replacement = array( $modelePropose => $modeleProposeAliase );

					$datas['querydata']['joins'][] = array_words_replace( $this->Dossierep->Passagecommissionep->{$modeleDecisions}->join( $modelePropose ), $replacement );
					$datas['querydata']['fields'] = array_merge(
						$datas['querydata']['fields'],
						array_words_replace(
							$this->Dossierep->Passagecommissionep->{$modeleDecisions}->{$modelePropose}->fields(),
							$replacement
						)
					);
				}

				// Traductions
				$datas['options'] = $this->Dossierep->Passagecommissionep->{$modeleDecisions}->enums();
				$datas['options']['Personne']['qual'] = ClassRegistry::init( 'Option' )->qual();

				Cache::write( $cacheKey, $datas );
			}


			$datas['querydata']['conditions']['Passagecommissionep.id'] = $passagecommissionep_id;
			// INFO: permet de ne pas avoir d'erreur avec les virtualFields aliasés
			$virtualFields = $this->Dossierep->Passagecommissionep->virtualFields;
			$this->Dossierep->Passagecommissionep->virtualFields = array();
			$gedooo_data = $this->Dossierep->Passagecommissionep->find( 'first', $datas['querydata'] );
			$this->Dossierep->Passagecommissionep->virtualFields = $virtualFields;

			if( empty( $gedooo_data ) || !isset( $gedooo_data[$modeleDecisions] ) || empty( $gedooo_data[$modeleDecisions] ) ) {
				return false;
			}

			$user = $this->Dossierep->Passagecommissionep->User->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => $user_id
					),
					'contain' => array(
						'Serviceinstructeur'
					)
				)
			);
			$gedooo_data = Set::merge( $gedooo_data, $user );
// debug($gedooo_data);
// die();
			// Choix du modèle de document
			$modeleOdt = "{$this->alias}/decision_reorientation.odt";

			return $this->_getOrCreateDecisionPdf( $passagecommissionep_id, $gedooo_data, $modeleOdt, $datas['options'] );
		}

		/**
		*
		*/

		public function search( $mesCodesInsee, $filtre_zone_geo, $params ) {
			$conditions = array();

			$date_impression = Set::extract( $params, 'Defautinsertionep66.isimprime' );

			$conditions = $this->conditionsAdresse( $conditions, $params, $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsPersonneFoyerDossier( $conditions, $params );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $params );

			// Statut impression
			if( !empty( $date_impression ) && in_array( $date_impression, array( 'I', 'N' ) ) ) {
				if( $date_impression == 'I' ) {
					$conditions[] = 'Defautinsertionep66.dateimpressionconvoc IS NOT NULL';
				}
				else {
					$conditions[] = 'Defautinsertionep66.dateimpressionconvoc IS NULL';
				}
			}
			else {
				$conditions[] = "Defautinsertionep66.dateimpressionconvoc IS NULL";
			}


			$query = array(
				'fields' => array(
					'Dossierep.id',
					'Dossierep.created',
					'Defautinsertionep66.id',
					'Dossier.matricule',
					'Personne.nir',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Adresse.numcom',
					'Adresse.nomcom'
				),
				'conditions' => $conditions,
				'joins' => array(
					array(
						'table' => 'defautsinsertionseps66',
						'alias' => 'Defautinsertionep66',
						'type' => 'INNER',
						'conditions' => array( 'Dossierep.id = Defautinsertionep66.dossierep_id' )
					),
					array(
						'alias' => 'Personne',
						'table' => 'personnes',
						'type' => 'INNER',
						'conditions' => array( 'Dossierep.personne_id = Personne.id' )
					),
					array(
						'alias' => 'Foyer',
						'table' => 'foyers',
						'type' => 'INNER',
						'conditions' => array(
							'Personne.foyer_id = Foyer.id'
						)
					),
					array(
						'alias' => 'Dossier',
						'table' => 'dossiers',
						'type' => 'INNER',
						'conditions' => array(
							'Foyer.dossier_id = Dossier.id'
						)
					),
					array(
						'alias' => 'Adressefoyer',
						'table' => 'adressesfoyers',
						'type' => 'INNER',
						'conditions' => array(
							'Adressefoyer.foyer_id = Foyer.id',
							'Adressefoyer.rgadr' => '01'
						)
					),
					array(
						'alias' => 'Adresse',
						'table' => 'adresses',
						'type' => 'INNER',
						'conditions' => array(
							'Adressefoyer.adresse_id = Adresse.id'
						)
					)
				),
				'contain' => false
			);

			$query = $this->Dossierep->Personne->PersonneReferent->completeQdReferentParcours( $query, $params );

			return $query;
		}

		/**
		 *
		 */
		public function nbDossiersATraiterCg( $commissionep_id ) {
			$conditions = array(
				'Dossierep.themeep' => Inflector::tableize( $this->name ),
				'Dossierep.id IN ( '.$this->Dossierep->Passagecommissionep->sq(
					array(
						'alias' => 'passagescommissionseps',
						'fields' => array(
							'passagescommissionseps.dossierep_id'
						),
						'conditions' => array(
							'passagescommissionseps.commissionep_id' => $commissionep_id,
							'passagescommissionseps.etatdossierep <>' => "decisionep",
						),
						'joins' => array(
							array_words_replace(
								$this->Dossierep->Passagecommissionep->join(
									'Decisiondefautinsertionep66',
									array(
										'type' => 'INNER',
										'conditions' => array(
											'Decisiondefautinsertionep66.decision' => $this->decisionsEplParcours
										)
									)
								),
								array(
									'Passagecommissionep' => 'passagescommissionseps',
									'Decisiondefautinsertionep66' => 'decisionsdefautsinsertionseps66'
								)
							)
						)
					)
				).' )'
			);

			return $this->Dossierep->find( 'count', array( 'conditions' => $conditions ) );
		}


		/**
		 * Vérifi si une personne est en attente d'une décision EP
		 * @param integer $personne_id
		 * @return Boolean
		 */
		public function hasDossierepEnCours( $personne_id ){
			$query = array(
				'fields' => array_merge(
					$this->Dossierep->Passagecommissionep->fields(),
					$this->Dossierep->Passagecommissionep->Dossierep->fields(),
					$this->Dossierep->Passagecommissionep->Commissionep->fields(),
					$this->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->fields()
				),
				'joins' => array(
					$this->Dossierep->Passagecommissionep->join( 'Dossierep', array( 'type' => 'INNER' ) ),
					$this->Dossierep->Passagecommissionep->join( 'Commissionep', array( 'type' => 'INNER' ) ),
					$this->Dossierep->Passagecommissionep->join(
						'Decisiondefautinsertionep66',
						array(
							'type' => 'INNER',
							'conditions' => array(
								'Decisiondefautinsertionep66.etape' => 'ep',
								'Decisiondefautinsertionep66.decision' => $this->decisionsEplParcours
							)
						)
					),
				),
				'conditions' => array(
					'Dossierep.personne_id' => $personne_id,
					'Commissionep.etatcommissionep' => array( 'traiteep', 'decisioncg' ),
				)
			);

			$result = $this->Dossierep->Passagecommissionep->find( 'all', $query );
			return !empty( $result );
		}

		/**
		 * Retourne le querydata qui sera utilisé par la thématique pour la
		 * sélection des dossiers à associer à une commission d'EP donnée.
		 *
		 * @param integer $commissionep_id
		 * @return array
		 */
		public function qdListeDossierChoose($commissionep_id = null) {
			$query = parent::qdListeDossierChoose($commissionep_id);

			$query['fields'][] = 'Dossierep.is_reporte';

			return $query;
		}
	}
?>