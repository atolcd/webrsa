<?php	
	/**
	 * Code source de la classe Aideapre66Pieceaide66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Aideapre66Pieceaide66 ...
	 *
	 * @package app.Model
	 */
	class Aideapre66Pieceaide66 extends AppModel
	{
		public $name = 'Aideapre66Pieceaide66';

		public $validate = array(
			'aideapre66_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'pieceaide66_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
		);

		public $belongsTo = array(
			'Aideapre66' => array(
				'className' => 'Aideapre66',
				'foreignKey' => 'aideapre66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Pieceaide66' => array(
				'className' => 'Pieceaide66',
				'foreignKey' => 'pieceaide66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>