<?php
	/**
	 * Code source de la classe Orgtransmisdossierpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Orgtransmisdossierpcg66 ...
	 *
	 * @package app.Model
	 */
	class Orgtransmisdossierpcg66 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Orgtransmisdossierpcg66';

		/**
		 * Tri par défaut pour ce modèle.
		 *
		 * @var array
		 */
		public $order = array( '%s.name' );

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Desactivable' => array(
				'fieldName' => 'isactif'
			),
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesComparison',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Règles de validation.
		 *
		 * @var array
		 */
		public $validate = array(
			'poledossierpcg66_id' => array(
				'notNullIf' => array(
					'rule' => array( 'notNullIf', 'generation_auto', true, array( '1' ) ),
					'message' => 'Un pôle lié est obligatoire en cas de création automatique d\'un dossier PCG'
				)
			)
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
        public $belongsTo = array(
			'Poledossierpcg66' => array(
				'className' => 'Poledossierpcg66',
				'foreignKey' => 'poledossierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
        );

        /**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Decisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'foreignKey' => 'orgtransmisdossierpcg66_id',
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
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
			'Notificationdecisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'joinTable' => 'decisionsdossierspcgs66_orgstransmisdossierspcgs66',
				'foreignKey' => 'orgtransmisdossierpcg66_id',
				'associationForeignKey' => 'decisiondossierpcg66_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Decdospcg66Orgdospcg66'
			)
		);
	}
?>