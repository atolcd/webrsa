<?php
	/**
	 * Code source de la classe DetailmoytransRev.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe DetailmoytransRev ...
	 *
	 * @package app.Model
	 */
	class DetailmoytransRev extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'DetailmoytransRev';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Behaviors utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'DspRev' => array(
				'className' => 'DspRev',
				'foreignKey' => 'dsp_rev_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);
	}
?>