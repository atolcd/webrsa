<?php
	/**
	 * Code source de la classe Defautinsertionep66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( ABSTRACTMODELS.'Thematiqueep.php' );

	/**
	 * Saisines d'EP pour les réorientations proposées par les structures
	 * référentes pour le conseil général du département 93.
	 *
	 * Il s'agit de l'un des thèmes des EPs pour le CG 93.
	 *
	 * @package app.Model
	 */
	class Reorientationep93 extends Thematiqueep
	{
		public $name = 'Reorientationep93';

		public $actsAs = array(
			'Autovalidate2',
			'Dependencies',
			'Enumerable' => array(
				'fields' => array(
					'accordaccueil',
					'accordallocataire',
					'urgent',
				)
			),
			'Formattable' => array(
				'suffix' => array(
					'structurereferente_id',
					'referent_id',
				)
			),
			'Gedooo.Gedooo',
			'ValidateTranslate',
		);

		/**
		 * Classes utilisées par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'WebrsaOrientstruct' );

		public $belongsTo = array(
			'Dossierep' => array(
				'className' => 'Dossierep',
				'foreignKey' => 'dossierep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Motifreorientep93' => array(
				'className' => 'Motifreorientep93',
				'foreignKey' => 'motifreorientep93_id',
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
			'Referent' => array(
				'className' => 'Referent',
				'foreignKey' => 'referent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
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
		);

		/**
		* Chemin relatif pour les modèles de documents .odt utilisés lors des
		* impressions. Utiliser %s pour remplacer par l'alias.
		*/
		public $modelesOdt = array(
			// Convocation EP
			'%s/convocationep_beneficiaire.odt',
			// Décision EP (décision CG)
			'%s/decision_accepte_poleemploi.odt',
			'%s/decision_accepte.odt',
			'%s/decision_refuse.odt',
			'%s/decision_annule.odt',
			'%s/decision_reporte.odt',
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
		* Retourne pour un personne_id donnée les queryDatas permettant de retrouver
		* ses réorientationseps93 si elle en a en cours
		*/

		public function qdReorientationEnCours( $personne_id ) {
			return array(
				'fields' => array(
					'Personne.nom',
					'Personne.prenom',
					'Reorientationep93.id',
					'Reorientationep93.datedemande',
					'Orientstruct.rgorient',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Passagecommissionep.etatdossierep'
				),
				'conditions' => array(
					'Dossierep.actif' => '1',
					'Dossierep.personne_id' => $personne_id,
					'Dossierep.themeep' => 'reorientationseps93',
					'Dossierep.id NOT IN ( '.$this->Orientstruct->Personne->Dossierep->Passagecommissionep->sq(
						array(
							'alias' => 'passagescommissionseps',
							'fields' => array(
								'passagescommissionseps.dossierep_id'
							),
							'conditions' => array(
								'passagescommissionseps.etatdossierep' => array( 'traite', 'annule' )
							)
						)
					).' )'
				),
				'joins' => array(
					array(
						'table' => 'dossierseps',
						'alias' => 'Dossierep',
						'type' => 'INNER',
						'conditions' => array(
							'Reorientationep93.dossierep_id = Dossierep.id'
						)
					),
					array(
						'table' => 'orientsstructs',
						'alias' => 'Orientstruct',
						'type' => 'INNER',
						'conditions' => array(
							'Reorientationep93.orientstruct_id = Orientstruct.id'
						)
					),
					array(
						'table' => 'typesorients',
						'alias' => 'Typeorient',
						'type' => 'INNER',
						'conditions' => array(
							'Reorientationep93.typeorient_id = Typeorient.id'
						)
					),
					array(
						'table' => 'structuresreferentes',
						'alias' => 'Structurereferente',
						'type' => 'INNER',
						'conditions' => array(
							'Reorientationep93.structurereferente_id = Structurereferente.id'
						)
					),
					array(
						'table' => 'passagescommissionseps',
						'alias' => 'Passagecommissionep',
						'type' => 'LEFT OUTER',
						'conditions' => array(
							'Passagecommissionep.dossierep_id = Dossierep.id'
						)
					),
					array(
						'table' => 'commissionseps',
						'alias' => 'Commissionep',
						'type' => 'LEFT OUTER',
						'conditions' => array(
							'Passagecommissionep.commissionep_id = Commissionep.id'
						)
					),
					array(
						'table' => 'personnes',
						'alias' => 'Personne',
						'type' => 'INNER',
						'conditions' => array(
							'Dossierep.personne_id = Personne.id'
						)
					)
				),
				'contain' => false,
				'order' => array( 'Commissionep.dateseance DESC', 'Commissionep.id DESC' )
			);
		}

		/**
		 *
		 */
		public function ajoutPossible( $personne_id ) {
			return $this->WebrsaOrientstruct->ajoutPossible( $personne_id );
		}

		/**
		* TODO: comment finaliser l'orientation précédente ?
		*/

		public function finaliser( $commissionep_id, $etape, $user_id ) {
			$dossierseps = $this->Dossierep->Passagecommissionep->find(
				'all',
				array(
					'fields' => array(
						'Passagecommissionep.id',
						'Passagecommissionep.commissionep_id',
						'Passagecommissionep.dossierep_id',
						'Passagecommissionep.etatdossierep',
						'Dossierep.personne_id',
						'Decisionreorientationep93.id',
						'Decisionreorientationep93.decision',
						'Decisionreorientationep93.typeorient_id',
						'Decisionreorientationep93.structurereferente_id',
						'Reorientationep93.id',
						'Reorientationep93.structurereferente_id',
						'Reorientationep93.referent_id',
						'Reorientationep93.user_id',
						'Reorientationep93.datedemande'
					),
					'conditions' => array(
						'Passagecommissionep.commissionep_id' => $commissionep_id
					),
					'joins' => array(
						array(
							'table' => 'dossierseps',
							'alias' => 'Dossierep',
							'type' => 'INNER',
							'conditions' => array(
								'Passagecommissionep.dossierep_id = Dossierep.id'
							)
						),
						array(
							'table' => 'reorientationseps93',
							'alias' => 'Reorientationep93',
							'type' => 'INNER',
							'conditions' => array(
								'Reorientationep93.dossierep_id = Dossierep.id'
							)
						),
						array(
							'table' => 'decisionsreorientationseps93',
							'alias' => 'Decisionreorientationep93',
							'type' => 'INNER',
							'conditions' => array(
								'Decisionreorientationep93.passagecommissionep_id = Passagecommissionep.id',
								'Decisionreorientationep93.etape' => $etape
							)
						)
					),
					'contain' => false
				)
			);

			$success = true;
			foreach( $dossierseps as $dossierep ) {
				if( $dossierep['Decisionreorientationep93']['decision'] == 'accepte' ) {
					$rgorient = $this->Orientstruct->WebrsaOrientstruct->rgorientMax( $dossierep['Dossierep']['personne_id'] ) + 1;
					$origine = ( $rgorient > 1 ? 'reorientation' : 'cohorte' );

					// Nouvelle orientation
					$orientstruct = array(
						'Orientstruct' => array(
							'personne_id' => $dossierep['Dossierep']['personne_id'],
							'typeorient_id' => $dossierep['Decisionreorientationep93']['typeorient_id'],
							'structurereferente_id' => $dossierep['Decisionreorientationep93']['structurereferente_id'],
							'date_propo' => date( 'Y-m-d' ),
							'date_valid' => date( 'Y-m-d' ),
							'statut_orient' => 'Orienté',
							'user_id' => $dossierep['Reorientationep93']['user_id'], // L'utilisateur à l'origine de la demande de réorientation devient l'utilisateur de la nouvelle orientsstruct
							'rgorient' => $rgorient,
							'origine' => $origine,
						)
					);

					// Si on avait choisi une personne référente et que le passage en EP
					// valide la structure à laquelle cette personne est attachée, alors,
					// on recopie cette personne -> FIXME: dans orientsstructs ou dans personnes_referents
					if( !empty( $dossierep['Reorientationep93']['referent_id'] ) && $dossierep['Reorientationep93']['structurereferente_id'] == $dossierep['Decisionreorientationep93']['structurereferente_id'] ) {
						$orientstruct['Orientstruct']['referent_id'] = $dossierep['Reorientationep93']['referent_id'];
					}

					// La date de proposition de l'orientation devient la date de demande de la réorientation.
					if( !empty( $dossierep['Reorientationep93']['datedemande'] ) ) {
						$orientstruct['Orientstruct']['date_propo'] = $dossierep['Reorientationep93']['datedemande'];
					}

					$this->Orientstruct->create( $orientstruct );
					$success = $this->Orientstruct->save() && $success;

					// Mise à jour de l'enregistrement de la thématique avec l'id de la nouvelle orientation
					$success = $success && $this->updateAllUnBound(
						array( "\"{$this->alias}\".\"nvorientstruct_id\"" => $this->Orientstruct->id ),
						array( "\"{$this->alias}\".\"id\"" => $dossierep[$this->alias]['id'] )
					);

					// Recherche dernier CER
					$dernierCerId = $this->Orientstruct->Personne->Contratinsertion->find(
						'first',
						array(
							'fields' => array( 'Contratinsertion.id' ),
							'conditions' => array(
								'Contratinsertion.personne_id' => $dossierep['Dossierep']['personne_id']
							),
							array( 'Contratinsertion.df_ci DESC' )
						)
					);

					if( !empty( $dernierCerId ) ) {
						// Clôture anticipée du dernier CER
						$success = $success && $this->Orientstruct->Personne->Contratinsertion->updateAllUnBound(
							array( 'Contratinsertion.df_ci' => "'".date( 'Y-m-d' )."'" ),
							array( 'Contratinsertion.id' => $dernierCerId['Contratinsertion']['id'] )
						);
					}

					// Fin de désignation du référent de la personne
					$this->Orientstruct->Personne->PersonneReferent->updateAllUnBound(
						array( 'PersonneReferent.dfdesignation' => "'".date( 'Y-m-d' )."'" ),
						array(
							'"PersonneReferent"."personne_id"' => $dossierep['Dossierep']['personne_id'],
							'"PersonneReferent"."dfdesignation" IS NULL'
						)
					);

					// Si l'allocataire est réorienté et qu'il avait un D1, il sort de l'accompagnement
					$success = $this->WebrsaOrientstruct->reorientationEpQuestionnaired2pdv93Auto( $dossierep, 'Decisionreorientationep93', $this->Orientstruct->id ) && $success;
				}
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
						'Motifreorientep93',
						'Typeorient',
						'Structurereferente',
						'Orientstruct' => array(
							'conditions' => array(
								'Orientstruct.rgorient IS NOT NULL'
							),
							'Typeorient',
							'Structurereferente',
						)
					),
					'Passagecommissionep' => array(
						'conditions' => array(
							'Passagecommissionep.commissionep_id' => $commissionep_id
						),
						'Decisionreorientationep93' => array(
							'order' => array( 'Decisionreorientationep93.etape DESC' ),
							'Typeorient',
							'Structurereferente',
						)
					)
				)
			);
		}

		/**
		*
		*/

		public function saveDecisions( $data, $niveauDecision ) {
			// FIXME: filtrer les données
			$themeData = Set::extract( $data, '/Decisionreorientationep93' );
			if( empty( $themeData ) ) {
				return true;
			}
			else {
				$success = $this->Dossierep->Passagecommissionep->Decisionreorientationep93->saveAll( $themeData, array( 'atomic' => false ) );
				$this->Dossierep->Passagecommissionep->updateAllUnBound(
					array( 'Passagecommissionep.etatdossierep' => '\'decision'.$niveauDecision.'\'' ),
					array( '"Passagecommissionep"."id"' => Set::extract( $data, '/Decisionreorientationep93/passagecommissionep_id' ) )
				);

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
		* @param string $niveauDecision Le niveau de décision ('ep' ou 'cg') pour
		*	lequel il faut préparer les données du formulaire
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
				$formData['Decisionreorientationep93'][$key]['passagecommissionep_id'] = @$datas[$key]['Passagecommissionep'][0]['id'];

				// On modifie les enregistrements de cette étape
				if( @$dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0]['etape'] == $niveauDecision ) {
					$formData['Decisionreorientationep93'][$key] = @$dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0];
					$formData['Decisionreorientationep93'][$key]['structurereferente_id'] = implode(
						'_',
						array(
							$formData['Decisionreorientationep93'][$key]['typeorient_id'],
							$formData['Decisionreorientationep93'][$key]['structurereferente_id']
						)
					);
				}
				// On ajoute les enregistrements de cette étape
				else if( $niveauDecision == 'cg' ) {
					$formData['Decisionreorientationep93'][$key]['decision'] = $dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0]['decision'];
					$formData['Decisionreorientationep93'][$key]['decisionpcg'] = 'valide';
					if ( $formData['Decisionreorientationep93'][$key]['decision'] == 'accepte' ) {
						$formData['Decisionreorientationep93'][$key]['typeorient_id'] = $dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0]['typeorient_id'];
						$formData['Decisionreorientationep93'][$key]['structurereferente_id'] = implode(
							'_',
							array(
								$dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0]['typeorient_id'],
								$dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0]['structurereferente_id']
							)
						);
					}
					else {
						$formData['Decisionreorientationep93'][$key]['typeorient_id'] = $dossierep[$this->alias]['typeorient_id'];
						$formData['Decisionreorientationep93'][$key]['structurereferente_id'] = implode(
							'_',
							array(
								$dossierep[$this->alias]['typeorient_id'],
								$dossierep[$this->alias]['structurereferente_id']
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

		public function containPourPv() {
			return array(
				'Reorientationep93' => array(
					'Decisionreorientationep93' => array(
						'conditions' => array(
							'etape' => 'ep'
						),
						'Typeorient',
						'Structurereferente'
					)
				)
			);
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
					$this->Dossierep->Passagecommissionep->Decisionreorientationep93->fields(),
					$this->Dossierep->Passagecommissionep->Decisionreorientationep93->Typeorient->fields(),
					$this->Dossierep->Passagecommissionep->Decisionreorientationep93->Structurereferente->fields()				),
				'joins' => array(
					array(
						'table'      => 'reorientationseps93',
						'alias'      => 'Reorientationep93',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Reorientationep93.dossierep_id = Dossierep.id' ),
					),
					array(
						'table'      => 'decisionsreorientationseps93',
						'alias'      => 'Decisionreorientationep93',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'Decisionreorientationep93.passagecommissionep_id = Passagecommissionep.id',
							'Decisionreorientationep93.etape' => 'ep'
						),
					),
					$this->Dossierep->Passagecommissionep->Decisionreorientationep93->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossierep->Passagecommissionep->Decisionreorientationep93->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) )
				)
			);

			$modeleDecisionPart = 'decreori'.Configure::read( 'Cg.departement' );
			return array_words_replace(
				$querydata,
				array(
					'Typeorient' => "Typeorient{$modeleDecisionPart}",
					'Structurereferente' => "Structurereferente{$modeleDecisionPart}"
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

				$datas['querydata']['fields'] = array_merge(
					$datas['querydata']['fields'],
					$this->Typeorient->fields(),
					$this->Structurereferente->fields()
				);

				$datas['querydata']['joins'][] = $this->join( 'Typeorient' );
				$datas['querydata']['joins'][] = $this->join( 'Structurereferente' );

				Cache::write( $cacheKey, $datas );
			}

			$datas['querydata']['conditions']['Passagecommissionep.id'] = $passagecommissionep_id;
			$gedooo_data = $this->Dossierep->Passagecommissionep->find( 'first', $datas['querydata'] );

			if( empty( $gedooo_data ) ) {
				return false;
			}

			$modeleOdt = "{$this->alias}/convocationep_beneficiaire.odt";

			return $this->ged(
				$gedooo_data,
				$modeleOdt,
				false,
				$datas['options']
			);
		}

		/**
		* Récupération de la décision suite au passage en commission d'un dossier
		* d'EP pour un certain niveau de décision.
		* TODO: à faire précharger
		*/
		public function getDecisionPdf( $passagecommissionep_id, $user_id = null  ) {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$datas = Cache::read( $cacheKey );

			if( $datas === false ) {
				$datas['querydata'] = $this->_qdDecisionPdf();

				// Jointures et champs propositions
				$modelesProposes = array(
					'Typeorient' => 'Typeorientpropose',
					'Structurereferente' => 'Structurereferentepropose',
					'Referent' => 'Referentpropose'
				);

				foreach( $modelesProposes as $modelePropose => $modeleProposeAliase ) {
					$replacement = array( $modelePropose => $modeleProposeAliase );

					$datas['querydata']['joins'][] = array_words_replace( $this->join( $modelePropose ), $replacement );
					$datas['querydata']['fields'] = array_merge( $datas['querydata']['fields'], array_words_replace( $this->{$modelePropose}->fields(), $replacement ) );
				}

				// Jointures et champs décisions
				$modelesProposes = array(
					'Typeorient' => 'Decisionreorientationep93typeorient',
					'Structurereferente' => 'Decisionreorientationep93structurereferente',
				);

				foreach( $modelesProposes as $modelePropose => $modeleProposeAliase ) {
					$replacement = array( $modelePropose => $modeleProposeAliase );

					$datas['querydata']['joins'][] = array_words_replace( $this->Dossierep->Passagecommissionep->Decisionreorientationep93->join( $modelePropose ), $replacement );
					$datas['querydata']['fields'] = array_merge( $datas['querydata']['fields'], array_words_replace( $this->Dossierep->Passagecommissionep->Decisionreorientationep93->{$modelePropose}->fields(), $replacement ) );
				}

				// Traductions
				$datas['options'] = $this->Dossierep->Passagecommissionep->Decisionreorientationep93->enums();
				$datas['options']['Personne']['qual'] = ClassRegistry::init( 'Option' )->qual();
				$datas['options']['type']['voie'] = ClassRegistry::init( 'Option' )->typevoie();

				Cache::write( $cacheKey, $datas );
			}

			$datas['querydata']['conditions']['Passagecommissionep.id'] = $passagecommissionep_id;

			// INFO: permet de ne pas avoir d'erreur avec les virtualFields aliasés
			$virtualFields = $this->Dossierep->Passagecommissionep->virtualFields;
			$this->Dossierep->Passagecommissionep->virtualFields = array();
			$gedooo_data = $this->Dossierep->Passagecommissionep->find( 'first', $datas['querydata'] );
			$this->Dossierep->Passagecommissionep->virtualFields = $virtualFields;

			if( empty( $gedooo_data ) || empty( $gedooo_data['Decisionreorientationep93']['id'] ) ) {
				return false;
			}

			// Choix du modèle de document
			$decision = $gedooo_data['Decisionreorientationep93']['decision'];

			if( $decision == 'accepte' ) {
				if( preg_match( '/emploi/i', $gedooo_data['Decisionreorientationep93typeorient']['lib_type_orient'] ) ) {
					$modeleOdt  = "{$this->alias}/decision_accepte_poleemploi.odt";
				}
				else {
					$modeleOdt  = "{$this->alias}/decision_accepte.odt";
				}
			}
			else { // annule (remobilisation), reporte, refuse
				$modeleOdt  = "{$this->alias}/decision_{$decision}.odt";
			}

			// La date d'impression sera enregistrée par Dossierep::getDecisionPdf()
			// après le retour de cette méthode-ci lorsqu'elle n'existe pas.
			if( empty( $gedooo_data['Passagecommissionep']['impressiondecision'] ) ) {
				$gedooo_data['Passagecommissionep']['impressiondecision'] = date( 'Y-m-d' );
			}

			return $this->_getOrCreateDecisionPdf( $passagecommissionep_id, $gedooo_data, $modeleOdt, $datas['options'] );
		}

		/**
		* Fonction retournant un querydata qui va permettre de retrouver des dossiers d'EP
		*/

		public function qdListeDossier( $commissionep_id = null ) {
			$return = array(
				'fields' => array(
					'Dossierep.id',
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Personne.id',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Structurereferente.lib_struc',
					'Motifreorientep93.name',
					'Reorientationep93.accordaccueil',
					'Reorientationep93.accordallocataire',
					'Reorientationep93.urgent',
					'Reorientationep93.datedemande',
					'Passagecommissionep.id',
					'Passagecommissionep.commissionep_id'
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
					'alias' => 'Structurereferente',
					'table' => 'structuresreferentes',
					'type' => 'INNER',
					'conditions' => array(
						'Structurereferente.id = '.$this->alias.'.structurereferente_id'
					)
				),
				array(
					'alias' => 'Motifreorientep93',
					'table' => 'motifsreorientseps93',
					'type' => 'INNER',
					'conditions' => array(
						'Motifreorientep93.id = '.$this->alias.'.motifreorientep93_id'
					)
				),
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
		* Modèles contenus pour l'historique des passages en EP
		*/

		public function containThematique() {
			return array(
				'Motifreorientep93'
			);
		}

		/**
		 * Retourne l'id de la personne à laquelle est (indirectement) lié un
		 * enregistrement.
		 *
		 * @param integer $id L'id de l'enregistrement
		 * @return integer
		 */
		public function personneId( $id ) {
			$querydata = array(
				'fields' => array( 'Orientstruct.personne_id' ),
				'joins' => array(
					$this->join( 'Orientstruct', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					'Reorientationep93.id' => $id
				),
				'recursive' => -1
			);

			$result = $this->find( 'first', $querydata );

			if( !empty( $result ) ) {
				return $result['Orientstruct']['personne_id'];
			}
			else {
				return null;
			}
		}
	}
?>