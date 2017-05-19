<?php
	/**
	 * Code source de la classe Decisionpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Decisionpdo ...
	 *
	 * @package app.Model
	 */
	class Decisionpdo extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Decisionpdo';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'libelle';

		/**
		 * Tri par défaut pour ce modèle.
		 *
		 * @var array
		 */
		public $order = array( '%s.libelle' );

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Desactivable' => array(
				'fieldName' => 'isactif'
			),
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Decisionpropopdo' => array(
				'className' => 'Decisionpropopdo',
				'foreignKey' => 'decisionpdo_id',
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
			'Decisionpersonnepcg66' => array(
				'className' => 'Decisionpersonnepcg66',
				'foreignKey' => 'decisionpdo_id',
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
            'Decisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'foreignKey' => 'decisionpdo_id',
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
		 * Retourne la liste des modèles odt paramétrés pour le impressions de
		 * cette classe.
		 *
		 * @return array
		 */
		public function modelesOdt() {
			$prefix = 'PDO'.DS;

			$items = $this->find(
				'all',
				array(
					'fields' => array(
						'( \''.$prefix.'\' || "'.$this->alias.'"."modeleodt" || \'.odt\' ) AS "'.$this->alias.'__modele"',
					),
					'recursive' => -1,
					'conditions' => array(
						"{$this->alias}.modeleodt IS NOT NULL"
					)
				)
			);
			return Set::extract( $items, '/'.$this->alias.'/modele' );
		}


        /**
         * Permet de connaître le nombre d'occurences de Dossierpcg dans
         * lesquelles apparaît ce type de PDOs
         * @return array()
         */
        public function qdOccurences() {
			return array(
				'fields' => array_merge(
					$this->fields(),
					array( 'COUNT("Decisiondossierpcg66"."id") AS "Decisionpdo__occurences"' )
				),
				'joins' => array(
					$this->join( 'Decisiondossierpcg66' )
				),
				'recursive' => -1,
				'group' => $this->fields(),
				'order' => array( 'Decisionpdo.id ASC' )
			);
		}

		/**
		 * Surcharge de la méthode enums pour ajouter les décisions de CER
		 * particuliers au CG 66.
		 *
		 * @return array
		 */
		public function enums() {
			$enums = parent::enums();

			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$enums[$this->alias]['decisioncerparticulier'] = ClassRegistry::init('Contratinsertion')->enum('decision_ci');
			}

			return $enums;
		}
	}
?>