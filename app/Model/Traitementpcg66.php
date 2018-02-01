<?php
	/**
	 * Code source de la classe Traitementpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Traitementpcg66 ...
	 *
	 * @package app.Model
	 */
	class Traitementpcg66 extends AppModel
	{
		public $name = 'Traitementpcg66';

		/**
		* Chemin relatif pour les modèles de documents .odt utilisés lors des
		* impressions. Utiliser %s pour remplacer par l'alias.
		*/
		public $modelesOdt = array(
			'PCG66/fichecalcul.odt',
		);

		public $actsAs = array(
			'Fichiermodulelie',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate',
			'Gedooo.Gedooo',
			'Allocatairelie' => array(
				'joins' => array('Personnepcg66')
			),
		);

		public $validate = array(
			'amortissements' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'required' => null,
					'allowEmpty' => false
				),
				'numeric' => array(
					'rule' => array( 'numeric' ),
					'required' => null,
					'allowEmpty' => true
				)
			),
			'benefoudef' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'required' => null,
					'allowEmpty' => false
				)
			),
			'chaffsrv' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'required' => null,
					'allowEmpty' => false
				)
			),
			'chaffvnt' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'required' => null,
					'allowEmpty' => false
				)
			),
			'chaffagri' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'required' => null,
					'allowEmpty' => false
				)
			),
			'commentairepropodecision' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'required' => null,
					'allowEmpty' => false
				)
			),
			'compofoyerpcg66_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'eplaudition', true, array( '1' ) ),
					'message' => 'Champ obligatoire'
				)
			),
			'datefinprisecompte' => array(
                'compareDates' => array(
					'rule' => array( 'compareDates', 'dtdebutprisecompte', '>' ),
					'message' => 'La date de fin de prise en compte doit être strictement supérieure à la date de début'
				)
			),
			'dtdebutprisecompte' => array(
                'compareDates' => array(
                    'rule' => array( 'compareDates', 'datefinprisecompte', '<' ),
					'message' => 'La date de début de prise en compte doit être strictement inférieure à la date de fin'
                )
			),
			'dureedepart' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'required' => false,
					'allowEmpty' => false
				)
			),
			'dureeecheance' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'required' => false,
					'allowEmpty' => false
				)
			),
			'nrmrcs' => array(
				'alphaNumeric' => array(
					'rule' => array( 'alphaNumeric' ),
					'message' => 'Merci de saisir des valeurs alphanumériques',
					'required' => false,
					'allowEmpty' => false
				)
			),
			'propodecision' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'required' => false,
					'allowEmpty' => false
				)
			),
			'recidive' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'eplaudition', true, array( '1' ) ),
					'message' => 'Champ obligatoire'
				)
			),
			'regime' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'required' => false,
					'allowEmpty' => false,
					'message' => 'Champ obligatoire'
				)
			),
			'situationpdo_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'typetraitement', false, array( 'revenu' ) ),
					'message' => 'Champ obligatoire'
				)
			),
			'typecourrierpcg66_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'typetraitement', true, array( 'courrier' ) ),
					'message' => 'Champ obligatoire'
				)
			),
            'typetraitement' => array(
                NOT_BLANK_RULE_NAME => array(
                    'rule' => array( NOT_BLANK_RULE_NAME ),
                    'message' => 'Champ obligatoire'
                )
            )
		);

		public $belongsTo = array(
			'Personnepcg66' => array(
				'className' => 'Personnepcg66',
				'foreignKey' => 'personnepcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Descriptionpdo' => array(
				'className' => 'Descriptionpdo',
				'foreignKey' => 'descriptionpdo_id',
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
			),
			'Compofoyerpcg66' => array(
				'className' => 'Compofoyerpcg66',
				'foreignKey' => 'compofoyerpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Situationpdo' => array(
				'className' => 'Situationpdo',
				'foreignKey' => 'situationpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typecourrierpcg66' => array(
				'className' => 'Typecourrierpcg66',
				'foreignKey' => 'typecourrierpcg66_id',
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
		);

		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Traitementpcg66\'',
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
			'Dataimpression' => array(
				'className' => 'Dataimpression',
				'foreignKey' => 'fk_value',
				'dependent' => false,
				'conditions' => array(
					'Dataimpression.modele = \'Traitementpcg66\'',
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Decisiontraitementpcg66' => array(
				'className' => 'Decisiontraitementpcg66',
				'foreignKey' => 'traitementpcg66_id',
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

		public $hasOne = array(
			'Saisinepdoep66' => array(
				'className' => 'Saisinepdoep66',
				'foreignKey' => 'traitementpcg66_id',
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
			'Modeletraitementpcg66' => array(
				'className' => 'Modeletraitementpcg66',
				'foreignKey' => 'traitementpcg66_id',
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

		public $hasAndBelongsToMany = array(
			'Courrierpdo' => array(
				'className' => 'Courrierpdo',
				'joinTable' => 'courrierspdos_traitementspcgs66',
				'foreignKey' => 'traitementpcg66_id',
				'associationForeignKey' => 'courrierpdo_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'CourrierpdoTraitementpcg66'
			),
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'WebrsaTraitementpcg66'
		);

		/**
		 * Retourne l'id de la personne à laquelle est lié un enregistrement.
		 *
		 * @param integer $id L'id de l'enregistrement
		 * @return integer
		 */
		public function dossierpcg66Id( $id ) {
			$querydata = array(
				'fields' => array( "Personnepcg66.dossierpcg66_id" ),
				'joins' => array(
					$this->join( 'Personnepcg66', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					"{$this->alias}.id" => $id
				),
				'recursive' => -1
			);

			$result = $this->find( 'first', $querydata );

			if( !empty( $result ) ) {
				return $result['Personnepcg66']['dossierpcg66_id'];
			}
			else {
				return null;
			}
		}

		/**
		 * Nettoyage des données en fonction du type de traitement, et si le type
		 * de traitement est "Fiche de calcul", en fonction du régime.
		 *
		 * @param array $options
		 * @return boolean
		 */
		public function beforeValidate( $options = array() ) {
			$return = parent::beforeValidate( $options );

			// Si le type de traitement est "Fiche de calcul"
			if( Hash::get( $this->data, "{$this->alias}.typetraitement" ) == 'revenu' ) {
				// Suppression des règles de validation des champs chaffvnt, chaffsrv, benefoudef; mise à null des champs lorsque c'est nécessaire en fonction du régime
				$regime = Hash::get( $this->data, "{$this->alias}.regime" );
				// Si 'fagri', on n'en garde aucun
				if( $regime == 'fagri' ) {
					unset( $this->validate['chaffvnt'], $this->validate['chaffsrv'], $this->validate['chaffagri'], $this->validate['benefoudef'] );
					$this->data[$this->alias]['chaffvnt'] = null;
					$this->data[$this->alias]['chaffsrv'] = null;
					$this->data[$this->alias]['chaffagri'] = null;
					$this->data[$this->alias]['benefoudef'] = null;
				}
				// Si 'ragri' ou 'reel', on garde tout (chaffvnt, chaffsrv, benefoudef)
				// Si 'microbic' ou 'microbicauto', on garde chaffvnt, chaffsrv
				else if( in_array( $regime, array( 'microbic', 'microbicauto' ) ) ) {
					unset( $this->validate['benefoudef'], $this->validate['chaffagri'] );
					$this->data[$this->alias]['chaffagri'] = null;
					$this->data[$this->alias]['benefoudef'] = null;
				}
				// Si 'microbnc', on garde chaffsrv
				else if( $regime == 'microbnc' ) {
					unset( $this->validate['chaffvnt'], $this->validate['chaffagri'], $this->validate['benefoudef'] );
					$this->data[$this->alias]['chaffvnt'] = null;
					$this->data[$this->alias]['chaffagri'] = null;
					$this->data[$this->alias]['benefoudef'] = null;
				}
				// Si 'microbicagri', on garde chaffvnt, chaffsrv, chaffagri
				else if( in_array( $regime, array( 'microbicagri' ) ) ) {
					unset( $this->validate['benefoudef'] );
					$this->data[$this->alias]['benefoudef'] = null;
				}
			}
			// Si le type de traitement est autre que "Fiche de calcul"
			else {
				unset( $this->validate['chaffvnt'], $this->validate['chaffsrv'], $this->validate['chaffagri'], $this->validate['benefoudef'] );
				$this->data[$this->alias]['chaffvnt'] = null;
				$this->data[$this->alias]['chaffsrv'] = null;
				$this->data[$this->alias]['chaffagri'] = null;
				$this->data[$this->alias]['benefoudef'] = null;
			}

			return $return;
		}

		/**
		 * Effectue une jointure sur la personne en couple avec la Personne concernée par le traitement.
		 *
		 * @param array $query
		 * @return array
		 */
		public function joinCouple( $query ) {
			$replacements = array( 'Personne' => 'Personne2', 'Prestation' => 'prestations' );

			$query['fields'] = array_merge(
				$query['fields'],
				array_words_replace(
					$this->Personnepcg66->Personne->Foyer->Personne->fields(),
					$replacements
				)
			);

			$sq = $this->Personnepcg66->Personne->Prestation->sq(
				array(
					'fields' => array( 'prestations.personne_id' ),
					'conditions' => array(
						'Prestation.personne_id = Personne.id',
						'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					),
					'contain' => false,
				)
			);

			$join = array_words_replace(
				$this->Personnepcg66->Personne->Foyer->join(
					'Personne',
					array(
						'type' => 'LEFT OUTER',
						'conditions' => array(
							"\"Personne\".\"id\" IN ( {$sq} )"
						)
					)
				),
				$replacements
			);
			$join['conditions'] = array(
				$join['conditions'],
				'Personne.id <> Personne2.id'
			);
			$query['joins'][] = $join;

			return $query;
		}
	}
?>