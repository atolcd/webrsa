<?php
	/**
	 * Code source de la classe Regressionorientationep58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Thematiqueep', 'Model/Abstractclass' );

	/**
	 * La classe Regressionorientationep58 ...
	 *
	 * @package app.Model
	 */
	class Regressionorientationep58 extends Thematiqueep
	{
		public $name = 'Regressionorientationep58';

		public $actsAs = array(
			'Dependencies',
			'Gedooo.Gedooo',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
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
			'Structureorientante' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structureorientante_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Referentorientant' => array(
				'className' => 'Referent',
				'foreignKey' => 'referentorientant_id',
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
			'%s/decision_annule.odt',
			'%s/decision_reporte.odt',
			'%s/decision_accepte.odt',
			'%s/decision_refuse.odt',
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
			'referentorientant_id' => array(
				'dependentForeignKeys' => array(
					'rule' => array( 'dependentForeignKeys', 'Referentorientant', 'Structureorientante', 'Structurereferente' ),
					'message' => 'La référent orientant n\'appartient pas à la structure chargée de l\'évaluation',
				)
			)
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
					$this->alias.'.id',
					$this->alias.'.datedemande',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Passagecommissionep.etatdossierep'
				),
				'conditions' => array(
					'Dossierep.actif' => '1',
					'Dossierep.personne_id' => $personne_id,
					'Dossierep.themeep' => Inflector::tableize( $this->alias ),
					'Dossierep.id NOT IN ( '.$this->Dossierep->Passagecommissionep->sq(
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
							$this->alias.'.dossierep_id = Dossierep.id'
						)
					),
					array(
						'table' => 'typesorients',
						'alias' => 'Typeorient',
						'type' => 'INNER',
						'conditions' => array(
							$this->alias.'.typeorient_id = Typeorient.id'
						)
					),
					array(
						'table' => 'structuresreferentes',
						'alias' => 'Structurereferente',
						'type' => 'INNER',
						'conditions' => array(
							$this->alias.'.structurereferente_id = Structurereferente.id'
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
						'Orientstruct' => array(
							'order' => array( 'Orientstruct.date_valid DESC' ),
							'limit' => 1,
							'Typeorient',
							'Structurereferente',
							'Referent',
						),
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
						'Referent',
					),
					'Passagecommissionep' => array(
						'conditions' => array(
							'Passagecommissionep.commissionep_id' => $commissionep_id
						),
						'Decision'.Inflector::underscore( $this->alias ) => array(
							'Typeorient',
							'Structurereferente',
							'order' => array( 'Decision'.Inflector::underscore( $this->alias ).'.etape DESC' )
						)
					)
				)
			);
		}

		public function prepareFormData( $commissionep_id, $datas, $niveauDecision ) {

			// Doit-on prendre une décision à ce niveau ?
			$themes = $this->Dossierep->Passagecommissionep->Commissionep->WebrsaCommissionep->themesTraites( $commissionep_id );
			$niveauFinal = Hash::get( $themes, Inflector::underscore($this->alias) );
			if( ( $niveauFinal == 'ep' ) && ( $niveauDecision == 'cg' ) ) {
				return array();
			}

			$formData = array();
			foreach( $datas as $key => $dossierep ) {
// debug( $dossierep );
				$formData['Decision'.Inflector::underscore( $this->alias )][$key]['passagecommissionep_id'] = @$datas[$key]['Passagecommissionep'][0]['id'];

				$formData['Decision'.Inflector::underscore( $this->alias )][$key]['id'] = $this->_prepareFormDataDecisionId( $dossierep );

				// On modifie les enregistrements de cette étape
				if( @$dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['etape'] == $niveauDecision && !empty( $dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['decision'] ) ) {
					$formData['Decision'.Inflector::underscore( $this->alias )][$key] = @$dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0];

					$formData['Decision'.Inflector::underscore( $this->alias )][$key]['referent_id'] = implode(
						'_',
						array(
							$formData['Decision'.Inflector::underscore( $this->alias )][$key]['structurereferente_id'],
							$formData['Decision'.Inflector::underscore( $this->alias )][$key]['referent_id']
						)
					);

					$formData['Decision'.Inflector::underscore( $this->alias )][$key]['structurereferente_id'] = implode(
						'_',
						array(
							$formData['Decision'.Inflector::underscore( $this->alias )][$key]['typeorient_id'],
							$formData['Decision'.Inflector::underscore( $this->alias )][$key]['structurereferente_id']
						)
					);


				}
				// On ajoute les enregistrements de cette étape
				else {
					if( $niveauDecision == 'ep' ) {
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['typeorient_id'] = $dossierep[$this->alias]['typeorient_id'];

						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['referent_id'] = implode(
							'_',
							array(
								$dossierep[$this->alias]['structurereferente_id'],
								$dossierep[$this->alias]['referent_id']
							)
						);

						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['structurereferente_id'] = implode(
							'_',
							array(
								$dossierep[$this->alias]['typeorient_id'],
								$dossierep[$this->alias]['structurereferente_id']
							)
						);


					}
					elseif( $niveauDecision == 'cg' ) {
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['decision'] = $dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['decision'];
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['raisonnonpassage'] = $dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['raisonnonpassage'];
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['commentaire'] = $dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['commentaire'];
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['typeorient_id'] = $dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['typeorient_id'];

						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['referent_id'] = implode(
							'_',
							array(
								$dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['structurereferente_id'],
								$dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['referent_id']
							)
						);

						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['structurereferente_id'] = implode(
							'_',
							array(
								$dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['typeorient_id'],
								$dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['structurereferente_id']
							)
						);

					}
				}
			}
