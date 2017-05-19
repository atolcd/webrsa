<?php
	/**
	 * Fichier source de la classe Cohorteci.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Cohorteci fournit un traitement des filtres de recherche concernant les CER.
	 *
	 * @deprecated since 3.0.00 (? -> vérifier recherche / cohorte)
	 *
	 * @package app.Model
	 */
	class Cohorteci extends AppModel
	{
		public $name = 'Cohorteci';

		public $useTable = false;

		public $actsAs = array( 'Conditionnable' );

		/**
		 * Retourne un querydata résultant du traitement du formulaire de recherche des cohortes de CER.
		 *
		 * @param type $statutValidation
		 * @param array $criteresci Critères du formulaire de recherche
		 * @param array $etatsdosrsa Une restriction éventuelle sur les états du dossier
		 * @return array
		 */
		public function search( $statutValidation, array $criteresci, array $etatsdosrsa = array() ) {
			/// Conditions de base
			$conditions = array();

            $this->Contratinsertion = ClassRegistry::init( 'Contratinsertion' );

            $conditions[] = array(
				array(
                    'OR' => array(
                        'Adressefoyer.id IS NULL',
                        'Adressefoyer.id IN ( '.$this->Contratinsertion->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
                    )
                ),
                'Prestation.rolepers' => array( 'DEM', 'CJT' )
			);

			if( !empty( $statutValidation ) ) {
				if( $statutValidation == 'Decisionci::nouveauxsimple' ) {
					$conditions[] = '( ( Contratinsertion.forme_ci = \'S\' ) AND ( ( Contratinsertion.decision_ci = \'E\' ) OR ( Contratinsertion.decision_ci IS NULL ) ) )';
				}
				else if( $statutValidation == 'Decisionci::nouveauxparticulier' ) {
					$conditions[] = '( ( Contratinsertion.forme_ci = \'C\' ) AND ( ( Contratinsertion.decision_ci = \'E\' ) OR ( Contratinsertion.decision_ci IS NULL ) ) )';
				}
				else if( $statutValidation == 'Decisionci::nouveaux' ) {
					$conditions[] = '( ( Contratinsertion.decision_ci = \'E\' ) OR ( Contratinsertion.decision_ci IS NULL ) )';
				}
				else if( $statutValidation == 'Decisionci::valides' ) {
					$conditions[] = 'Contratinsertion.decision_ci IS NOT NULL';
					$conditions[] = 'Contratinsertion.decision_ci <> \'E\'';
				}

				if( Configure::read( 'Cg.departement' ) == 93 ) {
					// Si on veut valider des CER complexes, on s'assurera qu'ils ne sont
					// pas en EP pour validation de contrat complexe, ou alors dans un état annulé
					if( in_array( $statutValidation, array( 'Decisionci::nouveaux'/*, 'Decisionci::enattente'*/ ) ) ) {
						$ModeleContratcomplexeep93 = ClassRegistry::init( 'Contratcomplexeep93' );
						$conditions[] = 'Contratinsertion.id NOT IN (
							'.$ModeleContratcomplexeep93->sq(
								array(
									'fields' => array( 'contratscomplexeseps93.contratinsertion_id' ),
									'alias' => 'contratscomplexeseps93',
									'joins' => array(
										array(
											'table'      => 'dossierseps',
											'alias'      => 'dossierseps',
											'type'       => 'INNER',
											'foreignKey' => false,
											'conditions' => array( 'dossierseps.id = contratscomplexeseps93.dossierep_id' )
										),
									),
									'conditions' => array(
										'contratscomplexeseps93.contratinsertion_id = Contratinsertion.id',
										'dossierseps.id NOT IN (
											'.$ModeleContratcomplexeep93->Dossierep->Passagecommissionep->sq(
												array(
													'fields' => array( 'passagescommissionseps.dossierep_id' ),
													'alias' => 'passagescommissionseps',
													'conditions' => array(
														'passagescommissionseps.dossierep_id = dossierseps.id',
														'passagescommissionseps.etatdossierep' => 'annule',
													),
												)
											).'
										)'
									),
								)
							).'
						)';

					}
				}
			}

			/// Critères
