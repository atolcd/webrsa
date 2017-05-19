<?php	
	/**
	 * Code source de la classe Jetonfonction.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Jetonfonction ...
	 *
	 * @package app.Model
	 */
	class Jetonfonction extends AppModel
	{
		public $name = 'Jetonfonction';

		public $validate = array(
			'controller' => array(
				'notempty' => array(
					'rule' => array('notempty'),
				),
			),
			'action' => array(
				'notempty' => array(
					'rule' => array('notempty'),
				),
			),
			'user_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
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
			)
		);
	}
?>