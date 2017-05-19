<?php
	/**
	 * Code source de la classe Poledossierpcg66User.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	/**
	 * La classe Poledossierpcg66User ...
	 *
	 * @package app.Model
	 */
	class Poledossierpcg66User extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Poledossierpcg66User';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

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
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Poledossierpcg66' => array(
				  'className' => 'Poledossierpcg66',
				  'foreignKey' => 'poledossierpcg66_id',
				  'conditions' => null,
				  'type' => null,
				  'fields' => null,
				  'order' => null,
				  'counterCache' => null
			),
			'User' => array(
				  'className' => 'User',
				  'foreignKey' => 'user_id',
				  'conditions' => null,
				  'type' => null,
				  'fields' => null,
				  'order' => null,
				  'counterCache' => null
			)
		);
	}
?>