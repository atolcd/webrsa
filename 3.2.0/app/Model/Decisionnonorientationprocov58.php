<?php
	/**
	 * Code source de la classe Decisionnonorientationprocov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Decisionnonorientationprocov58 ...
	 *
	 * @package app.Model
	 */
	class Decisionnonorientationprocov58 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Decisionnonorientationprocov58';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Dependencies',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2RulesComparison',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Passagecov58' => array(
				'className' => 'Passagecov58',
				'foreignKey' => 'passagecov58_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Referent' => array(
				'className' => 'Referent',
				'foreignKey' => 'referent_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Typeorient' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'typeorient_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			)
		);

		/**
		 * Les règles de validation qui seront utilisées lors de la validation
		 * en COV des décisions de la thématique
		 */
		public $validateFinalisation = array(
			'decisioncov' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				),
				'checkDecisionMaintienOuReorientation' => array(
					'rule' => array( 'checkDecisionMaintienOuReorientation' ),
					'message' => 'Décision et type d\'orientation non cohérents'
				)
			),
			'typeorient_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'decisioncov', true, array( 'maintienref', 'reorientation' ) ),
					'message' => 'Champ obligatoire',
				),
			),
			'structurereferente_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'decisioncov', true, array( 'maintienref', 'reorientation' ) ),
					'message' => 'Champ obligatoire',
				),
				'dependentForeignKeys' => array(
					'rule' => array( 'dependentForeignKeys', 'Structurereferente', 'Typeorient' ),
					'message' => 'La structure référente ne correspond pas au type d\'orientation',
				),
			),
			'referent_id' => array(
				'dependentForeignKeys' => array(
					'rule' => array( 'dependentForeignKeys', 'Referent', 'Structurereferente' ),
					'message' => 'Le référent n\'appartient pas à la structure référente',
				),
			)
		);

		/**
		 * Modèles contenus pour l'historique des passages en COV.
		 *
		 * @return array
		 */
		public function containDecision() {
			return array(
				'Typeorient',
				'Structurereferente',
				'Referent'
			);
		}

		/**
		 * Vérifie que lorsque la décision est un maintien, alors le type
		 * d'orientation ne sera pas de l'emploi et lorsque la décision est une
		 * réorientation, alors le type d'orientation sera de l'emploi.
		 *
		 * @param array $checks
		 * @return boolean
		 */
		public function checkDecisionMaintienOuReorientation( $checks ) {
			if( !is_array( $checks ) ) {
				return false;
			}

			$typeorientEmploiIds = (array)Configure::read( 'Typeorient.emploi_id' );
			$success = true;
			$typeorient_id = Hash::get( $this->data, "{$this->alias}.typeorient_id" );

			if( !empty( $checks ) ) {
				foreach( $checks as $value ) {
					if( !empty( $value ) && !empty( $typeorient_id ) ) {
						if( $value === 'maintienref' ) {
							$success = !in_array( $typeorient_id, $typeorientEmploiIds ) && $success;
						}
						else if( $value === 'reorientation' ) {
							$success = in_array( $typeorient_id, $typeorientEmploiIds ) && $success;
						}
					}
				}
			}

			return $success;
		}
	}
?>