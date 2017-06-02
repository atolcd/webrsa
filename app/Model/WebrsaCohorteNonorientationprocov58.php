<?php
	/**
	 * Code source de la classe WebrsaCohorteNonorientationprocov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteNonorientationprocov58 ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteNonorientationprocov58 extends AbstractWebrsaCohorte
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteNonorientationprocov58';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryNonorientationsproscovs58.cohorte.fields',
			'ConfigurableQueryNonorientationsproscovs58.cohorte.innerTable',
			'ConfigurableQueryNonorientationsproscovs58.exportcsv'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Allocataire', 'Orientstruct', 'Nonorientationprocov58' );

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Dossier.id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Orientstruct.id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Orientstruct.personne_id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Orientstruct.chosen' => array( 'type' => 'checkbox', 'label' => '&nbsp;' )
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

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				'Adresse' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Calculdroitrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Foyer' => 'INNER',
				'Personne' => 'INNER',
				'Prestation' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				// Propres au moteur de recherche
				'Structurereferente' => 'INNER',
				'Referent' => 'LEFT OUTER',
				'Typeorient' => 'INNER',
				'Contratinsertion' => 'INNER'
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Orientstruct' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Orientstruct,
							$this->Orientstruct->Personne->Contratinsertion,
							$this->Orientstruct->Referent,
							$this->Orientstruct->Structurereferente,
							$this->Orientstruct->Typeorient
						)
					),
					array(
						'Personne.id',
						'Dossier.id',
						'Orientstruct.id',
						'Orientstruct.personne_id'
					)
				);

				// 2. Jointures
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Orientstruct->join( 'Structurereferente', array( 'type' => $types['Structurereferente'] ) ),
						$this->Orientstruct->join( 'Typeorient', array( 'type' => $types['Typeorient'] ) ),
						$this->Orientstruct->join( 'Referent', array( 'type' => $types['Referent'] ) ),
						$this->Orientstruct->Personne->join(
							'Contratinsertion',
							array(
								'type' => $types['Contratinsertion'],
								'conditions' => array(
									// Le dernier CER
									'Contratinsertion.id IN ( '.$this->Orientstruct->Personne->Contratinsertion->WebrsaContratinsertion->sqDernierContrat( 'Personne.id', true ).' )'
								)
							)
						),
					)
				);

				// 3. Conditions
				$modelName = $this->Nonorientationprocov58->alias;
				$modelTable = Inflector::tableize( $modelName );

				$ancienneTableCov = 'proposnonorientationsproscovs58';
				$ancienneTableEp = 'nonorientationsproseps58';

				$delaiCreationContrat = Configure::read( "{$this->Nonorientationprocov58->alias}.delaiCreationContrat" );
				$typesorientEmploiIds = (array)Configure::read( 'Typeorient.emploi_id' );

				// Sous-requête pour qu'il n'existe pas actuellement de dossier de COV pouvant déboucher sur une réorientation
				$Cov58 = $this->Nonorientationprocov58->Dossiercov58->Passagecov58->Cov58;
				$queryDossierscovs58ReorientationsEnCours = $this->Nonorientationprocov58->Dossiercov58->getDossiersQuery();
				$queryDossierscovs58ReorientationsEnCours['fields'] = array( 'Dossiercov58.id' );
				$queryDossierscovs58ReorientationsEnCours['conditions'][] = array(
					'Dossiercov58.personne_id = Personne.id',
					'Dossiercov58.themecov58' => $this->Nonorientationprocov58->Dossiercov58->getThematiquesReorientations(),
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
				$alias = $this->Nonorientationprocov58->Dossiercov58->alias;
				$this->Nonorientationprocov58->Dossiercov58->alias = 'dossierscovs58';
				$sqDossierscovs58ReorientationsEnCours = $this->Nonorientationprocov58->Dossiercov58->sq( $queryDossierscovs58ReorientationsEnCours );
				$this->Nonorientationprocov58->Dossiercov58->alias = $alias;

				// TODO: Sous-requête pour qu'il n'existe pas actuellement de dossier d'EP pouvant déboucher sur une réorientation
				$Commissionep = $this->Nonorientationprocov58->Dossiercov58->Personne->Dossierep->Passagecommissionep->Commissionep;
				$queryDossiersepsReorientationsEnCours = $this->Nonorientationprocov58->Dossiercov58->Personne->Dossierep->getDossiersQuery();
				$queryDossiersepsReorientationsEnCours['fields'] = array( 'Dossierep.id' );
				$queryDossiersepsReorientationsEnCours['conditions'][] = array(
					'Dossierep.actif' => '1',
					'Dossierep.personne_id = Personne.id',
					'Dossierep.themeep' => $this->Nonorientationprocov58->Dossiercov58->Personne->Dossierep->getThematiquesReorientations(),
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
				$alias = $this->Nonorientationprocov58->Dossiercov58->Personne->Dossierep->alias;
				$this->Nonorientationprocov58->Dossiercov58->Personne->Dossierep->alias = 'dossierseps';
				$sqDossiersepsReorientationsEnCours = $this->Nonorientationprocov58->Dossiercov58->Personne->Dossierep->sq( $queryDossiersepsReorientationsEnCours );
				$this->Nonorientationprocov58->Dossiercov58->Personne->Dossierep->alias = $alias;

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
								SELECT decisions%s.modified::DATE
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
								SELECT decisions%s.modified::DATE
									FROM decisions%s
										INNER JOIN passagescommissionseps ON ( decisions%s.passagecommissionep_id = passagescommissionseps.id )
										INNER JOIN dossierseps ON ( passagescommissionseps.dossierep_id = dossierseps.id )
									ORDER BY modified DESC
									LIMIT 1
							) ) <= '.$delaiCreationContrat.'
							AND "dossierseps"."themeep" = \'%s\'
							AND "%s"."orientstruct_id" = "Orientstruct"."id"
					)';

				$query['conditions'] = array_merge(
					$query['conditions'],
					array(
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
												AND tosvt.id IN ( '.implode( ',', $typesorientEmploiIds ).' )
									)
								)
								AND typesorients.id NOT IN ( '.implode( ',', $typesorientEmploiIds ).' )
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
					)
				);

				// 4. Tri par défaut
				$query['order'] = array( $this->Orientstruct->Personne->Contratinsertion->sqVirtualField( 'nbjours', false )." DESC" );

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
			// On force pour quela plage de dates soit pris en compte (pas de case à cocher dans le formulaire)
			$search['Contratinsertion']['df_ci'] = '1';

			$query = $this->Allocataire->searchConditions( $query, $search );
			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'Contratinsertion.df_ci' );

			return $query;
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
			$data = array();
			return $data;
		}

		/**
		 * Tentative de sauvegarde de nouveaux dossiers de COV pour la thématique
		 * à partir de la cohorte.
		 *
		 * @param array $data
		 * @param array $params
		 * @param integer $user_id
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$validationErrors = array();
			$success = true;

			$themecov58 = Inflector::tableize( $this->Nonorientationprocov58->alias );

			// Recherche de l'id de la thématique
			$query = array(
				'fields' => array( 'Themecov58.id' ),
				'conditions' => array(
					'Themecov58.name' => $themecov58
				),
				'contain' => false
			);
			$result = (array)$this->Nonorientationprocov58->Dossiercov58->Themecov58->find( 'first', $query );
			$themecov58_id = Hash::get( (array)$result, 'Themecov58.id' );

			$this->Nonorientationprocov58->begin();

			// Enregistrement des dossiers COV pour la thématique
			foreach( $data as $key => $line ) {
				if( !empty( $line['Orientstruct']['chosen'] ) ) {
					// Enregistrement de l'entrée de dossier COV
					$dossiercov58 = array(
						'Dossiercov58' => array(
							'personne_id' => $line['Orientstruct']['personne_id'],
							'themecov58_id' => $themecov58_id,
							'themecov58' => $themecov58
						)
					);
					$this->Nonorientationprocov58->Dossiercov58->create( $dossiercov58 );
					$success = $success && $this->Nonorientationprocov58->Dossiercov58->save( null, array( 'atomic' => false ) );
					$validationErrors['Dossiercov58'][$key] = $this->Nonorientationprocov58->Dossiercov58->validationErrors;

					// Enregistrement de l'entrée de la thématique
					$nonorientationprocov58 = array(
						$this->Nonorientationprocov58->alias => array(
							'dossiercov58_id' => $this->Nonorientationprocov58->Dossiercov58->id,
							'orientstruct_id' => $line['Orientstruct']['id'],
							'user_id' => $user_id
						)
					);
					$this->Nonorientationprocov58->create( $nonorientationprocov58 );
					$success = $success && $this->Nonorientationprocov58->save( null, array( 'atomic' => false ) );
					$validationErrors['Nonorientationprocov58'][$key] = $this->Nonorientationprocov58->validationErrors;
				}
			}

			foreach ((array)Hash::filter($validationErrors) as $alias => $errors) {
				ClassRegistry::getObject($alias)->validationErrors = $errors;
			}

			if ($success) {
				$this->Nonorientationprocov58->commit();
			} else {
				$this->Nonorientationprocov58->rollback();
			}

			return $success;
		}

	}
?>