<?php
	/**
	 * Code source de la classe Propononorientationprocov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractThematiquecov58', 'Model/Abstractclass' );

	/**
	 * La classe Propononorientationprocov58 ...
	 *
	 * @package app.Model
	 */
	class Propononorientationprocov58 extends AbstractThematiquecov58
	{
		public $name = 'Propononorientationprocov58';

		public $actsAs = array(
			'Containable',
			'Dependencies',
			'Gedooo.Gedooo',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'structurereferente_id' => array(
				'choixStructure' => array(
					'rule' => array( 'choixStructure', 'statut_orient' ),
					'message' => 'Champ obligatoire'
				),
				'dependentForeignKeys' => array(
					'rule' => array( 'dependentForeignKeys', 'Structurereferente', 'Typeorient' ),
					'message' => 'La structure référente ne correspond pas au type d\'orientation',
				),
			),
			'date_propo' => array(
				'date' => array(
					'rule' => array( 'date' ),
					'message' => 'Veuillez entrer une date valide'
				)
			),
			'date_valid' => array(
				'date' => array(
					'rule' => array( 'date' ),
					'message' => 'Veuillez entrer une date valide'
				)
			),
			'referent_id' => array(
				'dependentForeignKeys' => array(
					'rule' => array( 'dependentForeignKeys', 'Referent', 'Structurereferente' ),
					'message' => 'Le référent n\'appartient pas à la structure référente',
				)
			)
		);

		public $belongsTo = array(
			'Typeorient' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'typeorient_id',
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
			'Covtypeorient' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'covtypeorient_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Covstructurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'covstructurereferente_id',
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
			'Dossiercov58' => array(
				'className' => 'Dossiercov58',
				'foreignKey' => 'dossiercov58_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
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
				'Orientstruct' => array(
					'Typeorient',
					'Structurereferente',
					'Referent'
				)
			);

			$result['contain']['Passagecov58'][$modeleDecision] = array(
				'Typeorient',
				'Structurereferente',
				'order' => array( 'etapecov DESC' )
			);

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
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Referent.qual',
				'Referent.nom',
				'Referent.prenom'
			);
		}

		/**
		 *
		 * @deprecated
		 */
		public function getJoins() {
			return array(
				array(
					'table' => 'proposnonorientationsproscovs58',
					'alias' => $this->alias,
					'type' => 'INNER',
					'conditions' => array(
						'Dossiercov58.id = Propononorientationprocov58.dossiercov58_id'
					)
				),
				array(
					'table' => 'structuresreferentes',
					'alias' => 'Structurereferente',
					'type' => 'INNER',
					'conditions' => array(
						'Propononorientationprocov58.structurereferente_id = Structurereferente.id'
					)
				),
				array(
					'table' => 'typesorients',
					'alias' => 'Typeorient',
					'type' => 'INNER',
					'conditions' => array(
						'Propononorientationprocov58.typeorient_id = Typeorient.id'
					)
				),
				array(
					'table' => 'referents',
					'alias' => 'Referent',
					'type' => 'LEFT OUTER',
					'conditions' => array(
						'Propononorientationprocov58.referent_id = Referent.id'
					)
				)
			);
		}

		/**
		 *
		 * @param array $data
		 * @return boolean
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

					if( in_array( $values['decisioncov'], array( 'valide', 'refuse' ) ) ) {
						$rgorient = $this->Dossiercov58->Personne->Orientstruct->WebrsaOrientstruct->rgorientMax( $passagecov58['Dossiercov58']['personne_id'] ) + 1;
						$origine = ( $rgorient > 1 ? 'reorientation' : 'manuelle' );

						if( $values['decisioncov'] == 'valide' ){
							$data[$modelDecisionName][$key]['typeorient_id'] = $passagecov58[$this->alias]['typeorient_id'];
							$data[$modelDecisionName][$key]['structurereferente_id'] = $passagecov58[$this->alias]['structurereferente_id'];
							$data[$modelDecisionName][$key]['referent_id'] = $passagecov58[$this->alias]['referent_id'];

							list($datevalidation, $heure) = explode(' ', $passagecov58['Cov58']['datecommission']);

							//Sauvegarde des décisions
							$this->Dossiercov58->Passagecov58->{$modelDecisionName}->create( array( $modelDecisionName => $data[$modelDecisionName][$key] ) );
							$success = $this->Dossiercov58->Passagecov58->{$modelDecisionName}->save( null, array( 'atomic' => false ) ) && $success;

							$orientstruct = array(
								'Orientstruct' => array(
									'personne_id' => $passagecov58['Dossiercov58']['personne_id'],
									'typeorient_id' => $passagecov58[$this->alias]['typeorient_id'],
									'structurereferente_id' => $passagecov58[$this->alias]['structurereferente_id'],
									'referent_id' => $passagecov58[$this->alias]['referent_id'],
									'date_propo' => $passagecov58[$this->alias]['datedemande'],
									'date_valid' => $datevalidation,
									'rgorient' => $rgorient,
									'statut_orient' => 'Orienté',
									'etatorient' => 'decision',
									'origine' => $origine,
									'user_id' => ( isset( $passagecov58[$this->alias]['user_id'] ) ) ? $passagecov58[$this->alias]['user_id'] : null
								)
							);

							$success = $this->Dossiercov58->Personne->PersonneReferent->changeReferentParcours(
								$passagecov58['Dossiercov58']['personne_id'],
								$passagecov58[$this->alias]['referent_id'],
								array(
									'PersonneReferent' => array(
										'personne_id' => $passagecov58['Dossiercov58']['personne_id'],
										'referent_id' => $passagecov58[$this->alias]['referent_id'],
										'dddesignation' => $datevalidation,
										'structurereferente_id' => $passagecov58[$this->alias]['structurereferente_id'],
										'user_id' => ( isset( $passagecov58[$this->alias]['user_id'] ) ) ? $passagecov58[$this->alias]['user_id'] : null
									)
								)
							) && $success;

							//Validation par la COV donc on déverse le dossier dans la corbeille EP
							$dossierep = array(
								'Dossierep' => array(
									'personne_id' => $passagecov58['Dossiercov58']['personne_id'],
									'themeep' => 'nonorientationsproseps58'
								)
							);
							$this->Dossiercov58->Passagecov58->Decisionpropononorientationprocov58->Nonorientationproep58->Dossierep->create( $dossierep );
							$success = $this->Dossiercov58->Passagecov58->Decisionpropononorientationprocov58->Nonorientationproep58->Dossierep->save( null, array( 'atomic' => false ) ) && $success;

							$nonorientationproep = array(
								'Nonorientationproep58' => array(
									'dossierep_id' => $this->Dossiercov58->Passagecov58->Decisionpropononorientationprocov58->Nonorientationproep58->Dossierep->id,
									'orientstruct_id' => $passagecov58[$this->alias]['orientstruct_id'],
									'user_id' => $passagecov58['Passagecov58']['user_id'],
									'decisionpropononorientationprocov58_id' => $this->Dossiercov58->Passagecov58->{$modelDecisionName}->id
								)
							);
							$this->Dossiercov58->Passagecov58->Decisionpropononorientationprocov58->Nonorientationproep58->create( $nonorientationproep );
							$success = $this->Dossiercov58->Passagecov58->Decisionpropononorientationprocov58->Nonorientationproep58->save( null, array( 'atomic' => false ) ) && $success;


						}
						else if( $values['decisioncov'] == 'refuse' ) {
							$typeorient_id = suffix( $values['typeorient_id'] );
							$structurereferente_id = suffix( $values['structurereferente_id'] );
							$referent_id = suffix( $values['referent_id'] );

							$data[$modelDecisionName][$key]['typeorient_id'] = $typeorient_id;
							$data[$modelDecisionName][$key]['structurereferente_id'] = $structurereferente_id;
							$data[$modelDecisionName][$key]['referent_id'] = $referent_id;

							$data[$modelDecisionName][$key]['datevalidation'] = $passagecov58['Cov58']['datecommission'];
							list($datevalidation, $heure) = explode(' ', $passagecov58['Cov58']['datecommission']);

							//Sauvegarde des décisions
							$this->Dossiercov58->Passagecov58->{$modelDecisionName}->create( array( $modelDecisionName => $data[$modelDecisionName][$key] ) );
							$success = $this->Dossiercov58->Passagecov58->{$modelDecisionName}->save( null, array( 'atomic' => false ) ) && $success;

							$orientstruct = array(
								'Orientstruct' => array(
									'personne_id' => $passagecov58['Dossiercov58']['personne_id'],
									'typeorient_id' => $data[$modelDecisionName][$key]['typeorient_id'],
									'structurereferente_id' => $data[$modelDecisionName][$key]['structurereferente_id'],
									'referent_id' => $data[$modelDecisionName][$key]['referent_id'],
									'date_propo' => $passagecov58['Propononorientationprocov58']['datedemande'],
									'date_valid' => $datevalidation,
									'rgorient' => $rgorient,
									'statut_orient' => 'Orienté',
									'etatorient' => 'decision',
									'origine' => $origine,
									'user_id' => ( isset( $passagecov58['Propononorientationprocov58']['user_id'] ) ) ? $passagecov58['Propononorientationprocov58']['user_id'] : null
								)
							);

							$success = $this->Dossiercov58->Personne->PersonneReferent->changeReferentParcours(
								$passagecov58['Dossiercov58']['personne_id'],
								$data[$modelDecisionName][$key]['referent_id'],
								array(
									'PersonneReferent' => array(
										'personne_id' => $passagecov58['Dossiercov58']['personne_id'],
										'referent_id' => $data[$modelDecisionName][$key]['referent_id'],
										'dddesignation' => $datevalidation,
										'structurereferente_id' => $data[$modelDecisionName][$key]['structurereferente_id'],
										'user_id' =>  ( isset( $passagecov58['Propononorientationprocov58']['user_id'] ) ) ? $passagecov58['Propononorientationprocov58']['user_id'] : null
									)
								)
							) && $success;
						}

						$this->Dossiercov58->Personne->Orientstruct->create( $orientstruct );
						$success = $this->Dossiercov58->Personne->Orientstruct->save( null, array( 'atomic' => false ) ) && $success;

						// Mise à jour de l'enregistrement de la thématique avec l'id du nouveau CER
						$success = $success && $this->updateAllUnBound(
							array( "\"{$this->alias}\".\"nvorientstruct_id\"" => $this->Dossiercov58->Personne->Orientstruct->id ),
							array( "\"{$this->alias}\".\"id\"" => $data[$this->alias][$key] )
						);
					}
					else{
						//Sauvegarde des décisions
						$this->Dossiercov58->Passagecov58->{$modelDecisionName}->create( array( $modelDecisionName => $data[$modelDecisionName][$key] ) );
						$success = $this->Dossiercov58->Passagecov58->{$modelDecisionName}->save( null, array( 'atomic' => false ) ) && $success;
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
				}// Fin du foreach
			}

			$success = $this->Dossiercov58->Passagecov58->{$modelDecisionName}->saveAll( Set::extract( $data, '/'.$modelDecisionName ), array( 'atomic' => false, 'validate' => 'only' ) ) && $success;

			return $success;
		}


		/**
		*
		*/

		public function qdProcesVerbal() {
			$modele = 'Propononorientationprocov58';
			$modeleDecisions = 'Decisionpropononorientationprocov58';

			$result = array(
				'fields' => array(
					"{$modele}.id",
					"{$modele}.dossiercov58_id",
					"{$modele}.orientstruct_id",
					"{$modele}.typeorient_id",
					"{$modele}.structurereferente_id",
					"{$modele}.referent_id",
					"{$modele}.datedemande",
					"{$modele}.datevalidation",
					"{$modeleDecisions}.id",
					"{$modeleDecisions}.etapecov",
					"{$modeleDecisions}.decisioncov",
					"{$modeleDecisions}.typeorient_id",
					"{$modeleDecisions}.structurereferente_id",
					"{$modeleDecisions}.referent_id",
					"{$modeleDecisions}.commentaire",
					"{$modeleDecisions}.created",
					"{$modeleDecisions}.modified",
					"{$modeleDecisions}.passagecov58_id",
					"{$modeleDecisions}.datevalidation",
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

			return $result;
		}

		/**
		 * Retourne une partie de querydata propre à la thématique et nécessaire
		 * à l'imprssion de l'odre du jour.
		 *
		 * @return array
		 */
		public function qdOrdreDuJour() {
			$result = parent::qdOrdreDuJour();

			$result['fields'][] = 'Typeorient.lib_type_orient';
			$result['fields'][] = 'Structurereferente.lib_struc';
			$result['joins'][] = $this->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) );
			$result['joins'][] = $this->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) );

			$result = array_words_replace(
				$result,
				array(
					'Typeorient' => "{$this->alias}typeorient",
					'Structurereferente' => "{$this->alias}structurereferente"
				)
			);

			return $result;
		}

		/**
		 * Retourne un querydata permettant de trouver les propositions d'orientations en cours de
		 * traitement par une COV pour un allocataire donné.
		 *
		 * @todo A factoriser avec les autres thématiques (Propoorientsocialecov58, ...).
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function qdEnCours( $personne_id ) {
			$sqDernierPassagecov58 = $this->Dossiercov58->Passagecov58->sqDernier();

			return array(
				'fields' => array(
					"{$this->alias}.id",
					"{$this->alias}.dossiercov58_id",
					"Dossiercov58.created",
					'Dossiercov58.personne_id',
					'Dossiercov58.themecov58',
					'Passagecov58.etatdossiercov',
					'Personne.id',
					'Personne.nom',
					'Personne.prenom'
				),
				'conditions' => array(
					'Dossiercov58.personne_id' => $personne_id,
					'Themecov58.name' => Inflector::tableize( $this->alias ),
					array(
						'OR' => array(
							'Passagecov58.etatdossiercov NOT' => array( 'traite', 'annule' ),
							'Passagecov58.etatdossiercov IS NULL'
						),
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
					$this->Dossiercov58->join( 'Themecov58', array( 'type' => 'INNER' ) ),
					$this->Dossiercov58->join( 'Passagecov58', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossiercov58->join( 'Personne', array( 'type' => 'INNER' ) )
				),
				'contain' => false,
			);
		}
	}
?>