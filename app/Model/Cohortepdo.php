<?php
	/**
	 * Fichier source de la classe Cohortepdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Cohortepdo s'occupe du moteur de recherche des cohortes de PDOs (CG 93).
	 *
	 * @deprecated since 3.0.00
	 *
	 * @package app.Model
	 */
	class Cohortepdo extends AppModel
	{
		public $name = 'Cohortepdo';

		public $useTable = false;

		public $actsAs = array(
			'Conditionnable'
		);

		/**
		 * Traitement du formulaire de recherche concernant les PDOs.
		 *
		 * @param string $statutValidationAvis
		 * @param array $mesCodesInsee La liste des codes INSEE à laquelle est lié l'utilisateur
		 * @param boolean $filtre_zone_geo L'utilisateur est-il limité au niveau des zones géographiques ?
		 * @param array $criterespdo['Search'] Critères du formulaire de recherche
		 * @param mixed $lockedDossiers
		 * @return array
		 */
		public function search( $statutValidationAvis, $mesCodesInsee, $filtre_zone_geo, $criterespdo, $lockedDossiers ) {
			$Situationdossierrsa = ClassRegistry::init( 'Situationdossierrsa' );
			$Personne = ClassRegistry::init( 'Personne' );

			// Conditions de base
			$conditions = array( );
			$conditions['Prestation.rolepers'] = array( 'DEM', 'CJT' );
			$conditions['Prestation.natprest'] = array( 'RSA' );
			$conditions[] = array(
				'OR' => array(
					'Adressefoyer.id IS NULL',
					'Adressefoyer.id IN ( '
						.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01('Adressefoyer.foyer_id')
					.' )'
				)
			);

			if( !empty( $statutValidationAvis ) ) {
				if( $statutValidationAvis == 'Decisionpdo::nonvalide' ) {
					$etatdossier = Set::extract( $criterespdo['Search'], 'Situationdossierrsa.etatdosrsa' );
					if( isset( $criterespdo['Search']['Situationdossierrsa']['etatdosrsa'] ) && !empty( $criterespdo['Search']['Situationdossierrsa']['etatdosrsa'] ) ) {
						$conditions[] = '( Situationdossierrsa.etatdosrsa IN ( \''.implode( '\', \'', $etatdossier ).'\' ) )';
					}
					else {
						$conditions[] = '( Situationdossierrsa.etatdosrsa IN ( \''.implode( '\', \'', $Situationdossierrsa->etatAttente() ).'\' ) )';
					}

					$conditions[] = 'Propopdo.user_id IS NULL';
				}
				else if( $statutValidationAvis == 'Decisionpdo::valide' ) {
					$conditions[] = 'Propopdo.user_id IS NOT NULL';
				}
			}

			/// Dossiers lockés
			if( !empty( $lockedDossiers ) ) {
				if( is_array( $lockedDossiers ) ) {
					$lockedDossiers = implode( ', ', $lockedDossiers );
				}
				$conditions[] = "NOT {$lockedDossiers}";
			}

			$conditions = $this->conditionsAdresse( $conditions, $criterespdo['Search'], $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsDossier( $conditions, $criterespdo['Search'] );
			$conditions = $this->conditionsPersonne( $conditions, $criterespdo['Search'] );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $criterespdo['Search'] );
			$conditions = $this->conditionsSituationdossierrsa( $conditions, $criterespdo['Search'] );

			/// Critères
			$typepdo_id = Set::extract( $criterespdo['Search'], 'Propopdo.typepdo_id' );
			$decisionpdo = Set::extract( $criterespdo['Search'], 'Propopdo.decisionpdo_id' );
			$motifpdo = Set::extract( $criterespdo['Search'], 'Propopdo.motifpdo' );
			$datedecisionpdo = Set::extract( $criterespdo['Search'], 'Propopdo.datedecisionpdo' );
			$gestionnaire = Set::extract( $criterespdo['Search'], 'Propopdo.user_id' );

			$daterevision = Set::extract( $criterespdo['Search'], 'Propopdo.daterevision' );
			$traitementCheck = false;
			if (!empty($daterevision)) {
				$valid_daterevision = ( valid_int( $criterespdo['Search']['Propopdo']['daterevision']['year'] ) && valid_int( $criterespdo['Search']['Propopdo']['daterevision']['month'] ) && valid_int( $criterespdo['Search']['Propopdo']['daterevision']['day'] ) );
				if ($valid_daterevision) {
					$conditions[] = 'Traitementpdo.daterevision BETWEEN \''.implode( '-', array( '1970', '01', '01' ) ).'\' AND \''.implode( '-', array( $criterespdo['Search']['Propopdo']['daterevision']['year'], $criterespdo['Search']['Propopdo']['daterevision']['month'], $criterespdo['Search']['Propopdo']['daterevision']['day'] ) ).'\'';
					$conditions[] = 'Traitementpdo.clos = 0';
					$traitementCheck = true;
				}
			}

			// Type de PDO
			if( !empty( $typepdo_id ) ) {
				$conditions[] = 'Propopdo.typepdo_id = \''.$typepdo_id.'\'';
			}

			// Motif de la PDO
			if( !empty( $motifpdo ) ) {
				$conditions[] = 'Propopdo.motifpdo ILIKE \'%'.Sanitize::clean( $motifpdo, array( 'encode' => false ) ).'%\'';
			}

			// Décision de la PDO
			if( !empty( $decisionpdo ) ) {
				$conditions[] = 'Decisionpropopdo.decisionpdo_id = \''.Sanitize::clean( $decisionpdo, array( 'encode' => false ) ).'\'';
			}

			// Décision CG
			if( !empty( $gestionnaire ) ) {
				$conditions[] = 'Propopdo.user_id = \''.$gestionnaire.'\'';
			}

			/// Critères sur les PDOs - date de décision
			if( isset( $criterespdo['Search']['Propopdo']['datedecisionpdo'] ) && !empty( $criterespdo['Search']['Propopdo']['datedecisionpdo'] ) ) {
				$valid_from = ( valid_int( $criterespdo['Search']['Propopdo']['datedecisionpdo_from']['year'] ) && valid_int( $criterespdo['Search']['Propopdo']['datedecisionpdo_from']['month'] ) && valid_int( $criterespdo['Search']['Propopdo']['datedecisionpdo_from']['day'] ) );
				$valid_to = ( valid_int( $criterespdo['Search']['Propopdo']['datedecisionpdo_to']['year'] ) && valid_int( $criterespdo['Search']['Propopdo']['datedecisionpdo_to']['month'] ) && valid_int( $criterespdo['Search']['Propopdo']['datedecisionpdo_to']['day'] ) );
				if( $valid_from && $valid_to ) {
					$conditions[] = 'Propopdo.datedecisionpdo BETWEEN \''.implode( '-', array( $criterespdo['Search']['Propopdo']['datedecisionpdo_from']['year'], $criterespdo['Search']['Propopdo']['datedecisionpdo_from']['month'], $criterespdo['Search']['Propopdo']['datedecisionpdo_from']['day'] ) ).'\' AND \''.implode( '-', array( $criterespdo['Search']['Propopdo']['datedecisionpdo_to']['year'], $criterespdo['Search']['Propopdo']['datedecisionpdo_to']['month'], $criterespdo['Search']['Propopdo']['datedecisionpdo_to']['day'] ) ).'\'';
				}
			}


			$query = array(
				'fields' => array(
					'Personne.id',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.nir',
					'Personne.qual',
					'Propopdo.user_id',
					'Dossier.id',
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Dossier.matricule',
					'Personne.nomcomnai',
					'Adresse.nomcom',
					'Adresse.codepos',
					'Adresse.numcom',
					'Situationdossierrsa.etatdosrsa',
					'Foyer.id',
					'Propopdo.id',
					'Decisionpropopdo.id',
					'Traitementpdo.id',
					'Adressefoyer.id',
					'Adresse.id',
					'Dossier.id',
					'Situationdossierrsa.id',
					'Prestation.id',
				),
				'joins' => array(
					$Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Propopdo', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$Personne->Propopdo->join( 'Decisionpropopdo', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Propopdo->join( 'Traitementpdo', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) )
				),
				'recursive' => -1,
				'conditions' => $conditions,
				'order' => array( 'Dossier.dtdemrsa ASC' )
			);

			$query = $Situationdossierrsa->Dossier->Foyer->Personne->PersonneReferent->completeQdReferentParcours( $query, $criterespdo['Search'] );

			return $query;
		}
	}
?>