<?php
	/**
	 * Code source de la classe Motifrupturecui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Motifrupturecui66 ...
	 *
	 * @package app.Model
	 */
	class Motifrupturecui66 extends AppModel
	{
		public $name = 'Motifrupturecui66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
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