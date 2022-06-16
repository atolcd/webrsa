<?php
	/**
	 * Fichier source du modèle Valeurparsoussujetcer.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Classe Valeurparsoussujetcer.
	 *
	 * @package app.Model
	 */
	class Valeurparsoussujetcer extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Valeurparsoussujetcer';

		public $useTable = 'valeursparsoussujetscers';


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
			'Soussujetcer' => array(
				'className' => 'Soussujetcer',
				'foreignKey' => 'soussujetcer_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
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
				'foreignKey' => 'valeurparsoussujetcer_id',
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