<?php
	/**
	 * Code source de la classe Typetitrecreancier.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Typetitrecreancier ...
	 *
	 * @package app.Model
	 */
	class Typetitrecreancier extends AppModel
	{
		public $name = 'Typetitrecreancier';

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
            'Titrecreancier' => array(
                'className' => 'Titrecreancier',
                'joinTable' => 'typestitrescreanciers_titrescreanciers',
                'foreignKey' => 'typetitrecreancier_id',
                'associationForeignKey' => 'titrecreancier_id',
                'unique' => true,
                'conditions' => '',
                'fields' => '',
                'order' => '',
                'limit' => '',
                'offset' => '',
                'finderQuery' => '',
                'deleteQuery' => '',
                'insertQuery' => '',
                'with' => 'TypetitrecreancierTitrecreancier'
            )
        );

	}
