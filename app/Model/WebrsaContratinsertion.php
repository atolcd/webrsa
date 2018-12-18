<?php
	/**
	 * Code source de la classe WebrsaContratinsertion.
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
	 * La classe WebrsaContratinsertion possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaContratinsertion extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaContratinsertion';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Contratinsertion');

		/**
		 * Mémorise le résultat d'une fonction en cas d'appels succéssifs de celles-ci
		 *
		 * @var array - array(__FUNCTION__.'.'.md5(json_encode(array($param1, $param2, ...))) => $results)
		 */
		private $_mem = array();

		/**
		 * Permet d'obtenir la clef pour le stockage du résultat de fonction en fonction des paramètres
		 *
		 * @param String $functionName
		 * @param mixed $params
		 * @return String
		 */
		private function _getKeyMem($functionName, $params = 'empty') {
			return $functionName.'.'.md5(json_encode($params));
		}

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$departement = (int)Configure::read('Cg.departement');
			$fields = array(
				'positioncer' => 'Contratinsertion.positioncer',
				'datenotification' => 'Contratinsertion.datenotification',
				'dd_ci' => 'Contratinsertion.dd_ci',
				'df_ci' => 'Contratinsertion.df_ci',
				'decision_ci' => 'Contratinsertion.decision_ci',
				'forme_ci' => 'Contratinsertion.forme_ci',
				'Personne.age' => $this->Contratinsertion->Personne->sqVirtualfield('age'),
				'Contratinsertion.dernier' => $this->Contratinsertion->sqVirtualfield('dernier'),
			);

			$query['joins'] = isset($query['joins']) ? $query['joins'] : array();
			$joinsAvailables = Hash::extract($query, 'joins.{n}.alias');

			if (!in_array('Personne', $joinsAvailables)) {
				$query['joins'][] = $this->Contratinsertion->join('Personne');
			}

			if ($departement === 66) {
				$fields['Propodecisioncer66.isvalidcer'] = 'Propodecisioncer66.isvalidcer';
				if (!in_array('Propodecisioncer66', $joinsAvailables)) {
					$query['joins'][] = $this->Contratinsertion->join('Propodecisioncer66');
				}
			} elseif ($departement === 58) {
				$fields['Passagecommissionep.etatdossierep'] = 'Passagecommissionep.etatdossierep';

				if (!in_array('Sanctionep58', $joinsAvailables)) {
					$query['joins'][] = $this->Contratinsertion->join('Sanctionep58');
				}
				if (!in_array('Dossierep', $joinsAvailables)) {
					$query['joins'][] = $this->Contratinsertion->Sanctionep58->join('Dossierep',
						array(
							'conditions' => array(
								'Dossierep.actif' => 1,
								'Dossierep.themeep' => 'sanctionseps58',
								'Dossierep.id NOT IN ( ' . $this->Contratinsertion->Sanctionep58->Dossierep->Passagecommissionep->sq(
										array(
											'alias' => 'passagescommissionseps',
											'fields' => array(
												'passagescommissionseps.dossierep_id'
											),
											'conditions' => array(
												'passagescommissionseps.etatdossierep' => array('traite', 'annule')
											)
										)
								) . ' )'
							)
						)
					);
				}
				if (!in_array('Passagecommissionep', $joinsAvailables)) {
					$query['joins'][] = $this->Contratinsertion->Sanctionep58->Dossierep->join('Passagecommissionep');
				}
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
					'Contratinsertion.id',
					'Contratinsertion.personne_id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Contratinsertion->join('Personne')
				),
				'contain' => false,
				'order' => array(
					'Contratinsertion.dd_ci' => 'DESC',
					'Contratinsertion.df_ci' => 'DESC',
					'Contratinsertion.id' => 'DESC',
				)
			);

			$results = $this->Contratinsertion->find('all', $this->completeVirtualFieldsForAccess($query));
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
			$results = $this->haveNeededDatas($personne_id);

			if (in_array('haveSanctionep', $params)) {
				$querydata = $this->qdThematiqueEp('Sanctionep58', $personne_id);
				$querydata['fields'] = 'Sanctionep58.id';
				$sanctionseps58 = $this->Contratinsertion->Signalementep93->Dossierep->find('first', $querydata);
				$results['haveSanctionep'] = !empty($sanctionseps58);
			}
			if (in_array('erreursCandidatePassage', $params)) {
				$results['erreursCandidatePassage'] = $this->Contratinsertion
					->Sanctionep58->Dossierep->getErreursCandidatePassage($personne_id)
				;
			}
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
			$departement = Configure::read('Cg.departement');
			extract($this->haveNeededDatas($personne_id));

			// Spécial CG
			$cgCond = true;
			if ($departement === 58) {
				$cgCond = $isSoumisdroitetdevoir;
			}

			return $haveOrient && !$haveOrientEmploi && !$haveCui && !$haveDossiercovnonfinal && $cgCond;
		}

		/**
		 * Vérifi la présence ou non, d'enregistrements sur d'autres tables qui
		 * influent sur la possibilitée d'ajout d'un contrat insertion
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function haveNeededDatas($personne_id) {
			$memKey = $this->_getKeyMem(__FUNCTION__, func_get_args());
			if (!isset($this->_mem[$memKey])) {
				$departement = (int)Configure::read('Cg.departement');
				$typeOrientPrincipaleEmploiId = Hash::get((array)Configure::read('Orientstruct.typeorientprincipale.Emploi'), 0);

				if ($typeOrientPrincipaleEmploiId === null) {
					$typeOrientPrincipaleEmploiId = Configure::read('Typeorient.emploi_id');
					if ($typeOrientPrincipaleEmploiId === null) {
						trigger_error(__('Le type orientation principale Emploi n\'est pas bien défini.'), E_USER_WARNING);
						$typeOrientPrincipaleEmploiId = 'NULL';
					}
				}

				$Personne =& $this->Contratinsertion->Personne;

				/**
				 * Query
				 */
				$query = array(
					'fields' => array(
						'("Orientstruct"."id" IS NOT NULL) AS "Personne__haveoriente"',
						'("Typeorient"."id" IS NOT NULL) AS "Personne__haveoriente_emploi"',
						'Typeorient.parentid'
					),
					'joins' => array(
						$Personne->join(
							'Orientstruct', array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Orientstruct.statut_orient' => 'Orienté',
									'Orientstruct.id IN ('.$Personne->Orientstruct->WebrsaOrientstruct->sqDerniere('Orientstruct.personne_id').')'
								)
							)
						),
						$Personne->Orientstruct->join(
							'Typeorient', array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'OR' => array(
										array(
											'Typeorient.parentid IS NULL',
											'Typeorient.id' => $typeOrientPrincipaleEmploiId,
										),
										'Typeorient.parentid' => $typeOrientPrincipaleEmploiId
									)
								)
							)
						),

					),
					'contain' => false,
					'conditions' => array(
						'Personne.id' => $personne_id,
					)
				);

				if ($departement === 66) {
					$query['fields'][] = '("Cui66"."id" IS NOT NULL) AS "Personne__havecui"';
					$query['joins'][] = $Personne->join('Cui');
					$query['joins'][] = $Personne->Cui->join(
						'Cui66', array(
							'conditions' => array(
								'NOT' => array(
									'Cui66.etatdossiercui66' => array(
										'perime', 'rupturecontrat', 'decisionsanssuite', 'nonvalide', 'annule'
									)
								)
							)
						)
					);
				}

				if ($departement === 58) {
					$dossiercov = $this->Contratinsertion->Personne->Dossiercov58->qdDossiersNonFinalises(
						$personne_id, 'proposcontratsinsertioncovs58'
					);

					$query['fields'][] = '("Dossiercov58"."id" IS NOT NULL) AS "Personne__havedossiercovnonfinal"';
					$query['joins'][] = $Personne->join('Dossiercov58',
						array('conditions' => $dossiercov['conditions'])
					);

					$query['fields'][] = '("Structurereferente"."typestructure" = \'oa\') AS "Personne__needReorientationsociale"';
					$query['joins'][] = $Personne->Orientstruct->join('Structurereferente');

					// Au CD 58, on ne bloque pas pour les structures débouchant sur CER pro (ATTENTION: surcharge du champ virtuel Personne.haveoriente_emploi)
					$query['fields'][] = '( "Typeorient"."id" IS NOT NULL AND "Structurereferente"."typestructure" <> \'msp\' ) AS "Personne__haveoriente_emploi"';
				}

				/**
				 * Find
				 */
				$record = $Personne->find('first', $query);

				/**
				 * Résultats
				 */
				$record['Personne']['isSoumisdroitetdevoir'] =
					$this->Contratinsertion->Personne->Calculdroitrsa->isSoumisAdroitEtDevoir($personne_id)
				;

				if ($departement === 58) {
					if ($this->limiteCumulDureeCER($personne_id) >= 12) {
						$demandedemaintien = $this->Contratinsertion->Personne->Dossiercov58->qdDossiersNonFinalises(
							$personne_id, 'proposnonorientationsproscovs58'
						);
						$demandedemaintien['fields'] = 'Dossiercov58.id';
						$demandeCovNonFinal = $this->Contratinsertion->Personne->Dossiercov58->find(
							'first', $demandedemaintien
						);
						$record['Personne']['haveDemandemaintiencovnonfinal'] = Hash::get($demandeCovNonFinal, 'Dossiercov58.id');
					}
				}

				//infos de la dernière fiche de candidature en cours
				$ficheCandidature = $this->getInfosFicheCandidature($personne_id);

				$results = array(
					'haveOrient' => (boolean)Hash::get($record, 'Personne.haveoriente'),
					'haveOrientEmploi' => (boolean)Hash::get($record, 'Personne.haveoriente_emploi'),
					'haveCui' => (boolean)Hash::get($record, 'Personne.havecui'),
					'haveDossiercovnonfinal' => (boolean)Hash::get($record, 'Personne.havedossiercovnonfinal'),
					'isSoumisdroitetdevoir' => (boolean)Hash::get($record, 'Personne.isSoumisdroitetdevoir'),
					'haveDemandemaintiencovnonfinal' => (boolean)Hash::get($record, 'Personne.haveDemandemaintiencovnonfinal'),
					'needReorientationsociale' => (boolean)Hash::get($record, 'Personne.needReorientationsociale'),
					'dureeFicheCandidature' => $ficheCandidature["dureeFicheCandidature"],
					'idFicheCandidature' => $ficheCandidature["idFicheCandidature"],
					'eligibleFSE' => $ficheCandidature["eligibleFSE"]
				);

				$this->_mem[$memKey] = $results;
			}

			return $this->_mem[$memKey];
		}

		/**
		 * Récupère les informations de la dernière fiche de candidature en cours
		 * Traité ensuite dans l'activation ou non de la "tacite reconduction"
		 */
		private function getInfosFicheCandidature($personne_id) {
			$infos = array(	'dureeFicheCandidature'=>0,
							'idFicheCandidature'=>0,
							'eligibleFSE'=>0);

			$infoFicheCandidature = $this->Contratinsertion->query('SELECT MAX(ap.id), ap.datesignature, ac.eligiblefse
																	FROM actionscandidats_personnes ap
																	LEFT JOIN actionscandidats ac ON ac.id=ap.actioncandidat_id
																	WHERE ap.personne_id='.$personne_id.' AND ap.positionfiche IN (\'encours\', \'enattente\')
																	GROUP BY ap.datesignature, ac.eligiblefse');
			if(isset($infoFicheCandidature[0][0]["max"])) {
				$infos['dureeFicheCandidature'] = $this->getNbMoisEntre2Dates($infoFicheCandidature[0][0]["datesignature"], date('Y-m-d'));
				$infos['idFicheCandidature'] = $infoFicheCandidature[0][0]["max"];
				$infos['eligibleFSE'] = $infoFicheCandidature[0][0]["eligiblefse"];
			}

			return $infos;
		}

		/**
		 * Calcul le nombre de mois entre 2 dates
		 *
		 * @param date $dateDebut Date de début du CER
		 * @param date $dateFin Date de fin du CER précédent
		 */
		private function getNbMoisEntre2Dates ($dateDebut, $dateFin) {
			$dtDeb = new DateTime($dateDebut);
			$dtFin = new DateTime($dateFin);
			$interval = $dtDeb->diff($dtFin);
			$nbmonth= $interval->format('%m');
			$nbyear = $interval->format('%y');
			return 12 * $nbyear + $nbmonth;
		}

		/**
		 * (CG 58, 93)
		 *
		 * @param type $modele
		 * @param type $personne_id
		 * @return type
		 */
		public function qdThematiqueEp($modele, $personne_id) {
			return array(
				'fields' => array(
					'Dossierep.id',
					'Dossierep.personne_id',
					'Dossierep.themeep',
					'Dossierep.created',
					'Dossierep.modified',
					'Passagecommissionep.etatdossierep',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
				),
				'conditions' => array(
					'Dossierep.actif' => '1',
					'Dossierep.personne_id' => $personne_id,
					'Dossierep.themeep' => Inflector::tableize($modele),
					'Dossierep.id NOT IN ( ' . $this->Contratinsertion->{$modele}->Dossierep->Passagecommissionep->sq(
							array(
								'alias' => 'passagescommissionseps',
								'fields' => array(
									'passagescommissionseps.dossierep_id'
								),
								'conditions' => array(
									'passagescommissionseps.etatdossierep' => array('traite', 'annule')
								)
							)
					) . ' )'
				),
				'joins' => array(
					array(
						'table' => Inflector::tableize($modele),
						'alias' => $modele,
						'type' => 'INNER',
						'foreignKey' => false,
						'conditions' => array("Dossierep.id = {$modele}.dossierep_id")
					),
					array(
						'table' => 'contratsinsertion',
						'alias' => 'Contratinsertion',
						'type' => 'INNER',
						'foreignKey' => false,
						'conditions' => array("Contratinsertion.id = {$modele}.contratinsertion_id")
					),
					array(
						'table' => 'passagescommissionseps',
						'alias' => 'Passagecommissionep',
						'type' => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array('Dossierep.id = Passagecommissionep.dossierep_id')
					),
				),
			);
		}

		/**
		 * Recalcul des rangs des contrats pour une personne donnée ou pour
		 * l'ensemble des personnes.
		 */
		protected function _updateRangsContrats( $personne_id = null ) {
			$condition = ( is_null( $personne_id ) ? "" : "contratsinsertion.personne_id = {$personne_id}" );

			$sql = "UPDATE contratsinsertion
						SET rg_ci = NULL".(!empty( $condition ) ? " WHERE {$condition}" : "" ).";";
			$success = ( $this->Contratinsertion->query( $sql ) !== false );

			$cg = Configure::read( 'Cg.departement' );

			$sql = "UPDATE contratsinsertion
						SET rg_ci = (
							SELECT ( COUNT(contratsinsertionpcd.id) + 1 )
								FROM contratsinsertion AS contratsinsertionpcd
								WHERE contratsinsertionpcd.personne_id = contratsinsertion.personne_id
									AND contratsinsertionpcd.id <> contratsinsertion.id
									".( $cg == 93 ? "AND contratsinsertionpcd.decision_ci IN ( 'E', 'V' )" : "AND contratsinsertionpcd.decision_ci = 'V'" )."
									AND contratsinsertionpcd.dd_ci IS NOT NULL
									".( $cg == 93 ? "" : "AND contratsinsertionpcd.datevalidation_ci IS NOT NULL" )."
									AND (
										contratsinsertionpcd.dd_ci < contratsinsertion.dd_ci
										OR (
											contratsinsertionpcd.dd_ci = contratsinsertion.dd_ci
											AND contratsinsertionpcd.datevalidation_ci IS NOT NULL
											AND contratsinsertion.datevalidation_ci IS NOT NULL
											AND contratsinsertionpcd.datevalidation_ci < contratsinsertion.datevalidation_ci
										)
										OR (
											contratsinsertionpcd.dd_ci = contratsinsertion.dd_ci
											AND contratsinsertionpcd.datevalidation_ci IS NOT NULL
											AND contratsinsertion.datevalidation_ci IS NOT NULL
											AND contratsinsertionpcd.datevalidation_ci = contratsinsertion.datevalidation_ci
											AND contratsinsertionpcd.id < contratsinsertion.id
										)
										OR (
											contratsinsertionpcd.dd_ci = contratsinsertion.dd_ci
											AND (
												contratsinsertionpcd.datevalidation_ci IS NULL
												OR contratsinsertion.datevalidation_ci IS NULL
											)
											AND contratsinsertionpcd.id < contratsinsertion.id
										)
									)
									".( $cg == 93 ? "" : "AND ( contratsinsertionpcd.positioncer IS NULL OR contratsinsertionpcd.positioncer <> 'annule' )" )."
						)
						WHERE
							contratsinsertion.dd_ci IS NOT NULL
							".( $cg == 93 ? "" : "AND contratsinsertion.datevalidation_ci IS NOT NULL" )."
							".(!empty( $condition ) ? " AND {$condition}" : "" )."
							".( $cg == 93 ? "AND contratsinsertion.decision_ci IN ( 'E', 'V' )" : "AND contratsinsertion.decision_ci = 'V'" )."
							".( $cg == 93 ? "" : "AND ( contratsinsertion.positioncer IS NULL OR contratsinsertion.positioncer <> 'annule' )" )."
							;";

			$success = ( $this->Contratinsertion->query( $sql ) !== false ) && $success;

			return $success;
		}

		/**
		 * Recalcul des rangs des contrats pour une personne donnée.
		 * afterSave, afterDelete, valider, annuler
		 */
		public function updateRangsContratsPersonne( $personne_id ) {
			return $this->_updateRangsContrats( $personne_id );
		}

		/**
		 * Recalcul des rangs des contrats pour l'ensemble des personnes.
		 */
		public function updateRangsContrats() {
			return $this->_updateRangsContrats();
		}

		/**
		 *
		 */
		public function valider( $data ) {
			$this->Contratinsertion->begin();
			$success = $this->Contratinsertion->saveAll( $data, array( 'atomic' => false ) );

			// Sortie de la procédure de relances / sanctions 93 en cas de validation d'un nouveau contrat ?
			if( $success && Configure::read( 'Cg.departement' ) == '93' ) {
				$success = $this->Contratinsertion->Nonrespectsanctionep93->calculSortieProcedureRelanceParValidationCer( $data ) && $success;
			}

			if( $success ) {
				$this->Contratinsertion->commit();
			}
			else {
				$this->Contratinsertion->rollback();
			}

			return $success;
		}

		/**
		 * Retourne les positions et les conditions CakePHP/SQL dans l'ordre dans
		 * lequel elles doivent être traitées pour récupérer la position actuelle.
		 *
		 * @return array
		 */
		protected function _getConditionsPositionsCers() {
			$intervalBilan = Configure::read( 'Contratinsertion.Cg66.updateEncoursbilan' );

			$conditionExistsAutreCerRemplacement = 'EXISTS (
				SELECT *
					FROM contratsinsertion AS autrecontratsinsertion
					WHERE
						autrecontratsinsertion.personne_id = '.$this->Contratinsertion->alias.'.personne_id
						AND autrecontratsinsertion.id <> '.$this->Contratinsertion->alias.'.id
						AND autrecontratsinsertion.dd_ci = '.$this->Contratinsertion->alias.'.dd_ci
						AND autrecontratsinsertion.df_ci = '.$this->Contratinsertion->alias.'.df_ci
						AND autrecontratsinsertion.positioncer <> \'annule\'
			)';

			// Seuls certains bilans de parcours sont concernés par un CER
			// Un CER ne concerne qu'un seul bilan de parcours
			$conditionExistsBilanEnCoursEpLieAuCer = 'EXISTS (
				SELECT *
					FROM bilansparcours66
					WHERE
						bilansparcours66.personne_id = '.$this->Contratinsertion->alias.'.personne_id
						AND bilansparcours66.positionbilan <> \'annule\'
						AND bilansparcours66.proposition NOT IN ( \'audition\' )
						AND bilansparcours66.datebilan >= ( '.$this->Contratinsertion->alias.'.df_ci - INTERVAL \''.$intervalBilan.'\' )::DATE
						AND NOT EXISTS(
							SELECT *
								FROM contratsinsertion AS nvcontratsinsertion
								WHERE
									nvcontratsinsertion.personne_id = "'.$this->Contratinsertion->alias.'"."personne_id"
									AND (
										nvcontratsinsertion.dd_ci > '.$this->Contratinsertion->alias.'.dd_ci
										OR (
											nvcontratsinsertion.dd_ci = '.$this->Contratinsertion->alias.'.dd_ci
											AND nvcontratsinsertion.id <> '.$this->Contratinsertion->alias.'.id
										)
									)
									AND nvcontratsinsertion.positioncer NOT IN ( \'annule\', \'nonvalid\' )
									AND bilansparcours66.datebilan >= ( nvcontratsinsertion.df_ci - INTERVAL \''.$intervalBilan.'\' )::DATE
						)
						AND EXISTS(
							SELECT *
								FROM saisinesbilansparcourseps66
									INNER JOIN dossierseps ON ( saisinesbilansparcourseps66.dossierep_id = dossierseps.id )
								WHERE
									saisinesbilansparcourseps66.bilanparcours66_id = bilansparcours66.id
									AND dossierseps.actif = \'1\'
									AND NOT EXISTS(
										SELECT *
											FROM passagescommissionseps
											WHERE
												passagescommissionseps.dossierep_id = dossierseps.id
												AND passagescommissionseps.etatdossierep IN ( \'traite\', \'annule\' )
												AND passagescommissionseps.id IN (
													SELECT "dernierspassagescommissionseps"."id" AS "dernierspassagescommissionseps__id"
														FROM "passagescommissionseps" AS "dernierspassagescommissionseps"
															INNER JOIN "public"."commissionseps" AS "commissionseps" ON ("dernierspassagescommissionseps"."commissionep_id" = "commissionseps"."id")
														WHERE "dernierspassagescommissionseps"."dossierep_id" = "dossierseps"."id"
														ORDER BY "commissionseps"."dateseance" DESC, "commissionseps"."id" DESC
														LIMIT 1
												)

									)
						)
			)';

			$return = array(
				// 1. CER qui devraient être "Annulé"
				'annule' => array(
					'OR' => array(
						$this->Contratinsertion->alias.'.positioncer' => 'annule',
						$this->Contratinsertion->alias.'.motifannulation IS NOT NULL',
						array(
							$this->Contratinsertion->alias.'.df_ci < NOW()::DATE',
							$this->Contratinsertion->alias.'.decision_ci' => 'E',
							$conditionExistsAutreCerRemplacement
						)
					)
				),
				// 2. CER qui devraient être "Non validés"
				'nonvalid' => array(
					'OR' => array(
						$this->Contratinsertion->alias.'.positioncer' => 'nonvalid',
						$this->Contratinsertion->alias.'.decision_ci' => 'N'
					)
				),
				// 3. CER qui devraient être en "Fin de contrat"
				'fincontrat' => array(
					$this->Contratinsertion->alias.'.decision_ci' => array( 'E', 'V' ),
						'OR' => array(
							$this->Contratinsertion->alias.'.forme_ci' => 'S', // Contrat simple
							array( // OR
								$this->Contratinsertion->alias.'.forme_ci' => 'C', // ( Contrat complexe AND
								// Date de début de contrat > Date de cloture des droits + x mois )
								"{$this->Contratinsertion->alias}.dd_ci > (
									(
										SELECT situationsdossiersrsa.dtclorsa
												FROM personnes
														INNER JOIN foyers ON ( personnes.foyer_id = foyers.id )
														INNER JOIN dossiers ON ( foyers.dossier_id = dossiers.id )
														INNER JOIN situationsdossiersrsa ON ( dossiers.id = situationsdossiersrsa.dossier_id )
												WHERE
														personnes.id = {$this->Contratinsertion->alias}.personne_id
												LIMIT 1
									)
									+ INTERVAL '".Configure::read( 'Contratinsertion.Cg66.toleranceDroitClosCerComplexe' )."'
								)::DATE"
							)
						),
					'EXISTS (
						SELECT *
							FROM personnes
								INNER JOIN foyers ON ( personnes.foyer_id = foyers.id )
								INNER JOIN dossiers ON ( foyers.dossier_id = dossiers.id )
								INNER JOIN situationsdossiersrsa ON ( dossiers.id = situationsdossiersrsa.dossier_id )
							WHERE
								personnes.id = '.$this->Contratinsertion->alias.'.personne_id
								AND situationsdossiersrsa.etatdosrsa IN ( \'5\', \'6\' )
					)'
				),
				// 4. CER qui devraient être "En attente de validation"
				'attvalid' => array(
					$this->Contratinsertion->alias.'.decision_ci' => 'E',
					$this->Contratinsertion->alias.'.df_ci >= NOW()::DATE'
				),
				// 5. CER qui devraient être "Bilan réalisé - En attente de décision de l'EPL Parcours"
				'bilanrealiseattenteeplparcours' => array(
					$this->Contratinsertion->alias.'.decision_ci' => 'V',
					'( '.$this->Contratinsertion->alias.'.df_ci - INTERVAL \''.$intervalBilan.'\' )::DATE <= NOW()::DATE',
					"{$conditionExistsBilanEnCoursEpLieAuCer}"
				),
				// 6. CER qui devraient être "Périme"
				'perime' => array(
					$this->Contratinsertion->alias.'.df_ci < NOW()::DATE',
					$this->Contratinsertion->alias.'.decision_ci' => array( 'V', 'E' )
				),
				// 7. CER qui devraient être "En cours"
				'encours' => array(
					$this->Contratinsertion->alias.'.decision_ci' => 'V',
					'OR' => array(
						'NOW()::DATE <= ( '.$this->Contratinsertion->alias.'.df_ci - INTERVAL \''.$intervalBilan.'\' )::DATE',
						array(
							'( '.$this->Contratinsertion->alias.'.df_ci - INTERVAL \''.$intervalBilan.'\' )::DATE <= NOW()::DATE',
							'NOT' => array( $conditionExistsBilanEnCoursEpLieAuCer )
						)
					)
				)
			);

			return $return;
		}

		/**
		 * Retourne les conditions permettant de cibler les CER qui devraient être
		 * dans une certaine position.
		 *
		 * @param string $positioncer
		 * @return array
		 */
		public function getConditionsPositioncer( $positioncer ) {
			$conditions = array();
			$found = false;

			foreach( $this->_getConditionsPositionsCers() as $keyPosition => $conditionsPosition ) {
				if( !$found ) {
					if( $keyPosition != $positioncer ) {
						$conditions[] = array( 'NOT' => array( $conditionsPosition ) );
					}
					else {
						$conditions[] = array( $conditionsPosition );
						$found = true;
					}
				}
			}

			return $conditions;
		}

		/**
		 * Retourne une CASE (PostgreSQL) pemettant de connaître la position que
		 * devrait avoir un CER (au CG 66).
		 *
		 * A utiliser par exemple en tant que chmap virtuel, à partir du moment
		 * où le modèle Contratinsertion (ou un alias) est présent dans la requête
		 * de base.
		 *
		 * @return string
		 */
		public function getCasePositionCer() {
			$return = '';
			$Dbo = $this->Contratinsertion->getDataSource();

			foreach( array_keys( $this->Contratinsertion->enum( 'positioncer' ) ) as $positioncer ) {
					$conditions = $this->getConditionsPositioncer( $positioncer );
					$conditions = $Dbo->conditions( $conditions, true, false, $this->Contratinsertion );
					$return .= "WHEN {$conditions} THEN '{$positioncer}' ";
				}

			$return = "( CASE {$return} ELSE NULL END )";

			return $return;
		}

		/**
		 * Retourne une condition permettant de trouver tous les CER dont la
		 * position ne peut pas être calculée.
		 *
		 * @return array
		 */
		public function getConditionNonCalculables() {
			$sqls = array();

			foreach( array_keys( $this->Contratinsertion->enum( 'positioncer' ) ) as $positioncer ) {
				$query = array(
					'alias' => 'contratsinsertioncalculables',
					'fields' => array( 'contratsinsertioncalculables.id' ),
					'conditions' => $this->getConditionsPositioncer( $positioncer ),
					'contain' => false
				);
				$sqls[] = alias( $this->Contratinsertion->sq( $query ), array( $this->Contratinsertion->alias => 'contratsinsertioncalculables' ) );
			}

			$sql = implode( ' UNION ', $sqls );
			return "{$this->Contratinsertion->alias}.id NOT IN ( {$sql} )";
		}

		/**
		 * Mise à jour des positions des CER suivant des conditions données.
		 *
		 * @param array $conditions
		 * @return boolean
		 */
		public function updatePositionsCersByConditions( array $conditions ) {
			$query = array( 'fields' => array( "{$this->Contratinsertion->alias}.{$this->Contratinsertion->primaryKey}" ), 'conditions' => $conditions, 'contain' => false );
			$sample = $this->Contratinsertion->find( 'first', $query );

			// INFO: il n'est pas possible d'utiliser Model::updateAllUnBound() car à ce moment-là,
			// l'alias Contratinsertion serait supprimé du CASE, hors il est nécessaire
			// La solution et d'écrire la requête d'UPDATE à la main.

			$Dbo = $this->Contratinsertion->getDataSource();

			$tableName = $Dbo->fullTableName( $this->Contratinsertion, true, true );
			$case = $this->getCasePositionCer();

			$sq = $Dbo->startQuote;
			$eq = $Dbo->endQuote;

			$conditions = $Dbo->conditions( $conditions, true, true, $this->Contratinsertion );

			$sql = "UPDATE {$tableName} AS {$sq}{$this->Contratinsertion->alias}{$eq} SET {$sq}positioncer{$eq} = {$case} {$conditions};";

			return (
				empty( $sample )
				|| ( $Dbo->query( $sql ) !== false )
			);
		}

		/**
		 * Mise à jour des positions des CER qui devraient se trouver dans une
		 * position donnée.
		 *
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function updatePositionsCersByPosition( $positioncer ) {
			$conditions = $this->getConditionsPositioncer( $positioncer );

			$query = array( 'fields' => array( "{$this->Contratinsertion->alias}.{$this->Contratinsertion->primaryKey}" ), 'conditions' => $conditions, 'contain' => false );
			$sample = $this->Contratinsertion->find( 'first', $query );

			return (
				empty( $sample )
				|| $this->Contratinsertion->updateAllUnBound(
					array( "{$this->Contratinsertion->alias}.positioncer" => "'{$positioncer}'" ),
					$conditions
				)
			);
		}

		/**
		 * Permet de mettre à jour les positions des CER d'un allocataire retrouvé
		 * grâce à la clé primaire d'un CER en particulier.
		 *
		 * @param integer $id La clé primaire d'un CER.
		 * @return boolean
		 */
		public function updatePositionsCersById( $id ) {
			$return = true;

			$query = array(
				'fields' => array( "{$this->Contratinsertion->alias}.personne_id" ),
				'contain' => false,
				'conditions' => array(
					 "{$this->Contratinsertion->alias}.{$this->Contratinsertion->primaryKey}" => $id
				)
			);
			$record = $this->Contratinsertion->find( 'first', $query );

			if( !empty( $record ) ) {
				$return = $this->updatePositionsCersByConditions(
					array( "{$this->Contratinsertion->alias}.personne_id" => Hash::get( $record, "{$this->Contratinsertion->alias}.personne_id" ) )
				);
			}

			return $return;
		}

		/**
		 *   Liste des anciennes demandes d'ouverture de droit pour un allocataire.
		 *
		 *   @see getDataForPdf
		 *
		 * @param integer $personne_id
		 * @return integer
		 */
		public function checkNumDemRsa( $personne_id ) {

			$personne = $this->Contratinsertion->Personne->find(
					'first', array(
				'conditions' => array(
					'Personne.id' => $personne_id
				),
				'contain' => array(
					'Foyer' => array(
						'Dossier'
					)
				)
					)
			);
			$this->Contratinsertion->set( compact( 'personne' ) );

			$nir13 = trim( $personne['Personne']['nir'] );
			$nir13 = ( empty( $nir13 ) ? null : substr( $nir13, 0, 13 ) );

			$autreNumdemrsa = $this->Contratinsertion->Personne->Foyer->Dossier->find(
					'all', array(
				'fields' => array(
					'COUNT(DISTINCT "Dossier"."id") AS "count"'
				),
				'joins' => array(
					$this->Contratinsertion->Personne->Foyer->Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Contratinsertion->Personne->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Contratinsertion->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
				),
				'conditions' => array(
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'OR' => array(
						array(
							'nir_correct13( Personne.nir  )',
							'nir_correct13( \''.$nir13.'\'  )',
							'SUBSTRING( TRIM( BOTH \' \' FROM Personne.nir ) FROM 1 FOR 13 )' => $nir13,
							'Personne.dtnai' => $personne['Personne']['dtnai']
						),
						array(
							'UPPER(Personne.nom)' => strtoupper( replace_accents( $personne['Personne']['nom'] ) ),
							'UPPER(Personne.prenom)' => strtoupper( replace_accents( $personne['Personne']['prenom'] ) ),
							'Personne.dtnai' => $personne['Personne']['dtnai']
						)
					)
				),
				'contain' => false,
				'recursive' => -1
					)
			);

			return $autreNumdemrsa[0][0]['count'];
		}

		/**
		 * Vérifie l'intervalle, par-rapport à la date de fin d'un CER, en deçà duquel
		 * un CER sera positionné « En cours:Bilan à réaliser » grâce au shell
		 * positioncer66.
		 */
		public function checkConfigUpdateEncoursbilanCg66() {
			return $this->_checkPostgresqlIntervals( array( 'Contratinsertion.Cg66.updateEncoursbilan' ), true );
		}

		/**
		 * Sous-requête permettant de récupérer le dernier contrat d'un allocataire.
		 *
		 * @param string $personneIdFied Le champ où trouver l'id de la personne.
		 * @return string
		 */
		public function sqDernierContrat( $personneIdFied = 'Personne.id', $cerValide = false ) {

			$conditions = ( $cerValide ? array( 'contratsinsertion.decision_ci' => 'V' ) : array() );
			return $this->Contratinsertion->sq(
				array(
					'fields' => array(
						'contratsinsertion.id'
					),
					'alias' => 'contratsinsertion',
					'conditions' => array(
						"contratsinsertion.personne_id = {$personneIdFied}",
						$conditions
					),
					'order' => array( 'contratsinsertion.dd_ci DESC' ),
					'limit' => 1
				)
			);
		}

		/**
		 * Récupération du PDF de la fiche de liaison du référent uniquement en cas de non validation du CER
		 * (CG 66).
		 *
		 * @param integer $contratinsertion_id
		 * @param integer $user_id
		 * @return string
		 */
		public function getPdfFicheliaisoncer( $contratinsertion_id, $user_id ) {
			$queryData = array(
				'fields' => array_merge(
					$this->Contratinsertion->fields(),
					$this->Contratinsertion->Structurereferente->fields(),
					$this->Contratinsertion->Propodecisioncer66->fields(),
					array(
						'( '.$this->Contratinsertion->Propodecisioncer66->Motifcernonvalid66->vfListeMotifs().' ) AS "Propodecisioncer66__motifscersnonvalids66"',
						'Referent.qual',
						'Referent.nom',
						'Referent.prenom',
						'Referent.numero_poste',
						'Referent.email',
						'Referent.fonction',
						'Dossier.matricule',
						'Personne.qual',
						'Personne.nom',
						'Personne.prenom',
						'Personne.dtnai',
						'Personne.typedtnai',
						'Personne.nir',
						'Personne.idassedic',
						'Personne.numfixe',
						'Personne.numport',
						'Personne.email',
						'Adresse.numvoie',
						'Adresse.libtypevoie',
						'Adresse.nomvoie',
						'Adresse.compladr',
						'Adresse.complideadr',
						'Adresse.nomcom',
						'Adresse.numcom',
						'Adresse.codepos'
					)
				),
				'joins' => array(
					$this->Contratinsertion->join( 'Personne' ),
					$this->Contratinsertion->join( 'Propodecisioncer66' ),
					$this->Contratinsertion->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					$this->Contratinsertion->join( 'Structurereferente' ),
					$this->Contratinsertion->Personne->join( 'Foyer' ),
					$this->Contratinsertion->Personne->Foyer->join( 'Dossier' ),
					$this->Contratinsertion->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Contratinsertion->Personne->Foyer->Adressefoyer->join( 'Adresse' )
				),
				'conditions' => array(
					'Contratinsertion.id' => $contratinsertion_id,
					'Adressefoyer.id IN ( '.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01( 'Adressefoyer.foyer_id' ).' )'
				),
				'recursive' => -1
			);

			$options = array(
				'Referent' => array( 'qual' => ClassRegistry::init( 'Option' )->qual() ),
				'Personne' => array( 'qual' => ClassRegistry::init( 'Option' )->qual() )
			);
			$options = Set::merge( $options, $this->Contratinsertion->enums() );


			$contratinsertion = $this->Contratinsertion->find( 'first', $queryData );

			$forme_ci = array( 'S' => 'Simple', 'C' => 'Particulier' );
			$formeci = Set::classicExtract( $contratinsertion, 'Contratinsertion.forme_ci' );

			$typestructure = Set::classicExtract( $contratinsertion, 'Structurereferente.typestructure' );

			$user = $this->Contratinsertion->User->find(
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
			$contratinsertion = Set::merge( $contratinsertion, $user );

			if( $formeci == 'C' ) {
				$modeleodt = "Contratinsertion/ficheliaisoncerParticulier.odt";
			}
			else {
				$modeleodt = "Contratinsertion/ficheliaisoncerSimplemsp.odt";
			}

			return $this->Contratinsertion->ged(
							array( $contratinsertion ), $modeleodt, true, $options
			);
		}

		/**
		 * Récupération du PDF de la notification au bénéficiaire (CG 66).
		 *
		 * @param integer $contratinsertion_id
		 * @param integer $user_id
		 * @return string
		 */
		public function getPdfNotifbenef( $contratinsertion_id, $user_id ) {
			$queryData = array(
				'fields' => array_merge(
					$this->Contratinsertion->fields(),
					$this->Contratinsertion->Structurereferente->fields(),
					array(
						'( '.$this->Contratinsertion->Propodecisioncer66->Motifcernonvalid66->vfListeMotifs().' ) AS "Propodecisioncer66__motifscersnonvalids66"',
						'Referent.qual',
						'Referent.nom',
						'Referent.prenom',
						'Referent.numero_poste',
						'Referent.email',
                        'Referent.fonction',
                        'Dossier.matricule',
						'Personne.qual',
						'Personne.nom',
						'Personne.prenom',
						'Personne.dtnai',
						'Personne.typedtnai',
						'Personne.nir',
						'Personne.idassedic',
						'Personne.numfixe',
						'Personne.numport',
						'Personne.email',
						'Adresse.numvoie',
						'Adresse.libtypevoie',
						'Adresse.nomvoie',
						'Adresse.compladr',
						'Adresse.complideadr',
						'Adresse.nomcom',
						'Adresse.numcom',
						'Adresse.codepos',
						'Propodecisioncer66.isvalidcer',
						'Propodecisioncer66.motifficheliaison',
						'Propodecisioncer66.motifnotifnonvalid',
						'Propodecisioncer66.nonvalidationparticulier'
					)
				),
				'joins' => array(
					$this->Contratinsertion->join( 'Personne' ),
					$this->Contratinsertion->join( 'Propodecisioncer66' ),
					$this->Contratinsertion->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					$this->Contratinsertion->join( 'Structurereferente' ),
					$this->Contratinsertion->Personne->join( 'Foyer' ),
					$this->Contratinsertion->Personne->Foyer->join( 'Dossier' ),
					$this->Contratinsertion->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Contratinsertion->Personne->Foyer->Adressefoyer->join( 'Adresse' )
				),
				'conditions' => array(
					'Contratinsertion.id' => $contratinsertion_id,
					'Adressefoyer.id IN ( '.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01( 'Adressefoyer.foyer_id' ).' )'
				),
				'recursive' => -1
			);
			$options = array(
				'Referent' => array( 'qual' => ClassRegistry::init( 'Option' )->qual() ),
				'Personne' => array( 'qual' => ClassRegistry::init( 'Option' )->qual() ),
				'duree' => array( 'engag' => Configure::read( 'cer.duree.engagement' ) )
			);


			$options = Set::merge( $options, $this->Contratinsertion->enums() );


			$contratinsertion = $this->Contratinsertion->find( 'first', $queryData );
			$referents = $this->Contratinsertion->Referent->find( 'list' );

			$datesaisici = Set::classicExtract( $contratinsertion, 'Contratinsertion.date_saisi_ci' );
			$contratinsertion['Contratinsertion']['delairegularisation'] = date( 'Y-m-d', strtotime( '+1 month', strtotime( $datesaisici ) ) );

			$user = $this->Contratinsertion->User->find(
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
			$contratinsertion = Set::merge( $contratinsertion, $user );

			$modelenotifdecision = '';

			$decision = Set::classicExtract( $contratinsertion, 'Propodecisioncer66.isvalidcer' );
			$nonvalidationparticulier = Set::classicExtract( $contratinsertion, 'Propodecisioncer66.nonvalidationparticulier' );

			$forme_ci = array( 'S' => 'Simple', 'C' => 'Particulier' );
			$valueFormeci = Set::classicExtract( $contratinsertion, 'Contratinsertion.forme_ci' );
			$formeci = Set::enum( Set::classicExtract( $contratinsertion, 'Contratinsertion.forme_ci' ), $forme_ci );

			if( !empty( $decision ) ) {
				if( $decision == 'O' ) {
					$modelenotifdecision = "valide";
				}
				else if( $decision == 'N' ) {
					$modelenotifdecision = "nonvalide";
					if( $valueFormeci == 'C' ) {
						$modelenotifdecision = $modelenotifdecision.$nonvalidationparticulier;
					}
				}
			}


			return $this->Contratinsertion->ged(
							array( $contratinsertion ), "Contratinsertion/notifbenef{$formeci}{$modelenotifdecision}.odt", true, $options
			);
		}

		/**
		 * Retourne le chemin relatif du modèle de document utilisé pour l'impression du PDF par défaut.
		 *
		 * @param array $data
		 * @return string
		 */
		public function modeleOdt( $data ) {
			$cgDepartement = Configure::read( 'Cg.departement' );

			if( $cgDepartement == 58 ) {
				return 'Contratinsertion/contratinsertioncg58.odt';
			}
			else if( $cgDepartement == 66 ) {
				if( strtotime( $data['Contratinsertion']['date_saisi_ci'] ) >= strtotime( '2012-02-10' ) ) {
					return 'Contratinsertion/contratinsertion.odt';
				}
				else {
					return 'Contratinsertion/contratinsertionold.odt';
				}
			}
			else if( $cgDepartement == 976 ) {
				return 'Contratinsertion/contratinsertioncg976.odt';
			}

			return 'Contratinsertion/contratinsertion.odt';
		}

		/**
		 * Récupération des données nécessaires à l'impression du PDF par défaut du contrat.
		 *
		 * @param integer $id
		 * @param integer $user_id
		 * @return array
		 */
		public function getDataForPdf( $id, $user_id ) {
			$contratinsertion = $this->Contratinsertion->find(
				'first',
				array(
					'fields' => array_merge(
						$this->Contratinsertion->fields(),
						$this->Contratinsertion->Personne->fields(),
						$this->Contratinsertion->Referent->fields(),
						$this->Contratinsertion->Structurereferente->fields(),
						$this->Contratinsertion->Structurereferente->Typeorient->fields(),
						$this->Contratinsertion->Typocontrat->fields(),
						$this->Contratinsertion->Zonegeographique->fields(),
						$this->Contratinsertion->Personne->Activite->fields(),
						$this->Contratinsertion->Personne->Dsp->fields(),
						$this->Contratinsertion->Personne->Foyer->fields(),
						$this->Contratinsertion->Personne->Prestation->fields(),
						$this->Contratinsertion->Personne->Foyer->Dossier->fields(),
						$this->Contratinsertion->Personne->Foyer->Modecontact->fields(),
						$this->Contratinsertion->Personne->Foyer->Adressefoyer->Adresse->fields(),
						$this->Contratinsertion->Personne->Foyer->Dossier->Detaildroitrsa->fields(),
						$this->Contratinsertion->Personne->Foyer->Dossier->Situationdossierrsa->fields(),
						$this->Contratinsertion->Personne->Foyer->Dossier->Situationdossierrsa->Suspensiondroit->fields(),
						array(
							'( '.$this->Contratinsertion->Personne->Foyer->Dossier->Detaildroitrsa->vfRsaMajore( '"Dossier"."id"' ).' ) AS "Infofinanciere__rsamaj"'
						)
					),
					'joins' => array(
						$this->Contratinsertion->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Contratinsertion->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$this->Contratinsertion->Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) ),
						$this->Contratinsertion->join( 'Typocontrat', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->join( 'Zonegeographique', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->Personne->join( 'Activite', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->Personne->join( 'Dsp', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Contratinsertion->Personne->join( 'Prestation', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Contratinsertion->Personne->Foyer->join(
							'Modecontact',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Modecontact.id IN ( '.$this->Contratinsertion->Personne->Foyer->Modecontact->sqDerniere( 'Foyer.id', array( 'Modecontact.autorutitel' => 'A' ) ).' )',
								)
							)
						),
						$this->Contratinsertion->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->Personne->Foyer->Dossier->join( 'Detaildroitrsa', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->Personne->Foyer->Dossier->Situationdossierrsa->join( 'Suspensiondroit', array( 'type' => 'LEFT OUTER' ) ),
					),
					'contain' => false,
					'conditions' => array(
						'Contratinsertion.id' => $id,
						array(
							'OR' => array(
								'Activite.id IS NULL',
								'Activite.id IN ('
								.$this->Contratinsertion->Personne->Activite->sqDerniere( 'Personne.id' )
								.')',
							)
						),
						array(
							'OR' => array(
								'Adressefoyer.id IS NULL',
								'Adressefoyer.id IN ('
								.$this->Contratinsertion->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' )
								.')',
							)
						),
						array(
							'OR' => array(
								'Dsp.id IS NULL',
								'Dsp.id IN ('
								.$this->Contratinsertion->Personne->Dsp->WebrsaDsp->sqDerniereDsp( 'Personne.id' )
								.')',
							)
						),
						array(
							'OR' => array(
								'Suspensiondroit.id IS NULL',
								'Suspensiondroit.id IN ('
								.$this->Contratinsertion->Personne->Foyer->Dossier->Situationdossierrsa->Suspensiondroit->sqDerniere( 'Situationdossierrsa.id' )
								.')',
							)
						)
					),
				)
			);

			// Recherche, traduction et ajout de champs virtuels concernant Autreavissuspension et Autreavisradiation
			$duree_engag = Hash::get( $contratinsertion, "{$this->Contratinsertion->alias}.duree_engag" );
			if( !empty( $duree_engag ) ) {
				$contratinsertion[$this->Contratinsertion->alias]['duree_engag'] = "{$duree_engag} mois";
			}

			$Option = ClassRegistry::init( 'Option' );
			$options = Set::merge(
				$this->Contratinsertion->enums(),
				$this->Contratinsertion->Autreavisradiation->enums(),
				$this->Contratinsertion->Autreavissuspension->enums()
			);

			$autresModeles = array( 'Autreavissuspension', 'Autreavisradiation' );
			foreach( $autresModeles as $autreModele ) {
				$fieldName = Inflector::underscore( $autreModele );
				$contratinsertion['Contratinsertion'][$fieldName] = '';
				if( isset( $contratinsertion['Contratinsertion']['id'] ) && !empty( $contratinsertion['Contratinsertion']['id'] ) ) {
					$items = $this->Contratinsertion->{$autreModele}->find(
							'all', array(
						"{$autreModele}.contratinsertion_id" => $contratinsertion['Contratinsertion']['id'],
						'contain' => false
							)
					);
					if( !empty( $items ) ) {
						$values = Set::extract( $items, "/{$autreModele}/{$fieldName}" );
						foreach( $values as $i => $value ) {
							$values[$i] = Set::enum( $value, $options[$autreModele][$fieldName] );
						}
						$contratinsertion['Contratinsertion'][$fieldName] = "\n".'  - '.implode( "\n  - ", $values )."\n";
					}
				}
			}

			// Données Référent lié à la personne récupérées
			if( empty( $contratinsertion['Contratinsertion']['referent_id'] ) ) {
				// 1°) Une personne désignée comme référent, juste avant la date de début du contrat ,dans la même structure
				$personne_referent = $this->Contratinsertion->Personne->PersonneReferent->find(
						'first', array(
					'fields' => Set::merge(
							$this->Contratinsertion->Personne->PersonneReferent->fields(), $this->Contratinsertion->Personne->PersonneReferent->Referent->fields()
					),
					'conditions' => array(
						'PersonneReferent.personne_id' => $contratinsertion['Personne']['id'],
						'PersonneReferent.structurereferente_id' => $contratinsertion['Contratinsertion']['structurereferente_id'],
						'PersonneReferent.dddesignation <=' => $contratinsertion['Contratinsertion']['dd_ci'],
						'PersonneReferent.id IN ('
						.$this->Contratinsertion->Personne->PersonneReferent->sq(
								array(
									'alias' => 'personnes_referents',
									'fields' => array( 'personnes_referents.id' ),
									'conditions' => array(
										'personnes_referents.personne_id = PersonneReferent.personne_id',
										'personnes_referents.dddesignation <=' => $contratinsertion['Contratinsertion']['dd_ci'],
									),
									'order' => array( 'personnes_referents.dddesignation DESC' ),
									'limit' => 1
								)
						)
						.')'
					),
					'contain' => false,
					'joins' => array(
						$this->Contratinsertion->Personne->PersonneReferent->join( 'Referent', array( 'type' => 'LEFT OUTER' ) )
					)
						)
				);

				// 2°) Une personne référente (n'importe laquelle) liée à la personne (comme avant)
				if( empty( $personne_referent ) ) {
					$personne_referent = $this->Contratinsertion->Personne->PersonneReferent->find(
							'first', array(
						'fields' => Set::merge(
								$this->Contratinsertion->Personne->PersonneReferent->fields(), $this->Contratinsertion->Personne->PersonneReferent->Referent->fields()
						),
						'conditions' => array(
							'PersonneReferent.personne_id' => $contratinsertion['Personne']['id'],
						),
						'contain' => false,
						'joins' => array(
							$this->Contratinsertion->Personne->PersonneReferent->join( 'Referent', array( 'type' => 'LEFT OUTER' ) )
						)
							)
					);
				}

				// 3°) Un référent (actif, sinon n'importe lequel) lié à la structure de mon contrat
				if( empty( $personne_referent ) ) {
					$personne_referent = $this->Contratinsertion->Personne->PersonneReferent->Referent->find(
							'first', array(
						'conditions' => array(
							'Referent.actif' => 'O',
							'Referent.structurereferente_id' => $contratinsertion['Contratinsertion']['structurereferente_id'],
						),
						'contain' => false,
							)
					);
				}

				if( !empty( $personne_referent ) ) {
					$contratinsertion = Set::merge( $contratinsertion, $personne_referent );
				}
			}

			// Traductions restantes (avec création de champs virtuels)
			$contratinsertion['Contratinsertion']['tc'] = $contratinsertion['Typocontrat']['lib_typo'];
			$contratinsertion['Contratinsertion']['datevalidation_ci'] = strftime( __( 'Locale->date' ), strtotime( $contratinsertion['Contratinsertion']['datevalidation_ci'] ) );
			$contratinsertion['Contratinsertion']['actions_prev'] = ( $contratinsertion['Contratinsertion']['actions_prev'] ? 'Oui' : 'Non' );
			$contratinsertion['Contratinsertion']['emp_trouv'] = ( $contratinsertion['Contratinsertion']['emp_trouv'] ? 'Oui' : 'Non' );

			// Données Dsp récupérées
			$contratinsertion['Dsp']['topcouvsoc'] = ( ( isset( $contratinsertion['Dsp']['topcouvsoc'] ) && ( $contratinsertion['Dsp']['topcouvsoc'] == '1' ) ) ? 'Oui' : 'Non' );

			// Ajout suite à la demande d'amélioration du CG66 sur la Forge du 02/02/2012 (#5578)
			$contratinsertion['Contratinsertion']['type_deci'] = $contratinsertion['Contratinsertion']['decision_ci'];

			// Affichage de la date seulement en cas de " Validation à compter de "
			if( $contratinsertion['Contratinsertion']['decision_ci'] == 'V' ) {
				$contratinsertion['Contratinsertion']['decision_ci'] = "{$options['Contratinsertion']['decision_ci'][$contratinsertion['Contratinsertion']['decision_ci']]} {$contratinsertion['Contratinsertion']['datevalidation_ci']}";
			}
			else {
				$contratinsertion['Contratinsertion']['decision_ci'] = $options['Contratinsertion']['decision_ci'][$contratinsertion['Contratinsertion']['decision_ci']];
			}

			// L'objet de l'engagement est un code à traduire pour le CG 93
			if( Configure::read( 'Cg.departement' ) == 93 ) {
				$options['Contratinsertion']['engag_object'] = ClassRegistry::init( 'Action' )->find( 'list', array( 'fields' => array( 'code', 'libelle' ), 'contain' => false ) );
				$contratinsertion['Contratinsertion']['engag_object'] = @$options['Contratinsertion']['engag_object'][$contratinsertion['Contratinsertion']['engag_object']];
			}

			///Utilisé pour savoir si le contrat est pour une insertion vers le social / emploi
			if( $contratinsertion['Contratinsertion']['typeinsertion'] == 'SOC' ) {
				$contratinsertion['Contratinsertion']['issociale'] = 'X';
			}
			else if( $contratinsertion['Contratinsertion']['typeinsertion'] == 'EMP' ) {
				$contratinsertion['Contratinsertion']['isemploi'] = 'X';
			}

			///Utilisé pour savoir si la personne est demandeur ou ayant droit
			if( $contratinsertion['Prestation']['rolepers'] == 'Demandeur du RSA' ) {
				$contratinsertion['Contratinsertion']['isallocataire'] = 'X';
			}
			else if( $contratinsertion['Prestation']['rolepers'] != 'Demandeur du RSA' ) {
				$contratinsertion['Contratinsertion']['isayantdroit'] = 'X';
			}

			///Utilisé pour savoir si la personne est demandeur ou ayant droit
			if( $contratinsertion['Contratinsertion']['num_contrat'] == 'PRE' ) {
				$contratinsertion['Contratinsertion']['ispremier'] = 'X';
			}
			else if( $contratinsertion['Contratinsertion']['num_contrat'] == 'REN' ) {
				$contratinsertion['Contratinsertion']['isrenouvel'] = 'X';
			}
			else if( !in_array( $contratinsertion['Contratinsertion']['num_contrat'], array( 'REN', 'PRE' ) ) ) {
				$contratinsertion['Contratinsertion']['isavenant'] = 'X';
			}

			// Permet d'afficher le nom de la zone géographique liée au contrat du cg58
			$contratinsertion['Contratinsertion']['zonegeographique_id'] = $contratinsertion['Zonegeographique']['libelle'];

			///Permet d'afficher le nb d'ouverture de droit de la personne
			$contratinsertion['Contratinsertion']['nbouv'] = $this->checkNumDemRsa( $contratinsertion['Personne']['id'] );

			$contratinsertion['Contratinsertion']['rg_ci'] = $contratinsertion['Contratinsertion']['rg_ci'] - 1;

			// Récupération de l'utilisateur connecté
			$user = $this->Contratinsertion->User->find(
					'first', array(
				'conditions' => array(
					'User.id' => $user_id
				),
				'contain' => array(
					'Serviceinstructeur'
				)
					)
			);
			$contratinsertion = Set::merge( $contratinsertion, $user );

			return $contratinsertion;
		}

		/**
		 * Retourne le PDF par défaut, stocké, ou généré par les appels aux méthodes getDataForPdf, modeleOdt et
		 * à la méthode ged du behavior Gedooo et le stocke,
		 *
		 * @param integer $id Id du CER
		 * @param integer $user_id Id de l'utilisateur connecté
		 * @return string
		 */
		public function getDefaultPdf( $id, $user_id ) {
			$pdf = $this->Contratinsertion->getStoredPdf( $id );

			if( !empty( $pdf ) ) {
				$pdf = $pdf['Pdf']['document'];
			}
			else {
				$Option = ClassRegistry::init( 'Option' );
				$options = Hash::merge(
					array(
						'Contratinsertion' => array(
							'typeocclog' => ClassRegistry::init('Foyer')->enum('typeocclog'),
						),
						'avisraison' => array(
							'ci' => $this->Contratinsertion->enum('avisraison_ci')
						),
						'duree' => array(
							'cdd' => $this->Contratinsertion->enum('duree_cdd')
						),
						'decision' => array(
							'ci' => $this->Contratinsertion->enum('decision_ci')
						),
						'raison' => array(
							'ci' => $this->Contratinsertion->enum('raison_ci')
						),
						'forme' => array(
							'ci' => array( 'S' => 'Simple', 'C' => 'Complexe' )
						),
						'Personne' => array(
							'qual' => $Option->qual(),
						),
						'Prestation' => array(
							'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers'),
						),
						'Foyer' => array(
							'sitfam' => $Option->sitfam(),
							'typeocclog' => ClassRegistry::init('Foyer')->enum('typeocclog'),
						),
						'Detaildroitrsa' => array(
							'oridemrsa' => ClassRegistry::init('Detaildroitrsa')->enum('oridemrsa'),
						),
					),
					$this->Contratinsertion->enums(),
					$this->Contratinsertion->Personne->Dsp->enums()
				);

				$contratinsertion = $this->getDataForPdf( $id, $user_id );
				$modeledoc = $this->modeleOdt( $contratinsertion );

				$pdf = $this->Contratinsertion->ged( $contratinsertion, $modeledoc, false, $options );

				if( !empty( $pdf ) ) {
					$this->Contratinsertion->storePdf( $id, $modeledoc, $pdf ); // FIXME ?
				}
			}

			return $pdf;
		}

		/**
		 * Retourne le PDF de notification du CER pour l'OP (CG 66).
		 *
		 * @param integer $id L'id du CER pour lequel générer la notification.
		 * @param integer $user_id L'id de l'utilisateur connecté générant la notification.
		 * @return string
		 */
		public function getNotificationopPdf( $id = null, $user_id = null ) {
			$contratinsertion = $this->Contratinsertion->find(
				'first', array(
					'fields' => array_merge(
						$this->Contratinsertion->fields(),
						$this->Contratinsertion->Personne->fields(),
						$this->Contratinsertion->Propodecisioncer66->fields(),
						$this->Contratinsertion->Referent->fields(),
						$this->Contratinsertion->Structurereferente->fields(),
						$this->Contratinsertion->Typocontrat->fields(),
						$this->Contratinsertion->Zonegeographique->fields(),
						$this->Contratinsertion->Personne->Foyer->fields(),
						$this->Contratinsertion->Personne->Foyer->Dossier->fields(),
						$this->Contratinsertion->Personne->Foyer->Adressefoyer->Adresse->fields()
					),
					'joins' => array(
						$this->Contratinsertion->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Contratinsertion->join( 'Propodecisioncer66', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$this->Contratinsertion->join( 'Typocontrat', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->join( 'Zonegeographique', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Contratinsertion->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$this->Contratinsertion->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Contratinsertion->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					),
					'conditions' => array(
						'Contratinsertion.id' => $id,
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.id IN ( '.$this->Contratinsertion->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
						)
					),
					'contain' => false
				)
			);

			$Option = ClassRegistry::init( 'Option' );
			$options = array(
				'Personne' => array(
					'qual' => $Option->qual()
				),
				'Referent' => array(
					'qual' => $Option->qual()
				)
			);

			$contratinsertion['Contratinsertion']['duree_engag'] = Set::enum( Set::classicExtract( $contratinsertion, 'Contratinsertion.duree_engag' ), $Option->duree_engag() );

			$user = $this->Contratinsertion->User->find(
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
			$contratinsertion = Set::merge( $contratinsertion, $user );

			///Utilisé pour savoir si le contrat est en accord de validation dans le modèle odt
			if( $contratinsertion['Contratinsertion']['decision_ci'] == 'V' ) {
				$contratinsertion['Contratinsertion']['accord'] = 'X';
			}

			///Utilisé pour savoir si le contrat est en premier contrat ou renouvellement
			if( $contratinsertion['Contratinsertion']['num_contrat'] == 'PRE' ) {
				$contratinsertion['Contratinsertion']['premier'] = 'X';
			}
			else if( $contratinsertion['Contratinsertion']['num_contrat'] == 'REN' ) {
				$contratinsertion['Contratinsertion']['renouvel'] = 'X';
			}

			return $this->Contratinsertion->ged(
							$contratinsertion, 'Contratinsertion/notificationop.odt', false, $options
			);
		}

		/**
		 * Retourne le PDF de notification de reconduction du CER pour les allocataires de + 55 ans(CG 66).
		 *
		 * @param integer $id L'id du CER pour lequel générer la notification.
		 * @return string
		 */
		public function getPdfReconductionCERPlus55Ans( $id, $user_id ) {
			$contratinsertion = $this->getDataForPdf( $id, $user_id );
			$Option = ClassRegistry::init( 'Option' );
			$options = array(
				'Personne' => array(
					'qual' => $Option->qual()
				),
				'Referent' => array(
					'qual' => $Option->qual()
				)
			);

			$contratinsertion['Contratinsertion']['duree_engag'] = Set::enum( Set::classicExtract( $contratinsertion, 'Contratinsertion.duree_engag' ), $Option->duree_engag() );

			return $this->Contratinsertion->ged(
				$contratinsertion,
				'Contratinsertion/tacitereconduction66.odt',
				false,
				$options
			);
		}

		/**
		 * Retourne un querydata permettant de connaître la liste des CER d'un allocataire, en fonction du CG
		 * (Configure::read( 'Cg.departement' )).
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function qdIndex( $personne_id ) {
			$querydata = array(
				'fields' => array(
					'Contratinsertion.id',
					'Contratinsertion.forme_ci',
					'Contratinsertion.decision_ci',
					'Contratinsertion.datedecision',
					'Contratinsertion.structurereferente_id',
					'Contratinsertion.referent_id',
					'Contratinsertion.num_contrat',
					'Contratinsertion.motifannulation',
					'Contratinsertion.dd_ci',
					'Contratinsertion.duree_engag',
					'Contratinsertion.positioncer',
					'Contratinsertion.df_ci',
					'Contratinsertion.date_saisi_ci',
					'Contratinsertion.observ_ci',
					'Contratinsertion.created',
					'Contratinsertion.datevalidation_ci',
					'Contratinsertion.datenotification',
					'Contratinsertion.avenant_id',
                    'Contratinsertion.actioncandidat_id',
					$this->Contratinsertion->Fichiermodule->sqNbFichiersLies( $this->Contratinsertion, 'nb_fichiers_lies' )
				),
				'conditions' => array(
					'Contratinsertion.personne_id' => $personne_id
				),
				'order' => array(
					'Contratinsertion.date_saisi_ci ASC',
					'Contratinsertion.df_ci DESC',
					'Contratinsertion.id DESC'
				),
				'contain' => false
			);

			// On veut connaître ...
			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$sqDernierPassageCov58 = $this->Contratinsertion->Propocontratinsertioncov58nv->Dossiercov58->Passagecov58->sqDernier();

				$querydata = Set::merge(
					$querydata,
					array(
						'fields' => array(
							'Sitecov58.name',
							'Cov58.observation',
							'Cov58.datecommission',
							'Decisionpropocontratinsertioncov58.commentaire'
						),
						'joins' => array(
							$this->Contratinsertion->join( 'Propocontratinsertioncov58nv', array( 'type' => 'LEFT OUTER' ) ),
							$this->Contratinsertion->Propocontratinsertioncov58nv->join( 'Dossiercov58', array( 'type' => 'LEFT OUTER' ) ),
							$this->Contratinsertion->Propocontratinsertioncov58nv->Dossiercov58->join( 'Passagecov58', array( 'type' => 'LEFT OUTER' ) ),
							$this->Contratinsertion->Propocontratinsertioncov58nv->Dossiercov58->Passagecov58->join( 'Cov58', array( 'type' => 'LEFT OUTER' ) ),
							$this->Contratinsertion->Propocontratinsertioncov58nv->Dossiercov58->Passagecov58->Cov58->join( 'Sitecov58', array( 'type' => 'LEFT OUTER' ) ),
							$this->Contratinsertion->Propocontratinsertioncov58nv->Dossiercov58->Passagecov58->join( 'Decisionpropocontratinsertioncov58', array( 'type' => 'LEFT OUTER' ) ),
						),
						'conditions' => array(
							'OR' => array(
								"Passagecov58.id IS NULL",
								"Passagecov58.id IN ( {$sqDernierPassageCov58} )"
							)
						)
					)
				);
			}
			else if( Configure::read( 'Cg.departement' ) == 66 ) {
				$querydata['joins'][] = $this->Contratinsertion->join( 'Personne', array( 'type' => 'INNER' ) );
				$querydata['fields'][] = '( ( EXTRACT ( YEAR FROM AGE( "Personne"."dtnai" ) ) ) >= 55 ) AS "Personne__plus55ans"';
				$querydata['fields'][] = 'Contratinsertion.num_contrat_66';

                $querydata['joins'][] = $this->Contratinsertion->join( 'Actioncandidat', array( 'type' => 'LEFT OUTER' ) );
				$querydata['fields'][] = 'Actioncandidat.name';

				$querydata = Set::merge(
					$querydata,
					array(
						'fields' => array_merge(
                            $this->Contratinsertion->Propodecisioncer66->fields()
                        ),
						'contain' => array(
                            'Propodecisioncer66'
                        )
					)
				);
			}
			else if( Configure::read( 'Cg.departement' ) == 976 ) {
				$querydata['fields'][] = 'Typeorient.lib_type_orient';
				$querydata['fields'][] = 'Structurereferente.lib_struc';
				$querydata['joins'][] = $this->Contratinsertion->join( 'Structurereferente', array( 'type' => 'INNER' ) );
				$querydata['joins'][] = $this->Contratinsertion->Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) );
			}

			return $querydata;
		}


        /**
         * Somme des durées de CER validés tel que ils soint liés à des
         * orientations sociales sans emploi au milieu.
         *
         * @param integer $personne_id
         * @return integer
         */
        public function limiteCumulDureeCER( $personne_id ){
            if( Configure::read( 'Cg.departement' ) == 66 ) {
                    $emploi = (array)Configure::read( 'Orientstruct.typeorientprincipale.Emploi' );
            }
            else if( Configure::read( 'Cg.departement' ) == 58 ) {
                    $emploi = (array)Configure::read( 'Typeorient.emploi_id' );
            }
            $emploi = '('.implode( ',', $emploi ).')';

            $sql = "SELECT SUM( contratsinsertion.duree_engag ) AS \"sum\"
                    FROM contratsinsertion
                    WHERE
                        decision_ci = 'V'
                        AND personne_id = {$personne_id}
                        AND positioncer <> 'annule'
                        AND contratsinsertion.datevalidation_ci >= (
                            SELECT
                                orientsstructs.date_valid
                            FROM orientsstructs
                            WHERE
                                orientsstructs.personne_id = contratsinsertion.personne_id
                                AND orientsstructs.statut_orient = 'Orienté'
                                AND (
                                    NOT EXISTS(
                                        SELECT *
                                            FROM orientsstructs AS osvt
                                            WHERE
                                                osvt.personne_id = orientsstructs.personne_id
                                                AND osvt.statut_orient = 'Orienté'
                                                AND osvt.date_valid > orientsstructs.date_valid
                                                AND osvt.typeorient_id IN {$emploi}
                                    )
                                )
                                AND orientsstructs.typeorient_id NOT IN {$emploi}
                                ORDER BY orientsstructs.date_valid ASC
                                LIMIT 1
                        )
                        AND (
                            NOT EXISTS(
                                SELECT bilansparcours66.id
                                    FROM bilansparcours66
                                    WHERE
                                        bilansparcours66.personne_id = contratsinsertion.personne_id
                                        AND (
                                            bilansparcours66.nvcontratinsertion_id IS NOT NULL
                                            OR (
                                                EXISTS (
                                                    SELECT saisinesbilansparcourseps66.bilanparcours66_id
                                                        FROM saisinesbilansparcourseps66
                                                        WHERE
                                                            bilansparcours66.id = saisinesbilansparcourseps66.bilanparcours66_id
                                                        ORDER BY saisinesbilansparcourseps66.created DESC
                                                        LIMIT 1
                                                )
                                            )
                                        )
                            )
                            OR (
                                contratsinsertion.dd_ci >= (
                                    SELECT bilansparcours66.created
                                    FROM bilansparcours66
                                    WHERE
                                        bilansparcours66.personne_id = contratsinsertion.personne_id
                                        AND (
                                            bilansparcours66.nvcontratinsertion_id IS NOT NULL
                                            OR (
                                                EXISTS (
                                                    SELECT saisinesbilansparcourseps66.bilanparcours66_id
                                                        FROM saisinesbilansparcourseps66
                                                        WHERE
                                                            bilansparcours66.id = saisinesbilansparcourseps66.bilanparcours66_id
                                                        ORDER BY saisinesbilansparcourseps66.created DESC
                                                        LIMIT 1
                                                )
                                            )
                                        )
                                    ORDER BY bilansparcours66.created DESC
                                    LIMIT 1
                                )
                            )
                        )
                    GROUP BY contratsinsertion.personne_id;";

            $result = $this->Contratinsertion->query( $sql );
            return ( isset( $result[0][0]['sum'] ) ? $result[0][0]['sum'] : 0 );
        }

		/**
		 * Retourne le rg_ci maximum pour un allocataire donné.
		 *
		 * @param integer $personne_id
		 * @return integer
		 */
		public function rgCiMax( $personne_id ) {
			return $this->Contratinsertion->find(
				'count',
				array(
					'conditions' => array(
						"{$this->Contratinsertion->alias}.decision_ci" => 'V',
						"{$this->Contratinsertion->alias}.personne_id" => $personne_id
					),
					'contain' => false
				)
			);
		}

		public function options() {
			$options = $this->Contratinsertion->enums();

			$options['Contratinsertion']['referent_id'] = $this->Contratinsertion->Referent->WebrsaReferent->listOptions();
			$options['Contratinsertion']['structurereferente_id'] = $this->Contratinsertion->Structurereferente->listOptions();

			return $options;
		}
	}