<?php	
	/**
	 * Code source de la classe CommissionepMembreep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe CommissionepMembreep ...
	 *
	 * @package app.Model
	 */
	class CommissionepMembreep extends AppModel
	{
		public $name = 'CommissionepMembreep';

		public $actsAs = array(
			'Autovalidate2',
			'ValidateTranslate',
			'Enumerable' => array(
				'fields' => array(
					'reponse',
					'presence',
					'suppleant' => array( 'domain' => 'default', 'type' => 'booleannumber' )
				)
			),
			'Formattable' => array(
				'suffix' => array( 'reponsesuppleant_id', 'presencesuppleant_id' )
			)
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

		/**
		 * Supprime ou ajoute des entrées dans la table commissionseps_membreseps lorsque la
		 * commission n'est pas encore validée et qu'il se pourrait que des membres aient été rajoutés ou
		 * supprimés de l'EP.
		 *
		 * @param integer $ep_id L'id de l'EP pour laquelle des membres ont été ajoutés ou supprimés
		 * @param array $membreseps_ids Les ids des membres actuels de l'EP
		 * @return boolean
		 */
		/*public function updateCommissionsNonValidees( $ep_id, $membreseps_ids ) {
			$success = true;

			// Commissions non validées pour EP
			$commissionsIdsNonValidees = $this->Commissionep->find(
				'list',
				array(
					'fields' => array(
						'Commissionep.id',
						'Commissionep.id'
					),
					'conditions' => array(
						'Commissionep.ep_id' => $ep_id,
						'Commissionep.etatcommissionep' => array( 'cree', 'associe' )
					),
					'contain' => false
				)
			);

			if( !empty( $commissionsIdsNonValidees ) ) {
				$commissionsIdsNonValidees = array_keys( $commissionsIdsNonValidees );

				foreach( $commissionsIdsNonValidees as $commissionep_id ) {
					$commissionseps_membreseps = $this->find(
						'list',
						array(
							'fields' => array(
								'CommissionepMembreep.membreep_id',
								'CommissionepMembreep.membreep_id'
							),
							'conditions' => array(
								'CommissionepMembreep.commissionep_id' => $commissionep_id
							),
							'contain' => false
						)
					);

					if( !empty( $commissionseps_membreseps ) ) {
						$commissionseps_membreseps = array_keys( $commissionseps_membreseps );

						// Suppression des entrées de membres qui ne sont plus associés
						$success = $this->deleteAll(
							array(
								'CommissionepMembreep.commissionep_id' => $commissionep_id,
								'CommissionepMembreep.membreep_id NOT' => $membreseps_ids
							)
						) && $success;

						// Ajout d'entrées pour les membres ajoutés
						$membreseps_ajoutes = array_diff( $membreseps_ids, $commissionseps_membreseps );
						if( !empty( $membreseps_ajoutes ) ) {
							$nouveaux_commissionseps_membreseps = array();
							foreach( $membreseps_ajoutes as $membreep_id ) {
								$nouveaux_commissionseps_membreseps[] = array(
									'CommissionepMembreep' => array(
										'commissionep_id' => $commissionep_id,
										'membreep_id' => $membreep_id
									)
								);
							}
							$success = $this->saveAll( $nouveaux_commissionseps_membreseps, array( 'atomic' => false ) ) && $success;
						}
					}
				}
			}

			return $success;
		}*/
	}
?>