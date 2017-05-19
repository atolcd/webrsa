<?php
	/**
	 * Code source de la classe Ressourcemensuelle.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Ressourcemensuelle ...
	 *
	 * @package app.Model
	 */
	class Ressourcemensuelle extends AppModel
	{
		public $name = 'Ressourcemensuelle';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		public $validate = array(
			'nbheumentra' => array(
				'comparison' => array(
					'rule' => array( 'comparison', '<=', 744 ),
					'message' => 'Veuillez entrer un nombre de 744 au maximum ',
					'allowEmpty' => true
				)
			),
			// Montant d'abattement / neutralisation
			'mtabaneu' => array(
				'comparison_lt' => array(
					'rule' => array( 'comparison', '<=', 33333332 ),
					'message' => 'Veuillez entrer un montant compris entre 0 et 33 333 332',
					'allowEmpty' => true
				),
				'comparison_ge' => array(
					'rule' => array( 'comparison', '>=', 0 ),
					'message' => 'Veuillez entrer un montant compris entre 0 et 33 333 332',
					'allowEmpty' => true
				),
				'between' => array(
					'rule' => array( 'between', 0, 11 ),
					'message' => 'Veuillez entrer au maximum 11 caractères',
					'allowEmpty' => true
				)
			)
		);

		public $belongsTo = array(
			'Ressource' => array(
				'className' => 'Ressource',
				'foreignKey' => 'ressource_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Detailressourcemensuelle' => array(
				'className' => 'Detailressourcemensuelle',
				'foreignKey' => 'ressourcemensuelle_id',
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

		public $hasAndBelongsToMany = array(
			'Ressource' => array(
				'className' => 'Ressource',
				'joinTable' => 'ressources_ressourcesmensuelles',
				'foreignKey' => 'ressourcemensuelle_id',
				'associationForeignKey' => 'ressource_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'RessourceRessourcemensuelle'
			),
			'Detailressourcemensuelle' => array(
				'className' => 'Detailressourcemensuelle',
				'joinTable' => 'detailressourcemensuelle_ressourcemensuelle',
				'foreignKey' => 'ressourcemensuelle_id',
				'associationForeignKey' => 'detailressourcemensuelle_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'DetailressourcemensuelleRessourcemensuelle'
			)
		);
	}
?>