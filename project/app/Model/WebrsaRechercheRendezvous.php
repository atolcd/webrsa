<?php
	/**
	 * Code source de la classe WebrsaRechercheRendezvous.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheRendezvous ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheRendezvous extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheRendezvous';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryRendezvous.search.fields',
			'ConfigurableQueryRendezvous.search.innerTable',
			'ConfigurableQueryRendezvous.exportcsv'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Allocataire', 'Rendezvous', 'Canton' );

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER',
				// Types propres à ce moteur de recherche
				'Structurereferente' => 'INNER',
				'Referent' => 'LEFT OUTER',
				'Typerdv' => 'LEFT OUTER',
				'Statutrdv' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Rendezvous' );

				// Ajout des spécificités du moteur de recherche
				$departement = (int)Configure::read( 'Cg.departement' );

				$query['fields'] = array_merge(
					array(
						'Rendezvous.id',
						'Rendezvous.personne_id'
					),
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Rendezvous,
							$this->Rendezvous->Referent,
							$this->Rendezvous->Statutrdv,
							$this->Rendezvous->Structurereferente,
							$this->Rendezvous->Typerdv
						)
					)
				);

				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Rendezvous->join( 'Referent', array( 'type' => $types['Referent'] ) ),
						$this->Rendezvous->join( 'Statutrdv', array( 'type' => $types['Statutrdv'] ) ),
						$this->Rendezvous->join( 'Structurereferente', array( 'type' => $types['Structurereferente'] ) ),
						$this->Rendezvous->join( 'Typerdv', array( 'type' => $types['Typerdv'] ) )
					)
				);

				// Si on utilise les thématiques de RDV, ajout du champ virtuel
				if( Configure::read('Rendezvous.useThematique' ) ) {
					$query['fields']['Rendezvous.thematiques'] = '( '.$this->Rendezvous->WebrsaRendezvous->vfListeThematiques( null ).' ) AS "Rendezvous__thematiques"';
				}

				if( 93 === $departement ) {
					foreach( array( '02', '03' ) as $rgadr ) {
						$replacements = array(
							'01' => $rgadr,
							'Adressefoyer' => "Adressefoyer{$rgadr}",
							'Adresse' => "Adresse{$rgadr}",
						);

						$join = $this->Rendezvous->Personne->Foyer->join(
							'Adressefoyer',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Adressefoyer.id IN( '.$this->Rendezvous->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
								)
							)
						);
						$query['joins'][] = array_words_replace( $join, $replacements );

						$join = $this->Rendezvous->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) );
						$query['joins'][] = array_words_replace( $join, $replacements );
					}
				}

				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			// FIXME: les critères ajoutés à la main dans le bloc dossier
			$query = $this->Allocataire->searchConditions( $query, $search );

			$foreignKeys = array( 'structurereferente_id', 'referent_id', 'permanence_id', 'typerdv_id' );
			foreach( $foreignKeys as $foreignKey ) {
				$path = 'Rendezvous.'.$foreignKey;
				$value = (string)suffix( (string)Hash::get( $search, $path ) );
				if( $value !== '' ) {
					$query['conditions'][$path] = $value;
				}
			}

			$value = Hash::get( $search, 'Rendezvous.statutrdv_id' );
			if( !empty( $value ) ) {
				$query['conditions']['Rendezvous.statutrdv_id'] = $value;
			}

			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'Rendezvous.daterdv' );

			// Recherche par thématique de rendez-vous si nécessaire
			$query['conditions'] = $this->Rendezvous->WebrsaRendezvous->conditionsThematique( $query['conditions'], $search, 'Rendezvous.thematiquerdv_id' );

			// Condition sur le projet insertion emploi territorial de la structure de rendez-vous
			$query['conditions'] = $this->conditionCommunautesr(
				$query['conditions'],
				$search,
				array( 'Rendezvous.communautesr_id' => 'Rendezvous.structurereferente_id' )
			);

			$from = (array)Hash::get( $search, 'Rendezvous.arevoirle' ) + array( 'day' => '01' );
			if( valid_date( $from ) ) {
				$to = strtotime( 'last day of this month', strtotime( "{$from['year']}-{$from['month']}-{$from['day']}" ) );
				$to = date_sql_to_cakephp( strftime( "%Y-%m-%d", $to ) );
				$rdv = array(
					'Rendezvous' => array(
						'arevoirle' => '1',
						'arevoirle_from' => $from,
						'arevoirle_to' => $to
					)
				);
				$query['conditions'] = $this->conditionsDates( $query['conditions'], $rdv, 'Rendezvous.arevoirle' );
			}

			return $query;
		}
	}
?>