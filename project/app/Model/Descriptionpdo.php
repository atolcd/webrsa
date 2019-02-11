<?php
	/**
	 * Code source de la classe Descriptionpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Descriptionpdo ...
	 *
	 * @package app.Model
	 */
	class Descriptionpdo extends AppModel
	{
		public $name = 'Descriptionpdo';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'sensibilite' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array(NOT_BLANK_RULE_NAME),
				),
			)
		);

		public $hasMany = array(
			'Traitementpdo' => array(
				'className' => 'Traitementpdo',
				'foreignKey' => 'descriptionpdo_id',
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
			'Traitementpcg66' => array(
				'className' => 'Traitementpcg66',
				'foreignKey' => 'descriptionpdo_id',
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
         * Permet de connaître le nombre d'occurences de Traitement PCGs dans
         * lesquelles apparaît cette description de Traitements PCGs
         * @return array()
         */
        public function qdOccurences() {
			return array(
				'fields' => array_merge(
					$this->fields(),
					array( 'COUNT("Traitementpcg66"."id") AS "Descriptionpdo__occurences"' )
				),
				'joins' => array(
					$this->join( 'Traitementpcg66' )
				),
				'recursive' => -1,
				'group' => $this->fields(),
				'order' => array( 'Descriptionpdo.id ASC' )
			);
		}
	}
?>