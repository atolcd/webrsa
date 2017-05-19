<?php
	/**
	 * Code source de la classe InsertionsAllocatairesComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe InsertionsAllocatairesComponent ...
	 *
	 * @todo ajouter un paramètre 'disabled' => true|false|null
	 *
	 * @deprecated since 3.1.0
	 *
	 * @package app.Controller.Component
	 */
	class InsertionsAllocatairesComponent extends Component
	{
		/**
		 *
		 * @var string
		 */
		public $name = 'InsertionsAllocataires';

		/**
		 * Paramètres de ce component
		 *
		 * @var array
		 */
		public $settings = array( );

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array( 'Session' );

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
		 * Retourne la liste des types d'oriention.
		 *
		 * Mise en cache dans la session de l'utilisateur.
		 *
		 * @todo 'optgroup' => false,
		 *
		 * @param type $options
		 * @return type
		 */
		public function typesorients( $options = array() ) {
			$Typeorient = ClassRegistry::init( 'Typeorient' );

			$options = Set::merge(
				array(
					'conditions' => array(),
				),
				$options
			);

			$conditions = array(
//				'Typeorient.actif' => 'O'
			);

			$conditions = Set::merge( $conditions, $options['conditions'] );

            if( ( Configure::read( 'Cg.departement' ) == 66 ) && $this->Session->read( 'Auth.User.type' ) === 'externe_ci' ) {
                $sq = $Typeorient->Structurereferente->sq(
                   array(
                       'alias' => 'structuresreferentes',
                       'fields' => array(
                           'structuresreferentes.typeorient_id'
                       ),
                       'conditions' => array(
                           'structuresreferentes.id' => $this->Session->read( 'Auth.User.structurereferente_id' )
                       ),
                       'contain' => false
                   )
               );
                $conditions[] = "Typeorient.id IN ( {$sq} )";
            }

			$query = array(
				'fields' => $Typeorient->fields(),
				'conditions' => $conditions,
				'contain' => false,
				'order' => array(
					'Typeorient.lib_type_orient ASC'
				)
			);

			$sessionKey = $this->sessionKey( __FUNCTION__, $query );
			$results = $this->Session->read( $sessionKey );

			if( $results === null ) {
				$results = array();

				$tmps = $Typeorient->find( 'all', $query );

				if( !empty( $tmps ) ) {
					foreach( $tmps as $tmp ) {
						$results[$tmp['Typeorient']['id']] = $tmp['Typeorient']['lib_type_orient'];
					}
				}

				$this->Session->write( $sessionKey, $results );
			}

			if( Hash::get( $options, 'empty' ) ) {
				$results = array( 0 => 'Non orienté' ) + (array)$results;
			}

			return $results;
        }

		/**
		 * Retourne la liste des structures référentes actives (pour un dependant
		 * select avec le type d'orientation) liées à un type d'oientation actif.
		 *
		 * Mise en cache dans la session de l'utilisateur.
		 *
		 * <pre>
		 * $options = array(
		 * 	'conditions' => array(),
		 * 	'optgroup' => false,
		 *	'ids' => false,
		 *	'list' => false,
		 * );
		 * </pre>
		 *
		 * @param array $options
		 * @return array
		 */
		public function structuresreferentes( $options = array( ) ) {
			$Structurereferente = ClassRegistry::init( 'Structurereferente' );

			/**
			 * Déplacé vers Structureferente
			 */
//
//			$options = Set::merge(
//				array(
//					'conditions' => array(),
//					'optgroup' => false,
//					'ids' => false,
//                    'list' => false
//				),
//				$options
//			);
//
//			$conditions = array(
//				'Typeorient.actif' => 'O',
//				'Structurereferente.actif' => 'O',
//			);
//
//			$conditions = Set::merge( $conditions, $options['conditions'] );
//
//			if( ( Configure::read( 'Cg.departement' ) == 93 ) && $this->Session->read( 'Auth.User.filtre_zone_geo' ) !== false ) {
//				$zonesgeographiques_ids = array_keys( $this->Session->read( 'Auth.Zonegeographique' ) );
//
//				$sqStructurereferente = $Structurereferente->StructurereferenteZonegeographique->sq(
//					array(
//						'alias' => 'structuresreferentes_zonesgeographiques',
//						'fields' => array( 'structuresreferentes_zonesgeographiques.structurereferente_id' ),
//						'conditions' => array(
//							'structuresreferentes_zonesgeographiques.zonegeographique_id' => $zonesgeographiques_ids
//						),
//						'contain' => false
//					)
//				);
//				$conditions[] = "Structurereferente.id IN ( {$sqStructurereferente} )";
//			}
//			else if( ( Configure::read( 'Cg.departement' ) == 66 ) && $this->Session->read( 'Auth.User.type' ) === 'externe_ci' ) {
//				$structurereferente_id = $this->Session->read( 'Auth.User.structurereferente_id' );
//				$conditions['Structurereferente.id'] = $structurereferente_id;
//			}
//
//			$query = array(
//				'fields' => array_merge(
//					$Structurereferente->Typeorient->fields(),
//					$Structurereferente->fields()
//				),
//				'joins' => array(
//					$Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) )
//				),
//				'conditions' => $conditions,
//				'contain' => false,
//				'order' => array(
//					'Typeorient.lib_type_orient ASC',
//					'Structurereferente.lib_struc ASC',
//				)
//			);
//
//			$sessionKey = $this->sessionKey( __FUNCTION__, $query );
//			$results = $this->Session->read( $sessionKey );
//
//			if( $results === null ) {
//				$results = array();
//
//				$tmps = $Structurereferente->find( 'all', $query );
//
//				if( !empty( $tmps ) ) {
//					foreach( $tmps as $tmp ) {
//						// Cas optgroup, structurereferente_id
//						if( !isset( $results['optgroup'][$tmp['Typeorient']['lib_type_orient']] ) ) {
//							$results['optgroup'][$tmp['Typeorient']['lib_type_orient']] = array();
//						}
//						$results['optgroup'][$tmp['Typeorient']['lib_type_orient']][$tmp['Structurereferente']['id']] = $tmp['Structurereferente']['lib_struc'];
//
//						// Cas seulement les ids
//						$results['ids'][] = $tmp['Structurereferente']['id'];
//
//						// Cas typeorient_id_structurereferente_id
//						$results['normal']["{$tmp['Structurereferente']['typeorient_id']}_{$tmp['Structurereferente']['id']}"] = $tmp['Structurereferente']['lib_struc'];
//
//                        // Cas du find list
//						$results['list'][$tmp['Structurereferente']['id']] = $tmp['Structurereferente']['lib_struc'];
//					}
//				}
//
//				$this->Session->write( $sessionKey, $results );
//			}
//
//			if( !empty( $results ) ) {
//				// Cas optgroup, structurereferente_id
//				if( $options['optgroup'] ) {
//					$results = $results['optgroup'];
//				}
//				// Cas où l'on ne veut que les ids des structures référentes
//				else if( $options['ids'] ) {
//					$results = $results['ids'];
//				}
//				// Cas où l'on veut les libellés des structures référentes
//				else if( $options['list'] ) {
//					$results = $results['list'];
//				}
//				// Cas typeorient_id_structurereferente_id
//				else {
//					$results = $results['normal'];
//				}
//			}

			return $Structurereferente->structuresreferentes( $options );
		}

		/**
		 * Retourne la liste des référents actifs (pour un dependant select avec
		 * la structure référente) liés à une structure référente active, liée à
		 * un type d'oientation actif.
		 *
		 * Mise en cache dans la session de l'utilisateur.
		 *
		 * @param array $options
		 * @return array
		 */
		public function referents( $options = array() ) {
			$Referent = ClassRegistry::init( 'Referent' );

			$options = Set::merge(
				array(
					'conditions' => array(),
					'prefix' => false,
				),
				$options
			);

			$conditions = array(
				'Typeorient.actif' => 'O',
				'Structurereferente.actif' => 'O',
				'Referent.actif' => 'O',
			);

			$conditions = Set::merge( $conditions, $options['conditions'] );

			if( ( Configure::read( 'Cg.departement' ) == 93 ) && $this->Session->read( 'Auth.User.filtre_zone_geo' ) !== false ) {
				$zonesgeographiques_ids = array_keys( (array)$this->Session->read( 'Auth.Zonegeographique' ) );

				$sqStructurereferente = $Referent->Structurereferente->StructurereferenteZonegeographique->sq(
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

			$query = array(
				'fields' => array_merge(
					$Referent->Structurereferente->Typeorient->fields(),
					$Referent->Structurereferente->fields(),
					$Referent->fields()
				),
				'joins' => array(
					$Referent->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
					$Referent->Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) ),
				),
				'conditions' => $conditions,
				'contain' => false,
				'order' => array(
					'Referent.nom ASC',
					'Referent.prenom ASC',
				)
			);

			$sessionKey = $this->sessionKey( __FUNCTION__, $query );
			$results = $this->Session->read( $sessionKey );

			if( $results === null ) {
				$results = array();

				$tmps = $Referent->find( 'all', $query );

				if( !empty( $tmps ) ) {
					$idsPrefix = Set::format( $tmps, '{0}_{1}', array( '{n}.Referent.structurereferente_id', '{n}.Referent.id' ) );
					$idsNormal = Set::extract( $tmps, '/Referent/id' );
					$values = Set::format( $tmps, '{0} {1} {2}', array( '{n}.Referent.qual', '{n}.Referent.nom', '{n}.Referent.prenom' ) );

					$results = array(
						'prefix' => array_combine( $idsPrefix, $values ),
						'normal' => array_combine( $idsNormal, $values ),
					);
				}

				$this->Session->write( $sessionKey, $results );
			}

			if( !empty( $results ) ) {
				// Cas prefix
				if( $options['prefix'] ) {
					$results = $results['prefix'];
				}
				// Cas typeorient_id_structurereferente_id
				else {
					$results = $results['normal'];
				}
			}

			return $results;
		}

		/**
		 * Permet d'ajouter les entrées de la structure référente et du référent
		 * de l'enregistrement à la liste des options pour ne pas perdre d'information
		 * lors de la modification d'un enregistrement.
		 *
		 * Le liste des structures référentes est une liste à deux niveaux avec
		 * en premier niveau le type d'orientation.
		 *
		 * La liste des référents est une liste à un niveau avec en clé
		 * <structurereferente_id>_<referent_id>.
		 *
		 * @param array $options Les options qui seront envoyées à la vue
		 * @param array $data L'enregistrement en cours de modification
		 * @param array $params Les clés structurereferente_id et referent_id
		 *	contiennent les chemins vers ces champs, dans les options et dans data.
		 * @return array
		 */
		public function completeOptionsWithCurrentReferent( array $options, array $data, array $params = array() ) {
			$params += array(
				'structurereferente_id' => 'structurereferente_id',
				'referent_id' => 'referent_id'
			);

			$structurereferente_id = Hash::get( $data, $params['structurereferente_id'] );
			$referent_id = Hash::get( $data, $params['referent_id'] );

			$structuresreferentes = (array)Hash::get( $options, $params['structurereferente_id'] );
			$referents = (array)Hash::get( $options, $params['referent_id'] );

			$available = array();
			foreach( $structuresreferentes as $group ) {
				$available = array_merge( $available, array_keys( $group ) );
			}

			if( in_array( $structurereferente_id, $available ) === false || in_array( $referent_id, $referents ) === false ) {
				$Typeorient = ClassRegistry::init( 'Typeorient' );

				$query = array(
					'fields' => array(
						'Typeorient.lib_type_orient',
						'Structurereferente.id',
						'Structurereferente.lib_struc',
						'Referent.id',
						'Referent.nom_complet',
					),
					'contain' => false,
					'joins' => array(
						$Typeorient->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$Typeorient->Structurereferente->join( 'Referent', array( 'type' => 'INNER' ) )
					),
					'conditions' => array(
						'Structurereferente.id' => $structurereferente_id,
						'Referent.id' => suffix( $referent_id )
					)
				);

				$forceVirtualFields = $Typeorient->forceVirtualFields;
				$Typeorient->forceVirtualFields = true;
				$result = $Typeorient->find( 'first', $query );
				$Typeorient->forceVirtualFields = $forceVirtualFields;

				if( !empty( $result ) ) {
					$structuresreferentes = Hash::merge(
						$structuresreferentes,
						array(
							$result['Typeorient']['lib_type_orient'] => array(
								$result['Structurereferente']['id'] => $result['Structurereferente']['lib_struc']
							)
						)
					);
					$options = Hash::insert( $options, $params['structurereferente_id'], $structuresreferentes );

					$referents = Hash::merge(
						$referents,
						array(
							"{$result['Structurereferente']['id']}_{$result['Referent']['id']}" => $result['Referent']['nom_complet']
						)
					);
					$options = Hash::insert( $options, $params['referent_id'], $referents );
				}
			}

			return $options;
		}
	}
?>