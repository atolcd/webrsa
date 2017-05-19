<?php
	/**
	 * Code source de la classe Textmailcui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Textmailcui66 ...
	 *
	 * @package app.Model
	 */
	class Textmailcui66 extends AppModel
	{
		public $name = 'Textmailcui66';

		public $actsAs = array(
			'Pgsqlcake.PgsqlAutovalidate',
			'Formattable'
		);

		public $validate = array(
			'name' => array(
				array(
					'rule' => 'isUnique',
					'message' => 'Valeur déjà utilisée'
				)
			)
		);

        /**
         * Associations "Has Many".
         * @var array
         */
        public $hasMany = array(
//            'Cui' => array(
//				'className' => 'Cui',
//				'foreignKey' => 'textmailcui66_id',
//				'dependent' => false,
//				'conditions' => '',
//				'fields' => '',
//				'order' => '',
//				'limit' => '',
//				'offset' => '',
//				'exclusive' => '',
//				'finderQuery' => '',
//				'counterQuery' => ''
//			),
//            'Decisioncui66' => array(
//				'className' => 'Decisioncui66',
//				'foreignKey' => 'textmailcui66_id',
//				'dependent' => false,
//				'conditions' => '',
//				'fields' => '',
//				'order' => '',
//				'limit' => '',
//				'offset' => '',
//				'exclusive' => '',
//				'finderQuery' => '',
//				'counterQuery' => ''
//			)
        );

	}
?>