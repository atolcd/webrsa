<?php
	/**
	 * Code source de la classe Documentbeneffp93Ficheprescription93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Documentbeneffp93Ficheprescription93 ...
	 *
	 * @package app.Model
	 */
	class Documentbeneffp93Ficheprescription93 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Documentbeneffp93Ficheprescription93';

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
			'Documentbeneffp93' => array(
				'className' => 'Documentbeneffp93',
				'foreignKey' => 'documentbeneffp93_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Ficheprescription93' => array(
				'className' => 'Ficheprescription93',
				'foreignKey' => 'ficheprescription93_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);
	}
?>