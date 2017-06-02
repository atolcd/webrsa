<?php
	/**
	 * Code source de la classe Decisionnonrespectsanctionep93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractDecisionep', 'Model/Abstractclass' );

	/**
	 * La classe Decisionnonrespectsanctionep93 ...
	 *
	 * @package app.Model
	 */
	class Decisionnonrespectsanctionep93 extends AbstractDecisionep
	{
		public $name = 'Decisionnonrespectsanctionep93';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesComparison',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		/**
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Passagecommissionep' => array(
				'className' => 'Passagecommissionep',
				'foreignKey' => 'passagecommissionep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		* Les règles de validation qui seront utilisées lors de la validation
		* en EP des décisions de la thématique
		*/

		public $validateFinalisation = array(
			'decision' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
		);
	}
?>
