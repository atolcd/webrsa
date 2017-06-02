<?php
	/**
	 * Code source de la classe Cohortefichecandidature66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Cohortefichecandidature66 s'occupe de la gestion du moteur de
	 * recherche des cohortes de fiches de candidatures du CG 66.
	 *
	 * @package app.Model
	 */
	class Cohortefichecandidature66 extends AppModel
	{
		public $name = 'Cohortefichecandidature66';

		public $useTable = false;

		/**
		 * Traitement des critères du moteur de recherche.
		 *
		 * @param string $statutFiche
		 * @param array $mesCodesInsee
		 * @param boolean $filtre_zone_geo
		 * @param array $criteresfichescandidature
		 * @param mixed $lockedDossiers
		 * @return array
		 */
		public function search( $statutFiche, $mesCodesInsee, $filtre_zone_geo, $criteresfichescandidature, $lockedDossiers ) {
			/// Conditions de base
			$conditions = array(
			);

			if( !empty( $statutFiche ) ) {
				if( $statutFiche == 'Suivifiche::fichesenattente' ) {
					$conditions[] = '( ( ActioncandidatPersonne.positionfiche = \'enattente\' ) )';
				}
				else if( $statutFiche == 'Suivifiche::fichesencours' ) {
					$conditions[] = 'ActioncandidatPersonne.positionfiche = \'encours\' ';
				}
			}

			/// Cohortefichecandidature66 zone géographique
			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );

            /// Dossiers lockés
			if( !empty( $lockedDossiers ) ) {
				if( is_array( $lockedDossiers ) ) {
					$conditions[] = 'Dossier.id NOT IN ( '.implode( ', ', $lockedDossiers ).' )';
				}
				$conditions[] = "NOT {$lockedDossiers}";
			}

			/// Critères
			$action = Set::extract( $criteresfichescandidature, 'Actioncandidat.id' );
			$correspondant = Set::extract( $criteresfichescandidature, 'Actioncandidat.referent_id' );
			$prescripteur = Set::extract( $criteresfichescandidature, 'ActioncandidatPersonne.referent_id' );
			$nomcom = Set::extract( $criteresfichescandidature, 'Adresse.nomcom' );
			$numcom = Set::extract( $criteresfichescandidature, 'Adresse.numcom' );
			$numdemrsa = Set::extract( $criteresfichescandidature, 'Dossier.numdemrsa' );
			$codepartenaire = Set::extract( $criteresfichescandidature, 'Partenaire.codepartenaire' );
			$matricule = Set::extract( $criteresfichescandidature, 'Dossier.matricule' );
			$partenaire = Set::extract( $criteresfichescandidature, 'Partenaire.id' );


			// Critères sur une personne du foyer - nom, prénom, nom de naissance -> FIXME: seulement demandeur pour l'instant
			foreach( array( 'nom', 'prenom', 'nomnai', 'nir' ) as $criterePersonne ) {
				if( isset( $criteresfichescandidature['Personne'][$criterePersonne] ) && !empty( $criteresfichescandidature['Personne'][$criterePersonne] ) ) {
					$conditions[] = 'Personne.'.$criterePersonne.' ILIKE \''.$this->wildcard( $criteresfichescandidature['Personne'][$criterePersonne] ).'\'';
				}
			}

			// Localité adresse
			if( !empty( $nomcom ) ) {
				$conditions[] = 'Adresse.nomcom ILIKE \'%'.Sanitize::clean( $nomcom, array( 'encode' => false ) ).'%\'';
			}


			// Commune au sens INSEE
			if( !empty( $numcom ) ) {
				$conditions[] = 'Adresse.numcom ILIKE \'%'.Sanitize::clean( $numcom, array( 'encode' => false ) ).'%\'';
			}

			// Nom de l'action
            if( !empty( $action ) ) {
                $conditions[] = 'Actioncandidat.id = \''.Sanitize::clean( $action, array( 'encode' => false ) ).'\'';
            }

            // Correspondant de l'action lié à l'action
            if( !empty( $prescripteur ) ) {
                $conditions[] = 'ActioncandidatPersonne.referent_id = \''.Sanitize::clean( $prescripteur, array( 'encode' => false ) ).'\'';
            }

            // Correspondant de l'action lié à l'action
            if( !empty( $correspondant ) ) {
                $conditions[] = 'Actioncandidat.referent_id = \''.Sanitize::clean( $correspondant, array( 'encode' => false ) ).'\'';
            }
            // PArtenaire lié à l'action
            if( !empty( $partenaire ) ) {
                $conditions[] = 'Partenaire.id  = \''.Sanitize::clean( $partenaire, array( 'encode' => false ) ).'\'';
            }
            // Correspondant de l'action lié à l'action
            if( !empty( $codepartenaire ) ) {
                $conditions[] = 'Partenaire.codepartenaire ILIKE \''.$this->wildcard( $codepartenaire ).'\'';
            }

			//Critères sur le dossier de l'allocataire - numdemrsa + matricule
			foreach( array( 'numdemrsa', 'matricule' ) as $critereDossier ) {
				if( isset( $criteresfichescandidature['Dossier'][$critereDossier] ) && !empty( $criteresfichescandidature['Dossier'][$critereDossier] ) ) {
					$conditions[] = 'Dossier.'.$critereDossier.' ILIKE \''.$this->wildcard( $criteresfichescandidature['Dossier'][$critereDossier] ).'\'';
				}
			}


			/// Critères sur la date de signature de la fiche de candidature
			if( isset( $criteresfichescandidature['Actioncandidat']['datesignature'] ) && !empty( $criteresfichescandidature['Actioncandidat']['datesignature'] ) ) {
				$valid_from = ( valid_int( $criteresfichescandidature['Actioncandidat']['datesignature_from']['year'] ) && valid_int( $criteresfichescandidature['Actioncandidat']['datesignature_from']['month'] ) && valid_int( $criteresfichescandidature['Actioncandidat']['datesignature_from']['day'] ) );
				$valid_to = ( valid_int( $criteresfichescandidature['Actioncandidat']['datesignature_to']['year'] ) && valid_int( $criteresfichescandidature['Actioncandidat']['datesignature_to']['month'] ) && valid_int( $criteresfichescandidature['Actioncandidat']['datesignature_to']['day'] ) );
				if( $valid_from && $valid_to ) {
					$conditions[] = 'ActioncandidatPersonne.datesignature BETWEEN \''.implode( '-', array( $criteresfichescandidature['ActioncandidatPersonne']['datesignature_from']['year'], $criteresfichescandidature['ActioncandidatPersonne']['datesignature_from']['month'], $criteresfichescandidature['ActioncandidatPersonne']['datesignature_from']['day'] ) ).'\' AND \''.implode( '-', array( $criteresfichescandidature['ActioncandidatPersonne']['datesignature_to']['year'], $criteresfichescandidature['ActioncandidatPersonne']['datesignature_to']['month'], $criteresfichescandidature['ActioncandidatPersonne']['datesignature_to']['day'] ) ).'\'';
				}
			}


			/// Requête
			$this->Dossier = ClassRegistry::init( 'Dossier' );

			$joins = array(
				array(
					'table'      => 'personnes',
					'alias'      => 'Personne',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Personne.id = ActioncandidatPersonne.personne_id' ),
				),
				array(
					'table'      => 'actionscandidats',
					'alias'      => 'Actioncandidat',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						'Actioncandidat.id = ActioncandidatPersonne.actioncandidat_id'
					),
				),
				array(
					'table'      => 'contactspartenaires',
					'alias'      => 'Contactpartenaire',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Contactpartenaire.id = Actioncandidat.contactpartenaire_id' ),
				),
				array(
					'table'      => 'partenaires',
					'alias'      => 'Partenaire',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Partenaire.id = Contactpartenaire.partenaire_id' ),
				),
				array(
					'table'      => 'referents',
					'alias'      => 'Referent',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Referent.id = ActioncandidatPersonne.referent_id' ),
				),
				array(
					'table'      => 'prestations',
					'alias'      => 'Prestation',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						'Personne.id = Prestation.personne_id',
						'Prestation.natprest = \'RSA\'',
						'( Prestation.rolepers = \'DEM\' OR Prestation.rolepers = \'CJT\' )',
					)
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
					'type'       => 'INNER',
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
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Adresse.id = Adressefoyer.adresse_id' )
				)
			);


			$query = array(
				'fields' => array(
					'ActioncandidatPersonne.id',
					'ActioncandidatPersonne.personne_id',
					'ActioncandidatPersonne.positionfiche',
					'ActioncandidatPersonne.datesignature',
					'ActioncandidatPersonne.bilanvenu',
					'ActioncandidatPersonne.bilanretenu',
					'ActioncandidatPersonne.actioncandidat_id',
					'ActioncandidatPersonne.referent_id',
					'ActioncandidatPersonne.motifsortie_id',
					'ActioncandidatPersonne.infocomplementaire',
					'ActioncandidatPersonne.precisionmotif',
					'Actioncandidat.contactpartenaire_id',
					'Actioncandidat.referent_id',
					'Actioncandidat.name',
					'Partenaire.libstruc',
					'Partenaire.codepartenaire',
					'Dossier.id',
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Dossier.matricule',
					'Personne.id',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.nir',
					'Personne.qual',
					'Personne.nomcomnai',
					'Referent.qual',
					'Referent.nom',
					'Referent.prenom',
					'Adresse.nomcom',
					'Adresse.codepos',
					'Adressefoyer.rgadr',
					'Adresse.numcom'
				),
				'joins' => $joins,
				'contain' => false,
				'conditions' => $conditions,
                'order' => array( 'ActioncandidatPersonne.datesignature ASC' )
			);

			$query = $this->Dossier->Foyer->Personne->PersonneReferent->completeQdReferentParcours( $query, $criteresfichescandidature );

			return $query;
		}
	}
?>