<?php
	/**
	 * Code source de la classe WebrsaRendezvous.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractLogic', 'Model');
	App::uses('WebrsaLogicAccessInterface', 'Model/Interface');

	/**
	 * La classe WebrsaRendezvous possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaRendezvous extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRendezvous';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Rendezvous');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$fields = array(
				'dernier' => $this->Rendezvous->sqVirtualField('dernier'),
			);

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
					'Rendezvous.id',
					'Rendezvous.personne_id',
				),
				'conditions' => $conditions,
				'contain' => false,
				'order' => array(
					'Rendezvous.daterdv DESC',
					'Rendezvous.heurerdv DESC'
				)
			);

			$results = $this->Rendezvous->find('all', $this->completeVirtualFieldsForAccess($query));
			return $results;
		}

		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 *
		 * @param integer $id
		 * @param String $modelName - nom du modèle qui désigne id : Personne : $id = $personne_id
		 * @return boolean
		 */
		public function ajoutPossible($id, $modelName = 'Personne') {
			if ((int)Configure::read('Cg.departement') !== 66) {
				return true;
			}

			$query = array(
				'fields' => 'Rendezvous.statutrdv_id',
				'contain' => false,
				'order' => array(
					'Rendezvous.daterdv DESC',
					'Rendezvous.heurerdv DESC'
				)
			);

			// On connait l'id de la Personne
			if ($modelName === 'Personne') {
				$query['conditions'] = array(
					'Rendezvous.personne_id' => $id,
				);
				$result = $this->Rendezvous->find('first', $query);
				$statutrdv_id = Hash::get($result, 'Rendezvous.statutrdv_id');

			// On ne connait que l'id du Rendezvous
			} elseif ($modelName === 'Rendezvous') {
				$query['conditions'] = array(
					'Rendezvous2.id' => $id,
				);
				$query['joins'] = array(
					array(
						'alias' => 'Rendezvous2',
						'table' => 'rendezvous',
						'conditions' => array(
							'Rendezvous2.personne_id = Rendezvous.personne_id',
						),
						'type' => 'INNER'
					)
				);
				$result = $this->Rendezvous->find('first', $query);
				$statutrdv_id = Hash::get($result, 'Rendezvous.statutrdv_id');

			// On connait déja l'id du Statutrdv
			} elseif ($modelName === 'Statutrdv') {
				$statutrdv_id = $id;

			// Erreur
			} else {
				trigger_error("modelName doit contenir Personne, Rendezvous ou Statutrdv");
				return false;
			}

			return !in_array(
				$statutrdv_id, (array)Configure::read('Rendezvous.Ajoutpossible.statutrdv_id')
			);
		}

		/**
		 * Vérifi si un Dossier de commission lié au rendez-vous existe
		 *
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function haveDossiercommissionLie($personne_id) {
			if ((int)Configure::read('Cg.departement') !== 58) {
				return false;
			}

			$query = array(
				'fields' => 'Rendezvous.id',
				'conditions' => array(
					'Rendezvous.personne_id' => $personne_id
				),
				'contain' => false,
				'order' => array(
					'Rendezvous.daterdv' => 'DESC',
					'Rendezvous.heurerdv' => 'DESC',
				)
			);
			$record = $this->Rendezvous->find('first', $query);
			$lastrdv_id = Hash::get($record, 'Rendezvous.id');

			$dossierepLie = $this->Rendezvous->Personne->Dossierep->find(
				'first',
				array(
					'fields' => array(
						'Dossierep.id'
					),
					'conditions' => array(
						'Dossierep.id IN ( '.
						$this->Rendezvous->Personne->Dossierep->Passagecommissionep->sq(
								array(
									'fields' => array(
										'passagescommissionseps.dossierep_id'
									),
									'alias' => 'passagescommissionseps',
									'conditions' => array(
										'passagescommissionseps.etatdossierep' => array( 'associe', 'decisionep', 'decisioncg', 'traite', 'annule', 'reporte' )
									)
								)
						)
						.' )'
					),
					'joins' => array(
						array(
							'table' => 'sanctionsrendezvouseps58',
							'alias' => 'Sanctionrendezvousep58',
							'type' => 'INNER',
							'conditions' => array(
								'Sanctionrendezvousep58.dossierep_id = Dossierep.id',
								'Sanctionrendezvousep58.rendezvous_id' => $lastrdv_id
							)
						)
					),
					'order' => array( 'Dossierep.created ASC' )
				)
			);

			if (Hash::get($dossierepLie, 'Dossierep.id')) {
				return true;
			}

			$dossiercovLie = $this->Rendezvous->Personne->Dossiercov58->find(
				'first',
				array(
					'fields' => array(
						'Dossiercov58.id'
					),
					'conditions' => array(
						'OR' => array(
							'Dossiercov58.id IS NULL',
							'Dossiercov58.id IN ( '.
								$this->Rendezvous->Personne->Dossiercov58->Passagecov58->sq(
									array(
										'fields' => array(
											'passagescovs58.dossiercov58_id'
										),
										'alias' => ' passagescovs58',
										'conditions' => array(
											'passagescovs58.etatdossiercov' => array( 'cree', 'associe', 'annule', 'reporte' )
										)
									)
								)
							.' )',
						),
						'Propoorientsocialecov58.rendezvous_id' => $lastrdv_id
					),
					'joins' => array(
						$this->Rendezvous->Personne->Dossiercov58->join( 'Propoorientsocialecov58', array( 'type' => 'LEFT OUTER' ) ),
						$this->Rendezvous->Personne->Dossiercov58->Propoorientsocialecov58->join( 'Rendezvous', array( 'type' => 'LEFT OUTER' ) )
					),
					'order' => array( 'Dossiercov58.created ASC' ),
					'contain' => false
				)
			);

			if (Hash::get($dossiercovLie, 'Dossiercov58.id')) {
				return true;
			} else {
				return false;
			}
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
			if (in_array('dossiercommissionLie', $params)) {
				$results['dossiercommissionLie'] = $this->haveDossiercommissionLie($personne_id);
			}

			return $results;
		}

		/**
		 * Retourne un booléen selon si un dossier d'EP doit ou non
		 * être créé pour la personne dont l'id est passé en paramètre
		 *
		 * @param array $data
		 * @return boolean
		 */
		public function passageEp( $data ) {
			// 1. Pour le type et le statut du RDV que l'on enregistre, doit-on créer un passage en commission ?
			$statutrdvtyperdv = $this->Rendezvous->Typerdv->StatutrdvTyperdv->find(
				'first',
				array(
					'conditions' => array(
						'StatutrdvTyperdv.typerdv_id' => $data['Rendezvous']['typerdv_id'],
						'StatutrdvTyperdv.statutrdv_id' => $data['Rendezvous']['statutrdv_id'],
						'StatutrdvTyperdv.typecommission' => array('ep', 'cov'),
						'StatutrdvTyperdv.actif' => true,
					),
					'contain' => false
				)
			);

			if( empty( $statutrdvtyperdv ) ) {
				return false;
			}

			// 2. Existe-t'il suffisamment de rendez-vous précédents des mêmes types et statuts ?
			$nbRdvPcd = ( $statutrdvtyperdv['StatutrdvTyperdv']['nbabsenceavantpassagecommission'] - 1 );
			if( !$this->nombreRendezvousSuffisant($nbRdvPcd, $data) ) {
				return false;
			}

			// 3. Existe-t'il déjà un passage en commission en cours pour la même raison ?
			if( $statutrdvtyperdv['StatutrdvTyperdv']['typecommission'] == 'ep' ) {
				$dossiercommission = $this->Rendezvous->Personne->Dossierep->find(
					'first',
					array(
						'conditions' => array(
							'Dossierep.actif' => '1',
							'Dossierep.personne_id' => $data['Rendezvous']['personne_id'],
							'Dossierep.themeep' => 'sanctionsrendezvouseps58',
							'Dossierep.id NOT IN ( '.
								$this->Rendezvous->Personne->Dossierep->Passagecommissionep->sq(
									array(
										'fields' => array(
											'passagescommissionseps.dossierep_id'
										),
										'alias' => 'passagescommissionseps',
										'conditions' => array(
											'passagescommissionseps.etatdossierep' => array ( 'traite', 'annule' )
										)
									)
								)
							.' )'
						),
						'contain' => array(
							'Sanctionrendezvousep58' => array(
								'Rendezvous' => array(
									'conditions' => array(
										'Rendezvous.typerdv_id' => $data['Rendezvous']['typerdv_id']
									)
								)
							)
						)
					)
				);
			}
			else {
				$dossiercommission = $this->Rendezvous->Personne->Dossiercov58->find(
					'first',
					array(
						'conditions' => array(
							'Dossiercov58.personne_id' => $data['Rendezvous']['personne_id'],
							'Dossiercov58.themecov58' => 'proposorientssocialescovs58',
							'Dossiercov58.id NOT IN ( '.
								$this->Rendezvous->Personne->Dossiercov58->Passagecov58->sq(
									array(
										'alias' => 'passagescovs58',
										'fields' => array(
											'passagescovs58.dossiercov58_id'
										),
										'conditions' => array(
											'passagescovs58.etatdossiercov' => array ( 'traite', 'annule' )
										)
									)
								)
							.' )'
						)
					)
				);
			}

			return empty( $dossiercommission );
		}

		/**
		 * Retourne le PDF d'un rendez-vous.
		 *
		 * @param integer $id L'id du rendez-vous pour lequel générer l'impression
		 * @param $user_id L'id de l'utilisateur qui génère l'impression.
		 * @return string
		 */
		public function getDefaultPdf( $id, $user_id ) {
			$rdv = $this->Rendezvous->find(
				'first',
				array(
					'fields' => array_merge(
						$this->Rendezvous->fields(),
						$this->Rendezvous->Permanence->fields(),
						$this->Rendezvous->Personne->fields(),
						$this->Rendezvous->Referent->fields(),
						$this->Rendezvous->Statutrdv->fields(),
						$this->Rendezvous->Structurereferente->fields(),
						$this->Rendezvous->Typerdv->fields(),
						$this->Rendezvous->Personne->Foyer->fields(),
						$this->Rendezvous->Personne->Foyer->Adressefoyer->Adresse->fields()
					),
					'joins' => array(
						$this->Rendezvous->join( 'Permanence', array( 'type' => 'LEFT OUTER' ) ),
						$this->Rendezvous->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Rendezvous->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
						$this->Rendezvous->join( 'Statutrdv', array( 'type' => 'LEFT OUTER' ) ),
						$this->Rendezvous->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$this->Rendezvous->join( 'Typerdv', array( 'type' => 'LEFT OUTER' ) ),
						$this->Rendezvous->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Rendezvous->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$this->Rendezvous->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					),
					'conditions' => array(
						'Rendezvous.id' => $id,
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.id IN ( '.$this->Rendezvous->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
						)
					),
					'contain' => false
				)
			);

			// Recherche spécifique sur le dossier pour récupérer les champs virtuels
			$dossier = $this->Rendezvous->Personne->Foyer->Dossier->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					"Dossier.id" => $rdv['Foyer']['dossier_id']
				)
			));
			$rdv['Dossier'] = $dossier['Dossier'];

			$User = ClassRegistry::init( 'User' );
			$user = $User->find(
				'first',
				array(
					'fields' => array_merge(
						$User->fields(),
						$User->Serviceinstructeur->fields()
					),
					'joins' => array(
						$User->join( 'Serviceinstructeur', array( 'type' => 'INNER' ) )
					),
					'conditions' => array(
						'User.id' => $user_id
					),
					'contain' => false
				)
			);
			$rdv = Set::merge( $rdv, $user );

			$rdv['Rendezvous']['heurerdv'] = date( "H:i", strtotime( $rdv['Rendezvous']['heurerdv'] ) );

			// Utilisation des thématiques de RDV ?
			$rdv = $this->Rendezvous->containThematique( $rdv );
			$thematiquesrdvs = $rdv['Thematiquerdv'];
			unset( $rdv['Thematiquerdv'] );

			if( !empty( $thematiquesrdvs ) ) {
				foreach( $thematiquesrdvs as $key => $values ) {
					$thematiquesrdvs[$key] = array( 'Thematiquerdv' => $values );
				}
			}

			$rdv = array(
				$rdv,
				'thematiquesrdvs' => $thematiquesrdvs
			);

			// Ajout du rendez vous précédent
			$rdvprec = $this->Rendezvous->find('first', array(
				'conditions' => array(
					"Rendezvous.daterdv < " => $rdv[0]['Rendezvous']['daterdv'],
					"Rendezvous.personne_id" => $rdv[0]['Personne']['id']
				),
				'order' => array(
					'Rendezvous.daterdv DESC',
					'Rendezvous.heurerdv DESC'
				)
			));
			if(!empty($rdvprec)) {
				$rdv[0]['Rdvprecedent'] = $rdvprec['Rendezvous'];
			}

			$Option = ClassRegistry::init( 'Option' );
			$options = array(
				'Personne' => array(
					'qual' => $Option->qual()
				),
				'Referent' => array(
					'qual' => $Option->qual()
				)
			);

			return $this->Rendezvous->ged(
				$rdv,
				"RDV/{$rdv[0]['Typerdv']['modelenotifrdv']}.odt",
				true,
				$options
			);
		}

		/**
		 * FIXME: devrait remplacer la méthode passageEp ?
		 *
		 * @param type $data
		 * @return type
		 */
		public function provoquePassageCommission( $data ) {
			return (
				Configure::read( 'Cg.departement' ) == 58
				&& !empty( $data['Rendezvous']['statutrdv_id'] )
				&& $this->Rendezvous->Statutrdv->provoquePassageCommission( $data['Rendezvous']['statutrdv_id'] )
				&& ($this->passageEp( $data ) || $this->orientationACreer($data))
			);
		}

		/**
		 * FIXME: le nombre vient du nouveau champ
		 *
		 * @param type $data
		 * @param type $user_id
		 * @return type
		 */
		public function creePassageCommission( $data, $user_id, $origine = 'manuelle' ) {
			$success = true;
			$statutrdv_typerdv_list = $this->Rendezvous->Statutrdv->StatutrdvTyperdv->find(
				'all',
				array(
					'conditions' => array(
						'StatutrdvTyperdv.statutrdv_id' => $data['Rendezvous']['statutrdv_id'],
						'StatutrdvTyperdv.typerdv_id' => $data['Rendezvous']['typerdv_id'],
					),
					'contain' => false
				)
			);

			//On peut avoir plusieurs actions à effectuer
			foreach ($statutrdv_typerdv_list as $statutrdv_typerdv){
				switch ($statutrdv_typerdv['StatutrdvTyperdv']['typecommission']) {

					case 'ep':
						$this->Rendezvous->Personne->Dossierep->clear();
						$dossierep = array(
							'Dossierep' => array(
								'personne_id' => $data['Rendezvous']['personne_id'],
								'themeep' => 'sanctionsrendezvouseps58'
							)
						);
						$success = $this->Rendezvous->Personne->Dossierep->save( $dossierep , array( 'atomic' => false ) ) && $success;

						$sanctionrendezvousep58 = array(
							'Sanctionrendezvousep58' => array(
								'dossierep_id' => $this->Rendezvous->Personne->Dossierep->id,
								'rendezvous_id' =>  $data['Rendezvous']['id']
							)
						);

						$success = $this->Rendezvous->Personne->Dossierep->Sanctionrendezvousep58->save( $sanctionrendezvousep58 , array( 'atomic' => false ) ) && $success;
						break;

					case 'cov':
						$this->Rendezvous->Personne->Dossiercov58->clear();
						$this->Rendezvous->Propoorientsocialecov58->clear();
						$themecov58_id = $this->Rendezvous->Propoorientsocialecov58->Dossiercov58->Themecov58->field( 'id', array( 'name' => 'proposorientssocialescovs58' ) );
						$dossiercov58 = array(
							'Dossiercov58' => array(
								'personne_id' => $data['Rendezvous']['personne_id'],
								'themecov58' => 'proposorientssocialescovs58',
								'themecov58_id' => $themecov58_id,
							)
						);
						$success = $this->Rendezvous->Personne->Dossiercov58->save( $dossiercov58 , array( 'atomic' => false ) ) && $success;

						$propoorientsocialecov58 = array(
							'Propoorientsocialecov58' => array(
								'dossiercov58_id' => $this->Rendezvous->Propoorientsocialecov58->Dossiercov58->id,
								'rendezvous_id' => $this->Rendezvous->id,
								'user_id' => $user_id
							)
						);

						$success = $this->Rendezvous->Propoorientsocialecov58->save( $propoorientsocialecov58 , array( 'atomic' => false ) ) && $success;
						break;

					case 'orientation':
						$this->Rendezvous->Personne->Orientstruct->clear();
						// on crée une orientation
						$referent = $this->Rendezvous->Personne->PersonneReferent->find(
							'first',
							array(
								'conditions' => array(
									'dfdesignation' => null,
									'personne_id' => $data['Rendezvous']['personne_id']
								)
							)
						);
						$date_du_jour = date('Y-m-d', time());
						$orientation['Orientstruct'] = array(
							'personne_id' => $data['Rendezvous']['personne_id'],
							'statut_orient' => 'Orienté',
							'date_propo' => $date_du_jour,
							'date_valid' => $date_du_jour,
							'origine' => $origine
						);
						if(!empty($referent)){
							$referent_id = $referent['Referent']['id'];
							$structurereferente_id = $referent['Structurereferente']['id'];
							$typeorient_id = $referent['Structurereferente']['typeorient_id'];
							$orientation['Orientstruct']['typeorient_id'] = $typeorient_id;
							$orientation['Orientstruct']['structurereferente_id'] = $structurereferente_id;
							$orientation['Orientstruct']['referent_id'] = $referent_id;
						}
						$success = $this->Rendezvous->Personne->Orientstruct->save( $orientation , array( 'atomic' => false ) ) && $success;
						break;

				}
			}

			return $success;
		}

		/**
		 * Retourne la liste des rendez-vous d'une personne, ordonnés par date
		 * et heure (du plus récent au plus ancien), et libellé de l'objet,
		 * formattés comme suit:
		 * <pre>
		 * array(
		 *	<Id du RDV> => "<Objet du RDV> du <Date du RDV> à <Heure du RDV>"
		 * )
		 * </pre>
		 *
		 * @param integer $personne_id L'id de la personne
		 * @return array
		 */
		public function findListPersonneId( $personne_id ) {
			$rendezvous = array();

			$results = $this->Rendezvous->find(
				'all',
				array(
					'fields' => array(
						'Rendezvous.id',
						'Rendezvous.daterdv',
						'Rendezvous.heurerdv',
						'Typerdv.libelle',
					),
					'contain' => false,
					'conditions' => array(
						'Rendezvous.personne_id' => $personne_id
					),
					'joins' => array(
						$this->Rendezvous->join( 'Typerdv', array( 'type' => 'INNER' ) )
					),
					'order' => array(
						'Rendezvous.daterdv DESC',
						'Rendezvous.heurerdv DESC',
						'Typerdv.libelle ASC',
					)
				)
			);

			if( !empty( $results ) ) {
				foreach( $results as $result ) {
					$rendezvous[$result['Rendezvous']['id']] = sprintf(
						'%s du %s à %s',
						$result['Typerdv']['libelle'],
						date( 'd/m/Y', strtotime( $result['Rendezvous']['daterdv'] ) ),
						date( 'H:i:s', strtotime( $result['Rendezvous']['heurerdv'] ) )
					);
				}
			}

			return $rendezvous;
		}

		/**
		 * Ajoute des conditions sur les thématiques de RDV.
		 *
		 * A utiliser dans les cohortes et moteur de recherche.
		 *
		 * Exemple:
		 * <pre>$this->Rendezvous->WebrsaRendezvous->conditionsThematique(
		 *	array(),
		 *	array(
		 *		'Rendezvous' => array(
		 *			'thematiquerdv_id' => array(
		 *				0 => 3,
		 *				1 => 5
		 *			)
		 *		)
		 *	),
		 *	'Rendezvous.thematiquerdv_id'
		 * );</pre>
		 * retournera
		 * <pre>array( Rendezvous.id IN ( SELECT "rendezvous_thematiquesrdvs"."rendezvous_id" AS rendezvous_thematiquesrdvs__rendezvous_id FROM thematiquesrdvs AS thematiquesrdvs INNER JOIN "public"."rendezvous_thematiquesrdvs" AS rendezvous_thematiquesrdvs ON ("rendezvous_thematiquesrdvs"."rendezvous_id" = "Rendezvous"."id") WHERE "rendezvous_thematiquesrdvs"."rendezvous_id" = "Rendezvous"."id" AND "rendezvous_thematiquesrdvs"."thematiquerdv_id" IN ('3', '5') )  )</pre>
		 *
		 * @param array $conditions Les conditions déjà existantes
		 * @param array $search Les critères renvoyés par le formulaire de recherche
		 * @param mixed $paths Le chemin (ou les chemins) sur lesquels on cherche à appliquer ces filtres.
		 * @return array
		 */
		public function conditionsThematique( $conditions, $search, $paths, array $replacements = array() ) {
			$paths = (array)$paths;
			$replacements += array(
				'RendezvousThematiquerdv' => 'rendezvous_thematiquesrdvs',
				'Thematiquerdv' => 'thematiquesrdvs'
			);

			foreach( $paths as $path ) {
				$thematiquerdv_id = Hash::get( $search, $path );
				if( !empty( $thematiquerdv_id ) ) {
					$qd = array(
						'alias' => 'Thematiquerdv',
						'fields' => array( 'RendezvousThematiquerdv.rendezvous_id' ),
						'contain' => false,
						'joins' => array(
							$this->Rendezvous->join( 'RendezvousThematiquerdv', array( 'type' => 'INNER' ) )
						),
						'conditions' => array(
							'RendezvousThematiquerdv.rendezvous_id = Rendezvous.id',
							'RendezvousThematiquerdv.thematiquerdv_id' => $thematiquerdv_id,
						)

					);
					$qd = array_words_replace( $qd, $replacements );

					$sq = $this->Rendezvous->Thematiquerdv->sq( $qd );
					$conditions[] = "Rendezvous.id IN ( {$sq} )";
				}
			}

			return $conditions;
		}

		/**
		 * Retourne un champ virtuel contenant la liste des theématiques liées à
		 * une RDV, séparées par la chaîne de caractères $glue.
		 *
		 * Si le nom du champ virtuel est vide, alors le champ non aliasé sera
		 * retourné.
		 *
		 * @see Configure Rendezvous.useThematique
		 *
		 * @param string $fieldName Le nom du champ virtuel; le modèle sera l'alias
		 *	du modèle (Rendezvous) utilisé.
		 * @param string $glue La chaîne de caratcères utilisée pour séparer les
		 *	noms des aides.
		 * @return string
		 */
		public function vfListeThematiques( $fieldName = 'thematiques', $glue = '\\n\r-' ) {
			$query = array(
				'fields' => array( 'Thematiquerdv.name' ),
				'alias' => 'rendezvous_thematiquesrdvs',
				'joins' => array(
					$this->Rendezvous->RendezvousThematiquerdv->join( 'Thematiquerdv', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					'RendezvousThematiquerdv.rendezvous_id = Rendezvous.id'
				),
				'contain' => false
			);
			$replacements = array( 'RendezvousThematiquerdv' => 'rendezvous_thematiquesrdvs', 'Thematiquerdv' => 'thematiquesrdvs' );
			$query = array_words_replace( $query, $replacements );

			$sql = "TRIM( BOTH ' ' FROM TRIM( TRAILING '{$glue}' FROM ARRAY_TO_STRING( ARRAY( ".$this->Rendezvous->RendezvousThematiquerdv->sq( $query )." ), '{$glue}' ) ) )";

			if( !empty( $fieldName ) ) {
				$sql = "{$sql} AS \"{$this->Rendezvous->alias}__{$fieldName}\"";
			}

			return $sql;
		}

		public function orientationACreer($data){
			// 1. Pour le type le statut du RDV que l'on enregistre, doit-on créer une orientation ?
			$statutrdvtyperdv = $this->Rendezvous->Typerdv->StatutrdvTyperdv->find(
				'first',
				array(
					'conditions' => array(
						'StatutrdvTyperdv.typerdv_id' => $data['Rendezvous']['typerdv_id'],
						'StatutrdvTyperdv.statutrdv_id' => $data['Rendezvous']['statutrdv_id'],
						'StatutrdvTyperdv.typecommission' => array('orientation'),
						'StatutrdvTyperdv.actif' => true
					),
					'contain' => false
				)
			);

			if( empty( $statutrdvtyperdv ) ) {
				return false;
			}

			// 2. Existe-t'il suffisamment de rendez-vous précédents des mêmes types et statuts ?
			$nbRdvPcd = ( $statutrdvtyperdv['StatutrdvTyperdv']['nbabsenceavantpassagecommission'] - 1 );

			return $this->nombreRendezvousSuffisant($nbRdvPcd, $data);

		}

		public function nombreRendezvousSuffisant($nbRdvPcd, $data){

			if( $nbRdvPcd > 0 ) {
				$daterdv = $data['Rendezvous']['daterdv'];
				if( is_array( $daterdv ) ) {
					$daterdv = date_cakephp_to_sql( $daterdv );
				}

				$heurerdv = $data['Rendezvous']['heurerdv'];
				if( is_array( $heurerdv ) ) {
					$heurerdv = time_cakephp_to_sql( $heurerdv );
				}

				$query = array(
					'fields' => array(
						'Rendezvous.typerdv_id',
						'Rendezvous.statutrdv_id'
					),
					'contain' => false,
					'conditions' => array(
						'Rendezvous.personne_id' => Hash::get( $data, 'Rendezvous.personne_id' )
					),
					'order' => array(
						'Rendezvous.daterdv' => 'DESC',
						'Rendezvous.heurerdv' => 'DESC',
						'Rendezvous.id' => 'DESC'
					),
					'limit' => $nbRdvPcd
				);

				// Ici, le compteur à revoir...
				$id = Hash::get( $data, "{$this->Rendezvous->alias}.{$this->Rendezvous->primaryKey}" );
				$action = ( empty( $id ) ? 'add' : 'edit' );

				if( $action === 'add' ) {
					$query['conditions']["( Rendezvous.daterdv || ' ' || Rendezvous.heurerdv )::TIMESTAMP <"] = "{$daterdv} {$heurerdv}";
				}
				else {
					$query['conditions'][] = array(
						'OR' => array(
							"( Rendezvous.daterdv || ' ' || Rendezvous.heurerdv )::TIMESTAMP <" => "{$daterdv} {$heurerdv}",
							array(
								"( Rendezvous.daterdv || ' ' || Rendezvous.heurerdv )::TIMESTAMP" => "{$daterdv} {$heurerdv}",
								'Rendezvous.id <' => $id
							)
						)
					);
				}

				$rdvs = $this->Rendezvous->find( 'all', $query );

				$creation = ( count($rdvs) == $nbRdvPcd );
				foreach( $rdvs as $rdv ) {
					if(
						( $rdv['Rendezvous']['typerdv_id'] != $data['Rendezvous']['typerdv_id'] )
						|| ( $rdv['Rendezvous']['statutrdv_id'] != $data['Rendezvous']['statutrdv_id'] )
					) {
						$creation = false;
					}
				}
			} else {
				return true;
			}

			return $creation;
		}
}