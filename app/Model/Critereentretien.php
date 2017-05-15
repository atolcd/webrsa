<?php
	/**
	 * Code source de la classe Critereentretien.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Critereentretien ...
	 *
	 * @deprecated see WebrsaRechercheEntretien
	 *
	 * @package app.Model
	 */
	class Critereentretien extends AppModel
	{
		public $name = 'Critereentretien';

		public $useTable = false;

		public $actsAs = array( 'Conditionnable' );

		/**
		*
		*/

		public function search( $criteresentretiens  ) {
			/// Conditions de base
			$conditions = array( );

			/// Critères zones géographiques
			$conditions = $this->conditionsAdresse( $conditions, $criteresentretiens );
			$conditions = $this->conditionsPersonneFoyerDossier( $conditions, $criteresentretiens );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $criteresentretiens );

			/// Critères
			$numeroapre = Set::extract( $criteresentretiens, 'Apre.numeroapre' );
			$referent = Set::extract( $criteresentretiens, 'Apre.referent_id' );
			$structure = Set::extract( $criteresentretiens, 'Entretien.structurereferente_id' );
			$referent = Set::extract( $criteresentretiens, 'Entretien.referent_id' );

			// Référent lié à l'APRE
			if( !empty( $arevoirle ) ) {
				$conditions[] = 'Entretien.arevoirle = \''.Sanitize::clean( $arevoirle, array( 'encode' => false ) ).'\'';
			}

			if( isset( $criteresentretiens['Entretien']['arevoirle'] ) && !empty( $criteresentretiens['Entretien']['arevoirle'] ) ) {
				if( valid_int( $criteresentretiens['Entretien']['arevoirle']['year'] ) ) {
					$conditions[] = 'EXTRACT(YEAR FROM Entretien.arevoirle) = '.$criteresentretiens['Entretien']['arevoirle']['year'];
				}
				if( valid_int( $criteresentretiens['Entretien']['arevoirle']['month'] ) ) {
					$conditions[] = 'EXTRACT(MONTH FROM Entretien.arevoirle) = '.$criteresentretiens['Entretien']['arevoirle']['month'];
				}
			}

			if ( isset($criteresentretiens['Entretien']['structurereferente_id']) && !empty($criteresentretiens['Entretien']['structurereferente_id']) ) {
				$conditions[] = array('Entretien.structurereferente_id'=>$criteresentretiens['Entretien']['structurereferente_id']);
			}

			if ( isset($criteresentretiens['Entretien']['referent_id']) && !empty($criteresentretiens['Entretien']['referent_id']) ) {
				$conditions[] = array('Entretien.referent_id'=>$criteresentretiens['Entretien']['referent_id']);
			}

			$conditions = $this->conditionsDates( $conditions, $criteresentretiens, 'Entretien.dateentretien' );

			/// Requête
			$this->Dossier = ClassRegistry::init( 'Dossier' );

			$joins = array(
				array(
					'table'      => 'personnes',
					'alias'      => 'Personne',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Personne.id = Entretien.personne_id' ),
				),
				array(
					'table'      => 'referents',
					'alias'      => 'Referent',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Referent.id = Entretien.referent_id' ),
				),
				array(
					'table'      => 'structuresreferentes',
					'alias'      => 'Structurereferente',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Structurereferente.id = Entretien.structurereferente_id' ),
				),
				array(
					'table'      => 'objetsentretien',
					'alias'      => 'Objetentretien',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Objetentretien.id = Entretien.objetentretien_id' ),
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
					'table'      => 'situationsdossiersrsa',
					'alias'      => 'Situationdossierrsa',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array( 'Situationdossierrsa.dossier_id = Dossier.id' )
				)
			);


			$query = array(
				'fields' => array(
					'Entretien.personne_id',
					'Entretien.arevoirle',
					'Entretien.dateentretien',
					'Entretien.structurereferente_id',
					'Entretien.referent_id',
					'Entretien.typeentretien',
					'Entretien.objetentretien_id',
					'Referent.qual',
					'Referent.nom',
					'Referent.prenom',
					'Structurereferente.lib_struc',
					'Objetentretien.name',
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
					'Adresse.numvoie',
                    'Adresse.libtypevoie',
                    'Adresse.nomvoie',
                    'Adresse.complideadr',
                    'Adresse.compladr',
					'Adresse.codepos',
                    'Adresse.nomcom',
					'Adressefoyer.rgadr',
					'Adresse.numcom'
				),
				'joins' => $joins,
				'contain' => false,
				'conditions' => $conditions
			);

			$query = $this->Dossier->Foyer->Personne->PersonneReferent->completeQdReferentParcours( $query, $criteresentretiens );

			return $query;


		}
	}
?>