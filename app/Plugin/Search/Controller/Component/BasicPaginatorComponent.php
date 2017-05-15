<?php
	/**
	 * Code source de la classe BasicPaginatorComponent.
	 *
	 * PHP 5.3
	 *
	 * CakePHP 2.2.2
	 *
	 * @package Search
	 * @subpackage Controller.Component
	 */
	App::uses( 'PaginatorComponent', 'Controller/Component' );

	/**
	 * TODO: par Kévin
	 *
	 * @package Search
	 * @subpackage Controller.Component
	 */
	class BasicPaginatorComponent extends PaginatorComponent
	{

		/**
		 * Surcharge de la méthode validateSort pour que les champs virtuels soient pris en compte pour le tri
		 *
		 * @see CakePHP 2.2.2
		 *
		 * @param Model $object
		 * @param array $options
		 * @param array $whitelist
		 * @return array
		 */
		public function validateSort( $object, $options, $whitelist = array( ) ) {
			if( isset( $options['sort'] ) ) {
				$direction = null;
				if( isset( $options['direction'] ) ) {
					$direction = strtolower( $options['direction'] );
				}
				if( $direction != 'asc' && $direction != 'desc' ) {
					$direction = 'asc';
				}
				$options['order'] = array( $options['sort'] => $direction );
			}

			if( !empty( $whitelist ) && isset( $options['order'] ) && is_array( $options['order'] ) ) {
				$field = key( $options['order'] );
				if( !in_array( $field, $whitelist ) ) {
					$options['order'] = null;
				}
			}

			if( !empty( $options['order'] ) && is_array( $options['order'] ) ) {
				$order = array( );
				foreach( $options['order'] as $key => $value ) {
					$field = $key;
					$alias = $object->alias;
					if( strpos( $key, '.' ) !== false ) {
						list($alias, $field) = explode( '.', $key );
					}

					if( $object->hasField( $field ) ) {
						$order[$alias.'.'.$field] = $value;
					}
					elseif( $object->hasField( $key, true ) ) {
						$order[$field] = $value;
					}
					elseif( isset( $object->{$alias} ) && $object->{$alias}->hasField( $field, true ) ) {
						$order[$alias.'.'.$field] = $value;
					}
					// Début modification
					else {
						$order[$key] = $value;
					}
					// Fin modification
				}
				$options['order'] = $order;
			}

			return $options;
		}
	}
?>