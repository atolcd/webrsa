<?php
	/**
	 * Code source de la classe WebrsaRecherchePlanpauvrete.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRecherchePlanpauvrete ...
	 *
	 * @package app.Model
	 */
	class WebrsaRecherchePlanpauvrete extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRecherchePlanpauvrete';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Personne',
			'Allocataire',
			'Dossier',
			'WebrsaCohortePlanpauvrete',
			'Informationpe'
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
					// INNER JOIN
					'Foyer' => 'INNER',
					'Adressefoyer' => 'INNER',
					'Calculdroitrsa' => 'INNER',
					'Dossier' => 'INNER',
					'Situationdossierrsa' => 'INNER',
					'Adresse' => 'INNER',
					'Prestation' => 'INNER',
					// LEFT OUTER JOIN
					'Detaildroitrsa' => 'LEFT OUTER',
					'Orientstruct' => 'LEFT OUTER',
					'Rendezvous' => 'LEFT OUTER',
					'Typerdv' => 'LEFT OUTER',
					'Informationpe' => 'LEFT OUTER',
					'Historiqueetatpe' => 'LEFT OUTER',
				);
				$query = $this->Allocataire->searchQuery( $types, 'Personne' );

				$query['fields']['Personne.id'] = 'DISTINCT ON ("Personne"."id") "Personne"."id" as "Personne__id"';
				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					// Champs nécessaires au traitement de la search
					array(
						'Foyer.id',
						'Dossier.id',
					)
				);
				// 2. Jointure
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Personne->join('Historiquedroit', array(
							'type' => 'INNER',
							'conditions' => array(
								'Personne.id = Historiquedroit.personne_id',
								'Personne.id = (SELECT personne_id
								from historiquesdroits WHERE
								personne_id = "Personne"."id"
								ORDER BY created DESC LIMIT 1)'
							)
						)),
						$this->Personne->join('Orientstruct'),
						$this->Personne->join('Rendezvous'),
						$this->Personne->join('Contratinsertion'),
						$this->Personne->join('Activite'),
						$this->Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', $types['Informationpe'] ),
						$this->Informationpe->Historiqueetatpe->joinInformationpeHistoriqueetatpe( true, 'Informationpe', 'Historiqueetatpe', $types['Historiqueetatpe'] ),
					)
				);

				// Vérification de la présence des SAMS dans la configuration et ajout si besoin
				$configuration = Configure::read('ConfigurableQuery.Planpauvrete.searchprimoaccedant');
				if(in_array('Sitecov58.name', $configuration['results']['fields']) == true) {
					$query['fields'] = array_merge(
						$query['fields'],
						array(
							'Sitecov58.name'
						)
					);

					$query['joins'] = array_merge(
						$query['joins'],
						array(
							array(
								'table' => 'cantons_sitescovs58',
								'alias' => 'CantonSitecov58',
								'type' => 'LEFT OUTER',
								'conditions' => '"Canton"."id" = "CantonSitecov58"."canton_id"'
							),
							array(
								'table' => 'sitescovs58',
								'alias' => 'Sitecov58',
								'type' => 'LEFT OUTER',
								'conditions' => '"Sitecov58"."id" = "CantonSitecov58"."sitecov58_id"'
							)
						)
					);
				}
				// 4. Conditions
				// Permet d'obtenir la dernière entrée de la table informationspe
				$sqDerniereInformationpe = $this->Informationpe->sqDerniere( 'Personne' );
				$query['conditions'][] = array(
					'OR' => array(
						"Informationpe.id IS NULL",
						"Informationpe.id IN ( {$sqDerniereInformationpe} )"
					)
				);

				// SDD & DOV
				$query = $this->WebrsaCohortePlanpauvrete->sdddov($query);
				$query = $this->WebrsaCohortePlanpauvrete->sdddovHistorique($query);

				// Sans RDV
				$query = $this->WebrsaCohortePlanpauvrete->sansRendezvous($query, true);

				// Sans Orientation
				$query = $this->WebrsaCohortePlanpauvrete->sansOrientation($query, true);

				// Sans CER
				$query = $this->WebrsaCohortePlanpauvrete->sansCER($query, true);

				//Dans le mois précédent :
				$query = $this->WebrsaCohortePlanpauvrete->nouveauxEntrants($query);

				Cache::write( $cacheKey, $query );
			}

			// Tri des joins pour mettre les INNER en premier
			$innerList = array();
			$leftList = array();
			foreach($query['joins'] as $key => $join) {
				if(isset($join['type']) && strpos($join['type'], 'INNER') !== false) {
					$innerList[] = $join;
				} else {
					$leftList[] = $join;
				}
			}
			$query['joins'] = array_merge($innerList, $leftList);

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
			$isCantonsSiteCov = false;
			if(isset($search['CantonSitecov58']) && !empty($search['CantonSitecov58'])) {
				$isCantonsSiteCov = true;
				// Suppression du join déjà réalisé car il est créé par la suite avec les bons paramètres
				foreach($query['joins'] as $key => $join) {
					if(isset($join['alias']) && $join['alias'] == 'CantonSitecov58') {
						unset($query['joins'][$key]);
					}
				}
			}
			$query = $this->Allocataire->searchConditions( $query, $search );
			if($isCantonsSiteCov) {
				// Remise à la bonne place du join
				foreach($query['joins'] as $key => $join) {
					if(isset($join['alias']) && $join['alias'] == 'Sitecov58') {
						$query['joins'][] = $join;
						unset($query['joins'][$key]);
					}
				}
			}

			// Inscrit à PE ?
			$is_inscritpe = Hash::get( $search, 'Personne.is_inscritpe' );
			if( !in_array( $is_inscritpe, array( '', null ), true ) ) {
				if( $is_inscritpe ) {
					$query['conditions']['Historiqueetatpe.etat'] = 'inscription';
				}
				else {
					$query['conditions'][] = array(
						'OR' => array(
							"Historiqueetatpe.etat <> 'inscription'",
							'Historiqueetatpe.etat IS NULL'
						)
					);
				}
			}

			return $query;
		}

	}
