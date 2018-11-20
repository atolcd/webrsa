<?php
	/**
	 * Code source de la classe Personne.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'FrValidation', 'Validation' );

	/**
	 * La classe Personne ...
	 *
	 * @package app.Model
	 */
	class Personne extends AppModel
	{
		public $name = 'Personne';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'nom_complet';

		public $actsAs = array(
			'Fichiermodulelie',
			'Validation2.Validation2Formattable' => array(
				'Validation2.Validation2DefaultFormatter' => array(
					'stripNotAlnum' => '/^(numfixe|numport)$/'
				)
			),
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'qual' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'nom' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'prenom' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'nir' => array(
				'between' => array(
					'rule' => array( 'between', 13, 15 ),
					'message' => 'Le NIR doit être compris entre 13 et 15 caractères',
					'allowEmpty' => true
				),
				'alphaNumeric' => array(
					'rule' => array( 'alphaNumeric' ),
					'message' => 'Veuillez entrer une valeur alpha-numérique.',
					'allowEmpty' => true
				)
			),
			'dtnai' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'rgnai' => array(
				'comparison' => array(
					'rule' => array( 'comparison', '>', 0 ),
					'message' => 'Veuillez entrer un nombre positif.',
					'allowEmpty' => true
				)
			),
			'numfixe' => array(
				'phone' => array(
					'rule' => array( 'phone', null, 'fr' ),
					'allowEmpty' => true
				)
			),
			'numport' => array(
				'phone' => array(
					'rule' => array( 'phone', null, 'fr' ),
					'allowEmpty' => true
				)
			),
			'email' => array(
				'email' => array(
					'rule' => array( 'email' ),
					'allowEmpty' => true
				)
			),
			'nati' => array(
				'inList' => array(
					'rule' => array( 'inList', array( 'A', 'C', 'F' ) ),
					'allowEmpty' => true
				)
			)
		);

		/**
		 * Liste de champs et de valeurs possibles qui ne peuvent pas être mis en
		 * règle de validation inList ou en contrainte dans la base de données en
		 * raison des valeurs actuellement en base, mais pour lequels un ensemble
		 * fini de valeurs existe.
		 *
		 * @see AppModel::enums
		 *
		 * @var array
		 */
		public $fakeInLists = array(
			'qual' => array('MME', 'MR'),
			'pieecpres' => array('E', 'P'),
            'sexe' => array('1', '2'),
            'typedtnai' => array('J', 'N', 'O'),
			'nati' => array( 'A', 'C', 'F' )
		);

		public $belongsTo = array(
			'Foyer' => array(
				'className' => 'Foyer',
				'foreignKey' => 'foyer_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
		public $hasOne = array(
			'Calculdroitrsa' => array(
				'className' => 'Calculdroitrsa',
				'foreignKey' => 'personne_id',
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
			'Dossiercaf' => array(
				'className' => 'Dossiercaf',
				'foreignKey' => 'personne_id',
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
			'Dsp' => array(
				'className' => 'Dsp',
				'foreignKey' => 'personne_id',
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
			'Prestation' => array(
				'className' => 'Prestation',
				'foreignKey' => 'personne_id',
				'dependent' => true,
				'conditions' => array( 'Prestation.natprest' => 'RSA' ),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'PrestationPfa' => array(
				'className' => 'Prestation',
				'foreignKey' => 'personne_id',
				'dependent' => true,
				'conditions' => array( 'PrestationPfa.natprest' => 'PFA' ),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Nonoriente66' => array(
				'className' => 'Nonoriente66',
				'foreignKey' => 'personne_id',
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
		public $hasMany = array(
			'Bilanparcours66' => array(
				'className' => 'Bilanparcours66',
				'foreignKey' => 'personne_id',
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
			'Cui' => array(
				'className' => 'Cui',
				'foreignKey' => 'personne_id',
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
			'Activite' => array(
				'className' => 'Activite',
				'foreignKey' => 'personne_id',
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
			'Allocationsoutienfamilial' => array(
				'className' => 'Allocationsoutienfamilial',
				'foreignKey' => 'personne_id',
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
			'Avispcgpersonne' => array(
				'className' => 'Avispcgpersonne',
				'foreignKey' => 'personne_id',
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
			'Creancealimentaire' => array(
				'className' => 'Creancealimentaire',
				'foreignKey' => 'personne_id',
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
			'Dossierep' => array(
				'className' => 'Dossierep',
				'foreignKey' => 'personne_id',
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
			'Dossiercov58' => array(
				'className' => 'Dossiercov58',
				'foreignKey' => 'personne_id',
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
			'Entretien' => array(
				'className' => 'Entretien',
				'foreignKey' => 'personne_id',
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
			'Grossesse' => array(
				'className' => 'Grossesse',
				'foreignKey' => 'personne_id',
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
			'Informationeti' => array(
				'className' => 'Informationeti',
				'foreignKey' => 'personne_id',
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
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'personne_id',
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
			'Apre' => array(
				'className' => 'Apre',
				'foreignKey' => 'personne_id',
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
			'Apre66' => array(
				'className' => 'Apre66',
				'foreignKey' => 'personne_id',
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
			'Memo' => array(
				'className' => 'Memo',
				'foreignKey' => 'personne_id',
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
			'Orientation' => array(
				'className' => 'Orientation',
				'foreignKey' => 'personne_id',
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
			'Parcours' => array(
				'className' => 'Parcours',
				'foreignKey' => 'personne_id',
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
			'Infoagricole' => array(
				'className' => 'Infoagricole',
				'foreignKey' => 'personne_id',
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
			'Rattachement' => array(
				'className' => 'Rattachement',
				'foreignKey' => 'personne_id',
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
			'Suiviappuiorientation' => array(
				'className' => 'Suiviappuiorientation',
				'foreignKey' => 'personne_id',
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
			'Ressource' => array(
				'className' => 'Ressource',
				'foreignKey' => 'personne_id',
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
			'Propopdo' => array(
				'className' => 'Propopdo',
				'foreignKey' => 'personne_id',
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
				'foreignKey' => 'personne_id',
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
			'Titresejour' => array(
				'className' => 'Titresejour',
				'foreignKey' => 'personne_id',
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
			'Orientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'personne_id',
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
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Personne\'',
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
			'EntiteTag' => array(
				'className' => 'EntiteTag',
				'foreignKey' => 'fk_value',
				'dependent' => false,
				'conditions' => array(
					'EntiteTag.modele' => 'Personne'
				),
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
				'foreignKey' => 'personne_id',
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
			'Traitementpdo' => array(
				'className' => 'Traitementpdo',
				'foreignKey' => 'personne_id',
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
			'Conditionactiviteprealable' => array(
				'className' => 'Conditionactiviteprealable',
				'foreignKey' => 'personne_id',
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
			'DspRev' => array(
				'className' => 'DspRev',
				'foreignKey' => 'personne_id',
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
			'Historiquedroit' => array(
				'className' => 'Historiquedroit',
				'foreignKey' => 'personne_id',
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
			'Questionnaired1pdv93' => array(
				'className' => 'Questionnaired1pdv93',
				'foreignKey' => 'personne_id',
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
			'Questionnaired2pdv93' => array(
				'className' => 'Questionnaired2pdv93',
				'foreignKey' => 'personne_id',
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
			'Questionnaireb7pdv93' => array(
				'className' => 'Questionnaireb7pdv93',
				'foreignKey' => 'personne_id',
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
			'Ficheprescription93' => array(
				'className' => 'Ficheprescription93',
				'foreignKey' => 'personne_id',
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
			'Situationallocataire' => array(
				'className' => 'Situationallocataire',
				'foreignKey' => 'personne_id',
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
			'Correspondancepersonne' => array(
				'className' => 'Correspondancepersonne',
				'foreignKey' => 'personne1_id',
			),
		);
		public $hasAndBelongsToMany = array(
			'Actioncandidat' => array(
				'className' => 'Actioncandidat',
				'joinTable' => 'actionscandidats_personnes',
				'foreignKey' => 'personne_id',
				'associationForeignKey' => 'actioncandidat_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'ActioncandidatPersonne'
			),
			'Dossier' => array(
				'className' => 'Dossier',
				'joinTable' => 'derniersdossiersallocataires',
				'foreignKey' => 'personne_id',
				'associationForeignKey' => 'dossier_id',
				'unique' => true,
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'finderQuery' => null,
				'deleteQuery' => null,
				'insertQuery' => null,
				'with' => 'Dernierdossierallocataire'
			),
			'Fichedeliaison' => array(
				'className' => 'Fichedeliaison',
				'joinTable' => 'fichedeliaisons_personnes',
				'foreignKey' => 'personne_id',
				'associationForeignKey' => 'fichedeliaison_id',
				'unique' => true,
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'finderQuery' => null,
				'deleteQuery' => null,
				'insertQuery' => null,
				'with' => 'FichedeliaisonPersonne'
			),
			'Referent' => array(
				'className' => 'Referent',
				'joinTable' => 'personnes_referents',
				'foreignKey' => 'personne_id',
				'associationForeignKey' => 'referent_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'PersonneReferent'
			)
		);

		public $virtualFields = array(
			'nom_complet' => array(
				'type' => 'string',
				'postgres' => '( COALESCE( "%s"."qual", \'\' ) || \' \' || "%s"."nom" || \' \' || "%s"."prenom" )'
			),
			'nom_complet_court' => array(
				'type' => 'string',
				//'postgres' => '( "%s"."nom" || \' \' || "%s"."prenom" )'
				'postgres' => '( COALESCE( "%s"."nom", \'\' ) || \' \' || COALESCE( "%s"."prenom", \'\' ) )'
			),
			'nom_complet_prenoms' => array(
				'type' => 'string',
				'postgres' => '( COALESCE( "%s"."qual", \'\' ) || \' \' || "%s"."nom" || \' \' || "%s"."prenom" || \' \' || COALESCE( "%s"."prenom2", \'\' ) || \' \' || COALESCE( "%s"."prenom3", \'\' ) )'
			),
			'civilite_nom_complet_prenoms' => array(
				'type' => 'string',
				'postgres' => '( (CASE COALESCE( "%s"."qual", \'\' ) WHEN \'MR\' THEN \'Monsieur\' WHEN \'MME\' THEN \'Madame\' ELSE \'\' END) || \' \' || "%s"."nom" || \' \' || "%s"."prenom" || \' \' || COALESCE( "%s"."prenom2", \'\' ) || \' \' || COALESCE( "%s"."prenom3", \'\' ) )'
			),
			'age' => array(
				'type' => 'integer',
				'postgres' => '( EXTRACT ( YEAR FROM AGE( "%s"."dtnai" ) ) )'
			),
			'dtnai_year' => array(
				'type' => 'integer',
				'postgres' => 'EXTRACT(YEAR FROM "%s"."dtnai")'
			),
			'dtnai_month' => array(
				'type' => 'integer',
				'postgres' => 'EXTRACT(MONTH FROM "%s"."dtnai")'
			),
		);

		/**
		 * Valeurs possibles pour le champ virtuel etat_dossier_orientation (CG 58).
		 *
		 * @var array
		 * @see Personne::enums
		 */
		public $etat_dossier_orientation = array( 'oriente', 'en_attente', 'non_oriente', 'en_cours_reorientation' );

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('WebrsaPersonne');

		/**
		 *
		 * @param array $options
		 * @return mixed
		 */
		public function beforeSave( $options = array( ) ) {
			$return = parent::beforeSave( $options );

			// Mise en majuscule de nom, prénom, nomnai
			foreach( array( 'nom', 'prenom', 'prenom2', 'prenom3', 'nomnai' ) as $field ) {
				if( isset( $this->data['Personne'][$field] ) ) {
					if( !empty( $this->data['Personne'][$field] ) ) {
						$this->data['Personne'][$field] = strtoupper( replace_accents( $this->data['Personne'][$field] ) );
					}
				}
			}

			// Champs déduits
			if( isset( $this->data['Personne']['qual'] ) ) {
				if( !empty( $this->data['Personne']['qual'] ) ) {
					$this->data['Personne']['sexe'] = ( $this->data['Personne']['qual'] == 'MR' ) ? 1 : 2;
				}

				if( $this->data['Personne']['qual'] != 'MME' ) {
					$this->data['Personne']['nomnai'] = $this->data['Personne']['nom'];
				}
			}

			if( isset( $this->data['Personne']['nir'] ) ) {
				$this->data['Personne']['nir'] = trim( $this->data['Personne']['nir'] );
				if( !empty( $this->data['Personne']['nir'] ) ) {
					if( strlen( $this->data['Personne']['nir'] ) == 13 ) {
						$this->data['Personne']['nir'] = $this->data['Personne']['nir'].cle_nir( $this->data['Personne']['nir'] );
					}
				}
			}
			return $return;
		}

		/**
		 * Retourne l'id du dossier à partir de l'id de la personne
		 *
		 * @param integer $personne_id
		 * @return integer
		 */
		public function dossierId( $personne_id ) {
			$querydata = array(
				'fields' => array( 'Foyer.dossier_id' ),
				'joins' => array(
					$this->join( 'Foyer', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					'Personne.id' => $personne_id
				),
				'recursive' => -1
			);

			$personne = $this->find( 'first', $querydata );

			if( !empty( $personne ) ) {
				return $personne['Foyer']['dossier_id'];
			}
			else {
				return null;
			}
		}

		public function enums() {
			$cacheKey = implode( '_', array( $this->useDbConfig, $this->alias, __FUNCTION__ ) );
			$enums = Cache::read( $cacheKey );

			if( $enums === false ) {
				$enums = parent::enums();

				if( Configure::read( 'Cg.departement' ) == 58 ) {
					$field = 'etat_dossier_orientation';
					$domain = 'personne';

					if( !isset( $enums[$this->alias][$field] ) ) {
						$fieldNameUpper = strtoupper( $field );

						$list = array();

						foreach( $this->{$field} as $value ) {
							$list[$value] = __d( $domain, "ENUM::{$fieldNameUpper}::{$value}" );
						}

						$enums[$this->alias][$field] = $list;
					}
				}

				Cache::write( $cacheKey, $enums );
			}

			return $enums;
		}

		/**
		 * Préchargement du cache du modèle.
		 *
		 * @see Configure AncienAllocataire.enabled
		 */
		public function prechargement() {
			return $this->WebrsaPersonne->prechargement();
		}

		 /**
		 *   Calcul du nombre d emois restant avant la fin du titre de séjour de l'alcoataire
		 *   @params integer personne_id
         *  return integer Nb de mois avant la fin du titre de séjour
		 *
		 * @deprecated since version 3.1		Utilisé dans une methode dépréciée
		 */

		public function nbMoisAvantFinTitreSejour( $personne_id = null ) {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
            $date1 = '"Titresejour"."dftitsej"';
            $date2 = '"Cui"."datefintitresejour"';
            $date3 = 'NOW()';
            $vfNbMoisAvantFin = "EXTRACT( YEAR FROM AGE( {$date1}, {$date3} ) ) * 12 +  EXTRACT( MONTH FROM AGE( {$date1}, {$date3} ) )";
            $vfNbMoisAvantFinCui = "EXTRACT( YEAR FROM AGE( {$date2}, {$date3} ) ) * 12 +  EXTRACT( MONTH FROM AGE( {$date2}, {$date3} ) )";

			$result = $this->find(
				'first',
				array(
					'fields' => array(
                        'Titresejour.dftitsej',
						"( {$vfNbMoisAvantFin} ) AS \"Titresejour__nbMoisAvantFin\"",
                        'Cui.datefintitresejour',
                        "( {$vfNbMoisAvantFinCui} ) AS \"Cui__nbMoisAvantFinCui\"",
					),
					'joins' => array(
						$this->join( 'Titresejour', array( 'type' => 'LEFT OUTER' ) ),
                        $this->join( 'Cui', array( 'type' => 'LEFT OUTER' ) )
					),
					'conditions' => array(
						'Personne.id' => $personne_id
					),
					'recursive' => -1
				)
			);

            return $result;
		}

	}
?>