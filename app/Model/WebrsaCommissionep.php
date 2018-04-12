<?php
	/**
	 * Code source de la classe WebrsaCommissionep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractLogic', 'Model' );
	App::uses( 'WebrsaLogicAccessInterface', 'Model/Interface' );

	/**
	 * La classe WebrsaCommissionep ...
	 *
	 * @package app.Model
	 */
	class WebrsaCommissionep extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCommissionep';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Commissionep' );

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @todo
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'Commissionep.id',
					'Commissionep.etatcommissionep',
				)
			);
			return $query;
		}

		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 *
		 * @todo
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array()) {
			$query = array(
				'fields' => array(
					'Commissionep.id',
					'Commissionep.etatcommissionep',
				),
				'conditions' => $conditions,
				'contain' => false,
				'order' => array(
					'Commissionep.dateseance' => 'DESC',
					'Commissionep.id' => 'DESC',
				)
			);

			$results = $this->Commissionep->find( 'all', $this->completeVirtualFieldsForAccess( $query ) );
			return $results;
		}

		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 *
		 * @see WebrsaAccess::getParamsList
		 * @param integer $personne_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess($personne_id, array $params = array()) {
			$results = array();

			if (in_array('ajoutPossible', $params)) {
				$results['ajoutPossible'] = $this->ajoutPossible($personne_id);
			}

			return $results;
		}

		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 *
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function ajoutPossible($personne_id) {
			return true;
		}

		/**
		 * Chemin relatif pour les modèles de documents .odt utilisés lors des
		 * impressions. Utiliser %s pour remplacer par l'alias.
		 */
		public $modelesOdt = array(
			'Commissionep/pv.odt',
			'Commissionep/convocationep_participant.odt',
			'Commissionep/convocationep_beneficiaire.odt',
		);

		/**
		 *
		 * @param array $criteresseanceep
		 * @param boolean $filtre_zone_geo
		 * @param array $zonesgeographiques
		 * @return array
		 */
		public function search( $criteresseanceep, $filtre_zone_geo, $zonesgeographiques ) {
			/// Conditions de base

			$conditions = $this->Commissionep->Ep->sqRestrictionsZonesGeographiques(
				'Commissionep.ep_id',
				$filtre_zone_geo,
				$zonesgeographiques
			);

			if ( isset($criteresseanceep['Ep']['regroupementep_id']) && !empty($criteresseanceep['Ep']['regroupementep_id']) ) {
				$conditions[] = array('Ep.regroupementep_id'=>$criteresseanceep['Ep']['regroupementep_id']);
			}

			if ( isset($criteresseanceep['Commissionep']['name']) && !empty($criteresseanceep['Commissionep']['name']) ) {
				$conditions[] = array('Commissionep.name'=>$criteresseanceep['Commissionep']['name']);
			}

			if ( isset($criteresseanceep['Commissionep']['identifiant']) && !empty($criteresseanceep['Commissionep']['identifiant']) ) {
				$conditions[] = array('Commissionep.identifiant'=>$criteresseanceep['Commissionep']['identifiant']);
			}

			if ( isset($criteresseanceep['Structurereferente']['ville']) && !empty($criteresseanceep['Structurereferente']['ville']) ) {
				$conditions[] = array('Commissionep.villeseance'=>$criteresseanceep['Structurereferente']['ville']);
			}

			/// Critères sur le Comité - date du comité
			$conditions = $this->Commissionep->conditionsDates( $conditions, $criteresseanceep, 'Commissionep.dateseance' );

			$query = array(
				'contain'=>array(
					'Ep' => array(
						'Regroupementep'
					),
					'Membreep'
				),
				'order' => array( '"Commissionep"."dateseance" ASC' ),
				'conditions' => $conditions
			);

			return $query;
		}

		/**
		 * Renvoie un array associatif contenant les thèmes traités par la commission
		 * ainsi que le niveau de décision pour chacun de ces thèmes.
		 *
		 * @param integer $id L'id technique de la commission d'EP
		 * @return array
		 * @access public
		 */

		public function themesTraites( $id ) {
			$regroupementep = $this->Commissionep->Ep->Regroupementep->find(
				'first',
				array(
					'contain' => false,
					'conditions' => array(
						'Regroupementep.id IN ( '.
							$this->Commissionep->Ep->sq(
								array(
									'alias' => 'eps',
									'fields' => array( 'eps.regroupementep_id' ),
									'conditions' => array(
										'eps.id IN ( '.
											$this->Commissionep->sq(
												array(
													'alias' => 'commissionseps',
													'fields' => array( 'commissionseps.ep_id' ),
													'conditions' => array(
														'commissionseps.id' => $id
													)
												)
											)
										.' )'
									)
								)
							)
						.' )'
					)
				)
			);

			$themes = $this->Commissionep->Ep->themes();
			$themesTraites = array();

			foreach( $themes as $theme ) {
				if( isset( $regroupementep['Regroupementep'][$theme] ) && in_array( $regroupementep['Regroupementep'][$theme], array( 'decisionep', 'decisioncg' ) ) ) {
					$themesTraites[$theme] = $regroupementep['Regroupementep'][$theme];
				}
			}

			return $themesTraites;
		}

		/**
		 * Sauvegarde des avis/décisions par liste d'une séance d'EP, au niveau ep ou cg
		 *
		 * @param integer $commissionep_id L'id technique de la séance d'EP
		 * @param array $data Les données à sauvegarder
		 * @param string $niveauDecision Le niveau de décision pour lequel il faut sauvegarder
		 * @return boolean
		 * @access public
		 */

		public function saveDecisions( $commissionep_id, $data, $niveauDecision ) {
			$commissionep = $this->Commissionep->find( 'first', array( 'conditions' => array( 'Commissionep.id' => $commissionep_id ) ) );

			if( empty( $commissionep ) ) {
				return false;
			}

			$success = true;

			// Champs à conserver en cas d'annulation ou de report
			$champsAGarder = array( 'id', 'etape', 'passagecommissionep_id', 'user_id', 'created', 'modified' );
			$champsAGarderPourNonDecision = Set::merge( $champsAGarder, array( 'decision', 'decisionpcg', 'decision2', 'commentaire', 'raisonnonpassage' ) );

			foreach( $this->themesTraites( $commissionep_id ) as $theme => $decision ) {
				$model = Inflector::classify( $theme );
				$modeleDecision = Inflector::classify( "decision{$theme}" );
				if( isset( $data[$model] ) || isset( $data[$modeleDecision] ) && !empty( $data[$modeleDecision] ) ) {
					// Mise à NULL de certains champs de décision
					$champsDecision = array_keys( $this->Commissionep->Passagecommissionep->{$modeleDecision}->schema( true ) );
					$champsANull = array_fill_keys( array_diff( $champsDecision, $champsAGarder ), null );
					$champsANullPourNonDecision = array_diff( $champsDecision, $champsAGarderPourNonDecision );
					foreach( $data[$modeleDecision] as $i => $decision ) {
						// 1°) En cas d'annulation ou de report
						if( in_array( $decision['decision'], array( 'annule', 'reporte' ) ) ) {
							foreach( $champsANullPourNonDecision as $champ ) {
								if( isset( $data[$modeleDecision][$i][$champ] ) ) {
									$data[$modeleDecision][$i][$champ] = null;
								}
							}
						}
						// 2°) Dans les autres cas
						else {
							$data[$modeleDecision][$i] = Set::merge( $champsANull, $decision );
						}
					}

					$success = $this->Commissionep->Passagecommissionep->Dossierep->{$model}->saveDecisions( $data, $niveauDecision ) && $success;
				}
			}

			///FIXME : calculer si tous les dossiers ont bien une décision avant de changer l'état ?
			$this->Commissionep->id = $commissionep_id;
			$this->Commissionep->set( 'etatcommissionep', "decision{$niveauDecision}" );
			$success = $this->Commissionep->save( null, array( 'atomic' => false ) ) && $success;

			return $success;
		}

		/**
		 * Retourne la liste des dossiers de la séance d'EP, groupés par thème,
		 * pour les dossiers qui doivent passer par liste.
		 *
		 * @see Commissionep::querydataFragmentsErrors()
		 *
		 * @param integer $commissionep_id L'id technique de la séance d'EP
		 * @param string $niveauDecision Le niveau de décision ('decisionep' ou 'decisioncg') pour
		 * 	lequel il faut les dossiers à passer par liste.
		 * @param string $actionName Le nom de l'action du Controller qui appel cette fonction (pour clef de config)
		 * @return array
		 * @access public
		 */
		public function dossiersParListe( $commissionep_id, $niveauDecision, $keyConf = 'default' ) {
			$dossiers = array();

			foreach( $this->themesTraites( $commissionep_id ) as $theme => $decision ) {
				$model = Inflector::classify( $theme );
				$queryData = $this->Commissionep->Passagecommissionep->Dossierep->{$model}->qdDossiersParListe( $commissionep_id, $niveauDecision );
				$dossiers[$model]['liste'] = array();
				if( !empty( $queryData ) ) {
					$configuredOrder = Configure::read( $keyConf );
					$queryData['order'] = $configuredOrder ? $configuredOrder : array( 'Personne.nom', 'Personne.prenom' );
					$dossiers[$model]['liste'] = $this->Commissionep->Passagecommissionep->Dossierep->find( 'all', $queryData );
				}
			}

			return $dossiers;
		}

		/**
		 * Retourne les données par défaut du formulaire de traitement par liste,
		 * pour une séance donnée, pour des dossiers données et à un niveau de
		 * décision donné.
		 *
		 * @param integer $commissionep_id L'id technique de la séance d'EP
		 * @param array $dossiers Array de résultats de requêtes CakePHP pour
		 * 	chacun des thèmes, par liste.
		 * @param string $niveauDecision Le niveau de décision ('decisionep' ou 'decisioncg')
		 *	pour lequel on veut obtenir les données par défaut du formulaire de
		 *	traitement.
		 * @return array
		 * @access public
		 */
		public function prepareFormData( $commissionep_id, $dossiers, $niveauDecision ) {
			$data = array();

			foreach( $this->themesTraites( $commissionep_id ) as $theme => $decision ) {
				$model = Inflector::classify( $theme );

				$data = Set::merge(
					$data,
					$this->Commissionep->Passagecommissionep->Dossierep->{$model}->prepareFormData(
						$commissionep_id,
						$dossiers[$model]['liste'],
						$niveauDecision
					)
				);
			}

			return $data;
		}

		/**
		 * Tentative de finalisation des décisions d'une séance donnée, pour un
		 * niveau de décision donné.
		 * Retourne false si tous les dossiers de la séance n'ont pas eu de décision
		 * ou si la finalisation n'a pas pu avoir lieu.
		 *
		 * TODO: être plus précis dans la description de la fonction + faire une
		 * doc précise pour les fonctions "finaliser" des différents modèles de
		 * thèmes.
		 *
		 * @param integer $commissionep_id L'id technique de la séance d'EP
		 * @param string $niveauDecision Le niveau de décision ('decisionep' ou 'decisioncg')
		 *	pour lequel on veut finaliser les décisions.
		 * @return boolean
		 * @access public
		 */

		public function finaliser( $commissionep_id, $data, $niveauDecision, $user_id ) {
			$themesTraites = $this->themesTraites( $commissionep_id );

			// Sauvegarde des règles de validation (pour les inList)
			$validates = array();

			// Première partie: revalidation "spéciale" des décisions
			foreach( $themesTraites as $themeTraite => $niveauDecisionTheme ) {
				$modeleDecision = Inflector::classify( "decision{$themeTraite}" );
				if( isset( $this->Commissionep->Passagecommissionep->{$modeleDecision}->validateFinalisation ) ) {
					$validates[$modeleDecision] = $this->Commissionep->Passagecommissionep->{$modeleDecision}->validate;
					$this->Commissionep->Passagecommissionep->{$modeleDecision}->validate = $this->Commissionep->Passagecommissionep->{$modeleDecision}->validateFinalisation;
				}
			}

			if( !$this->saveDecisions( $commissionep_id, $data, $niveauDecision ) ) {
				return false;
			}

			// Deuxième partie: recherche des dossiers pas encore traités à cette étape
			$success = true;
			$totalErrors = 0;
			foreach( $themesTraites as $themeTraite => $niveauDecisionTheme ) {
// 				$themeTraite = Inflector::tableize( $themeTraite );

				// On est au niveau de décision final ou au niveau cg
				if( ( $niveauDecisionTheme == "decision{$niveauDecision}" ) || $niveauDecisionTheme == 'decisioncg' ) {
					// FIXME: nbDossiersPourEtape et nbDossiersEtapeSuivante
					$themeTraite = Inflector::classify( $themeTraite );
					$totalErrors += $this->Commissionep->Passagecommissionep->Dossierep->{$themeTraite}->nbErreursFinaliserCg( $commissionep_id, $niveauDecision );
				}
			}

			if( empty( $totalErrors ) ) {
				$niveauMax = 'decisionep';
				foreach( $themesTraites as $themeTraite => $niveauDecisionTheme ) {
					$themeTraite = Inflector::tableize( $themeTraite );
					$tableDecisionTraite = "decisions".Inflector::underscore( $themeTraite );
					$modelDecisionTraite = Inflector::classify( $tableDecisionTraite );

					if( "decision{$niveauDecision}" == $niveauDecisionTheme ) {
						$this->Commissionep->Passagecommissionep->updateAllUnBound(
							array( 'Passagecommissionep.etatdossierep' => '\'traite\'' ),
							array(
								'"Passagecommissionep"."commissionep_id"' => $commissionep_id,
								'"Passagecommissionep"."id" NOT IN ( '. $this->Commissionep->Passagecommissionep->{$modelDecisionTraite}->sq(
									array(
										'fields' => array(
											"{$tableDecisionTraite}.passagecommissionep_id"
										),
										'alias' => "{$tableDecisionTraite}",
										'conditions' => array(
											"{$tableDecisionTraite}.decision" => array( 'reporte', 'annule' ),
											"{$tableDecisionTraite}.etape" => $niveauDecision
										)
									)
								).' )',
								'"Passagecommissionep"."dossierep_id" IN ( '. $this->Commissionep->Passagecommissionep->Dossierep->sq(
									array(
										'fields' => array(
											'dossierseps.id'
										),
										'alias' => 'dossierseps',
										'conditions' => array(
											'dossierseps.themeep' => $themeTraite
										)
									)
								).' )'
							)
						);

						$listeDecisions = array( 'annule', 'reporte' );
						foreach( $listeDecisions as $decision ) {
							$this->Commissionep->Passagecommissionep->updateAllUnBound(
								array( 'Passagecommissionep.etatdossierep' => "'{$decision}'" ),
								array(
									'"Passagecommissionep"."commissionep_id"' => $commissionep_id,
									'"Passagecommissionep"."id" IN ( '. $this->Commissionep->Passagecommissionep->{$modelDecisionTraite}->sq(
										array(
											'fields' => array(
												"{$tableDecisionTraite}.passagecommissionep_id"
											),
											'alias' => "{$tableDecisionTraite}",
											'conditions' => array(
												"{$tableDecisionTraite}.decision" => array( $decision ),
													"{$tableDecisionTraite}.etape" => $niveauDecision
											)
										)
									).' )'
								)
							);
						}

						if( $tableDecisionTraite == 'decisionsnonrespectssanctionseps93' || $tableDecisionTraite == 'decisionssignalementseps93' ) {
							$listeDecisions = array( '1pasavis', '2pasavis' );
							foreach( $listeDecisions as $decision ) {
								$this->Commissionep->Passagecommissionep->updateAllUnBound(
									array( 'Passagecommissionep.etatdossierep' => "'reporte'" ),
									array(
										'"Passagecommissionep"."commissionep_id"' => $commissionep_id,
										'"Passagecommissionep"."id" IN ( '. $this->Commissionep->Passagecommissionep->{$modelDecisionTraite}->sq(
											array(
												'fields' => array(
													"{$tableDecisionTraite}.passagecommissionep_id"
												),
												'alias' => "{$tableDecisionTraite}",
												'conditions' => array(
													"{$tableDecisionTraite}.decision" => array( $decision ),
														"{$tableDecisionTraite}.etape" => $niveauDecision
												)
											)
										).' )'
									)
								);
							}
						}

					}
					elseif( $niveauDecisionTheme == 'decisioncg' && "decision{$niveauDecision}" == 'decisionep' ) {
						$this->Commissionep->Passagecommissionep->updateAllUnBound(
							array( 'Passagecommissionep.etatdossierep' => '\'decisioncg\'' ),
							array(
								'"Passagecommissionep"."commissionep_id"' => $commissionep_id
							)
						);
					}

					if ( $niveauDecisionTheme == 'decisioncg' ) {
						$niveauMax = 'decisioncg';
					}
				}

				$commissionep = $this->Commissionep->find(
					'first',
					array(
						'conditions' => array(
							'Commissionep.id' => $commissionep_id
						)
					)
				);

				if( "decision{$niveauDecision}" == 'decisioncg' || ( $niveauMax == 'decisionep' && "decision{$niveauDecision}" == 'decisionep' ) ) {
					$commissionep['Commissionep']['etatcommissionep'] = 'traite';
					// Finalisation de chacun des dossiers
					foreach( $themesTraites as $themeTraite => $niveauDecisionTheme ) {
						if( $niveauDecisionTheme == "decision{$niveauDecision}" ) {
							$themeTraite = Inflector::tableize( $themeTraite );
							$model = Inflector::classify( $themeTraite );
							$success = $this->Commissionep->Passagecommissionep->Dossierep->{$model}->finaliser( $commissionep_id, $niveauDecision, $user_id ) && $success;
						}
					}
				}
				else {
					$niveauxDecisionsSeance = array_values( $themesTraites );
					$commissionep['Commissionep']['etatcommissionep'] = 'traiteep';
					if( !in_array( 'decisioncg', $niveauxDecisionsSeance ) ) {
						// Finalisation de chacun des dossiers
						foreach( $themesTraites as $themeTraite => $niveauDecisionTheme ) {
							$themeTraite = Inflector::tableize( $themeTraite );
							$model = Inflector::classify( $themeTraite );
							if( $niveauDecisionTheme == "decision{$niveauDecision}" ) {
								$success = $this->Commissionep->Passagecommissionep->Dossierep->{$model}->finaliser( $commissionep_id, $niveauDecisionTheme, $user_id ) && $success;
							}
							else {
								$success = $this->Commissionep->Passagecommissionep->Dossierep->{$model}->verrouiller( $commissionep_id, $niveauDecision, $user_id ) && $success;
							}
						}
					}
				}
				$this->Commissionep->id = $commissionep['Commissionep']['id'];
				$this->Commissionep->set( 'etatcommissionep', $commissionep['Commissionep']['etatcommissionep'] );
				$success = $this->Commissionep->save( null, array( 'atomic' => false ) ) && $success;
			}

			$success = $success && empty( $totalErrors );

			// FIXME: mettre en champs cachés les décisions de cette thématique au niveau CG lorsqu'au moins un dossier de cette thématique doit avoir une décision CG
			if( $success && Configure::read( 'Cg.departement' ) == 66 && $niveauDecision == 'ep' ) {
				$dataCg = $data;
				$nbDossierATraiterCg = 0;

				$containStoredDataCg = array();
				foreach( $themesTraites as $themeTraite => $niveauDecisionTheme ) {
					$modelName = Inflector::classify( $themeTraite );
					$modelDecisionName = 'Decision'.strtolower( $modelName );

					if( $niveauDecisionTheme == 'decisioncg' ) {
						$nbDossierATraiterCg += $this->Commissionep->Passagecommissionep->Dossierep->{$modelName}->nbDossiersATraiterCg( $commissionep_id );

						if( $nbDossierATraiterCg == 0 && isset($dataCg[$modelDecisionName]) ) {
							foreach( $dataCg[$modelDecisionName] as $i => $dataDecision  ) {
								unset( $dataCg[$modelDecisionName][$i]['id'] );
								$dataCg[$modelDecisionName][$i]['etape'] = 'cg';
							}
						}

						$containStoredDataCg[$modelDecisionName] = array( 'conditions' => array( 'etape' => 'cg' ) );
					}
				}

				if( $nbDossierATraiterCg == 0 ) {
					$success = $this->saveDecisions( $commissionep_id, $dataCg, 'cg' ) && $success;
					$storedDataCg = $this->Commissionep->Passagecommissionep->find(
						'first',
						array(
							'conditions' => array(
								'Passagecommissionep.commissionep_id' => $commissionep_id
							),
							'contain' => $containStoredDataCg
						)
					);
					unset( $storedDataCg['Passagecommissionep'] );

					$success = $this->finaliser( $commissionep_id, $storedDataCg, 'cg', $user_id ) && $success;
				}

				// On génère un dossier PCG à partir du niveau EP d'une commission Audition quoi qu'il arrive
				// @info mériterait une méthode du type "Traitement intermédiaire avant l'étape de finalisation"
				foreach( $themesTraites as $themeTraite => $niveauDecisionTheme ) {
					$themeTraite = Inflector::tableize( $themeTraite );
					$model = Inflector::classify( $themeTraite );
					$modeleDecision = 'Decision'.Inflector::underscore($model);

					if( $model == 'Defautinsertionep66' ) {
						$dateseanceCommission = $commissionep['Commissionep']['dateseance'];
						$success = $this->Commissionep->Passagecommissionep->Dossierep->{$model}->generateDossierpcg( $commissionep_id, $dateseanceCommission, $niveauDecision ) && $success;
					}

					$Bilanparcours = $this->Commissionep->Passagecommissionep->Dossierep->{$model}->Bilanparcours66;
					$bilansIds = Hash::extract(
						$Bilanparcours->find('all',
							array(
								'fields' => 'Bilanparcours66.id',
								'joins' => array(
									$Bilanparcours->join($model),
									$Bilanparcours->{$model}->join('Dossierep'),
									$Bilanparcours->{$model}->Dossierep->join('Passagecommissionep'),
									$this->Commissionep->Passagecommissionep->join($modeleDecision),
								),
								'conditions' => array(
									$modeleDecision.'.id' => Hash::extract($data, $modeleDecision.'.{n}.id')
								)
							)
						), '{n}.Bilanparcours66.id'
					);

					$Bilanparcours->WebrsaBilanparcours66->updatePositionsByConditions(
						array($Bilanparcours->alias.".id" => $bilansIds)
					);
				}
			}

			// Restauration des règles de validation
			foreach( $validates as $alias => $validate ) {
				$this->Commissionep->Passagecommissionep->{$modeleDecision}->validate = $validates[$modeleDecision];
			}

			return $success;

		}

		/**
		 * Change l'état de la commission d'EP entre 'cree' et 'associe'
		 * S'il existe au moins un dossier associé et un membre ayant donné une réponse
		 * "Confirmé" ou "Remplacé par", l'état devient associé, sinon l'état devient 'cree'
		 *
		 * FIXME: il faudrait une réponse pour tous les membres ?
		 *
		 * @param integer $commissionep_id L'identifiant technique de la commission d'EP
		 * @return boolean
		 */

		public function changeEtatCreeAssocie( $commissionep_id ) {
			$commissionep = $this->Commissionep->find(
				'first',
				array(
					'conditions' => array(
						'Commissionep.id' => $commissionep_id
					),
					'contain' => array(
						'Ep',
						'CommissionepMembreep'
					)
				)
			);

			if( empty( $commissionep ) || !in_array( $commissionep['Commissionep']['etatcommissionep'], array( 'cree', 'quorum', 'associe' ) ) ) {
				return false;
			}

			$success = true;

			$nbDossierseps = $this->Commissionep->Passagecommissionep->find(
				'count',
				array(
					'conditions' => array(
						'Passagecommissionep.commissionep_id' => $commissionep_id
					)
				)
			);

			$nbMembresepsNonRenseignes = $this->Commissionep->CommissionepMembreep->find(
				'count',
				array(
					'conditions' => array(
						'CommissionepMembreep.commissionep_id' => $commissionep_id,
						'CommissionepMembreep.reponse' => array( 'nonrenseigne' ),
					)
				)
			);

			$nbMembresepsTotal = $this->Commissionep->CommissionepMembreep->find(
				'count',
				array(
					'conditions' => array(
						'CommissionepMembreep.commissionep_id' => $commissionep_id
					)
				)
			);

			$this->Commissionep->id = $commissionep_id;
			if( ( $nbDossierseps > 0 ) && ( $nbMembresepsNonRenseignes == 0 ) && ( $nbMembresepsTotal > 0 ) && ( $commissionep['Commissionep']['etatcommissionep'] == 'cree' || $commissionep['Commissionep']['etatcommissionep'] == 'quorum' ) ) {
				$this->Commissionep->set( 'etatcommissionep', 'associe' );
				$success = $this->Commissionep->save( null, array( 'atomic' => false ) ) && $success;
			}
			else if( ( ( $nbDossierseps == 0 ) || ( $nbMembresepsNonRenseignes > 0 ) || ( $nbMembresepsTotal == 0 ) ) && ( $commissionep['Commissionep']['etatcommissionep'] == 'associe' || $commissionep['Commissionep']['etatcommissionep'] == 'quorum' ) ) {
				$this->Commissionep->set( 'etatcommissionep', 'cree' );
				$success = $this->Commissionep->save( null, array( 'atomic' => false ) ) && $success;
			}
			return $success;
		}

		/**
		 * Change l'état de la commission d'EP entre 'associe' et 'presence'
		 * S'il existe au moins un membre présent à la commission
		 *
		 * FIXME: à modifier lors de la mise en place du corum
		 *
		 * @param integer $commissionep_id L'identifiant technique de la commission d'EP
		 * @return boolean
		 */

		public function changeEtatAssociePresence( $commissionep_id ) {
			$commissionep = $this->Commissionep->find(
				'first',
				array(
					'conditions' => array(
						'Commissionep.id' => $commissionep_id
					),
					'contain' => array(
						'Ep',
						'CommissionepMembreep'
					)
				)
			);

			if( empty( $commissionep ) || !in_array( $commissionep['Commissionep']['etatcommissionep'], array( 'associe', 'valide', 'quorum', 'presence' ) ) ) {
				return false;
			}

			$success = true;
			$nbMembreseps = $this->Commissionep->CommissionepMembreep->find(
				'count',
				array(
					'conditions' => array(
						'CommissionepMembreep.commissionep_id' => $commissionep_id,
						'CommissionepMembreep.presence' => array( 'present', 'remplacepar' ),
					)
				)
			);

			$this->Commissionep->id = $commissionep_id;
			if( !empty( $nbMembreseps ) && in_array( $commissionep['Commissionep']['etatcommissionep'], array( 'associe', 'valide', 'quorum' ) ) ) {
				$this->Commissionep->set( 'etatcommissionep', 'presence' );
				$success = $this->Commissionep->save( null, array( 'atomic' => false ) ) && $success;
			}
			else if(  empty( $nbMembreseps ) && $commissionep['Commissionep']['etatcommissionep'] == 'presence' ) {
				if( Configure::read( 'Cg.departement' ) == 93 ) {
					$this->Commissionep->set( 'etatcommissionep', 'valide' );
				}
				else {
					$this->Commissionep->set( 'etatcommissionep', 'associe' );
				}
				$success = $this->Commissionep->save( null, array( 'atomic' => false ) ) && $success;
			}

			if ( Configure::read( 'Cg.departement' ) == 66 ) {
				$listeMembrePresentRemplace = array();
				foreach( $commissionep['CommissionepMembreep'] as $membre ) {
					if ( $membre['presence'] == 'present' || $membre['presence'] == 'remplacepar' ) {
						$listeMembrePresentRemplace[] = $membre['membreep_id'];
					}
				}

				$compositionValide = $this->Commissionep->Ep->Regroupementep->Compositionregroupementep->compositionValide( $commissionep['Ep']['regroupementep_id'], $listeMembrePresentRemplace );
				if( !$compositionValide['check'] ) {
					$this->Commissionep->set( 'etatcommissionep', 'quorum' );
					$success = $this->Commissionep->save( null, array( 'atomic' => false ) ) && $success;
				}
			}

			return $success;
		}

		/**
		 *
		 */
		public function checkEtat( $commissionep_id ) {
			$commissionep = $this->Commissionep->find(
				'first',
				array(
					'conditions' => array(
						'Commissionep.id' => $commissionep_id
					),
					'contain' => false
				)
			);

			return $commissionep['Commissionep']['etatcommissionep'];
		}

		/**
		 *
		 * @param type $commissionep_id
		 * @param type $participant_id
		 * @param type $user_id
		 * @return type
		 */
		public function getPdfPv( $commissionep_id, $participant_id, $user_id ) {
			$commissionep_data = $this->Commissionep->find(
				'first',
				array(
					'conditions' => array(
						'Commissionep.id' => $commissionep_id
					),
					'contain' => array(
						'Ep' => array(
							'Regroupementep'
						)
					)
				)
			);

			if( !is_null( $participant_id ) ) {
				$participant = $this->Commissionep->CommissionepMembreep->Membreep->find(
					'first',
					array(
						'conditions' => array(
							'Membreep.id' => $participant_id
						),
						'contain' => false
					)
				);

				if( !empty( $participant ) ) {
					$participant['Participant'] = $participant['Membreep'];
					unset($participant['Membreep']);
					$commissionep_data = Set::merge( $participant, $commissionep_data );
				}
			}


			$queryData = array(
				'fields' => array_merge(
					$this->Commissionep->Passagecommissionep->fields(),
					$this->Commissionep->Passagecommissionep->Dossierep->fields(),
					$this->Commissionep->Passagecommissionep->Dossierep->Personne->fields(),
					$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Dossier->fields(),
					$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Adressefoyer->Adresse->fields()
				),
				'joins' => array(
					$this->Commissionep->Passagecommissionep->Dossierep->join( 'Passagecommissionep', array( 'type' => 'INNER' ) ),
					$this->Commissionep->Passagecommissionep->Dossierep->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Commissionep->Passagecommissionep->Dossierep->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) )
				),
				'conditions' => array(
					'Passagecommissionep.dossierep_id = Dossierep.id',
					'Passagecommissionep.commissionep_id' => $commissionep_id,
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.id IN ( '.$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
						)
					)
				),
				'order' => array( 'Personne.nom ASC' )
			);

			$options = array( 'Personne' => array( 'qual' => ClassRegistry::init( 'Option' )->qual() ) );
			foreach( $this->themesTraites( $commissionep_id ) as $theme => $decision ) {
				$model = Inflector::classify( $theme );
				$options = Set::merge( $options, $this->Commissionep->Passagecommissionep->Dossierep->{$model}->enums() );

				$modeleDecision = Inflector::classify( "decision{$theme}" );
				$options = Set::merge( $options, $this->Commissionep->Passagecommissionep->{$modeleDecision}->enums() );

				foreach( array( 'fields', 'joins' ) as $key ) {
					$qdModele = $this->Commissionep->Passagecommissionep->Dossierep->{$model}->qdProcesVerbal();
					$queryData[$key] = array_merge( $queryData[$key], $qdModele[$key] );
				}
			}
			$options = Set::merge( $options, $this->Commissionep->enums() );
			$options = Set::merge( $options, $this->Commissionep->Passagecommissionep->Dossierep->enums() );
			$options = Set::merge( $options, $this->Commissionep->Membreep->enums() );
			$options = Set::merge( $options, $this->Commissionep->CommissionepMembreep->enums() );

			$dossierseps = $this->Commissionep->Passagecommissionep->Dossierep->find( 'all', $queryData );
			// FIXME: faire la traduction des enums dans les modèles correspondants ?

			// present, excuse, FIXME: remplace_par
			$presencesTmp = $this->Commissionep->CommissionepMembreep->find(
				'all',
				array(
					'conditions' => array(
						'CommissionepMembreep.commissionep_id' => $commissionep_id
					),
					'contain' => array(
						'Membreep' => array(
							'Fonctionmembreep'
						),
						'Remplacanteffectifmembreep',
						'Remplacantmembreep'
					)
				)
			);

			// FIXME: presence -> obliger de prendre les présences avant d'imprimer le PV
			$presences = array();
			foreach( $presencesTmp as $presence ) {
				// Y-a-t'il eu un remplaçant effectif ?
				if( ( $presence['CommissionepMembreep']['presence'] == 'remplacepar' ) && !empty( $presence['CommissionepMembreep']['presencesuppleant_id'] ) ) {
					$presence['CommissionepMembreep']['presence'] = 'present';
					$presence['Membreep'] = Set::merge( $presence['Membreep'], $presence['Remplacanteffectifmembreep'] );
				}
				// C'est bizzarre, mais si c'était nécessaire avant, c'est juste plus propre
				else if( $presence['CommissionepMembreep']['presence'] == 'excuse' ) {
					if( isset( $presence['Remplacantmembreep']['id'] ) && !empty( $presence['Remplacantmembreep']['id'] ) ) {
						$presence['Membreep'] = Set::merge( $presence['Membreep'], $presence['Remplacantmembreep'] );
					}
				}

				$presences["Presences_{$presence['CommissionepMembreep']['presence']}"][] = array( 'Membreep' => $presence['Membreep'] );
			}

			foreach( $options['CommissionepMembreep']['presence'] as $typepresence => $libelle ) {
				if( !isset( $presences["Presences_{$typepresence}"] ) ) {
					$presences["Presences_{$typepresence}"] = array();
				}
				$commissionep_data["presences_{$typepresence}_count"] = count( $presences["Presences_{$typepresence}"] );
			}

			// Nb de dossiers d'EP par thématique
			$themes = array();
			foreach( $dossierseps as $key => $theme ) {
				$themes["Themes_{$theme['Dossierep']['themeep']}"][] = array( 'Dossierep' => $theme['Dossierep'] );
			}
			foreach( $options['Dossierep']['themeep'] as $theme => $libelleTheme ) {
				if( !isset( $themes["Themes_{$theme}"] ) ) {
					$themes["Themes_{$theme}"] = array();
				}
				$commissionep_data["nbdossiers_{$theme}_count"] = count( $themes["Themes_{$theme}"] );
			}

			$user = $this->Commissionep->Passagecommissionep->User->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => $user_id
					),
					'contain' => array(
						'Serviceinstructeur'
					)
				)
			);
			$commissionep_data = Set::merge( $commissionep_data, $user );

			$typeEp = $commissionep_data['Ep']['Regroupementep'];
			if( Configure::read( 'Cg.departement' ) != 66 ) {
				$pv = "pv.odt";
			}
			else {
				if( $typeEp['saisinebilanparcoursep66'] != 'nontraite' ) {
					$pv = "pv_parcours.odt";
				}
				else {
					$pv = "pv_audition.odt";
				}
			}

			return $this->Commissionep->ged(
				array_merge(
					array(
						$commissionep_data,
						'Decisionseps' => $dossierseps
					),
					$presences
				),
				"{$this->Commissionep->alias}/{$pv}",
				true,
				$options
			);
		}

		/**
		 *
		 * Ctte méthode est utilisée par getFicheSynthese() et par getPdfOrdredujour()
		 *
		 * @param array|string $conditions
		 * @param boolean $fiche Si on veut le querydata pour la fiche synthétique
		 * @return array
		 */
		protected function _qdFichesSynthetiques( $conditions, $fiche = false ) {
			$cacheKey = Inflector::underscore( $this->Commissionep->useDbConfig ).'_'.Inflector::underscore( $this->Commissionep->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( compact( 'fiche' ) ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				// Permet d'obtenir une et une seule entrée de la table informationspe
				$sqDerniereInformationpe = ClassRegistry::init( 'Informationpe' )->sqDerniere( 'Personne' );

				$query = array(
					'fields' => array(
						'Dossierep.themeep',
						'Foyer.sitfam',
						'(
							SELECT
									dossiers.dtdemrsa
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
											nir_correct13( "Personne"."nir" )
											AND nir_correct13( personnes.nir )
											AND SUBSTRING( TRIM( BOTH \' \' FROM personnes.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM "Personne"."nir" ) FROM 1 FOR 13 )
											AND personnes.dtnai = "Personne"."dtnai"
										)
										OR
										(
											UPPER(personnes.nom) = UPPER("Personne"."nom")
											AND UPPER(personnes.prenom) = UPPER("Personne"."prenom")
											AND personnes.dtnai = "Personne"."dtnai"
										)
									)
								ORDER BY dossiers.dtdemrsa ASC
								LIMIT 1
						) AS "Dossier__dtdemrsa"',
						'( CASE WHEN "Serviceinstructeur"."lib_service" IS NULL THEN \'Hors département\' ELSE "Serviceinstructeur"."lib_service" END ) AS "Serviceinstructeur__lib_service"',
						'',
						'Orientstruct.date_valid',
						'( CASE WHEN "Historiqueetatpe"."etat" IN ( NULL, \'cessation\' ) THEN \'Non\' ELSE \'Oui\' END ) AS "Historiqueetatpe__inscritpe"',
						'Adresse.nomcom',
					),
					'joins' => array(
						$this->Commissionep->Passagecommissionep->Dossierep->join( 'Passagecommissionep', array( 'type' => 'INNER' ) ),
						$this->Commissionep->Passagecommissionep->Dossierep->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Dossier->join(
							'Suiviinstruction',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Suiviinstruction.id IN (
										'.$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Dossier->Suiviinstruction->sqDernier2().'
									)',
								)
							)
						),
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Dossier->Suiviinstruction->join( 'Serviceinstructeur', array( 'type' => 'LEFT OUTER' ) ),
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->join(
							'Orientstruct',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Orientstruct.id IN (
										'.$this->Commissionep->Passagecommissionep->Dossierep->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere().'
									)'
								)
							)
						),
						ClassRegistry::init( 'Informationpe' )->joinPersonneInformationpe(),
						array(
							'table'      => 'historiqueetatspe',
							'alias'      => 'Historiqueetatpe',
							'type'       => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array(
								'Historiqueetatpe.informationpe_id = Informationpe.id',
								'Historiqueetatpe.id IN (
											SELECT h.id
												FROM historiqueetatspe AS h
												WHERE h.informationpe_id = Informationpe.id
												ORDER BY h.date DESC
												LIMIT 1
								)'
							)
						),
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->join(
							'Adressefoyer',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Adressefoyer.id IN (
										'.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01('Adressefoyer.foyer_id').'
									)'
								)
							)
						),
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					),
					'conditions' => array(
						array(
							'OR' => array(
								"Informationpe.id IS NULL",
								"Informationpe.id IN ( {$sqDerniereInformationpe} )"
							)
						)
					),
					'order' => array(
						'Dossierep.themeep DESC',
						'Adresse.nomcom ASC'
					)
				);

				/*
				FIXME: utilisé dans getPdfOrdredujour mais également dans le fonction getFicheSynthese -> vérifier si ça ne casse pas
				OK - N° dossier: dossier_numdemrsa (pas encore présent)
				OK - Situation familiale: foyer_sitfam et foyer_nbenfants (pas encore présent)
				OK - Structure référente: structurereferente_lib_struc (pas encore présent) -> celle de l'orientation
				OK - Référent unique: referent_nom_complet (pas encore présent) -> personnes_referents
				OK - Si demande de maintien dans le social (Nonorientationproep58), proposition: si on parle de la structure, du référent et du contrat actuel, alors on peut l'ajouter (pas encore présent)oui c'est bien ça
				OK - Si réorientation, proposition: si on parle de la structure et du référent de la proposition d'orientation, alors on peut l'ajouter (pas encore présent)oui c'est ça
				 */
				if( Configure::read( 'Cg.departement' ) == 58 ) {
					// Nombre d'enfants -> TODO: factoriser avec ce qui se trouve au 93
					$vfNbEnfants = $this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->vfNbEnfants();
					$vfNbEnfants = "( {$vfNbEnfants} ) AS \"Foyer__nbenfants\"";
					$query['fields'][] = $vfNbEnfants;

					$query['fields'][] = 'Dossier.numdemrsa';

					// Structure référente
					$query['fields'] = array_merge(
						$query['fields'],
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->Orientstruct->Structurereferente->fields()
					);
					$query['joins'][] = $this->Commissionep->Passagecommissionep->Dossierep->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) );

					// Référent unique
					$query['fields'] = array_merge(
						$query['fields'],
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->PersonneReferent->Referent->fields()
					);
					$query['fields'][] = $this->Commissionep->Passagecommissionep->Dossierep->Personne->PersonneReferent->Referent->sqVirtualField( 'nom_complet' );
					$query['joins'][] = $this->Commissionep->Passagecommissionep->Dossierep->Personne->join( 'PersonneReferent', array( 'type' => 'LEFT OUTER' ) );
					$query['joins'][] = $this->Commissionep->Passagecommissionep->Dossierep->Personne->PersonneReferent->join( 'Referent', array( 'type' => 'LEFT OUTER' ) );

					// Si demande de maintien dans le social (Nonorientationproep58), proposition: si on parle de la structure, du référent et du contrat actuel, alors on peut l'ajouter (pas encore présent)oui c'est bien ça
					$queryNonorientationproep58 = array(
						'fields' => array_merge(
							$this->Commissionep->Passagecommissionep->Dossierep->Personne->Contratinsertion->fields(),
							$this->Commissionep->Passagecommissionep->Dossierep->Personne->Contratinsertion->Structurereferente->fields(),
							$this->Commissionep->Passagecommissionep->Dossierep->Personne->Contratinsertion->Referent->fields()
						),
						'joins' => array(
							array(
								'table'      => 'contratsinsertion',
								'alias'      => 'Contratinsertion',
								'type'       => 'LEFT OUTER',
								'foreignKey' => false,
								'conditions' => array(
									'Contratinsertion.personne_id = Orientstruct.personne_id',
	//								'Contratinsertion.structurereferente_id = Orientstruct.structurereferente_id'
								)
							),
							$this->Commissionep->Passagecommissionep->Dossierep->Personne->Contratinsertion->join( 'Structurereferente', array( 'type' =>'LEFT OUTER' ) ),
							$this->Commissionep->Passagecommissionep->Dossierep->Personne->Contratinsertion->join( 'Referent', array( 'type' =>'LEFT OUTER' ) ),
						),
						'conditions' => array(
							'OR' => array(
								'Contratinsertion.id IS NULL',
								'Contratinsertion.id IN ( '.$this->Commissionep->Passagecommissionep->Dossierep->Personne->Contratinsertion->WebrsaContratinsertion->sqDernierContrat().' )'
							)
						)
					);
					$queryNonorientationproep58 = array_words_replace(
						$queryNonorientationproep58,
						array(
							'Structurereferente' => 'Structurereferentecer',
							'Referent' => 'Referentcer',
						)
					);
					$query['fields'] = array_merge( $query['fields'], $queryNonorientationproep58['fields'] );
					$query['joins'] = array_merge( $query['joins'], $queryNonorientationproep58['joins'] );
					$query['conditions'] = array_merge( $query['conditions'], $queryNonorientationproep58['conditions'] );

					// Regressionorientationep58
					$queryRegressionorientationep58 = array(
						'fields' => array_merge(
							$this->Commissionep->Passagecommissionep->Dossierep->Regressionorientationep58->fields(),
							$this->Commissionep->Passagecommissionep->Dossierep->Regressionorientationep58->Typeorient->fields(),
							$this->Commissionep->Passagecommissionep->Dossierep->Regressionorientationep58->Structurereferente->fields(),
							$this->Commissionep->Passagecommissionep->Dossierep->Regressionorientationep58->Referent->fields()
						),
						'joins' => array(
							$this->Commissionep->Passagecommissionep->Dossierep->join( 'Regressionorientationep58', array( 'type' => 'LEFT OUTER' ) ),
							$this->Commissionep->Passagecommissionep->Dossierep->Regressionorientationep58->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
							$this->Commissionep->Passagecommissionep->Dossierep->Regressionorientationep58->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
							$this->Commissionep->Passagecommissionep->Dossierep->Regressionorientationep58->join( 'Referent', array( 'type' => 'LEFT OUTER' ) )
						)
					);
					$queryRegressionorientationep58 = array_words_replace(
						$queryRegressionorientationep58,
						array(
							'Typeorient' => 'Typeorientpropo',
							'Structurereferente' => 'Structurereferentepropo',
							'Referent' => 'Referentpropo',
						)
					);
					$query['fields'] = array_merge( $query['fields'], $queryRegressionorientationep58['fields'] );
					$query['joins'] = array_merge( $query['joins'], $queryRegressionorientationep58['joins'] );
				}
				else if( Configure::read( 'Cg.departement' ) == 93 && $fiche ) {
					// Date de demande RSA actuelle
					$query['fields'][] = "\"Dossier\".\"dtdemrsa\" AS \"Dossier__dtdemrsaactuelle\"";

					// Nombre d'enfants -> TODO: factoriser avec ce qui se trouve au 58
					$sqNbenfants = $this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->vfNbEnfants();
					$query['fields'][] = "( {$sqNbenfants} ) AS \"Foyer__nbenfants\"";

					// Age
					$query['fields'][] = $this->Commissionep->Passagecommissionep->Dossierep->Personne->sqVirtualField( 'age' );

					// Affiliation CAF
					$query['fields'][] = 'Dossiercaf.ddratdos';
					$query['joins'][] = $this->Commissionep->Passagecommissionep->Dossierep->Personne->join( 'Dossiercaf', array( 'type' => 'LEFT OUTER' ) );

					// Détails de la commission
					$query['fields'] = array_merge(
						$query['fields'],
						array(
							'Commissionep.dateseance',
							'Commissionep.salle',
							'Commissionep.lieuseance',
							'Commissionep.adresseseance',
							'Commissionep.codepostalseance',
							'Commissionep.villeseance'
						)
					);
					$query['joins'][] = $this->Commissionep->Passagecommissionep->join( 'Commissionep', array( 'type' => 'INNER' ) );

					// Thématique, type de suspension
					$query['fields'][] = 'Nonrespectsanctionep93.origine';
					$query['fields'][] = 'Nonrespectsanctionep93.rgpassage';
					$query['joins'][] = $this->Commissionep->Passagecommissionep->Dossierep->join( 'Nonrespectsanctionep93', array( 'type' => 'LEFT OUTER' ) );

					// Première et seconde relance
					$relances = array( 1 => 'Relance1', 2 => 'Relance2' );
					foreach( $relances as $numrelance => $alias ) {
						$query['fields'][] = "{$alias}.dateimpression";

						$query['joins'][] = array_words_replace(
							$this->Commissionep->Passagecommissionep->Dossierep->Nonrespectsanctionep93->join(
								'Relancenonrespectsanctionep93',
								array(
									'type' => 'LEFT OUTER',
									'conditions' => array(
										'Relancenonrespectsanctionep93.numrelance' => $numrelance
									)
								)
							),
							array( 'Relancenonrespectsanctionep93' => $alias )
						);
					}

					// -----------------------------------------------------------------------------
					// Nonrespectsanctionep93
					//	-> Nonrespect1, Passage1, Commission1, Decision1ep
					// -----------------------------------------------------------------------------
					foreach( array( 1, 2, 3 ) as $passage ) {
						$replacements = array(
							'Nonrespectsanctionep93' => "Nonrespect{$passage}",
							'Dossierep' => "Dossier{$passage}",
							'Commissionep' => "Commission{$passage}",
							'Passagecommissionep' => "Passage{$passage}"
						);

						$query['joins'][] = array(
							'table' => 'nonrespectssanctionseps93',
							'alias' => "Nonrespect{$passage}",
							'type' => 'LEFT OUTER',
							'conditions' => array(
								"Nonrespect{$passage}.rgpassage" => $passage,
								'OR' => array(
									array(
										"Nonrespectsanctionep93.orientstruct_id IS NOT NULL",
										"Nonrespect{$passage}.orientstruct_id = Nonrespectsanctionep93.orientstruct_id"
									),
									array(
										"Nonrespectsanctionep93.contratinsertion_id IS NOT NULL",
										"Nonrespect{$passage}.contratinsertion_id = Nonrespectsanctionep93.contratinsertion_id"
									),
									array(
										"Nonrespectsanctionep93.propopdo_id IS NOT NULL",
										"Nonrespect{$passage}.propopdo_id = Nonrespectsanctionep93.propopdo_id"
									),
									array(
										"Nonrespectsanctionep93.historiqueetatpe_id IS NOT NULL",
										"Nonrespect{$passage}.historiqueetatpe_id = Nonrespectsanctionep93.historiqueetatpe_id"
									)
								)
							)
						);

						$query['joins'][] = array_words_replace(
							$this->Commissionep->Passagecommissionep->Dossierep->Nonrespectsanctionep93->join( 'Dossierep', array( 'type' => 'LEFT OUTER' ) ),
							$replacements
						);

						$query['fields'][] = "{$replacements['Passagecommissionep']}.impressionconvocation";
						$query['joins'][] = array_words_replace(
							$this->Commissionep->Passagecommissionep->Dossierep->Nonrespectsanctionep93->Dossierep->join(
								'Passagecommissionep',
								array(
									'type' => 'LEFT OUTER',
									'conditions' => array(
										'Passagecommissionep.id IN ( '.$this->Commissionep->Passagecommissionep->sqDernier().' )'
									)
								)
							),
							$replacements
						);

						$query['joins'][] = array_words_replace(
							$this->Commissionep->Passagecommissionep->join( 'Commissionep', array( 'type' => 'LEFT OUTER' ) ),
							$replacements
						);

						// FIXME: les autres thématiques ?
						foreach( array( 'ep', 'cg' ) as $etape ) {
							$alias = "Decision{$passage}{$etape}";
							$replacements['Decisionnonrespectsanctionep93'] = $alias;

							$query['fields'][] = "{$alias}.decision";
							$query['fields'][] = "{$alias}.commentaire";

							$query['joins'][] = array_words_replace(
								$this->Commissionep->Passagecommissionep->join(
									'Decisionnonrespectsanctionep93',
									array(
										'type' => 'LEFT OUTER',
										'conditions' => array(
											'Decisionnonrespectsanctionep93.etape' => $etape
										)
									)
								),
								$replacements
							);
						}
					}

					// Service référent
					$query['fields'][] = 'Structurereferente.lib_struc';
					$query['joins'][] = $this->Commissionep->Passagecommissionep->Dossierep->Personne->Orientstruct->join( 'Structurereferente' );

					// Dernière Dsp / DspRev
					$query['fields'] = array_merge(
						$query['fields'],
						array(
							'( CASE WHEN "DspRev"."id" IS NOT NULL THEN "DspRev"."natlog" ELSE "Dsp"."natlog" END ) AS "Dsp__natlog"',
							'( CASE WHEN "DspRev"."id" IS NOT NULL THEN "DspRev"."nivetu" ELSE "Dsp"."nivetu" END ) AS "Dsp__nivetu"'
						)
					);
					$sqDerniereDsp = $this->Commissionep->Passagecommissionep->Dossierep->Personne->Dsp->WebrsaDsp->sqDerniereDsp();
					$query['joins'][] = $this->Commissionep->Passagecommissionep->Dossierep->Personne->join(
						'Dsp',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								"Dsp.id IN ( {$sqDerniereDsp} )"
							)
						)
					);
					$sqDerniereDsp = $this->Commissionep->Passagecommissionep->Dossierep->Personne->DspRev->sqDerniere();
					$query['joins'][] = $this->Commissionep->Passagecommissionep->Dossierep->Personne->join(
						'DspRev',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								"DspRev.id IN ( {$sqDerniereDsp} )"
							)
						)
					);

					// Inscription RSA -> FIXME: voir au-dessus pour le premier dossier
					$query['fields'][] = 'Dossier.dtdemrsa';


					// Radiation Pôle Emploi ?
					$query['fields'] = array_merge(
						$query['fields'],
						array(
							'Radiationpe.date',
							'Radiationpe.etat',
							'Radiationpe.code',
							'Radiationpe.motif'
						)
					);
					$query['joins'][] = array_words_replace(
						$this->Commissionep->Passagecommissionep->Dossierep->Nonrespectsanctionep93->join( 'Historiqueetatpe', array( 'type' => 'LEFT OUTER' ) ),
						array( 'Historiqueetatpe' => 'Radiationpe' )
					);
				}

				Cache::write( $cacheKey, $query );
			}

			$query['conditions'][] = $conditions;

			return $query;
		}

		/**
		 *   Impression de convocation pour un participant à une commission d'EP
		 */

		public function getPdfConvocationParticipant( $commissionep_id, $membreep_id, $user_id ) {
			$commissionep = $this->Commissionep->find(
				'first',
				array(
					'conditions' => array(
						'Commissionep.id' => $commissionep_id
					),
					'contain' => array(
						'Ep' => array(
							'Regroupementep'
						)
					)
				)
			);
            // FIXME: voir comment resortir la notion de prioritaire/facultatif

			$membreep = $this->Commissionep->Membreep->find(
				'first',
				array(
                    'fields' => array_merge(
                        $this->Commissionep->Membreep->fields(),
                        $this->Commissionep->Membreep->Fonctionmembreep->fields(),
                        $this->Commissionep->Membreep->Fonctionmembreep->Compositionregroupementep->fields()
                    ),
					'conditions' => array(
						'Membreep.id' => $membreep_id,
                        'OR' => array(
                            'Compositionregroupementep.regroupementep_id' => $commissionep['Ep']['Regroupementep']['id'],
                            'Compositionregroupementep.regroupementep_id IS NULL'
                        )
					),
                    'joins' => array(
                        $this->Commissionep->Membreep->join('Fonctionmembreep', array( 'type' => 'INNER' ) ),
                        $this->Commissionep->Membreep->Fonctionmembreep->join( 'Compositionregroupementep', array( 'type' => 'LEFT OUTER' ) ),
                    ),
                    'contain' => false
				)
			);

			$convocation = Set::merge( $commissionep, $membreep );

			$user = $this->Commissionep->Passagecommissionep->User->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => $user_id
					),
					'contain' => array(
						'Serviceinstructeur'
					)
				)
			);
			$convocation = Set::merge( $convocation, $user );


			$options = $this->Commissionep->Membreep->enums();

            $modele = null;
            if( Configure::read( 'Cg.departement' ) == 66 ) {
            	if( $commissionep['Ep']['Regroupementep']['saisinebilanparcoursep66'] == 'nontraite' ){
					if( $convocation['Compositionregroupementep']['prioritaire'] == '1' ) {
						$modele = 'convocationep_participant_prioritaire.odt';
					}
					else {
						$modele = 'convocationep_participant_facultatif.odt';
					}
				}
				else {
					$modele = 'convocationep_participant.odt';
				}
            }
            else {
                $modele = 'convocationep_participant.odt';
            }

			return $this->Commissionep->ged(
				$convocation,
				"{$this->Commissionep->alias}/{$modele}",
				false,
				$options
			);

		}

		/**
		 *   Impression de l'ordre du jour pour un participant à une commission d'EP
		 */

		public function getPdfOrdredujour( $commissionep_membreep_id, $user_id ) {
			// Participant auquel la convocation doit être envoyée
			$convocation = $this->Commissionep->CommissionepMembreep->find(
				'first',
				array(
					'conditions' => array(
						'CommissionepMembreep.id' => $commissionep_membreep_id
					),
					'contain' => array(
						'Commissionep' => array(
							'Ep' => array(
								'Regroupementep'
							)
						),
						'Membreep' => array(
							'Fonctionmembreep'
						)
					)
				)
			);

			// Si le membre est remplacé par un autre, il faut aller cherche le remplaçant
			if( $convocation['CommissionepMembreep']['reponse'] == 'remplacepar' ) {
				$membreep = $this->Commissionep->Membreep->find(
					'first',
					array(
						'conditions' => array(
							'Membreep.id' => $convocation['CommissionepMembreep']['reponsesuppleant_id']
						),
						'contain' => false
					)
				);
				$convocation = Set::merge( $convocation, $membreep );
			}

			$convocation = array( 'Participant' => $convocation['Membreep'], 'Commissionep' => $convocation['Commissionep'] );

			$queryData = $this->queryPdfOrdredujour($convocation);

			$options = array( 'Personne' => array( 'qual' => ClassRegistry::init( 'Option' )->qual() ) );
			$options = Set::merge( $options, $this->Commissionep->enums() );
			$options = Set::merge( $options, $this->Commissionep->Passagecommissionep->Dossierep->enums() );
			$options = Set::merge( $options, $this->Commissionep->Membreep->enums() );
			$options['Participant'] = $options['Membreep'];
			$options = Set::merge( $options, $this->Commissionep->CommissionepMembreep->enums() );
			$options = Set::merge( $options, $this->Commissionep->Passagecommissionep->enums() );
			$options = Set::merge( $options, $this->Commissionep->Passagecommissionep->Dossierep->Defautinsertionep66->enums() );
			$options['Remplacantmembreep'] = $options['Membreep'];

			$dossierseps = $this->Commissionep->Passagecommissionep->Dossierep->find( 'all', $queryData );
			// FIXME: faire la traduction des enums dans les modèles correspondants ?

			// FIXME: documentation
			$themesTraites = $this->themesTraites( $convocation['Commissionep']['id'] );
			$themesTraites = array_keys( $themesTraites );
			sort( $themesTraites );

			if ( Configure::read( 'Cg.departement' ) == 93 || Configure::read( 'Cg.departement' ) == 58 ) {
				$dossiersParCommune = array();
				foreach( $dossierseps as $dossierep ) {
					$commune = $dossierep['Adresse']['nomcom'];
					if( !isset( $dossiersParCommune[$commune] ) ) {
						$dossiersParCommune[$commune] = array();
					}
					$dossiersParCommune[$commune][$dossierep['Dossierep']['themeep']] = $dossierep[0]['nombre'];
				}

				$dossierseps = array();
				$default = array();
				foreach( $themesTraites as $themeTraite ) {
					$default[Inflector::pluralize($themeTraite)] = 0;
				}

				foreach( $dossiersParCommune as $commune => $dossierParCommune ) {
					$dossierParCommune = array_merge( array( 'commune' => $commune ), $default, $dossierParCommune );
					$dossierParCommune['total'] = array_sum( $dossierParCommune );
					$dossierseps[] = $dossierParCommune;
				}
			}

			// present, excuse, FIXME: remplace_par
			$reponsesTmp = $this->Commissionep->CommissionepMembreep->find(
				'all',
				array(
					'conditions' => array(
						'CommissionepMembreep.commissionep_id' => $convocation['Commissionep']['id']
					),
					'contain' => array(
						'Membreep' => array(
							'Fonctionmembreep'
						),
						'Remplacantmembreep'
					)
				)
			);

			$reponses = array();
			foreach( $reponsesTmp as $reponse ) {
				$reponses["Reponses_{$reponse['CommissionepMembreep']['reponse']}"][] = array( 'Membreep' => $reponse['Membreep'], 'Remplacantmembreep' => $reponse['Remplacantmembreep'] );
			}
			foreach( $options['CommissionepMembreep']['reponse'] as $typereponse => $libelle ) {
				if( !isset( $reponses["Reponses_{$typereponse}"] ) ) {
					$reponses["Reponses_{$typereponse}"] = array();
				}
				$commissionep_data["reponses_{$typereponse}_count"] = count( $reponses["Reponses_{$typereponse}"] );
			}

			// Fiches synthétiques des dossiers d'EP
			$fichessynthetiques = $this->Commissionep->Passagecommissionep->Dossierep->find(
				'all',
				$this->_qdFichesSynthetiques( array( 'Passagecommissionep.commissionep_id' => $convocation['Commissionep']['id'] ) )
			);

			$options['Foyer']['sitfam'] = ClassRegistry::init( 'Option' )->sitfam();


			$user = $this->Commissionep->Passagecommissionep->User->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => $user_id
					),
					'contain' => array(
						'Serviceinstructeur'
					)
				)
			);

			$convocation = Set::merge( $convocation, $user );


			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$options['Referentpropo']['qual'] = $options['Referentcer']['qual'] = $options['Referent']['qual'] = $options['Personne']['qual'];
				$options['Type']['voie'] = $options['type']['voie'] = $options['Participant']['typevoie'];
			}

			$typeEp = $convocation['Commissionep']['Ep']['Regroupementep'];
			if( Configure::read( 'Cg.departement' ) != 66 ) {
				$ordredujourodt = "ordredujour_participant_".Configure::read( 'Cg.departement' );
			}
			else {
				if( $typeEp['saisinebilanparcoursep66'] != 'nontraite' ) {
					$ordredujourodt = "ordredujour_participant_parcours";
				}
				else {
					$ordredujourodt = "ordredujour_participant_audition";
				}

			}

			return $this->Commissionep->ged(
				array_merge(
					array(
						$convocation,
						'Dossierseps' => $dossierseps,
						'Fichessynthetiques' => $fichessynthetiques
					),
					$reponses
				),
				"{$this->Commissionep->alias}/{$ordredujourodt}.odt",
				true,
				$options
			);
		}

		public function queryPdfOrdredujour ( $convocation ) {
			if ( Configure::read( 'Cg.departement' ) == 93 || Configure::read( 'Cg.departement' ) == 58 ) {
				$queryData = array(
					'fields' => array(
						'Dossierep.themeep',
						'Adresse.nomcom',
						'COUNT("Dossierep"."id") AS "nombre"',
					),
					'joins' => array(
						array(
							'table'      => 'passagescommissionseps',
							'alias'      => 'Passagecommissionep',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( "Dossierep.id = Passagecommissionep.dossierep_id" ),
						),
						array(
							'table'      => 'personnes',
							'alias'      => 'Personne',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( "Dossierep.personne_id = Personne.id" ),
						),
						array(
							'table'      => 'foyers',
							'alias'      => 'Foyer',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Personne.foyer_id = Foyer.id' )
						),
						array(
							'table'      => 'dossiers',
							'alias'      => 'Dossier',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Dossier.id = Foyer.dossier_id' )
						),
						array(
							'table'      => 'adressesfoyers',
							'alias'      => 'Adressefoyer',
							'type'       => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array(
								'Foyer.id = Adressefoyer.foyer_id',
								// FIXME: c'est un hack pour n'avoir qu'une seule adresse de rang 01 par foyer!
								'Adressefoyer.id IN (
									'.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01('Adressefoyer.foyer_id').'
								)'
							)
						),
						array(
							'table'      => 'adresses',
							'alias'      => 'Adresse',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Adresse.id = Adressefoyer.adresse_id' )
						)
					),
					'conditions' => array(
						'Passagecommissionep.commissionep_id' => $convocation['Commissionep']['id']
					),
					'group' => array(
						'Dossierep.themeep',
						'Adresse.nomcom'
					),
				);
			}
			else {
				// Jointure spéciale sur Dossierep suivant la thématique
				$joinSaisinebilanparcoursep66 = $this->Commissionep->Passagecommissionep->Dossierep->Saisinebilanparcoursep66->join( 'Bilanparcours66', array( 'type' => 'LEFT OUTER' ) );
				$joinDefautinsertionep66 = $this->Commissionep->Passagecommissionep->Dossierep->Defautinsertionep66->join( 'Bilanparcours66', array( 'type' => 'LEFT OUTER' ) );

				$joinBilanparcours66 = $joinSaisinebilanparcoursep66;
				$joinBilanparcours66['conditions'] = array(
					'OR' => array(
						$joinSaisinebilanparcoursep66['conditions'],
						$joinDefautinsertionep66['conditions']
					)
				);

				$queryData = array(
					'fields' => array_merge(
						$this->Commissionep->Passagecommissionep->fields(),
						$this->Commissionep->Passagecommissionep->Dossierep->fields(),
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->fields(),
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Dossier->fields(),
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Adressefoyer->Adresse->fields(),
						$this->Commissionep->Passagecommissionep->Dossierep->Saisinebilanparcoursep66->fields(),
						$this->Commissionep->Passagecommissionep->Dossierep->Defautinsertionep66->fields(),
						$this->Commissionep->Passagecommissionep->Dossierep->Defautinsertionep66->Bilanparcours66->fields(),
						$this->Commissionep->Passagecommissionep->Dossierep->Defautinsertionep66->Bilanparcours66->Referent->fields(),
						$this->Commissionep->Passagecommissionep->Dossierep->Defautinsertionep66->Bilanparcours66->Structurereferente->fields()
					),
					'joins' => array(
						$this->Commissionep->Passagecommissionep->Dossierep->join( 'Defautinsertionep66', array( 'type' => 'LEFT OUTER' ) ),
						$this->Commissionep->Passagecommissionep->Dossierep->join( 'Saisinebilanparcoursep66', array( 'type' => 'LEFT OUTER' ) ),
						$joinBilanparcours66,
						$this->Commissionep->Passagecommissionep->Dossierep->Saisinebilanparcoursep66->Bilanparcours66->join( 'Referent', array( 'type' => 'INNER' ) ),
						$this->Commissionep->Passagecommissionep->Dossierep->Saisinebilanparcoursep66->Bilanparcours66->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$this->Commissionep->Passagecommissionep->Dossierep->join( 'Passagecommissionep', array( 'type' => 'INNER' ) ),
						$this->Commissionep->Passagecommissionep->Dossierep->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) )
					),
					'conditions' => array(
						'Passagecommissionep.commissionep_id' => $convocation['Commissionep']['id'],
						array(
							'OR' => array(
								'Adressefoyer.id IS NULL',
								'Adressefoyer.id IN ( '.$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
							)
						)
					),
				);

				$order = Configure::read( 'Commissionseps.printOrdresDuJour.order' );
				$queryData['order'] = $order ? $order : array( 'Personne.nom', 'Personne.prenom' );
			}

			return $queryData;
		}

		/**
		 * Impression de la fiche synthétique d'un allocataire pour un passage en commission d'EP
		 */

		public function getFicheSynthese( $commissionep_id, $dossierep_id, $anonymiser = false ) {
			$Dossierep = ClassRegistry::init( 'Dossierep' );

			$queryData = array(
				'fields' => array(
					'Dossierep.id',
					'Dossierep.personne_id',
					'Dossierep.themeep',
					'Dossierep.created',
					'Dossierep.modified',
					'Personne.id',
					'Personne.foyer_id',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.nomnai',
					'Personne.prenom2',
					'Personne.prenom3',
					'Personne.nomcomnai',
					'Personne.dtnai',
					'Personne.rgnai',
					'Personne.typedtnai',
					'Personne.nir',
					'Personne.topvalec',
					'Personne.sexe',
					'Personne.nati',
					'Personne.dtnati',
					'Personne.pieecpres',
					'Personne.idassedic',
					'Personne.numagenpoleemploi',
					'Personne.dtinscpoleemploi',
					'Personne.numfixe',
					'Personne.numport',
					'Dossier.matricule',
					'Foyer.sitfam',
					'Adresse.numvoie',
					'Adresse.libtypevoie',
					'Adresse.nomvoie',
					'Adresse.compladr',
					'Adresse.nomcom',
					'Adresse.numcom',
					'Adresse.codepos',

				),
				'joins' => array(
					$Dossierep->join( 'Personne', array( 'type' => 'INNER' ) ),
					$Dossierep->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Dossierep->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$Dossierep->Personne->Foyer->join(
						'Adressefoyer',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Adressefoyer.id IN (
									'.$Dossierep->Personne->Foyer->Adressefoyer->sqDerniereRgadr01('Adressefoyer.foyer_id').'
								)'
							)
						)
					),
					$Dossierep->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
				),
				'conditions' => array(
					'Dossierep.id' => $dossierep_id
				)
			);

			// Fiches synthétiques des dossiers d'EP
			$dossierep = $this->Commissionep->Passagecommissionep->Dossierep->find( 'first', $queryData );

			$fichessynthetiques = $this->Commissionep->Passagecommissionep->Dossierep->find(
				'first',
				$this->_qdFichesSynthetiques( array( 'Passagecommissionep.commissionep_id' => $commissionep_id , 'Passagecommissionep.dossierep_id' => $dossierep_id ), true )
			);

			$dataFiche = Set::merge( $dossierep, $fichessynthetiques );
			$dataFiche['Dossierep']['anonymiser'] = ( $anonymiser ? 1 : 0 );

			$options = $this->getOptions( array( 'fiche' => true ) );

			return $this->Commissionep->ged(
				$dataFiche,
				"{$this->Commissionep->alias}/fichesynthese.odt",
				false,
				$options
			);
		}

		/**
		 * Retourne les options liées aux différentes méthodes du modèle.
		 *
		 * Clés possibles du paramètre $params:
		 *	- fiche (pour la fiche de synthèse)
		 *
		 * @param array $params
		 * @return array
		 */
		public function getOptions( array $params = array() ) {
			$params += array( 'fiche' => false );

			$cacheKey = Inflector::underscore( $this->Commissionep->useDbConfig ).'_'.Inflector::underscore( $this->Commissionep->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $params ) );
			$result = Cache::read( $cacheKey );

			if( $result === false ) {
				$result = $this->Commissionep->enums();
				if( $params['fiche'] ) {
					$Option = ClassRegistry::init( 'Option' );

					$result['Foyer']['sitfam'] = $Option->sitfam();
					$result['Personne']['qual'] = $Option->qual();

					if( Configure::read( 'Cg.departement' ) == 93 ) {
						$result['Dsp'] = array(
							'natlog' => $this->Commissionep->Passagecommissionep->Dossierep->Personne->Dsp->enum( 'natlog' ),
							'nivetu' => $this->Commissionep->Passagecommissionep->Dossierep->Personne->Dsp->enum( 'nivetu' )
						);
						$result['Nonrespectsanctionep93']['origine'] = $this->Commissionep->Passagecommissionep->Dossierep->Nonrespectsanctionep93->enum( 'origine' );
						$result['Dossierep']['themeep'] = $this->Commissionep->Passagecommissionep->Dossierep->enum( 'themeep' );

						$decisions = $this->Commissionep->Passagecommissionep->Decisionnonrespectsanctionep93->enum( 'decision' );
						foreach( array( 1, 2, 3 ) as $passage ) {
							foreach( array( 'ep', 'cg' ) as $niveau ) {
								$result["Decision{$passage}{$niveau}"]['decision'] = $decisions;
							}
						}
					}
				}

				Cache::write( $cacheKey, $result );
			}

			return $result;
		}

		/**
		 * Retourne le querydata (à utiliser sur le modèle Passagecommissionep)
		 * permettant d'obtenir la liste des dossiers d'EP à afficher dans la synthèse.
		 *
		 * @param integer $commissionep_id L'id technique de la commission d'EP
		 * @return array
		 */
		public function qdSynthese( $commissionep_id ) {
			$querydata = array(
				'fields' => array_merge(
					$this->Commissionep->Passagecommissionep->fields(),
					$this->Commissionep->Passagecommissionep->Dossierep->fields(),
					$this->Commissionep->Passagecommissionep->Dossierep->Personne->fields(),
					array(
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->sqVirtualField( 'enerreur' ),
						'Dossier.matricule'
					),
					$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Adressefoyer->fields(),
					$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Adressefoyer->Adresse->fields()
				),
				'joins' => array(
					$this->Commissionep->Passagecommissionep->join( 'Dossierep', array( 'type' => 'INNER' ) ),
					$this->Commissionep->Passagecommissionep->Dossierep->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Commissionep->Passagecommissionep->Dossierep->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->join( 'Dossier', array( 'type' => 'LEFT OUTER' ) ),
					$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
				),
				'conditions' => array(
					'Passagecommissionep.commissionep_id' => $commissionep_id,
					'Adressefoyer.id IN ('.$this->Commissionep->Passagecommissionep->Dossierep->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
				),
				'contain' => false,
				'order' => array(
					'Passagecommissionep.heureseance' => 'asc',
					'Personne.nom' => 'asc',
					'Personne.prenom' => 'asc',
					'Dossierep.id' => 'asc'
				),
			);

			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$querydata['joins'][] = $this->Commissionep->Passagecommissionep->Dossierep->join( 'Nonorientationproep58', array( 'type' => 'LEFT OUTER' ) );
				$querydata['joins'][] = $this->Commissionep->Passagecommissionep->Dossierep->Nonorientationproep58->join( 'Decisionpropononorientationprocov58', array( 'type' => 'LEFT OUTER' ) );
				$querydata['joins'][] = $this->Commissionep->Passagecommissionep->Dossierep->Nonorientationproep58->Decisionpropononorientationprocov58->join( 'Passagecov58', array( 'type' => 'LEFT OUTER' ) );
				$querydata['joins'][] = $this->Commissionep->Passagecommissionep->Dossierep->Nonorientationproep58->Decisionpropononorientationprocov58->Passagecov58->join( 'Cov58', array( 'type' => 'LEFT OUTER' ) );

				$sqDerniereorientstruct = $this->Commissionep->Passagecommissionep->Dossierep->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere( 'Personne.id' );
				$querydata['joins'][] = $this->Commissionep->Passagecommissionep->Dossierep->Personne->join( 'Orientstruct', array( 'type' => 'LEFT OUTER' ) );
				$querydata['joins'][] = $this->Commissionep->Passagecommissionep->Dossierep->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) );

				$querydata['conditions'][] = array(
					'OR' => array(
						'Orientstruct.id IS NULL',
						"Orientstruct.id IN ( {$sqDerniereorientstruct} )"
					)
				);

				$querydata['fields'] = array_merge(
					$querydata['fields'],
					array(
						'Cov58.datecommission',
						'Structurereferente.lib_struc',
						$this->Commissionep->Passagecommissionep->Dossierep->Personne->WebrsaPersonne->sqStructureorientante( 'Dossierep.personne_id', 'Structureorientante.lib_struc' )
					)
				);
			}

			return $querydata;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les fonctions vides.
		 */
		public function prechargement() {
			$success = parent::prechargement() !== false;

			$query = $this->_qdFichesSynthetiques( array() );
			$success = !empty( $query ) && $success;

			$query = $this->_qdFichesSynthetiques( array(), true );
			$success = !empty( $query ) && $success;

			$options = $this->getOptions( array( 'fiche' => false ) );
			$success = !empty( $options ) && $success;

			$options = $this->getOptions( array( 'fiche' => true ) );
			$success = !empty( $options ) && $success;

			return $success;
		}

		/**
		 *
		 * @see Commissionep::dossiersParListe()
		 *
		 * @return array
		 */
		public function querydataFragmentsErrors() {
			$checks = array();
			$keys = array(
				'Commissionseps.decisionep.order',
				'Commissionseps.decisioncg.order',
				'Commissionseps.traiterep.order',
			);

			$findFirst = $this->Commissionep->find('first', array('fields' => 'id'));
			$commissionep_id = Hash::get($findFirst, $this->Commissionep->alias.'.id');

			foreach( $keys as $key ) {
				foreach( $this->Commissionep->Ep->themes() as $theme ) {
					$model = Inflector::classify( $theme );
					$queryData = $this->Commissionep->Passagecommissionep->Dossierep->{$model}->qdDossiersParListe( $commissionep_id, 'ep' );

					if( !empty( $queryData ) ) {
						$configuredOrder = Configure::read( $key );
						unset( $queryData['fields'] );
						$queryData['order'] = $configuredOrder ? $configuredOrder : array( 'Personne.nom', 'Personne.prenom' );
						$check = array(
							'value' => var_export( $configuredOrder, true ),
							'success' => null,
							'message' => null
						);

						try {
							$this->Commissionep->Passagecommissionep->Dossierep->forceVirtualFields = false;
							$this->Commissionep->Passagecommissionep->Dossierep->find( 'first', $queryData );
							$check['success'] = true;
						} catch( Exception $e ) {
							$check['success'] = false;
							$check['message'] = $e->getMessage();
						}

						$checks["{$key} pour la thématique {$theme}"] = $check;
					}
				}
			}

			$departement = (int)Configure::read( 'Cg.departement' );
			if( $departement === 66 ) {
				$key = 'Commissionseps.printOrdresDuJour.order';
				$sql = $this->Commissionep->Passagecommissionep->Dossierep->sq( $this->queryPdfOrdredujour(array('Commissionep' => array('id' => 0))) );
				$check = $this->Commissionep->getDataSource()->checkPostgresSqlSyntax( $sql );
				$check['value'] = var_export( Configure::read($key), true );

				$checks[$key] = $check;
			}

			return $checks;
		}

		/**
		 * Annule la validation de la commission EP
		 *
		 * @param integer $commissionep_id
		 */
		public function annulervalidation( $commissionep_id = null ) {
			$commissionep = $this->Commissionep->findById ($commissionep_id);
			$this->Commissionep->create($commissionep);
			$response = $this->Commissionep->saveField('etatcommissionep', 'decisionep');

			if (isset ($response['Commissionep']['id'])) {
				$query = "
					UPDATE dossierspcgs66
					SET etatdossierpcg = 'annulationep'
					FROM personnes
						INNER JOIN dossierseps ON (dossierseps.personne_id = personnes.id)
						INNER JOIN passagescommissionseps ON (dossierseps.id = passagescommissionseps.dossierep_id)
					WHERE dossierspcgs66.foyer_id = personnes.foyer_id
						AND etatdossierpcg = 'attaffect'
						AND passagescommissionseps.commissionep_id = ".$response['Commissionep']['id'].";
				";
				$this->Commissionep->query($query);

				return true;
			}

			return false;
		}
	}
?>