<?php
	/**
	 * Code source de la classe Actionrole.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Actionrole ...
	 *
	 * @package app.Model
	 */
	class Actionrole extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Actionrole';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * @var array
		 */
		public $belongsTo = array(
			'Role' => array(
				'className' => 'Role',
				'foreignKey' => 'role_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Categorieactionrole' => array(
				'className' => 'Categorieactionrole',
				'foreignKey' => 'categorieactionrole_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);
	}
?>