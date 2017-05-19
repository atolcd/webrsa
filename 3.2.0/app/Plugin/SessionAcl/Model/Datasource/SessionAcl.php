<?php
/**
 * Fichier source de la classe SessionAcl.
 *
 * PHP 5.3
 *
 * @package SessionAcl.Model.Datasource
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe SessionAcl permet de vérifier les accès Acl d'un utilisateur
 * connecté.
 * Le résultat sera conservé en session.
 *
 * @package SessionAcl.Model.Datasource
 */
abstract class SessionAcl
{
	/**
	 * Classe de gestion des Acl
	 * 
	 * @var AclInterface 
	 */
	protected static $_acl;
	
	/**
	 * Classe d'utilisateur
	 * 
	 * @var Model
	 */
	protected static $_user;
	
	/**
	 * Préfix de le clef vers l'identifiant de l'utilisateur
	 * 
	 * @var string
	 */
	public static $keyPrefix;
	
	/**
	 * Initialise le Datasource
	 * 
	 * @param AclInterface $acl
	 * @param Model $user
	 */
	public static function init(AclInterface $acl, Model $user, $keyPrefix = 'Auth') {
		self::$_acl = $acl;
		self::$_user = $user;
		self::$keyPrefix = $keyPrefix;
	}
	
	/**
	 * Vérifi l'accès de l'utilisateur à un chemin
	 * Mémorise le résultat et le met en session
	 * 
	 * @param string $key ClefDeCacheSession.path/of/aco
	 * @param string $action (create, read, update, delete, *)
	 * @return boolean|NULL Access, null si non connecté
	 */
	public static function check($key, $action = "*") {
		$access = CakeSession::read($key);
		
		if ($access === null) {
			if (!preg_match('/[\w]+\.([^\.]+)$/', $key, $match)) {
				$acoPath = $key;
			} else {
				$acoPath = $match[1];
			}
			
			self::$_user->id = CakeSession::read(
				self::$keyPrefix.'.'.self::$_user->alias.'.id'
			);
			
			if (!empty(self::$_user->id)) {
				$access = self::$_acl->check(self::$_user, $acoPath, $action);
				CakeSession::write($key, $access);
			}
		}
		
		return $access;
	}
	
	/**
	 * Permet d'obtenir en lecture seule un attribut protégé
	 * 
	 * @param string $attr
	 * @return mixed self::_$attr
	 */
	public static function get($attr) {
		$attrName = '_'.ltrim($attr, '_');
		
		return self::${$attrName};
	}
}