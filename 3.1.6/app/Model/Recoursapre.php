<?php
	/**
	 * Code source de la classe Recoursapre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Recoursapre ...
	 *
	 * @package app.Model
	 */
	class Recoursapre extends AppModel
	{
		public $name = 'Recoursapre';

		public $useTable = false;

		public $actsAs = array(
			'Gedooo.Gedooo',
			'Conditionnable',
			'ModelesodtConditionnables' => array(
				93 => array(
					'APRE/DecisionComite/Recours/recoursOuibeneficiaire.odt',
					'APRE/DecisionComite/Recours/recoursNonbeneficiaire.odt',
					'APRE/DecisionComite/Recours/recoursreferent.odt'
				)
			)
		);

		/**
		*
		*/

		public function search( $avisRecours, $mesCodesInsee, $filtre_zone_geo, $criteresrecours ) {

			/// Conditions de base
			$conditions = array();

			/// Filtre zone géographique
			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsAdresse( $conditions, $criteresrecours, $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsPersonneFoyerDossier( $conditions, $criteresrecours );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $criteresrecours );

			if( !empty( $avisRecours ) ) {
				if( $avisRecours == 'Recoursapre::demande' ) {
					$conditions[] = 'ApreComiteapre.decisioncomite = \'REF\'';
					$conditions[] = 'ApreComiteapre.recoursapre IS NULL';
				}
				else {
					$conditions[] = 'ApreComiteapre.decisioncomite = \'REF\'';
					$conditions[] = 'ApreComiteapre.recoursapre IS NOT NULL';
					$conditions[] = 'ApreComiteapre.daterecours IS NOT NULL';
				}
			}

			///Criteres
			$numeroapre = Set::extract( $criteresrecours, 'Recoursapre.numeroapre' );

			/// Critères sur le Comité - intitulé du comité
			if( isset( $criteresrecours['Cohortecomiteapre']['id'] ) && !empty( $criteresrecours['Cohortecomiteapre']['id'] ) ) {
				$conditions['Comiteapre.id'] = $criteresrecours['Cohortecomiteapre']['id'];
			}

			/// Critères sur les recours APRE - date de recours
			if( isset( $criteresrecours['Recoursapre']['datedemandeapre'] ) && !empty( $criteresrecours['Recoursapre']['datedemandeapre'] ) ) {
				$valid_from = ( valid_int( $criteresrecours['Recoursapre']['datedemandeapre_from']['year'] ) && valid_int( $criteresrecours['Recoursapre']['datedemandeapre_from']['month'] ) && valid_int( $criteresrecours['Recoursapre']['datedemandeapre_from']['day'] ) );
				$valid_to = ( valid_int( $criteresrecours['Recoursapre']['datedemandeapre_to']['year'] ) && valid_int( $criteresrecours['Recoursapre']['datedemandeapre_to']['month'] ) && valid_int( $criteresrecours['Recoursapre']['datedemandeapre_to']['day'] ) );
				if( $valid_from && $valid_to ) {
					$conditions[] = 'Apre.datedemandeapre BETWEEN \''.implode( '-', array( $criteresrecours['Recoursapre']['datedemandeapre_from']['year'], $criteresrecours['Recoursapre']['datedemandeapre_from']['month'], $criteresrecours['Recoursapre']['datedemandeapre_from']['day'] ) ).'\' AND \''.implode( '-', array( $criteresrecours['Recoursapre']['datedemandeapre_to']['year'], $criteresrecours['Recoursapre']['datedemandeapre_to']['month'], $criteresrecours['Recoursapre']['datedemandeapre_to']['day'] ) ).'\'';
				}
			}


			// N° APRE
			if( !empty( $numeroapre ) ) {
				$conditions[] = 'Apre.numeroapre ILIKE \'%'.Sanitize::clean( $numeroapre, array( 'encode' => false ) ).'%\'';
			}

			/// Requête
			$this->Dossier = ClassRegistry::init( 'Dossier' );

			$query = array(
				'fields' => array(
					'"Comiteapre"."id"',
					'"Comiteapre"."datecomite"',
					'"Comiteapre"."heurecomite"',
					'"Comiteapre"."lieucomite"',
					'"Comiteapre"."intitulecomite"',
					'"Comiteapre"."observationcomite"',
					'"ApreComiteapre"."id"',
					'"ApreComiteapre"."apre_id"',
					'"ApreComiteapre"."comiteapre_id"',
					'"ApreComiteapre"."decisioncomite"',
					'"ApreComiteapre"."montantattribue"',
					'"ApreComiteapre"."observationcomite"',
					'"ApreComiteapre"."recoursapre"',
					'"ApreComiteapre"."daterecours"',
					'"ApreComiteapre"."observationrecours"',
					'"Dossier"."numdemrsa"',
					'"Dossier"."matricule"',
					'"Personne"."qual"',
					'"Personne"."nom"',
					'"Personne"."prenom"',
					'"Personne"."dtnai"',
					'"Personne"."nir"',
					'"Adresse"."nomcom"',
					'"Adresse"."codepos"',
					'"Apre"."id"',
					'"Apre"."datedemandeapre"',
					'"Apre"."numeroapre"',
					'"Apre"."mtforfait"',
				),
				'recursive' => -1,
				'joins' => array(
					array(
						'table'      => 'comitesapres',
						'alias'      => 'Comiteapre',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'ApreComiteapre.comiteapre_id = Comiteapre.id' )
					),
					array(
						'table'      => 'apres',
						'alias'      => 'Apre',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'ApreComiteapre.apre_id = Apre.id' )
					),
					array(
						'table'      => 'personnes',
						'alias'      => 'Personne',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Personne.id = Apre.personne_id' )
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
						'table'      => 'adressesfoyers',
						'alias'      => 'Adressefoyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Foyer.id = Adressefoyer.foyer_id', 'Adressefoyer.rgadr = \'01\'' )
					),
					array(
						'table'      => 'adresses',
						'alias'      => 'Adresse',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Adresse.id = Adressefoyer.adresse_id' )
					)
				),
				'order' => array( '"ApreComiteapre"."daterecours" ASC' ),
				'conditions' => $conditions
			);

			return $query;
		}

		/**
		 * Retourne le PDF de recours d'une APRE, pour un destinataire donné,
		 * et contenant les données de l'utilisateur connecté.
		 *
		 * @param integer $id L'id de APRE
		 * @param string $dest Le destinataire de l'impression (beneficiaire, referent)
		 * @param integer $user_id L'id de l'utilisateur qui demande l'impression
		 * @return string
		 */
		public function getDefaultPdf( $id, $dest, $user_id ) {
			$Apre = ClassRegistry::init( 'Apre' );

			$querydata = array(
				'fields' => array_merge(
					$Apre->fields(),
					$Apre->ApreComiteapre->fields(),
					$Apre->Personne->fields(),
					$Apre->Structurereferente->fields(),
					$Apre->Referent->fields(),
					array(
						'( '.$Apre->WebrsaApre->sqApreNomaide().' ) AS "Apre__Natureaide"'
					),
					$Apre->ApreComiteapre->Comiteapre->fields()
				),
				'conditions' => array(
					'Apre.id' => $id,
					'ApreComiteapre.id IN ( '.$Apre->ApreComiteapre->sqDernierComiteApre().' )',
					'OR' => array(
						'Adressefoyer.id IS NULL',
						'Adressefoyer.id IN ( '.$Apre->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )',
					)
				),
				'contain' => false,
				'joins' => array(
					$Apre->join( 'ApreComiteapre', array( 'type' => 'INNER' ) ),
					$Apre->join( 'Personne', array( 'type' => 'INNER' ) ),
					$Apre->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
					$Apre->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					$Apre->ApreComiteapre->join( 'Comiteapre', array( 'type' => 'INNER' ) ),
				)
			);

			$aidesApre = $Apre->WebrsaApre->aidesApre;
			sort( $aidesApre );
			foreach( $aidesApre as $aide ) {
				$querydata['fields'] = array_merge( $querydata['fields'], $Apre->{$aide}->fields() );
				$querydata['joins'][] = $Apre->join( $aide, array( 'type' => 'LEFT OUTER' ) );
			}

			$querydata['fields'] = array_merge(
				$querydata['fields'],
				$Apre->Personne->Foyer->Adressefoyer->Adresse->fields()
			);

			$querydata['joins'][] = $Apre->Personne->join( 'Foyer', array( 'type' => 'INNER' ) );
			$querydata['joins'][] = $Apre->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) );
			$querydata['joins'][] = $Apre->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) );

			$deepAfterFind = $Apre->deepAfterFind;
			$Apre->deepAfterFind = false;
			$apre = $Apre->find( 'first', $querydata );
			$Apre->deepAfterFind = $deepAfterFind;

			if( empty( $apre ) ) {
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
			$apre['User'] = $user['User'];

			// Traductions
			$Option = ClassRegistry::init( 'Option' );

			// Traduction des noms de table en libellés de l'aide
			$apre['Apre']['Natureaide'] = Set::enum( $apre['Apre']['Natureaide'], $Option->natureAidesApres() );
			$apre['Apre']['Natureaide'] = "  - {$apre['Apre']['Natureaide']}\n";

			$options = Hash::merge(
				$Apre->Personne->Foyer->enums(),
				array(
					'Personne' => array(
						'qual' => $Option->qual(),
					),
					'Prestation' => array(
						'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers'),
					),
					'Structurereferente' => array(
						'type_voie' =>  $Option->typevoie(),
					),
					'type' => array(
						'voie' =>  $Option->typevoie(),
					),
				),
				$Apre->ApreComiteapre->enums()
			);

			// Choix du modèle de document

			// Paramètre pour savoir si demande de recours ou non
			$recoursapre = Set::enum( Set::classicExtract( $apre, 'ApreComiteapre.recoursapre' ), $options['ApreComiteapre']['recoursapre'] );

			if( $dest == 'beneficiaire' ) {
				$modeleodt = 'APRE/DecisionComite/Recours/recours'.$recoursapre.$dest.'.odt';
			}
			else if( $dest == 'referent' ) {
				$modeleodt = 'APRE/DecisionComite/Recours/recours'.$dest.'.odt';
			}

			return $this->ged( $apre, $modeleodt, false, $options );
		}
	}
?>