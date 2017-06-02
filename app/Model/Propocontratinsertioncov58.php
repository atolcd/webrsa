<?php
	/**
	 * Code source de la classe Propocontratinsertioncov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractThematiquecov58', 'Model/Abstractclass' );

	/**
	 * La classe Propocontratinsertioncov58 ...
	 *
	 * @package app.Model
	 */
	class Propocontratinsertioncov58 extends AbstractThematiquecov58
	{
		public $name = 'Propocontratinsertioncov58';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Règles de validation à appliquer en plus de celles déduites de la
		 * base de données.
		 *
		 * @var array
		 */
		public $validate = array(
			'structurereferente_id' => array(
				'choixStructure' => array(
					'rule' => array( 'choixStructure', 'statut_orient' ),
					'message' => 'Champ obligatoire'
				)
			),
			'duree_engag' => array(
				'checkDureeDates' => array(
					'rule' => array( 'checkDureeDates', 'dd_ci', 'df_ci' ),
					'message' => 'Les dates de début et de fin ne correspondent pas à la durée'
				)
			)
		);

		public $belongsTo = array(
			'Nvcontratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'nvcontratinsertion_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Dossiercov58' => array(
				'className' => 'Dossiercov58',
				'foreignKey' => 'dossiercov58_id',
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
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		/**
		 * Modèles utilisés par le modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'WebrsaPropocontratinsertioncov58'
		);

		/**
		 * Règle de validation.
		 *
		 * @param type $field
		 * @param type $compare_field
		 * @return boolean
		 */
		public function choixStructure( $field = array(), $compare_field = null ) {
			foreach( $field as $key => $value ) {
				if( !empty( $this->data[$this->name][$compare_field] ) && ( $this->data[$this->name][$compare_field] != 'En attente' ) && empty( $value ) ) {
					return false;
				}
			}
			return true;
		}

		/**
		 * Surcharge de la méthode beforeValidate pour nettoyer la valeur de
		 * duree_engag qui peut être suivie d'un '_' au CG 58 lorsque le formulaire
		 * est renvoyé en appuyant sur entrée alors que l'on se trouve dans le
		 * l'input de ce champ.
		 *
		 * @see Contratinsertion::beforeValidate()
		 *
		 * @param array $options
		 * @return boolean
		 */
		public function beforeValidate( $options = array() ) {
			$result = parent::beforeValidate( $options );

			$path = "{$this->alias}.duree_engag";
			if( Hash::check( $this->data, $path ) ) {
				$value = Hash::get( $this->data, $path );
				$value = preg_replace( '/^[^0-9]*([0-9]+)[^0-9]*$/', '\1', $value );
				$this->data = Hash::insert( $this->data, $path, $value );
			}

			return $result;
		}

		/**
		 * Fonction retournant un querydata qui va permettre de retrouver des
		 * dossiers de COV à sélectionner pour passer dans une commission donnée.
		 *
		 * @param integer $cov58_id
		 * @return array
		 */
		public function qdListeDossier( $cov58_id = null ) {
			$return = parent::qdListeDossier( $cov58_id );

			// Ajout de la structure référente du CER
			$return['fields'][] = 'Structurereferente.lib_struc';
			$return['joins'][] = $this->join( 'Structurereferente', array( 'type' => 'INNER' ) );

			return $return;
		}

		/**
		 * Retourne le querydata utilisé dans la partie décisions d'une COV.
		 *
		 * Surchargé afin d'ajouter le modèle de la thématique et le modèle de
		 * décision dans le contain.
		 *
		 * @param integer $cov58_id
		 * @return array
		 */
		public function qdDossiersParListe( $cov58_id ) {
			$result = parent::qdDossiersParListe( $cov58_id );
			$modeleDecision = 'Decision'.Inflector::underscore( $this->alias );

			$result['contain'][$this->alias] = array(
				'Structurereferente',
				'Referent'
			);

			$result['contain']['Passagecov58'][$modeleDecision] = null;

			return $result;
		}

		/**
		 *
		 * @deprecated
		 */
		public function getFields() {
			return array(
				$this->alias.'.id',
				$this->alias.'.datedemande',
				'Structurereferente.lib_struc',
				'Referent.nom',
				'Referent.prenom',
				'Referent.qual'
			);
		}

		/**
		 *
		 * @deprecated
		 */
		public function getJoins() {
			return array(
				array(
					'table' => 'proposcontratsinsertioncovs58',
					'alias' => $this->alias,
					'type' => 'INNER',
					'conditions' => array(
						"Dossiercov58.id = {$this->alias}.dossiercov58_id"
					)
				),
				array(
					'table' => 'structuresreferentes',
					'alias' => 'Structurereferente',
					'type' => 'INNER',
					'conditions' => array(
						"{$this->alias}.structurereferente_id = Structurereferente.id"
					)
				),
				array(
					'table' => 'referents',
					'alias' => 'Referent',
					'type' => 'LEFT OUTER',
					'conditions' => array(
						"{$this->alias}.referent_id = Referent.id"
					)
				)
			);
		}

		/**
		*
		*/

		public function ajoutPossible( $personne_id ) {
			$nbDossierscov = $this->Dossiercov58->find(
				'count',
				array(
					'conditions' => array(
						'Dossiercov58.personne_id' => $personne_id/*,
						'Dossiercov58.etapecov <>' => 'finalise'*/
					),
					'contain' => array(
						$this->alias
					)
				)
			);

			$nbPersonnes = $this->Personne->find(
				'count',
				array(
					'conditions' => array(
						'Personne.id' => $personne_id,
					),
					'joins' => array(
						array(
							'table'      => 'prestations',
							'alias'      => 'Prestation',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Personne.id = Prestation.personne_id',
								'Prestation.natprest = \'RSA\'',
								'Prestation.rolepers' => array( 'DEM', 'CJT' )
							)
						),
						array(
							'table'      => 'calculsdroitsrsa',
							'alias'      => 'Calculdroitrsa',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Personne.id = Calculdroitrsa.personne_id',
								'Calculdroitrsa.toppersdrodevorsa' => '1'
							)
						),
						array(
							'table'      => 'foyers',
							'alias'      => 'Foyer',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Foyer.id = Personne.foyer_id' )
						),
						array(
							'table'      => 'dossiers',
							'alias'      => 'Dossier',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Foyer.dossier_id = Dossier.id' )
						),
						array(
							'table'      => 'situationsdossiersrsa',
							'alias'      => 'Situationdossierrsa',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Situationdossierrsa.dossier_id = Dossier.id',
								'Situationdossierrsa.etatdosrsa' => array( 'Z', '2', '3', '4' )
							)
						),
					),
					'recursive' => -1
				)
			);
			return ( ( $nbDossierscov == 0 ) && ( $nbPersonnes == 1 ) );
		}

		/**
		*
		*/
		public function saveDecisions( $data ) {
			$modelDecisionName = 'Decision'.Inflector::underscore( $this->alias );

			$success = true;
			if ( isset( $data[$modelDecisionName] ) && !empty( $data[$modelDecisionName] ) ) {
				foreach( $data[$modelDecisionName] as $key => $values ) {

					$passagecov58 = $this->Dossiercov58->Passagecov58->find(
						'first',
						array(
							'fields' => array_merge(
								$this->Dossiercov58->Passagecov58->fields(),
								$this->Dossiercov58->Passagecov58->Cov58->fields(),
								$this->Dossiercov58->fields(),
								$this->fields()
							),
							'conditions' => array(
								'Passagecov58.id' => $values['passagecov58_id']
							),
							'joins' => array(
								$this->Dossiercov58->Passagecov58->join( 'Dossiercov58' ),
								$this->Dossiercov58->Passagecov58->join( 'Cov58' ),
								$this->Dossiercov58->join( $this->alias )
							)
						)
					);

					$rg_ci = $this->Dossiercov58->Personne->Contratinsertion->WebrsaContratinsertion->rgCiMax( $passagecov58['Dossiercov58']['personne_id'] ) + 1;
					$num_contrat = ( $rg_ci == 1 ? 'PRE' : 'REN' );

					if( $values['decisioncov'] == 'valide' ){
						$contratinsertion = array(
							'Contratinsertion' => array(
								'personne_id' => $passagecov58['Dossiercov58']['personne_id'],
								'structurereferente_id' => $passagecov58[$this->alias]['structurereferente_id'],
								'referent_id' => $passagecov58[$this->alias]['referent_id'],
								'num_contrat' => $num_contrat,
								'dd_ci' => $values['dd_ci'],
								'duree_engag' => $values['duree_engag'],
								'df_ci' => $values['df_ci'],
								'forme_ci' => $passagecov58[$this->alias]['forme_ci'],
								'avisraison_ci' => $passagecov58[$this->alias]['avisraison_ci'],
								'rg_ci' => $rg_ci,
								'observ_ci' => $values['commentaire'],
								'date_saisi_ci' => $passagecov58[$this->alias]['datedemande'],
								'datevalidation_ci' => $values['datevalidation'],
								'decision_ci' => 'V',
								'avenant_id' => $passagecov58[$this->alias]['avenant_id']
							)
						);

						$this->Dossiercov58->Personne->Contratinsertion->create( $contratinsertion ) && $success;
						$success = $this->Dossiercov58->Personne->Contratinsertion->save( null, array( 'atomic' => false ) ) && $success;

						// Mise à jour de l'enregistrement de la thématique avec l'id du nouveau CER
						$success = $success && $this->updateAllUnBound(
							array( "\"{$this->alias}\".\"nvcontratinsertion_id\"" => $this->Dossiercov58->Personne->Contratinsertion->id ),
							array( "\"{$this->alias}\".\"id\"" => $data[$this->alias][$key] )
						);
					}
					else if( $values['decisioncov'] == 'refuse' ) {
						$contratinsertion = array(
							'Contratinsertion' => array(
								'personne_id' => $passagecov58['Dossiercov58']['personne_id'],
								'structurereferente_id' => $passagecov58[$this->alias]['structurereferente_id'],
								'referent_id' => $passagecov58[$this->alias]['referent_id'],
								'num_contrat' => null,
								'dd_ci' => $passagecov58[$this->alias]['dd_ci'],
								'duree_engag' => $passagecov58[$this->alias]['duree_engag'],
								'df_ci' => $passagecov58[$this->alias]['df_ci'],
								'forme_ci' => $passagecov58[$this->alias]['forme_ci'],
								'avisraison_ci' => $passagecov58[$this->alias]['avisraison_ci'],
								'rg_ci' => null,
								'observ_ci' => $values['commentaire'],
								'date_saisi_ci' => $passagecov58[$this->alias]['datedemande'],
								'datevalidation_ci' => $values['datevalidation'],
								'decision_ci' => 'N',
								'avenant_id' => $passagecov58[$this->alias]['avenant_id']
							)
						);

						$this->Dossiercov58->Personne->Contratinsertion->create( $contratinsertion ) && $success;
						$success = $this->Dossiercov58->Personne->Contratinsertion->save( null, array( 'atomic' => false ) ) && $success;

						// Mise à jour de l'enregistrement de la thématique avec l'id du nouveau CER
						$success = $success && $this->updateAllUnBound(
							array( "\"{$this->alias}\".\"nvcontratinsertion_id\"" => $this->Dossiercov58->Personne->Contratinsertion->id ),
							array( "\"{$this->alias}\".\"id\"" => $data[$this->alias][$key] )
						);
					}

					// Modification etat du dossier passé dans la COV
					if( in_array( $values['decisioncov'], array( 'valide', 'refuse' ) ) ){
						$this->Dossiercov58->Passagecov58->updateAllUnBound(
							array( 'Passagecov58.etatdossiercov' => '\'traite\'' ),
							array('"Passagecov58"."id"' => $passagecov58['Passagecov58']['id'] )
						);
					}
					else if( $values['decisioncov'] == 'annule' ){
						$this->Dossiercov58->Passagecov58->updateAllUnBound(
							array( 'Passagecov58.etatdossiercov' => '\'annule\'' ),
							array('"Passagecov58"."id"' => $passagecov58['Passagecov58']['id'] )
						);
					}
					else if( $values['decisioncov'] == 'reporte' ){
						$this->Dossiercov58->Passagecov58->updateAllUnBound(
							array( 'Passagecov58.etatdossiercov' => '\'reporte\'' ),
							array('"Passagecov58"."id"' => $passagecov58['Passagecov58']['id'] )
						);
					}
				}

				$success = $this->Dossiercov58->Passagecov58->{$modelDecisionName}->saveAll( Set::extract( $data, '/'.$modelDecisionName ), array( 'atomic' => false ) ) && $success;
			}

			return $success;
		}

		/**
		*
		*/
		public function acteDecision($data) {
			$modelDecisionName = 'Decision'.Inflector::underscore( $this->alias );
			$success = true;

			if ( isset( $data[$modelDecisionName] ) && !empty( $data[$modelDecisionName] ) ) {
				foreach( $data[$modelDecisionName] as $key => $values ) {
					$passagecov58 = $this->Dossiercov58->Passagecov58->find(
						'first',
						array(
							'fields' => array_merge(
								$this->Dossiercov58->Passagecov58->fields(),
								$this->Dossiercov58->Passagecov58->Cov58->fields(),
								$this->Dossiercov58->fields(),
								$this->fields()
							),
							'conditions' => array(
								'Passagecov58.id' => $values['passagecov58_id']
							),
							'joins' => array(
								$this->Dossiercov58->Passagecov58->join( 'Dossiercov58' ),
								$this->Dossiercov58->Passagecov58->join( 'Cov58' ),
								$this->Dossiercov58->join( $this->alias )
							)
						)
					);


// 					list($datevalidation, $heure) = explode(' ', $values['datevalidation']);

					if( $values['decisioncov'] == 'valide' ){
						$contratinsertion = array(
							'Contratinsertion' => array(
								'personne_id' => $passagecov58['Dossiercov58']['personne_id'],
								'structurereferente_id' => $passagecov58[$this->alias]['structurereferente_id'],
								'referent_id' => $passagecov58[$this->alias]['referent_id'],
								'num_contrat' => $passagecov58[$this->alias]['num_contrat'],
								'dd_ci' => $passagecov58[$this->alias]['dd_ci'],
								'duree_engag' => $passagecov58[$this->alias]['duree_engag'],
								'df_ci' => $passagecov58[$this->alias]['df_ci'],
								'forme_ci' => $passagecov58[$this->alias]['forme_ci'],
								'avisraison_ci' => $passagecov58[$this->alias]['avisraison_ci'],
								'rg_ci' => $passagecov58[$this->alias]['rg_ci'],
								'observ_ci' => $values['commentaire'],
								'date_saisi_ci' => $passagecov58[$this->alias]['datedemande'],
								'datevalidation_ci' => $values['datevalidation'],
								'decision_ci' => 'V',
								'avenant_id' => $passagecov58[$this->alias]['avenant_id']
							)
						);

					}

					$this->Dossiercov58->Personne->Contratinsertion->create( $contratinsertion ) && $success;
					$success = $this->Dossiercov58->Personne->Contratinsertion->save( null, array( 'atomic' => false ) ) && $success;
				}
			}


			return $success;
		}

		/**
		*
		*/

		public function qdProcesVerbal() {
			$modele = $this->alias;
			$modeleDecisions = 'Decisionpropocontratinsertioncov58';

			return array(
				'fields' => array(
					"{$modele}.id",
					"{$modele}.dossiercov58_id",
					"{$modele}.structurereferente_id",
					"{$modele}.referent_id",
					"{$modele}.datedemande",
					"{$modele}.num_contrat",
					"{$modele}.dd_ci",
					"{$modele}.duree_engag",
					"{$modele}.df_ci",
					"{$modele}.forme_ci",
					"{$modele}.avisraison_ci",
					"{$modele}.rg_ci",
					"{$modele}.datevalidation",
					"{$modele}.commentaire",
					"{$modele}.decisioncov",
					"{$modele}.avenant_id",
					"{$modeleDecisions}.id",
					"{$modeleDecisions}.etapecov",
					"{$modeleDecisions}.decisioncov",
					"{$modeleDecisions}.datevalidation",
					"{$modeleDecisions}.commentaire",
					"{$modeleDecisions}.created",
					"{$modeleDecisions}.modified",
					"{$modeleDecisions}.passagecov58_id"
				),
				'joins' => array(
					array(
						'table'      => Inflector::tableize( $modele ),
						'alias'      => $modele,
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( "{$modele}.dossiercov58_id = Dossiercov58.id" ),
					),
					array(
						'table'      => Inflector::tableize( $modeleDecisions ),
						'alias'      => $modeleDecisions,
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							"{$modeleDecisions}.passagecov58_id = Passagecov58.id",
							"{$modeleDecisions}.etapecov" => 'finalise'
						),
					),
				)
			);
		}

		/**
		*
		*/

		public function getPdfDecision( $dossiercov58_id ) {
			///INFO : pour le moment aucun courrier donc à faire dès qu'on en aura
			return false;
		}

		/**
		 * Retourne un querydata permettant de trouver les propositions de CER
		 * en cours de traitement par une COV pour un allocataire donné.
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function qdEnCours( $personne_id ) {
			$sqDernierPassagecov58 = $this->Dossiercov58->Passagecov58->sqDernier();

			$querydata = array(
				'fields' => array(
					'Propocontratinsertioncov58.id',
					'Propocontratinsertioncov58.dossiercov58_id',
					'Propocontratinsertioncov58.forme_ci',
					'Propocontratinsertioncov58.num_contrat',
					'Propocontratinsertioncov58.dd_ci',
					'Propocontratinsertioncov58.df_ci',
					'Propocontratinsertioncov58.avenant_id',
					'Dossiercov58.personne_id',
					'Passagecov58.etatdossiercov',
					'Personne.id',
					'Personne.nom',
					'Personne.prenom',
					'Decisionpropocontratinsertioncov58.decisioncov'
				),
				'conditions' => array(
					'Dossiercov58.personne_id' => $personne_id,
					'Themecov58.name' => 'proposcontratsinsertioncovs58',
					'OR' => array(
						'Passagecov58.etatdossiercov NOT' => array( 'traite', 'annule' ),
						'Passagecov58.etatdossiercov IS NULL'
					),
					array(
						'OR' => array(
							"Passagecov58.id IN ( {$sqDernierPassagecov58} )",
							'Passagecov58.etatdossiercov IS NULL'
						),
					),
				),
				'joins' => array(
					$this->join( 'Dossiercov58', array( 'type' => 'INNER' ) ),
					$this->Dossiercov58->join( 'Passagecov58', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossiercov58->Passagecov58->join( 'Decisionpropocontratinsertioncov58', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossiercov58->join( 'Themecov58', array( 'type' => 'INNER' ) ),
					$this->Dossiercov58->join( 'Personne', array( 'type' => 'INNER' ) )
				),
				'contain' => false,
				'order' => array( 'Propocontratinsertioncov58.df_ci DESC' )
			);

			return $querydata;
		}
	}
?>