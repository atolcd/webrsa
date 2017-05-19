<?php
	/**
	 * Code source de la classe Corpuspdv93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Corpuspdv93 ...
	 *
	 * @package app.Model
	 */
	class Corpuspdv93 extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Corpuspdv93';

		/**
		 * Behaviors utilisés.
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
			'Tableausuivipdv93' => array(
				 'className' => 'Tableausuivipdv93',
				 'foreignKey' => 'tableausuivipdv93_id',
				 'conditions' => null,
				 'type' => 'INNER',
				 'fields' => null,
				 'order' => null,
				 'counterCache' => null
			),
		);
	}
?>