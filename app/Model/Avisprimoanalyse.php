<?php
	/**
	 * Code source de la classe Avisprimoanalyse.
	 *
	 * @package app.Model
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Model.php.
	 */

	/**
	 * La classe Avisprimoanalyse ...
	 *
	 * @package app.Model
	 */
	class Avisprimoanalyse extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Avisprimoanalyse';

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
			'Primoanalyse' => array(
				'className' => 'Primoanalyse',
				'foreignKey' => 'primoanalyse_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Avisprimoanalyse' => array(
				'className' => 'Primoanalyse',
				'foreignKey' => 'primoanalyse_id',
				'conditions' => array('Avisprimoanalyse.etape' => 'avis'),
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Validationprimoanalyse' => array(
				'className' => 'Primoanalyse',
				'foreignKey' => 'primoanalyse_id',
				'conditions' => array('Validationprimoanalyse.etape' => 'validation'),
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);
	}
?>