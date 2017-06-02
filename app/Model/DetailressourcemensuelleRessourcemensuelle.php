<?php
	/**
	 * Code source de la classe DetailressourcemensuelleRessourcemensuelle.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe DetailressourcemensuelleRessourcemensuelle ...
	 *
	 * @package app.Model
	 */
	class DetailressourcemensuelleRessourcemensuelle extends AppModel
	{
		public $name = 'DetailressourcemensuelleRessourcemensuelle';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $validate = array(
			'detailressourcemensuelle_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'ressourcemensuelle_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
		);

		public $belongsTo = array(
			'Detailressourcemensuelle' => array(
				'className' => 'Detailressourcemensuelle',
				'foreignKey' => 'detailressourcemensuelle_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Ressourcemensuelle' => array(
				'className' => 'Ressourcemensuelle',
				'foreignKey' => 'ressourcemensuelle_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>