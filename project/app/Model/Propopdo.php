<?php
	/**
	 * Code source de la classe Propopdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Propopdo ...
	 *
	 * @package app.Model
	 */
	class Propopdo extends AppModel
	{
		public $name = 'Propopdo';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Allocatairelie',
			'Fichiermodulelie',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
			'Gedooo.Gedooo'
		);

		/**
		 * Autres modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Option' );

		public $validate = array(
			'typepdo_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'originepdo_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
            'structurereferente_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
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
			'motifpdo' => array('E', 'A', 'N'),
		);

		public $belongsTo = array(
			'Typepdo' => array(
				'className' => 'Typepdo',
				'foreignKey' => 'typepdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typenotifpdo' => array(
				'className' => 'Typenotifpdo',
				'foreignKey' => 'typenotifpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Originepdo' => array(
				'className' => 'Originepdo',
				'foreignKey' => 'originepdo_id',
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
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
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
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
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
			)
		);

		public $hasMany = array(
			'Piecepdo' => array(
				'className' => 'Piecepdo',
				'foreignKey' => 'propopdo_id',
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
			'Traitementpdo' => array(
				'className' => 'Traitementpdo',
				'foreignKey' => 'propopdo_id',
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
			'Decisionpropopdo' => array(
				'className' => 'Decisionpropopdo',
				'foreignKey' => 'propopdo_id',
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
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Propopdo\'',
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
				'foreignKey' => 'propopdo_id',
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
			'Situationpdo' => array(
				'className' => 'Situationpdo',
				'joinTable' => 'propospdos_situationspdos',
				'foreignKey' => 'propopdo_id',
				'associationForeignKey' => 'situationpdo_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'PropopdoSituationpdo'
			),
			'Statutdecisionpdo' => array(
				'className' => 'Statutdecisionpdo',
				'joinTable' => 'propospdos_statutsdecisionspdos',
				'foreignKey' => 'propopdo_id',
				'associationForeignKey' => 'statutdecisionpdo_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'PropopdoStatutdecisionpdo'
			),
			'Statutpdo' => array(
				'className' => 'Statutpdo',
				'joinTable' => 'propospdos_statutspdos',
				'foreignKey' => 'propopdo_id',
				'associationForeignKey' => 'statutpdo_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'PropopdoStatutpdo'
			)
		);

		/**
		 *
		 * @param array $options
		 * @return bool
		 */
		public function beforeSave( $options = array() ) {
			$return = parent::beforeSave( $options );

			if( Configure::read( 'nom_form_pdo_cg' ) == 'cg66' && isset( $this->data['Propopdo']['originepdo_id'] ) ) {

				$typepdo_id = Set::extract( $this->data, 'Propopdo.typepdo_id' );
				$user_id = Set::extract( $this->data, 'Propopdo.user_id' );
				$iscomplet = Set::extract( $this->data, 'Propopdo.iscomplet' );
				$decisionpdo_id = null;
				$avistechnique = null;
				$validationavis = null;

				if ( isset( $this->data['Propopdo']['id'] ) ) {
					$decisionpropopdo = $this->Decisionpropopdo->find(
						'first',
						array(
							'conditions' => array(
								'Decisionpropopdo.propopdo_id' => $this->data['Propopdo']['id']
							),
							'order' => array(
								'Decisionpropopdo.created DESC'
							)
						)
					);

					$decisionpdo_id = Set::extract( $decisionpropopdo, 'Decisionpropopdo.decisionpdo_id' );
					$avistechnique = Set::extract( $decisionpropopdo, 'Decisionpropopdo.avistechnique' );
					$validationavis = Set::extract( $decisionpropopdo, 'Decisionpropopdo.validationdecision' );

					$this->data['Propopdo']['etatdossierpdo'] = $this->etatDossierPdo( $typepdo_id, $user_id, $decisionpdo_id, $avistechnique, $validationavis, $iscomplet, $this->data['Propopdo']['id'] );
				}
				else {
					$this->data['Propopdo']['etatdossierpdo'] = $this->etatDossierPdo( $typepdo_id, $user_id, $decisionpdo_id, $avistechnique, $validationavis, $iscomplet );
				}
			}

			return $return;
		}

		/**
		 *
		 * @param integer $typepdo_id
		 * @param integer $user_id
		 * @param integer $decisionpdo_id
		 * @param type $avistechnique
		 * @param type $validationavis
		 * @param type $iscomplet
		 * @param type $propopdo_id
		 * @return string
		 */
		public function etatDossierPdo( $typepdo_id = null, $user_id = null, $decisionpdo_id = null, $avistechnique = null, $validationavis = null, $iscomplet = null, $propopdo_id = null ) {
			$etat = null;

			if ( !empty( $propopdo_id ) ) {
				$decisionpropopdo = $this->Decisionpropopdo->find(
					'first',
					array(
						'conditions' => array(
							'Decisionpropopdo.propopdo_id' => $propopdo_id
						),
						'order' => array(
							'Decisionpropopdo.created DESC'
						),
						'contain' => false
					)
				);

				$traitementspdos = $this->Traitementpdo->find(
					'all',
					array(
						'fields' => array(
							'Traitementpdo.id'
						),
						'conditions' => array(
							'Traitementpdo.propopdo_id' => $propopdo_id,
							'Traitementpdo.clos' => 0,
							'Traitementpdo.daterevision >' => date( 'Y-m-d' )
						),
						'contain' => false
					)
				);
			}

			if ( isset( $decisionpropopdo['Decisionpropopdo']['etatdossierpdo'] ) && $decisionpropopdo['Decisionpropopdo']['etatdossierpdo'] == 'instrencours' )
				$etat = 'instrencours';
			elseif ( isset( $traitementspdos ) && count( $traitementspdos ) > 0  && isset( $decisionpropopdo['Decisionpropopdo']['etatdossierpdo'] ) && $decisionpropopdo['Decisionpropopdo']['etatdossierpdo'] == 'decisionval' )
				$etat = 'attpj';
			elseif ( !empty($typepdo_id) && empty($user_id) )
				$etat = 'attaffect';
			elseif ( !empty($typepdo_id) && !empty($user_id) && empty($iscomplet) )
				$etat = 'attinstr';
			elseif ( !empty($typepdo_id) && !empty($user_id) && !empty($iscomplet) && empty($decisionpdo_id) )
				$etat = 'instrencours';
			else if ( !empty($typepdo_id) && !empty($user_id) && !empty($iscomplet) && !empty($decisionpdo_id) && !is_numeric($avistechnique) )
				$etat = 'attavistech';
			else if ( !empty($typepdo_id) && !empty($user_id) && !empty($iscomplet) && !empty($decisionpdo_id) && is_numeric($avistechnique) && !is_numeric( $validationavis ) )
				$etat = 'attval';
			elseif ( !empty($typepdo_id) && !empty($user_id) && !empty($iscomplet) && !empty($decisionpdo_id) && is_numeric($avistechnique) && is_numeric($validationavis) && $validationavis == '0' )
				$etat = 'instrencours';
			elseif ( !empty($typepdo_id) && !empty($user_id) && !empty($iscomplet) && !empty($decisionpdo_id) && is_numeric($avistechnique) && is_numeric($validationavis) && $validationavis == '1' && $iscomplet=='COM' )
				$etat = 'dossiertraite';
			elseif ( !empty($typepdo_id) && !empty($user_id) && !empty($iscomplet) && !empty($decisionpdo_id) && is_numeric($avistechnique) && is_numeric($validationavis) && $validationavis == '1' && $iscomplet=='INC' )
				$etat = 'attpj';
			return $etat;
		}

		/**
		 *
		 * @param integer $decisionpropopdo_id
		 * @return bool|array
		 */
		public function updateEtat( $decisionpropopdo_id ) {
			$decisionpropopdo = $this->Decisionpropopdo->find(
				'first',
				array(
					'conditions' => array(
						'Decisionpropopdo.id' => $decisionpropopdo_id
					),
					'contain' => array(
						'Propopdo'
					)
				)
			);

			$etat = $this->etatDossierPdo( $decisionpropopdo['Propopdo']['typepdo_id'], $decisionpropopdo['Propopdo']['user_id'], $decisionpropopdo['Decisionpropopdo']['decisionpdo_id'], $decisionpropopdo['Decisionpropopdo']['avistechnique'], $decisionpropopdo['Decisionpropopdo']['validationdecision'], $decisionpropopdo['Propopdo']['iscomplet'], $decisionpropopdo['Decisionpropopdo']['propopdo_id'] );

			$this->id = $decisionpropopdo['Decisionpropopdo']['propopdo_id'];
			$return = $this->saveField( 'etatdossierpdo', $etat );

			return $return;
		}

		/**
		 * Retourne le courrier d'une PDO.
		 *
		 * @param integer $propopdo_id
		 * @param integer $user_id
		 * @return string
		 */
		public function getCourrierPdo( $propopdo_id, $user_id ) {
			$Option = ClassRegistry::init( 'Option' );

			// Options pour les traductions
			$options = array(
				'Foyer' => array(
					'sitfam' => $Option->sitfam()
				),
			);
			$options = Hash::merge( $options, $this->enums() );

			// Lecture des données de la PDO
            $queryData = array(
                'fields' => array_merge(
                    $this->fields(),
                    $this->Personne->fields(),
                    $this->Personne->Foyer->fields(),
                    $this->Personne->Foyer->Dossier->fields(),
                    $this->Personne->Foyer->Adressefoyer->fields(),
                    $this->Personne->Foyer->Adressefoyer->Adresse->fields(),
                    $this->Decisionpropopdo->fields(),
                    $this->Decisionpropopdo->Decisionpdo->fields(),
                    $this->Structurereferente->fields()
                ),
                'joins' => array(
                    $this->join( 'Personne', array( 'type' => 'INNER' ) ),
                    $this->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
                    $this->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
                    $this->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
                    $this->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
                    $this->join( 'Decisionpropopdo', array( 'type' => 'LEFT OUTER' ) ),
                    $this->Decisionpropopdo->join( 'Decisionpdo', array( 'type' => 'LEFT OUTER' ) ),
                    $this->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
                ),
                'conditions' => array(
                    'Propopdo.id' => $propopdo_id,
                    'OR' => array(
                        'Adressefoyer.id IS NULL',
                        'Adressefoyer.id IN ( '.$this->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
                    )
                ),
                'contain' => false,
                'recursive' => -1,
            );

			$propopdo = $this->find( 'first', $queryData );

			// Lecture des données de l'utilisateur
			$user = $this->User->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => $user_id
					),
					'contain' => false
				)
			);
			$propopdo = Hash::merge( $propopdo, $user );

			if( Configure::read( 'Cg.departement' ) == 93 ) {
				// Lecture des données de la structure référente liée à la dernière orientation
				$sqDerniereOrientstruct = $this->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere( 'Orientstruct.personne_id' );
				$query = array(
					'fields' => $this->Personne->Orientstruct->Structurereferente->fields(),
					'contain' => false,
					'joins' => array(
						$this->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) )
					),
					'conditions' => array(
						'Orientstruct.personne_id' => $propopdo['Propopdo']['personne_id'],
						"Orientstruct.id IN ( {$sqDerniereOrientstruct} )",
					),
				);

				$structureactuelle = array_words_replace(
					$this->Personne->Orientstruct->find( 'first', $query ),
					array( 'Structurereferente' => 'Structureactuelle' )
				);

				if( empty( $structureactuelle ) ) {
					$structureactuelle = array(
						'Structureactuelle' => Set::normalize(
							array_keys( $this->Personne->Orientstruct->Structurereferente->schema() )
						)
					);
				}

				$propopdo = Hash::merge( $propopdo, $structureactuelle );
			}

			// Choix du modèle de document et génération du courrier
			$modeleodt = Hash::get( $propopdo, 'Decisionpdo.modeleodt' );

			return $this->ged(
				$propopdo,
				"PDO/{$modeleodt}.odt",
				false,
				$options
			);
		}

		/**
		 * Surcharge de la méthode enums() pour inclure les valeurs et traductions
		 * se trouvant encore dansle modèle Option.
		 *
		 * @return array
		 */
		public function enums() {
			$result = parent::enums();

			$result = Hash::merge(
				$result,
				array(
					$this->alias => array(
						'categoriegeneral' => ClassRegistry::init('Contratinsertion')->enum('sect_acti_emp'),
						'categoriedetail' => ClassRegistry::init('Contratinsertion')->enum('emp_occupe'),
						'orgpayeur' => array( 'CAF' => 'CAF', 'MSA' => 'MSA' )
					)
				)
			);

			return $result;
		}
	}
?>