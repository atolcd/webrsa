<?php
	/**
	 * Code source de la classe Personnelangue.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'FrValidation', 'Validation' );

	/**
	 * La classe Personnelangue ...
	 *
	 * @package app.Model
	 */
	class Personnelangue extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Personnelangue';

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable' => array(
				'Validation2.Validation2DefaultFormatter' => array(
					'stripNotAlnum' => '/^(numfixe|numport)$/'
				)
			),
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate',
			'PersonneCSV'
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);
	}
?>