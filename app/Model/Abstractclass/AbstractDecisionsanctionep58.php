<?php
	/**
	 * Code source de la classe AbstractDecisionsanctionep58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Abstractclass
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractDecisionep', 'Model/Abstractclass' );

	/**
	 * La classe AbstractDecisionsanctionep58 est une classe abstraite pour des
	 * décisions d'EP du CG 58: Decisionsanctionep58 et Decisionsanctionrendezvousep58.
	 *
	 * @package app.Model.Abstractclass
	 */
	abstract class AbstractDecisionsanctionep58 extends AbstractDecisionep
	{

		public $recursive = -1;

		public $actsAs = array(
			'Autovalidate2',
			'ValidateTranslate',
			'Enumerable' => array(
				'fields' => array(
					'etape',
					'decision',
					'decision2',
					'arretsanction'
				)
			),
			'Formattable',
		);

		/**
		 * Champs virtuels impressionfin1 et impressionfin2 permettant de savoir
		 * si un courrier de "fin de sanction 1" ou "fin de sanction 2" peut être
		 * imprimé.
		 *
		 * @var array
		 */
		public $virtualFields = array(
			'impressionfin1' => array(
				'type'      => 'boolean',
				'postgres'  => '( "%s"."decision" = \'sanction\' ) AND ( "%s"."arretsanction" IN ( \'finsanction1\', \'annulation1\' ) )'
			),
			'impressionfin2' => array(
				'type'      => 'boolean',
				'postgres'  => '( "%s"."decision2" = \'sanction\' ) AND ( "%s"."arretsanction" IN ( \'finsanction1\', \'annulation1\', \'finsanction2\', \'annulation2\' ) )'
			),
		);

		public $belongsTo = array(
			'Autrelistesanctionep58' => array(
				'className' => 'Listesanctionep58',
				'foreignKey' => 'autrelistesanctionep58_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Listesanctionep58' => array(
				'className' => 'Listesanctionep58',
				'foreignKey' => 'listesanctionep58_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Passagecommissionep' => array(
				'className' => 'Passagecommissionep',
				'foreignKey' => 'passagecommissionep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		/**
		 * Règles de validation "gestion des sanctions".
		 * Le message d'erreur est fourni à la volée.
		 *
		 * @var array
		 */
		public $validateGestionSanctions = array(
			'arretsanction' => array(
				'validateArretsanction58' => array(
					'rule' => array( 'validateArretsanction58' ),
				)
			)
		);

		/**
		 * Les règles de validation qui seront utilisées lors de la validation
		 * en EP des décisions de la thématique
		 *
		 * @var array
		 */
		public $validateFinalisation = array(
			'decision' => array(
				array(
					'rule' => array( 'notEmpty' )
				)
			),
			'listesanctionep58_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'decision', true, array( 'sanction' ) ),
					'message' => 'Champ obligatoire',
				),
			),
			'autrelistesanctionep58_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'decision', true, array( 'sanction2' ) ),
					'message' => 'Champ obligatoire',
				),
			),
		);

		/**
		 * Retourne les modèles contenus pour l'historique des passages en EP.
		 *
		 * @return array
		 */
		public function containDecision() {
			return array(
				'Listesanctionep58',
				'Autrelistesanctionep58'
			);
		}

		/**
		 * Validation pour la gestion des sanctions.
		 *
		 * Si la décision n'est pas une sanction, on ne peut pas prendre de décision
		 * de sanction pour celle-ci, que ce soit au niveau 1 ou au niveau 2.
		 *
		 * @param array $check
		 * @return boolean|string
		 */
		public function validateArretsanction58( array $check ) {
			if( empty( $check ) ) {
				return true;
			}

			$return = true;

			foreach( $check as $field => $value ) {
				if( isset( $this->data[$this->alias][$this->primaryKey] ) && !empty( $value ) ) {
					$record = $this->find(
						'first',
						array(
							'fields' => array(
								"{$this->alias}.decision",
								"{$this->alias}.decision2",
							),
							'conditions' => array(
								"{$this->alias}.{$this->primaryKey}" => $this->data[$this->alias][$this->primaryKey]
							),
							'contain' => false
						)
					);

					if( !empty( $record ) ) {
						if( $record[$this->alias]['decision2'] != 'sanction' && in_array( $value, array( 'finsanction2', 'annulation2' ) ) ) {
							$return = 'Il n\'existe pas de sanction 2, impossible de prendre une décision de sanction 2';
						}

						if( $record[$this->alias]['decision'] != 'sanction' && in_array( $value, array( 'finsanction1', 'annulation1' ) ) ) {
							$return = 'Il n\'existe pas de sanction 1, impossible de prendre une décision de sanction 1';
						}
					}
				}
			}

			return $return;
		}

		/**
		 *
		 * @param type $data
		 * @param type $dataPath
		 * @return array
		 */
		public function suivisanctions58( $data, $dataPath = 'Decision.0' ) {
			$return = array(
				'decision1' => array(
					'decision' => null,
					'sanction' => null,
					'duree' => null,
					'dd' => null,
					'df' => null,
					'etat' => null,
				),
				'decision2' => array(
					'decision' => null,
					'sanction' => null,
					'duree' => null,
					'dd' => null,
					'df' => null,
					'etat' => null,
				),
			);

			if( !is_null( $dataPath ) ) {
				$dataDecision = Set::classicExtract( $data, $dataPath );
			}
			else {
				$dataDecision = $data;
			}

			if( $dataDecision[$this->alias]['decision'] == 'sanction' ) {
				$return['decision1']['decision'] = 'Sanction 1';
				$return['decision1']['sanction'] = $dataDecision['Listesanctionep58']['sanction'];
				$return['decision1']['duree'] = $dataDecision['Listesanctionep58']['duree'];
				$return['decision1']['dd'] = date( 'Y-m-d', strtotime( $data['Commissionep']['dateseance'] ) );

				if( $dataDecision[$this->alias]['arretsanction'] == 'finsanction1' ) {
					$return['decision1']['etat'] = 'Fin de sanction';
					$return['decision1']['df'] = $dataDecision[$this->alias]['datearretsanction'];
				}
				else if( $dataDecision[$this->alias]['arretsanction'] == 'annulation1' ) {
					$return['decision1']['etat'] = 'Annulé';
					$return['decision1']['df'] = $dataDecision[$this->alias]['datearretsanction'];
				}
				else {
					$return['decision1']['etat'] = 'En cours';
				}
			}

			if( $dataDecision[$this->alias]['decision2'] == 'sanction' ) {
				$return['decision2']['decision'] = 'Sanction 2';
				$return['decision2']['sanction'] = $dataDecision['Autrelistesanctionep58']['sanction'];
				$return['decision2']['duree'] = $dataDecision['Autrelistesanctionep58']['duree'];
				$return['decision2']['dd'] = date( 'Y-m-d', strtotime( "+{$return['decision1']['duree']} month", strtotime( $return['decision1']['dd'] ) ) );

				if( $dataDecision[$this->alias]['arretsanction'] == 'finsanction2' ) {
					$return['decision2']['etat'] = 'Fin de sanction';
					$return['decision2']['df'] = $dataDecision[$this->alias]['datearretsanction'];
				}
				else if( $dataDecision[$this->alias]['arretsanction'] == 'annulation2' ) {
					$return['decision2']['etat'] = 'Annulé';
					$return['decision2']['df'] = $dataDecision[$this->alias]['datearretsanction'];
				}
				else if( in_array( $return['decision1']['etat'], array( 'Fin de sanction', 'Annulé' ) ) ) {
					$return['decision2']['etat'] = 'Annulé';
				}
				else {
					$return['decision2']['etat'] = 'En cours';
				}
			}

			$return = array(
				array( $this->alias => $return['decision1'] ),
				array( $this->alias => $return['decision2'] ),
			);

			for( $i = 0 ; $i <= 1 ; $i++ ) {
				if( empty( $return[$i][$this->alias]['decision'] ) ) {
					unset( $return[$i] );
				}
			}

			return $return;
		}

		/**
		 * Retourne la liste des clés de configuration pour lesquelles il faut
		 * vérifier la syntaxe de l'intervalle PostgreSQL.
		 *
		 * @return array
		 */
		public function checkPostgresqlIntervals() {
			$keys = array(
				'Decisionsanctionep58.datePrevisionnelleRadiation'
			);

			return $this->_checkPostgresqlIntervals( $keys );
		}
	}
?>