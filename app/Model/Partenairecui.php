<?php
	/**
	 * Fichier source de la classe Partenairecui.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Partenairecui est la classe contenant les partenaires (entreprises/mairie...) du CUI.
	 *
	 * @package app.Model
	 */
	class Partenairecui extends AppModel
	{
		public $name = 'Partenairecui';
		
		public $recursive = -1;
		
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
			'Formattable',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
		);
	}
?>