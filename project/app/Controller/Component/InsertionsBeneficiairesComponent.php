<?php
	/**
	 * Code source de la classe InsertionsBeneficiairesComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe InsertionsBeneficiairesComponent fournit des méthodes permettant
	 * d'obtenir les listes de types d'orientation, structures référentes et
	 * référents sous différentes formes, en fonction de l'utilisateur connecté.
	 *
	 * Elle fournit également une méthode permettant de compléter des options
	 * de structures référentes et de référents avec les entrées d'un enregistrement
	 * métier (afin que ces valeurs apparaissent quoi qu'il arrive lors d'une
	 * modification).
	 *
	 * @package app.Controller.Component
	 */
	class InsertionsBeneficiairesComponent extends Component
	{
		/**
		 * Type de liste "ids" retourné par les méthodes structuresreferentes et
		 * referents
		 */
		const TYPE_IDS = 'ids';

		/**
		 * Type de liste "list" retourné par les méthodes communautessrs,
		 * structuresreferentes et referents
		 */
		const TYPE_LIST = 'list';

		/**
		 * Type de liste "optgroup" retourné par les méthodes structuresreferentes
		 * et referents
		 */
		const TYPE_OPTGROUP = 'optgroup';

		/**
		 * Type de liste "links" retourné par la méthode communautessrs
		 */
		const TYPE_LINKS = 'links';

		/**
		 * Type de liste "links" retourné par la méthode communautessrs
		 */
		const TYPE_TYPEORIENT_ID = 'typeorient_id';

		/**
		 * Nom du component
		 *
		 * @var string
		 */
		public $name = 'InsertionsBeneficiaires';

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array(
			'Session',
			'WebrsaUsers'
		);

		/**
		 * Conditions par défaut pour les méthodes typesorients, structuresreferentes
		 * et referents.
		 *
		 * @var array
		 */
		public $conditions = array(
			'typesorients' => array(
				'Typeorient.actif' => 'O'
			),
			'structuresreferentes' => array(
				'Typeorient.actif' => 'O',
				'Structurereferente.actif' => 'O'
			),
			'referents' => array(
				'Typeorient.actif' => 'O',
				'Structurereferente.actif' => 'O',
				'Referent.actif' => 'O'
			),
			'communautessrs' => array(
				'Communautesr.actif' => '1'
			),
			'dreesorganismes' => array(
				'Dreesorganisme.actif' => '1'
			)
		);

		/**
		 * Retourne la clé de session pour une méthode et un querydata donnés.
		 *
		 * @param string $method Le nom de la méthode
		 * @param array $query Le querydata
		 * @return string
		 */
		public function sessionKey( $method, array $query ) {
			$queryHash = sha1( serialize( $query ) );
			$sessionKey = "Auth.{$this->name}.{$method}.{$queryHash}";
			return $sessionKey;
		}

		/**
		 * Retourne les options par défaut pour les différentes méthodes.
		 *
		 * @param string $method
		 * @param array $options
		 * @return array
		 * @throws RuntimeException
		 */
		public function options( $method, array $options = array() ) {
			switch( $method ) {
				case 'typesorients':
					$options += array(
						'conditions' => $this->conditions['typesorients'],
						'empty' => false,
						'cache' => true,
						'with_parentid' => Configure::read( 'with_parentid' )
					);
					break;
				case 'structuresreferentes':
					$options += array(
						'conditions' => $this->conditions['structuresreferentes'],
						'prefix' => true,
						'type' => self::TYPE_LIST,
						'cache' => true
					);
					break;
				case 'referents':
					$options += array(
						'conditions' => $this->conditions['referents'],
						'prefix' => true,
						'type' => self::TYPE_LIST,
						'cache' => true
					);
					break;
				case 'communautessrs':
					$options += array(
						'conditions' => $this->conditions['communautessrs'],
						'type' => self::TYPE_LIST,
						'cache' => true
					);
					break;
				case 'dreesorganismes':
					$options += array(
						'conditions' => $this->conditions['dreesorganismes'],
						'empty' => false,
						'cache' => true,
						'with_parentid' => true
					);
					break;
				default:
					$msgstr = sprintf( 'La méthode %s:%s n\'accepte pas la valeur %s comme paramètre $method', __CLASS__, __FUNCTION__, $method );
					throw new RuntimeException( $msgstr, 500 );
			}

			return $options;
		}

        /**
		 * Retourne la liste des types d'oriention.
		 * Mise en cache dans la session de l'utilisateur.
		 *
		 * Appelle la méthode InsertionsBeneficiairesComponent::_typesorients
		 * en ajoutant les conditions implicites suivant le département et
		 * l'utilisateur connecté.
		 *
		 * Options par défaut
		 * <pre>
		 * array(
		 * 	'conditions' => array(
		 *		'Typeorient.actif' => 'O'
		 *	),
		 * 	'empty' => false,
		 * 	'cache' => true,
		 *	'with_parentid' => null
		 * );
		 * </pre>
		 *
		 * @todo Typeorient->listOptions() -> -9 requêtes (max, en debug), -5 sinon
		 *
		 * @see InsertionsBeneficiairesComponent::_typesorients()
		 * @see options()
		 *
		 * @param array $options La clé conditions permet de spécifier ou
		 *	de surcharger les conditions, la clé empty permet de spécifier si
		 *	l'on veut une entrée dont la clé sera 0 et la valeur 'Non orienté',
		 *	la clé with_parentid permet de surcharger la valeur lue par
		 *	Configure::read( 'with_parentid' ).
		 * @return array
		 */
		public function typesorients( array $options = array() ) {
			$Controller = $this->_Collection->getController();
			$Controller->loadModel( 'Structurereferente' );

			$departement = Configure::read( 'Cg.departement' );
			$options = $this->options( __FUNCTION__, $options );

			if( $departement == 66 && $this->Session->read( 'Auth.User.type' ) === 'externe_ci' ) {
				$parent_sq = $Controller->Structurereferente->Typeorient->sq(
					array(
						'alias' => 't',
						'fields' => array(
							't.parentid'
						),
						'conditions' => array(
							't.id = structuresreferentes.typeorient_id'
						),
						'contain' => false
					)
				);
				$sq = $Controller->Structurereferente->sq(
					array(
						'alias' => 'structuresreferentes',
						'fields' => array(
							'typesorients.id'
						),
						'joins' => array(
							array(
								'type' => 'INNER',
								'table' => 'typesorients',
								'alias' => 'typesorients',
								'conditions' => $options['with_parentid']
									? array(
										'OR' => array(
											'typesorients.id = structuresreferentes.typeorient_id',
											"typesorients.id IN ( {$parent_sq} )",
										)
									) : array(
										'typesorients.id = structuresreferentes.typeorient_id'
									)
							)
						),
						'conditions' => array(
							'structuresreferentes.id' => $this->WebrsaUsers->structuresreferentes()
						),
						'contain' => false
					)
				);
                $options['conditions'][] = "Typeorient.id IN ( {$sq} )";
            }

			return $this->_typesorients( $options );
		}

        /**
		 * Retourne la liste des types d'oriention.
		 * Mise en cache dans la session de l'utilisateur.
		 *
		 * Options par défaut
		 * <pre>
		 * array(
		 * 	'conditions' => array(
		 *		'Typeorient.actif' => 'O'
		 *	),
		 * 	'empty' => false,
		 * 	'cache' => true,
		 *	'with_parentid' => null
		 * );
		 * </pre>
		 *
		 * @todo Typeorient->listOptions() -> -9 requêtes (max, en debug), -5 sinon
		 *
		 * @see options()
		 *
		 * @param array $options La clé conditions permet de spécifier ou
		 *	de surcharger les conditions, la clé empty permet de spécifier si
		 *	l'on veut une entrée dont la clé sera 0 et la valeur 'Non orienté',
		 *	la clé with_parentid permet de surcharger la valeur lue par
		 *	Configure::read( 'with_parentid' ).
		 * @return array
		 */
		protected function _typesorients( array $options = array() ) {
			$Controller = $this->_Collection->getController();
			$Controller->loadModel( 'Structurereferente' );

			$options = $this->options( 'typesorients', $options );

			$sessionKey = $this->sessionKey( __FUNCTION__, $options['conditions'] );
			$results = $this->Session->read( $sessionKey );

			if( $results === null || false == $options['cache'] ) {
				$query = array(
					'fields' => array(
						'Typeorient.id',
						'Typeorient.parentid',
						'Typeorient.lib_type_orient',
					),
					'conditions' => $options['conditions'],
					'contain' => false,
					'order' => array(
						'Typeorient.parentid IS NOT NULL ASC',
						'Typeorient.lib_type_orient ASC'
					)
				);

				$parents = array();
				$results = array();

				$typesorients = $Controller->Structurereferente->Typeorient->find( 'all', $query );

				if( !empty( $typesorients ) ) {
					foreach( $typesorients as $typeorient ) {
						if( true === $options['with_parentid'] ) {
							if( null === $typeorient['Typeorient']['parentid'] ) {
								$parents[$typeorient['Typeorient']['id']] = $typeorient['Typeorient']['lib_type_orient'];
							}
							else {
								$optgroup = $parents[$typeorient['Typeorient']['parentid']];
								$results[$optgroup][$typeorient['Typeorient']['id']] = $typeorient['Typeorient']['lib_type_orient'];
							}
						}
						else {
							$results[$typeorient['Typeorient']['id']] = $typeorient['Typeorient']['lib_type_orient'];
						}
					}
				}

				if( true == $options['cache'] ) {
					$this->Session->write( $sessionKey, $results );
				}
			}

			if( Hash::get( $options, 'empty' ) ) {
				$results = array( 0 => 'Non orienté' ) + (array)$results;
			}

			return $results;
		}


		/**
		 * Retourne une condition à ajouter pour les utilisateurs CG 93 limités au
		 * niveau des zones géographiques.
		 *
		 * @return string
		 */
		protected function _sqStructurereferenteZonesgeographiques93() {
			$Controller = $this->_Collection->getController();
			$Controller->loadModel( 'Structurereferente' );

			$sqStructurereferente = $Controller->Structurereferente->StructurereferenteZonegeographique->sq(
				array(
					'alias' => 'structuresreferentes_zonesgeographiques',
					'fields' => array( 'structuresreferentes_zonesgeographiques.structurereferente_id' ),
					'conditions' => array(
						'structuresreferentes_zonesgeographiques.zonegeographique_id' => array_keys( (array)$this->Session->read( 'Auth.Zonegeographique' ) )
					),
					'contain' => false
				)
			);

			return "Structurereferente.id IN ( {$sqStructurereferente} )";
		}

		/**
		 * Retourne la liste des structures référentes actives (pour un dependant
		 * select avec le type d'orientation) liées à un type d'oientation actif.
		 * Mise en cache dans la session de l'utilisateur.
		 *
		 * Appelle la méthode InsertionsBeneficiairesComponent::_structuresreferentes
		 * en ajoutant les conditions implicites suivant le département et
		 * l'utilisateur connecté.
		 *
		 * Options par défaut
		 * <pre>
		 * array(
		 * 	'conditions' => array(
		 *		'Typeorient.actif' => 'O',
		 *		'Structurereferente.actif' => 'O'
		 *	),
		 * 	'prefix' => true,
		 * 	'type' => 'list',
		 * 	'cache' => true
		 * );
		 * </pre>
		 *
		 * @see InsertionsBeneficiairesComponent::_structuresreferentes()
		 *
		 * @param array $options
		 * @return array
		 */
		public function structuresreferentes( $options = array( ) ) {
			$departement = Configure::read( 'Cg.departement' );
			$options = $this->options( __FUNCTION__, $options );

			if( $departement == 93 && $this->Session->read( 'Auth.User.filtre_zone_geo' ) !== false ) {
				$options['conditions'][] = $this->_sqStructurereferenteZonesgeographiques93();
			}
			else if( $departement == 66 && $this->Session->read( 'Auth.User.type' ) === 'externe_ci' ) {
				$structurereferente_id = $this->WebrsaUsers->structuresreferentes();
				$options['conditions']['Structurereferente.id'] = $structurereferente_id;
			}

			return $this->_structuresreferentes( $options );
		}

		/**
		 * Retourne la liste des structures référentes actives (pour un dependant
		 * select avec le type d'orientation) liées à un type d'oientation actif.
		 * Mise en cache dans la session de l'utilisateur.
		 *
		 * Options par défaut
		 * <pre>
		 * array(
		 * 	'conditions' => array(
		 *		'Typeorient.actif' => 'O',
		 *		'Structurereferente.actif' => 'O'
		 *	),
		 * 	'prefix' => true,
		 * 	'type' => 'list',
		 * 	'cache' => true
		 * );
		 * </pre>
		 *
		 * @see InsertionsBeneficiairesComponent::_structuresreferentes()
		 *
		 * @param array $options
		 * @return array
		 * @throws RuntimeException
		 */
		protected function _structuresreferentes( $options = array( ) ) {
			$Controller = $this->_Collection->getController();
			$options = $this->options( 'structuresreferentes', $options );

			$sessionKey = $this->sessionKey( __FUNCTION__, $options['conditions'] );
			$results = $this->Session->read( $sessionKey );

			if( $results === null || false == $options['cache'] ) {
				$Controller->loadModel( 'Structurereferente' );

				$query = array(
					'fields' => array(
						'Typeorient.lib_type_orient',
						'Structurereferente.id',
						'Structurereferente.typeorient_id',
						'Structurereferente.lib_struc'
					),
					'joins' => array(
						$Controller->Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) )
					),
					'conditions' => $options['conditions'],
					'contain' => false,
					'order' => array(
						'Typeorient.lib_type_orient ASC',
						'Structurereferente.lib_struc ASC',
					)
				);

				$results = array(
					'optgroup' => array(),
					'optgroup_prefix' => array(),
					'ids' => array(),
					'ids_prefix' => array(),
					'list' => array(),
					'list_prefix' => array(),
					'typeorient_id' => array(),
					'typeorient_id_prefix' => array()
				);

				$structuresreferentes = $Controller->Structurereferente->find( 'all', $query );

				if( !empty( $structuresreferentes ) ) {
					foreach( $structuresreferentes as $structurereferente ) {
						$typeorient_id = $structurereferente['Structurereferente']['typeorient_id'];
						$key = $structurereferente['Structurereferente']['id'];
						$keyPrefix = "{$typeorient_id}_{$structurereferente['Structurereferente']['id']}";

						// Cas optgroup
						if( !isset( $results['optgroup'][$structurereferente['Typeorient']['lib_type_orient']] ) ) {
							$results['optgroup'][$structurereferente['Typeorient']['lib_type_orient']] = array();
						}
						$results['optgroup'][$structurereferente['Typeorient']['lib_type_orient']][$key] = $structurereferente['Structurereferente']['lib_struc'];
						$results['optgroup_prefix'][$structurereferente['Typeorient']['lib_type_orient']][$keyPrefix] = $structurereferente['Structurereferente']['lib_struc'];

						// Cas ids
						$results['ids'][$key] = $structurereferente['Structurereferente']['id'];
						$results['ids_prefix'][$keyPrefix] = $structurereferente['Structurereferente']['id'];

                        // Cas list
						$results['list'][$key] = $structurereferente['Structurereferente']['lib_struc'];
						$results['list_prefix'][$keyPrefix] = $structurereferente['Structurereferente']['lib_struc'];

                        // Cas typeorient_id
						$results['typeorient_id'][$typeorient_id][$key] = $structurereferente['Structurereferente']['lib_struc'];
						$results['typeorient_id_prefix'][$typeorient_id][$keyPrefix] = $structurereferente['Structurereferente']['lib_struc'];
					}
				}

				// Pour les listes optgroup, tri par clé
				ksort( $results['optgroup'] );
				ksort( $results['optgroup_prefix'] );

				asort( $results['list'] );
				asort( $results['list_prefix'] );

				if( true == $options['cache'] ) {
					$this->Session->write( $sessionKey, $results );
				}
			}

			if( !empty( $results ) ) {
				if( $options['type'] === self::TYPE_OPTGROUP ) {
					$results = $options['prefix'] ? $results['optgroup_prefix'] : $results['optgroup'];
				}
				else if( $options['type'] === self::TYPE_IDS ) {
					$results = $options['prefix'] ? $results['ids_prefix'] : $results['ids'];
				}
				else if( $options['type'] === self::TYPE_LIST ) {
					$results = $options['prefix'] ? $results['list_prefix'] : $results['list'];
				}
				else if( $options['type'] === self::TYPE_TYPEORIENT_ID ) {
					$results = $options['prefix'] ? $results['typeorient_id_prefix'] : $results['typeorient_id'];
				}
				else {
					$msgstr = sprintf( 'La valeur du paramètre "type" "%s" n\'est pas acceptée dans la méthode %s::%s', $options['type'], __CLASS__, __FUNCTION__ );
					throw new RuntimeException( $msgstr, 500 );
				}
			}

			return $results;
		}

		/**
		 * Retourne la liste des référents actifs (pour un dependant select avec
		 * la structure référente) liés à une structure référente active, liée à
		 * un type d'oientation actif.
		 * Mise en cache dans la session de l'utilisateur.
		 *
		 * Appelle la méthode InsertionsBeneficiairesComponent::_referents
		 * en ajoutant les conditions implicites suivant le département et
		 * l'utilisateur connecté.
		 *
		 * <pre>
		 * array(
		 * 	'conditions' => array(
		 *		'Typeorient.actif' => 'O',
		 *		'Structurereferente.actif' => 'O',
		 *		'Referent.actif' => 'O'
		 *	),
		 * 	'prefix' => true,
		 * 	'type' => 'list',
		 * 	'cache' => true
		 * );
		 * </pre>
		 *
		 * @todo Referent::listOptions() -> actif, prefix
		 *
		 * @see InsertionsBeneficiairesComponent::_referents()
		 *
		 * @param array $options
		 * @return array
		 */
		public function referents( $options = array() ) {
			$departement = Configure::read( 'Cg.departement' );
			$options = $this->options( __FUNCTION__, $options );

			if( $departement == 93 && $this->Session->read( 'Auth.User.filtre_zone_geo' ) !== false ) {
				$options['conditions'][] = $this->_sqStructurereferenteZonesgeographiques93();
			}

			return $this->_referents( $options );
		}

		/**
		 * Retourne la liste des référents actifs (pour un dependant select avec
		 * la structure référente) liés à une structure référente active, liée à
		 * un type d'oientation actif.
		 * Mise en cache dans la session de l'utilisateur.
		 *
		 * <pre>
		 * array(
		 * 	'conditions' => array(
		 *		'Typeorient.actif' => 'O',
		 *		'Structurereferente.actif' => 'O',
		 *		'Referent.actif' => 'O'
		 *	),
		 * 	'prefix' => true,
		 * 	'type' => 'list',
		 * 	'cache' => true
		 * );
		 * </pre>
		 *
		 * @todo Referent::listOptions() -> actif, prefix
		 *
		 * @see InsertionsBeneficiairesComponent::_referents()
		 *
		 * @param array $options
		 * @return array
		 * @throws RuntimeException
		 */
		protected function _referents( $options = array() ) {
			$Controller = $this->_Collection->getController();
			$options = $this->options( 'referents', $options );

			$sessionKey = $this->sessionKey( __FUNCTION__, $options['conditions'] );
			$results = $this->Session->read( $sessionKey );

			if( $results === null || false == $options['cache'] ) {
				$Controller->loadModel( 'Structurereferente' );

				$query = array(
					'fields' => array(
						'Referent.id',
						'Referent.structurereferente_id',
						'Referent.qual',
						'Referent.nom',
						'Referent.prenom',
						'Structurereferente.lib_struc'
					),
					'joins' => array(
						$Controller->Structurereferente->Referent->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$Controller->Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) )
					),
					'conditions' => $options['conditions'],
					'contain' => false,
					'order' => array(
						'Referent.nom ASC',
						'Referent.prenom ASC'
					)
				);

				$results = array(
					'optgroup' => array(),
					'optgroup_prefix' => array(),
					'ids' => array(),
					'ids_prefix' => array(),
					'list' => array(),
					'list_prefix' => array()
				);

				$referents = $Controller->Structurereferente->Referent->find( 'all', $query );

				if( !empty( $referents ) ) {
					foreach( $referents as $referent ) {
						$libelle = "{$referent['Referent']['qual']} {$referent['Referent']['nom']} {$referent['Referent']['prenom']}";

						$key = $referent['Referent']['id'];
						$keyPrefix = "{$referent['Referent']['structurereferente_id']}_{$referent['Referent']['id']}";

						// Cas optgroup
						if( !isset( $results['optgroup'][$referent['Structurereferente']['lib_struc']] ) ) {
							$results['optgroup'][$referent['Structurereferente']['lib_struc']] = array();
						}
						$results['optgroup'][$referent['Structurereferente']['lib_struc']][$key] = $libelle;
						$results['optgroup_prefix'][$referent['Structurereferente']['lib_struc']][$keyPrefix] = $libelle;

						// Cas seulement les ids
						$results['ids'][$key] = $referent['Referent']['id'];
						$results['ids_prefix'][$keyPrefix] = $referent['Referent']['id'];

                        // Cas du find list
						$results['list'][$key] = $libelle;
						$results['list_prefix'][$keyPrefix] = $libelle;
					}
				}

				 if( true == $options['cache'] ) {
					$this->Session->write( $sessionKey, $results );
				 }
			}

			if( !empty( $results ) ) {
				if( $options['type'] === self::TYPE_OPTGROUP ) {
					$results = $options['prefix'] ? $results['optgroup_prefix'] : $results['optgroup'];
				}
				else if( $options['type'] === self::TYPE_IDS ) {
					$results = $options['prefix'] ? $results['ids_prefix'] : $results['ids'];
				}
				else if( $options['type'] === self::TYPE_LIST ) {
					$results = $options['prefix'] ? $results['list_prefix'] : $results['list'];
				}
				else {
					$msgstr = sprintf( 'La valeur du paramètre "type" "%s" n\'est pas acceptée dans la méthode %s::%s', $options['type'], __CLASS__, __FUNCTION__ );
					throw new RuntimeException( $msgstr, 500 );
				}
			}

			return $results;
		}

		/**
		 * Permet d'ajouter les entrées du type d'orientation, de la structure
		 * référente et du référent de l'enregistrement à la liste des options
		 * pour ne pas perdre d'information lors de la modification d'un
		 * enregistrement.
		 * Les entrées ajoutées ne sont pas triées.
		 *
		 * <pre>
		 * array(
		 *	'typesorients' => array(
		 *		'path' => 'typeorient_id',
		 *		'cache' => false
		 *	),
		 *	'structuresreferentes' => array(
		 *		'path' => 'structurereferente_id',
		 *		'cache' => false
		 *	),
		 *	'referents' => array(
		 *		'path' => 'referent_id',
		 *		'cache' => false
		 *	)
		 * );
		 * </pre>
		 *
		 * @see InsertionsBeneficiairesComponent::options() qui sera utilisée pour
		 * les autres paramètres.
		 * @see InsertionsBeneficiairesComponent::_typesorients()
		 * @see InsertionsBeneficiairesComponent::_structuresreferentes()
		 * @see InsertionsBeneficiairesComponent::_referents()
		 *
		 * @param array $options Les options qui seront envoyées à la vue
		 * @param array $data L'enregistrement en cours de modification
		 * @param array $params Les paramètres à utiliser pour chacune des méthodes
		 * @return array
		 */
		public function completeOptions( array $options, array $data, array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$Controller->loadModel( 'Structurereferente' );

			$defaultMethodsParams = array(
				'typesorients' => array(
					'path' => 'typeorient_id',
					'cache' => false
				),
				'structuresreferentes' => array(
					'path' => 'structurereferente_id',
					'cache' => false
				),
				'referents' => array(
					'path' => 'referent_id',
					'cache' => false
				)
			);

			foreach( $defaultMethodsParams as $method => $defaultMethodParams ) {
				$methodParams = Hash::get( $params, $method );
				if( false !== $methodParams ) {
					$params[$method] = $this->options( $method, (array)$methodParams + $defaultMethodParams );
				}
				else {
					$params[$method] = false;
				}
			}

			foreach( $params as $method => $methodParams ) {
				if( Hash::check( $data, $methodParams['path'] ) && false !== $methodParams ) {
					$value = Hash::get( $data, $methodParams['path'] );
					if( false === empty( $value ) ) {
						$modelName = Inflector::classify( $method );
						if( false === ( $method === 'typesorients' && $methodParams['with_parentid'] ) ) {
							$methodParams['conditions'] = array( "{$modelName}.id" => suffix( $value ) );
						}
						else {
							$subQuery = array(
								'alias' => 'typesorients',
								'fields' => array( 'typesorients.parentid' ),
								'conditions' => array(
									'typesorients.id' => suffix( $value )
								),
								'contain' => false,
								'limit' => 1
							);
							$sql = $Controller->Structurereferente->Typeorient->sq( $subQuery );
							$methodParams['conditions'] = array(
								'OR' => array(
									"{$modelName}.id" => suffix( $value ),
									"{$modelName}.id IN ( {$sql} )"
								)
							);
						}

						$realMethod = '_'.$method;
						$results = $this->{$realMethod}( $methodParams );

						$options[$methodParams['path']] = array_complete_recursive(
							(array)Hash::get( $options, $methodParams['path'] ),
							(array)$results
						);
					}
				}
			}

			return $options;
		}

        /**
		 * Retourne la liste des projets de villes territoriaux, utilisée par les
		 * utilisateurs de type "cg" et "cpdvcom" du CG 93.
		 * Mise en cache dans la session de l'utilisateur.
		 *
		 * Options par défaut
		 * <pre>
		 * array(
		 * 	'conditions' => array(
		 *		'Communautesr.actif' => '1'
		 *	),
		 * 	'type' => 'list,
		 * 	'cache' => true
		 * );
		 * </pre>
		 *
		 * @see options()
		 *
		 * @param array $options La clé conditions permet de spécifier ou
		 *	de surcharger les conditions, la clé "type" permet d'obtenir une liste
		 *	(valeur "list") ou les liens entre les ids des projets de villes
		 *	territoriaux et les structures référentes liées (type "links").
		 * @return array
		 */
		public function communautessrs( array $options = array() ) {
			$Controller = $this->_Collection->getController();
			$Controller->loadModel( 'Communautesr' );

			$departement = Configure::read( 'Cg.departement' );
			$options = $this->options( __FUNCTION__, $options );

			$sessionKey = $this->sessionKey( __FUNCTION__, $options['conditions'] );
			$results = $this->Session->read( $sessionKey );

			if( $results === null || false == $options['cache'] ) {
				$results = array(
					'list' => array(),
					'links' => array()
				);

				$user_type = $Controller->Session->read( 'Auth.User.type' );
				if( 93 == $departement && in_array( $user_type, array( 'cg', 'externe_cpdvcom' ) ) ) {
					// Liste des projets de villes communautaires
					$query = array(
						'fields' => array(
							'Communautesr.id',
							'Communautesr.name'
						),
						'conditions' => $options['conditions'],
						'contain' => false,
						'order' => array(
							'Communautesr.name ASC'
						)
					);

					// Si l'utilisateur est lié à un CPDVCOM, on limite la liste
					if( 'externe_cpdvcom' === $user_type ) {
						$communautesr_id = $Controller->Session->read( 'Auth.User.communautesr_id' );
						$query['conditions'][] = array( 'Communautesr.id' => $communautesr_id );
					}

					$results['list'] = $Controller->Communautesr->find( 'list', $query );

					// Liens entre les projets de villes comunautaires et les structures référentes
					$query = array(
						'fields' => array(
							'CommunautesrStructurereferente.communautesr_id',
							'CommunautesrStructurereferente.structurereferente_id'
						),
						'contain' => false
					);

					// Si l'utilisateur est lié à un CPDVCOM, on limite la liste
					if( 'externe_cpdvcom' === $user_type ) {
						$communautesr_id = $Controller->Session->read( 'Auth.User.communautesr_id' );
						$query['conditions'][] = array( 'CommunautesrStructurereferente.communautesr_id' => $communautesr_id );
					}

					$links = $Controller->Communautesr->CommunautesrStructurereferente->find( 'all', $query );

					foreach( $links as $link ) {
						$communautesr_id = $link['CommunautesrStructurereferente']['communautesr_id'];
						if( !isset( $results['links'][$communautesr_id] ) ) {
							$results['links'][$communautesr_id] = array();
						}
						$results['links'][$communautesr_id][] = $link['CommunautesrStructurereferente']['structurereferente_id'];
					}
				}

				if( true == $options['cache'] ) {
					$this->Session->write( $sessionKey, $results );
				}
			}

			if( !empty( $results ) ) {
				if( $options['type'] === self::TYPE_LINKS ) {
					$results = $results['links'];
				}
				else if( $options['type'] === self::TYPE_LIST ) {
					$results = $results['list'];
				}
				else {
					$msgstr = sprintf( 'La valeur du paramètre "type" "%s" n\'est pas acceptée dans la méthode %s::%s', $options['type'], __CLASS__, __FUNCTION__ );
					throw new RuntimeException( $msgstr, 500 );
				}
			}

			return $results;
		}

		/**
		 * Retourne la liste des types d'organismes DREES.
		 * Mise en cache dans la session de l'utilisateur.
		 *
		 * Appelle la méthode InsertionsBeneficiairesComponent::_dreesorganismes
		 * 		 *
		 * Options par défaut
		 * <pre>
		 * array(
		 * 	'conditions' => array(
		 *		'Dreesorganisme.actif' => '1'
		 *	),
		 * 	'empty' => false,
		 * 	'cache' => true,
		 *	'with_parentid' => true
		 * );
		 * </pre>
		 *
		 * @todo Dreesorganisme->listOptions() -> -9 requêtes (max, en debug), -5 sinon
		 *
		 * @see InsertionsBeneficiairesComponent::_dreesorganismes()
		 * @see options()
		 *
		 * @param array $options La clé conditions permet de spécifier ou
		 *	de surcharger les conditions, la clé empty permet de spécifier si
		 *	l'on veut une entrée dont la clé sera 0 et la valeur 'Non orienté',
		 *	la clé with_parentid permet de surcharger la valeur lue par
		 *	Configure::read( 'with_parentid' ).
		 * @return array
		 */
		public function dreesorganismes( array $options = array() ) {
			$options = $this->options('dreesorganismes');

			return $this->_dreesorganismes( $options );
		}

        /**
		 * Retourne la liste des types d'organismes DREES.
		 * Mise en cache dans la session de l'utilisateur.
		 *
		 * Options par défaut
		 * <pre>
		 * array(
		 * 	'conditions' => array(
		 *		'Dreesorganisme.actif' => '1'
		 *	),
		 * 	'empty' => false,
		 * 	'cache' => true,
		 *	'with_parentid' => true
		 * );
		 * </pre>
		 *
		 * @todo Dreesorganismes->listOptions() -> -9 requêtes (max, en debug), -5 sinon
		 *
		 * @see options()
		 *
		 * @param array $options La clé conditions permet de spécifier ou
		 *	de surcharger les conditions, la clé empty permet de spécifier si
		 *	l'on veut une entrée dont la clé sera 0 et la valeur 'Non orienté',
		 *	la clé with_parentid permet de surcharger la valeur lue par
		 *	Configure::read( 'with_parentid' ).
		 * @return array
		 */
		protected function _dreesorganismes( array $options = array() ) {
			$Controller = $this->_Collection->getController();
			$Controller->loadModel( 'Structurereferente' );

			$options = $this->options( 'dreesorganismes', $options );

			$sessionKey = $this->sessionKey( __FUNCTION__, $options['conditions'] );

			$results = $this->Session->read( $sessionKey );


			if( $results === null || false == $options['cache'] ) {
				$query = array(
					'fields' => array(
						'Dreesorganisme.id',
						'Dreesorganisme.parentid',
						'Dreesorganisme.lib_dreesorganisme',
					),
					'conditions' => $options['conditions'],
					'contain' => false,
					'order' => array(
						'Dreesorganisme.parentid IS NOT NULL ASC',
						'Dreesorganisme.lib_dreesorganisme ASC'
					)
				);

				$parents = array();
				$results = array();

				$dreesorganismes = $Controller->Structurereferente->Dreesorganisme->find( 'all', $query );

				if( !empty( $dreesorganismes ) ) {
					foreach( $dreesorganismes as $dreesorganisme ) {
						if( true === $options['with_parentid'] ) {
							if( null === $dreesorganisme['Dreesorganisme']['parentid'] ) {
								$parents[$dreesorganisme['Dreesorganisme']['id']] = $dreesorganisme['Dreesorganisme']['lib_dreesorganisme'];
							}
							else {
								$optgroup = $parents[$dreesorganisme['Dreesorganisme']['parentid']];
								$results[$optgroup][$dreesorganisme['Dreesorganisme']['id']] = $dreesorganisme['Dreesorganisme']['lib_dreesorganisme'];
							}
						}
						else {
							$results[$dreesorganisme['Dreesorganisme']['id']] = $dreesorganisme['Dreesorganisme']['lib_dressorganisme'];
						}
					}
				}

				if( true == $options['cache'] ) {
					$this->Session->write( $sessionKey, $results );
				}
			}

			if( Hash::get( $options, 'empty' ) ) {
				$results = array( 0 => 'Non orienté' ) + (array)$results;
			}

			return $results;
		}
	}
?>