<?php
	/**
	 * Code source de la classe WebrsaRecherchePropopdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRecherchePropopdo ...
	 *
	 * @package app.Model
	 */
	class WebrsaRecherchePropopdo extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRecherchePropopdo';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Propopdo', 'Allocataire' );

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryPropospdos.search.fields',
			'ConfigurableQueryPropospdos.search.innerTable',
			'ConfigurableQueryPropospdos.exportcsv'
		);

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
				'Originepdo' => 'LEFT OUTER',
				'Decisionpropopdo' => 'LEFT OUTER',
				'Decisionpdo' => 'LEFT OUTER',
				'User' => 'LEFT OUTER'
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Propopdo' );

				// Ajout des spécificités du moteur de recherche
				$departement = (int)Configure::read( 'Cg.departement' );

				$query['fields'] = array_merge(
					array(
						'Dossier.id',
						'Propopdo.id',
						'Personne.id',
						'Propopdo.personne_id'
					),
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Propopdo,
							$this->Propopdo->Decisionpropopdo,
							$this->Propopdo->Originepdo,
							$this->Propopdo->User,
							$this->Propopdo->Decisionpropopdo->Decisionpdo
						)
					)
				);

				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Propopdo->join( 'Decisionpropopdo', array( 'type' => $types['Decisionpropopdo'] ) ),
						$this->Propopdo->join( 'Originepdo', array( 'type' => $types['Originepdo'] ) ),
						$this->Propopdo->join( 'User', array( 'type' => $types['User'] ) ),
						$this->Propopdo->Decisionpropopdo->join( 'Decisionpdo', array( 'type' => $types['Decisionpdo'] ) )
					)
				);

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
				'dates' => array(
					'Propopdo.datereceptionpdo',
					'Decisionpropopdo.datedecisionpdo'
				),
				'foreignKeys' => array(
					'Propopdo.originepdo_id',
					'Decisionpropopdo.decisionpdo_id',
					'Propopdo.user_id',

				),
				'values' => array(
					'Propopdo.etatdossierpdo',
					'Propopdo.motifpdo',
				)
			);

			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, $paths['dates'] );

			foreach( $paths['foreignKeys'] as $path ) {
				$value = suffix( Hash::get( $search, $path ) );
				if( !empty( $value ) ) {
					$query['conditions'][$path] = $value;
				}
			}

			foreach( $paths['values'] as $path ) {
				$value = Hash::get( $search, $path );
				if( !empty( $value ) ) {
					$query['conditions'][$path] = $value;
				}
			}

			// Trouver les PDOs avec un traitement possédant une date d'échéance
			$traitementencours = Hash::get( $search, 'Propopdo.traitementencours' );
			if( $traitementencours ) {
				$this->Propopdo->Behaviors->attach( 'LinkedRecords' );
				$sql = $this->Propopdo->linkedRecordVirtualField( 'Traitementpdo', array( 'conditions' => array( 'traitementspdos.dateecheance IS NOT NULL' ) ) );
				$query['conditions'][] = $sql;
			}

			return $query;
		}
	}
?>