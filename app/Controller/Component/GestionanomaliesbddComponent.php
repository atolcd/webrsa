<?php
	/**
	 * Fichier source de la classe GestionanomaliesbddComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe GestionanomaliesbddComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class GestionanomaliesbddComponent extends Component
	{
		/**
		* The initialize method is called before the controller's beforeFilter method.
		*/

		public function initialize( Controller $controller ) {
			$settings = $this->settings;
			$this->controller = $controller;
		}

		/**
		* Adresses non référencées depuis la table adressesfoyers
		*/

		public function adressesSansAdressesfoyers( $solve = false ) {
			$Adresse = ClassRegistry::init( 'Adresse' );

			$querydata = array(
				'conditions' => array(
					'Adresse.id NOT IN (
							SELECT DISTINCT( adressesfoyers.adresse_id )
								FROM adressesfoyers
						)'
				),
				'contain' => false
			);

			if( !$solve ) {
				return $Adresse->find( 'count', $querydata );
			}
			else {
				$Adresse->begin();

				$success = $Adresse->deleteAll( $querydata['conditions'] );

				if( $success ) {
					$Adresse->commit();
				}
				else {
					$Adresse->rollback();
				}

				return $success;
			}
		}

		/**
		* Prestations de même nature et de même rôle pour une personne donnée.
		*/

		public function prestationsMemeNatureEtMemeRole( $solve = false ) {
			$Prestation = ClassRegistry::init( 'Prestation' );

			$querydata = array(
				'conditions' => array(
					'Prestation.id IN (
						SELECT p1.id
							FROM prestations p1,
								prestations p2
							WHERE p1.id < p2.id
								AND p1.personne_id = p2.personne_id
								AND p1.natprest = p2.natprest
								AND p1.rolepers = p2.rolepers
					)'
				),
				'contain' => false
			);

			if( !$solve ) {
				return $Prestation->find( 'count', $querydata );
			}
			else {
				$success = true;
				$Prestation->begin();

				$list = $Prestation->find( 'list', $querydata );

				if( !empty( $list ) ) {
					$success = $Prestation->deleteAll( array( 'Prestation.id' => array_keys( $list ) ) ) && $success;
				}

				if( $success ) {
					$Prestation->commit();
				}
				else {
					$Prestation->rollback();
				}

				return $success;
			}
		}

		/**
		* Permet de savoir si une colonne d'un modèle donné a un index unique
		* FIXME: utiliser le modèle Gestionano
		*/

		protected function _columnUnique( $model, $column, $expectedname = null ) {
			$indexes = $model->getDataSource( $model->useDbConfig )->index( $model );

			$uniqueFound = false;

			foreach( $indexes as $name => $index ) {
				if( $index['unique'] && $index['column'] == $column ) {
					if( is_null( $expectedname ) || ( !is_null( $expectedname ) && ( $name == $expectedname ) ) ) {
						$uniqueFound = true;
					}
				}
			}

			return $uniqueFound;
		}

		/**
		* Fonction permettant de créer de nouvelles adresses lorsque plusieurs
		* adressesfoyers pointent vers la même adresse.
		*
		* INFO: création d'un index unique sur adressesfoyers
		*
		* - cg58_20110426_v2_1
		*	* environ 3 minutes / 3994
		*	* SELECT COUNT(*) FROM adressesfoyers; 25120 -> 25120
		*	* SELECT COUNT(*) FROM adresses; 21126 -> 25120
		* - cg66_20110225_v2_1
		*	* environ 10 minutes / 14079
		*	* SELECT COUNT(*) FROM adressesfoyers; 90137 -> 90137
		*	* SELECT COUNT(*) FROM adresses; 76058 -> 90137
		* - cg93_20110621_0359_v2_1
		*	* environ 32 minutes / 45284
		*	* SELECT COUNT(*) FROM adressesfoyers; 298367 -> 298367
		*	* SELECT COUNT(*) FROM adresses; 253083 -> 298367
		*/

		public function adressesPourPlusieursAdressesfoyers( $solve = false ) {
			$Adressefoyer = ClassRegistry::init( 'Adressefoyer' );
			$uniqueIndexFound = $this->_columnUnique( $Adressefoyer, 'adresse_id' );

			if( $uniqueIndexFound ) {
				return ( $solve ? true : 0 );
			}

			$querydata = array(
				'contain' => false,
				'conditions' => array(
					'Adressefoyer.id IN (
							SELECT a1.id
								FROM adressesfoyers AS a1,
									adressesfoyers AS a2
								WHERE
									a1.id <> a2.id
									AND a1.adresse_id = a2.adresse_id
						)'
				),
			);

			if( !$solve ) {
				return $Adressefoyer->find( 'count', $querydata );
			}
			else {
				$success = true;
				$Adressefoyer->begin();

				$adresses_ids = $Adressefoyer->find( 'all', array_merge( $querydata, array( 'fields' => array( 'Adressefoyer.adresse_id' ) ) ) );

				if( !empty( $adresses_ids ) ) {
					foreach( $adresses_ids AS $adresse_id ) {
						$adressesfoyers = $Adressefoyer->find(
							'all',
							array(
								'contain' => array(
									'Adresse'
								),
								'conditions' => array(
									'Adressefoyer.adresse_id' => $adresse_id['Adressefoyer']['adresse_id']
								),
								'order' => array(
									'Adressefoyer.adresse_id ASC',
									'Adressefoyer.foyer_id ASC',
									'Adressefoyer.rgadr ASC',
									'Adressefoyer.dtemm ASC'
								)
							)
						);

						//FIXME -> typevoie (vérifier si ce n'est que lui qui fait casser) est un champ obligatoire (CakePHP)
						$Adressefoyer->Adresse->validate = array();
						//FIXME -> rgadr et typeadr (vérifier si ce sont bien les deux qui font casser) sont des champs obligatoire (CakePHP)
						$Adressefoyer->validate = array();

						foreach( $adressesfoyers as $i => $adressefoyer ) {
							if( !empty( $adressefoyer['Adresse']['id'] ) && $i > 0 ) {
								$nouvelleAdresse = array( 'Adresse' => $adressefoyer['Adresse'] );
								unset( $nouvelleAdresse['Adresse']['id'] );

								$Adressefoyer->Adresse->create( $nouvelleAdresse );
								$success = $Adressefoyer->Adresse->save( null, array( 'atomic' => false ) ) && $success;

								if( !empty( $Adressefoyer->Adresse->id ) ) {
									$adressefoyerMaj = array( 'Adressefoyer' => $adressefoyer['Adressefoyer'] );
									$adressefoyerMaj['Adressefoyer']['adresse_id'] = $Adressefoyer->Adresse->id;

									$Adressefoyer->create( $adressefoyerMaj );
									$success = $Adressefoyer->save( null, array( 'atomic' => false ) ) && $success;
								}
								else {
									debug( $adressefoyer );
									debug( $nouvelleAdresse );
									debug( $Adressefoyer->Adresse->validationErrors );
									debug( $Adressefoyer->validationErrors );
								}
							}
							else if( empty( $adressefoyer['Adresse']['id'] ) ) {
								debug( $adressefoyer );
							}
						}
					}
				}

				if( $success ) {
					$count = $Adressefoyer->find( 'count', $querydata );

					if( $success ) {
						$Adressefoyer->commit();
					}
					else {
						$Adressefoyer->rollback();
					}
				}
				else {
					$Adressefoyer->rollback();
				}

				return $success;
			}
		}

		/**
		*
		*/

		public function plusieursFoyersPourUnDossier( $solve = false ) {
			$Foyer = ClassRegistry::init( 'Foyer' );

			$querydata = array(
				'contain' => false,
				'conditions' => array(
					'Foyer.id IN (
						SELECT f1.id
							FROM foyers AS f1,
								foyers AS f2
							WHERE
								f1.id <> f2.id
								AND f1.dossier_id = f2.dossier_id
							ORDER BY f1.ddsitfam ASC
					)'
				),
			);

			if( $this->_columnUnique( $Foyer, 'dossier_id' ) ) {
				return ( $solve ? true : 0 );
			}

			if( !$solve ) {
				return $Foyer->find( 'count', $querydata );
			}
		}

		/**
		* Suppression des adressesfoyers (et des adresses attachées) en doublon.
		* INFO: à passer obligatoirement après _copieAdressesPourAdressesfoyers
		* INFO: création de deux indexes uniques sur adressesfoyers
		*/

		public function adressesfoyersEnDoublon( $solve = false ) {
			$Adressefoyer = ClassRegistry::init( 'Adressefoyer' );

			$querydata = array(
				'contain' => false,
				'conditions' => array(
					'Adressefoyer.id IN (
						SELECT a1.id
							FROM adressesfoyers AS a1,
								adressesfoyers AS a2
							WHERE
								a1.id <> a2.id
								AND a1.foyer_id = a2.foyer_id
								AND a1.rgadr = a2.rgadr
							ORDER BY a1.dtemm ASC
					)'
				),
			);

			$uniqueIndexesFound = (
				$this->_columnUnique( $Adressefoyer, array( 'foyer_id', 'rgadr' ), 'adressesfoyers_actuelle_rsa_idx' )
				&& $this->_columnUnique( $Adressefoyer, array( 'foyer_id', 'rgadr' ), 'adressesfoyers_foyer_id_rgadr_idx' )
			);

			if( $uniqueIndexesFound ) {
				return ( $solve ? true : 0 );
			}

			if( !$solve ) {
				return $Adressefoyer->find( 'count', $querydata );
			}
			else {
				$success = true;
				$Adressefoyer->begin();

				// 1°) Lorsqu'une des dates d'emménagement est à NULL -> FIXME: dtemm champ obligatoire ?
				$list = $Adressefoyer->find(
					'list',
					array(
						'fields' => array(
							'Adressefoyer.id',
							'Adressefoyer.adresse_id',
						),
						'contain' => false,
						'conditions' => array(
							'Adressefoyer.id IN (
								SELECT a1.id
									FROM adressesfoyers AS a1,
										adressesfoyers AS a2
									WHERE
										a1.id <> a2.id
										AND a1.foyer_id = a2.foyer_id
										AND a1.rgadr = a2.rgadr
										AND a1.dtemm IS NULL
										AND a2.dtemm IS NOT NULL
									ORDER BY a1.dtemm ASC
							)'
						),
					)
				);

				if( !empty( $list ) ) {
					$success = $Adressefoyer->deleteAll( array( 'Adressefoyer.id' => array_keys( $list ) ) ) && $success;
					$success = $Adressefoyer->Adresse->deleteAll( array( 'Adresse.id' => array_values( $list ) ) ) && $success;
				}

				if( !$success ) {
					$Adressefoyer->rollback();
					return false;
				}

				// 2°) Lorsqu'aucune des dates d'emménagement n'est à NULL -> on garde la plus récente (FIXME: est-ce correct ?)
				$adressesfoyers = $Adressefoyer->find(
					'all',
					array(
						'contain' => false,
						'conditions' => array(
							'Adressefoyer.id IN (
								SELECT a1.id
									FROM adressesfoyers AS a1,
										adressesfoyers AS a2
									WHERE
										a1.id <> a2.id
										AND a1.foyer_id = a2.foyer_id
										AND a1.rgadr = a2.rgadr
									ORDER BY a1.dtemm ASC
							)'
						),
						'order' => array(
							'Adressefoyer.foyer_id ASC',
							'Adressefoyer.dtemm DESC'
						)
					)
				);

				$listeASupprimer = array();
				$foyer_id = null;
				if( !empty( $adressesfoyers ) ) {
					foreach( $adressesfoyers as $adressefoyer ) {
						if( $adressefoyer['Adressefoyer']['foyer_id'] != $foyer_id ) {
							$foyer_id = $adressefoyer['Adressefoyer']['foyer_id'];
						}
						else {
							$listeASupprimer[$adressefoyer['Adressefoyer']['id']] = $adressefoyer['Adressefoyer']['adresse_id'];
						}
					}
				}

				if( !empty( $listeASupprimer ) ) {
					$success = $Adressefoyer->deleteAll( array( 'Adressefoyer.id' => array_keys( $listeASupprimer ) ) ) && $success;
					$success = $Adressefoyer->Adresse->deleteAll( array( 'Adresse.id' => array_values( $listeASupprimer ) ) ) && $success;
				}
				
				if( $success ) {
					$Adressefoyer->commit();
				}
				else {
					$Adressefoyer->rollback();
				}

				return $success;
			}
		}

		/**
		 *
		 * @param AppModel $mainModel
		 * @return array
		 */
		public function associations( &$mainModel ) {
			$cacheKey = Inflector::underscore( Inflector::camelize( implode( '_', array( __CLASS__, __FUNCTION__, $mainModel->useDbConfig, $mainModel->alias ) ) ) );
			$associations = Cache::read( $cacheKey );

			if( $associations === false ) {
				$associations = array();

				foreach( $mainModel->getAssociated() as $assocName => $assocType ) {
					if( $assocName == 'Prestation' ) {
						$assocType = 'hasMany'; // FIXME
					}
					else if( $assocType == 'belongsTo' ) {
						$assocName = false;
					}
					else if( $assocType == 'hasAndBelongsToMany' ) {
						$assocName = $mainModel->hasAndBelongsToMany[$assocName]['with'];
						$assocType = 'hasMany';
					}

					if( !empty( $assocName ) ) {
						$associations[$assocName] = $assocType;
					}
				}

				Cache::write( $cacheKey, $associations );
			}

			return $associations;
		}

		/**
		 *
		 * @param string $name
		 * @return boolean
		 */
		public function tablePourCg( $name ) { // FIXME: dans AppModel ?
			return ( !preg_match( '/[0-9]{2}$/', $name ) || preg_match( '/'.Configure::read( 'Cg.departement' ).'$/', $name ) );
		}

		/**
		* Merges a mixed set of string/array conditions
		*
		* @return array
		*/

		protected function _mergeConditions( $query, $assoc ) {
			if( empty( $assoc ) ) {
				return $query;
			}

			if (is_array($query)) {
				return array_merge((array)$assoc, $query);
			}

			if (!empty($query)) {
				$query = array($query);
				if (is_array($assoc)) {
					$query = array_merge($query, $assoc);
				} else {
					$query[] = $assoc;
				}
				return $query;
			}

			return $assoc;
		}

		/**
		 *
		 * @param AppModel $mainModel
		 * @return array
		 */
		public function assocConditions( &$mainModel ) {
			$cacheKey = Inflector::underscore( Inflector::camelize( implode( '_', array( __CLASS__, __FUNCTION__, $mainModel->useDbConfig, $mainModel->alias ) ) ) );
			$return = Cache::read( $cacheKey );
			if( $return === false ) {
				$dbo = $mainModel->getDataSource( $mainModel->useDbConfig );
				$sq = $dbo->startQuote;
				$eq = $dbo->startQuote;

				$return = array();
				foreach( $this->associations( $mainModel ) as $assocName => $assocType ) {
					if( !empty( $assocName ) ) {
						if( $this->tablePourCg( $assocName ) ) {
							if( $assocType == 'hasMany' && !isset( $mainModel->{$assocType}[$assocName] ) ) { // FIXME
								$assoc = array(
									'className' => $assocName,
									'foreignKey' => Inflector::underscore( $mainModel->alias ).'_id',
									'dependent' => 1,
									'conditions' => null,
									'fields' => null,
									'order' => null,
									'limit' => null,
									'offset' => null,
									'exclusive' => null,
									'finderQuery' => null,
									'counterQuery' => null
								);
							}
							else {
								$assoc = $mainModel->{$assocType}[$assocName];
							}

							$conditions = $dbo->getConstraint(
								'belongsTo',
								$mainModel->{$assocName},
								$mainModel,
								$mainModel->alias,
								$assoc
							);
							$conditions = $this->_mergeConditions(
								$assoc['conditions'],
								$conditions
							);
							$conditions = $dbo->conditions( $conditions, true, false );

							$conditions = str_replace(
								'{$__cakeID__$}',
								"{$sq}{$mainModel->alias}{$eq}.{$sq}{$mainModel->primaryKey}{$eq}",
								$conditions
							);

							$return[$assocName] = (array)$conditions;
						}
					}
				}
				ksort( $return );

				Cache::write( $cacheKey, $return );
			}

			return $return;
		}

		/**
		 *
		 * @param boolean $solve
		 * @return mixed
		 */
		public function personnesSansPrestationSansEntreeMetier( $solve = false ) {
			$Personne = ClassRegistry::init( 'Personne' );
			$assocConditions = $this->assocConditions( $Personne );

			$modelesCafAGarder = array( 'Activite', 'Conditionactiviteprealable', 'Dsp', 'Grossesse', 'Orientation', 'Parcours', 'Suiviappuiorientation', 'Titresejour' );

			$personneConditions = array( 'Personne.id NOT IN ( SELECT prestations.personne_id FROM prestations WHERE prestations.personne_id = Personne.id )' );//FIXME

			if( !empty( $assocConditions ) ) {
				foreach( $assocConditions as $linkedModelName => $conditions ) {
					$conditions = (array) $conditions;

					$fields = array();

					if( $this->tablePourCg( $linkedModelName ) && ( !$Personne->{$linkedModelName}->inModule( 'caf' ) || in_array( $linkedModelName, $modelesCafAGarder ) ) ) {
						if( $linkedModelName == 'Orientstruct' ) {
							$conditions["{$linkedModelName}.statut_orient"] = 'Orienté';
						}
						$autreTable = Inflector::tableize( $linkedModelName );
						$sq = $Personne->{$linkedModelName}->sq(
								array(
									'alias' => '',
									'fields' => array( 'COUNT(*)' ),
									'contain' => false,
									'conditions' => $conditions
								)
						);
						$sq = preg_replace( "/(?<!\.)(?<!\w)({$linkedModelName})(?!\w)/", $autreTable, $sq );

						$personneConditions["( {$sq} )"] = 0;
					}
				}
			}

			$querydata = array(
				'conditions' => $personneConditions,
				'contain' => false
			);

			if( !$solve ) {
				return $Personne->find( 'count', $querydata );
			}
			else {
				$success = true;
				$Personne->begin();

				$list = $Personne->find( 'list', $querydata );

				if( !empty( $list ) ) {
					$success = $Personne->deleteAll( array( 'Personne.id' => array_keys( $list ) ) ) && $success;
				}

				if( $success ) {
					$Personne->commit();
				}
				else {
					$Personne->rollback();
				}

				return $success;
			}
		}

		/**
		* The beforeRedirect method is invoked when the controller's redirect method
		* is called but before any further action. If this method returns false the
		* controller will not continue on to redirect the request.
		* The $url, $status and $exit variables have same meaning as for the controller's method.
		*/

		public function beforeRedirect( Controller $controller, $url, $status = null, $exit = true ) {
			parent::beforeRedirect( $controller, $url, $status , $exit );
		}
	}
?>