// 			$created = Set::extract( $criteresci, 'Contratinsertion.created' );
			$decision_ci = Set::extract( $criteresci, 'Contratinsertion.decision_ci' );
			$datevalidation_ci = Set::extract( $criteresci, 'Contratinsertion.datevalidation_ci' );
			$dd_ci = Set::extract( $criteresci, 'Contratinsertion.dd_ci' );
			$df_ci = Set::extract( $criteresci, 'Contratinsertion.df_ci' );
			$nir = Set::extract( $criteresci, 'Contratinsertion.nir' );
			$natpf = Set::extract( $criteresci, 'Contratinsertion.natpf' );
			$personne_suivi = Set::extract( $criteresci, 'Contratinsertion.pers_charg_suivi' );
			$forme_ci = Set::extract( $criteresci, 'Contratinsertion.forme_ci' );
			$structurereferente_id = Set::extract( $criteresci, 'Contratinsertion.structurereferente_id' );
			$referent_id = Set::extract( $criteresci, 'Contratinsertion.referent_id' );
			$matricule = Set::extract( $criteresci, 'Contratinsertion.matricule' );
			$positioncer = Set::extract( $criteresci, 'Contratinsertion.positioncer' );

            $conditions = $this->conditionsDates( $conditions, $criteresci, 'Contratinsertion.created' );
            $conditions = $this->conditionsDates( $conditions, $criteresci, 'Contratinsertion.dd_ci' );
            $conditions = $this->conditionsDates( $conditions, $criteresci, 'Contratinsertion.df_ci' );

			// Plage de dates pour la date de validation lors d'une recherche
			if( $statutValidation === null ) {
				$conditions = $this->conditionsDates( $conditions, $criteresci, 'Contratinsertion.datevalidation_ci' );
			}
			// Sinon, la date de validation est unique (et normalement présente uniquement sur la cohorte de CER validés)
			else {
				$conditions = $this->conditionsDate( $conditions, $criteresci, 'Contratinsertion.datevalidation_ci' );
			}

			// Trouver le dernier contrat d'insertion pour chacune des personnes du jeu de résultats
			if( isset( $criteresci['Contratinsertion']['dernier'] ) && $criteresci['Contratinsertion']['dernier'] ) {
				$conditions[] = 'Contratinsertion.id IN (
					SELECT
						contratsinsertion.id
					FROM
						contratsinsertion
						WHERE
							contratsinsertion.personne_id = Contratinsertion.personne_id
						ORDER BY
							contratsinsertion.rg_ci DESC,
							contratsinsertion.id DESC
						LIMIT 1
				)';
			}

			// On a un filtre par défaut sur l'état du dossier si celui-ci n'est pas renseigné dans le formulaire.
			$Situationdossierrsa = ClassRegistry::init( 'Situationdossierrsa' );
			$etatdossier = Set::extract( $criteresci, 'Situationdossierrsa.etatdosrsa' );
			if( ( !isset( $criteresci['Situationdossierrsa']['etatdosrsa'] ) || empty( $criteresci['Situationdossierrsa']['etatdosrsa'] ) ) && !empty( $etatsdosrsa ) ) {
				$criteresci['Situationdossierrsa']['etatdosrsa']  = $etatsdosrsa;
			}

			$conditions = $this->conditionsAdresse( $conditions, $criteresci );
			$conditions = $this->conditionsPersonneFoyerDossier( $conditions, $criteresci );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $criteresci );

			// Statut du contrat
			if( Configure::read( 'Cg.departement' ) == 93 ) {
				$positioncer = Hash::get( $criteresci, 'Cer93.positioncer' );
				if( !empty( $positioncer ) ) {
					$conditions['Cer93.positioncer'] = $positioncer;
				}
			}
			else {
				$decision_ci = Set::extract( $criteresci, 'Contratinsertion.decision_ci' );
				if( !empty( $decision_ci ) ) {
					$conditions[] = 'Contratinsertion.decision_ci = \''.Sanitize::clean( $decision_ci, array( 'encode' => false ) ).'\'';
				}

				// ...
				if( !empty( $positioncer ) ) {
					$conditions[] = 'Contratinsertion.positioncer = \''.Sanitize::clean( $positioncer, array( 'encode' => false ) ).'\'';
				}
			}

			// Personne chargée du suiv
			if( !empty( $personne_suivi ) ) {
				$conditions[] = 'Contratinsertion.pers_charg_suivi = \''.Sanitize::clean( $personne_suivi, array( 'encode' => false ) ).'\'';
			}

			// Forme du contrat
			if( !empty( $forme_ci ) ) {
				$conditions[] = 'Contratinsertion.forme_ci = \''.Sanitize::clean( $forme_ci, array( 'encode' => false ) ).'\'';
			}

			/// Structure référente
			if( !empty( $structurereferente_id ) ) {
				$conditions[] = 'Contratinsertion.structurereferente_id = \''.Sanitize::clean( $structurereferente_id, array( 'encode' => false ) ).'\'';
			}

			/// Référent
			if( !empty( $referent_id ) ) {
				$conditions[] = 'Referent.id = \''.Sanitize::clean( suffix( $referent_id ), array( 'encode' => false ) ).'\'';
			}

			// Contratinsertionr par durée du CER
			$duree_engag = preg_replace( '/^[^0-9]*([0-9]+)[^0-9]*$/', '\1', Hash::get( $criteresci, 'Contratinsertion.duree_engag' ) );
			if( !empty( $duree_engag ) ) {
				if( Configure::read( 'Cg.departement' ) != 93 ) {
					$conditions['Contratinsertion.duree_engag'] = $duree_engag;
				}
				else {
					$durees_engags = ClassRegistry::init( 'Option' )->duree_engag();
					$conditions[] = array(
						'OR' => array(
							'Contratinsertion.duree_engag' => $duree_engag,
							'Cer93.duree' => str_replace( ' mois', '', $durees_engags[$duree_engag] ),
						)
					);
				}
			}

			// Recherche des CER en cours de validité sur une période donnée
			if( Hash::get( $criteresci, 'Contratinsertion.periode_validite' ) ) {
				$encours_validite_from = date_cakephp_to_sql( Hash::get( $criteresci, 'Contratinsertion.periode_validite_from' ) );
				$encours_validite_to = date_cakephp_to_sql( Hash::get( $criteresci, 'Contratinsertion.periode_validite_to' ) );

				if( !empty( $encours_validite_from ) && !empty( $encours_validite_to ) ) {
					$conditions[] = array(
						'Contratinsertion.decision_ci' => 'V',
						'OR' => array(
							// Le nombre de CER dont la date de début est inférieure ou égale au 1er jour du mois M ET dont la date de fin est supérieure ou égale au 1er jour du mois M
							array(
								'Contratinsertion.dd_ci <=' => $encours_validite_from,
								'Contratinsertion.df_ci >=' => $encours_validite_from,
							),
							// Le nombre de CER dont la date de début est supérieure ou égale au 1er jour du mois M ET dont la date de début est inférieure ou égale au dernier jour du mois M
							array(
								'Contratinsertion.dd_ci >=' => $encours_validite_from,
								'Contratinsertion.dd_ci <=' => $encours_validite_to,
							),
						)
					);
				}
			}

			// Liste des CERs arrivant à échéance -> dont la date de fin est pour le mois en cours
			$echeanceproche = Hash::get( $criteresci, 'Contratinsertion.echeanceproche' );
			if( $echeanceproche ) {
				$conditions[] = 'Contratinsertion.id IN (
					SELECT
						contratsinsertion.id
					FROM
						contratsinsertion
						WHERE
							date_trunc( \'day\', contratsinsertion.df_ci ) >= DATE( NOW() )
							AND date_trunc( \'day\', contratsinsertion.df_ci ) <= ( DATE( NOW() ) + INTERVAL \''.Configure::read( 'Criterecer.delaiavanteecheance' ).'\' )
 				)';
			}

			// Liste des CERs échus -> dont la date de fin est au plus tard la date du jour
			$arriveaecheance = Hash::get( $criteresci, 'Contratinsertion.arriveaecheance' );
			if( $arriveaecheance ) {
				$conditions[] = 'Contratinsertion.id IN (
					SELECT
						contratsinsertion.id
					FROM
						contratsinsertion
						WHERE
							date_trunc( \'day\', contratsinsertion.df_ci ) <= DATE( NOW() )
 				)';
			}


			// Pour le CG66 : filtre permettant de retourner les CERs non validés et notifiés il y a 1 mois et demi
			if( isset( $criteresci['Contratinsertion']['notifienouveaux'] ) && !empty( $criteresci['Contratinsertion']['notifienouveaux'] ) ) {
				$conditions[] = 'Contratinsertion.id IN (
					SELECT
						contratsinsertion.id
					FROM
						contratsinsertion
						WHERE
							positioncer = \'nonvalid\'
							AND ( date_trunc( \'day\', contratsinsertion.datenotification ) + INTERVAL \''.Configure::read( 'Criterecer.delaidetectionnonvalidnotifie' ).'\' ) <= DATE( NOW() )
							AND contratsinsertion.id IN (
								SELECT c.id
									FROM contratsinsertion AS c
									WHERE
										c.personne_id = "Personne"."id"
									ORDER BY dd_ci DESC
									LIMIT 1
							)
 				)';
			}

            // Contratinsertion pour le CG66 afin d'exclure les CERs dont la date de tacite reconduction est non vide
            if( Configure::read( 'Cg.departement' ) == 66 ) {
                $istacitereconduction = Set::extract( $criteresci, 'Contratinsertion.istacitereconduction' );
                if( isset( $istacitereconduction ) && !empty( $istacitereconduction ) ) {
                    $conditions[] = 'Contratinsertion.datetacitereconduction IS NULL';
                }
            }


            /// Structure référente du parcours lié au référent du parcours
			/*if( !empty( $structureParcours ) ) {
				$conditions[] = 'Structurereferente.id = \''.Sanitize::clean( $structureParcours, array( 'encode' => false ) ).'\'';
			}*/
            // Référent du parcours en cours de l'allocataire
            /*$sqDernierReferent = $this->Contratinsertion->Personne->PersonneReferent->sqDerniere( 'Personne.id' );
			if( !empty( $referentParcours ) ) {
				$conditions[] = 'PersonneReferent.referent_id = \''.Sanitize::clean( suffix( $referentParcours ), array( 'encode' => false ) ).'\'';
			}*/

			$this->Dossier = ClassRegistry::init( 'Dossier' );

			// Dernière orientation
			$conditions[] = array(
				'OR' => array(
					'Orientstruct.id IS NULL',
					'Orientstruct.id IN ( '.$this->Contratinsertion->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere().' )',
				)
			);

			$typeorient_id = Hash::get( $criteresci, 'Orientstruct.typeorient' );
			if( !empty( $typeorient_id ) ) {
				$conditions[] = array(
					'Orientstruct.statut_orient' => 'Orienté',
					'Orientstruct.typeorient_id' => $typeorient_id
				);
			}
			else if( $typeorient_id != '' && $typeorient_id == 0 ) {
				$conditions[] = 'Orientstruct.id IS NULL';
			}

			$querydata = array(
				'fields' => array_merge(
					$this->Contratinsertion->fields(),
                    $this->Contratinsertion->Personne->fields(),
                    $this->Contratinsertion->Personne->Prestation->fields(),
                    $this->Contratinsertion->Referent->fields(),
					$this->Contratinsertion->Structurereferente->fields(),
                    $this->Contratinsertion->Personne->Foyer->fields(),
                    $this->Contratinsertion->Personne->Foyer->Dossier->fields(),
                    $this->Contratinsertion->Personne->Foyer->Dossier->Situationdossierrsa->fields(),
                    $this->Contratinsertion->Personne->Foyer->Adressefoyer->Adresse->fields(),
                    $this->Contratinsertion->Personne->PersonneReferent->fields(),
					array(
						'Cer93.duree',
						'Cer93.positioncer',
						'Typeorient.lib_type_orient',
						$this->Contratinsertion->Referent->sqVirtualField( 'nom_complet' )
					)
                ),
				'recursive' => -1,
				'joins' => array(
                    $this->Contratinsertion->join( 'Cer93', array( 'type' => ( Configure::read( 'Cg.departement' ) == 93 ? 'INNER' : 'LEFT OUTER' ) ) ),
                    $this->Contratinsertion->join( 'Personne', array( 'type' => 'INNER' ) ),
                    $this->Contratinsertion->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
                    $this->Contratinsertion->join( 'Propodecisioncer66', array( 'type' => 'LEFT OUTER' ) ),
					$this->Contratinsertion->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Contratinsertion->Personne->join( 'Orientstruct',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array( 'Orientstruct.statut_orient' => 'Orienté' )
						)
					),
                    $this->Contratinsertion->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
                    $this->Contratinsertion->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
                    $this->Contratinsertion->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
                    $this->Contratinsertion->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$this->Contratinsertion->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
                    $this->Contratinsertion->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
                    $this->Contratinsertion->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
                    $this->Contratinsertion->Personne->Foyer->Dossier->join( 'Detaildroitrsa', array( 'type' => 'LEFT OUTER' ) ),
                    /*$this->Contratinsertion->Personne->join(
                        'PersonneReferent',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								"PersonneReferent.id IN ( {$sqDernierReferent} )"
                            )
                        )
                    )*/
				),
				'limit' => 10,
				'order' => 'Contratinsertion.df_ci ASC',
				'conditions' => $conditions
			);

			if( Configure::read( 'CG.cantons' )  ) {
				$querydata['fields'][] = 'Canton.canton';
				$querydata['joins'][] = ClassRegistry::init( 'Canton' )->joinAdresse();
			}

			// Référent du parcours
			$querydata = $this->Contratinsertion->Personne->PersonneReferent->completeQdReferentParcours( $querydata, ( isset( $criteresci['Contratinsertion']['PersonneReferent'] ) ? $criteresci['Contratinsertion'] : $criteresci ) );

			if( empty( $statutValidation ) && Configure::read( 'Cg.departement' ) == 93 ) {
				// 1. Filtre par expérience professionnelle significative: on veut les valeurs SSI elles ont été sélectionnées par le filtre
				$expprocer93 = Hash::filter( (array)Hash::get( $criteresci, 'Expprocer93' ) );
				if( !empty( $expprocer93 ) ) {
					$querydata['joins'][] = $this->Contratinsertion->Cer93->join( 'Expprocer93', array( 'type' => 'LEFT OUTER' ) );

					// Partie filtre
					$conditions = array(
						'Expprocer93.cer93_id = Cer93.id'
					);
					foreach( $expprocer93 as $fieldName => $value ) {
						$value = suffix( $value );
						if( !empty( $value ) ) {
							if( in_array( $fieldName, array( 'metierexerce_id', 'secteuracti_id' ) ) ) {
								$conditions["Expprocer93.{$fieldName}"] = $value;
							}
							else {
								$conditions["Entreeromev3.{$fieldName}"] = $value;
							}
						}
					}

					// On veut éviter d'avoir des doublons de lignes de résultats
					$querySq = array(
						'alias' => 'Expprocer93',
						'fields' => array( 'Expprocer93.id' ),
						'contain' => false,
						'conditions' => $conditions,
						'joins' => array(
							$this->Contratinsertion->Cer93->Expprocer93->join( 'Entreeromev3', array( 'type' => 'LEFT OUTER' ) ),
						),
						'limit' => 1
					);
					$sql = $this->Contratinsertion->Cer93->Expprocer93->sq(
						array_words_replace(
							$querySq,
							array( 'Expprocer93' => 'expsproscers93', 'Entreeromev3'  => 'entreesromesv3' )
						)
					);
					$querydata['conditions'][] = "Expprocer93.id IN ( {$sql} )";

					// Ajout des champs et des jointures (aliasées) dans la requête principale
					$suffix = 'exppro';
					$aliases = array(
						// INSEE
						'Metierexerce' => "Metierexerce{$suffix}",
						'Secteuracti' => "Secteuracti{$suffix}",
						// ROME v.3
						'Entreeromev3' => "Entree{$suffix}",
						'Familleromev3' => "Famille{$suffix}",
						'Domaineromev3' => "Domaine{$suffix}",
						'Metierromev3' => "Metier{$suffix}",
						'Appellationromev3' => "Appellation{$suffix}"
					);
					$querydata['joins'][] = array_words_replace(
						$this->Contratinsertion->Cer93->Expprocer93->join( 'Entreeromev3', array( 'type' => 'LEFT OUTER' ) ),
						$aliases
					);
					$querydata = $this->Contratinsertion->Cer93->Expprocer93->Entreeromev3->getCompletedRomev3Joins( $querydata, 'LEFT OUTER', $aliases );

					// Ajout des champs et des jointures INSEE
					$querydata['fields'][] = "Metierexerce{$suffix}.name";
					$querydata['joins'][] = array_words_replace(
						$this->Contratinsertion->Cer93->Expprocer93->join( 'Metierexerce', array( 'type' => 'LEFT OUTER' ) ),
						$aliases
					);

					$querydata['fields'][] = "Secteuracti{$suffix}.name";
					$querydata['joins'][] = array_words_replace(
						$this->Contratinsertion->Cer93->Expprocer93->join( 'Secteuracti', array( 'type' => 'LEFT OUTER' ) ),
						$aliases
					);
				}

				// 2. Filtre par emploi trouvé
				// 2.1 Codes ROME v.3
				$querydata = $this->Contratinsertion->Cer93->getCompletedRomev3Joins( $querydata, 'emptrouv' );
				foreach( $this->Contratinsertion->Cer93->Emptrouvromev3->romev3Fields as $fieldName ) {
					$path = "Emptrouvromev3.{$fieldName}";
					$value = suffix( Hash::get( $criteresci, $path ) );
					if( !empty( $value ) ) {
						$querydata['conditions'][$path] = $value;
					}
				}

				// 2.2 Codes INSEE -> TODO: aliaser Metiertrouve ?
				$querydata['fields'][] = 'Metierexerce.name';
				$querydata['joins'][] = $this->Contratinsertion->Cer93->join( 'Metierexerce', array( 'type' => 'LEFT OUTER' ) );
				$querydata['fields'][] = 'Secteuracti.name';
				$querydata['joins'][] = $this->Contratinsertion->Cer93->join( 'Secteuracti', array( 'type' => 'LEFT OUTER' ) );

				foreach( array( 'metierexerce_id', 'secteuracti_id' ) as $fieldName ) {
					$path = "Cer93.{$fieldName}";
					$value = suffix( Hash::get( $criteresci, $path ) );
					if( !empty( $value ) ) {
						$querydata['conditions'][$path] = $value;
					}
				}

				// 3. Filtre par "Votre contrat porte sur"
				// On veut les valeurs SSI elles ont été sélectionnées par le filtre
				$sujetcer93_id = Hash::get( $criteresci, 'Cer93Sujetcer93.sujetcer93_id' );
				$soussujetcer93_id = suffix( Hash::get( $criteresci, 'Cer93Sujetcer93.soussujetcer93_id' ) );
				$valeurparsoussujetcer93_id = suffix( Hash::get( $criteresci, 'Cer93Sujetcer93.valeurparsoussujetcer93_id' ) );

				if( !empty( $sujetcer93_id ) || !empty( $soussujetcer93_id ) || !empty( $valeurparsoussujetcer93_id ) ) {
					$querydata['joins'][] = $this->Contratinsertion->Cer93->join( 'Cer93Sujetcer93', array( 'type' => 'INNER' ) );
					$querydata['joins'][] = $this->Contratinsertion->Cer93->Cer93Sujetcer93->join( 'Sujetcer93', array( 'type' => 'LEFT OUTER' ) );
					$querydata['joins'][] = $this->Contratinsertion->Cer93->Cer93Sujetcer93->join( 'Soussujetcer93', array( 'type' => 'LEFT OUTER' ) );
					$querydata['joins'][] = $this->Contratinsertion->Cer93->Cer93Sujetcer93->join( 'Valeurparsoussujetcer93', array( 'type' => 'LEFT OUTER' ) );

					$querydata['fields'] = array_merge(
						$querydata['fields'],
						array(
							'Cer93Sujetcer93.commentaireautre',
							'Cer93Sujetcer93.autrevaleur',
							'Cer93Sujetcer93.autresoussujet',
							'Sujetcer93.name',
							'Soussujetcer93.name',
							'Valeurparsoussujetcer93.name',
						)
					);

					$conditions = array( 'cers93_sujetscers93.cer93_id = Cer93.id' );

					foreach( array( 'sujetcer93_id', 'soussujetcer93_id', 'valeurparsoussujetcer93_id' ) as $field ) {
						$value = $$field;
						if( !empty( $value ) ) {
							$conditions["cers93_sujetscers93.{$field}"] = $value;
						}
					}

					// On veut éviter d'avoir des doublons de lignes de résultats
					$sql = $this->Contratinsertion->Cer93->Cer93Sujetcer93->sq(
						array(
							'alias' => 'cers93_sujetscers93',
							'fields' => array( 'cers93_sujetscers93.id' ),
							'contain' => false,
							'conditions' => $conditions,
							'limit' => 1
						)
					);
					$querydata['conditions'][] = "Cer93Sujetcer93.id IN ( {$sql} )";
				}

				// Votre contrat porte sur l'emploi
				$querydata = $this->Contratinsertion->Cer93->getCompletedRomev3Joins( $querydata, 'sujet' );
				foreach( $this->Contratinsertion->Cer93->Sujetromev3->romev3Fields as $fieldName ) {
					$path = "Sujetromev3.{$fieldName}";
					$value = suffix( Hash::get( $criteresci, $path ) );
					if( !empty( $value ) ) {
						$querydata['conditions'][$path] = $value;
					}
				}
			}

			// Doit-on exclure un type d'orientation ?
			$value = Hash::get( $criteresci, 'TypeorientAExclure.id' );
			if( !empty( $value ) ) {
				$querydata['conditions'][] = array(
					'OR' => array(
						array(
							'Typeorient.parentid IS NULL',
							'NOT' => array(
								'Typeorient.id' => $value
							)
						),
						array(
							'Typeorient.parentid IS NOT NULL',
							'NOT' => array(
								'Typeorient.parentid' => $value
							)
						),
					)
				);
			}

			// Ajout de l'étape du dossier d'orientation de l'allocataire pour le CG 58
			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$this->Contratinsertion->forceVirtualFields = true;
				$querydata = $this->Contratinsertion->Personne->WebrsaPersonne->completeQueryVfEtapeDossierOrientation58( $querydata, $criteresci );
			}

			return $querydata;
		}
	}
?>