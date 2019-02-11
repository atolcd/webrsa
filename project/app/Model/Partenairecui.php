<?php
	/**
	 * Fichier source de la classe Partenairecui.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Partenairecui est la classe contenant les partenaires (entreprises/mairie...) du CUI.
	 *
	 * @package app.Model
	 */
	class Partenairecui extends AppModel
	{
		public $name = 'Partenairecui';

        public $belongsTo = array(
			'Adressecui' => array(
				'className' => 'Adressecui',
				'foreignKey' => 'adressecui_id',
				'dependent' => true,
			),
        );

		public $hasOne = array(
			'Cui' => array(
				'className' => 'Cui',
				'foreignKey' => 'partenairecui_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'dependent' => true
			),
			'Partenairecui66' => array(
				'className' => 'Partenairecui66',
				'foreignKey' => 'partenairecui_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'dependent' => true
			),
		);

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate',
		);
	}
?>