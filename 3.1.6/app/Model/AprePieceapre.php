<?php	
	/**
	 * Code source de la classe AprePieceapre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe AprePieceapre ...
	 *
	 * @package app.Model
	 */
	class AprePieceapre extends AppModel
	{
		public $name = 'AprePieceapre';

		public $validate = array(
			'apre_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'pieceapre_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
		);

		public $belongsTo = array(
			'Apre' => array(
				'className' => 'Apre',
				'foreignKey' => 'apre_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Pieceapre' => array(
				'className' => 'Pieceapre',
				'foreignKey' => 'pieceapre_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>