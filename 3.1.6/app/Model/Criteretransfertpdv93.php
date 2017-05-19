<?php
	/**
	 * Fichier source du modèle Criteretransfertpdv93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe Criteretransfertpdv93.
	 *
	 * @deprecated since 3.0.0
	 * @see WebrsaRechercheTransfertpdv93.php
	 *
	 * @package app.Model
	 */
	class Criteretransfertpdv93 extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Criteretransfertpdv93';

		/**
		 * On n'utilise pas de table.
		 *
		 * @var boolean
		 */
		public $useTable = false;

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array( 'Conditionnable' );

		/**
		 * Retourne un querydata résultant du traitement du formulaire de
		 * recherche des dossiers transférés entre PDV.
		 *
		 * @param array $mesCodesInsee La liste des codes INSEE à laquelle est lié l'utilisateur
		 * @param boolean $filtre_zone_geo L'utilisateur est-il limité au niveau des zones géographiques ?
		 * @param array $search Critères du formulaire de recherche
		 * @param mixed $lockedDossiers
		 * @return array
		 */
		public function search( $mesCodesInsee, $filtre_zone_geo, $search, $lockedDossiers ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			// Un dossier possède un seul detail du droit RSA mais ce dernier possède plusieurs details de calcul
			// donc on limite au dernier detail de calcul du droit rsa
			$sqDernierDetailcalculdroitrsa = $Dossier->Foyer->Dossier->Detaildroitrsa->Detailcalculdroitrsa->sqDernier( 'Detaildroitrsa.id' );

			$conditions = array(
				// FIXME: LEFT OUTER JOIN sur les prestations ?
				'Prestation.natprest' => 'RSA',
				'Prestation.rolepers' => array( 'DEM', 'CJT' ),
				'Adressefoyer.rgadr' => array( '02', '03' ),
				'Adressefoyer.id = Transfertpdv93.vx_adressefoyer_id',
				"Detailcalculdroitrsa.id IN ( {$sqDernierDetailcalculdroitrsa} )",
			);

			$conditions = $this->conditionsAdresse( $conditions, $search, $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsPersonneFoyerDossier( $conditions, $search );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $search );

			// Préparation de la jointure de Transfertpdv93 avec NvOrientstruct et VxOrientstruct
			$joinTransfertpdv93 = array();
			$j1 = array_words_replace( $Dossier->Foyer->Personne->Orientstruct->join( 'NvTransfertpdv93', array( 'type' => 'INNER' ) ), array( 'NvTransfertpdv93' => 'Transfertpdv93', 'Orientstruct' => 'NvOrientstruct' ) );
			$j2 = array_words_replace( $Dossier->Foyer->Personne->Orientstruct->join( 'VxTransfertpdv93', array( 'type' => 'INNER' ) ), array( 'VxTransfertpdv93' => 'Transfertpdv93', 'Orientstruct' => 'VxOrientstruct' ) );
			$joinTransfertpdv93 = $j1;
			$joinTransfertpdv93['conditions'] = array(
				$joinTransfertpdv93['conditions'],
				$j2['conditions']
			);

			// Filtre par structure référente source et/ou cible
			foreach( array( 'Vx', 'Nv' ) as $prefix ) {
				$model = "{$prefix}Orientstruct";
				if( isset( $search[$model]['structurereferente_id'] ) && !empty( $search[$model]['structurereferente_id'] ) ) {
					$conditions["{$model}.structurereferente_id"] = $search[$model]['structurereferente_id'];
				}
			}

			// Filtre par type d'orientation (qui devrait être la même entre VxOrientstruct et NvOrientstruct)
			$typeorient_id = Hash::get( $search, 'Orientstruct.typeorient_id' );
			if( !empty( $typeorient_id ) ) {
				$conditions[] = array(
					'OR' => array(
						'VxOrientstruct.typeorient_id' => $typeorient_id,
						'NvOrientstruct.typeorient_id' => $typeorient_id,
					)
				);
			}

			$querydata = array(
				'fields' => array_merge(
					$Dossier->fields(),
					$Dossier->Detaildroitrsa->fields(),
					$Dossier->Detaildroitrsa->Detailcalculdroitrsa->fields(),
					$Dossier->Foyer->Adressefoyer->fields(),
					$Dossier->Foyer->Personne->fields(),
					$Dossier->Foyer->Adressefoyer->Adresse->fields(),
					$Dossier->Foyer->Personne->Calculdroitrsa->fields(),
					array_words_replace( $Dossier->Foyer->Personne->Orientstruct->fields(), array( 'Orientstruct' => 'VxOrientstruct' ) ),
					array_words_replace( $Dossier->Foyer->Personne->Orientstruct->fields(), array( 'Orientstruct' => 'NvOrientstruct' ) ),
					$Dossier->Foyer->Personne->Prestation->fields(),
					array_words_replace( $Dossier->Foyer->Personne->Orientstruct->VxTransfertpdv93->fields(), array( 'VxTransfertpdv93' => 'Transfertpdv93' ) ),
					array_words_replace( $Dossier->Foyer->Personne->Orientstruct->VxTransfertpdv93->VxOrientstruct->Structurereferente->fields(), array( 'Structurereferente' => 'VxStructurereferente' ) ),
					array_words_replace( $Dossier->Foyer->Personne->Orientstruct->NvTransfertpdv93->NvOrientstruct->Structurereferente->fields(), array( 'Structurereferente' => 'NvStructurereferente' ) )
				),
				'joins' => array(
					$Dossier->join( 'Detaildroitrsa', array( 'type' => 'INNER' ) ),
					$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
					$Dossier->Detaildroitrsa->join( 'Detailcalculdroitrsa', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
					array_words_replace( $Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => 'INNER' ) ), array( 'Orientstruct' => 'VxOrientstruct' ) ),
					array_words_replace( $Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => 'INNER' ) ), array( 'Orientstruct' => 'NvOrientstruct' ) ),
					$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$joinTransfertpdv93,
					array_words_replace( $Dossier->Foyer->Personne->Orientstruct->VxTransfertpdv93->VxOrientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ), array( 'Structurereferente' => 'VxStructurereferente' ) ),
					array_words_replace( $Dossier->Foyer->Personne->Orientstruct->NvTransfertpdv93->NvOrientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ), array( 'Structurereferente' => 'NvStructurereferente' ) )
				),
				'conditions' => $conditions,
				'contain' => false,
				'order' => array( 'Transfertpdv93.created DESC', 'Dossier.numdemrsa ASC', 'Dossier.id ASC', 'Personne.nom ASC', 'Personne.prenom ASC' ),
				'limit' => 10
			);

			$querydata['conditions'][] = 'CAST( DATE_PART( \'year\', "Transfertpdv93"."created" ) + 1 || \'-03-31\' AS date ) >= DATE_TRUNC( \'day\', NOW() )';

			$querydata = $Dossier->Foyer->Personne->PersonneReferent->completeQdReferentParcours( $querydata, $search );

			return $querydata;
		}
	}
?>