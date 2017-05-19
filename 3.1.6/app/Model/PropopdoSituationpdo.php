<?php	
	/**
	 * Code source de la classe PropopdoSituationpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe PropopdoSituationpdo ...
	 *
	 * @package app.Model
	 */
	class PropopdoSituationpdo extends AppModel
	{
		public $name = 'PropopdoSituationpdo';

		public $actsAs = array (
			'Formattable',
			'ValidateTranslate'
		);

		public $validate = array(
			'propopdo_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'situationpdo_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
		);

		public $belongsTo = array(
			'Propopdo' => array(
				'className' => 'Propopdo',
				'foreignKey' => 'propopdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Situationpdo' => array(
				'className' => 'Situationpdo',
				'foreignKey' => 'situationpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>