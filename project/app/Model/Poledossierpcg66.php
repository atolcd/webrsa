<?php
	/**
	 * Code source de la classe Poledossierpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Poledossierpcg66 ...
	 *
	 * @package app.Model
	 */
	class Poledossierpcg66 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Poledossierpcg66';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'WebrsaPoledossierpcg66' );

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
        public $belongsTo = array(
			'Originepdo' => array(
				'className' => 'Originepdo',
				'foreignKey' => 'originepdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typepdo' => array(
				'className' => 'Typepdo',
				'foreignKey' => 'typepdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
        );

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'poledossierpcg66_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
            'Dossierpcg66' => array(
				'className' => 'Dossierpcg66',
				'foreignKey' => 'poledossierpcg66_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
            'Orgtransmisdossierpcg66' => array(
				'className' => 'Orgtransmisdossierpcg66',
				'foreignKey' => 'poledossierpcg66_id',
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
			'Ancienuserpoledossierpcg66' => array(
				'className' => 'User',
				'joinTable' => 'polesdossierspcgs66_users',
				'foreignKey' => 'poledossierpcg66_id',
				'associationForeignKey' => 'user_id',
				'unique' => true,
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'finderQuery' => null,
				'deleteQuery' => null,
				'insertQuery' => null,
				'with' => 'Poledossierpcg66User'
			)
		);

        /**
         * Permet de connaître le nombre d'occurences de Dossierpcg dans
         * lesquelles apparaît ce pôle
         * @return array()
         */
        public function qdOccurences() {
			return array(
				'fields' => array_merge(
					$this->fields(),
					array( 'COUNT("Dossierpcg66"."id") AS "Poledossierpcg66__occurences"' )
				),
				'joins' => array(
					$this->join( 'Dossierpcg66' )
				),
				'recursive' => -1,
				'group' => $this->fields(),
				'order' => array( 'Poledossierpcg66.id ASC' )
			);
		}
	}
?>