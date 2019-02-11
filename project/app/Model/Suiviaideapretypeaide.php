<?php
	/**
	 * Code source de la classe Suiviaideapretypeaide.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Suiviaideapretypeaide ...
	 *
	 * @package app.Model
	 */
	class Suiviaideapretypeaide extends AppModel
	{
		public $name = 'Suiviaideapretypeaide';

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
			'Occurences',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Option' );

		public $belongsTo = array(
			'Suiviaideapre' => array(
				'className' => 'Suiviaideapre',
				'foreignKey' => 'suiviaideapre_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Surcharge de la méthode enums pour ajouter la civilité.
		 *
		 * @return array
		 */
		public function enums() {
			$options = parent::enums();
			$options[$this->alias]['typeaide'] = $this->Option->natureAidesApres();
			return $options;
		}
	}
?>