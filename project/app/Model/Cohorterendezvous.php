<?php
	/**
	 * Code source de la classe Cohorterendezvous.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe Cohorterendezvous ...
	 *
	 * @package app.Model
	 */
	class Cohorterendezvous extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Cohorterendezvous';

		/**
		 * On n'utilise pas de table.
		 *
		 * @var boolean
		 */
		public $useTable = false;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Conditionnable'
		);

		/**
		 * @todo permettre de paramétrer les champs
		 * @todo mettre les critères par défaut dans le webrsa.inc
		 *
		 * @return array
		 */
		public function cohorteQuery() {
			$Allocataire = ClassRegistry::init( 'Allocataire' );
			$Rendezvous = ClassRegistry::init( 'Rendezvous' );

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $Allocataire->searchQuery( array(), 'Rendezvous' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$Rendezvous,
							$Rendezvous->Typerdv,
							$Rendezvous->Statutrdv,
							$Rendezvous->Structurereferente,
							$Rendezvous->Referent,
							$Rendezvous->Permanence,
							$Rendezvous->Structurereferente->Typeorient
						)
					),
					// Champs nécessaires au traitement de la cohorte
					array(
						'Dossier.id',
						'Rendezvous.id',
						'Rendezvous.statutrdv_id',
						'Rendezvous.personne_id'
					)
				);

				// 2. Ajout des jointures supplémentaires
				array_unshift(
					$query['joins'],
					$Rendezvous->join( 'Typerdv', array( 'type' => 'LEFT OUTER' ) ),
					$Rendezvous->join( 'Statutrdv', array( 'type' => 'LEFT OUTER' ) ),
					$Rendezvous->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
					$Rendezvous->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					$Rendezvous->join( 'Permanence', array( 'type' => 'LEFT OUTER' ) ),
					$Rendezvous->Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) )
				);

				// 3. Tri par défaut: date, heure, id
				$query['order'] = array(
					'Rendezvous.daterdv' => 'ASC',
					'Rendezvous.heurerdv' => 'ASC',
					'Rendezvous.id' => 'ASC'
				);

				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * @todo: filtres spécifiques aux RDV
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function cohorteConditions( array $query, array $search ) {
			$Allocataire = ClassRegistry::init( 'Allocataire' );
			$Rendezvous = ClassRegistry::init( 'Rendezvous' );

			$query = $Allocataire->searchConditions( $query, $search );

			// 1. Valeurs simples
			$paths = array(
				'Rendezvous.structurereferente_id',
				'Rendezvous.referent_id',
				'Rendezvous.statutrdv_id',
				'Rendezvous.permanence_id',
				'Rendezvous.typerdv_id'
			);
			foreach( $paths as $path ) {
				$value = Hash::get( $search, $path );
				if( !empty( $value ) ) {
					if( !is_array( $value ) ) {
						$value = suffix( $value );
					}
					$query['conditions'][$path] = $value;
				}
			}

			// 2. Plage de dates du rendez-vous
			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'Rendezvous.daterdv' );

			// 3. Thématique(s) du RDV
			$query['conditions'] = $Rendezvous->WebrsaRendezvous->conditionsThematique( $query['conditions'], $search, 'Rendezvous.thematiquerdv_id' );

			// Condition sur le projet insertion emploi territorial de la structure de rendez-vous
			$query['conditions'] = $this->conditionCommunautesr(
				$query['conditions'],
				$search,
				array( 'Rendezvous.communautesr_id' => 'Rendezvous.structurereferente_id' )
			);

			return $query;
		}

		/**
		 * Cohorte de recherches des bénéficiaires pour lesquels un dossier COV
		 * peut être créé pour la thématique.
		 *
		 * @param array $search
		 * @return array
		 */
		public function cohorte( array $search ) {
			$query = $this->cohorteQuery();
			$query = $this->cohorteConditions( $query, $search );

			return $query;
		}

		public function saveCohorte( array $data, $user_id = null ) {
			debug( $data );
			return false;
		}

		/**
		 * Vérification que les champs spécifiés dans le paramétrage par les clés
		 * Cohortesrendezvous.cohorte.fields, Cohortesrendezvous.cohorte.innerTable
		 * et Cohortesrendezvous.exportcsv dans le webrsa.inc existent bien dans
		 * la requête de recherche renvoyée par la méthode cohorte().
		 *
		 * @param array $params Paramètres supplémentaires (clé 'query' possible)
		 * @return array
		 * @todo Utiliser AbstractWebrsaRecherche
		 */
		public function checkParametrage( array $params = array() ) {
			$keys = array( 'Cohortesrendezvous.cohorte.fields', 'Cohortesrendezvous.cohorte.innerTable', 'Cohortesrendezvous.exportcsv' );
			$query = $this->cohorte( array() );

			$return = ConfigurableQueryFields::getErrors( $keys, $query );

			return $return;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * Export de la liste des champs disponibles pour le moteur de recherche
		 * dans le fichier app/tmp/Cohorterendezvous__searchQuery__cgXX.csv.
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les méthodes qui ne font rien.
		 */
		public function prechargement() {
			$success = true;

			$query = $this->cohorteQuery();
			$success = $success && !empty( $query );

			// Export des champs disponibles
			$fileName = TMP.DS.'logs'.DS.__CLASS__.'__cohorteQuery__cg'.Configure::read( 'Cg.departement' ).'.csv';
			ConfigurableQueryFields::exportQueryFields( $query, Inflector::tableize( $this->name ), $fileName );

			return $success;
		}
	}
?>