<?php
	/**
	 * Code source de la classe Sanctionrendezvousep58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Thematiqueep', 'Model/Abstractclass' );

	/**
	 * ...
	 *
	 * @package app.Model
	 */
	class Sanctionrendezvousep58 extends Thematiqueep
	{
		public $name = 'Sanctionrendezvousep58';

		public $actsAs = array(
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
			'Rendezvous' => array(
				'className' => 'Rendezvous',
				'foreignKey' => 'rendezvous_id',
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
			'%s/decision_maintien.odt',
			'%s/decision_sanction.odt',
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
							'rendezvous_id',
							'created',
							'modified'

						),
						'Rendezvous' => array(
							'Typerdv'
						)
					),
					'Passagecommissionep' => array(
						'conditions' => array(
							'Passagecommissionep.commissionep_id' => $commissionep_id
						),
						'Decisionsanctionrendezvousep58' => array(
							'Listesanctionep58',
							'order' => array( 'etape DESC' )
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
			$niveauFinal = $themes[Inflector::underscore( $this->alias )];
			if( ( $niveauFinal == 'ep' ) && ( $niveauDecision == 'cg' ) ) {
				return array();
			}

			$formData = array();
			foreach( $datas as $key => $dossierep ) {
				$formData['Decisionsanctionrendezvousep58'][$key]['passagecommissionep_id'] = @$datas[$key]['Passagecommissionep'][0]['id'];
				$formData['Decisionsanctionrendezvousep58'][$key]['id'] = $this->_prepareFormDataDecisionId( $dossierep );

				// On modifie les enregistrements de cette étape
				if( @$dossierep['Passagecommissionep'][0]['Decisionsanctionrendezvousep58'][0]['etape'] == $niveauDecision && !empty( $dossierep['Passagecommissionep'][0]['Decisionsanctionrendezvousep58'][0]['decision'] )  ) {
					$formData['Decisionsanctionrendezvousep58'][$key] = @$dossierep['Passagecommissionep'][0]['Decisionsanctionrendezvousep58'][0];
				}
				// On ajoute les enregistrements de cette étape
				else {
					if( $niveauDecision == 'ep' ) {
						$nbdossierssanctions = $this->Dossierep->find(
							'count',
							array(
								'conditions' => array(
									'Dossierep.actif' => '1',
									'Dossierep.personne_id' => $dossierep['Personne']['id'],
									'Dossierep.themeep' => 'sanctionsrendezvouseps58',
									'Passagecommissionep.etatdossierep' => 'traite',
									'Decisionsanctionrendezvousep58.decision' => 'sanction'
								),
								'joins' => array(
									array(
										'table' => 'sanctionsrendezvouseps58',
										'alias' => 'Sanctionrendezvousep58',
										'type' => 'INNER',
										'conditions' => array(
											'Sanctionrendezvousep58.dossierep_id = Dossierep.id'
										)
									),
									array(
										'table' => 'passagescommissionseps',
										'alias' => 'Passagecommissionep',
										'type' => 'INNER',
										'conditions' => array(
											'Passagecommissionep.dossierep_id = Dossierep.id'
										)
									),
									array(
										'table' => 'decisionssanctionsrendezvouseps58',
										'alias' => 'Decisionsanctionrendezvousep58',
										'type' => 'INNER',
										'conditions' => array(
											'Decisionsanctionrendezvousep58.passagecommissionep_id = Passagecommissionep.id'
										)
									)
								),
								'contain' => false
							)
						);

						$listesanctionep58 = $this->Dossierep->Passagecommissionep->Decisionsanctionrendezvousep58->Listesanctionep58->find(
							'first',
							array(
								'fields' => array(
									'Listesanctionep58.id'
								),
								'conditions' => array(
									'Listesanctionep58.rang' => $nbdossierssanctions + 1
								),
								'contain' => false
							)
						);

						if ( empty( $listesanctionep58 ) ) {
							$listesanctionep58 = $this->Dossierep->Passagecommissionep->Decisionsanctionrendezvousep58->Listesanctionep58->find(
								'first',
								array(
									'fields' => array(
										'Listesanctionep58.id'
									),
									'order' => array(
										'Listesanctionep58.rang DESC'
									),
									'contain' => false
								)
							);
						}

						$formData['Decisionsanctionrendezvousep58'][$key]['listesanctionep58_id'] = $listesanctionep58['Listesanctionep58']['id'];
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
			$themeData = Set::extract( $data, '/Decisionsanctionrendezvousep58' );
			if( empty( $themeData ) ) {
				return true;
			}
			else {
				$success = $this->Dossierep->Passagecommissionep->Decisionsanctionrendezvousep58->saveAll( $themeData, array( 'atomic' => false ) );
				$this->Dossierep->Passagecommissionep->updateAllUnBound(
					array( 'Passagecommissionep.etatdossierep' => '\'decision'.$niveauDecision.'\'' ),
					array( '"Passagecommissionep"."id"' => Set::extract( $data, '/Decisionsanctionrendezvousep58/passagecommissionep_id' ) )
				);
				return $success;
			}
		}

		/**
		* TODO: docs
		*/

		public function finaliser( $commissionep_id, $etape, $user_id ) {
			// Aucune action utile ?
			return true;
		}


		/**
		 * Retourne une partie de querydata concernant la thématique pour le PV d'EP.
		 *
		 * @return array
		 */
		public function qdProcesVerbal() {
			$querydata =  array(
				'fields' => array_merge(
					$this->Rendezvous->fields(),
					$this->Rendezvous->Statutrdv->StatutrdvTyperdv->fields(),
					$this->fields(),
					$this->Dossierep->Passagecommissionep->Decisionsanctionrendezvousep58->fields(),
					$this->Dossierep->Passagecommissionep->Decisionsanctionrendezvousep58->Listesanctionep58->fields(),
					$this->Dossierep->Passagecommissionep->Decisionsanctionrendezvousep58->Autrelistesanctionep58->fields()
				),
				'joins' => array(
					array(
						'table'      => 'sanctionsrendezvouseps58',
						'alias'      => 'Sanctionrendezvousep58',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Sanctionrendezvousep58.dossierep_id = Dossierep.id' ),
					),
					array(
						'table'      => 'decisionssanctionsrendezvouseps58',
						'alias'      => 'Decisionsanctionrendezvousep58',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'Decisionsanctionrendezvousep58.passagecommissionep_id = Passagecommissionep.id',
							'Decisionsanctionrendezvousep58.etape' => 'ep'
						),
					),
					array(
						'table'      => 'rendezvous',
						'alias'      => 'Rendezvous',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Sanctionrendezvousep58.rendezvous_id = Rendezvous.id' ),
					),
					array(
						'table'      => 'statutsrdvs_typesrdv',
						'alias'      => 'StatutrdvTyperdv',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'StatutrdvTyperdv.statutrdv_id = Rendezvous.statutrdv_id',
							'StatutrdvTyperdv.typerdv_id = Rendezvous.typerdv_id'
						),
					),
					$this->Dossierep->Passagecommissionep->Decisionsanctionrendezvousep58->join( 'Listesanctionep58', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossierep->Passagecommissionep->Decisionsanctionrendezvousep58->join( 'Autrelistesanctionep58', array( 'type' => 'LEFT OUTER' ) )
				)
			);

			return array_words_replace(
				$querydata,
				array(
					'Listesanctionep58' => 'Listesanctionrendezvousep58',
					'Autrelistesanctionep58' => 'Autrelistesanctionrendezvousep58'
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
		* Fonction retournant ce qui va aller dans un contain permettant de retrouver la liste des
		* dossierseps liés à une commission
		*/
		public function qdContainListeDossier() {
			return array(
				'Dossierep' => array(
					$this->alias,
					'Personne' => array(
						'Foyer' => array(
							'Dossier',
							'Adressefoyer' => array(
								'conditions' => array(
									'Adressefoyer.rgadr' => '01'
								),
								'Adresse'
							)
						)
					)
				)
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
					$this->Dossierep->Passagecommissionep->{$modeleDecisions}->Listesanctionep58->fields()
				);
				$datas['querydata']['joins'][] = $this->Dossierep->Passagecommissionep->{$modeleDecisions}->join( 'Listesanctionep58' );

				// Traductions
				$datas['options'] = $this->Dossierep->Passagecommissionep->{$modeleDecisions}->enums();
				$datas['options']['Personne']['qual'] = ClassRegistry::init( 'Option' )->qual();

				Cache::write( $cacheKey, $datas );
			}

			$datas['querydata']['conditions']['Passagecommissionep.id'] = $passagecommissionep_id;
			$gedooo_data = $this->Dossierep->Passagecommissionep->find( 'first', $datas['querydata'] );

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
					'Typerdv.motifpassageep',
					'StatutrdvTyperdv.motifpassageep',
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
				),
				array(
					'alias' => 'Rendezvous',
					'table' => 'rendezvous',
					'type' => 'INNER',
					'conditions' => array(
						'Rendezvous.id = '.$this->alias.'.rendezvous_id'
					)
				),
				array(
					'alias' => 'Structurereferente',
					'table' => 'structuresreferentes',
					'type' => 'INNER',
					'conditions' => array(
						'Structurereferente.id = Rendezvous.structurereferente_id'
					)
				),
				array(
					'alias' => 'Statutrdv',
					'table' => 'statutsrdvs',
					'type' => 'INNER',
					'conditions' => array(
						'Rendezvous.statutrdv_id = Statutrdv.id'
					)
				),
				array(
					'alias' => 'Typerdv',
					'table' => 'typesrdv',
					'type' => 'INNER',
					'conditions' => array(
						'Rendezvous.typerdv_id = Typerdv.id'
					)
				),
				array(
					'alias' => 'StatutrdvTyperdv',
					'table' => 'statutsrdvs_typesrdv',
					'type' => 'LEFT OUTER',
					'conditions' => array(
						'StatutrdvTyperdv.typerdv_id = Typerdv.id',
						'StatutrdvTyperdv.statutrdv_id = Statutrdv.id'
					)
				),
			);
			return $return;
		}
	}
?>