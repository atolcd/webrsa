<?php
	/**
	 * Code source de la classe Suiviinstruction.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Suiviinstruction ...
	 *
	 * @package app.Model
	 */
	class Suiviinstruction extends AppModel
	{
		public $name = 'Suiviinstruction';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'typeserins';

		/**
		 * Autres modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Option' );

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		public $belongsTo = array(
			'Dossier' => array(
				'className' => 'Dossier',
				'foreignKey' => 'dossier_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Serviceinstructeur' => array(
				'className' => 'Serviceinstructeur',
				'foreignKey' => false,
				'conditions' => array(
					'Suiviinstruction.numdepins = Serviceinstructeur.numdepins',
					'Suiviinstruction.typeserins = Serviceinstructeur.typeserins',
					'Suiviinstruction.numcomins = Serviceinstructeur.numcomins',
					'Suiviinstruction.numagrins = Serviceinstructeur.numagrins'
				),
				'fields' => '',
				'order' => ''
			),
		);

		public $validate = array(
			'suiirsa' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'date_etat_instruction' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'nomins' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'numdepins' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'typeserins' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'numcomins' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'numagrins' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
		);

		/**
		 * Liste de champs et de valeurs possibles qui ne peuvent pas être mis en
		 * règle de validation inList ou en contrainte dans la base de données en
		 * raison des valeurs actuellement en base, mais pour lequels un ensemble
		 * fini de valeurs existe.
		 *
		 * @see AppModel::enums
		 *
		 * @var array
		 */
		public $fakeInLists = array(
			'suiirsa' => array('01', '11', '12', '13', '14', '21', '22', '23', '24', '31', '32', '33', '34'),
		);

		/**
		 *
		 * @deprecated Utiliser sqDernier2 puis renommer
		 *
		 * @param type $field
		 * @return type
		 */
		public function sqDerniere($field) {
			$dbo = $this->getDataSource( $this->useDbConfig );
			$table = $dbo->fullTableName( $this, false, false );
			return "
				SELECT {$table}.id
					FROM {$table}
					WHERE
						{$table}.dossier_id = ".$field."
					ORDER BY {$table}.id DESC
					LIMIT 1
			";
		}

		/**
		 * Retourne la sous-requête permettant de trouver le dernier suivi
		 * d'instruction d'un dossier par-rapport au champ date_etat_instruction.
		 *
		 * @param type $field Le champ de la requête principale représentant l'id du dossier
		 * @return string
		 */
		public function sqDernier2( $field = 'Dossier.id' ) {
			$dbo = $this->getDataSource( $this->useDbConfig );
			$alias = $dbo->fullTableName( $this, false, false );

			return $this->sq(
				array(
					'alias' => $alias,
					'fields' => array( "{$alias}.id" ),
					'conditions' => array( "{$alias}.dossier_id = {$field}" ),
					'order' => array( "{$alias}.date_etat_instruction DESC" ),
					'limit' => 1,
				)
			);
		}

		/**
		 * Retourne l'id du dossier auquel est lié un enregistrement.
		 *
		 * @param integer $id L'id de l'enregistrement
		 * @return integer
		 */
		public function dossierId( $id ) {
			$querydata = array(
				'fields' => array( "{$this->alias}.dossier_id" ),
				'conditions' => array(
					"{$this->alias}.id" => $id
				),
				'recursive' => -1
			);

			$result = $this->find( 'first', $querydata );

			if( !empty( $result ) ) {
				return $result[$this->alias]['dossier_id'];
			}
			else {
				return null;
			}
		}

		/**
		 * Retourne la liste des options possibles pour ce modèle.
		 *
		 * @return array
		 */
		public function enums() {
			$result = parent::enums();

			$result[$this->alias]['typeserins'] = $this->Option->typeserins();

			return $result;
		}
	}
?>