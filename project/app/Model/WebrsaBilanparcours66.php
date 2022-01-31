<?php
	/**
	 * Code source de la classe WebrsaBilanparcours66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractLogic', 'Model');
	App::uses('WebrsaLogicAccessInterface', 'Model/Interface');
	App::uses('WebrsaModelUtility', 'Utility');

	/**
	 * La classe WebrsaBilanparcours66 possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaBilanparcours66 extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaBilanparcours66';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Bilanparcours66');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$fields = array(
				'positionbilan' => 'Bilanparcours66.positionbilan',
				'proposition' => 'Bilanparcours66.proposition',
				'dateimpressionconvoc' => 'Defautinsertionep66.dateimpressionconvoc',
			);

			if (!WebrsaModelUtility::findJoinKey('Defautinsertionep66', $query)) {
				$query['joins'][] = $this->Bilanparcours66->join('Defautinsertionep66');
			}

			return Hash::merge($query, array('fields' => array_values($fields)));
		}

		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array()) {
			$query = array(
				'fields' => array(
					'Bilanparcours66.id',
					'Bilanparcours66.personne_id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Bilanparcours66->join('Personne')
				),
				'contain' => false,
				'order' => array(
					'Bilanparcours66.datebilan' => 'DESC',
					'Bilanparcours66.id' => 'DESC',
				)
			);

			$results = $this->Bilanparcours66->find('all', $this->completeVirtualFieldsForAccess($query));
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
		 *
		 * @param array $data
		 * @return string
		 */
		public function calculPositionBilan( $data ){
			// Si on nous donne la position du bilan, on ne la recalcule pas
			if( isset( $data[$this->Bilanparcours66->alias]['positionbilan'] ) && !empty( $data[$this->Bilanparcours66->alias]['positionbilan'] ) ) {
				return $data[$this->Bilanparcours66->alias]['positionbilan'];
			}

			$traitement = Set::classicExtract( $data, 'Bilanparcours66.proposition' );
			$positionbilan = null;
			// 'eplaudit', 'eplparc', 'attcga', 'attct', 'ajourne', 'annule'

			if ( ( $traitement == 'audition' || $traitement == 'auditionpe' ) && empty( $saisineep ) )
				$positionbilan = 'eplaudit';
			elseif ( ( $traitement == 'parcours' || $traitement == 'parcourspe' ) && empty( $saisineep ) )
				$positionbilan = 'eplparc';
			return $positionbilan;
		}

		/**
		 * Récupère une liste des ids des bilans de parcours à partir d'une liste
		 * d'ids d'entrées de passages en commissions EP.
		 *
		 * @param string $modeleThematique
		 * @param array $passagescommissionseps_ids
		 * @return array
		 */
		protected function _bilansparcours66IdsDepuisPassagescommissionsepsIds( $modeleThematique, $passagescommissionseps_ids ) {

// Fichier de logs : app/tmp/debug.log
Debugger::log(array(
		'fields' => array( "{$modeleThematique}.id", "{$modeleThematique}.bilanparcours66_id" ),
		'conditions' => array(
			"{$modeleThematique}.dossierep_id IN ("
				.$this->Bilanparcours66->{$modeleThematique}->Dossierep->sq(
					array(
						'alias' => 'dossierseps',
						'fields' => array( 'dossierseps.id' ),
						'conditions' => array(
							'dossierseps.id IN ('
								.$this->Bilanparcours66->{$modeleThematique}->Dossierep->Passagecommissionep->sq(
									array(
										'alias' => 'passagescommissionseps',
										'fields' => array( 'passagescommissionseps.dossierep_id' ),
										'conditions' => array(
											'passagescommissionseps.id' => $passagescommissionseps_ids
										),
										'contain' => false
									)
								)
							.')'
						),
						'contain' => false
					)
				)
			.')'
		),
		'contain' => false
	));

			return $this->Bilanparcours66->{$modeleThematique}->find(
				'list',
				array(
					'fields' => array( "{$modeleThematique}.id", "{$modeleThematique}.bilanparcours66_id" ),
					'conditions' => array(
						"{$modeleThematique}.dossierep_id IN ("
							.$this->Bilanparcours66->{$modeleThematique}->Dossierep->sq(
								array(
									'alias' => 'dossierseps',
									'fields' => array( 'dossierseps.id' ),
									'conditions' => array(
										'dossierseps.id IN ('
											.$this->Bilanparcours66->{$modeleThematique}->Dossierep->Passagecommissionep->sq(
												array(
													'alias' => 'passagescommissionseps',
													'fields' => array( 'passagescommissionseps.dossierep_id' ),
													'conditions' => array(
														'passagescommissionseps.id' => $passagescommissionseps_ids
													),
													'contain' => false
												)
											)
										.')'
									),
									'contain' => false
								)
							)
						.')'
					),
					'contain' => false
				)
			);
		}

		/**
		 * Mise à jour de la position du bilan de parcours à partir de la cohorte de
		 * décisions (niveau EP ou niveau CG) des EPs.
		 *
		 * @param string $modeleThematique
		 * @param array $datas
		 * @param string $niveauDecision
		 * @param array $passagescommissionseps_ids
		 * @return boolean
		 */
		public function updatePositionBilanDecisionsEp( $modeleThematique, $datas, $niveauDecision, $passagescommissionseps_ids ) {
			$success = true;

			// Niveau EP
			if( $niveauDecision == 'ep' ) {
				$bilansparcours66_ids = $this->_bilansparcours66IdsDepuisPassagescommissionsepsIds( $modeleThematique, $passagescommissionseps_ids );

				$position = ( ( $modeleThematique == 'Defautinsertionep66' ) ? 'attcga' : 'attct' );
				$success = $this->Bilanparcours66->updateAllUnBound(
					array( 'Bilanparcours66.positionbilan' => "'{$position}'" ),
					array( '"Bilanparcours66"."id"' => array_values( $bilansparcours66_ids ) )
				) && $success;

Debugger::log($bilansparcours66_ids);
Debugger::log(array( 'Bilanparcours66.positionbilan' => "'{$position}'" ));
Debugger::log(array( '"Bilanparcours66"."id"' => array_values( $bilansparcours66_ids ) ));

			}
			// Niveau CG
			else {
				$passagescommissionseps_ids_annule = array();
				$passagescommissionseps_ids_reporte = array();
				$passagescommissionseps_ids_autre = array();

				$modeleDecisionName = 'Decision'.Inflector::underscore( $modeleThematique );

				foreach( $datas as $themeTmpData ) {
					switch( $themeTmpData[$modeleDecisionName]['decision'] ) {
						case 'annule':
							$passagescommissionseps_ids_annule[] = $themeTmpData[$modeleDecisionName]['passagecommissionep_id'];
							break;
						case 'reporte':
							$passagescommissionseps_ids_reporte[] = $themeTmpData[$modeleDecisionName]['passagecommissionep_id'];
							break;
						default:
							$passagescommissionseps_ids_autre[] = $themeTmpData[$modeleDecisionName]['passagecommissionep_id'];
					}
				}

				if( !empty( $passagescommissionseps_ids_annule ) ) {
					$bilansparcours66_ids = $this->_bilansparcours66IdsDepuisPassagescommissionsepsIds( $modeleThematique, $passagescommissionseps_ids_annule );
					$success = $this->Bilanparcours66->updateAllUnBound(
						array( 'Bilanparcours66.positionbilan' => '\'annule\'' ),
						array( '"Bilanparcours66"."id"' => array_values( $bilansparcours66_ids ) )
					) && $success;
				}

				if( !empty( $passagescommissionseps_ids_reporte ) ) {
					foreach ( $passagescommissionseps_ids_reporte as $id ) {
						$bilanparcour66_id = implode((array)$this->_bilansparcours66IdsDepuisPassagescommissionsepsIds( $modeleThematique, $id ));

						foreach ( $datas as $data ) {
							if ( Hash::get($data, $modeleDecisionName.'.passagecommissionep_id') === $id ) {
								$dataBilanParcours = array(
									'id' => $bilanparcour66_id,
									'positionbilan' => 'ajourne',
									'motifreport' => Hash::get($data, $modeleDecisionName.'.commentaire')
								);

								$this->Bilanparcours66->create($dataBilanParcours);
								$success = $success && $this->Bilanparcours66->save( null, array( 'atomic' => false ) );

								break;
							}
						}
					}
				}
Debugger::log($passagescommissionseps_ids_annule);
Debugger::log($passagescommissionseps_ids_reporte);
Debugger::log($passagescommissionseps_ids_autre);
				if( !empty( $passagescommissionseps_ids_autre ) ) {
					$bilansparcours66_ids = $this->_bilansparcours66IdsDepuisPassagescommissionsepsIds( $modeleThematique, $passagescommissionseps_ids_autre );
// Fichier de logs : app/tmp/debug.log
Debugger::log($bilansparcours66_ids);
					$success = $this->Bilanparcours66->updateAllUnBound(
						array( 'Bilanparcours66.positionbilan' => '\'traite\'' ),
						array( '"Bilanparcours66"."id"' => array_values( $bilansparcours66_ids ) )
					) && $success;
				}
			}

			return $success;
		}

		/**
		 * Sauvegarde du bilan de parcours d'un allocataire.
		 *
		 * Le bilan de parcours entraîne:
		 * 	- pour le thème réorientation/saisinesbilansparcourseps66
		 * 		* soit un maintien de l'orientation, sans passage en EP
		 * 		* soit une saisine de l'EP locale, commission parcours
		 *
		 * @param array $data Les données du bilan à sauvegarder.
		 * @return boolean True en cas de succès, false sinon.
		 * @access public
		 */
		public function sauvegardeBilan( $data ) {
			if ( isset( $data['Pe']['Bilanparcours66']['id'] ) ) {
				$id = $data['Pe']['Bilanparcours66']['id'];
				unset( $data['Pe']['Bilanparcours66']['id'] );
			}
			if ( isset( $data['Pe']['Bilanparcours66'] ) && !empty( $data['Pe']['Bilanparcours66']['datebilan'] ) ) {
				$datape = $data['Pe'];
				unset($data['Pe']);
				$data = Set::merge( $data, $datape );

				if ( isset( $id ) ) {
					$data['Bilanparcours66']['id'] = $id;
				}
			}

			$data[$this->Bilanparcours66->alias]['saisineepparcours'] = ( @$data[$this->Bilanparcours66->alias]['proposition'] == 'parcours' );

			// Calcul et mise à jour de la position du bilan
			$data[$this->Bilanparcours66->alias]['positionbilan'] = $this->calculPositionBilan( $this->Bilanparcours66->data );

			// Recondution du contrat
			if( isset( $data[$this->Bilanparcours66->alias]['proposition'] ) && in_array( $data[$this->Bilanparcours66->alias]['proposition'], array( 'traitement', 'aucun' ) ) ){
				$cleanedData = $data;
				unset( $cleanedData['Saisinebilanparcoursep66'] );
				return $this->maintien( $cleanedData );
			}
			// Saisine de l'EP
			else {
				return $this->saisine( $data );
			}
		}

		/**
		 * Recherche du dernier CER (dd_ci) pour un allocataire donné, quel que
		 * soit son état ou sa position.
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		protected function _getDernierContratinsertion( $personne_id ) {
			$sql = $this->Bilanparcours66->Contratinsertion->WebrsaContratinsertion->sqDernierContrat( 'Contratinsertion.personne_id' );

			$result = $this->Bilanparcours66->Contratinsertion->find(
				'first',
				array(
					'conditions' => array(
						'Contratinsertion.personne_id' => $personne_id,
						"Contratinsertion.id IN ( {$sql} )"
					),
					'contain' => false
				)
			);

			return $result;
		}

		/**
		 * Sauvegarde d'un maintien de l'orientation d'un allocataire suite au bilan de parcours.
		 *
		 * Un maintien de l'orientation entraîne la création d'une nouvelle orientation,
		 * la création d'un nouveau CER. Ces nouvelles entrées sont des copies des
		 * anciennes (les dates changent).
		 *
		 * @param array $data Les données du bilan à sauvegarder.
		 * @return boolean True en cas de succès, false sinon.
		 * @access public
		 *
		 * FIXME: modification du bilan
		 */
		public function maintien( $data ) {
			$data[$this->Bilanparcours66->alias]['saisineepparcours'] = ( empty( $data[$this->Bilanparcours66->alias]['maintienorientation'] ) ? '1' : '0' );
			$data[$this->Bilanparcours66->alias]['positionbilan'] = 'traite';
			$this->Bilanparcours66->create( $data );
			if( $success = $this->validates() ) {
				if( $data[$this->Bilanparcours66->alias]['proposition'] == 'aucun' ) {
					// Sauvegarde du bilan de parcours
					$this->Bilanparcours66->create( $data );
					$success = $this->Bilanparcours66->save( null, array( 'atomic' => false ) ) && $success;

					// S'il s'agit d'un ajout, on met à jour la position du CER
					$primaryKey = Hash::get( $data, "{$this->Bilanparcours66->alias}.id" );
					if( empty( $primaryKey ) ) {
						$vxContratinsertion = $this->_getDernierContratinsertion( Hash::get( $data, "{$this->Bilanparcours66->alias}.personne_id" ) );
						if( !empty( $vxContratinsertion ) ) {
							$success = $success && $this->Bilanparcours66->Contratinsertion->WebrsaContratinsertion->updatePositionsCersByConditions(
								array( 'Contratinsertion.personne_id' => Hash::get( $data, "{$this->Bilanparcours66->alias}.personne_id" ) )
							);
						}
					}
				}
				else{
					// Recherche de l'ancienne orientation
					$vxOrientstruct = array();
					if( !empty( $data[$this->Bilanparcours66->alias]['orientstruct_id'] ) ) {
						$vxOrientstruct = $this->Bilanparcours66->Orientstruct->find(
							'first',
							array(
								'conditions' => array(
									'Orientstruct.id' => $data[$this->Bilanparcours66->alias]['orientstruct_id']
								),
								'contain' => false
							)
						);
					}

					if( empty( $vxOrientstruct ) ) {
						$this->Bilanparcours66->invalidate( 'proposition', 'Vieille orientation répondant aux critères non trouvé.');
						return false;
					}

                    if( $data['Bilanparcours66']['choixsanspassageep'] == 'maintien' ) {

                        if( $data['Bilanparcours66']['changementrefsansep'] != 'O' ) {
                            // Recherche de l'ancien contrat d'insertion
							$vxContratinsertion = $this->_getDernierContratinsertion( $vxOrientstruct['Orientstruct']['personne_id'] );

                            $sqDernierCui = $this->Bilanparcours66->Cui->WebrsaCui->sqDernierContrat( '"Cui"."personne_id"' );
                            $vxCui = $this->Bilanparcours66->Cui->find(
                                'first',
                                array(
                                    'conditions' => array(
                                        'Cui.personne_id' => $vxOrientstruct['Orientstruct']['personne_id'],
                                        "Cui.id IN ( {$sqDernierCui} )"
                                    ),
                                    'contain' => false,
                                    'recursive' => -1
                                )
                            );

                            if( empty( $vxContratinsertion ) && empty( $vxCui ) ) {
                                $this->Bilanparcours66->invalidate( 'changementref', 'Cette personne ne possède aucun contrat.' );
                                return false;
                            }
                        }

                        if ( $data['Bilanparcours66']['changementrefsansep'] == 'O' ) {
                            list( $typeorient_id, $structurereferente_id ) = explode( '_', $data['Bilanparcours66']['nvstructurereferente_id'] );

                            $rgorient = $this->Bilanparcours66->Orientstruct->WebrsaOrientstruct->rgorientMax( $vxOrientstruct['Orientstruct']['personne_id'] ) + 1;
                            $origine = ( $rgorient > 1 ? 'reorientation' : 'cohorte' );
							$referent_id = ( $vxOrientstruct['Orientstruct']['structurereferente_id'] == $structurereferente_id ? $vxOrientstruct['Orientstruct']['referent_id'] : null );

                            // Sauvegarde de la nouvelle orientation
                            $orientstruct = array(
                                'Orientstruct' => array(
                                    'personne_id' => $vxOrientstruct['Orientstruct']['personne_id'],
                                    'typeorient_id' => $typeorient_id,
                                    'structurereferente_id' => $structurereferente_id,
                                    'referent_id' => $referent_id,
                                    'date_propo' => date( 'Y-m-d' ),
                                    'date_valid' => date( 'Y-m-d' ),
                                    'statut_orient' => 'Orienté',
                                    'user_id' => $data['Bilanparcours66']['user_id'],
                                    'rgorient' => $rgorient,
                                    'origine' => $origine,
                                )
                            );
                            $this->Bilanparcours66->Orientstruct->create( $orientstruct );
                            $success = $this->Bilanparcours66->Orientstruct->save( null, array( 'atomic' => false ) ) && $success;

							// Clôture du référent du parcours actuel
							$this->Bilanparcours66->Orientstruct->Personne->PersonneReferent->updateAllUnBound(
								array( 'PersonneReferent.dfdesignation' => "'".date( 'Y-m-d' )."'" ),
								array(
									'"PersonneReferent"."personne_id"' => $vxOrientstruct['Orientstruct']['personne_id'],
									'"PersonneReferent"."dfdesignation" IS NULL'
								)
							);
                        }
                        else if( $data['Bilanparcours66']['changementrefsansep'] == 'N' ) {
							// FIXME: si c'est un ajout seulement / à la création du CER seulement ?
                            $contratinsertion = $vxContratinsertion;
                            unset( $contratinsertion['Contratinsertion']['id'] );
                            $contratinsertion['Contratinsertion']['dd_ci'] = $data['Bilanparcours66']['ddreconductoncontrat'];
                            $contratinsertion['Contratinsertion']['df_ci'] = $data['Bilanparcours66']['dfreconductoncontrat'];
                            $contratinsertion['Contratinsertion']['duree_engag'] = $data['Bilanparcours66']['duree_engag'];

                            $idRenouvellement = $this->Bilanparcours66->Contratinsertion->Typocontrat->field( 'Typocontrat.id', array( 'Typocontrat.lib_typo' => 'Renouvellement' ) );
                            $contratinsertion['Contratinsertion']['typocontrat_id'] = $idRenouvellement;
                            $contratinsertion['Contratinsertion']['num_contrat'] = 'REN';

                            $contratinsertion['Contratinsertion']['rg_ci'] = ( $contratinsertion['Contratinsertion']['rg_ci'] + 1 );

                            // La date de validation est à null afin de pouvoir modifier le contrat
                            $contratinsertion['Contratinsertion']['datevalidation_ci'] = null;
                            $contratinsertion['Contratinsertion']['datedecision'] = null;
                            // La date de saisie du nouveau contrat est égale à la date du jour
                            $contratinsertion['Contratinsertion']['date_saisi_ci'] = date( 'Y-m-d' );
							// La position du contrat est "En attente de validation"
							$contratinsertion['Contratinsertion']['positioncer'] = 'attvalid';

                            unset($contratinsertion['Contratinsertion']['decision_ci']);
                            unset($contratinsertion['Contratinsertion']['datevalidation_ci']);
                            unset($contratinsertion['Contratinsertion']['datedecision']);

                            $fields = array(
                                'actions_prev',
                                'aut_expr_prof',
                                'emp_trouv',
                                'sect_acti_emp',
                                'emp_occupe',
                                'duree_hebdo_emp',
                                'nat_cont_trav',
                                'duree_cdd',
                                'niveausalaire',
                                'datenotification',
                                'created',
                                'modified'
                            ); // FIXME: une variable du modèle
                            foreach( $fields as $field ) {
                                unset( $contratinsertion['Contratinsertion'][$field] );
                            }

                            // Calcul de la limite de cumul de durée de CER à l'enregistrement du bilan
                            $nbCumulDureeCER66 = $this->Bilanparcours66->Contratinsertion->WebrsaContratinsertion->limiteCumulDureeCER( $data['Bilanparcours66']['personne_id'] );
							$dureeEngagReconductionCER = $contratinsertion['Contratinsertion']['duree_engag'];

                            // Si les champs de reconduction ne sont pas renseignés,
                            // on empêche l'enregistrement du bilan
                            if( empty( $dureeEngagReconductionCER ) ) {
                                $this->Bilanparcours66->invalidate( 'duree_engag', 'Champ obligatoire' );
                                $this->Bilanparcours66->invalidate( 'ddreconductoncontrat', 'Champ obligatoire' );
                                $this->Bilanparcours66->invalidate( 'dfreconductoncontrat', 'Champ obligatoire' );
                                return false;
                            }

                            // Test correction FIXME
                            // Si les 24 mois sont dépassés mais que l'on confirme la volonté
                            // de renouveler un CER depuis le bilan de parcours, alors on passe
                            // la valeur du champ cumulduree à celle de la durée choisie
                            if( $nbCumulDureeCER66 > 24 && !empty( $dureeEngagReconductionCER ) ) {
                                $contratinsertion['Contratinsertion']['cumulduree'] = $dureeEngagReconductionCER;
                            }
                            // Fin test correction FIXME

                            if( ( $nbCumulDureeCER66 + $dureeEngagReconductionCER ) > 24 && $contratinsertion['Contratinsertion']['cumulduree'] > 24 ){
                                $this->Bilanparcours66->invalidate( 'duree_engag', 'La durée du CER sélectionnée dépasse la limite des 24 mois de contractualisation autorisée pour une orientation en SOCIAL' );
                                return false;
                            }

							$contratinsertion['Contratinsertion']['rg_ci'] = null;
                            $this->Bilanparcours66->Contratinsertion->create( $contratinsertion );
                            $success = $this->Bilanparcours66->Contratinsertion->save( null, array( 'atomic' => false ) ) && $success;
							if( $success ) {
								$success = $this->Bilanparcours66->Contratinsertion->WebrsaContratinsertion->updateRangsContratsPersonne( $contratinsertion['Contratinsertion']['personne_id'] ) && $success;
							}
							$success = $success && $this->Bilanparcours66->Contratinsertion->WebrsaContratinsertion->updatePositionsCersByConditions(
								array( 'Contratinsertion.personne_id' => $contratinsertion['Contratinsertion']['personne_id'] )
							);

                            $data[$this->Bilanparcours66->alias]['contratinsertion_id'] = $vxContratinsertion['Contratinsertion']['id'];
                            $data[$this->Bilanparcours66->alias]['nvcontratinsertion_id'] = $this->Bilanparcours66->Contratinsertion->id;
                        }

                        $data['Bilanparcours66']['typeorientprincipale_id'] = $data['Bilanparcours66']['sansep_typeorientprincipale_id'];
                        $data['Bilanparcours66']['changementref'] = $data['Bilanparcours66']['changementrefsansep'];
                    }
                    else if( $data['Bilanparcours66']['choixsanspassageep'] == 'reorientation' ) {
                        list( $typeorient_id, $structurereferente_id ) = explode( '_', $data['Bilanparcours66']['nvstructurereferente_id'] );

                        $rgorient = $this->Bilanparcours66->Orientstruct->WebrsaOrientstruct->rgorientMax( $vxOrientstruct['Orientstruct']['personne_id'] ) + 1;
                        $origine = ( $rgorient > 1 ? 'reorientation' : 'cohorte' );

                        // Sauvegarde de la nouvelle orientation
                        $orientstruct = array(
                            'Orientstruct' => array(
                                'personne_id' => $vxOrientstruct['Orientstruct']['personne_id'],
                                'typeorient_id' => $typeorient_id,
                                'structurereferente_id' => $structurereferente_id,
                                'referent_id' => null,
                                'date_propo' => date( 'Y-m-d' ),
                                'date_valid' => date( 'Y-m-d' ),
                                'statut_orient' => 'Orienté',
                                'user_id' => $data['Bilanparcours66']['user_id'],
                                'rgorient' => $rgorient,
                                'origine' => $origine,
                            )
                        );

                        $this->Bilanparcours66->Orientstruct->create( $orientstruct );
                        $success = $this->Bilanparcours66->Orientstruct->save( null, array( 'atomic' => false ) ) && $success;

                        $data['Bilanparcours66']['typeorientprincipale_id'] = $data['Bilanparcours66']['sansep_typeorientprincipale_id'];
                        $data['Bilanparcours66']['changementref'] = $data['Bilanparcours66']['changementrefsansep'];
                    }

					if( !empty( $this->Bilanparcours66->validationErrors ) ) {
						debug( $this->Bilanparcours66->validationErrors );
					}

					$this->Bilanparcours66->create( $data );
					$success = $this->Bilanparcours66->save( null, array( 'atomic' => false ) ) && $success;
				}
			}
			else {
				// S'il manque l'information "Bilan du parcours d'insertion", on supprime
				// l'erreur concernant les "Motifs de la saisine de l'équipe pluridisciplinaire"
				// car les caractères obligatoires de ces champs cont mutuellement exclusifs
				// et que les motifs n'ont normalement aucun sens lorsqu'on maintient
				if( isset( $this->Bilanparcours66->validationErrors['bilanparcoursinsertion'] ) ) {
					unset( $this->Bilanparcours66->validationErrors['motifep'] );
				}
			}

			return $success;
		}

		/**
		 * Sauvegarde du bilan de parcours, ainsi que d'une saisine d'EP suite
		 * à ce bilan de parcours. La saisine entraîne la création d'un dossier
		 * d'EP.
		 *
		 * @param array $data Les données du bilan à sauvegarder.
		 * @return boolean True en cas de succès, false sinon.
		 * @access public
		 *
		 * TODO: comment finaliser l'orientation précédente ?
		 * TODO: pouvoir envoyer la cause d'échec (ex.: $vxContratinsertion non
		 *       trouvé avec ces critères) depuis les règles de validation.
		 */
		public function saisine( $data ) {

			// Saisine parcours
			$success = true;
			if( isset($data['Bilanparcours66']['proposition']) && in_array( $data['Bilanparcours66']['proposition'], array( 'parcours', 'parcourspe' ) ) ) {
				$data[$this->Bilanparcours66->alias]['saisineepparcours'] = ( empty( $data[$this->Bilanparcours66->alias]['maintienorientation'] ) ? '1' : '0' );
				$this->Bilanparcours66->create( $data );

				$success = $this->Bilanparcours66->validates() && $success;

				if( $success ) {

					$vxOrientstruct = $this->Bilanparcours66->Orientstruct->find(
						'first',
						array(
							'conditions' => array(
								'Orientstruct.id' => $data[$this->Bilanparcours66->alias]['orientstruct_id'] // TODO: autre conditions ?
							),
							'contain' => false
						)
					);

					if( !isset( $data[$this->Bilanparcours66->alias]['origine'] ) || $data[$this->Bilanparcours66->alias]['origine'] != 'Defautinsertionep66' ) {
						if( empty( $vxOrientstruct ) ) {
							$this->Bilanparcours66->invalidate( 'choixparcours', 'Cette personne ne possède aucune orientation validée.' );
							return false;
						}

						// Possède-t-on un CER
						$vxContratinsertion = $this->_getDernierContratinsertion( $vxOrientstruct['Orientstruct']['personne_id'] );

						// Possède-t-on un CUI (pour rappel, un CUI vaut CER)
						$sqDernierCui = $this->Bilanparcours66->Cui->WebrsaCui->sqDernierContrat( '"Cui"."personne_id"' );
						$vxCui = $this->Bilanparcours66->Cui->find(
							'first',
							array(
								'conditions' => array(
									'Cui.personne_id' => $vxOrientstruct['Orientstruct']['personne_id'],
									"Cui.id IN ( {$sqDernierCui} )"
								),
								'contain' => false,
								'recursive' => -1
							)
						);

						if( Hash::get( $data, 'Bilanparcours66.changementrefavecep' ) == 'N' ) {
							if( empty( $vxContratinsertion ) && empty( $vxCui ) ) {
								$this->Bilanparcours66->invalidate( 'changementref', 'Cette personne ne possède aucun contrat.' );
								return false;
							}
						}

						// Sauvegarde du bilan
						$data[$this->Bilanparcours66->alias]['contratinsertion_id'] = @$vxContratinsertion['Contratinsertion']['id'];
						$data[$this->Bilanparcours66->alias]['cui_id'] = @$vxCui['Cui']['id'];

						// Si c'est un ajout et que la proposition du référent est "Commission Parcours": Examen du dossier avec passage en EP Locale avec maintien de l'orientation SOCIALE
						$primaryKey = Hash::get( $data, "{$this->Bilanparcours66->alias}.id" );
						if( empty( $primaryKey ) ) {
							$vxContratinsertion = $this->_getDernierContratinsertion( Hash::get( $data, "{$this->Bilanparcours66->alias}.personne_id" ) );
							if( !empty( $vxContratinsertion ) ) {
								$success = $success && $this->Bilanparcours66->Contratinsertion->WebrsaContratinsertion->updatePositionsCersByConditions(
									array( 'Contratinsertion.personne_id' => Hash::get( $data, "{$this->Bilanparcours66->alias}.personne_id" ) )
								);
							}
						}
					}

					if( isset( $data[$this->Bilanparcours66->alias]['origine'] ) && $data[$this->Bilanparcours66->alias]['origine'] == 'Defautinsertionep66' && !isset( $data[$this->Bilanparcours66->alias]['structurereferente_id'] ) ) {
						$data[$this->Bilanparcours66->alias]['structurereferente_id'] = $vxOrientstruct['Orientstruct']['structurereferente_id'];
					}

					if( isset( $data['Bilanparcours66']['avecep_typeorientprincipale_id'] ) ) {
						$data['Bilanparcours66']['typeorientprincipale_id'] = $data['Bilanparcours66']['avecep_typeorientprincipale_id'];
					}
					if( isset( $data['Bilanparcours66']['changementrefavecep'] ) ) {
						$data['Bilanparcours66']['changementref'] = $data['Bilanparcours66']['changementrefavecep'];
					}

					$this->Bilanparcours66->create( $data );
					$success = $this->Bilanparcours66->save( null, array( 'atomic' => false ) ) && $success;

					if( !empty( $this->Bilanparcours66->validationErrors ) ) {
						return false;
					}

					// Sauvegarde du dossier d'EP
					$dataDossierEp = array(
						'Dossierep' => array(
							'personne_id' => $vxOrientstruct['Orientstruct']['personne_id'],
							'themeep' => 'saisinesbilansparcourseps66'
						)
					);
					$this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->create( $dataDossierEp );
					$success = $this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->save( null, array( 'atomic' => false ) ) && $success;

					// Sauvegarde de la saisine
					$data['Saisinebilanparcoursep66']['bilanparcours66_id'] = $this->Bilanparcours66->id;
					$data['Saisinebilanparcoursep66']['dossierep_id'] = $this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->id;

					if( isset( $data['Bilanparcours66']['typeorientprincipale_id'] ) ) {
						$data['Saisinebilanparcoursep66']['typeorientprincipale_id'] = $data['Bilanparcours66']['typeorientprincipale_id'];
					}
					else {
						$data['Saisinebilanparcoursep66']['typeorientprincipale_id'] = $this->Bilanparcours66->Orientstruct->Typeorient->getIdLevel0( $data['Saisinebilanparcoursep66']['typeorient_id'] );
					}

					if( isset( $data['Bilanparcours66']['nvtypeorient_id'] ) ) {
						$data['Saisinebilanparcoursep66']['typeorient_id'] = $data['Bilanparcours66']['nvtypeorient_id'];
					}
					if( isset( $data['Bilanparcours66']['nvstructurereferente_id'] ) ) {
						$data['Saisinebilanparcoursep66']['structurereferente_id'] = $data['Bilanparcours66']['nvstructurereferente_id'];
					}

					if ( isset( $data['Bilanparcours66']['choixparcours'] ) ) {
						$data['Saisinebilanparcoursep66']['choixparcours'] = $data['Bilanparcours66']['choixparcours'];
					}
					if ( isset( $data['Bilanparcours66']['changementrefavecep'] ) ) {
						$data['Saisinebilanparcoursep66']['changementrefparcours'] = $data['Bilanparcours66']['changementref'];
					}
					if ( isset( $data['Bilanparcours66']['reorientation'] ) ) {
						$data['Saisinebilanparcoursep66']['reorientation'] = $data['Bilanparcours66']['reorientation'];
					}

					$this->Bilanparcours66->Saisinebilanparcoursep66->create( $data );
					$success = $this->Bilanparcours66->Saisinebilanparcoursep66->save( null, array( 'atomic' => false ) ) && $success;

				}
			}
			// Saisine audition
			else if( isset($data['Bilanparcours66']['proposition']) && $data['Bilanparcours66']['proposition'] == 'audition' ) {
				$data[$this->Bilanparcours66->alias]['saisineepparcours'] = '0';

				$this->Bilanparcours66->create( $data );
				if( $success = $this->Bilanparcours66->validates() ) {

					$nbOrientstruct = $this->Bilanparcours66->Orientstruct->find(
						'count',
						array(
							'conditions' => array(
								'Orientstruct.personne_id' => $data[$this->Bilanparcours66->alias]['personne_id']
							 ),
							 'contain' => false
						)
					);

					// Si pas d'orientaiton pour la personne, on peut créer un dossier EP
					if( $nbOrientstruct == 0 ) {

						// On vérifie que le choix du parcours est un EPL Audition avec passage pour Défaut de conclusion
						if( $data[$this->Bilanparcours66->alias]['examenaudition'] != 'DOD'  ) {
							$this->Bilanparcours66->invalidate( 'examenaudition', 'Cette personne ne possède aucune orientation, elle ne peut être signalée que pour un défaut de conclusion.' );
							return false;
						}

						$this->Bilanparcours66->create( $data );
						$success = $this->Bilanparcours66->save( null, array( 'atomic' => false ) ) && $success;


						// Sauvegarde du dossier d'EP
						$dataDossierEp = array(
							'Dossierep' => array(
								'personne_id' => $data[$this->Bilanparcours66->alias]['personne_id'],
								'themeep' => 'defautsinsertionseps66'
							)
						);
						$this->Bilanparcours66->Defautinsertionep66->Dossierep->create( $dataDossierEp );
						$success = $this->Bilanparcours66->Defautinsertionep66->Dossierep->save( null, array( 'atomic' => false ) ) && $success;

						// Sauvegarde de la saisine pour défaut d'insertion
						$data['Defautinsertionep66']['bilanparcours66_id'] = $this->Bilanparcours66->id;
						$data['Defautinsertionep66']['dossierep_id'] = $this->Bilanparcours66->Defautinsertionep66->Dossierep->id;
						$data['Defautinsertionep66']['origine'] = 'bilanparcours';

						$this->Bilanparcours66->Defautinsertionep66->create( $data );
						$success = $this->Bilanparcours66->Defautinsertionep66->save( null, array( 'atomic' => false ) ) && $success;
					}
					else {
						$vxOrientstruct = $this->Bilanparcours66->Orientstruct->find(
							'first',
							array(
								'conditions' => array(
									'Orientstruct.id' => $data[$this->Bilanparcours66->alias]['orientstruct_id'] // TODO: autre conditions ?
								),
								'contain' => false
							)
						);

						// FIXME: erreur pas dans choixparcours
						if( empty( $vxOrientstruct ) ) {
							$this->Bilanparcours66->invalidate( 'examenaudition', 'Cette personne ne possède aucune orientation validée.' );
							return false;
						}

						$vxContratinsertion = $this->_getDernierContratinsertion( $vxOrientstruct['Orientstruct']['personne_id'] );

						$sqDernierCui = $this->Bilanparcours66->Cui->WebrsaCui->sqDernierContrat( '"Cui"."personne_id"' );
						$vxCui = $this->Bilanparcours66->Cui->find(
							'first',
							array(
								'conditions' => array(
									'Cui.personne_id' => $vxOrientstruct['Orientstruct']['personne_id'],
									"Cui.id IN ( {$sqDernierCui} )"
								),
								'contain' => false,
								'recursive' => -1
							)
						);

						//Passage en EPL Audition pour non respect
						if( $data[$this->Bilanparcours66->alias]['examenaudition'] != 'DOD' ) {
							if( empty( $vxContratinsertion ) && empty( $vxCui ) ) {
								$this->Bilanparcours66->invalidate( 'examenaudition', 'Cette personne ne possède aucun contrat.' );
								return false;
							}
						}

						// Sauvegarde du bilan
						$data[$this->Bilanparcours66->alias]['contratinsertion_id'] = $vxContratinsertion['Contratinsertion']['id'];
						$data[$this->Bilanparcours66->alias]['cui_id'] = Hash::get($vxCui, 'Cui.id');
						$this->Bilanparcours66->create( $data );
						$success = $this->Bilanparcours66->save( null, array( 'atomic' => false ) ) && $success;

						if( !empty( $this->Bilanparcours66->validationErrors ) ) {
							debug( $this->Bilanparcours66->validationErrors );
						}

						// Sauvegarde du dossier d'EP
						$dataDossierEp = array(
							'Dossierep' => array(
								'personne_id' => $vxOrientstruct['Orientstruct']['personne_id'],
								'themeep' => 'defautsinsertionseps66'
							)
						);
						$this->Bilanparcours66->Defautinsertionep66->Dossierep->create( $dataDossierEp );
						$success = $this->Bilanparcours66->Defautinsertionep66->Dossierep->save( null, array( 'atomic' => false ) ) && $success;

						// Sauvegarde de la saisine pour défaut d'insertion
						$data['Defautinsertionep66']['bilanparcours66_id'] = $this->Bilanparcours66->id;
						$data['Defautinsertionep66']['dossierep_id'] = $this->Bilanparcours66->Defautinsertionep66->Dossierep->id;
						$data['Defautinsertionep66']['orientstruct_id'] = $vxOrientstruct['Orientstruct']['id'];
						$data['Defautinsertionep66']['contratinsertion_id'] = $vxContratinsertion['Contratinsertion']['id'];
						$data['Defautinsertionep66']['cui_id'] = Hash::get($vxCui, 'Cui.id');
						$data['Defautinsertionep66']['origine'] = 'bilanparcours';

						$this->Bilanparcours66->Defautinsertionep66->create( $data );
						$success = $this->Bilanparcours66->Defautinsertionep66->save( null, array( 'atomic' => false ) ) && $success;
					}
				}
			}
			// Saisine audition pôle emploi
			else if( isset($data['Bilanparcours66']['proposition']) && $data['Bilanparcours66']['proposition'] == 'auditionpe' ) {
				$data[$this->Bilanparcours66->alias]['saisineepparcours'] = '0';

				$this->Bilanparcours66->create( $data );
				if( $success = $this->Bilanparcours66->validates() ) {

					$success = $this->Bilanparcours66->save( null, array( 'atomic' => false ) ) && $success;

					// Avant de sauvegarder le dossier d'EP, on va rechercher
					// la radiation PE qui nous a conduit ici (si nécessaire)
					$historiqueetatpe_id = null;
					if( $data['Bilanparcours66']['examenauditionpe'] == 'radiationpe' ) {
						$queryDataPersonne = $this->Bilanparcours66->Defautinsertionep66->qdRadies( array(), array(), array() );
						$queryDataPersonne['fields'] = array( 'Historiqueetatpe.id' );
						$queryDataPersonne['conditions']['Personne.id'] = $data['Bilanparcours66']['personne_id'];
						$historiqueetatpe = $this->Bilanparcours66->Defautinsertionep66->Dossierep->Personne->find( 'first', $queryDataPersonne );
						$historiqueetatpe_id = Hash::get($historiqueetatpe, 'Historiqueetatpe.id');
					}

					$dossierep = array(
						'Dossierep' => array(
							'themeep' => 'defautsinsertionseps66',
							'personne_id' => $data['Bilanparcours66']['personne_id']
						)
					);
					$this->Bilanparcours66->Defautinsertionep66->Dossierep->create( $dossierep );
					$success = $this->Bilanparcours66->Defautinsertionep66->Dossierep->save( null, array( 'atomic' => false ) ) && $success;

					$defautinsertionep66 = array(
						'Defautinsertionep66' => array(
							'dossierep_id' => $this->Bilanparcours66->Defautinsertionep66->Dossierep->id,
							'orientstruct_id' => $data['Bilanparcours66']['orientstruct_id'],
							'bilanparcours66_id' => $this->Bilanparcours66->id,
							'origine' => $data['Bilanparcours66']['examenauditionpe'],
							'historiqueetatpe_id' => $historiqueetatpe_id
						)
					);

					$this->Bilanparcours66->Defautinsertionep66->create( $defautinsertionep66 );
					$success = $this->Bilanparcours66->Defautinsertionep66->save( null, array( 'atomic' => false ) ) && $success;
				}
			}
			else {
				$success = $this->Bilanparcours66->save( $data, array( 'atomic' => false ) ) && $success;
			}

			return $success;
		}

		/**
		 * Retourne le chemin relatif du modèle de document à utiliser pour
		 * l'enregistrement du PDF.
		 *
		 * @param array $data
		 * @return string
		 */
		public function modeleOdt( $data ) {
			return "Bilanparcours/bilanparcours.odt";
		}

		/**
		 * Récupère les données pour le PDF du bilan de parcours.
		 *
		 * @param integer $id L'id technique du bilan de parcours
		 * @param boolean $sendQueryData pour savoir si on renvoie les données ou uniquement la requête
		 * @return array
		 */
		public function getDataForPdf( $id, $sendQueryData = false ) {
			$cacheKey = Inflector::underscore( $this->Bilanparcours66->useDbConfig ).'_'.Inflector::underscore( $this->Bilanparcours66->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$querydata = Cache::read( $cacheKey );

			if( $querydata === false ) {
				$conditions = array(
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.id IN ( '.$this->Bilanparcours66->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
						)
					),
					$this->Bilanparcours66->Dossierpcg66->sqLatest( 'Decisiondossierpcg66', 'datevalidation' )
				);

				// Jointure spéciale sur Dossierep suivant la thématique
				$joinSaisinebilanparcoursep66 = $this->Bilanparcours66->Saisinebilanparcoursep66->join( 'Dossierep', array( 'type' => 'LEFT OUTER' ) );
				$joinDefautinsertionep66 = $this->Bilanparcours66->Defautinsertionep66->join( 'Dossierep', array( 'type' => 'LEFT OUTER' ) );

				$joinDossierep = $joinSaisinebilanparcoursep66;
				$joinDossierep['conditions'] = array(
					'OR' => array(
						$joinSaisinebilanparcoursep66['conditions'],
						$joinDefautinsertionep66['conditions']
					)
				);

				$joins = array(
					$this->Bilanparcours66->join( 'Orientstruct', array( 'type' => 'LEFT OUTER' ) ),
					$this->Bilanparcours66->Orientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
					$this->Bilanparcours66->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
					$this->Bilanparcours66->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					$this->Bilanparcours66->join( 'Serviceinstructeur', array( 'type' => 'LEFT OUTER' ) ),
					$this->Bilanparcours66->join( 'Saisinebilanparcoursep66', array( 'type' => 'LEFT OUTER' ) ),
					$this->Bilanparcours66->join( 'Defautinsertionep66', array( 'type' => 'LEFT OUTER' ) ),
					$this->Bilanparcours66->join( 'Contratinsertion', array( 'type' => 'LEFT OUTER' ) ),
					$this->Bilanparcours66->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Bilanparcours66->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Bilanparcours66->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$this->Bilanparcours66->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Bilanparcours66->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$joinDossierep,
					$this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->join( 'Passagecommissionep', array( 'type' => 'LEFT OUTER' ) ),
					$this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->join( 'Commissionep', array( 'type' => 'LEFT OUTER' ) ),
					$this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->join( 'Decisionsaisinebilanparcoursep66', array( 'type' => 'LEFT OUTER' ) ),

					array_words_replace(
						$this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->join(
							'Decisionsaisinebilanparcoursep66',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Decisionsaisinebilanparcoursep66.etape' => 'ep'
								)
							)
						),
						array(
							'Decisionsaisinebilanparcoursep66' => 'Decisionsaisinebilanparcoursep66ep'
						)
					),
					array_words_replace(
						$this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->join(
							'Decisionsaisinebilanparcoursep66',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Decisionsaisinebilanparcoursep66.etape' => 'cg'
								)
							)
						),
						array(
							'Decisionsaisinebilanparcoursep66' => 'Decisionsaisinebilanparcoursep66cg'
						)
					),

					$this->Bilanparcours66->Defautinsertionep66->Dossierep->Passagecommissionep->join( 'Decisiondefautinsertionep66', array( 'type' => 'LEFT OUTER' ) ),
					$this->Bilanparcours66->join( 'Dossierpcg66', array( 'type' => 'LEFT OUTER' ) ),
					$this->Bilanparcours66->Dossierpcg66->join( 'Decisiondossierpcg66', array( 'type' => 'LEFT OUTER' ) ),
					$this->Bilanparcours66->Dossierpcg66->Decisiondossierpcg66->join( 'Decisionpdo', array( 'type' => 'LEFT OUTER' ) )
				);

				// Liste des champs par étpae de décisions pour le passage en EPL Parcours du bilan
				$fieldsDecisionsaisinebilanparcoursep66 = $this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->Decisionsaisinebilanparcoursep66->fields();
				$fieldsDecisionsaisinebilanparcoursep66ep = array_words_replace(
					$fieldsDecisionsaisinebilanparcoursep66,
					array(
						'Decisionsaisinebilanparcoursep66' => 'Decisionsaisinebilanparcoursep66ep'
					)
				);
				$fieldsDecisionsaisinebilanparcoursep66cg = array_words_replace(
					$fieldsDecisionsaisinebilanparcoursep66,
					array(
						'Decisionsaisinebilanparcoursep66' => 'Decisionsaisinebilanparcoursep66cg'
					)
				);

				$querydata = array(
					'fields' => array_merge(
						$this->Bilanparcours66->fields(),
						$this->Bilanparcours66->Orientstruct->fields(),
						$this->Bilanparcours66->Orientstruct->Typeorient->fields(),
						$this->Bilanparcours66->Orientstruct->Structurereferente->fields(),
						$this->Bilanparcours66->Referent->fields(),
						$this->Bilanparcours66->Serviceinstructeur->fields(),
						$this->Bilanparcours66->Defautinsertionep66->fields(),
						$this->Bilanparcours66->Saisinebilanparcoursep66->fields(),
						$this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->fields(),
						$this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->fields(),
						$this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->Commissionep->fields(),
						$fieldsDecisionsaisinebilanparcoursep66,
						$this->Bilanparcours66->Defautinsertionep66->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->fields(),
						$this->Bilanparcours66->Contratinsertion->fields(),
						$this->Bilanparcours66->Personne->fields(),
						$this->Bilanparcours66->Personne->Foyer->fields(),
						$this->Bilanparcours66->Personne->Foyer->Dossier->fields(),
						$this->Bilanparcours66->Personne->Foyer->Adressefoyer->Adresse->fields(),
						$this->Bilanparcours66->Dossierpcg66->fields(),
						$this->Bilanparcours66->Dossierpcg66->Decisiondossierpcg66->fields(),
						$this->Bilanparcours66->Dossierpcg66->Decisiondossierpcg66->Decisionpdo->fields(),
						$fieldsDecisionsaisinebilanparcoursep66ep,
						$fieldsDecisionsaisinebilanparcoursep66cg
					),
					'joins' => $joins,
					'conditions' => $conditions,
					'contain' => false
				);

				Cache::write( $cacheKey, $querydata );
			}

			$querydata['conditions']['Bilanparcours66.id'] = $id;
			if($sendQueryData) {
				return $querydata;
			}
			$data = $this->Bilanparcours66->find( 'first', $querydata );

			return $data;
		}

		/**
		 * Récupère les données des options pour le PDF du bilan de parcours.
		 *
		 * @return array
		 */
		public function getOptionsForPdf( ) {
			$Option = ClassRegistry::init( 'Option' );
			$options =  Set::merge(
				$this->Bilanparcours66->enums(),
				$this->Bilanparcours66->Personne->enums(),
				$this->Bilanparcours66->Personne->Prestation->enums(),
				$this->Bilanparcours66->Defautinsertionep66->enums(),
				$this->Bilanparcours66->Defautinsertionep66->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->enums(),
				$this->Bilanparcours66->Saisinebilanparcoursep66->enums(),
				$this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->Decisionsaisinebilanparcoursep66->enums()
			);
			return $options;
		}

		/**
		 * Transfomer les données pour le PDF du bilan de parcours.
		 *
		 * @return array
		 */
		protected function _transformPDFData($data, $proposition ) {
			if( !empty( $data['Bilanparcours66']['examenaudition'] ) ){
				$data['Bilanparcours66']['examenaudition_value'] = $data['Bilanparcours66']['examenaudition'];
			}
			if( !empty( $data['Bilanparcours66']['choixparcours'] ) ){
				$data['Bilanparcours66']['choixparcours_value'] = $data['Bilanparcours66']['choixparcours'];
			}
			// Pour les données de Pôle emploi
			if( !empty( $data['Bilanparcours66']['examenauditionpe'] ) ){
				$data['Bilanparcours66']['examenauditionpe_value'] = $data['Bilanparcours66']['examenauditionpe'];
			}
            // FIXME: MAJ du champ changement de référent pour l'impression du bilan
            if( $proposition == 'aucun' ) {
                $data['Bilanparcours66']['changementref'] = $data['Bilanparcours66']['changementrefsansep'];
            }
            else if( !empty( $data['Bilanparcours66']['changementref'] ) ) {
                $data['Bilanparcours66']['changementref'] = $data['Bilanparcours66']['changementref'];
            }
            else if( !empty( $data['Bilanparcours66']['changementrefsansep'] ) ) {
                $data['Bilanparcours66']['changementref'] = $data['Bilanparcours66']['changementrefsansep'];
            }

			return $data;
		}

		/**
		 * Récupérer et Transfomer les données Manifestationbilanparcours66 pour le PDF du bilan de parcours.
		 *
		 * @return array
		 */
		protected function _getManifestationsData($data ) {
			$query = array (
				'conditions' => array (
					'Manifestationbilanparcours66.bilanparcours66_id' => $data['Bilanparcours66']['id'],
				)
			);
			$tmpDATA = $this->Bilanparcours66->Manifestationbilanparcours66-> find ( 'all', $query);

			if (!$tmpDATA) {
				foreach($this->Bilanparcours66->Manifestationbilanparcours66->fields() AS $field ){
					$field = substr($field, strlen('Manifestationbilanparcours66')+1);
					$tmpDATA[0]['Manifestationbilanparcours66'][$field] = NULL;
				}
			}

			$tmpDATA['Manifestationbilanparcours66'] = $tmpDATA;
			return $tmpDATA;
		}

		/**
		 * Récupérer et Transfomer les données ActioncandidatPersonne et ActioncandidatPersonne pour le PDF de la synthèse du bilan de parcours.
		 *
		 * @return array
		 */
		protected function _getActioncandidatPersonneData($data ) {
			$query = array (
				'recursive' => 0,
				'conditions' => array (
					'ActioncandidatPersonne.personne_id' => $data['Personne']['id'],
					'ActioncandidatPersonne.positionfiche NOT LIKE \'annule\' '
				)
			);
			$tmpDATA = $this->Bilanparcours66->Personne->ActioncandidatPersonne->find ( 'all', $query);
			if (!$tmpDATA) {
				foreach($this->Bilanparcours66->Personne->ActioncandidatPersonne->fields() AS $field ){
					$field = substr($field, strlen('ActioncandidatPersonne')+1);
					$tmpDATA[0]['ActioncandidatPersonne'][$field] = NULL;
				}
				foreach($this->Bilanparcours66->Personne->Actioncandidat->fields() AS $field ){
					$field = substr($field, strlen('Actioncandidat')+1);
					$tmpDATA[0]['Actioncandidat'][$field] = NULL;
				}
			}

			foreach ($tmpDATA as $key => $arrayVals) {
				$tmpDATA[$key]['Actioncandidatpersonne'] = $arrayVals['ActioncandidatPersonne'];
				unset($tmpDATA[$key]['ActioncandidatPersonne']);
			}

			return $tmpDATA;
		}

		/**
		 * Récupérer et Transfomer les données Entretien pour le PDF de la synthèse du bilan de parcours.
		 *
		 * @return array
		 */
		protected function _getEntretiensData($data ) {
			$query = array (
				'recursive' => -1,
				'conditions' => array (
					'Entretien.personne_id' => $data['Personne']['id'],
				)
			);
			$tmpDATA = $this->Bilanparcours66->Personne->Entretien->find ( 'all', $query);

			if (!$tmpDATA) {
				foreach($this->Bilanparcours66->Personne->Entretien->fields() AS $field ){
					$field = substr($field, strlen('Entretien')+1);
					$tmpDATA[0]['Entretien'][$field] = NULL;
				}
			}

			return $tmpDATA;
		}

		/**
		 * Récupere le PDF par défault pour l'impression du bilan de parcours
		 *
		 * @param integer $id
		 * @return string
		 */
		public function getDefaultPdf( $id ) {
			//Get data
			$data = $this->getDataForPdf( $id );
			//Get Model
			$modeleodt = $this->modeleOdt( $data );
			//Get Options
			$options = $this->getOptionsForPdf();
			//Get Status Propositions
			$proposition = Hash::get( $data, 'Bilanparcours66.proposition' );
			//Transform Data
			$data = $this->_transformPDFData($data, $proposition );
			//Choix Model par default
			$typeformulaire = Set::classicExtract( $data, 'Bilanparcours66.typeformulaire' );
			if( $typeformulaire == 'pe' ) {
				if( $proposition == 'parcourspe' ) {
					$modeleodt = 'Bilanparcours/bilanparcourspe_parcours.odt';
				}
				else {
					$modeleodt = 'Bilanparcours/bilanparcourspe_audition.odt';
				}
			}
			return $this->Bilanparcours66->ged( $data, $modeleodt, false, $options );
		}

		/**
		 * Récupere le PDF d'un modèle odt donné pour l'impression du bilan de parcours
		 *
		 * @param integer $id
		 * @return string
		 */
		public function getPdfModelODT( $id, $modeleOdt ) {
			//Get data
			$querydata = $this->getDataForPdf( $id, true );
			$querydata['fields'] =
			array_merge(
				$querydata['fields'],
				$this->Bilanparcours66->Personne->Foyer->Dossier->Detaildroitrsa->fields(),
				$this->Bilanparcours66->Personne->Prestation->fields()
			);
			$querydata['joins'][] =
				$this->Bilanparcours66->Personne->join( 'Prestation', array( 'type' => 'INNER' ) );
			$querydata['joins'][] =
				$this->Bilanparcours66->Personne->Foyer->Dossier->join( 'Detaildroitrsa', array( 'type' => 'LEFT OUTER' ) );

			$data = $this->Bilanparcours66->find( 'first', $querydata );

			//Get Options
			$options = $this->getOptionsForPdf();
			//Get Status Propositions
			$proposition = Hash::get( $data, 'Bilanparcours66.proposition' );
			//Transform Data
			$data = $this->_transformPDFData($data, $proposition );

			//Get Manifestations
			$Manifestations = $this->_getManifestationsData($data);

			//Get ActioncandidatPersonnes
			$ActioncandidatPersonne = $this->_getActioncandidatPersonneData($data);

			//Get Entretiens
			$Entretien = $this->_getEntretiensData($data);

			//Merge arrays
			$tmp = array_merge (
				$Manifestations,
				array (
					'Actioncandidatpersonne' => $ActioncandidatPersonne,
					'Entretien' => $Entretien
				)
			);

			//Merge array to data
			$data = array_merge (
				array( $data ),
				$tmp
			);

			return $this->Bilanparcours66->ged( $data, $modeleOdt, true, $options );
		}

		/**
		 * 	On cherche le nombre de dossiers d'EP pour une personne concernant une thématique donnée
		 * 	qui :
		 * 		- ne sont pas liés à un passage en EP (non lié à une commission)
		 * 		OU
		 * 		- sont liés à un passage en EP (lié à une commission) ET qui sont dans l'état traité ou annulé
		 *
		 * @param string $themeep
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function ajoutPossibleThematique66( $themeep, $personne_id ) {
			$Dossierep = ClassRegistry::init( 'Dossierep' );
			$count = $Dossierep->find(
				'count',
				array(
					'conditions' => array(
						'Dossierep.actif' => '1',
						'Dossierep.personne_id' => $personne_id,
						'Dossierep.themeep' => $themeep,
						'OR' => array(
							'Dossierep.id NOT IN ('
								.$Dossierep->Passagecommissionep->sq(
									array(
										'alias' => 'passagescommissionseps',
										'fields' => array( 'passagescommissionseps.dossierep_id' ),
										'conditions' => array(
											'passagescommissionseps.dossierep_id = Dossierep.id'
										),
										'contain' => false
									)
								)
							.')',
							'Dossierep.id IN ('
								.$Dossierep->Passagecommissionep->sq(
									array(
										'alias' => 'passagescommissionseps',
										'fields' => array( 'passagescommissionseps.dossierep_id' ),
										'conditions' => array(
											'passagescommissionseps.dossierep_id = Dossierep.id',
											'NOT' => array(
												'passagescommissionseps.etatdossierep' => array( 'traite', 'annule', 'reporte' )
											)
										),
										'contain' => false
									)
								)
							.')',

						)
					),
					'contain' => false
				)
			);

			return ( $count == 0 );
		}

		/**
		 * Retourne l'ensemble de données liées au Bilan de parcours en cours
		 *
		 * @param integer $id Id du Bilan de parcours
		 * @return array
		 */
		public function dataView( $bilanparcours66_id ) {
			$Informationpe = ClassRegistry::init( 'Informationpe' );


            // Liste des champs par étape de décisions pour le passage en EPL Parcours du bilan
            $fieldsDecisionsaisinebilanparcoursep66 = $this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->Decisionsaisinebilanparcoursep66->fields();
            $fieldsDecisionsaisinebilanparcoursep66ep = array_words_replace(
                $fieldsDecisionsaisinebilanparcoursep66,
                array(
                    'Decisionsaisinebilanparcoursep66' => 'Decisionsaisinebilanparcoursep66ep'
                )
            );
            $fieldsDecisionsaisinebilanparcoursep66cg = array_words_replace(
                $fieldsDecisionsaisinebilanparcoursep66,
                array(
                    'Decisionsaisinebilanparcoursep66' => 'Decisionsaisinebilanparcoursep66cg'
                )
            );

            $fieldsDecisiondefautinsertionep66 = $this->Bilanparcours66->Defautinsertionep66->Dossierep->Passagecommissionep->Decisiondefautinsertionep66->fields();
            $fieldsDecisiondefautinsertionep66ep = array_words_replace(
                $fieldsDecisiondefautinsertionep66,
                array(
                    'Decisiondefautinsertionep66' => 'Decisiondefautinsertionep66ep'
                )
            );
            $fieldsDecisiondefautinsertionep66cg = array_words_replace(
                $fieldsDecisiondefautinsertionep66,
                array(
                    'Decisiondefautinsertionep66' => 'Decisiondefautinsertionep66cg'
                )
            );

            // Jointure spéciale sur Dossierep suivant la thématique
            $joinSaisinebilanparcoursep66 = $this->Bilanparcours66->Saisinebilanparcoursep66->join( 'Dossierep', array( 'type' => 'LEFT OUTER' ) );
            $joinDefautinsertionep66 = $this->Bilanparcours66->Defautinsertionep66->join( 'Dossierep', array( 'type' => 'LEFT OUTER' ) );

            $joinDossierep = $joinSaisinebilanparcoursep66;
            $joinDossierep['conditions'] = array(
                'OR' => array(
                    $joinSaisinebilanparcoursep66['conditions'],
                    $joinDefautinsertionep66['conditions']
                )
            );

            // Recherche du bilan pour l'affichage
			$data = $this->Bilanparcours66->find(
				'first',
				array(
					'fields' => array_merge(
						$this->Bilanparcours66->fields(),
						$this->Bilanparcours66->Personne->fields(),
						$this->Bilanparcours66->Referent->fields(),
						$this->Bilanparcours66->Orientstruct->fields(),
						$this->Bilanparcours66->Personne->Foyer->fields(),
						$this->Bilanparcours66->Personne->Prestation->fields(),
						$this->Bilanparcours66->Personne->Foyer->Dossier->fields(),
						$this->Bilanparcours66->Personne->Foyer->Adressefoyer->Adresse->fields(),
                        $this->Bilanparcours66->Defautinsertionep66->fields(),
						$this->Bilanparcours66->Saisinebilanparcoursep66->fields(),
						$this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->fields(),
						$this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->fields(),
						$this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->Commissionep->fields(),
						$fieldsDecisionsaisinebilanparcoursep66,
                        $fieldsDecisiondefautinsertionep66,
						$this->Bilanparcours66->Dossierpcg66->fields(),
						$this->Bilanparcours66->Dossierpcg66->Decisiondossierpcg66->fields(),
						$this->Bilanparcours66->Dossierpcg66->Decisiondossierpcg66->Decisionpdo->fields(),
						$fieldsDecisionsaisinebilanparcoursep66ep,
						$fieldsDecisionsaisinebilanparcoursep66cg,
                        $fieldsDecisiondefautinsertionep66ep,
						$fieldsDecisiondefautinsertionep66cg,
						array(
							'NvTypeorient.lib_type_orient',
							'Structurereferente.lib_struc',
							'NvStructurereferente.lib_struc',
							'Serviceinstructeur.lib_service',
							$this->Bilanparcours66->Referent->sqVirtualField( 'nom_complet' ),
							'Historiqueetatpe.identifiantpe',
							'Historiqueetatpe.etat'
						),
						array_words_replace(
							$this->Bilanparcours66->Orientstruct->Typeorient->fields(),
							array(
								'Typeorient' => 'Typeorientorigine'
							)
						),
						array_words_replace(
							$this->Bilanparcours66->Orientstruct->Structurereferente->fields(),
							array(
								'Structurereferente' => 'Structurereferenteorigine'
							)
						),
						array_words_replace(
							$this->Bilanparcours66->Typeorientprincipale->fields(),
							array(
								'Typeorient' => 'Typeorientaccompagnement'
							)
						)
					),
					'conditions' => array(
						'Bilanparcours66.id' => $bilanparcours66_id,
						array(
							'OR' => array(
								'Adressefoyer.id IS NULL',
								'Adressefoyer.id IN ( '.$this->Bilanparcours66->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
							)
						),
						array(
							'OR' => array(
								'Historiqueetatpe.id IS NULL',
								'Historiqueetatpe.id IN( '.$Informationpe->Historiqueetatpe->sqDernier( 'Informationpe' ).' )'
							)
						),
                        array(
                            'OR' => array(
                                'Passagecommissionep.id IS NULL',
                                'Passagecommissionep.id IN ('.$this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->sqDernier().' )',
                                'NOT' => array(
                                    'Passagecommissionep.etatdossierep ' => 'reporte'
                                )
                            )
                        )
					),
					'joins' => array(
						$this->Bilanparcours66->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Bilanparcours66->join( 'Orientstruct', array( 'type' => 'LEFT OUTER' ) ),
						$this->Bilanparcours66->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
						$this->Bilanparcours66->join( 'NvStructurereferente', array( 'type' => 'LEFT OUTER' ) ),
						$this->Bilanparcours66->join( 'NvTypeorient', array( 'type' => 'LEFT OUTER' ) ),
						$this->Bilanparcours66->join( 'Serviceinstructeur', array( 'type' => 'LEFT OUTER' ) ),
						$this->Bilanparcours66->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
						$this->Bilanparcours66->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Bilanparcours66->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$this->Bilanparcours66->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$this->Bilanparcours66->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
						$this->Bilanparcours66->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', 'LEFT OUTER' ),
						$Informationpe->join( 'Historiqueetatpe', array( 'type' => 'LEFT OUTER' ) ),
						array_words_replace(
							$this->Bilanparcours66->Orientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
							array(
								'Typeorient' => 'Typeorientorigine'
							)
						),
						array_words_replace(
							$this->Bilanparcours66->join( 'Typeorientprincipale', array( 'type' => 'LEFT OUTER' ) ),
							array(
								'Typeorient' => 'Typeorientaccompagnement'
							)
						),
						array_words_replace(
							$this->Bilanparcours66->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
							array(
								'Structurereferente' => 'Structurereferenteorigine'
							)
						),
                        $this->Bilanparcours66->join( 'Saisinebilanparcoursep66', array( 'type' => 'LEFT OUTER' ) ),
                        $this->Bilanparcours66->join( 'Defautinsertionep66', array( 'type' => 'LEFT OUTER' ) ),
                        $joinDossierep,
                        $this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->join( 'Passagecommissionep', array( 'type' => 'LEFT OUTER' ) ),
                        $this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->join( 'Commissionep', array( 'type' => 'LEFT OUTER' ) ),
                        $this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->join( 'Decisionsaisinebilanparcoursep66', array( 'type' => 'LEFT OUTER' ) ),
                        $this->Bilanparcours66->Defautinsertionep66->Dossierep->Passagecommissionep->join( 'Decisiondefautinsertionep66', array( 'type' => 'LEFT OUTER' ) ),
                        array_words_replace(
                            $this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->join(
                                'Decisionsaisinebilanparcoursep66',
                                array(
                                    'type' => 'LEFT OUTER',
                                    'conditions' => array(
                                        'Decisionsaisinebilanparcoursep66.etape' => 'ep'
                                    )
                                )
                            ),
                            array(
                                'Decisionsaisinebilanparcoursep66' => 'Decisionsaisinebilanparcoursep66ep'
                            )
                        ),
                        array_words_replace(
                            $this->Bilanparcours66->Saisinebilanparcoursep66->Dossierep->Passagecommissionep->join(
                                'Decisionsaisinebilanparcoursep66',
                                array(
                                    'type' => 'LEFT OUTER',
                                    'conditions' => array(
                                        'Decisionsaisinebilanparcoursep66.etape' => 'cg'
                                    )
                                )
                            ),
                            array(
                                'Decisionsaisinebilanparcoursep66' => 'Decisionsaisinebilanparcoursep66cg'
                            )
                        ),
                        array_words_replace(
                            $this->Bilanparcours66->Defautinsertionep66->Dossierep->Passagecommissionep->join(
                                'Decisiondefautinsertionep66',
                                array(
                                    'type' => 'LEFT OUTER',
                                    'conditions' => array(
                                        'Decisiondefautinsertionep66.etape' => 'ep'
                                    )
                                )
                            ),
                            array(
                                'Decisiondefautinsertionep66' => 'Decisiondefautinsertionep66ep'
                            )
                        ),
                        array_words_replace(
                            $this->Bilanparcours66->Defautinsertionep66->Dossierep->Passagecommissionep->join(
                                'Decisiondefautinsertionep66',
                                array(
                                    'type' => 'LEFT OUTER',
                                    'conditions' => array(
                                        'Decisiondefautinsertionep66.etape' => 'cg'
                                    )
                                )
                            ),
                            array(
                                'Decisiondefautinsertionep66' => 'Decisiondefautinsertionep66cg'
                            )
                        ),
                        $this->Bilanparcours66->join( 'Dossierpcg66', array( 'type' => 'LEFT OUTER' ) ),
                        $this->Bilanparcours66->Dossierpcg66->join(
                            'Decisiondossierpcg66',
                            array(
                                'type' => 'LEFT OUTER',
                                'order' => array( 'Decisiondossierpcg66.created DESC' ),
                                'conditions' => array(
                                    'Decisiondossierpcg66.validationproposition' => 'O',
                                    'Decisiondossierpcg66.etatop' => 'transmis'
                                )
                            )
                        ),
                        $this->Bilanparcours66->Dossierpcg66->Decisiondossierpcg66->join( 'Decisionpdo', array( 'type' => 'LEFT OUTER' ) )
					),
					'contain' => false
				)
			);

			return $data;
		}

		/**
		 * Liste des options envoyées à la vue pour le Bilan de parcours 66
		 *
		 * @return array
		 */
		public function optionsView() {
			// Options
			$options = array(
				'Prestation' => array(
					'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers')
				),
				'Personne' => array(
					'qual' => ClassRegistry::init( 'Option' )->qual()
				),
				'Serviceinstructeur' => array(
					'typeserins' => ClassRegistry::init( 'Option' )->typeserins()
				),
				'Foyer' => array(
					'sitfam' => ClassRegistry::init( 'Option' )->sitfam()
				),
				'Bilanparcours66' => array(
					'duree_engag' => ClassRegistry::init( 'Option' )->duree_engag()
				),
			);
			$options = Set::merge(
				$this->Bilanparcours66->enums(),
				$options
			);
			return $options;

		}

		/**
		 * Permet d'obtenir le nombre de manifestations liées aux bilans d'une personne.
		 *
		 * @param integer $personne_id
		 * @return integer
		 */
		public function sqNbManifestations( $fieldId = 'Bilanparcours66.id', $fieldName = null, $modelAlias = null ){
			$alias = Inflector::tableize( $this->Bilanparcours66->Manifestationbilanparcours66->alias );

			$modelAlias = ( is_null( $modelAlias ) ? $this->Bilanparcours66->alias : $modelAlias );

			$sq = $this->Bilanparcours66->Manifestationbilanparcours66->sq(
				array(
					'fields' => array(
						"COUNT( {$alias}.id )"
					),
					'alias' => $alias,
					'conditions' => array(
						"{$alias}.bilanparcours66_id = $fieldId"
					)
				)
			);

			if( !is_null( $fieldName ) ) {
				$sq = "( {$sq} ) AS \"{$modelAlias}__{$fieldName}\"";
			}

			return $sq;
		}

		/**
		 * Renvoi le querydata pour l'index des bilans de parcours
		 *
		 * @return array
		 */
		public function getIndexQuery() {
			return $this->completeQueryDataForEps(
				array(
					'fields' => array(
						'Bilanparcours66.id',
						'Bilanparcours66.datebilan',
						'Bilanparcours66.positionbilan',
						'Bilanparcours66.motifannulation',
						'Serviceinstructeur.lib_service',
						'Structurereferente.lib_struc',
						$this->Bilanparcours66->Referent->sqVirtualField('nom_complet'),
						'Bilanparcours66.proposition',

						// Même colonne, ne pas fusionner pour utiliser les options
						'Bilanparcours66.examenauditionpe',
						'Bilanparcours66.examenaudition',
						'Bilanparcours66.choixparcours',

						// Proposition (note : pas de proposition dans le cas d'une thématique Audition/Defautinsertionep66)
						'ParcoursPropositionTypeorient.lib_type_orient',
						'ParcoursPropositionStructurereferente.lib_struc',

						// Greffe de la décision du dossier PCG lié (CGA)
						'Decisionpdo.libelle',
						'Dossierpcg66.etatdossierpcg',

						$this->Bilanparcours66->Fichiermodule->sqNbFichiersLies( $this->Bilanparcours66, 'nb_fichiers' ),
						$this->Bilanparcours66->WebrsaBilanparcours66->sqNbManifestations( 'Bilanparcours66.id', 'nb_manifestations' )
					),
					'joins' => array(
						$this->Bilanparcours66->join('Serviceinstructeur'),
						$this->Bilanparcours66->join('Structurereferente'),
						$this->Bilanparcours66->join('Referent'),
						$this->Bilanparcours66->join('Dossierpcg66'),
						$this->Bilanparcours66->Dossierpcg66->join(
							'Decisiondossierpcg66',
							array(
								'type' => 'LEFT',
								'conditions' => array(
									'Decisiondossierpcg66.id IN ('
									. $this->Bilanparcours66->Dossierpcg66->Decisiondossierpcg66
										->WebrsaDecisiondossierpcg66->sqDernier('Dossierpcg66.id') . ')'
								)
							)
						),
						$this->Bilanparcours66->Dossierpcg66->Decisiondossierpcg66->join('Decisionpdo'),
					),
					'contain' => false,
					'order' => array(
						'Bilanparcours66.datebilan' => 'DESC',
						'Bilanparcours66.id' => 'DESC'
					)
				)
			);
		}

		/**
		 * Fait les jointures sur les EPs liés, avec séparation par etape et par thématique.
		 *
		 * @param array $query
		 * @return array
		 */
		public function completeQueryDataForEps($query = array()) {
			// Thématique Saisinebilanparcoursep66 (commission Parcours)
			$jointuresParcours = array(
				$this->Bilanparcours66->join('Saisinebilanparcoursep66'),
				array(
					'table' => '"dossierseps"',
					'alias' => 'ParcoursDossierep',
					'type' => 'LEFT',
					'conditions' => '"Saisinebilanparcoursep66"."dossierep_id" = "ParcoursDossierep"."id"'
				),
				array(
					'table' => '"passagescommissionseps"',
					'alias' => 'ParcoursPassagecommissionep',
					'type' => 'LEFT',
					'conditions' => '"ParcoursPassagecommissionep"."dossierep_id" = "ParcoursDossierep"."id" '

					// @see Passagecommissionep::sqDernier()
					. 'AND "ParcoursPassagecommissionep"."id" IN ('
						. 'SELECT "passagescommissionseps"."id" AS passagescommissionseps__id '
						. 'FROM passagescommissionseps AS passagescommissionseps '
						. 'INNER JOIN "public"."commissionseps" AS commissionseps '
							. 'ON ("passagescommissionseps"."commissionep_id" = "commissionseps"."id") '
						. 'WHERE "passagescommissionseps"."dossierep_id" = "ParcoursDossierep"."id" '
						. 'ORDER BY "commissionseps"."dateseance" DESC, "commissionseps"."id" DESC '
						. 'LIMIT 1) '

					. 'AND "ParcoursPassagecommissionep"."etatdossierep" IN (\'traite\', \'annule\', \'reporte\')'
				),

				// Proposition
				array(
					'alias' => 'ParcoursPropositionTypeorient',
					'table' => '"typesorients"',
					'type' => 'LEFT',
					'conditions' => '"Saisinebilanparcoursep66"."typeorient_id" = "ParcoursPropositionTypeorient"."id"'
				),
				array(
					'table' => '"structuresreferentes"',
					'alias' => 'ParcoursPropositionStructurereferente',
					'type' => 'LEFT',
					'conditions' => '"Saisinebilanparcoursep66"."structurereferente_id" '
					. '= "ParcoursPropositionStructurereferente"."id"'
				),

				// Avis
				array(
					'table' => '"decisionssaisinesbilansparcourseps66"',
					'alias' => 'ParcoursAvis',
					'type' => 'LEFT',
					'conditions' => '"ParcoursAvis"."passagecommissionep_id" = "ParcoursPassagecommissionep"."id" '
					. 'AND "ParcoursAvis"."etape" = \'ep\''
				),
				array(
					'alias' => 'ParcoursAvisTypeorient',
					'table' => '"typesorients"',
					'type' => 'LEFT',
					'conditions' => '"ParcoursAvis"."typeorient_id" = "ParcoursAvisTypeorient"."id"'
				),
				array(
					'table' => '"structuresreferentes"',
					'alias' => 'ParcoursAvisStructurereferente',
					'type' => 'LEFT',
					'conditions' => '"ParcoursAvis"."structurereferente_id" '
					. '= "ParcoursAvisStructurereferente"."id"'
				),

				// Décision
				array(
					'table' => '"decisionssaisinesbilansparcourseps66"',
					'alias' => 'ParcoursDecision',
					'type' => 'LEFT',
					'conditions' => '"ParcoursDecision"."passagecommissionep_id" = "ParcoursPassagecommissionep"."id" '
					. 'AND "ParcoursDecision"."etape" = \'cg\''
				),
				array(
					'alias' => 'ParcoursDecisionTypeorient',
					'table' => '"typesorients"',
					'type' => 'LEFT',
					'conditions' => '"ParcoursDecision"."typeorient_id" = "ParcoursDecisionTypeorient"."id"'
				),
				array(
					'table' => '"structuresreferentes"',
					'alias' => 'ParcoursDecisionStructurereferente',
					'type' => 'LEFT',
					'conditions' => '"ParcoursDecision"."structurereferente_id" '
					. '= "ParcoursDecisionStructurereferente"."id"'
				),
			);

			// Thématique Defautinsertionep66 (commission Audition)
			$jointuresAudition = array(
				$this->Bilanparcours66->join('Defautinsertionep66'),
				array(
					'table' => '"dossierseps"',
					'alias' => 'AuditionDossierep',
					'type' => 'LEFT',
					'conditions' => '"Defautinsertionep66"."dossierep_id" = "AuditionDossierep"."id"'
				),
				array(
					'table' => '"passagescommissionseps"',
					'alias' => 'AuditionPassagecommissionep',
					'type' => 'LEFT',
					'conditions' => '"AuditionPassagecommissionep"."dossierep_id" = "AuditionDossierep"."id" '

					// @see Passagecommissionep::sqDernier()
					. 'AND "AuditionPassagecommissionep"."id" IN ('
						. 'SELECT "passagescommissionseps"."id" AS passagescommissionseps__id '
						. 'FROM passagescommissionseps AS passagescommissionseps '
						. 'INNER JOIN "public"."commissionseps" AS commissionseps '
							. 'ON ("passagescommissionseps"."commissionep_id" = "commissionseps"."id") '
						. 'WHERE "passagescommissionseps"."dossierep_id" = "AuditionDossierep"."id" '
						. 'ORDER BY "commissionseps"."dateseance" DESC, "commissionseps"."id" DESC '
						. 'LIMIT 1) '

					. 'AND "AuditionPassagecommissionep"."etatdossierep" IN (\'traite\', \'annule\', \'reporte\')'
				),

				// Avis
				array(
					'table' => '"decisionsdefautsinsertionseps66"',
					'alias' => 'AuditionAvis',
					'type' => 'LEFT',
					'conditions' => '"AuditionAvis"."passagecommissionep_id" = "AuditionPassagecommissionep"."id" '
					. 'AND "AuditionAvis"."etape" = \'ep\''
				),
				array(
					'alias' => 'AuditionAvisTypeorient',
					'table' => '"typesorients"',
					'type' => 'LEFT',
					'conditions' => '"AuditionAvis"."typeorient_id" = "AuditionAvisTypeorient"."id"'
				),
				array(
					'table' => '"structuresreferentes"',
					'alias' => 'AuditionAvisStructurereferente',
					'type' => 'LEFT',
					'conditions' => '"AuditionAvis"."structurereferente_id" '
					. '= "AuditionAvisStructurereferente"."id"'
				),

				// Décision
				array(
					'table' => '"decisionsdefautsinsertionseps66"',
					'alias' => 'AuditionDecision',
					'type' => 'LEFT',
					'conditions' => '"AuditionDecision"."passagecommissionep_id" = "AuditionPassagecommissionep"."id" '
					. 'AND "AuditionDecision"."etape" = \'cg\''
				),
				array(
					'alias' => 'AuditionDecisionTypeorient',
					'table' => '"typesorients"',
					'type' => 'LEFT',
					'conditions' => '"AuditionDecision"."typeorient_id" = "AuditionDecisionTypeorient"."id"'
				),
				array(
					'table' => '"structuresreferentes"',
					'alias' => 'AuditionDecisionStructurereferente',
					'type' => 'LEFT',
					'conditions' => '"AuditionDecision"."structurereferente_id" '
					. '= "AuditionDecisionStructurereferente"."id"'
				),
			);

			return array(
				'fields' => array_merge(
					(array)Hash::get($query, 'fields'),
					array(
						// Avis
						'(CASE WHEN "Saisinebilanparcoursep66"."id" IS NOT NULL '
							. 'THEN "ParcoursAvisTypeorient"."lib_type_orient" '
							. 'ELSE "AuditionAvisTypeorient"."lib_type_orient" '
						. 'END) AS "Avis__lib_type_orient"',

						'(CASE WHEN "Saisinebilanparcoursep66"."id" IS NOT NULL '
							. 'THEN "ParcoursAvisStructurereferente"."lib_struc" '
							. 'ELSE "AuditionAvisStructurereferente"."lib_struc" '
						. 'END) AS "Avis__lib_struc"',

						'(CASE WHEN "Saisinebilanparcoursep66"."id" IS NOT NULL '
							. 'THEN "ParcoursAvis"."decision"::text '
							. 'ELSE "AuditionAvis"."decision"::text '
						. 'END) AS "Avis__decision"',

						'(COALESCE("ParcoursAvis"."commentaire", "AuditionAvis"."commentaire")) AS "Avis__commentaire"',

						'(CASE WHEN COALESCE("ParcoursAvis"."commentaire", "AuditionAvis"."commentaire") IS NOT NULL '
							. 'THEN TRUE '
							. 'ELSE FALSE '
						. 'END) AS "Avis__havecommentaire"',

						'(CASE WHEN "Saisinebilanparcoursep66"."id" IS NOT NULL '
							. "THEN 'Parcours' "
							. 'ELSE CASE WHEN "Defautinsertionep66"."id" IS NOT NULL '
								. "THEN 'Audition' "
								. "ELSE NULL END "
						. 'END) AS "Avis__thematique"',

						// A fusionner avec Avis.decision (uniquement pour type Audition)
						'AuditionAvis.decisionsup',

						// Décision
						'(CASE WHEN "Saisinebilanparcoursep66"."id" IS NOT NULL '
							. 'THEN "ParcoursDecisionTypeorient"."lib_type_orient" '
							. 'ELSE "AuditionDecisionTypeorient"."lib_type_orient" '
						. 'END) AS "Decision__lib_type_orient"',

						'(CASE WHEN "Saisinebilanparcoursep66"."id" IS NOT NULL '
							. 'THEN "ParcoursDecisionStructurereferente"."lib_struc" '
							. 'ELSE "AuditionDecisionStructurereferente"."lib_struc" '
						. 'END) AS "Decision__lib_struc"',

						'(CASE WHEN "Saisinebilanparcoursep66"."id" IS NOT NULL '
							. 'THEN "ParcoursDecision"."decision"::text '
							. 'ELSE "AuditionDecision"."decision"::text '
						. 'END) AS "Decision__decision"',

						'(COALESCE("ParcoursDecision"."commentaire", "AuditionDecision"."commentaire")) '
						. 'AS "Decision__commentaire"',

						'(CASE WHEN COALESCE("ParcoursDecision"."commentaire", "AuditionDecision"."commentaire") IS NOT NULL '
							. 'THEN TRUE '
							. 'ELSE FALSE '
						. 'END) AS "Decision__havecommentaire"',

						// évite les doublons de commentaire
						'(COALESCE("ParcoursDecision"."commentaire", "AuditionDecision"."commentaire") '
						. '= COALESCE("ParcoursAvis"."commentaire", "AuditionAvis"."commentaire")) '
						. 'AS "Decision__commentaire_is_equal"',
					)
				),
				'joins' => array_merge(
					Hash::get($query, 'joins'),
					$jointuresParcours,
					$jointuresAudition
				)
			) + $query;
		}

		/**************************************************************************************************************/

		/**
		 * Retourne les positions et les conditions CakePHP/SQL dans l'ordre dans
		 * lequel elles doivent être traitées pour récupérer la position actuelle.
		 *
		 * @return array
		 */
		protected function _getConditionsPositions() {
			$sqAttenteCga = "EXISTS("
				. "SELECT a.id FROM bilansparcours66 a "
				. "JOIN defautsinsertionseps66 b ON b.bilanparcours66_id = a.id "
				. "JOIN passagescommissionseps d ON d.dossierep_id = b.dossierep_id "
				. "JOIN decisionsdefautsinsertionseps66 ep "
					. "ON ep.passagecommissionep_id = d.id AND ep.etape = 'ep' AND ep.decision NOT IN ('annule', 'reporte') "
				. "LEFT JOIN decisionsdefautsinsertionseps66 cg "
					. "ON cg.passagecommissionep_id = d.id AND cg.etape = 'cg' AND cg.decision NOT IN ('annule', 'reporte') "
				. 'WHERE a.id = "bilansparcours66"."id" '
				. "AND cg.id IS NULL)"
			;

			$sqAttenteCt = "EXISTS("
				. "SELECT a.id FROM bilansparcours66 a "
				. "JOIN saisinesbilansparcourseps66 b ON b.bilanparcours66_id = a.id "
				. "JOIN passagescommissionseps d ON d.dossierep_id = b.dossierep_id "
				. "JOIN decisionssaisinesbilansparcourseps66 ep "
					. "ON ep.passagecommissionep_id = d.id AND ep.etape = 'ep' AND ep.decision NOT IN ('annule', 'reporte') "
				. "LEFT JOIN decisionssaisinesbilansparcourseps66 cg "
					. "ON cg.passagecommissionep_id = d.id AND cg.etape = 'cg' AND cg.decision NOT IN ('annule', 'reporte') "
				. 'WHERE a.id = "bilansparcours66"."id" '
				. "AND cg.id IS NULL)"
			;

			$sqTraite = array('OR' => array(
				"EXISTS("
				. "SELECT a.id FROM bilansparcours66 a "
				. "JOIN defautsinsertionseps66 b ON b.bilanparcours66_id = a.id "
				. "JOIN passagescommissionseps d ON d.dossierep_id = b.dossierep_id "
				. "JOIN commissionseps e ON d.commissionep_id = e.id "
				. "JOIN decisionsdefautsinsertionseps66 cg ON cg.passagecommissionep_id = d.id AND cg.etape = 'cg' "
				. 'WHERE a.id = "bilansparcours66"."id" '
				. "AND cg.decision NOT IN ('annule', 'reporte') "
				. "AND e.etatcommissionep = 'traite' "
				. "AND d.etatdossierep = 'traite')",

				"EXISTS("
				. "SELECT a.id FROM bilansparcours66 a "
				. "JOIN saisinesbilansparcourseps66 b ON b.bilanparcours66_id = a.id "
				. "JOIN passagescommissionseps d ON d.dossierep_id = b.dossierep_id "
				. "JOIN commissionseps e ON d.commissionep_id = e.id "
				. "JOIN decisionssaisinesbilansparcourseps66 cg ON cg.passagecommissionep_id = d.id AND cg.etape = 'cg' "
				. 'WHERE a.id = "bilansparcours66"."id" '
				. "AND cg.decision NOT IN ('annule', 'reporte') "
				. "AND e.etatcommissionep = 'traite' "
				. "AND d.etatdossierep = 'traite')",
			));

			$return = array(
				// Annulé
				'annule' => array(
					$this->Bilanparcours66->alias.'.positionbilan' => 'annule',
				),

				// Traité
				'traite' => array(
					"OR" => array(
						$this->Bilanparcours66->alias.'.positionbilan' => 'traite',
						$sqTraite
					)
				),

				// En attente de décision de la CGA
				'attcga' => array(
					$sqAttenteCga
				),

				// En attente de décision du Coordonnateur Technique
				'attct' => array(
					$sqAttenteCt
				),

				// En attente de l'avis de l'EPL Audition
				'eplaudit' => array(
					$this->Bilanparcours66->alias.'.proposition' => array('audition', 'auditionpe'),
				),

				// En attente de l'avis de l'EPL Parcours
				'eplparc' => array(
					$this->Bilanparcours66->alias.'.proposition' => array('parcours', 'parcourspe'),
				),

				// Reporté
				'ajourne' => array(
					$this->Bilanparcours66->alias.'.positionbilan' => 'ajourne',
				),
			);

			return $return;
		}

		/**
		 * Retourne une CASE (PostgreSQL) pemettant de connaître la position
		 *
		 * @return string
		 */
		public function getCasePosition() {
			$switch = '';
			$Dbo = $this->getDataSource();

			foreach ($this->_getConditionsPositions() as $position => $conditions) {
				$switch .= "WHEN {$Dbo->conditions($conditions, true, false)} THEN '{$position}' ";
			}

			return "(CASE {$switch} ELSE NULL END)";
		}

		/**
		 * Mise à jour des positions
		 *
		 * @param array $conditions
		 * @return boolean
		 */
		public function updatePositionsByConditions(array $conditions) {
			if (!$this->Bilanparcours66->find('first', array('conditions' => $conditions))) {
				return true;
			}

			$success = $this->Bilanparcours66->updateAllUnBound(
				array(
					$this->Bilanparcours66->alias.'.positionbilan' => $this->getCasePosition()
				),
				$conditions
			);
Debugger::log(array($this->Bilanparcours66->alias.'.positionbilan' => $this->getCasePosition()));
Debugger::log($conditions);
			return $success;
		}

		/**
		 * Permet de mettre à jour les positions
		 *
		 * @param integer $id La clé primaire
		 * @return boolean
		 */
		public function updatePositionsById($id) {
			return $this->updatePositionsByConditions(
				array($this->Bilanparcours66->alias.".id" => $id)
			);
		}
	}