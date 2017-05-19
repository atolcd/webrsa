<?php
	/**
	 * Fichier source de la classe Critereapre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Critereapre s'occupe du moteur de recherche des APREs (CG 66 et 93).
	 *
	 * @package app.Model
	 * @deprecated since version 3.0.0
	 * @see WebrsaRechercheApre
	 */
	class Critereapre extends AppModel
	{
		public $name = 'Critereapre';

		public $useTable = false;

		public $actsAs = array( 'Conditionnable' );

		/**
		 * Traitement du formulaire de recherche concernant les APREs.
		 *
		 * @param type $etatApre
		 * @param array $mesCodesInsee La liste des codes INSEE à laquelle est lié l'utilisateur
		 * @param boolean $filtre_zone_geo L'utilisateur est-il limité au niveau des zones géographiques ?
		 * @param array $criteresapres Critères du formulaire de recherche
		 * @return array
		 */
		public function search( $etatApre, $mesCodesInsee, $filtre_zone_geo, $criteresapres ) {

            /// Conditions de base
			$conditions = array( );

			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsAdresse( $conditions, $criteresapres, $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsPersonneFoyerDossier( $conditions, $criteresapres );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $criteresapres );

			/// Critères
			$datedemandeapre = Set::extract( $criteresapres, 'Filtre.datedemandeapre' );
			$daterelance = Set::extract( $criteresapres, 'Filtre.daterelance' );
			$typedemandeapre = Set::extract( $criteresapres, 'Filtre.typedemandeapre' );
			$etatdossierapre = Set::extract( $criteresapres, 'Filtre.etatdossierapre' );
			$eligibiliteapre = Set::extract( $criteresapres, 'Filtre.eligibiliteapre' );
			$activitebeneficiaire = Set::extract( $criteresapres, 'Filtre.activitebeneficiaire' );
			$natureaidesapres = Set::extract( $criteresapres, 'Filtre.natureaidesapres' );
			$statutapre = Set::extract( $criteresapres, 'Filtre.statutapre' );
			$tiers = Set::extract( $criteresapres, 'Filtre.tiersprestataire' );
			$isdecision = Set::extract( $criteresapres, 'Filtre.isdecision' );
			$decisionapre = Set::extract( $criteresapres, 'Filtre.decisionapre' );
			$dateimpressionapre = Set::extract( $criteresapres, 'Filtre.dateimpressionapre' );
			$dateprint = Set::extract( $criteresapres, 'Filtre.dateprint' );
			$structurereferente_id = Set::extract( $criteresapres, 'Filtre.structurereferente_id' );
			$referent_id = Set::extract( $criteresapres, 'Filtre.referent_id' );
			$themeapre66_id = Set::extract( $criteresapres, 'Filtre.themeapre66_id' );
			$themeapre66_id = Set::extract( $criteresapres, 'Filtre.themeapre66_id' );
			$typeaideapre66_id = Set::extract( $criteresapres, 'Filtre.typeaideapre66_id' );

			/// Critères sur la demande APRE - date de demande

			$modelCG = 'Apre.datedemandeapre';
			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$modelCG = 'Aideapre66.datedemande';
			}
			if( isset( $criteresapres['Filtre']['datedemandeapre'] ) && !empty( $criteresapres['Filtre']['datedemandeapre'] ) ) {
				$valid_from = ( valid_int( $criteresapres['Filtre']['datedemandeapre_from']['year'] ) && valid_int( $criteresapres['Filtre']['datedemandeapre_from']['month'] ) && valid_int( $criteresapres['Filtre']['datedemandeapre_from']['day'] ) );
				$valid_to = ( valid_int( $criteresapres['Filtre']['datedemandeapre_to']['year'] ) && valid_int( $criteresapres['Filtre']['datedemandeapre_to']['month'] ) && valid_int( $criteresapres['Filtre']['datedemandeapre_to']['day'] ) );
				if( $valid_from && $valid_to ) {
					$conditions[] = $modelCG.' BETWEEN \''.implode( '-', array( $criteresapres['Filtre']['datedemandeapre_from']['year'], $criteresapres['Filtre']['datedemandeapre_from']['month'], $criteresapres['Filtre']['datedemandeapre_from']['day'] ) ).'\' AND \''.implode( '-', array( $criteresapres['Filtre']['datedemandeapre_to']['year'], $criteresapres['Filtre']['datedemandeapre_to']['month'], $criteresapres['Filtre']['datedemandeapre_to']['day'] ) ).'\'';
				}
			}

			/// Critères sur la relance d'APRE - date de relance
			if( isset( $criteresapres['Filtre']['daterelance'] ) && !empty( $criteresapres['Filtre']['daterelance'] ) ) {
				$valid_from = ( valid_int( $criteresapres['Filtre']['daterelance_from']['year'] ) && valid_int( $criteresapres['Filtre']['daterelance_from']['month'] ) && valid_int( $criteresapres['Filtre']['daterelance_from']['day'] ) );
				$valid_to = ( valid_int( $criteresapres['Filtre']['daterelance_to']['year'] ) && valid_int( $criteresapres['Filtre']['daterelance_to']['month'] ) && valid_int( $criteresapres['Filtre']['daterelance_to']['day'] ) );
				if( $valid_from && $valid_to ) {
					$conditions[] = 'Apre.id IN (
						SELECT relancesapres.apre_id
							FROM relancesapres
							WHERE relancesapres.daterelance BETWEEN \''.implode( '-', array( $criteresapres['Filtre']['daterelance_from']['year'], $criteresapres['Filtre']['daterelance_from']['month'], $criteresapres['Filtre']['daterelance_from']['day'] ) ).'\' AND \''.implode( '-', array( $criteresapres['Filtre']['daterelance_to']['year'], $criteresapres['Filtre']['daterelance_to']['month'], $criteresapres['Filtre']['daterelance_to']['day'] ) ).'\'
					)';
				}
			}

			//Type de demande
			if( !empty( $typedemandeapre ) ) {
				$conditions[] = 'Apre.typedemandeapre = \''.Sanitize::clean( $typedemandeapre, array( 'encode' => false ) ).'\'';
			}

			//Activité du bénéficiaire
			if( !empty( $activitebeneficiaire ) ) {
				$conditions[] = 'Apre.activitebeneficiaire = \''.Sanitize::clean( $activitebeneficiaire, array( 'encode' => false ) ).'\'';
			}

			//Etat du dossier apre
			if( !empty( $etatdossierapre ) ) {
				$conditions[] = 'Apre.etatdossierapre = \''.Sanitize::clean( $etatdossierapre, array( 'encode' => false ) ).'\'';
			}

			//Eligibilité du dossier apre
			if( !empty( $eligibiliteapre ) ) {
				$conditions[] = 'Apre.eligibiliteapre = \''.Sanitize::clean( $eligibiliteapre, array( 'encode' => false ) ).'\'';
			}

			//Eligibilité du dossier apre
			if( !empty( $statutapre ) ) {
				$conditions[] = 'Apre.statutapre = \''.Sanitize::clean( $statutapre, array( 'encode' => false ) ).'\'';
			}

			//Décision émise sur le dossier APRE
			if( !empty( $isdecision ) ) {
				$conditions[] = 'Apre.isdecision = \''.Sanitize::clean( $isdecision, array( 'encode' => false ) ).'\'';
			}

			//Accord ou Rejet
			if( !empty( $decisionapre ) ) {
				$conditions[] = 'Aideapre66.decisionapre = \''.Sanitize::clean( $decisionapre, array( 'encode' => false ) ).'\'';
			}

			//Thème de l'aide
			if( !empty( $themeapre66_id ) ) {
				$conditions[] = 'Aideapre66.themeapre66_id = \''.Sanitize::clean( $themeapre66_id, array( 'encode' => false ) ).'\'';
			}

			//Type d'aide
			if( !empty( $typeaideapre66_id ) ) {
				$conditions[] = 'Aideapre66.typeaideapre66_id = \''.Sanitize::clean( suffix( $typeaideapre66_id ), array( 'encode' => false ) ).'\'';
			}

			//Nature de l'aide
			if( !empty( $natureaidesapres ) ) {
				$table = Inflector::tableize( $natureaidesapres );
				$conditions[] = "Apre.id IN ( SELECT $table.apre_id FROM $table )";
			}


			// Statut impression
			if( !empty( $dateimpressionapre ) && in_array( $dateimpressionapre, array( 'I', 'N' ) ) ) {
				if( $dateimpressionapre == 'I' ) {
					$conditions[] = 'Apre.dateimpressionapre IS NOT NULL';
				}
				else {
					$conditions[] = 'Apre.dateimpressionapre IS NULL';
				}
			}

			// Date d'impression
			if( !empty( $dateprint ) && $dateprint != 0 ) {
				$dateimpressionapre_from = Set::extract( $criteres, 'Filtre.dateimpressionapre_from' );
				$dateimpressionapre_to = Set::extract( $criteres, 'Filtre.dateimpressionapre_to' );
				$dateimpressionapre_from = $dateimpressionapre_from['year'].'-'.$dateimpressionapre_from['month'].'-'.$dateimpressionapre_from['day'];
				$dateimpressionapre_to = $dateimpressionapre_to['year'].'-'.$dateimpressionapre_to['month'].'-'.$dateimpressionapre_to['day'];

				$conditions[] = 'Apre.dateimpressionapre BETWEEN \''.$dateimpressionapre_from.'\' AND \''.$dateimpressionapre_to.'\'';
			}

			//Structure référente où l'apre est faite
			if( !empty( $structurereferente_id ) ) {
				$conditions[] = 'Apre.structurereferente_id = \''.Sanitize::clean( $structurereferente_id, array( 'encode' => false ) ).'\'';
			}

			//Référent de l'APRE
			if( !empty( $referent_id ) ) {
				$conditions[] = 'Apre.referent_id = \''.Sanitize::clean( suffix( $referent_id ), array( 'encode' => false ) ).'\'';
			}

			/// Requête
			$this->Dossier = ClassRegistry::init( 'Dossier' );

			$type = 'INNER';
			if( Configure::read( 'Cg.departement' ) == 93 ) {
				$type = 'LEFT OUTER';
			}

			$query = array(
				'fields' => array(
                    '"Apre"."id"',
                    '"Apre"."personne_id"',
                    '"Apre"."numeroapre"',
                    '"Apre"."typedemandeapre"',
                    '"Apre"."datedemandeapre"',
                    '"Apre"."naturelogement"',
                    '"Apre"."anciennetepoleemploi"',
                    '"Apre"."activitebeneficiaire"',
                    '"Apre"."etatdossierapre"',
                    '"Apre"."dateentreeemploi"',
                    '"Apre"."eligibiliteapre"',
                    '"Apre"."typecontrat"',
                    '"Apre"."statutapre"',
                    '"Apre"."mtforfait"',
                    '"Apre"."isdecision"',
                    '"Apre"."nbenf12"',
                    '"Dossier"."id"',
                    '"Dossier"."numdemrsa"',
                    '"Dossier"."dtdemrsa"',
                    '"Dossier"."matricule"',
                    '"Personne"."id"',
                    '"Personne"."nom"',
                    '"Personne"."prenom"',
                    '"Personne"."dtnai"',
                    '"Personne"."nir"',
                    '"Personne"."qual"',
                    '"Personne"."nomcomnai"',
                    '"Adresse"."nomcom"',
                    '"Adresse"."codepos"',
                    '"Adresse"."canton"',
                    '"Adressefoyer"."rgadr"',
                    '"Adresse"."numcom"',
                    '"Apre"."isdecision"',
                    '"Referent"."nom"',
                    '"Referent"."prenom"',
                    'Structurereferente.lib_struc',
                    'Aideapre66.decisionapre',
                    'Aideapre66.montantaccorde',
                    'Aideapre66.datedemande',
                    'Typeaideapre66.name',
                    'Themeapre66.name'
                ),
				'recursive' => -1,
				'joins' => array(
					array(
						'table'      => 'personnes',
						'alias'      => 'Personne',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Personne.id = Apre.personne_id' )
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
					),
					array(
						'table'      => 'aidesapres66',
						'alias'      => 'Aideapre66',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
								"Aideapre66.apre_id = Apre.id"
						)
					),
					array(
						'table'      => 'typesaidesapres66',
						'alias'      => 'Typeaideapre66',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
								"Typeaideapre66.id = Aideapre66.typeaideapre66_id"
						)
					),
					array(
						'table'      => 'themesapres66',
						'alias'      => 'Themeapre66',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
								"Themeapre66.id = Aideapre66.themeapre66_id"
						)
					),
					array(
						'table'      => 'structuresreferentes',
						'alias'      => 'Structurereferente',
						'type'       => $type,
						'foreignKey' => false,
						'conditions' => array( 'Structurereferente.id = Apre.structurereferente_id' )
					),
					array(
						'table'      => 'referents',
						'alias'      => 'Referent',
						'type'       => $type,
						'foreignKey' => false,
						'conditions' => array( 'Referent.id = Apre.referent_id' )
					),
					array(
						'table'      => 'situationsdossiersrsa',
						'alias'      => 'Situationdossierrsa',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Situationdossierrsa.dossier_id = Dossier.id' )
					)
				),
				'limit' => 10,
				'conditions' => $conditions,
			);

			///Tiers prestataire lié à l'apre
			if( !empty( $tiers ) ) {
				$subQueries = array();
				$this->Apre = ClassRegistry::init( 'Apre' );
				foreach( $this->Apre->WebrsaApre->modelsFormation as $model ) {
					$table = Inflector::tableize( $model );

					$query['joins'][] = array(
						'table'      => $table,
						'alias'      => $model,
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( "{$model}.apre_id = Apre.id" )
					);

					//$subQueries[] = "( SELECT COUNT(tiersprestatairesapres.id) FROM tiersprestatairesapres WHERE tiersprestatairesapres.aidesliees = '$model' AND tiersprestatairesapres.id = $tiers AND $model.tiersprestataireapre_id = tiersprestatairesapres.id ) > 0";
					$subQueries[] = "EXISTS( SELECT tiersprestatairesapres.id FROM tiersprestatairesapres WHERE tiersprestatairesapres.aidesliees = '$model' AND tiersprestatairesapres.id = $tiers AND $model.tiersprestataireapre_id = tiersprestatairesapres.id )";
				}

				$query['conditions'][] = array( 'or' => $subQueries );
			}


            if( Configure::read( 'CG.cantons' )  ) {
                $query['fields'][] = 'Canton.canton';
                $query['joins'][] = ClassRegistry::init( 'Canton' )->joinAdresse();
            }

			// Référent du parcours
			$query = $this->Dossier->Foyer->Personne->PersonneReferent->completeQdReferentParcours( $query, $criteresapres );

			return $query;
		}
	}
?>