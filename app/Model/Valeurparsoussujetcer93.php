<?php
	/**
	 * Fichier source du modèle Valeurparsoussujetcer93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Classe Valeurparsoussujetcer93.
	 *
	 * @package app.Model
	 */
	class Valeurparsoussujetcer93 extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Valeurparsoussujetcer93';

		/**
		 * Tri par défaut
		 *
		 * @var array
		 */
		public $order = array( 'Valeurparsoussujetcer93.name ASC' );

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Liaisons "belongsTo" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Soussujetcer93' => array(
				'className' => 'Soussujetcer93',
				'foreignKey' => 'soussujetcer93_id',
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
			'Cer93Sujetcer93' => array(
				'className' => 'Cer93Sujetcer93',
				'foreignKey' => 'valeurparsoussujetcer93_id',
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
	}
?>