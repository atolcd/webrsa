<?php
	/**
	 * Code source de la classe Avisprimoanalyse.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

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
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2RulesFieldtypes',
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