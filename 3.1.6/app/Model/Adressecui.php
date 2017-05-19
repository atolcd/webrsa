<?php
	/**
	 * Fichier source de la classe Adressecui.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Adressecui est la classe contenant les information de contact du CUI.
	 *
	 * @package app.Model
	 */
	class Adressecui extends AppModel
	{
		public $name = 'Adressecui';
		
		public $recursive = -1;
		
		public $hasOne = array(
			'Partenairecui' => array(
				'className' => 'Partenairecui',
				'foreignKey' => 'adressecui_id',
				'dependent' => true,
			),
		);
		
		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Formattable',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Formattable' => array(
				'phone' => array( 'numtel', 'numfax', 'numtel2', 'numfax2' )
			),
		);
		
		
		/**
		 * Règles de validation.
		 *
		 * @var array
		 */
		public $validate = array(
			'numtel' => array(
				'phoneFr' => array(
					'rule' => array( 'phoneFr' ),
					'allowEmpty' => true,
				),
			),
			'numfax' => array(
				'phoneFr' => array(
					'rule' => array( 'phoneFr' ),
					'allowEmpty' => true,
				)
			),
			'numtel2' => array(
				'phoneFr' => array(
					'rule' => array( 'phoneFr' ),
					'allowEmpty' => true,
				)
			),
			'numfax2' => array(
				'phoneFr' => array(
					'rule' => array( 'phoneFr' ),
					'allowEmpty' => true,
				)
			),
			'email' => array(
				'email' => array(
					'rule' => array( 'email' ),
					'allowEmpty' => true,
				)
			),
			'email2' => array(
				'email' => array(
					'rule' => array( 'email' ),
					'allowEmpty' => true,
				)
			),
		);
	}
?>