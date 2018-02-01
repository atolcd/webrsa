<?php
	/**
	 * Fichier source de la classe Contratinsertion.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	define( 'DATE_DECISION_FACULTATIVE', Configure::read( 'Cg.departement' ) != 66 );
	define( 'REFERENT_FACULTATIF', Configure::read( 'Cg.departement' ) != 66 );
	define( 'STORABLE_PDF_ACTIVE', Configure::read( 'Cg.departement' ) != 66 );
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Contratinsertion permet de gérer les CER de manière individuelle.
	 *
	 * @package app.Model
	 */
	class Contratinsertion extends AppModel
	{
		public $name = 'Contratinsertion';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $uses = array( 'Option', 'WebrsaContratinsertion' );

		public $actsAs = array(
			'Allocatairelie',
			'Fichiermodulelie',
			'Gedooo.Gedooo',
			'ModelesodtConditionnables' => array(
				58 => '%s/contratinsertioncg58.odt',
				66 => array(
					'%s/notificationop.odt',
					'%s/notifbenefSimplevalide.odt',
					'%s/notifbenefParticuliervalide.odt',
					'%s/notifbenefSimplenonvalide.odt',
					'%s/notifbenefParticuliernonvalide.odt',
					'%s/contratinsertion.odt',
					'%s/contratinsertionold.odt',
					'%s/ficheliaisoncerParticulier.odt',
					'%s/ficheliaisoncerSimpleoa.odt',
					'%s/ficheliaisoncerSimplemsp.odt',
					'%s/tacitereconduction66.odt'
				),
				93 => '%s/contratinsertion.odt',
				976 => '%s/contratinsertioncg976.odt'
			),
			'StorablePdf' => array(
				'active' => STORABLE_PDF_ACTIVE,
				'afterSave' => 'deleteAll'
			),
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate',
		);

		public $validate = array(
			'actions_prev' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'dd_ci' => array(
				'compareDates' => array(
					'rule' => array( 'compareDates', 'df_ci', '<' ),
					'message' => 'La date de début de contrat doit être strictement inférieure à la date de fin de contrat'
				)
			),
			'df_ci' => array(
				'compareDates' => array(
					'rule' => array( 'compareDates', 'dd_ci', '>' ),
					'message' => 'La date de fin de contrat doit être strictement supérieure à la date de début de contrat'
				)
			),
			'aut_expr_prof' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'forme_ci' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'emp_trouv' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'sect_acti_emp' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'emp_occupe' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'duree_hebdo_emp' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'nat_cont_trav' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'duree_cdd' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'duree_engag' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				),
				'checkDureeDates' => array(
					'rule' => array( 'checkDureeDates', 'dd_ci', 'df_ci' ),
					'message' => 'Les dates de début et de fin ne correspondent pas à la durée'
				)
			),
			'nature_projet' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'decision_ci' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'datevalidation_ci' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'decision_ci', true, array( 'V' ) ),
					'message' => 'Veuillez entrer une date valide',
				)
			),
			'lieu_saisi_ci' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'niveausalaire' => array(
				'comparison' => array(
					'rule' => array( 'comparison', '>=', 0 ),
					'message' => 'Veuillez entrer un nombre positif.'
				)
			),
			'date_saisi_ci' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				),
				'datePassee' => array(
					'rule' => array( 'datePassee' ),
					'message' => 'Merci de choisir une date antérieure à la date du jour',
					'on' => 'create'
				)
			),
			'datedecision' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire',
                    'allowEmpty' => DATE_DECISION_FACULTATIVE
				)
			),
			/**
			 * Régle ajoutée suite à la demande du CG66
			 */
			'nature_projet' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
            'referent_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire',
                    'allowEmpty' => REFERENT_FACULTATIF
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
			'aviseqpluri' => array('R', 'M'),
            'avisraison_ci' => array('D', 'N', 'A'),
			'raison_ci' => array('S', 'R'),
			'sect_acti_emp' => array(
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
				'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'
			),
			'emp_occupe' => array(
				'10', '21', '22', '23', '31', '33', '34', '35', '37',
				'38', '42', '43', '44', '45', '46', '47', '48', '52',
				'53', '54', '55', '56', '62', '63', '64', '65', '67',
				'68', '69'
			),
			'duree_hebdo_emp' => array('DHT1', 'DHT2', 'DHT3'),
			'nat_cont_trav' => array('TCT1', 'TCT2', 'TCT3', 'TCT4', 'TCT5', 'TCT6', 'TCT7', 'TCT8', 'TCT9'),
			'duree_cdd' => array('DT1', 'DT2', 'DT3'),
			'decision_ci' => array('E', 'V', 'N', 'A'),
			'forme_ci' => array('S', 'C'),
		);

		public $belongsTo = array(
			'Action' => array(
				'className' => 'Action',
				'foreignKey' => false,
				'conditions' => array( 'Contratinsertion.engag_object = Action.code' ),
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
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typocontrat' => array(
				'className' => 'Typocontrat',
				'foreignKey' => 'typocontrat_id',
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
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'foreignKey' => 'zonegeographique_id',
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
			)
		);
		public $hasMany = array(
			'Actioninsertion' => array(
				'className' => 'Actioninsertion',
				'foreignKey' => 'contratinsertion_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Autreavissuspension' => array(
				'className' => 'Autreavissuspension',
				'foreignKey' => 'contratinsertion_id',
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
			'Autreavisradiation' => array(
				'className' => 'Autreavisradiation',
				'foreignKey' => 'contratinsertion_id',
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
			'Bilanparcours66' => array(
				'className' => 'Bilanparcours66',
				'foreignKey' => 'contratinsertion_id',
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
					'Fichiermodule.modele = \'Contratinsertion\'',
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
			'Nonrespectsanctionep93' => array(
				'className' => 'Nonrespectsanctionep93',
				'foreignKey' => 'contratinsertion_id',
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
			'Signalementep93' => array(
				'className' => 'Signalementep93',
				'foreignKey' => 'contratinsertion_id',
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
			'Contratcomplexeep93' => array(
				'className' => 'Contratcomplexeep93',
				'foreignKey' => 'contratinsertion_id',
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
			'Propocontratinsertioncov58nv' => array(
				'className' => 'Propocontratinsertioncov58',
				'foreignKey' => 'nvcontratinsertion_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Sanctionep58' => array(
				'className' => 'Sanctionep58',
				'foreignKey' => 'contratinsertion_id',
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
				'foreignKey' => 'contratinsertion_id',
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
			'Saisinebilanparcoursep66nv' => array(
				'className' => 'Saisinebilanparcoursep66',
				'foreignKey' => 'nvcontratinsertion_id',
				'dependent' => false,
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
		public $hasAndBelongsToMany = array(
			'User' => array(
				'className' => 'User',
				'joinTable' => 'contratsinsertion_users',
				'foreignKey' => 'contratinsertion_id',
				'associationForeignKey' => 'user_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'ContratinsertionUser'
			)
		);
		public $hasOne = array(
			'Bilanparcours66nv' => array(
				'className' => 'Bilanparcours66',
				'foreignKey' => 'nvcontratinsertion_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Cer93' => array(
				'className' => 'Cer93',
				'foreignKey' => 'contratinsertion_id',
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
			'Dossierpcg66' => array(
				'className' => 'Dossierpcg66',
				'foreignKey' => 'contratinsertion_id',
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
			'Propodecisioncer66' => array(
				'className' => 'Propodecisioncer66',
				'foreignKey' => 'contratinsertion_id',
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
		public $virtualFields = array(
			'nbjours' => array(
				'type' => 'integer',
				'postgres' => 'DATE_PART( \'day\', NOW() - "%s"."df_ci" )'
			),
			'present' => array(
				'type' => 'boolean',
				'postgres' => '(CASE WHEN "%s"."id" IS NOT NULL THEN true ELSE false END )'
			),
			'dernier' => array(
				'type'      => 'boolean',
				'postgres'  => 'NOT EXISTS(
					SELECT * FROM contratsinsertion AS a
					WHERE a.personne_id = "%s"."personne_id"
					AND (
						a.date_saisi_ci > "%s"."date_saisi_ci"
						OR (
							a.date_saisi_ci = "%s"."date_saisi_ci"
							AND a.created > "%s"."created"
						)
					)
					LIMIT 1)'
			),
		);

		/**
		 * Surcharge du constructeur avec ajout du champ virtuel num_contrat_66
		 * lorsqu'il s'agit d'un renouvellement et que l'allocataire avait plus
		 * de 55 ans à la date de début du CER (CG 66).
		 *
		 * @param integer|string|array $id Set this ID for this model on startup, can also be an array of options, see above.
		 * @param string $table Name of database table to use.
		 * @param string $ds DataSource connection name.
		 */
		public function __construct( $id = false, $table = null, $ds = null ) {
			parent::__construct( $id, $table, $ds );

			if( Configure::read( 'Cg.departement' ) == 66 && !class_exists( 'PHPUnit_Framework_TestCase', false ) ) {
				$this->loadVirtualFields();
			}
		}

		/**
		 * Surcharge de la méthode beforeValidate pour nettoyer la valeur de
		 * duree_engag qui peut être suivie d'un '_' au CG 58 lorsque le formulaire
		 * est renvoyé en appuyant sur entrée alors que l'on se trouve dans le
		 * l'input de ce champ.
		 *
		 * @see Propocontratinsertioncov58::beforeValidate()
		 *
		 * @param array $options
		 * @return boolean
		 */
		public function beforeValidate( $options = array() ) {
			$result = parent::beforeValidate( $options );

			$path = "{$this->alias}.duree_engag";
			if( Hash::check( $this->data, $path ) ) {
				$value = Hash::get( $this->data, $path );
				$value = preg_replace( '/^[^0-9]*([0-9]+)[^0-9]*$/', '\1', $value );
				$this->data = Hash::insert( $this->data, $path, $value );
			}

			return $result;
		}

		/**
		 * BeforeSave
		 */
		public function beforeSave( $options = array( ) ) {
			$return = parent::beforeSave( $options );

			if( array_key_exists( $this->name, $this->data ) && array_key_exists( 'structurereferente_id', $this->data[$this->name] ) ) {
				$this->data[$this->name]['structurereferente_id'] = suffix( $this->data[$this->name]['structurereferente_id'] );
			}

			///Ajout pour obtenir referent lié à structure
			$hasMany = ( array_depth( $this->data ) > 2 );

			if( !$hasMany ) { // INFO: 1 seul enregistrement
				if( array_key_exists( 'referent_id', $this->data[$this->name] ) ) {
						$this->data[$this->name]['referent_id'] = preg_replace( '/^[0-9]+_([0-9]+)$/', '\1', $this->data[$this->name]['referent_id'] );
				}
			}
			else { // INFO: plusieurs enregistrements
				foreach( $this->data[$this->name] as $key => $value ) {
					if( is_array( $value ) && array_key_exists( 'referent_id', $value ) ) {
						$this->data[$this->name][$key]['referent_id'] = preg_replace( '/^[0-9]+_([0-9]+)$/', '\1', $value['referent_id'] );
					}
				}
			}
			///Fin ajout pour récupération referent lié a structure
			/// FIXME: faire un behavior
			foreach( array( 'actions_prev' ) as $key ) {
				if( isset( $this->data[$this->name][$key] ) ) {
					$this->data[$this->name][$key] = Set::enum( $this->data[$this->name][$key], array( 'O' => '1', 'N' => '0' ) );
				}
			}

			// Si aucun emploi trouvé, alors il faut éventuellement vider certaines valeurs
			if( isset( $this->data[$this->name]['emp_trouv'] ) && $this->data[$this->name]['emp_trouv'] != 'O' ) {
				$champs = array( 'sect_acti_emp', 'emp_occupe', 'duree_hebdo_emp', 'nat_cont_trav', 'duree_cdd' );
				foreach( $champs as $champ ) {
					$this->data[$this->name][$champ] = null;
				}
			}

			foreach( array( 'emp_trouv' ) as $key ) {
				if( isset( $this->data[$this->name][$key] ) ) {
					$this->data[$this->name][$key] = Set::enum( $this->data[$this->name][$key], array( 'O' => true, 'N' => false ) );
				}
			}

			//Modification décision cer
			if( isset( $this->data[$this->name]['decision_ci'] ) && $this->data[$this->name]['decision_ci'] != 'V' ) {
				$this->data[$this->name]['datevalidation_ci'] = NULL;
			}

			//Pour le CG66, la date de validation est datedecision
			if( isset( $this->data[$this->name]['decision_ci'] ) && ( Configure::read( 'Cg.departement' ) == 66 ) ) {
				if( $this->data[$this->name]['decision_ci'] == 'V' ) {
					$this->data[$this->name]['datevalidation_ci'] = $this->data[$this->name]['datedecision'];
				}
				else if( $this->data[$this->name]['decision_ci'] == 'E' ) {
					$this->data[$this->name]['datedecision'] = NULL;
				}
			}

			// Si la nature du contrat de travail n'est pas un CDD, alors il faut éventuellement vider la valeur de la durée du CDD
			if( isset( $this->data[$this->name]['nat_cont_trav'] ) && $this->data[$this->name]['nat_cont_trav'] != 'TCT3' ) {
				$this->data[$this->name]['duree_cdd'] = NULL;
			}

			return $return;
		}

		/**
		 *   AfterSave
		 */
		public function afterSave( $created, $options = array() ) {
			parent::afterSave( $created, $options );

			// Mise à jour des APREs
			$this->query( "UPDATE apres SET eligibiliteapre = 'O' WHERE apres.personne_id = ".$this->data[$this->name]['personne_id']." AND apres.etatdossierapre = 'COM';" );
			$this->query( "UPDATE apres SET eligibiliteapre = 'N' WHERE apres.personne_id = ".$this->data[$this->name]['personne_id']." AND apres.etatdossierapre = 'INC';" );

			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$this->WebrsaContratinsertion->updatePositionsCersById( $this->{$this->primaryKey} );

				$this->_liaisonDossierpcg66( $created );
			}
		}

		/**
		 * Surcharge de la méthode enums() afin d'ajouter la valeur virtuelle
		 * "Renouvellement par tacite reconduction" pour le champ num_contrat
		 * pour le CG 66.
		 *
		 * @return array
		 */
		public function enums() {
			$departement = (integer)Configure::read('Cg.departement');
			$options = Hash::merge(
				parent::enums(),
				array(
					$this->alias => array(
						'duree_engag' => $this->Option->duree_engag()
					)
				)
			);

			if ($departement === 93) {
				$options[$this->alias]['decision_ci'] = array(
					'E' => 'En attente de décision',
					'V' => 'Validation à compter du',
					'A' => 'Annulé',
					'R' => 'Rejet'
				);
			}

			if ($departement === 66) {
				$options[$this->alias]['num_contrat_66'] = $options[$this->alias]['num_contrat'];

				$options[$this->alias]['num_contrat_66']['REN_TACITE'] = __d(
					Inflector::underscore( $this->name ),
					'ENUM::NUM_CONTRAT_66::REN_TACITE'
				);
			}

			if (Configure::read( 'nom_form_ci_cg' ) === 'cg66') {
				$options[$this->alias]['forme_ci'] = array('S' => 'Simple', 'C' => 'Particulier');
			}

			// Ticket #2007701: suppression des positions "En cours, Bilan à réaliser",
			// "En attente de renouvellement", "Périmé: bilan à réaliser"
			if( 66 === $departement ) {
				$keep = array(
					'encours'=> null,
					'attvalid'=> null,
					'annule'=> null,
					'fincontrat'=> null,
					'perime'=> null,
					'nonvalid'=> null,
					'bilanrealiseattenteeplparcours'=> null
				);
				$options[$this->alias]['positioncer'] = array_intersect_key( $options[$this->alias]['positioncer'], $keep );
			}

			return $options;
		}

		protected function _liaisonDossierpcg66( $created ) {
			$success = true;

			if( isset( $this->data[$this->alias]['forme_ci'] ) ) {
				$dossierpcg66Precedent = $this->Dossierpcg66->find(
						'first', array(
					'conditions' => array(
						'Dossierpcg66.contratinsertion_id' => $this->id
					),
					'contain' => false
						)
				);

				$forme_ci = $this->data[$this->alias]['forme_ci'];
				if( ( empty( $dossierpcg66Precedent ) || $created ) && ( $forme_ci == 'C' ) ) {
					$contrat = $this->find(
							'first', array(
						'fields' => array_merge(
								$this->fields(), $this->Personne->fields()
						),
						'conditions' => array(
							'Contratinsertion.id' => $this->id
						),
						'contain' => false,
						'joins' => array(
							$this->join( 'Personne', array( 'type' => 'INNER' ) )
						)
							)
					);
					if( empty( $contrat ) ) {
						return false;
					}

					// L'origine PDO est-elle bien définie comme liée à la création d'un CER ?
					$originepdo = $this->Dossierpcg66->Originepdo->find(
							'first', array(
						'fields' => array(
							'Originepdo.id'
						),
						'conditions' => array(
							'Originepdo.cerparticulier' => 'O'
						),
						'contain' => false
							)
					);
					if( empty( $originepdo ) ) {
						return false;
					}
					// Le type de PDO est-il bien défini comme liée à la création d'un CER ?
					$typepdo = $this->Dossierpcg66->Typepdo->find(
							'first', array(
						'fields' => array(
							'Typepdo.id'
						),
						'conditions' => array(
							'Typepdo.cerparticulier' => 'O'
						),
						'contain' => false
							)
					);
					if( empty( $typepdo ) ) {
						return false;
					}

// 					$datecreationCERParticulier = Set::classicExtract( $contrat, 'Contratinsertion.created' );

					$dossierpcg66 = array(
						'Dossierpcg66' => array(
							'contratinsertion_id' => $this->id,
							'foyer_id' => $contrat['Personne']['foyer_id'],
							'originepdo_id' => $originepdo['Originepdo']['id'],
							'typepdo_id' => $typepdo['Typepdo']['id'],
							'orgpayeur' => 'CAF',
							'datereceptionpdo' => date( 'Y-m-d', strtotime( $contrat[$this->alias]['date_saisi_ci'] ) ),
							'haspiecejointe' => 0
						)
					);

					if( !empty( $dossierpcg66 ) ) {
						$this->Dossierpcg66->create( $dossierpcg66 );
						$success = $this->Dossierpcg66->save( null, array( 'atomic' => false ) ) && $success;
					}
				}
				//else if( $forme_ci == 'S' ) {
				else if( !empty( $dossierpcg66Precedent ) && ( $forme_ci == 'S' ) ) {
					// FIXME: à voir avec le CG ...
					$this->log( 'Passage d\'un CER C à S; que fait-on ?', LOG_DEBUG );
				}
				else if( empty( $dossierpcg66Precedent ) && ( $forme_ci == 'S' ) ) {

				}
				else {
					return false;
				}
			}

			return $success; // FIXME: à traiter
		}

		/**
		 * Retourne la liste des clés de configuration pour lesquelles il faut
		 * vérifier la syntaxe de l'intervalle PostgreSQL.
		 *
		 * @return array
		 */
		public function checkPostgresqlIntervals() {
			$keys = array();

			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$keys[] = 'Contratinsertion.Cg66.updateEncoursbilan';
				$keys[] = 'Contratinsertion.Cg66.toleranceDroitClosCerComplexe';
			}

			return $this->_checkPostgresqlIntervals( $keys );
		}

		/**
		 * Chargement des champs virtuels dynamiques du modèle.
		 */
		public function loadVirtualFields() {
			$sql = $this->Personne->sq(
				array(
					'alias' => 'propriocer',
					'fields' => array( 'propriocer.dtnai' ),
					'contain' => false,
					'conditions' => array( "propriocer.id = {$this->alias}.personne_id" ),
					'limit' => 1
				)
			);

			$this->virtualFields['num_contrat_66'] = '(
				CASE
					WHEN ( "'.$this->alias.'"."num_contrat" = \'REN\' AND EXTRACT( YEAR FROM AGE( "'.$this->alias.'"."dd_ci", ( '.$sql.' ) ) ) >= 55 AND "'.$this->alias.'"."datetacitereconduction" IS NOT NULL ) THEN \'REN_TACITE\'
					ELSE "'.$this->alias.'"."num_contrat"::text
				END
			)';
		}

		/**
		 * Récupération des données nécessaires à l'impression du PDF par défaut
		 * du contrat.
		 *
		 * Pont vers la méthode WebrsaContratinsertion::getDataForPdf permettant de
		 * fonctionner avec StorablePdfBehavior.
		 *
		 * @param integer $id L'id technique de l'orientation
		 * @param integer $user_id L'id technique de l'utilisateur effectuant l'impression
		 * @return array
		 */
		public function getDataForPdf( $id, $user_id = null ) {
			return $this->WebrsaContratinsertion->getDataForPdf( $id, $user_id );
		}

		/**
		 * Retourne le chemin relatif du modèle de document utilisé pour
		 * l'impression du PDF par défaut.
		 *
		 * Pont vers la méthode WebrsaContratinsertion::modeleOdt permettant de
		 * fonctionner avec StorablePdfBehavior.
		 *
		 * @param array $data Les données envoyées au modèle pour construire le PDF
		 * @return string
		 */
		public function modeleOdt( $data ) {
			return $this->WebrsaContratinsertion->modeleOdt( $data );
		}
	}
?>