<?php
	/**
	 * Code source de la classe Serviceinstructeur.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Serviceinstructeur s'occupe de la gestion des services instructeurs.
	 *
	 * @package app.Model
	 */
	class Serviceinstructeur extends AppModel
	{
		public $name = 'Serviceinstructeur';

		public $displayField = 'lib_service';

		public $order = 'Serviceinstructeur.lib_service ASC';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Allocataire', 'Option' );

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'type_voie' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'code_insee' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'numdepins' => array(
				'alphaNumeric' => array(
					'rule' => array( 'alphaNumeric' ),
					'message' => 'Veuillez n\'utiliser que des lettres et des chiffres'
				),
				'between' => array(
					'rule' => array( 'between', 3, 3 ),
					'message' => 'Le n° de département est composé de 3 caractères'
				),
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'typeserins' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'numcomins' => array(
				'alphaNumeric' => array(
					'rule' => array( 'alphaNumeric' ),
					'message' => 'Veuillez n\'utiliser que des lettres et des chiffres'
				),
				'between' => array(
					'rule' => array( 'between', 3, 3 ),
					'message' => 'Le n° de commune est composé de 3 caractères'
				),
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'numagrins' => array(
				'between' => array(
					'rule' => array( 'between', 1, 2 ),
					'message' => 'Le n° d\'agrément est composé de 2 caractères'
				),
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'sqrecherche' => array(
				'validateSqrecherche' => array(
					'rule' => array( 'validateSqrecherche' ),
					'message' => 'Erreur SQL'
				)
			),
            'email' => array(
				'email' => array(
					'rule' => array( 'email' ),
                    'allowEmpty' => true,
					'message' => 'Veuillez entrer une adresse mail valide'
				)
			)
		);

		public $hasMany = array(
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'serviceinstructeur_id',
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
			'Bilanparcours66' => array(
				'className' => 'Bilanparcours66',
				'foreignKey' => 'serviceinstructeur_id',
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
				'foreignKey' => 'serviceinstructeur_id',
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
				'foreignKey' => 'serviceinstructeur_id',
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
			'Traitementpcg66' => array(
				'className' => 'Traitementpcg66',
				'foreignKey' => 'serviceinstructeur_id',
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
			'Suiviinstruction' => array(
				'className' => 'Suiviinstruction',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Suiviinstruction.numdepins = Serviceinstructeur.numdepins',
					'Suiviinstruction.typeserins = Serviceinstructeur.typeserins',
					'Suiviinstruction.numcomins = Serviceinstructeur.numcomins',
					'Suiviinstruction.numagrins = Serviceinstructeur.numagrins'
				),
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
			'Orientstruct' => array(
				'className' => 'Orientstruct',
				'joinTable' => 'orientsstructs_servicesinstructeurs',
				'foreignKey' => 'serviceinstructeur_id',
				'associationForeignKey' => 'orientstruct_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'OrientstructServiceinstructeur'
			)
		);

		/**
		 * @deprecated since 3.2.0
		 *
		 * @var array
		 */
		public $_types = array(
			'list' => array(
				'fields' => array(
					'"Serviceinstructeur"."id"',
					'"Serviceinstructeur"."lib_service"',
					'"Serviceinstructeur"."num_rue"',
					'"Serviceinstructeur"."nom_rue"',
					'"Serviceinstructeur"."complement_adr"',
					'"Serviceinstructeur"."code_insee"',
					'"Serviceinstructeur"."code_postal"',
					'"Serviceinstructeur"."ville"',
                    '"Serviceinstructeur"."email"',
					'"Serviceinstructeur"."numdepins"',
					'"Serviceinstructeur"."typeserins"',
					'"Serviceinstructeur"."numcomins"',
					'"Serviceinstructeur"."numagrins"',
					'"Serviceinstructeur"."type_voie"',
					'( "Serviceinstructeur"."sqrecherche" IS NOT NULL ) AS "Serviceinstructeur__sqrecherche"',
					'COUNT("User"."id") AS "Serviceinstructeur__nbUsers"',
				),
				'recursive' => -1,
				'joins' => array(
					array(
						'table'      => 'users',
						'alias'      => 'User',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Serviceinstructeur.id = User.serviceinstructeur_id' )
					),
				),
				'group' => array(
					'"Serviceinstructeur"."id"',
					'"Serviceinstructeur"."lib_service"',
					'"Serviceinstructeur"."num_rue"',
					'"Serviceinstructeur"."nom_rue"',
					'"Serviceinstructeur"."complement_adr"',
					'"Serviceinstructeur"."code_insee"',
					'"Serviceinstructeur"."code_postal"',
					'"Serviceinstructeur"."ville"',
                    '"Serviceinstructeur"."email"',
					'"Serviceinstructeur"."numdepins"',
					'"Serviceinstructeur"."typeserins"',
					'"Serviceinstructeur"."numcomins"',
					'"Serviceinstructeur"."numagrins"',
					'"Serviceinstructeur"."type_voie"',
					'( "Serviceinstructeur"."sqrecherche" IS NOT NULL )',
				),
				'order' => 'Serviceinstructeur.lib_service ASC',
			)
		);

		/**
		 * Retourne une liste de services instructeurs pour peupler des listes déroulantes.
		 * Le résultat est mis en cache.
		 *
		 * @param array $conditions Des conditions pour filtrer les enregistrements retournés.
		 * @return array
		 */
		public function listOptions( $conditions = array() ) {
			$cacheKey = $this->useDbConfig.'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.md5( serialize( $conditions ) );
			$results = Cache::read( $cacheKey );

			if( $results === false ) {
				$results = $this->find(
					'list',
					array (
						'fields' => array(
							'Serviceinstructeur.id',
							'Serviceinstructeur.lib_service'
						),
						'order'  => array( 'Serviceinstructeur.lib_service ASC' ),
						'conditions' => $conditions
					)
				);

				Cache::write( $cacheKey, $results );
				ModelCache::write( $cacheKey, array( 'Serviceinstructeur' ) );
			}

			return $results;
		}

		/**
		 * @deprecated since 3.2.0
		 *
		 * @param string $type
		 * @param array $params
		 * @return array
		 */
		public function prepare( $type, $params = array() ) {
			$types = array_keys( $this->_types );
			if( !in_array( $type, $types ) ) {
				trigger_error( 'Invalid parameter "'.$type.'" for '.$this->name.'::prepare()', E_USER_WARNING );
			}
			else {
				$querydata = $this->_types[$type];
				$querydata = Set::merge( $querydata, $params );

				return $querydata;
			}
		}

		/**
		 * Vérifie, pour chacun des enregistrements comportant une valeur pour
		 * sqrecherche, que la requête fonctionne avec le query du modèle
		 * Allocataire.
		 *
		 * Renvoie un tableau contenant les enregistrements en erreur.
		 *
		 * @return array
		 */
		public function sqRechercheErrors() {
			$results = array();

			if( Configure::read( 'Recherche.qdFilters.Serviceinstructeur' ) ) {
				$records = $this->find(
					'all',
					array(
						'fields' => array(
							"{$this->primaryKey} AS \"{$this->alias}__id\"",
							"{$this->displayField} AS \"{$this->alias}__name\"",
							"sqrecherche AS \"{$this->alias}__sqrecherche\""
						),
						'contain' => false,
						'conditions' => array( "{$this->alias}.sqrecherche IS NOT NULL" )
					)
				);

				foreach( $records as $record ) {
					$check = $this->testSqRechercheConditions( $record[$this->alias]['sqrecherche'] );

					if( true !== $check['success'] ) {
						$record[$this->alias]['message'] = $check['message'];
						$results[] = $record;
					}
				}
			}

			return false === empty( $results ) ? array( $this->alias => $results ) : array();
		}

		/**
		 * Vérification de conditions supplémentaires à utiliser avec le modèle
		 * Allocataire.
		 *
		 * @param string|array $conditions
		 * @return array
		 */
		public function testSqRechercheConditions( $conditions ) {
			$Dbo = $this->getDataSource();
			$query = $this->Allocataire->searchQuery();
			$query['conditions'][] = $conditions;
			$this->Allocataire->Personne->forceVirtualFields = true;
			$query = $this->Allocataire->Personne->beforeFind( $query );
			$sql = $this->Allocataire->Personne->sq( $query );

			return $Dbo->checkPostgresSqlSyntax( $sql );
		}

		/**
		 * Validation des conditions supplémentaires éventuelles à utiliser avec
		 * le modèle Allocataire.
		 *
		 * @param mixed $check
		 * @return boolean
		 */
		public function validateSqrecherche( $check ) {
			if( !is_array( $check ) ) {
				return false;
			}

			$result = true;
			foreach( Hash::normalize( $check ) as $key => $condition ) {
				$tmp = $this->testSqRechercheConditions( $condition );
				$result = true === $tmp['success'] && $result;
			}

			return $result;
		}

		/**
		 * Retourne les enregistrements pour lesquels une erreur de paramétrage
		 * a été détectée.
		 * Il s'agit des services instructeurs pour lesquels on ne connaît pas
		 * le nom du service, ou une des colonnes permettant de faire la jointure
		 * avec les dossiers.
		 *
		 * @return array
		 */
		public function storedDataErrors() {
			$conditionsErrors = array(
				'identification' => array(
					'OR' => array(
						'Serviceinstructeur.lib_service IS NULL',
						'TRIM(BOTH \' \' FROM "Serviceinstructeur"."lib_service")' => ''
					)
				),
				'rapprochement' => array(
					'OR' => array(
						'Serviceinstructeur.numdepins IS NULL',
						'TRIM(BOTH \' \' FROM "Serviceinstructeur"."numdepins")' => '',
						'Serviceinstructeur.typeserins IS NULL',
						'TRIM(BOTH \' \' FROM "Serviceinstructeur"."typeserins")' => '',
						'Serviceinstructeur.numcomins IS NULL',
						'TRIM(BOTH \' \' FROM "Serviceinstructeur"."numcomins")' => '',
						'Serviceinstructeur.numagrins IS NULL'
					)
				)
			);

			$query = array(
				'fields' => array(
					'Serviceinstructeur.id',
					'Serviceinstructeur.lib_service',
					'Serviceinstructeur.numdepins',
					'Serviceinstructeur.typeserins',
					'Serviceinstructeur.numcomins',
					'Serviceinstructeur.numagrins',
				),
				'conditions' => array(),
				'contain' => false,
			);

			// Ajout des champs et des conditions concernant les erreurs
			$Dbo = $this->getDataSource();
			foreach( $conditionsErrors as $errorName => $errorConditions ) {
				$conditions = $Dbo->conditions( $errorConditions, true, false );
				$query['fields'][] = "( {$conditions} ) AS \"{$this->alias}__error_{$errorName}\"";
			}
			$query['conditions']['OR'] = array_values( $conditionsErrors );

			return $this->find( 'all', $query );
		}

		/**
		 * Suppression et regénération du cache.
		 *
		 * @return boolean
		 */
		protected function _regenerateCache() {
			// Suppression des éléments du cache.
			$this->_clearModelCache();

			// Regénération des éléments du cache.
			$success = ( $this->listOptions() !== false );

			return $success;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les fonctions vides.
		 */
		public function prechargement() {
			$success = $this->_regenerateCache();
			return $success;
		}

		/**
		 * Retourne la liste des énumérations du modèle.
		 *
		 * @return array
		 */
		public function enums() {
			$result = parent::enums();

			if(false === isset($result[$this->alias]['typeserins'])) {
				$result[$this->alias]['typeserins'] = $this->Option->typeserins();
			}
			$result[$this->alias]['type_voie'] = $this->Option->libtypevoie();

			return $result;
		}
	}
?>