<?php
	/**
	 * Code source de la classe Informationpe.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Informationpe ...
	 *
	 * @package app.Model
	 */

	App::uses( 'Sanitize', 'Utility' );

	class Informationpe extends AppModel
	{
		public $name = 'Informationpe';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $hasMany = array(
			'Historiqueetatpe' => array(
				'className' => 'Historiqueetatpe',
				'foreignKey' => 'informationpe_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);

		/**
		* Retourne un morceau de querydata permettant de comparer les nirs et les
		* dates de naissance d'une entrée de la table informationspe et d'une
		* entrée de la table personnes.
		*
		* @param string Le nom de l'alias de la table informationspe
		* @param string Le nom de l'alias de la table personnes
		* @return array
		*/
		public function qdNirsCmp( $informationpe, $personne ) {
			return array(
				"{$informationpe}.nir IS NOT NULL",
				"SUBSTRING( {$informationpe}.nir FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH ' ' FROM {$personne}.nir ) FROM 1 FOR 13 )",
				"{$informationpe}.dtnai = {$personne}.dtnai"
			);
		}

		/**
		* Retourne un morceau de requête SQL permettant de comparer les nirs et
		* les dates de naissance d'une entrée de la table informationspe et d'une
		* entrée de la table personnes.
		*
		* @param string Le nom de l'alias de la table informationspe
		* @param string Le nom de l'alias de la table personnes
		* @return string
		*/
		public function sqNirsCmp( $informationpe, $personne ) {
			return implode( ' AND ', $this->qdNirsCmp( $informationpe, $personne ) );
		}

		/**
		* Retourne un morceau de querydata contenant les conditions pour faire
		* la jointure entre la table informationspe et la table personnes.
		*
		* @param string Le nom de l'alias de la table informationspe
		* @param string Le nom de l'alias de la table personnes
		* @return array
		*/
		public function qdConditionsJoinPersonne( $informationpe, $personne ) {
			return array(
				'OR' => array(
					$this->qdNirsCmp( $informationpe, $personne ),
					array(
						"{$personne}.nom IS NOT NULL",
						"{$personne}.prenom IS NOT NULL",
						"{$personne}.dtnai IS NOT NULL",
						"TRIM( BOTH ' ' FROM {$personne}.nom ) <>" => '',
						"TRIM( BOTH ' ' FROM {$personne}.prenom ) <>" => '',
						"TRIM( BOTH ' ' FROM {$informationpe}.nom ) = TRIM( BOTH ' ' FROM {$personne}.nom )",
						"TRIM( BOTH ' ' FROM {$informationpe}.prenom ) = TRIM( BOTH ' ' FROM {$personne}.prenom )",
						"{$informationpe}.dtnai = {$personne}.dtnai"
					)
				)
			);
		}

		/**
		* Retourne un morceau de requête SQL contenant les conditions pour faire
		* la jointure entre la table informationspe et la table personnes.
		*
		* @param string Le nom de l'alias de la table informationspe
		* @param string Le nom de l'alias de la table personnes
		* @return array
		*/
		public function sqConditionsJoinPersonne( $informationpe, $personne ) {
			$querydata = $this->qdConditionsJoinPersonne( $informationpe, $personne );
			$ds = $this->getDataSource( $this->useDbConfig );
			return $ds->conditions( $querydata, false, false, null );
		}

		/**
		* Retourne un morceau de querydata contenant les conditions pour filtrer
		* les entrées de la table informationspe avec des valeurs.
		*
		* @param string $informationpe Le nom de l'alias de la table informationspe
		* @param array $values Les données sur lesquelles filtrer (clés nécessaires: nir, nom, prenom, dtnai)
		* @return array
		*/
		public function qdConditionsJoinPersonneOnValues( $informationpe, $values ) {

			$extractedNir = trim( Set::classicExtract( $values, 'nir' ) );
			$extractedNir = ( empty( $extractedNir ) ? null : substr( $extractedNir, 0, 13 ) );
			return array(
				'OR' => array(
					array(
						"{$informationpe}.nir IS NOT NULL",
						"SUBSTRING({$informationpe}.nir FROM 1 FOR 13)" => $extractedNir,
						"{$informationpe}.dtnai" => Set::classicExtract( $values, 'dtnai' )
					),
					array(
						( ( trim( Set::classicExtract( $values, 'nom' ) ) == '' ) ? 'FALSE' : 'TRUE' ),
						( ( trim( Set::classicExtract( $values, 'prenom' ) ) == '' ) ? 'FALSE' : 'TRUE' ),
						"TRIM( BOTH ' ' FROM {$informationpe}.nom )" => trim( Set::classicExtract( $values, 'nom' ) ),
						"TRIM( BOTH ' ' FROM {$informationpe}.prenom )" =>  trim( Set::classicExtract( $values, 'prenom' ) ),
						"{$informationpe}.dtnai" => trim( Set::classicExtract( $values, 'dtnai' ) ),
					)
				)
			);
		}

		/**
		*
		*/
		public function qdRadies() {
			$queryData['fields'][] = 'Historiqueetatpe.id';
			$queryData['fields'][] = 'Historiqueetatpe.informationpe_id';
			$queryData['fields'][] = 'Historiqueetatpe.etat';
			$queryData['fields'][] = 'Historiqueetatpe.identifiantpe';
			$queryData['fields'][] = 'Historiqueetatpe.date';
			$queryData['fields'][] = 'Historiqueetatpe.motif';
			$queryData['fields'][] = 'Historiqueetatpe.code';

			$queryData['joins'][] = array(
				'table'      => 'informationspe', // FIXME:
				'alias'      => 'Informationpe',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => $this->qdConditionsJoinPersonne( 'Informationpe', 'Personne' )
			);
			$queryData['joins'][] = array(
				'table'      => 'historiqueetatspe', // FIXME:
				'alias'      => 'Historiqueetatpe',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array(
					'Historiqueetatpe.informationpe_id = Informationpe.id',
					'Historiqueetatpe.id IN (
								SELECT h.id
									FROM historiqueetatspe AS h
									WHERE h.informationpe_id = Informationpe.id
									ORDER BY h.date DESC, h.id DESC
									LIMIT 1
					)'
				)
			);

			// Si on ne trouve pas la clé Selectionradies.conditions dans la configuration, on ne se basera
			// que sur l'état "radiation", sinon on utliisera les conditions définies dans la configuration (CG 58).
			$conditionsConfigure = Configure::read( 'Selectionradies.conditions' );
			if( empty( $conditionsConfigure ) ) {
				$queryData['conditions']['Historiqueetatpe.etat'] = 'radiation';
			}
			else {
				if( !isset( $queryData['conditions'] ) ) {
					$queryData['conditions'] = array();
				}
				$queryData['conditions'] = Set::merge( $conditionsConfigure, $queryData['conditions'] );
			}

			// Permet d'obtenir une et une seule entrée de la table informationspe
			$sqDerniereInformationpe = $this->sqDerniere( 'Personne' );
			$queryData['conditions'][] = "Informationpe.id IN ( {$sqDerniereInformationpe} )";

			$queryData['order'] = array( 'Historiqueetatpe.date ASC', 'Historiqueetatpe.id ASC' );

			return $queryData;
		}

		/**
		 * TODO: et qui ne sont pas passés dans une EP pour ce motif depuis au moins 1 mois (?)
		 * @param boolean useOrientdatevalid
		 * @return array
		 */
		public function qdNonInscrits($useOrientdatevalid = true) {
			$queryData['fields'][] = 'Orientstruct.id';
			$queryData['fields'][] = 'Orientstruct.date_valid';
			$queryData['fields'][] = 'Typeorient.lib_type_orient';
			$queryData['fields'][] = 'Structurereferente.lib_struc';
			if ($useOrientdatevalid == true) {
				$queryData['conditions'][] = 'Orientstruct.date_valid + INTERVAL \''.Configure::read( 'Selectionnoninscritspe.intervalleDetection' ).'\' < DATE_TRUNC( \'day\', NOW() )';
			}

			$queryData['conditions'][] = 'Personne.id NOT IN (
				SELECT
						personnes.id
					FROM informationspe
						INNER JOIN historiqueetatspe ON (
							informationspe.id = historiqueetatspe.informationpe_id
							AND historiqueetatspe.id IN (
										SELECT h.id
											FROM historiqueetatspe AS h
											WHERE h.informationpe_id = informationspe.id
											ORDER BY h.date DESC, h.id DESC
											LIMIT 1
							)
						)
						INNER JOIN personnes ON (
							'.$this->sqConditionsJoinPersonne( 'informationspe', 'personnes' ).'
						)
					WHERE
						personnes.id = Personne.id
						AND historiqueetatspe.etat = \'inscription\'
						'.( Configure::read( 'Cg.departement' ) == 58 ? null : 'AND historiqueetatspe.date >= Orientstruct.date_valid' ).'
			)';

			$queryData['joins'][] = array(
				'table'      => 'typesorients',
				'alias'      => 'Typeorient',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array(
					'Typeorient.id = Orientstruct.typeorient_id'
				)
			);
			$queryData['joins'][] = array(
				'table'      => 'structuresreferentes',
				'alias'      => 'Structurereferente',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array(
					'Structurereferente.id = Orientstruct.structurereferente_id'
				)
			);

			$queryData['order'] = array( 'Orientstruct.date_valid ASC' );

			return $queryData;
		}


		/**
		* Récupère le dernier identifiant Pôle Emploi d'une personne donnée.
		* Note : l'utilisation de l'identifiant Personne.idassedic est déconseillé.
		* @param $personneId
		*/
		public function dernierIdentifiantpe( $personneId) {
			$query = "
				SELECT
					historiqueetatspe.identifiantpe
					FROM informationspe
						INNER JOIN historiqueetatspe ON (
							informationspe.id = historiqueetatspe.informationpe_id
							AND historiqueetatspe.id IN (
								SELECT h.id
									FROM historiqueetatspe AS h
									WHERE h.informationpe_id = informationspe.id
									ORDER BY h.date DESC, h.id DESC
									LIMIT 1
							)
						)
						INNER JOIN personnes ON (
							".$this->sqConditionsJoinPersonne( 'Informationpe', 'Personne' )."
						)
					WHERE personnes.id = {$personneId}
				;";
			$result = $this->query( $query );
			return array('Informationpe'=> Set::classicExtract( $result, '0.0' ) );
		}

		/**
		*
		*/
		public function derniereInformation( $personne ) {
			$conditions = $this->qdConditionsJoinPersonneOnValues( 'Informationpe', $personne['Personne'] );

			// Permet d'obtenir une et une seule entrée de la table informationspe
			if( !empty( $personne['Personne']['dtnai'] ) ) {
				$sqDerniereInformationpe = $this->sqDerniere( 'Personne' );
				foreach( array( 'nom', 'prenom', 'dtnai', 'nir' ) as $field ) {
					$sqDerniereInformationpe = str_replace( "\"Personne\".\"{$field}\"", "'".Sanitize::escape( $personne['Personne'][$field] )."'", $sqDerniereInformationpe );
				}
				$conditions[] = "Informationpe.id IN ( {$sqDerniereInformationpe} )";

				$infope = $this->find(
					'first',
					array(
						'contain' => array(
							'Historiqueetatpe' => array(
								'order' => array( "Historiqueetatpe.date DESC", "Historiqueetatpe.id DESC" ),
								'limit' => 1
							)
						),
						'conditions' => $conditions
					)
				);

				return $infope;
			}
		}

		/**
		* Retourne une array à utiliser comme jointure entre la table personnes
		* et la table informationspe.
		*
		* @param string $aliasPersonne Alias pour la table personnes
		* @param string $aliasInformationpe Alias pour la table informationspe
		* @param string $type Type de jointure à effectuer
		* @return array
		*/

		public function joinPersonneInformationpe( $aliasPersonne = 'Personne', $aliasInformationpe = 'Informationpe', $type = 'LEFT OUTER' ) {
			return array(
				'table'      => 'informationspe',
				'alias'      => $aliasInformationpe,
				'type'       => $type,
				'foreignKey' => false,
				'conditions' => $this->qdConditionsJoinPersonne( $aliasInformationpe, $aliasPersonne )
			);
		}

		/**
		* Vérifie le délai (intervalle) accordé pour la détection des allocataires
		* non inscrits au Pôle Emploi par rapport à leur date de validation d'orientation
		*/

		public function checkConfigUpdateIntervalleDetectionNonInscritsPe() {
			return $this->_checkPostgresqlIntervals( array( 'Selectionnoninscritspe.intervalleDetection'  ), true );
		}

		/**
		 * Retourne une sous-requête SQL permettant d'obtenir une et une seule entrée de
		 * informationspe pour une personne donnée, compte tenu de l'entrée la plus récente
		 * de historiqueetatspe.
		 *
		 * @param string $personneAlias L'alias de la table personnes sur lequel réaliser l'appariement
		 * @return string
		 */
		public function sqDerniere( $personneAlias = 'Personne' ) {
			$sqDernierhistoriqueetatspe = $this->Historiqueetatpe->sq(
				array(
					'alias' => 'dernierhistoriqueetatspe',
					'fields' => array( 'dernierhistoriqueetatspe.id' ),
					'conditions' => array(
						'dernierhistoriqueetatspe.informationpe_id = i.id'
					),
					'order' => array( 'dernierhistoriqueetatspe.date DESC', 'dernierhistoriqueetatspe.id DESC' ),
					'limit' => 1
				)
			);

			$sq = $this->sq(
				array(
					'alias' => 'i',
					'fields' => array( 'i.id', 'h.date' ),
					'joins' => array(
						array_words_replace(
							$this->join( 'Historiqueetatpe', array( 'type' => 'INNER' ) ),
							array(
								'Informationpe' => 'i',
								'Historiqueetatpe' => 'h',
							)
						)
					),
					'conditions' => array(
						'OR' => array(
							array(
								"i.nir IS NOT NULL",
								"{$personneAlias}.nir IS NOT NULL",
								"TRIM( BOTH ' ' FROM i.nir ) <> ''",
								"TRIM( BOTH ' ' FROM {$personneAlias}.nir ) <> ''",
								"SUBSTRING( i.nir FROM 1 FOR 13 ) = SUBSTRING( {$personneAlias}.nir FROM 1 FOR 13 )",
								"i.dtnai = {$personneAlias}.dtnai",
							),
							array(
								"i.nom IS NOT NULL",
								"{$personneAlias}.nom IS NOT NULL",
								"i.prenom IS NOT NULL",
								"{$personneAlias}.prenom IS NOT NULL",
								"TRIM( BOTH ' ' FROM i.nom ) <> ''",
								"TRIM( BOTH ' ' FROM i.prenom ) <> ''",
								"TRIM( BOTH ' ' FROM {$personneAlias}.nom ) <> ''",
								"TRIM( BOTH ' ' FROM {$personneAlias}.prenom ) <> ''",
								"TRIM( BOTH ' ' FROM i.nom ) = {$personneAlias}.nom",
								"TRIM( BOTH ' ' FROM i.prenom ) = {$personneAlias}.prenom",
								"i.dtnai = {$personneAlias}.dtnai",
							),
						),
						"h.id IN ( {$sqDernierhistoriqueetatspe} )"
					),
				)
			);

			$sq = "SELECT \"derniereinformationspe\".\"i__id\" FROM ( {$sq} ) AS \"derniereinformationspe\" ORDER BY \"derniereinformationspe\".\"h__date\" DESC LIMIT 1";//FIXME

			return $sq;
		}

		/**
		 * Permet d'obtenir une et une seule entrée de la table informationspe pour une personne donnée.
		 *
		 * @param array $personne
		 * @return string
		 */
		public function sqDernierePourPersonne( $personne ) {
			$sqDerniereInformationpe = $this->sqDerniere( 'Personne' );

			foreach( array( 'nom', 'prenom', 'dtnai', 'nir' ) as $field ) {
				$sqDerniereInformationpe = str_replace(
					"\"Personne\".\"{$field}\"",
					"'".str_replace( "'", "\\'", $personne['Personne'][$field] )."'",
					$sqDerniereInformationpe
				);
			}

			return $sqDerniereInformationpe;
		}

		/**
		 * Retourne la liste des clés de configuration pour lesquelles il faut
		 * vérifier la syntaxe de l'intervalle PostgreSQL.
		 *
		 * @return array
		 */
		public function checkPostgresqlIntervals() {
			$departement = Configure::read( 'Cg.departement' );
			if( in_array( $departement, array( 58, 66 ) ) ) {
				return $this->_checkPostgresqlIntervals(
					array( 'Selectionnoninscritspe.intervalleDetection' )
				);
			}

			return array();
		}
	}
?>