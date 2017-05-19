<?php
/**
 * Code source de la classe WebrsaAccess.
 *
 * PHP 5.3
 *
 * @package app.Utility
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe WebrsaAccess utilise les rêgles métier afin de griser ou pas un ou plusieurs liens
 *
 * @package app.Utility
 */
interface WebrsaAccessInterface
{
	/**
	 * Paramètres par défaut
	 * 
	 * @param array $params
	 * @return array
	 */
	public static function params(array $params = array());
	
	/**
	 * Renvoi la liste des actions disponibles
	 * 
	 * @param array $params
	 * @return array - array('action1', 'action2', ...)
	 */
	public static function actions(array $params = array());
	
	/**
	 * Permet d'obtenir les accès pour un find first
	 * 
	 * @param array $record
	 * @param array $params
	 * @return array
	 */
	public static function access(array $record, array $params = array());
	
	/**
	 * Permet d'obtenir les accès pour un find all
	 *
	 * @param array $records
	 * @param array $params
	 * @return array
	 */
	public static function accesses(array $records, array $params = array());
	
	/**
	 * Merge et normalize les actions par défault avec celles ajoutés
	 * 
	 * @param array $defaults
	 * @param array $actions
	 * @return array
	 */
	public static function merge_actions(array $defaults, array $actions);
	
	/**
	 * Normalize la liste des actions
	 * 
	 * @param array $actions
	 * @return array
	 */
	public static function normalize_actions(array $actions);
	
	/**
	 * Permet d'obtenir les clefs à calculer pour connaitre les droits d'accès 
	 * à toutes les actions disponnibles
	 * 
	 * @return array
	 */
	public static function getParamsList(array $params = array());
	
	/**
	 * Même utilitée que self::getParamsList, à la différence qu'on ne récupère 
	 * la liste que d'une seule action
	 * 
	 * @param String $action
	 * @param array $params
	 * @return array
	 */
	public static function getActionParamsList($action, array $params = array());
}