<?php
	/**
	 * Code source de la classe Gestionanomaliebdd.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Gestionanomaliebdd ...
	 *
	 * @package app.Model
	 */
	class Gestionanomaliebdd extends AppModel
	{
		/**
		*
		*/
		public $useTable = false;

		/**
		*
		*/
		public $actsAs = array( 'Conditionnable' );

		/**
		* FIXME
		*/
		public function qdPersonnesEnDoublons( $methode, $sansprestation = null, $foyerId = 'personnes.foyer_id', $similarityThreshold = 0.3 ) {
			$conditionsCmpAllocataire = array();

			// 2°) Comparaison moins stricte -> 4852@cg66_20111110_v21
			// Ex.@cg66_20111110_v21: /personnes/index/35633
			if( !empty( $methode ) && $methode == 'normale' ) {
				$conditionsCmpAllocataire = array(
					'OR' => array(
						array(
							'nir_correct13(p1.nir)',
							'nir_correct13(p2.nir)',
							'SUBSTRING( TRIM( BOTH \' \' FROM p1.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM p2.nir ) FROM 1 FOR 13 )',
							'p1.dtnai = p2.dtnai'
						),
// 						array(
// 							'TRIM( BOTH \' \' FROM p1.nom ) = TRIM( BOTH \' \' FROM p2.nom )',
// 							'TRIM( BOTH \' \' FROM p1.prenom ) = TRIM( BOTH \' \' FROM p2.prenom )',
// 							'p1.dtnai = p2.dtnai'
// 						)
						array(
							'UPPER(p1.nom) = UPPER(p2.nom)',
							'UPPER(p1.prenom) = UPPER(p2.prenom)',
							'p1.dtnai = p2.dtnai'
						)
					)
				);
			}

			// 3°) Voir https://www.postgresql.org/docs/11/pgtrgm.html
			// Ex.@cg66_20111110_v21: /personnes/index/35665 (4) -> 5098@cg66_20111110_v21
			// Ex.@cg66_20111110_v21: /personnes/index/31773 (3) -> 5206@cg66_20111110_v21
			if( !empty( $methode ) && $methode == 'approchante' ) {
				$conditionsCmpAllocataire = array(
					'OR' => array(
						array(
							'nir_correct13(p1.nir)',
							'nir_correct13(p2.nir)',
							'SUBSTRING( TRIM( BOTH \' \' FROM p1.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM p2.nir ) FROM 1 FOR 13 )',
						),
						array(
							'similarity(p1.nom, p2.nom) > ' . $similarityThreshold,
							'OR' => array(
								'similarity(p1.prenom, p2.prenom) > ' . $similarityThreshold,
								"p1.prenom ILIKE p2.prenom || ' ' || p2.prenom2 || '%'",
								"p2.prenom ILIKE p1.prenom || ' ' || p1.prenom2 || '%'"
							),
						)
					),
					'p1.dtnai = p2.dtnai'
				);
			}

			if( empty( $conditionsCmpAllocataire ) ) {
				trigger_error( 'Invalid parameter "'.$methode.'" for '.$this->name.'::qdPersonnesEnDoublons()', E_USER_WARNING );
				return array();
			}

			if( !is_null( $sansprestation ) ) {
				if( $sansprestation ) {
					$conditionsCmpAllocataire[] = 'r2.rolepers IS NULL';
				}
				else {
					$conditionsCmpAllocataire[] = 'r2.rolepers IS NOT NULL';
				}
			}

			return array(
				'alias' => 'p1',
				'fields' => array( 'DISTINCT(p1.id)' ),
				'joins' => array(
					array(
						'table'      => 'personnes',
						'alias'      => 'p2',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'p1.id <> p2.id',
							'p1.foyer_id = p2.foyer_id'
						)
					),
					array(
						'table'      => 'prestations',
						'alias'      => 'r1',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'r1.personne_id = p1.id',
							'r1.natprest = \'RSA\''
						)
					),
					array(
						'table'      => 'prestations',
						'alias'      => 'r2',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'r2.personne_id = p2.id',
							'r2.natprest = \'RSA\''
						)
					),
				),
				'conditions' => array(
					"p1.foyer_id = {$foyerId}",
					$conditionsCmpAllocataire
				),
				'contain' => false,
			);
		}

		/**
		* FIXME
		*/
		public function qdPersonnesEnDoublonsBak( $methode, $prestationObligatoire = true, $foyerId = 'personnes.foyer_id', $similarityThreshold = 0.3 ) {
			$conditionsCmpAllocataire = array();

			// TODO: méthode pour comparer les nirs, méthode pour comparer les noms/prénoms
			// 1°) Comparaison stricte -> 4825@cg66_20111110_v21
			if( !!empty( $methode ) || !in_array( $methode, array( 'normale', 'approchante' ) ) ) {
				$conditionsCmpAllocataire = array(
					'OR' => array(
						array(
							'nir_correct(p1.nir)',
							'nir_correct(p2.nir)',
							'p1.nir = p2.nir',
							'p1.dtnai = p2.dtnai'
						),
						array(
							'UPPER(p1.nom) = UPPER(p2.nom)',
							'UPPER(p1.prenom) = UPPER(p2.prenom)',
							'p1.dtnai = p2.dtnai'
						)
					)
				);
			}

			/*
			trim nom/prénom (sans/avec), méthode normale -> paramètre nir et paramètre nom/prénom:
				- 58 -> 235 / 235
				- 66 -> 4852 / 4857
				- 93 -> 5070 / 5070
			*/

			// 2°) Comparaison moins stricte -> 4852@cg66_20111110_v21
			// Ex.@cg66_20111110_v21: /personnes/index/35633
			if( !empty( $methode ) && $methode == 'normale' ) {
				$conditionsCmpAllocataire = array(
					'OR' => array(
						array(
							'nir_correct13(p1.nir)',
							'nir_correct13(p2.nir)',
							'SUBSTRING( TRIM( BOTH \' \' FROM p1.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM p2.nir ) FROM 1 FOR 13 )',
							'p1.dtnai = p2.dtnai'
						),
// 						array(
// 							'TRIM( BOTH \' \' FROM p1.nom ) = TRIM( BOTH \' \' FROM p2.nom )',
// 							'TRIM( BOTH \' \' FROM p1.prenom ) = TRIM( BOTH \' \' FROM p2.prenom )',
// 							'p1.dtnai = p2.dtnai'
// 						)
						array(
							'UPPER(p1.nom) = UPPER(p2.nom)',
							'UPPER(p1.prenom) = UPPER(p2.prenom)',
							'p1.dtnai = p2.dtnai'
						)
					)
				);
			}

			// 3°) Voir https://www.postgresql.org/docs/11/pgtrgm.html
			// Ex.@cg66_20111110_v21: /personnes/index/35665 (4) -> 5098@cg66_20111110_v21
			// Ex.@cg66_20111110_v21: /personnes/index/31773 (3) -> 5206@cg66_20111110_v21
			if( !empty( $methode ) && $methode == 'approchante' ) {
				$conditionsCmpAllocataire = array(
					'OR' => array(
						array(
							'nir_correct13(p1.nir)',
							'nir_correct13(p2.nir)',
							'SUBSTRING( TRIM( BOTH \' \' FROM p1.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM p2.nir ) FROM 1 FOR 13 )',
							'p1.dtnai = p2.dtnai'
						),
						array(
							'similarity(p1.nom, p2.nom) >=' => $similarityThreshold,
							'similarity(p1.prenom, p2.prenom ) >=' => $similarityThreshold,
							'p1.dtnai = p2.dtnai'
						)
					)
				);
			}

			if( empty( $conditionsCmpAllocataire ) ) {
				trigger_error( 'Invalid parameter "'.$methode.'" for '.$this->name.'::qdPersonnesEnDoublonsBak()', E_USER_WARNING );
				return array();
			}

			return array(
				'alias' => 'p1',
				'fields' => array( 'DISTINCT(p1.id)' ),
				'joins' => array(
					array(
						'table'      => 'personnes',
						'alias'      => 'p2',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'p1.id <> p2.id',
							'p1.foyer_id = p2.foyer_id'
						)
					),
					array(
						'table'      => 'prestations',
						'alias'      => 'r1',
						'type'       => ( $prestationObligatoire ? 'INNER' : 'LEFT OUTER' ),
						'foreignKey' => false,
						'conditions' => array(
							'r1.personne_id = p1.id',
							'r1.natprest = \'RSA\''
						)
					),
					array(
						'table'      => 'prestations',
						'alias'      => 'r2',
						'type'       => ( $prestationObligatoire ? 'INNER' : 'LEFT OUTER' ),
						'foreignKey' => false,
						'conditions' => array(
							'r2.personne_id = p2.id',
							'r2.natprest = \'RSA\''
						)
					),
				),
				'conditions' => array(
					"p1.foyer_id = {$foyerId}",
					$conditionsCmpAllocataire
				),
				'contain' => false,
			);
		}

		/**
		* FIXME
		*/
		public function sqPersonnesEnDoublonsBak( $methode, $prestationObligatoire = true, $foyerId = 'personnes.foyer_id', $differenceThreshold = 4 ) {
			$qdFoyersPersonnesEnDoublons = $this->qdPersonnesEnDoublonsBak( $methode, $prestationObligatoire, $foyerId, $differenceThreshold );
			$Personne = ClassRegistry::init( 'Personne' );
			return $Personne->sq(
				array(
					'alias' => 'personnes',
					'fields' => array( 'personnes.foyer_id' ),
					'conditions' => array(
						'personnes.foyer_id = Foyer.id',
						'personnes.id IN ( '
							.$Personne->sq( $qdFoyersPersonnesEnDoublons )
						.' )'
					)
				)
			);
		}

		/**
		* Retourne des champs virtuels concernant le foyer (le dossier):
		* 	- Foyer.enerreur
		* 	- Foyer.sansprestation
		*	- Foyer.doublonspersonnes
		*/
		public function vfsInformationsFoyer( &$Foyer, $sqPersonnesEnDoublons ) {
			if( empty( $sqLockedDossiers ) ) {
				$sqLockedDossiers = 0; // INFO: 0 car lorsque c'est à NULL, ça ne réagit pas comme prévu
			}

			return array(
				$Foyer->sqVirtualField( 'enerreur', false ).' AS "Foyer__enerreur"',
				$Foyer->sqVirtualField( 'sansprestation', false ).' AS "Foyer__sansprestation"',
				"( CASE WHEN \"Foyer\".\"id\" IN ( {$sqPersonnesEnDoublons} ) THEN 'Personnes en doublon détectées' ELSE NULL END ) AS \"Foyer__doublonspersonnes\"",
			);
		}

		/**
		* TODO: vérifier que les filtres suivants n'ont pas cassé (à cause de la modification de conditionsPersonneFoyerDossier)
		* 	- app/models/defautinsertionep66.php
		* 	- app/models/dossier.php
		* 	- app/models/dsp.php
		*/
		public function search( $mesCodesInsee, $filtre_zone_geo, $params, $sqLockedDossiers ) {
			$options = array();
			$conditions = array();
			$conditionsAllocataire = array();

			$Dossier = ClassRegistry::init( 'Dossier' );
			$Option = ClassRegistry::init( 'Option' );

			/// 0°) Nettoyage, lecture des paramètres
			// 0.1°) Nettoyage des filtres
			unset( $params['active'] );
			// 0.2°) Dossiers ayant n'importe quelle erreur
			$touteerreur = Set::classicExtract( $params, 'Gestionanomaliebdd.touteerreur' );
			// 0.3°) Dossiers en erreur concernant le nombre de demandeurs ou de conjoints
			$enerreur = Set::classicExtract( $params, 'Gestionanomaliebdd.enerreur' );
			$enerreur = ( $enerreur === '' ? null : $enerreur );
			unset( $params['Gestionanomaliebdd']['enerreur'] );
			// 0.4°) Dossiers possédant des personnes sans prestation
			$sansprestation = Set::classicExtract( $params, 'Gestionanomaliebdd.sansprestation' );
			$sansprestation = ( $sansprestation === '' ? null : $sansprestation );
			unset( $params['Gestionanomaliebdd']['sansprestation'] );
			// 0.5°) ) Dossiers possédant des personnes en doublons
			$doublons = Set::classicExtract( $params, 'Gestionanomaliebdd.doublons' );
			$doublons = ( $doublons === '' ? null : $doublons );
			unset( $params['Gestionanomaliebdd']['doublons'] );
			// 0.6°) Méthode de comparaison des personnes en doublons
			$methode = Set::classicExtract( $params, 'Gestionanomaliebdd.methode' );
			unset( $params['Gestionanomaliebdd']['methode'] );
			// 0.7°) Préparation du champ permettant de comptabiliser les personnes en doublons au sein du foyer
			$qdPersonnesEnDoublons = $this->qdPersonnesEnDoublons(
				$methode,
				$sansprestation,
				'Foyer.id'
			);
			$qdPersonnesEnDoublons['fields'] = array( 'p1.foyer_id' );
			$sqPersonnesEnDoublons = $Dossier->Foyer->Personne->sq( $qdPersonnesEnDoublons );

			/// 1°) Conditions de base
			// 1.1°) Dossier non lockés par un autre utilisateur
// 			$conditions[] = "Dossier.id NOT IN ( {$sqLockedDossiers} )";
			// 1.2°) Possédant une adresse de rang '01'
			$conditions[] = 'Adressefoyer.id IN ('
				.$Dossier->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' )
			.')';

			/// 2°) Conditions venant des filtres de détection de personnes en anomalie au sein d'un dossier
			// 2.1°) Dossiers en erreur concernant le nombre de demandeurs ou de conjoints
			if( !is_null( $enerreur ) || $touteerreur ) {
				$sqDossiersEnerreur = $Dossier->Foyer->sqVirtualField( 'enerreur', false );
				$sqDossiersEnerreur = "{$sqDossiersEnerreur} ".( ( $enerreur || $touteerreur ) ? 'IS NOT NULL' : 'IS NULL' );
			}
			// 2.2°) Dossiers possédant des personnes n'ayant pas de prestation
			if( !is_null( $sansprestation ) || $touteerreur ) {
				$sqDossiersPersonnesSansPrestation = $Dossier->Foyer->sqVirtualField( 'sansprestation', false );
				$sqDossiersPersonnesSansPrestation = "{$sqDossiersPersonnesSansPrestation} ".( ( $sansprestation || $touteerreur ) ? 'IS NOT NULL' : 'IS NULL' );
			}
			// 2.3°) Dossiers possédant des personnes en doublon
			if( !is_null( $doublons ) || $touteerreur ) {
				$sqFoyersPersonnesEnDoublons = $this->sqPersonnesEnDoublonsBak( $methode, false );
				$sqFoyersPersonnesEnDoublons = "Foyer.id ".( ( $doublons || $touteerreur ) ? 'IN' : 'NOT IN' )." ( {$sqFoyersPersonnesEnDoublons} )";
			}

			if( $touteerreur ) {
				$conditions[]['OR'] = array(
					$sqDossiersEnerreur,
					$sqDossiersPersonnesSansPrestation,
					$sqFoyersPersonnesEnDoublons,
				);
			}
			else {
				if( isset( $sqDossiersEnerreur ) ) {
					$conditions[] = $sqDossiersEnerreur;
				}
				if( isset( $sqDossiersPersonnesSansPrestation ) ) {
					$conditions[] = $sqDossiersPersonnesSansPrestation;
				}
				if( isset( $sqFoyersPersonnesEnDoublons ) ) {
					$conditions[] = $sqFoyersPersonnesEnDoublons;
				}
			}

			/// 3°) Autres filtres
			// 3.1°) Conditions sur l'adresse, le dossier, le foyer, la situation du dossier
			$conditions = $this->conditionsAdresse( $conditions, $params, $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsDossier( $conditions, $params );
			$conditions = $this->conditionsFoyer( $conditions, $params );
			$conditions = $this->conditionsSituationdossierrsa( $conditions, $params );

			// 3.2°) Conditions sur une personne appartenant au foyer
			$conditionsPersonne = array();
			$conditionsPersonne = $this->conditionsPersonne( $conditionsPersonne, $params );
			if( !empty( $conditionsPersonne ) ) {
				$conditionsPersonne = array_words_replace( $conditionsPersonne, array( 'Personne' => 'personnes' ) );
				$sqFiltrePersonnes = $Dossier->Foyer->Personne->sq(
					array(
						'alias' => 'personnes',
						'fields' => array( 'personnes.foyer_id' ),
						'conditions' => $conditionsPersonne,
						'contain' => false
					)
				);
				$conditions[] = "Foyer.id IN ( {$sqFiltrePersonnes} )";
			}

			// 5351@cg66_20111110_v21 -> TODO
			// Ex.@cg66_20111110_v21 -> /personnes/index/19
			$querydata = array(
				'fields' => array_merge(
					$Dossier->fields(),
					$Dossier->Foyer->fields(),
					$Dossier->Situationdossierrsa->fields(),
					$this->vfsInformationsFoyer( $Dossier->Foyer, $sqPersonnesEnDoublons ),
					array(
						'Adresse.nomcom',
						'Adresse.numcom',
					),
					(array)$sqLockedDossiers
				),
				'conditions' => array(),
				'joins' => array(
					$Dossier->join( 'Foyer' ),
					$Dossier->join( 'Situationdossierrsa' ),
					$Dossier->Foyer->join( 'Adressefoyer' ),
					$Dossier->Foyer->Adressefoyer->join( 'Adresse' ),
				),
				'contain' => false,
				'limit' => isset ($params['Search']['limit']) ? $params['Search']['limit'] : Configure::read('ResultatsParPage.nombre_par_defaut')
			);

			$querydata['conditions'] = Set::merge( $querydata['conditions'], $conditions );
			return $querydata;
		}
	}
?>