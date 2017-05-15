<?php	
	/**
	 * Code source de la classe Themeapre66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Themeapre66 ...
	 *
	 * @package app.Model
	 */
	class Themeapre66 extends AppModel
	{
		public $name = 'Themeapre66';

		public $order = 'Themeapre66.name ASC';

		public $validate = array(
			'name' => array(
				'notempty' => array(
					'rule' => array('notempty'),
				),
			),
		);

		public $hasMany = array(
			'Aideapre66' => array(
				'className' => 'Aideapre66',
				'foreignKey' => 'themeapre66_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Typeaideapre66' => array(
				'className' => 'Typeaideapre66',
				'foreignKey' => 'themeapre66_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);
	}
?>