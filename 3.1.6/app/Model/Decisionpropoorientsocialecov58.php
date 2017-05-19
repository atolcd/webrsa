<?php
	/**
	 * Fichier source de la classe Decisionpropoorientsocialecov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Decisionpropoorientsocialecov58 est la classe qui gère les décisions de la thématique de COV
	 * "Orientation sociale de fait" pour le CG 58.
	 *
	 * @package app.Model
	 */
	class Decisionpropoorientsocialecov58 extends AppModel
	{
		public $name = 'Decisionpropoorientsocialecov58';

		public $recursive = -1;

		public $actsAs = array(
			'Autovalidate2',
			'Dependencies',
			'Enumerable' => array(
				'fields' => array(
					'etapecov',
					'decisioncov'
				)
			),
			'Formattable' => array(
				'suffix' => array(
					'structurereferente_id',
					'referent_id'
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
			'Passagecov58' => array(
				'className' => 'Passagecov58',
				'foreignKey' => 'passagecov58_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Referent' => array(
				'className' => 'Referent',
				'foreignKey' => 'referent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		// TODO: lorsqu'on pourra reporter les dossiers,
		// il faudra soit faire soit un report, soit les validations ci-dessous
		// FIXME: dans ce cas, il faudra permettre au champ decision de prendre la valeur NULL

		/**
		 * Les règles de validation qui seront utilisées lors de la validation
		 * en COV des décisions de la thématique
		 */
		public $validateFinalisation = array(
			'decisioncov' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' )
				)
			),
			'typeorient_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'decisioncov', true, array( 'valide' ) ),
					'message' => 'Champ obligatoire',
				),
			),
			'structurereferente_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'decisioncov', true, array( 'valide' ) ),
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
			/*return array(
				'Typeorient',
				'Structurereferente'
			);*/
		}
	}
?>