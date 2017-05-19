<?php
	/**
	 * Code source de la classe Departement.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Departement ...
	 *
	 * @package app.Model
	 */
	class Departement extends AppModel
	{

		public $name = 'Departement';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $hasMany = array(
			'Adresse' => array(
				'className' => 'Adresse',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'"Departement"."numdep" = SUBSTRING( "Adresse"."codepos" FROM 1 FOR 2 )'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		public $validate = array(
			'numdep' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				),
			),
			'name' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				),
			),
		);

	}
?>