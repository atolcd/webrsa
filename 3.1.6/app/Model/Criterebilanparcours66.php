<?php
	/**
	 * Code source de la classe Criterebilanparcours66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Moteur de recherche pour les bilans de parcours du CG 66
	 *
	 * @package app.Model
	 * @deprecated since version 3.0.0
	 * @see WebrsaRechercheBilanparcours66
	 */
	class Criterebilanparcours66 extends AppModel
	{
		public $name = 'Criterebilanparcours66';

		public $useTable = false;

		public $actsAs = array( 'Conditionnable' );


		public function search( $mesCodesInsee, $filtre_zone_geo, $criteresbilansparcours66 ) {
			/// Conditions de base

			$conditions = array();
			/// Filtre zone géographique
			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsAdresse( $conditions, $criteresbilansparcours66, $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsPersonneFoyerDossier( $conditions, $criteresbilansparcours66 );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $criteresbilansparcours66 );

			if ( isset($criteresbilansparcours66['Bilanparcours66']['choixparcours']) && !empty($criteresbilansparcours66['Bilanparcours66']['choixparcours']) ) {
				$conditions[] = array('Bilanparcours66.choixparcours'=>$criteresbilansparcours66['Bilanparcours66']['choixparcours']);
			}

			if ( isset($criteresbilansparcours66['Bilanparcours66']['proposition']) && !empty($criteresbilansparcours66['Bilanparcours66']['proposition']) ) {
				$conditions[] = array('Bilanparcours66.proposition'=>$criteresbilansparcours66['Bilanparcours66']['proposition']);
			}

			if ( isset($criteresbilansparcours66['Bilanparcours66']['examenaudition']) && !empty($criteresbilansparcours66['Bilanparcours66']['examenaudition']) ) {
				$conditions[] = array('Bilanparcours66.examenaudition'=>$criteresbilansparcours66['Bilanparcours66']['examenaudition']);
			}

			if ( isset($criteresbilansparcours66['Bilanparcours66']['maintienorientation']) && is_numeric($criteresbilansparcours66['Bilanparcours66']['maintienorientation']) ) {
				$conditions[] = array('Bilanparcours66.maintienorientation'=>$criteresbilansparcours66['Bilanparcours66']['maintienorientation']);
			}

			if ( isset($criteresbilansparcours66['Bilanparcours66']['referent_id']) && !empty($criteresbilansparcours66['Bilanparcours66']['referent_id']) ) {
				$conditions[] = array('Bilanparcours66.referent_id'=>suffix($criteresbilansparcours66['Bilanparcours66']['referent_id']));
			}

			if ( isset($criteresbilansparcours66['Bilanparcours66']['structurereferente_id']) && !empty($criteresbilansparcours66['Bilanparcours66']['structurereferente_id']) ) {
				$conditions[] = array('Bilanparcours66.structurereferente_id'=>$criteresbilansparcours66['Bilanparcours66']['structurereferente_id']);
			}

			if ( isset($criteresbilansparcours66['Bilanparcours66']['positionbilan']) && !empty($criteresbilansparcours66['Bilanparcours66']['positionbilan']) ) {
				$conditions[] = array('Bilanparcours66.positionbilan'=>$criteresbilansparcours66['Bilanparcours66']['positionbilan']);
			}

			/// Critères sur le Bilan - date du bilan
			if( isset( $criteresbilansparcours66['Bilanparcours66']['datebilan'] ) && !empty( $criteresbilansparcours66['Bilanparcours66']['datebilan'] ) ) {
				$valid_from = ( valid_int( $criteresbilansparcours66['Bilanparcours66']['datebilan_from']['year'] ) && valid_int( $criteresbilansparcours66['Bilanparcours66']['datebilan_from']['month'] ) && valid_int( $criteresbilansparcours66['Bilanparcours66']['datebilan_from']['day'] ) );
				$valid_to = ( valid_int( $criteresbilansparcours66['Bilanparcours66']['datebilan_to']['year'] ) && valid_int( $criteresbilansparcours66['Bilanparcours66']['datebilan_to']['month'] ) && valid_int( $criteresbilansparcours66['Bilanparcours66']['datebilan_to']['day'] ) );
				if( $valid_from && $valid_to ) {
					$conditions[] = 'Bilanparcours66.datebilan BETWEEN \''.implode( '-', array( $criteresbilansparcours66['Bilanparcours66']['datebilan_from']['year'], $criteresbilansparcours66['Bilanparcours66']['datebilan_from']['month'], $criteresbilansparcours66['Bilanparcours66']['datebilan_from']['day'] ) ).'\' AND \''.implode( '-', array( $criteresbilansparcours66['Bilanparcours66']['datebilan_to']['year'], $criteresbilansparcours66['Bilanparcours66']['datebilan_to']['month'], $criteresbilansparcours66['Bilanparcours66']['datebilan_to']['day'] ) ).'\'';
				}
			}

			/// Présence de manifestations sur un bilan ?
			$hasManifestation = Set::extract( $criteresbilansparcours66, 'Bilanparcours66.hasmanifestation' );
			if( !empty( $hasManifestation ) && in_array( $hasManifestation, array( 'O', 'N' ) ) ) {
				if( $hasManifestation == 'O' ) {
					$conditions[] = '( SELECT COUNT(manifestationsbilansparcours66.id) FROM manifestationsbilansparcours66 WHERE manifestationsbilansparcours66.bilanparcours66_id = "Bilanparcours66"."id" ) > 0';
				}
				else {
					$conditions[] = '( SELECT COUNT(manifestationsbilansparcours66.id) FROM manifestationsbilansparcours66 WHERE manifestationsbilansparcours66.bilanparcours66_id = "Bilanparcours66"."id" ) = 0';
				}
			}

			$Bilanparcours66 = ClassRegistry::init( 'Bilanparcours66' );

			$joins = array(
				array(
					'table'      => 'personnes',
					'alias'      => 'Personne',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Personne.id = Bilanparcours66.personne_id' ),
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
				$Bilanparcours66->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
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
				),
				array(
					'table'      => 'orientsstructs',
					'alias'      => 'Orientstruct',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array( 'Orientstruct.id = Bilanparcours66.orientstruct_id' ),
				),
				array(
					'table'      => 'referents',
					'alias'      => 'Referent',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Referent.id = Bilanparcours66.referent_id' ),
				),
				array(
					'table'      => 'structuresreferentes',
					'alias'      => 'Structurereferente',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Structurereferente.id = Bilanparcours66.structurereferente_id' ),
				),
				array(
					'table'      => 'defautsinsertionseps66',
					'alias'      => 'Defautinsertionep66',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array( 'Bilanparcours66.id = Defautinsertionep66.bilanparcours66_id' ),
				),
				array(
					'table'      => 'saisinesbilansparcourseps66',
					'alias'      => 'Saisinebilanparcoursep66',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array( 'Bilanparcours66.id = Saisinebilanparcoursep66.bilanparcours66_id' ),
				),
				array(
					'table'      => 'dossierseps',
					'alias'      => 'Dossierep',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						'OR' => array(
							'Defautinsertionep66.dossierep_id = Dossierep.id',
							'Saisinebilanparcoursep66.dossierep_id = Dossierep.id',
						)
					),
				)
			);

			$query = array(
				'fields' => array(
					'Bilanparcours66.id',
					'Bilanparcours66.orientstruct_id',
					'Bilanparcours66.structurereferente_id',
					'Bilanparcours66.referent_id',
					'Bilanparcours66.datebilan',
					'Bilanparcours66.choixparcours',
					'Bilanparcours66.proposition',
					'Bilanparcours66.examenaudition',
					'Bilanparcours66.examenauditionpe',
					'Bilanparcours66.maintienorientation',
					'Bilanparcours66.saisineepparcours',
					'Bilanparcours66.positionbilan',
					'Personne.id',
					$Bilanparcours66->Personne->sqVirtualField( 'nom_complet' ),
                    $Bilanparcours66->Referent->sqVirtualField( 'nom_complet' ),
					'Structurereferente.lib_struc',
					'Dossier.matricule',
					'Dossier.numdemrsa',
					'Dossierep.themeep',
					'Adresse.nomcom',
					'Adresse.codepos',
					'Adresse.numcom'
				),
				'joins' => $joins,
				'order' => array( '"Bilanparcours66"."datebilan" ASC' ),
				'conditions' => $conditions
			);

			$query = $Bilanparcours66->Personne->PersonneReferent->completeQdReferentParcours( $query, $criteresbilansparcours66 );

			return $query;
		}
	}
?>