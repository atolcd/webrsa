<?php
	/**
	 * Code source de la classe Critere.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Critere ...
	 *
	 * @package app.Model
	 * @deprecated since version 3.0.0
	 * @see WebrsaRechercheOrientstruct
	 */
	class Critere extends AppModel
	{
		public $name = 'Critere';

		public $useTable = false;

		public $actsAs = array( 'Conditionnable' );

		/**
		 *
		 * @param array $criteres
		 * @param boolean $reorientationEp
		 * @return array
		 */
		public function search( $criteres, $reorientationEp = false ) {
			/// Conditions de base
			$conditions = array();

			/// Critères
			$nomcom = Set::extract( $criteres, 'Adresse.nomcom' );
			$numcom = Set::extract( $criteres, 'Adresse.numcom' );
			$natpf = Set::extract( $criteres, 'Detaildroitrsa.natpf' );
			$nir = Set::extract( $criteres, 'Personne.nir' );
			$statut_orient = Set::extract( $criteres, 'Orientstruct.statut_orient' );
			$typeorient_id = Set::extract( $criteres, 'Orientstruct.typeorient_id' );
			$structurereferente_id = Set::extract( $criteres, 'Orientstruct.structurereferente_id' );
			$serviceinstructeur_id = Set::extract( $criteres, 'Orientstruct.serviceinstructeur_id' );
			$dtnai = Set::extract( $criteres, 'Personne.dtnai' );
			$matricule = Set::extract( $criteres, 'Dossier.matricule' );
			$identifiantpe = Set::extract( $criteres, 'Historiqueetatpe.identifiantpe' );
			$structureorientante_id = Set::extract( $criteres, 'Orientstruct.structureorientante_id' );
			$referentorientant_id = Set::extract( $criteres, 'Orientstruct.referentorientant_id' );


			$conditions = $this->conditionsAdresse( $conditions, $criteres );
			$conditions = $this->conditionsPersonneFoyerDossier( $conditions, $criteres );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $criteres );


			/// Critères sur l'orientation - date d'orientation
			if( isset( $criteres['Orientstruct']['date_valid'] ) && !empty( $criteres['Orientstruct']['date_valid'] ) ) {
				$valid_from = ( valid_int( $criteres['Orientstruct']['date_valid_from']['year'] ) && valid_int( $criteres['Orientstruct']['date_valid_from']['month'] ) && valid_int( $criteres['Orientstruct']['date_valid_from']['day'] ) );
				$valid_to = ( valid_int( $criteres['Orientstruct']['date_valid_to']['year'] ) && valid_int( $criteres['Orientstruct']['date_valid_to']['month'] ) && valid_int( $criteres['Orientstruct']['date_valid_to']['day'] ) );
				if( $valid_from && $valid_to ) {
					$conditions[] = 'Orientstruct.date_valid BETWEEN \''.implode( '-', array( $criteres['Orientstruct']['date_valid_from']['year'], $criteres['Orientstruct']['date_valid_from']['month'], $criteres['Orientstruct']['date_valid_from']['day'] ) ).'\' AND \''.implode( '-', array( $criteres['Orientstruct']['date_valid_to']['year'], $criteres['Orientstruct']['date_valid_to']['month'], $criteres['Orientstruct']['date_valid_to']['day'] ) ).'\'';
				}
			}

			// Trouver la dernière orientation pour chacune des personnes du jeu de résultats
			if( isset( $criteres['Orientstruct']['derniere'] ) && $criteres['Orientstruct']['derniere'] ) {
				$conditions[] = 'Orientstruct.id IN (
					SELECT
						orientsstructs.id
					FROM
						orientsstructs
						WHERE
							orientsstructs.personne_id = Orientstruct.personne_id
						ORDER BY
							orientsstructs.date_valid DESC,
							orientsstructs.id DESC
						LIMIT 1
				)';
			}

			// ...
			if( !empty( $statut_orient ) ) {
				$conditions[] = 'Orientstruct.statut_orient = \''.Sanitize::clean( $statut_orient, array( 'encode' => false ) ).'\'';
				// INFO: nouvelle manière de générer les PDFs
				if( $statut_orient == 'Orienté' && Configure::read( 'Cg.departement' ) != 66 ) {
					$conditions[] = 'Orientstruct.id IN ( SELECT pdfs.fk_value FROM pdfs WHERE modele = \'Orientstruct\' )';
				}
			}


			// Recherche par identifiant Pôle Emploi
			if( !empty( $identifiantpe ) ) {
				$conditions[] = ClassRegistry::init( 'Historiqueetatpe' )->conditionIdentifiantpe( $identifiantpe );
			}

			// ...
			if( !empty( $typeorient_id ) ) {
				// TODO: à mettre dans ConditionnableBehavior (problème, le chemin est Critere.typeorient_id)
				if( Configure::read( 'with_parentid' ) ) {
					$Typeorient = ClassRegistry::init( 'Typeorient' );
					$sqTypeorient = $Typeorient->sq(
						array(
							'alias' => 'typesorients',
							'fields' => array(
								'typesorients.id'
							),
							'conditions' => array(
								'OR' => array(
									'typesorients.id' => $typeorient_id,
									'typesorients.parentid' => $typeorient_id
								)
							),
							'contain' => false
						)
					);
					$conditions[] = "Orientstruct.typeorient_id IN ( {$sqTypeorient} )";
				}
				else {
					$conditions['Orientstruct.typeorient_id'] = $typeorient_id;
				}
			}

			// ...
			if( !empty( $structurereferente_id ) ) {
				$conditions[] = 'Orientstruct.structurereferente_id = \''.Sanitize::clean( suffix( $structurereferente_id ), array( 'encode' => false ) ).'\'';
			}

			// Recherche sur la structureréférente ayant fait l'orientation
			if( !empty( $structureorientante_id ) ) {
				$conditions[] = 'Orientstruct.structureorientante_id = \''.Sanitize::clean( $structureorientante_id, array( 'encode' => false ) ).'\'';
			}

			// Recherche sur le référent ayant fait l'orientation
			if( !empty( $referentorientant_id ) ) {
				$conditions[] = 'Orientstruct.referentorientant_id = \''.Sanitize::clean( suffix( $referentorientant_id ), array( 'encode' => false ) ).'\'';
			}

			// ... FIXME
			if( !empty( $serviceinstructeur_id ) ) {

				$conditions[] = 'Serviceinstructeur.id = \''.Sanitize::clean( $serviceinstructeur_id, array( 'encode' => false ) ).'\'';
			}

			$hasContrat  = Set::extract( $criteres, 'Critere.hascontrat' );

			/// Statut contrat engagement reciproque
			if( !empty( $hasContrat ) && in_array( $hasContrat, array( 'O', 'N' ) ) ) {
				if( $hasContrat == 'O' ) {
					$conditions[] = '( SELECT COUNT(contratsinsertion.id) FROM contratsinsertion WHERE contratsinsertion.personne_id = "Personne"."id" ) > 0';
				}
				else {
					$conditions[] = '( SELECT COUNT(contratsinsertion.id) FROM contratsinsertion WHERE contratsinsertion.personne_id = "Personne"."id" ) = 0';
				}
			}

			if( isset( $criteres['Orientstruct']['origine'] ) && !empty( $criteres['Orientstruct']['origine'] ) ) {
				$conditions[] = 'Orientstruct.origine = \''.Sanitize::clean( $criteres['Orientstruct']['origine'], array( 'encode' => false ) ).'\'';
			}

			$hasReferent  = Set::extract( $criteres, 'Critere.hasreferent' );
			/// Statut contrat engagement reciproque
			if( !empty( $hasReferent ) && in_array( $hasReferent, array( 'O', 'N' ) ) ) {
				if( $hasReferent == 'O' ) {
					$conditions[] = '( SELECT COUNT(personnes_referents.id) FROM personnes_referents WHERE personnes_referents.personne_id = "Personne"."id" AND personnes_referents.dfdesignation IS NULL  ) > 0';
				}
				else {
					$conditions[] = '(
						( SELECT personnes_referents.dfdesignation
							FROM personnes_referents
							WHERE
								personnes_referents.personne_id = "Personne"."id"
							ORDER BY personnes_referents.dddesignation DESC,
									personnes_referents.id DESC
							LIMIT 1
						) IS NOT NULL
						OR
						( SELECT COUNT(personnes_referents.id)
							FROM personnes_referents
							WHERE personnes_referents.personne_id = "Personne"."id"
						) = 0
					)';
				}
			}

			// Permet d'obtenir une et une seule entrée de la table informationspe
			$sqDerniereInformationpe = ClassRegistry::init( 'Informationpe' )->sqDerniere( 'Personne' );
			$conditions[] = array(
				'OR' => array(
					"Informationpe.id IS NULL",
					"Informationpe.id IN ( {$sqDerniereInformationpe} )"
				)
			);

			/// Inscrit à PE ?
			$isinscritpe  = Set::extract( $criteres, 'Critere.isinscritpe' );
			if( !empty( $isinscritpe ) && in_array( $isinscritpe, array( 'O', 'N' ) ) ) {
				if( $isinscritpe == 'O' ) {
					$conditions['Historiqueetatpe.etat'] = 'inscription';
				}
				else {
					$conditions['Historiqueetatpe.etat <>'] = 'inscription';
				}
			}

			// On s'assure d'avoir la dernière adresse de rang 01 ou pas d'adresse du tout
			$conditions[] = array(
				'OR' => array(
					'Adressefoyer.id IS NULL',
					"Adressefoyer.id IN ( ".ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01('Adressefoyer.foyer_id')." )"
				)
			);

			/// Requête
			$Situationdossierrsa = ClassRegistry::init( 'Situationdossierrsa' );
			$dbo = $this->getDataSource( $this->useDbConfig );

			$querydata = array(
				'fields' => array(
					'"Orientstruct"."id"',
					'"Orientstruct"."personne_id"',
					'"Orientstruct"."typeorient_id"',
					'"Orientstruct"."structurereferente_id"',
					'"Orientstruct"."propo_algo"',
					'"Orientstruct"."valid_cg"',
					'"Orientstruct"."date_propo"',
					'"Orientstruct"."date_valid"',
					'"Orientstruct"."statut_orient"',
					'"Orientstruct"."date_impression"',
					'"Orientstruct"."origine"',
					'"Dossier"."id"',
					'"Dossier"."numdemrsa"',
					'"Dossier"."matricule"',
					'"Dossier"."dtdemrsa"',
					'Typeorient.lib_type_orient',
					'"Structurereferente"."lib_struc"',
					'"Personne"."id"',
					'"Personne"."nom"',
					'"Personne"."prenom"',
					'"Personne"."nir"',
					'"Personne"."dtnai"',
					'"Personne"."qual"',
					'"Personne"."nomcomnai"',
					'"Adresse"."numvoie"',
					'"Adresse"."nomvoie"',
					'"Adresse"."complideadr"',
					'"Adresse"."compladr"',
					'"Adresse"."libtypevoie"',
					'"Adresse"."codepos"',
					'"Adresse"."nomcom"',
					'"Adresse"."numcom"',
					'"Modecontact"."numtel"',
					'"Serviceinstructeur"."id"',
					'"Serviceinstructeur"."lib_service"',
					'"Situationdossierrsa"."etatdosrsa"',
					'"Calculdroitrsa"."toppersdrodevorsa"',
					'"PersonneReferent"."referent_id"',
					'Historiqueetatpe.identifiantpe',
					'"Prestation"."rolepers"',
                    'Canton.id',
                    'Canton.canton',
				),
				'recursive' => -1,
				'joins' => array(
					array(
						'table'      => $dbo->fullTableName( ClassRegistry::init( 'Personne' ), false, false ), /// FIXME: performances -> à faire pour les autres ou pas ?
						'alias'      => 'Personne',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Personne.id = Orientstruct.personne_id' )
					),
					array(
						'table'      => 'prestations',
						'alias'      => 'Prestation',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'Prestation.personne_id = Personne.id',
							'Prestation.natprest = \'RSA\'',
							'Prestation.rolepers IN ( \'DEM\', \'CJT\' )'
						)
					),
					ClassRegistry::init( 'Informationpe' )->joinPersonneInformationpe(),
					ClassRegistry::init( 'Historiqueetatpe' )->joinInformationpeHistoriqueetatpe(),
					array(
						'table'      => 'calculsdroitsrsa',
						'alias'      => 'Calculdroitrsa',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Calculdroitrsa.personne_id = Personne.id' )
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
						'table'      => 'modescontact',
						'alias'      => 'Modecontact',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'Modecontact.foyer_id = Foyer.id',
							'Modecontact.id IN (
								'.ClassRegistry::init( 'Modecontact' )->sqDerniere('Modecontact.foyer_id').'
							)'
						)
					),
					array(
						'table'      => 'typesorients',
						'alias'      => 'Typeorient',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Typeorient.id = Orientstruct.typeorient_id' )
					),
					array(
						'table'      => 'structuresreferentes',
						'alias'      => 'Structurereferente',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Orientstruct.structurereferente_id = Structurereferente.id' )
					),
					array(
						'table'      => 'suivisinstruction',
						'alias'      => 'Suiviinstruction',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'Suiviinstruction.dossier_id = Dossier.id',
							'Suiviinstruction.id IN (
								'.ClassRegistry::init( 'Suiviinstruction' )->sqDerniere('Suiviinstruction.dossier_id').'
							)'
						)
					),
					array(
						'table'      => 'servicesinstructeurs',
						'alias'      => 'Serviceinstructeur',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Suiviinstruction.numdepins = Serviceinstructeur.numdepins AND Suiviinstruction.typeserins = Serviceinstructeur.typeserins AND Suiviinstruction.numcomins = Serviceinstructeur.numcomins AND Suiviinstruction.numagrins = Serviceinstructeur.numagrins' )
					),
					array(
						'table'      => 'situationsdossiersrsa',
						'alias'      => 'Situationdossierrsa',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Situationdossierrsa.dossier_id = Dossier.id /*AND ( Situationdossierrsa.etatdosrsa IN ( \''.implode( '\', \'', $Situationdossierrsa->etatOuvert() ).'\' ) )*/' )
					),
					array(
						'table'      => 'detailsdroitsrsa',
						'alias'      => 'Detaildroitrsa',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Detaildroitrsa.dossier_id = Dossier.id' )
					),
                    array(
                        'table'      => 'adressesfoyers',
                        'alias'      => 'Adressefoyer',
                        'type'       => 'LEFT OUTER',
                        'foreignKey' => false,
                        'conditions' => array( 'Foyer.id = Adressefoyer.foyer_id' )
                    ),
                    array(
                        'table'      => 'adresses',
                        'alias'      => 'Adresse',
                        'type'       => 'LEFT OUTER',
                        'foreignKey' => false,
                        'conditions' => array( 'Adresse.id = Adressefoyer.adresse_id' )
                    ),
                    ClassRegistry::init( 'Canton' )->joinAdresse()
				),
				'limit' => 10,
				'conditions' => $conditions
			);

			// Réorientation suite à passage en EP (pour le CG connecté) ?
			if( $reorientationEp ) {
				$querydata = $Situationdossierrsa->Dossier->Foyer->Personne->Dossierep->completeQdReorientation( $querydata );
			}

			// Référent du parcours
			$querydata = $Situationdossierrsa->Dossier->Foyer->Personne->PersonneReferent->completeQdReferentParcours( $querydata, $criteres );

			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$Personne = ClassRegistry::init( 'Personne' );
				$querydata['fields'][] = 'Activite.act';
				$querydata['joins'][] = $Personne->join(
					'Activite',
					array(
						'type' => 'LEFT OUTER',
						'conditions' => array(
							'Activite.id IN ( '.$Personne->Activite->sqDerniere().' )'
						),
					)
				);
			}

			return $querydata;
		}
	}
?>