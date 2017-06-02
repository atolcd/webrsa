<?php	
	/**
	 * Code source de la classe Decisionpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Decisionpdo ...
	 *
	 * @package app.Model
	 */
	class Decisionpdo extends AppModel
	{
		public $name = 'Decisionpdo';

		public $displayField = 'libelle';

		public $order = 'Decisionpdo.id ASC';

		public $actsAs = array(
//			'Autovalidate2',
            'Pgsqlcake.PgsqlAutovalidate',
			'Enumerable' => array(
				'fields' => array(
					'clos',
					'nbmoisecheance',
					'cerparticulier'
				)
			),
			'Formattable'
		);

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

		public $validate = array(
			'libelle' => array(
				array(
					'rule' => 'isUnique',
					'message' => 'Cette valeur est déjà utilisée'
				),
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
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
	}
?>