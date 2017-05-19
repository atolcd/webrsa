<?php
	/**
	 * Code source de la classe Membreep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'FrValidation', 'Validation' );

	/**
	 * La classe Membreep ...
	 *
	 * @package app.Model
	 */

    define( 'CHAMP_FACULTATIF', Configure::read( 'Cg.departement' ) != 93 );

	class Membreep extends AppModel
	{
		public $name = 'Membreep';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Validation2.Validation2Formattable' => array(
				'Validation2.Validation2DefaultFormatter' => array(
					'stripNotAlnum' => '/^(tel)$/'
				)
			),
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'mail' => array(
				'email' => array(
					'rule' => array( 'email' ),
					'allowEmpty' => true,
					'message' => 'Le mail n\'est pas valide'
				)
			),
            'organisme' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF
				)
			),
            'tel' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF
				),
				'phone' => array(
					'rule' => array( 'phone', null, 'fr' ),
					'allowEmpty' => true
				)
			),
            'numvoie' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF
				)
			),
            'typevoie' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF
				)
			),
            'nomvoie' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF
				)
			),
            'ville' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF
				)
			),
            'codepostal' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF
				)
			)
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Option' );

		/**
		 * Champ virtuel "Nom complêt" (nomcomplet)
		 *
		 * @var array
		 */
		public $virtualFields = array(
			'nomcomplet' => array(
				'type'      => 'string',
				'postgres'  => 'COALESCE( "%s"."qual", \'\' ) || \' \' || COALESCE( "%s"."nom", \'\' ) || \' \' || COALESCE( "%s"."prenom", \'\' )'
			),
			'adresse' => array(
				'type'      => 'string',
				'postgres'  => 'COALESCE( "%s"."numvoie", \'\' ) || \' \' || COALESCE( "%s"."typevoie", \'\' ) || \' \' || COALESCE( "%s"."nomvoie", \'\' ) || \' \' || COALESCE( "%s"."compladr", \'\' ) || \' \' || COALESCE( "%s"."codepostal", \'\' ) || \' \' || COALESCE( "%s"."ville", \'\' )'
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

		/**
		 * Surcharge de la méthode enums pour ajouter les valeurs de type de
		 * voie.
		 *
		 * @return array
		 */
		public function enums() {
			$enums = parent::enums();

			$enums[$this->alias]['typevoie'] = $this->Option->libtypevoie();

			return $enums;
		}
	}
?>
