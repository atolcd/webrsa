<?php
	/**
	 * Code source de la classe Membreep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Membreep ...
	 *
	 * @package app.Model
	 */

    define( 'CHAMP_FACULTATIF', Configure::read( 'Cg.departement' ) != 93 );

	class Membreep extends AppModel
	{
		public $name = 'Membreep';

		public $actsAs = array(
			'Autovalidate2',
			'ValidateTranslate',
			'Enumerable' => array(
				'fields' => array(
					'qual'
				)
			),
			'Formattable' => array(
				'phone' => array( 'tel' )
			),
			'Validation.ExtraValidationRules',
		);

		public $validate = array(
			'mail' => array(
				'email' => array(
					'rule' => 'email',
					'allowEmpty' => true,
					'message' => 'Le mail n\'est pas valide'
				)
			),
            'organisme' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF
				)
			),
            'tel' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF
				),
				'phoneFr' => array(
					'rule' => array( 'phoneFr' ),
					'allowEmpty' => true,
				),
			),
            'numvoie' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF
				)
			),
            'typevoie' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF
				)
			),
            'nomvoie' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF
				)
			),
            'ville' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF
				)
			),
            'codepostal' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF
				)
			)
		);

		public $belongsTo = array(
			'Fonctionmembreep' => array(
				'className' => 'Fonctionmembreep',
				'foreignKey' => 'fonctionmembreep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasAndBelongsToMany = array(
			'Commissionep' => array(
				'className' => 'Commissionep',
				'joinTable' => 'commissionseps_membreseps',
				'foreignKey' => 'membreep_id',
				'associationForeignKey' => 'commissionep_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'CommissionepMembreep'
			),
			'Ep' => array(
				'className' => 'Ep',
				'joinTable' => 'eps_membreseps',
				'foreignKey' => 'membreep_id',
				'associationForeignKey' => 'ep_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'EpMembreep' // TODO
			),
		);


		public function search( $criteres ) {
			$conditions = array();

			foreach( array( 'nom', 'prenom', 'ville', 'organisme' ) as $critereMembre ) {
				if( isset( $criteres['Membreep'][$critereMembre] ) && !empty( $criteres['Membreep'][$critereMembre] ) ) {
					$conditions[] = 'UPPER(Membreep.'.$critereMembre.') LIKE \''.$this->wildcard( strtoupper( replace_accents( $criteres['Membreep'][$critereMembre] ) ) ).'\'';
				}
			}

			if( isset( $criteres['Membreep']['fonctionmembreep_id'] ) && !empty( $criteres['Membreep']['fonctionmembreep_id'] ) ) {
				$conditions[] = array( 'Membreep.fonctionmembreep_id' => $criteres['Membreep']['fonctionmembreep_id'] );
			}


			$query = array(
				'fields' => array_merge(
					$this->fields(),
					$this->Fonctionmembreep->fields()
				),
				'order' => array( 'Membreep.nom ASC', 'Membreep.prenom ASC' ),
				'joins' => array(
					$this->join( 'Fonctionmembreep', array( 'type' => 'INNER' ) )
				),
				'recursive' => -1,
				'conditions' => $conditions
			);

			return $query;
		}
	}
?>
