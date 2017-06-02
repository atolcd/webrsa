<?php
	/**
	 * Code source de la classe Service66.
	 *
	 * @package app.Model
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Model.php.
	 */

	/**
	 * La classe Service66 ...
	 *
	 * @package app.Model
	 */
	class Service66 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Service66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
		);
		
		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'service66_id',
			),
			'FichedeliaisonExpediteur' => array(
				'className' => 'Fichedeliaison',
				'foreignKey' => 'expediteur_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
			'FichedeliaisonDestinataire' => array(
				'className' => 'Fichedeliaison',
				'foreignKey' => 'destinataire_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
		);
	}