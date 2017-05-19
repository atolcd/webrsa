<?php
	/**
	 * Code source de la classe Dossiercov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Dossiercov58 ...
	 *
	 * @package app.Model
	 */
	class Dossiercov58 extends AppModel
	{
		public $name = 'Dossiercov58';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Allocatairelie',
			'Containable',
			'DossierCommission',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Themecov58' => array(
				'className' => 'Themecov58',
				'foreignKey' => 'themecov58_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasOne = array(
			'Propoorientationcov58' => array(
				'className' => 'Propoorientationcov58',
				'foreignKey' => 'dossiercov58_id',
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
			'Propoorientsocialecov58' => array(
				'className' => 'Propoorientsocialecov58',
				'foreignKey' => 'dossiercov58_id',
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
			'Propocontratinsertioncov58' => array(
				'className' => 'Propocontratinsertioncov58',
				'foreignKey' => 'dossiercov58_id',
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
			'Propononorientationprocov58' => array(
				'className' => 'Propononorientationprocov58',
				'foreignKey' => 'dossiercov58_id',
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
			'Nonorientationprocov58' => array(
				'className' => 'Nonorientationprocov58',
				'foreignKey' => 'dossiercov58_id',
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
			'Regressionorientationcov58' => array(
				'className' => 'Regressionorientationcov58',
				'foreignKey' => 'dossiercov58_id',
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
			'Passagecov58' => array(
				'className' => 'Passagecov58',
				'foreignKey' => 'dossiercov58_id',
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
		 * Liste des thématiques de COV qui disparaissent.
		 *
		 * Permettra de ne pas afficher d'onglet pour cette thématique (choix des
		 * dossiers, liste des dossiers d'une commission, prise de décisions, ...)
		 * si celle-ci ne contient aucun dossier.
		 *
		 * @var array
		 */
		public $anciennesThematiques = array( 'proposnonorientationsproscovs58' );

		/**
		* FIXME -> aucun dossier en cours, pour certains thèmes:
		*		- CG 93
		*			* Nonrespectsanctionep93 -> ne débouche pas sur une orientation: '1reduction', '1maintien', '1sursis', '2suspensiontotale', '2suspensionpartielle', '2maintien'
		*			* Reorientationep93 -> peut déboucher sur une réorientation
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
			$nbDossierscov = $this->find(
				'count',
				array(
					'conditions' => array(
						'Dossiercov58.personne_id' => $personne_id,
						'OR' => array(
							'Passagecov58.etatdossiercov NOT' => array( 'traite', 'annule' ),
							'Passagecov58.etatdossiercov IS NULL'
						),
						"Passagecov58.id IN ( {$this->Passagecov58->sqDernier()} )"
					),
					'joins' => array(
						$this->join( 'Passagecov58' )
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
					'contain' => false
				)
			);

			return ( ( $nbDossierscov == 0 ) && ( $nbPersonnes == 1 ) );
		}

		/**
		 * Retourne un querydata permettant de trouver les dossiers COV en cours.
		 *
		 * @param integer $personne_id
		 * @param string|array $themes
		 * @return array
		 */
		public function qdDossiersNonFinalises( $personne_id, $themes = null ) {
			$qdSubquery = array(
				'fields' => array(
					'passagescovs58.dossiercov58_id'
				),
				'alias' => 'passagescovs58',
				'conditions' => array(
					'dossierscovs58.personne_id' => $personne_id,
					'passagescovs58.etatdossiercov' => array( 'traite', 'annule' )
				),
				'joins' => array(
					array(
						'table' => 'dossierscovs58',
						'alias' => 'dossierscovs58',
						'type' => 'INNER',
						'conditions' => array(
							'passagescovs58.dossiercov58_id = dossierscovs58.id'
						)
					),
					array(
						'table' => 'covs58',
						'alias' => 'covs58',
						'type' => 'INNER',
						'conditions' => array(
							'passagescovs58.cov58_id = covs58.id'
						)
					)
				)
			);

			$themes = (array)$themes;

			if( !empty( $themes ) ) {
				$qdSubquery['conditions']['dossierscovs58.themecov58'] = $themes;
			}

			$querydata = array(
				'conditions' => array(
					'Dossiercov58.id NOT IN ( '.$this->Passagecov58->sq( $qdSubquery ).' )',
					'Dossiercov58.personne_id' => $personne_id
				),
				'joins' => array(
					$this->join( 'Passagecov58', array( 'type' => 'LEFT OUTER' ) )
				),
				'contain' => false
			);

			if( !empty( $themes ) ) {
				$querydata['conditions']['Dossiercov58.themecov58'] = $themes;

				foreach( $themes as $theme ) {
					$modelDecisionTheme = Inflector::classify( Inflector::singularize( "decisions{$theme}" ) );
					$querydata['joins'][] = $this->Passagecov58->join( $modelDecisionTheme, array( 'type' => 'LEFT OUTER' ) );
				}
			}

			return $querydata;
		}

		/**
		 * Liste des thématiques de COV conduisant à une réorientation, selon le
		 * département configuré.
		 *
		 * @see dossierscovs58.themecov58
		 *
		 * @return array
		 */
		public function getThematiquesReorientations() {
			$thematiques = array();

			foreach( $this->Personne->Orientstruct->hasMany as $alias => $params ) {
				if(
					( $params['foreignKey'] === 'nvorientstruct_id' )
					&& preg_match( '/cov'.Configure::read( 'Cg.departement' ).'$/', $params['className'] )
				) {
					$thematiques[] = Inflector::tableize( $params['className'] );
				}
			}

			return $thematiques;
		}

		/**
		 * Retourne le querydata permettant d'obtenir les dossiers, personnes,
		 * dernier passage éventuel et sa commission éventuelle.
		 *
		 * @return array
		 */
		public function getDossiersQuery() {
			$query = array(
				'fields' => array_merge(
					$this->fields(),
					$this->Passagecov58->fields(),
					$this->Personne->fields(),
					$this->Passagecov58->Cov58->fields()
				),
				'contain' => false,
				'joins' => array(
					$this->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->join( 'Passagecov58', array( 'type' => 'LEFT OUTER' ) ),
					$this->Passagecov58->join( 'Cov58', array( 'type' => 'LEFT OUTER' ) ),
				),
				'conditions' => array(
					array(
						'OR' => array(
							'Passagecov58.id IS NULL',
							'Passagecov58.id IN ( '.$this->Passagecov58->sqDernier().' )'
						)
					)
				)
			);

			return $query;
		}

		/**
		 * Retourne un querydata permettant de cibler tous les dossiers de COV en
		 * cours de traitement pour un bénéficiaire donné.
		 *
		 * @param integer $personne_id L'id du bénéficiaire
		 * @return array
		 */
		public function qdDossiersepsOuverts( $personne_id ) {
			$Cov58 = $this->Passagecov58->Cov58;

			return array(
				'conditions' => array(
					'Dossiercov58.personne_id' => $personne_id,
					array(
						'OR' => array(
							'Cov58.id IS NULL',
							'Cov58.etatcov' => $Cov58::$etatsEnCours,
							array(
								'NOT' => array(
									'Passagecov58.etatdossiercov' => array( 'traite', 'annule' )
								)
							)
						)
					)
				),
				'contain' => false
			);
		}

		/**
		 * Retourne la liste des dossiers de l'allocataire en cours de passage
		 * en commission et pouvant déboucher sur une réorientation.
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function getReorientationsEnCours( $personne_id ) {
			if( Configure::read( 'Cg.departement' ) != 58 ) {
				return array();
			}

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$Cov58 = $this->Passagecov58->Cov58;

				$query = $this->getDossiersQuery();
				$query['fields'] = array(
					'Personne.id',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Dossiercov58.id',
					'Dossiercov58.created',
					'Dossiercov58.themecov58',
					'Passagecov58.id',
					'Passagecov58.etatdossiercov',
					'Cov58.id',
					'Cov58.datecommission',
					'Cov58.etatcov',
				);

				$query['conditions'][] = array(
					'Dossiercov58.themecov58' => $this->getThematiquesReorientations(),
					array(
						'OR' => array(
							'Cov58.id IS NULL',
							'Cov58.etatcov' => $Cov58::$etatsEnCours,
							array(
								'NOT' => array(
									'Passagecov58.etatdossiercov' => array( 'traite', 'annule' )
								)
							)
						)
					)
				);

				$query = $this->getCompletedQueryDetailsOrientstruct( $query );

				Cache::write( $cacheKey, $query );
			}

			// La condition sur la personne
			$query['conditions'][] = array( "{$this->alias}.personne_id" => $personne_id );

			// On force les champs virtuels pour la requête
			$forceVirtualFields = $this->forceVirtualFields;
			$this->forceVirtualFields = true;
			$results = (array)$this->find( 'all', $query );
			$this->forceVirtualFields = $forceVirtualFields;

			return $results;
		}

		/**
		 * Complète un querydata afin d'avoir les informations sur le dossier
		 * COV, son pasage en COV et la COV qui a mené à un Orientstruct donné.
		 *
		 * @param array $query
		 * @param string $field
		 * @return array
		 */
		public function getCompletedQueryOrientstruct( array $query, $field = 'Orientstruct.id' ) {
			// 1. Jointure sur le dossier COV
			$sql = $this->Passagecov58->sq(
				array(
					'fields' => array(
						'Passagecov58.dossiercov58_id'
					),
					'joins' => array(
						$this->Passagecov58->join( 'Dossiercov58', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Themecov58', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Propononorientationprocov58', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Propoorientationcov58', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Propoorientsocialecov58', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Nonorientationprocov58', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Regressionorientationcov58', array( 'type' => 'LEFT OUTER' ) ),
					),
					'conditions' => array(
						'Dossiercov58.personne_id = Personne.id',
						'Themecov58.name' => $this->getThematiquesReorientations(),
						'Passagecov58.etatdossiercov' => 'traite',
						'OR' => array(
							array(
								'Propoorientationcov58.nvorientstruct_id IS NULL',
								'Propoorientsocialecov58.nvorientstruct_id IS NULL',
								'Propononorientationprocov58.nvorientstruct_id IS NOT NULL',
								'Nonorientationprocov58.nvorientstruct_id IS NULL',
								'Regressionorientationcov58.nvorientstruct_id IS NULL',
								'Propononorientationprocov58.nvorientstruct_id = Orientstruct.id',
							),
							array(
								'Propoorientationcov58.nvorientstruct_id IS NOT NULL',
								'Propoorientsocialecov58.nvorientstruct_id IS NULL',
								'Propononorientationprocov58.nvorientstruct_id IS NULL',
								'Nonorientationprocov58.nvorientstruct_id IS NULL',
								'Regressionorientationcov58.nvorientstruct_id IS NULL',
								'Propoorientationcov58.nvorientstruct_id = Orientstruct.id',
							),
							array(
								'Propoorientationcov58.nvorientstruct_id IS NULL',
								'Propoorientsocialecov58.nvorientstruct_id IS NOT NULL',
								'Propononorientationprocov58.nvorientstruct_id IS NULL',
								'Nonorientationprocov58.nvorientstruct_id IS NULL',
								'Regressionorientationcov58.nvorientstruct_id IS NULL',
								'Propoorientsocialecov58.nvorientstruct_id = Orientstruct.id',
							),
							array(
								'Propoorientationcov58.nvorientstruct_id IS NULL',
								'Propoorientsocialecov58.nvorientstruct_id IS NULL',
								'Propononorientationprocov58.nvorientstruct_id IS NULL',
								'Regressionorientationcov58.nvorientstruct_id IS NULL',
								'Nonorientationprocov58.nvorientstruct_id IS NOT NULL',
								'Nonorientationprocov58.nvorientstruct_id = Orientstruct.id',
							),
							array(
								'Propoorientationcov58.nvorientstruct_id IS NULL',
								'Propoorientsocialecov58.nvorientstruct_id IS NULL',
								'Propononorientationprocov58.nvorientstruct_id IS NULL',
								'Nonorientationprocov58.nvorientstruct_id IS NULL',
								'Regressionorientationcov58.nvorientstruct_id IS NOT NULL',
								'Regressionorientationcov58.nvorientstruct_id = Orientstruct.id',
							),
						)
					),
					'contain' => false,
				)
			);

			$sql = array_words_replace(
				array( $sql ),
				array(
					'Passagecov58' => 'passagescovs58',
					'Dossiercov58' => 'dossierscovs58',
					'Themecov58' => 'themescovs58',
					'Propoorientationcov58' => 'proposorientationscovs58',
					'Propononorientationprocov58' => 'proposnonorientationsproscovs58',
					'Propoorientsocialecov58' => 'proposorientssocialescovs58',
					'Nonorientationprocov58' => 'nonorientationsproscovs58',
					'Regressionorientationcov58' => 'regressionsorientationscovs58',
					'Passagecov58__dossiercov58_id' => 'passagescovs58__dossiercov58_id',
				)
			);
			$sql = $sql[0];

			$query['joins'][] = $this->Personne->join(
				'Dossiercov58',
				array(
					'type' => 'LEFT OUTER',
					'conditions' => array(
						'OR' => array(
							'Dossiercov58.id IS NULL',
							array(
								'Dossiercov58.themecov58' => $this->getThematiquesReorientations(),
								"Dossiercov58.id IN ( {$sql} )"
							)
						)
					)
				)
			);

			// 2. Jointure sur le dernier passage en COV
			$sqDernierPassagecov58 = $this->Passagecov58->sqDernier();
			$query['joins'][] = $this->join(
				'Passagecov58',
				array(
					'type' => 'LEFT OUTER',
					'conditions' => array(
						"Passagecov58.id IN ( {$sqDernierPassagecov58} )"
					)
				)
			);

			 $joinPassagecov58Cov58 = $this->Passagecov58->join( 'Cov58', array( 'type' => 'LEFT OUTER' ) );
			 $joinPassagecov58Cov58['conditions'] = array(
				$joinPassagecov58Cov58['conditions'],
				'Cov58.etatcov' => 'finalise'
			);
			$query['joins'][] = $joinPassagecov58Cov58;

			// 3. Jointure sur le site COV
			$query['joins'][] = $this->Passagecov58->Cov58->join( 'Sitecov58', array( 'type' => 'LEFT OUTER' ) );

			// 4. Ajout des champs
			$query['fields'] = Hash::merge(
				$query['fields'],
				array_merge(
					$this->fields(),
					$this->Passagecov58->fields(),
					$this->Passagecov58->Cov58->fields(),
					$this->Passagecov58->Cov58->Sitecov58->fields()
				)
			);

			return $query;
		}

		/**
		 * Surcharge de la méthode enums pour ajouter les anciennes thématiques
		 * à la liste des options.
		 *
		 * @see $anciennesThematiques
		 *
		 * @return array
		 */
		public function enums() {
			$enums = parent::enums();

			$enums[$this->alias]['vx_themecov58'] = $this->anciennesThematiques;

			return $enums;
		}

		/**
		 * Retourne une sous-requête permettant d'obtenir l'id du dossier de COV de la personne
		 * associé à la COV la plus récente.
		 *
		 * @param string $personneIdAlias Le champ désignant l'id de la personne
		 * @param array $conditions Les conditions supplémentaires à prendre en compte
		 * @return string
		 */
		public function sqDernierPassagePersonne( $personneIdAlias = 'Personne.id', array $conditions = array() ) {
			$replacements = array(
				'Dossiercov58' => 'dossierscovs58',
				'Passagecov58' => 'passagescovs58',
				'Cov58' => 'covs58'
			);

			$conditions = array_words_replace( $conditions, $replacements );
			$conditions[] = "dossierscovs58.personne_id = {$personneIdAlias}";

			return $this->sq(
				array(
					'fields' => array( 'dossierscovs58.id' ),
					'alias' => 'dossierscovs58',
					'joins' => array(
						array_words_replace( $this->join( 'Passagecov58', array( 'type' => 'INNER' ) ), $replacements ),
						array_words_replace( $this->Passagecov58->join( 'Cov58', array( 'type' => 'INNER' ) ), $replacements ),
					),
					'contain' => false,
					'conditions' => $conditions,
					'order' => array( 'covs58.datecommission DESC' ),
					'limit' => 1
				)
			);
		}
	}
?>