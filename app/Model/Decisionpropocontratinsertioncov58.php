<?php
	/**
	 * Code source de la classe Decisionpropocontratinsertioncov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Decisionpropocontratinsertioncov58 ...
	 *
	 * @package app.Model
	 */
	class Decisionpropocontratinsertioncov58 extends AppModel
	{
		public $name = 'Decisionpropocontratinsertioncov58';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Règles de validation à appliquer en plus de celles déduites de la
		 * base de données.
		 *
		 * @var array
		 */
		public $validate = array(
			'duree_engag' => array(
				'checkDureeDates' => array(
					'rule' => array( 'checkDureeDates', 'dd_ci', 'df_ci' ),
					'message' => 'Les dates de début et de fin ne correspondent pas à la durée'
				)
			)
		);

		public $belongsTo = array(
			'Passagecov58' => array(
				'className' => 'Passagecov58',
				'foreignKey' => 'passagecov58_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>