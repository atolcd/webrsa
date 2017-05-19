<?php
	/**
	 * Code source de la classe FormattableBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe FormattableBehavior ...
	 *
	 * @package app.Model.Behavior
	 */
	class FormattableBehavior extends ModelBehavior
	{
		/**
		* Settings
		*/

		public $settings = array();

		/**
		* Default settings
		*/

		public $defaultSettings = array(
			'null' => true,
			'trim' => true,
			'phone' => false,
			'suffix' => false,
			'amount' => false,
		);

		/**
		*
		*/

		public function setup( Model $model, $settings = array() ) {
			$settings = Set::merge( $this->defaultSettings, $settings );

			if (!isset($this->settings[$model->alias])) {
				$this->settings[$model->alias] = array();
			}

			$settings = Set::normalize( $settings );
			$this->settings[$model->alias] = array_merge(
				$this->settings[$model->alias],
				(array) $settings
			);
		}

		/**
		* Get fields affected by a given formatting option.
		*/

		protected function _fields( Model $model, $type, $null = null ) {
			if( Set::check( $this->settings, "{$model->alias}.{$type}" ) ) {
				$fields = Set::classicExtract( $this->settings, "{$model->alias}.{$type}" );

				if( $fields === false ) {
					return array();
				}
				else if( $fields === true ) {
					$fields = array();
					foreach( $model->schema() as $field => $params ) {
						if( is_null( $null ) || ( empty( $params['null'] ) != $null ) ) {
							$fields[] = $field;
						}
					}

					return $fields;
				}
				else {
					$fields = Set::normalize( $fields ); // If not array ?
					return array_keys( $fields );
				}
			}
		}

		/**
		* INFO: true par défaut (pour tous les champs qui peuvent être null -> ?)
		* OK -> 0.Model.id => null if type of value is string and its length is 0
		* TODO: vérifier Model.0.id
		*/

		public function formatNull( Model $model, $datas ) {
			$fields = $this->_fields( $model, 'null', true );
			if( !empty( $fields ) ) {
				$results = Hash::flatten( $datas );

				$fields = '('.implode( '|', $fields ).')';
				foreach( $results as $key => $value ) {
					if( preg_match( "/(?<!\w){$model->alias}(\.|\.[0-9]+\.){$fields}$/", $key ) ) {
						if( is_string( $value ) && ( strlen( $value ) == 0 ) ) {
							$results[$key] = null;
						}
					}
				}
				return Hash::expand( $results );
			}
			return $datas;
		}

		/**
		* OK -> 04 04 04 04 04 -> 0404040404
		* TODO: per-locale ?
		*/

		public function formatPhone( Model $model, $datas ) {
			$fields = $this->_fields( $model, 'phone' );
			if( !empty( $fields ) ) {
				$results = Hash::flatten( $datas );

				$fields = '('.implode( '|', $fields ).')';
				foreach( $results as $key => $value ) {
					if( preg_match( "/(?<!\w){$model->alias}(\.|\.[0-9]+\.){$fields}$/", $key ) ) {
						$results[$key] = preg_replace( '/[ \.\-]/', '', $value );
					}
				}
				return Hash::expand( $results );
			}
			return $datas;
		}

		/**
		* OK -> Precoreorientequipe.structurereferente_id 2_3 -> 3
		*/

		public function formatSuffix( Model $model, $datas ) {
			$fields = $this->_fields( $model, 'suffix' );
			if( !empty( $fields ) ) {
				$results = Hash::flatten( $datas );

				$fields = '('.implode( '|', $fields ).')';
				foreach( $results as $key => $value ) {
					if( preg_match( "/(?<!\w){$model->alias}(\.|\.[0-9]+\.){$fields}$/", $key ) ) {
						$results[$key] = preg_replace( '/^(.*)_([0-9]+)$/', '\2', $value );
					}
				}
				return Hash::expand( $results );
			}
			return $datas;
		}

		/**
		* Trim whitespace from values.
		*/

		public function formatTrim( Model $model, $datas ) {
			$fields = $this->_fields( $model, 'trim' );
			if( !empty( $fields ) ) {
				$results = Hash::flatten( $datas );

				$fields = '('.implode( '|', $fields ).')';
				foreach( $results as $key => $value ) {
					if( preg_match( "/(?<!\w){$model->alias}(\.|\.[0-9]+\.){$fields}$/", $key ) ) {
						$results[$key] = trim( $value );
					}
				}
				return Hash::expand( $results );
			}
			return $datas;
		}

		/**
		* OK -> 30 000,00 => 30000.00
		* TODO: per-locale ?
		* TODO: 10.000,00 ?
		*/

		public function formatAmount( Model $model, $datas ) {
			$fields = $this->_fields( $model, 'amount' );
			if( !empty( $fields ) ) {
				$results = Hash::flatten( $datas );

				$fields = '('.implode( '|', $fields ).')';
				foreach( $results as $key => $value ) {
					if( preg_match( "/(?<!\w){$model->alias}(\.|\.[0-9]+\.){$fields}$/", $key ) ) {
						$value = str_replace( ' ', '', $value );
						$results[$key] = preg_replace( '/^(.*),([0-9]+)$/', '\1.\2', $value );
					}
				}
				return Hash::expand( $results );
			}
			return $datas;
		}


		/**
		* Format data according to rules defined in the settings for the
		* current model
		*/

		public function doFormatting( Model $model, $data ) {
			$data = $this->formatTrim( $model, $data );
			$data = $this->formatAmount( $model, $data );
			$data = $this->formatPhone( $model, $data );
			$data = $this->formatSuffix( $model, $data );
			$data = $this->formatNull( $model, $data );
			return $data;
		}

		/**
		* TODO: vérifier que qu'on n'ait pas besoin de parent::beforeValidate
		*/

		public function beforeValidate( Model $model ) {
			$model->data = $this->doFormatting( $model, $model->data );
			return true;
		}

		/**
		*
		*/

		public function beforeSave( Model $model ) {
			$model->data = $this->doFormatting( $model, $model->data );
			return true;
		}
	}
?>