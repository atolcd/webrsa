<?php
	/**
	 * Code source de la classe WebrsaRechercheChangementadresse.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheChangementadresse ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheChangementadresse extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheChangementadresse';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Adressefoyer',
			'Canton',
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
				'Prestation' => 'INNER',
				'Adressefoyer' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'Emailcui' => 'LEFT OUTER',
				'Partenairecui' => 'LEFT OUTER',
				'Adressecui' => 'LEFT OUTER',
				'Entreeromev3' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore($this->useDbConfig).'_'.Inflector::underscore($this->alias).'_'.Inflector::underscore(__FUNCTION__).'_'.sha1(serialize($types));
			$query = Cache::read($cacheKey);

			if ($query === false) {
				$query = $this->Allocataire->searchQuery($types, 'Dossier');
				
				$query['fields'][] = 'Dossier.id';
				$query['conditions'][] = array(
					'"Adressefoyer"."dtemm" + INTERVAL \''.Configure::read('Alerte.changement_adresse.delai').' months\' >= NOW()'
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
		public function searchConditions(array $query, array $search) {
			$query = $this->Allocataire->searchConditions($query, $search);
			return $query;
		}
	}
?>