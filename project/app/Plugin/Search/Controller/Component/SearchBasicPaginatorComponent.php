<?php
	/**
	 * Code source de la classe SearchBasicPaginatorComponent.
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
	class SearchBasicPaginatorComponent extends PaginatorComponent
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
		public function validateSort( Model $object, array $options, array $whitelist = array( ) ) {
			if (empty($options['order']) && is_array($object->order)) {
				$options['order'] = $object->order;
			}

			if( isset( $options['sort'] ) ) {
				$direction = null;
				if( isset( $options['direction'] ) ) {
					$direction = strtolower( $options['direction'] );
				}
				if (!in_array($direction, array('asc', 'desc'))) {
					$direction = 'asc';
				}
				$options['order'] = array( $options['sort'] => $direction );
			}

			if( !empty( $whitelist ) && isset( $options['order'] ) && is_array( $options['order'] ) ) {
				$field = key( $options['order'] );
				$inWhitelist = in_array($field, $whitelist, true);
				if (!$inWhitelist) {

					// Si $field n'est pas un champ de la table, cela peut être un alias.
					$alias = str_replace('.', '__', $field);
					$isAlias = false;
					foreach ($whitelist as $value) {
						if (preg_match('#AS "'.$alias.'"#', $value)) {
							$isAlias = true;
							break;
						}
					}

					if ($isAlias) {
						$options['order'] = array ($alias => $options['order'][$field]);
					}
					else {
						$options['order'] = null;
					}
				}
				return $options;
			}

			if( !empty( $options['order'] ) && is_array( $options['order'] ) ) {
				$order = array( );
				foreach( $options['order'] as $key => $value ) {
					if (is_int($key)) {
						$key = $value;
						$value = 'asc';
					}
					$field = $key;
					$alias = $object->alias;
					if( strpos( $key, '.' ) !== false ) {
						list($alias, $field) = explode( '.', $key );
					}

					$correctAlias = ($object->alias === $alias);

					if ($correctAlias && $object->hasField($field)) {
						$order[$object->alias . '.' . $field] = $value;
					} elseif ($correctAlias && $object->hasField($key, true)) {
						$order[$field] = $value;
					} elseif (isset($object->{$alias}) && $object->{$alias}->hasField($field, true)) {
						$order[$alias . '.' . $field] = $value;
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