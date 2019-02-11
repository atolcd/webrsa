<?php
	/**
	 * Code source de la classe Detailressourcemensuelle.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Detailressourcemensuelle ...
	 *
	 * @package app.Model
	 */
	class Detailressourcemensuelle extends AppModel
	{
		public $name = 'Detailressourcemensuelle';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Behaviors utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		public $validate = array(
			// Montant de la ressource selon la nature
			'mtnatressmen' => array(
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

		/**
		 * Liste de champs et de valeurs possibles qui ne peuvent pas être mis en
		 * règle de validation inList ou en contrainte dans la base de données en
		 * raison des valeurs actuellement en base, mais pour lequels un ensemble
		 * fini de valeurs existe.
		 *
		 * @see AppModel::enums
		 *
		 * @var array
		 */
		public $fakeInLists = array(
			'abaneu' => array('A', 'N'),
			'natress' => array(
				'000', '001', '002', '003', '004', '005', '006', '007', '008',
				'009', '010', '011', '012', '013', '014', '020', '021', '022',
				'023', '024', '025', '026', '027', '028', '029', '030', '031',
				'032', '033', '034', '040', '041', '042', '043', '044', '050',
				'051', '052', '053', '054', '055', '060', '061', '062', '063',
				'064', '065', '066', '070', '071', '072', '080', '082', '083',
				'085', '087', '088', '100', '200', '201', '203', '204', '205',
				'206', '207', '211', '212', '213', '214', '215', '216', '217',
				'300', '301', '302', '303', '305', '306', '400', '402', '403',
				'404', '405', '406', '407', '408', '409', '410', '500', '600',
				'602', '777', '888', '999'
			)
		);

		public $belongsTo = array(
			'Ressourcemensuelle' => array(
				'className' => 'Ressourcemensuelle',
				'foreignKey' => 'ressourcemensuelle_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasAndBelongsToMany = array(
			'Ressourcemensuelle' => array(
				'className' => 'Ressourcemensuelle',
				'joinTable' => 'detailsressourcesmensuelles_ressourcesmensuelles',
				'foreignKey' => 'detailressourcemensuelle_id',
				'associationForeignKey' => 'ressourcemensuelle_id',
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