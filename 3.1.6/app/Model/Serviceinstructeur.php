<?php
	/**
	 * Code source de la classe Serviceinstructeur.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

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

		public $recursive = -1;

		public $actsAs = array(
			'Autovalidate2',
			'Formattable'
		);

		public $validate = array(
			'lib_service' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
				array(
					'rule' => 'isUnique',
					'message' => 'Valeur déjà utilisée'
				),
			),
			'type_voie' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'code_insee' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
					// FIXME: format
				)
			),
			'numdepins' => array(
				array(
					'rule' => 'alphaNumeric',
					'message' => 'Veuillez n\'utiliser que des lettres et des chiffres'
				),
				array(
					'rule' => array( 'between', 3, 3 ),
					'message' => 'Le n° de département est composé de 3 caractères'
				),
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'typeserins' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'numcomins' => array(
				array(
					'rule' => 'alphaNumeric',
					'message' => 'Veuillez n\'utiliser que des lettres et des chiffres'
				),
				array(
					'rule' => array( 'between', 3, 3 ),
					'message' => 'Le n° de commune est composé de 3 caractères'
				),
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'numagrins' => array(
				array(
					'rule' => 'numeric',
					'message' => 'Veuillez n\'utiliser que des lettres et des chiffres'
				),
				array(
					'rule' => array( 'between', 1, 2 ),
					'message' => 'Le n° d\'agrément est composé de 2 caractères'
				),
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'sqrecherche' => array(
				array(
					'rule' => 'validateSqrecherche',
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
		*
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
		*
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
		*
		*/

		protected function _queryDataError( &$model, $querydata ) {
			$querydata['limit'] = 1;
			$sql = $model->sq( $querydata );
			$ds = $model->getDataSource( $model->useDbConfig );

			$result = false;
			try {
				$result = @$model->query( "EXPLAIN $sql" );
			} catch( Exception $e ) {
			}

			if( $result === false ) {
				return $sql;
			}
			else {
				return false;
			}
		}

		/**
		*
		*/

		// 			$this->Serviceinstructeur->sqrechercheErrors( 'foo' );
		// FIXME: criterespdos/index, criterespdos/nouvelles, criterespdos/exportcsv ($this->Criterepdo->listeDossierPDO, Criterepdo->search)
		public function sqrechercheErrors( $condition ) {
			$errors = array();

			if( Configure::read( 'Recherche.qdFilters.Serviceinstructeur' ) ) {
				$models = array(
					'Dossier' => 'Dossier',
					'Critere' => 'Orientstruct',
					'Cohorteci' => 'Contratinsertion',
					'Criterecui' => 'Cui',
					'Cohorteindu' => 'Dossier',
					'Critererdv' => 'Rendezvous',
					'Criterepdo' => 'Propopdo',
				);

				foreach( $models as $modelSearch => $modelName ) {
					$search = ClassRegistry::init( $modelSearch );
					$model = ClassRegistry::init( $modelName );

					$querydata = @$search->search( array(), array(), array(), array(), array() );

					if( !empty( $condition ) ) {
						$querydata['conditions'][] = $condition;
					}

					$model->forceVirtualFields = true;
					$querydata = $model->beforeFind( $querydata );

					$error = $this->_queryDataError( $model, $querydata );

					if( !empty( $error ) ) {
						$ds = $model->getDataSource( $model->useDbConfig );
						$errors[$model->alias] = array(
							'sql' => $error,
							'error' => $ds->lastError()
						);
					}
				}
			}
			return $errors;
		}

		/**
		*
		*/

		public function validateSqrecherche( $check ) {
			if( !is_array( $check ) ) {
				return false;
			}

			// TODO: meilleure validation ?
			$result = true;
			foreach( Set::normalize( $check ) as $key => $condition ) {
				$errors = $this->sqrechercheErrors( $condition );
				$result = empty( $errors ) && $result;
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
			return $this->find(
				'all',
				array(
					'fields' => array(
						'Serviceinstructeur.id',
						'Serviceinstructeur.lib_service',
						'Serviceinstructeur.numdepins',
						'Serviceinstructeur.typeserins',
						'Serviceinstructeur.numcomins',
						'Serviceinstructeur.numagrins',
					),
					'conditions' => array(
						'OR' => array(
							'Serviceinstructeur.lib_service IS NULL',
							'TRIM(Serviceinstructeur.lib_service)' => null,
							'Serviceinstructeur.numdepins IS NULL',
							'TRIM(Serviceinstructeur.numdepins)' => null,
							'Serviceinstructeur.typeserins IS NULL',
							'TRIM(Serviceinstructeur.typeserins)' => null,
							'Serviceinstructeur.numcomins IS NULL',
							'TRIM(Serviceinstructeur.numcomins)' => null,
							'Serviceinstructeur.numagrins IS NULL'
						)
					),
					'contain' => false,
				)
			);
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
	}
?>