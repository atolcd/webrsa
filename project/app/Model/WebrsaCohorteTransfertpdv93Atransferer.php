<?php
	/**
	 * Code source de la classe WebrsaCohorteTransfertpdv93Atransferer.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );


	/**
	 * La classe WebrsaCohorteTransfertpdv93Atransferer représente la partie
	 * "logique métier" de la cohorte de transferts PDV à à transférer du CG 93.
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteTransfertpdv93Atransferer extends AbstractWebrsaCohorte
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteTransfertpdv93Atransferer';

		/**
		 * Modèles utilisés par ce modèle
		 *
		 * @var array
		 */
		public $uses = array( 'Dossier', 'Allocataire', 'Transfertpdv93', 'Orientstruct' );

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Dossier.id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Transfertpdv93.action' => array( 'type' => 'radio', 'fieldset' => false, 'legend' => false, 'div' => false ),
			'Transfertpdv93.structurereferente_dst_id' => array( 'type' => 'select', 'label' => '', 'empty' => true, 'required' => false ),
			'Transfertpdv93.personne_id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Transfertpdv93.vx_orientstruct_id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Transfertpdv93.vx_adressefoyer_id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Transfertpdv93.nv_adressefoyer_id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
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
		 * Retourne le querydata de base à utiliser dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				'Detaildroitrsa' => 'INNER',
				'Foyer' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Detailcalculdroitrsa' => 'INNER',
				'Adressefoyer' => 'INNER',
				'VxAdressefoyer' => 'INNER',
				'Personne' => 'INNER',
				'Adresse' => 'INNER',
				'VxAdresse' => 'INNER',
				'Calculdroitrsa' => 'INNER',
				'Orientstruct' => 'INNER',
				'Contratinsertion' => 'LEFT OUTER',
				'Cer93' => 'LEFT OUTER',
				'Prestation' => 'INNER',
				'Structurereferente' => 'INNER',
				'Typeorient' => 'INNER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER'
			);

			$cacheKey = Inflector::underscore( $this->Transfertpdv93->useDbConfig ).'_'.Inflector::underscore( $this->Transfertpdv93->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Dossier' );

				// Jointures supplémentaires
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						array_words_replace(
							$this->Dossier->Foyer->join(
								'Adressefoyer',
								array(
									'type' => $types['VxAdressefoyer'],
									'conditions' => array(
										'Adressefoyer.id IN( '.$this->Dossier->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
									)
								)
							),
							array( 'Adressefoyer' => 'VxAdressefoyer', '01' => '02' )
						),
						array_words_replace(
							$this->Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => $types['VxAdresse'] ) ),
							array( 'Adressefoyer' => 'VxAdressefoyer', 'Adresse' => 'VxAdresse' )
						),
						$this->Dossier->Foyer->Personne->join(
							'Orientstruct',
							array(
								'type' => $types['Orientstruct'],
								'conditions' => array(
									'Orientstruct.statut_orient' => 'Orienté',
									'Orientstruct.id IN ( '.$this->Dossier->Foyer->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere().' )'
								)
							)
						),
						$this->Dossier->Foyer->Personne->join(
							'Contratinsertion',
							array(
								'type' => $types['Contratinsertion'],
								'conditions' => array(
									'Contratinsertion.id IN ( '.$this->Dossier->Foyer->Personne->Contratinsertion->WebrsaContratinsertion->sqDernierContrat().' )'
								)
							)
						),
						$this->Dossier->Foyer->Personne->Contratinsertion->join( 'Cer93', array( 'type' => $types['Cer93'] ) ),
						$this->Dossier->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => $types['Structurereferente'] ) ),
						$this->Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => $types['Typeorient'] ) ),
						$this->Dossier->Detaildroitrsa->join(
							'Detailcalculdroitrsa',
							array(
								'type' => $types['Detailcalculdroitrsa'],
								'conditions' => array(
									'Detailcalculdroitrsa.id IN ( '.$this->Dossier->Foyer->Dossier->Detaildroitrsa->Detailcalculdroitrsa->sqDernier( 'Detaildroitrsa.id' ).' )'
								)
							)
						)
					)
				);

				// Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					array(
						'Dossier.id',
						'Personne.id',
						'Orientstruct.id',
						'Orientstruct.typeorient_id',
						'Adressefoyer.id',
						'Adresse.numcom',
						'VxAdressefoyer.id',
						'Structurereferente.typeorient_id'
					),
					$query['fields'],
					array_words_replace(
						ConfigurableQueryFields::getModelsFields(
							array(
								$this->Dossier->Foyer->Adressefoyer,
								$this->Dossier->Foyer->Adressefoyer->Adresse
							)
						),
						array( 'Adressefoyer' => 'VxAdressefoyer', 'Adresse' => 'VxAdresse' )
					),
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Dossier->Foyer->Personne->Orientstruct,
							$this->Dossier->Foyer->Personne->Contratinsertion,
							$this->Dossier->Foyer->Personne->Contratinsertion->Cer93,
							$this->Dossier->Foyer->Personne->Orientstruct->Typeorient,
							$this->Dossier->Foyer->Personne->Orientstruct->Structurereferente,
							$this->Dossier->Detaildroitrsa->Detailcalculdroitrsa,
						)
					)
				);

				// Ajout des conditions de base
				$sqZonesgeographiquesStructuresreferentes = $this->Dossier->Foyer->Personne->Orientstruct->Structurereferente->StructurereferenteZonegeographique->sq(
					array(
						'alias' => 'structuresreferentes_zonesgeographiques',
						'fields' => array( 'zonesgeographiques.codeinsee' ),
						'joins' => array(
							array_words_replace(
								$this->Dossier->Foyer->Personne->Orientstruct->Structurereferente->StructurereferenteZonegeographique->join( 'Zonegeographique', array( 'type' => 'INNER' ) ),
								array(
									'StructurereferenteZonegeographique' => 'structuresreferentes_zonesgeographiques',
									'Zonegeographique' => 'zonesgeographiques',
								)
							)
						),
						'contain' => false,
						'conditions' => array(
							'structuresreferentes_zonesgeographiques.structurereferente_id = Structurereferente.id'
						)
					)
				);

				$query['conditions'] = array_merge(
					$query['conditions'],
					array(
						'Adresse.numcom LIKE' => Configure::read( 'Cg.departement' ).'%',
						'Adresse.numcom <> VxAdresse.numcom',
						"Adressefoyer.dtemm > Orientstruct.date_valid",
						'Structurereferente.filtre_zone_geo' => true,
						"Adresse.numcom NOT IN ( {$sqZonesgeographiquesStructuresreferentes} )",
					)
				);

				// Tri par défaut
				$query['order'] = array(
					'Adressefoyer.dtemm DESC',
					'Dossier.id ASC',
					'Personne.nom ASC',
					'Personne.prenom ASC'
				);

				// Enregistrement dans le cache
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
			// 1. On complète les conditions de base de l'allocataire
			$query = $this->Allocataire->searchConditions( $query, $search );

			// Filtre par date d'orientation
			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'Orientstruct.date_valid' );

			// Filtre par type d'orientation
			$typeorient_id = Hash::get( $search, 'Orientstruct.typeorient_id' );
			if( !empty( $typeorient_id ) ) {
				$query['conditions']['Orientstruct.typeorient_id'] =  $typeorient_id;
			}

			return $query;
		}

		/**
		 * Préremplissage des champs du formulaire de cohorte.
		 *
		 * @param array $results
		 * @param array $params
		 * @param array $options
		 * @return array
		 */
		public function prepareFormDataCohorte( array $results, array $params = array(), array &$options = array() ) {
			$data = array();

			foreach( $results as $key => $result ) {
				$data[$key] = array(
					'Transfertpdv93' => array(
						'action' => '0',
						'personne_id' => $result['Personne']['id'],
						'vx_orientstruct_id' => $result['Orientstruct']['id'],
						'vx_adressefoyer_id' => $result['VxAdressefoyer']['id'],
						'nv_adressefoyer_id' => $result['Adressefoyer']['id'],
						'structurereferente_dst_id' => null
					)
				);

				// Pré-sélection de la structure référente de destination
				$needles = array();
				$haystack = (array)Hash::get($options, "Transfertpdv93.structurereferente_dst_id.{$result['Adresse']['numcom']}.{$result['Orientstruct']['typeorient_id']}");
				foreach( array_keys( $haystack ) as $needle ) {
					if( $data[$key]['Transfertpdv93']['structurereferente_dst_id'] === null ) {
						if( strpos( $needle, "{$result['Adresse']['numcom']}_" ) === 0 ) {
							$needles[] = $needle;
						}
					}
				}

				// Si aucune ou plusieurs structures du même type oeuvrent sur la commune, pas de présélection
				if( count($needles) === 1 ) {
					$data[$key]['Transfertpdv93']['structurereferente_dst_id'] = $needles[0];
				}
			}

			return $data;
		}

		/**
		 * Enregistrement du formulaire de cohorte: ...
		 *
		 * @param array $data
		 * @param array $params
		 * @param integer $user_id
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$success = true;
			$validationErrors = array();
			
			$this->Transfertpdv93->begin();

			foreach( $data as $key => $line ) {
				if( (string)Hash::get( $line, 'Transfertpdv93.action' ) === '1' ) {
					$structurereferente_dst_id = Hash::get( $line, 'Transfertpdv93.structurereferente_dst_id' );

					// Puisqu'on peut en fait être réorienté du Socioprofessionnel en Emploi, il va falloir aller chercher la bonne valeur de typeorient_id à partir de la structure désignée
					$query = array(
						'fields' => array( 'Structurereferente.typeorient_id' ),
						'conditions' => array(
							'Structurereferente.id' => suffix( $structurereferente_dst_id )
						),
						'contain' => false
					);
					$structurereferente = $this->Orientstruct->Structurereferente->find( 'first', $query );
					$typeorient_id = Hash::get( $structurereferente, 'Structurereferente.typeorient_id' );

					$orientstruct = array(
						'Orientstruct' => array(
							'personne_id' => $line['Transfertpdv93']['personne_id'],
							'typeorient_id' => $typeorient_id,
							'structurereferente_id' => suffix( $structurereferente_dst_id ),
							'date_valid' => date( 'Y-m-d' ),
							'statut_orient' => 'Orienté',
							'user_id' => $user_id,
							'origine' => 'demenagement'
						)
					);

					// Info: il faut que le transfert soit créé pour que le bon PDF d'orientation soit généré
					$this->Orientstruct->Behaviors->disable( 'StorablePdf' );
					$this->Orientstruct->create( $orientstruct );
					$success = $this->Orientstruct->save( null, array( 'atomic' => false ) ) && $success;
					$this->Orientstruct->forceRecalculeRang ($orientstruct);
					$this->Orientstruct->Behaviors->enable( 'StorablePdf' );

					if( !empty( $this->Orientstruct->validationErrors ) ) {
						$validationErrors[$key]['structurereferente_dst_id'] = array_unique( Hash::extract( $this->Orientstruct->validationErrors, '{s}.{n}' ) );
					}

					if( $success ) {
						if( !empty( $structurereferente_dst_id ) ) {
							$line['Transfertpdv93']['user_id'] = $orientstruct['Orientstruct']['user_id'];
							$line['Transfertpdv93']['vx_orientstruct_id'] = $line['Transfertpdv93']['vx_orientstruct_id'];
							$line['Transfertpdv93']['nv_orientstruct_id'] = $this->Orientstruct->id;

							$this->Transfertpdv93->create( $line );
							$success = $this->Transfertpdv93->save( null, array( 'atomic' => false ) ) && $success;
							if( !empty( $this->Transfertpdv93->validationErrors ) ) {
								$validationErrors[$key]['structurereferente_dst_id'] = array_unique( Hash::extract( $this->Transfertpdv93->validationErrors, '{s}.{n}' ) );
							}
						}

						// Maintenant que les données du transfert ont été enregistrées, on peut générer le bon PDF d'orientation
						if( $success ) {
							$success = $this->Orientstruct->generatePdf( $this->Orientstruct->id ) && $success;
						}

						// Si on change de PDV, et que l'allocataire possède un D1 sans D2 dans l'ancien PDV, on enregistre automatiquement un D2
						if( $success && $line['Transfertpdv93']['vx_orientstruct_id'] !== $line['Transfertpdv93']['nv_orientstruct_id'] ) {
							$questionnaired1pdv93_id = $this->Orientstruct->Personne->Questionnaired2pdv93->questionnairesd1pdv93Id( $line['Transfertpdv93']['personne_id'] );
							if( !empty( $questionnaired1pdv93_id ) ) {
								$success = $this->Orientstruct->Personne->Questionnaired2pdv93->saveAuto(
									$line['Transfertpdv93']['personne_id'],
									'changement_situation',
									'modif_commune'
								) && $success;
							}
						}

						// On clôture le référent actuel à la date
						$count = $this->Orientstruct->Personne->PersonneReferent->find(
							'count',
							array(
								'conditions' => array(
									'PersonneReferent.personne_id' => $line['Transfertpdv93']['personne_id'],
									'PersonneReferent.dfdesignation IS NULL'
								)
							)
						);

						$datedfdesignation = ( is_array( date( 'Y-m-d' ) ) ? date_cakephp_to_sql( date( 'Y-m-d' ) ) : date( 'Y-m-d' ) );

						if( $count > 0 ) {
							$success = $this->Orientstruct->Personne->PersonneReferent->updateAllUnBound(
								array( 'PersonneReferent.dfdesignation' => '\''.$datedfdesignation.'\'' ),
								array(
									'"PersonneReferent"."personne_id"' => $line['Transfertpdv93']['personne_id'],
									'PersonneReferent.dfdesignation IS NULL'
								)
							) && $success;
						}
					}
				}
			}

			$this->Transfertpdv93->validationErrors = $validationErrors;
			
			if ($success) {
				$this->Transfertpdv93->commit();
			} else {
				$this->Transfertpdv93->rollback();
			}

			return $success;
		}

		/**
		 * Liste des structures référentes groupées par type d'orientation.
		 *
		 * @return array
		 */
		public function structuresParZonesGeographiques() {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$results = Cache::read( $cacheKey );

			if( $results === false ) {
				$results = $this->Orientstruct->Typeorient->find(
					'all',
					array(
						'fields' => array(
							'Typeorient.id',
							'Typeorient.lib_type_orient',
							'Structurereferente.id',
							'Structurereferente.lib_struc',
							'Zonegeographique.codeinsee'
						),
						'conditions' => array(
							'Typeorient.actif' => 'O',
							'Structurereferente.actif' => 'O',
						),
						'joins' => array(
							$this->Orientstruct->Typeorient->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
							$this->Orientstruct->Typeorient->Structurereferente->join( 'StructurereferenteZonegeographique', array( 'type' => 'INNER' ) ),
							$this->Orientstruct->Typeorient->Structurereferente->StructurereferenteZonegeographique->join( 'Zonegeographique', array( 'type' => 'INNER' ) )
						),
						'contain' => false,
						'order' => array(
							'Zonegeographique.codeinsee ASC',
							'Typeorient.lib_type_orient ASC',
							'Structurereferente.lib_struc ASC',
						)
					)
				);

				$tmp = array();
				if( !empty( $results ) ) {
					foreach( $results as $result ) {
						if( !isset( $tmp[$result['Typeorient']['id']] ) ) {
							$tmp[$result['Typeorient']['id']] = array();
						}

						$tmp[$result['Typeorient']['id']]["{$result['Zonegeographique']['codeinsee']}_{$result['Structurereferente']['id']}"] = $result['Structurereferente']['lib_struc'];
					}
				}
				$results = $tmp;

				Cache::write( $cacheKey, $results );
				ModelCache::write( $cacheKey, array( 'Typeorient', 'Structurereferente', 'Zonegeographique' ) );
			}

			return $results;
		}

		/**
		 * Liste des structures référentes groupées par code INSEE.
		 *
		 * @return array
		 */
		public function structuresParZonesGeographiquesPourTransfertPdv() {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$results = Cache::read( $cacheKey );

			if( $results === false ) {
				$structuresParZonesGeographiques = $this->structuresParZonesGeographiques();

				// Comptage
				$foos = array();
				foreach( $structuresParZonesGeographiques as $typeorient_id => $datas ) {
					foreach( $datas as $key => $label ) {
						list( $codeinsee, $structurereferente_id ) = explode( '_', $key );

						if( !isset( $foos[$codeinsee][$typeorient_id] ) ) {
							$foos[$codeinsee][$typeorient_id] = 0;
						}
						$foos[$codeinsee][$typeorient_id]++;
					}
				}

				// Nouvelle liste d'options
				// Configure::write( 'Orientstruct.typeorientprincipale', array( 'Socioprofessionnelle' => array( 1 ), 'Social' => array( 2 ), 'Emploi' => array( 3 ) ) );
				$typesorients = Configure::read( 'Orientstruct.typeorientprincipale' );
				$pdvsCodeInsee = array();
				foreach( $foos as $codeinsee => $datas ) {

					$hasSociopro = false;
					foreach( $typesorients['Socioprofessionnelle'] as $typeorient_sociopro_id ) {
						if( isset( $datas[$typeorient_sociopro_id] ) && !empty( $datas[$typeorient_sociopro_id] ) ) {
							$hasSociopro = true;
						}
					}

					$pdvsCodeInsee[$codeinsee] = $hasSociopro;
				}

				$results = array();
				foreach( $pdvsCodeInsee as $codeinsee => $hasPdv ) {
					$results[$codeinsee] = $structuresParZonesGeographiques;

					// Si mon code INSEE n'a pas de sociopro, alors les options auront tous les sociopro + tous les emploi
					if( !$hasPdv ) {
						foreach( $typesorients['Socioprofessionnelle'] as $typeorient_sociopro_id ) {
							if (!isset($results[$codeinsee][$typeorient_sociopro_id])) {
								break;
							}
							foreach( $typesorients['Emploi'] as $typeorient_emploi_id ) {
								$results[$codeinsee][$typeorient_sociopro_id] = array_merge(
									$results[$codeinsee][$typeorient_sociopro_id],
									$results[$codeinsee][$typeorient_emploi_id]
								);
							}
						}
					}
				}

				Cache::write( $cacheKey, $results );
				ModelCache::write( $cacheKey, array( 'Typeorient', 'Structurereferente', 'Zonegeographique' ) );
			}

			return $results;
		}

		/**
		 * Suppression et regénération du cache.
		 *
		 * @return boolean
		 */
		protected function _regenerateCache() {
			// Suppression des éléments du cache.
			$this->_clearModelCache();
			$success = true;

			// Regénération des éléments du cache.
			$success = ( $this->structuresParZonesGeographiques() !== false ) && $success;
			$success = ( $this->structuresParZonesGeographiquesPourTransfertPdv() !== false ) && $success;

			return $success;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les fonctions vides.
		 */
		public function prechargement() {
			$success = $this->_regenerateCache();
			return $success;
		}
	}
?>