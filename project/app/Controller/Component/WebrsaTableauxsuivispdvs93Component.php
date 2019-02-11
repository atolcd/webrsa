<?php
	/**
	 * Code source de la classe WebrsaTableauxsuivispdvs93Component.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * La classe WebrsaTableauxsuivispdvs93Component ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaTableauxsuivispdvs93Component extends Component
	{
		/**
		 * Nom du component.
		 *
		 * @var string
		 */
		public $name = 'WebrsaTableauxsuivispdvs93';

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array(
			'Session'
		);

		/**
		 * "Live cache" de la liste des photographes.
		 *
		 * @var array
		 */
		protected $_photographes = null;

		/**
		 * Retourne la liste des photographes des tableaux PDV en fonction de
		 * l'utilisateur connecté.
		 *
		 * @param string $type
		 * @param integer $user_id
		 * @return array
		 */
		public function photographes() {
			if( $this->_photographes === null ) {
				$Controller = $this->_Collection->getController();
				$Controller->loadModel( 'Tableausuivipdv93' );
				$Controller->loadModel( 'User' );

				$sq = $Controller->Tableausuivipdv93->sq( array( 'fields' => array( 'DISTINCT(user_id)' ) ) );

				$query = array(
					'fields' => array( 'Photographe.id', 'Photographe.nom_complet' ),
					'contain' => false,
					'joins' => array(),
					'conditions' => array(
						"Photographe.id IN ( {$sq} )"
					),
					'order' => array( 'Photographe.nom_complet' )
				);

				$type = $this->Session->read( 'Auth.User.type' );
				$user_id = $this->Session->read( 'Auth.User.id' );

				// Restriction de la liste des photographes en fonction de l'utilisateur connecté
				$replacements = array(
					'User' => 'users',
					'Communautesr' => 'communautessrs',
					'CommunautesrStructurereferente' => 'communautessrs_structuresreferentes',
				);

				if( $type === 'externe_cpdvcom' ) {
					$sq = $Controller->User->sq(
						array(
							'alias' => 'users',
							'fields' => array( 'communautessrs_structuresreferentes.structurereferente_id' ),
							'joins' => array(
								array_words_replace(
									$Controller->User->join( 'Communautesr', array( 'type' => 'INNER' ) ),
									$replacements
								),
								array_words_replace(
									$Controller->User->Communautesr->join( 'CommunautesrStructurereferente', array( 'type' => 'INNER' ) ),
									$replacements
								)
							),
							'contain' => false,
							'conditions' => array( 'users.id' => $user_id )
						)
					);

					$query['joins'][] = array_words_replace(
						$Controller->Tableausuivipdv93->Photographe->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
						$replacements
					);

					$query['conditions'][] = array(
						'OR' => array(
							'Photographe.id' => $user_id,
							"Photographe.structurereferente_id IN ( {$sq} )",
							"Referent.structurereferente_id IN ( {$sq} )"
						)
					);
				}
				else if( in_array( $type, array( 'externe_cpdv', 'externe_secretaire' ) ) ) {
					$sq = $Controller->User->sq(
						array(
							'alias' => 'users',
							'fields' => array( 'users.structurereferente_id' ),
							'contain' => false,
							'conditions' => array( 'users.id' => $user_id )
						)
					);

					$query['joins'][] = array_words_replace(
						$Controller->Tableausuivipdv93->Photographe->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
						$replacements
					);

					$query['conditions'][] = array(
						'OR' => array(
							'Photographe.id' => $user_id,
							"Referent.structurereferente_id IN ( {$sq} )",
						)
					);
				}
				else if( $type === 'externe_ci' ) {
					$query['conditions']['Photographe.id'] = $user_id;
				}

				$this->_photographes = $Controller->Tableausuivipdv93->Photographe->find( 'list', $query );
				$this->_photographes = array( 'NULL' => 'Photographie automatique' ) + $this->_photographes;
			}

			return $this->_photographes;
		}

		/**
		 * Retourne la liste des ids des photographes auxquels l'utilisateur a
		 * le droit d'accéder.
		 *
		 * @return array
		 */
		public function photographesIds() {
			$list = $this->photographes();
			unset( $list['NULL'] );
			return array_keys( $list );
		}

		/**
		 * Vérification de l'accès à un tableau de suivi historisé pour
		 * l'utilisateur connecté.
		 *
		 * @param array $tableausuivipdv93
		 * @throws Error403Exception
		 */
		public function checkAccess( array $tableausuivipdv93 ) {
			$access = isset( $tableausuivipdv93['Tableausuivipdv93'] )
				&& array_key_exists( 'user_id', $tableausuivipdv93['Tableausuivipdv93'] )
				&& (
					empty( $tableausuivipdv93['Tableausuivipdv93']['user_id'] )
					|| in_array(
						$tableausuivipdv93['Tableausuivipdv93']['user_id'],
						$this->photographesIds()
					)
					|| $tableausuivipdv93['Tableausuivipdv93']['user_id'] == $this->Session->read( 'Auth.User.id' )
			);

			if( false === $access ) {
				$msgid = 'L\'utilisateur %s n\'a pas le droit d\'accéder au tableau de suivi d\'id %d';
				$msgstr = sprintf( $msgid, $this->Session->read( 'Auth.User.username' ), $tableausuivipdv93['Tableausuivipdv93']['id'] );
				throw new Error403Exception( $msgstr );
			}
		}
	}
?>