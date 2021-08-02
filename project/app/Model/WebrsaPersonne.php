<?php
	/**
	 * Code source de la classe WebrsaPersonne.
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
	 * La classe WebrsaPersonne possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaPersonne extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaPersonne';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Personne');

		/**
		 * Liste des alias de modèles pour lesquels on prend en compte les
		 * anciens dossiers dans lesquels l'allocataire n'a plus de prestation.
		 *
		 *
		 * INFO: on ne prend en compte ni Dossiercov58, ni Dossierep.
		 *
		 * @var array
		 */
		public $anciensDossiersModelNames = array(
			'ActioncandidatPersonne',
			'Apre',
			'Bilanparcours66',
			'Contratinsertion',
			'Cui',
			'Dsp',
			'DspRev',
			'Entretien',
			'Ficheprescription93',
			'Memo',
			'Orientstruct',
			'PersonneReferent',
			'Propopdo',
			'Questionnaired1pdv93',
			'Questionnaired2pdv93',
			'Rendezvous',
		);

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$fields = array('Personne.nom');
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
					'Foyer.id',
					'Personne.id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Personne->Foyer->join('Personne'),
					$this->Personne->join('Prestation'),
				),
				'contain' => false,
				'order' => array(
					'(CASE WHEN "Prestation"."rolepers" = \'DEM\' THEN 3 '
					. 'WHEN "Prestation"."rolepers" = \'CJT\' THEN 2 '
					. 'WHEN "Prestation"."rolepers" = \'ENF\' THEN 1 '
					. 'ELSE 0 END)' => 'DESC',
					'Personne.nom' => 'ASC',
					'Personne.prenom' => 'ASC',
				)
			);

			$results = $this->Personne->Foyer->find('all', $this->completeVirtualFieldsForAccess($query));
			return $results;
		}

		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 *
		 * @see WebrsaAccess::getParamsList
		 * @param integer $foyer_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess($foyer_id, array $params = array()) {
			$results = array();

			if (in_array('ajoutPossible', $params)) {
				$results['ajoutPossible'] = $this->ajoutPossible($foyer_id);
			}

			return $results;
		}

		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 *
		 * @param integer $foyer_id
		 * @return boolean
		 */
		public function ajoutPossible($foyer_id) {
			return true;
		}

		/**
		 *
		 */
		public function soumisDroitsEtDevoirs( $personne_id ) {
			$qd_personne = array(
				'conditions' => array(
					'Personne.id' => $personne_id
				),
				'fields' => null,
				'order' => null,
				'contain' => array(
					'Ressource' => array(
						'order' => array( 'dfress DESC' )
					),
					'Dsp',
					'Prestation',
					'Calculdroitrsa'
				)
			);
			$personne = $this->Personne->find( 'first', $qd_personne );

			if( isset( $personne['Prestation'] ) && ( $personne['Prestation']['rolepers'] == 'DEM' || $personne['Prestation']['rolepers'] == 'CJT' ) ) {
				$montant = Set::classicExtract( $personne, 'Calculdroitrsa.mtpersressmenrsa' );

				if( $montant < 500 ) {
					return true;
				}
				else {
					$montantForfaitaire = $this->Personne->Foyer->montantForfaitaire( $personne['Personne']['foyer_id'] );
					if( $montantForfaitaire ) {
						return $montantForfaitaire;
					}
				}

				$dsp = array_filter( array( 'Dsp' => $personne['Dsp'] ) );
				$hispro = Set::extract( $dsp, 'Dsp.hispro' );
				if( $hispro !== NULL ) {
					// Passé professionnel ? -> Emploi
					//     1901 : Vous avez toujours travaillé
					//     1902 : Vous travaillez par intermittence
					if( $dsp['Dsp']['hispro'] == '1901' || $dsp['Dsp']['hispro'] == '1902' ) {
						return false;
					}
					else {
						return true;
					}
				}
			}
			return false;
		}


		/**
		 * Détails propres à la personne pour l'APRE
		 */
		public function detailsApre( $personne_id, $user_id = null ) {
			$Informationpe = ClassRegistry::init( 'Informationpe' );
			$query = array(
				'fields' => array_merge(
					$this->Personne->fields(),
					$this->Personne->Prestation->fields(),
					$this->Personne->Foyer->fields(),
					$this->Personne->Foyer->Dossier->fields(),
					$this->Personne->Foyer->Adressefoyer->Adresse->fields(),
					$this->Personne->Orientstruct->fields(),
					$this->Personne->Orientstruct->Typeorient->fields(),
					$this->Personne->Orientstruct->Structurereferente->fields(),
					array(
						'( '.$this->Personne->Foyer->vfNbEnfants().' ) AS "Foyer__nbenfants"',
						'Historiqueetatpe.id',
						'Historiqueetatpe.etat',
						'Historiqueetatpe.date',
						'Historiqueetatpe.identifiantpe',
						'PersonneReferent.referent_id',
						'Titresejour.dftitsej'
					)
				),
				'joins' => array(
					$this->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$this->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Personne->join( 'PersonneReferent', array( 'type' => 'LEFT OUTER' ) ),
					$this->Personne->join( 'Titresejour', array( 'type' => 'LEFT OUTER' ) ),
					$this->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$this->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$this->Personne->join( 'Orientstruct', array( 'type' => 'LEFT OUTER' ) ),
					$this->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
					$this->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
					$Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', 'LEFT OUTER' ),
					$Informationpe->join( 'Historiqueetatpe', array( 'type' => 'LEFT OUTER' ) ),
				),
				'conditions' => array(
					'Personne.id' => $personne_id,
					'Prestation.natprest' => 'RSA',
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'OR' => array(
						'Adressefoyer.id IS NULL',
						'Adressefoyer.id IN ( '.$this->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
					)
				),
				'contain' => false,
				'recursive' => -1
			);

			if (Configure::read( 'CG.cantons' )) {
				$query['fields'][] = 'Canton.id';
				$query['fields'][] = 'Canton.canton';

				if (Configure::read('Canton.useAdresseCanton')) {
					$query['joins'][] = $this->Personne->Foyer->Adressefoyer->Adresse->join('AdresseCanton', array('type' => 'LEFT OUTER'));
					$query['joins'][] = $this->Personne->Foyer->Adressefoyer->Adresse->AdresseCanton->join('Canton', array('type' => 'LEFT OUTER'));
				} else {
					$query['joins'][] = $this->Personne->Foyer->Adressefoyer->Adresse->AdresseCanton->Canton->joinAdresse();
				}
			}

			$personne = $this->Personne->find('first', $query);

			///Récupération des données propres au contrat d'insertion, notammenrt le premier contrat validé ainsi que le dernier.
			$contrat = $this->Personne->Contratinsertion->find(
				'first',
				array(
					'fields' => array( 'Contratinsertion.datevalidation_ci' ),
					'conditions' => array(
						'Contratinsertion.personne_id' => $personne_id,
						'Contratinsertion.decision_ci' => 'V'
					),
					'contain' => false,
					'order' => 'Contratinsertion.datevalidation_ci DESC',
					'recursive' => -1
				)
			);
			if( !empty( $contrat ) ) {
				$personne['Contratinsertion']['dernier'] = $contrat['Contratinsertion'];
			}

// debug( $personne );
			/// Récupération du service instructeur
			$suiviinstruction = $this->Personne->Foyer->Dossier->Suiviinstruction->find(
				'first',
				array(
					'fields' => array_keys( // INFO: champs des tables Suiviinstruction et Serviceinstructeur
							Set::merge(
									Hash::flatten( array( 'Suiviinstruction' => Set::normalize( array_keys( $this->Personne->Foyer->Dossier->Suiviinstruction->schema() ) ) ) ), Hash::flatten( array( 'Serviceinstructeur' => Set::normalize( array_keys( ClassRegistry::init( 'Serviceinstructeur' )->schema() ) ) ) )
							)
					),
					'recursive' => -1,
					'contain' => false,
					'conditions' => array(
						'Suiviinstruction.dossier_id' => $personne['Foyer']['dossier_id']
					),
					'joins' => array(
						array(
							'table' => 'servicesinstructeurs',
							'alias' => 'Serviceinstructeur',
							'type' => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array( 'Suiviinstruction.numdepins = Serviceinstructeur.numdepins AND Suiviinstruction.typeserins = Serviceinstructeur.typeserins AND Suiviinstruction.numcomins = Serviceinstructeur.numcomins AND Suiviinstruction.numagrins = Serviceinstructeur.numagrins' )
						)
					)
				)
			);

			$personne = Set::merge( $personne, $suiviinstruction );

			$User = ClassRegistry::init( 'User' );
			$user = $User->find(
					'first', array(
				'fields' => array_merge(
						$User->fields(), $User->Serviceinstructeur->fields()
				),
				'conditions' => array(
					'User.id' => $user_id
				),
				'joins' => array(
					$User->join( 'Serviceinstructeur' )
				),
				'contain' => false,
				'recursive' => -1
					)
			);
			$personne = Set::merge( $personne, $user );

// debug($personne);
			return $personne;
		}

		/**
		 *
		 */
		public function newDetailsCi( $personne_id, $user_id = null ) {

			$sqDernierReferent = $this->Personne->PersonneReferent->sqDerniere( 'Personne.id', false );

			///Recup personne
			$personne = $this->Personne->find(
				'first',
				array(
					'fields' => array(
						'Dossier.id',
						'Dossier.numdemrsa',
						'Dossier.dtdemrsa',
						'Dossier.fonorg',
						'Dossier.matricule',
						'Personne.id',
						'Personne.foyer_id',
						'Personne.qual',
						'Personne.nom',
						'Personne.prenom',
						'Personne.dtnai',
						'Personne.nir',
						'Personne.numfixe',
						'Personne.numport',
						'Personne.email',
						'Personne.idassedic',
						'Prestation.rolepers',
						'Adresse.numvoie',
						'Adresse.libtypevoie',
						'Adresse.nomvoie',
						'Adresse.nomcom',
						'Adresse.codepos',
						'Adresse.nomvoie',
						'Adresse.numcom',
						'Serviceinstructeur.lib_service',
						'Serviceinstructeur.numdepins',
						'Serviceinstructeur.typeserins',
						'Serviceinstructeur.numcomins',
						'Serviceinstructeur.numagrins',
						'Suiviinstruction.typeserins',
						ClassRegistry::init( 'Detaildroitrsa' )->vfRsaMajore().' AS "Detailcalculdroitrsa__majore"',
						$this->Personne->PersonneReferent->Referent->sqVirtualField( 'nom_complet'),
						'Referent.numero_poste'
					),
					'conditions' => array(
						'Personne.id' => $personne_id,
						'Prestation.rolepers' => array( 'DEM', 'CJT' ),
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.id IN ( '.$this->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
						)
					),
					'joins' => array(
						$this->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
						$this->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Foyer->Dossier->join( 'Suiviinstruction', array( 'type' => 'LEFT OUTER' ) ),
						array(
							'table' => 'servicesinstructeurs',
							'alias' => 'Serviceinstructeur',
							'type' => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array( 'Suiviinstruction.numdepins = Serviceinstructeur.numdepins AND Suiviinstruction.typeserins = Serviceinstructeur.typeserins AND Suiviinstruction.numcomins = Serviceinstructeur.numcomins AND Suiviinstruction.numagrins = Serviceinstructeur.numagrins' )
						),
						$this->Personne->join(
							'PersonneReferent',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									"PersonneReferent.id IN ( {$sqDernierReferent} )"
								)
							)
						),
						$this->Personne->PersonneReferent->join( 'Referent', array( 'type' => 'LEFT OUTER' ) )
					),
					'recursive' => -1
				)
			);


			// FIXME -> comment distinguer ? + FIXME autorutitel / autorutiadrelec
			$modecontact = $this->Personne->Foyer->Modecontact->find(
				'all',
				array(
					'conditions' => array(
						'Modecontact.foyer_id' => $personne['Personne']['foyer_id']
					),
					'recursive' => -1,
					'order' => 'Modecontact.nattel ASC'
				)
			);

			foreach( $modecontact as $index => $value ) {
				$personne = Set::merge( $personne, array( 'Modecontact' => Set::extract( $modecontact, '{n}.Modecontact' ) ) );
			}

			$activite = $this->Personne->Activite->find(
				'first',
				array(
					'fields' => array(
						'Activite.act'
					),
					'conditions' => array(
						'Activite.personne_id' => $personne_id
					),
					'recursive' => -1,
					'order' => 'Activite.dfact DESC'
				)
			);
			if( !empty( $activite ) ) {
				$personne = Set::merge( $personne, $activite );
			}

			return $personne;
		}

		/**
		 * Fonction permettant de récupérer le responsable du dossier (DEM + RSA)
		 * Dans les cas où un dossier possède plusieurs Demandeurs
		 * on s'assure de n'en prendre qu'un seul des 2
		 *
		 * @param string $field Le champ Foyer.id de la requête principale.
		 */
		public function sqResponsableDossierUnique( $foyerId = 'Foyer.id' ) {
			return $this->Personne->sq(
							array(
								'alias' => 'personnes',
								'fields' => array( 'personnes.id' ),
								'conditions' => array(
									'personnes.foyer_id = '.$foyerId,
									'prestations.rolepers' => 'DEM'
								),
								'joins' => array(
									array_words_replace(
											$this->Personne->join( 'Prestation' ), array(
										'Personne' => 'personnes',
										'Prestation' => 'prestations'
											)
									)
								),
								'contain' => false,
								'limit' => 1
							)
			);
		}

		/**
		 * Retourne une sous-requête permettant de connaître la structure chargée
		 * de l'évaluation d'un allocataire (CG 58).
		 *
		 * @param string $personneIdPath Le chemin désignant le champ personne_id
		 *	(ex.: Dossiercov58.personne_id)
		 * @param string $structureFieldPath Le chemin vers l'information de la
		 *	structure orientante que l'on veut obtenir (ex.: Structureorientante.lib_struc)
		 * @param boolean $alias Doit-on aliaser la sous-requête ?
		 * @return string
		 */
		public function sqStructureorientante( $personneIdPath, $structureFieldPath, $alias = true ) {
			list( $personneModelName, $personneFieldName ) = model_field( $personneIdPath );
			list( $soModelName, $soFieldName ) = model_field( $structureFieldPath );

			$sql = "SELECT
					structuresreferentes.{$soFieldName}
				FROM proposorientationscovs58
					INNER JOIN dossierscovs58 ON ( proposorientationscovs58.dossiercov58_id = dossierscovs58.id )
					LEFT OUTER JOIN structuresreferentes ON ( proposorientationscovs58.structureorientante_id = structuresreferentes.id )
				WHERE dossierscovs58.personne_id = \"{$personneModelName}\".\"{$personneFieldName}\"
				ORDER BY dossierscovs58.created DESC
                LIMIT 1";

			if( $alias ) {
				return "( {$sql} ) AS \"{$soModelName}__{$soFieldName}\"";
			}

			return $sql;
		}

		/**
		 * Retourne la condition permettant de savoir qu'il existe au moins un
		 * enregistrement d'une des tables métiers lié à "Personne"."id" alors
		 * que celle-ci se trouve sans prestation dans un dossier.
		 *
		 * @return string
		 */
		public function sqAncienAllocataire() {
			$cacheKey = Inflector::underscore( $this->Personne->useDbConfig ).'_'.Inflector::underscore( $this->Personne->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$condition = Cache::read( $cacheKey );

			if( $condition === false ) {
				if( !$this->Personne->Behaviors->attached( 'LinkedRecords' ) ) {
					$this->Personne->Behaviors->attach( 'LinkedRecords' );
				}

				$savedVirtualFields = $this->Personne->virtualFields;
				$this->Personne->linkedRecordsLoadVirtualFields( $this->anciensDossiersModelNames );

				$virtualFields = array();
				foreach( $this->Personne->virtualFields as $fieldName => $fieldCondition ) {
					if( strpos( $fieldName, 'has_' ) === 0 ) {
						$virtualFields[] = $fieldCondition;
					}
				}

				$this->Personne->virtualFields = $savedVirtualFields;

				$condition = $this->Personne->getDatasource()->conditions( array( 'OR' => $virtualFields ), true, false, $this->Personne );

				Cache::write( $cacheKey, $condition );
			}

			return $condition;
		}

		/**
		 * Permet d'obtenir un querydata ou les résultats du querydata des
		 * anciens dossiers de la personne, dans lesquels celle-ci n'a plus de
		 * prestation mais possède toujours des enregistrements du modèle.
		 *
		 * @param integer $personne_id L'id de la personne
		 * @param string $modelAlias L'alias du modèle
		 * @param boolean $asQuery true pour obtenir le querydata, false pour obtenir les résultats
		 * @param integer $differenceThreshold
		 * @return array
		 */
		public function getEntriesAnciensDossiers( $personne_id, $modelAlias, $asQuery = false, $differenceThreshold = 4 ) {
			if( !$this->Personne->Behaviors->attached( 'LinkedRecords' ) ) {
				$this->Personne->Behaviors->attach( 'LinkedRecords' );
			}

			$cacheKey = Inflector::underscore( $this->Personne->useDbConfig ).'_'.Inflector::underscore( $this->Personne->alias ).'_'.Inflector::underscore( __FUNCTION__ )."_{$modelAlias}_{$differenceThreshold}";
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$replacements = array( 'Personne' => 'Personne2' );
				$virtualField = $this->Personne->linkedRecordVirtualField( $modelAlias );
				$virtualField = preg_replace( '/^EXISTS\( SELECT (.*) AS (.*) FROM /', '( SELECT COUNT( \1 ) FROM ', $virtualField );
				$virtualField = str_replace( '"Personne"."id"', '"Personne2"."id"', $virtualField );

				$aliasedVirtualField = $virtualField.' AS "Personne__records"';

				$query = array(
					'fields' => array(
						'Personne2.id',
						'Dossier.id',
						'Dossier.numdemrsa',
						'Dossier.matricule',
						'Dossier.dtdemrsa',
						$aliasedVirtualField
					),
					'contain' => false,
					'joins' => array(
						array(
							'table' => '"personnes"',
							'alias' => 'Personne2',
							'type' => 'INNER',
							'conditions' => $this->conditionsRapprochementPersonne1Personne2( 'Personne', 'Personne2', false )
						),
						array_words_replace( $this->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ), $replacements ),
						$this->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					),
					'conditions' => array(
						'NOT EXISTS( SELECT prestations.id FROM prestations WHERE prestations.personne_id = Personne2.id )',
						"{$virtualField} >" => 0
					),
					'order' => array(
						'Dossier.dtdemrsa DESC'
					)
				);

				Cache::write( $cacheKey, $query );
			}

			$query['conditions']['Personne.id'] = $personne_id;

			if( $asQuery ) {
				return $query;
			}

			return $this->Personne->find( 'all', $query );
		}

		/**
		 * Retourne un array de conditions permettant de voir si les 2 personnes
		 * sont les mêmes, au sein d'un même foyer ou non.
		 *
		 * Pour faire le rapprochement, on se base soit:
		 *	- sur les NIR (13) et la date de naissance
		 *  - sur nom, prénom et date de naissance
		 *	- sur de faibles différences de nom et prénom, plus la date de naissance,
		 *		si la librairie pg_trgm de PostgreSQL est installée.
		 *
		 * @param string $personne1Alias
		 * @param string $personne2Alias
		 * @param boolean $memeFoyer
		 * @param integer $similarityThreshold
		 * @return array
		 */
		public function conditionsRapprochementPersonne1Personne2( $personne1Alias = 'Personne1', $personne2Alias = 'Personne2', $memeFoyer = false, $similarityThreshold = 0.3 ) {
			$memeFoyer = ( $memeFoyer ? '=' : '<>' );

			$conditions = array(
				"{$personne1Alias}.id <> {$personne2Alias}.id",
				"{$personne1Alias}.foyer_id {$memeFoyer} {$personne2Alias}.foyer_id",
				"OR" => array(
					array(
						"nir_correct13({$personne1Alias}.nir)",
						"nir_correct13({$personne2Alias}.nir)",
						"SUBSTRING( TRIM( BOTH ' ' FROM {$personne1Alias}.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH ' ' FROM {$personne2Alias}.nir ) FROM 1 FOR 13 )",
						"{$personne1Alias}.dtnai = {$personne2Alias}.dtnai"
					),
					array(
						"UPPER({$personne1Alias}.nom) = UPPER({$personne2Alias}.nom)",
						"UPPER({$personne1Alias}.prenom) = UPPER({$personne2Alias}.prenom)",
						"{$personne1Alias}.dtnai = {$personne2Alias}.dtnai"
					),
				)
			);

			$WebrsaCheck = ClassRegistry::init( "WebrsaCheck" );
			if( Hash::get( $WebrsaCheck->checkPostgresPgtrgmFunctions(), "success" ) ) {
				$conditions['OR'][] = array(
					"similarity({$personne1Alias}.nom, {$personne2Alias}.nom) >=" => $similarityThreshold,
					"similarity({$personne1Alias}.prenom, {$personne2Alias}.prenom) >=" => $similarityThreshold,
					"{$personne1Alias}.dtnai = {$personne2Alias}.dtnai"
				);
			}

			return $conditions;
		}

		/**
		 *
		 * @param integer $personne_id
		 * @param boolean $asQuery
		 * @param integer $differenceThreshold
		 * @return array
		 */
		public function getAnciensDossiers( $personne_id, $asQuery = false, $differenceThreshold = 4 ) {
			if( !$this->Personne->Behaviors->attached( 'LinkedRecords' ) ) {
				$this->Personne->Behaviors->attach( 'LinkedRecords' );
			}

			$cacheKey = Inflector::underscore( $this->Personne->useDbConfig ).'_'.Inflector::underscore( $this->Personne->alias ).'_'.Inflector::underscore( __FUNCTION__ )."_{$differenceThreshold}";
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$replacements = array( 'Personne' => 'Personne2' );

				$virtualFields = array();
				foreach( $this->anciensDossiersModelNames as $modelName ) {
					$virtualField = $this->Personne->linkedRecordVirtualField( $modelName );
					$virtualFields[] = str_replace( '"Personne"."id"', '"Personne2"."id"', $virtualField );
				}

				$query = array(
					'fields' => array(
						'Personne2.id',
						'Dossier.id',
						'Dossier.numdemrsa',
						'Dossier.matricule',
						'Dossier.dtdemrsa',
						'Situationdossierrsa.etatdosrsa'
					),
					'contain' => false,
					'joins' => array(
						array(
							'table' => '"personnes"',
							'alias' => 'Personne2',
							'type' => 'INNER',
							'conditions' => $this->conditionsRapprochementPersonne1Personne2( 'Personne', 'Personne2', false )
						),
						array_words_replace( $this->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ), $replacements ),
						$this->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'LEFT OUTER' ) ),
					),
					'conditions' => array(
						'NOT EXISTS( SELECT prestations.id FROM prestations WHERE prestations.personne_id = Personne2.id )',
						'OR' => $virtualFields
					),
					'order' => array(
						'Dossier.dtdemrsa DESC'
					)
				);

                if( Configure::read( 'Cg.departement' ) == 66 ) {
                    $query['fields'][] = '( '.$this->Personne->Foyer->vfNbDossierPCG66( 'Foyer.id ').' ) AS "Foyer__nbdossierspcgs"';
                }

				Cache::write( $cacheKey, $query );
			}

			$query['conditions']['Personne.id'] = $personne_id;

			if( $asQuery ) {
				return $query;
			}

			return $this->Personne->find( 'all', $query );
		}

		/**
		 * Préchargement du cache du modèle.
		 *
		 * @see Configure AncienAllocataire.enabled
		 */
		public function prechargement() {
			$success = parent::prechargement();

			if( Configure::read( 'AncienAllocataire.enabled' ) ) {
				$success = ( $success !== false );

				foreach( $this->anciensDossiersModelNames as $modelAlias ) {
					$query = $this->getEntriesAnciensDossiers( null, $modelAlias, true );
					$success = !empty( $query ) && $success;
				}

				$query = $this->getAnciensDossiers( null, true );
				$success = !empty( $query ) && $success;

				$condition = $this->sqAncienAllocataire();
				$success = !empty( $condition ) && $success;
			}

			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$vfEtapeDossierOrientation58 = $this->vfEtapeDossierOrientation58();
				$success = !empty( $vfEtapeDossierOrientation58 ) && $success;
			}

			return $success;
		}

		/**
		 * Étape du dossier d'orientation de l'allocataire, du point de vue du CG 58:
		 *	- Orienté: l'allocataire possède une orientation effective
		 *	- En attente: l'allocataire ne possède pas d'orientation effective,
		 *		mais possède un dossier de  COV ou d'EP pouvant entraîner une orientation.
		 *	- Non orienté: l'allocataire ne possède pas d'orientation effective et
		 *		ne possède pas de dossier de COV ou d'EP pouvant entraîner une orientation.
		 *
		 * INFO: les tables des sous-requêtes sont aliasées et en minuscules (sinon,
		 * ça pose un problème pour les conditions).
		 *
		 * @param string $personneId L'alias du champ Personne.id (défaut avec l'alias du modèle)
		 * @return string
		 */
		public function vfEtapeDossierOrientation58( $personneId = null ) {
			$personneId = ( $personneId !== null ? $personneId : "{$this->Personne->alias}.{$this->Personne->primaryKey}" );
			$cacheKey = implode( '_', array( $this->Personne->useDbConfig, $this->Personne->alias, __FUNCTION__, $personneId ) );
			$return = Cache::read( $cacheKey );

			if( $return === false ) {
				// 1. Orientation effective ?
				$query = array(
					'fields' => array(
						'Orientstruct.id'
					),
					'contain' => false,
					'conditions' => array(
						"Orientstruct.personne_id = {$personneId}",
						'Orientstruct.statut_orient' => 'Orienté'
					),
					'limit' => 1
				);

				$existsOrientsstruct = $this->Personne->Orientstruct->sq( $query );
				$existsOrientsstruct = array_words_replace(
					array( $existsOrientsstruct ),
					array(
						'Orientstruct' => 'orientsstructseffectives'
					)
				);
				$existsOrientsstruct = "EXISTS( {$existsOrientsstruct[0]} )";

				// 2. Passage en EP pouvant conduire à une réorientation ?
				$Commissionep = $this->Personne->Dossierep->Passagecommissionep->Commissionep;

				$query = array(
					'fields' => array(
						'Passagecommissionep.id'
					),
					'contain' => false,
					'joins' => array(
						$this->Personne->Dossierep->join( 'Passagecommissionep', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Dossierep->Passagecommissionep->join( 'Commissionep', array( 'type' => 'LEFT OUTER' ) )
					),
					'conditions' => array(
						'Dossierep.actif' => '1',
						"Dossierep.personne_id = {$personneId}",
						'Dossierep.themeep' => $this->Personne->Dossierep->getThematiquesReorientations(),
						'Commissionep.etatcommissionep' => $Commissionep::$etatsEnCours
					),
					'limit' => 1
				);

				$existsPassagecommissionep = $this->Personne->Dossierep->sq( $query );
				$existsPassagecommissionep = array_words_replace(
					array( $existsPassagecommissionep ),
					array(
						'Dossierep' => 'dossiersepsorients',
						'Passagecommissionep' => 'passagescommissionsepsorients',
						'Commissionep' => 'commissionsepsorients',
					)
				);
				$existsPassagecommissionep = "EXISTS( {$existsPassagecommissionep[0]} )";

				// 3. Passage en COV pouvant conduire à une réorientation ?
				$Cov58 = $this->Personne->Dossiercov58->Passagecov58->Cov58;

				$query = array(
					'fields' => array(
						'Passagecov58.id'
					),
					'contain' => false,
					'joins' => array(
						$this->Personne->Dossiercov58->join( 'Passagecov58', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Dossiercov58->Passagecov58->join( 'Cov58', array( 'type' => 'LEFT OUTER' ) )
					),
					'conditions' => array(
						"Dossiercov58.personne_id = {$personneId}",
						'Dossiercov58.themecov58' => $this->Personne->Dossiercov58->getThematiquesReorientations(),
						'Cov58.etatcov' => $Cov58::$etatsEnCours
					),
					'limit' => 1
				);

				$existsPassagecov58 = $this->Personne->Dossiercov58->sq( $query );
				$existsPassagecov58 = array_words_replace(
					array( $existsPassagecov58 ),
					array(
						'Dossiercov58' => 'dossierscovs58orients',
						'Passagecov58' => 'passagescovs58orients',
						'Cov58' => 'covs58orients',
					)
				);
				$existsPassagecov58 = "EXISTS( {$existsPassagecov58[0]} )";

				$return = '( CASE
						WHEN ( ( '.$existsOrientsstruct.' ) AND ( '.$existsPassagecommissionep.' OR '.$existsPassagecov58.' ) ) THEN \'en_cours_reorientation\'
						WHEN ( '.$existsOrientsstruct.' ) THEN \'oriente\'
						WHEN ( '.$existsPassagecommissionep.' OR '.$existsPassagecov58.' ) THEN \'en_attente\'
						ELSE \'non_oriente\'
					END )';

				Cache::write( $cacheKey, $return );
			}

			return $return;
		}

		/**
		 * Permet de compléter un querydata avec le champ virtuel Personne.etat_dossier_orientation
		 * et d'ajouter une condition concernant ce champ s'il existe une valeur
		 * non nulle dans les filtres envoyés par le moteur de recherche. (CG 58)
		 *
		 * @param array $query Le querydata à compléter.
		 * @param array $search Les filtres envoyés par le moteur de recherche.
		 * @return array
		 */
		public function completeQueryVfEtapeDossierOrientation58( array $query, array $search = array() ) {
			$field = 'Personne.etat_dossier_orientation';

			$this->Personne->virtualFields['etat_dossier_orientation'] = $this->vfEtapeDossierOrientation58();
			$query['fields'][] = $field;

			$etat_dossier_orientation = Hash::get( $search, $field );
			if( !empty( $etat_dossier_orientation ) ) {
				$query['conditions'][$field] = $etat_dossier_orientation;
			}

			return $query;
		}

		/**
		 * Complète le querydata avec des conditions permettant de savoir s'il
		 * existe (au moins) un enregistrement lié à un des modèles passés en
		 * paramètre, suivant les filtres de recherche (<alias>.has_<modèle lié underscored>).
		 *
		 * @param array $linkedModelNames La liste des modèles contenant un éventuel
		 *	querydata en valeur.
		 * @param array $query Le querydata à compléter
		 * @param array $search Les filtres venant du moteur de recherche.
		 * @return array
		 */
		public function completeQueryHasLinkedRecord( array $linkedModelNames, array $query, array $search = array() ) {
			if( $this->Personne->Behaviors->attached( 'LinkedRecords' ) === false ) {
				$this->Personne->Behaviors->attach( 'LinkedRecords' );
			}

			foreach( Hash::normalize( $linkedModelNames ) as $linkedModelName => $qd ) {
				$fieldName = 'has_'.Inflector::underscore( $linkedModelName );
				$exists = (string)Hash::get( $search, "{$this->Personne->alias}.{$fieldName}" );
				if( in_array( $exists, array( '0', '1' ), true ) ) {
					$sql = $this->Personne->linkedRecordVirtualField( $linkedModelName, $qd );
					$query['conditions'][] = $exists ? $sql : 'NOT ' . $sql;
				}
			}

			return $query;
		}

		/**
		 * Permet d'économiser énormement de traitements en mode dev
		 * @var boolean|array
		 */
		protected $_listedForeignKey = false;

		/**
		 * Permet d'obtenir la liste des tables liés à Personne
		 *
		 * @return array <array 0: 'table1', 1: 'table2', ...>
		 */
		public function listForeignKey() {
			$cacheKey = 'cache_'.__CLASS__.'-'.__FUNCTION__;
			$cache = $this->_listedForeignKey ?: Cache::read($cacheKey);

			if ($cache === false ) {
				$schema = Hash::get( $this->Personne->getDataSource()->config, 'schema' );
				$cache = Hash::extract($this->Personne->getDataSource()->getPostgresForeignKeys(
					array(
						'"From"."table_schema" = \''.$schema.'\'',
						'"To"."table_schema" = \''.$schema.'\'',
						'"To"."table_name" = \'personnes\'',
						'"To"."column_name" = \'id\''
					)
				), '{n}.From.table');
				Cache::write($cacheKey, $cache);
				$this->_listedForeignKey = $cache;
			}

			return $cache;
		}

		/**
		 * Liste des modeles à ne pas parcourir ou déjà parcouru
		 * Ne pas parcourir les modèles qui ne dépendent pas directement d'une personne
		 *
		 * ATTENTION !!! Modifier les valeurs dans resetFindLinkedPersonne()
		 *
		 * @var array
		 */
		public $explored = array(
			'personnes',
			'foyers',
			'dossiers',
			'correspondancespersonnes',
			'fichiersmodules',
			'tags',
			'structuresreferentes',
			'referents',
			'users',
			'entreesromesv3',
			'typesorients',
			'codesromemetiersdsps66',
			'codesromesecteursdsps66',
			'partenaires',
			'servicesinstructeurs',
			'rendezvous_thematiquesrdvs',
			'thematiquesrdvs',
			'typesrdv',
			'populationsb3pdvs93',
			'polesdossierspcgs66',
		);

		/**
		 * Liste des liens trouvés
		 * @var array - <array 'Model1.Model2...': $id, ...> En clef la liste des Tables parcouru et en valeur l'id de la derniere table
		 */
		public $found = array();

		/**
		 * Enregistre l'existance d'un enregistrement en base
		 * <array 'Model1': array($personne_id: true), 'Model2': array($personne_id: false), ...>
		 * @var array
		 */
		public $haveLine = array();

		/**
		 * Verifi l'existance d'une ligne pour un modele donné grâce à personne_id et met en cache
		 *
		 * @param Model $Model
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function haveLine($Model, $personne_id) {
			$modelName = $Model->alias;
			$ignoreList = array(
				'Correspondancepersonne', // Contient personne1_id et personne2_id
			);

			if (Configure::read('Cg.departement') == 66) {
				$ignoreList[] = 'Apre'; // Apre66 remplace Apre
			}

			if (in_array($modelName, $ignoreList)) {
				return false;
			}

			$cache = isset($this->haveLine[$modelName][$personne_id]) ? array($modelName => $this->haveLine[$modelName][$personne_id]) : false;

			if ($cache === false) {
				$join = $this->Personne->join( $modelName );

				$Dbo = $Model->getDataSource();
				$dq = $Dbo->startQuote;
				$eq = $Dbo->endQuote;
				$escapedModelName = $dq.$this->Personne->alias.$eq;
				$escapedPrimary = $dq.$this->Personne->primaryKey.$eq;

				$conditions = array_words_replace(
					(array)$join['conditions'],
					array(
						'{$__cakeID__$}' => $personne_id,
						"{$escapedModelName}.{$escapedPrimary}" => $personne_id
					)
				);

				$result = ClassRegistry::init($modelName)->find('first',
					array(
						'fields' => 'id',
						'conditions' => $conditions,
						'contain' => false
					)
				);
				$cache[$modelName] = !empty($result) ? Hash::get($result, $modelName.'.id') : false;
				$this->haveLine = array_merge($this->haveLine, $cache);
			}

			return $cache[$modelName];
		}

		/**
		 * Permet d'obtenir la liste des enregistrements dont dépendendes une Personne en fonction d'un nom de modele
		 * Utiliser <Personne::resetFindLinkedPersonne()> entre deux utilisations pour obtenir des liens à valeur identique depuis
		 * un autre nom de modele.
		 *
		 * /!\ <ATTENTION> - Cette fonction utilise begin() et rollback() pour tester la suppression en cascade des modèles liés.
		 * Ne pas utiliser à l'interieur d'une autre transaction sous peine de bug.
		 *
		 * @param integer $personne_id
		 * @param string $modelName
		 * @param array $prevNames - Laisser vide
		 * @return array $this->found
		 */
		public function findLinkedDepedent( $personne_id, $modelName, $prevNames = array() ) {
			if (preg_match('/([0-9]{2,3})$/', $modelName, $matches) && $matches[1] !== Configure::read('Cg.departement')) {
				return $this->found;
			}

			$linkedModels = array(
				'belongsTo' => array(),
				'hasOne' => array(),
				'hasMany' => array(),
				'hasAndBelongsToMany' => array(),
			);
			$prevNames[] = $modelName;
			$Model = $this->Personne;
			foreach ($prevNames as $name) {
				$Model = $Model->{$name};
			}

			// Si le premier modele n'a pas d'enregistrement, inutile de continuer (sinon c'est mis en cache donc rapide)
			if (!$this->haveLine($this->Personne->{$prevNames[0]}, $personne_id)) {
				return $this->found;
			}

			// Profondeur de recherche maximale
			if (count($prevNames) > 10) {
				return $this->found;
			}

			$this->explored = array_merge($this->explored, array($Model->useTable));

			foreach (array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany') as $typeJoin) {
				if (isset($Model->{$typeJoin})) {
					$linkedModels[$typeJoin] = $Model->{$typeJoin};
					foreach (array_keys($linkedModels[$typeJoin]) as $key) {
						$linkedModels[$typeJoin][$key]['typeJoin'] = $typeJoin;
					}
				} else {
					$linkedModels[$typeJoin] = array();
				}
			}

			$models = array_merge(
				$linkedModels['belongsTo'],
				$linkedModels['hasOne'],
				$linkedModels['hasMany'],
				$linkedModels['hasAndBelongsToMany']
			);

			$findMore = array();
			foreach ($models as $linkedModelName => $params) {
				$className = $params['typeJoin'] === 'hasAndBelongsToMany' ? $params['with'] : $params['className'];
				$LinkedModel = ClassRegistry::init($className);
				$usableName = $params['typeJoin'] === 'hasAndBelongsToMany' ? $params['with'] : $linkedModelName;
				$isLinked = false;

				// Ne traite pas les modèles des autres départements
				if (preg_match('/([0-9]{2,3})$/', $usableName, $matches) && $matches[1] !== Configure::read('Cg.departement')) {
					continue;
				}

				// On vérifi qu'un lien existe avec la personne concerné
				if (!in_array($LinkedModel->useTable, $this->explored)) {
					$query = array(
						'fields' => array(
							$usableName.'.id',
							$prevNames[0].'.id',
						),
						'contain' => false,
						'joins' => array(),
						'conditions' => array($prevNames[0].'.personne_id' => $personne_id),
						'limit' => 101
					);

					// Si la table actuelle est lié à personne, on s'assure qu'elle est la même que $personne_id
					if (in_array($LinkedModel->useTable, $this->listForeignKey())) {
						$query['conditions'][] = array($usableName.'.personne_id' => $personne_id);
					}

					$lastModel = $this->Personne;
					try {
						foreach (array_merge($prevNames, array($usableName)) as $name) {
							// @throw [CakeException] Unknown status code #0 - Si $lastModel ne connait pas $name
							$query['joins'][] = $lastModel->join($name, array('type' => 'INNER'));
							$lastModel = $lastModel->{$name};
						}

						// @throw SQLSTATE[42703]: Undefined column - Si une mauvaise relation est définie
						$isLinked = $this->Personne->find('all', $query);

					} catch (Exception $e) {
						debug('bug! '.$lastModel->alias.' -> '.$name);
					}
				}

				// Alerte en mode dev pour avertir qu'il manque des valeurs dans Personne::$explored
				if (count($isLinked) > 100) {
					debug(array('ALERTE! cette requête renvoi plus de 100 lignes ! Ajoutez des valeurs dans Personne::$explored sur les tables qui ne dépendent pas d\'un allocataire ex: '.$LinkedModel->useTable => $query));
				}

				// La table actuelle fait parti des tables reliés directement à Personne
				if (in_array($LinkedModel->useTable, $this->listForeignKey())
						&& !empty($isLinked)
				) {
					$this->found = array_merge(
						$this->found,
						array(implode('.', $prevNames).'.'.$usableName => $isLinked)
					);
				}

				// Si un lien existe, on approfondi la recherche pour le modele actuel
				if (!empty($isLinked)) {
					$findMore[$usableName] = $params;
				}
			}

			foreach ($findMore as $linkedModelName => $params) {
				$LinkedModel = $Model->{$linkedModelName};
				if (!in_array($LinkedModel->useTable, $this->explored)) {
					$this->findLinkedDepedent($personne_id, $linkedModelName, $prevNames);
				}
			}

			return $this->found;
		}

		/**
		 * Permet d'effacer la mémoire de la fonction Personne::findLinkedPersonne()
		 */
		public function resetFindLinkedPersonne() {
			$this->explored = array(
				'personnes',
				'foyers',
				'dossiers',
				'correspondancespersonnes',
				'fichiersmodules',
				'tags',
				'structuresreferentes',
				'referents',
				'users',
				'entreesromesv3',
				'typesorients',
				'codesromemetiersdsps66',
				'codesromesecteursdsps66',
				'partenaires',
				'servicesinstructeurs',
				'actionscandidats_zonesgeographiques',
				'zonesgeographiques',
				'eps_zonesgeographiques',
				'membreseps',
				'rendezvous_thematiquesrdvs',
				'thematiquesrdvs',
				'typesrdv',
				'populationsb3pdvs93',
				'polesdossierspcgs66',
			);
			$this->found = array();
		}

		/**
		 * Permet d'obtenir les liens entre les tables liés à Personne
		 *
		 * @param string|array $personne_ids
		 * @return array - <array $personne_id1: array ('Model1.Model2.Model3': $model3_id), ...>
		 */
		public function getLinksBetweenTables($personne_ids) {
			$links = array();
			$personneLinkedModels = array_merge(
				Hash::extract($this->Personne->hasOne, '{s}.className'),
				Hash::extract($this->Personne->hasMany, '{s}.className')
			);
			foreach ((array)$personne_ids as $id) {
				$links[$id] = array();

				foreach ($personneLinkedModels as $name) {
					$link = $this->findLinkedDepedent($id, $name);
					if (!empty($link)) {
						$links[$id] = array_merge($links[$id], $link);
					}
					$this->resetFindLinkedPersonne();
				}
			}

			return $links;
		}
	}