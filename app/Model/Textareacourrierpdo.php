<?php
	/**
	 * Code source de la classe Textareacourrierpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Textareacourrierpdo ...
	 *
	 * @package app.Model
	 */
	class Textareacourrierpdo extends AppModel
	{
		public $name = 'Textareacourrierpdo';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Courrierpdo' => array(
				'className' => 'Courrierpdo',
				'foreignKey' => 'courrierpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Contenutextareacourrierpdo' => array(
				'className' => 'Contenutextareacourrierpdo',
				'foreignKey' => 'textareacourrierpdo_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);
	}
?>