<?php
	/**
	 * Code source de la classe Dreesactioncer.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Dreesactioncer ...
	 *
	 * @package app.Model
	 */

	class Dreesactionscer extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Dreesactionscer';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'dreesactionscer';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'lib_dreesactioncer';

		/**
		 * Associations "Has Many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Sujetcer93' => array(
				'className' => 'Sujetcer93',
				'foreignKey' => 'dreesactionscer_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Soussujetcer93' => array(
				'className' => 'Soussujetcer93',
				'foreignKey' => 'dreesactionscer_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Valeurparsoussujetcer93' => array(
				'className' => 'Valeurparsoussujetcer93',
				'foreignKey' => 'dreesactionscer_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Actioncandidat' => array(
				'className' => 'Actioncandidat',
				'foreignKey' => 'dreesactionscer_id',
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

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesComparison',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

	}
?>