<?php
	/**
	 * Code source de la classe LogicielprimoPrimoanalyse.
	 *
	 * @package app.Model
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Model.php.
	 */

	/**
	 * La classe LogicielprimoPrimoanalyse ...
	 *
	 * @package app.Model
	 */
	class LogicielprimoPrimoanalyse extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'LogicielprimoPrimoanalyse';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Logicielprimo' => array(
				'className' => 'Logicielprimo',
				'foreignKey' => 'logicielprimo_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Primoanalyse' => array(
				'className' => 'Primoanalyse',
				'foreignKey' => 'primoanalyse_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);
	}
?>