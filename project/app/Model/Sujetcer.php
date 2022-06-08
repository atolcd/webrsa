<?php
	/**
	 * Fichier source du modèle Sujetcer.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Classe Sujetcer.
	 *
	 * @package app.Model
	 */
	class Sujetcer extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Sujetcer';

		public $useTable = 'sujetscers';


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
		 * Liaisons "hasMany" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Soussujetcer' => array(
				'className' => 'Soussujetcer',
				'foreignKey' => 'sujetcer_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		/**
		 * Liaisons "hasAndBelongsToMany" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
            'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'joinTable' => 'contratsinsertion_sujetscers',
				'foreignKey' => 'sujetcer_id',
				'associationForeignKey' => 'contratinsertion_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
			),
		);
	}
?>