<?php
	/**
	 * Fichier source de la classe ConditionnableBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 */
	App::uses( 'ModelBehavior', 'Model' );
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * Ce behavior permet de transformer les critères d'un filtre de recherche en conditions pour les queryData
	 * d'une requête CakePHP pour le projet WebRSA
	 *
	 * @package app.Model.Behavior
	 */
	class ConditionnableBehavior extends ModelBehavior
	{
		/**
		 * Filtre par Adresse pour les moteurs de recherche.
		 *
		 * @param Model $model
		 * @param array $conditions
		 * @param array $search
		 * @param boolean $filtre_zone_geo
		 * @param array $mesCodesInsee
		 * @return array
		 */
		public function conditionsAdresse( Model $model, $conditions, $search, $filtre_zone_geo = false, $mesCodesInsee = array(), $filtre_site_cov_zone_geo = true ) {
			$CantonModel = ClassRegistry::init( 'Canton' );

			/// Critères sur l'adresse - nom de commune
			foreach( array( 'nomcom', 'nomvoie' ) as $filtre ) {
				if( isset( $search['Adresse'][$filtre] ) && !empty( $search['Adresse'][$filtre] ) ) {
					$conditions[] = "Adresse.$filtre ILIKE '".$model->wildcard( Sanitize::clean( $search['Adresse'][$filtre], array( 'encode' => false ) ) )."'";
				}
			}

			// Critères sur l'adresse - code insee
			$numscoms = Hash::get( $search, 'Adresse.numcom' );
			if( !empty( $numscoms ) ) {
				$numscoms = (array)$numscoms;
				$or = array();
				foreach( $numscoms as $key => $numcom ) {
					$numcom = Sanitize::clean( trim( $numcom ), array( 'encode' => false ) );
					if( strlen( $numcom ) == 5 ) {
						$or[] = "Adresse.numcom = '{$numcom}'";
					}
					else {
						$or[] = "Adresse.numcom ILIKE '%{$numcom}%'";
					}
				}

				if( count( $or ) === 1 ) {
					$conditions[] = $or[0];
				}
				else {
					$conditions[] = array( 'OR' => $or );
				}
			}

			// Critères sur l'adresse - canton
			if( Configure::read( 'CG.cantons' ) ) {
				if( isset( $search['Canton']['canton'] ) && !empty( $search['Canton']['canton'] ) ) {
					$conditions[] = $CantonModel->queryConditions( $search['Canton']['canton'] );
				}
			}

			// Critère pour le CG 58
			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$Sitecov58 = ClassRegistry::init( 'Sitecov58' );

				// Critère sur le site COV
				$sitecov58_id = Hash::get( $search, 'Sitecov58.id' );

				if( !empty( $sitecov58_id ) ) {
					if ($filtre_site_cov_zone_geo) {
						$conditions[] = $Sitecov58->queryConditionsByZonesgeographiques ( $sitecov58_id );
					}
					else {
						$conditions[] = $Sitecov58->queryConditions ( $sitecov58_id );
					}
				}

				// Critère sur le Site d'Action Médico-Sociale
				$sitecov58_id = Hash::get( $search, 'CantonSitecov58.id' );

				if( !empty( $sitecov58_id ) ) {
					$conditions[] = $Sitecov58->queryConditions ( $sitecov58_id );
				}
			}

			/// Filtre zone géographique de l'utilisateur
			if( $filtre_zone_geo ) {
				// Si on utilise la table des cantons plutôt que la table zonesgeographiques
				if( Configure::read( 'CG.cantons' ) ) {
					$conditions[] = $CantonModel->queryConditionsByZonesgeographiques( array_keys( $mesCodesInsee ) );
				}
				else {
					$mesCodesInsee = ( !empty( $mesCodesInsee ) ? $mesCodesInsee : array( null ) );
					$conditions[] = '( Adresse.numcom IN ( \''.implode( '\', \'', $mesCodesInsee ).'\' ) )';
				}
			}

			// Filtre sur les adresses sans zone géographique associée
			// FIXME: sauf pour le 66!
			$sans_zonegeographique = Hash::get( $search, 'Adresse.sans_zonegeographique' );
			if( $sans_zonegeographique ) {
				$Zonegeographique = ClassRegistry::init( 'Zonegeographique' );
				$query = array(
					'fields' => array( 'Zonegeographique.codeinsee' ),
					'contain' => false
				);
				$sql = $Zonegeographique->sq( $query );
				$sqls = array_words_replace( array( $sql ), array( 'Zonegeographique' => 'zonesgeographiques' ) );
				$conditions[] = "Adresse.numcom NOT IN ( {$sqls[0]} )";
			}

			return $conditions;
		}

		/**
		 * Filtres sur le Dossier: numdemrsa, matricule, dtdemrsa, fonorg
		 *
		 * @param Model $model
		 * @param array $conditions
		 * @param array $search
		 * @return array
		 */
		public function conditionsDossier( Model $model, $conditions, $search ) {
			foreach( array( 'numdemrsa', 'matricule' ) as $critereDossier ) {
				if( isset( $search['Dossier'][$critereDossier] ) && !empty( $search['Dossier'][$critereDossier] ) ) {
					$conditions[] = 'Dossier.'.$critereDossier.' ILIKE \''.$model->wildcard( Configure::read ('search.conditions.numdemrsa_matricule.before')."{$search['Dossier'][$critereDossier]}".Configure::read ('search.conditions.numdemrsa_matricule.after') ).'\'';
				}
			}

			foreach( array( 'dtdemrsa' ) as $critereDossier ) {
				if( isset( $search['Dossier'][$critereDossier] )  ) {
					if( is_array( $search['Dossier'][$critereDossier] ) && !empty( $search['Dossier'][$critereDossier]['day'] ) && !empty( $search['Dossier'][$critereDossier]['month'] ) && !empty( $search['Dossier'][$critereDossier]['year'] ) ) {
						$conditions["Dossier.{$critereDossier}"] = "{$search['Dossier'][$critereDossier]['year']}-{$search['Dossier'][$critereDossier]['month']}-{$search['Dossier'][$critereDossier]['day']}";
					}
					else if( ( is_int( $search['Dossier'][$critereDossier] ) || is_bool( $search['Dossier'][$critereDossier] ) || ( $search['Dossier'][$critereDossier] == '1' ) ) && isset( $search['Dossier']['dtdemrsa_from'] ) && isset( $search['Dossier']['dtdemrsa_to'] ) ) {
						$search['Dossier']['dtdemrsa_from'] = $search['Dossier']['dtdemrsa_from']['year'].'-'.$search['Dossier']['dtdemrsa_from']['month'].'-'.$search['Dossier']['dtdemrsa_from']['day'];
						$search['Dossier']['dtdemrsa_to'] = $search['Dossier']['dtdemrsa_to']['year'].'-'.$search['Dossier']['dtdemrsa_to']['month'].'-'.$search['Dossier']['dtdemrsa_to']['day'];

						$conditions[] = 'Dossier.dtdemrsa BETWEEN \''.$search['Dossier']['dtdemrsa_from'].'\' AND \''.$search['Dossier']['dtdemrsa_to'].'\'';
					}
				}
			}

			if( isset( $search['Dossier']['fonorg'] ) && !empty( $search['Dossier']['fonorg'] ) ) {
				$conditions[] = array( 'Dossier.fonorg' => $search['Dossier']['fonorg'] );
			}

			$anciennete_dispositif = Hash::get( $search, 'Dossier.anciennete_dispositif' );
			if( !empty( $anciennete_dispositif ) ) {
				list( $min, $max ) = explode( '_', $anciennete_dispositif );
				$conditions[] = 'EXTRACT( YEAR FROM AGE( NOW(), "Dossier"."dtdemrsa" ) ) BETWEEN '.$min.' AND '.$max;
			}

			return $conditions;
		}

		/**
		 * Filtres sur le Serviceinstructeur: id
		 *
		 * @param Model $model
		 * @param array $conditions
		 * @param array $search
		 * @return array
		 */
		public function conditionsServiceinstructeur( Model $model, $conditions, $search ) {
			$serviceinstructeur_id = (string)Hash::get( $search, 'Serviceinstructeur.id' );
			if( $serviceinstructeur_id !== '' ) {
				$Dossier = ClassRegistry::init( 'Dossier' );

				$subQuery = array_words_replace(
					array(
						'alias' => 'Suiviinstruction',
						'fields' => array( 'Suiviinstruction.dossier_id' ),
						'contain' => false,
						'joins' => array(
							$Dossier->Suiviinstruction->join( 'Serviceinstructeur', array( 'type' => 'INNER' ) )
						),
						'conditions' => array(
							'Serviceinstructeur.id' => $serviceinstructeur_id
						)
					),
					array( 'Suiviinstruction' => 'suivisinstruction', 'Serviceinstructeur' => 'servicesinstructeurs' )
				);
				$conditions[] = '"Dossier"."id" IN ( '.$Dossier->Suiviinstruction->sq( $subQuery ).' )';
			}

			$suiviinstruction_typeserins = (string)Hash::get( $search, 'Suiviinstruction.typeserins' );
			if( $suiviinstruction_typeserins !== '' ) {
				/*
				 * Comme la valeur '' ne passe pas lors de la génération de la liste déroulante.
				 * l'option choisie a été de mettre un 0 à la place.
				 *
				 * Pour un dossier "Non renseigné", il n'y a pas de suivi instruction
				 */
				if ($suiviinstruction_typeserins === '0') {
					$Dossier = ClassRegistry::init( 'Dossier' );

					$subQuery = array_words_replace(
						array(
							'alias' => 'Suiviinstruction',
							'fields' => array( 'Suiviinstruction.dossier_id' ),
							'contain' => false,
						),
						array( 'Suiviinstruction' => 'suivisinstruction' )
					);
					$conditions[] = '"Dossier"."id" NOT IN ( '.$Dossier->Suiviinstruction->sq( $subQuery ).' )';
				}
				else {
					$Dossier = ClassRegistry::init( 'Dossier' );

					$subQuery = array_words_replace(
						array(
							'alias' => 'Suiviinstruction',
							'fields' => array( 'Suiviinstruction.dossier_id' ),
							'contain' => false,
							'conditions' => array(
								'Suiviinstruction.typeserins' => $suiviinstruction_typeserins
							)
						),
						array( 'Suiviinstruction' => 'suivisinstruction' )
					);
					$conditions[] = '"Dossier"."id" IN ( '.$Dossier->Suiviinstruction->sq( $subQuery ).' )';
				}
			}

			return $conditions;
		}

		/**
		 * Filtres sur le Foyer: sitfam, ddsitfam
		 *
		 * @param Model $model
		 * @param array $conditions
		 * @param array $search
		 * @return array
		 */
		public function conditionsFoyer( Model $model, $conditions, $search ) {
			foreach( array( 'sitfam' ) as $critere ) {
				if( isset( $search['Foyer'][$critere] ) && !empty( $search['Foyer'][$critere] ) ) {
					$conditions["Foyer.{$critere}"] = $search['Foyer'][$critere];
				}
			}

			foreach( array( 'ddsitfam' ) as $critere ) {
				if( isset( $search['Foyer'][$critere] ) && !empty( $search['Foyer'][$critere]['day'] ) && !empty( $search['Foyer'][$critere]['month'] ) && !empty( $search['Foyer'][$critere]['year'] ) ) {
					$conditions["Foyer.{$critere}"] = "{$search['Foyer'][$critere]['year']}-{$search['Foyer'][$critere]['month']}-{$search['Foyer'][$critere]['day']}";
				}
			}

			return $conditions;
		}

		/**
		 * Filtres sur la Situationdossierrsa: etatdosrsa
		 *
		 * @param Model $model
		 * @param array $conditions
		 * @param array $search
		 * @return array
		 */
		public function conditionsSituationdossierrsa( Model $model, $conditions, $search ) {
			$etatdossier = Set::extract( $search, 'Situationdossierrsa.etatdosrsa' );
			if( ( Hash::get( $search, 'Situationdossierrsa.etatdosrsa_choice' ) !== '0' )
				&& isset( $search['Situationdossierrsa']['etatdosrsa'] )
				&& !empty( $search['Situationdossierrsa']['etatdosrsa'] )
			) {
				$key =  array_search ('NULL',$etatdossier);
				if ($key !== false) {
					unset($etatdossier[$key]);
				}

				$strCondition = '( Situationdossierrsa.etatdosrsa IN ( \''.implode( '\', \'', $etatdossier ).'\' ) )';

				if ($key !== false) {
					$strCondition = '( Situationdossierrsa.etatdosrsa IS NULL OR '.$strCondition.')' ;
				}
				$conditions[] = $strCondition;
			}

			return $conditions;
		}

		/**
		 * Filtres sur le Detaildroitrsa: oridemrsa
		 *
		 * @param Model $model
		 * @param array $conditions
		 * @param array $search
		 * @return array
		 */
		public function conditionsDetaildroitrsa( Model $model, $conditions, $search ) {
			$oridemrsa = Set::extract( $search, 'Detaildroitrsa.oridemrsa' );
			if( ( Hash::get( $search, 'Detaildroitrsa.oridemrsa_choice' ) !== '0' ) && isset( $search['Detaildroitrsa']['oridemrsa'] ) && !empty( $search['Detaildroitrsa']['oridemrsa'] ) ) {
				$conditions[] = '( Detaildroitrsa.oridemrsa IN ( \''.implode( '\', \'', $oridemrsa ).'\' ) )';
			}

			return $conditions;
		}

		/**
		 * Filtres sur la Personne:  nom, prenom, nomnai, nir, dtnai
		 *
		 * @param Model $model
		 * @param array $conditions
		 * @param array $search
		 * @return array
		 */
		public function conditionsPersonne( Model $model, $conditions, $search ) {
			foreach( array( 'nom', 'prenom', 'nomnai', 'nir' ) as $criterePersonne ) {
				if( isset( $search['Personne'][$criterePersonne] ) && !empty( $search['Personne'][$criterePersonne] ) ) {
					$conditions[] = 'UPPER(Personne.'.$criterePersonne.') LIKE \''.$model->wildcard( strtoupper( replace_accents( $search['Personne'][$criterePersonne] ) ) ).'\'';
				}
			}

			foreach( array( 'sexe' ) as $critere ) {
				if( isset( $search['Personne'][$critere] ) && !empty( $search['Personne'][$critere] ) ) {
					$conditions["Personne.{$critere}"] = $search['Personne'][$critere];
				}
			}

			/// Critères sur une personne du foyer - date de naissance
			$conditions = $this->conditionsDate( $model, $conditions, $search, 'Personne.dtnai' );

			// Voir si une sous-requête ne serait pas plus simple
			if( isset( $search['Personne']['trancheage'] ) ) {
				$trancheage = Hash::get( $search, 'Personne.trancheage' );

				if( !empty( $trancheage )  ) {
					list( $ageMin, $ageMax ) = explode( '_', $trancheage );
					$conditions[] = '( EXTRACT ( YEAR FROM AGE( Personne.dtnai ) ) ) BETWEEN '.$ageMin.' AND '.$ageMax;
				}
			}

			if( isset( $search['Personne']['trancheagesup'] ) ) {
				$trancheagesup = Hash::get( $search, 'Personne.trancheagesup' );

				if( !empty( $trancheagesup )  ) {
					$conditions[] = '( EXTRACT ( YEAR FROM AGE( Personne.dtnai ) ) ) >= '.$trancheagesup;
				}
			}

			if( isset( $search['Personne']['trancheageprec'] ) ) {
				$trancheagesup = Hash::get( $search, 'Personne.trancheageprec' );

				if( !empty( $trancheagesup )  ) {
					$conditions[] = '( EXTRACT ( YEAR FROM AGE( Personne.dtnai ) ) ) >= '.$trancheagesup;
				}
			}

			// Filtre par code activité
			$value = Hash::get( $search, 'Activite.act' );
			if( !empty( $value ) ) {
				$conditions['Activite.act'] = $value;
			}

			return $conditions;
		}

		/**
		 * Filtres sur le Detailcalculdroitrsa:  natpf
		 *
		 * FIXME: remplacer , et - par _PLUS_ et _MINUS_
		 *
		 * @param Model $model
		 * @param array $conditions
		 * @param array $search
		 * @return array
		 */
		public function conditionsDetailcalculdroitrsa( Model $model, $conditions, $search ) {
			if( isset( $search['Detailcalculdroitrsa']['natpf'] ) && !empty( $search['Detailcalculdroitrsa']['natpf'] ) ) {
				if( !is_array( $search['Detailcalculdroitrsa']['natpf'] ) ) {
					if( strstr( $search['Detailcalculdroitrsa']['natpf'], ',' ) === false && strstr( $search['Detailcalculdroitrsa']['natpf'], '-' ) === false ) {
						$condition = 'Detaildroitrsa.id IN (
									SELECT detailscalculsdroitsrsa.detaildroitrsa_id
										FROM detailscalculsdroitsrsa
											INNER JOIN detailsdroitsrsa ON (
												detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id
											)
										WHERE
											detailsdroitsrsa.dossier_id = Dossier.id
											AND detailscalculsdroitsrsa.natpf = \''.Sanitize::clean( $search['Detailcalculdroitrsa']['natpf'], array( 'encode' => false ) ).'\'
								)';

						$conditions[] = $condition;
					}
					// Si -
					else if( strstr( $search['Detailcalculdroitrsa']['natpf'], '-' ) !== false ) {
						$natspfs = explode( '-', $search['Detailcalculdroitrsa']['natpf'] );
						$natpfOui = $natspfs[0];
						$natpfNon = $natspfs[1];

						$conditions = $this->conditionsDetailcalculdroitrsa(
							$model,
							$conditions,
							array(
								'Detailcalculdroitrsa' => array(
									'natpf' => $natpfOui
								)
							)
						);

						$conditionsNon = $this->conditionsDetailcalculdroitrsa(
							$model,
							array(),
							array(
								'Detailcalculdroitrsa' => array(
									'natpf' => $natpfNon
								)
							)
						);

						$conditions[] = array( 'NOT' => $conditionsNon );
					}
					else {
						$natspfs = explode( ',', $search['Detailcalculdroitrsa']['natpf'] );
						foreach( $natspfs as $natpf ) {
							$conditions = $this->conditionsDetailcalculdroitrsa(
								$model,
								$conditions,
								array(
									'Detailcalculdroitrsa' => array(
										'natpf' => $natpf
									)
								)
							);
						}
					}
				}
				else {
					$multipleEnd = false;
					foreach( $search['Detailcalculdroitrsa']['natpf'] as $natpf ) {
						if( strstr( $natpf, ',' ) !== false || strstr( $natpf, '-' ) !== false ) {
							$multipleEnd = true;
						}
					}

					if( !$multipleEnd ) {
						$condition = 'Detaildroitrsa.id IN (
									SELECT detailscalculsdroitsrsa.detaildroitrsa_id
										FROM detailscalculsdroitsrsa
											INNER JOIN detailsdroitsrsa ON (
												detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id
											)
										WHERE
											detailsdroitsrsa.dossier_id = Dossier.id
											AND detailscalculsdroitsrsa.natpf IN ( \''.implode( '\', \'', $search['Detailcalculdroitrsa']['natpf'] ).'\' )
								)';
						$conditions[] = $condition;
					}
					else {
						$conditionsMultiples = array();

						foreach( $search['Detailcalculdroitrsa']['natpf'] as $natpf ) {
							$conditionsMultiples[] = $this->conditionsDetailcalculdroitrsa(
								$model,
								array(),
								array(
									'Detailcalculdroitrsa' => array(
										'natpf' => $natpf
									)
								)
							);
						}

						$conditions[] = array( 'OR' => $conditionsMultiples );
					}
				}
			}

			return $conditions;
		}

		/**
		 * Filtres sur le Calculdroitrsa: toppersdrodevorsa
		 *
		 * @param Model $model
		 * @param array $conditions
		 * @param array $search
		 * @return array
		 */
		public function conditionsCalculdroitrsa( Model $model, $conditions, $search ) {
			if( isset( $search['Calculdroitrsa']['toppersdrodevorsa'] ) ) {
				if( is_numeric( $search['Calculdroitrsa']['toppersdrodevorsa'] ) ) {
					$conditions[] = array( 'Calculdroitrsa.toppersdrodevorsa' => $search['Calculdroitrsa']['toppersdrodevorsa'] );
				}
				else if( $search['Calculdroitrsa']['toppersdrodevorsa'] == 'NULL' ) {
					$conditions[] = array( 'Calculdroitrsa.toppersdrodevorsa IS NULL' );
				}
			}

			return $conditions;
		}

		/**
		 * Combinaison des filtres conditionsDossier, conditionsPersonne, conditionsFoyer,
		 * conditionsSituationdossierrsa, conditionsDetaildroitrsa, conditionsDetailcalculdroitrsa
		 * et conditionsCalculdroitrsa.
		 *
		 * @param Model $model
		 * @param array $conditions
		 * @param array $search
		 * @return array
		 */
		public function conditionsPersonneFoyerDossier( Model $model, $conditions, $search ) {
			$conditions = $this->conditionsDossier( $model, $conditions, $search );
			$conditions = $this->conditionsServiceinstructeur( $model, $conditions, $search );
			$conditions = $this->conditionsPersonne( $model, $conditions, $search );
			$conditions = $this->conditionsFoyer( $model, $conditions, $search );
			$conditions = $this->conditionsSituationdossierrsa( $model, $conditions, $search );
			$conditions = $this->conditionsDetaildroitrsa( $model, $conditions, $search );
			$conditions = $this->conditionsDetailcalculdroitrsa( $model, $conditions, $search );
			$conditions = $this->conditionsCalculdroitrsa( $model, $conditions, $search );

			return $conditions;
		}

		/**
		 * Conditions permettant d'obtenir le dernier dossier pour un allocataire donné.
		 *
		 * Si dans la configuration, la clé Optimisations.useTableDernierdossierallocataire
		 * est à la valeur booléenne true, alors la table derniersdossiersallocataires
		 * sera utilisée, sinon on effectuera la sous-requête avec les jointures.
		 *
		 * @param Model $model
		 * @param array $conditions
		 * @param array $search
		 * @return array
		 */
		public function conditionsDernierDossierAllocataire( Model $model, $conditions, $search ) {
			if( isset( $search['Dossier']['dernier'] ) && $search['Dossier']['dernier'] ) {
				if( Configure::read( 'Optimisations.useTableDernierdossierallocataire' ) === true ) {
					$conditions[] = 'Dossier.id IN (
						SELECT
								derniersdossiersallocataires.dossier_id
							FROM derniersdossiersallocataires
							WHERE
								derniersdossiersallocataires.personne_id = Personne.id
					)';
				}
				else {
					// Ordre par prestation pour avoir réellement le dernier dossier de l'allocataire lié à sa dernière action plutôt qu'à son ID le plus haut
					$conditions[] = 'Dossier.id IN (
						SELECT
								dossiers.id
							FROM personnes
								INNER JOIN prestations ON (
									personnes.id = prestations.personne_id
									AND prestations.natprest = \'RSA\'
								)
								INNER JOIN foyers ON (
									personnes.foyer_id = foyers.id
								)
								INNER JOIN dossiers ON (
									dossiers.id = foyers.dossier_id
								)
							WHERE
								prestations.rolepers IN ( \'DEM\', \'CJT\' )
								AND (
									(
										nir_correct13( Personne.nir )
										AND nir_correct13( personnes.nir )
										AND SUBSTRING( TRIM( BOTH \' \' FROM personnes.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM Personne.nir ) FROM 1 FOR 13 )
										AND personnes.dtnai = Personne.dtnai
									)
									OR
									(
										UPPER(personnes.nom) = UPPER(Personne.nom)
										AND UPPER(personnes.prenom) = UPPER(Personne.prenom)
										AND personnes.dtnai = Personne.dtnai
									)
								)
							ORDER BY dossiers.dtdemrsa DESC, prestations.id DESC, dossiers.id DESC
							LIMIT 1
					)';
				}
			}

			return $conditions;
		}

		/**
		 * Ajoute des conditions sur des plages de dates. Pour chacun des $paths, on extrait le nom du
		 * modèle et le nom du champ; si un checkbox existe avec ce chemin-là, on cherchera une date
		 * située entre <chemin>_from (inclus) et <chemin>_to (exclus).
		 *
		 * Exemple:
		 * <pre>$this->conditionsDates(
		 *	$model,
		 *	array(),
		 *	array(
		 *		'Orientstruct' => array(
		 *			'date_valid' => true,
		 *			'date_valid_from' => array(
		 *				'year' => '2012',
		 *				'month' => '03',
		 *				'day' => '01'
		 *			),
		 *			'date_valid_to' => array(
		 *				'year' => '2012',
		 *				'month' => '03',
		 *				'day' => '02'
		 *			),
		 *		)
		 *	),
		 *	'Orientstruct.date_valid'
		 * );</pre>
		 * retournera
		 * <pre>array( '"Orientstruct"."date_valid" BETWEEN \'2012-03-01\' AND \'2012-03-02\'' )</pre>
		 *
		 * @see app/views/criteres/index.ctp
		 * @see app/views/cohortes/filtre.ctp
		 * @see Dossier.dtdemrsa, ...
		 *
		 * @param Model $model Le modèle auquel ce behavior est attaché
		 * @param array $conditions Les conditions déjà existantes
		 * @param array $search Les critères renvoyés par le formulaire de recherche
		 * @param mixed $paths Le chemin (ou les chemins) sur lesquels on cherche à appliquer ces filtres.
		 * @return array
		 */
		public function conditionsDates( Model $model, $conditions, $search, $paths ) {
			$paths = (array)$paths;

			if( !empty( $paths ) ) {
				foreach( $paths as $path ) {
					list( $modelName, $fieldName ) = model_field( $path );
					if( isset( $search[$modelName][$fieldName] ) && $search[$modelName][$fieldName] ) {
						$from = Hash::get( $search, "{$modelName}.{$fieldName}_from" );
						$to = Hash::get( $search, "{$modelName}.{$fieldName}_to" );

						// Ajout des jours pour une recherche par MM/YYYY
						if (!isset ($from['day'])) {
							// Le premier jour du mois
							$from['day'] = '1';
						}
						if (!isset ($to['day'])) {
							// Le dernier jour du mois
							$to['day'] = date ('t', mktime (0, 0, 0, $to['month'], 1, $to['year']));
						}

						if( is_string( $from ) && !empty( $from ) ) {
							$from = date_sql_to_cakephp( $from );
						}

						if( is_string( $to ) && !empty( $to ) ) {
							$to = date_sql_to_cakephp( $to );
						}

						if( valid_date( $from ) && valid_date( $to ) ) {
							$from = $from['year'].'-'.$from['month'].'-'.$from['day'];
							$to = $to['year'].'-'.$to['month'].'-'.$to['day'];

							$conditions[] = "DATE( {$modelName}.{$fieldName} ) BETWEEN '{$from}' AND '{$to}'";
						}
					}
				}
			}

			return $conditions;
		}

		/**
		 * Ajoute des conditions sur des dates. Pour chacun des $paths, on
		 * extrait le nom du modèle et le nom du champ.
		 *
		 * Exemple:
		 * <pre>$this->conditionsDate(
		 *	$model,
		 *	array(),
		 *	array(
		 *		'Orientstruct' => array(
		 *			'date_valid' => array(
		 *				'year' => '2012',
		 *				'month' => '03',
		 *				'day' => '01'
		 *			)
		 *		)
		 *	),
		 *	'Orientstruct.date_valid'
		 * );</pre>
		 * retournera
		 * <pre>array( '"Orientstruct"."date_valid" = \'2012-03-01\'' )</pre>
		 *
		 * @param Model $model Le modèle auquel ce behavior est attaché
		 * @param array $conditions Les conditions déjà existantes
		 * @param array $search Les critères renvoyés par le formulaire de recherche
		 * @param mixed $paths Le chemin (ou les chemins) sur lesquels on cherche à appliquer ces filtres.
		 * @return array
		 */
		public function conditionsDate( Model $model, $conditions, $search, $paths ) {
			$paths = (array)$paths;

			if( !empty( $paths ) ) {
				foreach( $paths as $path ) {
					list( $modelName, $fieldName ) = model_field( $path );
					if( isset( $search[$modelName][$fieldName] ) ) {
						if( is_string( $search[$modelName][$fieldName] ) && !empty( $search[$modelName][$fieldName] ) ) {
							$value = date_sql_to_cakephp( $search[$modelName][$fieldName] );
						}
						else {
							$value = $search[$modelName][$fieldName];
						}

						if( valid_date( $value ) ) {
							$value = date_cakephp_to_sql( $value );
							$conditions[] = "DATE( {$modelName}.{$fieldName} ) = '{$value}'";
						}
					}
				}
			}

			return $conditions;
		}

		/**
		 * Ajoute des conditions sur des plages d'heure. Pour chacun des $paths, on extrait le nom du
		 * modèle et le nom du champ; si un checkbox existe avec ce chemin-là, on cherchera une date
		 * située entre <chemin>_from (inclus) et <chemin>_to (inclus).
		 *
		 * Exemple:
		 * <pre>$this->conditionsDates(
		 *	$model,
		 *	array(),
		 *	array(
		 *		'Rendezvous' => array(
		 * 			'daterdv' => '0',
		 *			'heurerdv' => '1',
		 *			'heurerdv_from' => array(
		 *				'hour' => '12',
		 *				'min' => '25'
		 *			),
		 *			'heurerdv_to' => array(
		 *				'hour' => '18',
		 *				'min' => '25'
		 *			)
		 *		),
		 *	),
		 * );</pre>
		 * retournera
		 * <pre>array( '"Rendezvous"."heurerdv" BETWEEN \'12:25:00\' AND \'18:25:00\'' )</pre>
		 *
		 * @see app/views/rendezvous/search.ctp
		 *
		 * @param Model $model Le modèle auquel ce behavior est attaché
		 * @param array $conditions Les conditions déjà existantes
		 * @param array $search Les critères renvoyés par le formulaire de recherche
		 * @param mixed $paths Le chemin (ou les chemins) sur lesquels on cherche à appliquer ces filtres.
		 * @return array
		 */
		public function conditionsHeures( Model $model, $conditions, $search, $paths ) {
			$paths = (array)$paths;
			if( !empty( $paths ) ) {
				foreach( $paths as $path ) {
					list( $modelName, $fieldName ) = model_field( $path );
					if( isset( $search[$modelName][$fieldName] ) && $search[$modelName][$fieldName] ) {
						$from = Hash::get( $search, "{$modelName}.{$fieldName}_from" );
						$to = Hash::get( $search, "{$modelName}.{$fieldName}_to" );
						if( is_string( $from ) && !empty( $from ) ) {
							$from = time_sql_to_cakephp( $from );
						}elseif ( count($from) == 2 && isset( $from['hour'] ) && isset( $from['min'] ) ){
							$from['sec'] = '00';
						}

						if( is_string( $to ) && !empty( $to ) ) {
							$to = time_sql_to_cakephp( $to );
						}elseif ( count($to) == 2 && isset( $to['hour'] ) && isset( $to['min'] ) ){
							$to['sec'] = '00';
						}

						if( valid_time( $from ) && valid_time( $to ) ) {
							$from = time_cakephp_to_sql($from);
							$to = time_cakephp_to_sql($to);

							$conditions[] = "{$modelName}.{$fieldName} BETWEEN '{$from}' AND '{$to}'";
						}
					}
				}
			}

			return $conditions;
		}

		/**
		 * Ajoute des conditions sur la communauté de structures référentes s'il
		 * y a lieu (pour le CG 93 uniquement).
		 *
		 * Exemple:
		 * <pre>$this->conditionCommunautesr(
		 *	$model,
		 *	array(),
		 *	array(
		 *		'Orientstruct' => array(
		 *			'communautesr_id' => '1'
		 *		)
		 *	),
		 *	array( 'Orientstruct.communautesr_id' => 'Orientstruct.structurereferente_id' )
		 * );</pre>
		 * retournera
		 * <pre>array( Orientstruct.structurereferente_id IN (SELECT "communautessrs_structuresreferentes"."structurereferente_id" AS "CommunautesrStructurereferente__structurereferente_id" FROM "communautessrs_structuresreferentes" AS "communautessrs_structuresreferentes"   WHERE "communautessrs_structuresreferentes"."communautesr_id" = 1   ))</pre>
		 *
		 * @param Model $model
		 * @param array $conditions
		 * @param array $search
		 * @param array $paths
		 * @return array
		 */
		public function conditionCommunautesr( Model $model, array $conditions, array $search, array $paths ) {
			$departement = Configure::read( 'Cg.departement' );
			$Communautesr = ClassRegistry::init( 'Communautesr' );

			if( 93 == $departement && !empty( $paths ) ) {
				foreach( $paths as $filterPath => $conditionPath ) {
					$communautesr_id = suffix( Hash::get( $search, $filterPath ) );

					if( !empty( $communautesr_id ) ) {
						$sql = $Communautesr->sqStructuresreferentes( $communautesr_id );
						$conditions[] = array( "{$conditionPath} IN ({$sql})" );
					}
				}
			}

			return $conditions;
		}

		/**
		 * Filtres sur le Calculdroitrsa: toppersdrodevorsa
		 *
		 * @param Model $model
		 * @param array $conditions
		 * @param array $search
		 * @return array
		 */
		public function conditionsRendezvous ( Model $model, $conditions, $search ) {
			// Dates et heures
			$conditions = $this->conditionsDates ( $model, $conditions, $search, 'Rendezvous.daterdv' );
			$conditions = $this->conditionsHeures ( $model, $conditions, $search, 'Rendezvous.heurerdv' );

			// Structure référente
			if( isset( $search['Rendezvous']['structurereferente_id'] ) ) {
				if( is_numeric( $search['Rendezvous']['structurereferente_id'] ) ) {
					$conditions[] = array( 'Rendezvous.structurereferente_id' => $search['Rendezvous']['structurereferente_id'] );
				}
			}

			// Permanence
			if( isset( $search['Rendezvous']['permanence_id'] ) ) {
				$permanence_id = explode ('_', $search['Rendezvous']['permanence_id']);
				$permanence_id = end ($permanence_id);
				if( is_numeric( $permanence_id ) ) {
					$conditions[] = array( 'Rendezvous.permanence_id' => $permanence_id );
				}
			}

			return $conditions;
		}
	}
?>