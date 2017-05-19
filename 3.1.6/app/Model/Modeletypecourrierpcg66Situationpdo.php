<?php	
	/**
	 * Code source de la classe Modeletypecourrierpcg66Situationpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Modeletypecourrierpcg66Situationpdo ...
	 *
	 * @package app.Model
	 */
	class Modeletypecourrierpcg66Situationpdo extends AppModel
	{
		public $name = 'Modeletypecourrierpcg66Situationpdo';

		public $recursive = -1;

		public $actsAs = array(
			'Autovalidate2',
			'ValidateTranslate',
			'Formattable'
		);

		public $belongsTo = array(
			'Modeletypecourrierpcg66' => array(
				'className' => 'Modeletypecourrierpcg66',
				'foreignKey' => 'modeletypecourrierpcg66_id',
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
			),
		);

// 		public $hasMany = array(
// 			'Traitementpcg66' => array(
// 				'className' => 'Traitementpcg66',
// 				'foreignKey' => 'personnepcg66_situationpdo_id',
// 				'dependent' => true,
// 				'conditions' => '',
// 				'fields' => '',
// 				'order' => '',
// 				'limit' => '',
// 				'offset' => '',
// 				'exclusive' => '',
// 				'finderQuery' => '',
// 				'counterQuery' => ''
// 			)
// 		);
	}
?>