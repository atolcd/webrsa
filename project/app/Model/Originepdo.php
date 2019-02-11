<?php
	/**
	 * Code source de la classe Originepdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Originepdo ...
	 *
	 * @package app.Model
	 */
	class Originepdo extends AppModel
	{
		public $name = 'Originepdo';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'libelle';

		public $order = '%s.libelle';

		public $actsAs = array(
			'Desactivable',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $hasMany = array(
			'Propopdo' => array(
				'className' => 'Propopdo',
				'foreignKey' => 'originepdo_id',
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
				'foreignKey' => 'originepdo_id',
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
			'Poledossierpcg66' => array(
				'className' => 'Poledossierpcg66',
				'foreignKey' => 'originepdo_id',
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
					array( 'COUNT("Dossierpcg66"."id") AS "Originepdo__occurences"' )
				),
				'joins' => array(
					$this->join( 'Dossierpcg66' )
				),
				'recursive' => -1,
				'group' => $this->fields(),
				'order' => array( 'Originepdo.id ASC' )
			);
		}
	}
?>