<?php
	/**
	 * Code source de la classe WebrsaStructurereferente.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe WebrsaStructurereferente s'occupe de la logique métier des
	 * structures référentes.
	 *
	 * @package app.Model
	 */
	class WebrsaStructurereferente extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaStructurereferente';

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
			'Structurereferente'
		);

		/**
		 * Retourne le querydata de base pour la recherche par structures
		 * référentes.
		 * Le querydata est mis en cache.
		 *
		 * @return array
		 */
		public function searchQuery() {
			$cacheKey = implode( '_', array( $this->useDbConfig, $this->alias, __FUNCTION__ ) );
			$query = Cache::read( $cacheKey );

			if( false === $query ) {
				if( false === $this->Structurereferente->Behaviors->attached( 'Occurences' ) ) {
					$this->Structurereferente->Behaviors->attach( 'Occurences' );
				}

				$query = array(
					'fields' => array_merge(
						$this->Structurereferente->fields(),
						$this->Structurereferente->Typeorient->fields(),
						$this->Structurereferente->Dreesorganisme->fields(),
						array(
							$this->Structurereferente->sqHasLinkedRecords()
						)
					),
					'order' => array( 'Structurereferente.lib_struc ASC' ),
					'joins' => array(
						$this->Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) ),
						$this->Structurereferente->join( 'Dreesorganisme', array( 'type' => 'LEFT' ) )
					),
					'recursive' => -1,
					'conditions' => array()
				);

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
			$departement = (int)Configure::read( 'Cg.departement' );

			// 1. Valeurs approchantes
			foreach( array( 'lib_struc', 'ville' ) as $field ) {
				$value = (string)Hash::get( $search, "Structurereferente.{$field}" );
				if( '' !== $value ) {
					$query['conditions'][] = 'Structurereferente.'.$field.' ILIKE \''.$this->Structurereferente->wildcard( $value ).'\'';
				}
			}

			// 2. Valeurs exactes
			foreach( array( 'typeorient_id', 'actif', 'typestructure', 'contratengagement', 'apre', 'orientation', 'pdo', 'cui' ) as $field ) {
				$value = (string)Hash::get( $search, "Structurereferente.{$field}" );
				if( '' !== $value ) {
					$query['conditions'][] = array( "Structurereferente.{$field}" => $value );
				}
			}

			// 3. Filtre par Projet Insertion Emploi communautaire
			if( 93 === $departement ) {
				$communautesr_id = (string)Hash::get( $search, 'Structurereferente.communautesr_id' );
				if( '' !== $communautesr_id ) {
					$subQuery = array(
						'alias' => 'communautessrs_structuresreferentes',
						'fields' => array( 'communautessrs_structuresreferentes.structurereferente_id' ),
						'contain' => false,
						'conditions' => array(
							'Structurereferente.id = communautessrs_structuresreferentes.structurereferente_id',
							'communautessrs_structuresreferentes.communautesr_id' => $communautesr_id
						)
					);
					$sql = $this->Structurereferente->CommunautesrStructurereferente->sq( $subQuery );
					$query['conditions'][] = "Structurereferente.id IN ( {$sql} )";
				}
			}

			// Véfirication de l'organisme DREES
			if(isset($search['Structurereferente']['dreesorganisme_id'])) {
				$query['conditions'][] = array('Structurereferente.dreesorganisme_id' => $search['Structurereferente']['dreesorganisme_id']);
			}

			return $query;
		}

		/**
		 * Moteur de recherche des structures référentes, retourne un querydata.
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
		 * Retourne un tableau avec en clé le code INSEE et en valeur un array des
		 * id des structures référentes présentes sur celui-ci.
		 *
		 * @return array
		 */
		public function listeParCodeInsee() {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$results = Cache::read( $cacheKey );

			if( $results === false ) {
				$results = array();

				$query = array(
					'fields' => array(
						'StructurereferenteZonegeographique.structurereferente_id',
						'Zonegeographique.codeinsee'
					),
					'joins' => array(
						$this->Structurereferente->StructurereferenteZonegeographique->join( 'Zonegeographique', array( 'type' => 'INNER' ) )
					),
					'contain' => false,
					'order' => array( 'Zonegeographique.codeinsee' )
				);
				$tmp = $this->Structurereferente->StructurereferenteZonegeographique->find( 'all', $query );

				foreach( $tmp as $result ) {
					$codeinsee = $result['Zonegeographique']['codeinsee'];
					$structurereferente_id = $result['StructurereferenteZonegeographique']['structurereferente_id'];

					if( false === isset( $results[$codeinsee] ) ) {
						$results[$codeinsee] = array();
					}

					$results[$codeinsee][] = $structurereferente_id;
				}

				Cache::write( $cacheKey, $results );
				ModelCache::write( $cacheKey, array( 'Structurereferente', 'StructurereferenteZonegeographique', 'Zonegeographique' ) );
			}

			return $results;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application.
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les méthodes qui ne font rien.
		 */
		public function prechargement() {
			$success = true;

			$query = $this->searchQuery();
			$success = false === empty( $query ) && $success;

			try {
				$this->listeParCodeInsee();
			} catch( Exception $e ) {
				$result = false;
			}

			return $success;
		}
	}
?>