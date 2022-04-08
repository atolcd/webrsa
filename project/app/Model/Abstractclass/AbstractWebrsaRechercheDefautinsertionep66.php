<?php
	/**
	 * Code source de la classe AbstractWebrsaRechercheDefautinsertionep66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe AbstractWebrsaRechercheDefautinsertionep66 ...
	 *
	 * @package app.Model
	 */
	abstract class AbstractWebrsaRechercheDefautinsertionep66 extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'AbstractWebrsaRechercheDefautinsertionep66';

		/**
		 * Modèles utilisés
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Personne',
			'Canton',
			'Historiqueetatpe',
			'Dossierep'
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
				$query = $this->Allocataire->searchQuery( $types, 'Personne' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Personne,
							$this->Personne->Orientstruct,
							$this->Personne->Foyer,
							$this->Personne->Foyer->Dossier->Situationdossierrsa,
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Personne.id',
					)
				);

				// 2. Jointures
				// Récupération des types d'orientation de type EMPLOI
				$typeOrientEmploi = implode(',', $this->Personne->Orientstruct->Typeorient->listIdTypeOrient('EMPLOI'));

				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Personne->join('Orientstruct', array(
							'type' => $types['Orientstruct'],
							'conditions' => array(
								'"Orientstruct"."id" IN (SELECT "o"."id"
											FROM orientsstructs AS o
											WHERE
												"o"."personne_id" = "Personne"."id"
												AND "o"."date_valid" IS NOT NULL
											ORDER BY "o"."date_valid" DESC
											LIMIT 1) AND "Orientstruct"."typeorient_id" IN ('.$typeOrientEmploi.')'
							)
						))
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
			 * Conditions spéciales
			 */
			$month = Hash::get($search, 'Orientstruct.date_valid.month');
			if ( $month ) {
				$query['conditions']['EXTRACT(MONTH FROM Orientstruct.date_valid) = '] = $month;
			}

			$year = Hash::get($search, 'Orientstruct.date_valid.year');
			if ( $year ) {
				$query['conditions']['EXTRACT(YEAR FROM Orientstruct.date_valid) = '] = $year;
			}

			return $query;
		}

		/**
		 * Conditions de defaut d'insertion EP
		 *
		 * @param array $query
		 * @return array
		 */
		public function getConditionEp( $query = array() ) {
			$query['conditions'][] = 
				'NOT EXISTS(
					SELECT "a".id
					FROM dossierseps AS "a"
					LEFT JOIN passagescommissionseps AS "b" ON "a".id = "b".dossierep_id
					LEFT JOIN commissionseps AS "c" ON "c".id = "b".commissionep_id
					WHERE "a".personne_id = "Personne".id 
					AND "a".themeep = \'defautsinsertionseps66\' 
					AND "a".actif = \'1\'
					AND
					(
						"b".id IS NULL
						OR
						"b".etatdossierep IN (\'associe\', \'decisionep\', \'decisioncg\', \'reporte\')
						OR
						"c".dateseance >= \''.date( 'Y-m-d', strtotime( '-2 mons' ) ).'\'
					)
				)'
			;

			return $query;
		}
	}
?>