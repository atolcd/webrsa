<?php	
	/**
	 * Code source de la classe ContratinsertionUser.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ContratinsertionUser ...
	 *
	 * @package app.Model
	 */
	class ContratinsertionUser extends AppModel
	{
		public $name = 'ContratinsertionUser';

		public $validate = array(
			'user_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'contratinsertion_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
		);

		public $belongsTo = array(
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'contratinsertion_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>