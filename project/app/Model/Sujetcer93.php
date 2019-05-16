<?php
	/**
	 * Fichier source du modèle Sujetcer93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Classe Sujetcer93.
	 *
	 * @package app.Model
	 */
	class Sujetcer93 extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Sujetcer93';

		/**
		 * Tri par défaut
		 *
		 * @var array
		 */
		public $order = array( 'Sujetcer93.name ASC' );

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
			'Dreesactionscer' => array(
				'className' => 'Dreesactionscer',
				'foreignKey' => 'dreesactionscer_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Liaisons "hasMany" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Soussujetcer93' => array(
				'className' => 'Soussujetcer93',
				'foreignKey' => 'sujetcer93_id',
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
            'Cer93' => array(
				'className' => 'Cer93',
				'joinTable' => 'cers93_sujetscers93',
				'foreignKey' => 'sujetcer93_id',
				'associationForeignKey' => 'cer93_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Cer93Sujetcer93'
			),
		);
	}
?>