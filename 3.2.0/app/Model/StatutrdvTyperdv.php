<?php
	/**
	 * Code source de la classe StatutrdvTyperdv.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe StatutrdvTyperdv ...
	 *
	 * @package app.Model
	 */
	class StatutrdvTyperdv extends AppModel
	{
		public $name = 'StatutrdvTyperdv';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'statutrdv_id' => array(
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'statutrdv_id', 'typerdv_id' ) ),
					'message' => 'Ce statut est déjà utilisé avec ce type.'
				)
			),
			'typerdv_id' => array(
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'statutrdv_id', 'typerdv_id' ) ),
					'message' => 'Ce statut est déjà utilisé avec ce type.'
				)
			)
		);

		public $belongsTo = array(
			'Statutrdv' => array(
				'className' => 'Statutrdv',
				'foreignKey' => 'statutrdv_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typerdv' => array(
				'className' => 'Typerdv',
				'foreignKey' => 'typerdv_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>