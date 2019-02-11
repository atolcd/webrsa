<?php
	/**
	 * Code source de la classe Situationpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Situationpdo ...
	 *
	 * @package app.Model
	 */
	class Situationpdo extends AppModel
	{
		public $name = 'Situationpdo';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'libelle';

		public $order = array( '%s.libelle' );

		/**
		 * Behaviors utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Desactivable' => array(
				'fieldName' => 'isactif'
			),
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'Situationpdo' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			)
		);

		public $hasAndBelongsToMany = array(
			'Propopdo' => array(
				'className' => 'Propopdo',
				'joinTable' => 'propospdos_situationspdos',
				'foreignKey' => 'situationpdo_id',
				'associationForeignKey' => 'propopdo_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'PropopdoSituationpdo'
			),
			'Personnepcg66' => array(
				'className' => 'Personnepcg66',
				'joinTable' => 'personnespcgs66_situationspdos',
				'foreignKey' => 'situationpdo_id',
				'associationForeignKey' => 'personnepcg66_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Personnepcg66Situationpdo'
			),
		);

		/**
		*	Récupération de la liste des situations liées à la personne
		*/

		public function listeMotifsPersonne( $personnepcg66_id ) {
			$listeSituation = $this->find(
				'list',
				array(
					'conditions' => array(
						'Situationpdo.id IN (
							'.$this->Personnepcg66Situationpdo->sq(
								array(
									'alias' => 'personnespcgs66_situationspdos',
									'fields' => array( 'personnespcgs66_situationspdos.situationpdo_id' ),
									'conditions' => array(
										'personnespcgs66_situationspdos.personnepcg66_id' => $personnepcg66_id
									),
									'contain' => false
								)
							).' )'
						),
						'recursive' => -1
					)
				);
			return $listeSituation;
		}

         /**
         * Permet de connaître le nombre d'occurences de Personnepcg66 dans
         * lesquelles apparaît cette situation PDOs
         * @return array()
         */
        public function qdOccurences() {
			return array(
				'fields' => array_merge(
					$this->fields(),
					array( 'COUNT("Personnepcg66"."id") AS "Situationpdo__occurences"' )
				),
				'joins' => array(
					$this->join( 'Personnepcg66Situationpdo' ),
                    $this->Personnepcg66Situationpdo->join( 'Personnepcg66' )
				),
				'recursive' => -1,
				'group' => $this->fields(),
				'order' => array( 'Situationpdo.libelle ASC' )
			);
		}
	}
?>