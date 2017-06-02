<?php
	/**
	 * Code source de la classe Contratcomplexeep93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( ABSTRACTMODELS.'Thematiqueep.php' );

	/**
	 * La classe Contratcomplexeep93 ...
	 *
	 * @package app.Model
	 */
	class Contratcomplexeep93 extends Thematiqueep
	{
		/**
		*
		*/

		public $actsAs = array(
			'Autovalidate2',
			'ValidateTranslate',
			'Formattable',
			'Gedooo.Gedooo',
			'ModelesodtConditionnables' => array(
				93 => array(
					// Convocation EP
					'%s/convocationep_beneficiaire.odt',
					// Décision EP (décision CG)
					'%s/decision_annule.odt',
					'%s/decision_reporte.odt',
					'%s/decision_valide.odt',
					'%s/decision_rejete.odt'
				)
			)
		);

		/**
		*
		*/

		public $belongsTo = array(
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
		);

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

			$modeleDecisions = Inflector::classify( 'decisions'.Inflector::tableize( $this->alias ) );

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
						'fields' => array(
							'id',
							'dossierep_id',
							'contratinsertion_id',
							'created',
							'modified'

						),
						'Contratinsertion' => array(
							'Structurereferente',
						)
					),
					'Passagecommissionep' => array(
						'conditions' => array(
							'Passagecommissionep.commissionep_id' => $commissionep_id
						),
						$modeleDecisions => array(
							'order' => array( $modeleDecisions.'.etape DESC' ),
						)
					)
				)
			);
		}

		/**
		* FIXME
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

			$modeleDecisions = Inflector::classify( 'decisions'.Inflector::tableize( $this->alias ) );

			$formData = array();
			foreach( $datas as $key => $dossierep ) {
				$formData[$modeleDecisions][$key]['passagecommissionep_id'] = @$datas[$key]['Passagecommissionep'][0]['id'];

				// On modifie les enregistrements de cette étape
				if( @$dossierep['Passagecommissionep'][0][$modeleDecisions][0]['etape'] == $niveauDecision ) {
					$formData[$modeleDecisions][$key] = @$dossierep['Passagecommissionep'][0][$modeleDecisions][0];
				}
				// On ajoute les enregistrements de cette étape
				else {
					if( $niveauDecision == 'cg' ) {
						$formData[$modeleDecisions][$key]['decision'] = $dossierep['Passagecommissionep'][0][$modeleDecisions][0]['decision'];
						$formData[$modeleDecisions][$key]['datevalidation_ci'] = $dossierep['Passagecommissionep'][0][$modeleDecisions][0]['datevalidation_ci'];
						$formData[$modeleDecisions][$key]['observ_ci'] = $dossierep['Passagecommissionep'][0][$modeleDecisions][0]['observ_ci'];
						$formData[$modeleDecisions][$key]['observationdecision'] = $dossierep['Passagecommissionep'][0][$modeleDecisions][0]['observationdecision'];
						$formData[$modeleDecisions][$key]['raisonnonpassage'] = $dossierep['Passagecommissionep'][0][$modeleDecisions][0]['raisonnonpassage'];
						if ( Configure::read( 'Cg.departement' ) != 93 ) {
							$formData[$modeleDecisions][$key]['commentaire'] = $dossierep['Passagecommissionep'][0][$modeleDecisions][0]['commentaire'];
						}
						$formData[$modeleDecisions][$key]['decisionpcg'] = 'valide';
					}
				}
			}

			return $formData;
		}

		/**
		 * Retourne une partie de querydata concernant la thématique pour le PV d'EP.
		 *
		 * @return array
		 */
		public function qdProcesVerbal() {
			$modeleDecisions = Inflector::classify( 'decisions'.Inflector::tableize( $this->alias ) );

			return array(
				'fields' => array_merge(
					$this->fields(),
					$this->Dossierep->Passagecommissionep->{$modeleDecisions}->fields()
				),
				'joins' => array(
					array(
						'table'      => Inflector::tableize( $this->alias ),
						'alias'      => $this->alias,
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( "{$this->alias}.dossierep_id = Dossierep.id" ),
					),
					array(
						'table'      => 'decisions'.Inflector::tableize( $this->alias ),
						'alias'      => $modeleDecisions,
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							"{$modeleDecisions}.passagecommissionep_id = Passagecommissionep.id",
							"{$modeleDecisions}.etape" => 'ep'
						),
					),
				)
			);
		}

		/**
		* TODO: docs
		*/

		public function saveDecisions( $data, $niveauDecision ) {
			// FIXME: filtrer les données
			$themeData = Set::extract( $data, '/Decisioncontratcomplexeep93' );
			if( empty( $themeData ) ) {
				return true;
			}
			else {
				foreach( array_keys( $themeData ) as $key ) {
					// On complètre /on nettoie si ce n'est pas envoyé par le formulaire
					if( $themeData[$key]['Decisioncontratcomplexeep93']['decision'] == 'valide' ) {
						$themeData[$key]['Decisioncontratcomplexeep93']['raisonnonpassage'] = null;
					}
					else if( $themeData[$key]['Decisioncontratcomplexeep93']['decision'] == 'refuse' ) {
						$themeData[$key]['Decisioncontratcomplexeep93']['datevalidation_ci'] = null;
					}
					else if( in_array( $themeData[$key]['Decisioncontratcomplexeep93']['decision'], array( 'annule', 'reporte' ) ) ) {
						$themeData[$key]['Decisioncontratcomplexeep93']['datevalidation_ci'] = null;
						$themeData[$key]['Decisioncontratcomplexeep93']['observ_ci'] = null;
						$themeData[$key]['Decisioncontratcomplexeep93']['observationdecision'] = null;
					}
					// FIXME: la même chose pour l'étape 2
				}

				$success = $this->Dossierep->Passagecommissionep->Decisioncontratcomplexeep93->saveAll( $themeData, array( 'atomic' => false ) );
				$this->Dossierep->Passagecommissionep->updateAllUnBound(
					array( 'Passagecommissionep.etatdossierep' => '\'decision'.$niveauDecision.'\'' ),
					array( '"Passagecommissionep"."id"' => Set::extract( $data, '/Decisioncontratcomplexeep93/passagecommissionep_id' ) )
				);

				return $success;
			}
		}

		/**
		 *
		 * @param integer $commissionep_id
		 * @param string $etape
		 * @param integer $user_id
		 * @return boolean
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

			$dossierseps = $this->Dossierep->Passagecommissionep->find(
				'all',
				array(
					'fields' => array(
						'Passagecommissionep.id',
						'Passagecommissionep.commissionep_id',
						'Passagecommissionep.dossierep_id',
						'Passagecommissionep.etatdossierep',
						'Dossierep.personne_id',
						'Decisioncontratcomplexeep93.decision',
						'Decisioncontratcomplexeep93.observ_ci',
						'Decisioncontratcomplexeep93.observationdecision',
						'Decisioncontratcomplexeep93.datevalidation_ci',
						'Contratcomplexeep93.contratinsertion_id'
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
							'table' => 'contratscomplexeseps93',
							'alias' => 'Contratcomplexeep93',
							'type' => 'INNER',
							'conditions' => array(
								'Contratcomplexeep93.dossierep_id = Dossierep.id'
							)
						),
						array(
							'table' => 'decisionscontratscomplexeseps93',
							'alias' => 'Decisioncontratcomplexeep93',
							'type' => 'INNER',
							'conditions' => array(
								'Decisioncontratcomplexeep93.passagecommissionep_id = Passagecommissionep.id',
								'Decisioncontratcomplexeep93.etape' => $etape
							)
						)
					),
				)
			);

			$enum = array(
				'valide' => 'V',
				'rejete' => 'R',
				'annule' => 'R',
				'reporte' => 'E'
			);

			$enumCer93 = array(
				'valide' => '99valide',
				'rejete' => '99rejete',
				'annule' => '99rejete',
			);

			$success = true;
			$validate = $this->Contratinsertion->validate;
			foreach( $dossierseps as $dossierep ) {
				if( $niveauDecisionFinale == "decision{$etape}" ) {
					$this->Contratinsertion->validate = array();
					$contratinsertion = $this->Contratinsertion->find(
						'first',
						array(
							'conditions' => array(
								'Contratinsertion.id' => $dossierep['Contratcomplexeep93']['contratinsertion_id']
							),
							'contain' => false
						)
					);

					$contratinsertion['Contratinsertion']['decision_ci'] = Set::enum( @$dossierep['Decisioncontratcomplexeep93']['decision'], $enum );
					$contratinsertion['Contratinsertion']['observ_ci'] = @$dossierep['Decisioncontratcomplexeep93']['observ_ci'];
					$contratinsertion['Contratinsertion']['datevalidation_ci'] = @$dossierep['Decisioncontratcomplexeep93']['datevalidation_ci'];
					$contratinsertion['Contratinsertion']['datedecision'] = @$dossierep['Decisioncontratcomplexeep93']['datevalidation_ci'];

					$this->Contratinsertion->create( $contratinsertion );
					$success = $this->Contratinsertion->save() && $success;

					if( in_array( $dossierep['Decisioncontratcomplexeep93']['decision'], array( 'valide', 'rejete', 'annule' ) ) ) {
						$success = $this->Contratinsertion->Cer93->updateAllUnBound(
							array(
								'Cer93.positioncer' => '\''.Set::enum( @$dossierep['Decisioncontratcomplexeep93']['decision'], $enumCer93 ).'\'',
							),
							array( '"Cer93"."contratinsertion_id"' => $contratinsertion['Contratinsertion']['id'] )
						) && $success;

						if( $dossierep['Decisioncontratcomplexeep93']['decision'] == 'valide' ) {
							$success = $this->Contratinsertion->WebrsaContratinsertion->updateRangsContratsPersonne( $contratinsertion['Contratinsertion']['personne_id'] ) && $success;
							$success = $this->Contratinsertion->Nonrespectsanctionep93->calculSortieProcedureRelanceParValidationCer( $contratinsertion ) && $success;
						}
					}
				}
			}
			$this->Contratinsertion->validate = $validate;

			return $success;
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
					$this->Contratinsertion->fields(),
					$this->Contratinsertion->Structurereferente->fields()
				);

				$datas['querydata']['joins'][] = $this->join( 'Contratinsertion' );
				$datas['querydata']['joins'][] = $this->Contratinsertion->join( 'Structurereferente' );

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
		*/
		public function getDecisionPdf( $passagecommissionep_id, $user_id = null  ) {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$datas = Cache::read( $cacheKey );

			if( $datas === false ) {
				$datas['querydata'] = $this->_qdDecisionPdf();

				$datas['querydata']['fields'] = array_merge(
					$datas['querydata']['fields'],
					$this->Contratinsertion->fields(),
					$this->Contratinsertion->Structurereferente->fields()
				);

				$datas['querydata']['joins'][] = $this->join( 'Contratinsertion' );
				$datas['querydata']['joins'][] = $this->Contratinsertion->join( 'Structurereferente' );

				// Traductions
				$datas['options'] = $this->Dossierep->Passagecommissionep->Decisioncontratcomplexeep93->enums();
				$datas['options']['Personne']['qual'] = ClassRegistry::init( 'Option' )->qual();
				$datas['options']['type']['voie'] = ClassRegistry::init( 'Option' )->typevoie();

				Cache::write( $cacheKey, $datas );
			}

			$datas['querydata']['conditions']['Passagecommissionep.id'] = $passagecommissionep_id;
			$gedooo_data = $this->Dossierep->Passagecommissionep->find( 'first', $datas['querydata'] );

			if( empty( $gedooo_data ) || !isset( $gedooo_data['Decisioncontratcomplexeep93'] ) || empty( $gedooo_data['Decisioncontratcomplexeep93'] ) ) {
				return false;
			}

			// Choix du modèle de document: valide,rejete,annule,reporte
			$decision = $gedooo_data['Decisioncontratcomplexeep93']['decision'];
			$modeleOdt  = "{$this->alias}/decision_{$decision}.odt";

			return $this->_getOrCreateDecisionPdf( $passagecommissionep_id, $gedooo_data, $modeleOdt, $datas['options'] );
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
					'Dossier.numdemrsa',
					'Adresse.nomcom',
					'Contratinsertion.num_contrat',
					'Contratinsertion.dd_ci',
					'Contratinsertion.duree_engag',
					'Cer93.duree',
					'Contratinsertion.df_ci',
					'Contratinsertion.structurereferente_id',
					'Contratinsertion.nature_projet',
					'Contratinsertion.type_demande',
					'Structurereferente.lib_struc',
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
					'alias' => 'Contratinsertion',
					'table' => 'contratsinsertion',
					'type' => 'INNER',
					'conditions' => array(
						'Contratinsertion.id = '.$this->alias.'.contratinsertion_id'
					)
				),
				$this->Contratinsertion->join( 'Cer93', array( 'type' => 'INNER' ) ),
				array(
					'alias' => 'Structurereferente',
					'table' => 'structuresreferentes',
					'type' => 'INNER',
					'conditions' => array(
						'Structurereferente.id = Contratinsertion.structurereferente_id'
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
	}
?>