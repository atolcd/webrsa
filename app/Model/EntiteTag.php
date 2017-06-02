<?php
	/**
	 * Code source de la classe EntiteTag.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe EntiteTag ...
	 *
	 * @package app.Model
	 */
	class EntiteTag extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'EntiteTag';

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

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Tag' => array(
				'className' => 'Tag',
				'foreignKey' => 'tag_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Foyer' => array(
				'className' => 'Foyer',
				'foreignKey' => 'fk_value',
				'conditions' => array(
					'EntiteTag.modele' => 'Foyer'
				),
				'fields' => '',
				'order' => ''
			),
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'fk_value',
				'conditions' => array(
					'EntiteTag.modele' => 'Personne'
				),
				'fields' => '',
				'order' => ''
			),
		);
	}
?>