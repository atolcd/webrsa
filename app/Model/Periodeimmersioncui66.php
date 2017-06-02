<?php
	/**
	 * Code source de la classe Periodeimmersioncui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Periodeimmersioncui66 ...
	 *
	 * @package app.Model
	 */
	class Periodeimmersioncui66 extends AppModel
	{
		public $name = 'Periodeimmersioncui66';

		public $recursive = -1;

		public $actsAs = array(
            'Pgsqlcake.PgsqlAutovalidate',
            'Formattable' => array(
				'suffix' => array( 'metieraffectation_id' ),
			),
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