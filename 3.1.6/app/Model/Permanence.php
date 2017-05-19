<?php	
	/**
	 * Code source de la classe Permanence.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Permanence ...
	 *
	 * @package app.Model
	 */
	class Permanence extends AppModel {
		public $name = 'Permanence';

		public $displayField = 'libpermanence';

		
		public $actsAs = array(
			'Enumerable' => array(
				'fields' => array(
					'actif' => array( 'type' => 'no', 'domain' => 'default' )
				)
			),
			'Formattable'
		);
		
		public $validate = array(
			'structurereferente_id' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'libpermanence' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
			'typevoie' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'nomvoie' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'codepos' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'ville' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'numtel' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			)
		);
		//The Associations below have been created with all possible keys, those that are not needed can be removed

		public $belongsTo = array(
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Rendezvous' => array(
				'className' => 'Rendezvous',
				'foreignKey' => 'permanence_id',
				'dependent' => false,
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
		*
		*/

		public function listOptions() {
			$conditions = array();
			$conditions = array( 'Permanence.actif' => 'O' );
			
			$tmp = $this->find(
				'all',
				array (
					'conditions' => $conditions,
					'fields' => array(
						'Permanence.id',
						'Permanence.structurereferente_id',
						'Permanence.libpermanence'
					),
					'recursive' => -1,
					'order' => 'Permanence.libpermanence ASC',
				)
			);

			$return = array();
			foreach( $tmp as $key => $value ) {
				$return[$value['Permanence']['structurereferente_id'].'_'.$value['Permanence']['id']] = $value['Permanence']['libpermanence'];
			}
			return $return;
		}
	}
?>