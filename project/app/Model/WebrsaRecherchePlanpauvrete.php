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
		public $uses = array('Personne', 'Allocataire', 'Dossier', 'WebrsaCohortePlanpauvrete' );

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
					// INNER JOIN
					'Foyer' => 'INNER',
					'Adressefoyer' => 'INNER',
					'Calculdroitrsa' => 'INNER',
					'Dossier' => 'INNER',
					'Situationdossierrsa' => 'INNER',
					'Adresse' => 'INNER',
					// LEFT OUTER JOIN
					'Prestation' => 'LEFT OUTER',
					'Detaildroitrsa' => 'LEFT OUTER',
					'Orientstruct' => 'LEFT OUTER',
					'Rendezvous' => 'LEFT OUTER',
					'Typerdv' => 'LEFT OUTER',
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
						$this->Personne->join('Contratinsertion')
					)
				);

				// 4. Conditions
				// SDD & DOV
				$query = $this->WebrsaCohortePlanpauvrete->sdddov($query);

				// Sans RDV
				$query = $this->WebrsaCohortePlanpauvrete->sansRendezvous($query);

				// Sans Orientation
				$query = $this->WebrsaCohortePlanpauvrete->sansOrientation($query);

				// Sans CER
				$query = $this->WebrsaCohortePlanpauvrete->sansCER($query);

				//Dans le mois précédent :
				$query = $this->WebrsaCohortePlanpauvrete->nouveauxEntrants($query);

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
			return $query;
		}

	}
