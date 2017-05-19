<?php	
	/**
	 * Code source de la classe AcqmatprofPieceacqmatprof.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe AcqmatprofPieceacqmatprof ...
	 *
	 * @package app.Model
	 */
	class AcqmatprofPieceacqmatprof extends AppModel
	{
		public $name = 'AcqmatprofPieceacqmatprof';

		public $validate = array(
			'acqmatprof_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'pieceacqmatprof_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
		);

		public $belongsTo = array(
			'Acqmatprof' => array(
				'className' => 'Acqmatprof',
				'foreignKey' => 'acqmatprof_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Pieceacqmatprof' => array(
				'className' => 'Pieceacqmatprof',
				'foreignKey' => 'pieceacqmatprof_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>