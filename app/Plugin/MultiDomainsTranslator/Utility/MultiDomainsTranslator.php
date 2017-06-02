<?php
	/**
	 * Code source de la classe MultiDomainsTranslator.
	 *
	 * @package MultiDomainsTranslator
	 * @subpackage Utility
	 */

	/**
	 * La classe MultiDomainsTranslator choisi automatiquement le domain à utiliser pour une traduction.
	 * MultiDomainsTranslator::translate('A traduire') sera automatiquement traduit par un fichier .po existant
	 * en fonction du controller ou du controller + action.
	 * 
	 * On peux aussi faire appel à un fichier .po portant le nom du model contenu dans la chaine à traduire :
	 * MultiDomainsTranslator::translate('Monmodel.field')
	 *
	 * @package MultiDomainsTranslator
	 * @subpackage Utility
	 */
	abstract class MultiDomainsTranslator
	{
		/**
		 * Liste des domains à utiliser dans le cadre de cette URL
		 * @var array 
		 */
		protected static $_contexts = null;

		/**
		 * Liste des traductions déjà effectués
		 * @var array 
		 */
		protected static $_map = array();
		
		/**
		 * Domaines additionnels
		 * @var array
		 */
		protected static $_domains = array();
		
		/**
		 * Permet d'obtenir la traduction d'une phrase de façon automatique.
		 * 
		 * @param string $singular Mot/Phrase à traduire
		 * @param array
		 * @return string Mot/Phrase traduite
		 */
		public static function translate( $singular, $args = null ) {
			if( !isset( self::$_map[$singular] ) ) {
				// Recherche par controller / action
				self::_findTrad($singular, self::urlDomains(), $args);
				
				// Recherche par le model contenu dans $singular
				$posPoint = strpos($singular, '.');
				if( !isset( self::$_map[$singular] ) && $posPoint !== false && $posPoint < strlen($singular) ) {
					list( $modelName ) = self::model_field( $singular );
					$domain = Inflector::underscore( $modelName );
					$params = self::_params();
					self::_findTrad($singular, self::_existingDomains(array( $params['prefix'].$domain, $domain )), $args);
				}
				
				// Si rien trouvé, on renvoi $singular
				if( !isset( self::$_map[$singular] ) ) {
					self::$_map[$singular] = $singular;
				}
			}
			
			return self::$_map[$singular];
		}
		
		/**
		 * Permet d'obtenir la traduction au singulier ou au pluriel de façon automatique.
		 * 
		 * @param string $singular Mot/Phrase au singulier à traduire
		 * @param string $plural Mot/Phrase au pluriel à traduire
		 * @param integer $count Nombre pour savoir si on est au singulier ou au pluriel (ex: 1 = cheval, 2 = chevaux)
		 * @return string
		 */
		public static function translatePlural( $singular, $plural, $count, $args = null ) {
			$memoryKey = $singular . '_!_' . $plural . '_!_' . $count;
			if ( !isset(self::$_map[$memoryKey]) ){
				// Recherche par controller / action
				self::_findMultipleTrad($singular, $plural, $count, self::urlDomains(), $args);
				
				// Recherche par le model contenu dans $singular
				$posPoint = strpos($singular, '.');
				if( !isset( self::$_map[$memoryKey] ) && $posPoint !== false && $posPoint < strlen($singular) ) {
					list( $modelName ) = self::model_field( $singular );
					$domain = Inflector::underscore( $modelName );
					$params = self::_params();
					self::_findMultipleTrad($singular, $plural, $count, self::_existingDomains(array( $params['prefix'].$domain, $domain )), $args);
				}
				
				if( !isset( self::$_map[$memoryKey] ) ) {
					self::$_map[$memoryKey] = __n( $singular, $plural, $count );
				}
			}
			
			return self::$_map[$memoryKey];
		}

		/**
		 * Retourne la langue utilisée actuellement, soit définie dans la session,
		 * soit avec Config.language.
		 * 
		 * @return string 'fre', 'eng', etc...
		 */
		public static function language() {
			$language = !empty($_SESSION['Config']['language']) 
					? $_SESSION['Config']['language'] 
					: Configure::read('Config.language')
			;
			
			return $language;
		}
		
		/**
		 * Supprime le cache de la class
		 */
		public static function reset() {
			self::$_contexts = null;
			self::$_map = null;
		}

		/**
		 * Retourne la liste des domaines à vérifier en fonction du controller,
		 * de l'action et du prefix (optionnel).
		 * 
		 * @return array array( 'nomducontroller_nomdelaction', 'nomducontroller', ... )
		 */
		public static function urlDomains() {
			if( self::$_contexts === null ) {
				$routerParams = Router::getParams();
				$params = self::_params();
				
				// Ucfirst fonctionne dans l'url mais invalide les traductions
				$routerParams['controller'] = strtolower($routerParams['controller']);

				$urlDomains = array(
					$params['prefix'] . $routerParams['controller'] . $params['separator'] . $routerParams['action'],
					$routerParams['controller'] . $params['separator'] . $routerParams['action'],
					$params['prefix'] . $routerParams['controller']
				);
				
				if ( $params['prefix'] !== '' ){
					$urlDomains[] = $routerParams['controller'];
				}
				
				self::$_contexts = self::_existingDomains( $urlDomains );
			}
			
			return self::$_contexts;
		}
		
		/**
		 * Extrait le nom d'un modèle et d'un champ à partir d'un chemin.
		 *
		 * @param string $path ie. User.username, User.0.id
		 * @return array( string $model, string $field ) ie. array( 'User', 'username' ), array( 'User', 'id' )
		 */
		public static function model_field( $path ) {
			if( preg_match( "/(?<!\w)(\w+)(\.|\.[0-9]+\.)(\w+)$/", $path, $matches ) ) {
				return array( $matches[1], $matches[3] );
			}

			return null;
		}
		
		/**
		 * Permet l'ajout manuel de domaines possible
		 * @param mixed $domains Liste de domaines
		 */
		public static function add_domains( $domains ) {
			self::$_domains += (array)$domains;
		}

		/**
		 * Assigne à < self::$_map[$singular] > et renvoi la traduction selon une liste de domains.
		 * 
		 * @param string $singular Texte à traduire
		 * @param array $urls Liste des domains
		 * @return string Texte traduit
		 */
		protected static function _findTrad( $singular, $urls, $args ) {
			foreach ( $urls as $domain ) {
				$tempTrad = __d( $domain, $singular, $args );
				if ( $tempTrad !== $singular ){
					self::$_map[$singular] = $tempTrad;
					return $tempTrad;
				}
			}
			return false;
		}
		
		/**
		 * Assigne à < self::$_map[$singular . '_!_' . $plural . '_!_' . $count] > 
		 * et renvoi la traduction au singulier ou au pluriel selon une liste de domains.
		 * 
		 * @param string $singular Mot/Phrase au singulier à traduire
		 * @param string $plural Mot/Phrase au pluriel à traduire
		 * @param integer $count Nombre pour le calcul du pluriel (ex: 1 = cheval, 2 = chevaux)
		 * @param array $urls Liste des domains
		 * @return string Texte traduit
		 */
		protected static function _findMultipleTrad( $singular, $plural, $count, $urls, $args ) {
			$memoryKey = $singular . '_!_' . $plural . '_!_' . $count;
			foreach ( $urls as $domain ) {
				$tempTrad = __dn($domain, $singular, $plural, $count, $args);
				if ( $tempTrad !== $singular && $tempTrad !== $plural ){
					self::$_map[$memoryKey] = $tempTrad;
					return $tempTrad;
				}
			}
			return false;
		}
		
		/**
		 * Retire de $urlDomains les domains n'existant pas
		 * 
		 * @param array $urlDomains Liste des domains
		 * @return array Liste des domains existant
		 */
		protected static function _existingDomains( $urlDomains ) {
			$existing = array();
			
			$domains = $urlDomains + self::$_domains;
			
			foreach( $domains as $domain ){
				$thisDomainExist = false;
				foreach( App::path('locales') as $path ){
					if ( is_file($path . self::language() . DS . 'LC_MESSAGES' . DS . $domain . '.po') ){
						$thisDomainExist = true;
						break;
					}
				}
				
				if ( $thisDomainExist ){
					$existing[] = $domain;
				}
			}
			
			return $existing;
		}
		
		/**
		 * Récupère les paramètres par défaut et/ou le récupère dans la conf sous
		 * <MultiDomainsTranslator>
		 * 
		 * $params['prefix'] = null - Permet d'ajouter un fichier à haute prioritée
		 * $params['separator'] = '_' - Séparateur dans le nom du fichier, ici : controller_action
		 * 
		 * ex : important_model.po avec le prefix important sera pris en compte en priorité
		 * 
		 * @return array Params de la class
		 */
		protected static function _params() {
			$params = (array)Configure::read('MultiDomainsTranslator') + array( 'prefix' => null, 'separator' => '_' );
			$params['prefix'] = $params['prefix'] !== null ? "{$params['prefix']}{$params['separator']}" : '';
			return $params;
		}
	}
?>