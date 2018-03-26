<?php
	/**
	 * Code source de la classe Ficheprescription93Motifactionachevefp93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Ficheprescription93Motifactionachevefp93 ...
	 *
	 * @package app.Model
	 */
	class Ficheprescription93Motifactionachevefp93 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Ficheprescription93Motifactionachevefp93';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Ficheprescription93' => array(
				'className' => 'Ficheprescription93',
				'foreignKey' => 'ficheprescription93_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Motifactionachevefp93' => array(
				'className' => 'motifactionachevefp93',
				'foreignKey' => 'motifactionachevefp93_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);
	}
?>