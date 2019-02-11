<?php
	/**
	 * Code source de la classe WebrsaCohortesPersonnesReferentsAffectation93Component.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesPersonnesReferentsAffectation93Component ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesPersonnesReferentsAffectation93Component extends WebrsaAbstractCohortesComponent
	{
		/**
		 * Surcharge de la méthode params pour pouvoir désactiver le bloc "Suivi
		 * du parcours".
		 *
		 * @param array $params
		 * @return array
		 */
		protected function _params( array $params = array() ) {
			$Controller = $this->_Collection->getController();

			$params = parent::_params( $params );
			$configureKey = "{$params['searchKeyPrefix']}.{$params['configurableQueryFieldsKey']}";

			// 1. Surcharge de la configuration "filters.skip"
			$skip = (array)Configure::read( "{$configureKey}.filters.skip" );
			$skip = array_merge(
				$skip,
				array(
					'PersonneReferent.structurereferente_id',
					'PersonneReferent.referent_id'
				)
			);
			Configure::write( "{$configureKey}.filters.skip", $skip );

			return $params;
		}

		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params ) {
			$Controller = $this->_Collection->getController();

			if( !isset( $Controller->PersonneReferent ) ) {
				$Controller->loadModel( 'PersonneReferent' );
			}

			return Hash::merge(
				parent::_optionsEnums( $params ),
				$Controller->PersonneReferent->Personne->Contratinsertion->enums(),
				$Controller->PersonneReferent->Personne->Contratinsertion->Cer93->enums(),
				array(
					'Personne' => array(
						'situation' => array(
							1 => 'Allocataire non affecté sans CER',
							2 => 'Allocataire non affecté ayant un CER non-signé',
							3 => 'Allocataire non affecté ayant un CER signé',
							4 => 'Allocataire affecté sans CER',
							5 => 'Allocataire affecté ayant un CER non-signé',
							6 => 'Allocataire affecté ayant un CER signé',
							7 => 'Allocataire non affecté ayant un CER terminé',
							8 => 'Allocataire non affecté ayant un CER se terminant bientôt',
							9 => 'Allocataire affecté ayant un CER terminé',
							10 => 'Allocataire affecté ayant un CER se terminant bientôt',
							11 => 'Allocataire affecté ayant un CER rejeté CG',
						)
					),
					'PersonneReferent' => array(
						'active' => array( '1' => 'Activer', '0' => 'Désactiver' )
					),
					'Referent' => array(
						'designe' => array( '0' => 'Référent non désigné', '1' => 'Référent désigné' )
					),
					'Contratinsertion' => array(
						'interne' => array( '1' => 'Oui', '0' => 'Non' )
					),
					'exists' => array( '1' => 'Oui', '0' => 'Non' )
				)
			);
		}

		/**
		 * Retourne les options stockées en session, liées à l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsSession( array $params ) {
			$result = parent::_optionsSession( $params );
			$Controller = $this->_Collection->getController();

			if( false === isset( $Controller->WebrsaTableausuivipdv93 ) ) {
				$Controller->loadModel( 'WebrsaTableausuivipdv93' );
			}

			$structuresreferentes_ids = Hash::extract(
				(array)$Controller->Session->read( 'Auth.Structurereferente' ),
				'{n}.id'
			);

			// Moteur de recherche -> TODO: faire apparaître les inactifs ?
			$result['Referent']['id'] = $Controller->InsertionsBeneficiaires->referents(
				array(
					'type' => 'optgroup',
					'prefix' => true,
					'conditions' => $Controller->InsertionsBeneficiaires->conditions['referents']
						+ array( 'Structurereferente.id' => $structuresreferentes_ids )
				)
			);

			// Cohorte
			$result['PersonneReferent']['referent_id'] = $Controller->InsertionsBeneficiaires->referents(
				array(
					'type' => 'optgroup',
					'prefix' => true,
					'conditions' => $Controller->InsertionsBeneficiaires->conditions['referents']
						+ array( 'Structurereferente.id' => $structuresreferentes_ids )
				)
			);

			// Référent précédent
			$result['PersonneReferentPcd']['referent_id'] = $Controller->InsertionsBeneficiaires->referents(
				array(
					'type' => 'optgroup',
					'prefix' => true,
					'conditions' => array( 'Structurereferente.id' => $structuresreferentes_ids )
				)
			);

			return $result;
		}

		/**
		 *
		 * @param array $query
		 * @param array $params
		 * @return array
		 */
		protected function _queryOrder( array $query, array $params ) {
			$query = parent::_queryOrder( $query, $params );

			$query['order'] = array(
				'Personne.situation ASC',
				'Orientstruct.date_valid ASC',
				'Personne.nom ASC',
				'Personne.prenom ASC',
			);

			return $query;
		}

		/**
		 * Surcharge de la méthode _queryConditions pour limiter les résultats
		 * à ceux dont la structure référente de l'orientation actuelle fait
		 * partie des structures référentes accessibles à l'utilisateur.
		 *
		 * @param array $query
		 * @param array $filters
		 * @param array $params
		 * @return array
		 */
		protected function _queryConditions( array $query, array $filters, array $params ) {
			$Controller = $this->_Collection->getController();
			$query = parent::_queryConditions( $query, $filters, $params );

			if( !isset( $Controller->WebrsaUsers ) ) {
				$Controller->WebrsaUsers = $Controller->Components->load( 'WebrsaUsers' );
			}

			$query['conditions'][] = array(
				'Orientstruct.structurereferente_id' => $Controller->WebrsaUsers->structuresreferentes( array( 'type' => 'ids', 'prefix' => false ) )
			);

			return $query;
		}

		/**
		 * Surcharge de la méthode _queryBase afin d'ajouter les champs virtuels
		 * manquants.
		 *
		 * @param array $keys
		 * @param array $params
		 * @return array
		 */
		protected function _queryBase( $keys, array $params ) {
			$Controller = $this->_Collection->getController();

			$Controller->Personne->virtualFields['situation'] = $Controller->{$params['modelRechercheName']}->vfPersonneSituation;

			return parent::_queryBase( $keys, $params );
		}
	}
?>