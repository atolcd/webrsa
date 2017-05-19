<?php
	/**
	 * Code source de la classe Bilancui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Bilancui66 ...
	 *
	 * @package app.Model
	 */
	class Bilancui66 extends AppModel
	{
		public $name = 'Bilancui66';

		public $recursive = -1;

		public $actsAs = array(
			'Formattable' => array(
                'suffix' => array(
                    'refsuivicui66_id'
                )
            ),
            'Pgsqlcake.PgsqlAutovalidate'
		);

		public $belongsTo = array(
			'Accompagnementcui66' => array(
				'className' => 'Accompagnementcui66',
				'foreignKey' => 'accompagnementcui66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
            'Orgsuivicui66' => array(
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