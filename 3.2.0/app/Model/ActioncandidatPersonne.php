<?php
	/**
	 * Code source de la classe ActioncandidatPersonne.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe ActioncandidatPersonne ...
	 *
	 * @package app.Controller
	 */
	class ActioncandidatPersonne extends AppModel
	{
		public $name = 'ActioncandidatPersonne';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Actioncandidat' => array(
				'className' => 'Actioncandidat',
				'foreignKey' => 'actioncandidat_id',
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
			'Motifsortie' => array(
				'className' => 'Motifsortie',
				'foreignKey' => 'motifsortie_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Progfichecandidature66' => array(
				'className' => 'Progfichecandidature66',
				'foreignKey' => 'progfichecandidature66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $actsAs = array (
			'Allocatairelie',
			'Gedooo.Gedooo',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);


		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'ActioncandidatPersonne\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
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
			'nivetu'  => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
            'bilanvenu' => array(
                // INFO: il s'agit d'un champ "virtuel" dans les cohortes de fiches de candidature
                'notEmptyIf' => array(
                    'rule' => array( 'notEmptyIf', 'atraiter', true, array( '1' ) ),
                    'message' => 'Champ obligatoire',
                )
            ),
			'bilanretenu'  => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'bilanvenu', true, array( 'VEN' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'horairerdvpartenaire' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'rendezvouspartenaire', true, array( '1' ) ),
					'message' => 'Champ obligatoire'
				)
			),
			'ddaction' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'motifdemande' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'sortiele' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'issortie', true, array( 1 ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'motifsortie_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'issortie', true, array( 1 ) ),
					'message' => 'Champ obligatoire',
				)
			)
		);

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('WebrsaActioncandidatPersonne');

		/**
		 * __construct($id = false, $table = null, $ds = null)
		 *
		 * Rend la saisie d'un Nom du programme obligatoire si Actions Région : formation est selectionné
		 *
		 * @param array $id
		 * @param unknown_type $table
		 * @param unknown_type $ds
		 */
		public function __construct($id = false, $table = null, $ds = null){
            parent::__construct($id, $table, $ds);

            // Rend la saisie d'un Nom du programme obligatoire si Actions Région : formation est selectionné
			$this->validate['progfichecandidature66_id'] = array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'actioncandidat_id', true, (array)Configure::read('ActioncandidatPersonne.Actioncandidat.typeregionId') ),
					'message' => 'Champ obligatoire'
				)
			);
        }

		/**
		*   BeforeSave
		*/

		public function beforeSave( $options = array() ) {
			$return = parent::beforeSave( $options );
			//  Calcul de la position de la fiche de calcul
			$this->data = $this->WebrsaActioncandidatPersonne->bilanAccueil( $this->data );

			return $return;
		}
	}
?>