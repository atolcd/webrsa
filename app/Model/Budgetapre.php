<?php
	/**
	 * Code source de la classe Budgetapre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Budgetapre ...
	 *
	 * @package app.Model
	 */
	class Budgetapre extends AppModel
	{
		public $name = 'Budgetapre';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'exercicebudgetai';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'montantattretat' => array(
				'inclusiveRange' => array(
					'rule' => array( 'inclusiveRange', 0, 99999999 ),
					'message' => 'Veuillez saisir un montant compris entre 0 et 99 999 999 € maximum.'
				)
			)
		);

		public $hasMany = array(
			'Etatliquidatif' => array(
				'className' => 'Etatliquidatif',
				'foreignKey' => 'budgetapre_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);
	}
?>