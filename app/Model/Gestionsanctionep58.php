<?php
	/**
	 * Fichier source de la classe Gestionsanctionep58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Gestionsanctionep58 fournit un traitement des filtres de
	 * recherche concernant la gestion des sanctions émises par une EP du CG 58.
	 *
	 * @package app.Model
	 */
	class Gestionsanctionep58 extends AppModel
	{
		public $name = 'Gestionsanctionep58';

		public $useTable = false;

		public $actsAs = array(
			'Conditionnable',
			'Gedooo.Gedooo',
			'ModelesodtConditionnables' => array(
				58 => array(
					'Sanctionep58/finsanction1.odt',
					'Sanctionep58/finsanction2.odt',
					'Sanctionrendezvousep58/finsanction1.odt',
					'Sanctionrendezvousep58/finsanction2.odt',
				)
			)
		);

		/**
		 * Moteur de recherche de sanctions.
		 *
		 * @param string $statutSanctionep
		 * @param array $criteressanctionseps
		 * @param array $mesCodesInsee
		 * @param boolean $filtre_zone_geo
		 * @param string|array $lockedDossiers
		 * @return array
		 */
		public function search( $statutSanctionep, $criteressanctionseps, $mesCodesInsee, $filtre_zone_geo, $lockedDossiers = null ) {
			$Personne = ClassRegistry::init( 'Personne' );
			$Ep = ClassRegistry::init( 'Ep' );

			/// Conditions de base
			$conditions = $Ep->sqRestrictionsZonesGeographiques(
				'Commissionep.ep_id',
				$filtre_zone_geo,
				$mesCodesInsee
			);


			if( !empty( $statutSanctionep ) ) {
				if( $statutSanctionep == 'Gestion::traitement' ) {
					if( !empty( $criteressanctionseps['Decision']['sanction'] ) ) {
						if( $criteressanctionseps['Decision']['sanction'] == 'N' ) {
							$conditions[] = array(
								'AND' => array(
									'Decisionsanctionep58.arretsanction IS NULL',
									'Decisionsanctionrendezvousep58.arretsanction IS NULL'
								)
							);
						}
						else {
							$conditions[] = array(
								'OR' => array(
									'Decisionsanctionep58.arretsanction IS NOT NULL',
									'Decisionsanctionrendezvousep58.arretsanction IS NOT NULL'
								)
							);
						}
					}
				}
				else if( $statutSanctionep == 'Gestion::visualisation' ) {
					$conditions[] = array(
						'OR' => array(
							'Decisionsanctionep58.arretsanction IS NOT NULL',
							'Decisionsanctionrendezvousep58.arretsanction IS NOT NULL'
						)
					);
				}
			}

			// Il faut que la décision1 ou la décision 2 soit une sanction
			$conditions[] = array(
				'OR' => array(
					'Decisionsanctionep58.decision' => 'sanction',
					'Decisionsanctionep58.decision2' => 'sanction',
					'Decisionsanctionrendezvousep58.decision' => 'sanction',
					'Decisionsanctionrendezvousep58.decision2' => 'sanction'
				)
			);

			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsAdresse( $conditions, $criteressanctionseps, $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsDossier( $conditions, $criteressanctionseps );
			$conditions = $this->conditionsPersonne( $conditions, $criteressanctionseps );
			$conditions = $this->conditionsSituationdossierrsa( $conditions, $criteressanctionseps );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $criteressanctionseps );

			/// Dossiers lockés
			if( !empty( $lockedDossiers ) ) {
				if( is_array( $lockedDossiers ) ) {
					$conditions[] = 'Dossier.id NOT IN ( '.implode( ', ', $lockedDossiers ).' )';
				}
				$conditions[] = "NOT {$lockedDossiers}";
			}

			// Conditions pour les jointures
			$conditions[] = array(
				'OR' => array(
					'Adressefoyer.id IS NULL',
					'Adressefoyer.id IN ( '
						.$Personne->Foyer->Adressefoyer->sqDerniereRgadr01('Adressefoyer.foyer_id')
					.' )'
				)
			);


			// Le dernier passage d'un dossier d'EP
			$conditions[] = 'Passagecommissionep.id IN ( '.$Personne->Dossierep->Passagecommissionep->sqDernier().' )';

			// Un passage traité pour une commission traitée
			$conditions['Commissionep.etatcommissionep'] = 'traite';
			$conditions['Passagecommissionep.etatdossierep'] = 'traite';

			// Doit-on traiter seulement une des deux thématiques ou les deux ?
			if( isset( $criteressanctionseps['Dossierep']['themeep'] ) && !empty( $criteressanctionseps['Dossierep']['themeep'] ) ) {
				$conditions['Dossierep.themeep'] = $criteressanctionseps['Dossierep']['themeep'];
			}
			else {
				$conditions['Dossierep.themeep'] = array( 'sanctionseps58', 'sanctionsrendezvouseps58' );
			}


			if ( isset($criteressanctionseps['Ep']['regroupementep_id']) && !empty($criteressanctionseps['Ep']['regroupementep_id']) ) {
				$conditions[] = array('Ep.regroupementep_id'=>$criteressanctionseps['Ep']['regroupementep_id']);
			}

			if ( isset($criteressanctionseps['Commissionep']['name']) && !empty($criteressanctionseps['Commissionep']['name']) ) {
				$conditions[] = array('Commissionep.name'=>$criteressanctionseps['Commissionep']['name']);
			}

			if ( isset($criteressanctionseps['Commissionep']['identifiant']) && !empty($criteressanctionseps['Commissionep']['identifiant']) ) {
				$conditions[] = array('Commissionep.identifiant'=>$criteressanctionseps['Commissionep']['identifiant']);
			}

			if ( isset($criteressanctionseps['Structurereferente']['ville']) && !empty($criteressanctionseps['Structurereferente']['ville']) ) {
				$conditions[] = array('Commissionep.villeseance'=>$criteressanctionseps['Structurereferente']['ville']);
			}

			/// Critères sur le Comité - date du comité
			if( isset( $criteressanctionseps['Commissionep']['dateseance'] ) && !empty( $criteressanctionseps['Commissionep']['dateseance'] ) ) {
				$valid_from = ( valid_int( $criteressanctionseps['Commissionep']['dateseance_from']['year'] ) && valid_int( $criteressanctionseps['Commissionep']['dateseance_from']['month'] ) && valid_int( $criteressanctionseps['Commissionep']['dateseance_from']['day'] ) );
				$valid_to = ( valid_int( $criteressanctionseps['Commissionep']['dateseance_to']['year'] ) && valid_int( $criteressanctionseps['Commissionep']['dateseance_to']['month'] ) && valid_int( $criteressanctionseps['Commissionep']['dateseance_to']['day'] ) );
				if( $valid_from && $valid_to ) {
					$conditions[] = 'Commissionep.dateseance BETWEEN \''.implode( '-', array( $criteressanctionseps['Commissionep']['dateseance_from']['year'], $criteressanctionseps['Commissionep']['dateseance_from']['month'], $criteressanctionseps['Commissionep']['dateseance_from']['day'] ) ).'\' AND \''.implode( '-', array( $criteressanctionseps['Commissionep']['dateseance_to']['year'], $criteressanctionseps['Commissionep']['dateseance_to']['month'], $criteressanctionseps['Commissionep']['dateseance_to']['day'] ) ).'\'';
				}
			}


			$query = array(
				'fields' => array_merge(
					$Personne->fields(),
					$Personne->Foyer->fields(),
					$Personne->Foyer->Adressefoyer->fields(),
					$Personne->Foyer->Adressefoyer->Adresse->fields(),
					$Personne->Foyer->Dossier->fields(),
					$Personne->Dossierep->fields(),
					$Personne->Dossierep->Passagecommissionep->fields(),
					$Personne->Dossierep->Passagecommissionep->Decisionsanctionep58->fields(),
					$Personne->Dossierep->Passagecommissionep->Decisionsanctionrendezvousep58->fields(),
					$Personne->Dossierep->Passagecommissionep->Commissionep->fields(),
					$Personne->Dossierep->Passagecommissionep->Commissionep->Ep->fields(),
					$Personne->Dossierep->Passagecommissionep->Commissionep->Ep->Regroupementep->fields(),
					array(
						$Personne->Dossierep->Passagecommissionep->Decisionsanctionep58->sqVirtualField( 'impressionfin1' ),
						$Personne->Dossierep->Passagecommissionep->Decisionsanctionep58->sqVirtualField( 'impressionfin2' ),
						$Personne->Dossierep->Passagecommissionep->Decisionsanctionrendezvousep58->sqVirtualField( 'impressionfin1' ),
						$Personne->Dossierep->Passagecommissionep->Decisionsanctionrendezvousep58->sqVirtualField( 'impressionfin2' )
					)
				),
				'joins'=>array(
					$Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Prestation', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Dossierep', array( 'type' => 'INNER' ) ),
					$Personne->Dossierep->join( 'Passagecommissionep', array( 'type' => 'INNER' ) ),
					$Personne->Dossierep->Passagecommissionep->join( 'Commissionep', array( 'type' => 'INNER' ) ),
					$Personne->Dossierep->Passagecommissionep->join( 'Decisionsanctionep58', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Dossierep->Passagecommissionep->join( 'Decisionsanctionrendezvousep58', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Dossierep->Passagecommissionep->Commissionep->join( 'Ep', array( 'type' => 'INNER' ) ),
					$Personne->Dossierep->Passagecommissionep->Commissionep->Ep->join( 'Regroupementep', array( 'type' => 'INNER' ) )
				),
				'contain' => false,
// 				'order' => array( '"Commissionep"."dateseance" ASC' ),
				'conditions' => $conditions
			);

			$query = $Personne->PersonneReferent->completeQdReferentParcours( $query, $criteressanctionseps );

			return $query;
		}

		/**
		 * Retourne la liste des thèmes d'EP concernant les sanctions au CG 58.
		 *
		 * @return array
		 */
		public function themes() {
			return array(
				'sanctionseps58' => __d( 'dossierep', 'ENUM::THEMEEP::sanctionseps58', true ),
				'sanctionsrendezvouseps58' =>  __d( 'dossierep', 'ENUM::THEMEEP::sanctionsrendezvouseps58', true ),
			);
		}

		/**
		 * Retourne le querydata nécessaire à l'impression des courriers d'EP pour
		 * sanction à destination de l'allocataire.
		 *
		 * @param integer $id
		 * @param integer $user_id
		 * @return array
		 */
		public function getQuerydataForPdf( $passagecommissionep_id ) {
			$Personne = ClassRegistry::init( 'Personne' );

			$querydata = array(
				'fields' => array_merge(
					$Personne->fields(),
					$Personne->Foyer->fields(),
					$Personne->Foyer->Adressefoyer->fields(),
					$Personne->Foyer->Adressefoyer->Adresse->fields(),
					$Personne->Foyer->Dossier->fields(),
					$Personne->Dossierep->fields(),
					$Personne->Dossierep->Passagecommissionep->fields(),
					$Personne->Dossierep->Passagecommissionep->Decisionsanctionep58->fields(),
					$Personne->Dossierep->Passagecommissionep->Decisionsanctionrendezvousep58->fields(),
					$Personne->Dossierep->Passagecommissionep->Commissionep->fields(),
					$Personne->Dossierep->Passagecommissionep->Commissionep->Ep->fields(),
					$Personne->Dossierep->Passagecommissionep->Commissionep->Ep->Regroupementep->fields(),
					array(
						$Personne->Dossierep->Passagecommissionep->Decisionsanctionep58->sqVirtualField( 'impressionfin1' ),
						$Personne->Dossierep->Passagecommissionep->Decisionsanctionep58->sqVirtualField( 'impressionfin2' ),
						$Personne->Dossierep->Passagecommissionep->Decisionsanctionrendezvousep58->sqVirtualField( 'impressionfin1' ),
						$Personne->Dossierep->Passagecommissionep->Decisionsanctionrendezvousep58->sqVirtualField( 'impressionfin2' )
					)
				),
				'joins' => array(
					$Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Dossierep', array( 'type' => 'INNER' ) ),
					$Personne->Dossierep->join( 'Passagecommissionep', array( 'type' => 'INNER' ) ),
					$Personne->Dossierep->Passagecommissionep->join( 'Commissionep', array( 'type' => 'INNER' ) ),
					$Personne->Dossierep->Passagecommissionep->join( 'Decisionsanctionep58', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Dossierep->Passagecommissionep->join( 'Decisionsanctionrendezvousep58', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Dossierep->Passagecommissionep->Commissionep->join( 'Ep', array( 'type' => 'INNER' ) ),
					$Personne->Dossierep->Passagecommissionep->Commissionep->Ep->join( 'Regroupementep', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					'Passagecommissionep.id' => $passagecommissionep_id,
					'OR' => array(
						'Adressefoyer.id IS NULL',
						'Adressefoyer.id IN ( '.$Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
					)
				),
				'contain' => false
			);
			return $querydata;
		}

		/**
		 * Retourne le PDF par défaut généré pour l'impression du courrier de fin de sanciton 1 ou 2.
		 *
		 * @param type $id Id de la personne
		 * @param type $user_id Id de l'utilisateur connecté
		 * @return string
		 */
		public function getPdfSanction( $niveauSanction, $passagecommissionep_id, $themeep, $user_id ) {
			$Option = ClassRegistry::init( 'Option' );
			$Personne = ClassRegistry::init( 'Personne' );

			$options = array(
				'Personne' => array(
					'qual' => $Option->qual()
				)
			);

			$querydata = $this->getQuerydataForPdf( $passagecommissionep_id );

			$personne = $Personne->find( 'first', $querydata );

			if( empty( $personne ) ) {
				$this->cakeError( 'error404' );
			}

			/// Récupération de l'utilisateur
			$user = ClassRegistry::init( 'User' )->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => $user_id
					),
					'contain' => false
				)
			);
			$personne['User'] = $user['User'];

			$modeleName = Inflector::classify( $themeep );
			$modeleDecisionName = Inflector::classify( "decisions{$themeep}" );

			if( !$personne[$modeleDecisionName]["impressionfin{$niveauSanction}"] ) {
				return null;
			}

			$modeleodt = "{$modeleName}/finsanction{$niveauSanction}.odt";

			return $this->ged(
				$personne,
				$modeleodt,
				false,
				$options
			);
		}


		/**
		 * Retourne les PDF de sanction pour la cohorte.
		 *
		 * @param string $search Les critères de recherche de la cohorte
		 * @param integer $user_id L'id de l'utilisateur qui fait l'impression
		 * @return string
		 */
		public function getCohortePdfSanction( $niveauSanction, $statutSanctionep, $mesCodesInsee, $filtre_zone_geo, $search, $page, $user_id ) {
			$querydata = $this->search( $statutSanctionep, $search, $mesCodesInsee, $filtre_zone_geo, null );

			$querydata['limit'] = 100;
			$querydata['offset'] = ( ( $page ) <= 1 ? 0 : ( $querydata['limit'] * ( $page - 1 ) ) );

			$Personne = ClassRegistry::init( 'Personne' );
			$cohorte = $Personne->find( 'all', $querydata );

			$pdfs = array();
			foreach( $cohorte as $result ) {
				$modeleDecisionName = Inflector::classify( "decisions{$result['Dossierep']['themeep']}" );
				if( $result[$modeleDecisionName]["impressionfin{$niveauSanction}"] ) {
					$passagecommissionep_id = $result['Passagecommissionep']['id'];
					$pdf = $this->getPdfSanction( $niveauSanction, $passagecommissionep_id, $result['Dossierep']['themeep'], $user_id );
					if( !empty( $pdf ) ) {
						$pdfs[] = $pdf;
					}
				}
			}

			return $pdfs;
		}

		/**
		 * Préparation des données pour le formulaire de traitement.
		 *
		 * @param array $datas
		 * @return array
		 */
		public function prepareFormDataTraitement( array $datas ) {
			$return = array();

			if( !empty( $datas ) ) {
				foreach( $datas as $i => $data ) {
					$modeleDecisionName = Inflector::classify( "decisions{$data['Dossierep']['themeep']}" );
					$return[$modeleDecisionName][$i] = Set::classicExtract( $data, $modeleDecisionName );
				}
			}

			return $return;
		}
	}
?>