<?php
	/**
	 * Code source de la classe AjaxComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe AjaxComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class AjaxComponent extends Component
	{

		/**
		 * Permet d'exporter le contenu de la variable dans le log de debug.
		 *
		 * @param mixed $variable
		 */
		public function debug( $variable ) {
			$this->log( var_export( $variable, true ), LOG_DEBUG );
		}

		/**
		 * Permet de préfixer ce qui sera retourné par l'appel Ajax s'il y a lieu.
		 *
		 * @todo Dans le parent
		 *
		 * @param string $prefix
		 * @param array $result
		 * @return array
		 */
		public function prefixAjaxResult( $prefix, array $result ) {
			if( !empty( $result ) && !empty( $prefix ) ) {
				$return = array();
				foreach( $result as $key => $params ) {
					if( isset( $params['id'] ) ) {
						$params['id'] = "{$prefix}{$params['id']}";
					}
					$return["{$prefix}{$key}"] = $params;
				}
			}
			else {
				$return = $result;
			}

			return $return;
		}

		/**
		 * Permet d'enlever le préfixe s'il y a lieu.
		 *
		 * @todo Dans le parent
		 *
		 * @param array $request
		 * @return array
		 */
		public function unprefixAjaxRequest( array $request ) {
			$prefix = Hash::get( $request, 'prefix' );

			if( !empty( $request ) && !empty( $prefix ) ) {
				$prefixed = (array)Hash::get( $request, $prefix );
				unset( $request[$prefix] );

				$request['Target']['domId'] = preg_replace( "/^{$prefix}/", "", Hash::get( $request, 'Target.domId' ) );
				$request['Target']['name'] = preg_replace( "/^data\[{$prefix}\]/", "data", Hash::get( $request, 'Target.name' ) );
				$request['Target']['path'] = preg_replace( '/^data\[(.*)\]$/', '\1', str_replace( '][', '.', $request['Target']['name'] ) );;

				$request = Hash::merge( $prefixed, $request );
			}

			return $request;
		}


		/**
		 * Retourne une liste d'enregistrements, sans le nom du modèle, triés par
		 * displayField et complétés par la query passée en paramètres.
		 *
		 * @todo Dans le parent
		 *
		 * @param string $modelName
		 * @param array $query
		 * @return array
		 */
		public function ajaxOptions( $modelName, array $query = array() ) {
			$Model = ClassRegistry::init( $modelName );

			$query = Hash::merge(
				$query,
				array(
					'fields' => array(
						"{$Model->alias}.{$Model->primaryKey}",
						"{$Model->alias}.{$Model->displayField}"
					),
					'conditions' => array()
				)
			);
			$query['group'] = $query['fields'];
			$query['order'] = "{$Model->alias}.{$Model->displayField}";

			$results = $Model->find( 'all', $query );

			return Hash::extract( (array)$results, "{n}.{$Model->alias}" );
		}

		/**
		 * Traite l'événement Ajax.
		 *
		 * @param array $data
		 * @return array
		 * @throws InternalErrorException
		 */
		public function ajaxAction( array $data ) {
			$type = Hash::get( $data, 'Event.type' );
			$method_name = Inflector::camelize( "ajax_on_{$type}" );

			if( $type == 'dataavailable' ) {
				$return = $this->ajaxOnLoad( $data );
			}
			else if( method_exists( $this, $method_name ) ) {
				$return = $this->{$method_name}( $data );
			}
			else {
				$msgstr = 'Unhandeled Ajax event "%s"';
				throw new InternalErrorException( sprintf( $msgstr, $type ) );
			}

			return $return;
		}
	}
?>