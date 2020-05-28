<?php
	/**
	 * Code source de la classe Canton.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Canton ...
	 *
	 * @package app.Model
	 */
	class Canton extends AppModel
	{
		public $name = 'Canton';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'canton';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'foreignKey' => 'zonegeographique_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
            'Adresse' => array(
				'className' => 'Adresse',
				'joinTable' => 'adresses_cantons',
				'foreignKey' => 'canton_id',
				'associationForeignKey' => 'adresse_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'AdresseCanton'
			),
			'Sitecov58' => array(
				'className' => 'Sitecov58',
				'joinTable' => 'cantons_sitescovs58',
				'foreignKey' => 'canton_id',
				'associationForeignKey' => 'sitecov58_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'CantonSitecov58'
			),
		);

		public $validate = array(
			'canton' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'nomcom' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'codepos' => array(
				'between' => array(
					'rule' => array( 'between', 5, 5 ),
					'message' => 'Le code postal se compose de 5 caractères',
					'allowEmpty' => true
				)
			),
			'numcom' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				),
				'between' => array(
					'rule' => array( 'between', 5, 5 ),
					'message' => 'Le code INSEE se compose de 5 caractères'
				)
			)
		);

		/**
		*	FIXME: docs
		*/
		public function selectList( $filtre_zone_geo = false, $zonesgeographiques = array() ) {
			$conditions = array( 'Canton.canton IS NOT NULL', 'Canton.canton <> \'\'' );

			if( $filtre_zone_geo ) {
				$conditions['Canton.zonegeographique_id'] = $zonesgeographiques;
			}

			$queryData = array(
				'fields' => array( 'DISTINCT Canton.canton' ),
				'conditions' => $conditions,
				'recursive' => -1,
				'order' => array( 'Canton.canton ASC' )
			);

			$results = parent::find( 'all', $queryData );

			if( !empty( $results ) ) {
				$cantons = Set::extract( $results, '/Canton/canton' );
				return array_combine( $cantons, $cantons );
			}
			else {
				return $results;
			}
		}

		/**
		*	FIXME: docs
		*/
		public function queryConditions( $canton ) {
			$cantons = $this->find(
				'all',
				array(
					'conditions' => array(
					'Canton.canton' => $canton
					)
				)
			);

			$_conditions = $this->constructionConditionAdresses ($cantons);

			return array( 'or' => $_conditions );
		}

		/**
		*	FIXME: docs
		*/
		public function constructionConditionAdresses ( $cantons ) {
			$_conditions = array();

			foreach( $cantons as $canton ) {
				$_condition = array();

				// INFO: les couples numcom / codepos de la table adresses ne correspondent
				// pas toujours aux couples de la table cantons.

				if( !empty( $canton['Canton']['numcom'] ) && !empty( $canton['Canton']['codepos'] ) ) {
					$_condition['OR'] = array(
						 'Adresse.numcom' => $canton['Canton']['numcom'],
						 'Adresse.codepos' => $canton['Canton']['codepos']
					);
				}
				else {
					if( !empty( $canton['Canton']['numcom'] ) ) {
						$_condition['Adresse.numcom'] = $canton['Canton']['numcom'];
					}
					if( !empty( $canton['Canton']['codepos'] ) ) {
						$_condition['Adresse.codepos'] = $canton['Canton']['codepos'];
					}
				}
				if( !empty( $canton['Canton']['nomcom'] ) ) {
					$_condition['Adresse.nomcom ILIKE'] = $canton['Canton']['nomcom'];
				}
				if( !empty( $canton['Canton']['libtypevoie'] ) ) {
					$_condition['Adresse.libtypevoie ILIKE'] = $canton['Canton']['libtypevoie'];
				}
				if( !empty( $canton['Canton']['nomvoie'] ) ) {
                    $_condition['Adresse.nomvoie ILIKE'] = '%'.$canton['Canton']['nomvoie'].'%';
//					$_condition['Adresse.nomvoie ILIKE'] = $canton['Canton']['nomvoie'];
				}

				$_conditions[] = $_condition;
			}

			return $_conditions;
		}

		/**
		*	FIXME: docs
		*/
		public function queryConditionsByZonesgeographiques( $zonesgeographiques ) {
			$cantons = array();
			if( !empty( $zonesgeographiques ) ) {
				$cantons = $this->find(
					'all',
					array(
						'conditions' => array(
							'Canton.zonegeographique_id' => $zonesgeographiques
						),
						'contain' => false
					)
				);
			}
			$_conditions = array();
			foreach( $cantons as $canton ) {
				$_condition = array();
				// INFO: les couples numcom / codepos de la table adresses ne correspondent
				// pas toujours aux couples de la table cantons.
				if( !empty( $canton['Canton']['numcom'] ) && !empty( $canton['Canton']['codepos'] ) ) {
					$_condition['OR'] = array(
						 'Adresse.numcom' => $canton['Canton']['numcom'],
						 'Adresse.codepos' => $canton['Canton']['codepos']
					);
				}
				else {
					if( !empty( $canton['Canton']['numcom'] ) ) {
						$_condition['Adresse.numcom'] = $canton['Canton']['numcom'];
					}
					if( !empty( $canton['Canton']['codepos'] ) ) {
						$_condition['Adresse.codepos'] = $canton['Canton']['codepos'];
					}
				}
				if( !empty( $canton['Canton']['nomcom'] ) ) {
					$_condition['Adresse.nomcom ILIKE'] = $canton['Canton']['nomcom'];
				}
				if( !empty( $canton['Canton']['libtypevoie'] ) ) {
					$_condition['Adresse.libtypevoie ILIKE'] = $canton['Canton']['libtypevoie'];
				}
				if( !empty( $canton['Canton']['nomvoie'] ) ) {
					$_condition['Adresse.nomvoie ILIKE'] = $canton['Canton']['nomvoie'];
				}
				$_conditions[] = $_condition;
			}
			return array( 'OR' => $_conditions );
		}

		/**
		 *
		 */
		public function joinAdresse( $type = 'LEFT OUTER' ) {
			$dbo = $this->getDataSource( $this->useDbConfig );
			$fullTableName = $dbo->fullTableName( $this, true, false );

			$conditions = array(
				'OR' => array(
					// 156-161
					array(
						'OR' => array(
							'OR' => array(
								"Canton.numcom IS NULL",
								"TRIM( BOTH ' ' FROM Canton.numcom ) = ''",
								"Canton.codepos IS NULL",
								"TRIM( BOTH ' ' FROM Canton.codepos ) = ''",
							),
							array(
								"Canton.numcom = Adresse.numcom",
								"Canton.codepos = Adresse.codepos",
							)
						),
					),
					array(
						array(
							'OR' => array(
								array(
									"Canton.numcom IS NULL",
									"TRIM( BOTH ' ' FROM Canton.numcom ) = ''",
								),
								"Canton.numcom = Adresse.numcom",
							)
						),
						array(
							'OR' => array(
								array(
									"Canton.codepos IS NULL",
									"TRIM( BOTH ' ' FROM Canton.codepos ) = ''",
								),
								"Canton.codepos = Adresse.codepos",
							)
						),
					)
				),
				// 170/178
				array(
					'OR' => array(
						'OR' => array(
							'Canton.nomcom IS NULL',
							"TRIM( BOTH ' ' FROM Canton.nomcom ) = ''",
						),
						'Adresse.nomcom ILIKE Canton.nomcom'
					)
				),
				array(
					'OR' => array(
						'OR' => array(
							'Canton.libtypevoie IS NULL',
							"TRIM( BOTH ' ' FROM Canton.libtypevoie ) = ''",
						),
						'Adresse.libtypevoie ILIKE Canton.libtypevoie'
					)
				),
				array(
					'OR' => array(
						'OR' => array(
							'Canton.nomvoie IS NULL',
							"TRIM( BOTH ' ' FROM Canton.nomvoie ) = ''",
						),
						'Adresse.nomvoie ILIKE Canton.nomvoie'
					)
				),
				//
				array(
					'OR' => array(
						'OR' => array(
							'Canton.numvoie IS NULL',
							"TRIM( BOTH ' ' FROM Canton.numvoie ) = ''",
						),
						'Adresse.numvoie ILIKE Canton.numvoie'
					)
				),
			);

			$sq = $this->sq(
				array(
					'alias' => 'cantons',
					'fields' => array( 'cantons.id' ),
					'conditions' => array_words_replace( $conditions, array( 'Canton' => 'cantons' ) ),
					'contain' => false,
					'recursive' => -1,
					'order' => array(
						'cantons.nomvoie DESC',
						'cantons.libtypevoie DESC',
					),
					'limit' => 1
				)
			);

			$conditions[] = "Canton.id IN ( {$sq} )";

			return array(
				'table'      => $fullTableName,
				'alias'      => $this->alias,
				'type'       => $type,
				'foreignKey' => false,
				'conditions' => $conditions
			);
		}

		/**
		*	FIXME: docs
		*/

		public function beforeSave( $options = array() ) {
			$return = parent::beforeSave( $options );

			foreach( array( 'nomvoie', 'nomcom', 'canton' ) as $field ) {
				if( !empty( $this->data[$this->name][$field] ) ) {
					$this->data[$this->name][$field] = strtoupper( replace_accents( $this->data[$this->name][$field] ) );
				}
			}

			return $return;
		}

		/**
		*	Recherche des partenaires dans le paramétrage de l'application
		*
		*/
		public function search( $criteres ) {
			/// Conditions de base
			$conditions = array();

			// Critères sur une personne du foyer - nom, prénom, nom de naissance -> FIXME: seulement demandeur pour l'instant
			$filtersCantons = array();
			foreach( array( 'canton', 'nomcom', 'codepos', 'numcom' ) as $critereCanton ) {
				if( isset( $criteres['Canton'][$critereCanton] ) && !empty( $criteres['Canton'][$critereCanton] ) ) {
					$conditions[] = 'Canton.'.$critereCanton.' ILIKE \''.$this->wildcard( $criteres['Canton'][$critereCanton] ).'\'';
				}
			}

			// Critère sur la structure référente de l'utilisateur
			if( isset( $criteres['Canton']['zonegeographique_id'] ) && !empty( $criteres['Canton']['zonegeographique_id'] ) ) {
				$conditions[] = array( 'Canton.zonegeographique_id' => $criteres['Canton']['zonegeographique_id'] );
			}

			// Critère sur les noms de cantons à vide
			if( isset ($criteres['Canton']['cantonvide']) && $criteres['Canton']['cantonvide'] == 1) {
				$conditions[] = array('OR' => array(
					'Canton.canton' => '',
					'Canton.canton IS NULL'
				));
			}
			$query = array(
				'fields' => array_merge(
					$this->fields(),
					$this->Zonegeographique->fields()
				),
				'order' => array( 'Canton.canton ASC' ),
				'joins' => array(
					$this->join( 'Zonegeographique', array( 'type' => 'LEFT OUTER' ) )
				),
				'recursive' => -1,
				'conditions' => $conditions
			);

			return $query;
		}

		/**
		 * Surcharge de la méthode enums pour les valeurs du champ libtypevoie.
		 *
		 * @return array
		 */
		public function enums() {
			$enums = parent::enums();

			$Adresse = ClassRegistry::init( 'Adresse' );
			$enums[$this->alias]['libtypevoie'] = array_combine( $Adresse->libtypevoie, $Adresse->libtypevoie );

			return $enums;
		}
	}
?>