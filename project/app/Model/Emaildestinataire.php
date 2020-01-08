<?php
	/**
	 * Code source de la classe Emaildestinataire.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Emaildestinataire ...
	 *
	 * @package app.Model
	 */
	class Emaildestinataire extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Emaildestinataire';

		/**
		 * Table pour le modèle
		 *
		 * @var string
		 */
		public $useTable = 'emailsdestinataires';

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
		 * Associations "Has Many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Email' => array(
				'className' => 'Email',
				'foreignKey' => 'emaildestinataire_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);

		public $virtualFields = array(
			'info_complet' => array(
				'type' => 'string',
				'postgres' => '( COALESCE( "%s"."nom", \'\' ) || \' \' || "%s"."prenom" || \' : \' || "%s"."email" )'
			),
		);

	}