<?php
	/**
	 * Code source de la classe WebrsaUser.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe WebrsaUser ...
	 *
	 * @package app.Model
	 */
	class WebrsaUser extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaUser';

		/**
		 * On n'utilise pas de table.
		 *
		 * @var mixed
		 */
		public $useTable = false;

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'User'
		);

		/**
		 * Retourne le querydata de base pour la recherche par utilisateurs.
		 * Le querydata comporte une clé "virtualFields" pour le modèle User.
		 * Le querydata est mis en cache.
		 *
		 * @return array
		 */
		public function searchQuery() {
			$cacheKey = implode( '_', array( $this->useDbConfig, $this->alias, __FUNCTION__ ) );
			$query = Cache::read( $cacheKey );

			if( false === $query ) {
				if( false === $this->User->Behaviors->attached( 'Occurences' ) ) {
					$this->User->Behaviors->attach( 'Occurences' );
				}

				$blacklist = array(
					'connections',
					// 'jetons', // INFO: pas de foreignkey
					// 'jetonsfonctions', // INFO: pas de foreignkey
					'users_zonesgeographiques'
				);

				$query = array(
					'fields' => array(
						'User.id',
						'User.nom',
						'User.prenom',
						'User.username',
						'User.date_deb_hab',
						'User.date_fin_hab',
						'User.date_naissance',
						'User.numtel',
						'User.type',
						'Group.name',
						'Serviceinstructeur.lib_service',
						'ReferentAccueil.nom',
						'ReferentAccueil.prenom',
						$this->User->sqHasLinkedRecords( true, $blacklist )
					),
					'joins' => array(
						$this->User->join( 'Group', array( 'type' => 'INNER' ) ),
						$this->User->join( 'Serviceinstructeur', array( 'type' => 'INNER' ) ),
						$this->User->join( 'ReferentAccueil', array( 'type' => 'LEFT' ) )
					),
					'contain' => false,
					'conditions' => array(),
					'order' => array( 'User.nom' )
				);

				// Préparation de champs virtuels
				$exists = Hash::normalize(
					array(
						'Connection',
						'Jeton' => ( Configure::read( 'Jetons2.disabled' ) ? '0' : '1' ),
						'Jetonfonction' => ( Configure::read( 'Jetonsfonctions2.disabled' ) ? '0' : '1' ),
					)
				);

				foreach( $exists as $className => $enabled ) {
					if( in_array( $enabled, array( '1', null ), true ) ) {
						$primaryKey = $this->User->{$className}->primaryKey;
						$tableName = Inflector::tableize( $className );

						$sql = $this->User->{$className}->sq(
							array(
								'alias' => $tableName,
								'fields' => array( "{$tableName}.{$primaryKey}" ),
								'conditions' => array(
									"{$tableName}.user_id = User.id"
								),
								'contain' => false
							)
						);

						$exists["has_{$tableName}"] = "EXISTS( {$sql} )";
					}
					unset( $exists[$className] );
				}

				$query['virtualFields'] = $exists;

				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * Applique les conditions envoyées par le moteur de recherche au querydata.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			// Filtre par valeur approchante
			foreach( array( 'nom', 'prenom', 'username' ) as $field ) {
				if( isset( $search['User'][$field] ) && !empty( $search['User'][$field] ) ) {
					$query['conditions'][] = 'User.'.$field.' ILIKE \''.$this->User->wildcard( $search['User'][$field] ).'\'';
				}
			}

			// Filtres par valeur exacte
			foreach( array( 'serviceinstructeur_id', 'group_id', 'communautesr_id', 'type', 'has_connections', 'has_jetons', 'has_jetonsfonctions' ) as $field ) {
				$value = (string)Hash::get( $search, "User.{$field}" );
				if( '' !== $value ) {
					$query['conditions'][] = array( "User.{$field}" => $value );
				}
			}

			// Filtres par suffixe
			foreach( array( 'referent_id' ) as $field ) {
				$value = (string)suffix( (string)Hash::get( $search, "User.{$field}" ) );
				if( '' !== $value ) {
					$query['conditions'][] = array( "User.{$field}" => $value );
				}
			}

			// Filtre par structure référente
			$structurereferente_id = (string)suffix( (string)Hash::get( $search, 'User.structurereferente_id' ) );
			if( '' !== $structurereferente_id ) {
				$sql = $this->User->Referent->sq(
					array(
						'alias' => 'referents',
						'fields' => 'referents.id',
						'contain' => false,
						'conditions' => array(
							'referents.structurereferente_id' => $structurereferente_id
						),
					)
				);
				$query['conditions'][] = array(
					'OR' => array(
						'User.structurereferente_id' => $structurereferente_id,
						"User.referent_id IN ( {$sql} )"
					)
				);
			}

			// CD 66: Pôle PCG actuel et anciens pôles PCG liés à l'utilisateur
			$departement = Configure::read( 'Cg.departement' );
			if( 66 === $departement ) {
				// Pôle actuel lié au gestionnaire
				$poledossierpcg66_id = (string) Hash::get( $search, 'User.poledossierpcg66_id' );
				if( '' !== $poledossierpcg66_id ) {
					$query['conditions'][] = array( 'User.poledossierpcg66_id' => $poledossierpcg66_id );
				}

				// Ancien pôle lié au gestionnaire
				$ancienpoledossierpcg66_id = (string) Hash::get( $search, 'User.ancienpoledossierpcg66_id' );
				if( '' !== $ancienpoledossierpcg66_id ) {
					$subQuery = array(
						'alias' => 'polesdossierspcgs66_users',
						'fields' => array(
							'polesdossierspcgs66_users.user_id'
						),
						'contain' => false,
						'conditions' => array(
							'polesdossierspcgs66_users.poledossierpcg66_id' => $ancienpoledossierpcg66_id
						)
					);
					$sql = $this->User->Poledossierpcg66User->sq( array_words_replace( $subQuery, array( 'Poledossierpcg66User' => 'polesdossierspcgs66_users' ) ) );

					$query['conditions'][] = "User.id IN ( {$sql} )";
				}
			}

			return $query;
		}

		/**
		 * Moteur de recherche des utilisateurs, retourne un querydata.
		 *
		 * @param array $search
		 * @return array
		 */
		public function search( $search ) {
			$query = $this->searchQuery();
			$query = $this->searchConditions( $query, $search );

			return $query;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application.
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les méthodes qui ne font rien.
		 */
		public function prechargement() {
			$query = $this->searchQuery();
			return !empty( $query );
		}

		/**
		 * Retourne la liste des gestionnaires des dossiers PCG, à utiliser
		 * dans un select.
		 *
		 * Si on se sert de cette liste pour du traitement, on fera apparaitre
		 * uniquement les gestionnaires actuellement liés à un pole.
		 *
		 * @param boolean $traitement Cette liste doit-elle servir pour du
		 * 	traitement (true par défaut) ?
		 * @param boolean $prefix Les clés doivent-elles être préfixées par l'id
		 *	du pôle ? (false par défaut) ?
		 * @return array
		 */
		public function gestionnaires( $traitement = true, $prefix = false ) {
			$query = array(
				'fields' => array(
					'User.id',
					'User.nom_complet',
					'Poledossierpcg66.id'
				),
				'conditions' => array(),
				'joins' => array(),
				'order' => array( 'User.nom ASC', 'User.prenom ASC' ),
				'contain' => false
			);

			if( true === $traitement ) {
				$query['conditions']['User.isgestionnaire'] = 'O';
				$query['conditions']['Poledossierpcg66.isactif'] = '1';
				$query['joins'][] = $this->User->join( 'Poledossierpcg66', array( 'type' => 'INNER' ) );
			}
			else {
				$subQuery = array(
					'alias' => 'polesdossierspcgs66_users',
					'fields' => 'Poledossierpcg66User.user_id',
					'conditions' => array(
						'Poledossierpcg66User.user_id = User.id'
					),
					'contain' => false
				);
				$sql = words_replace( $this->User->Poledossierpcg66User->sq( $subQuery ), array( 'Poledossierpcg66User' => 'polesdossierspcgs66_users' ) );

				$query['conditions']['OR'] = array(
					'User.isgestionnaire' => 'O',
					"\"User\".\"id\" IN ( {$sql} )"
				);
				$query['joins'][] = $this->User->join( 'Poledossierpcg66', array( 'type' => 'LEFT OUTER' ) );
			}

			$results = $this->User->find( 'all', $query );

			if( true === $prefix ) {
				$extra = array();
				if( false === $traitement ) {
					$query = array(
						'fields' => array(
							'User.id',
							'User.nom_complet',
							'Poledossierpcg66User.poledossierpcg66_id'
						),
						'conditions' => array(),
						'joins' => array(
							$this->User->join( 'Poledossierpcg66User', array( 'type' => 'INNER' ) )
						),
						'order' => array( 'User.nom ASC', 'User.prenom ASC' ),
						'contain' => false
					);
					$extra = $this->User->find( 'all', $query );
				}

				$results = Hash::combine( $results, array( '%d_%d', '{n}.Poledossierpcg66.id', '{n}.User.id' ), '{n}.User.nom_complet' )
					+ Hash::combine( $extra, array( '%d_%d', '{n}.Poledossierpcg66User.poledossierpcg66_id', '{n}.User.id' ), '{n}.User.nom_complet' );
				asort( $results );
				return $results;
			}
			else {
				return Hash::combine( $results, '{n}.User.id', '{n}.User.nom_complet' );
			}
		}


		/**
		 * Retourne les enregistrements pour lesquels une erreur de paramétrage
		 * a été détectée.
		 * Il s'agit des utilisateurs pour lesquels on ne connaît pas une des
		 * valeurs suivantes: nom, prenom, service instructeur, date de début
		 * d'habilitation, date de fin d'habilitation.
		 * @fixme docBlock
		 *
		 * @return array
		 */
		public function storedDataErrors() {
			$departement = (int)Configure::read( 'Cg.departement' );

			$conditionsErrors = array(
				'identification' => array(
					'OR' => array(
						'User.nom IS NULL',
						'TRIM(BOTH \' \' FROM "User"."nom")' => '',
						'User.prenom IS NULL',
						'TRIM(BOTH \' \' FROM "User"."prenom")' => ''
					)
				),
				'habilitations' => array(
					'OR' => array(
						'User.date_deb_hab IS NULL',
						'User.date_fin_hab IS NULL'
					)
				)
			);

			$query = array(
				'fields' => array(
					'User.id',
					'User.username',
					'User.nom',
					'User.prenom',
					'User.date_deb_hab',
					'User.date_fin_hab',
				),
				'contain' => false,
				'joins' => array(),
				'conditions' => array(),
				'order' => array( 'User.username ASC' )
			);


			if( 66 === $departement ) {
				$conditionsErrors['poledossierpcg66'] = array(
					'User.isgestionnaire' => 'N',
					'User.poledossierpcg66_id IS NOT NULL'
				);

				$query['fields'][] = 'Poledossierpcg66.name';
				$query['joins'][] = $this->User->join( 'Poledossierpcg66', array( 'type' => 'LEFT OUTER' ) );
			}

			// Ajout des champs et des conditions concernant les erreurs
			$Dbo = $this->User->getDataSource();
			foreach( $conditionsErrors as $errorName => $errorConditions ) {
				$conditions = $Dbo->conditions( $errorConditions, true, false );
				$query['fields'][] = "( {$conditions} ) AS \"{$this->User->alias}__error_{$errorName}\"";
			}
			$query['conditions']['OR'] = array_values( $conditionsErrors );

			return $this->User->find( 'all', $query );
		}
	}
?>