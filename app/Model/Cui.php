<?php
	/**
	 * Fichier source de la classe Cui.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Cui est la classe contenant le CERFA CUI.
	 *
	 * @package app.Model
	 */
	class Cui extends AppModel
	{
		/**
		 * Alias de la table et du model
		 * @var string
		 */
		public $name = 'Cui';

		/**
		 * Possède des clefs étrangères vers d'autres models
		 * @var array
		 */
        public $belongsTo = array(
			'Partenairecui' => array(
				'className' => 'Partenairecui',
				'foreignKey' => 'partenairecui_id',
			),
			'Partenaire' => array(
				'className' => 'Partenaire',
				'foreignKey' => 'partenaire_id',
			),
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
			),
			'Personnecui' => array(
				'className' => 'Personnecui',
				'foreignKey' => 'personnecui_id',
			),
			'Entreeromev3' => array(
				'className' => 'Entreeromev3',
				'foreignKey' => 'entreeromev3_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id'
			)
        );

		/**
		 * Ces models possèdent une clef étrangère vers ce model
		 * @var array
		 */
		public $hasOne = array(
			'Cui66' => array(
				'className' => 'Cui66',
				'foreignKey' => 'cui_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'dependent' => true
			),
		);

		/**
		 * Ces models possèdent une clef étrangère vers ce model
		 * @var array
		 */
		public $hasMany = array(
			'Emailcui' => array(
				'className' => 'Emailcui',
				'foreignKey' => 'cui_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'dependent' => true
			),
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Cui\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);

		/**
		 * Champs suplémentaire virtuel (n'existe pas en base)
		 * @var array
		 */
		public $virtualFields = array(
			'dureecontrat' => array(
				'type'      => 'string',
				'postgres'  => '(( "%s"."findecontrat" - "%s"."dateembauche") / 30)'
			),
		);

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Allocatairelie',
			'Gedooo.Gedooo',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate',
		);

		/**
		 * Valeur des checkbox du champ beneficiairede
		 * @var array
		 */
		public $beneficiairede = array(
			'ASS',
			'AAH',
			'ATA',
			'RSA'
		);

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('WebrsaCui');
	}
?>