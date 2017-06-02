<?php
	/**
	 * Code source de la classe TypeableBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * La classe TypeableBehavior ...
	 *
	 * @package app.Model.Behavior
	 */
    class TypeableBehavior extends ModelBehavior
	{
		/**
		* Settings
		*/

		public $settings = array();

		/**
		*
		*/

		protected function _xschema( Model $model, $settings ) {
			$schema = $model->schema( null, true );
			if( !empty( $settings ) ) {
				foreach( $settings as $field => $params ) {
					if( !is_array( $params ) ) {
						$params = array( 'type' => $params );
					}
					$settings[$field] = $params;
				}
			}

			$xschema = Set::merge( $schema, $settings );
			foreach( $xschema as $key => $values ) {
				$defaults = Configure::read( "Typeable.{$values['type']}" );
				$xschema[$key] = Set::merge( $defaults, $values );
			}

			return $xschema;
		}

		/**
		* FIXME: dans AppBehavior
		*/

		public function setup( Model $model, $settings = array() ) {
			if (!isset($this->settings[$model->alias])) {
				$this->settings[$model->alias] = array();
			}

			$settings = Set::normalize( $settings );
			$settings = array_merge( $this->settings[$model->alias], (array) $settings );
			$this->settings[$model->alias]['settings'] = $settings;
			$this->settings[$model->alias]['xschema'] = $this->_xschema( $model, $settings );
// 			debug( $this->settings[$model->alias]['xschema'] );
		}

		/**
		*
		*/

		public function getTypeInfos( Model $model, $field ) {
			return Set::extract( $this->settings[$model->alias]['xschema'], $field );
		}
	}
?>