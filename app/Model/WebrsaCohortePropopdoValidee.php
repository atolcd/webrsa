<?php
	/**
	 * Code source de la classe WebrsaCohortePropopdoValidee.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohortePropopdoNouvelle ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePropopdoValidee extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohortePropopdoNouvelle';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Allocataire', 'Personne' );

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				'Foyer' => 'INNER',
				'Propopdo' => 'INNER',
				'Prestation' => 'INNER',
				'Decisionpdo' => 'LEFT OUTER',
				'Decisionpropopdo' => 'LEFT OUTER',
				'Traitementpdo' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'INNER',
				'Typepdo' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'User' => 'INNER'
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Personne->Propopdo,
							$this->Personne->Propopdo->Decisionpropopdo,
							$this->Personne->Propopdo->Traitementpdo,
							$this->Personne->Propopdo->Typepdo,
							$this->Personne->Propopdo->User,
							$this->Personne->Propopdo->Decisionpropopdo->Decisionpdo
						)
					),
					array(
						'Propopdo.id',
						'Propopdo.personne_id',
						'Propopdo.user_id'
					)
				);

				// 2. Jointures
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Personne->join( 'Propopdo', array( 'type' => $types['Propopdo'] ) ),
						$this->Personne->Propopdo->join( 'Decisionpropopdo', array( 'type' => $types['Decisionpropopdo'] ) ),
						$this->Personne->Propopdo->join( 'Typepdo', array( 'type' => $types['Typepdo'] ) ),
						$this->Personne->Propopdo->join( 'Traitementpdo', array( 'type' => $types['Traitementpdo'] ) ),
						$this->Personne->Propopdo->join( 'User', array( 'type' => $types['User'] ) ),
						$this->Personne->Propopdo->Decisionpropopdo->join( 'Decisionpdo', array( 'type' => $types['Decisionpdo'] ) )
					)
				);

				// 3. Conditions
				// En attente de gestionnaire
				$query['conditions'][] = 'Propopdo.user_id IS NOT NULL';

				// 4. Tri par défaut
				$query['order'] = array( 'Dossier.dtdemrsa' => 'ASC' );

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
			$query = $this->Allocataire->searchConditions( $query, $search );

			$paths = array(
				'Propopdo.typepdo_id',
				'Decisionpropopdo.decisionpdo_id',
				'Propopdo.motifpdo',
				'Propopdo.user_id',
			);
			foreach( $paths as $path ) {
				$value = (string)Hash::get( $search, $path );
				if( $value !== '' ) {
					$query['conditions'][$path] = $value;
				}
			}

			// Date de décision
			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'Propopdo.datedecisionpdo' );

			return $query;
		}
	}
?>