// debug($formData);
			return $formData;
		}

		/**
		*
		*/

		public function saveDecisions( $data, $niveauDecision ) {
			$success = true;
			$themeData = Set::extract( $data, '/Decision'.Inflector::underscore( $this->alias ) );
			if( empty( $themeData ) ) {
				return true;
			}
			else {
				$success = $this->Dossierep->Passagecommissionep->{'Decision'.Inflector::underscore( $this->alias )}->saveAll( $themeData, array( 'atomic' => false ) ) && $success;
				$this->Dossierep->Passagecommissionep->updateAllUnBound(
					array( 'Passagecommissionep.etatdossierep' => '\'decision'.$niveauDecision.'\'' ),
					array( '"Passagecommissionep"."id"' => Set::extract( $data, '/Decision'.Inflector::underscore( $this->alias ).'/passagecommissionep_id' ) )
				);

				return $success;
			}
		}

		/**
		 * Retourne une partie de querydata concernant la thématique pour le PV d'EP.
		 *
		 * @return array
		 */
		public function qdProcesVerbal() {
			$modele = $this->alias;
			$modeleDecisions = 'Decision'.Inflector::underscore( $this->alias );

			$querydata = array(
				'fields' => array(
					"{$modele}.id",
					"{$modele}.dossierep_id",
					"{$modele}.typeorient_id",
					"{$modele}.structurereferente_id",
					"{$modele}.datedemande",
					"{$modele}.referent_id",
					"{$modele}.user_id",
					"{$modele}.commentaire",
					"{$modele}.created",
					"{$modele}.modified",
					"{$modeleDecisions}.id",
					"{$modeleDecisions}.typeorient_id",
					"{$modeleDecisions}.structurereferente_id",
					"{$modeleDecisions}.referent_id",
					"{$modeleDecisions}.etape",
					"{$modeleDecisions}.commentaire",
					"{$modeleDecisions}.created",
					"{$modeleDecisions}.modified",
					"{$modeleDecisions}.passagecommissionep_id",
					"{$modeleDecisions}.decision",
					"{$modeleDecisions}.raisonnonpassage",
				),
				'joins' => array(
					array(
						'table'      => Inflector::tableize( $modele ),
						'alias'      => $modele,
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( "{$modele}.dossierep_id = Dossierep.id" ),
					),
					array(
						'table'      => Inflector::tableize( $modeleDecisions ),
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

			$modeleDecisionPart = 'decregressori58';
			$aliases = array(
				'Typeorient' => "Typeorient{$modeleDecisionPart}",
				'Structurereferente' => "Structurereferente{$modeleDecisionPart}",
				'Referent' => "Referent{$modeleDecisionPart}"
			);

			$fields = array_merge(
				$this->Dossierep->Passagecommissionep->{$modeleDecisions}->Typeorient->fields(),
				$this->Dossierep->Passagecommissionep->{$modeleDecisions}->Structurereferente->fields(),
				$this->Dossierep->Passagecommissionep->{$modeleDecisions}->Referent->fields()
			);
			$fields = array_words_replace( $fields, $aliases );
			$querydata['fields'] = array_merge( $querydata['fields'], $fields );


			$joins = array(
				$this->Dossierep->Passagecommissionep->{$modeleDecisions}->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
				$this->Dossierep->Passagecommissionep->{$modeleDecisions}->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
				$this->Dossierep->Passagecommissionep->{$modeleDecisions}->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
			);
			$joins = array_words_replace( $joins, $aliases );
			$querydata['joins'] = array_merge( $querydata['joins'], $joins );

			return $querydata;
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

				// Champs supplémentaires
				$datas['querydata']['fields'] = array_merge(
					$datas['querydata']['fields'],
					$this->Typeorient->fields(),
					$this->Structurereferente->fields(),
					$this->Referent->fields()
				);

				$datas['querydata']['joins'][] = $this->join( 'Typeorient' );
				$datas['querydata']['joins'][] = $this->join( 'Structurereferente' );
				$datas['querydata']['joins'][] = $this->join( 'Referent' );

				// Traductions
				$datas['options']['Referent']['qual'] = $datas['options']['Personne']['qual'];

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
		* Récupération de la décision suite au passage en commission d'un dossier
		* d'EP pour un certain niveau de décision.
		*/

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

				$datas['querydata']['fields'] = array_merge(
					$datas['querydata']['fields'],
					$this->Typeorient->fields(),
					$this->Structurereferente->fields(),
					$this->Referent->fields()
				);
				$datas['querydata']['joins'][] = $this->join( 'Typeorient' );
				$datas['querydata']['joins'][] = $this->join( 'Structurereferente' );
				$datas['querydata']['joins'][] = $this->join( 'Referent' );

				// Nouveau type d'orientation, de structureréférente et de référent
				$aliases = array(
					'Typeorient' => "{$modeleDecisions}typeorient",
					'Structurereferente' => "{$modeleDecisions}structurereferente",
					'Referent' => "{$modeleDecisions}referent",
				);
				foreach( $aliases as $modelName => $aliasModelName ) {
					$join = array_words_replace( $this->Dossierep->Passagecommissionep->{$modeleDecisions}->join( $modelName, array( 'type' => 'LEFT OUTER' ) ), $aliases );
					$fields = array_words_replace( $this->Dossierep->Passagecommissionep->{$modeleDecisions}->{$modelName}->fields(), $aliases );

					$datas['querydata']['fields'] = array_merge( $datas['querydata']['fields'], $fields );
					$datas['querydata']['joins'][] = $join;
				}

				// Traductions
				$datas['options'] = $this->Dossierep->Passagecommissionep->{$modeleDecisions}->enums();
				$datas['options']['Personne']['qual'] = ClassRegistry::init( 'Option' )->qual();
				$datas['options']['Referent']['qual'] = $datas['options']['Personne']['qual'];

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

			// Choix du modèle de document
			$decision = $gedooo_data[$modeleDecisions]['decision'];
			$modeleOdt = "{$this->alias}/decision_{$decision}.odt";

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
					'Personne.dtnai',
					'Dossier.matricule',
					'Structurereferente.lib_struc',
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
					'alias' => 'Structurereferente',
					'table' => 'structuresreferentes',
					'type' => 'INNER',
					'conditions' => array(
						'Structurereferente.id = '.$this->alias.'.structurereferente_id'
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
		* Finalisation de la décision pour le cg58
		*/

		public function finaliser( $commissionep_id, $etape, $user_id ) {
			$success = true;

			$commissionep = $this->Dossierep->Passagecommissionep->Commissionep->find(
				'first',
				array(
					'conditions' => array(
						'Commissionep.id' => $commissionep_id
					),
					'contain' => false
				)
			);
			list( $dateseance, $heureseance ) = explode ( ' ', $commissionep['Commissionep']['dateseance'] );

			$dossierseps = $this->Dossierep->find(
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
						'themeep' => 'regressionsorientationseps58'
					),
					'contain' => array(
						'Regressionorientationep58',
						'Passagecommissionep' => array(
							'conditions' => array(
								'Passagecommissionep.commissionep_id' => $commissionep_id
							),
							'Decisionregressionorientationep58'
						)
					)
				)
			);

			$this->Structurereferente->Orientstruct->Behaviors->detach( 'StorablePdf' );
			foreach( $dossierseps as $dossierep ) {
				if ( in_array( $dossierep['Passagecommissionep'][0]['Decisionregressionorientationep58'][0]['decision'], array( 'accepte', 'refuse' ) ) ) {
					$rgorient = $this->Structurereferente->Orientstruct->WebrsaOrientstruct->rgorientMax( $dossierep['Dossierep']['personne_id'] ) + 1;
					$origine = ( $rgorient > 1 ? 'reorientation' : 'manuelle' );

					$orientstruct = array(
						'Orientstruct' => array(
							'personne_id' => $dossierep['Dossierep']['personne_id'],
							'typeorient_id' => $dossierep['Passagecommissionep'][0]['Decisionregressionorientationep58'][0]['typeorient_id'],
							'structurereferente_id' => $dossierep['Passagecommissionep'][0]['Decisionregressionorientationep58'][0]['structurereferente_id'],
							'date_propo' => $dossierep['Regressionorientationep58']['datedemande'],
							'date_valid' => $dateseance,
							'statut_orient' => 'Orienté',
							'referent_id' => $dossierep['Passagecommissionep'][0]['Decisionregressionorientationep58'][0]['referent_id'],
							'etatorient' => 'decision',
							'rgorient' => $rgorient,
							'origine' => $origine,
							'user_id' => $dossierep['Regressionorientationep58']['user_id']
						)
					);
					$this->Structurereferente->Orientstruct->create( $orientstruct );
					$success = $this->Structurereferente->Orientstruct->save( null, array( 'atomic' => false ) ) && $success;

					// Mise à jour de l'enregistrement de la thématique avec l'id de la nouvelle orientation
					$success = $success && $this->updateAllUnBound(
						array( "\"{$this->alias}\".\"nvorientstruct_id\"" => $this->Structurereferente->Orientstruct->id ),
						array( "\"{$this->alias}\".\"id\"" => $dossierep[$this->alias]['id'] )
					);

					$success = $this->Structurereferente->Orientstruct->Personne->PersonneReferent->changeReferentParcours(
						$dossierep['Dossierep']['personne_id'],
						$dossierep['Passagecommissionep'][0]['Decisionregressionorientationep58'][0]['referent_id'],
						array(
							'PersonneReferent' => array(
								'personne_id' => $dossierep['Dossierep']['personne_id'],
								'referent_id' => $dossierep['Passagecommissionep'][0]['Decisionregressionorientationep58'][0]['referent_id'],
								'dddesignation' => $dateseance,
								'structurereferente_id' => $dossierep['Passagecommissionep'][0]['Decisionregressionorientationep58'][0]['structurereferente_id'],
								'user_id' => $dossierep['Regressionorientationep58']['user_id']
							)
						)
					) && $success;
				}
			}
			$this->Structurereferente->Orientstruct->Behaviors->attach( 'StorablePdf' );
			return $success;
		}

	}

?>