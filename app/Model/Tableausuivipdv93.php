<?php
	/**
	 * Code source de la classe Tableausuivipdv93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Tableausuivipdv93 ...
	 *
	 * @package app.Model
	 */
	class Tableausuivipdv93 extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Tableausuivipdv93';

		/**
		 * Récursivité par défaut de ce modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Pgsqlcake.PgsqlAutovalidate',
			'Formattable',
		);

		/**
		 * Liste des modèles supplémentaires utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'WebrsaTableausuivipdv93'
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Communautesr' => array(
				'className' => 'Communautesr',
				'foreignKey' => 'communautesr_id',
				'conditions' => null,
				'fields' => null,
				'order' => null
			),
			'Pdv' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
				'conditions' => null,
				'fields' => null,
				'order' => null
			),
			'Referent' => array(
				'className' => 'Referent',
				'foreignKey' => 'referent_id',
				'conditions' => null,
				'fields' => null,
				'order' => null
			),
			'Photographe' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => null,
				'fields' => null,
				'order' => null
			),
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Populationb3pdv93' => array(
				'className' => 'Populationb3pdv93',
				'foreignKey' => 'tableausuivipdv93_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
			'Populationb4b5pdv93' => array(
				'className' => 'Populationb4b5pdv93',
				'foreignKey' => 'tableausuivipdv93_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
			'Populationb6pdv93' => array(
				'className' => 'Populationb6pdv93',
				'foreignKey' => 'tableausuivipdv93_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
			'Populationd1d2pdv93' => array(
				'className' => 'Populationd1d2pdv93',
				'foreignKey' => 'tableausuivipdv93_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
			'Corpuspdv93' => array(
				'className' => 'Corpuspdv93',
				'foreignKey' => 'tableausuivipdv93_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
		);

		/**
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'joinTable' => 'structuresreferentes_tableauxsuivispdvs93',
				'foreignKey' => 'tableausuivipdv93_id',
				'associationForeignKey' => 'structurereferente_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'StructurereferenteTableausuivipdv93'
			)
		);
	}
?>
