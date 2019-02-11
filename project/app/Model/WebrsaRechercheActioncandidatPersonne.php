<?php
	/**
	 * Code source de la classe WebrsaRechercheActioncandidatPersonne.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheActioncandidatPersonne ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheActioncandidatPersonne extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheActioncandidatPersonne';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'ActioncandidatPersonne',
			'Canton',
			'WebrsaCohorteActioncandidatPersonneEnattente',
		);
		
		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Prestation' => 'LEFT OUTER',
				'Adressefoyer' => 'INNER',
				'Dossier' => 'INNER',
				'Adresse' => 'INNER',
				'Situationdossierrsa' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Personne' => 'INNER',
				'Typeorient' => 'LEFT OUTER',
				'Structurereferente' => 'INNER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Orientstruct' => 'LEFT OUTER',
				'Referent' => 'INNER',
				'Actioncandidat' => 'INNER',
				'Contactpartenaire' => 'INNER',
				'Partenaire' => 'INNER',
				'Progfichecandidature66' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->WebrsaCohorteActioncandidatPersonneEnattente->searchQuery($types);
				
				App::uses('WebrsaModelUtility', 'Utility');
				$highPriority = array('Actioncandidat', 'Referent');
				$query = WebrsaModelUtility::changeJoinPriority($highPriority, $query);

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
			return $this->WebrsaCohorteActioncandidatPersonneEnattente->searchConditions($query, $search);
		}
	}
?>