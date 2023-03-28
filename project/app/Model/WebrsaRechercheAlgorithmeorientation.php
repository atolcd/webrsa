<?php
	/**
	 * Code source de la classe WebrsaRechercheAlgorithmeorientation.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheNouveauxOrientes ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheAlgorithmeorientation extends AbstractWebrsaRecherche
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheAlgorithmeorientation';

		public $useTable=false;


		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryAlgorithmeorientation.nouveaux_orientes.fields',
			'ConfigurableQueryAlgorithmeorientation.nouveaux_orientes.innerTable',
			'ConfigurableQueryAlgorithmeorientation.nouveaux_orientes.exportcsv'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Orientstruct',
			'Informationpe',
			'Canton',
			'Rendezvous'
		);

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$departement = Configure::read( 'Cg.departement' );
			$types += array(
				'Calculdroitrsa' => 'INNER',
				'Foyer' => 'INNER',
				'Prestation' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Personne' => 'INNER',

				'Informationpe' => 'LEFT OUTER',
				'Historiqueetatpe' => 'LEFT OUTER',
				'Modecontact' => 'LEFT OUTER',
				'Typeorient' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Structureorientante' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Suiviinstruction' => 'LEFT OUTER',
				'Serviceinstructeur' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Rendezvous' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Orientstruct', false );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					['DISTINCT ON ("Personne"."id") "Personne"."id" as "Personne__id"'],
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Orientstruct,
							$this->Orientstruct->Personne->PersonneReferent,
							$this->Orientstruct->Typeorient,
							$this->Orientstruct->Typeorient->Structurereferente,
							$this->Orientstruct->Structureorientante,
							$this->Informationpe,
							$this->Informationpe->Historiqueetatpe,
							$this->Orientstruct->Personne->Foyer->Modecontact
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Orientstruct.id',
						'Orientstruct.personne_id',
						'Orientstruct.date_propo',
					)
				);

				// 2. Jointures
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Orientstruct->join( 'Typeorient', array( 'type' => $types['Typeorient'] ) ),
						$this->Orientstruct->join( 'Structurereferente', array( 'type' => $types['Structurereferente'] ) ),
						$this->Orientstruct->join( 'Structureorientante', array( 'type' => $types['Structureorientante'] ) ),
						$this->Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', $types['Informationpe'] ),
						$this->Informationpe->Historiqueetatpe->joinInformationpeHistoriqueetatpe( true, 'Informationpe', 'Historiqueetatpe', $types['Historiqueetatpe'] ),
						$this->Orientstruct->Personne->Foyer->join(
							'Modecontact',
							array(
								'conditions' => array(
									'Modecontact.id IN ( '.$this->Orientstruct->Personne->Foyer->Modecontact->sqDerniere( 'Foyer.id' ).' )'
								),
								'type' => $types['Modecontact']
							)
							),
						$this->Orientstruct->Personne->join( 'Rendezvous', array( 'type' => $types['Rendezvous'] ) ),
						$this->Orientstruct->Personne->Rendezvous->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
						$this->Orientstruct->Personne->Rendezvous->join( 'Statutrdv', array( 'type' => 'LEFT OUTER' ) ),
						// $this->Orientstruct->Personne->Rendezvous->join( 'Structurereferente', array( 'type' => $types['Structurereferente'] ) ),
						$this->Orientstruct->Personne->Rendezvous->join( 'Typerdv', array( 'type' => 'LEFT OUTER' ) )
					)
				);

				// Si on utilise les thématiques de RDV, ajout du champ virtuel
				if( Configure::read('Rendezvous.useThematique' ) ) {
					$query['fields']['Rendezvous.thematiques'] = '( '.$this->Rendezvous->WebrsaRendezvous->vfListeThematiques( null ).' ) AS "Rendezvous__thematiques"';
				}

				// Permet d'obtenir la dernière entrée de la table informationspe
				$sqDerniereInformationpe = $this->Informationpe->sqDerniere( 'Personne' );
				$query['conditions'][] = array(
					'OR' => array(
						"Informationpe.id IS NULL",
						"Informationpe.id IN ( {$sqDerniereInformationpe} )"
					)
				);

				// 5. Pour le CG 58, ajout des champs et jointure sur Activite
				if( $departement == 58 ) {
					// Dernière activité
					$query['fields'] = array_merge(
						$query['fields'],
						ConfigurableQueryFields::getModelsFields(
							array(
								$this->Orientstruct->Personne->Activite
							)
						)
					);

					$query['joins'][] = $this->Orientstruct->Personne->join(
						'Activite',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Activite.id IN ( '.$this->Orientstruct->Personne->Activite->sqDerniere().' )'
							),
						)
					);
				}

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

			$paths = array(
				'Orientstruct.origine',
				'Orientstruct.structureorientante_id',
				'Orientstruct.typeorient_id',
				'Orientstruct.statut_orient',
				'Orientstruct.serviceinstructeur_id',
				// INFO: plus nécessaire depuis l'utilisation de la méthode PersonneReferent::completeSearchConditionsReferentParcours
				'PersonneReferent.structurereferente_id',
				'PersonneReferent.referent_id',
			);

			// Fils de dependantSelect
			$pathsToExplode = array(
				'Orientstruct.referentorientant_id',
				'Orientstruct.structurereferente_id',
			);

			$pathsDate = array(
				'Orientstruct.date_valid',
			);

			if( Hash::get( $search, 'Orientstruct.derniere' ) ) {
				$query['conditions'][] = array(
					"Orientstruct.id IN (SELECT
						orientsstructs.id
					FROM
						orientsstructs
						WHERE
							orientsstructs.personne_id = Orientstruct.personne_id
						ORDER BY
							orientsstructs.id DESC
						LIMIT 1)"
				);
			}

			if( Hash::get( $search, 'Orientstruct.dernierevalid' ) ) {
				$query['conditions'][] = array(
					"Orientstruct.id IN (SELECT
						orientsstructs.id
					FROM
						orientsstructs
						WHERE
							orientsstructs.personne_id = Orientstruct.personne_id
							AND orientsstructs.statut_orient = 'Orienté'
						ORDER BY
							orientsstructs.id DESC
						LIMIT 1)"
				);
			}

			foreach( $paths as $path ) {
				$value = suffix( Hash::get( $search, $path ) );
				if( $value !== null && $value !== '' ) {
					$query['conditions'][$path] = $value;
				}
			}

			foreach( $pathsToExplode as $path ) {
				$value = Hash::get( $search, $path );
				if( $value !== null && $value !== '' && strpos($value, '_') > 0 ) {
					list(,$value) = explode('_', $value);
					$query['conditions'][$path] = $value;
				}
			}

			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, $pathsDate );

			// Possède un CER ou un référent (en cours)
			$query = $this->Orientstruct->Personne->WebrsaPersonne->completeQueryHasLinkedRecord(
				array(
					'Contratinsertion',
					'PersonneReferent' => array(
						'conditions' => array(
							'PersonneReferent.dfdesignation IS NULL'
						)
					)
				),
				$query,
				$search
			);

			// Recherche par identifiant Pôle Emploi
			$identifiantpe = Hash::get( $search, 'Historiqueetatpe.identifiantpe' );
			if( !empty( $identifiantpe ) ) {
				$query['conditions'][] = $this->Informationpe->Historiqueetatpe->conditionIdentifiantpe( $identifiantpe );
			}

			// Inscrit à PE ?
			$is_inscritpe = Hash::get( $search, 'Personne.is_inscritpe' );
			if( !in_array( $is_inscritpe, array( '', null ), true ) ) {
				if( $is_inscritpe ) {
					$query['conditions']['Historiqueetatpe.etat'] = 'inscription';
				}
				else {
					$query['conditions']['Historiqueetatpe.etat <>'] = 'inscription';
				}
			}

			// Condition sur le projet insertion emploi territorial de la structure de l'orientation
			$query['conditions'] = $this->conditionCommunautesr(
				$query['conditions'],
				$search,
				array( 'Orientstruct.communautesr_id' => 'Orientstruct.structurereferente_id' )
			);

			//recherche par rendez-vous
			$foreignKeys = array( 'structurereferente_id', 'referent_id', 'permanence_id', 'typerdv_id' );
			foreach( $foreignKeys as $foreignKey ) {
				$path = 'Rendezvous.'.$foreignKey;
				$value = (string)suffix( (string)Hash::get( $search, $path ) );
				if( $value !== '' ) {
					$query['conditions'][$path] = $value;
				}
			}

			$value = Hash::get( $search, 'Rendezvous.statutrdv_id' );
			if( !empty( $value ) ) {
				$query['conditions']['Rendezvous.statutrdv_id'] = $value;
			}

			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'Rendezvous.daterdv' );
			$query['conditions'] = $this->conditionsHeures( $query['conditions'], $search, 'Rendezvous.heurerdv' );

			// Recherche par thématique de rendez-vous si nécessaire
			$query['conditions'] = $this->Rendezvous->WebrsaRendezvous->conditionsThematique( $query['conditions'], $search, 'Rendezvous.thematiquerdv_id' );

			// Condition sur le projet insertion emploi territorial de la structure de rendez-vous
			$query['conditions'] = $this->conditionCommunautesr(
				$query['conditions'],
				$search,
				array( 'Rendezvous.communautesr_id' => 'Rendezvous.structurereferente_id' )
			);

			$from = (array)Hash::get( $search, 'Rendezvous.arevoirle' ) + array( 'day' => '01' );
			if( valid_date( $from ) ) {
				$to = strtotime( 'last day of this month', strtotime( "{$from['year']}-{$from['month']}-{$from['day']}" ) );
				$to = date_sql_to_cakephp( strftime( "%Y-%m-%d", $to ) );
				$rdv = array(
					'Rendezvous' => array(
						'arevoirle' => '1',
						'arevoirle_from' => $from,
						'arevoirle_to' => $to
					)
				);
				$query['conditions'] = $this->conditionsDates( $query['conditions'], $rdv, 'Rendezvous.arevoirle' );
			}



			//Possède un rdv sur les dates d'orientation
			if( Hash::get( $search, 'Rendezvous.periodeorientation' ) ) {
				$from = Hash::get( $search, "Orientstruct.date_valid_from" );
				$to = Hash::get( $search, "Orientstruct.date_valid_to" );
				$from = $from['year'].'-'.$from['month'].'-'.$from['day'];
				$to = $to['year'].'-'.$to['month'].'-'.$to['day'];
				$query['conditions'][] = "DATE(Rendezvous.daterdv) BETWEEN '{$from}' AND '{$to}'";
			}


			return $query;
		}
	}
?>