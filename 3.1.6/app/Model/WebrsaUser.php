<?php
	/**
	 * Code source de la classe WebrsaUser.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
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
						$this->User->sqHasLinkedRecords( true, $blacklist )
					),
					'joins' => array(
						$this->User->join( 'Group', array( 'type' => 'INNER' ) ),
						$this->User->join( 'Serviceinstructeur', array( 'type' => 'INNER' ) )
					),
					'contain' => false,
					'conditions' => array(),
					'order' => array( 'User.nom ASC' )
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
				$value = suffix( (string)Hash::get( $search, "User.{$field}" ) );
				if( '' !== $value ) {
					$query['conditions'][] = array( "User.{$field}" => $value );
				}
			}

			// Filtre par structure référente
			$structurereferente_id = suffix( (string)Hash::get( $search, 'User.structurereferente_id' ) );
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
	}
?>