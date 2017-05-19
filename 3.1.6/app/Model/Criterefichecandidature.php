<?php
	/**
	 * Code source de la classe Criterefichecandidature.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Moteur de recherche pour les fiche de candidature (actioncandidat_personne).
	 *
	 * @package app.Model
	 * @deprecated since version 3.0.0
	 * @see WebrsaRechercheActioncandidatPersonne
	 */
	class Criterefichecandidature extends AppModel
	{
		public $name = 'Criterefichecandidature';

		public $useTable = false;

		public $actsAs = array(
			'Conditionnable',
			'Formattable' => array(
				'suffix' => array( 'actioncandidat_id' ),
			)
		);

		public function search( $mesCodesInsee, $filtre_zone_geo, $criteresfichescandidature ) {
			/// Conditions de base

			$conditions = array();
            $ActioncandidatPersonne = ClassRegistry::init( 'ActioncandidatPersonne' );

			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsAdresse( $conditions, $criteresfichescandidature, $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsPersonneFoyerDossier( $conditions, $criteresfichescandidature );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $criteresfichescandidature );

            $conditions[] = array(
                array(
                    'OR' => array(
                        'Adressefoyer.id IS NULL',
                        'Adressefoyer.id IN ( '.$ActioncandidatPersonne->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
                    )
                )
            );

			if ( isset($criteresfichescandidature['ActioncandidatPersonne']['actioncandidat_id']) && !empty($criteresfichescandidature['ActioncandidatPersonne']['actioncandidat_id']) ) {
				$conditions[] = array('ActioncandidatPersonne.actioncandidat_id'=>suffix($criteresfichescandidature['ActioncandidatPersonne']['actioncandidat_id']));
			}

			if ( isset($criteresfichescandidature['ActioncandidatPersonne']['referent_id']) && !empty($criteresfichescandidature['ActioncandidatPersonne']['referent_id']) ) {
				$conditions[] = array('ActioncandidatPersonne.referent_id'=>$criteresfichescandidature['ActioncandidatPersonne']['referent_id']);
			}

			if ( isset($criteresfichescandidature['Partenaire']['libstruc']) && !empty($criteresfichescandidature['Partenaire']['libstruc']) ) {
				$conditions[] = array('Partenaire.id'=>$criteresfichescandidature['Partenaire']['libstruc']);
			}

			if ( isset($criteresfichescandidature['ActioncandidatPersonne']['positionfiche']) && !empty($criteresfichescandidature['ActioncandidatPersonne']['positionfiche']) ) {
				$conditions[] = array('ActioncandidatPersonne.positionfiche'=>$criteresfichescandidature['ActioncandidatPersonne']['positionfiche']);
			}

			/// Critères sur la date de signature de la fiche de candidature
			if( isset( $criteresfichescandidature['ActioncandidatPersonne']['datesignature'] ) && !empty( $criteresfichescandidature['ActioncandidatPersonne']['datesignature'] ) ) {
				$valid_from = ( valid_int( $criteresfichescandidature['ActioncandidatPersonne']['datesignature_from']['year'] ) && valid_int( $criteresfichescandidature['ActioncandidatPersonne']['datesignature_from']['month'] ) && valid_int( $criteresfichescandidature['ActioncandidatPersonne']['datesignature_from']['day'] ) );
				$valid_to = ( valid_int( $criteresfichescandidature['ActioncandidatPersonne']['datesignature_to']['year'] ) && valid_int( $criteresfichescandidature['ActioncandidatPersonne']['datesignature_to']['month'] ) && valid_int( $criteresfichescandidature['ActioncandidatPersonne']['datesignature_to']['day'] ) );
				if( $valid_from && $valid_to ) {
					$conditions[] = 'ActioncandidatPersonne.datesignature BETWEEN \''.implode( '-', array( $criteresfichescandidature['ActioncandidatPersonne']['datesignature_from']['year'], $criteresfichescandidature['ActioncandidatPersonne']['datesignature_from']['month'], $criteresfichescandidature['ActioncandidatPersonne']['datesignature_from']['day'] ) ).'\' AND \''.implode( '-', array( $criteresfichescandidature['ActioncandidatPersonne']['datesignature_to']['year'], $criteresfichescandidature['ActioncandidatPersonne']['datesignature_to']['month'], $criteresfichescandidature['ActioncandidatPersonne']['datesignature_to']['day'] ) ).'\'';
				}
			}


			if ( isset($criteresfichescandidature['ActioncandidatPersonne']['formationregion']) && !empty($criteresfichescandidature['ActioncandidatPersonne']['formationregion']) ) {
				$conditions[] = array('ActioncandidatPersonne.formationregion ILIKE \''.$this->wildcard( $criteresfichescandidature['ActioncandidatPersonne']['formationregion'] ).'\'');
			}

			if ( isset($criteresfichescandidature['Progfichecandidature66']['name']) && !empty($criteresfichescandidature['Progfichecandidature66']['name']) ) {
				$conditions[] = array('Progfichecandidature66.id ILIKE \''.$this->wildcard( $criteresfichescandidature['ActioncandidatPersonne']['formationregion'] ).'\'');
			}

            $poledossierpcg66_id = Set::extract( $criteresfichescandidature, 'Progfichecandidature66.id' );
            if( !empty( $poledossierpcg66_id ) ){
                $conditions[] = 'Progfichecandidature66.id IN ( \''.implode( '\', \'', $poledossierpcg66_id ).'\' )';
            }

            $joins = array(
                $ActioncandidatPersonne->join( 'Actioncandidat', array( 'type' => 'INNER' ) ),
                $ActioncandidatPersonne->join( 'Personne', array( 'type' => 'INNER' ) ),
                $ActioncandidatPersonne->join( 'Referent', array( 'type' => 'INNER' ) ),
                $ActioncandidatPersonne->Referent->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
                $ActioncandidatPersonne->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
                $ActioncandidatPersonne->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'INNER' ) ),
                $ActioncandidatPersonne->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) ),
                $ActioncandidatPersonne->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
                $ActioncandidatPersonne->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'LEFT OUTER' ) ),
                $ActioncandidatPersonne->join( 'Motifsortie', array( 'type' => 'LEFT OUTER' ) ),
                $ActioncandidatPersonne->Actioncandidat->join( 'Contactpartenaire', array( 'type' => 'INNER' ) ),
                $ActioncandidatPersonne->Actioncandidat->Contactpartenaire->join( 'Partenaire', array( 'type' => 'INNER' ) ),
                $ActioncandidatPersonne->join( 'Progfichecandidature66', array( 'type' => 'LEFT OUTER' ) )
            );


			$query = array(
                'fields' => array_merge(
                    $ActioncandidatPersonne->fields(),
                    $ActioncandidatPersonne->Actioncandidat->fields(),
                    $ActioncandidatPersonne->Personne->fields(),
                    $ActioncandidatPersonne->Referent->fields(),
                    $ActioncandidatPersonne->Referent->Structurereferente->fields(),
                    $ActioncandidatPersonne->Personne->Foyer->fields(),
                    $ActioncandidatPersonne->Personne->Foyer->Adressefoyer->Adresse->fields(),
                    $ActioncandidatPersonne->Personne->Foyer->Dossier->fields(),
                    $ActioncandidatPersonne->Personne->Foyer->Dossier->Situationdossierrsa->fields(),
                    $ActioncandidatPersonne->Motifsortie->fields(),
                    $ActioncandidatPersonne->Actioncandidat->Contactpartenaire->fields(),
                    $ActioncandidatPersonne->Actioncandidat->Contactpartenaire->Partenaire->fields(),
//                    $ActioncandidatPersonne->CandidatureProg66->fields(),
                    $ActioncandidatPersonne->Progfichecandidature66->fields()
                ),
				'joins' => $joins,
				'contain' => false,
				'order' => array( '"ActioncandidatPersonne"."datesignature" ASC' ),
				'conditions' => $conditions
			);



//            $qdProgsfichescandidatures66 = array(
//                'fields' => array(
//                    'Progfichecandidature66.name'
//                ),
//                'conditions' => array_merge(
//                    array('CandidatureProg66.actioncandidat_personne_id = ActioncandidatPersonne.id'),
//                    $conditions
//                ),
//                'joins' => array(
//                    $ActioncandidatPersonne->join( 'CandidatureProg66', array( 'type' => 'LEFT OUTER' ) ),
//                    $ActioncandidatPersonne->CandidatureProg66->join( 'Progfichecandidature66', array( 'type' => 'LEFT OUTER' ) )
//                ),
//                'contain' => false
//            );
//
//            $vfProgsfichescandidatures66 = $ActioncandidatPersonne->vfListe( $qdProgsfichescandidatures66 );
//            $query['fields'][] = "{$vfProgsfichescandidatures66} AS \"ActioncandidatPersonne__listenoms\"";

			$query = $ActioncandidatPersonne->Personne->PersonneReferent->completeQdReferentParcours( $query, $criteresfichescandidature );

			return $query;
		}
	}
?>