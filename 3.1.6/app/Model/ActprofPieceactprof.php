<?php	
	/**
	 * Code source de la classe ActprofPieceactprof.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ActprofPieceactprof ...
	 *
	 * @package app.Model
	 */
	class ActprofPieceactprof extends AppModel
	{
		public $name = 'ActprofPieceactprof';

		public $validate = array(
			'actprof_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'pieceactprof_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
		);

		public $belongsTo = array(
			'Actprof' => array(
				'className' => 'Actprof',
				'foreignKey' => 'actprof_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Pieceactprof' => array(
				'className' => 'Pieceactprof',
				'foreignKey' => 'pieceactprof_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>