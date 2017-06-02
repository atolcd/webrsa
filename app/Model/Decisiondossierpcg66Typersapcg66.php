<?php
	/**
	 * Code source de la classe Decisiondossierpcg66Typersapcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Decisiondossierpcg66Typersapcg66 ...
	 *
	 * @package app.Model
	 */
	class Decisiondossierpcg66Typersapcg66 extends AppModel
	{
		public $name = 'Decisiondossierpcg66Typersapcg66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Decisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'foreignKey' => 'decisiondossierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typersapcg66' => array(
				'className' => 'Typersapcg66',
				'foreignKey' => 'typersapcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>