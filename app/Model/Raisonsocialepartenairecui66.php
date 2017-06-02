<?php
	/**
	 * Code source de la classe Raisonsocialepartenairecui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Raisonsocialepartenairecui66 ...
	 *
	 * @package app.Model
	 */
	class Raisonsocialepartenairecui66 extends AppModel
	{
		public $name = 'Raisonsocialepartenairecui66';

		public $displayField = 'name';

		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		public $hasMany = array(
			'Partenaire' => array(
				'className' => 'Partenaire',
				'foreignKey' => 'raisonsocialepartenairecui66_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);
	}
?>