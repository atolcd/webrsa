<?php
	/**
	 * Code source de la classe Decisionreorientationep93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractDecisionep', 'Model/Abstractclass' );

	/**
	 * La classe Decisionreorientationep93 ...
	 *
	 * @package app.Model
	 */
	class Decisionreorientationep93 extends AbstractDecisionep
	{
		public $name = 'Decisionreorientationep93';

		public $recursive = -1;

		public $actsAs = array(
			'Autovalidate2',
			'Dependencies',
			'Enumerable' => array(
				'fields' => array(
					'etape',
					'decision',
					'decisionpcg'
				)
			),
			'Formattable' => array(
				'suffix' => array(
					'structurereferente_id'
				)
			),
			'ValidateTranslate',
		);

		public $belongsTo = array(
			'Typeorient' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'typeorient_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
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

		// TODO: lorsqu'on pourra reporter les dossiers,
		// il faudra soit faire soit un report, soit les validations ci-dessous
		// FIXME: dans ce cas, il faudra permettre au champ decision de prendre la valeur NULL

		/**
		* Les règles de validation qui seront utilisées lors de la validation
		* en EP des décisions de la thématique
		*/

		public $validateFinalisation = array(
			'decision' => array(
				array(
					'rule' => array( 'notEmpty' )
				)
			),
			'typeorient_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'decision', true, array( 'accepte' ) ),
					'message' => 'Champ obligatoire',
				),
			),
			'structurereferente_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'decision', true, array( 'accepte' ) ),
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
			),
		);

		/**
		* Modèles contenus pour l'historique des passages en EP
		*/

		public function containDecision() {
			return array(
				'Typeorient',
				'Structurereferente'
			);
		}
	}
?>