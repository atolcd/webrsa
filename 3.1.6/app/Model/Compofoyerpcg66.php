<?php	
	/**
	 * Code source de la classe Compofoyerpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Compofoyerpcg66 ...
	 *
	 * @package app.Model
	 */
	class Compofoyerpcg66 extends AppModel
	{
		public $name = 'Compofoyerpcg66';

		public $recursive = -1;

		public $actsAs = array(
			'Autovalidate2'
		);

		public $hasMany = array(
			'Questionpcg66' => array(
				'className' => 'Questionpcg66',
				'foreignKey' => 'compofoyerpcg66_id',
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
				'foreignKey' => 'compofoyerpcg66_id',
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
				'foreignKey' => 'compofoyerpcg66_id',
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
			'name' => array(
				array(
					'rule' => 'isUnique',
					'message' => 'Valeur déjà utilisée'
				)
			)
		);
		
		public function qdOccurences() {
			return array(
				'fields' => array_merge(
					$this->fields(),
					array( 'COUNT("Questionpcg66"."id") AS "Compofoyerpcg66__occurences"' )
				),
				'joins' => array( 
					$this->join( 'Questionpcg66' )
				),
				'recursive' => -1,
				'group' => $this->fields(),
				'order' => array( 'Compofoyerpcg66.id ASC' )
			);
		}
	}
?>