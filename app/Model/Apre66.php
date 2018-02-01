<?php
	/**
	 * Code source de la classe Apre66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Apre66 ...
	 *
	 * @package app.Model
	 */
	class Apre66 extends AppModel
	{

		public $name = 'Apre66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'numeroapre';

		public $useTable = 'apres';

		public $actsAs = array(
			'Allocatairelie',
			'Fichiermodulelie',
			'Gedooo.Gedooo',
			'Conditionnable',
			'ModelesodtConditionnables' => array(
				66 => array(
					'APRE/apre66.odt',
					'APRE/accordaide.odt',
					'APRE/refusaide.odt',
				)
			),
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'activitebeneficiaire' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'typedemandeapre' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'avistechreferent' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'secteurprofessionnel' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'montantattribue' => array(
				'numeric' => array(
					'rule' => array( 'numeric' ),
					'message' => 'Veuillez entrer une valeur numérique.'
				),
			),
			'structurereferente_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'datedemandeapre' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'referent_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			//Partie activité bénéficiaire
			'typecontrat' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'activitebeneficiaire', true, array( 'E' ) ),
					'message' => 'Champ obligatoire',
					'required' => false
				)
			),
			'dureecontrat' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'activitebeneficiaire', true, array( 'E' ) ),
					'message' => 'Champ obligatoire'
				)
			),
			'nomemployeur' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'activitebeneficiaire', true, array( 'E' ) ),
					'message' => 'Champ obligatoire'
				)
			),
			'adresseemployeur' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'activitebeneficiaire', true, array( 'E' ) ),
					'message' => 'Champ obligatoire'
				)
			),
			'hascer' => array(
				'equalTo' => array(
					'rule' => array( 'equalTo', '1' ),
					'message' => 'Champ obligatoire'
				)
			),
			'isbeneficiaire' => array(
				'equalTo' => array(
					'rule' => array( 'equalTo', '1' ),
					'message' => 'Champ obligatoire'
				)
			),
			'respectdelais' => array(
				'equalTo' => array(
					'rule' => array( 'equalTo', '1' ),
					'message' => 'Champ obligatoire'
				)
			)
		);

		public $hasOne = array(
			'Aideapre66' => array(
				'className' => 'Aideapre66',
				'foreignKey' => 'apre_id',
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
		public $belongsTo = array(
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
			'Referent' => array(
				'className' => 'Referent',
				'foreignKey' => 'referent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Referentapre' => array(
				'className' => 'Referent',
				'foreignKey' => 'referent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Apre66\'',
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

		public $virtualFields = array(
			'nb_fichiers_lies' => array(
				'type' => 'integer',
				'postgres'  => '(SELECT COUNT(*) FROM fichiersmodules AS f
					WHERE "%s"."id" = f.fk_value AND f.modele = \'%s\')'
			)
		);

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('WebrsaApre66');

		/**
		 *
		 */
		public function numeroapre() {
			$numSeq = $this->query( "SELECT nextval('apres_numeroapre_seq');" );
			if( $numSeq === false ) {
				return null;
			}

			$numapre = date( 'Ym' ).sprintf( "%010s", $numSeq[0][0]['nextval'] );
			return $numapre;
		}

		/**
		 * Ajout de l'identifiant de la séance lors de la sauvegarde.
		 */
		public function beforeValidate( $options = array( ) ) {
			$primaryKey = Set::classicExtract( $this->data, "{$this->alias}.{$this->primaryKey}" );
			$numeroapre = Set::classicExtract( $this->data, "{$this->alias}.numeroapre" );

			if( empty( $primaryKey ) && empty( $numeroapre ) && empty( $this->{$this->primaryKey} ) ) {
				$this->data[$this->alias]['numeroapre'] = $this->numeroapre();
			}

			return true;
		}

		/**
		 * Retourne le PDF par défaut généré par les appels aux méthodes getDataForPdf, modeleOdt et
		 * à la méthode ged du behavior Gedooo
		 *
		 * @param type $id Id de l'APRE
		 * @param type $user_id Id de l'utilisateur connecté
		 * @return string
		 */
		public function getDefaultPdf( $id, $user_id ) {
			return $this->WebrsaApre66->getDefaultPdf($id, $user_id);
		}
	}
?>