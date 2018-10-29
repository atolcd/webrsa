<?php
	/**
	 * Code source de la classe Titrecreancier.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe TitreCreance ...
	 *
	 * @package app.Model
	 */
	class Titrecreancier extends AppModel
	{
		public $name = 'Titrecreancier';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $fakeInLists = array(
			'haspiecejointe' => array('0', '1'),
			'typetitre' => array('A', 'B'),
			'etatranstitr' => array('CRE', 'VAL', 'SEN', 'PAY','SUP')
		);

		public $validate = array(
			'creances_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'qual' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'MR', 'MME'
						)
					)
				),
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			)
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Creance' => array(
				'className' => 'Creance',
				'foreignKey' => 'creance_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			)
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Titrecreancier\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
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
		 * Retourne l'id d'une creance à partir de l'id d'un TitreCreance.
		 *
		 * @param integer $titrecreancier_id
		 * @return integer
		 */
		public function creanceId( $titrecreancier_id ) {
			$qd_creance = array(
				'fields' => array( 'Titrecreancier.creance_id' ),
				'joins' => array(
					$this->join( 'Creance', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					'Titrecreancier.id' => $titrecreancier_id
				),
				'recursive' => -1
			);
			$creance = $this->find('first', $qd_creance);

			if( !empty( $creance ) ) {
				return $creance['Titrecreancier']['creance_id'];
			}
			else {
				return null;
			}
		}

		/**
		 * Retourne l'id d'un foyer à partir de l'id d'un TitreCreance.
		 *
		 * @param integer $titrecreancier_id
		 * @return integer
		 */
		public function foyerId( $creance_id ) {
			$qd_creance = "SELECT foyer_id FROM Creances WHERE id = ".$creance_id." LIMIT 1";
			$creance = $this->query($qd_creance);
			if( !empty( $creance ) ) {
				return $creance[0][0]['foyer_id'];
			}
			else {
				return null;
			}
		}

		/**
		 * Retourne l'id d'un dossier à partir de l'id d'un TitreCreance.
		 *
		 * @param integer $titrecreancier_id
		 * @return integer
		 */
		public function dossierId( $creance_id ) {
			$qd_creance = "SELECT Foyers.dossier_id FROM Creances INNER JOIN Foyers ON Foyers.id = Creances.foyer_id WHERE Creances.id = ".$creance_id." LIMIT 1";
			$creance = $this->query($qd_creance);
			if( !empty( $creance ) ) {
				return $creance[0][0]['dossier_id'];
			}
			else {
				return null;
			}

		}
	}
?>