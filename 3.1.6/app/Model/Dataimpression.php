<?php
	/**
	 * Code source de la classe Dataimpression.
	 *
	 * @package app.Model
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Model.php.
	 */

	/**
	 * La classe Dataimpression ...
	 *
	 * @package app.Model
	 */
	class Dataimpression extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Dataimpression';

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
		public $actsAs = array();

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Traitementpcg66' => array(
				'className' => 'Traitementpcg66',
				'foreignKey' => 'fk_value',
				'conditions' => array(
					'Dataimpression.modele = \'Traitementpcg66\''
				),
				'fields' => '',
				'order' => ''
			),
		);
	}
?>