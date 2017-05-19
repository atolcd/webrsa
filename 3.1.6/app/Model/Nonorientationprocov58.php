<?php
	/**
	 * Code source de la classe Nonorientationprocov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( ABSTRACTMODELS.'AbstractThematiquecov58.php' );

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
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

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
						$success = $this->Orientstruct->save() && $success;

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
//					$success = $this->Dossiercov58->Passagecov58->{$modelDecisionName}->save() && $success;

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
		 * Retourne le querydata de base à utiliser dans le moteur de recherche
		 * de la cohorte.
		 *
		 * @deprecated since 3.0.0
		 *
		 * @return array
		 */
		public function cohorteQuery() {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$modelName = $this->alias;
				$modelTable = Inflector::tableize( $modelName );

				$ancienneTableCov = 'proposnonorientationsproscovs58';
				$ancienneTableEp = 'nonorientationsproseps58';

				$delaiCreationContrat = Configure::read( "{$this->alias}.delaiCreationContrat" );
				$typesorientEmploiId = Configure::read( 'Typeorient.emploi_id' );

				// Sous-requête pour qu'il n'existe pas actuellement de dossier de COV pouvant déboucher sur une réorientation
				$Cov58 = $this->Dossiercov58->Passagecov58->Cov58;
				$queryDossierscovs58ReorientationsEnCours = $this->Dossiercov58->getDossiersQuery();
				$queryDossierscovs58ReorientationsEnCours['fields'] = array( 'Dossiercov58.id' );
				$queryDossierscovs58ReorientationsEnCours['conditions'][] = array(
					'Dossiercov58.personne_id = Personne.id',
					'Dossiercov58.themecov58' => $this->Dossiercov58->getThematiquesReorientations(),
					array(
						'OR' => array(
							'Cov58.id IS NULL',
							'Cov58.etatcov' => $Cov58::$etatsEnCours,
							array(
								'NOT' => array(
									'Passagecov58.etatdossiercov' => array( 'traite', 'annule' )
								)
							)
						)
					)
				);
				$queryDossierscovs58ReorientationsEnCours = array_words_replace(
					$queryDossierscovs58ReorientationsEnCours,
					array(
						'Cov58' => 'covs58',
						'Dossiercov58' => 'dossierscovs58',
						'Passagecov58' => 'passagescovs58'
					)
				);
				$alias = $this->Dossiercov58->alias;
				$this->Dossiercov58->alias = 'dossierscovs58';
				$sqDossierscovs58ReorientationsEnCours = $this->Dossiercov58->sq( $queryDossierscovs58ReorientationsEnCours );
				$this->Dossiercov58->alias = $alias;

				// TODO: Sous-requête pour qu'il n'existe pas actuellement de dossier d'EP pouvant déboucher sur une réorientation
				$Commissionep = $this->Dossiercov58->Personne->Dossierep->Passagecommissionep->Commissionep;
				$queryDossiersepsReorientationsEnCours = $this->Dossiercov58->Personne->Dossierep->getDossiersQuery();
				$queryDossiersepsReorientationsEnCours['fields'] = array( 'Dossierep.id' );
				$queryDossiersepsReorientationsEnCours['conditions'][] = array(
					'Dossierep.actif' => '1',
					'Dossierep.personne_id = Personne.id',
					'Dossierep.themeep' => $this->Dossiercov58->Personne->Dossierep->getThematiquesReorientations(),
					array(
						'OR' => array(
							'Commissionep.id IS NULL',
							'Commissionep.etatcommissionep' => $Commissionep::$etatsEnCours,
							array(
								'NOT' => array(
									'Passagecommissionep.etatdossierep' => array( 'traite', 'annule' )
								)
							)
						)
					)
				);
				$queryDossiersepsReorientationsEnCours = array_words_replace(
					$queryDossiersepsReorientationsEnCours,
					array(
						'Commissionep' => 'commissionseps',
						'Dossierep' => 'dossierseps',
						'Passagecommissionep' => 'passagescommissionseps'
					)
				);
				$alias = $this->Dossiercov58->Personne->Dossierep->alias;
				$this->Dossiercov58->Personne->Dossierep->alias = 'dossierseps';
				$sqDossiersepsReorientationsEnCours = $this->Dossiercov58->Personne->Dossierep->sq( $queryDossiersepsReorientationsEnCours );
				$this->Dossiercov58->Personne->Dossierep->alias = $alias;

				// On souhaite n'afficher que les orientations en social ne possédant encore pas de dossier COV
				// 1°) On a un dossier COV en cours de passage (<> finalisé (accepté/refusé), <> reporté) // {cree,traitement,ajourne,finalise}
				// 2°) Si COV accepte -> on a un dossier en EP -> OK (voir plus haut)
				// 3°) Si COV refuse -> il doit réapparaître
				// 4°) ATTENTION: accepté/refusé -> nouvelle orientation
				$sqOrientstructEnCoursCov = 'Orientstruct.id NOT IN (
					SELECT "%s"."orientstruct_id"
						FROM "%s"
							INNER JOIN "dossierscovs58" ON ( "dossierscovs58"."id" = "%s"."dossiercov58_id" )
						WHERE "dossierscovs58"."id" NOT IN (
							SELECT "passagescovs58"."dossiercov58_id"
							FROM  passagescovs58
							WHERE "passagescovs58"."etatdossiercov" = \'traite\'
						)
						AND "dossierscovs58"."themecov58" = \'%s\'
						AND "%s"."orientstruct_id" = "Orientstruct"."id"
					)';

				$sqOrientstructEnCoursEp = 'Orientstruct.id NOT IN (
					SELECT "%s"."orientstruct_id"
						FROM "%s"
							INNER JOIN "dossierseps" ON ( "dossierseps"."id" = "%s"."dossierep_id" )
						WHERE "dossierseps"."id" NOT IN (
							SELECT "passagescommissionseps"."dossierep_id"
							FROM  passagescommissionseps
							WHERE "passagescommissionseps"."etatdossierep" = \'traite\'
						)
						AND "dossierseps"."themeep" = \'%s\'
						AND "%s"."orientstruct_id" = "Orientstruct"."id"
					)';

				$sqOrientstructDelaiPassageCov = 'Orientstruct.id NOT IN (
					SELECT %s.orientstruct_id
						FROM %s
							INNER JOIN dossierscovs58 ON (
								%s.dossiercov58_id = dossierscovs58.id
							)
						WHERE
							%s.orientstruct_id = Orientstruct.id
							AND dossierscovs58.id IN (
								SELECT "passagescovs58"."dossiercov58_id"
								FROM passagescovs58
								WHERE "passagescovs58"."etatdossiercov" = \'traite\'
							)
							AND ( DATE( NOW() ) - (
								SELECT CAST( decisions%s.modified AS DATE )
									FROM decisions%s
										INNER JOIN passagescovs58 ON ( decisions%s.passagecov58_id = passagescovs58.id )
										INNER JOIN dossierscovs58 ON ( passagescovs58.dossiercov58_id = dossierscovs58.id )
									ORDER BY modified DESC
									LIMIT 1
							) ) <= '.$delaiCreationContrat.'
					)';

				$sqOrientstructDelaiPassageEp = 'Orientstruct.id NOT IN (
					SELECT "%s"."orientstruct_id"
						FROM "%s"
							INNER JOIN "dossierseps" ON ( "dossierseps"."id" = "%s"."dossierep_id" )
						WHERE
							"dossierseps"."id" NOT IN (
								SELECT "passagescommissionseps"."dossierep_id"
								FROM  passagescommissionseps
								WHERE "passagescommissionseps"."etatdossierep" = \'traite\'
							)
							AND ( DATE( NOW() ) - (
								SELECT CAST( decisions%s.modified AS DATE )
									FROM decisions%s
										INNER JOIN passagescommissionseps ON ( decisions%s.passagecommissionep_id = passagescommissionseps.id )
										INNER JOIN dossierseps ON ( passagescommissionseps.dossierep_id = dossierseps.id )
									ORDER BY modified DESC
									LIMIT 1
							) ) <= '.$delaiCreationContrat.'
							AND "dossierseps"."themeep" = \'%s\'
							AND "%s"."orientstruct_id" = "Orientstruct"."id"
					)';

				$query = array(
					'fields' => array(
						'Orientstruct.id',
						'Orientstruct.date_valid',
						'Orientstruct.user_id',
						'Typeorient.id',
						'Typeorient.lib_type_orient',
						'Structurereferente.id',
						'Structurereferente.lib_struc',
						$this->Orientstruct->Personne->Foyer->sqVirtualField( 'enerreur', true ),
						'Referent.qual',
						'Referent.nom',
						'Referent.prenom',
						$this->Orientstruct->Personne->sqVirtualField( 'nom_complet', true ),
						'Personne.id',
						'Personne.qual',
						'Personne.nom',
						'Personne.prenom',
						'Personne.dtnai',
						'Personne.nir',
						'Dossier.id',
						'Dossier.numdemrsa',
						'Adresse.codepos',
						'Adresse.nomcom',
						'Contratinsertion.df_ci',
						$this->Orientstruct->Personne->Contratinsertion->sqVirtualField( 'nbjours', true ),
						$this->Orientstruct->Personne->Orientstruct->Referent->sqVirtualField( 'nom_complet', true ),
					),
					'conditions' => array(
						// Conditions de base pour qu'un allocataire puisse passer en EP
						'Prestation.rolepers' => array( 'DEM', 'CJT' ),
						'Calculdroitrsa.toppersdrodevorsa' => 1,
						'Situationdossierrsa.etatdosrsa' => $this->Orientstruct->Personne->Foyer->Dossier->Situationdossierrsa->etatOuvert(),
						// La dernière orientation
						'Orientstruct.id IN ( '.$this->Orientstruct->WebrsaOrientstruct->sqDerniere().' )',
						// ...
						'EXISTS(
							SELECT
								*
							FROM orientsstructs
								INNER JOIN typesorients ON ( typesorients.id = orientsstructs.typeorient_id )
							WHERE
								orientsstructs.personne_id = Personne.id
								AND orientsstructs.statut_orient = \'Orienté\'
								AND (
									NOT EXISTS(
										SELECT *
											FROM orientsstructs AS osvt
												INNER JOIN typesorients AS tosvt ON ( tosvt.id = osvt.typeorient_id )
											WHERE
												osvt.personne_id = orientsstructs.personne_id
												AND osvt.statut_orient = \'Orienté\'
												AND osvt.date_valid > orientsstructs.date_valid
												AND tosvt.id = '.$typesorientEmploiId.'
									)
								)
								AND typesorients.id <> '.$typesorientEmploiId.'
								LIMIT 1
						)',
						// La personne ne doit pas être en cours de passage en COV pour cette thématique
						str_replace( '%s', $modelTable, $sqOrientstructEnCoursCov ),
						// La personne ne doit pas être en cours de passage en COV pour l'ancienne thématique
						str_replace( '%s', $ancienneTableCov, $sqOrientstructEnCoursCov ),
						// La personne ne doit pas être en cours de passage en EP pour l'ancienne thématique
						str_replace( '%s', $ancienneTableEp, $sqOrientstructEnCoursEp ),
						// On peut repasser pour cette thématique si le passage lié à cette orientation est plus vieux que
						// le délai que l'on laisse pour créer le CER
						str_replace( '%s', $modelTable, $sqOrientstructDelaiPassageCov ),
						// Même chose pour l'ancienne thématique COV
						str_replace( '%s', $ancienneTableCov, $sqOrientstructDelaiPassageCov ),
						// Même chose pour l'ancienne thématique EP
						str_replace( '%s', $ancienneTableEp, $sqOrientstructDelaiPassageEp ),
						// Pour lequel il n'existe ni de dossier COV ni de dossier d'EP pouvant déboucher sur une réorientation
						"NOT EXISTS( {$sqDossierscovs58ReorientationsEnCours} )",
						"NOT EXISTS( {$sqDossiersepsReorientationsEnCours} )"
					),
					'joins' => array(
						$this->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$this->Orientstruct->join( 'Typeorient', array( 'type' => 'INNER' ) ),
						$this->Orientstruct->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
						$this->Orientstruct->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Orientstruct->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
						$this->Orientstruct->Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
						$this->Orientstruct->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Orientstruct->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Orientstruct->Personne->Foyer->Dossier->join( 'Detaildroitrsa', array( 'type' => 'LEFT OUTER' ) ),
						$this->Orientstruct->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
						$this->Orientstruct->Personne->Foyer->join(
							'Adressefoyer',
							array(
								'type' => 'INNER',
								'conditions' => array(
									'Adressefoyer.id IN ( '.$this->Orientstruct->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
								)
							)
						),
						$this->Orientstruct->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) ),
						$this->Orientstruct->Personne->join(
							'Contratinsertion',
							array(
								'type' => 'INNER',
								'conditions' => array(
									// Le dernier CER
									'Contratinsertion.id IN ( '.$this->Orientstruct->Personne->Contratinsertion->WebrsaContratinsertion->sqDernierContrat( 'Personne.id', true ).' )'
								)
							)
						),
					),
					'contain' => false,
					'order' => array( $this->Orientstruct->Personne->Contratinsertion->sqVirtualField( 'nbjours', false )." DESC" )
				);

				$query = $this->Orientstruct->Personne->PersonneReferent->completeSearchQueryReferentParcours( $query );

				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche de la cohorte.
		 *
		 * @deprecated since 3.0.0
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function cohorteConditions( array $query, array $search ) {
			// On force certaines valeurs si besoin
			$search['Dossier']['dernier'] = true;

			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'Contratinsertion.df_ci' );
			$query['conditions'] = $this->conditionsPersonneFoyerDossier( $query['conditions'], $search );
			$query['conditions'] = $this->conditionsDernierDossierAllocataire( $query['conditions'], $search );
			$query['conditions'] = $this->conditionsAdresse( $query['conditions'], $search );

			$query = $this->Orientstruct->Personne->PersonneReferent->completeSearchConditionsReferentParcours( $query, $search );

			return $query;
		}

		/**
		 * Cohorte de recherches des bénéficiaires pour lesquels un dossier COV
		 * peut être créé pour la thématique.
		 *
		 * @deprecated since 3.0.0
		 *
		 * @param array $search
		 * @return array
		 */
		public function cohorte( array $search ) {
			$query = $this->cohorteQuery();
			$query = $this->cohorteConditions( $query, $search );

			return $query;
		}

		/**
		 * Tentative de sauvegarde de nouveaux dossiers de COV pour la thématique
		 * à partir de la cohorte.
		 *
		 * @deprecated since 3.0.0
		 *
		 * @param array $orientsstructs
		 * @param integer $user_id
		 * @return boolean
		 */
		public function saveCohorte( array $orientsstructs, $user_id = null ) {
			$success = true;
			$themecov58 = Inflector::tableize( $this->alias );

			// Recherche de l'id de la thématique
			$query = array(
				'fields' => array( 'Themecov58.id' ),
				'conditions' => array(
					'Themecov58.name' => $themecov58
				),
				'contain' => false
			);
			$result = (array)$this->Dossiercov58->Themecov58->find( 'first', $query );
			$themecov58_id = Hash::get( (array)$result, 'Themecov58.id' );

			// Enregistrement des dossiers COV pour la thématique
			foreach( $orientsstructs as $orientsstruct ) {
				if( !empty( $orientsstruct['id'] ) ) {
					// Enregistrement de l'entrée de dossier COV
					$dossiercov58 = array(
						'Dossiercov58' => array(
							'personne_id' => $orientsstruct['personne_id'],
							'themecov58_id' => $themecov58_id,
							'themecov58' => $themecov58
						)
					);
					$this->Dossiercov58->create( $dossiercov58 );
					$success = $success && $this->Dossiercov58->save();

					// Enregistrement de l'entrée de la thématique
					$nonorientationprocov58 = array(
						$this->alias => array(
							'dossiercov58_id' => $this->Dossiercov58->id,
							'orientstruct_id' => $orientsstruct['id'],
							'user_id' => $user_id
						)
					);
					$this->create( $nonorientationprocov58 );
					$success = $success && $this->save();
				}
			}

			return $success;
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
					'Personne' => array( 'qual' => ClassRegistry::init( 'Option' )->qual() ),
					'type' => array( 'voie' => ClassRegistry::init( 'Option' )->typevoie() )
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