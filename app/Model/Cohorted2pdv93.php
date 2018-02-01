<?php
	/**
	 * Code source de la classe Cohorted2pdv93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Cohorted2pdv93 ...
	 *
	 * @package app.Model
	 */
	class Cohorted2pdv93 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Cohorted2pdv93';

		/**
		 * On n'utilise pas de table de la base de données.
		 *
		 * @var mixed
		 */
		public $useTable = false;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array( 'Conditionnable' );

		public function search( $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$Personne = ClassRegistry::init( 'Personne' );

			$sqDerniereRgadr01 = $Dossier->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' );

			// Un dossier possède un seul detail du droit RSA mais ce dernier possède plusieurs details de calcul
			// donc on limite au dernier detail de calcul du droit rsa
			$sqDernierDetailcalculdroitrsa = $Dossier->Detaildroitrsa->Detailcalculdroitrsa->sqDernier( 'Detaildroitrsa.id' );

			$conditions = array(
				'Prestation.natprest' => 'RSA',
				'Prestation.rolepers' => array( 'DEM', 'CJT' ),
				'Adressefoyer.rgadr' => '01',
				"Adressefoyer.id IN ( {$sqDerniereRgadr01} )",
				'OR' => array(
					'Detailcalculdroitrsa.id IS NULL',
					"Detailcalculdroitrsa.id IN ( {$sqDernierDetailcalculdroitrsa} )",
				)
			);

			$conditions = $this->conditionsAdresse( $conditions, $search );
			$conditions = $this->conditionsPersonneFoyerDossier( $conditions, $search );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $search );

			// -----------------------------------------------------------------
			// Filtres sur le suivi
			// -----------------------------------------------------------------

			// Année de suivi
			$annee = Hash::get( $search, 'Questionnaired1pdv93.annee' );
			$conditions[] = "Rendezvous.daterdv BETWEEN '{$annee}-01-01' AND '{$annee}-12-31'";

			// PDV effectuant le suivi
			$structurereferente_id = Hash::get( $search, 'Rendezvous.structurereferente_id' );
			if( !empty( $structurereferente_id ) ) {
				$conditions['Rendezvous.structurereferente_id'] = $structurereferente_id;
			}

			// Possédant un questionnaire D2 pour l'année de suivi ?
			$questionnaired2Exists = ( Hash::get( $search, 'Questionnaired2pdv93.exists' ) ? true : false );
			if( $questionnaired2Exists ) {
				$conditions[] = 'Questionnaired2pdv93.id IS NOT NULL';
			}
			else {
				$conditions[] = 'Questionnaired2pdv93.id IS NULL';
			}

			// Filtrer par réponses au questionnaire D2
			$fields = array( 'situationaccompagnement', 'sortieaccompagnementd2pdv93_id', 'chgmentsituationadmin' );
			foreach( $fields as $field ) {
				$path = "Questionnaired2pdv93.{$field}";
				$value = Hash::get( $search, $path );
				if( !empty( $value ) ) {
					$conditions[$path] = $value;
				}
			}

			$querydata = array(
				'fields' => array_merge(
					$Personne->fields(),
					$Personne->Calculdroitrsa->fields(),
					$Personne->Prestation->fields(),
					$Personne->Questionnaired1pdv93->fields(),
					$Personne->Questionnaired1pdv93->Questionnaired2pdv93->fields(),
					$Personne->Questionnaired1pdv93->Rendezvous->fields(),
					$Personne->Questionnaired1pdv93->Rendezvous->Structurereferente->fields(),
					$Personne->Foyer->Adressefoyer->fields(),
					$Personne->Foyer->Adressefoyer->Adresse->fields(),
					$Personne->Foyer->Dossier->fields(),
					$Personne->Foyer->Dossier->Detaildroitrsa->fields(),
					$Personne->Foyer->Dossier->Detaildroitrsa->Detailcalculdroitrsa->fields()
				),
				'joins' => array(
					$Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Questionnaired1pdv93', array( 'type' => 'INNER' ) ),
					$Personne->Questionnaired1pdv93->join( 'Questionnaired2pdv93', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Questionnaired1pdv93->join( 'Rendezvous', array( 'type' => 'INNER' ) ),
					$Personne->Questionnaired1pdv93->Rendezvous->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->Dossier->join( 'Detaildroitrsa', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->Dossier->Detaildroitrsa->join( 'Detailcalculdroitrsa', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) ),
				),
				'conditions' => $conditions,
				'contain' => false,
				'order' => array(
					'Questionnaired2pdv93.modified ASC',
					'Rendezvous.daterdv ASC',
					'Questionnaired1pdv93.id ASC',
				),
				'limit' => isset ($search['limit']) ? $search['limit'] : Configure::read('ResultatsParPage.nombre_par_defaut')
			);

			// Condition sur le projet de ville territorial de la structure de rendez-vous
			$querydata['conditions'] = $this->conditionCommunautesr(
				$querydata['conditions'],
				$search,
				array( 'Rendezvous.communautesr_id' => 'Rendezvous.structurereferente_id' )
			);

			$querydata = $Personne->PersonneReferent->completeQdReferentParcours( $querydata, $search );

			return $querydata;
		}
	}
?>