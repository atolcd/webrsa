<?php
	/**
	 * Code source de la classe FormpermfimoPieceformpermfimo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe FormpermfimoPieceformpermfimo ...
	 *
	 * @package app.Model
	 */
	class FormpermfimoPieceformpermfimo extends AppModel
	{
		public $name = 'FormpermfimoPieceformpermfimo';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $validate = array(
			'formpermfimo_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'pieceformpermfimo_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
		);

		public $belongsTo = array(
			'Formpermfimo' => array(
				'className' => 'Formpermfimo',
				'foreignKey' => 'formpermfimo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Pieceformpermfimo' => array(
				'className' => 'Pieceformpermfimo',
				'foreignKey' => 'pieceformpermfimo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>