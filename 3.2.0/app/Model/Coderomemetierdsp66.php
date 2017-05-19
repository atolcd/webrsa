<?php
	/**
	 * Code source de la classe Coderomemetierdsp66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Coderomemetierdsp66 ...
	 *
	 * @package app.Model
	 */
	class Coderomemetierdsp66 extends AppModel
	{
		public $name = 'Coderomemetierdsp66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'intitule';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $hasMany = array(
			'Correspondanceromev2v3' => array(
				'className' => 'Correspondanceromev2v3',
				'foreignKey' => 'coderomemetierdsp66_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
			'Libactdomi66MetierDsp' => array(
				'className' => 'Dsp',
				'foreignKey' => 'libactdomi66_metier_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Libactdomi66MetierDspRev' => array(
				'className' => 'DspRev',
				'foreignKey' => 'libactdomi66_metier_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Libderact66MetierDsp' => array(
				'className' => 'Dsp',
				'foreignKey' => 'libderact66_metier_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Libderact66MetierDspRev' => array(
				'className' => 'DspRev',
				'foreignKey' => 'libderact66_metier_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Libemploirech66MetierDsp' => array(
				'className' => 'Dsp',
				'foreignKey' => 'libemploirech66_metier_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Libemploirech66MetierDspRev' => array(
				'className' => 'DspRev',
				'foreignKey' => 'libemploirech66_metier_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
            'Personnepcg66' => array(
				'className' => 'Personnepcg66',
				'foreignKey' => 'categoriedetail',
				'dependent' => false,
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

		public $belongsTo = array(
			'Coderomesecteurdsp66' => array(
				'className' => 'Coderomesecteurdsp66',
				'foreignKey' => 'coderomesecteurdsp66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $virtualFields = array(
			'intitule' => array(
				'type'      => 'string',
				'postgres'  => '( "%s"."code" || \'. \' || "%s"."name" )'
			),
		);
	}
?>