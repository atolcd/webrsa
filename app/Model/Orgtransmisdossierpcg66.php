<?php
	/**
	 * Code source de la classe Orgtransmisdossierpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Orgtransmisdossierpcg66 ...
	 *
	 * @package app.Model
	 */
	class Orgtransmisdossierpcg66 extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Orgtransmisdossierpcg66';

        public $recursive = -1;

		public $actsAs = array(
			'Formattable',
			'Pgsqlcake.PgsqlAutovalidate'
		);
        
        
        public $belongsTo = array(
			'Poledossierpcg66' => array(
				'className' => 'Poledossierpcg66',
				'foreignKey' => 'poledossierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
        );

        /**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Decisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'foreignKey' => 'orgtransmisdossierpcg66_id',
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
		
		public $hasAndBelongsToMany = array(
			'Notificationdecisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'joinTable' => 'decisionsdossierspcgs66_orgstransmisdossierspcgs66',
				'foreignKey' => 'orgtransmisdossierpcg66_id',
				'associationForeignKey' => 'decisiondossierpcg66_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Decdospcg66Orgdospcg66'
			)
		);
		
	}
?>