<?php
	/**
	 * Code source de la classe WebrsaCohortePersonneReferentAffectation93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe WebrsaCohortePersonneReferentAffectation93 ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePersonneReferentAffectation93 extends AbstractWebrsaCohorte
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohortePersonneReferentAffectation93';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Allocataire', 'Personne' );

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Dossier.id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'PersonneReferent.id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'PersonneReferent.personne_id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'PersonneReferent.dddesignation' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'PersonneReferent.active' => array( 'type' => 'radio', 'fieldset' => false, 'legend' => false, 'div' => false ),
			'PersonneReferent.referent_id' => array( 'type' => 'select', 'label' => '', 'empty' => true, 'required' => false ),
		);

		/**
		 * Valeurs par défaut pour le préremplissage des champs du formulaire de cohorte
		 * array(
		 *		'Mymodel' => array( 'Myfield' => 'MyValue' ) )
		 * )
		 *
		 * @var array
		 */
		public $defaultValues = array();

		public $vfPersonneSituation = '(
			CASE
				WHEN ( "PersonneReferent"."id" IS NULL AND "Contratinsertion"."id" IS NULL ) THEN 1
				WHEN ( "PersonneReferent"."id" IS NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'E\' AND "Cer93"."positioncer" = \'00enregistre\' ) THEN 2
				WHEN ( "PersonneReferent"."id" IS NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'E\' AND "Cer93"."positioncer" = \'01signe\' ) THEN 3
				WHEN ( "PersonneReferent"."id" IS NOT NULL AND "Contratinsertion"."id" IS NULL ) THEN 4
				WHEN ( "PersonneReferent"."id" IS NOT NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'E\' AND "Cer93"."positioncer" = \'00enregistre\' ) THEN 5
				WHEN ( "PersonneReferent"."id" IS NOT NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'E\' AND "Cer93"."positioncer" = \'01signe\' ) THEN 6
				WHEN ( "PersonneReferent"."id" IS NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'V\' AND "Contratinsertion"."df_ci" <= NOW() ) THEN 7
				WHEN ( "PersonneReferent"."id" IS NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'V\' AND "Contratinsertion"."df_ci" > NOW() ) THEN 8
				WHEN ( "PersonneReferent"."id" IS NOT NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'V\' AND "Contratinsertion"."df_ci" <= NOW() ) THEN 9
				WHEN ( "PersonneReferent"."id" IS NOT NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'V\' AND "Contratinsertion"."df_ci" > NOW() ) THEN 10
				WHEN ( "PersonneReferent"."id" IS NOT NULL AND "Contratinsertion"."id" IS NOT NULL AND "Contratinsertion"."decision_ci" = \'R\' AND "Cer93"."positioncer" = \'99rejete\' ) THEN 11
				ELSE 12
			END
		)';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesComparison',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Règles de validation de la cohorte.
		 *
		 * @var array
		 */
		public $validate = array(
			'active' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire',
					'required' => true
				)
			),
			'referent_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'active', true, array( '1' ) ),
					'message' => 'Champ obligatoire'
				)
			),
		);

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array(), $baseModelName = 'Personne', $forceBeneficiaire = true ) {
			$types += array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Contratinsertion' => 'LEFT OUTER',
				'Orientstruct' => 'INNER',
				'PersonneReferent' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Prestation' => 'INNER',
				'Cer93' => 'LEFT OUTER',
				'Adressefoyer' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Dossier' => 'INNER',
				'Adresse' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Orientstructpcd' => 'LEFT OUTER',
				'Structurereferentepcd' => 'LEFT OUTER',
				'Referent' => 'LEFT OUTER',
			);

			// Ajout de champs virtuels supplémentaires
			$this->Personne->virtualFields['situation'] = $this->vfPersonneSituation;
			$this->Personne->virtualFields['has_dsp'] = 'EXISTS( SELECT "dsps"."id" FROM "dsps" WHERE "dsps"."personne_id" = "Personne"."id" LIMIT 1 )';

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, $baseModelName, $forceBeneficiaire );

				$replacements = array(
					'Orientstruct' => 'Orientstructpcd',
					'Structurereferente' => 'Structurereferentepcd'
				);

				// 1. Ajout des champs supplémentaires
				$sqDspId = 'SELECT dsps.id FROM dsps WHERE dsps.personne_id = "Personne"."id" LIMIT 1';
				$sqDspExists = "( {$sqDspId} ) IS NOT NULL";

				$query['fields'] = array_merge(
					$query['fields'],
					array(
						'Dossier.id',
						'Personne.id',
						'PersonneReferent.id',
						'PersonneReferent.personne_id',
						'PersonneReferent.dddesignation',
						'PersonneReferent.referent_id',
						'Referent.structurereferente_id',
						'Dsp.exists' => "( {$sqDspExists} ) AS \"Dsp__exists\"",
						'Contratinsertion.interne' => '( "Contratinsertion"."structurereferente_id" = "Referent"."structurereferente_id" ) AS "Contratinsertion__interne"',
						'Personne.situation' => $this->Personne->sqVirtualField( 'situation', true ),
						'Personne.has_dsp' => $this->Personne->sqVirtualField( 'has_dsp', true ),
						'Orientstruct.structurereferente_id',
					),
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Personne->Contratinsertion,
							$this->Personne->Contratinsertion->Cer93,
							$this->Personne->Orientstruct,
							$this->Personne->PersonneReferent->Referent
						)
					),
					array_words_replace(
						ConfigurableQueryFields::getModelsFields(
							array(
								$this->Personne->Orientstruct,
								$this->Personne->Orientstruct->Structurereferente
							)
						),
						$replacements
					)
				);

				// 2. Jointures
				$sqDernierPersonneReferent = $this->Personne->PersonneReferent->sqDerniere( 'Personne.id', false );
				$sqDerniereOrientstruct = $this->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere();
				$sqOrientstructpcd = $this->Personne->sqLatest(
					'Orientstruct',
					'date_valid',
					array(
						'Orientstruct.statut_orient' => 'Orienté',
						'Orientstruct.date_valid IS NOT NULL',
						"Orientstruct.id NOT IN ( {$sqDerniereOrientstruct} )",
					),
					false
				);
				$conditionDernierContratinsertion = $this->Personne->sqLatest( 'Contratinsertion', 'created', array(), true );

				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Personne->join(
							'Contratinsertion',
							array(
								'type' => $types['Contratinsertion'],
								'conditions' => $conditionDernierContratinsertion
							)
						),
						$this->Personne->Contratinsertion->join( 'Cer93', array( 'type' => $types['Cer93'] ) ),
						$this->Personne->join(
							'Orientstruct',
							array(
								'type' => $types['Orientstruct'],
								'conditions' => "Orientstruct.id IN ( {$sqDerniereOrientstruct} )"
							)
						),
						array_words_replace(
							$this->Personne->join(
								'Orientstruct',
								array(
									'type' => $types['Orientstructpcd'],
									'conditions' => "Orientstruct.id IN ( {$sqOrientstructpcd} )"
								)
							),
							$replacements
						),
						array_words_replace(
							$this->Personne->Orientstruct->join(
								'Structurereferente',
								array( 'type' => $types['Structurereferentepcd'] )
							),
							$replacements
						),
						$this->Personne->PersonneReferent->join( 'Referent', array( 'type' => $types['Referent'] ) )
					)
				);

				// 3. Conditions
				$query['conditions'][] = array(
					'OR' => array(
						'Contratinsertion.id IS NULL',
						// Les allocataires transférés possédant un CER en cours de validation ou de validité
						array(
							'Contratinsertion.id IS NOT NULL',
							'Orientstructpcd.id IS NOT NULL'
						),
						array(
							'Contratinsertion.id IS NOT NULL',
							'Contratinsertion.decision_ci' => 'V',
							'Contratinsertion.df_ci <=' => date( 'Y-m-d', strtotime( Configure::read( 'Cohortescers93.saisie.periodeRenouvellement' ) ) )
						),
						array(
							'Contratinsertion.id IS NOT NULL',
							'Contratinsertion.decision_ci' => 'E',
							'Cer93.positioncer' => array( '00enregistre', '01signe' ),
						),
						array(
							'Contratinsertion.id IS NOT NULL',
							'Contratinsertion.decision_ci' => 'R',
							'Cer93.positioncer' => '99rejete'
						),
					)
				);

				// 4. Tri par défaut
				$query['order'] = array(
					'Personne.situation' => 'ASC',
					'Orientstruct.date_valid ASC',
					'Personne.nom ASC',
					'Personne.prenom ASC',
				);

				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			$query = $this->Allocataire->searchConditions( $query, $search );

			// Filtre par affectation
			$filtrer = (string)Hash::get( $search, 'Referent.filtrer' );
			if( $filtrer === '1' ) {
				// Filtre par référent désigné / non désigné
				$designe = (string)Hash::get( $search, 'Referent.designe' );
				if( $designe !== null ) {
					if( $designe === '1' ) {
						$query['conditions'][] = 'PersonneReferent.referent_id IS NOT NULL';
					}
					else if( $designe === '0' ) {
						$query['conditions'][] = 'PersonneReferent.referent_id IS NULL';
					}
				}

				// Choix du référent affecté ?
				$referent_id = suffix( Hash::get( $search, 'Referent.id' ) );
				if( !empty( $referent_id ) ) {
					$query['conditions'][] = array( 'PersonneReferent.referent_id' => $referent_id );
				}

				$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'PersonneReferent.dddesignation' );
			}

			// Filtre sur la situation de l'allocataire
			$situation = (array)Hash::get( $search, 'Personne.situation' );
			if( !empty( $situation ) ) {
				$vfSituation = $this->Personne->sqVirtualField( 'situation', false );
				$query['conditions'][] = array(
					 "{$vfSituation} IN ( '".implode( "', '", (array)$situation )."' )"
				);
			}

			// Dossier transféré ?
			$transfere = (string)Hash::get( $search, 'Dossier.transfere' );
			if( $transfere !== '' ) {
				if( !empty( $transfere ) ) {
					$query['conditions']['Orientstruct.origine'] = 'demenagement';
				}
				else {
					$query['conditions'][] = array(
						'NOT' => array( 'Orientstruct.origine' => 'demenagement' ),
					);
				}
			}

			// Filtrer par affectation précédente: référent précédent
			$referentpcd_id = (string)Sanitize::clean( suffix( (string)Hash::get( $search, 'PersonneReferentPcd.referent_id' ) ) );
			if( '' !== $referentpcd_id ) {
				$subQuery = array(
					'alias' => 'personnes_referents',
					'fields' => array( 'personnes_referents.referent_id' ),
					'contain' => false,
					'conditions' => array(
						'personnes_referents.personne_id = Personne.id'
					),
					'order' => array(
						'personnes_referents.dddesignation DESC',
						'personnes_referents.id DESC',
					),
					'limit' => 1
				);
				$sql = $this->Personne->PersonneReferent->sq( $subQuery );
				$query['conditions'][] = array(
					'PersonneReferent.id IS NULL',
					"( {$sql} ) = '{$referentpcd_id}'"
				);
			}

			// Filtrer par affectation précédente: date de fin de désignation du référent précédent
			$dfdesignation_pcd = Hash::get( $search, 'PersonneReferentPcd.dfdesignation' );
			if( '1' === $dfdesignation_pcd ) {
				$from = Hash::get( $search, 'PersonneReferentPcd.dfdesignation_from' );
				$to = Hash::get( $search, 'PersonneReferentPcd.dfdesignation_to' );
				$subQuery = array(
					'alias' => 'personnes_referents',
					'fields' => array( 'personnes_referents.dfdesignation' ),
					'contain' => false,
					'conditions' => array(
						'personnes_referents.personne_id = Personne.id'
					),
					'order' => array(
						'personnes_referents.dddesignation DESC',
						'personnes_referents.id DESC',
					),
					'limit' => 1
				);
				$sql = $this->Personne->PersonneReferent->sq( $subQuery );
				$query['conditions'][] = array(
					'PersonneReferent.id IS NULL',
					"( {$sql} ) BETWEEN '".date_cakephp_to_sql( $from )."' AND '".date_cakephp_to_sql( $to )."'"
				);
			}

			// Filtrer par date de validation de l'orientation
			$date_valid_Orientstruct = Hash::get( $search, 'Orientstruct.date_valid' );
			if( '1' === $date_valid_Orientstruct ) {
				$from = Hash::get( $search, 'Orientstruct.date_valid_from' );
				$to = Hash::get( $search, 'Orientstruct.date_valid_to' );
				$query['conditions'][] = "Orientstruct.date_valid BETWEEN '".date_cakephp_to_sql( $from )."' AND '".date_cakephp_to_sql( $to )."'";
			}

			return $query;
		}

		/**
		 * Préremplissage des champs du formulaire de cohorte.
		 *
		 * @param array $results
		 * @param array $params
		 * @param array $options
		 * @return array
		 */
		public function prepareFormDataCohorte( array $results, array $params = array(), array &$options = array() ) {
			$data = array();

			foreach( $results as $index => $result ) {
				$structurereferente_id = Hash::get( $result, 'Referent.structurereferente_id' );
				$referent_id = Hash::get( $result, 'PersonneReferent.referent_id' );

				$data[$index] = array(
					'PersonneReferent' => array(
						'id' => $result['PersonneReferent']['id'],
						'dossier_id' => $result['Dossier']['id'],
						'personne_id' => $result['Personne']['id'],
						'active' => '0',
						'referent_id' => !empty( $structurereferente_id ) ? "{$structurereferente_id}_{$referent_id}" : null
					)
				);
			}

			return $data;
		}

		/**
		 * Enregistrement du formulaire de cohorte.
		 * On ne clôture pas le référent du parcours précédent.
		 *
		 * @param array $data
		 * @param array $params
		 * @param integer $user_id
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$records = array();

			foreach( $data as $key => $values ) {
				$active = Hash::get( $values, 'PersonneReferent.active' );
				if( $active ) {
					$referent_id = Hash::get( $values, 'PersonneReferent.referent_id' );
					$records[$key] = array(
						'id' => Hash::get( $values, 'PersonneReferent.id' ),
						'active' => $active,
						'personne_id' => Hash::get( $values, 'PersonneReferent.personne_id' ),
						'dddesignation' => date( 'Y-m-d' ),
						'structurereferente_id' => prefix( $referent_id ),
						'referent_id' => suffix( $referent_id )
					);
				}
			}

			// Tentative d'enregistrement
			$validate = $this->Personne->PersonneReferent->validate;
			$this->Personne->PersonneReferent->validate = $this->validate;

			$success = !empty( $records )
				&& $this->saveResultAsBool(
					$this->Personne->PersonneReferent->saveAll( $records, array( 'validate' => 'first', 'atomic' => false ) )
				);
			$this->Personne->PersonneReferent->validate = $validate;

			return $success;
		}
	}
?>