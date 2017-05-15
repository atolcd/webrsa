<?php
	/**
	 * Code source de la classe WebrsaCohorteDossierpcg66Atransmettre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorteDossierpcg66', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteDossierpcg66Atransmettre ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteDossierpcg66Atransmettre extends AbstractWebrsaCohorteDossierpcg66
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteDossierpcg66Atransmettre';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryDossierspcgs66.cohorte_atransmettre.fields',
			'ConfigurableQueryDossierspcgs66.cohorte_atransmettre.innerTable',
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'WebrsaRechercheDossierpcg66',
			'Dossierpcg66',
			'Allocataire',
			'Canton'
		);

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Dossierpcg66.id' => array( 'type' => 'hidden' ),
			'Dossierpcg66.etatdossierpcg' => array( 'type' => 'hidden', 'value' => 'transmisop' ),
			'Dossierpcg66.istransmis' => array( 'type' => 'checkbox' ),
			'Decdospcg66Orgdospcg66.decisiondossierpcg66_id' => array( 'type' => 'hidden' ),
			'Decdospcg66Orgdospcg66.orgtransmisdossierpcg66_id' => array( 'multiple' => 'checkbox', 'class' => 'largeColumn' ),
			'Decisiondossierpcg66.id' => array( 'type' => 'hidden' ),
			'Decisiondossierpcg66.etatop' => array( 'type' => 'hidden', 'value' => 'transmis' ),
			'Decisiondossierpcg66.datetransmissionop' => array( 'type' => 'date' ),
		);

		/**
		 * Valeurs par défaut pour le préremplissage des champs du formulaire de cohorte
		 * array(
		 *		'Mymodel' => array( 'Myfield' => 'MyValue' ) )
		 * )
		 *
		 * @var array
		 */
		public $defaultValues = array();
		
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
				'Personne' => 'INNER',
				'Dossier' => 'INNER',
				'Originepdo' => 'INNER',
				'Typepdo' => 'INNER',
				'Prestation' => 'INNER',
				
				// LEFT JOIN
				'Adressefoyer' => 'LEFT OUTER',
				'Adresse' => 'LEFT OUTER',
				'Serviceinstructeur' => 'LEFT OUTER',
				'Decisiondossierpcg66' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Situationdossierrsa' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Dossierpcg66' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Dossierpcg66,
							$this->Dossierpcg66->Foyer->Personne->PersonneReferent,
							$this->Dossierpcg66->Decisiondossierpcg66,
							$this->Dossierpcg66->Serviceinstructeur,
							$this->Dossierpcg66->Originepdo,
							$this->Dossierpcg66->Typepdo,
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Dossierpcg66.id',
						'Dossierpcg66.foyer_id',
						'Decisiondossierpcg66.id',
					)
				);

				// 2. Jointures
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Dossierpcg66->join('Decisiondossierpcg66', 
							array(
								'type' => $types['Decisiondossierpcg66'],
								'conditions' => array(
									'Decisiondossierpcg66.validationproposition' => 'O',
									'Decisiondossierpcg66.id IN ('
									. 'SELECT "decisionsdossierspcgs66"."id" '
									. 'FROM decisionsdossierspcgs66 '
									. 'WHERE "decisionsdossierspcgs66"."dossierpcg66_id" = "Dossierpcg66"."id" '
									. 'ORDER BY "decisionsdossierspcgs66"."created" DESC '
									. 'LIMIT 1)'
								)
							)
						),
						$this->Dossierpcg66->join('Serviceinstructeur', array('type' => $types['Serviceinstructeur'])),
						$this->Dossierpcg66->join('Originepdo', array('type' => $types['Originepdo'])),
						$this->Dossierpcg66->join('Typepdo', array('type' => $types['Typepdo'])),
					)
				);
				
				// Conditions
				$query['conditions'][] = array('Dossierpcg66.etatdossierpcg' => 'atttransmisop');
				$query['conditions'][] = 'Dossierpcg66.dateimpression IS NOT NULL';
				$query['conditions'][] = array('Dossierpcg66.istransmis' => '0');
				$query['conditions'][] = array('Prestation.rolepers' => 'DEM');
				
				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * Préremplissage du formulaire en cohorte
		 *
		 * @param array $results
		 * @param array $params
		 * @param array $options
		 * @return array
		 */
		public function prepareFormDataCohorte( array $results, array $params = array(), array &$options = array() ) {
			$data = array();
			$orgTransmissisonBaseQuery = array(
				'fields' => array(
					'Decdospcg66Orgdospcg66.orgtransmisdossierpcg66_id'
				),
				'contain' => false
			);

			for ($i=0; $i<count($results); $i++) {
				$data[$i] = $results[$i];

				$condition['conditions'] = array(
					'Decdospcg66Orgdospcg66.decisiondossierpcg66_id' => $results[$i]['Decisiondossierpcg66']['id']
				);

				$data[$i]['Decdospcg66Orgdospcg66']['orgtransmisdossierpcg66_id'] = Hash::extract(
					$this->Dossierpcg66->Decisiondossierpcg66->Decdospcg66Orgdospcg66->find(
						'all', $orgTransmissisonBaseQuery + $condition
					),
					'{n}.Decdospcg66Orgdospcg66.orgtransmisdossierpcg66_id'
				);

				$data[$i]['Decdospcg66Orgdospcg66']['decisiondossierpcg66_id'] = $results[$i]['Decisiondossierpcg66']['id'];

				$data[$i] += $this->defaultValues;
			}

			return $data;
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
			$query = $this->WebrsaRechercheDossierpcg66->searchConditions( $query, $search );
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
			foreach ( $data as $key => $value ) {
				if ( $value['Dossierpcg66']['istransmis'] === '0' ) {
					unset($data[$key]);
				}
			}

			$success = !empty($data);
			if ( $success ) {
				foreach( $data as $key => $value ) {
					// On sauvegarde Dossierpcg66
					$this->Dossierpcg66->create($value['Dossierpcg66']);
					$success = $this->Dossierpcg66->save() && $success;

					// On sauvegarde Decisiondossierpcg66
					$this->Dossierpcg66->Decisiondossierpcg66->create($value['Decisiondossierpcg66']);
					$success = $this->Dossierpcg66->Decisiondossierpcg66->save() && $success;

					// On sauvegarde Decdospcg66Orgdospcg66 (table de liaison entre les organismes et la décision)
					$orgs = (array)Hash::get($value, 'Decdospcg66Orgdospcg66.orgtransmisdossierpcg66_id');
					$datasDec = array();
					foreach ( $orgs as $orgKey => $orgtransmisdossierpcg66_id ) {
						$datasDec[$orgKey] = array(
							'orgtransmisdossierpcg66_id' => $orgtransmisdossierpcg66_id,
							'decisiondossierpcg66_id' => $value['Decdospcg66Orgdospcg66']['decisiondossierpcg66_id']
						);

						// On supprime les anciennes entrées si il y en a (dans le cas d'un "en attente transmission à ...")
						$deleteConditions = array( 'decisiondossierpcg66_id' => $value['Decdospcg66Orgdospcg66']['decisiondossierpcg66_id'] );
						$success = $this->Dossierpcg66->Decisiondossierpcg66->Decdospcg66Orgdospcg66
							->deleteAllUnbound( $deleteConditions, false ) && $success
						;
					}

					// Provoquera une erreur sur le formulaire pour ne pas avoir sélectionné d'organismes
					if ( empty($datasDec) ) {
						$datasDec[$key] = array(
							'orgtransmisdossierpcg66_id' => null,
							'decisiondossierpcg66_id' => $value['Decdospcg66Orgdospcg66']['decisiondossierpcg66_id']
						);
					}

					$success = $this->Dossierpcg66->Decisiondossierpcg66->Decdospcg66Orgdospcg66->saveAll($datasDec) && $success;
				}
			}

			return $success;
		}
	}
?>