<?php
	/**
	 * Code source de la classe Relancenonrespectsanctionep93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Relancenonrespectsanctionep93 ...
	 *
	 * @package app.Model
	 */
	class Relancenonrespectsanctionep93 extends AppModel
	{
		public $name = 'Relancenonrespectsanctionep93';

		/**
		 * Behaviors utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Gedooo.Gedooo',
			'StorablePdf',
			'Conditionnable',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Règles de validation du modèle.
		 *
		 * @var array
		 */
		public $validate = array(
			'daterelance' => array(
				'checkForRelance' => array(
					'rule' => array( 'checkForRelance' ),
					'message' => 'Date de relance erronée'
				)
			)
		);

		/**
		 * Relations "belongsTo" du modèle.
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Nonrespectsanctionep93' => array(
				'className' => 'Nonrespectsanctionep93',
				'foreignKey' => 'nonrespectsanctionep93_id',
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
			)
		);

		/**
		 * Relations "hasOne" du modèle.
		 *
		 * @var array
		 */
		public $hasOne = array(
			'Pdf' => array(
				'className' => 'Pdf',
				'foreignKey' => 'fk_value',
				'dependent' => true,
				'conditions' => array(
					'Pdf.modele' => 'Relancenonrespectsanctionep93'
				),
				'fields' => null,
				'order' => null
			)
		);

		/**
		* Chemin relatif pour les modèles de documents .odt utilisés lors des
		* impressions. Utiliser %s pour remplacer par l'alias.
		*/
		public $modelesOdt = array(
			// FIXME: pdo,radiepe ? + dans checks_controller ligne 74
			'%s/notification_orientstruct_relance1.odt',
			'%s/notification_orientstruct_relance2.odt',
			'%s/notification_contratinsertion_relance1.odt',
			'%s/notification_contratinsertion_relance2.odt',

		);

		/**
		 * Fonction de validation qui vérifie si la date de relance demandée est suffisamment
		 * éloignée de la date d'orientation ou de validation du contrat, ainsi que par-rapport
		 * aux relances précédentes vis-à-vis du paramétrage.
		 *
		 * @param type $check
		 * @return boolean
		 */
		public function checkForRelance( $check ) {
			// Est-ce un nouvel enregistrement de relance / de la thématique nonrespectsanctionep ?
			$id = Hash::get( $this->data, 'Relancenonrespectsanctionep93.id' );
			if( empty( $id ) && !empty( $this->id ) ) {
				$id = $this->id;
			}

			$nonrespectsanctionep93_id = Hash::get( $this->data, 'Relancenonrespectsanctionep93.nonrespectsanctionep93_id' );
			if( empty( $nonrespectsanctionep93_id ) && !empty( $this->Nonrespectsanctionep93->id ) ) {
				$nonrespectsanctionep93_id = $this->Nonrespectsanctionep93->id;
			}

			if( !empty( $nonrespectsanctionep93_id ) ) {
				$nonrespectsanctionep93 = $this->Nonrespectsanctionep93->find(
					'first',
					array(
						'conditions'=>array(
							'Nonrespectsanctionep93.id' => $nonrespectsanctionep93_id
						),
						'contain'=>false
					)
				);
			}
			else if( !empty( $id ) ) {
				$nonrespectsanctionep93 = $this->Nonrespectsanctionep93->find(
					'first',
					array(
						'conditions'=>array(
							'Relancenonrespectsanctionep93.id' => $id
						),
						'joins' => array(
							$this->Nonrespectsanctionep93->join( $this->alias, array( 'type' => 'INNER' ) )
						),
						'contain'=>false
					)
				);
			}
			else {
				$nonrespectsanctionep93 = array(
					'Nonrespectsanctionep93' => $this->Nonrespectsanctionep93->data['Nonrespectsanctionep93']
				);
			}

			$dateminrelance = null;
			$possible = true;
			if ($nonrespectsanctionep93['Nonrespectsanctionep93']['origine']=='orientstruct') {
				switch($this->data['Relancenonrespectsanctionep93']['numrelance']) {
					case 1:
						$orientstruct = $this->Nonrespectsanctionep93->Orientstruct->find(
							'first',
							array(
								'conditions' => array(
									'Orientstruct.id' => $nonrespectsanctionep93['Nonrespectsanctionep93']['orientstruct_id']
								),
								'contain' => false
							)
						);
						$nbjours = Configure::read('Nonrespectsanctionep93.relanceOrientstructCer1');
						$dateminrelance = strtotime( "+{$nbjours} days", strtotime( $orientstruct['Orientstruct']['date_impression'] ) );
						break;
					case 2:
					case 3:
						$relanceprecedente = $this->find(
							'first',
							array(
								'conditions'=>array(
									'Relancenonrespectsanctionep93.numrelance' => $this->data['Relancenonrespectsanctionep93']['numrelance']-1,
									'Relancenonrespectsanctionep93.nonrespectsanctionep93_id' => $nonrespectsanctionep93_id
								),
								'contain'=>false
							)
						);
						$nbjours = Configure::read('Nonrespectsanctionep93.relanceOrientstructCer'.$this->data['Relancenonrespectsanctionep93']['numrelance']);
						$dateminrelance = strtotime( "+{$nbjours} days", strtotime( $relanceprecedente['Relancenonrespectsanctionep93']['daterelance'] ) );
						break;
				}
			}
			else {
				switch($this->data['Relancenonrespectsanctionep93']['numrelance']) {
					case 1:
						$contratinsertion = $this->Nonrespectsanctionep93->Contratinsertion->find(
							'first',
							array(
								'conditions' => array(
									'Contratinsertion.id' => $nonrespectsanctionep93['Nonrespectsanctionep93']['contratinsertion_id']
								),
								'contain' => false
							)
						);
						$nbjours = Configure::read('Nonrespectsanctionep93.relanceCerCer1');
						$dateminrelance = strtotime( "+{$nbjours} days", strtotime( $contratinsertion['Contratinsertion']['df_ci'] ) );
						break;
					case 2:
						$relanceprecedente = $this->find(
							'first',
							array(
								'conditions'=>array(
									'Relancenonrespectsanctionep93.numrelance' => 1,
									'Relancenonrespectsanctionep93.nonrespectsanctionep93_id' => $nonrespectsanctionep93_id
								),
								'contain'=>false
							)
						);
						$nbjours = Configure::read('Nonrespectsanctionep93.relanceCerCer2');
						$dateminrelance = strtotime( "+{$nbjours} days", strtotime( $relanceprecedente['Relancenonrespectsanctionep93']['daterelance'] ) );
						break;
				}
			}

			if ( null === $dateminrelance || $dateminrelance >= strtotime( $this->data['Relancenonrespectsanctionep93']['daterelance'] ) ) {
				$possible = false;
			}

			return $possible;
		}

		/**
		 * Fonction de sauvegarde de la cohorte.
		 *
		 * @param array $newdata
		 * @param array $data
		 * @return boolean
		 */
		public function saveCohorte( $newdata, $data ) {
			$success = true;
			$validationErrors = array( $this->alias => array() );
			foreach( $newdata as $i => $relance ) {
				switch( $data['Relance']['numrelance'] ) {
					case 1:
						if( $data['Relance']['contrat'] == 0 ) {
							$this->Nonrespectsanctionep93->Orientstruct->id = $relance['orientstruct_id'];
							$personne_id = $this->Nonrespectsanctionep93->Orientstruct->field( 'personne_id' );
							$days = Configure::read( 'Nonrespectsanctionep93.relanceOrientstructCer1' );
						}
						else {
							$days = Configure::read( 'Nonrespectsanctionep93.relanceCerCer1' );
							$this->Nonrespectsanctionep93->Contratinsertion->id = $relance['contratinsertion_id'];
							$personne_id = $this->Nonrespectsanctionep93->Contratinsertion->field( 'personne_id' );
						}

						$nbpassagespcd = $this->Nonrespectsanctionep93->Dossierep->find(
							'count',
							array(
								'conditions' => array(
									'Dossierep.actif' => '1',
									'Dossierep.personne_id' => $personne_id,
									'Dossierep.themeep' => 'nonrespectssanctionseps93',
								)
							)
						);

						if( $data['Relance']['contrat'] == 0 ) {
							$nbpassages = $this->Nonrespectsanctionep93->find(
								'count',
								array(
									'conditions' => array(
										'Nonrespectsanctionep93.orientstruct_id' => $relance['orientstruct_id'],
										'Nonrespectsanctionep93.origine' => 'orientstruct',
										'Nonrespectsanctionep93.sortieprocedure IS NULL',
										'Nonrespectsanctionep93.active' => '0',
									),
									'contain' => false
								)
							);

							$item = array(
								'Nonrespectsanctionep93' => array(
									'orientstruct_id' => $relance['orientstruct_id'],
									'origine' => 'orientstruct',
									'active' => 1,
									'rgpassage' => ( $nbpassages + 1 ),
								),
								'Relancenonrespectsanctionep93' => array(
									array(
										'numrelance' => $relance['numrelance'],
										'daterelance' => $relance['daterelance'],
										'dateimpression' => $relance['daterelance'],
										'user_id' => $relance['user_id']
									)
								)
							);
						}
						else {
							$nbpassages = $this->Nonrespectsanctionep93->find(
								'count',
								array(
									'conditions' => array(
										'Nonrespectsanctionep93.contratinsertion_id' => $relance['contratinsertion_id'],
										'Nonrespectsanctionep93.origine' => 'contratinsertion',
										'Nonrespectsanctionep93.sortieprocedure IS NULL',
										'Nonrespectsanctionep93.active' => '0',
									),
									'contain' => false
								)
							);

							$item = array(
								'Nonrespectsanctionep93' => array(
									'contratinsertion_id' => $relance['contratinsertion_id'],
									'origine' => 'contratinsertion',
									'active' => 1,
									'rgpassage' => ( $nbpassages + 1 ),
								),
								'Relancenonrespectsanctionep93' => array(
									array(
										'numrelance' => $relance['numrelance'],
										'daterelance' => $relance['daterelance'],
										'dateimpression' => $relance['daterelance'],
										'user_id' => $relance['user_id']
									)
								)
							);
						}

						$success = $this->Nonrespectsanctionep93->saveAll( $item, array( 'atomic' => false ) ) && $success;
						if( !empty( $this->Nonrespectsanctionep93->validationErrors ) ) {
							$validationErrors['Relancenonrespectsanctionep93'][$i] = $this->Nonrespectsanctionep93->validationErrors['Relancenonrespectsanctionep93'][0];
						}
						break;
					case 2:
						if( $data['Relance']['contrat'] == 0 ) {
							$days = Configure::read( 'Nonrespectsanctionep93.relanceOrientstructCer2' );
						}
						else {
							$days = Configure::read( 'Nonrespectsanctionep93.relanceCerCer2' );
						}

						$item = array(
							'Relancenonrespectsanctionep93' => array(
								'nonrespectsanctionep93_id' => $relance['nonrespectsanctionep93_id'],
								'numrelance' => $relance['numrelance'],
								'daterelance' => $relance['daterelance'],
								'dateimpression' => $relance['daterelance'],
								'user_id' => $relance['user_id']
							)
						);
						$this->create( $item );
						$success = $this->save( null, array( 'atomic' => false ) ) && $success;

						if( !empty( $this->validationErrors ) ) {
							$validationErrors[$this->alias][$i] = $this->validationErrors;
						}

						/// INFO: Modification après la suppression de la troisième relance
						if( $data['Relance']['contrat'] == 1 ) {
							$this->Nonrespectsanctionep93->Contratinsertion->id = $relance['contratinsertion_id'];
							$personne_id = $this->Nonrespectsanctionep93->Contratinsertion->field( 'personne_id' );
						}
						else {
							$this->Nonrespectsanctionep93->Orientstruct->id = $relance['orientstruct_id'];
							$personne_id = $this->Nonrespectsanctionep93->Orientstruct->field( 'personne_id' );
						}

						$dossierep = array(
							'Dossierep' => array(
								'personne_id' => $personne_id,
								'themeep' => 'nonrespectssanctionseps93',
							),
						);

						$this->Nonrespectsanctionep93->Dossierep->create( $dossierep );
						$success = $this->Nonrespectsanctionep93->Dossierep->save( null, array( 'atomic' => false ) ) && $success;

						// Nonrespectsanctionep93
						$nonrespectsanctionep93 = array(
							'Nonrespectsanctionep93' => array(
								'id' => $relance['nonrespectsanctionep93_id'],
								'dossierep_id' => $this->Nonrespectsanctionep93->Dossierep->id,
								'active' => 0,
							)
						);

						$this->Nonrespectsanctionep93->create( $nonrespectsanctionep93 );
						$success = $this->Nonrespectsanctionep93->save( null, array( 'atomic' => false ) ) && $success;
						break;
				}
			}

			$this->validationErrors = $validationErrors[$this->alias];

			return $success;
		}

		/**
		 * Conditions: que pour le premier passage (le second se fera via le shell)
		 *
		 * @param string $alias
		 * @return array
		 */
		protected function _conditionPremierPassage( $alias ) {
			$origine = Inflector::underscore( $alias );
			$foreignKey = "{$origine}_id";

			return array(
				'OR' => array(
					"{$alias}.id NOT IN (
						SELECT nonrespectssanctionseps93.{$foreignKey}
							FROM nonrespectssanctionseps93
							WHERE
								nonrespectssanctionseps93.origine = '{$origine}'
								AND nonrespectssanctionseps93.{$foreignKey} = {$alias}.id
								AND nonrespectssanctionseps93.rgpassage = '1'
								AND nonrespectssanctionseps93.active = '0'
					)",
					"{$alias}.id IN (
						SELECT nonrespectssanctionseps93.{$foreignKey}
							FROM nonrespectssanctionseps93
							WHERE
								nonrespectssanctionseps93.origine = '{$origine}'
								AND nonrespectssanctionseps93.{$foreignKey} = {$alias}.id
								AND nonrespectssanctionseps93.rgpassage = '1'
								AND nonrespectssanctionseps93.active = '1'
								AND nonrespectssanctionseps93.sortieprocedure IS NULL
					)"
				)
			);
		}

		/**
		 * Fonction de recherche des dossiers à relancer.
		 *
		 * @param array $mesCodesInsee
		 * @param boolean $filtre_zone_geo
		 * @param array $search
		 * @param mixed $lockedDossiers
		 * @return array
		 */
		public function search( $mesCodesInsee, $filtre_zone_geo, $search, $lockedDossiers ) {
			$limit = Configure::read( 'ResultatsParPage.nombre_par_defaut' );
			if (isset ($search['limit'])) {
				$limit = $search['limit'];
			}

			unset( $search['page'], $search['sort'], $search['direction'], $search['limit'] );

			$conditions = array();
			$joins = array();

			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );

			// Récupération des types d'orientation de type EMPLOI
			$typeOrientEmploi = implode(',', $this->Nonrespectsanctionep93->Orientstruct->Typeorient->listIdTypeOrient('EMPLOI'));

			// Personne orientée sans contrat
			// FIXME: dernière orientation
			// FIXME: et qui ne se trouve pas dans les EPs en cours de traitement
			// FIXME: sauvegarder le PDF

			// Champs de base
			$fields = array(
				'Personne.id',
				'Personne.nom',
				'Personne.prenom',
				'Personne.nir',
				'Personne.dtnai',
				'Dossier.id',
				'Dossier.matricule',
				'Adresse.nomcom',
				'Orientstruct.typeorient_id',
				$this->Nonrespectsanctionep93->Orientstruct->Personne->Foyer->sqVirtualField( 'enerreur' )
			);

			/// Jointures de base
			if( $search['Relance.contrat'] == 0 ) {
				$fields = array_merge($fields, array(
					'Orientstruct.id',
					'Orientstruct.propo_algo',
					'Orientstruct.valid_cg',
					'Orientstruct.date_propo',
					'Orientstruct.date_valid',
					'Orientstruct.statut_orient',
					'Orientstruct.date_impression',
					'Orientstruct.daterelance',
					'Orientstruct.statutrelance',
					'Orientstruct.date_impression_relance',
					'Orientstruct.etatorient',
					'Orientstruct.rgorient'
				) );
				$joins[] = array(
							'table'      => 'personnes',
							'alias'      => 'Personne',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Personne.id = Orientstruct.personne_id' )
						);
			}
			else {
				$fields = array_merge( $fields, array(
					'Contratinsertion.id',
					'Contratinsertion.df_ci'
				) );
				$joins[] = array(
					'table'      => 'personnes',
					'alias'      => 'Personne',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Personne.id = Contratinsertion.personne_id' )
				);
				//FIXME: voir si cela ne génère pas des doublons d'affichage d'orientation
				$joins[] = array(
					'table'      => 'orientsstructs',
					'alias'      => 'Orientstruct',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Orientstruct.personne_id = Personne.id' )
				);
			}
			$joins[] = array(
				'table'      => 'prestations',
				'alias'      => 'Prestation',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array(
					'Personne.id = Prestation.personne_id',
					'Prestation.natprest' => 'RSA',
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
				)
			);

			$joins[] = array(
				'table'      => 'calculsdroitsrsa',
				'alias'      => 'Calculdroitrsa',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array(
					'Personne.id = Calculdroitrsa.personne_id',
					'Calculdroitrsa.toppersdrodevorsa' => '1'
				)
			);

			$joins[] = array(
				'table'      => 'foyers',
				'alias'      => 'Foyer',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array( 'Personne.foyer_id = Foyer.id' )
			);

			$joins[] = array(
				'table'      => 'dossiers',
				'alias'      => 'Dossier',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array( 'Dossier.id = Foyer.dossier_id' )
			);

			$joins[] = array(
				'table'      => 'situationsdossiersrsa',
				'alias'      => 'Situationdossierrsa',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array(
					'Dossier.id = Situationdossierrsa.dossier_id',
					'Situationdossierrsa.etatdosrsa' => $this->Nonrespectsanctionep93->Orientstruct->Personne->Foyer->Dossier->Situationdossierrsa->etatOuvert()
				)
			);

			$joins[] = array(
				'table'      => 'adressesfoyers',
				'alias'      => 'Adressefoyer',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array(
					'Adressefoyer.foyer_id = Foyer.id',
					'Adressefoyer.rgadr' => '01'
				)
			);

			$joins[] = array(
				'table'      => 'adresses',
				'alias'      => 'Adresse',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array( 'Adressefoyer.adresse_id = Adresse.id' )
			);


			if( ( isset( $search['Dossiercaf.nomtitulaire'] ) && !empty( $search['Dossiercaf.nomtitulaire'] ) ) ||
				( isset( $search['Dossiercaf.prenomtitulaire'] ) && !empty( $search['Dossiercaf.prenomtitulaire'] ) ) ) {
				$joins[] = array(
					'table'      => 'dossierscaf',
					'alias'      => 'Dossiercaf',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						'Dossiercaf.personne_id = Personne.id',
						'Dossiercaf.toprespdos = true',
						'OR' => array(
							'Dossiercaf.dfratdos IS NULL',
							'Dossiercaf.dfratdos >= NOW()'
						),
						'Dossiercaf.ddratdos <= NOW()'
					)
				);
			}

			$search = Hash::expand( $search );


			$valeurtag_id = '';
			if (isset ($search['Tag']['valeurtag_id'])) {
				$valeurtag_id = $search['Tag']['valeurtag_id'];
			}
			$etat = '';
			if (isset ($search['Tag']['etat'])) {
				$etat = $search['Tag']['etat'];
			}
			$exclusionValeur = (isset ($search['Tag']['exclusionValeur']) && $search['Tag']['exclusionValeur']) ? true : false;
			$exclusionEtat = (isset ($search['Tag']['exclusionEtat']) && $search['Tag']['exclusionEtat']) ? true : false;
			$createdFrom =  null;
			$createdTo = null;
			if (isset ($search['Tag']['created']) && $search['Tag']['created'] === '1') {
				$createdFrom = isset ($search['Tag']['created_from']) ? $search['Tag']['created_from'] : null;
				$createdTo = isset ($search['Tag']['created_to']) ? $search['Tag']['created_to'] : null;
			}

			if (false === empty($valeurtag_id) || false === empty($etat) || false === is_null($createdFrom)) {
				$conditions[] = ClassRegistry::init('Tag')->sqHasTagValue($valeurtag_id, '"Foyer"."id"', '"Personne"."id"', $etat, $exclusionValeur, $exclusionEtat, $createdFrom, $createdTo);
			}

			unset($search['ByTag']);
			unset($search['Tag']);

			$search = Hash::flatten( $search );

			$paths = array(
				'Orientstruct.origine',
				'Orientstruct.typeorient_id',
				'Orientstruct.statut_orient',
				'Orientstruct.serviceinstructeur_id',
			);

			// Fils de dependantSelect
			$pathsToExplode = array(
				'Orientstruct.structurereferente_id',
			);

			$pathsOrient = array_merge(
				[
					'Orientstruct.dernierevalid',
					'Orientstruct.derniere',
					'Orientstruct.date_valid_from.day',
					'Orientstruct.date_valid_from.month',
					'Orientstruct.date_valid_from.year',
					'Orientstruct.date_valid_to.day',
					'Orientstruct.date_valid_to.month',
					'Orientstruct.date_valid_to.year',
					'Orientstruct.date_valid',
					'Orientstruct.communautesr_id'
				],
				$paths,
				$pathsToExplode
			);

			if(isset($search['Orientstruct.derniere']) && $search['Orientstruct.derniere']) {
				$conditions[] = array(
					"Orientstruct.id IN (SELECT
						orientsstructs.id
					FROM
						orientsstructs
						WHERE
							orientsstructs.personne_id = Orientstruct.personne_id
						ORDER BY
							orientsstructs.id DESC
						LIMIT 1)"
				);
			}

			if(isset($search['Orientstruct.dernierevalid']) && $search['Orientstruct.dernierevalid']) {
				$conditions[] = array(
					"Orientstruct.id IN (SELECT
						orientsstructs.id
					FROM
						orientsstructs
						WHERE
							orientsstructs.personne_id = Orientstruct.personne_id
							AND orientsstructs.statut_orient = 'Orienté'
						ORDER BY
							orientsstructs.id DESC
						LIMIT 1)"
				);
			}

			if( isset( $search['Orientstruct.date_valid'] ) && $search['Orientstruct.date_valid'] ) {
				$from = $search['Orientstruct.date_valid_from.year'].$search['Orientstruct.date_valid_from.month'].$search['Orientstruct.date_valid_from.day'];
				$to = $search['Orientstruct.date_valid_to.year'].$search['Orientstruct.date_valid_to.month'].$search['Orientstruct.date_valid_to.day'];

				$conditions[] = "Orientstruct.date_valid  BETWEEN '{$from}' AND '{$to}'";
			}

			foreach( $paths as $path ) {
				$value = isset($search[$path]) ? suffix($search[$path]) : null;
				if( $value !== null && $value !== '' ) {
					$conditions[$path] = $value;
				}
			}

			foreach( $pathsToExplode as $path ) {
				$value = isset($search[$path]) ? $search[$path] : null;
				if( $value !== null && $value !== '' && strpos($value, '_') > 0 ) {
					list(,$value) = explode('_', $value);
					$conditions[$path] = $value;
				}
			}

			// FIXME: jointures (Dossier)
			foreach( $search as $field => $condition ) {
				if( in_array( $field, array( 'Personne.nom', 'Personne.prenom' ) ) ) {
					$conditions["UPPER({$field}) LIKE"] = $this->wildcard( strtoupper( replace_accents( $condition ) ) );
				}
				else if($field == 'Personne.trancheage'){
					list( $ageMin, $ageMax ) = explode( '_', $condition );
						$conditions[] = '( EXTRACT ( YEAR FROM AGE( Personne.dtnai ) ) ) BETWEEN '.$ageMin.' AND '.$ageMax;
				} else if($field == 'Personne.trancheagesup'){
					$conditions[] = '( EXTRACT ( YEAR FROM AGE( Personne.dtnai ) ) ) >= '.$condition;
				} else if($field == 'Personne.trancheageprec'){
					$conditions[] = '( EXTRACT ( YEAR FROM AGE( Personne.dtnai ) ) ) <= '.$condition;
				}
				else if( $field == 'Adresse.numcom' && !empty( $condition ) ) {
					$conditions[] = array( 'Adresse.numcom' => $condition );
				}
				else if( $field == 'Serviceinstructeur.id' && !empty( $condition ) ) {
					$joins[] = array(
							'table'      => 'suivisinstruction',
							'alias'      => 'Suiviinstruction',
							'type'       => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array(
								'Suiviinstruction.dossier_id = Dossier.id',
								'Suiviinstruction.id IN (
									'.ClassRegistry::init( 'Suiviinstruction' )->sqDerniere('Suiviinstruction.dossier_id').'
								)'
							)
						);
					$joins[] = array(
							'table'      => 'servicesinstructeurs',
							'alias'      => 'Serviceinstructeur',
							'type'       => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array(
								'Suiviinstruction.numdepins = Serviceinstructeur.numdepins',
								'Suiviinstruction.typeserins = Serviceinstructeur.typeserins',
								'Suiviinstruction.numcomins = Serviceinstructeur.numcomins',
								'Suiviinstruction.numagrins = Serviceinstructeur.numagrins'
							)
						);
					$conditions[] = array( 'Serviceinstructeur.id' => $condition );
				}
				else if( $field == 'Dossier.matricule' && !empty( $condition ) ) {
					$conditions[] = array( 'Dossier.matricule LIKE' => $this->wildcard( "*{$condition}*" ) );
				}
				else if( ( $field == 'Dossiercaf.nomtitulaire' || $field == 'Dossiercaf.prenomtitulaire' ) && !empty( $condition ) ) {
					$field = preg_replace( '/^Dossiercaf\.(.*)titulaire$/', '\1', $field );
					$conditions["UPPER({$field}) LIKE"] = $this->wildcard( strtoupper( replace_accents( $condition ) ) );
				}
				else if( !in_array( $field, array_merge(array( 'Relance.numrelance', 'Relance.contrat', 'Relance.compare0', 'Relance.compare1', 'Relance.nbjours0', 'Relance.nbjours1', 'PersonneReferent.referent_id', 'PersonneReferent.structurereferente_id' ), $pathsOrient )) ) {
					$conditions[$field] = $condition;
				}
			}

			$conditions = $this->conditionCommunautesr(
				$conditions,
				Hash::expand($search),
				array( 'Orientstruct.communautesr_id' => 'Orientstruct.structurereferente_id' )
			);

			/// Dossiers lockés
			if( !empty( $lockedDossiers ) ) {
				if( is_array( $lockedDossiers ) ) {
					$conditions[] = 'Dossier.id NOT IN ( '.implode( ', ', $lockedDossiers ).' )';
				}
				$conditions[] = "NOT {$lockedDossiers}";
			}

			if ( $search['Relance.numrelance'] > 1 ) {
				if( $search['Relance.contrat'] == 0 ) {
					$joins[] = array(
								'table'      => 'nonrespectssanctionseps93',
								'alias'      => 'Nonrespectsanctionep93',
								'type'       => 'LEFT OUTER',
								'foreignKey' => false,
								'conditions' => array(
									'Orientstruct.id = Nonrespectsanctionep93.orientstruct_id'
									// Sous requête pour avoir le Nonrespectsanctionep93 le plus récent
								),
							);
				}
				else {
					$joins[] = array(
								'table'      => 'nonrespectssanctionseps93',
								'alias'      => 'Nonrespectsanctionep93',
								'type'       => 'LEFT OUTER',
								'foreignKey' => false,
								'conditions' => array(
									'Contratinsertion.id = Nonrespectsanctionep93.contratinsertion_id'
									// Sous requête pour avoir le Nonrespectsanctionep93 le plus récent
								),
							);
				}
				$joins[] = array(
							'table'      => 'relancesnonrespectssanctionseps93',
							'alias'      => 'Relancenonrespectsanctionep93',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Nonrespectsanctionep93.id = Relancenonrespectsanctionep93.nonrespectsanctionep93_id',
								// On ne fait la jointure que sur la dernière relance
								'Relancenonrespectsanctionep93.id IN (
									SELECT relancesnonrespectssanctionseps93.id
										FROM relancesnonrespectssanctionseps93
										WHERE relancesnonrespectssanctionseps93.nonrespectsanctionep93_id = Nonrespectsanctionep93.id
										ORDER BY relancesnonrespectssanctionseps93.numrelance DESC
										LIMIT 1
								)',
								'Relancenonrespectsanctionep93.numrelance' => ( $search['Relance.numrelance']-1 )
							),
						);
				$fieldssup = array(
					'Nonrespectsanctionep93.id',
					'Relancenonrespectsanctionep93.daterelance',
				);
				$fields = array_merge($fields, $fieldssup);
			}

			// Relances pour personnes sans contrat
			/// FIXME: que les dernières orientations / les derniers contrats
			/// Exemple: 1ère relance de /orientsstructs/index/351610
			if( $search['Relance.contrat'] == 0 ) {
				$conditions['Orientstruct.statut_orient'] = 'Orienté';

				// Dernière Orientstruct de la personne
				$conditions[] = 'Orientstruct.id IN (
					SELECT orientsstructs.id
						FROM orientsstructs
						WHERE
							orientsstructs.date_valid IS NOT NULL
							AND orientsstructs.personne_id = Personne.id
						ORDER by orientsstructs.date_valid DESC
						LIMIT 1
				)';



				//Ajout suite au bug #5293 du 12/07/2011
				// Toutes les orientations sauf emploi
				$conditions[] = 'Orientstruct.typeorient_id NOT IN (
							SELECT t.id
								FROM typesorients AS t
								WHERE t.id in ('.$typeOrientEmploi.')
						)';

				// On accepte les orientations validées durant le CER, si c'est pour la même structure référente
				$conditions[] = 'Orientstruct.personne_id NOT IN (
									SELECT contratsinsertion.personne_id
										FROM contratsinsertion
										WHERE
											contratsinsertion.personne_id = Orientstruct.personne_id
											AND (
												date_trunc( \'day\', contratsinsertion.datevalidation_ci ) >= Orientstruct.date_valid
												OR (
													date_trunc( \'day\', "contratsinsertion"."dd_ci" ) <= "Orientstruct"."date_valid"
													AND date_trunc( \'day\', "contratsinsertion"."df_ci" ) >= "Orientstruct"."date_valid"
													AND "contratsinsertion"."structurereferente_id" = "Orientstruct"."structurereferente_id"
												)
											)
										)';
				$conditions[] = "Orientstruct.date_impression <= DATE( NOW() )";
				if( !empty( $search['Relance.compare0'] ) && !empty( $search['Relance.nbjours0'] ) ) {
					$conditions[] = "( DATE( NOW() ) - Orientstruct.date_impression ) {$search['Relance.compare0']} {$search['Relance.nbjours0']}";
				}

				switch( $search['Relance.numrelance'] ) {
					case 1:
						$conditions[] = "( DATE( NOW() ) - Orientstruct.date_impression ) >= ".Configure::read( 'Nonrespectsanctionep93.relanceOrientstructCer1' );

						// Il n'existe pas de dossier d'EP en cours pour cette même thématique.
						$conditions[] = 'Orientstruct.id NOT IN (
							SELECT nonrespectssanctionseps93.orientstruct_id
								FROM nonrespectssanctionseps93
								WHERE
									nonrespectssanctionseps93.active = \'1\'
									AND nonrespectssanctionseps93.dossierep_id IS NULL
									AND nonrespectssanctionseps93.orientstruct_id = Orientstruct.id
						)';

						// Il n'existe pas de dossier d'EP finalisé depuis moins de XXX jours pour cette même thématique.
						$conditions[] = 'Orientstruct.id NOT IN (
							SELECT nonrespectssanctionseps93.orientstruct_id
								FROM nonrespectssanctionseps93
									INNER JOIN dossierseps ON (
										nonrespectssanctionseps93.dossierep_id = dossierseps.id
									)
								WHERE
									nonrespectssanctionseps93.active = \'0\'
									AND nonrespectssanctionseps93.orientstruct_id = Orientstruct.id
									AND dossierseps.id NOT IN ( '.$this->Nonrespectsanctionep93->Dossierep->Passagecommissionep->sq(
										// Les états traite et annule étant des états finaux, on est certains
										// qu'il s'agit du dernier passage en commission pour ces dossiers
										array(
											'alias' => 'passagescommissionseps',
											'fields' => array(
												'passagescommissionseps.dossierep_id'
											),
											'conditions' => array(
												'passagescommissionseps.dossierep_id = dossierseps.id',
												'passagescommissionseps.etatdossierep' => array( 'traite', 'annule' )
											),
										)
									).' )
									AND ( DATE( NOW() ) - (
										SELECT "decisionsnonrespectssanctionseps93"."modified"::DATE
											FROM decisionsnonrespectssanctionseps93
												INNER JOIN passagescommissionseps ON (
													decisionsnonrespectssanctionseps93.passagecommissionep_id = passagescommissionseps.id
												)
												INNER JOIN dossierseps ON (
													nonrespectssanctionseps93.dossierep_id = dossierseps.id
												)
											ORDER BY modified DESC
											LIMIT 1
									) ) <= '.Configure::read( 'Nonrespectsanctionep93.relanceDecisionNonRespectSanctions' ).'
							)';
						break;
					case 2:
						$conditions[] = 'Orientstruct.id IN (
							SELECT nonrespectssanctionseps93.orientstruct_id
								FROM nonrespectssanctionseps93
								WHERE
									nonrespectssanctionseps93.active = \'1\'
									AND nonrespectssanctionseps93.dossierep_id IS NULL
									AND nonrespectssanctionseps93.orientstruct_id = Orientstruct.id
									AND (
										SELECT
												relancesnonrespectssanctionseps93.numrelance
												FROM relancesnonrespectssanctionseps93
												WHERE
													relancesnonrespectssanctionseps93.nonrespectsanctionep93_id = nonrespectssanctionseps93.id
													AND ( DATE( NOW() ) - relancesnonrespectssanctionseps93.daterelance ) >= '.Configure::read( "Nonrespectsanctionep93.relanceOrientstructCer{$search['Relance.numrelance']}" ).'
												ORDER BY relancesnonrespectssanctionseps93.numrelance DESC
												LIMIT 1
									) = '.( $search['Relance.numrelance'] - 1 ).'
						)';
						break;
				}

				// Conditions: que pour le premier passage (le second se fera via le shell)
				$conditions[] = $this->_conditionPremierPassage( 'Orientstruct' );

				$queryData = array(
					'fields' => $fields,
					'conditions' => $conditions,
					'joins' => $joins,
					'contain' => false,
					'limit' => $limit,
					'order' => 'Orientstruct.date_impression ASC',
				);

				$queryData = ClassRegistry::init( 'PersonneReferent' )->completeQdReferentParcours( $queryData, Hash::expand( $search ) );

				return $queryData;
			}
			else {
				// Dernièr contrat
				$conditions[] = '( DATE( NOW() ) - Contratinsertion.df_ci ) >= '.Configure::read( 'Nonrespectsanctionep93.relanceCerCer'.$search['Relance.numrelance'] );
				$conditions[] = 'Contratinsertion.df_ci <= DATE( NOW() )';
				if( !empty( $search['Relance.compare1'] ) && !empty( $search['Relance.nbjours1'] ) ) {
					$conditions[] = '( DATE( NOW() ) - Contratinsertion.df_ci ) '.$search['Relance.compare1'].' '.$search['Relance.nbjours1'];
				}

				//Ajout suite au bug #5293 du 12/07/2011
				// Toutes les orientations sauf emploi
				$conditions[] = 'Orientstruct.typeorient_id NOT IN (
							SELECT t.id
								FROM typesorients AS t
								WHERE t.id in ('.$typeOrientEmploi.')
						)';

				// Qui ne possède pas d'orientation validée plus récente que la date de début du CER
				$conditions[] = 'NOT EXISTS (
					SELECT orientsstructs.id
						FROM orientsstructs
						WHERE
							orientsstructs.personne_id = Contratinsertion.personne_id
							AND orientsstructs.statut_orient = \'Orienté\'
							AND orientsstructs.date_valid > Contratinsertion.dd_ci
				)';

				switch( $search['Relance.numrelance'] ) {
					case 1:
						//Le dernier contrat en cours
						$conditions[] = 'Contratinsertion.id IN (
							SELECT contratsinsertion.id
								FROM contratsinsertion
								WHERE
									contratsinsertion.datevalidation_ci IS NOT NULL
									AND contratsinsertion.personne_id = Personne.id
								ORDER by contratsinsertion.df_ci DESC
								LIMIT 1
						)';

						// Il n'existe pas de dossier d'EP en cours pour cette même thématique.
						$conditions[] = 'Contratinsertion.id NOT IN (
							SELECT nonrespectssanctionseps93.contratinsertion_id
								FROM nonrespectssanctionseps93
								WHERE
									nonrespectssanctionseps93.active = \'1\'
									AND nonrespectssanctionseps93.dossierep_id IS NULL
									AND nonrespectssanctionseps93.contratinsertion_id = Contratinsertion.id
						)';

						// Il n'existe pas de dossier d'EP finalisé depuis moins de XXX jours pour cette même thématique.
						$conditions[] = 'Contratinsertion.id NOT IN (
							SELECT nonrespectssanctionseps93.contratinsertion_id
								FROM nonrespectssanctionseps93
									INNER JOIN dossierseps ON (
										nonrespectssanctionseps93.dossierep_id = dossierseps.id
									)
								WHERE
									nonrespectssanctionseps93.active = \'0\'
									AND nonrespectssanctionseps93.contratinsertion_id = Contratinsertion.id
									AND dossierseps.id NOT IN ( '.$this->Nonrespectsanctionep93->Dossierep->Passagecommissionep->sq(
										// Les états traite et annule étant des états finaux, on est certains
										// qu'il s'agit du dernier passage en commission pour ces dossiers
										array(
											'alias' => 'passagescommissionseps',
											'fields' => array(
												'passagescommissionseps.dossierep_id'
											),
											'conditions' => array(
												'passagescommissionseps.dossierep_id = dossierseps.id',
												'passagescommissionseps.etatdossierep' => array( 'traite', 'annule' )
											),
										)
									).' )
									AND ( DATE( NOW() ) - (
										SELECT "decisionsnonrespectssanctionseps93"."modified"::DATE
											FROM decisionsnonrespectssanctionseps93
												INNER JOIN passagescommissionseps ON (
													decisionsnonrespectssanctionseps93.passagecommissionep_id = passagescommissionseps.id
												)
												INNER JOIN dossierseps ON (
													nonrespectssanctionseps93.dossierep_id = dossierseps.id
												)
											ORDER BY modified DESC
											LIMIT 1
									) ) <= '.Configure::read( 'Nonrespectsanctionep93.relanceDecisionNonRespectSanctions' ).'
							)';
						break;
					case 2:
						$conditions[] = 'Contratinsertion.id IN (
							SELECT nonrespectssanctionseps93.contratinsertion_id
								FROM nonrespectssanctionseps93
								WHERE
									nonrespectssanctionseps93.active = \'1\'
									AND nonrespectssanctionseps93.dossierep_id IS NULL
									AND nonrespectssanctionseps93.contratinsertion_id = Contratinsertion.id
									AND (
										SELECT
												relancesnonrespectssanctionseps93.numrelance
												FROM relancesnonrespectssanctionseps93
												WHERE
													relancesnonrespectssanctionseps93.nonrespectsanctionep93_id = nonrespectssanctionseps93.id
												ORDER BY relancesnonrespectssanctionseps93.numrelance DESC
												LIMIT 1
									) = '.( $search['Relance.numrelance'] - 1 ).'
						)';
						break;
				}

				// Conditions: que pour le premier passage (le second se fera via le shell)
				$conditions[] = $this->_conditionPremierPassage( 'Contratinsertion' );

				$queryData = array(
					'fields' => $fields,
					'conditions' => $conditions,
					'joins' => $joins,
					'contain' => false,
					'limit' => $limit,
					'order' => array( 'Contratinsertion.df_ci ASC' ),
				);

				$queryData = ClassRegistry::init( 'PersonneReferent' )->completeQdReferentParcours( $queryData, Hash::expand( $search ) );

				return $queryData;
			}
		}

		/**
		 * Fonction de recherche des dossiers déjà relancés.
		 *
		 * @param array $mesCodesInsee
		 * @param boolean $filtre_zone_geo
		 * @param array $search
		 * @return array
		 */
		public function qdSearchRelances( $mesCodesInsee, $filtre_zone_geo, $search ) {
			$conditions = array();

			$valeurtag_id = '';
			if (isset ($search['Search']['Tag']['valeurtag_id'])) {
				$valeurtag_id = $search['Search']['Tag']['valeurtag_id'];
			}
			$etat = '';
			if (isset ($search['Search']['Tag']['etat'])) {
				$etat = $search['Search']['Tag']['etat'];
			}
			$exclusionValeur = (isset ($search['Search']['Tag']['exclusionValeur']) && $search['Search']['Tag']['exclusionValeur']) ? true : false;
			$exclusionEtat = (isset ($search['Search']['Tag']['exclusionEtat']) && $search['Search']['Tag']['exclusionEtat']) ? true : false;
			$createdFrom =  null;
			$createdTo = null;
			if (isset ($search['Search']['Tag']['created']) && $search['Search']['Tag']['created'] === '1') {
				$createdFrom = isset ($search['Search']['Tag']['created_from']) ? $search['Search']['Tag']['created_from'] : null;
				$createdTo = isset ($search['Search']['Tag']['created_to']) ? $search['Search']['Tag']['created_to'] : null;
			}

			if (false === empty($valeurtag_id) || false === empty($etat) || false === is_null($createdFrom)) {
				$conditions[] = ClassRegistry::init('Tag')->sqHasTagValue($valeurtag_id, '"Foyer"."id"', '"Personne"."id"', $etat, $exclusionValeur, $exclusionEtat, $createdFrom, $createdTo);
			}

			$search = Hash::flatten( $search );
			$search = Hash::filter( (array)$search );


			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );


			$paths = array(
				'Orientstruct.origine',
				'Orientstruct.typeorient_id',
				'Orientstruct.statut_orient',
				'Orientstruct.serviceinstructeur_id',
			);

			// Fils de dependantSelect
			$pathsToExplode = array(
				'Orientstruct.structurereferente_id',
			);

			$pathsOrient = array_merge(
				[
					'Orientstruct.dernierevalid',
					'Orientstruct.derniere',
					'Orientstruct.date_valid_from.day',
					'Orientstruct.date_valid_from.month',
					'Orientstruct.date_valid_from.year',
					'Orientstruct.date_valid_to.day',
					'Orientstruct.date_valid_to.month',
					'Orientstruct.date_valid_to.year',
					'Orientstruct.date_valid',
					'Orientstruct.communautesr_id'
				],
				$paths,
				$pathsToExplode
			);

			if($search['Orientstruct.derniere'] ) {
				$conditions[] = array(
					"Orientstruct.id IN (SELECT
						orientsstructs.id
					FROM
						orientsstructs
						WHERE
							orientsstructs.personne_id = Orientstruct.personne_id
						ORDER BY
							orientsstructs.id DESC
						LIMIT 1)"
				);
			}

			if($search['Orientstruct.dernierevalid']) {
				$conditions[] = array(
					"Orientstruct.id IN (SELECT
						orientsstructs.id
					FROM
						orientsstructs
						WHERE
							orientsstructs.personne_id = Orientstruct.personne_id
							AND orientsstructs.statut_orient = 'Orienté'
						ORDER BY
							orientsstructs.id DESC
						LIMIT 1)"
				);
			}

			if( isset( $search['Orientstruct.date_valid'] ) && $search['Orientstruct.date_valid'] ) {
				$from = $search['Orientstruct.date_valid_from.year'].$search['Orientstruct.date_valid_from.month'].$search['Orientstruct.date_valid_from.day'];
				$to = $search['Orientstruct.date_valid_to.year'].$search['Orientstruct.date_valid_to.month'].$search['Orientstruct.date_valid_to.day'];

				$conditions[] = "Orientstruct.date_valid  BETWEEN '{$from}' AND '{$to}'";
			}

			foreach( $paths as $path ) {
				$value = isset($search[$path]) ? suffix($search[$path]) : null;
				if( $value !== null && $value !== '' ) {
					$conditions[$path] = $value;
				}
			}

			foreach( $pathsToExplode as $path ) {
				$value = isset($search[$path]) ? $search[$path] : null;
				if( $value !== null && $value !== '' && strpos($value, '_') > 0 ) {
					list(,$value) = explode('_', $value);
					$conditions[$path] = $value;
				}
			}

			foreach( $search as $field => $condition ) {
				if( in_array( $field, array( 'Personne.nom', 'Personne.prenom', 'Personne.nomnai' ) ) ) {
					$conditions["UPPER({$field}) LIKE"] = $this->wildcard( strtoupper( replace_accents( $condition ) ) );
				}
				else if($field == 'Personne.trancheage'){
					list( $ageMin, $ageMax ) = explode( '_', $condition );
						$conditions[] = '( EXTRACT ( YEAR FROM AGE( Personne.dtnai ) ) ) BETWEEN '.$ageMin.' AND '.$ageMax;
				} else if($field == 'Personne.trancheagesup'){
					$conditions[] = '( EXTRACT ( YEAR FROM AGE( Personne.dtnai ) ) ) >= '.$condition;
				} else if($field == 'Personne.trancheageprec'){
					$conditions[] = '( EXTRACT ( YEAR FROM AGE( Personne.dtnai ) ) ) <= '.$condition;
				}
				else if( $field == 'Adresse.numcom' && !empty( $condition ) ) {
					$conditions['Adresse.numcom'] = $condition;
				}
				else if( $field == 'Serviceinstructeur.id' && !empty( $condition ) ) {
					$conditions['Serviceinstructeur.id'] = $condition;
				}
				else if( $field == 'Dossier.matricule' && !empty( $condition ) ) {
					$conditions['Dossier.matricule LIKE'] = $this->wildcard( "*{$condition}*" );
				}
				else if( ( $field == 'Dossiercaf.nomtitulaire' || $field == 'Dossiercaf.prenomtitulaire' ) && !empty( $condition ) ) {
					$field = preg_replace( '/^Dossiercaf\.(.*)titulaire$/', '\1', $field );
					$conditions["UPPER({$field}) LIKE"] = $this->wildcard( strtoupper( replace_accents( $condition ) ) );
				}
				else if( $field == 'Nonrespectsanctionep93.origine' && !empty( $condition ) ) {
					$conditions['Nonrespectsanctionep93.origine'] = $condition;
				}
				else if( $field == 'Relancenonrespectsanctionep93.daterelance' && !empty( $condition ) ) {
					$daterelance_from = "{$search['Relancenonrespectsanctionep93.daterelance_from.year']}-{$search['Relancenonrespectsanctionep93.daterelance_from.month']}-{$search['Relancenonrespectsanctionep93.daterelance_from.day']}";
					$daterelance_to = "{$search['Relancenonrespectsanctionep93.daterelance_to.year']}-{$search['Relancenonrespectsanctionep93.daterelance_to.month']}-{$search['Relancenonrespectsanctionep93.daterelance_to.day']}";

					$conditions[] = "Relancenonrespectsanctionep93.daterelance BETWEEN '{$daterelance_from}' AND '{$daterelance_to}'";
				}
				else if( !in_array( $field, array_merge(array( 'sort', 'page', 'direction', 'Relance.daterelance' ), $pathsOrient) ) && !preg_match( '/^Relancenonrespectsanctionep93\.daterelance.*$/', $field ) && !preg_match( '/^Search\./', $field ) ) {
					$conditions[$field] = $condition;
				}
			}

			$conditions = $this->conditionCommunautesr(
				$conditions,
				Hash::expand($search)['Search'],
				array( 'Orientstruct.communautesr_id' => 'Orientstruct.structurereferente_id' )
			);


			$joins = array(
				array(
					'table'      => 'nonrespectssanctionseps93',
					'alias'      => 'Nonrespectsanctionep93',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Relancenonrespectsanctionep93.nonrespectsanctionep93_id = Nonrespectsanctionep93.id' )
				),
				array(
					'table'      => 'orientsstructs',
					'alias'      => 'Orientstruct',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array( 'Orientstruct.id = Nonrespectsanctionep93.orientstruct_id' )
				),
				array(
					'table'      => 'contratsinsertion',
					'alias'      => 'Contratinsertion',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array( 'Contratinsertion.id = Nonrespectsanctionep93.contratinsertion_id' )
				),
				array(
					'table'      => 'personnes',
					'alias'      => 'Personne',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						'OR' => array(
							'Personne.id = Orientstruct.personne_id',
							'Personne.id = Contratinsertion.personne_id'
						)
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
					'conditions' => array( 'Dossier.id = Foyer.dossier_id' )
				),
				array(
					'table'      => 'adressesfoyers',
					'alias'      => 'Adressefoyer',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						'Foyer.id = Adressefoyer.foyer_id',
						'Adressefoyer.rgadr' => '01'
					)
				),
				array(
					'table'      => 'adresses',
					'alias'      => 'Adresse',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Adresse.id = Adressefoyer.adresse_id' )
				),
				array(
					'table'      => 'dossierseps',
					'alias'      => 'Dossierep',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array( 'Dossierep.id = Nonrespectsanctionep93.dossierep_id' )
				),
				array(
					'table'      => 'suivisinstruction',
					'alias'      => 'Suiviinstruction',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						'Suiviinstruction.dossier_id = Dossier.id',
						'Suiviinstruction.id IN (
							'.ClassRegistry::init( 'Suiviinstruction' )->sqDerniere('Suiviinstruction.dossier_id').'
						)'
					)
				),
				array(
					'table'      => 'servicesinstructeurs',
					'alias'      => 'Serviceinstructeur',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						'Suiviinstruction.numdepins = Serviceinstructeur.numdepins',
						'Suiviinstruction.typeserins = Serviceinstructeur.typeserins',
						'Suiviinstruction.numcomins = Serviceinstructeur.numcomins',
						'Suiviinstruction.numagrins = Serviceinstructeur.numagrins'
					)
				),
				array(
					'table'      => 'pdfs',
					'alias'      => 'Pdf',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						'Pdf.modele' => $this->alias,
						'Pdf.fk_value = Relancenonrespectsanctionep93.id'
					)
				),
			);

			$joins[] = array(
				'table'      => 'dossierscaf',
				'alias'      => 'Dossiercaf',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array(
					'Dossiercaf.personne_id = Personne.id',
					//'Dossiercaf.toprespdos = true',
					'OR' => array(
						'Dossiercaf.dfratdos IS NULL',
						'Dossiercaf.dfratdos >= NOW()'
					),
					'Dossiercaf.ddratdos <= NOW()'
				)
			);

			$joins[] = array(
				'table'      => 'passagescommissionseps',
				'alias'      => 'Passagecommissionep',
				'type'       => 'LEFT OUTER',
				'foreignKey' => false,
				'conditions' => array(
					'Passagecommissionep.dossierep_id = Dossierep.id',
				),
				'order' => array( 'Passagecommissionep.created DESC' ), // FIXME
				'limit' => 1
			);

			$queryData = array(
				'fields' => array(
					'Dossier.matricule',
					'Adresse.nomcom',
					$this->Nonrespectsanctionep93->Orientstruct->Personne->Foyer->sqVirtualField( 'enerreur' ),
					'Personne.id',
					'Personne.nom',
					'Personne.prenom',
					'Personne.nir',
					'Nonrespectsanctionep93.origine',
					'Orientstruct.date_impression',
					$this->Nonrespectsanctionep93->Orientstruct->sqVirtualField( 'nbjours' ),
//                    'Orientstruct.nbjours',
					'Contratinsertion.id',
					'Contratinsertion.df_ci',
                    $this->Nonrespectsanctionep93->Orientstruct->Personne->Contratinsertion->sqVirtualField( 'nbjours' ),
//					'Contratinsertion.nbjours',
					'Contratinsertion.datevalidation_ci',
					'Dossierep.id',
					'Passagecommissionep.etatdossierep',
					'Relancenonrespectsanctionep93.id',
					'Relancenonrespectsanctionep93.daterelance',
					'Relancenonrespectsanctionep93.numrelance',
					'Pdf.id',
				),
				'joins' => $joins,
				'conditions' => $conditions,
				'contain' => false
			);

			$queryData = ClassRegistry::init( 'PersonneReferent' )->completeQdReferentParcours( $queryData, (array)Hash::get( Hash::expand( $search ), 'Search' ) );

			return $queryData;
		}

		/**
		 *
		 * @param array $datas
		 * @return boolean
		 */
	   public function checkCompareError( $datas ) {
			$searchError = false;
			if( $datas['Relance']['contrat'] == 0 ) {
				if ( ( @$datas['Relance']['compare0'] == '<' && @$datas['Relance']['nbjours0'] <= Configure::read( 'Nonrespectsanctionep93.relanceOrientstructCer'.$datas['Relance']['numrelance'] ) ) || ( @$datas['Relance']['compare0'] == '<=' && @$datas['Relance']['nbjours0'] < Configure::read( 'Nonrespectsanctionep93.relanceOrientstructCer'.$datas['Relance']['numrelance'] ) ) )
					$searchError = true;
			}
			else {
				if ( ( @$datas['Relance']['compare1'] == '<' && @$datas['Relance']['nbjours1'] <= Configure::read( 'Nonrespectsanctionep93.relanceCerCer'.$datas['Relance']['numrelance'] ) ) || ( @$datas['Relance']['compare1'] == '<=' && @$datas['Relance']['nbjours1'] < Configure::read( 'Nonrespectsanctionep93.relanceCerCer'.$datas['Relance']['numrelance'] ) ) )
					$searchError = true;
			}
			return $searchError;
		}

		/**
		* Retourne un array de chaînes de caractères indiquant pourquoi on ne
		* peut pas créer de relance pour cette personne.
		*
		* Les valeurs possibles sont:
		*	- 1°) Par-rapport à la possibilité de créer un dossier d'EP:
		* 		* Personne.id: la personne n'existe pas en base ou n'a pas de prestation RSA
		* 		* Situationdossierrsa.etatdosrsa: le dossier ne se trouve pas dans un état ouvert
		* 		* Prestation.rolepers: la personne n'est ni demandeur ni conjoint RSA
		* 		* Calculdroitrsa.toppersdrodevorsa: la personne n'est pas soumise à droits et devoirs
		*	- 2°)
		* 		* Dossierep.id: il existe déjà un dossier d'EP non finalisé pour "Demande de suspension"
		* 		* Dossierep.datedecision: il existe déjà un dossier d'EP dont la date de décision est trop récente
		*		* Contratinsertion.df_ci: la date de fin du contrat d'insertion est égale ou supérieure à la date du jour
		*		* Contratinsertion.Orientstruct: la personne ne possède ni orientation validée et éditée, ni contrat validé
		*		* Nonrespectsanctionep93.relanceCerCer1: le délai pour la relance n° 1 pour non recontratctualisation n'est pas encore dépassé
		*		* Nonrespectsanctionep93.relanceCerCer2: le délai pour la relance n° 2 pour non recontratctualisation n'est pas encore dépassé
		*		* Nonrespectsanctionep93.relanceOrientstructCer1: le délai pour la relance n° 1 pour non contratctualisation n'est pas encore dépassé
		*		* Nonrespectsanctionep93.relanceOrientstructCer2: le délai pour la relance n° 2 pour non contratctualisation n'est pas encore dépassé
		*		* Nonrespectsanctionep93.relanceOrientstructCer3: le délai pour la relance n° 3 pour non contratctualisation n'est pas encore dépassé
		*
		* @param integer $personne_id L'id technique de la personne
		* @return array
		* @access public
		*/
		public function erreursPossibiliteAjout( $personne_id ) {
			$erreurs = $this->Nonrespectsanctionep93->Dossierep->getErreursCandidatePassage( $personne_id );

			if( empty( $erreurs ) ) {
				// 0.1°) Il n'existe pas de dossier d'EP en cours pour la thématique "Demande de suspension"
				$count = $this->Nonrespectsanctionep93->Dossierep->find(
					'count',
					array(
						'conditions' => array(
							'Dossierep.actif' => '1',
							'Dossierep.personne_id' => $personne_id,
							//'Dossierep.etapedossierep <>' => 'traite',
							'Dossierep.id NOT IN ( '.$this->Nonrespectsanctionep93->Dossierep->Passagecommissionep->sq(
								// Les états traite et annule étant des états finaux, on est certains
								// qu'il s'agit du dernier passage en commission pour ces dossiers
								array(
									'alias' => 'passagescommissionseps',
									'fields' => array(
										'passagescommissionseps.dossierep_id'
									),
									'conditions' => array(
										'passagescommissionseps.dossierep_id = Dossierep.id',
										'passagescommissionseps.etatdossierep' => array( 'traite', 'annule' )
									),
								)
							).' )',
							'Dossierep.themeep' => 'nonrespectssanctionseps93',
						),
						'contain' => false
					)
				);

				if( $count > 0 ) {
					$erreurs[] = 'Dossierep.id';
				}
				else {
					// 0.2°) Il n'existe pas de dossier d'EP dont la date de décision est plus récente que XXX jours pour la thématique "Demande de suspension"
					//Configure::read( 'Nonrespectsanctionep93.relanceDecisionNonRespectSanctions' )
					$count = $this->Nonrespectsanctionep93->Dossierep->find(
						'count',
						array(
							'conditions' => array(
								'Dossierep.actif' => '1',
								'Dossierep.personne_id' => $personne_id,
								//'Dossierep.etapedossierep' => 'traite',
								'Dossierep.id IN ( '.$this->Nonrespectsanctionep93->Dossierep->Passagecommissionep->sq(
										// Les états traite et annule étant des états finaux, on est certains
										// qu'il s'agit du dernier passage en commission pour ces dossiers
										array(
											'alias' => 'passagescommissionseps',
											'fields' => array(
												'passagescommissionseps.dossierep_id'
											),
											'conditions' => array(
												'passagescommissionseps.dossierep_id = Dossierep.id',
												'passagescommissionseps.etatdossierep' => array( 'traite', 'annule' )
											),
										)
								).' )',
								'Dossierep.themeep' => 'nonrespectssanctionseps93',
								'( DATE( NOW() ) - ( "Commissionep"."dateseance"::DATE ) ) <= '.Configure::read( 'Nonrespectsanctionep93.relanceDecisionNonRespectSanctions' )
							),
							'contain' => false,
							'joins' => array(
								array(
									'table'      => 'nonrespectssanctionseps93',
									'alias'      => 'Nonrespectsanctionep93',
									'type'       => 'INNER',
									'foreignKey' => false,
									'conditions' => array( 'Dossierep.id = Nonrespectsanctionep93.dossierep_id' )
								),
								array(
									'table'      => 'passagescommissionseps',
									'alias'      => 'Passagecommissionep',
									'type'       => 'INNER',
									'foreignKey' => false,
									'conditions' => array( 'Dossierep.id = Passagecommissionep.dossierep_id' )
								),
								$this->Nonrespectsanctionep93->Dossierep->Passagecommissionep->join( 'Commissionep', array( 'type' => 'INNER' ) ),
								array(
									'table'      => 'decisionsnonrespectssanctionseps93',
									'alias'      => 'Decisionnonrespectsanctionep93',
									'type'       => 'INNER',
									'foreignKey' => false,
									'conditions' => array(
										'Passagecommissionep.id = Decisionnonrespectsanctionep93.passagecommissionep_id',
									)
								)
							)
						)
					);

					if( $count > 0 ) {
						$erreurs[] = 'Dossierep.datedecision';
					}
					else {
						// 1°) La personne possède un dernièr contrat d'insertion validé
						$contratinsertion = $this->Nonrespectsanctionep93->Contratinsertion->find(
							'first',
							array(
								'conditions' => array(
									'Contratinsertion.personne_id' => $personne_id,
									'Contratinsertion.decision_ci' => 'V',
									'Contratinsertion.df_ci IS NOT NULL',
									'Contratinsertion.datevalidation_ci IS NOT NULL',

								),
								'order' => array( 'Contratinsertion.df_ci DESC' ),
								'contain' => false
							)
						);

						$orientstruct = $this->Nonrespectsanctionep93->Orientstruct->find(
							'first',
							array(
								'conditions' => array(
									'Orientstruct.personne_id' => $personne_id,
									'Orientstruct.date_valid IS NOT NULL',
									'Orientstruct.statut_orient' => 'Orienté',
									'Orientstruct.date_impression IS NOT NULL',

								),
								'order' => array( 'Orientstruct.date_impression DESC' ),
								'contain' => false
							)
						);

						// 2.2°) La personne ne possède ni orientation validée et éditée, ni contrat validé
						if( empty( $contratinsertion ) && empty( $orientstruct ) ) {
							$erreurs[] = 'Contratinsertion.Orientstruct';
						}
						else {
							// On a un contrat, pas d'orientation ou une date de contrat postérieure à la date d'orientation
							// Donc on se base sur le contrat
							if( !empty( $contratinsertion ) && ( empty( $orientstruct ) || ( $orientstruct['Orientstruct']['date_impression'] < $contratinsertion['Contratinsertion']['datevalidation_ci'] ) ) ) {
								$relanceCerCer1 = Configure::read( 'Nonrespectsanctionep93.relanceCerCer1' );
								$relanceCerCer2 = Configure::read( 'Nonrespectsanctionep93.relanceCerCer2' );

								$relances = $this->Nonrespectsanctionep93->find(
									'first',
									array(
										'conditions' => array(
											'Nonrespectsanctionep93.origine' => 'contratinsertion',
											'Nonrespectsanctionep93.contratinsertion_id' => $contratinsertion['Contratinsertion']['id'],
											'Nonrespectsanctionep93.dossierep_id IS NULL',
											'Nonrespectsanctionep93.active' => 1,
										),
										'joins' => array(
											array(
												'table'      => 'relancesnonrespectssanctionseps93',
												'alias'      => 'Relancenonrespectsanctionep93',
												'type'       => 'INNER',
												'foreignKey' => false,
												'conditions' => array(
													'Relancenonrespectsanctionep93.nonrespectsanctionep93_id = Nonrespectsanctionep93.id'
												)
											),
										),
										'contain' => array(
											'Relancenonrespectsanctionep93' => array(
												'order' => array( 'daterelance ASC' )
											)
										)
									)
								);

								$nbrelances = count( @$relances['Relancenonrespectsanctionep93'] );
								if( $nbrelances > 0 ) {
									$derniererelance = $relances['Relancenonrespectsanctionep93'][$nbrelances-1];
								}

								// 2.1°) La contrat est toujours en cours
								if( strtotime( $contratinsertion['Contratinsertion']['df_ci'] ) >= strtotime( date( 'Y-m-d' ) ) ) {
									$erreurs[] = 'Contratinsertion.df_ci';
								}
								else if( ( $nbrelances == 1 ) && strtotime( "+{$relanceCerCer2} days", strtotime( $derniererelance['daterelance'] ) ) >= strtotime( date( 'Y-m-d' ) ) ) {
									$erreurs[] = 'Nonrespectsanctionep93.relanceCerCer2';
								}
								else if( ( $nbrelances == 0 ) && strtotime( "+{$relanceCerCer1} days", strtotime( $contratinsertion['Contratinsertion']['df_ci'] ) ) >= strtotime( date( 'Y-m-d' ) ) ) {
									$erreurs[] = 'Nonrespectsanctionep93.relanceCerCer1';
								}
							}
							// On a une orientation, pas de contrat, ou une date d'orientation postérieure à la date de contrat
							// Donc on se base sur l'orientation
							else {
								$relanceOrientstructCer1 = Configure::read( 'Nonrespectsanctionep93.relanceOrientstructCer1' );
								$relanceOrientstructCer2 = Configure::read( 'Nonrespectsanctionep93.relanceOrientstructCer2' );
								$relanceOrientstructCer3 = Configure::read( 'Nonrespectsanctionep93.relanceOrientstructCer3' );

								$nbrelances = $this->Nonrespectsanctionep93->find(
									'first',
									array(
										'conditions' => array(
											'Nonrespectsanctionep93.origine' => 'orientstruct',
											'Nonrespectsanctionep93.orientstruct_id' => $orientstruct['Orientstruct']['id'],
											'Nonrespectsanctionep93.dossierep_id IS NULL',
											'Nonrespectsanctionep93.active' => 1,
										),
										'joins' => array(
											array(
												'table'      => 'relancesnonrespectssanctionseps93',
												'alias'      => 'Relancenonrespectsanctionep93',
												'type'       => 'INNER',
												'foreignKey' => false,
												'conditions' => array(
													'Relancenonrespectsanctionep93.nonrespectsanctionep93_id = Nonrespectsanctionep93.id'
												)
											),
										),
										'contain' => array(
											'Relancenonrespectsanctionep93' => array(
												'order' => array( 'daterelance ASC' )
											)
										)
									)
								);

								$nbrelances = count( @$relances['Relancenonrespectsanctionep93'] );
								if( $nbrelances > 0 ) {
									$derniererelance = $relances['Relancenonrespectsanctionep93'][$nbrelances-1];
								}

								if( ( $nbrelances == 2 ) && strtotime( "+{$relanceOrientstructCer3} days", strtotime( $derniererelance['daterelance'] ) ) >= strtotime( date( 'Y-m-d' ) ) ) {
									$erreurs[] = 'Nonrespectsanctionep93.relanceOrientstructCer3';
								}
								else if( ( $nbrelances == 1 ) && strtotime( "+{$relanceOrientstructCer2} days", strtotime( $derniererelance['daterelance'] ) ) >= strtotime( date( 'Y-m-d' ) ) ) {
									$erreurs[] = 'Nonrespectsanctionep93.relanceOrientstructCer2';
								}
								else if( ( $nbrelances == 0 ) && strtotime( "+{$relanceOrientstructCer1} days", strtotime( $orientstruct['Orientstruct']['date_impression'] ) ) >= strtotime( date( 'Y-m-d' ) ) ) {
									$erreurs[] = 'Nonrespectsanctionep93.relanceOrientstructCer1';
								}
							}
						}
					}
				}

				// La personne ne sera plus relancée, son deuxième passage sera créé par le shell
				$qd = $this->Nonrespectsanctionep93->qdSecondsPassagesCerOrientstruct();
				// $qd['fields'] = array( 'Dossierep.personne_id' );
				$qd['conditions']['Dossierep.personne_id'] = $personne_id;
				$result = $this->Nonrespectsanctionep93->find( 'first', $qd );

				if( !empty( $result ) ) {
					$erreurs[] = 'Nonrespectsanctionep93.shellSecondPassage';
				}
			}

			return $erreurs;
		}

		/**
		*
		*/

		public function dateRelanceMinimale( $typerelance, $numrelance, $data ) {
			if( $typerelance == 'orientstruct' ) {
				if( $numrelance == 1 ) {
					return date(
						'Y-m-d',
						strtotime(
							'+'.( Configure::read( 'Nonrespectsanctionep93.relanceOrientstructCer1' ) + 1 ).' days',
							strtotime( $data['Orientstruct']['date_impression'] )
						)
					);
				}
				else if( $numrelance > 1 ) {
					return date(
						'Y-m-d',
						strtotime(
							'+'.( Configure::read( "Nonrespectsanctionep93.relanceOrientstructCer{$numrelance}" ) + 1 ).' days',
							strtotime( $data['Relancenonrespectsanctionep93']['daterelance'] )
						)
					);
				}
			}
			else if( $typerelance == 'contratinsertion' ) {
				// Calcul de la date de relance minimale
				if( $numrelance == 1 ) {
					return date(
						'Y-m-d',
						strtotime(
							'+'.( Configure::read( 'Nonrespectsanctionep93.relanceCerCer1' ) + 1 ).' days',
							strtotime( $data['Contratinsertion']['df_ci'] )
						)
					);
				}
				else if( $numrelance > 1 ) {
					return date(
						'Y-m-d',
						strtotime(
							'+'.( Configure::read( "Nonrespectsanctionep93.relanceCerCer{$numrelance}" ) + 1 ).' days',
							strtotime( $data['Relancenonrespectsanctionep93']['daterelance'] )
						)
					);
				}
			}
		}

		/**
		* Récupère les données pour le PDf
		*/

		public function getDataForPdf( $id ) {
			// TODO: error404/error500 si on ne trouve pas les données
			$optionModel = ClassRegistry::init( 'Option' );
			$qual = $optionModel->qual();

			$qdPersonne = array(
				'Foyer' => array(
					'Dossier',
					'Adressefoyer' => array(
						'conditions' => array(
							'Adressefoyer.id IN ( '.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01( 'Adressefoyer.foyer_id' ).' )'
						),
						'Adresse'
					)
				)
			);

			$data = $this->find(
				'first',
				array(
					'conditions' => array(
						'Relancenonrespectsanctionep93.id' => $id
					),
					'contain' => array(
						'User',
						'Nonrespectsanctionep93' => array(
							'Orientstruct' => array(
								'Structurereferente',
								'Personne' => $qdPersonne
							),
							'Contratinsertion' => array(
								'Cer93',
								'Structurereferente',
								'Personne' => $qdPersonne
							),
							'Dossierep'
						)
					)
				)
			);

			if( !empty( $data ) ) {
				if( !empty( $data['Nonrespectsanctionep93']['Orientstruct'] ) && !empty( $data['Nonrespectsanctionep93']['Orientstruct']['Personne'] ) ) {
					$data['Nonrespectsanctionep93']['Personne'] = $data['Nonrespectsanctionep93']['Orientstruct']['Personne'];
					unset( $data['Nonrespectsanctionep93']['Orientstruct']['Personne'] );
					if ( $data['Relancenonrespectsanctionep93']['numrelance'] == 1 ) {
						$delairelance = Configure::read( 'Nonrespectsanctionep93.relanceOrientstructCer2' );
						$data['Relancenonrespectsanctionep93']['dateseconderelance'] = date( 'Y-m-d', strtotime( "+{$delairelance} days", strtotime( $data['Relancenonrespectsanctionep93']['dateimpression'] ) ) );
					}
				}
				else if( !empty( $data['Nonrespectsanctionep93']['Contratinsertion'] ) && !empty( $data['Nonrespectsanctionep93']['Contratinsertion']['Personne'] ) ) {
					$data['Nonrespectsanctionep93']['Personne'] = $data['Nonrespectsanctionep93']['Contratinsertion']['Personne'];
					unset( $data['Nonrespectsanctionep93']['Contratinsertion']['Personne'] );
					if ( $data['Relancenonrespectsanctionep93']['numrelance'] == 1 ) {
						$delairelance = Configure::read( 'Nonrespectsanctionep93.relanceCerCer2' );
						$data['Relancenonrespectsanctionep93']['dateseconderelance'] = date( 'Y-m-d', strtotime( "+{$delairelance} days", strtotime( $data['Relancenonrespectsanctionep93']['dateimpression'] ) ) );
					}
				}
			}
			else {
				return null;
			}

			// Traduction des enums du modèle Cer93
			$options = $this->Nonrespectsanctionep93->Contratinsertion->Cer93->enums();
			foreach( $options as $modelName => $modelOptions ) {
				foreach( $modelOptions as $fieldName => $fieldOptions ) {
					if( isset( $data['Nonrespectsanctionep93']['Contratinsertion'][$modelName][$fieldName] ) ) {
						$data['Nonrespectsanctionep93']['Contratinsertion'][$modelName][$fieldName] = value(
							$options[$modelName][$fieldName],
							$data['Nonrespectsanctionep93']['Contratinsertion'][$modelName][$fieldName]
						);
					}
				}
			}

			// Traduction des autres "enums"
			$data['Nonrespectsanctionep93']['Personne']['qual'] = Set::enum( $data['Nonrespectsanctionep93']['Personne']['qual'], $qual );

			// On va chercher la dernière orientation de l'allocataire pour l'ajouter aux données
			$sqDerniere = $this->Nonrespectsanctionep93->Orientstruct->WebrsaOrientstruct->sqDerniere( 'Orientstruct.personne_id' );
			$query = array(
				'fields' => array_merge(
					$this->Nonrespectsanctionep93->Orientstruct->fields(),
					$this->Nonrespectsanctionep93->Orientstruct->Typeorient->fields(),
					$this->Nonrespectsanctionep93->Orientstruct->Structurereferente->fields()
				),
				'contain' => false,
				'joins' => array(
					$this->Nonrespectsanctionep93->Orientstruct->join( 'Typeorient', array( 'type' => 'INNER' ) ),
					$this->Nonrespectsanctionep93->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
				),
				'conditions' => array(
					'Orientstruct.personne_id' => $data['Nonrespectsanctionep93']['Personne']['id'],
					"Orientstruct.id IN ( {$sqDerniere} )"
				)
			);
			$orientstruct = $this->Nonrespectsanctionep93->Orientstruct->find( 'first', $query );
			$orientstruct = array_words_replace(
				$orientstruct,
				array(
					'Orientstruct' => 'Orientstructactuelle',
					'Typeorient' => 'Typeorientactuel',
					'Structurereferente' => 'Structurereferenteactuelle',
				)
			);

			$data = Hash::merge( $data, $orientstruct );

			return $data;
		}

		/**
		* Retourne le chemin relatif du modèle de document à utiliser pour l'enregistrement du PDF.
		*/

		public function modeleOdt( $data ) {
			return "{$this->alias}/notification_{$data['Nonrespectsanctionep93']['origine']}_relance{$data['Relancenonrespectsanctionep93']['numrelance']}.odt";
		}

		/**
		 * Sous-requête permettant de récupérer la dernière relance liée à un enregistrement de la table
		 * nonrespectssanctionseps93.
		 *
		 * @param string $nonrespectsanctionep93IdFied Le champ de la requête principale correspondant
		 *	à la clé primaire de la table nonrespectssanctionseps93
		 * @return string
		 */
		public function sqDerniere( $nonrespectsanctionep93IdFied = 'Nonrespectsanctionep93.id' ) {
			return $this->sq(
				array(
					'fields' => array(
						'relancesnonrespectssanctionseps93.id'
					),
					'alias' => 'relancesnonrespectssanctionseps93',
					'conditions' => array(
						"relancesnonrespectssanctionseps93.nonrespectsanctionep93_id = {$nonrespectsanctionep93IdFied}"
					),
					'order' => array( 'relancesnonrespectssanctionseps93.daterelance DESC' ),
					'limit' => 1
				)
			);
		}

		/**
		 * Retourne l'id de la personne à laquelle est lié un enregistrement.
		 *
		 * @param integer $id L'id de l'enregistrement
		 * @return integer
		 */
		public function personneId( $id ) {
			$querydata = array(
				'fields' => array(
					"Contratinsertion.personne_id",
					"Orientstruct.personne_id",
					"Propopdo.personne_id",
				),
				'joins' => array(
					$this->join( 'Nonrespectsanctionep93', array( 'type' => 'INNER' ) ),
					$this->Nonrespectsanctionep93->join( 'Contratinsertion', array( 'type' => 'LEFT OUTER' ) ),
					$this->Nonrespectsanctionep93->join( 'Orientstruct', array( 'type' => 'LEFT OUTER' ) ),
					$this->Nonrespectsanctionep93->join( 'Propopdo', array( 'type' => 'LEFT OUTER' ) ),
				),
				'conditions' => array(
					"{$this->alias}.id" => $id
				),
				'recursive' => -1
			);

			$result = $this->find( 'first', $querydata );

			if( !empty( $result ) ) {
				if( !empty( $result['Contratinsertion']['personne_id'] ) ) {
					return $result['Contratinsertion']['personne_id'];
				}
				else if( !empty( $result['Orientstruct']['personne_id'] ) ) {
					return $result['Orientstruct']['personne_id'];
				}
				else if( !empty( $result['Propopdo']['personne_id'] ) ) {
					return $result['Propopdo']['personne_id'];
				}
			}

			return null;
		}

		/**
		 * Retourne la liste des options ainsi que des champs possédant la règle
		 * de validation inList, auxquels on ajoute.
		 * les options pour numrelance.
		 *
		 * @return string
		 */
		public function enums() {
			$enums = parent::enums();

			if( !isset( $enums[$this->alias]['numrelance'] ) ) {
				$enums[$this->alias]['numrelance'] = array(
					1 => 'Première relance',
					2 => 'Confirmation passage en EP'
				);
			}

			return $enums;
		}

		/**
		 *
		 * @param integer $personne_id
		 * @param array $mesCodesInsee
		 * @param boolean $filtre_zone_geo
		 * @param mixed $sqLocked
		 * @param integer $user_id
		 * @return array
		 */
		public function getRelance( $personne_id, $mesCodesInsee, $filtre_zone_geo, $sqLocked, $user_id ) {
			$foos = array(
				'Contratinsertion' => array(
					array(
						'Relance.contrat' => '1',
						'Relance.numrelance' => '1'
					),
					array(
						'Relance.contrat' => '2',
						'Relance.numrelance' => '2'
					),
				),
				'Orientstruct' => array(
					array(
						'Relance.contrat' => '0',
						'Relance.numrelance' => '1'
					),
					array(
						'Relance.contrat' => '0',
						'Relance.numrelance' => '2'
					),
				)
			);

			foreach( $foos as $modelName => $relancesParams ) {
				foreach( $relancesParams as $relanceParams ) {
					$querydata = $this->search(
						$mesCodesInsee,
						$filtre_zone_geo,
						$relanceParams,
						$sqLocked
					);
					$querydata['conditions']['Personne.id'] = $personne_id;
					$results = $this->Nonrespectsanctionep93->{$modelName}->find( 'all', $querydata );
					if( !empty( $results ) ) {
						$relanceParams = Hash::expand( $relanceParams );
						foreach( array_keys( $results ) as $i ) {
							$results[$i] = Hash::merge( $results[$i], $relanceParams );
						}
						return $results;
					}
				}
			}

			return array();
		}

		/**
		 * Préparation des données du formulaire de cohortes (pour un premier passage).
		 *
		 * Pour savoir si la relance sera la première ou la seconde, pour une
		 * orientation ou un CER, on regarde les clés ['Relance.contrat'] et
		 * ['Relance.numrelance'] du paramètre $search, sinon, on regarde pour
		 * chacun des résultats les clés ['Relance']['contrat'] et
		 * ['Relance']['numrelance'].
		 *
		 * @param array $results
		 * @param array $search
		 * @return array
		 */
		public function prepareFormData( array $results, array $search = array() ) {
			if( !empty( $results ) ) {
				foreach( $results as $i => $result ) {
					// Si on vient de la cohorte, on doit ajouter des infos
					if( !empty( $search ) ) {
						$result['Relance']['contrat'] = $search['Relance.contrat'];
						$result['Relance']['numrelance'] = $search['Relance.numrelance'];
					}

					// Orientations non contractualisées
					if( $result['Relance']['contrat'] == 0 ) {
						// Calcul de la date de relance minimale
						if( $result['Relance']['numrelance'] == 1 ) {
							$results[$i]['Nonrespectsanctionep93']['datemin'] = date(
								'Y-m-d',
								strtotime(
									'+'.( Configure::read( 'Nonrespectsanctionep93.relanceOrientstructCer1' ) + 1 ).' days',
									strtotime( $result['Orientstruct']['date_impression'] )
								)
							);
						}
						else if( $result['Relance']['numrelance'] > 1 ) {
							$results[$i]['Nonrespectsanctionep93']['datemin'] = date(
								'Y-m-d',
								strtotime(
									'+'.( Configure::read( "Nonrespectsanctionep93.relanceOrientstructCer{$result['Relance']['numrelance']}" ) + 1 ).' days',
									strtotime( $result['Relancenonrespectsanctionep93']['daterelance'] )
								)
							);
						}

						$results[$i]['Orientstruct']['nbjours'] = round(
							( time() - strtotime( $result['Orientstruct']['date_impression'] ) ) / ( 60 * 60 * 24 )
						);
					}
					// Contrats non renouvelés
					else {
						// Calcul de la date de relance minimale
						if( $result['Relance']['numrelance'] == 1 ) {
							$results[$i]['Nonrespectsanctionep93']['datemin'] = date(
								'Y-m-d',
								strtotime(
									'+'.( Configure::read( 'Nonrespectsanctionep93.relanceCerCer1' ) + 1 ).' days',
									strtotime( $result['Contratinsertion']['df_ci'] )
								)
							);
						}
						else if( $result['Relance']['numrelance'] > 1 ) {
							$results[$i]['Nonrespectsanctionep93']['datemin'] = date(
								'Y-m-d',
								strtotime(
									'+'.( Configure::read( "Nonrespectsanctionep93.relanceCerCer{$result['Relance']['numrelance']}" ) + 1 ).' days',
									strtotime( $result['Relancenonrespectsanctionep93']['daterelance'] )
								)
							);
						}

						$results[$i]['Contratinsertion']['nbjours'] = round(
							( time() - strtotime( $result['Contratinsertion']['df_ci'] ) ) / ( 60 * 60 * 24 )
						);
					}
				}
			}

			return $results;
		}

		/**
		 * Préparation des données du formulaire d'ajout individuel (pour un premier passage).
		 *
		 * Pour savoir si la relance sera la première ou la seconde, pour une
		 * orientation ou un CER, on regarde les clés ['Relance']['contrat'] et
		 * ['Relance']['numrelance'].
		 *
		 * @param array $result
		 * @param integer $user_id
		 * @return array
		 */
		public function prepareFormDataAdd( array $result, $user_id ) {
			$origine = ( Hash::get( $result, 'Relance.contrat' ) ? 'contratinsertion' : 'orientstruct' );

			$formData = array(
				'Nonrespectsanctionep93' => array(
					'id' => Hash::get( $result, 'Nonrespectsanctionep93.id' ),
					'orientstruct_id' => Hash::get( $result, 'Orientstruct.id' ),
					'contratinsertion_id' => Hash::get( $result, 'Contratinsertion.id' ),
					'origine' => $origine,
					'dossierep_id' => null,
					'propopdo_id' => null,
					'historiqueetatpe_id' => null,
					'rgpassage' => 1,
					'sortieprocedure' => null,
					'active' => '1',
				),
				'Relancenonrespectsanctionep93' => array(
					'id' => null,
					'nonrespectsanctionep93_id' => null,
					'numrelance' => Hash::get( $result, 'Relance.numrelance' ),
					'dateimpression' => null,
					'daterelance_min' => Hash::get( $result, 'Nonrespectsanctionep93.datemin' ),
					'daterelance' => null,
					'user_id' => $user_id,
				)

			);

			return $formData;
		}
	}
?>