<?php
	/**
	 * Code source de la classe Decisiontraitementpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Decisiontraitementpcg66 ...
	 *
	 * @package app.Model
	 */
	class Decisiontraitementpcg66 extends AppModel
	{
		public $name = 'Decisiontraitementpcg66';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		public $belongsTo = array(
			'Traitementpcg66' => array(
				'className' => 'Traitementpcg66',
				'foreignKey' => 'traitementpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasAndBelongsToMany = array(
			'Decisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'joinTable' => 'decisionsdossierspcgs66_decisionstraitementspcgs66',
				'foreignKey' => 'decisiontraitementpcg66_id',
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
				'with' => 'Decisiondossierpcg66Decisiontraitementpcg66'
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
						'Decisiontraitementpcg66.personnepcg66_situationpdo_id IN (
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
	}
?>