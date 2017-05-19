<?php
	/**
	 * Code source de la classe Requestgroup.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Requestgroup ...
	 *
	 * @package app.Model
	 */
	class Requestgroup extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Requestgroup';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesComparison',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Règles de validation.
		 *
		 * @var array
		 */
		public $validate = array(
			'name' => array(
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'parent_id', 'name' ) ),
					'message' => 'Valeur déjà utilisée'
				)
			),
			'parent_id' => array(
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'parent_id', 'name' ) ),
					'message' => 'Valeur déjà utilisée'
				)
			)
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Requestmanager' => array(
				'className' => 'Requestmanager',
				'foreignKey' => 'requestgroup_id',
			),
		);
	}
?>