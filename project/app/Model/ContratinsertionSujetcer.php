<?php
	/**
	 * Code source de la classe ContratinsertionSujetcer.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe ContratinsertionSujetcer ...
	 *
	 * @package app.Model
	 */
	class ContratinsertionSujetcer extends AppModel
	{
		public $name = 'ContratinsertionSujetcer';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

			/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'contratsinsertion_sujetscers';



		public $belongsTo = array(
			'Sujetcer' => array(
				'className' => 'Sujetcer',
				'foreignKey' => 'sujetcer_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Soussujetcer' => array(
				'className' => 'Soussujetcer',
				'foreignKey' => 'soussujetcer_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Valeurparsoussujetcer' => array(
				'className' => 'Valeurparsoussujetcer',
				'foreignKey' => 'valeurparsoussujetcer_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'contratinsertion_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}