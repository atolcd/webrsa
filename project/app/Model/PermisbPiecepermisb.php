<?php
	/**
	 * Code source de la classe PermisbPiecepermisb.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe PermisbPiecepermisb ...
	 *
	 * @package app.Model
	 */
	class PermisbPiecepermisb extends AppModel
	{
		public $name = 'PermisbPiecepermisb';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $validate = array(
			'permisb_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'piecepermisb_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
		);

		public $belongsTo = array(
			'Permisb' => array(
				'className' => 'Permisb',
				'foreignKey' => 'permisb_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Piecepermisb' => array(
				'className' => 'Piecepermisb',
				'foreignKey' => 'piecepermisb_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>