<?php
	/**
	 * Code source de la classe Cohortedossierpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Cohortedossierpcg66 ...
	 *
	 * @package app.Model
	 */
	class Cohortedossierpcg66 extends AppModel
	{
		public $name = 'Cohortedossierpcg66';

		public $useTable = false;

		public $actsAs = array(
			'Conditionnable'
		);
		/**
		*
		*/

		public function search( $statutAffectation, $mesCodesInsee, $filtre_zone_geo, $criteresdossierspcgs66, $lockedDossiers = null ) {
			/// Conditions de base
			$conditions = array(
			);

			if( !empty( $statutAffectation ) ) {
				if( $statutAffectation == 'Affectationdossierpcg66::enattenteaffectation' ) {
					$conditions[] = '( ( Dossierpcg66.etatdossierpcg = \'attaffect\' ) AND ( Dossierpcg66.poledossierpcg66_id IS NULL ) )';
				}
				else if( $statutAffectation == 'Affectationdossierpcg66::affectes' ) {
					$conditions[] = 'Dossierpcg66.etatdossierpcg = \'attinstr\' ';
				}
				else if( $statutAffectation == 'Affectationdossierpcg66::aimprimer' ) {
					$conditions[] = array(
							'Decisiondossierpcg66.etatdossierpcg IS NULL',
							'Dossierpcg66.etatdossierpcg' => 'decisionvalid',
							'Dossierpcg66.dateimpression IS NULL',
					);
				}
				else if( $statutAffectation == 'Affectationdossierpcg66::attentetransmission' ) {
					$conditions[] = '( Dossierpcg66.etatdossierpcg IN ( \'decisionvalid\' ) ) AND ( Decisiondossierpcg66.dossierpcg66_id IS NOT NULL ) AND ( Decisiondossierpcg66.validationproposition = \'O\') AND ( Dossierpcg66.etatdossierpcg NOT IN ( \'transmisop\' ) ) ';
				}
				else if( $statutAffectation == 'Affectationdossierpcg66::atransmettre' ) {
					$conditions[] = '( Dossierpcg66.etatdossierpcg = \'atttransmisop\' ) AND ( Dossierpcg66.dateimpression IS NOT NULL  ) AND ( Dossierpcg66.istransmis = \'0\' )';
				}
			}

			 // Dossiers lockés
			if( !empty( $lockedDossiers ) ) {
				if( is_array( $lockedDossiers ) ) {
					$conditions[] = 'Dossier.id NOT IN ( '.implode( ', ', $lockedDossiers ).' )';
				}
				$conditions[] = "NOT {$lockedDossiers}";
			}

			/// Critères
			$originepdo_id = Set::extract( $criteresdossierspcgs66, 'Search.Originepdo.libelle' );
			$serviceinstructeur_id = Set::extract( $criteresdossierspcgs66, 'Search.Dossierpcg66.serviceinstructeur_id' );
			$typepdo_id = Set::extract( $criteresdossierspcgs66, 'Search.Typepdo.libelle' );
			$orgpayeur = Set::extract( $criteresdossierspcgs66, 'Search.Dossierpcg66.orgpayeur' );
			$gestionnaire = Set::extract( $criteresdossierspcgs66, 'Search.Dossierpcg66.user_id' );
			$poledossierpcg66_id = Set::extract( $criteresdossierspcgs66, 'Search.Dossierpcg66.poledossierpcg66_id' );


			$conditions = $this->conditionsAdresse( $conditions, $criteresdossierspcgs66['Search'], $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsDossier( $conditions, $criteresdossierspcgs66['Search'] );
			$conditions = $this->conditionsPersonne( $conditions, $criteresdossierspcgs66['Search'] );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $criteresdossierspcgs66['Search'] );
			$conditions = $this->conditionsDates($conditions, $criteresdossierspcgs66['Search'], 'Dossierpcg66.dateaffectation');


			/// Critères sur la date de signature de la fiche de candidature
			if( isset( $criteresdossierspcgs66['Search']['Dossierpcg66']['datereceptionpdo'] ) && !empty( $criteresdossierspcgs66['Search']['Dossierpcg66']['datereceptionpdo'] ) ) {
				$valid_from = ( valid_int( $criteresdossierspcgs66['Search']['Dossierpcg66']['datereceptionpdo_from']['year'] ) && valid_int( $criteresdossierspcgs66['Search']['Dossierpcg66']['datereceptionpdo_from']['month'] ) && valid_int( $criteresdossierspcgs66['Search']['Dossierpcg66']['datereceptionpdo_from']['day'] ) );
				$valid_to = ( valid_int( $criteresdossierspcgs66['Search']['Dossierpcg66']['datereceptionpdo_to']['year'] ) && valid_int( $criteresdossierspcgs66['Search']['Dossierpcg66']['datereceptionpdo_to']['month'] ) && valid_int( $criteresdossierspcgs66['Search']['Dossierpcg66']['datereceptionpdo_to']['day'] ) );
				if( $valid_from && $valid_to ) {
					$conditions[] = 'Dossierpcg66.datereceptionpdo BETWEEN \''.implode( '-', array( $criteresdossierspcgs66['Search']['Dossierpcg66']['datereceptionpdo_from']['year'], $criteresdossierspcgs66['Search']['Dossierpcg66']['datereceptionpdo_from']['month'], $criteresdossierspcgs66['Search']['Dossierpcg66']['datereceptionpdo_from']['day'] ) ).'\' AND \''.implode( '-', array( $criteresdossierspcgs66['Search']['Dossierpcg66']['datereceptionpdo_to']['year'], $criteresdossierspcgs66['Search']['Dossierpcg66']['datereceptionpdo_to']['month'], $criteresdossierspcgs66['Search']['Dossierpcg66']['datereceptionpdo_to']['day'] ) ).'\'';
				}
			}

			// Critères sur un dossier pcg - originepdo, typepdo, serviecinstructeur, orgpayeur
			foreach( array( 'serviceinstructeur_id', 'orgpayeur' ) as $criteredossierpcg66 ) {
				if( isset( $criteresdossierspcgs66['Search']['Dossierpcg66'][$criteredossierpcg66] ) && !empty( $criteresdossierspcgs66['Search']['Dossierpcg66'][$criteredossierpcg66] ) ) {
					$conditions[] = 'Dossierpcg66.'.$criteredossierpcg66.' = \''.Sanitize::clean( $criteresdossierspcgs66['Search']['Dossierpcg66'][$criteredossierpcg66], array( 'encode' => false ) ).'\'';
				}
			}

			// Commune au sens INSEE
			if( !empty( $originepdo_id ) ) {
				$conditions[] = 'Dossierpcg66.originepdo_id = \''.Sanitize::clean( $originepdo_id, array( 'encode' => false ) ).'\'';
			}

			// Commune au sens INSEE
			if( !empty( $typepdo_id ) ) {
				$conditions[] = 'Dossierpcg66.typepdo_id = \''.Sanitize::clean( $typepdo_id, array( 'encode' => false ) ).'\'';
			}

			// Gestionnaire de la PDO
			if (!empty($gestionnaire)) {
				$conditions[] = 'Dossierpcg66.user_id IN ( \'' . implode('\', \'', $gestionnaire) . '\' )';
			}


            // Filtre sur l'état du dossier PCG
            $etatdossierpcg = Set::extract( $criteresdossierspcgs66, 'Search.Dossierpcg66.etatdossierpcg' );
			if( isset( $criteresdossierspcgs66['Search']['Dossierpcg66']['etatdossierpcg'] ) && !empty( $criteresdossierspcgs66['Search']['Dossierpcg66']['etatdossierpcg'] ) ) {
				$conditions[] = '( Dossierpcg66.etatdossierpcg IN ( \''.implode( '\', \'', $etatdossierpcg ).'\' ) )';
			}

			// Pôle chargé du dossier PCG
			if( !empty( $poledossierpcg66_id ) ) {
				$conditions[] = 'Dossierpcg66.poledossierpcg66_id IN ( \'' . implode('\', \'', $poledossierpcg66_id) . '\' )';
			}

			/// Requête
			$this->Dossier = ClassRegistry::init( 'Dossier' );



			$joins = array(
				array(
					'table'      => 'foyers',
					'alias'      => 'Foyer',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Dossierpcg66.foyer_id = Foyer.id' )
				),
				array(
					'table'      => 'personnes',
					'alias'      => 'Personne',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						'Personne.foyer_id = Foyer.id',
						'Personne.id IN (
							'.ClassRegistry::init( 'Personne' )->WebrsaPersonne->sqResponsableDossierUnique('Foyer.id').'
						)'
					)
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
						'Adressefoyer.id IN (
							'.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01('Adressefoyer.foyer_id').'
						)'
					)
				),
				array(
					'table'      => 'adresses',
					'alias'      => 'Adresse',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array( 'Adresse.id = Adressefoyer.adresse_id' )
				),
				array(
					'table'      => 'originespdos',
					'alias'      => 'Originepdo',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Originepdo.id = Dossierpcg66.originepdo_id' )
				),
				array(
					'table'      => 'typespdos',
					'alias'      => 'Typepdo',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Typepdo.id = Dossierpcg66.typepdo_id' )
				),
				array(
					'table'      => 'servicesinstructeurs',
					'alias'      => 'Serviceinstructeur',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array( 'Serviceinstructeur.id = Dossierpcg66.serviceinstructeur_id' )
				),
				array(
					'table'      => 'decisionsdossierspcgs66',
					'alias'      => 'Decisiondossierpcg66',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						'Decisiondossierpcg66.dossierpcg66_id = Dossierpcg66.id',
						'Decisiondossierpcg66.id IN ('
							.ClassRegistry::init( 'Decisiondossierpcg66' )->sq(
								array(
									'alias' => 'decisionsdossierspcgs66',
									'fields' => array( 'decisionsdossierspcgs66.id' ),
									'conditions' => array(
										'decisionsdossierspcgs66.dossierpcg66_id = Dossierpcg66.id'
									),
									'order' => array( 'decisionsdossierspcgs66.modified DESC' ),
									'limit' => 1
								)
							)
						.')'
					)
				),
				$this->Dossier->Foyer->Personne->join( 'Prestation', 
					array( 
						'type' => 'INNER',
						'conditions' => array( 'Prestation.rolepers' => 'DEM' )
					)
				)
			);

			$query = array(
				'fields' => array(
					'Dossierpcg66.id',
					'Dossierpcg66.foyer_id',
					'Dossierpcg66.etatdossierpcg',
					'Dossierpcg66.originepdo_id',
					'Dossierpcg66.orgpayeur',
					'Dossierpcg66.typepdo_id',
					'Dossierpcg66.serviceinstructeur_id',
					'Dossierpcg66.datereceptionpdo',
					'Dossierpcg66.user_id',
					'Dossierpcg66.poledossierpcg66_id',
					'Dossierpcg66.istransmis',
					'Dossierpcg66.datetransmission',
					'Dossierpcg66.dateaffectation',
					'Originepdo.libelle',
					'Typepdo.libelle',
					'Serviceinstructeur.lib_service',
					'Dossier.id',
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Dossier.matricule',
					'Personne.id',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.nir',
					'Personne.qual',
					'Personne.nomcomnai',
					'Adresse.nomcom',
					'Adresse.codepos',
					'Adressefoyer.rgadr',
					'Adresse.numcom',
					'Decisiondossierpcg66.id',
					'Decisiondossierpcg66.datetransmissionop',
					'Decisiondossierpcg66.datevalidation'
				),
				'joins' => $joins,
				'contain' => false,
				'conditions' => $conditions
			);
			
			if( $statutAffectation === 'Affectationdossierpcg66::aimprimer' ) {
				$query['order'] = array( 'Foyer.id' );
			}

			$query = $this->Dossier->Foyer->Personne->PersonneReferent->completeQdReferentParcours( $query, $criteresdossierspcgs66['Search'] );

			return $query;
		}
	}
?>