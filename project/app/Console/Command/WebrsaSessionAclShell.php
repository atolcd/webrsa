<?php
/**
 * Fichier source de la classe WebrsaSessionAclShell.
 *
 * PHP 5.3
 *
 * @package Console.Command
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

App::uses('SessionAclShell', 'SessionAcl.Console/Command');
App::uses('WebrsaSessionAclUtility', 'Utility');

/**
 * La classe WebrsaSessionAclShell permet l'initialisation de SessionAcl
 * 
 * @package Console.Command
 */
class WebrsaSessionAclShell extends SessionAclShell
{
	/**
	 * @var string
	 */
	public $sessionAclUtility = 'WebrsaSessionAclUtility';
}