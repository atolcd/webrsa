<?php
	/**
	 * Code source de la classe AbstractWebrsaCohorteTitrecreancier.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe AbstractWebrsaCohorteTitrecreancier ...
	 *
	 * @package app.Model
	 */
	class AbstractWebrsaCohorteTitrecreancier extends AbstractWebrsaCohorte
	{

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Creance',
			'Titrecreancier',
			'Canton',
			'Dossier',
			'Foyer',
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
					'Creance' => 'INNER JOIN',
					'Calculdroitrsa' => 'LEFT OUTER',
					'Foyer' => 'INNER',
					'Prestation' => $departement == 66 ? 'LEFT OUTER' : 'INNER',
					'Personne' => 'INNER',
					'Adressefoyer' => 'LEFT OUTER',
					'Dossier' => 'INNER',
					'Adresse' => 'LEFT OUTER',
					'Situationdossierrsa' => 'LEFT OUTER',
					'Detaildroitrsa' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Creance' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Titrecreancier,
							$this->Creance,
							$this->Titrecreancier->Motifemissiontitrecreancier,
							$this->Foyer
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Personne.id',
						'Dossier.id',
						'Creance.id',
						'Creance.foyer_id',
						'Titrecreancier.id',
						'Titrecreancier.motifemissiontitrecreancier_id',
						'Motifemissiontitrecreancier.emissiontitre',
					)
				);

				// 2. Jointures
				$query['joins'] = array_merge(
					array(
						$this->Titrecreancier->join(
							'Creance',
							array(
								'type' => 'INNER ',
							)
						)
					),
					array(
						$this->Titrecreancier->join(
							'Motifemissiontitrecreancier',
							array(
								'type' => 'INNER ',
							)
						)
					),
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

				// 4. Tri par défaut
				//$query['order'] = array( 'Contratinsertion.df_ci' => 'ASC' );

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

			// if Origine de la créances Selected then Creances.orgcre LIKE
			$originecreance = (string)Hash::get( $search, 'Creance.orgcre' );
			if ( !empty($originecreance) ) {
				$query['conditions'][] = " Creance.orgcre LIKE '".$originecreance."'" ;
			}

			// if Motif indus de la créances Selected then Creances.motiindu LIKE
			$motiinducreance = (string)Hash::get( $search, 'Creance.motiindu' );
			if ( !empty($motiinducreance) ) {
				$query['conditions'][] = " Creance.motiindu LIKE '".$motiinducreance."'"  ;
			}

			//
			$arrayDtimplcre_from = Hash::get( $search, 'Creance.dtimplcre_from' );
			$arrayDtimplcre_to = Hash::get( $search, 'Creance.dtimplcre_to' );
			if ( !empty($arrayDtimplcre_from) && !empty($arrayDtimplcre_to)) {
				$dtimplcre_from = date_cakephp_to_sql( $arrayDtimplcre_from );
				$dtimplcre_to = date_cakephp_to_sql( $arrayDtimplcre_to );
				$query['conditions'][] = " Creance.dtimplcre BETWEEN '".$dtimplcre_from ."' AND '".$dtimplcre_to."'";
			}

			//
			$arrayMoismoucompta_from = Hash::get( $search, 'Creance.moismoucompta_from' );
			$arrayMoismoucompta_to = Hash::get( $search, 'Creance.moismoucompta_to' );
			if ( !empty($arrayMoismoucompta_from) && !empty($arrayMoismoucompta_to)) {
				$moismoucompta_from = date_cakephp_to_sql( $arrayMoismoucompta_from );
				$moismoucompta_to = date_cakephp_to_sql( $arrayMoismoucompta_to );
				$query['conditions'][] = " Creance.moismoucompta BETWEEN '".$moismoucompta_from ."' AND '".$moismoucompta_to."'";
			}

			// if etat de la créances Selected then Creances.etat LIKE
			$etatTitrecreancier = (string)Hash::get( $search, 'Titrecreancier.etat' );
			if ( !empty($etatTitrecreancier) ) {
				$query['conditions'][] = " Titrecreancier.etat LIKE '".$etatTitrecreancier."'"  ;
			}

			// if hastitrecreancier checked then Creances.hasTitreCreancier > 0
			$sanstitrecreancier = (string)Hash::get( $search, 'Creance.sanstitrecreancier ' );
			if ($sanstitrecreancier === '1') {
				$query['conditions'][] = " Titrecreancier.id IS NULL ";
				$query['joins'][] = array (
					'table' => '"titrescreanciers"',
					'alias' => 'Titrecreancier',
					'type' => 'LEFT OUTER JOIN ',
					'conditions' => '"Titrecreancier"."creance_id" = "Creance"."id"'
				);
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
