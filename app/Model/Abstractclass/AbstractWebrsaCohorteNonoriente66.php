<?php
	/**
	 * Code source de la classe AbstractWebrsaCohorteNonoriente66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe AbstractWebrsaCohorteNonoriente66 ...
	 *
	 * @package app.Model
	 */
	abstract class AbstractWebrsaCohorteNonoriente66 extends AbstractWebrsaCohorte
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'AbstractWebrsaCohorteNonoriente66';
		
		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Nonoriente66',
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
				// INNER JOIN
				'Foyer' => 'INNER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Calculdroitrsa' => 'INNER',
				'Dossier' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Adresse' => 'INNER',
				
				// LEFT OUTER JOIN
				'Orientstruct' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Typeorient' => 'LEFT OUTER',
				'Nonoriente66' => 'LEFT OUTER',
				'Informationpe' => 'LEFT OUTER',
				'Canton' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Historiqueetatpe' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Personne' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Nonoriente66->Personne->Orientstruct,
							$this->Nonoriente66->Personne->Orientstruct->Structurereferente,
							$this->Nonoriente66->Personne->Orientstruct->Typeorient,
							$this->Nonoriente66,
							$this->Nonoriente66->Historiqueetatpe->Informationpe,
							$this->Nonoriente66->Historiqueetatpe,
							$this->Nonoriente66->Personne->PersonneReferent,
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Historiqueetatpe.id',
						'Nonoriente66.id',
						'Nonoriente66.personne_id',
						'Nonoriente66.origine',
						'Nonoriente66.historiqueetatpe_id',
						'Nonoriente66.user_id',
						'Orientstruct.origine',
						'Orientstruct.personne_id',
						'Orientstruct.statut_orient',
						'Orientstruct.typeorient_id',
						'Orientstruct.structurereferente_id',
						'Orientstruct.date_valid',
						'Personne.id',
						'Dossier.id',
						'Foyer.enerreur' => $this->Nonoriente66->Personne->Foyer->sqVirtualField( 'enerreur', true ),
						'Foyer.nbenfants' => '( '.$this->Nonoriente66->Personne->Foyer->vfNbEnfants().' ) AS "Foyer__nbenfants"',
					)
				);
				
				// 2. Jointure
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Nonoriente66->Personne->join( 'Orientstruct', array( $types['Orientstruct'] ) ),
						$this->Nonoriente66->Personne->Orientstruct->join( 'Structurereferente', array( $types['Structurereferente'] ) ),
						$this->Nonoriente66->Personne->Orientstruct->join( 'Typeorient', array( $types['Typeorient'] ) ),
						$this->Nonoriente66->Personne->join( 'Nonoriente66', array( $types['Nonoriente66'] ) ),
						$this->Nonoriente66->Historiqueetatpe->Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', $types['Informationpe'] ),
						$this->Nonoriente66->Historiqueetatpe->Informationpe->join( 'Historiqueetatpe', array( $types['Historiqueetatpe'] ) ),
					)
				);
				
				// 4. Conditions
				$query['conditions'][] =  array(
					'OR' => array(
						'Historiqueetatpe.id IS NULL',
						'Historiqueetatpe.id IN ( '.$this->Nonoriente66->Historiqueetatpe->sqDernier( 'Informationpe' ).' )'
					)
				);
				$query['conditions']['Calculdroitrsa.toppersdrodevorsa'] = '1';
				$query['conditions'][] = array(
					'OR' => array(
						'Informationpe.id IS NULL',
						'Informationpe.id IN ( '
							.$this->Nonoriente66->Historiqueetatpe->Informationpe->sqDerniere('Personne')
						.' )'
					)
				);
				$query['conditions']['Situationdossierrsa.etatdosrsa'] = $this->Nonoriente66->Personne->Foyer->Dossier->Situationdossierrsa->etatOuvert();

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
				'Nonoriente66.user_id'
			);

			// Fils de dependantSelect
			$pathsToExplode = array(
				
			);

			$pathsDate = array(
				'Nonoriente66.dateimpression',
				'Nonoriente66.datenotification',
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

			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, $pathsDate );
			
			return $query;
		}
	}
?>