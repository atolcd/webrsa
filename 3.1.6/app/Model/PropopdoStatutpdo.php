<?php	
	/**
	 * Code source de la classe PropopdoStatutpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe PropopdoStatutpdo ...
	 *
	 * @package app.Model
	 */
	class PropopdoStatutpdo extends AppModel {
		public $name = 'PropopdoStatutpdo';

		public $actsAs = array (
			'Formattable',
			'ValidateTranslate'
		);

		public $validate = array(
			'propopdo_id' => array(
				array( 'rule' => 'notEmpty' )
			),
			'statutpdo_id' => array(
				array( 'rule' => 'notEmpty' )
			)
		);
		//The Associations below have been created with all possible keys, those that are not needed can be removed

		public $belongsTo = array(
			'Propopdo' => array(
				'className' => 'Propopdo',
				'foreignKey' => 'propopdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Statutpdo' => array(
				'className' => 'Statutpdo',
				'foreignKey' => 'statutpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>