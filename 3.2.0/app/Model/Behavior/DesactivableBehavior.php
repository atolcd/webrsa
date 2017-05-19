<?php
	/**
	 * Code source de la classe DesactivableBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * La classe DesactivableBehavior ...
	 *
	 * @package app.Model.Behavior
	 */
	class DesactivableBehavior extends ModelBehavior
	{
		public $settings = array();

		/**
		 * Valeurs de configuration par défaut.
		 *
		 * @var type
		 */
		protected $_defaults = array(
			'fieldName' => 'actif',
			'true' => '1',
			'false' => '0'
		);

		/**
		 * Configuration du behavior.
		 *
		 * @param Model $model
		 * @param array $config
		 */
		public function setup(Model $model, $config = array()) {
			parent::setup($model, $config);

			if(false === isset($this->settings[$model->alias])) {
				$this->settings[$model->alias] = $config + $this->_defaults;
			}
		}

		/**
		 * Retourne les résultats du find qui peuvent être utilisés pour du
		 * traitement.
		 *
		 * @param Model $model
		 * @param string $type
		 * @param array $query
		 * @return array|null
		 */
		public function findForTraitement( Model $model, $type = 'first', $query = array() ) {
			$query += array( 'contain' => false );

			$query['conditions'][] = array(
				"{$model->alias}.{$this->settings[$model->alias]['fieldName']}" => $this->settings[$model->alias]['true']
			);

			return $model->find( $type, $query );
		}

		/**
		 * Retourne les résultats du find qui peuvent être utilisés dans des
		 * filtres de moteurs de recherche.
		 *
		 * @param Model $model
		 * @param string $type
		 * @param array $query
		 * @return array|null
		 */
		public function findForRecherche( Model $model, $type = 'first', $query = array() ) {
			$query += array( 'contain' => false );

			return $model->find( $type, $query );
		}

		/**
		 *
		 * @param Model $model
		 * @param array $params
		 * @return array
		 */
		protected function _completeOptionsParams( Model $model, array $params = array() ) {
			$params = Hash::normalize( $params );

			foreach( array_keys( $params ) as $fieldName ) {
				$params[$fieldName] = (array)$params[$fieldName]
					+ array(
						'value_path' => $fieldName,
						'options_path' => $fieldName,
						'primaryKey' => "{$model->alias}.{$model->primaryKey}",
						'displayField' => "{$model->alias}.{$model->displayField}",
						'prefix' => false,
						'contain' => false,
						'joins' => array()
					);
			}

			return $params;
		}

		/**
		 *
		 * @param Model $model
		 * @param array $options
		 * @param array $data
		 * @param array $params
		 * @return array
		 */
		public function completeOptions( Model $model, array $options = array(), array $data = array(), array $params = array() ) {
			$params = $this->_completeOptionsParams( $model, $params );

			foreach( $params as $localParams ) {
				$isArray = 0 !== preg_match( '/^([^\.]+)\.(\1)$/', $localParams['value_path'] );
				if( false === $isArray ) {
					$dataValue = (string)Hash::get( $data, $localParams['value_path'] );
				}
				else {
					$modelName = preg_replace( '/^([^\.]+)\.(\1)$/', '\1', $localParams['value_path'] );
					$dataValue = Hash::extract( $data, "{$modelName}.{n}.id" );
				}

				if( '' !== $dataValue && array() !== $dataValue ) {
					$optionsValues = Hash::get( $options, $localParams['options_path'] );
					$found = false === $isArray
						? true === isset( $optionsValues[$dataValue] )
						: $dataValue === array_intersect($dataValue, array_keys($optionsValues));

					if( false === $found ) {
						$fields = array_intersect_key( $localParams, array( 'primaryKey' => null, 'displayField' => null, 'prefix' => null ) );
						if(false === $fields['prefix']) {
							unset($fields['prefix']);
						}

						$query = array(
							'fields' => $fields,
							'contain' => $localParams['contain'],
							'joins' => $localParams['joins'],
							'conditions' => array(
								"{$model->alias}.{$model->primaryKey}" => suffix(
									array_merge(
										array_keys( $optionsValues ),
										false === $isArray
											? array( $dataValue )
											: $dataValue
									)
								)
							)
						);
						$results = $model->find( 'all', $query );

						if( false === $localParams['prefix'] ) {
							$keyPath = "{n}.{$localParams['primaryKey']}";
						}
						else {
							$keyPath = array( '%d_%d', "{n}.{$localParams['prefix']}", "{n}.{$localParams['primaryKey']}" );
						}

						$options = Hash::remove( $options, $localParams['options_path'] );
						$options = Hash::insert(
							$options,
							$localParams['options_path'],
							Hash::combine(
								$results,
								$keyPath,
								"{n}.{$localParams['displayField']}"
							)
						);
					}
				}
			}

			return $options;
		}
	}
?>