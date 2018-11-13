<?php
	/**
	 * Code source de la classe WebrsaCohorteContratinsertionNouveau.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteContratinsertionNouveau ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteContratinsertionNouveau extends AbstractWebrsaCohorte
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteContratinsertionNouveau';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		// FIXME: on n'en a plus besoin ? Nettoyer dans les autres
		/*public $keysRecherche = array(
			'ConfigurableQueryNonorientationsproscovs58.cohorte.fields',
			'ConfigurableQueryNonorientationsproscovs58.cohorte.innerTable',
			'ConfigurableQueryNonorientationsproscovs58.exportcsv'
		);*/

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Allocataire', 'Contratinsertion' );

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Dossier.id' => array( 'type' => 'hidden', 'hidden' => true ),
			'Contratinsertion.id' => array( 'type' => 'hidden', 'hidden' => true ),
			'Contratinsertion.personne_id' => array( 'type' => 'hidden', 'hidden' => true ),
			'Contratinsertion.decision_ci' => array( 'type' => 'select' ),
			'Contratinsertion.datevalidation_ci' => array( 'type' => 'date' ),
			'Contratinsertion.observ_ci' => array( 'type' => 'textarea' ),
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
				'Cer93' => 'INNER',
				'Personne' => 'INNER',
				'Referent' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Prestation' => 'INNER',
				'Dossier' => 'INNER',
				'Adressefoyer' => 'LEFT OUTER',
				'Adresse' => 'LEFT OUTER',
				'Typeorient' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'Orientstruct' => 'LEFT OUTER'
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Contratinsertion' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Contratinsertion,
							$this->Contratinsertion->Cer93,
							$this->Contratinsertion->Referent,
							$this->Contratinsertion->Structurereferente,
							$this->Contratinsertion->Personne->Orientstruct,
							$this->Contratinsertion->Personne->Orientstruct->Typeorient
						)
					),
					array(
						'Personne.id',
						'Dossier.id',
						'Contratinsertion.id',
						'Contratinsertion.personne_id',
						'Contratinsertion.dd_ci',
						'Contratinsertion.df_ci',
						'Contratinsertion.datevalidation_ci',
						'Contratinsertion.observ_ci',
					)
				);

				// 2. Jointures
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Contratinsertion->join( 'Cer93', array( 'type' => $types['Cer93'] ) ),
						$this->Contratinsertion->join( 'Referent', array( 'type' => $types['Referent'] ) ),
						$this->Contratinsertion->join( 'Structurereferente', array( 'type' => $types['Structurereferente'] ) ),
						$this->Contratinsertion->Personne->join( 'Orientstruct',
							array(
								'type' => $types['Orientstruct'],
								'conditions' => array(
									'Orientstruct.statut_orient' => 'Orienté',
									'Orientstruct.id IN ( '.$this->Contratinsertion->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere().' )'
								)
							)
						),
						$this->Contratinsertion->Personne->Orientstruct->join( 'Typeorient', array( 'type' => $types['Typeorient'] ) ),
					)
				);

				// 3. Conditions
				// Contrats en attente de validation
				$query['conditions'][] = array(
					'OR' => array(
						'Contratinsertion.decision_ci' => 'E',
						'Contratinsertion.decision_ci IS NULL'
					)
				);

				$query['conditions'][] = 'Contratinsertion.id NOT IN (
					'.$this->Contratinsertion->Contratcomplexeep93->sq(
						array(
							'fields' => array( 'contratscomplexeseps93.contratinsertion_id' ),
							'alias' => 'contratscomplexeseps93',
							'joins' => array(
								array(
									'table'      => 'dossierseps',
									'alias'      => 'dossierseps',
									'type'       => 'INNER',
									'foreignKey' => false,
									'conditions' => array( 'dossierseps.id = contratscomplexeseps93.dossierep_id' )
								),
							),
							'conditions' => array(
								'contratscomplexeseps93.contratinsertion_id = Contratinsertion.id',
								'dossierseps.id NOT IN (
									'.$this->Contratinsertion->Contratcomplexeep93->Dossierep->Passagecommissionep->sq(
										array(
											'fields' => array( 'passagescommissionseps.dossierep_id' ),
											'alias' => 'passagescommissionseps',
											'conditions' => array(
												'passagescommissionseps.dossierep_id = dossierseps.id',
												'passagescommissionseps.etatdossierep' => 'annule',
											),
										)
									).'
								)'
							),
						)
					).'
				)';

				// 4. Tri par défaut
				$query['order'] = array( 'Contratinsertion.df_ci' => 'ASC' );

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

			// Date de création
			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'Contratinsertion.created' );

			foreach( array( 'structurereferente_id', 'referent_id' ) as $field ) {
				$value = (string)suffix( Hash::get( $search, "Contratinsertion.{$field}" ) );
				if( $value !== '' ) {
					$query['conditions']["Contratinsertion.{$field}"] = $value;
				}
			}

			// Forme du CER
			$query['conditions']['Contratinsertion.forme_ci'] = Hash::get( $search, 'Contratinsertion.forme_ci' );

			// Condition sur le projet insertion emploi territorial de la structure du CER
			$query['conditions'] = $this->conditionCommunautesr(
				$query['conditions'],
				$search,
				array( 'Contratinsertion.communautesr_id' => 'Contratinsertion.structurereferente_id' )
			);

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
			$data = parent::prepareFormDataCohorte( $results, $params );

			foreach( $data as $key => $line ) {
				$line = array(
					'Contratinsertion' => $line['Contratinsertion'],
					'Dossier' => $line['Dossier']
				);

				if( empty( $line['Contratinsertion']['datevalidation_ci'] ) ) {
					$line['Contratinsertion']['datevalidation_ci'] = $line['Contratinsertion']['dd_ci'];
				}

				$data[$key] = $line;
			}

			return $data;
		}

		/**
		 * Tentative de sauvegarde de nouveaux dossiers de COV pour la thématique
		 * à partir de la cohorte.
		 *
		 * @param array $data
		 * @param array $params
		 * @param integer $user_id
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$success = parent::saveCohorte($data, $params, $user_id);
			$contratsatraiter = Hash::extract( $data, '{n}.Contratinsertion[decision_ci!=E]' );

			$success = $this->Contratinsertion->saveAll( $data, array( 'validate' => 'only', 'atomic' => false ) ) && $success;

			if( $success ) {
				$this->Contratinsertion->begin();
				$success = $this->Contratinsertion->saveAll( $contratsatraiter, array( 'validate' => 'first', 'atomic' => false ) );

				if( $success ) {
					$this->Contratinsertion->commit();
				}
				else {
					$this->Contratinsertion->rollback();
				}
			}

			return $success;
		}
	}
?>