<?php
	/**
	 * Code source de la classe Decisiondossierpcg66Decisiontraitementpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Decisiondossierpcg66Decisiontraitementpcg66 ...
	 *
	 * @package app.Model
	 */
	class Decisiondossierpcg66Decisiontraitementpcg66 extends AppModel
	{
		public $name = 'Decisiondossierpcg66Decisiontraitementpcg66';

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
			'Decisiontraitementpcg66' => array(
				'className' => 'Decisiontraitementpcg66',
				'foreignKey' => 'decisiontraitementpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>