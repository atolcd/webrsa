<?php	
	/**
	 * Code source de la classe UserZonegeographique.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe UserZonegeographique ...
	 *
	 * @package app.Model
	 */
	class UserZonegeographique extends AppModel
	{
		public $name = 'UserZonegeographique';

		public $validate = array(
			'user_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'zonegeographique_id' => array(
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
			),
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'foreignKey' => 'zonegeographique_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>