<?php
	/**
	 * Code source de la classe Decisioncontratcomplexeep93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractDecisionep', 'Model/Abstractclass' );

	/**
	 * La classe Decisioncontratcomplexeep93 ...
	 *
	 * @package app.Model
	 */
	class Decisioncontratcomplexeep93 extends AbstractDecisionep
	{
		public $name = 'Decisioncontratcomplexeep93';

		public $recursive = -1;

		public $actsAs = array(
			'Enumerable' => array(
				'fields' => array(
					'etape',
					'decision',
					'decisionpcg'
				)
			),
			'Autovalidate2',
			'ValidateTranslate',
			'Formattable',
		);

		/**
		*
		*/

		public $belongsTo = array(
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
		* Les règles de validation qui seront utilisées lors de la validation
		* en EP des décisions de la thématique
		*/

		public $validateFinalisation = array(
			'decision' => array(
				array(
					'rule' => array( 'notEmpty' )
				)
			),
			'datevalidation_ci' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'decision', true, array( 'valide' ) ),
					'message' => 'Champ obligatoire',
				),
				'notEmpty' => array(
					'rule' => 'date',
					'message' => 'Veuillez entrer une date valide',
					'allowEmpty'    => true
				)
			),
		);
	}
?>
