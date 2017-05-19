<?php
	/**
	 * Fichier source du modèle Cohortetransfertpdv93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Classe Cohortetransfertpdv93.
	 *
	 * @deprecated since 3.0.0
	 *
	 * @package app.Model
	 */
	class Cohortetransfertpdv93 extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Cohortetransfertpdv93';

		public $useTable = false;

		public $actsAs = array(
			'Conditionnable'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'WebrsaCohorteTransfertpdv93Atransferer'
		);

		/**
		 * Retourne un querydata résultant du traitement du formulaire de
		 * recherche des cohortes de transfert de PDV.
		 *
		 * @param array $mesCodesInsee La liste des codes INSEE à laquelle est lié l'utilisateur
		 * @param boolean $filtre_zone_geo L'utilisateur est-il limité au niveau des zones géographiques ?
		 * @param array $search Critères du formulaire de recherche
		 * @param mixed $lockedDossiers
		 * @return array
		 */
		public function search( $statut, $mesCodesInsee, $filtre_zone_geo, $search, $lockedDossiers ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			$sqDerniereRgadr01 = $Dossier->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' );
			$sqDerniereRgadr02 = str_replace( '01', '02', $sqDerniereRgadr01 );

			$sqDerniereOrientstruct = $Dossier->Foyer->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere( 'Personne.id' );
			$sqZonesgeographiquesStructuresreferentes = $Dossier->Foyer->Personne->Orientstruct->Structurereferente->StructurereferenteZonegeographique->sq(
				array(
					'alias' => 'structuresreferentes_zonesgeographiques',
					'fields' => array( 'zonesgeographiques.codeinsee' ),
					'joins' => array(
						array_words_replace(
							$Dossier->Foyer->Personne->Orientstruct->Structurereferente->StructurereferenteZonegeographique->join( 'Zonegeographique', array( 'type' => 'INNER' ) ),
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

			// Un dossier possède un seul detail du droit RSA mais ce dernier possède plusieurs details de calcul
			// donc on limite au dernier detail de calcul du droit rsa
			$sqDernierDetailcalculdroitrsa = $Dossier->Foyer->Dossier->Detaildroitrsa->Detailcalculdroitrsa->sqDernier( 'Detaildroitrsa.id' );

			//Dernier CER en cours pour un allocataire
			$sqDernierContratinsertion = $Dossier->Foyer->Personne->sqLatest( 'Contratinsertion', 'dd_ci' );

			$conditions = array(
				'Prestation.natprest' => 'RSA',
				'Prestation.rolepers' => array( 'DEM', 'CJT' ),
				'Adressefoyer.rgadr' => '01',
				"Adressefoyer.id IN ( {$sqDerniereRgadr01} )",
				"VxAdressefoyer.id IN ( {$sqDerniereRgadr02} )",
				'Orientstruct.statut_orient' => 'Orienté',
				"Orientstruct.id IN ( {$sqDerniereOrientstruct} )",
				"Detailcalculdroitrsa.id IN ( {$sqDernierDetailcalculdroitrsa} )",
				$sqDernierContratinsertion
			);

			if( $statut == 'atransferer' ) {
				$conditions = array_merge(
					$conditions,
					array(
						'Adresse.numcom LIKE' => Configure::read( 'Cg.departement' ).'%',
						'Adresse.numcom <> VxAdresse.numcom',
						"Adressefoyer.dtemm > Orientstruct.date_valid",
						'Structurereferente.filtre_zone_geo' => true,
						"Adresse.numcom NOT IN ( {$sqZonesgeographiquesStructuresreferentes} )",
					)
				);
			}
			else {
				$conditions = array_merge(
					$conditions,
					array(
//						"Adressefoyer.dtemm <= Orientstruct.date_valid",
						'Orientstruct.origine' => 'demenagement'
					)
				);
			}

			$conditions = $this->conditionsAdresse( $conditions, $search, $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsPersonneFoyerDossier( $conditions, $search );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $search );

			if( isset( $search['Orientstruct']['typeorient_id'] ) && trim( $search['Orientstruct']['typeorient_id'] ) != '' ) {
				$conditions['Orientstruct.typeorient_id'] = $search['Orientstruct']['typeorient_id'];
			}

			/// Dossiers lockés
			if( !empty( $lockedDossiers ) ) {
				if( is_array( $lockedDossiers ) ) {
					$lockedDossiers = implode( ', ', $lockedDossiers );
				}
				$conditions[] = "NOT {$lockedDossiers}";
			}

            // Conditions sur les dates de validation de l'orientation
            $conditions = $this->conditionsDates( $conditions, $search, 'Orientstruct.date_valid' );
            // Conditions sur les dates de transfert du dossier
            $conditions = $this->conditionsDates( $conditions, $search, 'Transfertpdv93.created' );


			$querydata = array(
				'fields' => array_merge(
					$Dossier->fields(),
					$Dossier->Detaildroitrsa->fields(),
					$Dossier->Detaildroitrsa->Detailcalculdroitrsa->fields(),
					$Dossier->Foyer->Adressefoyer->fields(),
					array_words_replace( $Dossier->Foyer->Adressefoyer->fields(), array( 'Adressefoyer' => 'VxAdressefoyer' ) ),
					$Dossier->Foyer->Personne->fields(),
					$Dossier->Foyer->Adressefoyer->Adresse->fields(),
					array_words_replace( $Dossier->Foyer->Adressefoyer->Adresse->fields(), array( 'Adresse' => 'VxAdresse' ) ),
					$Dossier->Foyer->Personne->Calculdroitrsa->fields(),
					$Dossier->Foyer->Personne->Orientstruct->fields(),
					$Dossier->Foyer->Personne->Contratinsertion->Cer93->fields(),
					$Dossier->Foyer->Personne->Prestation->fields(),
					$Dossier->Foyer->Personne->Orientstruct->Structurereferente->fields(),
					$Dossier->Foyer->Personne->Orientstruct->Typeorient->fields()
				),
				'joins' => array(
					$Dossier->join( 'Detaildroitrsa', array( 'type' => 'INNER' ) ),
					$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
					$Dossier->Detaildroitrsa->join( 'Detailcalculdroitrsa', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'INNER' ) ),
					array_words_replace( $Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'INNER' ) ), array( 'Adressefoyer' => 'VxAdressefoyer' ) ),
					$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) ),
					array_words_replace( $Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) ), array( 'Adressefoyer' => 'VxAdressefoyer', 'Adresse' => 'VxAdresse' ) ),
					$Dossier->Foyer->Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Personne->join( 'Contratinsertion', array( 'type' => 'LEFT OUTER' ) ),
					$Dossier->Foyer->Personne->Contratinsertion->join( 'Cer93', array( 'type' => 'LEFT OUTER' ) ),
					$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'INNER' ) ),
				),
				'conditions' => $conditions,
				'contain' => false,
				'order' => array( 'Adressefoyer.dtemm DESC', 'Dossier.id ASC', 'Personne.nom ASC', 'Personne.prenom ASC' ),
				'limit' => 10
			);

			if( $statut != 'atransferer' ) {
				$Transfertpdv93 = ClassRegistry::init( 'Transfertpdv93' );

				$querydata['fields'] = array_merge( $querydata['fields'], $Transfertpdv93->fields() );
				$querydata['fields'] = array_merge( $querydata['fields'], $Transfertpdv93->VxOrientstruct->fields() );
				$querydata['fields'] = array_merge( $querydata['fields'], array_words_replace( $Transfertpdv93->VxOrientstruct->Structurereferente->fields(), array( 'Structurereferente' => 'VxStructurereferente' ) ) );

				$querydata['joins'][] = array_words_replace( $Transfertpdv93->NvOrientstruct->join( 'NvTransfertpdv93', array( 'type' => 'INNER' ) ), array( 'NvOrientstruct' => 'Orientstruct', 'NvTransfertpdv93' => 'Transfertpdv93' ) );
				$querydata['joins'][] = array_words_replace( $Transfertpdv93->join( 'VxOrientstruct', array( 'type' => 'INNER' ) ), array( 'VxTransfertpdv93' => 'Transfertpdv93' ) );
				$querydata['joins'][] = array_words_replace( $Transfertpdv93->VxOrientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ), array( 'Structurereferente' => 'VxStructurereferente' ) );
			}

			// FIXME: et qui n'ont pas encore été transférés
			// FIXME: ici, on a ceux qui sortent du département

			$querydata = $Dossier->Foyer->Personne->PersonneReferent->completeQdReferentParcours( $querydata, $search );

			return $querydata;
		}

		/**
		 * Liste des structures référentes groupées par type d'orientation.
		 *
		 * TODO, à vérifier:
		 *	- ajouter un shell qui clôture XXXXX en cas de déménagement hors département
		 *  - ajouter un filtre que dans le dépt/que hors dépt
		 *
		 * @return array
		 * @deprecated since 3.0.00
		 */
		public function structuresParZonesGeographiques() {
			return $this->WebrsaCohorteTransfertpdv93Atransferer->structuresParZonesGeographiques();
		}

		/**
		 * Liste des structures référentes groupées par code INSEE.
		 *
		 * @return array
		 * @deprecated since 3.0.00
		 */
		public function structuresParZonesGeographiquesPourTransfertPdv() {
			return $this->WebrsaCohorteTransfertpdv93Atransferer->structuresParZonesGeographiquesPourTransfertPdv();
		}

		/**
		 * TODO: mettre En attente par défaut
		 *
		 * @param array $results
		 * @param array $structuresParZonesGeographiques
		 * @return array
		 */
		public function prepareFormDataIndex( $results, $structuresParZonesGeographiques ) {
			$formData = array( 'Transfertpdv93' => array() );

			if( !empty( $results ) ) {
				foreach( $results as $index => $result ) {
					$formData['Transfertpdv93'][$index] = array();
					$formData['Transfertpdv93'][$index]['dossier_id'] = $result['Dossier']['id'];
					$formData['Transfertpdv93'][$index]['vx_adressefoyer_id'] = $result['VxAdressefoyer']['id'];
					$formData['Transfertpdv93'][$index]['nv_adressefoyer_id'] = $result['Adressefoyer']['id'];
					$formData['Transfertpdv93'][$index]['vx_orientstruct_id'] = $result['Orientstruct']['id'];
					$formData['Transfertpdv93'][$index]['personne_id'] = $result['Orientstruct']['personne_id'];
					$formData['Transfertpdv93'][$index]['typeorient_id'] = $result['Orientstruct']['typeorient_id'];
					$formData['Transfertpdv93'][$index]['action'] = '0';

					$structurereferente_dst_id = null;

					if( isset( $structuresParZonesGeographiques[$result['Adresse']['numcom']] ) ) {
						if( isset( $structuresParZonesGeographiques[$result['Adresse']['numcom']][$result['Orientstruct']['typeorient_id']] ) ) {
							$selectables = array();
							$structures = $structuresParZonesGeographiques[$result['Adresse']['numcom']][$result['Orientstruct']['typeorient_id']];

							if( !empty( $structures ) ) {
								foreach( array_keys( $structures ) as $key ) {
									if( preg_match( "/^{$result['Adresse']['numcom']}_/", $key ) ) {
										$selectables[] = $key;
									}
								}
							}

							if( count( $selectables ) == 1 ) {
								$structurereferente_dst_id = $selectables[0];
							}
						}
					}

					$formData['Transfertpdv93'][$index]['structurereferente_dst_id'] = $structurereferente_dst_id;
				}
			}

			return $formData;
		}

		// FIXME: vx_orientstruct_id, nv_orientstruct_id
		// TODO:
		// Formattable.suffix -> structurereferente_dst_id
		// Validation structurereferente_dst_id -> NOT NULL
		// FIXME: mettre la date de fin de transfert à jour (ajouter personne_id et nvorientstruct_id dans la table ???)
		public function transfertAllocataire( $data, $user_id ) {
			$success = true;

			$Orientstruct = ClassRegistry::init( 'Orientstruct' );

			// Puisqu'on peut en fait être réorienté du Socioprofessionnel en Emploi, il va falloir aller chercher la bonne valeur de typeorient_id à partir de la structure désignée
			$query = array(
				'fields' => array( 'Structurereferente.typeorient_id' ),
				'conditions' => array(
					'Structurereferente.id' => suffix( $data['Transfertpdv93']['structurereferente_dst_id'] )
				),
				'contain' => false
			);
			$structurereferente = $Orientstruct->Structurereferente->find( 'first', $query );
			$typeorient_id = Hash::get( $structurereferente, 'Structurereferente.typeorient_id' );

			$orientstruct = array(
				'Orientstruct' => array(
					'personne_id' => $data['Transfertpdv93']['personne_id'],
					'typeorient_id' => $typeorient_id,
					'structurereferente_id' => $data['Transfertpdv93']['structurereferente_dst_id'],
					'date_valid' => date( 'Y-m-d' ),
					'statut_orient' => 'Orienté',
					'user_id' => $user_id,
					'origine' => 'demenagement', // FIXME: changer le beforeSave de orientstruct
				)
			);

			// Info: il faut que le transfert soit créé pour que le bon PDF d'orientation soit généré
			$Orientstruct->Behaviors->disable( 'StorablePdf' );
			$Orientstruct->create( $orientstruct );
			$success = $Orientstruct->save( null, array( 'atomic' => false ) ) && $success;
			$Orientstruct->Behaviors->enable( 'StorablePdf' );

			if( !empty( $Orientstruct->validationErrors ) ) {
				debug( $Orientstruct->validationErrors );
			}

			if( $success ) {
				if( !empty( $data['Transfertpdv93']['structurereferente_dst_id'] ) ) {
					$Transfertpdv93 = ClassRegistry::init( 'Transfertpdv93' );

					$data['Transfertpdv93']['user_id'] = $orientstruct['Orientstruct']['user_id'];
					$data['Transfertpdv93']['vx_orientstruct_id'] = $data['Transfertpdv93']['vx_orientstruct_id'];
					$data['Transfertpdv93']['nv_orientstruct_id'] = $Orientstruct->id;

					$Transfertpdv93->create( $data );
					$success = $Transfertpdv93->save( null, array( 'atomic' => false ) ) && $success;
					if( !empty( $Transfertpdv93->validationErrors ) ) {
						debug( $Transfertpdv93->validationErrors );
					}
				}

				// Maintenant que les données du transfert ont été enregistrées, on peut générer le bon PDF d'orientation
				if( $success ) {
					$success = $Orientstruct->generatePdf( $Orientstruct->id ) && $success;
				}

				// Si on change de PDV, et que l'allocataire possède un D1 sans D2 dans l'ancien PDV, on enregistre automatiquement un D2
				if( $success && $data['Transfertpdv93']['vx_orientstruct_id'] !== $data['Transfertpdv93']['nv_orientstruct_id'] ) {
					$questionnaired1pdv93_id = $Orientstruct->Personne->Questionnaired2pdv93->questionnairesd1pdv93Id( $data['Transfertpdv93']['personne_id'] );
					if( !empty( $questionnaired1pdv93_id ) ) {
						$success = $Orientstruct->Personne->Questionnaired2pdv93->saveAuto(
							$data['Transfertpdv93']['personne_id'],
							'changement_situation',
							'modif_commune'
						) && $success;
					}
				}

				// On clôture le référent actuel à la date
				$count = $Orientstruct->Personne->PersonneReferent->find(
					'count',
					array(
						'conditions' => array(
							'PersonneReferent.personne_id' => $data['Transfertpdv93']['personne_id'],
							'PersonneReferent.dfdesignation IS NULL'
						)
					)
				);

				$datedfdesignation = ( is_array( date( 'Y-m-d' ) ) ? date_cakephp_to_sql( date( 'Y-m-d' ) ) : date( 'Y-m-d' ) );

				if( $count > 0 ) {
					$success = $Orientstruct->Personne->PersonneReferent->updateAllUnBound(
						array( 'PersonneReferent.dfdesignation' => '\''.$datedfdesignation.'\'' ),
						array(
							'"PersonneReferent"."personne_id"' => $data['Transfertpdv93']['personne_id'],
							'PersonneReferent.dfdesignation IS NULL'
						)
					) && $success;
				}
			}

			return $success;
		}

		/**
		 *
		 * @param array $data
		 * @param integer $user_id
		 * @return boolean
		 */
		public function saveCohorte( $data, $user_id ) {
			$success = true;

			if( !empty( $data ) ) {
				foreach( $data as $line ) {
					$success = $this->transfertAllocataire( $line, $user_id ) && $success;
				}
			}

			return $success;
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
			if( Configure::read( 'Cg.departement' ) == 93 ) {
				$success = ( $this->structuresParZonesGeographiques() !== false ) && $success;
				$success = ( $this->structuresParZonesGeographiquesPourTransfertPdv() !== false ) && $success;
			}

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