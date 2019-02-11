<?php
	/**
	 * Code source de la classe Cov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Commission d'orientation et validation (COV)
	 *
	 * @package app.Model
	 */
	class Cov58 extends AppModel
	{
		public $name = 'Cov58';

		public $actsAs = array(
			'Conditionnable',
			'Gedooo.Gedooo',
			'ModelesodtConditionnables' => array(
				58 => array(
					'%s/ordredujour.odt',
					'%s/pv.odt',
				)
			),
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		public $belongsTo = array(
			'Sitecov58' => array(
				'className' => 'Sitecov58',
				'foreignKey' => 'sitecov58_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		public $hasMany = array(
			'Passagecov58' => array(
				'className' => 'Passagecov58',
				'foreignKey' => 'cov58_id',
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

		/**
		 * Valeurs de etatcommissionep signifiant qu'une commission est "En cours".
		 *
		 * @var array
		 */
		public static $etatsEnCours = array( 'cree', 'associe', 'valide', 'decision' );

		/**
		 * Moteur de recherche par COV.
		 *
		 * @param array $search
		 * @return array
		 */
		public function search( array $search = array() ) {
			$query = array(
				'fields' => array(
					'Cov58.id',
					'Cov58.name',
					'Cov58.datecommission',
					'Cov58.etatcov',
					'Cov58.observation',
					'Sitecov58.name'
				),
				'joins' => array(
					$this->join( 'Sitecov58', array( 'type' => 'iNNER' ) )
				),
				'contain' => false,
				'conditions' => array(),
				'order' => array( '"Cov58"."datecommission" ASC' ),
			);

			// Valeurs approchantes
			$name = Hash::get( $search, 'Cov58.name' );
			if( !empty( $name ) ) {
				$query['conditions'][] = array( 'Cov58.name ILIKE' => $this->wildcard( $name ) );
			}

			// Valeurs exactes
			foreach( array( 'sitecov58_id', 'lieu', 'etatcov' ) as $key ) {
				$value = Hash::get( $search, "Cov58.{$key}" );
				if( !empty( $value ) ) {
					$query['conditions'][] = array( "Cov58.{$key}" => $value );
				}
			}

			// Plages de dates
			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, array( 'Cov58.datecommission' ) );

			return $query;
		}

		/**
		* Retourne la liste des dossiers de la séance d'une COV, groupés par thème,
		* pour les dossiers qui doivent passer par liste.
		*
		* @param integer $cov58_id L'id technique de la COV
		* @return array
		* @access public
		*/

		public function dossiersParListe( $cov58_id ) {
			$dossiers = array();

			foreach( $this->themesTraites( $cov58_id ) as $theme => $decision ) {
				$model = Inflector::classify( $theme );
				$queryData = $this->Passagecov58->Dossiercov58->{$model}->qdDossiersParListe( $cov58_id );
				$dossiers[$model]['liste'] = array();
				if( !empty( $queryData ) ) {
					$dossiers[$model]['liste'] = $this->Passagecov58->Dossiercov58->find( 'all', $queryData );
				}
			}

			return $dossiers;
		}

		/**
		* Sauvegarde des avis/décisions par liste d'une séance d'EP, au niveau ep ou cg
		*
		* @param integer $cov58_id L'id technique de la séance d'EP
		* @param array $data Les données à sauvegarder
		* @return boolean
		* @access public
		*/

		public function saveDecisions( $cov58_id, $data ) {
			$cov58 = $this->find( 'first', array( 'conditions' => array( 'Cov58.id' => $cov58_id ) ) );

			if( empty( $cov58 ) ) {
				return false;
			}

			$success = true;

			// Champs à conserver en cas d'annulation ou de report
			$champsAGarder = array( 'id', 'etapecov', 'passagecov58_id', 'created', 'modified' );
			$champsAGarderPourNonDecision = Set::merge( $champsAGarder, array( 'decisioncov', 'commentaire' ) );

			// Sauvegarde des règles de validation (pour les inList)
			$validates = array();

			foreach( $this->themesTraites( $cov58_id ) as $theme => $decision ) {
				$model = Inflector::classify( $theme );
				$modeleDecision = Inflector::classify( "decision{$theme}" );

				if( isset( $this->Passagecov58->{$modeleDecision}->validateFinalisation ) ) {
					$validates[$modeleDecision] = $this->Passagecov58->{$modeleDecision}->validate;
					$this->Passagecov58->{$modeleDecision}->validate = $this->Passagecov58->{$modeleDecision}->validateFinalisation;
				}

				if( isset( $data[$model] ) || isset( $data[$modeleDecision] ) && !empty( $data[$modeleDecision] ) ) {

					// Mise à NULL de certains champs de décision
					$champsDecision = array_keys( $this->Passagecov58->{$modeleDecision}->schema( true ) );
					$champsANull = array_fill_keys( array_diff( $champsDecision, $champsAGarder ), null );
					$champsANullPourNonDecision = array_diff( $champsDecision, $champsAGarderPourNonDecision );

					foreach( $data[$modeleDecision] as $i => $decision ) {
						// 1°) En cas d'annulation ou de report
						if( in_array( $decision['decisioncov'], array( 'annule', 'reporte' ) ) ) {
							foreach( $champsANullPourNonDecision as $champ ) {
								$data[$modeleDecision][$i][$champ] = null;
							}
						}
						// 2°) Dans les autres cas
						else {
							$data[$modeleDecision][$i] = Set::merge( $champsANull, $decision );
						}
					}

					$success = $this->Passagecov58->Dossiercov58->{$model}->saveDecisions( $data ) && $success;
				}
			}

			// Restauration des règles de validation
			foreach( $validates as $alias => $validate ) {
				$this->Passagecov58->{$alias}->validate = $validates[$alias];
			}

			///FIXME : calculer si tous les dossiers ont bien une décision avant de changer l'état ?
			$this->id = $cov58_id;
			$this->set( 'etatcov', "finalise" );
			$success = $this->save( null, array( 'atomic' => false ) ) && $success;

			return $success;
		}


		/**
		*
		*/

		public function getPdfOrdreDuJour( $cov58_id ) {
			$cov58_data = $this->find(
				'first',
				array(
					'conditions' => array(
						'Cov58.id' => $cov58_id
					),
					'contain' => false
				)
			);

			$queryData = array(
				'fields' => array(
					'Dossiercov58.id',
					'Dossiercov58.personne_id',
					'Dossiercov58.themecov58_id',
					'Dossiercov58.themecov58',
					'Themecov58.id',
					'Themecov58.name',
					//
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
					'Adresse.nomcom',
					'Adresse.numcom',
					'Adresse.codepos',
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa'/*,
					'Typeorient.lib_type_orient',*/
				),
				'joins' => array(
					array(
						'table'      => 'themescovs58',
						'alias'      => 'Themecov58',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( "Themecov58.id = Dossiercov58.themecov58_id" ),
					),
					array(
						'table'      => 'passagescovs58',
						'alias'      => 'Passagecov58',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( "Passagecov58.dossiercov58_id = Dossiercov58.id" ),
					),
					array(
						'table'      => 'covs58',
						'alias'      => 'Cov58',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( "Cov58.id = Passagecov58.cov58_id" ),
					),
					array(
						'table'      => 'personnes',
						'alias'      => 'Personne',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( "Dossiercov58.personne_id = Personne.id" ),
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
						'conditions' => array( 'Foyer.dossier_id = Dossier.id' )
					),
					array(
						'table'      => 'adressesfoyers',
						'alias'      => 'Adressefoyer',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'Foyer.id = Adressefoyer.foyer_id',
							// FIXME: c'est un hack pour n'avoir qu'une seule adresse de range 01 par foyer!
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
				'contain' => false,
				'conditions' => array(
					'Cov58.id' => $cov58_id
				)
			);

			$options = array( 'Personne' => array( 'qual' => ClassRegistry::init( 'Option' )->qual() ) );
			foreach( $this->Passagecov58->Dossiercov58->Themecov58->themes() as $theme ) {
				$model = Inflector::classify( $theme );
				$options = Set::merge( $options, $this->Passagecov58->Dossiercov58->{$model}->enums() );

				$qdModele = $this->Passagecov58->Dossiercov58->{$model}->qdOrdreDuJour();
				foreach( array( 'fields', 'joins', 'contain' ) as $key ) {
					if( isset( $qdModele[$key] ) ) {
						if( !isset( $queryData[$key] ) ) {
							$queryData[$key] = array();
						}
						$queryData[$key] = array_merge( (array)$queryData[$key], (array)$qdModele[$key] );
					}
				}
			}

			$options = Set::merge( $options, $this->enums() );

			$dossierscovs58 = $this->Passagecov58->Dossiercov58->find( 'all', $queryData );
			// FIXME: faire la traduction des enums dans les modèles correspondants ?
			$this->Informationpe = ClassRegistry::init( 'Informationpe' );
			foreach( $dossierscovs58 as $key => $dossiercov58 ) {

				$infope = $this->Informationpe->derniereInformation( $dossiercov58 );
				$dossierscovs58[$key]['Personne']['inscritpe'] = ( isset( $infope['Historiqueetatpe'][0]['etat'] ) && $infope['Historiqueetatpe'][0]['etat'] == 'inscription' ) ? 'Oui' : 'Non';

				// Traduction ...
				$dossierscovs58[$key]['Themecov58']['name'] = __d( 'dossiercov58', 'ENUM::THEMECOV::'.$dossiercov58['Themecov58']['name'] );
			}
// debug($dossierscovs58);
// die();
			return $this->ged(
				array_merge(
					array(
						$cov58_data,
						'Decisionscovs58' => $dossierscovs58
					)
				),
				"{$this->alias}/ordredujour.odt",
				true,
				$options
			);
		}




		/**
		* Change l'état de la commission de COV entre 'cree' et 'associe'
		* S'il existe au moins un dossier associé et un membre ayant donné une réponse
		* "Confirmé" ou "Remplacé par", l'état devient associé, sinon l'état devient 'cree'
		*
		* FIXME: il faudrait une réponse pour tous les membres ?
		*
		* @param integer $cov58_id L'identifiant technique de la commission d'EP
		* @return boolean
		*/

		public function changeEtatCreeAssocie( $cov58_id ) {
			$cov58 = $this->find(
				'first',
				array(
					'conditions' => array(
						'Cov58.id' => $cov58_id
					),
					'contain' => false
				)
			);

			if( empty( $cov58 ) || !in_array( $cov58['Cov58']['etatcov'], array( 'cree', 'associe' ) ) ) {
				return false;
			}
// debug($cov58);
// die();
			$success = true;

			$nbDossierscovs58 = $this->Passagecov58->find(
				'count',
				array(
					'conditions' => array(
						'Passagecov58.cov58_id' => $cov58_id
					)
				)
			);
// debug($nbDossierscovs58);
// die();
			$this->id = $cov58_id;
			if( ( $nbDossierscovs58 > 0 ) && ( $cov58['Cov58']['etatcov'] == 'cree' ) ) {
				$this->set( 'etatcov', 'associe' );
				$success = $this->save( null, array( 'atomic' => false ) ) && $success;

			}
			else if( ( ( $nbDossierscovs58 == 0 ) && ( $cov58['Cov58']['etatcov'] == 'associe' ) ) ) {
				$this->set( 'etatcov', 'cree' );
				$success = $this->save( null, array( 'atomic' => false ) ) && $success;
			}
			return $success;
		}

		public function themesTraites( $cov58_id ){
			$themecov58 = $this->Passagecov58->Dossiercov58->Themecov58->find(
				'first',
				array(
					'contain' => false,
					'conditions' => array(
						'Themecov58.id IN ( '.
							$this->Passagecov58->Dossiercov58->sq(
								array(
									'alias' => 'dossierscovs58',
									'fields' => array( 'dossierscovs58.themecov58_id' ),
									'conditions' => array(
										'dossierscovs58.id IN ( '.
											$this->sq(
												array(
													'alias' => 'covs58',
													'fields' => array( 'covs58.id' ),
													'conditions' => array(
														'covs58.id' => $cov58_id
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

			$themes = $this->Passagecov58->Dossiercov58->Themecov58->themes();
			$themesTraites = array();

			foreach( $themes as $theme ) {
				$themesTraites[$theme] = Hash::get( $themecov58, "Themecov58.{$theme}" );
			}

			return $themesTraites;
		}




		/**
		*
		*/

		public function getPdfPv( $cov58_id ) {
			$cov58_data = $this->find(
				'first',
				array(
					'conditions' => array(
						'Cov58.id' => $cov58_id
					),
					'contain' => false
				)
			);

			$queryData = array(
				'fields' => array(
					'Dossiercov58.id',
					'Dossiercov58.personne_id',
					'Dossiercov58.themecov58_id',
					'Themecov58.id',
					'Themecov58.name',
					//
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
					'Adresse.nomcom',
					'Adresse.numcom',
					'Adresse.codepos',
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
				),
				'conditions' => array(
					'Cov58.id' => $cov58_id
				),
				'joins' => array(
					array(
						'table'      => 'themescovs58',
						'alias'      => 'Themecov58',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( "Themecov58.id = Dossiercov58.themecov58_id" ),
					),
					array(
						'table'      => 'passagescovs58',
						'alias'      => 'Passagecov58',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( "Passagecov58.dossiercov58_id = Dossiercov58.id" ),
					),
					array(
						'table'      => 'covs58',
						'alias'      => 'Cov58',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( "Cov58.id = Passagecov58.cov58_id" ),
					),
					array(
						'table'      => 'personnes',
						'alias'      => 'Personne',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( "Dossiercov58.personne_id = Personne.id" ),
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
						'conditions' => array( 'Foyer.dossier_id = Dossier.id' )
					),
					array(
						'table'      => 'adressesfoyers',
						'alias'      => 'Adressefoyer',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'Foyer.id = Adressefoyer.foyer_id',
							// FIXME: c'est un hack pour n'avoir qu'une seule adresse de range 01 par foyer!
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
				'contain' => false
			);
			$options = array( 'Personne' => array( 'qual' => ClassRegistry::init( 'Option' )->qual() ) );

			$themeClassNames = array();
			foreach( $this->Passagecov58->Dossiercov58->Themecov58->themes() as $theme ) {
				$model = Inflector::classify( $theme );
				$themeClassNames[] = $model;

				$options = Set::merge( $options, $this->Passagecov58->Dossiercov58->{$model}->enums() );

				$qdModele = $this->Passagecov58->Dossiercov58->{$model}->qdProcesVerbal();
				foreach( array( 'fields', 'joins' ) as $key ) {
					$queryData[$key] = array_merge( $queryData[$key], $qdModele[$key] );
				}
			}
			$options = Set::merge( $options, $this->enums() );

			// Combinaison des jointures
			if( isset( $queryData['joins'] ) && !empty( $queryData['joins'] ) ) {
				$joins = array();
				$joinIndices = array();
				$mergedJoins = array();

				foreach( $queryData['joins'] as $join ) {
					$join['conditions'] = (array)$join['conditions'];
					if( !isset( $joinIndices[$join['alias']] ) ) {
						$joins[] = $join;
						$joinIndices[$join['alias']] = count( $joins ) - 1;
					}
					else {
						$mergedJoins[] = $joinIndices[$join['alias']];
						if( !isset( $joins[$joinIndices[$join['alias']]]['conditions']['OR'] ) ) {
							$joins[$joinIndices[$join['alias']]]['conditions'] = array(
								'OR' => array(
									$joins[$joinIndices[$join['alias']]]['conditions'],
									$join['conditions']
								)
							);
						}
						else {
							$joins[$joinIndices[$join['alias']]]['conditions']['OR'][] = $join['conditions'];
						}
					}
				}

				if( !empty( $mergedJoins ) ) {
					foreach( $mergedJoins as $indice ) {
						if( isset( $joins[$indice] ) ) {
							$join = $joins[$indice];
							unset( $joins[$indice] );
							$joins[] = $join;
						}
					}
				}

				$queryData['joins'] = array_values( $joins );
			}

			$dossierscovs58 = $this->Passagecov58->Dossiercov58->find( 'all', $queryData );

			// Préparation d'un enregistrement vide
			if( !empty( $dossierscovs58 ) ) {
				$empty = array_keys( Hash::flatten( $dossierscovs58[0] ) );
				$empty = Hash::expand( Set::normalize( $empty ) );
				foreach( $themeClassNames as $themeClassName ) {
					$empty[$themeClassName]['Typeorient'] = $empty['Typeorient'];
					$empty[$themeClassName]['Structurereferente'] = $empty['Structurereferente'];
				}
				unset( $empty['Typeorient'], $empty['Structurereferente'] );
			}

			// FIXME: faire la traduction des enums dans les modèles correspondants ?
			$this->Informationpe = ClassRegistry::init( 'Informationpe' );
			foreach( $dossierscovs58 as $key => $dossiercov58 ) {

				// Déplacement des données du type d'orientation et de la structure référente
				foreach( $themeClassNames as $themeClassName ) {
					if( !empty( $dossierscovs58[$key][$themeClassName]['id'] ) ) {
						$dossierscovs58[$key][$themeClassName]['Typeorient'] = $dossierscovs58[$key]['Typeorient'];
						$dossierscovs58[$key][$themeClassName]['Structurereferente'] = $dossierscovs58[$key]['Structurereferente'];

						unset( $dossierscovs58[$key]['Typeorient'], $dossierscovs58[$key]['Structurereferente'] );
					}
				}

				// Ajout de données à NULL pour l'impression en sections
				$dossierscovs58[$key] = Set::merge( $empty, $dossierscovs58[$key] );

				$infope = $this->Informationpe->derniereInformation( $dossiercov58 );
				$dossierscovs58[$key]['Personne']['inscritpe'] = ( isset( $infope['Historiqueetatpe'][0]['etat'] ) && $infope['Historiqueetatpe'][0]['etat'] == 'inscription' ) ? 'Oui' : 'Non';

				// Traduction ...
				$dossierscovs58[$key]['Themecov58']['name'] = __d( 'dossiercov58', 'ENUM::THEMECOV::'.$dossiercov58['Themecov58']['name'], true );
			}

			$decisionscovs = array( 'accepte' => 'Accepté', 'refus' => 'Refusé', 'ajourne' => 'Ajourné' );
			foreach( $themeClassNames as $themeClassName ) {
				$options[$themeClassName]['decisioncov'] = $decisionscovs;
			}

			return $this->ged(
				array_merge(
					array(
						$cov58_data,
						'Decisionscovs58' => $dossierscovs58,
					)
				),
				"{$this->alias}/pv.odt",
				true,
				$options
			);
		}
	}
?>