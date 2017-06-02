<?php
	/**
	 * Code source de la classe LogicielprimoPrimoanalyse.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

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
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
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