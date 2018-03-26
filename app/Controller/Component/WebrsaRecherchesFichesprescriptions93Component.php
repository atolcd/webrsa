<?php
	/**
	 * Code source de la classe WebrsaRecherchesFichesprescriptions93Component.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesFichesprescriptions93Component ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesFichesprescriptions93Component extends WebrsaAbstractRecherchesComponent
	{
		public $components = array( 'Allocataires', 'WebrsaSearchesAccesses' );

		/**
		 * Retourne la valeur de modelName sur lequel faire la pagination,
		 * Ficheprescription93 ou Personne suivant la valeur du filtre
		 * Search.Ficheprescription93.exists.
		 *
		 * @return string
		 */
		protected function _modelName() {
			$Controller = $this->_Collection->getController();

			if( Hash::get( $Controller->request->data, 'Search.Ficheprescription93.exists' ) ) {
				return 'Ficheprescription93';
			}

			return 'Personne';
		}

		protected function _params( array $params = array() ) {
			$defaults = array(
				'modelName' => $this->_modelName(),
				'structurereferente_id' => 'Referent.structurereferente_id'
			);

			return $this->WebrsaSearchesAccesses->completeParams( parent::_params( $params + $defaults ) );
		}

		protected function _queryBase( $keys, array $params ) {
			$Controller = $this->_Collection->getController();
			$query = parent::_queryBase( $keys, $params );

			$modelName = $this->_modelName();

			// Optimisation: on attaque fichesprescriptions93 en premier lieu
			if( $modelName === 'Ficheprescription93' ) {
				foreach( $query['joins'] as $i => $join ) {
					if( $join['alias'] === 'Ficheprescription93' ) {
						unset( $query['joins'][$i] );
						array_unshift( $query['joins'], $Controller->Ficheprescription93->join( 'Personne', array( 'type' => 'INNER' ) ) );
					}
				}
			}

			return $query;
		}

		protected function _optionsEnums( array $params ) {
			$Controller = $this->_Collection->getController();

			return Hash::merge(
				parent::_optionsEnums( $params ),
				$Controller->Ficheprescription93->options( array( 'allocataire' => false, 'find' => false, 'autre' => false, 'enums' => true ) )
			);
		}

		protected function _optionsRecords( array $params ) {
			$Controller = $this->_Collection->getController();

			return Hash::merge(
				parent::_optionsRecords( $params ),
				$Controller->Ficheprescription93->options( array( 'allocataire' => false, 'find' => true, 'autre' => true, 'enums' => false ) )
			);
		}

		protected function _optionsRecordsModels( array $params ) {
			return array_merge(
				parent::_optionsRecordsModels( $params ),
				array( 'Thematiquefp93', 'Modtransmfp93', 'Documentbeneffp93', 'Motifnonretenuefp93', 'Motifnonintegrationfp93', 'Documentbeneffp93' )
			);
		}

		/**
		 * Retourne les options stockées en session, liées à l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsSession( array $params ) {
			$Controller = $this->_Collection->getController();
			$departement = (int)Configure::read( 'Cg.departement' );

			$options = parent::_optionsSession( $params );

			if( $departement === 93 ) {
				$options = Hash::merge(
					$options,
					$this->Allocataires->optionsSessionCommunautesr( 'Ficheprescription93' )
				);
			}

			return $options;
		}

		/**
		 * Complète le querydata afin de pouvoir calculer les droits d'accès aux
		 * enregistrements.
		 *
		 * @param array $params Les paramètres de la recherche
		 * @param array $query Le querydata à compléter
		 * @return array
		 */
		public function beforeSearch( array $params, array $query ) {
			return $this->WebrsaSearchesAccesses->completeQuery( $params, $query );
		}

		/**
		 * Complète les results avec les droits d'accès aux enregistrements.
		 *
		 * @param array $params Les paramètres de la recherche
		 * @param array $results Les enregistrements à compléter
		 * @return array
		 */
		public function afterSearch( array $params, array $results ) {
			return $this->WebrsaSearchesAccesses->completeResults( $params, $results );
		}
	}
?>