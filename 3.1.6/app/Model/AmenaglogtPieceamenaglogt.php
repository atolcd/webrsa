<?php	
	/**
	 * Code source de la classe AmenaglogtPieceamenaglogt.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe AmenaglogtPieceamenaglogt ...
	 *
	 * @package app.Model
	 */
	class AmenaglogtPieceamenaglogt extends AppModel
	{
		public $name = 'AmenaglogtPieceamenaglogt';

		public $validate = array(
			'amenaglogt_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'pieceamenaglogt_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
		);

		public $belongsTo = array(
			'Amenaglogt' => array(
				'className' => 'Amenaglogt',
				'foreignKey' => 'amenaglogt_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Pieceamenaglogt' => array(
				'className' => 'Pieceamenaglogt',
				'foreignKey' => 'pieceamenaglogt_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>