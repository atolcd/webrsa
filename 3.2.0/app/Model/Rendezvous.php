<?php
	/**
	 * Code source de la classe Rendezvous.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'DefaultUtility', 'Default.Utility' );

	/**
	 * La classe Rendezvous ...
	 *
	 * @package app.Model
	 */
	class Rendezvous extends AppModel
	{
		public $name = 'Rendezvous';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'libelle';

		public $actsAs = array(
			'Allocatairelie',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate',
			'Gedooo.Gedooo'
		);

		public $validate = array(
			'structurereferente_id' => array(
				'checkThematiqueAnnuelleParStructurereferente' => array(
					'rule' => array( 'checkThematiqueAnnuelleParStructurereferente' )
				)
			),
			'typerdv_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				),
				'checkThematiquesObligatoires' => array(
					'rule' => array( 'checkThematiquesObligatoires' ),
					'message' => 'Veuillez choisir au moins une thématique de rendez-vous'
				),
				'checkThematiqueAnnuelleParQuestionnaireD1' => array(
					'rule' => array( 'checkThematiqueAnnuelleParQuestionnaireD1' ),
					'message' => 'Ce RDV est déjà lié à un D1. Vous ne pouvez pas décocher "Premier RDV de l\'année", sauf en supprimant le D1'
				),
				'checkNotPassageCovOrientationSocial' => array(
					'rule' => array( 'checkNotPassageCovOrientationSocial' ),
					'message' => 'Ce dossier est actuellement en cours de passage dans une COV pour orientation sociale.'
				)
			),
			'daterdv' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'heurerdv' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				),
			),
			'statutrdv_id' => array(
				'checkThematiqueAnnuelleParStatutRdvId' => array(
					'rule' => array( 'checkThematiqueAnnuelleParStatutRdvId' ),
					'message' => 'Si le RDV n\'a pas eu lieu (statut: excusé ou non honoré), il faut supprimer le D1'
				)
			)
		);

		public $virtualFields = array(
			'dernier' => array(
				'type'      => 'boolean',
				'postgres'  => '"%s"."id" IN (
					SELECT a.id FROM rendezvous AS a
					WHERE a.personne_id = "%s"."personne_id"
					ORDER BY a.daterdv DESC,
						a.heurerdv DESC
					LIMIT 1)'
			),
		);

		/**
		 * Règle de validation: on vérifie qu'il n'existe pas d'autre RDV pour
		 * un allocataire donné dans une structure référente donnée dans l'année
		 * pour une des thématiques annuelles.
		 *
		 * @see Configure::write Rendezvous.useThematique
		 * @see Configure::write Rendezvous.thematiqueAnnuelleParStructurereferente
		 *
		 * @param array $check
		 * @return boolean
		 */
		public function checkThematiqueAnnuelleParStructurereferente( $check ) {
			if( !is_array( $check ) ) {
				return false;
			}

			if( !Configure::read( 'Rendezvous.useThematique' ) ) {
				return true;
			}

			$thematiquesAnnuelles = (array)Configure::read( 'Rendezvous.thematiqueAnnuelleParStructurereferente' );
			if( empty( $thematiquesAnnuelles ) ) {
				return true;
			}

			$check = array_values( $check );
			$structurereferente_id = ( isset( $check[0] ) ? $check[0] : null );

			$id = Hash::get( $this->data, "{$this->alias}.{$this->primaryKey}" );
			$personne_id = Hash::get( $this->data, "{$this->alias}.personne_id" );
			$daterdv = Hash::get( $this->data, "{$this->alias}.daterdv" );
			$values = (array)Hash::extract( $this->data, 'Thematiquerdv.Thematiquerdv' );

			if( empty( $values ) || empty( $daterdv ) || empty( $personne_id ) || empty( $structurereferente_id ) ) {
				return true;
			}

			$intersect = array_intersect( $thematiquesAnnuelles, $values );
			if( empty( $intersect ) ) {
				return true;
			}

			$year = date( 'Y', strtotime( $daterdv ) );
			$sqThematiqueRdv = $this->RendezvousThematiquerdv->sq(
				array(
					'alias' => 'rendezvous_thematiquesrdvs',
					'fields' => array( 'rendezvous_thematiquesrdvs.rendezvous_id' ),
					'contain' => false,
					'conditions' => array(
						'rendezvous_thematiquesrdvs.rendezvous_id = Rendezvous.id',
						'rendezvous_thematiquesrdvs.thematiquerdv_id' => $intersect,
					),
				)
			);

			$querydata = array(
				'fields' => array( 'RendezvousThematiquerdv.thematiquerdv_id' ),
				'conditions' => array(
					"{$this->alias}.personne_id" => $personne_id,
					"{$this->alias}.structurereferente_id" => $structurereferente_id,
					"{$this->alias}.daterdv BETWEEN '{$year}-01-01' AND '{$year}-12-31'",
					"{$this->alias}.{$this->primaryKey} IN ( {$sqThematiqueRdv} )",
					// Qui est dans un état permettant ou ayant permis d'ajouter un QD1
					"{$this->alias}.statutrdv_id" => (array)Configure::read( 'Rendezvous.checkThematiqueAnnuelleParStructurereferente.statutrdv_id' ),
				),
				'contain' => false,
				'joins' => array(
					$this->join( 'RendezvousThematiquerdv', array( 'type' => 'INNER' ) )
				)
			);

			if( !empty( $id ) ) {
				$querydata['conditions']["{$this->alias}.{$this->primaryKey} <>"] = $id;
			}

			$found = $this->find( 'all', $querydata );

			if( $found ) {
				$thematiques = $this->Thematiquerdv->find(
					'list',
					array(
						'conditions' => array(
							'Thematiquerdv.id' => (array)Hash::extract( $found, '{n}.RendezvousThematiquerdv.thematiquerdv_id' )
						),
					'contain' => false
					)
				);

				if( count( $thematiques ) == 1 ) {
					$message = 'Il existe déjà un rendez-vous pour la thématique « %s » dans cette structure référente pour l\'année %d.';
				}
				else {
					$message = 'Il existe déjà au moins un rendez-vous pour les thématiques « %s » dans cette structure référente pour l\'année %d.';
				}

				return sprintf( $message, implode( ' », « ', $thematiques ), $year );
			}

			return true;
		}

		/**
		 * Vérifie, si on utilise les thématiques de RDV, qu'au moins une
		 * thématique a été sélectionnée.
		 *
		 * @see Rendezvous.useThematique
		 *
		 * @param mixed $check
		 * @return boolean
		 */
		public function checkThematiquesObligatoires( $check ) {
			if( !is_array( $check ) ) {
				return false;
			}

			if( !Configure::read( 'Rendezvous.useThematique' ) ) {
				return true;
			}

			if( Hash::check( $this->data, 'Thematiquerdv.Thematiquerdv' ) ) {
				$thematiquesIds = (array)Hash::get( $this->data, 'Thematiquerdv.Thematiquerdv' );
				$thematiquesIds = Hash::filter( $thematiquesIds );

				return !empty( $thematiquesIds );
			}

			return true;
		}

		/**
		 * Vérification lors de la modification d'un RDV: si les thématiques
		 * annuelles par structure référente sont utilisées et qu'aucune d'entre
		 * elles n'est sélectionnée dans le formulaire du RDV, il faut qu'il
		 * n'existe pas de questionnaire D1 pour cet enregistrement.
		 *
		 * @see Rendezvous.useThematique
		 * @see Rendezvous.thematiqueAnnuelleParStructurereferente
		 *
		 * @param mixed $check
		 * @return boolean
		 */
		public function checkThematiqueAnnuelleParQuestionnaireD1( $check ) {
			if( !is_array( $check ) ) {
				return false;
			}

			$id = Hash::get( $this->data, "{$this->alias}.{$this->primaryKey}" );
			$thematiquesAnnuellesIds = (array)Configure::read( 'Rendezvous.thematiqueAnnuelleParStructurereferente' );
			if( !Configure::read( 'Rendezvous.useThematique' ) || empty( $id ) || empty( $thematiquesAnnuellesIds ) ) {
				return true;
			}

			$thematiquesSelectionneesIds = (array)Hash::get( $this->data, 'Thematiquerdv.Thematiquerdv' );
			$intersect = array_intersect( $thematiquesSelectionneesIds, $thematiquesAnnuellesIds );

			if( empty( $intersect ) ) {
				$query = array(
					'fields' => array(
						"{$this->Questionnaired1pdv93->alias}.{$this->Questionnaired1pdv93->primaryKey}"
					),
					'contain' => false,
					'conditions' => array(
						"{$this->Questionnaired1pdv93->alias}.rendezvous_id" => $id
					)
				);

				$found = $this->Questionnaired1pdv93->find( 'first', $query );
				return empty( $found );
			}

			return true;
		}

		/**
		 * Vérification lors de la modification d'un RDV: si le statut
		 * du RDV ne figure pas dans les statuts acceptés pour la création d'un
		 * questionnaire D1, alors il faut qu'il n'existe pas de questionnaire D1
		 * pour cet enregistrement.
		 *
		 * @see Rendezvous.useThematique
		 * @see Rendezvous.checkThematiqueAnnuelleParStructurereferente.statutrdv_id
		 *
		 * @param mixed $check
		 * @return boolean
		 */
		public function checkThematiqueAnnuelleParStatutRdvId( $check ) {
			if( !is_array( $check ) ) {
				return false;
			}

			$id = Hash::get( $this->data, "{$this->alias}.{$this->primaryKey}" );
			$statutsrdvsAcceptesId = (array)Configure::read( 'Rendezvous.checkThematiqueAnnuelleParStructurereferente.statutrdv_id' );
			if( !Configure::read( 'Rendezvous.useThematique' ) || empty( $id ) || empty( $statutsrdvsAcceptesId ) ) {
				return true;
			}

			$statutrdv_id = (array)Hash::get( $this->data, "{$this->alias}.statutrdv_id" );
			$intersect = array_intersect( $statutrdv_id, $statutsrdvsAcceptesId );

			if( empty( $intersect ) ) {
				$query = array(
					'fields' => array(
						"{$this->Questionnaired1pdv93->alias}.{$this->Questionnaired1pdv93->primaryKey}"
					),
					'contain' => false,
					'conditions' => array(
						"{$this->Questionnaired1pdv93->alias}.rendezvous_id" => $id
					)
				);

				$found = $this->Questionnaired1pdv93->find( 'first', $query );
				return empty( $found );
			}

			return true;
		}

		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
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
			'Typerdv' => array(
				'className' => 'Typerdv',
				'foreignKey' => 'typerdv_id',
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
			'Permanence' => array(
				'className' => 'Permanence',
				'foreignKey' => 'permanence_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Statutrdv' => array(
				'className' => 'Statutrdv',
				'foreignKey' => 'statutrdv_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasOne = array(
			'Propoorientsocialecov58' => array(
				'className' => 'Propoorientsocialecov58',
				'foreignKey' => 'rendezvous_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Questionnaired1pdv93' => array(
				'className' => 'Questionnaired1pdv93',
				'foreignKey' => 'rendezvous_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);

		public $hasMany = array(
			'Entretien' => array(
				'className' => 'Entretien',
				'foreignKey' => 'rendezvous_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Rendezvous\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Populationb3pdv93' => array(
				'className' => 'Populationb3pdv93',
				'foreignKey' => 'rendezvous_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Populationb6pdv93' => array(
				'className' => 'Populationb6pdv93',
				'foreignKey' => 'rendezvous_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Sanctionrendezvousep58' => array(
				'className' => 'Sanctionrendezvousep58',
				'foreignKey' => 'rendezvous_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		/**
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
			'Thematiquerdv' => array(
				'className' => 'Thematiquerdv',
				'joinTable' => 'rendezvous_thematiquesrdvs',
				'foreignKey' => 'rendezvous_id',
				'associationForeignKey' => 'thematiquerdv_id',
				'unique' => true,
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'finderQuery' => null,
				'deleteQuery' => null,
				'insertQuery' => null,
				'with' => 'RendezvousThematiquerdv'
			),
		);

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('WebrsaRendezvous');

		/**
		 * Surcharge du constructeur pour ajouter des champs virtuels.
		 *
		 * @param mixed $id Set this ID for this model on startup, can also be an array of options, see above.
		 * @param string $table Name of database table to use.
		 * @param string $ds DataSource connection name.
		 */
		public function __construct( $id = false, $table = null, $ds = null ) {
			parent::__construct( $id, $table, $ds );

			// Seulement l'on utilise les thématiques, lorsque l'on n'est pas en
			// train d'importer des fixtures
			if( !( unittesting() && $this->useDbConfig === 'default' ) && Configure::read( 'Rendezvous.useThematique' ) ) {
				$this->virtualFields['thematiques'] = $this->WebrsaRendezvous->vfListeThematiques( null );
				$this->virtualFields['thematiques_virgules'] = $this->WebrsaRendezvous->vfListeThematiques( null, ', ' );
			}

			if( 58 !== (int)Configure::read( 'Cg.departement' ) ){
				$rule = array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire',
				);

				$this->validate['statutrdv_id'][NOT_BLANK_RULE_NAME] = $rule;
			}
		}

		/**
		* FIXME: la même avec dossier COV
		*/
		public function beforeSave( $options = array ( ) ) {
			$return = parent::beforeSave( $options );

			if ( Configure::read( 'Cg.departement' ) == 58 ) {
				// Pour les nouveaux enregistrements,
				if ( !isset( $this->data['Rendezvous']['id'] ) || empty( $this->data['Rendezvous']['id'] ) ) {
					$dossierep = $this->Personne->Dossierep->find(
						'first',
						array(
							'conditions' => array(
								'Dossierep.actif' => '1',
								'Dossierep.personne_id' => $this->data['Rendezvous']['personne_id'],
								'Dossierep.themeep' => 'sanctionsrendezvouseps58',
								'Dossierep.id NOT IN ( '.
									$this->Personne->Dossierep->Passagecommissionep->sq(
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
									'Rendezvous'
								)
							)
						)
					);

					if ( isset( $dossierep['Sanctionrendezvousep58']['Rendezvous']['typerdv_id'] ) ) {
						$this->invalidate( 'typerdv_id', 'Un passage en EP est déjà en cours pour cette objet, vous ne pouvez créer un nouveau rendez-vous pour ce même objet.' );
						$return = false;
					}
				}
				else {
					if ( !$this->Statutrdv->provoquePassageCommission( $this->data['Rendezvous']['statutrdv_id'] ) || !$this->WebrsaRendezvous->passageEp( $this->data ) ) {
						$dossierep = $this->Sanctionrendezvousep58->find(
							'first',
							array(
								'fields' => array(
									'Sanctionrendezvousep58.id',
									'Sanctionrendezvousep58.dossierep_id'
								),
								'conditions' => array(
									'Sanctionrendezvousep58.rendezvous_id' => $this->data['Rendezvous']['id']
								),
								'contain' => false
							)
						);

						if ( !empty( $dossierep ) ) {
							$this->Sanctionrendezvousep58->delete( $dossierep['Sanctionrendezvousep58']['id'] );
							$this->Sanctionrendezvousep58->Dossierep->delete( $dossierep['Sanctionrendezvousep58']['dossierep_id'] );
						}
					}
				}
			}

			return $return;
		}

		/**
		 * Règle de validation sur le statut du RDV uniquement si pas CG58.
		 *
		 * @param array $options
		 * @return boolean
		 */
		public function beforeValidate( $options = array() ) {
			$return = parent::beforeValidate( $options );

			// -----------------------------------------------------------------
			// FIXME: ailleurs + la même année
			// TODO: configuration si on utilise la fonctionnalité ?
			$CheckModelName = 'Thematiquerdv';
			$masterField = 'typerdv_id';
			$slaveField = 'statutrdv_id';
			// -----------------------------------------------------------------
			$checked = Hash::get( $this->data, "{$CheckModelName}.{$CheckModelName}" );
			if( !empty( $checked ) ) {
				$querydata = array(
					'conditions' => array(
						"{$CheckModelName}.id" => $checked,
						"{$CheckModelName}.{$masterField}" => Hash::get( $this->data, "{$this->alias}.{$masterField}" ),
						"{$CheckModelName}.{$slaveField} IS NOT NULL",
						// TODO: pas la condition ci-dessous (permettra de faire égal ou différent) ?
						"{$CheckModelName}.{$slaveField}" => Hash::get( $this->data, "{$this->alias}.{$slaveField}" ),
					)
				);
				$checks = $this->{$CheckModelName}->find( 'all', $querydata );

				if( !empty( $checks ) ) {
					$messages = array();

					foreach( $checks as $check ) {
						$Model = ClassRegistry::init( $check[$CheckModelName]['linkedmodel'] );

						// TODO: les conditions en paramétrage de la règle de validation
						$conditions = array(
							"{$Model->alias}.personne_id" => "#Rendezvous.personne_id#",
							"DATE_TRUNC( 'YEAR', {$Model->alias}.date_validation ) = DATE_TRUNC( 'YEAR', TIMESTAMP '#Rendezvous.daterdv#' )",
						);
						$conditions = DefaultUtility::evaluate( $this->data, $conditions );

						$querydata = array(
							'fields' => array(
								"{$Model->alias}.{$Model->primaryKey}"
							),
							'contain' => false,
							'conditions' => $conditions,
						);
						$found = $Model->find( 'first', $querydata );

						if( empty( $found ) ) {
							$messages[] = __d( Inflector::underscore( $this->alias ), "{$Model->alias}::missing" );
						}
					}

					if( !empty( $messages ) ) {
						// TODO: un ensemble de messages dans la vue ?
						$this->invalidate( $slaveField, $messages[0] );
						$return = false;
					}
				}
			}


			return $return;
		}

		/**
		 * Retourne une sous-requête permettant de connaître le dernier rendez-vous pour un
		 * allocataire donné.
		 *
		 * @param string $field Le champ Personne.id sur lequel faire la sous-requête
		 * @return string
		 */
		public function sqDernier( $field ) {
			$dbo = $this->getDataSource( $this->useDbConfig );
			$table = $dbo->fullTableName( $this, false, false );
			return "SELECT {$table}.id
					FROM {$table}
					WHERE
						{$table}.personne_id = ".$field."
					ORDER BY {$table}.daterdv DESC
					LIMIT 1";
		}

		/**
		 *
		 * TODO: si on utilise les thematiquesrdv seulement
		 *
		 * @param array $results
		 * @param string $thematiqueAlias -> un array plus complet
		 * @return array
		 *
		 * @deprecated since version 3.1	Utilisé dans une classe dépréciée
		 */
		public function containThematique( array $results, $thematiqueAlias = 'Thematiquerdv' ) {
			if( !empty( $results ) ) {
				// Une liste ou un seul enregistrement ?
				$find = 'all';
				if( !is_int( key( $results ) ) ) {
					$find = 'first';
					reset( $results );
					$results = array( $results );
				}
				else {
					reset( $results );
				}

				foreach( $results as $key => $result ) {
					$thematiquesrdvs = $this->{$thematiqueAlias}->find(
						'all',
						array(
							'fields' => array(
								"{$thematiqueAlias}.id",
								"{$thematiqueAlias}.name",
							),
							'contain' => false,
							'joins' => array(
								$this->{$thematiqueAlias}->join( 'RendezvousThematiquerdv', array( 'type' => 'INNER' ) )
							),
							'conditions' => array(
								'RendezvousThematiquerdv.rendezvous_id' => $result[$this->alias]['id']
							)
						)
					);
					$results[$key][$thematiqueAlias] = (array)Hash::extract( $thematiquesrdvs, "{n}.{$thematiqueAlias}" );
				}

				// Une liste ou un seul enregistrement ?
				if( $find != 'all' ) {
					$results = $results[0];
				}
			}

			return $results;
		}

		/**
		 * Permet de vérifier que le dossier n'est pas actuellement en cours de
		 * passage COV pour orientation sociale
		 *
		 *
		 * @param array $check
		 * @param array $rule
		 * @return boolean
		 * @see RendezvousController::index (même querydata)
		 * @todo Factoriser le querydata
		 */
		public function checkNotPassageCovOrientationSocial($check, $rule) {
			$valide = true;

			if ((int)Configure::read('Cg.departement') === 58
				&& $check['typerdv_id'] == Configure::read('Rendezvous.elaborationCER.typerdv_id')
				&& !Hash::get($this->data, 'Rendezvous.id') // N'est pas un ajout si rempli
				&& Hash::get($this->data, 'Rendezvous.personne_id')
			) {
				$valide = !$this->Personne->Dossiercov58->find(
					'first',
					array(
						'fields' => array(
							'StatutrdvTyperdv.motifpassageep',
						),
						'joins' => array(
							$this->Personne->Dossiercov58->join('Propoorientsocialecov58'),
							$this->Personne->Dossiercov58->Propoorientsocialecov58->join('Rendezvous'),
							$this->Personne->Dossiercov58->Propoorientsocialecov58->Rendezvous->join('Typerdv'),
							$this->Personne->Dossiercov58->Propoorientsocialecov58->Rendezvous->Typerdv->join('StatutrdvTyperdv')
						),
						'conditions' => array(
							'Dossiercov58.themecov58' => 'proposorientssocialescovs58',
							'Dossiercov58.personne_id' => Hash::get($this->data, 'Rendezvous.personne_id'),
							'Dossiercov58.id NOT IN ( '.
								$this->Personne->Dossiercov58->Passagecov58->sq(
									array(
										'fields' => array(
											'passagescovs58.dossiercov58_id'
										),
										'alias' => ' passagescovs58',
										'conditions' => array(
											'passagescovs58.etatdossiercov' => array('traite', 'annule')
										)
									)
								)
							.' )'
						),
						'order' => array('Dossiercov58.created ASC'),
						'contain' => false
					)
				);
			}

			return $valide;
		}
	}
?>