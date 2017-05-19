<?php
	/**
	 * Fichier source de la classe Jetons2Component.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Jetons2Component permet de mettre des jetons (des locks fonctionnels) sur des
	 * enregistrements de la table dossiers pour un utilisateur particulier.
	 *
	 * @package app.Controller.Component
	 */
	class Jetons2Component extends Component
	{
		/**
		 * Controller using this component.
		 *
		 * @var Controller
		 */
		public $Controller = null;

		/**
		 * On a besoin d'un esession.
		 *
		 * @var array
		 */
		public $components = array( 'Session' );

		/**
		 * On initialise le modèle Jeton si Configure::write( 'Jetons2.disabled' ) n'est pas à true.
		 *
		 * @param Controller $controller Controller with components to initialize
		 * @return void
		 */
		public function initialize( Controller $controller ) {
			$settings = $this->settings;
			parent::initialize( $controller, $settings );
			$this->Controller = $controller;

			$this->Jeton = ClassRegistry::init( 'Jeton' );
		}

		/**
		 * On essaie d'acquérir un (ensemble de) jeton(s) pour l'utilisateur connecté au sein d'une transaction.
		 *
		 * Si un jeton est locké par un autre utilisateur, on annule la transaction et on en informe l'utilisateur
		 * via ue page d'erreur.
		 *
		 * @param mixed $dossiers Un id de dossier ou un array d'ids de dossiers.
		 * @return boolean
		 */
		public function get( $dossiers ) {
			if( Configure::read( 'Jetons2.disabled' ) ) {
				return true;
			}

			$dossiers = array_unique( (array) $dossiers );
			if( empty( $dossiers ) ) {
				return true;
			}

			$this->Jeton->begin();

			$sq = $this->Jeton->sq(
				array(
					'alias' => 'jetons',
					'fields' => array(
						'jetons.id',
						'jetons.dossier_id',
						'jetons.php_sid',
						'jetons.user_id',
						'jetons.modified',
					),
					'conditions' => array(
						'dossier_id'  => $dossiers,
						// INFO: si on get et que l'on release dans la même page, il ne fera pas le second
						// SELECT même si cacheQueries est à false.
						'( \''.microtime( true ).'\' IS NOT NULL )'
					),
					'recursive' => -1
				)
			);

			$sq = "{$sq} FOR UPDATE";
			$results =@$this->Jeton->query( $sq );

			if( $results === false ) {
				$this->Jeton->rollback();
				$this->Controller->cakeError( 'error500' );
				return;
			}

			$jetonsObtenus = Set::combine( $results, '{n}.jetons.dossier_id', '{n}.jetons' );

			foreach( $dossiers as $dossier ) {
				$jetonObtenu = ( isset( $jetonsObtenus[$dossier] ) ? $jetonsObtenus[$dossier] : null );

				$dossierNonVerrouille = (
					is_null( $jetonObtenu )
					|| empty( $jetonObtenu['php_sid'] )
					|| $jetonObtenu['php_sid'] ==  $this->Session->id()
					|| ( strtotime( $jetonObtenu['modified'] ) < strtotime( '-'.readTimeout().' seconds' ) )
				);

				if( $dossierNonVerrouille ) {
					$jeton = array(
						'Jeton' => array(
							'dossier_id' => $dossier,
							'php_sid' => $this->Session->id(),
							'user_id' => $this->Session->read( 'Auth.User.id' ),
						)
					);

					if( !empty( $jetonObtenu ) && !empty( $jetonObtenu['id'] ) ) {
						$jeton['Jeton']['id'] = $jetonObtenu['id'];
					}

					$this->Jeton->create( $jeton );
					if( !$this->Jeton->save() ) {
						$this->Jeton->rollback();
						$this->Controller->cakeError( 'error500' );
						// return
					}
				}
				else {
					$this->Jeton->rollback();

					$lockingUser = $this->Jeton->User->find(
						'first',
						array(
							'conditions' => array(
								'User.id' => $jetonObtenu['user_id']
							),
							'recursive' => -1
						)
					);

					$dossier = $this->Jeton->Dossier->find(
						'first',
						array(
							'conditions' => array(
								'Dossier.id' => $dossier
							),
							'recursive' => -1
						)
					);

					throw new LockedDossierException(
						'Dossier verrouillé',
						401,
						array(
							'time' => ( strtotime( $jetonObtenu['modified'] ) + readTimeout() ),
//							'user' => $lockingUser['User']['username'],
                            'user' => $lockingUser['User']['nom'].' '.$lockingUser['User']['prenom'],
							'dossier' => $dossier
						)
					);
					return;
				}
			}

			$this->Jeton->commit();

			return true;
		}

		/**
		 * On relache un (ensemble de) jeton(s).
		 *
		 * @param mixed $dossiers Un id de dossier ou un array d'ids de dossiers.
		 * @return boolean
		 */
		public function release( $dossiers ) {
			if( Configure::read( 'Jetons2.disabled' ) ) {
				return true;
			}

			$dossiers = array_unique( (array) $dossiers );
			if( empty( $dossiers ) ) {
				return true;
			}

			$this->Jeton->begin();

			$conditions = array( 'dossier_id'  => $dossiers );

			$sq = $this->Jeton->sq(
				array(
					'alias' => 'jetons',
					'fields' => array(
						'jetons.id',
						'jetons.dossier_id',
						'jetons.php_sid',
						'jetons.user_id',
						'jetons.modified',
					),
					// INFO: si on get et que l'on release dans la même page, il ne fera pas le second
					// SELECT même si cacheQueries est à false.
					'conditions' => $conditions + array( '( \''.microtime( true ).'\' IS NOT NULL )' ),
					'recursive' => -1
				)
			);

			$sq = "{$sq} FOR UPDATE";

			$results =@$this->Jeton->query( $sq );
			if( $results === false ) {
				$this->Jeton->rollback();
				die( 'Erreur étrange' );
				return false;
			}

			if( $this->Jeton->deleteAll( $conditions, false, false ) == false ) {
				$this->Jeton->rollback();
				die( 'Erreur étrange' );
				return false;
			}

			$this->Jeton->commit();

			return true;
		}

		/**
		 * Retourne une condition concernant l'instant pivot en-dessous duquel les connections sont
		 * considérées comme étant expirées.
		 *
		 * @return string
		 */
		protected function _conditionsValid() {
			return array( 'modified >=' => strftime( '%Y-%m-%d %H:%M:%S', strtotime( '-'.readTimeout().' seconds' ) ) );
		}

		/**
		 * Retourne une sous-reqûete permettant de savoir si le Dossier est locké.
		 *
		 * @param string $modelAlias Alias du modèle Dossier
		 * @param string $fieldName Si non null, alors la sous-reqête est aliasée
		 *	pour utiliser dans l'attribut 'fields' d'un querydata.
		 * @return string
		 */
		public function sqLocked( $modelAlias = 'Dossier', $fieldName = null ) {
			if( Configure::read( 'Jetons2.disabled' ) ) {
				$sq = "( 0 = 1 )";
			}
			else {
				$sq = $this->Jeton->sq(
					array(
						'alias' => 'jetons',
						'fields' => array(
							'jetons.dossier_id',
						),
						'conditions' => array(
							'NOT' => array(
								'jetons.php_sid' => $this->Session->id(),
								'jetons.user_id' => $this->Session->read( 'Auth.User.id' )
							),
							$this->_conditionsValid(),
							'jetons.dossier_id = Dossier.id'
						),
						'recursive' => -1
					)
				);

				$sq = "( \"{$modelAlias}\".\"id\" IN ( {$sq} ) )";
			}

			if( !empty( $fieldName ) ) {
				$sq = "{$sq} AS \"Dossier__locked\"";
			}

			return $sq;
		}

		/**
		 * Retourne une sous-requête permettant de connaître le login de
		 * l'utilisateur qui verrouille un Dossier.
		 *
		 * @param string $modelAlias Alias du modèle pour le champ virtuel
		 * @param string $fieldName Nom du champ pour le champ virtuel
		 * @return string
		 */
		public function sqLockingUser( $modelAlias = 'Dossier', $fieldName = 'locking_user' ) {
			if( Configure::read( 'Jetons2.disabled' ) ) {
				$sq = "( NULL )";
			}
			else {
				$sq = $this->Jeton->sq(
					array(
						'alias' => 'jetons',
						'fields' => array(
							'users.username'
						),
						'joins' => array(
							array_words_replace(
								$this->Jeton->join( 'User', array( 'LEFT OUTER' ) ),
								array(
									'Jeton' => 'jetons',
									'User' => 'users',
								)
							)
						),
						'conditions' => array(
							'NOT' => array(
								array(
									'jetons.php_sid' => $this->Session->id(),
									'jetons.user_id' => $this->Session->read( 'Auth.User.id' )
								),
								'NOT' => $this->_conditionsValid()
							),
							'jetons.dossier_id = Dossier.id'
						),
						'recursive' => -1
					)
				);
			}

			$sq = "( {$sq} ) AS \"{$modelAlias}__{$fieldName}\"";

			return $sq;
		}

		/**
		 * Retourne une sous-requête permettant de savoir si je suis l'utilisateur
		 * qui possède un jeton sur le dossier en question.
		 *
		 * @param string $modelAlias Alias du modèle pour le champ virtuel
		 * @param string $fieldName Nom du champ pour le champ virtuel
		 * @return string
		 */
		public function sqLockerIsMe( $modelAlias = 'Dossier', $fieldName = 'locker_is_me' ) {
			if( Configure::read( 'Jetons2.disabled' ) ) {
				$sq = "( FALSE )";
			}
			else {
				$sq = $this->Jeton->sq(
					array(
						'alias' => 'jetons',
						'fields' => array(
							'users.username',
						),
						'joins' => array(
							array_words_replace(
								$this->Jeton->join( 'User', array( 'LEFT OUTER' ) ),
								array(
									'Jeton' => 'jetons',
									'User' => 'users',
								)
							)
						),
						'conditions' => array(
							array(
								array(
									'jetons.php_sid' => $this->Session->id(),
									'jetons.user_id' => $this->Session->read( 'Auth.User.id' )
								),
								$this->_conditionsValid()
							),
							'jetons.dossier_id = Dossier.id'
						),
						'recursive' => -1
					)
				);
			}

			$sq = "( ( {$sq} ) IS NOT NULL ) AS \"{$modelAlias}__{$fieldName}\"";

			return $sq;
		}

		/**
		 * Retourne une sous-requête permettant de connaître le moment maximum
		 * théorique de verrouillage d'un Dossier.
		 *
		 * @param string $modelAlias
		 * @param string $fieldName
		 * @return string
		 */
		public function sqLockedTo( $modelAlias = 'Dossier', $fieldName = 'locked_to' ) {
			if( Configure::read( 'Jetons2.disabled' ) ) {
				$sq = "( NULL )";
			}
			else {
				$sq = $this->Jeton->sq(
					array(
						'alias' => 'jetons',
						'fields' => array(
							'( "jetons"."modified" + INTERVAL \''.readTimeout().' seconds\' ) AS "jetons__locked_to"',
						),
						'conditions' => array(
							'NOT' => array(
								array(
									'jetons.php_sid' => $this->Session->id(),
									'jetons.user_id' => $this->Session->read( 'Auth.User.id' )
								),
								'NOT' => $this->_conditionsValid()
							),
							'jetons.dossier_id = Dossier.id'
						),
						'recursive' => -1
					)
				);
			}

			$sq = "( {$sq} ) AS \"{$modelAlias}__{$fieldName}\"";

			return $sq;
		}

		/**
		 * Retourne des parties de querydata (les clés 'fields' et 'joins')
		 * permettant de compléter un querydata avec les informations de dossiers
		 * lockés, appliqué sur le modèle Dossier ou contenant une jointure sur
		 * le modèle Dossier.
		 *
		 * @todo permettre d'aliaser les champs / les modèles pour pouvoir
		 * l'utiliser ailleurs que dans Dossier::menu(), comme par exemple dans
		 * CohortesController::_index() / Cohorte::search().
		 *
		 * Cette méthode permettra de remplacer à terme les méthodes suivantes:
		 *	- Jetons2::sqLocked()
		 *	- Jetons2::sqLockingUser()
		 *	- Jetons2::sqLockedTo()
		 *
		 * @return array
		 */
		public function qdLockParts() {
			$querydata = array(
				'joins' => array(
					$this->Jeton->Dossier->join(
						'Jeton',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'NOT' => array(
									array(
										'Jeton.php_sid' => $this->Session->id(),
										'Jeton.user_id' => $this->Session->read( 'Auth.User.id' )
									),
									'NOT' => $this->_conditionsValid()
								)
							)
						)
					),
					$this->Jeton->join( 'User', array( 'type' => 'LEFT OUTER' ) )
				)
			);

			if( Configure::read( 'Jetons2.disabled' ) ) {
				$querydata['fields'] = array(
					'( FALSE ) AS "Dossier__locked"',
					'( NULL ) AS "Dossier__locking_user"',
					'( NULL ) AS "Dossier__locked_to"',
					'( FALSE ) AS "Dossier__locker_is_me"',
				);
			}
			else {
				$querydata['fields'] = array(
					'( "Jeton"."dossier_id" IS NOT NULL ) AS "Dossier__locked"',
//					'"User"."username" AS "Dossier__locking_user"',
                    '( "User"."nom" || \' \' || "User"."prenom" ) AS "Dossier__locking_user"',
					'( "Jeton"."modified" + INTERVAL \''.readTimeout().' seconds\' ) AS "Dossier__locked_to"',
					$this->sqLockerIsMe()
				);
			}

			return $querydata;
		}

		// TODO
		/*public function clean() {

		}*/

		/**
		 * Called before Controller::redirect().  Allows you to replace the url that will
		 * be redirected to with a new url. The return of this method can either be an array or a string.
		 *
		 * If the return is an array and contains a 'url' key.  You may also supply the following:
		 *
		 * - `status` The status code for the redirect
		 * - `exit` Whether or not the redirect should exit.
		 *
		 * If your response is a string or an array that does not contain a 'url' key it will
		 * be used as the new url to redirect to.
		 *
		 * @param Controller $controller Controller with components to beforeRedirect
		 * @param string|array $url Either the string or url array that is being redirected to.
		 * @param integer $status The status code of the redirect
		 * @param boolean $exit Will the script exit.
		 * @return array|null Either an array or null.
		 * @link @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::beforeRedirect
		 */
		public function beforeRedirect( Controller $controller, $url, $status = null, $exit = true ) {
			return array( 'url' => $url, 'status' => $status, 'exit' => $exit );
		}
		
		/**
		 * Permet d'obtenir le nombre de jetons qu'un utilisateur possède
		 * Dans le cas d'un multilogin, on précise le php_sid
		 * 
		 * @return int
		 */
		public function count() {
			if( Configure::read( 'Jetons2.disabled' ) ) {
				return 0;
			}
			
			$query = array(
				'conditions' => array(
					'user_id' => $this->Session->read( 'Auth.User.id' )
				),
				'contain' => false
			);
			
			if( Configure::read( 'Utilisateurs.multilogin' ) ) {
				$query['conditions']['php_sid'] = $this->Session->id();
			}
			
			$result = ClassRegistry::init('Jeton')->find('count', $query);
			
			return $result;
		}
		
		/**
		 * Supprime tout les jetons d'un utilisateur
		 * Dans le cas d'un multilogin, on précise le php_sid
		 * 
		 * @return boolean
		 */
		public function deleteJetons() {
			if( Configure::read( 'Jetons2.disabled' ) ) {
				return true;
			}
			$conditions = array('Jeton.user_id' => $this->Session->read( 'Auth.User.id' ));

			if ( Configure::read( 'Utilisateurs.multilogin' ) ) {
				$conditions['php_sid'] = $this->Session->id();
			}
			
			return ClassRegistry::init('Jeton')->deleteAll($conditions);
		}
	}
?>