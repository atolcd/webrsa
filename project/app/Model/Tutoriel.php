<?php
	/**
	 * Code source de la classe Tutoriel.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Tutoriel ...
	 *
	 * @package app.Model
	 */
	class Tutoriel extends AppModel
	{

		/**
		 * Nom par défaut du modèle.
		 *
		 * @var integer
		 */
		public $name = 'Tutoriel';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'tutoriels';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Associations "Has One".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => 'fichiermodule_id',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Parent' => array(
				'className' => 'Tutoriel',
				'foreignKey' => 'parentid',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			)
		);

		/**
		 * Règles de validation ne pouvant être déduites de la base de données,
		 * pour l'ensemble des départements (surcharge possible dans le constructeur).
		 *
		 * @var array
		 */
		public $validate = array(
			'titre' => array(
				'notBlank' => array(
					'rule' => 'notBlank',
					'allowEmpty' => false,
				),
			),
			'rg' => array(
				'notBlank' => array(
					'rule' => 'notBlank',
					'allowEmpty' => false,
				),
				'numeric' => array(
					'rule' => 'numeric',
				)
			)
		);

		/**
		 * Surcharge du constructeur pour ajouter les message de règles de validation
		 *
		 * @param mixed $id Set this ID for this model on startup, can also be an array of options, see above.
		 * @param string $table Name of database table to use.
		 * @param string $ds DataSource connection name.
		 */
		public function __construct( $id = false, $table = null, $ds = null ) {
			$this->validate['titre']['notBlank']['message'] = __d( 'default', 'Validate::notBlank');
			$this->validate['rg']['notBlank']['message'] = __d( 'default', 'Validate::notBlank');
			$this->validate['rg']['numeric']['message'] = __d( 'default', 'Validate::numeric');
			parent::__construct( $id, $table, $ds );
		}
	}
