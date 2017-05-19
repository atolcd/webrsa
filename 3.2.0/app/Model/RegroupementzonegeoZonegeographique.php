<?php
	/**
	 * Code source de la classe RegroupementzonegeoZonegeographique.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe RegroupementzonegeoZonegeographique ...
	 *
	 * @package app.Model
	 */
	class RegroupementzonegeoZonegeographique extends AppModel {

		public $name = 'RegroupementzonegeoZonegeographique';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

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

		public $belongsTo = array(
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'foreignKey' => 'zonegeographique_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Regroupementzonegeo' => array(
				'className' => 'Regroupementzonegeo',
				'foreignKey' => 'regroupementzonegeo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

	}
?>
