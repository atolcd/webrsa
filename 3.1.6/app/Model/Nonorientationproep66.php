<?php
	/**
	 * Code source de la classe Nonorientationproep66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * INFO: cette thématique n'est actuellement pas utilisée lorsqu'on crée un regroupementep, même si
	 * la thématique figure dans l'enum type_themeep.
	 */
	require_once( ABSTRACTMODELS.'Nonorientationproep.php' );

	/**
	 * La classe Nonorientationproep66 ...
	 *
	 * @package app.Model
	 * @deprecated since version 3.0.0
	 * @see WebrsaRechercheNonorientationproep
	 */
	class Nonorientationproep66 extends Nonorientationproep
	{
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
		*
		*/

		public function finaliser( $commissionep_id, $etape, $user_id ) {
			$commissionep = $this->Dossierep->Passagecommissionep->Commissionep->find(
				'first',
				array(
					'conditions' => array( 'Commissionep.id' => $commissionep_id ),
					'contain' => array(
						'Ep' => array(
							'Regroupementep'
						)
					)
				)
			);

			$niveauDecisionFinale = $commissionep['Ep']['Regroupementep'][Inflector::underscore( $this->alias )];

			$dossierseps = $this->find(
				'all',
				array(
					'conditions' => array(
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
						.' )',
						'Dossierep.themeep' => Inflector::tableize( $this->alias )
					),
					'contain' => array(
						'Dossierep' => array(
							'Passagecommissionep' => array(
								'conditions' => array(
									'Passagecommissionep.commissionep_id' => $commissionep_id
								),
								'Decisionnonorientationproep66' => array(
									'conditions' => array(
										'Decisionnonorientationproep66.etape' => $etape
									)
								)
							)
						)
					)
				)
			);

			$success = true;

			if( $niveauDecisionFinale == "decision{$etape}" ) {
				foreach( $dossierseps as $dossierep ) {
					if( !isset( $dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep66'][0]['decision'] ) || empty( $dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep66'][0]['decision'] ) ) {
						$success = false;
					}
					elseif ( $dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep66'][0]['decision'] == 'reorientation' ) {
						list($date_propo, $heure_propo) = explode( ' ', $dossierep['Nonorientationproep66']['created'] );
						list($date_valid, $heure_valid) = explode( ' ', $commissionep['Commissionep']['dateseance'] );

						$rgorient = $this->Orientstruct->WebrsaOrientstruct->rgorientMax( $dossierep['Dossierep']['personne_id'] ) + 1;
						$origine = ( $rgorient > 1 ? 'reorientation' : 'cohorte' );

						$orientstruct = array(
							'Orientstruct' => array(
								'personne_id' => $dossierep['Dossierep']['personne_id'],
								'typeorient_id' => @$dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep66'][0]['typeorient_id'],
								'structurereferente_id' => @$dossierep['Dossierep']['Passagecommissionep'][0]['Decisionnonorientationproep66'][0]['structurereferente_id'],
								'date_propo' => $date_propo,
								'date_valid' => $date_valid,
								'statut_orient' => 'Orienté',
								'rgorient' => $rgorient,
								'origine' => $origine,
								'etatorient' => 'decision',
								'user_id' => $dossierep['Nonorientationproep66']['user_id']
							)
						);

						$this->Orientstruct->create( $orientstruct );
						$success = $this->Orientstruct->save() && $success;

						$success = $this->Orientstruct->generatePdf( $this->Orientstruct->id, $dossierep['Nonorientationproep66']['user_id'] ) && $success;
					}
				}
			}

			return $success;
		}

		/**
		* Fonction retournant un querydata qui va permettre de retrouver des dossiers d'EP
		*/
		public function qdListeDossier( $commissionep_id = null ) {
			$querydata = parent::qdListeDossier( $commissionep_id );

				$joins = array(
					$this->Dossierep->Nonorientationproep66->join( 'Decisionpropononorientationprocov58' ),
					$this->Dossierep->Nonorientationproep66->Decisionpropononorientationprocov58->join( 'Passagecov58' ),
					$this->Dossierep->Nonorientationproep66->Decisionpropononorientationprocov58->Passagecov58->join( 'Cov58' )
				);

				$querydata['joins'] = array_merge( $querydata['joins'], $joins );



			return $querydata;
		}
		
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
			// Filtre sur la caton et la comune
			$conditions = $this->conditionsAdresse( array(), $datas, $filtre_zone_geo, $mesCodesInsee );

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
							SELECT CAST( decisions'.Inflector::tableize( $this->alias ).'.modified AS DATE )
								FROM decisions'.Inflector::tableize( $this->alias ).'
									INNER JOIN passagescommissionseps ON ( decisions'.Inflector::tableize( $this->alias ).'.passagecommissionep_id = passagescommissionseps.id )
									INNER JOIN dossierseps ON ( passagescommissionseps.dossierep_id = dossierseps.id )
								ORDER BY modified DESC
								LIMIT 1
						) ) <= '.Configure::read( $this->alias.'.delaiCreationContrat' ).'
			)';


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
				'order' => array( $this->Orientstruct->Personne->Contratinsertion->sqVirtualField( 'nbjours', false )." DESC" )
			);

			$querydata = $this->Orientstruct->Personne->PersonneReferent->completeQdReferentParcours( $querydata, $datas['Filtre'] );

			return $querydata;
		}
	}
?>