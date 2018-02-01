<?php
	/**
	 * Code source de la classe Bilanparcours66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Bilan de parcours pour le conseil général du département 66.
	 *
	 * @package app.Model
	 */
	class Bilanparcours66 extends AppModel
	{
		public $name = 'Bilanparcours66';

		public $actsAs = array(
			'Allocatairelie',
			'Fichiermodulelie',
			'Gedooo.Gedooo',
			'ModelesodtConditionnables' => array(
				66 => array(
					'Bilanparcours/bilanparcours.odt',
					'Bilanparcours/courrierinformationavantep.odt',
				)
			),
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'proposition' => array(
				'alphaNumeric' => array(
					'rule' => array( 'alphaNumeric' ),
					'message' => 'La proposition du référent est obligatoire',
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create'
				)
			),
			'datebilan' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire',
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create'
				),
				'datePassee' => array(
					'rule' => array( 'datePassee' ),
					'message' => 'Merci de choisir une date antérieure à la date du jour'
				)
			),
			'bilanparcoursinsertion' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'motifep', true, array( '0' ) ),
					'message' => 'Veuillez saisir une information',
				)
			),
			'motifep' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'bilanparcoursinsertion', true, array( '0' ) ),
					'message' => 'Veuillez saisir une information',
				)
			),
			'sansep_typeorientprincipale_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'proposition', true, array( 'traitement' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'avecep_typeorientprincipale_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'proposition', true, array( 'parcours', 'parcourspe' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'nvtypeorient_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'proposition', true, array( 'traitement', 'parcours', 'parcourspe' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'nvstructurereferente_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'proposition', true, array( 'traitement', 'parcours', 'parcourspe' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'choixparcours' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'proposition', true, array( 'parcours', 'parcourspe' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'examenaudition' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'proposition', true, array( 'audition' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'examenauditionpe' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'proposition', true, array( 'auditionpe' ) ),
					'message' => 'Champ obligatoire',
				)
			)
		);

		public $belongsTo = array(
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'contratinsertion_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Nvcontratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'nvcontratinsertion_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Cui' => array(
				'className' => 'Cui',
				'foreignKey' => 'cui_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Orientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'orientstruct_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
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
			),
			'Serviceinstructeur' => array(
				'className' => 'Serviceinstructeur',
				'foreignKey' => 'serviceinstructeur_id',
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
			'NvStructurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'nvstructurereferente_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typeorientprincipale' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'typeorientprincipale_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'NvTypeorient' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'nvtypeorient_id',
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
			)
		);

		public $hasOne = array(
			'Saisinebilanparcoursep66' => array(
				'className' => 'Saisinebilanparcoursep66',
				'foreignKey' => 'bilanparcours66_id',
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
			'Defautinsertionep66' => array(
				'className' => 'Defautinsertionep66',
				'foreignKey' => 'bilanparcours66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Bilanparcours66\'',
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
			'Dossierpcg66' => array(
				'className' => 'Dossierpcg66',
				'foreignKey' => 'bilanparcours66_id',
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
			'Manifestationbilanparcours66' => array(
				'className' => 'Manifestationbilanparcours66',
				'foreignKey' => 'bilanparcours66_id',
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

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'WebrsaBilanparcours66'
		);

		/**
		 *
		 * @param array $options
		 * @return mixed
		 */
		public function beforeSave( $options = array() ) {
			$return = parent::beforeSave( $options );

			if ( isset( $this->data['Pe']['Bilanparcours66']['id'] ) ) {
				$id = $this->data['Pe']['Bilanparcours66']['id'];
				unset( $this->data['Pe']['Bilanparcours66']['id'] );
			}
			if ( isset( $this->data['Pe']['Bilanparcours66'] ) ) {
				$datape = $this->data['Pe'];
				unset($this->data['Pe']);
				$this->data = Set::merge( $this->data, $datape );

				if ( isset( $id ) ) {
					$this->data['Bilanparcours66']['id'] = $id;
				}
			}


			$this->data[$this->alias]['positionbilan'] = $this->WebrsaBilanparcours66->calculPositionBilan( $this->data );

			return $return;
		}

		/**
		 * Called after each successful save operation.
		 *
		 * @param boolean $created True if this save created a new record
		 * @return void
		 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#aftersave
		 */
		public function afterSave( $created, $options = array() ) {
			parent::afterSave( $created, $options );
			$this->WebrsaBilanparcours66->updatePositionsById($this->id);
		}
	}
?>