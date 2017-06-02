<?php
	/**
	 * Code source de la classe WebrsaCohorteNonoriente66Imprimernotifications.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorteNonoriente66', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteNonoriente66Imprimernotifications ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteNonoriente66Imprimernotifications extends AbstractWebrsaCohorteNonoriente66
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteNonoriente66Imprimernotifications';
		
		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				// INNER JOIN
				'Foyer' => 'INNER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Calculdroitrsa' => 'INNER',
				'Dossier' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Adresse' => 'INNER',
				'Nonoriente66' => 'INNER',
				'Orientstruct' => 'INNER',
				
				// LEFT OUTER JOIN
				'Structurereferente' => 'LEFT OUTER',
				'Typeorient' => 'LEFT OUTER',
				'Informationpe' => 'LEFT OUTER',
				'Canton' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Historiqueetatpe' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER',
			);
			
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = parent::searchQuery($types);
				
				$query['fields'][] = 'Orientstruct.id';
				
				$query['conditions'][] = array(
					'Nonoriente66.orientstruct_id IS NOT NULL',
					'Nonoriente66.datenotification IS NULL',
				);
				
				Cache::write($cacheKey, $query);
			}
			
			return $query;
		}
	}
	