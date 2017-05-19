<?php
	/**
	 * Code source de la classe Bilanparcours66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Bilan de parcours pour le conseil général du département 66.
	 *
	 * @package app.Model
	 */
	class Bilanparcours66 extends AppModel
	{
		public $name = 'Bilanparcours66';

		public $recursive = -1;

		public $actsAs = array(
			'Allocatairelie',
			'Formattable' => array(
				'suffix' => array(
					'structurereferente_id',
					'referent_id',
					'nvstructurereferente_id'
				)
			),
			'Enumerable' => array(
				'fields' => array(
					'presenceallocataire',
					'saisineepparcours',
					'maintienorientation',
					'changereferent',
					'accordprojet',
					'maintienorientsansep',
					'choixparcours',
					'changementrefsansep',
					'maintienorientparcours',
					'changementrefparcours',
					'reorientation',
					'examenaudition',
					'examenauditionpe',
					'maintienorientavisep',
					'changementrefeplocale',
					'reorientationeplocale',
					'typeeplocale',
					'accompagnement',
					'typeformulaire',
					'saisineepl',
					'sitfam',
					'proposition',
					'positionbilan',
					'haspiecejointe'
				)
			),
			'Gedooo.Gedooo',
			'ModelesodtConditionnables' => array(
				66 => array(
					'Bilanparcours/bilanparcours.odt',
					'Bilanparcours/courrierinformationavantep.odt',
				)
			),
            'Pgsqlcake.PgsqlAutovalidate',
		);

		public $validate = array(
			'proposition' => array(
				array(
					'rule' => 'alphanumeric',
					'message' => 'La proposition du référent est obligatoire',
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create'
				)
			),
			'datebilan' => array(
				'datePassee' => array(
					'rule' => 'datePassee',
					'message' => 'Merci de choisir une date antérieure à la date du jour'
				),
				'date' => array(
					'rule' => 'date',
					'message' => 'Merci de rentrer une date valide',
					'allowEmpty' => false,
					'required' => true,
					'on' => 'create'
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
				array(
					'rule' => array( 'notEmptyIf', 'proposition', true, array( 'traitement' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'avecep_typeorientprincipale_id' => array(
				array(
					'rule' => array( 'notEmptyIf', 'proposition', true, array( 'parcours', 'parcourspe' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'nvtypeorient_id' => array(
				array(
					'rule' => array( 'notEmptyIf', 'proposition', true, array( 'traitement', 'parcours', 'parcourspe' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'nvstructurereferente_id' => array(
				array(
					'rule' => array( 'notEmptyIf', 'proposition', true, array( 'traitement', 'parcours', 'parcourspe' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'choixparcours' => array(
				array(
					'rule' => array( 'notEmptyIf', 'proposition', true, array( 'parcours', 'parcourspe' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'examenaudition' => array(
				array(
					'rule' => array( 'notEmptyIf', 'proposition', true, array( 'audition' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'examenauditionpe' => array(
				array(
					'rule' => array( 'notEmptyIf', 'proposition', true, array( 'auditionpe' ) ),
					'message' => 'Champ obligatoire',
				)
			),
            'presenceallocataire' => array(
				array(
					'rule' => array( 'notEmpty' ),
					'message' => 'Champ obligatoire',
				)
			),
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
			if ( isset( $this->data['Pe']['Bilanparcours66'] )/* && !empty( $data['Pe']['Bilanparcours66']['datebilan'] )*/ ) {
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
		public function afterSave($created) {
			parent::afterSave($created);
			$this->WebrsaBilanparcours66->updatePositionsById($this->id);
		}
	}
?>