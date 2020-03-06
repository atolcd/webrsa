<?php
/**
 * Fichier source de la classe WebrsaSessionAclShell.
 *
 * PHP 7.2
 *
 * @package Console.Command
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

App::uses('SessionAclShell', 'SessionAcl.Console/Command');
App::uses('WebrsaSessionAclUtility', 'Utility');
App::uses('WebrsaPermissionsComponent', 'Controller/Component');
App::uses( 'ComponentCollection', 'Controller' );

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

	/**
	 * Les modèles utilisés par ce shell.
	 *
	 * Il faut que ces modèles soient uniquement les modèles qui servent à
	 * l'enregistrement d'une ligne et qu'ils soient dans le bon ordre.
	 *
	 * @var array
	 */
	public $uses = array(
		'User', 'Group'
	);

	/**
	 * Les tâches utilisées par ce shell.
	 *
	 * @var array
	 */
	public $tasks = array( 'XProgressBar' );

	/**
	 * Force à 'hériter' tous les droits de tous les utilisateurs du groupe
	 * Lancement : sudo -u apache vendor/cakephp/cakephp/lib/Cake/Console/cake WebrsaSessionAcl setHeritageAllUsers [Group_ID] -app app
	 */
	public function setHeritageAllUsers() {

		if (!isset($this->args[0])) {
			$this->err(__d('groups','SHELL:GROUPS:ErreurVAR'));
			$this->_stop(self::ERROR);
		}

		$WebrsaPermissions = new WebrsaPermissionsComponent( new ComponentCollection() );

		$permissions['Permission'] = $WebrsaPermissions->getPermissionsHeritage($this->Group, $this->args[0]);
		$permissions['Permission'] = array_fill_keys(array_keys ($permissions['Permission']), 0);
		$users = $this->User->find( 'all', array ('contain' => false, 'conditions' => array ('group_id' => $this->args[0])) );

		$keys = array_keys($users);
		foreach ($users as $user) {
			$this->XProgressBar->start( end($keys) );
			$this->User->id = $user['User']['id'];
			$this->User->begin();
			$success = $WebrsaPermissions->updatePermissions($this->User, $user['User']['id'], $permissions);

			if ($success) {
				$this->User->commit();
			} else {
				$this->User->rollback();
				break;
			}
			$this->XProgressBar->next();
		}
		if ($success) {
			$this->out(__d('groups',  'SHELL:GROUPS:Succes', $this->args[0]));
			$this->_stop(self::SUCCESS);
		} else {
			$this->err(__d('groups', 'SHELL:GROUPS:ErreurMAJ' ));
			$this->_stop(self::ERROR);
		}
	}

}