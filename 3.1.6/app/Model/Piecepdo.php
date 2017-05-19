<?php	
	/**
	 * Code source de la classe Piecepdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Piecepdo ...
	 *
	 * @package app.Model
	 */
	class Piecepdo extends AppModel
	{
		public $name = 'Piecepdo';

		public $belongsTo = array(
			'Propopdo' => array(
				'className' => 'Propopdo',
				'foreignKey' => 'propopdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>