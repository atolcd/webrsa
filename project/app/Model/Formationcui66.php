<?php
	/**
	 * Code source de la classe Formationcui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Formationcui66 ...
	 *
	 * @package app.Model
	 */
	class Formationcui66 extends AppModel
	{
		public $name = 'Formationcui66';

		public $actsAs = array(
            'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		public $belongsTo = array(
			'Accompagnementcui66' => array(
				'className' => 'Accompagnementcui66',
				'foreignKey' => 'accompagnementcui66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
            'Orgsuivicui66formation' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'orgsuivicui66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
            'Refsuivicui66' => array(
				'className' => 'Referent',
				'foreignKey' => 'refsuivicui66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);
	}
?>