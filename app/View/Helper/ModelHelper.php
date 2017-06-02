<?php
	/**
	 * Fichier source de la classe ModelHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Permet la remplacement dans une chaîne de caractère de valeurs venant
	 * d'une array.
	 *
	 * Exemple:
	 * <pre>
	 *	$data = array( 'User' => array( 'username' => 'BigFoot' ) );
	 *  $result = dataTranslate( $data, 'I am #User.username#.' );
	 *  // $result contient 'I am BigFoot.'
	 * </pre>
	 *
	 * TODO: dans une classe (AppHelper ?), un meilleur nom ?
	 *
	 * @param array $data
	 * @param string $string
	 * @return string
	 */
	function dataTranslate( $data, $string ) {
		if( preg_match_all( '/#(?<!\w)((\w+)(\.|\.[0-9]+\.))+(\w+)#/', $string, $matches, PREG_SET_ORDER ) ) {
			$matches = Set::extract( $matches, '{n}.0' );

			foreach( $matches as $match ) {
				$modelField = str_replace( '#', '', $match );
				if( Set::check( $data, $modelField ) ) {
					$value = Set::classicExtract( $data, $modelField );
					$value = ( is_bool( $value ) ? ( $value ? 1 : 0 ) : $value );
					$string = str_replace( $match, $value, $string );
				}
			}
		}

		return $string;
	}

	/**
	 * La classe ModelHelper fournit des fonctions d'accès à un cache d'informations
	 * concernant les modèles, ainsi que des fonctions de manipulation de données
	 * liées aux modèles.
	 *
	 * Cette classe est abstraite car uniquement destinée à être sous-classée.
	 *
	 * @package app.View.Helper
	 */
	abstract class ModelHelper extends AppHelper
	{

		/**
		 * Cache pour les informations des modèles.
		 *
		 * @var array
		 */
		protected $_modelInfos = array();

		/**
		 * Retourne un array contenant les informations suivantes concernant le
		 * modèle: primaryKey, displayField, schema.
		 *
		 * @param string $modelName
		 * @return array
		 */
		protected function _modelInfos( $modelName ) {
			if( !isset( $this->_modelInfos[$modelName] ) ) {
				$cacheKey = $this->_cacheKey( $modelName );

				$this->_modelInfos[$modelName] = Cache::read( $cacheKey );

				if( empty( $this->_modelInfos[$modelName] ) ) {
					// FIXME ?
					if( !ClassRegistry::isKeySet( $modelName ) ) {
						return array();
					}

					$model = ClassRegistry::init( $modelName );
					$this->_modelInfos[$modelName] = array(
						'primaryKey' => $model->primaryKey,
						'displayField' => $model->displayField,
						'schema' => $model->schema(),
					);

					// MySQL enum ? dans un projet qui utilise PostgreSQL!
//					foreach( $this->_modelInfos[$modelName]['schema'] as $field => $infos ) {
//						if( strstr( $infos['type'], 'enum(' ) ) {
//							$this->_modelInfos[$modelName]['schema'][$field]['type'] = 'string';
//							if( preg_match_all( "/'([^']+)'/", $infos['type'], $matches ) ) {
//								$this->_modelInfos[$modelName]['schema'][$field]['options'] = $matches[1];
//							}
//						}
//					}

					Cache::write( $cacheKey, $this->_modelInfos[$modelName] );
				}
			}

			return $this->_modelInfos[$modelName];
		}

		/**
		 * Retourne le nom du champ qui sert de clé primaire au modèle.
		 *
		 * @param string $modelName
		 * @return string
		 */
		public function primaryKey( $modelName ) {
			$modelInfos = $this->_modelInfos( $modelName );
			if( !isset( $modelInfos['primaryKey'] ) ) {
				return false;
			}
			else {
				return $modelInfos['primaryKey'];
			}
		}

		/**
		 * Retourne le nom du champ qui sert de champ d'affichage au modèle.
		 *
		 * @param string $modelName
		 * @return string
		 */
		public function displayField( $modelName ) {
			$modelInfos = $this->_modelInfos( $modelName );
			return $modelInfos['displayField'];
		}

		/**
		 * Retourne le type du champ d'un modèle.
		 * Si $fieldName est null, un $modelName du type User.username est attendu.
		 *
		 * @param string $modelName
		 * @param string $fieldName
		 * @return string
		 */
		public function type( $modelName, $fieldName = null ) {
			if( is_null( $fieldName ) ) {
				list( $modelName, $fieldName ) = Xinflector::modelField( $modelName );
			}
			$modelInfos = $this->_modelInfos( $modelName );
			return $modelInfos['schema'][$fieldName]['type'];
		}

		/**
		 * Retourne la partie du schéma concernant un champ donné.
		 *
		 * @param string $path ie. User.username, User.0.id
		 * @return array ie.
		 * <pre>
		 * 	array(
		 * 		'type' => 'integer',
		 * 		'null' => false,
		 * 		'default' => null,
		 * 		'length' => 11,
		 * 		'key' => 'primary'
		 * 	)
		 * </pre>
		 */
		protected function _typeInfos( $path ) {
			list( $modelName, $fieldName ) = Xinflector::modelField( $path );
			$modelInfos = $this->_modelInfos( $modelName );
			return Set::extract( $modelInfos, "schema.{$fieldName}" );
		}
	}
?>