<?php
	/**
	 * Code source de la classe WebrsaRechercheDemenagementhorsdpt.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'CakeSession', 'Model/Datasource' );

	/**
	 * La classe WebrsaRechercheDemenagementhorsdpt ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheDemenagementhorsdpt extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheDemenagementhorsdpt';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryDemenagementshorsdpts.search.fields',
			'ConfigurableQueryDemenagementshorsdpts.search.innerTable',
			'ConfigurableQueryDemenagementshorsdpts.exportcsv',
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Personne',
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
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Prestation' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Personne' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Referent' => 'LEFT OUTER',
				'Adressefoyer2' => 'LEFT OUTER',
				'Adresse2' => 'LEFT OUTER',
				'Adressefoyer3' => 'LEFT OUTER',
				'Adresse3' => 'LEFT OUTER',
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
							$this->Personne->Foyer,
							$this->Personne->Foyer->Dossier,
							$this->Personne->Foyer->Adressefoyer->Adresse,
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Dossier.id'
					)
				);

				// 2. Jointures
				$query['joins'] = array_merge(
					$query['joins'],
					array(

					)
				);

				foreach( array( 2, 3 ) as $rgadr ) {
					$replacements = array(
						'Adressefoyer' => "Adressefoyer{$rgadr}",
						'Adresse' => "Adresse{$rgadr}",
						'01' => "0{$rgadr}"
					);

					$query['fields'] = Hash::merge(
						$query['fields'],
						array_words_replace(
							ConfigurableQueryFields::getModelsFields(
								array(
									$this->Personne->Foyer->Adressefoyer,
									$this->Personne->Foyer->Adressefoyer->Adresse
								)
							),
							$replacements
						)
					);

					// Join sur Adressefoyer
					$join = $this->Personne->Foyer->join(
						'Adressefoyer',
						array(
							'type' => $types["Adressefoyer{$rgadr}"],
							'conditions' => array(
								'Adressefoyer.id IN ( '.$this->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
							)
						)
					);
					$join = array_words_replace( $join, $replacements );
					$query['joins'][] = $join;

					// Join sur Adresse
					$join = $this->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => $types["Adresse{$rgadr}"] ) );
					$join = array_words_replace( $join, $replacements );
					$query['joins'][] = $join;
				}

				// 5. Ajout des champs virtuels des modèles aliasés
				$Models = array( $this->Personne->Foyer->Adressefoyer, $this->Personne->Foyer->Adressefoyer->Adresse );
				$rangs = array( 2, 3 );
				foreach( $Models as $Model ) {
					foreach( array_keys( $Model->virtualFields ) as $virtualFieldName ) {
						foreach( $rangs as $rang ) {
							$replacements = array( 'Adressefoyer' => 'Adressefoyer'.$rang, 'Adresse' => 'Adresse'.$rang );
							$virtualFieldExpression = array( $Model->sqVirtualField( $virtualFieldName, false ) );
							$virtualFieldExpression = array_words_replace($virtualFieldExpression, $replacements );
							$query['fields']["{$Model->alias}{$rang}.{$virtualFieldName}"] = "( {$virtualFieldExpression[0]} ) AS \"{$Model->alias}{$rang}__{$virtualFieldName}\"";
						}
					}
				}
//debug( $query['fields'] );
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
			$departement = Configure::read( 'Cg.departement' );
			$query = $this->Allocataire->searchConditions( $query, $search );

			$conditions = array(
				'Adresse.numcom NOT LIKE' => $departement.'%',
				array(
					'OR' => array(
						'Adresse2.numcom LIKE' => $departement.'%',
						'Adresse3.numcom LIKE' => $departement.'%'
					)
				)
			);

			$query = $this->searchConditionsAdressesRang0203($query, $conditions);

			// Conditions sur les dates d'emménagement pour les externes
			if( Configure::read( 'Cg.departement' ) == 93 && ( strpos( CakeSession::read( 'Auth.User.type' ), 'externe_' ) === 0 ) ) {
				$query['conditions'][] = array(
					'OR' => array(
						// L'allocataire a quitté le CG en rang 01 et l'adresse de rang 2 ...
						array(
							'Adresse2.numcom LIKE' => "{$departement}%",
							"( DATE_PART( 'year', \"Adressefoyer\".\"dtemm\" ) + 1 || '-03-31' )::DATE >= NOW()",
						),
						// L'allocataire a quitté l'adresse de rang 3 ...
						array(
							'Adresse3.numcom LIKE' => "{$departement}%",
							"( DATE_PART( 'year', \"Adressefoyer2\".\"dtemm\" ) + 1 || '-03-31' )::DATE >= NOW()",
						),
					)
				);
			}

			return $query;
		}

		/**
		 * Permet d'appliquer les conditions concernant les adresses sur les
		 * adresses de rang 2 et 3 au $query et complète le $query avec la variable
		 * $conditions non modifiée.
		 *
		 * @param array $query
		 * @param array $conditions
		 * @return array
		 */
		public function searchConditionsAdressesRang0203( array $query, array $conditions = array() ) {
			$conditionsAdresses = array();

			foreach( $query['conditions'] as $key => $value ) {
				$replacements = array(
					'Adressefoyer' => "Adressefoyer2",
					'Adresse' => "Adresse2",
					'01' => "02"
				);

				$keyValue = array( $key => $value );
				$newKeyValue = array_words_replace( $keyValue, $replacements );
				$diff = Hash::diff( $keyValue, $newKeyValue );
				if( !empty( $diff ) ) {
					$conditionsAdresses = Hash::merge( $conditionsAdresses, $newKeyValue );
				}
				else {
					if( !is_numeric( $key ) ) {
						$conditions[$key] = $value;
					}
					else {
						$conditions[] = $value;
					}
				}
			}

			if( !empty( $conditionsAdresses ) ) {
				$replacements = array(
					'Adressefoyer2' => "Adressefoyer3",
					'Adresse2' => "Adresse3",
					'02' => '03'
				);

				$conditions[] = array(
					'OR' => array(
						$conditionsAdresses,
						array_words_replace( $conditionsAdresses, $replacements )
					)
				);
			}

			$query['conditions'] = $conditions;

			return $query;
		}
	}
?>