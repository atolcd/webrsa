<?php
	/**
	 * Code source de la classe WebrsaRechercheDefautinsertionep66Noninscrit.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRechercheDefautinsertionep66', 'Model/Abstractclass' );
	
	/**
	 * La classe WebrsaRechercheDefautinsertionep66Noninscrit ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheDefautinsertionep66Noninscrit extends AbstractWebrsaRechercheDefautinsertionep66
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheDefautinsertionep66Noninscrit';
		
		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				// INNER
				'Prestation' => 'INNER',
				'Foyer' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Adresse' => 'INNER',
				'Dossier' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Calculdroitrsa' => 'INNER',
				'Orientstruct' => 'INNER',
				'Typeorient' => 'INNER',
				'Structurereferente' => 'INNER',
				
				// LEFT
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				
				'Detaildroitrsa' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->getConditionEp(parent::searchQuery($types));
				
				$qdNonInscrits = $this->Historiqueetatpe->Informationpe->qdNonInscrits();
				$query['conditions'] = array_merge($query['conditions'], $qdNonInscrits['conditions']);
				$query['conditions'][] = 'Calculdroitrsa.toppersdrodevorsa = \'1\'';
				
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
			$query = parent::searchConditions( $query, $search );
			
			return $query;
		}
	}
?>