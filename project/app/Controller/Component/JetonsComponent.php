<?php
	/**
	 * Fichier source de la classe JetonsComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe JetonsComponent ...
	 *
	 * @package app.Controller.Component
	 */
    class JetonsComponent extends Component
    {
        public $components = array( 'Session' );
        protected $_userId;


        /**
        *	The initialize method is called before the controller's beforeFilter method.
        */

		public function initialize( Controller $controller ) {
			$settings = $this->settings;
			$this->controller = $controller;

			if( Configure::read( 'Jetons.disabled' ) ) {
				return;
			}

			$this->Jeton = ClassRegistry::init( 'Jeton' );
		}

		/**
		* Retourn l'id d'un dossier à partir des paramètres ($params = dossier_id, 'Personne.id', 'Dossier.id')
		*/

		protected function _dossierId( $params = array() ) {
			if( !is_array( $params ) ) {
				return $params;
			}
			else {
				if( array_key_exists( 'Personne.id', $params ) ) {
					$this->Personne = ClassRegistry::init( 'Personne' );
					$personne = $this->Personne->find(
						'first',
						array(
							'conditions' => array(
								'Personne.id' => $params['Personne.id']
							),
							'contain' => array(
								'Foyer'
							)
						)
					);
					$this->controller->assert( !empty( $personne ), 'invalidParamForToken' );
					return $personne['Foyer']['dossier_id'];
				}
				else if( array_key_exists( 'Dossier.id', $params ) ) {
					return $params['Dossier.id'];
				}
			}
		}

		/**
		* Retourne l'instant pivot en-dessous duquel les connections sont
		* considérées comme étant expirées.
		*/

		protected function _timeoutThreshold() {
			return strftime( '%Y-%m-%d %H:%M:%S', strtotime( '-'.Configure::read( 'Jetons.duree' ).' seconds' ) );
		}

		/**
		*
		*/

		protected function _clean() {
			$count = $this->Jeton->find(
				'count',
				array(
					'conditions' => array( 'Jeton.modified <' => $this->_timeoutThreshold() ),
					'recursive' => -1
				)
			);

			if( $count > 0 ) {
				$this->_lock();
				return $this->Jeton->deleteAll(
					array(
						'Jeton.modified <' => $this->_timeoutThreshold()
					)
				);
			}

			return false;
		}

		/**
		* FIXME: en faire une sous-requête
		*/

		public function ids() {
			if( Configure::read( 'Jetons.disabled' ) ) {
				return array();
			}

			$jetons = $this->Jeton->find(
				'list',
				array(
					'fields' => array(
						'Jeton.dossier_id',
						'Jeton.dossier_id'
					),
					'conditions' => array(
						'NOT' => array(
							'Jeton.php_sid' => $this->Session->id(),
							'Jeton.user_id' => $this->Session->read( 'Auth.User.id' )
						)
					),
					'recursive' => -1
				)
			);

			return $jetons;
		}

		/**
		* Retourne une sous-requête permettant d'exclure les dossiers non visibles
		* par l'utilisateur connecté. Si les jetons ne sont pas utilisés, un array
		* vide est retourné.
		*
		* @return mixed
		*/
		public function sqIds() {
			if( Configure::read( 'Jetons.disabled' ) ) {
				return array();
			}

			return $this->Jeton->sq(
				array(
					'alias' => 'jetons',
					'fields' => array(
						'jetons.dossier_id',
					),
					'conditions' => array(
						'NOT' => array(
							'jetons.php_sid' => $this->Session->id(),
							'jetons.user_id' => $this->Session->read( 'Auth.User.id' )
						)
					),
					'recursive' => -1
				)
			);
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
						'jetons.dossier_id = Dossier.id'
					),
					'recursive' => -1
				)
			);

			$sq = "( \"Dossier\".\"id\" IN ( {$sq} ) )";

			if( !empty( $fieldName ) ) {
				$sq = "{$sq} AS \"Dossier__locked\"";
			}

			return $sq;
		}

		/**
		*
		*/

		public function check( $params ) {
			if( Configure::read( 'Jetons.disabled' ) ) {
				return true;
			}

			$dossier_id = $this->_dossierId( $params );

			$this->_clean();

			$jeton = $this->Jeton->find(
				'first',
				array(
					'conditions' => array(
						'Jeton.dossier_id'  => $dossier_id,
						'and NOT' => array(
							'Jeton.php_sid' => $this->Session->id(),
							'Jeton.user_id' => $this->Session->read( 'Auth.User.id' )
						)
					),
					'recursive' => -1
				)
			);

			if( !empty( $jeton ) ) {
				$lockingUser = $this->Jeton->User->find(
					'first',
					array(
						'conditions' => array(
							'User.id' => $jeton['Jeton']['user_id']
						),
						'recursive' => -1
					)
				);
				$this->controller->assert( !empty( $lockingUser ), 'invalidParamForToken' );
				$this->controller->cakeError(
					'lockedDossier',
					array(
						'time' => ( strtotime( $jeton['Jeton']['modified'] ) + Configure::read( 'Jetons.duree' )() ),
						'user' => $lockingUser['User']['username']
					)
				); // FIXME: paramètres ?
			}

			return empty( $jeton );
		}

		/**
		* Retourne vrai si le dossier est locké
		*/

		public function locked( $params ) {
			if( Configure::read( 'Jetons.disabled' ) ) {
				return false;
			}

			$dossier_id = $this->_dossierId( $params );

			$count = $this->Jeton->find(
				'count',
				array(
					'conditions' => array(
						'Jeton.dossier_id'  => $dossier_id,
						'and NOT' => array(
							'Jeton.php_sid' => $this->Session->id(),
							'Jeton.user_id' => $this->Session->read( 'Auth.User.id' )
						),
						'Jeton.modified >=' => $this->_timeoutThreshold()
					),
					'recursive' => -1
				)
			);

			return !empty( $count );
		}

		/**
		* Retourne vrai si les dossiers sont lockés
		*/

		public function lockedList( $dossiers_ids ) {
			if( Configure::read( 'Jetons.disabled' ) ) {
				return array();
			}

			if( empty( $dossiers_ids ) ) {
				return array();
			}

			$list = $this->Jeton->find(
				'list',
				array(
					'fields' => array( 'Jeton.id', 'Jeton.dossier_id' ),
					'conditions' => array(
						'Jeton.dossier_id'  => $dossiers_ids,
						'and NOT' => array(
							'Jeton.php_sid' => $this->Session->id(),
							'Jeton.user_id' => $this->Session->read( 'Auth.User.id' )
						),
						'Jeton.modified >=' => $this->_timeoutThreshold()
					),
					'recursive' => -1
				)
			);

			return $list;
		}


		/**
		* Obtient un jeton sur un dossier
		*/

		public function get( $params ) {
			if( Configure::read( 'Jetons.disabled' ) ) {
				return true;
			}

			$this->_lock();
			$dossier_id = $this->_dossierId( $params );

			if( $this->check( $params ) ) {
				$jeton = array(
					'Jeton' => array(
						'dossier_id'    => $dossier_id,
						'php_sid'   => $this->Session->id(),
						'user_id'   => $this->Session->read( 'Auth.User.id' )
					)
				);

				$vieuxJeton = $this->Jeton->find(
					'first',
					array(
						'conditions' => array(
							'Jeton.dossier_id'  => $dossier_id,
							'Jeton.php_sid' => $this->Session->id(),
							'Jeton.user_id' => $this->Session->read( 'Auth.User.id' )
						),
						'recursive' => -1
					)
				);

				if( !empty( $vieuxJeton ) ) {
					$jeton['Jeton']['id'] = $vieuxJeton['Jeton']['id'];
					$jeton['Jeton']['created'] = $vieuxJeton['Jeton']['created'];
				}

				$this->Jeton->create( $jeton );
				return ( $this->Jeton->save( null, array( 'atomic' => false ) ) !== false );
			}
			else {
				return false;
			}
		}

		/**
		* Supprime le jeton sur un dossier
		*/

		public function release( $params ) {
			if( Configure::read( 'Jetons.disabled' ) ) {
				return true;
			}

			$dossier_id = $this->_dossierId( $params );

			$this->_lock();

			//Call Recours Gracieux update pour mettre l'état à jour
			$Dossiermodifie= ClassRegistry::init('Dossiermodifie');
			$modified = $Dossiermodifie->setModified( array('dossier_id'=>array($dossier_id )) );

			return $modified && $this->Jeton->deleteAll(
				array(
					'Jeton.dossier_id'    => $dossier_id,
					'Jeton.php_sid'   => $this->Session->id(),
					'Jeton.user_id'   => $this->Session->read( 'Auth.User.id' )
				)
			);
		}

		/**
		*
		*/

		public function checkList( $params ) {
			if( Configure::read( 'Jetons.disabled' ) ) {
				return true;
			}

			$this->_clean();

			$jetons = $this->Jeton->find(
				'all',
				array(
					'conditions' => array(
						'Jeton.dossier_id'  => $params,
						'and NOT' => array(
							'Jeton.php_sid' => $this->Session->id(),
							'Jeton.user_id' => $this->Session->read( 'Auth.User.id' )
						)
					),
					'recursive' => -1
				)
			);

			if( !empty( $jetons ) ) {
				$jeton = $jetons[0];
				$lockingUser = $this->Jeton->User->find(
					'first',
					array(
						'conditions' => array(
							'User.id' => $jeton['Jeton']['user_id']
						),
						'recursive' => -1
					)
				);
				$this->controller->assert( !empty( $lockingUser ), 'invalidParamForToken' );
				$this->controller->cakeError(
					'lockedDossier',
					array(
						'time' => ( strtotime( $jeton['Jeton']['modified'] ) + Configure::read( 'Jetons.duree' )() ),
						'user' => $lockingUser['User']['username']
					)
				); // FIXME: paramètres ?
			}

			return empty( $jetons );
		}

		/**
		* Obtient un jeton sur un ensemble de dossiers
		*/

		public function getList( $params ) {
			if( Configure::read( 'Jetons.disabled' ) ) {
				return true;
			}

			$this->_lock();

			if( $this->checkList( $params ) ) {
				$success = true;
				foreach( $params as $dossier_id ) {
					$jeton = array(
						'Jeton' => array(
							'dossier_id'    => $dossier_id,
							'php_sid'   => $this->Session->id(),
							'user_id'   => $this->Session->read( 'Auth.User.id' )
						)
					);

					$vieuxJeton = $this->Jeton->find(
						'first',
						array(
							'conditions' => array(
								'Jeton.dossier_id'  => $dossier_id,
								'Jeton.php_sid' => $this->Session->id(),
								'Jeton.user_id' => $this->Session->read( 'Auth.User.id' )
							),
							'recursive' => -1
						)
					);

					if( !empty( $vieuxJeton ) ) {
						$jeton['Jeton']['id'] = $vieuxJeton['Jeton']['id'];
						$jeton['Jeton']['created'] = $vieuxJeton['Jeton']['created'];
					}

					$this->Jeton->create( $jeton );
					$success = ( $this->Jeton->save( null, array( 'atomic' => false ) ) !== false ) && $success;
				}
				return $success;
			}
			else {
				return false;
			}
		}

		/**
		* Supprime le jeton sur un ensemble de dossiers
		*/

		public function releaseList( $params ) {
			if( Configure::read( 'Jetons.disabled' ) ) {
				return true;
			}

			$this->_lock();

			return $this->Jeton->deleteAll(
				array(
					'Jeton.dossier_id'    => $params,
					'Jeton.php_sid'   => $this->Session->id(),
					'Jeton.user_id'   => $this->Session->read( 'Auth.User.id' )
				)
			);
		}

		/**
		* Crée un verrou sur la table des jetons
		*
		* MODE						-> 										-> modif personne	-> cohorte
		**************************************************************************************************
		* ACCESS SHARE				-> transactions entremélées				-> 4 form			-> X
		* ROW SHARE					-> transactions entremélées				->
		* ROW EXCLUSIVE			    -> transactions entremélées				-> 4 form			-> X
		* SHARE UPDATE EXCLUSIVE	-> 1 transaction puis l'autre			->
		* SHARE						-> transactions entremélées, deadlock	->
		* SHARE ROW EXCLUSIVE		-> 1 transaction puis l'autre			-> 1 form, 3 401	-> 1 form, 3 401
		* EXCLUSIVE					-> 1 transaction puis l'autre			->
		* ACCESS EXCLUSIVE			-> 1 transaction puis l'autre			->
		*/

		public function _lock() {
			$sql = 'LOCK TABLE "jetons" IN SHARE ROW EXCLUSIVE MODE;';
			$this->Jeton->query( $sql );
		}

		/**
		* The beforeRedirect method is invoked when the controller's redirect method
		* is called but before any further action. If this method returns false the
		* controller will not continue on to redirect the request.
		* The $url, $status and $exit variables have same meaning as for the controller's method.
		**/

		public function beforeRedirect( Controller $controller, $url, $status = null, $exit = true ) {
			parent::beforeRedirect( $controller, $url, $status , $exit );
			return $url;
		}
	}
?>
