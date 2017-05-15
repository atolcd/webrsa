<?php	
	/**
	 * Code source de la classe Textareacourrierpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Textareacourrierpdo ...
	 *
	 * @package app.Model
	 */
	class Textareacourrierpdo extends AppModel
	{
		public $name = 'Textareacourrierpdo';

		public $actsAs = array(
			'Autovalidate2'
		);

		public $belongsTo = array(
			'Courrierpdo' => array(
				'className' => 'Courrierpdo',
				'foreignKey' => 'courrierpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Contenutextareacourrierpdo' => array(
				'className' => 'Contenutextareacourrierpdo',
				'foreignKey' => 'textareacourrierpdo_id',
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