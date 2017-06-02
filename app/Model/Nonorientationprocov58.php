<?php
	/**
	 * Code source de la classe Nonorientationprocov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractThematiquecov58', 'Model/Abstractclass' );

	/**
	 * La classe Nonorientationprocov58 ...
	 *
	 * @package app.Model
	 */
	class Nonorientationprocov58 extends AbstractThematiquecov58
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Nonorientationprocov58';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Conditionnable',
			'Dependencies',
			'Gedooo.Gedooo',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Chemin relatif pour les modèles de documents .odt utilisés lors des
		 * impressions. Utiliser %s pour remplacer par l'alias.
		 *
		 * @var array
		 */
		public $modelesOdt = array(
			'Cov58/%s_decision_reorientation.odt',
			'Cov58/%s_decision_maintienref.odt',
//			'Cov58/%s_decision_annule.odt',
//			'Cov58/%s_decision_reporte.odt',
		);

		/**
		 * Règles de validation par défaut du modèle.
		 *
		 * @var array
		 */
		public $validate = array(
			'referent_id' => array(
				'dependentForeignKeys' => array(
					'rule' => array( 'dependentForeignKeys', 'Referent', 'Structurereferente' ),
					'message' => 'Le référent n\'appartient pas à la structure référente',
				),
			)
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Dossiercov58' => array(
				'className' => 'Dossiercov58',
				'foreignKey' => 'dossiercov58_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Nvorientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'nvorientstruct_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Orientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'orientstruct_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			)
		);

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
				'Referent',
				'order' => array( 'etapecov DESC' )
			);

			return $result;
		}

		/**
		 * Tentative de sauvegarde des décisions de la thématique.
		 *
		 * Une nouvelle orientation sera crée lorsque la décision est
		 * "Réorientation" ou "Maintien dans le social"; le référent du parcours
		 * sera clôturé s'il existe et un nouveau référent du parcours sera désigné
		 * s'il a été choisi.
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

					if( in_array( $values['decisioncov'], array( 'reorientation', 'maintienref' ) ) ) {
						list( $date_propo, $heure_propo ) = explode( ' ', Hash::get( $passagecov58, 'Nonorientationprocov58.created' ) );
						list( $date_valid, $heure_valid ) = explode( ' ', Hash::get( $passagecov58, 'Cov58.datecommission' ) );

						$rgorient = $this->Dossiercov58->Personne->Orientstruct->WebrsaOrientstruct->rgorientMax( $passagecov58['Dossiercov58']['personne_id'] ) + 1;
						$origine = ( $rgorient > 1 ? 'reorientation' : 'manuelle' );

						$orientstruct = array(
							'Orientstruct' => array(
								'personne_id' => Hash::get( $passagecov58, 'Dossiercov58.personne_id' ),
								'typeorient_id' => Hash::get( $values, 'typeorient_id' ),
								'structurereferente_id' => suffix( Hash::get( $values, 'structurereferente_id' ) ),
								'referent_id' => suffix( Hash::get( $values, 'referent_id' ) ),
								'date_propo' => $date_propo,
								'date_valid' => $date_valid,
								'statut_orient' => 'Orienté',
								'rgorient' => $rgorient,
								'origine' => $origine,
								'etatorient' => 'decision',
								'user_id' => Hash::get( $passagecov58, 'Nonorientationprocov58.user_id' )
							)
						);

						$this->Orientstruct->create( $orientstruct );
						$success = $this->Orientstruct->save( null, array( 'atomic' => false ) ) && $success;

						// Mise à jour de l'enregistrement de la thématique avec l'id de la nouvelle orientation
						$success = $success && $this->updateAllUnBound(
							array( "\"{$this->alias}\".\"nvorientstruct_id\"" => $this->Orientstruct->id ),
							array( "\"{$this->alias}\".\"id\"" => Hash::get( $passagecov58, 'Nonorientationprocov58.id' ) )
						);

						$success = $this->Orientstruct->Personne->PersonneReferent->changeReferentParcours(
							Hash::get( $passagecov58, 'Dossiercov58.personne_id' ),
							suffix( Hash::get( $values, 'referent_id' ) ),
							array(
								'PersonneReferent' => array(
									'personne_id' => Hash::get( $passagecov58, 'Dossiercov58.personne_id' ),
									'referent_id' => suffix( Hash::get( $values, 'referent_id' ) ),
									'dddesignation' => $date_valid,
									'structurereferente_id' => suffix( Hash::get( $values, 'structurereferente_id' ) ),
									'user_id' => Hash::get( $passagecov58, 'Nonorientationprocov58.user_id' )
								)
							)
						) && $success;
					}

					// Sauvegarde des décisions
					// FIXME: ben non, c'est en bas
//					$this->Dossiercov58->Passagecov58->{$modelDecisionName}->create( array( $modelDecisionName => $data[$modelDecisionName][$key] ) );
//					$success = $this->Dossiercov58->Passagecov58->{$modelDecisionName}->save( null, array( 'atomic' => false ) ) && $success;

					// Modification etat du dossier passé dans la COV
					if( in_array( $values['decisioncov'], array( 'reorientation', 'maintienref' ) ) ) {
						$this->Dossiercov58->Passagecov58->updateAllUnBound(
							array( 'Passagecov58.etatdossiercov' => '\'traite\'' ),
							array('"Passagecov58"."id"' => $passagecov58['Passagecov58']['id'] )
						);
					}
					else if( $values['decisioncov'] == 'annule' ) {
						$this->Dossiercov58->Passagecov58->updateAllUnBound(
							array( 'Passagecov58.etatdossiercov' => '\'annule\'' ),
							array('"Passagecov58"."id"' => $passagecov58['Passagecov58']['id'] )
						);
					}
					else if( $values['decisioncov'] == 'reporte' ) {
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
		 * Retourne un morceau de querydata propre à la thématique utilisée pour
		 * l'impression du procès-verbal de la COV.
		 *
		 * Sucharge de la méthode de la classe parente afin d'ajouter les champs
		 * et les jointures depuis le modèle de décision vers d'autres modèles
		 * propres à la thématique.
		 *
		 * @return array
		 */
		public function qdProcesVerbal() {
			$modeleDecision = 'Decision'.Inflector::underscore( $this->alias );
			$query = parent::qdProcesVerbal();

			$query['fields'] = array_merge(
				$query['fields'],
				$this->Nvorientstruct->fields(),
				$this->Nvorientstruct->Typeorient->fields(),
				$this->Nvorientstruct->Structurereferente->fields(),
				$this->Nvorientstruct->Referent->fields()
			);

			$query['joins'][] = $this->join( 'Nvorientstruct', array( 'type' => 'LEFT OUTER' ) );
			$query['joins'][] = $this->Nvorientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) );
			$query['joins'][] = $this->Nvorientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) );
			$query['joins'][] = $this->Nvorientstruct->join( 'Referent', array( 'type' => 'LEFT OUTER' ) );

			$query = array_words_replace(
				$query,
				array(
					'Typeorient' => 'Nvtypeorient',
					'Structurereferente' => 'Nvstructurereferente',
					'Referent' => 'Nvreferent'
				)
			);

			return $query;
		}

		/**
		 * Retourne une partie de querydata propre à la thématique et nécessaire
		 * à l'impression de l'ordre du jour.
		 *
		 * @return array
		 */
		public function qdOrdreDuJour() {
			$query = parent::qdOrdreDuJour();

			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'Orientstruct.date_valid',
					'Orientstruct.rgorient',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					$this->Orientstruct->Referent->sqVirtualField( 'nom_complet', false )." AS \"{$this->alias}Referent__nom_complet\""
				)
			);
			$query['joins'][] = $this->join( 'Orientstruct', array( 'type' => 'LEFT OUTER' ) );
			$query['joins'][] = $this->Orientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) );
			$query['joins'][] = $this->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) );
			$query['joins'][] = $this->Orientstruct->join( 'Referent', array( 'type' => 'LEFT OUTER' ) );

			return $query;
		}

		/**
		 * Retourne le PDF de décision pour la thématique.
		 *
		 * @param integer $passagecov58_id
		 * @return string
		 * @throws NotFoundException
		 */
		public function getPdfDecision( $passagecov58_id ) {
			$query = array(
				'fields' => array_merge(
					$this->Dossiercov58->Passagecov58->fields(),
					$this->Dossiercov58->Passagecov58->Dossiercov58->fields(),
					$this->Dossiercov58->Passagecov58->Decisionnonorientationprocov58->fields(),
					$this->Dossiercov58->Nonorientationprocov58->fields(),
					$this->Dossiercov58->Nonorientationprocov58->Nvorientstruct->fields(),
					$this->Dossiercov58->Nonorientationprocov58->Nvorientstruct->Typeorient->fields(),
					$this->Dossiercov58->Nonorientationprocov58->Nvorientstruct->Structurereferente->fields(),
					$this->Dossiercov58->Nonorientationprocov58->Nvorientstruct->Referent->fields(),
					$this->Dossiercov58->Personne->fields(),
					$this->Dossiercov58->Personne->Foyer->fields(),
					$this->Dossiercov58->Personne->Foyer->Dossier->fields(),
					$this->Dossiercov58->Personne->Foyer->Adressefoyer->fields(),
					$this->Dossiercov58->Personne->Foyer->Adressefoyer->Adresse->fields(),
					$this->Dossiercov58->Nonorientationprocov58->User->fields(),
					$this->Dossiercov58->Nonorientationprocov58->User->Serviceinstructeur->fields(),
					$this->Dossiercov58->Passagecov58->Cov58->fields(),
					$this->Dossiercov58->Passagecov58->Cov58->Sitecov58->fields()
				),
				'conditions' => array(
					'Passagecov58.id' => $passagecov58_id,
					'OR' => array(
						'Adressefoyer.id IS NULL',
						'Adressefoyer.id IN ('.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01( 'Adressefoyer.foyer_id' ).')'
					)
				),
				'joins' => array(
					$this->Dossiercov58->Passagecov58->join( 'Dossiercov58' ),
					$this->Dossiercov58->Passagecov58->join( 'Decisionnonorientationprocov58' ),
					$this->Dossiercov58->join( 'Nonorientationprocov58' ),
					$this->Dossiercov58->Nonorientationprocov58->join( 'Nvorientstruct', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossiercov58->Nonorientationprocov58->Nvorientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossiercov58->Nonorientationprocov58->Nvorientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossiercov58->Nonorientationprocov58->Nvorientstruct->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossiercov58->join( 'Personne' ),
					$this->Dossiercov58->Personne->join( 'Foyer' ),
					$this->Dossiercov58->Personne->Foyer->join( 'Dossier' ),
					$this->Dossiercov58->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossiercov58->Personne->Foyer->Adressefoyer->join( 'Adresse' ),
					$this->Dossiercov58->Nonorientationprocov58->join( 'User' ),
					$this->Dossiercov58->Nonorientationprocov58->User->join( 'Serviceinstructeur' ),
					$this->Dossiercov58->Passagecov58->join( 'Cov58' ),
					$this->Dossiercov58->Passagecov58->Cov58->join( 'Sitecov58' )
				),
				'recursive' => -1
			);

			$query = array_words_replace(
				$query,
				array(
					'Typeorient' => 'Nvtypeorient',
					'Structurereferente' => 'Nvstructurereferente',
					'Referent' => 'Nvreferent'
				)
			);

			$data = $this->Dossiercov58->Passagecov58->find( 'first', $query );
			if( empty( $data ) ) {
				throw new NotFoundException();
			}

			$options = Hash::merge(
				array(
					'Personne' => array( 'qual' => ClassRegistry::init( 'Option' )->qual() )
				),
				$this->enums(),
				$this->Dossiercov58->enums(),
				$this->Dossiercov58->Passagecov58->Decisionnonorientationprocov58->enums()
			);

			$fileName = sprintf( 'Cov58/%s_decision_%s.odt', $this->alias, $data['Decisionnonorientationprocov58']['decisioncov'] );

			return $this->ged(
				$data,
				$fileName,
				false,
				$options
			);
		}
	}
?>