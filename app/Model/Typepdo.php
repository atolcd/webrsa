<?php	
	/**
	 * Code source de la classe Typepdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Typepdo ...
	 *
	 * @package app.Model
	 */
	class Typepdo extends AppModel
	{
		public $name = 'Typepdo';

		public $displayField = 'libelle';

		public $actsAs = array(
			'ValidateTranslate',
			'Autovalidate2',
			'Enumerable' => array(
				'fields' => array(
					'originepcg',
					'cerparticulier'
				)
			)
		);

		public $order = 'Typepdo.id ASC';

		public $validate = array(
			'libelle' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
				array(
					'rule' => 'isUnique',
					'message' => 'Valeur déjà utilisée'
				),
			)
		);

		public $hasMany = array(
			'Propopdo' => array(
				'className' => 'Propopdo',
				'foreignKey' => 'typepdo_id',
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
				'foreignKey' => 'typepdo_id',
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
				'foreignKey' => 'typepdo_id',
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
         * Permet de connaître le nombre d'occurences de Dossierpcg dans 
         * lesquelles apparaît ce type de PDOs
         * @return array()
         */
        public function qdOccurences() {
			return array(
				'fields' => array_merge(
					$this->fields(),
					array( 'COUNT("Dossierpcg66"."id") AS "Typepdo__occurences"' )
				),
				'joins' => array( 
					$this->join( 'Dossierpcg66' )
				),
				'recursive' => -1,
				'group' => $this->fields(),
				'order' => array( 'Typepdo.id ASC' )
			);
		}
	}
?>