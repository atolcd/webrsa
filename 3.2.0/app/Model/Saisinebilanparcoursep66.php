<?php
	/**
	 * Code source de la classe Saisinebilanparcoursep66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Thematiqueep', 'Model/Abstractclass' );

	/**
	 * Saisines d'EP pour les bilans de parcours pour le conseil général du
	 * département 66.
	 *
	 * Une saisine regoupe plusieurs thèmes des EPs pour le CG 66.
	 *
	 * @package app.Model
	 */
	class Saisinebilanparcoursep66 extends Thematiqueep
	{
		public $name = 'Saisinebilanparcoursep66';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate',
			'Dependencies',
			'Gedooo.Gedooo'
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
			'Nvorientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'nvorientstruct_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Nvcontratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'nvcontratinsertion_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typeorient' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'typeorient_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		/**
		* Chemin relatif pour les modèles de documents .odt utilisés lors des
		* impressions. Utiliser %s pour remplacer par l'alias.
		*/
		public $modelesOdt = array(
			// Convocation EP
			'Commissionep/convocationep_beneficiaire.odt',
			// Décision EP (décision CG)
			'%s/decision_maintien_avec_changement.odt',
			'%s/decision_maintien_sans_changement.odt',
			'%s/decision_reorientation.odt',
			'%s/decision_reporte.odt',
			'%s/decision_annule.odt',
		);

		public $validate = array(
			'structurereferente_id' => array(
				'dependentForeignKeys' => array(
					'rule' => array( 'dependentForeignKeys', 'Structurereferente', 'Typeorient' ),
					'message' => 'La structure référente ne correspond pas au type d\'orientation',
				),
			),
			'referent_id' => array(
				'dependentForeignKeys' => array(
					'rule' => array( 'dependentForeignKeys', 'Referent', 'Structurereferente' ),
					'message' => 'Le référent n\'appartient pas à la structure référente',
				),
			),
		);

		/**
		*
		*/
		public function containQueryData() {
			return array(
				'Saisinebilanparcoursep66' => array(
					'Decisionsaisinebilanparcoursep66'=>array(
						'Typeorient',
						'Structurereferente'
					),
				)
			);
		}


		/**
		* TODO: comment finaliser l'orientation précédente ?
		* FIXME: à ne faire que quand le cg valide sa décision
		*/

		public function finaliser( $commissionep_id, $etape, $user_id ) {
			$dossierseps = $this->Dossierep->find(
				'all',
				array(
					'conditions' => array(
						'Dossierep.themeep' => Inflector::tableize( $this->alias ),
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
						.' )'
					),
					'contain' => array(
						$this->alias => array(
							'Bilanparcours66' => array(
								'Orientstruct'
							)
						),
						'Passagecommissionep' => array(
							'conditions' => array(
								'Passagecommissionep.commissionep_id' => $commissionep_id
							),
							'Decisionsaisinebilanparcoursep66' => array(
								'conditions' => array(
									'Decisionsaisinebilanparcoursep66.etape' => $etape
								)
							)
						)
					)
				)
			);
			$typeOrientPrincipaleEmploiId = Configure::read( 'Orientstruct.typeorientprincipale.Emploi' );
			if( is_array( $typeOrientPrincipaleEmploiId ) && isset( $typeOrientPrincipaleEmploiId[0] ) ){
				$typeOrientPrincipaleEmploiId = $typeOrientPrincipaleEmploiId[0];
			}
			else{
				trigger_error( __( 'Le type orientation principale Emploi n\'est pas bien défini.' ), E_USER_WARNING );
			}

			$success = true;
			$themeData = array();

			foreach( $dossierseps as $dossierep ) {
				$personne_id = Hash::get( $dossierep, "{$this->alias}.Bilanparcours66.Orientstruct.personne_id" );
				$decision = Hash::get( $dossierep, 'Passagecommissionep.0.Decisionsaisinebilanparcoursep66.0.decision' );

				// Si la décision n'est pas un report
				if( in_array( $decision, array( 'maintien', 'reorientation', 'annule' ) ) ) {
					// 1. Si la décision n'est pas une annulation
					if( in_array( $decision, array( 'maintien', 'reorientation' ) ) ) {
						// Création de la nouvelle orientation
						$rgorient = $this->Bilanparcours66->Orientstruct->WebrsaOrientstruct->rgorientMax( $personne_id ) + 1;
						$origine = ( $rgorient > 1 ? 'reorientation' : 'manuelle' );

						$orientstruct = array(
							'Orientstruct' => array(
								'personne_id' => $personne_id,
								'typeorient_id' => $dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['typeorient_id'],
								'structurereferente_id' => $dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['structurereferente_id'],
								'date_propo' => date( 'Y-m-d' ),
								'date_valid' => date( 'Y-m-d' ),
								'statut_orient' => 'Orienté',
								'user_id' => $user_id,
								'rgorient' => $rgorient,
								'origine' => $origine,
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
								'"PersonneReferent"."personne_id"' => $personne_id,
								'"PersonneReferent"."dfdesignation" IS NULL'
							)
						);

						// Création du nouveau référent du parcours s'il a été désigné
						if( !empty( $dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['referent_id'] ) ) {
							$referent = array(
								'PersonneReferent' => array(
									'personne_id' => $personne_id,
									'referent_id' => $dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['referent_id'],
									'dddesignation' => date( 'Y-m-d' ),
									'structurereferente_id' => $dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['structurereferente_id'],
									'user_id' => $user_id
								)
							);
							$this->Bilanparcours66->Orientstruct->Personne->PersonneReferent->create( $referent );
							$success = $this->Bilanparcours66->Orientstruct->Personne->PersonneReferent->save( null, array( 'atomic' => false ) ) && $success;
						}
					}

					// 2. Passage de la position du CER "Bilan réalisé – En attente de décision de l'EPL Parcours" à "En attente de renouvellement"
					$query = array(
						'fields' => array( 'Contratinsertion.id' ),
						'conditions' => array(
							'Contratinsertion.id' => Hash::get( $dossierep, "{$this->alias}.Bilanparcours66.contratinsertion_id" ),
							'Contratinsertion.positioncer' => 'bilanrealiseattenteeplparcours',
						),
						'contain' => false
					);
					$contratinsertion = $this->Bilanparcours66->Contratinsertion->find( 'first', $query );

					$contratinsertion_id = Hash::get( $contratinsertion, 'Contratinsertion.id' );
					if( !empty( $contratinsertion_id ) ) {
						$success = $success && $this->Bilanparcours66->Contratinsertion->WebrsaContratinsertion->updatePositionsCersById( $contratinsertion_id );
					}
				}

				$themeData[] = array( 'Decisionsaisinebilanparcoursep66' => $dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0] );
			}

			// Mise à jour de la position du bilan de parcours
			if( !empty( $themeData ) ) {
				$passagescommissionseps_ids = Hash::extract( $themeData, '{n}.Decisionsaisinebilanparcoursep66.passagecommissionep_id' );
				$success = $this->Bilanparcours66->WebrsaBilanparcours66->updatePositionBilanDecisionsEp( $this->name, $themeData, $etape, $passagescommissionseps_ids ) && $success;
			}

			return $success;
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

			return array(
				'conditions' => array(
					'Dossierep.themeep' => Inflector::tableize( $this->alias ),
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
					.' )'
				),
				'contain' => array(
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
					$this->alias => array(
						'Typeorient',
						'Structurereferente',
						'Bilanparcours66' => array(
							'Orientstruct' => array(
								'Typeorient',
								'Structurereferente',
							),
						)
					),
					'Passagecommissionep' => array(
						'conditions' => array(
							'Passagecommissionep.commissionep_id' => $commissionep_id
						),
						'Decisionsaisinebilanparcoursep66' => array(
							'order' => array(
								'Decisionsaisinebilanparcoursep66.etape DESC'
							),
							'Typeorient',
							'Structurereferente'
						),
					)
				)
			);
		}

		/**
		*
		*/

		public function saveDecisions( $data, $niveauDecision ) {
			// Calcul du changement de référent
			if( isset( $data['Bilanparcours66'] ) && !empty( $data['Bilanparcours66'] ) ) {
				foreach( $data['Bilanparcours66'] as $i => $values ) {
					if ( isset( $data['Decisionsaisinebilanparcoursep66'][$i]['structurereferente_id'] ) && !empty( $data['Decisionsaisinebilanparcoursep66'][$i]['structurereferente_id'] ) ) {
						list( $typeorient_id, $structurereferente_id ) = explode( '_', $data['Decisionsaisinebilanparcoursep66'][$i]['structurereferente_id'] );
						if ( $values['oldstructurereferente_id'] == $structurereferente_id ) {
							$data['Decisionsaisinebilanparcoursep66'][$i]['changementrefparcours'] = 'N';
						}
						else {
							$data['Decisionsaisinebilanparcoursep66'][$i]['changementrefparcours'] = 'O';
						}
					}
				}
			}

			// Filtrage des données
			$themeData = Set::extract( $data, '/Decisionsaisinebilanparcoursep66' );
			if( empty( $themeData ) ) {
				return true;
			}
			else {
				$success = $this->Dossierep->Passagecommissionep->Decisionsaisinebilanparcoursep66->saveAll( $themeData, array( 'atomic' => false ) );
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
				$formData['Decisionsaisinebilanparcoursep66'][$key]['passagecommissionep_id'] = @$datas[$key]['Passagecommissionep'][0]['id'];

				// On modifie les enregistrements de cette étape
				if( @$dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['etape'] == $niveauDecision  ) {
					$formData['Decisionsaisinebilanparcoursep66'][$key] = @$dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0];
					$formData['Decisionsaisinebilanparcoursep66'][$key]['checkcomm'] = !empty( $dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['commentaire'] );
					$formData['Decisionsaisinebilanparcoursep66'][$key]['referent_id'] = implode(
						'_',
						array(
							$formData['Decisionsaisinebilanparcoursep66'][$key]['structurereferente_id'],
							$formData['Decisionsaisinebilanparcoursep66'][$key]['referent_id']
						)
					);
					$formData['Decisionsaisinebilanparcoursep66'][$key]['structurereferente_id'] = implode(
						'_',
						array(
							$formData['Decisionsaisinebilanparcoursep66'][$key]['typeorient_id'],
							$formData['Decisionsaisinebilanparcoursep66'][$key]['structurereferente_id']
						)
					);
					$formData['Decisionsaisinebilanparcoursep66'][$key]['typeorient_id'] = implode(
						'_',
						array(
							$formData['Decisionsaisinebilanparcoursep66'][$key]['typeorientprincipale_id'],
							$formData['Decisionsaisinebilanparcoursep66'][$key]['typeorient_id']
						)
					);
				}
				// On ajoute les enregistrements de cette étape
				else {
					if( $niveauDecision == 'ep' ) {
						$formData['Decisionsaisinebilanparcoursep66'][$key]['decision'] = $dossierep[$this->alias]['choixparcours'];
						$formData['Decisionsaisinebilanparcoursep66'][$key]['checkcomm'] = 0;
						$formData['Decisionsaisinebilanparcoursep66'][$key]['typeorientprincipale_id'] = $dossierep[$this->alias]['typeorientprincipale_id'];

						$formData['Decisionsaisinebilanparcoursep66'][$key]['structurereferente_id'] = implode(
							'_',
							array(
								$dossierep[$this->alias]['typeorient_id'],
								$dossierep[$this->alias]['structurereferente_id']
							)
						);
						$formData['Decisionsaisinebilanparcoursep66'][$key]['typeorient_id'] = implode(
							'_',
							array(
								$dossierep[$this->alias]['typeorientprincipale_id'],
								$dossierep[$this->alias]['typeorient_id']
							)
						);
						if ( $dossierep[$this->alias]['Bilanparcours66']['changereferent'] == 'O' ) {
							$formData['Decisionsaisinebilanparcoursep66'][$key]['referent_id'] = implode(
								'_',
								array(
									$dossierep[$this->alias]['Bilanparcours66']['Orientstruct']['structurereferente_id'],
									$dossierep[$this->alias]['Bilanparcours66']['Orientstruct']['referent_id']
								)
							);
						}
					}
					elseif( $niveauDecision == 'cg' ) {
						$formData['Decisionsaisinebilanparcoursep66'][$key]['decision'] = $dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['decision'];
						$formData['Decisionsaisinebilanparcoursep66'][$key]['commentaire'] = $dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['commentaire'];
						$formData['Decisionsaisinebilanparcoursep66'][$key]['checkcomm'] = !empty( $dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['commentaire'] );
						$formData['Decisionsaisinebilanparcoursep66'][$key]['typeorientprincipale_id'] = $dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['typeorientprincipale_id'];
						$formData['Decisionsaisinebilanparcoursep66'][$key]['referent_id'] = implode(
							'_',
							array(
								$dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['structurereferente_id'],
								$dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['referent_id']
							)
						);
						$formData['Decisionsaisinebilanparcoursep66'][$key]['structurereferente_id'] = implode(
							'_',
							array(
								$dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['typeorient_id'],
								$dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['structurereferente_id']
							)
						);
						$formData['Decisionsaisinebilanparcoursep66'][$key]['typeorient_id'] = implode(
							'_',
							array(
								$dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['typeorientprincipale_id'],
								$dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0]['typeorient_id']
							)
						);
					}
				}
			}

			return $formData;
		}

		/**
		*
		*/

		public function prepareFormDataUnique( $dossierep_id, $dossierep, $niveauDecision ) {
			$formData = array();
			return $formData;
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
					$this->Dossierep->Passagecommissionep->Decisionsaisinebilanparcoursep66->fields(),
					$this->Dossierep->Passagecommissionep->Decisionsaisinebilanparcoursep66->Typeorient->fields(),
					$this->Dossierep->Passagecommissionep->Decisionsaisinebilanparcoursep66->Structurereferente->fields(),
					$this->Dossierep->Passagecommissionep->Decisionsaisinebilanparcoursep66->Referent->fields(),
					$this->Dossierep->Saisinebilanparcoursep66->Bilanparcours66->fields(),
					array_words_replace(
						$this->Dossierep->Saisinebilanparcoursep66->Bilanparcours66->Structurereferente->fields(),
						array( 'Structurereferente' => 'Structurereferentebilan' )
					),
					array_words_replace(
						$this->Dossierep->Saisinebilanparcoursep66->Bilanparcours66->Referent->fields(),
						array( 'Referent' => 'Referentbilan' )
					)
				),
				'joins' => array(
					array(
						'table'      => 'saisinesbilansparcourseps66',
						'alias'      => 'Saisinebilanparcoursep66',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Saisinebilanparcoursep66.dossierep_id = Dossierep.id' ),
					),
					array(
						'table'      => 'decisionssaisinesbilansparcourseps66',
						'alias'      => 'Decisionsaisinebilanparcoursep66',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'Decisionsaisinebilanparcoursep66.passagecommissionep_id = Passagecommissionep.id',
							'Decisionsaisinebilanparcoursep66.etape' => 'ep'
						),
					),
					$this->Dossierep->Passagecommissionep->Decisionsaisinebilanparcoursep66->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossierep->Passagecommissionep->Decisionsaisinebilanparcoursep66->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossierep->Passagecommissionep->Decisionsaisinebilanparcoursep66->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossierep->Saisinebilanparcoursep66->join( 'Bilanparcours66', array( 'type' => 'INNER' ) ),
					array_words_replace(
						$this->Dossierep->Saisinebilanparcoursep66->Bilanparcours66->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						array( 'Structurereferente' => 'Structurereferentebilan' )
					),
					array_words_replace(
						$this->Dossierep->Saisinebilanparcoursep66->Bilanparcours66->join( 'Referent', array( 'type' => 'INNER' ) ),
						array( 'Referent' => 'Referentbilan' )
					)
				)
			);


			$modeleDecisionPart = 'decbilan'.Configure::read( 'Cg.departement' );
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
			$gedooo_data = $this->Dossierep->Passagecommissionep->find( 'first', $datas['querydata'] );
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
		* d'EP pour un certain niveau de décision.
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

            if( Configure::read( 'Cg.departement' ) == 66 ) {
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
            }

			// Choix du modèle de document
			$decision = $gedooo_data[$modeleDecisions]['decision'];
			$proposition = $gedooo_data['Bilanparcours66']['proposition'];
			if( $decision == 'maintien' ) {
				if( $proposition != 'parcourspe' ) {
					if( $gedooo_data[$modeleDecisions]['changementrefparcours'] == 'O' ) {
						$modeleOdt  = "{$this->alias}/decision_maintien_avec_changement.odt";
					}
					else if( $gedooo_data[$modeleDecisions]['changementrefparcours'] == 'N' ) {
						$modeleOdt  = "{$this->alias}/decision_maintien_sans_changement.odt";
					}
				}
				else {
					$modeleOdt  = "{$this->alias}/decision_maintien.odt";
				}
			}
			else { // reorientation, reporte, annule
				$modeleOdt = "{$this->alias}/decision_{$decision}.odt";
			}

			return $this->_getOrCreateDecisionPdf( $passagecommissionep_id, $gedooo_data, $modeleOdt, $datas['options'] );
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
