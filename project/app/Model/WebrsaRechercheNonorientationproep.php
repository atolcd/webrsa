<?php
	/**
	 * Code source de la classe WebrsaRechercheNonorientationproep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheNonorientationproep ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheNonorientationproep extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheNonorientationproep';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryNonorientationsproseps.search.fields',
			'ConfigurableQueryNonorientationsproseps.search.innerTable',
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Orientstruct',
			'Canton',
			'Nonorientationproep58',
			'Nonorientationproep93',
			'Nonorientationproep66',
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
				'Calculdroitrsa' => 'INNER',
				'Foyer' => 'INNER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Dossier' => 'INNER',
				'Adresse' => 'INNER',
				'Situationdossierrsa' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Personne' => 'INNER',
				'Structurereferente' => 'INNER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Referent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Typeorient' => 'INNER',
				'Contratinsertion' => 'INNER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Orientstruct' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Orientstruct,
							$this->Orientstruct->Referent,
							$this->Orientstruct->Structurereferente,
							$this->Orientstruct->Typeorient,
							$this->Orientstruct->Personne,
							$this->Orientstruct->Personne->Contratinsertion,
							$this->Orientstruct->Personne->Foyer,
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Personne.id',
						'Personne.nom_complet',
					)
				);

				// 2. Jointure
				$query['joins'] = array_merge(
					array(
						$this->Orientstruct->join('Structurereferente', array('type' => $types['Structurereferente'])),
						$this->Orientstruct->join('Typeorient', array('type' => $types['Typeorient'])),
						$this->Orientstruct->join('Referent', array('type' => $types['Referent'])),
					),
					$query['joins'],
					array(
						$this->Orientstruct->Personne->join('Contratinsertion', array('type' => $types['Contratinsertion'])),
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

			$oldQuery = $this->{'Nonorientationproep'.Configure::read('Cg.departement')}->searchNonReoriente(
				array(),
				array(),
				array( 'Filtre' => $search )
			);

			foreach($oldQuery['conditions'] as $key => $condition) {
				$query['conditions'][] = array( $key => $condition );
			}

			return $query;
		}
	}
?>