<?php	
	/**
	 * Code source de la classe Motifreorientep93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Motifreorientep93 ...
	 *
	 * @package app.Model
	 */
	class Motifreorientep93 extends AppModel
	{
		public $name = 'Motifreorientep93';

		public $actsAs = array(
			'Autovalidate2',
			'ValidateTranslate',
			'Formattable'
		);

		public $hasMany = array(
			'Reorientationep93' => array(
				'className' => 'Reorientationep93',
				'foreignKey' => 'motifreorientep93_id',
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