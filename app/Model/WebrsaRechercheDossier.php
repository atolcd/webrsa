<?php
	/**
	 * Code source de la classe WebrsaRechercheDossier.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheDossier ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheDossier extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheDossier';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryDossiers.search.fields',
			'ConfigurableQueryDossiers.search.innerTable',
			'ConfigurableQueryDossiers.exportcsv'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Allocataire', 'Dossier' );

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
				$departement = (int)Configure::read( 'Cg.departement' );

				$types += array(
					'Calculdroitrsa' => 'LEFT OUTER',
					'Foyer' => 'INNER',
					'Prestation' => $departement == 66 ? 'LEFT OUTER' : 'INNER',
					'Personne' => 'INNER',
					'Adressefoyer' => 'LEFT OUTER',
					'Dossier' => 'INNER',
					'Adresse' => 'LEFT OUTER',
					'Situationdossierrsa' => 'INNER',
					'Detaildroitrsa' => 'LEFT OUTER'
				);
				$query = $this->Allocataire->searchQuery( $types, 'Dossier' );

				// Ajout des spécificités du moteur de recherche
				$query['fields'] = array_merge(
					array( 0 => 'Dossier.id' ),
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Dossier->Foyer->Personne->Dsp,
							$this->Dossier->Foyer->Personne->DspRev,
							$this->Dossier->Foyer->Personne->Orientstruct,
							$this->Dossier->Foyer->Personne->Orientstruct->Structurereferente,
							$this->Dossier->Foyer->Personne->Orientstruct->Typeorient
						)
					)
				);

				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Dossier->Foyer->Personne->join(
							'Dsp',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Dsp.id IN ( '.$this->Dossier->Foyer->Personne->Dsp->WebrsaDsp->sqDerniereDsp().' )'
								)
							)
						),
						$this->Dossier->Foyer->Personne->join(
							'DspRev',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'DspRev.id IN ( '.$this->Dossier->Foyer->Personne->DspRev->sqDerniere().' )'
								)
							)
						),
						$this->Dossier->Foyer->Personne->join(
							'Orientstruct',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Orientstruct.statut_orient' => 'Orienté',
									'Orientstruct.id IN ( '.$this->Dossier->Foyer->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere().' )'
								)
							)
						),
						$this->Dossier->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
						$this->Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
					)
				);

				// Début des jointures supplémentaires par département

				// CD 58
				if( $departement == 58 ) {
					// Travailleur social chargé de l'évaluation: "Nom du chargé de
					// l'évaluation" lorsque l'on crée une orientation
					$query['fields'] = array_merge(
						$query['fields'],
						ConfigurableQueryFields::getModelsFields(
							array(
								$this->Dossier->Foyer->Personne->Dossiercov58,
								$this->Dossier->Foyer->Personne->Dossiercov58->Propoorientationcov58,
								$this->Dossier->Foyer->Personne->Dossiercov58->Propoorientationcov58->Referentorientant,
								$this->Dossier->Foyer->Personne->Dossiercov58->Propoorientationcov58->Structureorientante
							)
						)
					);
					$query['joins'][] = $this->Dossier->Foyer->Personne->join(
						'Dossiercov58',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Dossiercov58.id IN ( '.$this->Dossier->Foyer->Personne->Dossiercov58->sqDernierPassagePersonne( 'Personne.id', array( 'Dossiercov58.themecov58' => 'proposorientationscovs58' ) ).' )'
							)
						)
					);
					$query['joins'][] = $this->Dossier->Foyer->Personne->Dossiercov58->join( 'Propoorientationcov58', array( 'type' => 'LEFT OUTER' ) );
					$query['joins'][] = $this->Dossier->Foyer->Personne->Dossiercov58->Propoorientationcov58->join( 'Referentorientant', array( 'type' => 'LEFT OUTER' ) );
					$query['joins'][] = $this->Dossier->Foyer->Personne->Dossiercov58->Propoorientationcov58->join( 'Structureorientante', array( 'type' => 'LEFT OUTER' ) );

					// Dernière activité
					$query['fields'] = array_merge(
						$query['fields'],
						ConfigurableQueryFields::getModelsFields(
							array(
								$this->Dossier->Foyer->Personne->Activite
							)
						)
					);

					$query['joins'][] = $this->Dossier->Foyer->Personne->join(
						'Activite',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Activite.id IN ( '.$this->Dossier->Foyer->Personne->Activite->sqDerniere().' )'
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

			// Condition sur la nature du logement
			$natlog = (string)Hash::get( $search, 'Dsp.natlog' );
			if( $natlog !== '' ) {
				$query['conditions'][] = array(
					'OR' => array(
						array(
							// On cherche dans les Dsp si pas de Dsp mises à jour
							'DspRev.id IS NULL',
							'Dsp.natlog' => $natlog
						),
						'DspRev.natlog' => $natlog,
					)
				);
			}

			// Début des spécificités par département
			$departement = (int)Configure::read( 'Cg.departement' );

			// CD 58: travailleur social chargé de l'évaluation: "Nom du chargé de
			// l'évaluation" lorsque l'on crée une orientation
			if( $departement === 58 ) {
				$referentorientant_id = (string)Hash::get( $search, 'Propoorientationcov58.referentorientant_id' );
				if( $referentorientant_id !== '' ) {
					$query['conditions']['Propoorientationcov58.referentorientant_id'] = $referentorientant_id;
				}
			}

			// CD 66: Personne ne possédant pas d'orientation et sans entrée Nonoriente66
			if( $departement === 66 ) {
				$exists = (string)Hash::get( $search, 'Personne.has_orientstruct' );
				if( $exists === '0' ) {
					$this->Dossier->Foyer->Personne->Behaviors->load('LinkedRecords');
					$sql = $this->Dossier->Foyer->Personne->linkedRecordVirtualField( 'Nonoriente66' );
					$query['conditions'][] = 'NOT ' . $sql;
				}

				// Recherche par Tag / état du Tag
				$valeurtag_id = (array)Hash::get($search, 'Tag.valeurtag_id');
				$etat = (array)Hash::get($search, 'Tag.etat');
				if (false === empty($valeurtag_id) || false === empty($etat)) {
					$query['conditions'][] = ClassRegistry::init('Tag')->sqHasTagValue($valeurtag_id, '"Foyer"."id"', '"Personne"."id"', $etat);
				}
			}

			return $query;
		}
	}
?>