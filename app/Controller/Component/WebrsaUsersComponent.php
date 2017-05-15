<?php
	/**
	 * Code source de la classe WebrsaUsersComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe WebrsaUsersComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaUsersComponent extends Component
	{

		/**
		 * Paramètres de ce component
		 *
		 * @var array
		 */
		public $settings = array();

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array(
			'Session'
		);

		/**
		 * Chargement des données liées à un référent dans la session.
		 *
		 * @param integer $referent_id
		 * @throws RuntimeException
		 */
		protected function _loadReferent( $referent_id ) {
			$Controller = $this->_Collection->getController();
			$Controller->loadModel( 'User' );

			if( false === $this->Session->check( 'Auth.Referent' ) && !empty( $referent_id ) ) {
				$query = array(
					'conditions' => array(
						'Referent.id' => $referent_id,
						'Referent.actif' => 'O'
					),
					'fields' => null,
					'order' => null,
					'contain' => false
				);

				$result = $Controller->User->Referent->find( 'first', $query );

				if( empty( $result ) ) {
					$msgstr = sprintf( 'Impossible de charger le référent actif d\'id %d', $referent_id );
					throw new RuntimeException( $msgstr, 500 );
				}

				$this->Session->write( 'Auth.Referent', $result['Referent'] );
				$this->_loadStructurereferente( (array)$result['Referent']['structurereferente_id'] );
			}
		}

		/**
		 * Si l'utilisateur est lié à une ou plusieurs structures référentes.
		 *
		 * @param array|integer $structuresreferentes_ids
		 * @throws RuntimeException
		 */
		protected function _loadStructurereferente( $structuresreferentes_ids ) {
			$Controller = $this->_Collection->getController();
			$Controller->loadModel( 'User' );
			$structuresreferentes_ids = (array)$structuresreferentes_ids;

			if( false === $this->Session->check( 'Auth.Structurereferente' ) ) {
				if( !empty( $structuresreferentes_ids ) ) {
					$query = array(
						'conditions' => array(
							'Structurereferente.id' => $structuresreferentes_ids
						),
						'fields' => null,
						'order' => null,
						'contain' => array(
							'Zonegeographique'
						)
					);

					$results = $Controller->User->Structurereferente->find( 'all', $query );

					if( count( $results ) !== count( $structuresreferentes_ids ) ) {
						$msgstr = sprintf( 'Impossible de charger l\'ensemble des structures référentes d\'ids %s', implode( ', ', $structuresreferentes_ids ) );
						throw new RuntimeException( $msgstr, 500 );
					}

					$this->Session->write( 'Auth.Structurereferente', empty( $results ) ? false : Hash::extract( $results, '{n}.Structurereferente' ) );

					// Surcharge de la limitation par zones géographiques
					$zonesgeographiques = Hash::combine(
						$results,
						'{n}.Zonegeographique.{n}.id',
						'{n}.Zonegeographique.{n}.codeinsee'
					);

					$this->Session->write( 'Auth.Zonegeographique', $zonesgeographiques );
					$this->Session->write( 'Auth.User.filtre_zone_geo', !empty( $zonesgeographiques ) );
				}
			}
		}

		/**
		 * Si l'utilisateur est lié à une communauté de structures référentes.
		 *
		 * @param integer $communautesr_id
		 */
		protected function _loadCommunautesr( $communautesr_id ) {
			$Controller = $this->_Collection->getController();
			$Controller->loadModel( 'User' );

			$query = array(
				'fields' => array(
					'CommunautesrStructurereferente.structurereferente_id'
				),
				'conditions' => array(
					'CommunautesrStructurereferente.communautesr_id' => $communautesr_id
				),
				'contain' => false
			);

			$results = $Controller->User->Communautesr->CommunautesrStructurereferente->find( 'all', $query );
			$structuresreferentes_ids = (array)Hash::extract( $results, '{n}.CommunautesrStructurereferente.structurereferente_id' );
			$this->_loadStructurereferente( $structuresreferentes_ids );
		}

		/**
		 * Chargement des structures référentes liés (directement ou indirectement)
		 * à l'utilisateur connecté dans la session.
		 */
		public function loadStructurereferente() {
			$departement = (int)Configure::read( 'Cg.departement' );
			$Controller = $this->_Collection->getController();
			$Controller->loadModel( 'User' );

			$type = $this->Session->read( 'Auth.User.type' );

			if( strpos( $type, 'externe_' ) === 0 ) {
				if( $type === 'externe_ci' ) {
					$this->_loadReferent( $this->Session->read( 'Auth.User.referent_id' ) );
				}
				else if( $type === 'externe_cpdvcom' ) {
					$this->Session->write( 'Auth.Referent', false );
					$this->_loadCommunautesr( $this->Session->read( 'Auth.User.communautesr_id' ) );
				}
				else {
					$this->Session->write( 'Auth.Referent', false );
					$structuresreferentes_ids = (array)$this->Session->read( 'Auth.User.structurereferente_id' );
					$this->_loadStructurereferente( $structuresreferentes_ids );
				}
			}
			else {
				$this->Session->write( 'Auth.Referent', false );
				$this->Session->write( 'Auth.Structurereferente', false );
			}
		}

		/**
		 * Retourne la liste des ids des structures référentes auxquelles
		 * l'utilisateur est lié.
		 *
		 * @return array
		 */
		public function structuresreferentes() {
			$results = $this->Session->read( 'Auth.Structurereferente' );
			return (array)( empty( $results ) ? array() : Hash::extract( $results, '{n}.id' ) );
		}

		/**
		 * Retourne la liste des ids des zones géographiques auxquelles
		 * l'utilisateur est lié.
		 *
		 * @return array
		 */
		public function zonesgeographiques() {
			$results = $this->Session->read( 'Auth.Zonegeographique' );
			return (array)( empty( $results ) ? array() : array_keys( $results ) );
		}

		/**
		 *
		 * @see Jetons2Component::deleteJetons()
		 *
		 * @param integer $user_id
		 * @param string $session_id
		 */
		public function clearJetons( $user_id = null, $session_id = null ) {
			$Controller = $this->_Collection->getController();

			$user_id = ( null === $user_id )
				? $this->Session->read( 'Auth.User.id' )
				: $user_id;

			$session_id = ( null === $session_id )
				? $this->Session->id()
				: $session_id;

			// Conditions pour la suppression
			$conditions = array( 'user_id' => $user_id );
			if ( Configure::read( 'Utilisateurs.multilogin' ) ) {
				$conditions['php_sid'] = $session_id;
			}

			// Suppression des jetons sur les dossiers
			if( !Configure::read( 'Jetons2.disabled' ) ) {
				if( false === isset( $Controller->Jeton ) ) {
					$Controller->loadModel( 'Jeton' );
				}

				$Controller->Jeton->deleteAllUnbound( $conditions );
			}

			// Suppression des jetons sur les fonctions
			// TODO: dans Jetonsfonctions2Component ou dans le modèle Jeton
			if( !Configure::read( 'Jetonsfonctions2.disabled' ) ) {
				if( false === isset( $Controller->User ) ) {
					$Controller->loadModel( 'User' );
				}

				$Controller->User->Jetonfonction->deleteAll( $conditions );
			}
		}
	}
?>