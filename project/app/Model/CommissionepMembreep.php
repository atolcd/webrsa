<?php
	/**
	 * Code source de la classe CommissionepMembreep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe CommissionepMembreep ...
	 *
	 * @package app.Model
	 */
	class CommissionepMembreep extends AppModel
	{
		public $name = 'CommissionepMembreep';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Membreep' => array(
				'className' => 'Membreep',
				'foreignKey' => 'membreep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Commissionep' => array(
				'className' => 'Commissionep',
				'foreignKey' => 'commissionep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Remplacantmembreep' => array(
				'className' => 'Membreep',
				'foreignKey' => 'reponsesuppleant_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Remplacanteffectifmembreep' => array(
				'className' => 'Membreep',
				'foreignKey' => 'presencesuppleant_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		public $validate = array(
			'reponsesuppleant_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'reponse', true, array( 'remplacepar' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'presencesuppleant_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'presence', true, array( 'remplacepar' ) ),
					'message' => 'Champ obligatoire',
				)
			)
		);

		/**
		 * Fonction qui retourne vrai si dans les données envoyées au moins 2 membres sont
		 * remplacés par la même personne. Retourne faux dans le cas contraire.
		 */
		public function checkDoublon( $datas ) {
			$doublon = false;
			$liste = array();
			foreach( $datas as $data ) {
				if ( isset( $data['suppleant_id'] ) && !empty( $data['suppleant_id'] ) ) {
					if ( in_array( $data['suppleant_id'], $liste ) ) {
						$doublon = true;
					}
					else {
						$liste[] = $data['suppleant_id'];
					}
				}
			}
			return $doublon;
		}

		/**
		 * Retourne un array contenant les ids des membres d'une commission
		 * n'ayant pas décliné. Lorsqu'un membre est remplacé par un autre, c'est l'id du
		 * remplaçant qui est retourné.
		 *
		 * @param integer $commissionep_id
		 * @return array
		 */
		public function idsMembresPrevus( $commissionep_id ) {
			$membreseps = $this->Commissionep->Ep->EpMembreep->find(
				'all',
				array(
					'fields' => array_merge(
						$this->Commissionep->Ep->EpMembreep->fields(),
						$this->Commissionep->CommissionepMembreep->fields()
					),
					'conditions' => array(
						'Commissionep.id' => $commissionep_id,
						'OR' => array(
							'CommissionepMembreep.membreep_id IS NULL',
							'EpMembreep.membreep_id = CommissionepMembreep.membreep_id'
						)
					),
					'joins' => array(
						$this->Commissionep->Ep->EpMembreep->join( 'Ep' ),
						$this->Commissionep->Ep->join( 'Commissionep' ),
						$this->Commissionep->join( 'CommissionepMembreep', array( 'type' => 'LEFT OUTER' ) ),
					),
					'recursive' => -1
				)
			);

			$membresEpsIds = array();
			foreach( $membreseps as $membreep ) {
				if( $membreep['CommissionepMembreep']['reponse'] != 'decline' ) {
					if( $membreep['CommissionepMembreep']['reponse'] == 'remplacepar' ) {
						$membreep_id = $membreep['CommissionepMembreep']['reponsesuppleant_id'];
					}
					else {
						$membreep_id = $membreep['EpMembreep']['membreep_id'];
					}

					if( !empty( $membreep_id ) ) {
						$membresEpsIds[] = $membreep_id;
					}
				}
			}

			return $membresEpsIds;
		}
	}
?>