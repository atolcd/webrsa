<?php
	/**
	 * Code source de la classe Structurereferente.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Structurereferente s'occupe de la gestion des structures référentes.
	 *
	 * @package app.Model
	 */
	class Structurereferente extends AppModel
	{
		public $name = 'Structurereferente';

		public $displayField = 'lib_struc';

		public $order = array( 'lib_struc ASC' );

		public $actsAs = array(
			'Formattable' => array(
				'phone' => array( 'numtel', 'numfax' )
			),
			'Pgsqlcake.PgsqlAutovalidate',
			'Validation.ExtraValidationRules',
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Option',
			'WebrsaStructurereferente'
		);

		public $validate = array(
			'numtel' => array(
				'phoneFr' => array(
					'rule' => array( 'phoneFr' ),
					'allowEmpty' => true,
				)
			),
			'numfax' => array(
				'phoneFr' => array(
					'rule' => array( 'phoneFr' ),
					'allowEmpty' => true,
				)
			),
		);

		public $belongsTo = array(
			'Typeorient' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'typeorient_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasOne = array(
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'structurereferente_id',
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
				'foreignKey' => 'structurereferente_id',
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
				'foreignKey' => 'structurereferente_id',
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
				'foreignKey' => 'structurereferente_id',
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
			'Permanence' => array(
				'className' => 'Permanence',
				'foreignKey' => 'structurereferente_id',
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
			'PersonneReferent' => array(
				'className' => 'PersonneReferent',
				'foreignKey' => 'structurereferente_id',
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
				'foreignKey' => 'structurereferente_id',
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
				'foreignKey' => 'structurereferente_id',
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
			'Referent' => array(
				'className' => 'Referent',
				'foreignKey' => 'structurereferente_id',
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
				'foreignKey' => 'structurereferente_id',
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
			'Regressionorientationep58' => array(
				'className' => 'Regressionorientationep58',
				'foreignKey' => 'structurereferente_id',
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
			'Decisionpropoorientsocialecov58' => array(
				'className' => 'Decisionpropoorientsocialecov58',
				'foreignKey' => 'structurereferente_id',
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
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'structurereferente_id',
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
			'Decisionnonorientationproep58' => array(
				'className' => 'Decisionnonorientationproep58',
				'foreignKey' => 'structurereferente_id',
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
			'Decisionnonorientationproep93' => array(
				'className' => 'Decisionnonorientationproep93',
				'foreignKey' => 'structurereferente_id',
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
			'Decisionnonorientationprocov58' => array(
				'className' => 'Decisionnonorientationprocov58',
				'foreignKey' => 'structurereferente_id',
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
			'Regressionorientationcov58' => array(
				'className' => 'Regressionorientationcov58',
				'foreignKey' => 'structurereferente_id',
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
			'Decisionregressionorientationcov58' => array(
				'className' => 'Decisionregressionorientationcov58',
				'foreignKey' => 'structurereferente_id',
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
			'Tableausuivipdv93' => array(
				'className' => 'Tableausuivipdv93',
				'foreignKey' => 'structurereferente_id',
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
			'Communautesr' => array(
				'className' => 'Communautesr',
				'joinTable' => 'communautessrs_structuresreferentes',
				'foreignKey' => 'structurereferente_id',
				'associationForeignKey' => 'communautesr_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'CommunautesrStructurereferente'
			),
			'Tableausuivipdv93' => array(
				'className' => 'Tableausuivipdv93',
				'joinTable' => 'structuresreferentes_tableauxsuivispdvs93',
				'foreignKey' => 'structurereferente_id',
				'associationForeignKey' => 'tableausuivipdv93_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'StructurereferenteTableausuivipdv93'
			),
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'joinTable' => 'structuresreferentes_zonesgeographiques',
				'foreignKey' => 'structurereferente_id',
				'associationForeignKey' => 'zonegeographique_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'StructurereferenteZonegeographique'
			)
		);

		/**
		 *
		 * @return array
		 */
		public function list1Options() {
			$cacheKey = 'structurereferente_list1_options';
			$results = Cache::read( $cacheKey );

			if( $results === false ) {
				$tmp = $this->find(
					'all',
					array(
						'conditions' => array( 'Structurereferente.actif' => 'O' ),
						'fields' => array(
							'Structurereferente.id',
							'Structurereferente.typeorient_id',
							'Structurereferente.lib_struc'
						),
						'order'  => array( 'Structurereferente.lib_struc ASC' ),
						'recursive' => -1
					)
				);

				$results = array();
				foreach( $tmp as $key => $value ) {
					$results[$value['Structurereferente']['typeorient_id'].'_'.$value['Structurereferente']['id']] = $value['Structurereferente']['lib_struc'];
				}

				Cache::write( $cacheKey, $results );
				ModelCache::write( $cacheKey, array( 'Structurereferente', 'Typeorient' ) );
			}

			return $results;
		}

		/**
		 * Récupère la liste des structures référentes groupées par type d'orientation.
		 * Cette liste est mise en cache et on se sert de la classe ModelCache
		 * pour savoir quelles clés de cache supprimer lorsque les données de ce
		 * modèle changent.
		 *
		 * @return array
		 */
		public function listOptions() {
			$cacheKey = 'structurereferente_list_options';
			$results = Cache::read( $cacheKey );

			if( $results === false ) {
				$results = $this->find(
					'list',
					array(
						'fields' => array(
							'Structurereferente.id',
							'Structurereferente.lib_struc',
							'Typeorient.lib_type_orient'
						),
						'recursive' => -1,
						'joins' => array(
							$this->join( 'Typeorient', array( 'type' => 'INNER' ) )
						),
						'order' => array(
							'Typeorient.lib_type_orient ASC',
							'Structurereferente.lib_struc'
						),
						'conditions' => array(
							'Structurereferente.actif' => 'O'
						)
					)
				);
				Cache::write( $cacheKey, $results );
				ModelCache::write( $cacheKey, array( 'Structurereferente', 'Typeorient' ) );
			}
			return $results;
		}

		/**
		*
		*/

		public function listePourApre() {
			///Récupération de la liste des référents liés à l'APRE
			$structsapre = $this->Structurereferente->find( 'list', array( 'conditions' => array( 'Structurereferente.apre' => 'O' ) ) );
			$this->set( 'structsapre', $structsapre );
		}

		/**
		*   Retourne la liste des structures référentes filtrée selon un type donné
		* @param array $types ( array( 'apre' => true, 'contratengagement' => true ) )
		* par défaut, toutes les clés sont considérées commen étant à false
		*/

		public function listeParType( $types ) {
			$conditions = array();

			foreach( array( 'apre', 'contratengagement', 'orientation', 'pdo', 'cui' ) as $type ) {
				$bool = Set::classicExtract( $types, $type );
				if( !empty( $bool ) ) {
					$conditions[] = "Structurereferente.{$type} = 'O'";
				}
			}

			return $this->find( 'list', array( 'conditions' => $conditions, 'recursive' => -1 ) );
		}

		/**
		 * Retourne les enregistrements pour lesquels une erreur de paramétrage
		 * a été détectée.
		 * Il s'agit des structures pour lesquelles on ne sait pas si elles gèrent
		 * l'APRE ni le CER.
		 *
		 * @return array
		 */
		public function storedDataErrors() {
			return $this->find(
				'all',
				array(
					'fields' => array(
						'Structurereferente.id',
						'Structurereferente.lib_struc',
						'Structurereferente.apre',
						'Structurereferente.contratengagement',
						'Structurereferente.cui'
					),
					'recursive' => -1,
					'conditions' => array(
						'OR' => array(
							'Structurereferente.apre' => NULL,
							'Structurereferente.contratengagement' => NULL,
							'Structurereferente.cui' => NULL
						)
					),
					'contain' => false
				)
			);
		}

		/**
		 * Suppression et regénération du cache.
		 *
		 * @return boolean
		 */
		protected function _regenerateCache() {
			$this->_clearModelCache();

			// Regénération des éléments du cache.
			$success = true;

			if( $this->alias == 'Structurereferente' ) {
				$success = ( $this->listOptions() !== false )
					&& ( $this->list1Options() !== false );
			}

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
		 * Retourne la clé de session pour une méthode et un querydata donnés.
		 *
		 * @param string $method
		 * @param array $query
		 * @return string
		 */
		public function sessionKey( $method, array $query ) {
			$queryHash = sha1( serialize( $query ) );
			$sessionKey = "Auth.{$this->name}.{$method}.{$queryHash}";
			return $sessionKey;
		}

		/**
		 * @deprecated since 3.1.0
		 * @see InsertionsBeneficiairesComponent::structuresreferentes()
		 *
		 * @param array $options
		 * @return array
		 */
		public function structuresreferentes( $options = array() ){
			App::uses('SessionComponent', 'Controller/Component');
			App::uses('ComponentCollection', 'Controller/Component');
			$Session = new SessionComponent(new ComponentCollection());

			$options = Set::merge(
				array(
					'conditions' => array(),
					'optgroup' => false,
					'ids' => false,
                    'list' => false
				),
				$options
			);

			$conditions = array(
				'Typeorient.actif' => 'O',
				'Structurereferente.actif' => 'O',
			);

			$conditions = Set::merge( $conditions, $options['conditions'] );

			if( ( Configure::read( 'Cg.departement' ) == 93 ) && $Session->read( 'Auth.User.filtre_zone_geo' ) !== false ) {
				$zonesgeographiques_ids = array_keys( (array)$Session->read( 'Auth.Zonegeographique' ) );

				$sqStructurereferente = $this->StructurereferenteZonegeographique->sq(
					array(
						'alias' => 'structuresreferentes_zonesgeographiques',
						'fields' => array( 'structuresreferentes_zonesgeographiques.structurereferente_id' ),
						'conditions' => array(
							'structuresreferentes_zonesgeographiques.zonegeographique_id' => $zonesgeographiques_ids
						),
						'contain' => false
					)
				);
				$conditions[] = "Structurereferente.id IN ( {$sqStructurereferente} )";
			}
			else if( ( Configure::read( 'Cg.departement' ) == 66 ) && $Session->read( 'Auth.User.type' ) === 'externe_ci' ) {
				$structurereferente_id = $Session->read( 'Auth.User.structurereferente_id' );
				$conditions['Structurereferente.id'] = $structurereferente_id;
			}

			$query = array(
				'fields' => array_merge(
					$this->Typeorient->fields(),
					$this->fields()
				),
				'joins' => array(
					$this->join( 'Typeorient', array( 'type' => 'INNER' ) )
				),
				'conditions' => $conditions,
				'contain' => false,
				'order' => array(
					'Typeorient.lib_type_orient ASC',
					'Structurereferente.lib_struc ASC',
				)
			);

			$sessionKey = $this->sessionKey( __FUNCTION__, $query );
			$results = $Session->read( $sessionKey );

			if( $results === null ) {
				$results = array();

				$tmps = $this->find( 'all', $query );

				if( !empty( $tmps ) ) {
					foreach( $tmps as $tmp ) {
						// Cas optgroup, structurereferente_id
						if( !isset( $results['optgroup'][$tmp['Typeorient']['lib_type_orient']] ) ) {
							$results['optgroup'][$tmp['Typeorient']['lib_type_orient']] = array();
						}
						$results['optgroup'][$tmp['Typeorient']['lib_type_orient']][$tmp['Structurereferente']['id']] = $tmp['Structurereferente']['lib_struc'];

						// Cas seulement les ids
						$results['ids'][] = $tmp['Structurereferente']['id'];

						// Cas typeorient_id_structurereferente_id
						$results['normal']["{$tmp['Structurereferente']['typeorient_id']}_{$tmp['Structurereferente']['id']}"] = $tmp['Structurereferente']['lib_struc'];

                        // Cas du find list
						$results['list'][$tmp['Structurereferente']['id']] = $tmp['Structurereferente']['lib_struc'];
					}
				}

				// Pour les listes simples, tri par valeur en gardant l'association avec la clé
				asort( $results['normal'] );
				asort( $results['list'] );

				$Session->write( $sessionKey, $results );
			}

			if( !empty( $results ) ) {
				// Cas optgroup, structurereferente_id
				if( $options['optgroup'] ) {
					$results = $results['optgroup'];
				}
				// Cas où l'on ne veut que les ids des structures référentes
				else if( $options['ids'] ) {
					$results = $results['ids'];
				}
				// Cas où l'on veut les libellés des structures référentes
				else if( $options['list'] ) {
					$results = $results['list'];
				}
				// Cas typeorient_id_structurereferente_id
				else {
					$results = $results['normal'];
				}
			}

			return $results;
		}

		/**
		 * Surcharge de la méthode enums pour ajouter le type de voie ainsi que
		 * des traductions distinctes pour le CG 58.
		 *
		 * @return array
		 */
		public function enums() {
			$departement = (int)Configure::read( 'Cg.departement' );
			$results = parent::enums();

			$results[$this->alias]['type_voie'] = $this->Option->typevoie();

			if( 58 === $departement ) {
				$results[$this->alias]['typestructure'] = array_merge(
					$results[$this->alias]['typestructure'],
					array(
						'oa' => 'Structure liée à un PPAE',
						'msp' => 'Structure débouchant sur CER pro'
					)
				);
			}

			return $results;
		}
	}
?>