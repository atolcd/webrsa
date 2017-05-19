<?php
	/**
	 * Code source de la classe Propoorientationcov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractThematiquecov58', 'Model/Abstractclass' );

	/**
	 * La classe Propoorientationcov58 ...
	 *
	 * @package app.Model
	 */
	class Propoorientationcov58 extends AbstractThematiquecov58
	{
		public $name = 'Propoorientationcov58';

		/**
		* Chemin relatif pour les modèles de documents .odt utilisés lors des
		* impressions. Utiliser %s pour remplacer par l'alias.
		*/
		public $modelesOdt = array(
			'Cov58/decisionorientationpro.odt',
			'Cov58/decisionreorientationpro.odt',
			'Cov58/decisionorientationsoc.odt',
			'Cov58/decisionreorientationsoc.odt',
			'Cov58/decisionrefusreorientation.odt',
		);

		public $actsAs = array(
			'Containable',
			'Dependencies',
			'Gedooo.Gedooo',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'structurereferente_id' => array(
				'choixStructure' => array(
					'rule' => array( 'choixStructure', 'statut_orient' ),
					'message' => 'Champ obligatoire'
				),
				'dependentForeignKeys' => array(
					'rule' => array( 'dependentForeignKeys', 'Structurereferente', 'Typeorient' ),
					'message' => 'La structure référente ne correspond pas au type d\'orientation',
				),
			),
			'referent_id' => array(
				'dependentForeignKeys' => array(
					'rule' => array( 'dependentForeignKeys', 'Referent', 'Structurereferente' ),
					'message' => 'Le référent n\'appartient pas à la structure référente',
				),
			),
			'referentorientant_id' => array(
				'dependentForeignKeys' => array(
					'rule' => array( 'dependentForeignKeys', 'Referentorientant', 'Structureorientante', 'Structurereferente' ),
					'message' => 'La référent orientant n\'appartient pas à la structure chargée de l\'évaluation',
				),
			),
			'date_propo' => array(
				'date' => array(
					'rule' => array( 'date' ),
					'message' => 'Veuillez entrer une date valide'
				)
			),
			'date_valid' => array(
				'date' => array(
					'rule' => array( 'date' ),
					'message' => 'Veuillez entrer une date valide'
				)
			)
		);

		public $belongsTo = array(
			'Nvorientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'nvorientstruct_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typeorient' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'typeorient_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Covtypeorient' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'covtypeorient_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Covstructurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'covstructurereferente_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Referent' => array(
				'className' => 'Referent',
				'foreignKey' => 'referent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Dossiercov58' => array(
				'className' => 'Dossiercov58',
				'foreignKey' => 'dossiercov58_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Structureorientante' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structureorientante_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Referentorientant' => array(
				'className' => 'Referent',
				'foreignKey' => 'referentorientant_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		/**
		 * Règle de validation.
		 *
		 * @param type $field
		 * @param type $compare_field
		 * @return boolean
		 */
		public function choixStructure( $field = array(), $compare_field = null ) {
			foreach( $field as $key => $value ) {
				if( !empty( $this->data[$this->name][$compare_field] ) && ( $this->data[$this->name][$compare_field] != 'En attente' ) && empty( $value ) ) {
					return false;
				}
			}
			return true;
		}

		/**
		 *
		 * @deprecated
		 */
		public function getFields() {
			return array(
				$this->alias.'.id',
				$this->alias.'.datedemande',
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Referent.qual',
				'Referent.nom',
				'Referent.prenom'
			);
		}

		/**
		 *
		 * @deprecated
		 */
		public function getJoins() {
			return array(
				array(
					'table' => 'proposorientationscovs58',
					'alias' => $this->alias,
					'type' => 'INNER',
					'conditions' => array(
						'Dossiercov58.id = Propoorientationcov58.dossiercov58_id'
					)
				),
				array(
					'table' => 'structuresreferentes',
					'alias' => 'Structurereferente',
					'type' => 'INNER',
					'conditions' => array(
						'Propoorientationcov58.structurereferente_id = Structurereferente.id'
					)
				),
				array(
					'table' => 'typesorients',
					'alias' => 'Typeorient',
					'type' => 'INNER',
					'conditions' => array(
						'Propoorientationcov58.typeorient_id = Typeorient.id'
					)
				),
				array(
					'table' => 'referents',
					'alias' => 'Referent',
					'type' => 'LEFT OUTER',
					'conditions' => array(
						'Propoorientationcov58.referent_id = Referent.id'
					)
				)
			);
		}

		/**
		 * Retourne le querydata utilisé dans la partie décisions d'une COV.
		 *
		 * Surchargé afin d'ajouter le modèle de la thématique et le modèle de
		 * décision dans le contain.
		 *
		 * @param integer $cov58_id
		 * @return array
		 */
		public function qdDossiersParListe( $cov58_id ) {
			$result = parent::qdDossiersParListe( $cov58_id );
			$modeleDecision = 'Decision'.Inflector::underscore( $this->alias );

			$result['contain'][$this->alias] = array(
				'Typeorient',
				'Structurereferente',
				'Referent'
			);

			$result['contain']['Passagecov58'][$modeleDecision] = array(
				'Typeorient',
				'Structurereferente',
				'order' => array( 'etapecov DESC' )
			);

			return $result;
		}

		/**
		* FIXME -> aucun dossier en cours, pour certains thèmes:
		*		- CG 93
		*			* Nonrespectsanctionep93 -> ne débouche pas sur une orientation: '1reduction', '1maintien', '1sursis', '2suspensiontotale', '2suspensionpartielle', '2maintien'
		*			* Propoorientationcov58 -> peut déboucher sur une réorientation
		*		- CG 66
		*			* Defautinsertionep66 -> peut déboucher sur une orientation: 'suspensionnonrespect', 'suspensiondefaut', 'maintien', 'reorientationprofverssoc', 'reorientationsocversprof'
		*			* Saisinebilanparcoursep66 -> peut déboucher sur une réorientation
		*			* Saisinepdoep66 -> 'CAN', 'RSP' -> ne débouche pas sur une orientation
		* FIXME -> CG 93: s'il existe une procédure de relance, on veut faire signer un contrat,
					mais on veut peut-être aussi demander une réorientation.
		* FIXME -> doit-on vérifier si:
		* 			- la personne est soumise à droits et devoirs (oui)
		*			- la personne est demandeur ou conjoint RSA (oui) ?
		*			- le dossier est dans un état ouvert (non) ?
		*/

		public function ajoutPossible( $personne_id ) {
			$nbDossierscov = $this->Dossiercov58->find(
				'count',
				array(
					'conditions' => array(
						'Dossiercov58.personne_id' => $personne_id/*,
						'Dossiercov58.etapecovcov <>' => 'finalise'*/
					),
					'contain' => array(
						'Propoorientationcov58'
					)
				)
			);

			$nbPersonnes = $this->Personne->find(
				'count',
				array(
					'conditions' => array(
						'Personne.id' => $personne_id,
					),
					'joins' => array(
						array(
							'table'      => 'prestations',
							'alias'      => 'Prestation',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Personne.id = Prestation.personne_id',
								'Prestation.natprest = \'RSA\'',
								'Prestation.rolepers' => array( 'DEM', 'CJT' )
							)
						),
						array(
							'table'      => 'calculsdroitsrsa',
							'alias'      => 'Calculdroitrsa',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Personne.id = Calculdroitrsa.personne_id',
								'Calculdroitrsa.toppersdrodevorsa' => '1'
							)
						),
						array(
							'table'      => 'foyers',
							'alias'      => 'Foyer',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Foyer.id = Personne.foyer_id' )
						),
						array(
							'table'      => 'dossiers',
							'alias'      => 'Dossier',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Foyer.dossier_id = Dossier.id' )
						),
						array(
							'table'      => 'situationsdossiersrsa',
							'alias'      => 'Situationdossierrsa',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Situationdossierrsa.dossier_id = Dossier.id',
								'Situationdossierrsa.etatdosrsa' => array( 'Z', '2', '3', '4' )
							)
						),
					),
					'recursive' => -1
				)
			);

			return ( ( $nbDossierscov == 0 ) && ( $nbPersonnes == 1 ) );
		}



		/**
		*
		*/

		public function saveDecisions( $data ) {
			$modelDecisionName = 'Decision'.Inflector::underscore( $this->alias );
			$success = true;

			if ( isset( $data[$modelDecisionName] ) && !empty( $data[$modelDecisionName] ) ) {
				foreach( $data[$modelDecisionName] as $key => $values ) {
					$passagecov58 = $this->Dossiercov58->Passagecov58->find(
						'first',
						array(
							'fields' => array_merge(
								$this->Dossiercov58->Passagecov58->fields(),
								$this->Dossiercov58->Passagecov58->Cov58->fields(),
								$this->Dossiercov58->fields(),
								$this->fields()
							),
							'conditions' => array(
								'Passagecov58.id' => $values['passagecov58_id']
							),
							'joins' => array(
								$this->Dossiercov58->Passagecov58->join( 'Dossiercov58' ),
								$this->Dossiercov58->Passagecov58->join( 'Cov58' ),
								$this->Dossiercov58->join( $this->alias )
							)
						)
					);

					if( in_array( $values['decisioncov'], array( 'valide', 'refuse' ) ) ){
						$rgorient = $this->Dossiercov58->Personne->Orientstruct->WebrsaOrientstruct->rgorientMax( $passagecov58['Dossiercov58']['personne_id'] ) + 1;
						$origine = ( $rgorient > 1 ? 'reorientation' : 'manuelle' );

						if( $values['decisioncov'] == 'valide' ){
							$data[$modelDecisionName][$key]['typeorient_id'] = $passagecov58[$this->alias]['typeorient_id'];
							$data[$modelDecisionName][$key]['structurereferente_id'] = $passagecov58[$this->alias]['structurereferente_id'];
							$data[$modelDecisionName][$key]['referent_id'] = $passagecov58[$this->alias]['referent_id'];

							list($datevalidation, $heure) = explode(' ', $passagecov58['Cov58']['datecommission']);

							$orientstruct = array(
								'Orientstruct' => array(
									'personne_id' => $passagecov58['Dossiercov58']['personne_id'],
									'typeorient_id' => $passagecov58[$this->alias]['typeorient_id'],
									'structurereferente_id' => $passagecov58[$this->alias]['structurereferente_id'],
									'referent_id' => $passagecov58[$this->alias]['referent_id'],
									'date_propo' => $passagecov58['Propoorientationcov58']['datedemande'],
									'date_valid' => $datevalidation,
									'rgorient' => $rgorient,
									'statut_orient' => 'Orienté',
									'etatorient' => 'decision',
									'origine' => $origine,
									'user_id' => $passagecov58['Propoorientationcov58']['user_id'],
								)
							);

							$success = $this->Dossiercov58->Personne->PersonneReferent->changeReferentParcours(
								$passagecov58['Dossiercov58']['personne_id'],
								$passagecov58[$this->alias]['referent_id'],
								array(
									'PersonneReferent' => array(
										'personne_id' => $passagecov58['Dossiercov58']['personne_id'],
										'referent_id' => $passagecov58[$this->alias]['referent_id'],
										'dddesignation' => $datevalidation,
										'structurereferente_id' => $passagecov58[$this->alias]['structurereferente_id'],
										'user_id' => $passagecov58[$this->alias]['user_id']
									)
								)
							) && $success;
						}
						else if( $values['decisioncov'] == 'refuse' ) {
							$typeorient_id = suffix( $values['typeorient_id'] );
							$structurereferente_id = suffix( $values['structurereferente_id'] );
							$referent_id = suffix( $values['referent_id'] );

							$data[$modelDecisionName][$key]['typeorient_id'] = $typeorient_id;
							$data[$modelDecisionName][$key]['structurereferente_id'] = $structurereferente_id;
							$data[$modelDecisionName][$key]['referent_id'] = $referent_id;

							$data[$modelDecisionName][$key]['datevalidation'] = $passagecov58['Cov58']['datecommission'];
							list($datevalidation, $heure) = explode(' ', $passagecov58['Cov58']['datecommission']);

							$orientstruct = array(
								'Orientstruct' => array(
									'personne_id' => $passagecov58['Dossiercov58']['personne_id'],
									'typeorient_id' => $data[$modelDecisionName][$key]['typeorient_id'],
									'structurereferente_id' => $data[$modelDecisionName][$key]['structurereferente_id'],
									'referent_id' => $data[$modelDecisionName][$key]['referent_id'],
									'date_propo' => $passagecov58['Propoorientationcov58']['datedemande'],
									'date_valid' => $datevalidation,
									'rgorient' => $rgorient,
									'statut_orient' => 'Orienté',
									'etatorient' => 'decision',
									'origine' => $origine,
									'user_id' => $passagecov58['Propoorientationcov58']['user_id']
								)
							);

							$success = $this->Dossiercov58->Personne->PersonneReferent->changeReferentParcours(
								$passagecov58['Dossiercov58']['personne_id'],
								$data[$modelDecisionName][$key]['referent_id'],
								array(
									'PersonneReferent' => array(
										'personne_id' => $passagecov58['Dossiercov58']['personne_id'],
										'referent_id' => $data[$modelDecisionName][$key]['referent_id'],
										'dddesignation' => $datevalidation,
										'structurereferente_id' => $data[$modelDecisionName][$key]['structurereferente_id'],
										'user_id' => $passagecov58[$this->alias]['user_id'],
									)
								)
							) && $success;
						}

						$this->Dossiercov58->Personne->Orientstruct->create( $orientstruct );
						$success = $this->Dossiercov58->Personne->Orientstruct->save( null, array( 'atomic' => false ) ) && $success;

						// Mise à jour de l'enregistrement de la thématique avec l'id du nouveau CER
						$success = $success && $this->updateAllUnBound(
							array( "\"{$this->alias}\".\"nvorientstruct_id\"" => $this->Dossiercov58->Personne->Orientstruct->id ),
							array( "\"{$this->alias}\".\"id\"" => $data[$this->alias][$key] )
						);
					}


					// Modification etat du dossier passé dans la COV
					if( in_array( $values['decisioncov'], array( 'valide', 'refuse' ) ) ){
						$this->Dossiercov58->Passagecov58->updateAllUnBound(
							array( 'Passagecov58.etatdossiercov' => '\'traite\'' ),
							array('"Passagecov58"."id"' => $passagecov58['Passagecov58']['id'] )
						);
					}
					else if( $values['decisioncov'] == 'annule' ){
						$this->Dossiercov58->Passagecov58->updateAllUnBound(
							array( 'Passagecov58.etatdossiercov' => '\'annule\'' ),
							array('"Passagecov58"."id"' => $passagecov58['Passagecov58']['id'] )
						);
					}
					else if( $values['decisioncov'] == 'reporte' ){
						$this->Dossiercov58->Passagecov58->updateAllUnBound(
							array( 'Passagecov58.etatdossiercov' => '\'reporte\'' ),
							array('"Passagecov58"."id"' => $passagecov58['Passagecov58']['id'] )
						);
					}
				}

				$success = $this->Dossiercov58->Passagecov58->{$modelDecisionName}->saveAll( Set::extract( $data, '/'.$modelDecisionName ), array( 'atomic' => false ) ) && $success;
			}

			return $success;
		}




		/**
		*
		*/

		public function qdProcesVerbal() {
			$modele = 'Propoorientationcov58';
			$modeleDecisions = 'Decisionpropoorientationcov58';

			$result = array(
				'fields' => array(
					"{$modele}.id",
					"{$modele}.dossiercov58_id",
					"{$modele}.typeorient_id",
					"{$modele}.structurereferente_id",
					"{$modele}.datedemande",
					"{$modele}.rgorient",
					"{$modele}.commentaire",
					"{$modele}.covtypeorient_id",
					"{$modele}.covstructurereferente_id",
					"{$modele}.datevalidation",
					"{$modele}.commentaire",
					"{$modele}.user_id",
					"{$modele}.decisioncov",
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					"{$modeleDecisions}.id",
					"{$modeleDecisions}.etapecov",
					"{$modeleDecisions}.decisioncov",
					"{$modeleDecisions}.typeorient_id",
					"{$modeleDecisions}.structurereferente_id",
					"{$modeleDecisions}.referent_id",
					"{$modeleDecisions}.commentaire",
					"{$modeleDecisions}.created",
					"{$modeleDecisions}.modified",
					"{$modeleDecisions}.passagecov58_id",
					"{$modeleDecisions}.datevalidation",
				),
				'joins' => array(
					array(
						'table'      => Inflector::tableize( $modele ),
						'alias'      => $modele,
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( "{$modele}.dossiercov58_id = Dossiercov58.id" ),
					),
					array(
						'table'      => Inflector::tableize( $modeleDecisions ),
						'alias'      => $modeleDecisions,
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							"{$modeleDecisions}.passagecov58_id = Passagecov58.id",
							"{$modeleDecisions}.etapecov" => 'finalise'
						),
					),
					array(
						'table'      => 'typesorients',
						'alias'      => 'Typeorient',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( "{$modeleDecisions}.typeorient_id = Typeorient.id" ),
					),
					array(
						'table'      => 'structuresreferentes',
						'alias'      => 'Structurereferente',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( "{$modeleDecisions}.structurereferente_id = Structurereferente.id" ),
					)
				)
			);

			return $result;
		}

		/**
		 * Retourne une partie de querydata propre à la thématique et nécessaire
		 * à l'imprssion de l'odre du jour.
		 *
		 * @return array
		 */
		public function qdOrdreDuJour() {
			$result = parent::qdOrdreDuJour();

			$result['fields'][] = 'Typeorient.lib_type_orient';
			$result['fields'][] = 'Structurereferente.lib_struc';
			$result['joins'][] = $this->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) );
			$result['joins'][] = $this->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) );

			$result = array_words_replace(
				$result,
				array(
					'Typeorient' => "{$this->alias}typeorient",
					'Structurereferente' => "{$this->alias}structurereferente"
				)
			);

			return $result;
		}

		public function getPdfDecision( $passagecov58_id ) {
			$replacements = array(
				'Typeorient' => 'Nvtypeorient',
				'Structurereferente' => 'Nvstructurereferente',
				'Referent' => 'Nvreferent'
			);

			$data = $this->Dossiercov58->Passagecov58->find(
				'first',
				array(
					'fields' => array_merge(
						$this->Dossiercov58->Passagecov58->fields(),
						$this->Dossiercov58->Passagecov58->Dossiercov58->fields(),
						$this->Dossiercov58->Passagecov58->Decisionpropoorientationcov58->fields(),
						$this->Dossiercov58->Propoorientationcov58->fields(),
						$this->Dossiercov58->Personne->fields(),
						$this->Dossiercov58->Personne->Foyer->fields(),
						$this->Dossiercov58->Personne->Foyer->Dossier->fields(),
						$this->Dossiercov58->Personne->Foyer->Adressefoyer->fields(),
						$this->Dossiercov58->Personne->Foyer->Adressefoyer->Adresse->fields(),
						$this->Dossiercov58->Propoorientationcov58->Typeorient->fields(),
						$this->Dossiercov58->Propoorientationcov58->Structurereferente->fields(),
						$this->Dossiercov58->Propoorientationcov58->Covtypeorient->fields(),
						$this->Dossiercov58->Propoorientationcov58->Covstructurereferente->fields(),
						$this->Dossiercov58->Propoorientationcov58->Nvorientstruct->fields(),
						array_words_replace(
							$this->Dossiercov58->Propoorientationcov58->Nvorientstruct->Typeorient->fields(),
							$replacements
						),
						array_words_replace(
							$this->Dossiercov58->Propoorientationcov58->Nvorientstruct->Structurereferente->fields(),
							$replacements
						),
						array_words_replace(
							$this->Dossiercov58->Propoorientationcov58->Nvorientstruct->Referent->fields(),
							$replacements
						),
						$this->Dossiercov58->Propoorientationcov58->User->fields(),
						$this->Dossiercov58->Propoorientationcov58->User->Serviceinstructeur->fields(),
						$this->Dossiercov58->Passagecov58->Cov58->fields(),
						$this->Dossiercov58->Passagecov58->Cov58->Sitecov58->fields()
					),
					'conditions' => array(
						'Passagecov58.id' => $passagecov58_id,
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.id IN ('.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01('Adressefoyer.foyer_id').')'
						)
					),
					'joins' => array(
						$this->Dossiercov58->Passagecov58->join( 'Dossiercov58' ),
						$this->Dossiercov58->Passagecov58->join( 'Decisionpropoorientationcov58' ),
						$this->Dossiercov58->join( 'Propoorientationcov58' ),
						$this->Dossiercov58->join( 'Personne' ),
						$this->Dossiercov58->Personne->join( 'Foyer' ),
						$this->Dossiercov58->Personne->Foyer->join( 'Dossier' ),
						$this->Dossiercov58->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$this->Dossiercov58->Personne->Foyer->Adressefoyer->join( 'Adresse' ),
						$this->Dossiercov58->Propoorientationcov58->join( 'Nvorientstruct', array( 'type' => 'LEFT OUTER' ) ),
						array_words_replace(
							$this->Dossiercov58->Propoorientationcov58->Nvorientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
							$replacements
						),
						array_words_replace(
							$this->Dossiercov58->Propoorientationcov58->Nvorientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
							$replacements
						),
						array_words_replace(
							$this->Dossiercov58->Propoorientationcov58->Nvorientstruct->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
							$replacements
						),
						$this->Dossiercov58->Propoorientationcov58->join( 'Typeorient' ),
						$this->Dossiercov58->Propoorientationcov58->join( 'Structurereferente' ),
						$this->Dossiercov58->Propoorientationcov58->join( 'Covtypeorient', array( 'type' => 'LEFT OUTER' ) ),
						$this->Dossiercov58->Propoorientationcov58->join( 'Covstructurereferente', array( 'type' => 'LEFT OUTER' ) ),
						$this->Dossiercov58->Propoorientationcov58->join( 'User' ),
						$this->Dossiercov58->Propoorientationcov58->User->join( 'Serviceinstructeur' ),
						$this->Dossiercov58->Passagecov58->join( 'Cov58' ),
						$this->Dossiercov58->Passagecov58->Cov58->join( 'Sitecov58' )
					),
					'contain' => false
				)
			);

			$options = array(
				'Personne' => array( 'qual' => ClassRegistry::init( 'Option' )->qual() )
			);
			$options = Set::merge( $options, $this->Dossiercov58->enums() );

			$fileName = '';

			$typeorientEmploiId = Configure::read( 'Typeorient.emploi_id' );
			$reorientation = ( $data['Nvorientstruct']['rgorient'] == 1 || $data['Propoorientationcov58']['rgorient'] == 0 ) === false;

			if ( $data['Decisionpropoorientationcov58']['decisioncov'] == 'valide' ) {
				if( $typeorientEmploiId == $data['Decisionpropoorientationcov58']['typeorient_id'] ) {
					if ( $reorientation === false ) {
						$fileName = 'decisionorientationpro.odt';
					}
					else {
						$fileName = 'decisionreorientationpro.odt';
					}
				}
				else {
					if ( $reorientation === false ) {
						$fileName = 'decisionorientationsoc.odt';
					}
					else {
						$fileName = 'decisionreorientationsoc.odt';
					}
				}

			}
			else {
				$fileName = 'decisionrefusreorientation.odt';
			}

			return $this->ged(
				$data,
				"Cov58/{$fileName}",
				false,
				$options
			);
		}

		/**
		 * Retourne un querydata permettant de trouver les propositions d'orientations en cours de
		 * traitement par une COV pour un allocataire donné.
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function qdEnCours( $personne_id ) {
			$sqDernierPassagecov58 = $this->Dossiercov58->Passagecov58->sqDernier();

			return array(
				'fields' => array(
					'Propoorientationcov58.id',
					'Propoorientationcov58.dossiercov58_id',
					'Propoorientationcov58.datedemande',
					'Propoorientationcov58.rgorient',
					'Propoorientationcov58.typeorient_id',
					'Typeorient.lib_type_orient',
					'Propoorientationcov58.structurereferente_id',
					'Structurereferente.lib_struc',
					'Dossiercov58.personne_id',
					'Dossiercov58.themecov58',
					'Passagecov58.etatdossiercov',
					'Personne.id',
					'Personne.nom',
					'Personne.prenom'
				),
				'conditions' => array(
					'Dossiercov58.personne_id' => $personne_id,
					'Themecov58.name' => 'proposorientationscovs58',
					array(
						'OR' => array(
							'Passagecov58.etatdossiercov NOT' => array( 'traite', 'annule' ),
							'Passagecov58.etatdossiercov IS NULL'
						),
					),
					array(
						'OR' => array(
							"Passagecov58.id IN ( {$sqDernierPassagecov58} )",
							'Passagecov58.etatdossiercov IS NULL'
						),
					),
				),
				'joins' => array(
					$this->join( 'Dossiercov58', array( 'type' => 'INNER' ) ),
					$this->Dossiercov58->join( 'Themecov58', array( 'type' => 'INNER' ) ),
					$this->Dossiercov58->join( 'Passagecov58', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossiercov58->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->join( 'Typeorient', array( 'type' => 'INNER' ) ),
					$this->join( 'Structurereferente', array( 'type' => 'INNER' ) )
				),
				'contain' => false,
				'order' => array( 'Propoorientationcov58.rgorient DESC' )
			);
		}
	}
?>