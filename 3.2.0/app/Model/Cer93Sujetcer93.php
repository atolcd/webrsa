<?php
	/**
	 * Fichier source du modèle Cer93Sujetcer93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Classe Cer93Sujetcer93.
	 *
	 * @package app.Model
	 */
	class Cer93Sujetcer93 extends AppModel
	{

		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Cer93Sujetcer93';

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
		 * Liaisons "belongsTo" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Cer93' => array(
				'className' => 'Cer93',
				'foreignKey' => 'cer93_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Soussujetcer93' => array(
				'className' => 'Soussujetcer93',
				'foreignKey' => 'soussujetcer93_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Sujetcer93' => array(
				'className' => 'Sujetcer93',
				'foreignKey' => 'sujetcer93_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Valeurparsoussujetcer93' => array(
				'className' => 'Valeurparsoussujetcer93',
				'foreignKey' => 'valeurparsoussujetcer93_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>