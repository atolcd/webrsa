<?php
	/**
	 * Code source de la classe AbstractWebrsaCohorteActioncandidatPersonne.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe AbstractWebrsaCohorteActioncandidatPersonne ...
	 *
	 * @package app.Model
	 */
	class AbstractWebrsaCohorteActioncandidatPersonne extends AbstractWebrsaCohorte
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'AbstractWebrsaCohorteActioncandidatPersonne';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'ActioncandidatPersonne',
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
				'Adressefoyer' => 'INNER',
				'Dossier' => 'INNER',
				'Adresse' => 'INNER',
				'Situationdossierrsa' => 'INNER',
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
				$query = $this->Allocataire->searchQuery( $types, 'ActioncandidatPersonne' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->ActioncandidatPersonne,
							$this->ActioncandidatPersonne->Referent,
							$this->ActioncandidatPersonne->Actioncandidat,
							$this->ActioncandidatPersonne->Actioncandidat->Contactpartenaire->Partenaire,
							$this->ActioncandidatPersonne->Progfichecandidature66,
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'ActioncandidatPersonne.id',
						'ActioncandidatPersonne.personne_id',
						'ActioncandidatPersonne.datebilan',
						'( ARRAY_TO_STRING( ARRAY( '
							. 'SELECT a_m.motifsortie_id '
							. 'FROM actionscandidats_motifssortie AS a_m '
							. 'WHERE a_m.actioncandidat_id = "Actioncandidat"."id" '
						. '), \'_\') ) AS "ActioncandidatPersonne__motifsortie"'
					)
				);

				$joinActionPartenaire = array(
					'table' => '"partenaires"',
					'alias' => 'Partenaire',
					'type' => 'LEFT OUTER',
					'conditions' => '"Partenaire"."actioncandidat_id" = {$__cakeID__$} AND "Partenaire"."partenaire_id" = .id'
				);

				// 2. Jointure
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->ActioncandidatPersonne->join( 'Referent', array( 'type' => $types['Referent'] ) ),
						$this->ActioncandidatPersonne->join( 'Actioncandidat', array( 'type' => $types['Actioncandidat'] ) ),
						$this->ActioncandidatPersonne->Actioncandidat->join( 'Contactpartenaire', array( 'type' => $types['Contactpartenaire'] ) ),
						$this->ActioncandidatPersonne->Actioncandidat->Contactpartenaire->join( 'Partenaire', array( 'type' => $types['Partenaire'] ) ),
					)
				);

				if ( (int)Configure::read('Cg.departement') === 66 ) {
					$query['joins'][] = $this->ActioncandidatPersonne->join( 'Progfichecandidature66', array( 'type' => $types['Progfichecandidature66'] ) );
				}

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
				'Contactpartenaire.partenaire_id',
				'ActioncandidatPersonne.referent_id',
				'ActioncandidatPersonne.positionfiche',
				'Partenaire.codepartenaire'
			);

			// Fils de dependantSelect
			$pathsToExplode = array(
				'ActioncandidatPersonne.actioncandidat_id',
			);

			$pathsDate = array(
				'ActioncandidatPersonne.datesignature',
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