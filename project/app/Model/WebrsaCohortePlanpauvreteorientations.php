<?php
	/**
	 * Code source de la classe WebrsaCohortePlanpauvreteorientations.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaCohortePlanpauvrete', 'Model' );

	/**
	 * La classe WebrsaCohortePlanpauvreteorientations ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePlanpauvreteorientations extends WebrsaCohortePlanpauvrete
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohortePlanpauvreteorientations';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Personne',
			'Historiqueetatpe',
			'Allocataire',
			'Nonoriente66',
			'Canton',
		);

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Personne.id' => array( 'type' => 'hidden' ),
			'Historiqueetatpe.id' => array( 'type' => 'hidden' ),
			'Nonoriente66.id' => array( 'type' => 'hidden' ),
			'Nonoriente66.personne_id' => array( 'type' => 'hidden' ),
			'Nonoriente66.origine' => array( 'type' => 'hidden' ),
			'Nonoriente66.historiqueetatpe_id' => array( 'type' => 'hidden' ),
			'Nonoriente66.user_id' => array( 'type' => 'hidden' ),
			'Orientstruct.origine' => array( 'type' => 'hidden' ),
			'Orientstruct.personne_id' => array( 'type' => 'hidden' ),
			'Orientstruct.statut_orient' => array( 'type' => 'hidden' ),
			'Nonoriente66.selection' => array( 'type' => 'checkbox' ),
			'Orientstruct.date_valid' => array( 'type' => 'date' )
		);

		/**
		 * Liste des conditions supplémentaires éventuelles pour les tests
		 * réalisés par la méthode WebrsaAbstractCohortesComponent::checkHiddenCohorteValues
		 *
		 * @var array
		 */
		public $checkHiddenCohorteValuesConditions = array(
			'Orientstruct.personne_id IS NOT NULL',
			'Orientstruct.statut_orient' => 'Orienté',
			'Orientstruct.date_valid IS NOT NULL',
			'Orientstruct.rgorient' => 1,
			'Orientstruct.origine' => 'cohorte',
			'Orientstruct.user_id IS NOT NULL'
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
				// INNER JOIN
				'Foyer' => 'INNER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Calculdroitrsa' => 'INNER',
				'Dossier' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Adresse' => 'INNER',
				'Historiquedroit' => 'INNER',

				// LEFT OUTER JOIN
				'Orientstruct' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Typeorient' => 'LEFT OUTER',
				'Nonoriente66' => 'LEFT OUTER',
				'Informationpe' => 'LEFT OUTER',
				'Canton' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Historiqueetatpe' => 'LEFT OUTER',
				'Rendezvous' => 'LEFT OUTER',
				'Contratinsertion' => 'LEFT OUTER',

			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				App::uses('WebrsaModelUtility', 'Utility');
				$query = $this->Allocataire->searchQuery( $types, 'Personne' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Nonoriente66->Personne->Orientstruct,
							$this->Nonoriente66->Personne->Orientstruct->Structurereferente,
							$this->Nonoriente66->Personne->Orientstruct->Typeorient,
							$this->Nonoriente66,
							$this->Nonoriente66->Historiqueetatpe->Informationpe,
							$this->Nonoriente66->Historiqueetatpe,
							$this->Nonoriente66->Personne->PersonneReferent,
							$this->Nonoriente66->Personne->Rendezvous,
							$this->Nonoriente66->Personne->Contratinsertion,
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Historiqueetatpe.id',
						'Nonoriente66.id',
						'Nonoriente66.personne_id',
						'Nonoriente66.origine',
						'Nonoriente66.historiqueetatpe_id',
						'Nonoriente66.user_id',
						'Orientstruct.origine',
						'Orientstruct.personne_id',
						'Orientstruct.statut_orient',
						'Orientstruct.typeorient_id',
						'Orientstruct.structurereferente_id',
						'Orientstruct.date_valid',
						'Rendezvous.personne_id',
						'Personne.id',
						'Dossier.id',
						'Foyer.enerreur' => $this->Nonoriente66->Personne->Foyer->sqVirtualField( 'enerreur', true ),
						'Foyer.nbenfants' => '( '.$this->Nonoriente66->Personne->Foyer->vfNbEnfants().' ) AS "Foyer__nbenfants"',
					)
				);
				// 2. Jointure
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Nonoriente66->Personne->join( 'Orientstruct', array( $types['Orientstruct'] ) ),
						$this->Nonoriente66->Personne->Orientstruct->join( 'Structurereferente', array( $types['Structurereferente'] ) ),
						$this->Nonoriente66->Personne->Orientstruct->join( 'Typeorient', array( $types['Typeorient'] ) ),
						$this->Nonoriente66->Personne->join( 'Nonoriente66', array( $types['Nonoriente66'] ) ),
						$this->Nonoriente66->Personne->join( 'Rendezvous', array( $types['Rendezvous'] ) ),
						$this->Nonoriente66->Personne->join( 'Contratinsertion', array( $types['Contratinsertion'] ) ),
						$this->Nonoriente66->Personne->join( 'Historiquedroit', array( $types['Historiquedroit'] ) ),
						$this->Nonoriente66->Historiqueetatpe->Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', $types['Informationpe'] ),
						$this->Nonoriente66->Historiqueetatpe->Informationpe->join( 'Historiqueetatpe', array( $types['Historiqueetatpe'] ) ),
					)
				);

				// 4. Conditions
				// SDD & DOV
				$query = $this->sdddov($query);
				//Sans orientation
				$query = $this->sansOrientation($query);
				//Sans RDV
				$query = $this->sansRendezvous($query);
				//Sans CER
				$query = $this->sansCER($query);
				//Inscrits à Pôle Emploi
				$query = $this->inscritPE($query);

				Cache::write($cacheKey, $query);
			}

			return $query;
		}

		/**
		 * Logique de sauvegarde de la cohorte
		 *
		 * @param type $data
		 * @param type $params
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$departement = Configure::read('Cg.departement');
			foreach ( $data as $key => $value ) {
				// Si non selectionné, on retire tout
				if ( $value['Nonoriente66']['selection'] === '0' ) {
					unset($data[$key]);
					continue;
				}

				if ( empty($value['Nonoriente66']['id']) ) {
					$data[$key]['Nonoriente66'] = array(
						'personne_id' => Hash::get($value, 'Personne.id'),
						'origine' => 'isemploi',
						'dateimpression' => null,
						'historiqueetatpe_id' => Hash::get($value, 'Historiqueetatpe.id'),
						'user_id' => $user_id
					);
				}

				$data[$key]['Orientstruct']['personne_id'] = Hash::get($value, 'Personne.id');
				$data[$key]['Orientstruct']['origine'] = 'cohorte';
				$data[$key]['Orientstruct']['statut_orient'] = 'Orienté';
				$data[$key]['Orientstruct']['structureorientante_id'] =
					Configure::read('PlanPauvrete.Cohorte.ValeursOrientations.structureorientante_id');
				$data[$key]['Orientstruct']['referentorientant_id'] =
					Configure::read('PlanPauvrete.Cohorte.ValeursOrientations.referentorientant_id') ;
				$data[$key]['Orientstruct']['typeorient_id'] =
					Configure::read('PlanPauvrete.Cohorte.ValeursOrientations.typeorient_id') ;
				$data[$key]['Orientstruct']['structurereferente_id'] =
					Configure::read('PlanPauvrete.Cohorte.ValeursOrientations.structurereferente_id') ;
				$data[$key]['Orientstruct']['referent_id'] =
					Configure::read('PlanPauvrete.Cohorte.ValeursOrientations.referent_id') ;
				$data[$key]['Orientstruct']['date_propo'] = $data[$key]['Orientstruct']['date_valid'];
				if( $departement == 66 ) {
					$data[$key]['Orientstruct']['typenotification'] =
					Configure::read('PlanPauvrete.Cohorte.ValeursOrientations.typenotification') ;
				}
			}

			$this->Nonoriente66->begin();

			$success = !empty($data) && $this->Nonoriente66->saveAll($data, array('atomic' => false));
			$success = !empty($data)
				&& $this->Nonoriente66->Personne->Orientstruct->saveAll($data, array('atomic' => false))
				&& $success;

			if ($success) {
				$this->Nonoriente66->commit();
			} else {
				$this->Nonoriente66->rollback();
			}

			return $success;
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

			/**
			 * Generateur de conditions
			 */
			$paths = array(
				'Nonoriente66.user_id'
			);

			// Fils de dependantSelect
			$pathsToExplode = array(
			);

			$pathsDate = array(
				'Nonoriente66.dateimpression',
				'Nonoriente66.datenotification',
			);

			foreach( $paths as $path ) {
				$value = Hash::get( $search, $path );
				if( $value !== null && $value !== '' ) {
					$query['conditions'][$path] = $value;
				}
			}

			foreach( $pathsToExplode as $path ) {
				$value = Hash::get( $search, $path );
				if( $value !== null && $value !== '' && strpos($value, '_') > 0 ) {
					list(,$value) = explode('_', $value);
					$query['conditions'][$path] = $value;
				}
			}

			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, $pathsDate );

			return $query;
		}
	}
?>