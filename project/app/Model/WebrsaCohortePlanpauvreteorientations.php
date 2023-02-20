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
			'Orientstruct.origine' => array( 'type' => 'hidden' ),
			'Orientstruct.personne_id' => array( 'type' => 'hidden' ),
			'Orientstruct.statut_orient' => array( 'type' => 'hidden' ),
			'Orientstruct.selection' => array( 'type' => 'checkbox' ),
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
		public function searchQuery( array $types = array(), $nouvelentrant = true ) {

			$types += array(
				// INNER JOIN
				'Foyer' => 'INNER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Calculdroitrsa' => 'INNER',
				'Personne' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT',
				'Adresse' => 'INNER',
				'Historiquedroit' => 'INNER',
				'Informationpe' => 'INNER',

				// LEFT OUTER JOIN
				'Orientstruct' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Typeorient' => 'LEFT OUTER',
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

			//période pour les nouveaux entrants
			$dates = parent::datePeriodeCohorte ();

			if( $query === false ) {
				App::uses('WebrsaModelUtility', 'Utility');
				$query = $this->Allocataire->searchQuery( $types, 'Dossier' );

				$query['fields']['Personne.id'] = 'DISTINCT ON ("Personne"."id") "Personne"."id" as "Personne__id"';
				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					// Champs nécessaires au traitement de la search
					array(
						'Historiqueetatpe.identifiantpe',
						'Historiqueetatpe.date',
						'Historiqueetatpe.id',
						'Orientstruct.id',
						'Orientstruct.origine',
						'Orientstruct.personne_id',
						'Orientstruct.statut_orient',
						'Orientstruct.typeorient_id',
						'Orientstruct.structurereferente_id',
						'Orientstruct.date_valid',
						'Rendezvous.personne_id',
						'Dossier.id',
						'Dossier.dtdemrsa',
						'Dossier.ddarrmut',
						'Historiquedroit.created'
					)
				);

				//on récupère les jointures sur foyer et personne pour pouvoir faire la jointure sur historique droit au début.
				//cela permet de réduire le temps d'execution de la requête
				[$join, $query] = parent::separeJointures($query);


				// 2. Jointure
				$query['joins'] = array_merge(
					$join,
					[
						$this->Personne->join('Historiquedroit',
							array(
								'type' => 'INNER',
								'conditions' => parent::conditionsJointureHistoriquedroit($dates, $nouvelentrant)
							)
						)
					],
					$query['joins'],
					parent::jointures(),
					array(
						$this->Personne->Orientstruct->join( 'Structurereferente', array( $types['Structurereferente'] ) ),
						$this->Personne->Orientstruct->join( 'Typeorient', array( $types['Typeorient'] ) ),
						$this->Historiqueetatpe->Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', $types['Informationpe'] ),
						$this->Historiqueetatpe->Informationpe->join( 'Historiqueetatpe', array( $types['Historiqueetatpe'] ) ),
					)
				);

				// 4. Conditions
				// SDD & DOV
				$query = $this->sdddov($query);

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
				if ( $value['Orientstruct']['selection'] === '0' ) {
					unset($data[$key]);
					continue;
				}

				$data[$key]['Orientstruct']['personne_id'] = Hash::get($value, 'Personne.id');
				$data[$key]['Orientstruct']['origine'] = 'cohorte';
				$data[$key]['Orientstruct']['statut_orient'] = 'Orienté';
				$data[$key]['Orientstruct']['structureorientante_id'] =
					Configure::read('PlanPauvrete.Cohorte.ValeursOrientations.structureorientante_id');
				$data[$key]['Orientstruct']['referentorientant_id'] =
					Configure::read('PlanPauvrete.Cohorte.ValeursOrientations.referentorientant_id');
				$data[$key]['Orientstruct']['typeorient_id'] =
					Configure::read('PlanPauvrete.Cohorte.ValeursOrientations.typeorient_id');
				$data[$key]['Orientstruct']['structurereferente_id'] =
					Configure::read('PlanPauvrete.Cohorte.ValeursOrientations.structurereferente_id');
				$data[$key]['Orientstruct']['referent_id'] =
					Configure::read('PlanPauvrete.Cohorte.ValeursOrientations.referent_id');
				$data[$key]['Orientstruct']['date_propo'] = date ('Y-m-d');
				$data[$key]['Orientstruct']['typenotification'] =
					Configure::read('PlanPauvrete.Cohorte.ValeursOrientations.typenotification');
			}
			$this->Personne->Orientstruct->begin();

			$success = !empty($data)
				&& $this->Personne->Orientstruct->saveAll($data, array('atomic' => false));

			if ($success) {
				$this->Personne->Orientstruct->commit();
			} else {
				$this->Personne->Orientstruct->rollback();
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


			return $query;
		}
	}
?>