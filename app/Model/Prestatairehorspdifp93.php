<?php
	/**
	 * Code source de la classe Prestatairehorspdifp93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Prestatairehorspdifp93 ...
	 *
	 * @package app.Model
	 */
	class Prestatairehorspdifp93 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Prestatairehorspdifp93';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable' => array(
				'Validation2.Validation2DefaultFormatter' => array(
					'stripNotAlnum' => '/^(tel|fax)$/'
				)
			),
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Associations "Has one".
		 *
		 * @var array
		 */
		public $hasOne = array(
			'Ficheprescription93' => array(
				'className' => 'Ficheprescription93',
				'foreignKey' => 'prestatairehorspdifp93_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'dependent' => true
			),
		);
	}
?>