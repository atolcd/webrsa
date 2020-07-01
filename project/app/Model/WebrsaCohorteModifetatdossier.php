<?php
	/**
	 * Code source de la classe WebrsaCohorteModifetatdossier.
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
	 * La classe WebrsaCohorteModifetatdossier ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteModifetatdossier extends AbstractWebrsaCohorte
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteModifetatdossier';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Dossier',
			'Allocataire',
			'Motifetatdossier'
		);

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Dossier.id' => array( 'type' => 'hidden' ),
			'Dossier.selection' => array( 'type' => 'checkbox' ),
			'Dossier.nouveletatdos' => array( 'type' => 'select', 'required' => true ),
			'Dossier.motif' => array( 'type' => 'select', 'required' => true ),
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
				$departement = Configure::read( 'Cg.departement' );

				$types += array(
					'Calculdroitrsa' => 'LEFT OUTER',
					'Foyer' => 'INNER',
					'Prestation' => $departement == 66 ? 'LEFT OUTER' : 'INNER',
					'Personne' => 'INNER',
					'Adressefoyer' => 'LEFT OUTER',
					'Dossier' => 'INNER',
					'Adresse' => 'LEFT OUTER',
					'Situationdossierrsa' => 'LEFT OUTER',
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
			$departement = Configure::read( 'Cg.departement' );

			// CD 58: travailleur social chargé de l'évaluation: "Nom du chargé de
			// l'évaluation" lorsque l'on crée une orientation
			if( $departement == 58 ) {
				$referentorientant_id = (string)Hash::get( $search, 'Propoorientationcov58.referentorientant_id' );
				if( $referentorientant_id !== '' ) {
					$query['conditions']['Propoorientationcov58.referentorientant_id'] = $referentorientant_id;
				}
			}

			// Recherche dossier PCG
			$etat_dossierpcg66 = (string)Hash::get( $search, 'Dossierpcg66.has_dossierpcg66' );
			if ($etat_dossierpcg66 === '0') {
				$query['conditions'][] = 'NOT ' . ' EXISTS ( ' . $this->Dossier->Foyer->dossiersPCG66 () . ' )';
			}
			else if ($etat_dossierpcg66 === '1') {
				$query['conditions'][] = ' EXISTS ( ' . $this->Dossier->Foyer->dossiersPCG66 () . ' )';
			}

			// CD 66: Personne ne possédant pas d'orientation et sans entrée Nonoriente66
			if( $departement == 66 ) {
				$exists = (string)Hash::get( $search, 'Personne.has_orientstruct' );
				if ( $exists === '1' ) {
					$this->Dossier->Foyer->Personne->Behaviors->load('LinkedRecords');
					$sql = $this->Dossier->Foyer->Personne->linkedRecordVirtualField( 'Nonoriente66' );
					$query['conditions'][] = 'NOT ' . $sql;
				}
			}

			return $query;
		}

		/**
		 * Tentative de sauvegarde des modifications de dossier
		 *
		 * @param array $data
		 * @param array $params
		 * @param integer $user_id
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$success = true;

			// Récupération de la liste des motifs
			$listemotifs = $this->Motifetatdossier->find('list', array(
				'fields' => array( 'Motifetatdossier.id', 'Motifetatdossier.lib_motif'),
				'conditions' => array('actif' => 1)
			));

			// Enregistrement
			foreach ( $data as $value ) {
				if($value['Dossier']['selection'] == 1) {
					$dataToSave = array(
						'Situationdossierrsa' => array(
							'etatdosrsa' => $value['Dossier']['nouveletatdos'],
							'dossier_id' => $value['Dossier']['id']
						)
					);
					$success = $this->Dossier->saveModifEtat($value['Dossier']['id'], $dataToSave, $listemotifs[$value['Dossier']['motif']]) && $success;
				}
			}

			return $success;
		}
	}