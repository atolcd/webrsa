<?php
	/**
	 * Code source de la classe WebrsaRechercheFluxPoleEmploi.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheFluxPoleEmploi ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheFluxpoleemploi extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheFluxpoleemploi';

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
		public $uses = array( 'Allocataire', 'Dossier', 'Informationpe', 'Personne' );

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
					'Detaildroitrsa' => 'LEFT OUTER',
					'Informationpe' => 'INNER',
				);
				$query = $this->Allocataire->searchQuery( $types, 'Personne' );

				$joinPersonne = array (array (
					'table' => '"personnes"',
					'alias' => 'Personne',
					'type' => 'INNER',
					'conditions' => '
						(
							Fluxpoleemploi.nir IS NOT NULL
							AND Personne.dtnai = Fluxpoleemploi.dtnai
							AND SUBSTRING( Fluxpoleemploi.nir FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM Personne.nir ) FROM 1 FOR 13 )
						)
						OR
						(
							Personne.nom IS NOT NULL
							AND Personne.prenom IS NOT NULL
							AND Personne.dtnai IS NOT NULL
							AND Personne.dtnai = Fluxpoleemploi.dtnai
							AND TRIM( BOTH \' \' FROM Personne.nom ) <> \'\'
							AND TRIM( BOTH \' \' FROM Personne.prenom ) <> \'\'
							AND TRIM( BOTH \' \' FROM Fluxpoleemploi.nom ) = TRIM( BOTH \' \' FROM Personne.nom )
							AND TRIM( BOTH \' \' FROM Fluxpoleemploi.prenom ) = TRIM( BOTH \' \' FROM Personne.prenom )
							AND Personne.dtnai = Fluxpoleemploi.dtnai
						)
						OR
						(
							Personne.nom IS NOT NULL
							AND Personne.prenom IS NOT NULL
							AND Personne.dtnai IS NOT NULL
							AND Personne.dtnai = Fluxpoleemploi.dtnai
							AND TRIM( BOTH \' \' FROM Personne.nom ) <> \'\'
							AND TRIM( BOTH \' \' FROM Personne.prenom ) <> \'\'
							AND TRIM( BOTH \' \' FROM Fluxpoleemploi.nom ) = TRIM( BOTH \' \' FROM Personne.nom )
							AND TRIM( BOTH \' \' FROM Fluxpoleemploi.prenom ) = TRIM( BOTH \' \' FROM Personne.prenom )
							AND Personne.dtnai = Fluxpoleemploi.dtnai
							AND Fluxpoleemploi.nir IS NOT NULL
							AND SUBSTRING( Fluxpoleemploi.nir FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM Personne.nir ) FROM 1 FOR 13 )
						)
					',
				));
				$query['joins'] = array_merge ($joinPersonne, $query['joins']);

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

			// Flux Pôle Emploi
			$pathsDate = array(
				'Fluxpoleemploi.inscription_date_debut_ide',
				'Fluxpoleemploi.inscription_date_cessation_ide',
				'Fluxpoleemploi.inscription_date_radiation_ide',
				'Fluxpoleemploi.ppae_date_signature',
				'Fluxpoleemploi.ppae_date_notification',
				'Fluxpoleemploi.ppae_date_dernier_ent',
			);

			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, $pathsDate );

			$allocataireCodePe = (string)Hash::get( $search, 'Fluxpoleemploi.allocataire_code_pe' );
			if( $allocataireCodePe !== '' ) {
				$query['conditions'][]['Fluxpoleemploi.allocataire_code_pe'] = $allocataireCodePe ;
			}

			$allocataireIdentifiantPe = (string)Hash::get( $search, 'Fluxpoleemploi.allocataire_identifiant_pe' );
			if( $allocataireIdentifiantPe !== '' ) {
				$query['conditions'][]['Fluxpoleemploi.allocataire_identifiant_pe'] = $allocataireIdentifiantPe ;
			}
 			// Flux Pôle Emploi

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

			// Pour le type d'orientation
			$paths = array (
				'Orientstruct.typeorient_id',
			);

			foreach( $paths as $path ) {
				$value = suffix( Hash::get( $search, $path ) );
				if( $value !== null && $value !== '' ) {
					$query['conditions'][$path] = $value;
				}
			}

			// Modalité d'accompagnement
			if (isset ($search['Fluxpoleemploi']['accompagnement']) && is_array ($search['Fluxpoleemploi']['accompagnement'])) {
				$accompagnement = '';
				$separateur = '(';

				foreach ($search['Fluxpoleemploi']['accompagnement'] as $key => $value) {
					$accompagnement .= $separateur.'Fluxpoleemploi.ppae_modalite_code LIKE \''.$value.'\'';
					$separateur = ' OR ';
				}

				$query['conditions'][] = $accompagnement.')';
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
				if( $exists === '0' ) {
					$this->Dossier->Foyer->Personne->Behaviors->load('LinkedRecords');
					$sql = $this->Dossier->Foyer->Personne->linkedRecordVirtualField( 'Nonoriente66' );
					$query['conditions'][] = ' ' . $sql;
				}
				else if ( $exists === '1' ) {
					$this->Dossier->Foyer->Personne->Behaviors->load('LinkedRecords');
					$sql = $this->Dossier->Foyer->Personne->linkedRecordVirtualField( 'Nonoriente66' );
					$query['conditions'][] = 'NOT ' . $sql;
				}
			}

			return $query;
		}
	}
?>