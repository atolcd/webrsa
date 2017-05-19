<?php
	/**
	 * Code source de la classe Cohortevalidationapre66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Cohortevalidationapre66 ...
	 *
	 * @package app.Model
	 */
	class Cohortevalidationapre66 extends AppModel
	{
		public $name = 'Cohortevalidationapre66';

		public $useTable = false;

		/**
		*
		*/

		public function search( $statutValidation, $mesCodesInsee, $filtre_zone_geo, $criteresapres, $lockedDossiers = null ) {
			$Apre66 = ClassRegistry::init( 'Apre66' );

			/// Conditions de base
			$conditions = array();

			if( !empty( $statutValidation ) ) {
				if( $statutValidation == 'Validationapre::apresavalider' ) {
					$conditions[] = '( ( Apre66.etatdossierapre = \'COM\' ) AND ( Apre66.isdecision = \'N\' ) )';
				}
				else if( $statutValidation == 'Validationapre::validees' ) {
					$conditions[] = '( Apre66.etatdossierapre = \'VAL\' ) AND  ( Apre66.datenotifapre IS NULL ) AND ( Typeaideapre66.isincohorte = \'O\' )';
				}
				else if( $statutValidation == 'Validationapre::notifiees' ) {
					$conditions[] = '( Apre66.etatdossierapre = \'VAL\' ) AND  ( Apre66.datenotifapre IS NOT NULL )  AND ( Typeaideapre66.isincohorte = \'O\' )';
				}
				else if( $statutValidation == 'Validationapre::transfert' ) {
					$conditions[] = '( Apre66.etatdossierapre = \'VAL\' ) AND  ( Apre66.datenotifapre IS NOT NULL ) AND ( Apre66.istraite = \'0\' ) AND ( Apre66.istraite = \'0\' ) AND ( Apre66.istransfere = \'0\' )  AND ( Typeaideapre66.isincohorte = \'O\' )';
				}
				else if( $statutValidation == 'Validationapre::traitementcellule' ) {
					$conditions[] = '( Apre66.etatdossierapre = \'TRA\' ) AND  ( Apre66.datenotifapre IS NOT NULL )  AND ( Apre66.istraite = \'0\' ) AND ( Apre66.istransfere = \'1\' )  AND ( Typeaideapre66.isincohorte = \'O\' )';
				}
			}

			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );

			/// Dossiers lockés
			if( !empty( $lockedDossiers ) ) {
				if( is_array( $lockedDossiers ) ) {
					$conditions[] = 'Dossier.id NOT IN ( '.implode( ', ', $lockedDossiers ).' )';
				}
				$conditions[] = "NOT {$lockedDossiers}";
			}

			/// Critères
			$numeroapre = Set::extract( $criteresapres, 'Search.Apre66.numeroapre' );
			$referent = Set::extract( $criteresapres, 'Search.Apre66.referent_id' );
			$nomcom = Set::extract( $criteresapres, 'Search.Adresse.nomcom' );
			$numcom = Set::extract( $criteresapres, 'Search.Adresse.numcom' );
			$numdemrsa = Set::extract( $criteresapres, 'Search.Dossier.numdemrsa' );
			$matricule = Set::extract( $criteresapres, 'Search.Dossier.matricule' );
			$themeapre66_id = Set::extract( $criteresapres, 'Search.Aideapre66.themeapre66_id' );
			$typeaideapre66_id = Set::extract( $criteresapres, 'Search.Aideapre66.typeaideapre66_id' );

			// Critères sur une personne du foyer - nom, prénom, nom de naissance -> FIXME: seulement demandeur pour l'instant
			foreach( array( 'nom', 'prenom', 'nomnai', 'nir' ) as $criterePersonne ) {
				if( isset( $criteresapres['Search']['Personne'][$criterePersonne] ) && !empty( $criteresapres['Search']['Personne'][$criterePersonne] ) ) {
					$conditions[] = 'Personne.'.$criterePersonne.' ILIKE \''.$this->wildcard( $criteresapres['Search']['Personne'][$criterePersonne] ).'\'';
				}
			}


			/// Critères sur l'adresse - canton
			if( Configure::read( 'CG.cantons' ) ) {
				if( isset( $criteresapres['Search']['Canton']['canton'] ) && !empty( $criteresapres['Search']['Canton']['canton'] ) ) {
					$this->Canton = ClassRegistry::init( 'Canton' );
					$conditions[] = $this->Canton->queryConditions( $criteresapres['Search']['Canton']['canton'] );
				}
			}

			// Localité adresse
			if( !empty( $nomcom ) ) {
				$conditions[] = 'Adresse.nomcom ILIKE \'%'.Sanitize::clean( $nomcom, array( 'encode' => false ) ).'%\'';
			}

			// Commune au sens INSEE
			if( !empty( $numcom ) ) {
				$conditions[] = 'Adresse.numcom ILIKE \'%'.Sanitize::clean( $numcom, array( 'encode' => false ) ).'%\'';
			}

			// Référent lié à l'APRE
			if( !empty( $referent ) ) {
				$conditions[] = 'Apre66.referent_id = \''.Sanitize::clean( $referent, array( 'encode' => false ) ).'\'';
			}

			//Critères sur le dossier de l'allocataire - numdemrsa + matricule
			foreach( array( 'numdemrsa', 'matricule' ) as $critereDossier ) {
				if( isset( $criteresapres['Search']['Dossier'][$critereDossier] ) && !empty( $criteresapres['Search']['Dossier'][$critereDossier] ) ) {
					$conditions[] = 'Dossier.'.$critereDossier.' ILIKE \''.$this->wildcard( $criteresapres['Search']['Dossier'][$critereDossier] ).'\'';
				}
			}


			//Thème de l'aide
			if( !empty( $themeapre66_id ) ) {
				$conditions[] = 'Aideapre66.themeapre66_id = \''.Sanitize::clean( $themeapre66_id, array( 'encode' => false ) ).'\'';
			}

			//Type d'aide
			if( !empty( $typeaideapre66_id ) ) {
				$conditions[] = 'Aideapre66.typeaideapre66_id = \''.Sanitize::clean( suffix( $typeaideapre66_id ), array( 'encode' => false ) ).'\'';
			}

			// Conditions pour les jointures
			$conditions['Prestation.rolepers'] = array( 'DEM', 'CJT' );

			$query = array(
				'fields' => array(
					'Apre66.id',
					'Apre66.personne_id',
					'Apre66.numeroapre',
					'Apre66.typedemandeapre',
					'Apre66.datedemandeapre',
					'Apre66.naturelogement',
					'Apre66.anciennetepoleemploi',
					'Apre66.activitebeneficiaire',
					'Apre66.etatdossierapre',
					'Apre66.dateentreeemploi',
					'Apre66.eligibiliteapre',
					'Apre66.typecontrat',
					'Apre66.statutapre',
					'Apre66.mtforfait',
					'Apre66.isdecision',
					'Apre66.istraite',
					'Apre66.nbenf12',
					'Apre66.referent_id',
					'Apre66.istransfere',
					'Aideapre66.id',
					'Aideapre66.apre_id',
					'Aideapre66.decisionapre',
					'Aideapre66.montantaccorde',
					'Aideapre66.typeaideapre66_id',
					'Aideapre66.montantpropose',
					'Aideapre66.datedemande',
					'Aideapre66.datemontantaccorde',
					'Aideapre66.datemontantpropose',
					'Aideapre66.motifrejetequipe',
					'Dossier.id',
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Dossier.matricule',
					'Personne.id',
					'Personne.dtnai',
					'Personne.nir',
					'Personne.nomcomnai',
					'Adresse.nomcom',
					'Adresse.codepos',
					'Adressefoyer.rgadr',
					'Adresse.numcom',
					'Typeaideapre66.name',
					'Themeapre66.name',
					$Apre66->Personne->Referent->sqVirtualField( 'nom_complet' ),
					$Apre66->Personne->sqVirtualField( 'nom_complet' ),
					$Apre66->Fichiermodule->sqNbFichiersLies( $Apre66, 'nbfichiers', 'Apre66' ),
				),
				'joins' => array(
					$Apre66->join( 'Aideapre66', array( 'type' => 'INNER' ) ),
					$Apre66->join( 'Personne', array( 'type' => 'INNER' ) ),
					$Apre66->join( 'Referent', array( 'type' => 'INNER' ) ),
					$Apre66->Aideapre66->join( 'Themeapre66', array( 'type' => 'INNER' ) ),
					$Apre66->Aideapre66->join( 'Typeaideapre66', array( 'type' => 'INNER' ) ),
					$Apre66->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Apre66->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$Apre66->Personne->Foyer->join(
						'Adressefoyer',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Adressefoyer.id IN ( ' .ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01('Adressefoyer.foyer_id') .' )'
							)
						)
					),
					$Apre66->Personne->Foyer->join( 'Dossier', array( 'type' => 'LEFT OUTER' ) ),
					$Apre66->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
				),
				'contain' => false,
				'conditions' => $conditions,
				'order' => array( 'Personne.nom ASC, Personne.prenom ASC' ),
			);

			$query = $Apre66->Personne->PersonneReferent->completeQdReferentParcours( $query, $criteresapres['Search'] );

			return $query;
		}
	}
?>