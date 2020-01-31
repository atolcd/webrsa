<?php
	/**
	 * Code source de la classe Typerdv.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Typerdv ...
	 *
	 * @package app.Model
	 */
	class Typerdv extends AppModel
	{
		public $name = 'Typerdv';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'libelle';

		public $order = '%s.libelle ASC';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		public $hasMany = array(
			'Entretien' => array(
				'className' => 'Entretien',
				'foreignKey' => 'typerdv_id',
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
			'Rendezvous' => array(
				'className' => 'Rendezvous',
				'foreignKey' => 'typerdv_id',
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
			'Thematiquerdv' => array(
				'className' => 'Thematiquerdv',
				'foreignKey' => 'typerdv_id',
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
		);

		public $validate = array();

		public $hasAndBelongsToMany = array(
			'Statutrdv' => array(
				'className' => 'Statutrdv',
				'joinTable' => 'statutsrdvs_typesrdv',
				'foreignKey' => 'typerdv_id',
				'associationForeignKey' => 'statutrdv_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'StatutrdvTyperdv'
			)
		);

		public function __construct () {
			parent::__construct();
			$this->validate = array(
				'motifpassageep' => array(
					'notEmptyIf' => array(
						'rule' => array( 'notEmptyIf', 'nbabsencesavpassageep', false, array( 0 ) ),
						'message' => __d( 'typerdv', 'Validate::motifpassageep::ERR'),
					)
				),
				'code_type' => array(
					'checkUniqueCodeType' => array(
						'rule' => array( 'checkUnique', array( 'code_type' ) ),
						'message' => __d( 'typerdv', 'Validate::code_type::ERR')
					)
				)
			);
		}

		/**
		 * Retourne la liste des modèles odt paramétrés pour le impressions de
		 * cette classe.
		 *
		 * @return array
		 */
		public function modelesOdt() {
			$prefix = 'RDV'.DS;

			$typesrdv = $this->find(
				'all',
				array(
					'fields' => array(
						'( \''.$prefix.'\' || "'.$this->alias.'"."modelenotifrdv" || \'.odt\' ) AS "'.$this->alias.'__modele"',
					),
					'recursive' => -1
				)
			);
			return Set::extract( $typesrdv, '/'.$this->alias.'/modele' );
		}
	}
?>