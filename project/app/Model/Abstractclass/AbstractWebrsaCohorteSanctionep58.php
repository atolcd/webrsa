<?php
	/**
	 * Code source de la classe AbstractWebrsaCohorteSanctionep58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe AbstractWebrsaCohorteSanctionep58 ...
	 *
	 * @package app.Model.Abstractclass
	 */
	abstract class AbstractWebrsaCohorteSanctionep58 extends AbstractWebrsaCohorte
	{
		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Allocataire', 'Personne', 'Informationpe', 'Sanctionep58' );

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			// TODO: quels champs ?
			'Dossier.id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Personne.id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Historiqueetatpe.id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Orientstruct.id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Dossierep.id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Dossierep.chosen' => array( 'type' => 'checkbox', 'label' => '&nbsp;' )
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

			foreach( $results as $key => $result ) {
				$data[$key]['Dossierep']['chosen'] = $result['Dossierep']['chosen'];
			}

			return $data;
		}

		/**
		 * Retourne l'origine de la cohorte: radiepe ou noninscritpe à partir du
		 * nom de la classe concrête (WebrsaCohorteSanctionep58Radiepe ou
		 * WebrsaCohorteSanctionep58Noninscritpe).
		 *
		 *
		 * @return string
		 * @throws RuntimeException Lorsque l'origine n'est pas une des valeurs
		 *	attendue
		 */
		protected function _origine() {
			$origine = Inflector::underscore( preg_replace( '/^WebrsaCohorteSanctionep58/', '', get_class( $this ) ) );
			$valid = array( 'radiepe', 'noninscritpe' );

			if( in_array( $origine, $valid ) === false ) {
				$message = sprintf( '', implode( ', ', $valid ), $origine );
				throw new RuntimeException( $message );
			}

			return $origine;
		}

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$origine = $this->_origine();

			$types += array(
				'Adressefoyer' => 'LEFT OUTER',
				'Adresse' => 'LEFT OUTER',
				'Calculdroitrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'Dossierep' => 'LEFT OUTER',
				'Passagecommissionep' => 'LEFT OUTER',
				'Historiqueetatpe' => $origine === 'radiepe' ? 'INNER' : 'LEFT OUTER',
				'Informationpe' => $origine === 'radiepe' ? 'INNER' : 'LEFT OUTER',
				'Orientstruct' => 'LEFT OUTER',
				'Sanctionep58' => 'LEFT OUTER',
				'Typeorient' => 'LEFT OUTER',
				'Serviceinstructeur' => 'LEFT OUTER',
				'Referentorientant' => 'LEFT OUTER',
				'Structureorientante' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Suiviinstruction' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery($types);

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					array('DISTINCT Personne.id'),
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Personne->Foyer->Dossier->Suiviinstruction,
							$this->Personne->Foyer->Dossier->Suiviinstruction->Serviceinstructeur,
							$this->Personne->Orientstruct,
							$this->Personne->Orientstruct->Referentorientant,
							$this->Personne->Orientstruct->Structureorientante,
							$this->Personne->Orientstruct->Structurereferente,
							$this->Personne->Orientstruct->Typeorient,
							$this->Informationpe,
							$this->Informationpe->Historiqueetatpe
						)
					),
					array(
						'Dossier.id',
						'Historiqueetatpe.id',
						'Orientstruct.id',
						'Dossierep.id',
						'( "Dossierep"."id" IS NOT NULL AND "Passagecommissionep"."etatdossierep" IS NULL ) AS "Dossierep__chosen"',
					)
				);

				// 2. Jointures
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Personne->join(
							'Orientstruct',
							array(
								'type' => $types['Typeorient'],
								'conditions' => array(
									'Orientstruct.id IN ('.$this->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere().')',
									// En emploi
									'Orientstruct.typeorient_id IN (
										SELECT t.id
											FROM typesorients AS t
											WHERE t.id IN ( '.implode( ',', (array)Configure::read( 'Typeorient.emploi_id' ) ).' )
									)'
								)
							)
						),
						$this->Personne->Orientstruct->join( 'Typeorient', array( 'type' => $types['Typeorient'] ) ),
						$this->Personne->Orientstruct->join( 'Sanctionep58', array( 'type' => $types['Sanctionep58'] ) ),
						$this->Personne->Orientstruct->join( 'Referentorientant', array( 'type' => $types['Referentorientant'] ) ),
						$this->Personne->Orientstruct->join( 'Structureorientante', array( 'type' => $types['Structureorientante'] ) ),
						$this->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => $types['Structurereferente'] ) ),
						$this->Personne->Orientstruct->Sanctionep58->join( 'Dossierep', array( 'type' => $types['Dossierep'] ) ),
						$this->Personne->Orientstruct->Sanctionep58->Dossierep->join( 'Passagecommissionep', array( 'type' => $types['Passagecommissionep'] ) ),
						$this->Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', $types['Informationpe'] ),
						$this->Informationpe->Historiqueetatpe->joinInformationpeHistoriqueetatpe( true, 'Informationpe', 'Historiqueetatpe', $types['Historiqueetatpe'] ),
						$this->Personne->Foyer->Dossier->join( 'Suiviinstruction', array( 'type' => $types['Suiviinstruction'] ) ),
						$this->Personne->Foyer->Dossier->Suiviinstruction->join( 'Serviceinstructeur', array( 'type' => $types['Serviceinstructeur'] ) )
					)
				);

				// 3. Conditions
				// Qui sont sélectionnables pour passer en EP
				$conditionsSelection = Configure::read( 'Dossierseps.conditionsSelection' );
				if( !empty( $conditionsSelection ) ) {
					$query['conditions'][] = $conditionsSelection;
				}

				// Et qui ne possèdent pas d'autre dossier d'EP non traité
				$query['conditions'][] = $this->Personne->Dossierep->conditionsPersonneSansDossierEpEnCours();

				// Et qui ne possède pas de dossier EP associé
				$query['conditions'][] = array(
					'OR' => array(
						'Passagecommissionep.id IS NULL',
						"Passagecommissionep.etatdossierep NOT IN ('associe')"
					)
				);

				// Qui sont orientés au Pôle Emploi
				$query['conditions'][] = array(
					'Orientstruct.structurereferente_id' => Configure::read( 'Sanctionseps58.selection.structurereferente_id' )
				);

				if( $origine === 'radiepe' ) {
					$query['conditions'][] = 'Informationpe.id IN ('.$this->Informationpe->sqDerniere( 'Personne' ).')';

					// Qui sont orientés au Pôle Emploi
					$query['conditions'][] = array(
						'Orientstruct.structurereferente_id' => Configure::read( 'Sanctionseps58.selection.structurereferente_id' )
					);

					// Si on ne trouve pas la clé Selectionradies.conditions dans la
					// configuration, on ne se basera que sur l'état "radiation",
					// sinon on utlisera les conditions définies dans la configuration (CG 58).
					$conditionsConfigure = Configure::read( 'Selectionradies.conditions' );
					if( empty( $conditionsConfigure ) ) {
						$query['conditions'][] = array( 'Historiqueetatpe.etat' => 'radiation' );
					}
					else {
						$query['conditions'][] = $conditionsConfigure;
					}
				}
				else {
					$query['conditions'][] = array(
						'OR' => array(
							'Informationpe.id IS NULL',
							'Informationpe.id IN ('.$this->Informationpe->sqDerniere( 'Personne' ).')'
						)
					);

					// -------------------------------------------------------------

					$qdNonInscrits = $this->Informationpe->qdNonInscrits();
					$query['conditions'][] = $qdNonInscrits['conditions'];

					$qdRadies = $this->Informationpe->qdRadies();

					$qdRadies['fields'] = array( 'Personne.id' );
					$qdRadies['alias'] = 'personnesradiees';
					$qdRadies['joins'][] = $this->Personne->Dossierep->Personne->join(
						'Orientstruct',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Orientstruct.id IN ('.$this->Personne->Dossierep->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere().')',
								// en emploi
								'Orientstruct.typeorient_id IN (
									SELECT t.id
										FROM typesorients AS t
										WHERE t.id IN ( '.implode( ',', (array)Configure::read( 'Typeorient.emploi_id' ) ).' )
								)'
							)
						)
					);
					$qdRadies['conditions'][] = 'Orientstruct.typeorient_id IS NOT NULL';

					$qdRadies = array_words_replace(
						$qdRadies,
						array(
							'Personne' => 'personnesradiees',
							'Informationpe' => 'informationsperadiees',
							'Historiqueetatpe' => 'historiqueetatsperadiees',
							'Orientstruct' => 'orientsstructsradiees',
						)
					);
					$qdRadies['conditions'][] = 'personnesradiees.id = Personne.id';

					$sqRadies = $this->Personne->Dossierep->Personne->sq( $qdRadies );

					$query['conditions'][] = "\"Personne\".\"id\" NOT IN ( {$sqRadies} )";
				}

				// 4. Tri par défaut
				$query['order'] = array( 'Dossierep.id DESC' );
				if( $origine === 'radiepe' ) {
					$query['order'] = array_merge(
						$query['order'],
						array(
							'Historiqueetatpe.date ASC',
							'Historiqueetatpe.id ASC'
						)
					);
				}
				else {
					$query['order'] = array_merge(
						$query['order'],
						array( 'Orientstruct.date_valid ASC' )
					);
				}
				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * Logique de sauvegarde de la cohorte
		 *
		 * @see AbstractWebrsaCohorteSanctionep58::_origine()
		 *
		 * @param type $data
		 * @param type $params
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$success = true;
			$origine = $this->_origine();

			$this->Sanctionep58->begin();

			foreach( $data as $line ) {
				// La personne était-elle sélectionnée précédemment ?
				$dossierep_id = Hash::get( $line, 'Dossierep.id' );

				// On vérifie si la personne a un dossier EP en cours et si oui on récupère son dernier passage en EP
				if(!empty($dossierep_id) ) {
					$passageCommision = $this->Sanctionep58->Dossierep->Passagecommissionep->find('first', array(
						'conditions' => array(
							'Passagecommissionep.dossierep_id' => $dossierep_id,
							'Passagecommissionep.etatdossierep NOT IN' => array( 'associe'),
						)
					));
				}

				$chosen = Hash::get( $line, 'Dossierep.chosen' );

				// Personnes non cochées que l'on sélectionne
				if( $chosen == 1 ) {
					// Si la personne a un dossier EP en cours et qu'il n'est pas rattaché à une commission EP on passe à la ligne suivante
					if(!empty($dossierep_id) && empty($passageCommision) ) {
						continue;
					}

					$dossierep = array(
						'Dossierep' => array(
							'themeep' => 'sanctionseps58',
							'personne_id' => $line['Personne']['id']
						)
					);
					$this->Sanctionep58->Dossierep->create( $dossierep );
					$success = $this->Sanctionep58->Dossierep->save( null, array( 'atomic' => false ) ) && $success;

					if( $origine === 'radiepe' ) {
						$sanctionep58 = array(
							'Sanctionep58' => array(
								'dossierep_id' => $this->Sanctionep58->Dossierep->id,
								'orientstruct_id' => $line['Orientstruct']['id'],
								'origine' => $origine,
								'historiqueetatpe_id' => $line['Historiqueetatpe']['id']
							)
						);
					}
					else {
						$sanctionep58 = array(
							'Sanctionep58' => array(
								'dossierep_id' => $this->Sanctionep58->Dossierep->id,
								'orientstruct_id' => $line['Orientstruct']['id'],
								'origine' => $origine
							)
						);
					}

					$this->Sanctionep58->create( $sanctionep58 );
					$success = $this->Sanctionep58->save( null, array( 'atomic' => false ) ) && $success;
				}
				// Si la personne n'est pas sélectionnée, a un dossier EP et pas de passage en commission, le dossier EP
				// doit être supprimé ainsi que la sanctionep correspondante
				elseif( $chosen == 0 && !empty($dossierep_id) && empty($passageCommision)) {
					// Suppression de la sanction EP
					$success = $this->Sanctionep58->deleteAll(
						array(
							'dossierep_id' => $dossierep_id
						),
						false
					) && $success;
					// Suppression du dossier EP
					$success = $this->Sanctionep58->Dossierep->delete($dossierep_id, false) && $success;
				}
			}

			if ($success) {
				$this->Sanctionep58->commit();
			} else {
				$this->Sanctionep58->rollback();
			}

			return $success;
		}
	}
?>