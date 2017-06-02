<?php
	/**
	 * Code source de la classe Statutpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Statutpdo ...
	 *
	 * @package app.Model
	 */
	class Statutpdo extends AppModel
	{
		public $name = 'Statutpdo';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'libelle';

		public $order = array( '%s.libelle' );

		public $actsAs = array(
			'Desactivable' => array(
				'fieldName' => 'isactif'
			),
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $hasAndBelongsToMany = array(
			'Propopdo' => array(
				'className' => 'Propopdo',
				'joinTable' => 'propospdos_statutspdos',
				'foreignKey' => 'statutpdo_id',
				'associationForeignKey' => 'propopdo_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'PropopdoStatutpdo'
			),
			'Personnepcg66' => array(
				'className' => 'Personnepcg66',
				'joinTable' => 'personnespcgs66_statutspdos',
				'foreignKey' => 'statutpdo_id',
				'associationForeignKey' => 'personnepcg66_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Personnepcg66Statutpdo'
			)
		);

         /**
         * Permet de connaître le nombre d'occurences de Personnepcg66 dans
         * lesquelles apparaît ce statut PDOs
         * @return array()
         */
        public function qdOccurences() {
			return array(
				'fields' => array_merge(
					$this->fields(),
					array( 'COUNT("Personnepcg66"."id") AS "Statutpdo__occurences"' )
				),
				'joins' => array(
					$this->join( 'Personnepcg66Statutpdo' ),
                    $this->Personnepcg66Statutpdo->join( 'Personnepcg66' )
				),
				'recursive' => -1,
				'group' => $this->fields(),
				'order' => array( 'Statutpdo.libelle ASC' )
			);
		}
	}
?>
