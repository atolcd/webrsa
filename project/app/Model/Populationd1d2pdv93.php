<?php
	/**
	 * Code source de la classe Populationd1d2pdv93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Populationd1d2pdv93 ...
	 *
	 * @package app.Model
	 */
	class Populationd1d2pdv93 extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Populationd1d2pdv93';

		/**
		 * Behaviors utilisés.
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
			'Questionnaired2pdv93' => array(
				'className' => 'Questionnaired2pdv93',
				'foreignKey' => 'questionnaired2pdv93_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Questionnaired1pdv93' => array(
				'className' => 'Questionnaired1pdv93',
				'foreignKey' => 'questionnaired1pdv93_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Tableausuivipdv93' => array(
				'className' => 'Tableausuivipdv93',
				'foreignKey' => 'tableausuivipdv93_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);
	}
?>