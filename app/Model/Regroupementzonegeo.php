<?php
	/**
	 * Code source de la classe Regroupementzonegeo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Regroupementzonegeo ...
	 *
	 * @package app.Model
	 */
	class Regroupementzonegeo extends AppModel
	{
		public $name = 'Regroupementzonegeo';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		public $validate = array(
			'lib_rgpt' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			)
		);

		public $hasAndBelongsToMany = array(
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'joinTable' => 'regroupementszonesgeo_zonesgeographiques',
				'foreignKey' => 'regroupementzonegeo_id',
				'associationForeignKey' => 'zonegeographique_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'RegroupementzonegeoZonegeographique'
			)
		);
	}
?>
