<?php
	/**
	 * Code source de la classe WebrsaCohortesPlanpauvreteorientationsComponent.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesPlanpauvreteorientationsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesPlanpauvreteorientationsComponent extends WebrsaAbstractCohortesComponent
	{
		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params = array() ) {
			$Controller = $this->_Collection->getController();

			$Controller->loadModel('WebrsaOptionTag');
			$options = $Controller->WebrsaOptionTag->optionsEnums( parent::_optionsEnums( $params ) );

			if( !isset( $Controller->Nonoriente66 ) ) {
				$Controller->loadModel( 'Nonoriente66' );
			}
			$options = array_merge(
				$options,
				$Controller->Nonoriente66->enums()
			);

			//$Controller->loadModel('Zonegeographique');

			return $options;
		}

		/**
		 * Retourne les options stockées liées à des enregistrements en base de
		 * données, ne dépendant pas de l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsRecords( array $params = array() ) {
			$options = parent::_optionsRecords($params);

			$Controller = $this->_Collection->getController();
			if( !isset( $Controller->Nonoriente66 ) ) {
				$Controller->loadModel( 'Nonoriente66' );
			}

			return $options;
		}

		/**
		 * Retourne les noms des modèles dont des enregistrements seront mis en
		 * cache après l'appel à la méthode _optionsRecords() afin que la clé de
		 * cache générée par la méthode _options() se trouve associée dans
		 * ModelCache.
		 *
		 * @see _optionsRecords(), _options()
		 *
		 * @return array
		 */
		protected function _optionsRecordsModels( array $params ) {
			$result = array_merge(
				parent::_optionsRecordsModels( $params ),
				array(
					'Structurereferente',
					'Referent',
				)
			);

			return $result;
		}

		/**
		 * Surcharge de la méthode afterSearch pour du code spécifique
		 *
		 * @param type $params
		 * @param type $results
		 * @return array
		 */
		public function afterSearch( array $params, array $results ) {
			$results = parent::afterSearch( $params, $results );
			$Nonoriente66 = ClassRegistry::init( 'Nonoriente66' );

			foreach ($results as $key => $result) {
				$historique = $Nonoriente66->find (
					'first',
					array (
						'recurssive' => -1,
						'conditions' => array (
							'historiqueetatpe_id' => $result['Historiqueetatpe']['id']
						)
					)
				);

				if (isset ($historique['Nonoriente66'])) {
					$results[$key]['Historiqueetatpe']['alert_exists'] = 'ALERTE';
				}
			}

			return $results;
		}
	}
?>