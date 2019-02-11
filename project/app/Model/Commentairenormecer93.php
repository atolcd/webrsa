<?php
	/**
	 * Fichier source du modèle Commentairenormecer93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Classe Commentairenormecer93.
	 *
	 * @package app.Model
	 */
	class Commentairenormecer93 extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Commentairenormecer93';

		/**
		 * Tri par défaut
		 *
		 * @var array
		 */
		public $order = array( 'Commentairenormecer93.name ASC' );

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
		 * Liaisons "hasAndBelongsToMany" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
            'Histochoixcer93' => array(
				'className' => 'Histochoixcer93',
				'joinTable' => 'commentairesnormescers93_histoschoixcers93',
				'foreignKey' => 'commentairenormecer93_id',
				'associationForeignKey' => 'histochoixcer93_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Commentairenormecer93Histochoixcer93'
			),
		);
	}
?>