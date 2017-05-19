<?php
	/**
	 * Code source de la classe WebrsaRechercheIndu.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheIndu ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheIndu extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheIndu';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryIndus.search.fields',
			'ConfigurableQueryIndus.search.innerTable',
			'ConfigurableQueryIndus.exportcsv'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Infofinanciere',
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
			$cgDepartement = Configure::read( 'Cg.departement' );
			$types += array(
				'Calculdroitrsa' => 'INNER',
				'Foyer' => 'INNER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Personne' => 'INNER',
				'Detailcalculdroitrsa' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Dossier' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Infofinanciere,
							$this->Infofinanciere->Dossier->Foyer->Personne->PersonneReferent,
							$this->Infofinanciere->Dossier->Detaildroitrsa->Detailcalculdroitrsa
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Dossier.id',
						'Personne.nom',
						'Personne.prenom',
						'Indu.moismoucompta' => 'COALESCE("IndusConstates"."moismoucompta","IndusTransferesCG"."moismoucompta","RemisesIndus"."moismoucompta") AS "Indu__moismoucompta"',

					)
				);

				$query['joins'][] = $this->Infofinanciere->Dossier->Detaildroitrsa->join('Detailcalculdroitrsa', array('type' => $types['Detailcalculdroitrsa']));

				// 2. Ajout des champs et des jointures sur les infosfinancieres, par type d'indu
				$types_allocations = array( 'IndusConstates', 'IndusTransferesCG', 'RemisesIndus' );
				foreach( $types_allocations as $type_allocation ) {
					// Ajout des champs pour ce modèle aliasé
					$fields = ConfigurableQueryFields::getModelsFields( array( $this->Infofinanciere ) );
					$query['fields'] = array_merge(
						$query['fields'],
						array_words_replace( $fields, array( 'Infofinanciere' => $type_allocation ) )
					);

					// Ajout des la jointure pour ce modèle aliasé
					$join = $this->Infofinanciere->Dossier->join(
						'Infofinanciere',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'IndusConstates.type_allocation' => $type_allocation
							)
						)
					);
					$query['joins'][] = array_words_replace( $join, array( 'Infofinanciere' => $type_allocation ) );
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
			 * Conditions obligatoire
			 */
			$query['conditions'][] = array(
				'Prestation.rolepers' => 'DEM',
				array(
					'OR' => array(
						'IndusConstates.type_allocation IS NOT NULL',
						'IndusTransferesCG.type_allocation IS NOT NULL',
						'RemisesIndus.type_allocation IS NOT NULL',
					),
				),
				array(
					'OR' => array(
						array(
							'OR' => array(
								'IndusConstates.moismoucompta = IndusTransferesCG.moismoucompta',
								'IndusConstates.moismoucompta IS NULL',
								'IndusTransferesCG.moismoucompta IS NULL',
							)
						),
						array(
							'OR' => array(
								'IndusConstates.moismoucompta = RemisesIndus.moismoucompta',
								'IndusConstates.moismoucompta IS NULL',
								'RemisesIndus.moismoucompta IS NULL',
							)
						),
						array(
							'OR' => array(
								'IndusTransferesCG.moismoucompta = RemisesIndus.moismoucompta',
								'IndusTransferesCG.moismoucompta IS NULL',
								'RemisesIndus.moismoucompta IS NULL',
							)
						)
					)
				),
			);

			/**
			 * Generateur de conditions
			 */
			$paths = array(
				'Dossier.typeparte'
			);

			foreach( $paths as $path ) {
				$value = Hash::get( $search, $path );
				if( $value !== null && $value !== '' ) {
					$query['conditions'][$path] = $value;
				}
			}

			/**
			 * Conditions spéciales
			 */
			$mtmoucompta = Hash::get( $search, 'Infofinanciere.mtmoucompta' );
			$compare = Hash::get( $search, 'Infofinanciere.compare' );
			if ( in_array($compare, array('<', '>', '<=', '>=')) ) {
				$query['conditions'][] = array(
					'OR' => array(
						"IndusConstates.mtmoucompta " . $compare => $mtmoucompta,
						"IndusTransferesCG.mtmoucompta " . $compare => $mtmoucompta,
						"RemisesIndus.mtmoucompta " . $compare => $mtmoucompta,
					)
				);
			}
			elseif( $mtmoucompta ) {
				$query['conditions'][] = array(
					'OR' => array(
						"IndusConstates.mtmoucompta" => $mtmoucompta,
						"IndusTransferesCG.mtmoucompta" => $mtmoucompta,
						"RemisesIndus.mtmoucompta" => $mtmoucompta,
					)
				);
			}

			$natpfcre = Hash::get( $search, 'Infofinanciere.natpfcre' );
			if( $natpfcre ) {
				$query['conditions'][] = array(
					'OR' => array(
						"IndusConstates.natpfcre" => $natpfcre,
						"IndusTransferesCG.natpfcre" => $natpfcre,
						"RemisesIndus.natpfcre" => $natpfcre,
					)
				);
			}

			return $query;
		}
	}
?>