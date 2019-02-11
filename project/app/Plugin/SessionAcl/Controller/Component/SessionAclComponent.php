<?php
/**
 * Fichier source de la classe SessionAclComponent.
 *
 * PHP 5.3
 *
 * @package SessionAcl.Controller.Component
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

App::uses('AclComponent', 'Controller/Component');
App::uses('SessionAcl', 'SessionAcl.Model/Datasource');

/**
 * La classe SessionAclComponent permet l'initialisation de SessionAcl
 * 
 * @see SessionAcl/Model/Datasource/SessionAcl.php
 * @package SessionAcl.Controller.Component
 */
class SessionAclComponent extends AclComponent
{
	/**
	 * Paramètres de ce component
	 * 
	 * 'userModel' =>	Nom du modèle des utilisateurs (default: User)<br/>
	 * 'keyPrefix' =>	préfix de la clef pour trouver l'identifiant de 
	 *					l'utilisateur (default: 'Auth')
	 *					exemple: <b>Auth</b>.User.id
	 *
	 * @var array
	 */
	public $settings = array(
		'userModel' => 'User',
		'keyPrefix' => 'Auth',
	);
	
	/**
	 * Nom du modèle des utilisateurs (default: User)
	 * 
	 * @var string
	 */
	public $userModel = 'User';
	
	/**
	 * préfix de la clef pour trouver l'identifiant de 
	 * l'utilisateur (default: 'Auth')
	 * exemple: <b>Auth</b>.User.id
	 * 
	 * @var string
	 */
	public $keyPrefix = 'Auth';
	
	/**
	 * Constructor. Will return an instance of the correct ACL class as defined in `Configure::read('Acl.classname')`
	 *
	 * @param ComponentCollection $collection
	 * @param array $settings
	 * @throws CakeException when Acl.classname could not be loaded.
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		
		SessionAcl::init(
			$this->adapter(), 
			ClassRegistry::init($this->userModel), 
			$this->keyPrefix
		);
	}
}