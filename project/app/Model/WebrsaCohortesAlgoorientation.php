<?php
	/**
	 * Code source de la classe WebrsaCohortesAlgoorientation.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesAlgoorientation ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortesAlgoorientation extends AbstractWebrsaCohorte
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohortesAlgoorientation';

        	/**
		 * On n'utilise pas de table.
		 *
		 * @var mixed
		 */
		public $useTable = false;

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Personne',
			'Allocataire',
			'Structurereferente',
			'Referent',
			'PersonneReferent',
            'Orientstruct',
			'Informationpe',
			'Modecontact'
		);

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {

				$types += array(
                    'Foyer' => 'INNER',
					'Dossier' => 'INNER',
					'Prestation' => 'INNER',
					'Personne' => 'INNER',
					'Calculdroitrsa' => 'LEFT OUTER',
					'Adressefoyer' => 'LEFT OUTER',
					'Adresse' => 'LEFT OUTER',
					'Situationdossierrsa' => 'LEFT OUTER',
					'Detaildroitrsa' => 'LEFT OUTER',
					'Orientstruct' => 'LEFT OUTER',
                    'Typeorient' => 'LEFT OUTER',
				    'Structurereferente' => 'LEFT OUTER',
                    'Informationpe' => 'LEFT OUTER',
                    'Historiqueetatpe' => 'LEFT OUTER',
                    'Referentparcours' => 'LEFT OUTER',
                    'Dsp' => 'LEFT OUTER',
				);
				$query = $this->Allocataire->searchQuery( $types, 'Personne' );
				$query['fields'] = array_merge(
					array(
						0 => 'Dossier.id',
						1 => 'Personne.id',
					),
                    // Champs nécessaires au traitement de la search
					array(
						'Orientstruct.id',
						'Orientstruct.personne_id',
						'Orientstruct.date_propo',
						'Orientstruct.statut_orient',
                    ),
					$query['fields']
				);

				//champs pour l'export csv et les graphiques
				$query['fields'][] = "Personne.sexe";
				$query['fields'][] = "Personne.qual";
				$query['fields'][] = "Personne.age";
				$query['fields'][] = "Dsp.id";
				$query['fields'][] = "Foyer.sitfam";
				$query['fields'][] = "Foyer.id";
				$query['fields'][] = "Modecontact.numtel";
				$query['fields'][] = "Modecontact.numposte";
				$query['fields'][] = "Modecontact.adrelec";


                // 2. Jointures
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Personne->join( 'Orientstruct', array( 'type' => $types['Typeorient'] ) ),
						$this->Personne->Orientstruct->join( 'Typeorient', array( 'type' => $types['Typeorient'] ) ),
						$this->Orientstruct->join( 'Structurereferente', array( 'type' => $types['Structurereferente'] ) ),
						$this->Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', $types['Informationpe'] ),
						$this->Informationpe->Historiqueetatpe->joinInformationpeHistoriqueetatpe( true, 'Informationpe', 'Historiqueetatpe', $types['Historiqueetatpe'] ),
						$this->Personne->join( 'Dsp', array( 'type' => $types['Dsp'] ) ),
						$this->Personne->Foyer->join( 'Modecontact', array( 'type' => 'LEFT OUTER', 'conditions' => 'Modecontact.id IN ('.$this->Personne->Foyer->Modecontact->sqDerniere('Foyer.id').')') ),
					)
				);


				// Permet d'obtenir la dernière entrée de la table informationspe
				$sqDerniereInformationpe = $this->Informationpe->sqDerniere( 'Personne' );
				$query['conditions'][] = array(
					'OR' => array(
						"Informationpe.id IS NULL",
						"Informationpe.id IN ( {$sqDerniereInformationpe} )"
					)
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

			$sqDerniereOrientstruct = $this->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere( 'Personne.id', 'orientsstructs', false );
			if( Hash::get( $search, 'Orientstruct.derniere' ) ) {
				$query['conditions'][] = array(
					"Orientstruct.id IN ({$sqDerniereOrientstruct})"
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
			$query = $this->Personne->WebrsaPersonne->completeQueryHasLinkedRecord(
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

			$query['conditions'][] = "Adresse.numcom ILIKE '93%'";


			return $query;
		}

	}