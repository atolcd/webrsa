<?php
	/**
	 * Code source de la classe ValidateAllowEmptyUtility.
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ValidateAllowEmptyUtility ...
	 *
	 * @package app.Utility
	 */
	abstract class ValidateAllowEmptyUtility
	{
		/**
		 * Clef de conf contenant la rêgle défini par l'utilisateur sur l'attribu AllowEmpty d'un champ
		 * 
		 * @var string
		 */
		public static $confKey = 'ValidateAllowEmpty';
		
		/**
		 * Clef de conf/cache pour garder en mémoire les valeurs notEmpty d'un champs
		 * 
		 * @var string
		 */
		protected static $_memKey = '_ValidationConfiguredAllowEmptyFields';

		/**
		 * Renvoi la clef de config pour un path donnée
		 * 
		 * @param string $path
		 * @return string
		 */
		public static function configureKey( $path ) {
			list($modelName, $fieldName) = model_field($path);
			return self::$confKey.".{$modelName}.{$fieldName}";
		}
		
		/**
		 * Si le cache n'existe pas pour ce modele, on calcule si un champ est obligatoire et on garde le tout en cache.
		 * Attention, restitue le validate en cache.
		 * 
		 * @param Model $Model
		 */
		public static function initialize( Model $Model ) {
			if ( is_array(self::_getCacheByModelName( $Model->alias )) === false ) {
				self::_applyConfiguredAllowEmpty( $Model );
				self::_writeRequiredFieldsConf( $Model );
			}
			
			$Model->validate = self::_getCacheByModelName( $Model->alias, 'validate' );
		}
		
		/**
		 * Pour utilisation dans une vue lors de l'emploi de Form->input() dans le label
		 * ex: $this->Form->input('Monmodel.monfield', array('label' => ValidateAllowEmptyUtility::label('Monmodel.monfield'))
		 * 
		 * @param type $path
		 * @return string
		 */
		public static function label( $path, $domain = null, $args = null ) {
			if ($args !== null && !is_array($args)) {
				$args = array_slice(func_get_args(), 3);
			}
			
			$traduction = $domain === null ? __m($path, $args) : __d($domain, $path, $args);
			$cache = self::_getCacheByPath($path);
			
			if ( $cache ) {
				return h($traduction) . ' ' . REQUIRED_MARK;
			}
			
			return h($traduction);
		}
		
		/**
		 * Permet de savoir si un champ est required
		 * 
		 * @param string $path
		 * @return boolean
		 */
		public static function isRequired( $path ) {
			return self::_getCacheByPath( $path );
		}
		
		/**
		 * Renvoi toute la conf concernant cet utilitaire
		 * 
		 * @return array
		 */
		public static function allConf() {
			return (array)Configure::read( self::$confKey );
		}
		
		/**
		 * Permet de récupérer la liste des champs configuré d'un modele, utile pour la vérification de l'application.
		 * 
		 * @param Model $Model
		 * @return array
		 */
		public static function configuredFields( Model $Model ) {
			$results = array();
			
			foreach ( $Model->configuredAllowEmptyFields as $field ) {
				$results[] = array( self::$confKey.".{$Model->alias}.$field" => array( 'rule' => 'isarray', 'allowEmpty' => true ) );
			}
			
			return $results;
		}

		/**
		 * Vérifie si un champ est requis en fonction de la conf et des rêgles de validation.
		 * 
		 * @param Model $Model
		 * @param string $fieldName
		 * @return boolean
		 */
		protected static function _required( Model $Model, $fieldName ) {
			$allowEmpty = true;
			
			$cache = self::_getCacheByModelName( $Model->alias );
			
			if ( !isset($cache[$fieldName]) ) {
				if ( isset( $Model->validate[$fieldName] ) ) {
					foreach ( $Model->validate[$fieldName] as $key => $value ) {
						if ( $key === 'notEmpty' || (is_array($value) && self::_getRuleName( $value ) === 'notEmpty') ) {
							$allowEmpty = false;
						}
						elseif ( isset($Model->validate[$fieldName][$key]['allowEmpty']) ) {
							$allowEmpty = $Model->validate[$fieldName][$key]['allowEmpty'];
						}

						if ( $allowEmpty === false ) {
							return true;
						}
					}
				}
			}
			
			return !$allowEmpty;
		}
		
		/**
		 * Applique la conf sur les champs du modele.
		 * 
		 * @param Model $Model
		 */
		protected static function _applyConfiguredAllowEmpty( Model $Model ) {
			foreach ( $Model->configuredAllowEmptyFields as $fieldName ) {
				$configuredRequired = Configure::read( self::$confKey.".{$Model->alias}.{$fieldName}" );

				if ( $configuredRequired !== null ) {
					$allowEmpty = (boolean)$configuredRequired;
					
					// Si des rêgles de validation existent déjà pour ce champ, on modifie les valeurs allowEmpty
					if ( isset( $Model->validate[$fieldName] ) ) {
						foreach ( $Model->validate[$fieldName] as $key => $value ) {
							// Si la conf autorise le champ vide, il faut supprimer la rêgle notEmpty
							if ( $allowEmpty && ( $key === 'notEmpty' || self::_getRuleName( $value ) === 'notEmpty' ) ) {
								unset( $Model->validate[$fieldName][$key] );
							}
							else {
								// Les autres rêgles doivent être en allowEmpty = la conf
								$Model->validate[$fieldName][$key]['allowEmpty'] = $allowEmpty;
							}
						}
					}
					
					// Si aucune rêgle de validation n'existe pour ce champ et que la conf ne permet pas qu'il soit vide, on ajoute un notEmpty
					elseif ( $allowEmpty === false ) {
						$Model->validate[$fieldName]['notEmpty'] = array(
							'rule' => 'notEmpty',
							'message' => 'Champ obligatoire'
						);
					}
				}
			}
		}
		
		/**
		 * Garde en mémoire les champs requis
		 * 
		 * @param Model $Model
		 */
		protected static function _writeRequiredFieldsConf( Model $Model ) {
			$key = self::$_memKey . '.' . $Model->alias;
			
			if ( is_array(self::_getCache($key)) === false ) {
				$confData = array();
				foreach ( array_keys($Model->validate) as $fieldName ) {
					$confData[$fieldName] = self::_required($Model, $fieldName);
				}
				
				Cache::write($key.'.validate', $Model->validate);
				Configure::write($key.'.validate', $Model->validate);
				
				Cache::write($key.'.required', $confData);
				Configure::write($key.'.required', $confData);
			}
		}
		
		/**
		 * Récupère le nom de la validation d'un champ
		 * ex: ValidateAllowEmptyUtility::getRuleName( $Model->validate['monchamp'][0] ) renverra inList ou notEmpty.
		 * 
		 * @param array $rule
		 * @return string
		 */
		protected static function _getRuleName( array $rule ) {
			$ruleName = '';
			if ( isset($rule['rule']) ) {
				if ( is_string($rule['rule']) ) {
					$ruleName = $rule['rule'];
				}
				elseif ( isset($rule['rule'][0]) && is_string($rule['rule'][0]) ) {
					$ruleName = $rule['rule'][0];
				}
			}
			
			return $ruleName;
		}
	
		/**
		 * Récupère le cache en fonction d'un path
		 * 
		 * @param string $path
		 * @return boolean
		 */
		protected static function _getCacheByPath( $path ) {
			list($modelName, $fieldName) = model_field($path);
			$key = self::$_memKey . '.' . $modelName . '.' . 'required';
			$cache = self::_getCache($key);
			
			if ( isset($cache[$fieldName]) ) {
				return $cache[$fieldName];
			}
			
			return false;
		}
		
		/**
		 * Récupère le cache en fonction du nom de modele
		 * 
		 * @param string $modelName
		 * @param string $key
		 * @return array
		 */
		protected static function _getCacheByModelName( $modelName, $key = 'required' ) {
			return self::_getCache(self::$_memKey . '.' . $modelName . '.' . $key);
		}
		
		/**
		 * Récupère le cache (court ou long terme selon celui disponnible)
		 * 
		 * @param type $key
		 * @return type
		 */
		protected static function _getCache( $key ) {
			$cache = Configure::read($key);
			if ( $cache !== null ) {
				return $cache;
			}
			else {
				return Cache::read($key);
			}
		}
	}
?>