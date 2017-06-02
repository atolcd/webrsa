<?php	
	/**
	 * Code source de la classe Acqmatprof.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Acqmatprof ...
	 *
	 * @package app.Model
	 */
	class Acqmatprof extends AppModel
	{
		public $name = 'Acqmatprof';

		public $actsAs = array(
			'Aideapre',
			'Frenchfloat' => array( 'fields' => array( 'montantaide' ) )
		);

		public $validate = array(
			'apre_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'montantaide' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
				array(
					'rule' => 'numeric',
					'message' => 'Veuillez entrer une valeur numérique.',
					'allowEmpty' => true
				)
			)
		);

		public $belongsTo = array(
			'Apre' => array(
				'className' => 'Apre',
				'foreignKey' => 'apre_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasAndBelongsToMany = array(
			'Pieceacqmatprof' => array(
				'className' => 'Pieceacqmatprof',
				'joinTable' => 'acqsmatsprofs_piecesacqsmatsprofs',
				'foreignKey' => 'acqmatprof_id',
				'associationForeignKey' => 'pieceacqmatprof_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'AcqmatprofPieceacqmatprof'
			)
		);
	}
?>