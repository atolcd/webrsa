<?php
	/**
	 * Code source de la classe FormqualifPieceformqualif.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe FormqualifPieceformqualif ...
	 *
	 * @package app.Model
	 */
	class FormqualifPieceformqualif extends AppModel
	{
		public $name = 'FormqualifPieceformqualif';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $validate = array(
			'formqualif_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'pieceformqualif_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
		);

		public $belongsTo = array(
			'Formqualif' => array(
				'className' => 'Formqualif',
				'foreignKey' => 'formqualif_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Pieceformqualif' => array(
				'className' => 'Pieceformqualif',
				'foreignKey' => 'pieceformqualif_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>