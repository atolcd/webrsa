<?php
	/**
	 * Code source de la classe Demenagementhorsdpt.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractSearch', 'Model/Abstractclass' );

	/**
	 * La classe Demenagementhorsdpt fournit un moteur de recherche permettant
	 * de retrouver les allocataires ayant quitté le département.
	 *
	 * @package app.Model
	 * @deprecated since version 3.0.0
	 * @see WebrsaRechercheDemenagementhorsdpt
	 * @todo WebrsaRechercheDemenagementhorsdpt::searchConditions() Retirer le lien vers self::searchConditionsAdressesRang0203()
	 */
	class Demenagementhorsdpt extends AbstractSearch
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Demenagementhorsdpt';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $useTable = false;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Conditionnable',
		);


		/**
		 * Retourne le querydata de base à utiliser dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				'Adressefoyer2' => 'LEFT OUTER',
				'Adresse2' => 'LEFT OUTER',
				'Adressefoyer3' => 'LEFT OUTER',
				'Adresse3' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$Allocataire = ClassRegistry::init( 'Allocataire' );
				$Foyer = ClassRegistry::init( 'Foyer' );

				$query = $Allocataire->searchQuery( $types );

				$query['fields'][] = 'Personne.nom_complet';

				foreach( array( 2, 3 ) as $rgadr ) {
					$replacements = array(
						'Adressefoyer' => "Adressefoyer{$rgadr}",
						'Adresse' => "Adresse{$rgadr}",
						'01' => "0{$rgadr}"
					);

					$query['fields'] = Hash::merge(
						$query['fields'],
						array_words_replace( $Foyer->Adressefoyer->fields(), $replacements ),
						array_words_replace( $Foyer->Adressefoyer->Adresse->fields(), $replacements )
					);

					// Join sur Adressefoyer
					$join = $Foyer->join(
						'Adressefoyer',
						array(
							'type' => $types["Adressefoyer{$rgadr}"],
							'conditions' => array(
								'Adressefoyer.id IN ( '.$Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
							)
						)
					);
					$join = array_words_replace( $join, $replacements );
					$query['joins'][] = $join;

					// Join sur Adresse
					$join = $Foyer->Adressefoyer->join( 'Adresse', array( 'type' => $types["Adresse{$rgadr}"] ) );
					$join = array_words_replace( $join, $replacements );
					$query['joins'][] = $join;
				}

				$departement = Configure::read( 'Cg.departement' );
				$query['conditions']['Adresse.numcom NOT LIKE'] = "{$departement}%";
				$query['conditions'][] = array(
					'OR' => array(
						'Adresse2.numcom LIKE' => "{$departement}%",
						'Adresse3.numcom LIKE' => "{$departement}%",
					)
				);

				// Enregistrement dans le cache
				Cache::write( $cacheKey, $query );
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

		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			// 1. On complète les conditions de base de l'allocataire
			$Allocataire = ClassRegistry::init( 'Allocataire' );

			// 1.1 Astuce, les conditions sur l'adresse doivent s'appliquer aux adresses de rang 2 et 3
			$conditions = (array)Hash::get( $query, 'conditions' );
			$query['conditions'] = array();
			$query = $Allocataire->searchConditions( $query, $search );
			$query = $this->searchConditionsAdressesRang0203( $query, $conditions );

			return $query;
		}

		/**
		 * Retourne les options nécessaires au formulaire de recherche, au formulaire,
		 * aux impressions, ...
		 *
		 * @param array $params <=> array( 'allocataire' => true )
		 * @return array
		 */
		public function options( array $params = array() ) {
			$options = array();
			$params = $params + array( 'allocataire' => true, 'find' => false );

			if( Hash::get( $params, 'allocataire' ) ) {
				$Allocataire = ClassRegistry::init( 'Allocataire' );

				$options = $Allocataire->options();
			}

			$Option = ClassRegistry::init( 'Option' );

			$options = Hash::merge(
				$options,
				array(
					'Adresse2' => array(
						'pays' => ClassRegistry::init('Adresse')->enum('pays'),
						'typeres' => ClassRegistry::init('Adresse')->enum('typeres')
					),
					'Adressefoyer2' => array(
						'rgadr' => ClassRegistry::init('Adresse')->enum('rgadr'),
						'typeadr' => ClassRegistry::init('Adressefoyer')->enum('typeadr'),
					),
					'Adresse3' => array(
						'pays' => ClassRegistry::init('Adresse')->enum('pays'),
						'typeres' => ClassRegistry::init('Adresse')->enum('typeres')
					),
					'Adressefoyer3' => array(
						'rgadr' => ClassRegistry::init('Adresse')->enum('rgadr'),
						'typeadr' => ClassRegistry::init('Adressefoyer')->enum('typeadr'),
					),
				)
			);

			return $options;
		}
	}
?>