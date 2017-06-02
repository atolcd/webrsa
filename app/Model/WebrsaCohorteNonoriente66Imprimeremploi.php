<?php
	/**
	 * Code source de la classe WebrsaCohorteNonoriente66Imprimeremploi.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorteNonoriente66', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteNonoriente66Imprimeremploi ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteNonoriente66Imprimeremploi extends AbstractWebrsaCohorteNonoriente66
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteNonoriente66Imprimeremploi';
		
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
				'Dossier' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Adresse' => 'INNER',
				'Calculdroitrsa' => 'INNER',
				
				// LEFT OUTER JOIN
				'Detaildroitrsa' => 'LEFT OUTER',
				'Orientstruct' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Typeorient' => 'LEFT OUTER',
				'Nonorient66' => 'LEFT OUTER',
				'Informationpe' => 'LEFT OUTER',
				'Canton' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
			);
			
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = parent::searchQuery($types);
				
				$query['conditions'][] = array(
					'OR' => array(
						'Historiqueetatpe.id IS NULL',
						array( 'NOT' => array('Historiqueetatpe.etat' => 'inscription') )
					)
				);
				$query['conditions'][] = '( SELECT COUNT(orientsstructs.id) FROM orientsstructs WHERE orientsstructs.personne_id = "Personne"."id" AND orientsstructs.statut_orient = \'Orienté\' ) = 0';
				$query['conditions'][] = 'Personne.id NOT IN (
						SELECT nonorientes66.personne_id
						FROM nonorientes66
						WHERE nonorientes66.personne_id = Personne.id
					)'
				;
				
				Cache::write($cacheKey, $query);
			}
			
			return $query;
		}
	}
?>
