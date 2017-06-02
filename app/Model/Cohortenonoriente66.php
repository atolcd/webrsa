<?php
	/**
	 * Fichier source de la classe Cohortenonoriente66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Cohortenonoriente66 fournit un traitement des filtres de recherche des cohortes d'orientation
	 * au CG 66 et des impression liées.
	 *
	 * @package app.Model
	 */
	class Cohortenonoriente66 extends AppModel
	{
		public $name = 'Cohortenonoriente66';

		public $useTable = false;

		public $modelesOdt = array( 'Orientation/questionnaireorientation66.odt' );

		public $actsAs = array(
			'Conditionnable',
			'Gedooo.Gedooo'
		);

		/**
		*
			Concernant la recherche des allocataires du Pôle Emploi, voici les critères pris en compte si aucune valeur n'est renseignée dans le formulaire :
			La case "Uniquement la dernière demande RSA pour un même allocataire" est laissée cochée, donc une partie des conditions sera sur :
				l'allocataire possède une entrée dans la table prestation de type RSA
				l'allocataire possède une entrée dans la table foyer
				l'allocataire possède une entrée dans la table dossier
				le rôle de l'allocataire est de type demandeur ou conjoint
				le NIR de l'allocataire est sur 13 caractères et bien formaté
				la date de naissance de l'allocataire est renseignée
			De plus, les conditions de base sont les suivantes :
				L'allocataire ne possède aucune entrée dans la table orientsstructs avec pour valeur de statut d'orientation = Orienté
				On se base sur la dernière information PE ou bien on ne prend pas en compte cette information
				L'allocataire doit avoir comme prestation Demandeur ou Conjoint du RSA
				L'allocataire doit être soumis  droit et devoir
				L'état du dossier de l'allocataire doit se trouver dans un état ouvert ( Z, 2, 3, 4 )
				Son adresse de rang 01 doit être renseignée
				Les zones géographiques de l'utilisateur doivent couvrir celle de l'allocataire (afin que l'utilisateur puisse visualiser l'allocataire)

			Plus spécifiquement, les conditions suivantes sont ajoutées pour les 2 cas en question :

			Gestion de listes -> Non orientation -> Inscrits PE
				La dernière information PE doit être en état = "inscription"

			Gestion de listes -> Non orientation -> Non inscrits PE
				L'allocataire ne doit pas posséder d'entrée dans la table "nonorientes66"
				NB: cette table permet de stocker et de distinguer les allocataires orientés via la gestion des listes des allocataires orientés via le module Orientation normal. Une fois l'action réalisée via les formulaires des liens Inscrits PE et Non inscrits PE, une entrée est stockée dans cette table.
				La dernière information PE doit être différente d'inscription ( = cessation ou radiation).
		*/

		/**
		 * Retourne un querydata ...
		 *
		 * @param string $statutNonoriente
		 * @param array $mesCodesInsee La liste des codes INSEE à laquelle est lié l'utilisateur
		 * @param boolean $filtre_zone_geo L'utilisateur est-il limité au niveau des zones géographiques ?
		 * @param array $criteresci Critères du formulaire de recherche
		 * @param mixed $criteresnonorientes
		 * @return array
		 */
		public function search( $statutNonoriente, $mesCodesInsee, $filtre_zone_geo, $criteresnonorientes, $lockedDossiers = array() ) {
			$Personne = ClassRegistry::init( 'Personne' );
			$Informationpe = ClassRegistry::init( 'Informationpe' );

			/// Conditions de base
			$conditions = array();
			if( !in_array( $statutNonoriente, array( 'Nonoriente::oriente', 'Nonoriente::notifaenvoyer' ) ) ) {
				$conditions[] = '( SELECT COUNT(orientsstructs.id) FROM orientsstructs WHERE orientsstructs.personne_id = "Personne"."id" AND orientsstructs.statut_orient = \'Orienté\' ) = 0';
			}
			$conditions[] =  array(
				'OR' => array(
					'Historiqueetatpe.id IS NULL',
					'Historiqueetatpe.id IN ( '.$Informationpe->Historiqueetatpe->sqDernier( 'Informationpe' ).' )'
				)
			);

			if( !empty( $statutNonoriente ) ) {
				if( $statutNonoriente == 'Nonoriente::isemploi' ) {
					// FIXME: Historiqueetatpe::sqDerniere + historiqueetatspe.etat = \'inscription\'
					$conditions['Historiqueetatpe.etat'] = 'inscription';
					$conditions[] = 'Personne.id NOT IN (
						SELECT nonorientes66.personne_id
							FROM nonorientes66
								WHERE
									nonorientes66.personne_id = Personne.id
						)';
				}
				else if( $statutNonoriente == 'Nonoriente::notisemploiaimprimer' ) {
					$conditions[] = 'Personne.id NOT IN (
						SELECT nonorientes66.personne_id
							FROM nonorientes66
								WHERE
									nonorientes66.personne_id = Personne.id
						)';

// 					$conditions['NOT'] = array( 'Historiqueetatpe.etat' => 'inscription' ); // 1117
					$conditions[] = array(
						'OR' => array(
							'Historiqueetatpe.id IS NULL',
							'NOT' => array( 'Historiqueetatpe.etat' => 'inscription' )
						)
					); // 7642
				}
				else if( $statutNonoriente == 'Nonoriente::notisemploi' ) {
					$conditions[] = 'Personne.id IN (
						SELECT nonorientes66.personne_id
							FROM nonorientes66
								WHERE
									nonorientes66.personne_id = Personne.id
						)';

					$conditions[] = array( 'Nonoriente66.origine' => 'notisemploi' );
				}
				else if( $statutNonoriente == 'Nonoriente::notifaenvoyer' ) {
					$conditions[] = 'Personne.id IN (
						SELECT nonorientes66.personne_id
							FROM nonorientes66
								WHERE
									nonorientes66.personne_id = Personne.id
									AND nonorientes66.orientstruct_id IS NOT NULL
									AND nonorientes66.datenotification IS NULL
						)';
				}
				else if( $statutNonoriente == 'Nonoriente::oriente' ) {
					$conditions[] = 'Personne.id IN (
						SELECT nonorientes66.personne_id
							FROM nonorientes66
								WHERE
									nonorientes66.personne_id = Personne.id
									AND nonorientes66.orientstruct_id IS NOT NULL
									AND nonorientes66.datenotification IS NOT NULL
						)';
				}
			}

			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsAdresse( $conditions, $criteresnonorientes['Search'], $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsDossier( $conditions, $criteresnonorientes['Search'] );
			$conditions = $this->conditionsPersonne( $conditions, $criteresnonorientes['Search'] );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $criteresnonorientes['Search'] );
			$conditions = $this->conditionsSituationdossierrsa( $conditions, $criteresnonorientes['Search'] );

			/// Dossiers lockés
			if( !empty( $lockedDossiers ) ) {
				if( is_array( $lockedDossiers ) ) {
					$conditions[] = 'Dossier.id NOT IN ( '.implode( ', ', $lockedDossiers ).' )';
				}
				$conditions[] = "NOT {$lockedDossiers}";
			}

			// Code historique etat PE (radiation, cessation, inscription)
			$etatHistoriqueetatpe = Set::extract( $criteresnonorientes['Search'], 'Historiqueetatpe.etat' );
			if( !empty( $etatHistoriqueetatpe ) ) {
				$conditions[] = 'Historiqueetatpe.etat = \''.Sanitize::clean( $etatHistoriqueetatpe, array( 'encode' => false ) ).'\'';
			}

			// Conditions pour les jointures
			$conditions['Prestation.rolepers'] = array( 'DEM', 'CJT' );
			$conditions['Calculdroitrsa.toppersdrodevorsa'] = '1';
			$conditions['Situationdossierrsa.etatdosrsa'] = $Personne->Orientstruct->Personne->Foyer->Dossier->Situationdossierrsa->etatOuvert();
			$conditions[] = array(
				'OR' => array(
					'Adressefoyer.id IS NULL',
					'Adressefoyer.id IN ( '
						.$Personne->Foyer->Adressefoyer->sqDerniereRgadr01('Adressefoyer.foyer_id')
					.' )'
				)
			);
			$conditions[] = array(
				'OR' => array(
					'Informationpe.id IS NULL',
					'Informationpe.id IN ( '
						.$Informationpe->sqDerniere('Personne')
					.' )'
				)
			);

			// Conditions sur le nombre d'enfants du foyer
			if( isset( $criteresnonorientes['Search']['Foyer']['nbenfants'] ) && !empty( $criteresnonorientes['Search']['Foyer']['nbenfants'] ) ) {
				if( $criteresnonorientes['Search']['Foyer']['nbenfants'] == 'O' ) {
					$conditions['( '.$Personne->Foyer->vfNbEnfants().' ) >'] = 0;
				}
				else if( $criteresnonorientes['Search']['Foyer']['nbenfants'] == 'N' ) {
					$conditions['( '.$Personne->Foyer->vfNbEnfants().' )'] = 0;
				}
			}


			// conditions sur la date d'impression du courrier aux allocataires non inscrits PE
			foreach( array( 'dateimpression', 'datenotification' ) as $critereNonoriente ) {
				if( isset( $criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente] )  ) {
					if( is_array( $criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente] ) && !empty( $criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente]['day'] ) && !empty( $criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente]['month'] ) && !empty( $criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente]['year'] ) ) {
						$conditions["Nonoriente66.{$critereNonoriente}"] = "{$criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente]['year']}-{$criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente]['month']}-{$criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente]['day']}";
					}
					else if( ( is_int( $criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente] ) || is_bool( $criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente] ) || ( $criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente] == '1' ) ) && isset( $criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente."_from"] ) && isset( $criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente."_to"] ) ) {
						$criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente."_from"] = $criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente."_from"]['year'].'-'.$criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente."_from"]['month'].'-'.$criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente."_from"]['day'];
						$criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente."_to"] = $criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente."_to"]['year'].'-'.$criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente."_to"]['month'].'-'.$criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente."_to"]['day'];

						$conditions[] = 'Nonoriente66.'.$critereNonoriente.' BETWEEN \''.$criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente."_from"].'\' AND \''.$criteresnonorientes['Search']['Nonoriente66'][$critereNonoriente."_to"].'\'';
					}
				}
			}

			// Conditions sur l'utilisateur ayant réalisé l'orientation
			if( isset( $criteresnonorientes['Search']['Nonoriente66']['user_id'] ) && !empty( $criteresnonorientes['Search']['Nonoriente66']['user_id'] ) ) {
				$conditions[] = 'Nonoriente66.user_id = \''.Sanitize::clean( $criteresnonorientes['Search']['Nonoriente66']['user_id'], array( 'encode' => false ) ).'\'';
			}

			$query = array(
				'fields' => array_merge(
					$Personne->fields(),
					$Personne->Foyer->fields(),
					$Personne->Foyer->Dossier->fields(),
					$Personne->Foyer->Adressefoyer->Adresse->fields(),
					$Personne->Foyer->Dossier->Situationdossierrsa->fields(),
					$Personne->Orientstruct->fields(),
					$Personne->Orientstruct->Typeorient->fields(),
					$Personne->Orientstruct->Structurereferente->fields(),
					$Personne->Nonoriente66->fields(),
					array(
						$Personne->Foyer->sqVirtualField( 'enerreur' ),
						'( '.$Personne->Foyer->vfNbEnfants().' ) AS "Foyer__nbenfants"',
						'Historiqueetatpe.id',
						'Historiqueetatpe.etat',
						'Historiqueetatpe.date',
						$Personne->Nonoriente66->Fichiermodule->sqNbFichiersLies( $Personne->Nonoriente66, 'nbfichiers', 'Nonoriente66' ),
						$Personne->Orientstruct->Fichiermodule->sqNbFichiersLies( $Personne->Orientstruct, 'nbfichiers', 'Orientstruct' ),
						'Canton.id',
						'Canton.canton'
					)
				),
				'joins' => array(
					$Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Orientstruct', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->join( 'Nonoriente66', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) ),
					$Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', 'LEFT OUTER' ),
					$Informationpe->join( 'Historiqueetatpe', array( 'type' => 'LEFT OUTER' ) ),
					ClassRegistry::init( 'Canton' )->joinAdresse()
				),
				'contain' => false,
				'conditions' => $conditions,
				'order' => array( 'Personne.id ASC' )
			);

			$query = $Personne->PersonneReferent->completeQdReferentParcours( $query, $criteresnonorientes['Search'] );

			return $query;
		}

		/**
		 * Retourne les données nécessaires à l'impression du questionnaire pour les non orientés du CG66
		 * Les données contiennent les informations de la personne
		 *
		 * @param integer $id
		 * @param integer $user_id
		 * @return array
		 */
		public function getDataForPdf() {
			$typesvoies = ClassRegistry::init( 'Option' )->typevoie();
			$Personne = ClassRegistry::init( 'Personne' );

			$querydata = array(
				'fields' => array_merge(
					$Personne->fields(),
					$Personne->Foyer->Adressefoyer->Adresse->fields(),
					$Personne->Foyer->fields(),
					$Personne->Foyer->Dossier->fields()
				),
				'joins' => array(
					$Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
				),
				'conditions' => array(
					'Adressefoyer.id IN ( '.$Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
				),
				'contain' => false
			);
			return $querydata;
		}

		/**
		 * Retourne le chemin vers le modèle odt (questionnaire)utilisé pour les non orientés du CG66
		 *
		 * @param array $data
		 * @return string
		 */
		public function modeleOdt( $data ) {
			return 'Orientation/questionnaireorientation66.odt'; // INFO courrier 1
		}

		/**
		 * Fonction permettant d'enregistrer la date du jour de l'impression du courrier envoyé
		 * aux allocataires ne possédant pas encore d'orientation
		 * @param array $data
		 * @return array
		 *
		 */
		protected function _saveImpression( $personne_id, $user_id ) {
			$Nonoriente66 = ClassRegistry::init( 'Nonoriente66' );
			$nonoriente66 = array(
				'Nonoriente66' => array(
					'personne_id' => $personne_id,
					'dateimpression' => date( 'Y-m-d' ),
					'orientstruct_id' => null,
					'historiqueetatpe_id' => null,
					'origine' => 'notisemploi',
					'user_id' => $user_id
				)
			);

			$Nonoriente66->create( $nonoriente66 );
			return $Nonoriente66->save();
		}


		/**
		 * Retourne le PDF par défaut généré par les appels aux méthodes getDataForPdf, modeleOdt et
		 * à la méthode ged du behavior Gedooo
		 * Le courrier généré est le questionnaire à destination des allocataires non orientés et non inscrits au PE
		 *
		 * @param type $id Id de la personne
		 * @param type $user_id Id de l'utilisateur connecté
		 * @return string
		 */
		public function getDefaultPdf( $id, $user_id ) {
			$Option = ClassRegistry::init( 'Option' );
			$Personne = ClassRegistry::init( 'Personne' );

			$options = array(
				'Personne' => array(
					'qual' => $Option->qual()
				)
			);

			$querydata = $this->getDataForPdf();

			$querydata = Set::merge(
				$querydata,
				array(
					'conditions' => array(
						'Personne.id' => $id
					)
				)
			);
			$personne = $Personne->find( 'first', $querydata );

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
			$personne['User'] = $user['User'];

			if( empty( $personne ) ) {
				$this->cakeError( 'error404' );
			}
// debug($personne);
// die();

			$this->_saveImpression( $id, $user_id );




			return $this->ged(
				$personne,
				$this->modeleOdt( $personne ),
				false,
				$options
			);
		}

		/**
		 * Retourne le PDF concernant le questionnaire de la personne non orientée
		 *
		 * @param string $search
		 * @param integer $user_id
		 * @return string
		 */
		public function getDefaultCohortePdf( $statutNonoriente, $mesCodesInsee, $filtre_zone_geo, $user_id, $search, $page ) {
// 			$querydata = $this->getDataForPdf();

			$querydata = $this->search( $statutNonoriente, $mesCodesInsee, $filtre_zone_geo, $search, null );

			$querydata['limit'] = 100;
			$querydata['offset'] = ( ( $page ) <= 1 ? 0 : ( $querydata['limit'] * ( $page - 1 ) ) );

			$Personne = ClassRegistry::init( 'Personne' );
			$querydata['fields'] = array( 'Personne.id' );
			$nonorientes66 = $Personne->find( 'all', $querydata );

			// Jointure bizarre sur la table users pour récupérer l'utilisateur connecté
			$User = ClassRegistry::init( 'User' );
			$dbo = $User->getDataSource( $User->useDbConfig );
			$querydata['fields'] = Set::merge( $querydata['fields'], $User->fields() );
			$querydata['joins'][] = array(
				'table' => $dbo->fullTableName( $User, true, false ),
				'alias' => $User->alias,
				'type' => 'LEFT OUTER',
				'conditions' => array(
					'User.id' => $user_id
				)
			);


			$Personne = ClassRegistry::init( 'Personne' );
			$nonorientes66 = $Personne->find( 'all', $querydata );

			$this->begin();
			$success = true;
			foreach( $nonorientes66 as $nonoriente66 ) {
				$success = $this->_saveImpression( $nonoriente66['Personne']['id'], $user_id ) && $success;
			}

			if( !$success ) {
				$this->rollback();
				return array();
			}

			$this->commit();

			$modeleodt = $this->modeleOdt( $nonorientes66 );

			// Traductions
			$Option = ClassRegistry::init( 'Option' );
			$options = array(
				'Personne' => array(
					'qual' => $Option->qual()
				)
			);

			return $this->ged(
				array( 'cohorte' => $nonorientes66 ),
				$modeleodt,
				true,
				$options
			);
		}

		/**
		 * Retourne le PDF concernant lee courrier d'orientation effective
		 *
		 * @param string $search
		 * @return array
		 */
		public function getCohortePdfNonoriente66( $statutNonoriente66, $mesCodesInsee, $filtre_zone_geo, $search, $page, $user_id ) {

			$querydata = $this->search( $statutNonoriente66, $mesCodesInsee, $filtre_zone_geo, $search, null );

			$querydata['limit'] = 100;
			$querydata['offset'] = ( ( $page ) <= 1 ? 0 : ( $querydata['limit'] * ( $page - 1 ) ) );

			$querydata['fields'] = array( 'Nonoriente66.orientstruct_id' );

			$Personne = ClassRegistry::init( 'Personne' );
			$nonorientes66 = $Personne->find( 'all', $querydata );

			$pdfs = array();
			foreach( $nonorientes66 as $nonoriente66 ) {
				$pdfs[] = $Personne->Orientstruct->WebrsaOrientstruct->getPdfNonoriente66( $nonoriente66['Nonoriente66']['orientstruct_id'], $user_id );
			}

			return $pdfs;
		}

		/**
		*
		*/

		public function structuresAutomatiques() {
			$this->Structurereferente = ClassRegistry::init( 'Structurereferente' );

			$results = $this->Structurereferente->find(
				'all',
				array(
					'fields' => array(
						'Structurereferente.typeorient_id',
						'( "Structurereferente"."typeorient_id" || \'_\' || "Structurereferente"."id" ) AS "Structurereferente__id"',
						'Canton.canton'
					),
					'conditions' => array(
						'Structurereferente.typeorient_id' => Configure::read( 'Nonoriente66.notisemploi.typeorientId' )
					),
					'joins' => array(
						$this->Structurereferente->join( 'StructurereferenteZonegeographique' ),
						$this->Structurereferente->StructurereferenteZonegeographique->join( 'Zonegeographique' ),
						$this->Structurereferente->StructurereferenteZonegeographique->Zonegeographique->join( 'Canton' )
					),
					'contain' => false
				)
			);

			return Set::combine( $results, '{n}.Structurereferente.typeorient_id', '{n}.Structurereferente.id', '{n}.Canton.canton' );
		}


	}
?>