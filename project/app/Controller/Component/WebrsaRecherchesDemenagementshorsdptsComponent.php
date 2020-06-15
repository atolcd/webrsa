<?php
	/**
	 * Code source de la classe WebrsaRecherchesDemenagementshorsdptsComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesDemenagementshorsdptsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesDemenagementshorsdptsComponent extends WebrsaAbstractRecherchesComponent
	{
		protected function _queryConditions( array $query, array $filters, array $params ) {
			$Controller = $this->_Collection->getController();
			$departement = Configure::read( 'Cg.departement' );

			// On veut que les conditions sur les zones géographiques soit faites sur les rangs 02 et 03
			$query = parent::_queryConditions(
				$query,
				$filters,
				$params + array( 'completequery_zonesgeos_disabled' => true )
			);

			$q = $this->Allocataires->Gestionzonesgeos->completeQuery( array() );
			if( !empty( $q ) ) {
				$conditions = array(
					'OR' => array(
						array_words_replace(
							$q['conditions'],
							array( 'Adresse' => 'Adresse2', 'Adressefoyer' => 'Adressefoyer2' )
						),
						array_words_replace(
							$q['conditions'],
							array( 'Adresse' => 'Adresse3', 'Adressefoyer' => 'Adressefoyer3' )
						)
					)
				);
				$query['conditions'][] = $conditions;
			}

			// Conditions sur les dates d'emménagement pour les externes
			if( $departement == 93 && ( strpos( $Controller->Session->read( 'Auth.User.type' ), 'externe_' ) === 0 ) ) {
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
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$this->Option = ClassRegistry::init( 'Option' );

			$options = Hash::merge(
				parent::_optionsEnums( $params ),
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