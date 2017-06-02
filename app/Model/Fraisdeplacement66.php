<?php
	/**
	 * Code source de la classe Fraisdeplacement66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Fraisdeplacement66 ...
	 *
	 * @package app.Model
	 */
	class Fraisdeplacement66 extends AppModel
	{
		public $name = 'Fraisdeplacement66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'destination' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			)
		);

		public $belongsTo = array(
			'Aideapre66' => array(
				'className' => 'Aideapre66',
				'foreignKey' => 'aideapre66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>