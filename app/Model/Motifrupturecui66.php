<?php
	/**
	 * Code source de la classe Motifrupturecui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Motifrupturecui66 ...
	 *
	 * @package app.Model
	 */
	class Motifrupturecui66 extends AppModel
	{
		public $name = 'Motifrupturecui66';

		public $actsAs = array(
			'Pgsqlcake.PgsqlAutovalidate',
			'Formattable'
		);

		public $validate = array(
			'name' => array(
				array(
					'rule' => 'isUnique',
					'message' => 'Valeur déjà utilisée'
				)
			)
		);

        /**
         * Associations "Has And Belongs To Many".
         * @var array
         */
        public $hasAndBelongsToMany = array(
            'Rupturecui66' => array(
                'className' => 'Rupturecui66',
                'joinTable' => 'motifsrupturescuis66s_rupturescuis66',
                'foreignKey' => 'motifrupturecui66_id',
                'associationForeignKey' => 'rupturecui66_id',
                'unique' => true,
                'conditions' => '',
                'fields' => '',
                'order' => '',
                'limit' => '',
                'offset' => '',
                'finderQuery' => '',
                'deleteQuery' => '',
                'insertQuery' => '',
                'with' => 'Motifrupturecui66Rupturecui66'
            )
        );

	}
?>