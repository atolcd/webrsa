<?php	
	/**
	 * Code source de la classe Budgetapre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Budgetapre ...
	 *
	 * @package app.Model
	 */
	class Budgetapre extends AppModel
	{
		public $name = 'Budgetapre';

		public $displayField = 'exercicebudgetai';

		public $validate = array(
			'exercicebudgetai' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
				array(
					'rule' => 'numeric',
					'message' => 'Veuillez entrer une valeur numérique.',
					'allowEmpty' => true
				),
				array(
					'rule' => 'isUnique',
					'message' => 'Valeur déjà utilisée'
				),
			),
			'montantattretat' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
				array(
					'rule' => array( 'inclusiveRange', 0, 99999999 ),
					'message' => 'Veuillez saisir un montant compris entre 0 et 99 999 999 € maximum.'
				),
				array(
					'rule' => 'numeric',
					'message' => 'Veuillez entrer une valeur numérique.',
					'allowEmpty' => true
				),
			),
			'ddexecutionbudge' => array(
				array(
					'rule' => 'date',
					'message' => 'Veuillez entrer une date correcte',
					'allowEmpty' => true
				),
			),
			'dfexecutionbudge' => array(
				array(
					'rule' => 'date',
					'message' => 'Veuillez entrer une date correcte',
					'allowEmpty' => true
				),
			),
			// FIXME: faire les autres
		);

		public $hasMany = array(
			'Etatliquidatif' => array(
				'className' => 'Etatliquidatif',
				'foreignKey' => 'budgetapre_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);
	}
?>