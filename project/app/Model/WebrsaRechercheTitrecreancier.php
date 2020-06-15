<?php
	/**
	 * Code source de la classe WebrsaRechercheTitrecreancier.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheTitrecreancier ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheTitrecreancier extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheTitrecreancier';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryTitrescreanciers.search.fields',
			'ConfigurableQueryTitrescreanciers.search.innerTable',
			'ConfigurableQueryTitrescreanciers.exportcsv'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Titrecreancier',
			'Creance',
			'Dossier',
			'Foyer',
			'Allocataire'
		);

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).
			'_'.Inflector::underscore( $this->alias ).
			'_'.Inflector::underscore( __FUNCTION__ );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$departement = Configure::read( 'Cg.departement' );

				$types += array(
					'Creance' => 'INNER JOIN',
					'Prestation' => $departement == 66 ? 'LEFT OUTER' : 'INNER',
					'Adressefoyer' => 'LEFT OUTER',
					'Adresse' => 'LEFT OUTER',
					'Situationdossierrsa' => 'LEFT OUTER',
					'Detaildroitrsa' => 'LEFT OUTER',
					'Titrecreancier' => 'INNER JOIN',
				);
				$query = $this->Allocataire->searchQuery( $types, 'Creance' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					array( 0 => 'Dossier.id' ),
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Titrecreancier,
							$this->Creance,
							$this->Foyer
						)
					)
				);

				// 2. Jointure
				$query['joins'] = array_merge(
					array(
						$this->Titrecreancier->join(
							'Creance',
							array(
								'type' => 'INNER ',
							)
						),
					),
					$query['joins']
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

				// 3. Ajout de champs et de jointures spécifiques au CG
				//Aucun

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

			//
			$arrayMoismoucompta_from = Hash::get( $search, 'Creance.moismoucompta_from' );
			$arrayMoismoucompta_to = Hash::get( $search, 'Creance.moismoucompta_to' );
			if ( !empty($arrayMoismoucompta_from) && !empty($arrayMoismoucompta_to)) {
				$moismoucompta_from = date_cakephp_to_sql( $arrayMoismoucompta_from );
				$moismoucompta_to = date_cakephp_to_sql( $arrayMoismoucompta_to );
				$query['conditions'][] = " Creance.moismoucompta BETWEEN '".$moismoucompta_from ."' AND '".$moismoucompta_to."'";
			}

			// if etat du titre créancier Selected then Titrecreancier.etat LIKE
			$etatTitrecreancier = (string)Hash::get( $search, 'Titrecreancier.etat' );
			if ( !empty($etatTitrecreancier) ) {
				$query['conditions'][] = " Titrecreancier.etat LIKE '".$etatTitrecreancier."'"  ;
			}

			// if etat de la créances Selected then Titrecreancier.numtitr LIKE
			$numtitrTitrecreancier = (string)Hash::get( $search, 'Titrecreancier.numtitr' );
			if ( !empty($numtitrTitrecreancier) ) {
				$query['conditions'][] = " Titrecreancier.numtitr LIKE '".$numtitrTitrecreancier."'"  ;
			}

			// if etat de la créances Selected then Titrecreancier.numtitr LIKE
			$numbordereauTitrecreancier = (string)Hash::get( $search, 'Titrecreancier.numbordereau' );
			if ( !empty($numbordereauTitrecreancier) ) {
				$query['conditions'][] = " Titrecreancier.numbordereau LIKE '".$numbordereauTitrecreancier."'"  ;
			}

			// if etat du motif d'émission Selected then Titrecreancier.motifemissiontitrecreancier_id LIKE
			$etatMotifEmissionTitrecreancier = (string)Hash::get( $search, 'Titrecreancier.motifemissiontitrecreancier_id' );
			if ( !empty($etatMotifEmissionTitrecreancier) ) {
				$query['conditions'][] = " Titrecreancier.motifemissiontitrecreancier_id = " . $etatMotifEmissionTitrecreancier;
			}

			// Début des spécificités par département
			$departement = Configure::read( 'Cg.departement' );

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