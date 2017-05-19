<?php
	/**
	 * Code source de la classe Cohortereferent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Cohortereferent permet de rechercher le allocataires ne possédant pas de référent de parcours
	 * en cours.
	 *
	 * @deprecated since 3.1.0
	 * @package app.Model
	 */
	class Cohortereferent93 extends AppModel
	{
		/**
		 * @var string
		 */
		public $name = 'Cohortereferent';

		/**
		 * @var boolean
		 */
		public $useTable = false;

		/**
		 * @var array
		 */
		public $actsAs = array( 'Conditionnable' );

		/**
		 * Règles de validation pour la cohorte d'affectation d'un référent.
		 *
		 * @var array
		 */
		public $validatePersonneReferent = array(
			'action' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' ),
					'message' => 'Champ obligatoire',
					'required' => true
				)
			),
			'referent_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'action', true, array( 'Valider' ) ),
					'message' => 'Champ obligatoire'
				)
			),
		);

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
		 * Retourne un querydata résultant du traitement du formulaire de recherche des cohortes de référent
		 * du parcours.
		 *
		 * @param integer $structurereferente_id L'id technique de la structure référente pour laquelle on effectue la recherche
		 * @param array $mesCodesInsee La liste des codes INSEE à laquelle est lié l'utilisateur
		 * @param boolean $filtre_zone_geo L'utilisateur est-il limité au niveau des zones géographiques ?
		 * @param array $search Critères du formulaire de recherche
		 * @param mixed $lockedDossiers
		 * @return array
		 */
		public function search( $structurereferente_id, $mesCodesInsee, $filtre_zone_geo, $search, $lockedDossiers ) {
			$Personne = ClassRegistry::init( 'Personne' );

			// INFO: sinon on ne peut pas trier comme on veut
			$Personne->virtualFields['situation'] = $this->vfPersonneSituation;

			$sqDerniereRgadr01 = $Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' );
			$sqDerniereOrientstruct = $Personne->Orientstruct->WebrsaOrientstruct->sqDerniere();

			$sqOrientstructpcd = $Personne->sqLatest(
				'Orientstruct',
				'date_valid',
				array(
					'Orientstruct.statut_orient' => 'Orienté',
					'Orientstruct.date_valid IS NOT NULL',
					"Orientstruct.id NOT IN ( {$sqDerniereOrientstruct} )",
				),
				false
			);

			$sqDernierReferent = $Personne->PersonneReferent->sqDerniere( 'Personne.id', false );
			$sqDernierContratinsertion = $Personne->sqLatest( 'Contratinsertion', 'created', array(), true );

			$sqDspId = 'SELECT dsps.id FROM dsps WHERE dsps.personne_id = "Personne"."id" LIMIT 1';
			$sqDspExists = "( {$sqDspId} ) IS NOT NULL";

			$conditions = array(
				'Prestation.rolepers' => array( 'DEM', 'CJT' ),
				"Adressefoyer.id IN ( {$sqDerniereRgadr01} )",
				"Orientstruct.id IN ( {$sqDerniereOrientstruct} )",
				'Orientstruct.structurereferente_id' => $structurereferente_id,
				$sqDernierContratinsertion,
				array(
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
				)
			);

			if( isset( $search['Referent']['filtrer'] ) && $search['Referent']['filtrer'] == '1' ) {
				// Filtre par référent désigné / non désigné
				if( isset( $search['Referent']['designe'] ) ) {
					if( $search['Referent']['designe'] === '1' ) {
						$conditions[] = 'PersonneReferent.referent_id IS NOT NULL';
					}
					else if( $search['Referent']['designe'] === '0' ) {
						$conditions[] = 'PersonneReferent.referent_id IS NULL';
					}
				}

				// Choix du référent affecté ?
				$referent_id = suffix( Hash::get( $search, 'PersonneReferent.referent_id' ) );
				if( !empty( $referent_id ) ) {
					$conditions['PersonneReferent.referent_id'] = $referent_id;
				}

				$conditions = $this->conditionsDates( $conditions, $search, 'PersonneReferent.dddesignation' );
			}


			// Présence DSP ?
			if( isset( $search['Dsp']['exists'] ) && ( $search['Dsp']['exists'] != '' ) ) {
				if( $search['Dsp']['exists'] ) {
					$conditions[] = "( {$sqDspExists} )";
				}
				else {
					$conditions[] = "( ( {$sqDspId} ) IS NULL )";
				}
			}

			// Présence CER ?
// 			if( isset( $search['Contratinsertion']['exists'] ) && ( $search['Contratinsertion']['exists'] != '' ) ) {
// 				if( $search['Contratinsertion']['exists'] ) {
// 					$conditions[] = "( ( {$sqDernierContratinsertion} ) IS NOT NULL )";
// 				}
// 				else {
// 					$conditions[] = "( ( {$sqDernierContratinsertion} ) IS NULL )";
// 				}
// 			}

			/// Présence ou non d'un CER
			if( isset( $search['Contratinsertion']['exists'] ) && ( $search['Contratinsertion']['exists'] != '' ) ) {
				if( $search['Contratinsertion']['exists'] ) {
					$conditions[] = '( SELECT COUNT(contratsinsertion.id) FROM contratsinsertion WHERE contratsinsertion.personne_id = "Personne"."id" ) > 0';
				}
				else {
					$conditions[] = '( SELECT COUNT(contratsinsertion.id) FROM contratsinsertion WHERE contratsinsertion.personne_id = "Personne"."id" ) = 0';
				}
			}

			$conditions = $this->conditionsAdresse( $conditions, $search, $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsPersonneFoyerDossier( $conditions, $search );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $search );
			$conditions = $this->conditionsDates( $conditions, $search, 'Orientstruct.date_valid' );

			/// Dossiers lockés
			if( !empty( $lockedDossiers ) ) {
				if( is_array( $lockedDossiers ) ) {
					$lockedDossiers = implode( ', ', $lockedDossiers );
				}
				$conditions[] = "NOT {$lockedDossiers}";
			}

			// Filtre sur la situation de l'allocataire
			if( isset( $search['Personne']['situation'] ) && !empty( $search['Personne']['situation'] ) ) { // FIXME traduction
				$vfSituation = $Personne->sqVirtualField( 'situation', false );
				$conditions[] = array(
					 "{$vfSituation} IN ( '".implode( "', '", (array)$search['Personne']['situation'] )."' )"
				);
			}

			// Dossier transféré ?
			if( isset( $search['Dossier']['transfere'] ) && ( $search['Dossier']['transfere'] != '' ) ) {
				if( $search['Dossier']['transfere'] ) {
					$conditions['Orientstruct.origine'] = 'demenagement';
				}
				else {
					$conditions[] = array(
						'NOT' => array( 'Orientstruct.origine' => 'demenagement' ),
					);
				}
			}


			$querydata = array(
				'fields' => array_merge(
					$Personne->fields(),
					$Personne->PersonneReferent->fields(),
					$Personne->PersonneReferent->Referent->fields(),
					$Personne->Calculdroitrsa->fields(),
					$Personne->Contratinsertion->fields(),
					$Personne->Contratinsertion->Cer93->fields(),
					$Personne->Orientstruct->fields(),
					array_words_replace( $Personne->Orientstruct->Structurereferente->fields(), array( 'Orientstruct' => 'Orientstructpcd' ) ),
					$Personne->Prestation->fields(),
					$Personne->Foyer->Dossier->fields(),
					$Personne->Foyer->Adressefoyer->Adresse->fields(),
					$Personne->Foyer->Dossier->Situationdossierrsa->fields(),
					// Présence DSP
					array(
						$Personne->sqVirtualField( 'nom_complet_court', true ),
						"( {$sqDspExists} ) AS \"Dsp__exists\"", // TODO: mettre dans le modèle,
//						"( \"Contratinsertion\".\"structurereferente_id\" = {$structurereferente_id} ) AS \"Contratinsertion__interne\"",
						"( \"Contratinsertion\".\"structurereferente_id\" IN ( '".implode( "', '", (array)$structurereferente_id )."' ) ) AS \"Contratinsertion__interne\"",
						$Personne->sqVirtualField( 'situation', true ),
					)
				),
				'contain' => false,
				'joins' => array(
					$Personne->join( 'Calculdroitrsa', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->join( 'Contratinsertion', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Orientstruct', array( 'type' => 'INNER' ) ),
					$Personne->join(
						'PersonneReferent',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								"PersonneReferent.id IN ( {$sqDernierReferent} )"
							)
						)
					),
					$Personne->PersonneReferent->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$Personne->Contratinsertion->join( 'Cer93', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
					array_words_replace(
						$Personne->join(
							'Orientstruct',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'OR' => array(
										'Orientstruct.id IS NULL',
										"Orientstruct.id IN ( {$sqOrientstructpcd} )"
									)
								)
							)
						),
						array( 'Orientstruct' => 'Orientstructpcd' )
					),
					array_words_replace( $Personne->Orientstruct->join( 'Structurereferente' ), array( 'Orientstruct' => 'Orientstructpcd' ) ),
				),
				'conditions' => $conditions,
				'order' => array(
					'Personne.situation' => 'ASC',
					'Orientstruct.date_valid ASC',
					'Personne.nom ASC',
					'Personne.prenom ASC',
				),
				'limit' => 10
			);

			return $querydata;
		}

		/**
		 * Préremplissage du formulaire en cohorte
		 *
		 * @param array $results
		 * @param array $params
		 * @param array $options
		 * @return array
		 */
		public function prepareFormDataCohorte( array $results, array $params = array(), array &$options = array() ) {
			$data = array( 'PersonneReferent' => array() );

			foreach( $results as $index => $result ) {
				$structurereferente_id = Hash::get( $result, 'Referent.structurereferente_id' );
				$referent_id = Hash::get( $result, 'PersonneReferent.referent_id' );

				$data['PersonneReferent'][$index] = array(
					'id' => $result['PersonneReferent']['id'],
					'dossier_id' => $result['Dossier']['id'],
					'personne_id' => $result['Personne']['id'],
					'dddesignation' => date( 'Y-m-d' ),
					'action' => 'Desactiver',
					'referent_id' => !empty( $structurereferente_id ) ? "{$structurereferente_id}_{$referent_id}" : null
				);
			}

			return $data;
		}
	}
?>