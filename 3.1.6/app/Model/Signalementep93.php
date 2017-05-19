<?php
	/**
	 * Code source de la classe Signalementep93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( ABSTRACTMODELS.'Thematiqueep.php' );

	/**
	 * La classe Signalementep93 ...
	 *
	 * @package app.Model
	 */
	class Signalementep93 extends Thematiqueep
	{
		public $name = 'Signalementep93';

		public $actsAs = array(
			'Autovalidate2',
			'ValidateTranslate',
			'Formattable',
			'Gedooo.Gedooo'
		);

		public $belongsTo = array(
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'contratinsertion_id',
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
		);

		/**
		* Chemin relatif pour les modèles de documents .odt utilisés lors des
		* impressions. Utiliser %s pour remplacer par l'alias.
		*/
		public $modelesOdt = array(
			// Convocation EP
			'%s/convocationep_beneficiaire.odt',
			// Décision EP (décision CG)
			'%s/decision_delai.odt',
			'%s/decision_reduction_ppae.odt',
			'%s/decision_reduction_pdv.odt',
			'%s/decision_maintien.odt',
			'%s/decision_suspensiontotale.odt',
			'%s/decision_suspensionpartielle.odt',
			'%s/decision_reporte.odt',
			'%s/decision_annule.odt',
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
					//'Dossierep.commissionep_id' => $commissionep_id
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
							'fields' => array(
								'id',
								'dossier_id',
								'sitfam',
								'ddsitfam',
								'typeocclog',
								'mtvallocterr',
								'mtvalloclog',
								'contefichliairsa',
								'mtestrsa',
								'raisoctieelectdom',
								"( SELECT COUNT(DISTINCT(personnes.id)) FROM personnes INNER JOIN prestations ON ( personnes.id = prestations.personne_id ) WHERE personnes.foyer_id = \"Foyer\".\"id\" AND prestations.natprest = 'RSA' AND prestations.rolepers = 'ENF' ) AS \"Foyer__nbenfants\"",
							),
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
							'date',
							'motif',
							'rang',
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
				else if( $niveauDecision == 'cg' ) {
					$formData[$modeleDecisions][$key]['decision'] = $dossierep['Passagecommissionep'][0][$modeleDecisions][0]['decision'];
					$formData[$modeleDecisions][$key]['decisionpcg'] = 'valide';
					$formData[$modeleDecisions][$key]['raisonnonpassage'] = $dossierep['Passagecommissionep'][0][$modeleDecisions][0]['raisonnonpassage'];
					if ( Configure::read( 'Cg.departement' ) != 93 ) {
						$formData[$modeleDecisions][$key]['commentaire'] = $dossierep['Passagecommissionep'][0][$modeleDecisions][0]['commentaire'];
					}
				}
			}
			return $formData;
		}

		/**
		* TODO: docs
		*/

		public function saveDecisions( $data, $niveauDecision ) {
			// FIXME: filtrer les données
			$modeleDecisions = Inflector::classify( 'decisions'.Inflector::tableize( $this->alias ) );

			$themeData = Set::extract( $data, "/{$modeleDecisions}" );
			if( empty( $themeData ) ) {
				return true;
			}
			else {
				foreach( array_keys( $themeData ) as $key ) {
					// On complètre /on nettoie si ce n'est pas envoyé par le formulaire
					if( $themeData[$key][$modeleDecisions]['decision'] == '1reduction' ) {
						$themeData[$key][$modeleDecisions]['dureesursis'] = null;
						$themeData[$key][$modeleDecisions]['montantreduction'] = Configure::read( "{$this->alias}.montantReduction" );
					}
					else if( $themeData[$key][$modeleDecisions]['decision'] == '1delai' ) {
						$themeData[$key][$modeleDecisions]['montantreduction'] = null;
						$themeData[$key][$modeleDecisions]['dureesursis'] = Configure::read( "{$this->alias}.dureeSursis" );
					}
					else if( in_array( $themeData[$key][$modeleDecisions]['decision'],  array( '1maintien', '1pasavis', '2pasavis', 'reporte' ) ) ) {
						$themeData[$key][$modeleDecisions]['montantreduction'] = null;
						$themeData[$key][$modeleDecisions]['dureesursis'] = null;
					}
					// FIXME: la même chose pour l'étape 2
				}

				$success = $this->Dossierep->Passagecommissionep->{$modeleDecisions}->saveAll( $themeData, array( 'atomic' => false ) );
				$this->Dossierep->Passagecommissionep->updateAllUnBound(
					array( 'Passagecommissionep.etatdossierep' => '\'decision'.$niveauDecision.'\'' ),
					array( '"Passagecommissionep"."id"' => Set::extract( $data, "/{$modeleDecisions}/passagecommissionep_id" ) )
				);

				return $success;
			}
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

			$modeleDecisions = Inflector::classify( 'decisions'.Inflector::tableize( $this->alias ) );

			$niveauDecisionFinale = $commissionep['Ep']['Regroupementep'][Inflector::underscore( $this->alias )];

			$dossierseps = $this->Dossierep->Passagecommissionep->find(
				'all',
				array(
					'fields' => array_merge(
						array(
							'Passagecommissionep.id',
							'Passagecommissionep.commissionep_id',
							'Passagecommissionep.dossierep_id',
							'Passagecommissionep.etatdossierep',
							'Dossierep.personne_id',
							"{$modeleDecisions}.decision"
						),
						$this->fields()
					),
					'conditions' => array(
						'Passagecommissionep.commissionep_id' => $commissionep_id
						/*'Dossierep.commissionep_id' => $commissionep_id,
						'Dossierep.themeep' => Inflector::tableize( $this->alias ),//FIXME: ailleurs aussi*/
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
							'table' => Inflector::tableize( $this->alias ),
							'alias' => $this->alias,
							'type' => 'INNER',
							'conditions' => array(
								"{$this->alias}.dossierep_id = Dossierep.id"
							)
						),
						array(
							'table' => 'decisions'.Inflector::tableize( $this->alias ),
							'alias' => $modeleDecisions,
							'type' => 'INNER',
							'conditions' => array(
								"{$modeleDecisions}.passagecommissionep_id = Passagecommissionep.id",
								"{$modeleDecisions}.etape" => $etape
							)
						)
					),
					'contain' => false
				)
			);

			$success = true;
			foreach( $dossierseps as $dossierep ) {
				if( $niveauDecisionFinale == "decision{$etape}" ) { // FIXME: patch SQL pour le passif -> $niveauDecisionFinale == $etape
					// Désactivation des entrées de la thématique
					$nonrespectsanctionep = array( $this->alias => $dossierep[$this->alias] );
					$nonrespectsanctionep[$this->alias]['active'] = 0;
					if( !isset( $dossierep[$modeleDecisions]['decision'] ) ) {
						$success = false;
					}

					$this->create( $nonrespectsanctionep );
					$success = $this->save() && $success;

					// Si l'allocataire est sanctionné et qu'il avait un D1, il sort de l'accompagnement
					if( in_array( $dossierep[$modeleDecisions]['decision'], array( '1reduction', '2suspensiontotale', '2suspensionpartielle' ) ) ) {
						$success = $this->Dossierep->Personne->Questionnaired2pdv93->saveAuto( $dossierep['Dossierep']['personne_id'], 'abandon' ) && $success;
					}
				}
			}

			return $success;
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
		* Récupération du courrier de convocation à l'allocataire pour un passage
		* en commission donné.
		* FIXME: spécifique par thématique
		*/

		/*public function getConvocationBeneficiaireEpPdf( $passagecommissionep_id ) {
			$gedooo_data = $this->Dossierep->Passagecommissionep->find(
				'first',
				array(
					'conditions' => array( 'Passagecommissionep.id' => $passagecommissionep_id ),
					'contain' => array(
						'Dossierep' => array(
							'Personne',
						),
						'Commissionep'
					)
				)
			);

			if( empty( $gedooo_data ) ) {
				return false;
			}

			return $this->ged( $gedooo_data, "Commissionep/convocationep_beneficiaire.odt" );
		}*/

		/**
		* Récupération de la décision suite au passage en commission d'un dossier
		* d'EP pour un certain niveau de décision.
		* FIXME: que pour le 93 ?
		* FIXME: autre
		*/

		public function getDecisionPdf( $passagecommissionep_id, $user_id = null  ) {
			$modele = $this->alias;
			$modeleDecisions = 'Decision'.Inflector::underscore( $modele );

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$datas = Cache::read( $cacheKey );

			if( $datas === false ) {
				$datas['querydata'] = $this->_qdDecisionPdf();

				$datas['querydata']['fields'] = array_merge(
					$datas['querydata']['fields'],
					$this->Contratinsertion->fields(),
					$this->Contratinsertion->Structurereferente->fields(),
					$this->Contratinsertion->Structurereferente->Typeorient->fields()
				);

				$datas['querydata']['joins'][] =  $this->join( 'Contratinsertion' );
				$datas['querydata']['joins'][] =  $this->Contratinsertion->join( 'Structurereferente' );
				$datas['querydata']['joins'][] =  $this->Contratinsertion->Structurereferente->join( 'Typeorient' );

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

			// Choix du modèle de document
			$decision = $gedooo_data[$modeleDecisions]['decision'];

			if( $decision == '1delai' ) {
				$delairelance = Configure::read( "{$this->alias}.decisionep.delai" );
				$gedooo_data[$this->alias]['datedelaisuppl'] = date( 'Y-m-d', strtotime( "+{$delairelance} days", strtotime( $gedooo_data['Passagecommissionep']['impressiondecision'] ) ) );
				$modeleOdt  = "{$this->alias}/decision_delai.odt";
			}
			else if( $decision == '1reduction' ) {
				$emploi = preg_match( '/Emploi/i', $gedooo_data['Typeorient']['lib_type_orient'] );
				if( $emploi ) {
					$modeleOdt  = "{$this->alias}/decision_reduction_ppae.odt";
				}
				else {
					$modeleOdt  = "{$this->alias}/decision_reduction_pdv.odt";
				}
			}
			else if( in_array( $decision, array( '1maintien', '2maintien' ) ) ) {
				$modeleOdt  = "{$this->alias}/decision_maintien.odt";
			}
			else if( $decision == '2suspensiontotale' ) {
				$modeleOdt  = "{$this->alias}/decision_suspensiontotale.odt";
			}
			else if( $decision == '2suspensionpartielle' ) {
				$modeleOdt  = "{$this->alias}/decision_suspensionpartielle.odt";
			}
			else if( in_array( $decision, array( '1pasavis', '2pasavis', 'reporte' ) ) ) {
				$modeleOdt  = "{$this->alias}/decision_reporte.odt";
			}
			else if( $decision == 'annule' ) {
				$modeleOdt  = "{$this->alias}/decision_annule.odt";
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
					'Personne.id',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Dossier.numdemrsa',
					'Adresse.nomcom',
					'Contratinsertion.num_contrat',
					'Contratinsertion.dd_ci',
					'Cer93.duree',
					'Contratinsertion.duree_engag',
					'Contratinsertion.df_ci',
					'Contratinsertion.structurereferente_id',
					'Contratinsertion.nature_projet',
					'Contratinsertion.type_demande',
					'Structurereferente.lib_struc',
					"{$this->alias}.rang",
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

		/**
		* Récupération du courrier de convocation à l'allocataire pour un passage
		* en commission donné.
		*
		* @param integer $passagecommissionep_id
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

			return $this->ged( $gedooo_data, "{$this->alias}/convocationep_beneficiaire.odt", false, $datas['options'] );
		}

		/**
		 * Retourne l'id de la personne à laquelle est lié un enregistrement.
		 *
		 * @param integer $id L'id de l'enregistrement
		 * @return integer
		 */
		public function personneId( $id ) {
			$querydata = array(
				'fields' => array( "Contratinsertion.personne_id" ),
				'joins' => array(
					$this->join( 'Contratinsertion', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					"{$this->alias}.id" => $id
				),
				'recursive' => -1
			);

			$result = $this->find( 'first', $querydata );

			if( !empty( $result ) ) {
				return $result['Contratinsertion']['personne_id'];
			}
			else {
				return null;
			}
		}
	}
?>
