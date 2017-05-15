<?php
	/**
	 * Code source de la classe DefaultCsvHelper.
	 *
	 * PHP 5.3
	 *
	 * @package Default
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe DefaultCsvHelper ...
	 *
	 * @package Default
	 * @subpackage View.Helper
	 */
	class DefaultCsvHelper extends AppHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Default.DefaultData',
		);

		/**
		 * Ajout d'un champ d'en-tête avec la traduction des noms des champs en
		 * fonction, soit de la clé "domain" de l'attribut d'un field, soit de la
		 * même clé des paramètres généraux.
		 *
		 * @param array $fields
		 * @param array $params Les paramètres généraux
		 */
		protected function _addHeaderRow( array $fields, array $params ) {
			$row = array();

			foreach( $fields as $path => $attributes ) {
				$domain = ( isset( $attributes['domain'] ) ? $attributes['domain'] : $params['domain'] );
				$row[] = __d( $domain, $path );
			}

			$this->Csv->addRow( $row );
		}

		/**
		 * Retourne la valeur d'une cellule.
		 *
		 * @return string
		 */
		protected function _getBodyCell( array $data, $path, array $types, array $params ) {
			$value = Hash::get( $data, $path );

			$type = Hash::get( $params, 'type' );
			$type = $type === null ? $types[$path] : $type;

			$value = $this->DefaultData->format( $value, $type, Hash::get( $params, 'format' ) );
			if( !in_array( $value, array( null, array() ), true ) && isset( $params['options'] ) ) {
				$value = $this->DefaultData->translateOptions( $value, array( 'options' => &$params['options'] ) );
			}

			if( is_array( $value ) ) {
				$value = implode( $value, ', ' );
			}

			return $value;
		}

		/**
		 * Ajout d'un champ d'enregistrements, suivant le type de champs et la
		 * traducton possible par les options des paramètres généraux ou plus
		 * spécifiquement des attributs des champs.
		 *
		 * @param array $fields
		 * @param array $params Les paramètres généraux
		 */
		protected function _addBodyRow( array $data, array $fields, array $types, array $params ) {
			$row = array();

			foreach( $fields as $path => $innerParams ) {
				$condition = true;
				if( isset( $innerParams['condition'] ) ) {
					$condition = $innerParams['condition'];
					unset( $innerParams['condition'] );

					if( is_string( $condition ) ) {
						$condition = eval( 'return '.DefaultUtility::evaluate( $data, $condition ).';' );
					}
				}

				if( $condition ) {
					list( $modelName, $fieldName ) = model_field( $path );
					$innerParams = (array)$innerParams;
					if( !isset( $innerParams['options'] ) && isset( $params['options'][$modelName][$fieldName] ) ) {
						$innerParams['options'] = $params['options'][$modelName][$fieldName];
					}
					$row[] = $this->_getBodyCell( $data, $path, $types, $innerParams );
				}
			}

			$this->Csv->addRow( $row );
		}

		/**
		 * Effectue le rendu CSV.
		 *
		 * @param array $datas
		 * @param array $fields
		 * @param array $params
		 * @return string
		 */
		public function render( array $datas, array $fields, array $params = array() ) {
			if( empty( $fields ) ) {
				return null;
			}

			$default = array(
				'domain' => Inflector::underscore( $this->request->params['controller'] ),
				'options' => array(),
				'headers' => true,
				'filename' => sprintf( "%s-%s-%s.csv", $this->request->params['controller'], $this->request->params['action'], date( 'Ymd-His' ) ),
			);
			$params += $default;

			$this->Csv->preserveLeadingZerosInExcel = true;

			$fields = Hash::normalize( $fields );

			// Recherche des types de données
			$types = array();
			foreach( $fields as $path => $attributes ) {
				$types[$path] = ( isset( $attributes['type'] ) ? $attributes['type'] : $this->DefaultData->type( $path ) );
			}

			// En-têtes du tableau
			if( $params['headers'] ) {
				$this->_addHeaderRow( $fields, $params );
			}

			// Corps du tableau
			if( !empty( $datas ) ) {
				foreach( $datas as $data ) {
					$this->_addBodyRow( $data, $fields, $types, $params );
				}
			}

			return $this->Csv->render( $params['filename'] );
		}
	}
?>