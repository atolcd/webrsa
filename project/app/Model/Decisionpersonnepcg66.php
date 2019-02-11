<?php
	/**
	 * Code source de la classe Decisionpersonnepcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Decisionpersonnepcg66 ...
	 *
	 * @package app.Model
	 */
	class Decisionpersonnepcg66 extends AppModel
	{
		public $name = 'Decisionpersonnepcg66';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Personnepcg66Situationpdo' => array(
				'className' => 'Personnepcg66Situationpdo',
				'foreignKey' => 'personnepcg66_situationpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Decisionpdo' => array(
				'className' => 'Decisionpdo',
				'foreignKey' => 'decisionpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		public $hasAndBelongsToMany = array(
			'Decisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'joinTable' => 'decisionsdossierspcgs66_decisionspersonnespcgs66',
				'foreignKey' => 'decisionpersonnepcg66_id',
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
				'with' => 'Decisiondossierpcg66Decisionpersonnepcg66'
			)
		);
		/**
		*	Récupération de la liste des situations liées à la personne
		*/

		public function listeDecisionsParPersonnepcg66( $personnepcg66_id ) {

			$personnepcg66situationpdo = $this->Personnepcg66Situationpdo->find(
				'all',
				array(
					'conditions' => array(
						'Personnepcg66Situationpdo.personnepcg66_id' => $personnepcg66_id
					),
					'contain' => false
				)
			);
			$personnepcg66situationpdo_id = array();
			foreach( $personnepcg66situationpdo as $i => $value ){
				$personnepcg66situationpdo_id[] = $value['Personnepcg66Situationpdo']['id'];
			}

			$listeDecisions = $this->find(
				'all',
				array(
					'conditions' => array(
						'Decisionpersonnepcg66.personnepcg66_situationpdo_id IN (
                            '.$this->Personnepcg66Situationpdo->sq(
                                array(
									'alias' => 'personnespcgs66_situationspdos',
                                    'fields' => array( 'personnespcgs66_situationspdos.id' ),
                                    'conditions' => array(
										'personnespcgs66_situationspdos.id' => $personnepcg66situationpdo_id
									),
									'contain' => false
								)
							).' )',
						),
						'contain' => array(
							'Personnepcg66Situationpdo' => array(
								'Situationpdo',
								'Personnepcg66'
							),
							'Decisionpdo'
						)
					)
				);

			return $listeDecisions;
		}

		/**
		 * Retourne l'id de la personne à laquelle est lié un enregistrement.
		 *
		 * @param integer $id L'id de l'enregistrement
		 * @return integer
		 */
		public function personneId( $id ) {
			$querydata = array(
				'fields' => array( "Personnepcg66.personne_id" ),
				'joins' => array(
					$this->join( 'Personnepcg66Situationpdo', array( 'type' => 'INNER' ) ),
					$this->Personnepcg66Situationpdo->join( 'Personnepcg66', array( 'type' => 'INNER' ) ),
				),
				'conditions' => array(
					"{$this->alias}.id" => $id
				),
				'recursive' => -1
			);

			$result = $this->find( 'first', $querydata );

			if( !empty( $result ) ) {
				return $result['Personnepcg66']['personne_id'];
			}
			else {
				return null;
			}
		}
	}
?>