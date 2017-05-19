<?php
	/**
	 * Code source de la classe Valeurtag.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Valeurtag ...
	 *
	 * @package app.Model
	 */
	class Valeurtag extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Valeurtag';

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
					'rule' => array( 'checkUnique', array( 'categorietag_id', 'name' ) ),
					'message' => 'Valeur déjà utilisée'
				)
			),
			'categorietag_id' => array(
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'categorietag_id', 'name' ) ),
					'message' => 'Valeur déjà utilisée'
				)
			)
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Categorietag' => array(
				'className' => 'Categorietag',
				'foreignKey' => 'categorietag_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Tag' => array(
				'className' => 'Tag',
				'foreignKey' => 'valeurtag_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
		);
	}
?>