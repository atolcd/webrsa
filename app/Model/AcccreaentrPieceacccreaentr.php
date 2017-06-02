<?php	
	/**
	 * Code source de la classe AcccreaentrPieceacccreaentr.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe AcccreaentrPieceacccreaentr ...
	 *
	 * @package app.Model
	 */
	class AcccreaentrPieceacccreaentr extends AppModel
	{
		public $name = 'AcccreaentrPieceacccreaentr';

		public $validate = array(
			'acccreaentr_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'pieceacccreaentr_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
		);

		public $belongsTo = array(
			'Acccreaentr' => array(
				'className' => 'Acccreaentr',
				'foreignKey' => 'acccreaentr_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Pieceacccreaentr' => array(
				'className' => 'Pieceacccreaentr',
				'foreignKey' => 'pieceacccreaentr_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>