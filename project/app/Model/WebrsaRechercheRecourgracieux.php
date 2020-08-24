<?php
	/**
	 * Code source de la classe WebrsaRechercheRecourgracieux.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheRecourgracieux ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheRecourgracieux extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheRecourgracieux';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryRecoursgracieux.search.fields',
			'ConfigurableQueryRecoursgracieux.search.innerTable',
			'ConfigurableQueryRecoursgracieux.exportcsv'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Recourgracieux',
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
					'Calculdroitrsa' => 'LEFT OUTER',
					'Foyer' => 'INNER',
					'Prestation' => $departement == 66 ? 'LEFT OUTER' : 'INNER',
					'Personne' => 'INNER',
					'Adressefoyer' => 'LEFT OUTER',
					'Dossier' => 'INNER',
					'Adresse' => 'LEFT OUTER',
					'Situationdossierrsa' => 'LEFT OUTER',
					'Detaildroitrsa' => 'LEFT OUTER',
					'Recourgracieux' => 'INNER JOIN'
				);
				$query = $this->Allocataire->searchQuery( $types, 'Recourgracieux' );

				// Ajout des spécificités du moteur de recherche
				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Recourgracieux,
							$this->Foyer
						)
					)
				);

				// 2. Jointures
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Foyer->Personne->join(
							'Dsp',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Dsp.id IN ( '.$this->Dossier->Foyer->Personne->Dsp->WebrsaDsp->sqDerniereDsp().' )'
								)
							)
						),
						$this->Foyer->Personne->join(
							'DspRev',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'DspRev.id IN ( '.$this->Dossier->Foyer->Personne->DspRev->sqDerniere().' )'
								)
							)
						),
						$this->Foyer->Personne->join(
							'Orientstruct',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Orientstruct.statut_orient' => 'Orienté',
									'Orientstruct.id IN ( '.$this->Dossier->Foyer->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere().' )'
								)
							)
						),
						$this->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
						$this->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
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

			// if Date d’arrivée du dossier au CD entre debut et fin
			if( Hash::get( $search, 'Recourgracieux.dtarrivee' ) == 1 ) {
				$dtarrivee_from = date_cakephp_to_sql( Hash::get( $search, 'Recourgracieux.dtarrivee_from' ) );
				$dtarrivee_to = date_cakephp_to_sql( Hash::get( $search, 'Recourgracieux.dtarrivee_to') );
				$query['conditions'][] = " Recourgracieux.dtarrivee BETWEEN '".$dtarrivee_from ."' AND '".$dtarrivee_to."'";
			}

			// if Date butoir de réponse entre debut et fin
			if( Hash::get( $search, 'Recourgracieux.dtbutoir' ) == 1 ) {
				$dtbutoir_from = date_cakephp_to_sql( Hash::get( $search, 'Recourgracieux.dtbutoir_from' ) );
				$dtbutoir_to = date_cakephp_to_sql( Hash::get( $search, 'Recourgracieux.dtbutoir_to' ) );
				$query['conditions'][] = " Recourgracieux.dtbutoir BETWEEN '".$dtbutoir_from ."' AND '".$dtbutoir_to."'";
			}

			// if Date réception du dossier dans le service entre debut et fin
			if( Hash::get( $search, 'Recourgracieux.dtreception' ) == 1 ) {
				$dtreception_from = date_cakephp_to_sql( Hash::get( $search, 'Recourgracieux.dtreception_from' ) );
				$dtreception_to = date_cakephp_to_sql( Hash::get( $search, 'Recourgracieux.dtreception_to' ) );
				$query['conditions'][] = " Recourgracieux.dtreception BETWEEN '".$dtreception_from ."' AND '".$dtreception_to."'";
			}

			// if Origine du dossier Selected then Recourgracieux.originerecoursgracieux_id LIKE
			$originerecoursgracieux = (string)Hash::get( $search, 'Recourgracieux.originerecoursgracieux_id' );
			if ( !empty($originerecoursgracieux) ) {
				$query['conditions'][] = " Recourgracieux.originerecoursgracieux_id = ".$originerecoursgracieux;
			}

			// if Date d’affectation du dossier entre debut et fin
			if ( Hash::get( $search, 'Recourgracieux.dtaffectation' ) == 1 ) {
				$dtaffectation_from = date_cakephp_to_sql( Hash::get( $search, 'Recourgracieux.dtaffectation_from' ) );
				$dtaffectation_to = date_cakephp_to_sql( Hash::get( $search, 'Recourgracieux.dtaffectation_to' ) );
				$query['conditions'][] = " Recourgracieux.dtaffectation BETWEEN '".$dtaffectation_from ."' AND '".$dtaffectation_to."'";
			}

			// if Gestionnaire du dossier Selected then Recourgracieux.user_id LIKE
			$user_id = (string)Hash::get( $search, 'Recourgracieux.user_id' );
			if ( !empty($user_id) ) {
				$query['conditions'][] = " Recourgracieux.user_id = ".explode('_', $user_id)[1];
			}

			// if État du dossier Selected then Recourgracieux.etat LIKE
			$etatRecourgracieux = (string)Hash::get( $search, 'Recourgracieux.etat' );
			if ( !empty($etatRecourgracieux) ) {
				$query['conditions'][] = " Recourgracieux.etat LIKE '".$etatRecourgracieux."'"  ;
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