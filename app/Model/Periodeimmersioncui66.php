<?php
	/**
	 * Code source de la classe Periodeimmersioncui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Periodeimmersioncui66 ...
	 *
	 * @package app.Model
	 */
	class Periodeimmersioncui66 extends AppModel
	{
		public $name = 'Periodeimmersioncui66';

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
            'Metieraffectation' => array(
				'className' => 'Coderomemetierdsp66',
				'foreignKey' => 'metieraffectation_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Secteuraffectation' => array(
				'className' => 'Coderomesecteurdsp66',
				'foreignKey' => 'secteuraffectation_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>