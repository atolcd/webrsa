<?php
	/**
	 * Code source de la classe Nonorientationproep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Abstractclass
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );
	App::uses( 'Thematiqueep', 'Model/Abstractclass' );

	/**
	 * La classe Nonorientationproep ...
	 *
	 * @package app.Model.Abstractclass
	 */
	abstract class Nonorientationproep extends Thematiqueep
	{
		public $actsAs = array(
			'Conditionnable',
			'Gedooo.Gedooo',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Dossierep' => array(
				'className' => 'Dossierep',
				'foreignKey' => 'dossierep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Orientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'orientstruct_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Retourne un querydata permettant de retrouver les allocataires  orientés en social depuis un
		 * certain temps (suivant le filtre ou la configuration au CG 66), qui ne possèdent pas de CER en
		 * cours, qui ne sont pas en cours de passage en EP ou en COV pour cette thématique (pour la
		 * dernière orientation en cours).
		 *
		 * @param array $mesCodesInsee
		 * @param boolean $filtre_zone_geo
		 * @param array $datas
		 * @return array
		 */
		public function searchNonReoriente( $mesCodesInsee, $filtre_zone_geo, $datas) {
			$conditions = array();
			$cg = Configure::read( 'Cg.departement' );

			// Formulaires de filtre
			if( $cg == 58 ){
				// Critères sur le CI - date de saisi contrat
				if( isset( $datas['Filtre']['df_ci_from'] ) && !empty( $datas['Filtre']['df_ci_from'] ) ) {
					$valid_from = ( valid_int( $datas['Filtre']['df_ci_from']['year'] ) && valid_int( $datas['Filtre']['df_ci_from']['month'] ) && valid_int( $datas['Filtre']['df_ci_from']['day'] ) );
					$valid_to = ( valid_int( $datas['Filtre']['df_ci_to']['year'] ) && valid_int( $datas['Filtre']['df_ci_to']['month'] ) && valid_int( $datas['Filtre']['df_ci_to']['day'] ) );
					if( $valid_from && $valid_to ) {
						$conditions[] = 'Contratinsertion.df_ci BETWEEN \''.implode( '-', array( $datas['Filtre']['df_ci_from']['year'], $datas['Filtre']['df_ci_from']['month'], $datas['Filtre']['df_ci_from']['day'] ) ).'\' AND \''.implode( '-', array( $datas['Filtre']['df_ci_to']['year'], $datas['Filtre']['df_ci_to']['month'], $datas['Filtre']['df_ci_to']['day'] ) ).'\'';
					}
				}

				// Critère sur le temps passé en orientation sociale
				$nbmois = Set::classicExtract($datas, 'Filtre.dureenonreorientation');
				$conditions[] = 'EXISTS(
					SELECT
						*
					FROM orientsstructs
						INNER JOIN typesorients ON ( typesorients.id = orientsstructs.typeorient_id )
					WHERE
						orientsstructs.personne_id = Personne.id
						AND orientsstructs.statut_orient = \'Orienté\'
						AND (
							NOT EXISTS(
								SELECT *
									FROM orientsstructs AS osvt
										INNER JOIN typesorients AS tosvt ON ( tosvt.id = osvt.typeorient_id )
									WHERE
										osvt.personne_id = orientsstructs.personne_id
										AND osvt.statut_orient = \'Orienté\'
										AND osvt.date_valid > orientsstructs.date_valid
										AND tosvt.id = '.Configure::read( 'Typeorient.emploi_id' ).'
							)
						)
						AND typesorients.id <> '.Configure::read( 'Typeorient.emploi_id' ).'
						LIMIT 1
				)';
			}
			else if( $cg == 66 ){
				// Filtre sur la caton et la comune
				$conditions = $this->conditionsAdresse( $conditions, $datas, $filtre_zone_geo, $mesCodesInsee );

				// Filtre sur la structure référente
				if ( isset($datas['Filtre']['structurereferente_id']) && !empty($datas['Filtre']['structurereferente_id']) ) {
					$structs = Set::classicExtract($datas, 'Filtre.structurereferente_id');
					$conditions[] = 'Orientstruct.structurereferente_id = \''.Sanitize::clean( $structs, array( 'encode' => false ) ).'\'';
				}

				// Filtre sur le référent
				if ( isset($datas['Filtre']['referent_id']) && !empty($datas['Filtre']['referent_id']) ) {
					$referents = Set::classicExtract($datas, 'Filtre.referent_id');
					$conditions[] = 'Orientstruct.referent_id = \''.Sanitize::clean( $referents, array( 'encode' => false ) ).'\'';
				}

				// Paramétrage, date d'orientation
				$typesorientsParentidsSocial = Configure::read( 'Orientstruct.typeorientprincipale.SOCIAL' );
				$typesorientsParentidsEmploi = Configure::read( 'Orientstruct.typeorientprincipale.Emploi' );

				$conditions[] = 'EXISTS(
					SELECT
						*
					FROM orientsstructs
						INNER JOIN typesorients ON ( typesorients.id = orientsstructs.typeorient_id )
					WHERE
						orientsstructs.personne_id = Personne.id
						AND orientsstructs.statut_orient = \'Orienté\'
						AND (
							NOT EXISTS(
								SELECT *
									FROM orientsstructs AS osvt
										INNER JOIN typesorients AS tosvt ON ( tosvt.id = osvt.typeorient_id )
									WHERE
										osvt.personne_id = orientsstructs.personne_id
										AND osvt.statut_orient = \'Orienté\'
										AND osvt.date_valid > orientsstructs.date_valid
										AND tosvt.parentid IN ( '.implode( ',', $typesorientsParentidsEmploi ).' )
							)
						)
						AND typesorients.parentid IN ( '.implode( ',', $typesorientsParentidsSocial ).' )
						AND orientsstructs.date_valid <= \''.date( 'Y-m-d', strtotime( '- 24 month', time() ) ).'\'
				)';
			}
			else if( $cg == 93 ) {
				// Filtre, date d'orientation
				$nbmois = Set::classicExtract($datas, 'Filtre.dureenonreorientation');
				$conditions[] = 'EXISTS(
					SELECT
						*
					FROM orientsstructs
						INNER JOIN typesorients ON ( typesorients.id = orientsstructs.typeorient_id )
					WHERE
						orientsstructs.personne_id = Personne.id
						AND orientsstructs.statut_orient = \'Orienté\'
						AND (
							NOT EXISTS(
								SELECT *
									FROM orientsstructs AS osvt
										INNER JOIN typesorients AS tosvt ON ( tosvt.id = osvt.typeorient_id )
									WHERE
										osvt.personne_id = orientsstructs.personne_id
										AND osvt.statut_orient = \'Orienté\'
										AND osvt.date_valid > orientsstructs.date_valid
										AND tosvt.lib_type_orient LIKE \'Emploi%\'
							)
						)
						AND typesorients.lib_type_orient NOT LIKE \'Emploi%\'
						AND orientsstructs.date_valid <= \''.date( 'Y-m-d', strtotime( '- '.$nbmois.' month', time() ) ).'\'
						LIMIT 1
				)';
			}

			// 66: conditions sur le bilan de parcours
			if( $cg == 66 ) {
				$conditions[] = array(
					'Contratinsertion.id NOT IN (
						SELECT contratsinsertion.id
							FROM contratsinsertion
								INNER JOIN bilansparcours66 ON (
									bilansparcours66.contratinsertion_id = contratsinsertion.id
								)
							WHERE
								bilansparcours66.contratinsertion_id = Contratinsertion.id
					)'
				);
			}

			// La dernière orientation
			$conditions[] = 'Orientstruct.id IN ( '.$this->Orientstruct->WebrsaOrientstruct->sqDerniere().' )';

			// Conditions de base pour qu'un allocataire puisse passer en EP
			$conditions['Prestation.rolepers'] = array( 'DEM', 'CJT' );
			$conditions['Calculdroitrsa.toppersdrodevorsa'] = '1';
			$conditions['Situationdossierrsa.etatdosrsa'] = $this->Orientstruct->Personne->Foyer->Dossier->Situationdossierrsa->etatOuvert();
			$conditions[] = 'Adressefoyer.id IN ( '.$this->Orientstruct->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )';

			// Une zone géographique à laquelle l'utilisateur peut avoir accès
			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );

			// Le dernier CER
			$conditions[] = 'Contratinsertion.id IN ( '.$this->Orientstruct->Personne->Contratinsertion->WebrsaContratinsertion->sqDernierContrat().' )';

			// Le dernier dossier de l'allocataire
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $datas['Filtre'] );


			// La personne ne doit pas être en cours de passage en EP pour cette thématique
			$modelName = $this->alias;
			$modelTable = Inflector::tableize( $modelName );
			$conditions[] = 'Orientstruct.id NOT IN (
				SELECT "'.$modelTable.'"."orientstruct_id"
				FROM "'.$modelTable.'"
					INNER JOIN "dossierseps" ON ( "dossierseps"."id" = "'.$modelTable.'"."dossierep_id" )
				WHERE "dossierseps"."id" NOT IN (
					SELECT "passagescommissionseps"."dossierep_id"
					FROM passagescommissionseps
					WHERE "passagescommissionseps"."etatdossierep" = \'traite\'
				)
				AND "dossierseps"."themeep" = \''.$modelTable.'\'
				AND "'.$modelTable.'"."orientstruct_id" = "Orientstruct"."id"
			)';

			// On peut repasser pour cette thématique si le passage lié à cette orientation est plus vieux que
			// le délai que l'on laisse pour créer le CER
			$conditions[] = 'Orientstruct.id NOT IN (
				SELECT '.Inflector::tableize( $this->alias ).'.orientstruct_id
					FROM '.Inflector::tableize( $this->alias ).'
						INNER JOIN dossierseps ON (
							'.Inflector::tableize( $this->alias ).'.dossierep_id = dossierseps.id
						)
					WHERE
						'.Inflector::tableize( $this->alias ).'.orientstruct_id = Orientstruct.id
						AND dossierseps.id IN (
							SELECT "passagescommissionseps"."dossierep_id"
							FROM passagescommissionseps
							WHERE "passagescommissionseps"."etatdossierep" = \'traite\'
						)
						AND ( DATE( NOW() ) - (
							SELECT "decisions'.Inflector::tableize( $this->alias ).'"."modified"::DATE
								FROM decisions'.Inflector::tableize( $this->alias ).'
									INNER JOIN passagescommissionseps ON ( decisions'.Inflector::tableize( $this->alias ).'.passagecommissionep_id = passagescommissionseps.id )
									INNER JOIN dossierseps ON ( passagescommissionseps.dossierep_id = dossierseps.id )
								ORDER BY modified DESC
								LIMIT 1
						) ) <= '.Configure::read( $this->alias.'.delaiCreationContrat' ).'
			)';

			// On souhaite n'afficher que les orientations en social ne possédant encore pas de dossier COV
			// 1°) On a un dossier COV en cours de passage (<> finalisé (accepté/refusé), <> reporté) // {cree,traitement,ajourne,finalise}
			// 2°) Si COV accepte -> on a un dossier en EP -> OK (voir plus haut)
			// 3°) Si COV refuse -> il doit réapparaître
			// 4°) ATTENTION: accepté/refusé -> nouvelle orientation
			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$conditions[] = array(
					'Orientstruct.id NOT IN (
						SELECT "proposnonorientationsproscovs58"."orientstruct_id"
							FROM proposnonorientationsproscovs58
								INNER JOIN "dossierscovs58"
									ON ( "dossierscovs58"."id" = "proposnonorientationsproscovs58"."dossiercov58_id" )
							WHERE
								"dossierscovs58"."id" NOT IN (
									SELECT "passagescovs58"."dossiercov58_id"
									FROM passagescovs58
									WHERE "passagescovs58"."etatdossiercov" = \'traite\'
								)
								AND "dossierscovs58"."themecov58" = \'proposnonorientationsproscovs58\'
								AND "proposnonorientationsproscovs58"."orientstruct_id" = Orientstruct.id
					)'
				);
			}

			$querydata = array(
				'fields' => array(
					'Orientstruct.id',
					'Orientstruct.date_valid',
					'Orientstruct.user_id',
					'Typeorient.id',
					'Typeorient.lib_type_orient',
					'Structurereferente.id',
					'Structurereferente.lib_struc',
					$this->Orientstruct->Personne->Foyer->sqVirtualField( 'enerreur', true ),
					'Referent.qual',
					'Referent.nom',
					'Referent.prenom',
					'Personne.id',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.nir',
					'Dossier.numdemrsa',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Contratinsertion.df_ci',
					$this->Orientstruct->Personne->Contratinsertion->sqVirtualField( 'nbjours', true )
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
					$this->Orientstruct->join( 'Typeorient', array( 'type' => 'INNER' ) ),
					$this->Orientstruct->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					$this->Orientstruct->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Orientstruct->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$this->Orientstruct->Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
					$this->Orientstruct->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Orientstruct->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$this->Orientstruct->Personne->Foyer->Dossier->join( 'Detaildroitrsa', array( 'type' => 'LEFT OUTER' ) ),
					$this->Orientstruct->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
					$this->Orientstruct->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'INNER' ) ),
					$this->Orientstruct->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) ),
					$this->Orientstruct->Personne->join( 'Contratinsertion', array( 'type' => 'INNER' ) ),
				),
				'contain' => false,
				'order' => $this->Orientstruct->Personne->Contratinsertion->sqVirtualField( 'nbjours', false )." DESC"
			);

			$querydata = $this->Orientstruct->Personne->PersonneReferent->completeQdReferentParcours( $querydata, $datas['Filtre'] );

			return $querydata;
		}

		/**
		 *
		 * @param array $datas
		 * @return boolean
		 */
		public function saveCohorte( $datas ) {
			$success = true;

			foreach( $datas['Nonorientationproep'] as $dossier ) {
				if( isset( $dossier['passageep'] ) && $dossier['passageep'] == 1 ) {
					$dossierep = array(
						'Dossierep' => array(
							'personne_id' => $dossier['personne_id'],
							'etapedossierep' => 'cree',
							'themeep' => Inflector::tableize( $this->alias )
						)
					);
					$this->Dossierep->create( $dossierep );
					$success = $this->Dossierep->save( null, array( 'atomic' => false ) ) && $success;

					$nonorientationproep = array(
						$this->alias => array(
							'dossierep_id' => $this->Dossierep->id,
							'orientstruct_id' => $dossier['orientstruct_id'],
							'user_id' => ( isset( $dossier['user_id'] ) ) ? $dossier['user_id'] : null
						)
					);
					$this->create( $nonorientationproep );
					$success = $this->save( null, array( 'atomic' => false ) ) && $success;
				}
				else if ( ( Configure::read( 'Cg.departement' ) == 58 ) && isset( $dossier['passagecov'] ) && $dossier['passagecov'] == 1 ) {

					$themecov58 = $this->Orientstruct->Propononorientationprocov58->Dossiercov58->Themecov58->find(
						'first',
						array(
							'conditions' => array(
								'Themecov58.name' => Inflector::tableize($this->Orientstruct->Propononorientationprocov58->alias)
							),
							'contain' => false
						)
					);

					$dossiercov58 = array(
						'Dossiercov58' => array(
							'themecov58_id' => $themecov58['Themecov58']['id'],
							'themecov58' => 'proposnonorientationsproscovs58',
							'personne_id' => $dossier['personne_id']
						)
					);
					$this->Orientstruct->Propononorientationprocov58->Dossiercov58->create( $dossiercov58 );
					$success = $this->Orientstruct->Propononorientationprocov58->Dossiercov58->save( null, array( 'atomic' => false ) ) && $success;

					$propononorientationprocov58 = array(
						'Propononorientationprocov58' => array(
							'dossiercov58_id' => $this->Orientstruct->Propononorientationprocov58->Dossiercov58->id,
							'personne_id' => $dossier['personne_id'],
							'typeorient_id' => $dossier['typeorient_id'],
							'structurereferente_id' => $dossier['structurereferente_id'],
							'orientstruct_id' => $dossier['orientstruct_id'],
							'rgorient' => $this->Orientstruct->WebrsaOrientstruct->rgorientMax( $dossiercov58['Dossiercov58']['personne_id'] ) + 1,
							'datedemande' => date( 'd-m-Y' ),
							'user_id' => ( isset( $dossier['user_id'] ) ) ? $dossier['user_id'] : null
						)
					);
					$this->Orientstruct->Propononorientationprocov58->create( $propononorientationprocov58 );
					$success = $this->Orientstruct->Propononorientationprocov58->save( null, array( 'atomic' => false ) ) && $success;
				}
			}
			return $success;
		}

		public function qdDossiersParListe( $commissionep_id, $niveauDecision ) {
			// Doit-on prendre une décision à ce niveau ?
			$themes = $this->Dossierep->Passagecommissionep->Commissionep->WebrsaCommissionep->themesTraites( $commissionep_id );
			$niveauFinal = Hash::get( $themes, Inflector::underscore($this->alias) );
			if( ( $niveauFinal == 'ep' ) && ( $niveauDecision == 'cg' ) ) {
				return array();
			}

			$querydata = array(
				'conditions' => array(
					'Dossierep.themeep' => Inflector::tableize( $this->alias ),
					'Dossierep.id IN ( '.
						$this->Dossierep->Passagecommissionep->sq(
							array(
								'fields' => array(
									'passagescommissionseps.dossierep_id'
								),
								'alias' => 'passagescommissionseps',
								'conditions' => array(
									'passagescommissionseps.commissionep_id' => $commissionep_id
								)
							)
						)
					.' )'
				),
				'contain' => array(
					'Personne' => array(
						'Foyer' => array(
							'Adressefoyer' => array(
								'conditions' => array(
									'Adressefoyer.rgadr' => '01'
								),
								'Adresse'
							)
						)
					),
					$this->alias => array(
						'Orientstruct' => array(
							'Typeorient',
							'Structurereferente',
							'Referent'
						)
					),
					'Passagecommissionep' => array(
						'conditions' => array(
							'Passagecommissionep.commissionep_id' => $commissionep_id
						),
						'Decision'.Inflector::underscore( $this->alias ) => array(
							'Typeorient',
							'Structurereferente',
							'order' => array( 'etape DESC' )
						)
					),
				)
			);

			if( Configure::read( 'Cg.departement' ) == 58 ){
				$querydata['contain'][$this->alias] = array_merge(
					$querydata['contain'][$this->alias],
					array(
						'Decisionpropononorientationprocov58' => array(
							'Passagecov58' => array(
								'Cov58'
							)
						)
					)
				);
			}

			return $querydata;
		}

		public function prepareFormData( $commissionep_id, $datas, $niveauDecision ) {
			// Doit-on prendre une décision à ce niveau ?
			$themes = $this->Dossierep->Passagecommissionep->Commissionep->WebrsaCommissionep->themesTraites( $commissionep_id );
			$niveauFinal = Hash::get( $themes, Inflector::underscore($this->alias) );
			if( ( $niveauFinal == 'ep' ) && ( $niveauDecision == 'cg' ) ) {
				return array();
			}

			$formData = array();
			foreach( $datas as $key => $dossierep ) {
				$formData['Decision'.Inflector::underscore( $this->alias )][$key]['passagecommissionep_id'] = @$datas[$key]['Passagecommissionep'][0]['id'];

				$formData['Decision'.Inflector::underscore( $this->alias )][$key]['id'] = $this->_prepareFormDataDecisionId( $dossierep );

				// On récupère l'orientation en question afin de trouver le typeorient_id, le structurereferente_id et le referent_id s'il existe
				$orientstruct = $this->Orientstruct->find(
					'first',
					array(
						'conditions' => array(
							'Orientstruct.id' => $dossierep['Nonorientationproep58']['orientstruct_id']
						),
						'contain' => false
					)
				);

				// On modifie les enregistrements de cette étape
				if( @$dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['etape'] == $niveauDecision ) {
					$formData['Decision'.Inflector::underscore( $this->alias )][$key] = @$dossierep['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0];

					$formData['Decision'.Inflector::underscore( $this->alias )][$key]['referent_id'] = implode(
						'_',
						array(
							$formData['Decision'.Inflector::underscore( $this->alias )][$key]['structurereferente_id'],
							$formData['Decision'.Inflector::underscore( $this->alias )][$key]['referent_id']
						)
					);

					$formData['Decision'.Inflector::underscore( $this->alias )][$key]['structurereferente_id'] = implode(
						'_',
						array(
							$formData['Decision'.Inflector::underscore( $this->alias )][$key]['typeorient_id'],
							$formData['Decision'.Inflector::underscore( $this->alias )][$key]['structurereferente_id']
						)
					);
				}
				// On ajoute les enregistrements de cette étape
				else {
					if( $niveauDecision == 'ep' ) {
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['typeorient_id'] = $orientstruct['Orientstruct']['typeorient_id'];

						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['referent_id'] = implode(
							'_',
							array(
								$orientstruct['Orientstruct']['structurereferente_id'],
								$orientstruct['Orientstruct']['referent_id']
							)
						);

						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['structurereferente_id'] = implode(
							'_',
							array(
								$orientstruct['Orientstruct']['typeorient_id'],
								$orientstruct['Orientstruct']['structurereferente_id']
							)
						);


					}
					elseif( $niveauDecision == 'cg' ) {
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['decision'] = @$datas[$key]['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['decision'];
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['raisonnonpassage'] = @$datas[$key]['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['raisonnonpassage'];
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['commentaire'] = @$datas[$key]['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['commentaire'];
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['structurereferente_id'] = @$datas[$key]['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['typeorient_id'].'_'.@$datas[$key]['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['structurereferente_id'];
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['typeorient_id'] = @$datas[$key]['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['typeorient_id'];
						$formData['Decision'.Inflector::underscore( $this->alias )][$key]['referent_id'] = @$datas[$key]['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['structurereferente_id'].'_'.@$datas[$key]['Passagecommissionep'][0]['Decision'.Inflector::underscore( $this->alias )][0]['referent_id'];
					}
				}
			}
// debug($formData);
			return $formData;
		}

		/**
		*
		*/

		public function saveDecisions( $data, $niveauDecision ) {
			$success = true;
			if ( isset( $data['Decision'.Inflector::underscore( $this->alias )] ) && !empty( $data['Decision'.Inflector::underscore( $this->alias )] ) ) {
				$success = $this->Dossierep->Passagecommissionep->{'Decision'.Inflector::underscore($this->alias)}->saveAll( Set::extract( $data, '/'.'Decision'.Inflector::underscore( $this->alias ) ), array( 'atomic' => false ) );
				$this->Dossierep->Passagecommissionep->updateAllUnBound(
					array( 'Passagecommissionep.etatdossierep' => '\'decision'.$niveauDecision.'\'' ),
					array( '"Passagecommissionep"."id"' => Set::extract( $data, '/Decision'.Inflector::underscore( $this->alias ).'/passagecommissionep_id' ) )
				);
			}

			return $success;
		}

		/**
		 * Retourne une partie de querydata concernant la thématique pour le PV d'EP.
		 *
		 * @return array
		 */
		public function qdProcesVerbal() {
			$modele = 'Nonorientationproep'.Configure::read( 'Cg.departement' );
			$modeleDecisions = 'Decisionnonorientationproep'.Configure::read( 'Cg.departement' );

			$querydata = array(
				'fields' => array(
					"{$modele}.id",
					"{$modele}.dossierep_id",
					"{$modele}.orientstruct_id",
					"{$modele}.created",
					"{$modele}.modified",
					"{$modele}.user_id",
					"{$modeleDecisions}.id",
					"{$modeleDecisions}.etape",
					"{$modeleDecisions}.decision",
					"{$modeleDecisions}.typeorient_id",
					"{$modeleDecisions}.structurereferente_id",
					"{$modeleDecisions}.commentaire",
					"{$modeleDecisions}.created",
					"{$modeleDecisions}.modified",
					"{$modeleDecisions}.passagecommissionep_id",
					"{$modeleDecisions}.raisonnonpassage",
				),
				'joins' => array(
					array(
						'table'      => Inflector::tableize( $modele ),
						'alias'      => $modele,
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( "{$modele}.dossierep_id = Dossierep.id" ),
					),
					array(
						'table'      => Inflector::tableize( $modeleDecisions ),
						'alias'      => $modeleDecisions,
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							"{$modeleDecisions}.passagecommissionep_id = Passagecommissionep.id",
							"{$modeleDecisions}.etape" => 'ep'
						),
					),
				)
			);

			$modeleDecisionPart = 'decnonopro'.Configure::read( 'Cg.departement' );
			$aliases = array(
				'Typeorient' => "Typeorient{$modeleDecisionPart}",
				'Structurereferente' => "Structurereferentedecision{$modeleDecisionPart}",
			);

			$fields = array_merge(
				$this->Dossierep->Passagecommissionep->{$modeleDecisions}->Typeorient->fields(),
				$this->Dossierep->Passagecommissionep->{$modeleDecisions}->Structurereferente->fields()
			);
			$fields = array_words_replace( $fields, $aliases );
			$querydata['fields'] = array_merge( $querydata['fields'], $fields );


			$joins = array(
				$this->Dossierep->Passagecommissionep->{$modeleDecisions}->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
				$this->Dossierep->Passagecommissionep->{$modeleDecisions}->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
			);
			$joins = array_words_replace( $joins, $aliases );
			$querydata['joins'] = array_merge( $querydata['joins'], $joins );

			return $querydata;
		}

		/**
		* Récupération du courrier de convocation à l'allocataire pour un passage
		* en commission donné.
		*/

		public function getConvocationBeneficiaireEpPdf( $passagecommissionep_id ) {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$datas = Cache::read( $cacheKey );

			if( $datas === false ) {
				$datas = $this->_qdConvocationBeneficiaireEpPdf();

				// Champs supplémentaires
				$datas['querydata']['fields'] = array_merge(
					$datas['querydata']['fields'],
					$this->Orientstruct->fields(),
					$this->Orientstruct->Typeorient->fields(),
					$this->Orientstruct->Structurereferente->fields()
				);

				$datas['querydata']['joins'][] = $this->join( 'Orientstruct' );
				$datas['querydata']['joins'][] = $this->Orientstruct->join( 'Typeorient' );
				$datas['querydata']['joins'][] = $this->Orientstruct->join( 'Structurereferente' );

				Cache::write( $cacheKey, $datas );
			}

			$datas['querydata']['conditions']['Passagecommissionep.id'] = $passagecommissionep_id;
			$gedooo_data = $this->Dossierep->Passagecommissionep->find( 'first', $datas['querydata'] );
			$modeleOdt = $this->_modeleOdtConvocationepBeneficiaire;

			if( empty( $gedooo_data ) ) {
				return false;
			}

			return $this->ged(
				$gedooo_data,
				$modeleOdt,
				false,
				$datas['options']
			);
		}

		/**
		* Récupération de la décision suite au passage en commission d'un dossier
		* d'EP pour un certain niveau de décision.
		*/

		public function getDecisionPdf( $passagecommissionep_id, $user_id = null ) {
			$modele = $this->alias;
			$modeleDecisions = 'Decision'.Inflector::underscore( $this->alias );

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$datas = Cache::read( $cacheKey );

			if( $datas === false ) {
				$datas['querydata'] = $this->_qdDecisionPdf();

				$datas['querydata']['fields'] = array_merge(
					$datas['querydata']['fields'],
					$this->Orientstruct->fields(),
					$this->Orientstruct->Typeorient->fields(),
					$this->Orientstruct->Structurereferente->fields(),
					$this->Orientstruct->Referent->fields()
				);
				$datas['querydata']['joins'][] = $this->join( 'Orientstruct' );
				$datas['querydata']['joins'][] = $this->Orientstruct->join( 'Typeorient' );
				$datas['querydata']['joins'][] = $this->Orientstruct->join( 'Structurereferente' );
				$datas['querydata']['joins'][] = $this->Orientstruct->join( 'Referent' );

				// Nouveau type d'orientation, de structureréférente, ...
				$aliases = array(
					'Typeorient' => "{$modeleDecisions}typeorient",
					'Structurereferente' => "{$modeleDecisions}structurereferente",
				);
				foreach( $aliases as $modelName => $aliasModelName ) {
					$join = array_words_replace( $this->Dossierep->Passagecommissionep->{$modeleDecisions}->join( $modelName, array( 'type' => 'LEFT OUTER' ) ), $aliases );
					$fields = array_words_replace( $this->Dossierep->Passagecommissionep->{$modeleDecisions}->{$modelName}->fields(), $aliases );

					$datas['querydata']['fields'] = array_merge( $datas['querydata']['fields'], $fields );
					$datas['querydata']['joins'][] = $join;
				}

				// Traductions
				$datas['options'] = $this->Dossierep->Passagecommissionep->{$modeleDecisions}->enums();
				$datas['options']['Personne']['qual'] = ClassRegistry::init( 'Option' )->qual();

				Cache::write( $cacheKey, $datas );
			}

			$datas['querydata']['conditions']['Passagecommissionep.id'] = $passagecommissionep_id;
			// INFO: permet de ne pas avoir d'erreur avec les virtualFields aliasés
			$virtualFields = $this->Dossierep->Passagecommissionep->virtualFields;
			$this->Dossierep->Passagecommissionep->virtualFields = array();
			$gedooo_data = $this->Dossierep->Passagecommissionep->find( 'first', $datas['querydata'] );
			$this->Dossierep->Passagecommissionep->virtualFields = $virtualFields;

			if( empty( $gedooo_data ) || !isset( $gedooo_data[$modeleDecisions] ) || empty( $gedooo_data[$modeleDecisions] ) ) {
				return false;
			}

			// Choix du modèle de document
			$decision = $gedooo_data[$modeleDecisions]['decision'];
			$modeleOdt = "{$this->alias}/decision_{$decision}.odt";

			return $this->_getOrCreateDecisionPdf( $passagecommissionep_id, $gedooo_data, $modeleOdt, $datas['options'] );
		}

		/**
		* Fonction retournant un querydata qui va permettre de retrouver des dossiers d'EP
		*/
		public function qdListeDossier( $commissionep_id = null ) {
			$return = array(
				'fields' => array(
					'Dossierep.id',
					'Dossierep.created',
					'Dossierep.themeep',
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Personne.id',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Adresse.nomcom',
					'Typeorient.lib_type_orient',
					'Structurereferente.lib_struc',
					'Orientstruct.date_valid',
					'Passagecommissionep.id',
					'Passagecommissionep.commissionep_id',
					'Passagecommissionep.etatdossierep',
					'Passagecommissionep.heureseance',
				)
			);

			if( !empty( $commissionep_id ) ) {
				$join = array(
					'alias' => 'Dossierep',
					'table' => 'dossierseps',
					'type' => 'INNER',
					'conditions' => array(
						'Dossierep.id = '.$this->alias.'.dossierep_id'
					)
				);
			}
			else {
				$join = array(
					'alias' => $this->alias,
					'table' => Inflector::tableize( $this->alias ),
					'type' => 'INNER',
					'conditions' => array(
						'Dossierep.id = '.$this->alias.'.dossierep_id'
					)
				);
			}

			$return['joins'] = array(
				$join,
				array(
					'alias' => 'Orientstruct',
					'table' => 'orientsstructs',
					'type' => 'INNER',
					'conditions' => array(
						'Orientstruct.id = '.$this->alias.'.orientstruct_id'
					)
				),
				array(
					'alias' => 'Structurereferente',
					'table' => 'structuresreferentes',
					'type' => 'INNER',
					'conditions' => array(
						'Structurereferente.id = Orientstruct.structurereferente_id'
					)
				),
				array(
					'alias' => 'Typeorient',
					'table' => 'typesorients',
					'type' => 'INNER',
					'conditions' => array(
						'Typeorient.id = Orientstruct.typeorient_id'
					)
				),
				array(
					'alias' => 'Personne',
					'table' => 'personnes',
					'type' => 'INNER',
					'conditions' => array(
						'Dossierep.personne_id = Personne.id'
					)
				),
				array(
					'alias' => 'Foyer',
					'table' => 'foyers',
					'type' => 'INNER',
					'conditions' => array(
						'Personne.foyer_id = Foyer.id'
					)
				),
				array(
					'alias' => 'Dossier',
					'table' => 'dossiers',
					'type' => 'INNER',
					'conditions' => array(
						'Foyer.dossier_id = Dossier.id'
					)
				),
				array(
					'alias' => 'Adressefoyer',
					'table' => 'adressesfoyers',
					'type' => 'INNER',
					'conditions' => array(
						'Adressefoyer.foyer_id = Foyer.id',
						'Adressefoyer.rgadr' => '01'
					)
				),
				array(
					'alias' => 'Adresse',
					'table' => 'adresses',
					'type' => 'INNER',
					'conditions' => array(
						'Adressefoyer.adresse_id = Adresse.id'
					)
				),
				array(
					'alias' => 'Passagecommissionep',
					'table' => 'passagescommissionseps',
					'type' => 'LEFT OUTER',
					'conditions' => Set::merge(
						array( 'Passagecommissionep.dossierep_id = Dossierep.id' ),
						empty( $commissionep_id ) ? array() : array(
							'OR' => array(
								'Passagecommissionep.commissionep_id IS NULL',
								'Passagecommissionep.commissionep_id' => $commissionep_id
							)
						)
					)
				)
			);
			return $return;
		}
	}
?>