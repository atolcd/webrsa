<?php
	/**
	 * Code source de la classe Corpuspdv93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
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
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Pgsqlcake.PgsqlAutovalidate',
			'Formattable',
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