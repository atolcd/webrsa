<?php
	/**
	 * Code source de la classe Motifcernonvalid66Propodecisioncer66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Motifcernonvalid66Propodecisioncer66 ...
	 *
	 * @package app.Model
	 */
	class Motifcernonvalid66Propodecisioncer66 extends AppModel
	{
		public $name = 'Motifcernonvalid66Propodecisioncer66';

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
			'Motifcernonvalid66' => array(
				'className' => 'Motifcernonvalid66',
				'foreignKey' => 'motifcernonvalid66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Propodecisioncer66' => array(
				'className' => 'Propodecisioncer66',
				'foreignKey' => 'propodecisioncer66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>