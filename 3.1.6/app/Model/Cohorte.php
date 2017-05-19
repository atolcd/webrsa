<?php
	/**
	 * Fichier source de la classe Cohorte.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Cohorte s'occupe du moteur de recherche des cohortes d'orientation et de la préorientation (CG 93).
	 *
	 * @deprecated since 3.0.00
	 * ATTENTION à la méthode preOrientation
	 *
	 * @package app.Model
	 */
	class Cohorte extends AppModel
	{
		public $name = 'Cohorte';

		public $useTable = false;

		public $actsAs = array( 'Gedooo.Gedooo', 'Conditionnable' );

		/**
		 * Calcul du type de préorientation d'un allocataire (CG 93).
		 *
		 * Dernière version des règles de préorientation:
		 * 	- prise en compte des informations Pôle Emploi le 04/01/2011, par mail
		 * 	- changement règle 4 le 16/04/2010, par mail
		 *
		 * @param array $element
		 * @return string
		 */
		public function preOrientation( $element ) {
			$propo_algo = null;

			/// Inscription Pôle Emploi ?
			$this->Informationpe = Classregistry::init( 'Informationpe' );

			$conditions = $this->Informationpe->qdConditionsJoinPersonneOnValues( 'Informationpe', $element['Personne'] );

			$sqDernierePourPersonne = $this->Informationpe->sqDernierePourPersonne( $element );
			$conditions[] = "Informationpe.id IN ( {$sqDernierePourPersonne} )";

			$informationpe = $this->Informationpe->find(
				'first',
				array(
					'fields' => array(
						'(
							SELECT
									"Historiqueetatpe"."etat"
								FROM "historiqueetatspe" AS "Historiqueetatpe"
								WHERE
									"Historiqueetatpe"."informationpe_id" = "Informationpe"."id"
									ORDER BY "Historiqueetatpe"."date" DESC LIMIT 1
						) AS "Historiqueetatpe__dernieretat"'
					),
					'conditions' => $conditions,
					'contain' => false
				)
			);

			// La personne se retrouve préorientée en emploi si la dernière information
			// venant de Pôle Emploi la concernant est une inscription
			if( !empty( $informationpe ) ) {
				if( @$informationpe['Historiqueetatpe']['dernieretat'] == 'inscription' ) {
					return 'Emploi';
				}
			}

			// On ne peut pas préorienter à partir des informations Pôle Emploi
			if( is_null( $propo_algo ) ) {
				/// Dsp
				$this->Dsp = Classregistry::init( 'Dsp' );
				$dsp = $this->Dsp->find(
					'first',
					array(
						'fields' => array(
							'Dsp.natlog',
							'Dsp.sitpersdemrsa',
							'Dsp.cessderact',
							'Dsp.hispro',
							'Detaildiflog.diflog',
						),
						'conditions' => array( 'Dsp.personne_id' => $element['Personne']['id'] ),
						'contain' => false,
						'joins' => array(
							array(
								'table'      => 'detailsdiflogs',
								'alias'      => 'Detaildiflog',
								'type'       => 'LEFT OUTER',
								'foreignKey' => false,
								'conditions' => array(
									'Detaildiflog.dsp_id = Dsp.id',
									'Detaildiflog.diflog' => '1006'
								)
							),
						)
					)
				);

				/// Règles de gestion déduites depuis les DSP
				if( !empty( $dsp ) ) {
					// Règle 1 (Prioritaire) : Code XML instruction : « NATLOG ». Nature du logement ?
					// 0904 = Logement d'urgence : CHRS → Orientation vers le Social
					// 0911 = Logement précaire : résidence sociale → Orientation vers le Social
					$natlog = Set::classicExtract( $dsp, 'Dsp.natlog' );
					if( empty( $propo_algo ) && !empty( $natlog ) ) {
						if( in_array( $natlog, array( '0904', '0911' ) ) ) {
							$propo_algo = 'Social';
						}
					}

					// Règle 2 (Prioritaire)  : Code XML instruction : « DIFLOG ». Difficultés logement ?
					// 1006 = Fin de bail, expulsion → Orientation vers le Service Social
					$diflog = Set::classicExtract( $dsp, 'Detaildiflog.diflog' );
					if( empty( $propo_algo ) && !empty( $diflog ) ) {
						if( $diflog == '1006' ) {
							$propo_algo = 'Social';
						}
					}

					// Règle 3 (Prioritaire)  : Code XML instruction : « sitpersdemrsa ». "Quel est le motif de votre demande de rSa ?"
					// 0102 = Fin de droits AAH → Orientation vers le Social
					// 0105 = Attente de pension vieillesse ou invalidité‚ ou d'allocation handicap → Orientation vers le Social
					// 0109 = Fin d'études → Orientation vers le Pôle Emploi
					// 0101 = Fin de droits ASSEDIC → Orientation vers le Pôle Emploi
					$sitpersdemrsa = Set::extract( $dsp, 'Dsp.sitpersdemrsa' );
					if( empty( $propo_algo ) && !empty( $sitpersdemrsa ) ) {
						if( in_array( $sitpersdemrsa, array( '0102', '0105' ) ) ) {
							$propo_algo = 'Social';
						}
						else if( in_array( $sitpersdemrsa, array( '0109', '0101' ) ) ) {
							$propo_algo = 'Emploi';
						}
					}

					// Règle 4 : Code XML instruction : « DTNAI ». Date de Naissance.
					$dtnai = Set::extract( $element, 'Personne.dtnai' );
					/// FIXME: change chaque année ...
					$cessderact = Set::extract( $dsp, 'Dsp.cessderact' );

					// Si le code CESSDERACT n'est pas renseigné : Règle 5
					if( empty( $propo_algo ) && !empty( $cessderact ) ) {
						$age = age( $dtnai );

						// Si - de 57 a :
						// "2701" : Encore en activité ou cessation depuis moins d'un an ->Pôle Emploi
						// "2702" : Cessation d'activité depuis plus d'un an -> PDV
						if( $age < 57 ) {
							if( $cessderact == '2701' ) {
								$propo_algo = 'Emploi';
							}
							else if( $cessderact == '2702' ) {
								$propo_algo = 'Socioprofessionnelle';
							}
						}

						// Si + de 57 a :
						// "2701" : Encore en activité ou cessation depuis moins d'un an -> PDV
						// "2702" : Cessation d'activité depuis plus d'un an ->Service Social
						else if( $age >= 57 ) {
							if( $cessderact == '2701' ) {
								$propo_algo = 'Socioprofessionnelle';
							}
							else if( $cessderact == '2702' ) {
								$propo_algo = 'Social';
							}
						}
					}

					// Règle 5 : Code XML instruction : « HISPRO ». Question : Passé professionnel ?
					// 1901 = Oui → Orientation vers le Pôle Emploi
					// 1902 = Oui → Orientation vers le PDV
					// 1903 = Oui → Orientation vers le PDV
					// 1904 = Oui → Orientation vers le PDV
					$hispro = Set::extract( $dsp, 'Dsp.hispro' );
					if( empty( $propo_algo ) && !empty( $hispro ) ) {
						if( $hispro == '1901' ) {
							$propo_algo = 'Emploi';
						}
						else if( in_array( $hispro, array( '1902', '1903', '1904' ) ) ) {
							$propo_algo = 'Socioprofessionnelle';
						}
					}
				}
			}

			return $propo_algo;
		}

		/**
		 * Traitement du formulaire de recherche concernant les statistiques des cohortes d'orientation.
		 *
		 * @param array $mesCodesInsee La liste des codes INSEE à laquelle est lié l'utilisateur
		 * @param boolean $filtre_zone_geo L'utilisateur est-il limité au niveau des zones géographiques ?
		 * @param array $criteres Critères du formulaire de recherche
		 * @return array
		 */
		/*public function statistiques( $mesCodesInsee, $filtre_zone_geo, $criteres ) {
			$Personne = ClassRegistry::init( 'Personne' );
			$Option = ClassRegistry::init( 'Option' );

			$statuts = array( 'Orienté', 'Non orienté', 'En attente' );

			$return = array();

			$typeorient = $criteres['Filtre']['typeorient'];

			foreach( $statuts as $statut ) {
				if( $statut != 'Orienté' ) {
					$criteres['Filtre']['propo_algo'] = $typeorient;
					$criteres['Filtre']['typeorient'] = null;
				}
				else {
					$criteres['Filtre']['propo_algo'] = null;
					$criteres['Filtre']['typeorient'] = $typeorient;
				}

				$querydata = $this->recherche( $statut, $mesCodesInsee, $filtre_zone_geo, $criteres, array() );
				unset( $querydata['fields'] );
				$return[$statut] = $Personne->find( 'count', $querydata );
			}

			return  $return;
		}*/

		/**
		 * Retourne un querydata résultant du traitement du formulaire de recherche des cohortes d'orientation.
		 *
		 * @param string $statutOrientation
		 * @param array $mesCodesInsee La liste des codes INSEE à laquelle est lié l'utilisateur
		 * @param boolean $filtre_zone_geo L'utilisateur est-il limité au niveau des zones géographiques ?
		 * @param array $criteres Critères du formulaire de recherche
		 * @param mixed $lockedDossiers Un array ou une sous-requête (liste des dossiers lockés pour l'utilisateur)
		 * @return array
		 */
		public function search( $statutOrientation, $mesCodesInsee, $filtre_zone_geo, $criteres, $lockedDossiers ) {
			$conditions = array();

			/// Requête
			$Situationdossierrsa = ClassRegistry::init( 'Situationdossierrsa' );

			/// Conditions de base
			$conditions = array();
			$conditions = $this->conditionsSituationdossierrsa( $conditions, $criteres );

//			if( in_array( $statutOrientation, array( 'Calculables', 'Non calculables' ) ) ) {
//				$enattente = Set::classicExtract( $criteres, 'Filtre.enattente' );
//				if( empty( $enattente ) ) {
//					$enattente = array( 'En attente', 'Non orienté' );
//				}
//				$conditions['Orientstruct.statut_orient'] = $enattente;
//			}

			$conditions[] = 'Orientstruct.statut_orient = \''.Sanitize::clean( $statutOrientation, array( 'encode' => false ) ).'\'';

			if( $statutOrientation == 'Orienté' ) {
				// INFO: nouvelle manière de générer les PDFs
				$conditions[] = 'Orientstruct.id IN ( SELECT pdfs.fk_value FROM pdfs WHERE modele = \'Orientstruct\' )';
			}

			if( Hash::get( $criteres, 'Detailcalculdroitrsa.natpf_choice' ) ) {
				$conditions = $this->conditionsDetailcalculdroitrsa( $conditions, $criteres );
			}

			// -----------------------------------------------------------------

			$toppersdrodevorsa = Hash::get( $criteres, 'Filtre.toppersdrodevorsa' );
			if( !is_null( $toppersdrodevorsa ) && ( $toppersdrodevorsa != '' ) ) {
				if( $toppersdrodevorsa === 'NULL' ) {
					$conditions[] = 'Calculdroitrsa.toppersdrodevorsa IS NULL';
				}
				else {
					$conditions['Calculdroitrsa.toppersdrodevorsa'] = $toppersdrodevorsa;
				}
			}

			// -----------------------------------------------------------------

			/// Filtre zone géographique
			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );

			/// Dossiers lockés
			if( !empty( $lockedDossiers ) ) {
				if( is_array( $lockedDossiers ) ) {
					$lockedDossiers = implode( ', ', $lockedDossiers );
				}
				$conditions[] = "NOT {$lockedDossiers}";
			}

			/// Critères
			$oridemrsa = Set::extract( $criteres, 'Filtre.oridemrsa' );
			$nomcom = Set::extract( $criteres, 'Filtre.nomcom' );
			$numcom = Set::extract( $criteres, 'Filtre.numcom' );
			$codepos = Set::extract( $criteres, 'Filtre.codepos' );
			$dtdemrsa = Set::extract( $criteres, 'Filtre.dtdemrsa' );
			$date_impression = Set::extract( $criteres, 'Filtre.date_impression' );
			$date_print = Set::extract( $criteres, 'Filtre.date_print' );
			$date_valid = Set::extract( $criteres, 'Filtre.date_valid' );
			if ( isset( $criteres['Filtre']['typeorient'] ) && !empty( $criteres['Filtre']['typeorient'] ) ) {
				$typeorient = Set::extract( $criteres, 'Filtre.typeorient' );
			}
			elseif ( isset( $criteres['Filtre']['propo_algo'] ) && !empty( $criteres['Filtre']['propo_algo'] ) ) {
				$preorient = Set::extract( $criteres, 'Filtre.propo_algo' );
			}

			$hasDsp = Set::extract( $criteres, 'Filtre.hasDsp' );
			//-------------------------------------------------------
			$cantons = Set::extract( $criteres, 'Filtre.cantons' );

			if( isset( $typeorient ) && !empty( $typeorient ) ) {
				if( Configure::read( 'with_parentid' ) ) { // TODO: subquery
					$conditions[] = 'Orientstruct.typeorient_id IN ( SELECT typesorients.id FROM typesorients WHERE typesorients.parentid = \''.Sanitize::clean( $typeorient, array( 'encode' => false ) ).'\' )';
				}
				else {
					$conditions[] = 'Orientstruct.typeorient_id = \''.Sanitize::clean( $typeorient, array( 'encode' => false ) ).'\'';
				}
			}
			elseif( isset( $preorient ) && !empty( $preorient ) ) {
				if ( $preorient == 'NULL' ) {
					$conditions[] = 'Orientstruct.propo_algo IS NULL';
				}
				else if ( $preorient == 'NOTNULL' ) {
					$conditions[] = 'Orientstruct.propo_algo IS NOT NULL';
				}
				else {
					$conditions[] = 'Orientstruct.propo_algo = \''.Sanitize::clean( $preorient, array( 'encode' => false ) ).'\'';
				}
			}

			//-------------------------------------------------------

			if( isset( $criteres['Filtre']['origine'] ) && !empty( $criteres['Filtre']['origine'] ) ) {
				$conditions[] = 'Orientstruct.origine = \''.Sanitize::clean( $criteres['Filtre']['origine'], array( 'encode' => false ) ).'\'';
			}

			// Origine de la demande
			if( !empty( $oridemrsa ) ) {
				$conditions[] = 'Detaildroitrsa.oridemrsa IN ( \''.implode( '\', \'', $oridemrsa ).'\' )';
			}

			// Critères sur une personne du foyer - nom, prénom, nom de naissance -> FIXME: seulement demandeur pour l'instant
			$filtersPersonne = array();
			foreach( array( 'nom', 'prenom', 'nomnai' ) as $criterePersonne ) {
				if( isset( $criteres['Filtre'][$criterePersonne] ) && !empty( $criteres['Filtre'][$criterePersonne] ) ) {
					$conditions[] = 'Personne.'.$criterePersonne.' ILIKE \''.$this->wildcard( replace_accents( $criteres['Filtre'][$criterePersonne] ) ).'\'';
				}
			}

			// Localité adresse
			if( !empty( $nomcom ) ) {
				$conditions[] = 'Adresse.nomcom ILIKE \'%'.Sanitize::clean( $nomcom, array( 'encode' => false ) ).'%\'';
			}
			// Commune au sens INSEE
			if( !empty( $numcom ) ) {
				$conditions[] = 'Adresse.numcom = \''.Sanitize::clean( $numcom, array( 'encode' => false ) ).'\'';
			}
			// Code postal adresse
			if( !empty( $codepos ) ) {
				$conditions[] = 'Adresse.codepos = \''.Sanitize::clean( $codepos, array( 'encode' => false ) ).'\'';
			}

			/// Critères sur l'adresse - canton
			if( Configure::read( 'CG.cantons' ) ) {
				if( isset( $criteres['Canton']['canton'] ) && !empty( $criteres['Canton']['canton'] ) ) {
					$this->Canton = ClassRegistry::init( 'Canton' );
					$tmpConditions = $this->Canton->queryConditions( $criteres['Canton']['canton'] );
					$_conditions = array();
					foreach( $tmpConditions['or'] as $tmpCondition ) {
						$_condition = array();
						foreach( $tmpCondition as $field => $value ) {
							if( valid_int( $value ) ) {
								$_condition[] = "$field = '".str_replace( "'", "\\'", $value )."'";
							}
							else {
								$_condition[] = "$field '".str_replace( "'", "\\'", $value )."'";
							}
						}
						if( !empty( $_condition ) ) {
							$_conditions[] = '( '.implode( ') AND (', $_condition ).' )';
						}
					}
					if( !empty( $_conditions ) ) {
						$conditions[] = '( ( '.implode( ') OR (', $_conditions ).' ) )';
					}
				}
			}

			// Date de demande
			if( !empty( $dtdemrsa ) && $dtdemrsa != 0 ) {
				$dtdemrsa_from = Set::extract( $criteres, 'Filtre.dtdemrsa_from' );
				$dtdemrsa_to = Set::extract( $criteres, 'Filtre.dtdemrsa_to' );
				// FIXME: vérifier le bon formatage des dates
				$dtdemrsa_from = $dtdemrsa_from['year'].'-'.$dtdemrsa_from['month'].'-'.$dtdemrsa_from['day'];
				$dtdemrsa_to = $dtdemrsa_to['year'].'-'.$dtdemrsa_to['month'].'-'.$dtdemrsa_to['day'];

				$conditions[] = 'Dossier.dtdemrsa BETWEEN \''.$dtdemrsa_from.'\' AND \''.$dtdemrsa_to.'\'';
			}

			// Statut impression
			if( !empty( $date_impression ) && in_array( $date_impression, array( 'I', 'N' ) ) ) {
				if( $date_impression == 'I' ) {
					$conditions[] = 'Orientstruct.date_impression IS NOT NULL';
				}
				else {
					$conditions[] = 'Orientstruct.date_impression IS NULL';
				}
			}

			// Date d'orientation
			if( !empty( $date_valid ) && $date_valid != 0 ) {
				$date_valid_from = Set::extract( $criteres, 'Filtre.date_valid_from' );
				$date_valid_to = Set::extract( $criteres, 'Filtre.date_valid_to' );
				// FIXME: vérifier le bon formatage des dates
				$date_valid_from = $date_valid_from['year'].'-'.$date_valid_from['month'].'-'.$date_valid_from['day'];
				$date_valid_to = $date_valid_to['year'].'-'.$date_valid_to['month'].'-'.$date_valid_to['day'];

				$conditions[] = 'Orientstruct.date_valid BETWEEN \''.$date_valid_from.'\' AND \''.$date_valid_to.'\'';
			}

			// Date d'impression
			if( !empty( $date_print ) && $date_print != 0 ) {
				$date_impression_from = Set::extract( $criteres, 'Filtre.date_impression_from' );
				$date_impression_to = Set::extract( $criteres, 'Filtre.date_impression_to' );
				// FIXME: vérifier le bon formatage des dates
				$date_impression_from = $date_impression_from['year'].'-'.$date_impression_from['month'].'-'.$date_impression_from['day'];
				$date_impression_to = $date_impression_to['year'].'-'.$date_impression_to['month'].'-'.$date_impression_to['day'];

				$conditions[] = 'Orientstruct.date_impression BETWEEN \''.$date_impression_from.'\' AND \''.$date_impression_to.'\'';
			}

			// Trouver la dernière demande RSA pour chacune des personnes du jeu de résultats
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $criteres );

			// INFO: on veut récupérer tout ce qui est orienté (LEFT OUTER) et ne garder que ce qui a du sens pour l'orientation (INNER)
			$joinType = ( ( $statutOrientation == 'Orienté' ) ? 'LEFT OUTER' : 'INNER' );

			// INFO: n'apparaît plus que dans l'export CSV
			$sqDernierContrat = ClassRegistry::init( 'Contratinsertion' )->sq(
				array(
					'alias' => 'contratsinsertion',
					'fields' => array( 'contratsinsertion.dd_ci' ),
					'contain' => false,
					'conditions' => array( 'contratsinsertion.personne_id = Personne.id' ),
					'order' => array( 'contratsinsertion.dd_ci DESC' ),
					'limit' => 1
				)
			);

			// Présence de DSPs
			if( !empty( $hasDsp ) && in_array( $hasDsp, array( 'O', 'N' ) ) ) {
				if( $hasDsp == 'O' ) {
					$conditions[] = 'Dsp.id IS NOT NULL';
				}
				else {
					$conditions[] = 'Dsp.id IS NULL';
				}
			}

			$fields = array(
				'Dossier.id',
				'Dossier.numdemrsa',
				'Dossier.dtdemrsa',
				'Dossier.matricule',
				'( CASE WHEN dtdemrsa >= \'2009-06-01 00:00:00\' THEN \'Nouvelle demande\' ELSE \'Diminution des ressources\' END ) AS "Dossier__statut"',
				'Personne.id',
				'Personne.nom',
				'Personne.prenom',
				'Personne.nir',
				'Personne.dtnai',
				'Foyer.dossier_id',
				'Dsp.id',
				'Adresse.nomcom',
				'Adresse.codepos',
				'Adresse.canton',
				'Adresse.numcom',
				'Situationdossierrsa.dtclorsa',
				'Situationdossierrsa.moticlorsa',
				'Suiviinstruction.typeserins',
				'Orientstruct.id',
				'Orientstruct.date_valid',
				'Orientstruct.propo_algo',
				'Orientstruct.date_propo',
				'Orientstruct.typeorient_id',
				'Orientstruct.structurereferente_id',
				'Orientstruct.origine',
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Orientstruct.statut_orient',
				'( '.$sqDernierContrat.' ) AS "Contratinsertion__dd_ci"',
				'"Prestation"."rolepers"',
				'"Situationdossierrsa"."etatdosrsa"',
			);

			$queryData = array(
				'fields' => $fields,
				'joins' => array(
					array(
						'table' => 'prestations',
						'alias' => 'Prestation',
						'type' => $joinType,
						'foreignKey' => false,
						'conditions' => array(
							'Personne.id = Prestation.personne_id',
							'Prestation.natprest' => 'RSA',
							'Prestation.rolepers' => array( 'DEM', 'CJT' ),
						)
					),
					array(
						'table' => 'calculsdroitsrsa',
						'alias' => 'Calculdroitrsa',
						'type' => $joinType,
						'foreignKey' => false,
						'conditions' => array( 'Personne.id = Calculdroitrsa.personne_id' )
					),
					array(
						'table' => 'dsps',
						'alias' => 'Dsp',
						'type' => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'Personne.id = Dsp.personne_id',
							'Dsp.id IN ('
								.ClassRegistry::init( 'Dsp' )->WebrsaDsp->sqDerniereDsp()
							.')'
						)
					),
					array(
						'table' => 'foyers',
						'alias' => 'Foyer',
						'type' => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Personne.foyer_id = Foyer.id' )
					),
					array(
						'table' => 'dossiers',
						'alias' => 'Dossier',
						'type' => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Dossier.id = Foyer.dossier_id' )
					),
					array(
						'table' => 'suivisinstruction',
						'alias' => 'Suiviinstruction',
						'type' => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'Dossier.id = Suiviinstruction.dossier_id',
							'Suiviinstruction.id IN (
								SELECT suivisinstruction.id
									FROM suivisinstruction
									WHERE suivisinstruction.dossier_id = Suiviinstruction.dossier_id
									ORDER BY suivisinstruction.id DESC
									LIMIT 1
							)'
						)
					),
					array(
						'table' => 'adressesfoyers',
						'alias' => 'Adressefoyer',
						'type' => $joinType,
						'foreignKey' => false,
						'conditions' => array(
							'Adressefoyer.foyer_id = Foyer.id',
							'Adressefoyer.rgadr' => '01',
							'Adressefoyer.id IN (
								'.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01( 'Adressefoyer.foyer_id' ).'
							)'
						),
					),
					array(
						'table' => 'adresses',
						'alias' => 'Adresse',
						'type' => $joinType,
						'foreignKey' => false,
						'conditions' => array( 'Adresse.id = Adressefoyer.adresse_id' )
					),
					array(
						'table' => 'orientsstructs',
						'alias' => 'Orientstruct',
						'type' => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Orientstruct.personne_id = Personne.id', )
					),
					array(
						'table' => 'typesorients',
						'alias' => 'Typeorient',
						'type' => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Typeorient.id = Orientstruct.typeorient_id' )
					),
					array(
						'table' => 'structuresreferentes',
						'alias' => 'Structurereferente',
						'type' => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Structurereferente.id = Orientstruct.structurereferente_id' )
					),
					array(
						'table' => 'detailsdroitsrsa',
						'alias' => 'Detaildroitrsa',
						'type' => $joinType,
						'foreignKey' => false,
						'conditions' => array( 'Detaildroitrsa.dossier_id = Dossier.id', )
					),
					array(
						'table' => 'situationsdossiersrsa',
						'alias' => 'Situationdossierrsa',
						'type' => $joinType,
						'foreignKey' => false,
						'conditions' => array( 'Situationdossierrsa.dossier_id = Dossier.id', )
					),
				),
				'conditions' => $conditions,
				'order' => 'Dossier.dtdemrsa ASC',
				'contain' => false,
				'recursive' => -1,
			);

			$queryData = ClassRegistry::init( 'PersonneReferent' )->completeQdReferentParcours( $queryData, $criteres['Filtre'] );


			return $queryData;
		}

		/**
		 * Retourne un array à deux niveaux de clés permettant de connaître une structure référente à partir
		 * d'un type d'orientation et d'une zone géographique, afin de permettre de désigner automatiquement
		 * une structure référente à un allocataire.
		 *
		 * Le résultat est mis en cache.
		 *
		 * @deprecated since 3.0.00 use WebrsaCohorteOrientstructNouvelle::structuresAutomatiques()
		 *
		 * @return array
		 */
		public function structuresAutomatiques() {
			return ClassRegistry::init( 'WebrsaCohorteOrientstructNouvelle' )->structuresAutomatiques();
			/*$cacheKey = 'cohorte_structures_automatiques';
			$results = Cache::read( $cacheKey );

			if( $results === false ) {
				$this->Structurereferente = ClassRegistry::init( 'Structurereferente' );
				$this->Typeorient = ClassRegistry::init( 'Typeorient' );

				// FIXME: valeurs magiques
				$intitule = null;
				if( Configure::read( 'Cg.departement' ) == 66 ) {
					$intitule = array( 'Emploi', 'Social', 'Préprofessionnelle' );
				}
				else if( Configure::read( 'Cg.departement' ) == 93 ) {
					$intitule = array( 'Emploi', 'Social', 'Socioprofessionnelle' );
				}
				else if( Configure::read( 'Cg.departement' ) == 58 ) {
					$intitule = array( 'Professionnelle', 'Sociale' );
				}

				$typesPermis = $this->Typeorient->find(
					'list',
					array(
						'conditions' => array(
							'Typeorient.lib_type_orient' => $intitule
						),
						'recursive' => -1
					)
				);
				$typesPermis = array_keys( $typesPermis );

				$structures = $this->Structurereferente->find(
					'all',
					array(
						'conditions' => array(
							'Structurereferente.typeorient_id' => $typesPermis
						),
						'contain' => array(
							'Zonegeographique'
						)
					)
				);


				$results = array();
				foreach( $structures as $structure ) {
					if( !empty( $structure['Zonegeographique'] ) ) {
						foreach( $structure['Zonegeographique'] as $zonegeographique ) {
							$results[$structure['Structurereferente']['typeorient_id']][$zonegeographique['codeinsee']] = $structure['Structurereferente']['typeorient_id'].'_'.$structure['Structurereferente']['id'];
						}
					}
				}

				Cache::write( $cacheKey, $results );
				ModelCache::write( $cacheKey, array( 'Structurereferente', 'Typeorient', 'Zonegeographique' ) );
			}

			return $results;*/
		}

		/**
		 * Suppression et regénération du cache.
		 *
		 * @deprecated since 3.0.00
		 *
		 * @return boolean
		 */
		protected function _regenerateCache() {
			// Suppression des éléments du cache.
			$this->_clearModelCache();

			// Regénération des éléments du cache.
			$success = ( $this->structuresAutomatiques() !== false );

			return $success;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * @deprecated since 3.0.00
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les fonctions vides.
		 */
		public function prechargement() {
			$success = $this->_regenerateCache();
			return $success;
		}
	}
?>