<?php
	/**
	 * Code source de la classe AbstractWebrsaCohorteApre66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe AbstractWebrsaCohorteApre66 ...
	 *
	 * @package app.Model
	 */
	abstract class AbstractWebrsaCohorteApre66 extends AbstractWebrsaCohorte
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'AbstractWebrsaCohorteApre66';
		
		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Apre66',
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
				'Dossier' => 'LEFT OUTER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Personne' => 'INNER',
				'Typeorient' => 'LEFT OUTER',
				'Structurereferente' => 'INNER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Orientstruct' => 'LEFT OUTER',
				'Referent' => 'INNER',
				'Dossierep' => 'LEFT OUTER',
				'Aideapre66' => 'INNER',
				'Referentapre' => 'INNER',
				'Themeapre66' => 'INNER',
				'Typeaideapre66' => 'INNER',
				'Dernierreferent' => 'LEFT',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );
			$this->Apre66->Referentapre->forceVirtualFields = true;

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Apre66' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Apre66,
							$this->Apre66->Aideapre66,
							$this->Apre66->Referentapre,
							$this->Apre66->Aideapre66->Themeapre66,
							$this->Apre66->Aideapre66->Typeaideapre66,
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Apre66.id',
						'Apre66.personne_id',
						'Personne.id',
						'Dossier.id',
						'Aideapre66.id',
						'Aideapre66.montantpropose',
						'Aideapre66.datemontantpropose',
						'Aideapre66.typeaideapre66_id',
						$this->Apre66->virtualField('nb_fichiers_lies')
					)
				);

				// 2. Jointure
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Apre66->join( 'Aideapre66', array( 'type' => $types['Aideapre66'] ) ),
						$this->Apre66->join( 'Referentapre', array( 'type' => $types['Referentapre'] ) ),
						$this->Apre66->Aideapre66->join( 'Themeapre66', array( 'type' => $types['Themeapre66'] ) ),
						$this->Apre66->Aideapre66->join( 'Typeaideapre66', array( 'type' => $types['Typeaideapre66'] ) ),
						$this->Apre66->Referentapre->join( 'Dernierreferent', array( 'type' => $types['Dernierreferent'] ) ),
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

			/**
			 * Generateur de conditions
			 */
			$paths = array(
				'Apre66.etatdossierapre',
				'Apre66.isdecision',
				'Aideapre66.themeapre66_id',
				'Apre66.numeroapre',
				'Apre66.referent_id',
				'Dernierreferent.dernierreferent_id',
			);

			// Fils de dependantSelect
			$pathsToExplode = array(
				'Aideapre66.typeaideapre66_id',
			);
			
			foreach( $paths as $path ) {
				$value = Hash::get( $search, $path );
				if( $value !== null && $value !== '' ) {
					$query['conditions'][$path] = $value;
				}
			}

			foreach( $pathsToExplode as $path ) {
				$value = Hash::get( $search, $path );
				if( $value !== null && $value !== '' && strpos($value, '_') > 0 ) {
					list(,$value) = explode('_', $value);
					$query['conditions'][$path] = $value;
				}
			}

			/**
			 * Conditions spéciales
			 */

			return $query;
		}
	}
?>