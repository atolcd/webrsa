<?php	
	/**
	 * Code source de la classe Poledossierpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Poledossierpcg66 ...
	 *
	 * @package app.Model
	 */
	class Poledossierpcg66 extends AppModel
	{
		public $name = 'Poledossierpcg66';

		public $recursive = -1;

		public $actsAs = array(
			'Pgsqlcake.PgsqlAutovalidate',
			'Formattable'
		);
        
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