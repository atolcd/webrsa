<?php
	/**
	 * Code source de la classe Cohorteindu.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Cohorteindu fournit un traitement du moteur de recherche par indus et permet de valider les
	 * paramètres du moteur de recherche.
	 *
	 * @package app.Model
	 * @deprecated since version 3.0.0
	 * @see WebrsaRechercheIndu
	 */
	class Cohorteindu extends AppModel
	{
		public $name = 'Cohorteindu';

		public $useTable = false;

		public $actsAs = array( 'Conditionnable' );

		public $validate = array(
			'compare' => array(
				array(
					'rule' => array( 'allEmpty', 'mtmoucompta' ),
					'message' => 'Si opérateurs est renseigné, nombre de jours depuis l\'orientation doit l\'être aussi'
				)
			),
			'mtmoucompta' => array(
				array(
					'rule' => array( 'allEmpty', 'compare' ),
					'message' => 'Si le montant est saisi, opérateurs doit l\'être aussi'
				),
				array(
					'rule' => 'numeric',
					'message' => 'Veuillez entrer un chiffre valide',
					'allowEmpty' => true
				)
			)
		);

		/**
		 * Validation des paramètres envoyés au moteur de recherche.
		 *
		 * @param array $options
		 * @return boolean
		 */
		public function beforeValidate( $options = array() ) {
			$_compare = Set::extract( $this->data, 'Cohorteindu.compare' );
			$_mtmoucompta = Set::extract( $this->data, 'Cohorteindu.mtmoucompta' );

			if( empty( $_compare ) != empty( $_mtmoucompta )  ) {
				$this->data['Cohorteindu']['compare'] = $_compare;
				$this->data['Cohorteindu']['mtmoucompta'] = $_mtmoucompta;
			}

			return parent::beforeValidate( $options );
		}

		/**
		 * Retourne un querydata résultant du traitement du formulaire de recherche des indus.
		 *
		 * @param array $mesCodesInsee La liste des codes INSEE à laquelle est lié l'utilisateur
		 * @param boolean $filtre_zone_geo L'utilisateur est-il limité au niveau des zones géographiques ?
		 * @param array $criteresindu Critères du formulaire de recherche
		 * @return array
		 */
		public function search( $mesCodesInsee, $filtre_zone_geo, $criteresindu ) {
			$this->Dossier = ClassRegistry::init( 'Dossier' );

			/// Conditions de base
			$sqLatestAdressefoyer = $this->Dossier->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' );
			$conditions = array(
				'Prestation.rolepers' => 'DEM',
				'Adressefoyer.rgadr' => '01',
				"Adressefoyer.id IN ( {$sqLatestAdressefoyer} )"
			);

			// On a un filtre par défaut sur l'état du dossier si celui-ci n'est pas renseigné dans le formulaire.
			$Situationdossierrsa = ClassRegistry::init( 'Situationdossierrsa' );
			$etatdossier = Set::extract( $criteresindu, 'Situationdossierrsa.etatdosrsa' );
			if( !isset( $criteresindu['Situationdossierrsa']['etatdosrsa'] ) || empty( $criteresindu['Situationdossierrsa']['etatdosrsa'] ) ) {
				$criteresindu['Situationdossierrsa']['etatdosrsa']  = $Situationdossierrsa->etatOuvert();
			}

			/// Filtre zone géographique
			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );

			$conditions = $this->conditionsAdresse( $conditions, $criteresindu, $filtre_zone_geo, $mesCodesInsee );
			$conditions = $this->conditionsPersonneFoyerDossier( $conditions, $criteresindu );
			$conditions = $this->conditionsDernierDossierAllocataire( $conditions, $criteresindu );

			/// Critères
			$natpfcre = Set::extract( $criteresindu, 'Cohorteindu.natpfcre' );
			$typeparte = Set::extract( $criteresindu, 'Cohorteindu.typeparte' );
			$structurereferente_id = Set::extract( $criteresindu, 'Cohorteindu.structurereferente_id' );
			$mtmoucompta = Set::extract( $criteresindu, 'Cohorteindu.mtmoucompta' );
			$compare = Set::extract( $criteresindu, 'Cohorteindu.compare' );


			// Suivi
			if( !empty( $typeparte ) ) {
				$conditions[] = 'Dossier.typeparte = \''.Sanitize::clean( $typeparte, array( 'encode' => false ) ).'\'';
			}

			// Structure référente
			if( !empty( $structurereferente_id ) ) {
				$conditions[] = 'Structurereferente.id = \''.$structurereferente_id.'\'';
			}

			$date_start = date( 'Y-m-d', strtotime( 'previous month', strtotime( date( 'Y-m-01' ) ) ) );
			$date_end = date( 'Y-m-d', strtotime( 'next month', strtotime( date( 'Y-m-d', strtotime( $date_start ) ) ) ) - 1 );

			$querydata = array(
				'fields' => array(
					'Dossier.id',
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Dossier.typeparte',
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
					'Adresse.numcom',
					'Situationdossierrsa.id',
					'Situationdossierrsa.etatdosrsa',
					'Prestation.rolepers'
				),
				'recursive' => -1,
				'joins' => array(
					$this->Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$this->Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
					$this->Dossier->join( 'Detaildroitrsa', array( 'type' => 'LEFT OUTER' ) ),
					$this->Dossier->Detaildroitrsa->join( 'Detailcalculdroitrsa', array( 'type' => 'LEFT OUTER' ) )
				),
				'limit' => 10,
				'conditions' => array(
                    array(
                        'OR' => array(
                            'Adressefoyer.id IS NULL',
                            'Adressefoyer.id IN ( '.$this->Dossier->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
                        )
                    )
                )
			);

			$typesAllocation = array( 'IndusConstates', 'IndusTransferesCG', 'RemisesIndus' );
			$conditionsNotNull = array();
			$conditionsComparator = array();
			$conditionsNat = array();
			$coalesce = array();

			foreach( $typesAllocation as $type ) {
				$meu  = Inflector::singularize( Inflector::tableize( $type ) );
				$querydata['fields'][] = '"'.$type.'"."mtmoucompta" AS mt_'.$meu;

				$join = array(
					'table'      => 'infosfinancieres',
					'alias'      => $type,
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						$type.'.dossier_id = Dossier.id',
						$type.'.type_allocation' => $type
					)
				);

				$querydata['joins'][] = $join;
				$conditionsNotNull[] = $type.'.mtmoucompta IS NOT NULL';

				$coalesce[] = '"'.$type.'"."moismoucompta"';

				// Montant indu + comparatif vis à vis du montant
				if( !empty( $compare ) && !empty( $mtmoucompta ) ) {
					$conditionsComparator[] = $type.'.mtmoucompta '.$compare.' '.Sanitize::clean( $mtmoucompta, array( 'encode' => false ) );
				}

				// Nature de la prestation de créance
				if( !empty( $natpfcre ) ) {
					$conditionsNat[] = $type.'.natpfcre = \''.Sanitize::clean( $natpfcre, array( 'encode' => false ) ).'\'';
				}
			}
			$querydata['fields'][] = 'COALESCE( '.implode( ',', $coalesce ).' ) AS "moismoucompta"';
			$conditions[] = '( '.implode( ' OR ', $conditionsNotNull  ).' )';
			if( !empty( $conditionsComparator ) ) {
				$conditions[] = '( '.implode( ' OR ', $conditionsComparator  ).' )';
			}
			if( !empty( $natpfcre ) ) {
				$conditions[] = '( '.implode( ' OR ', $conditionsNat  ).' )';
			}
			$querydata['conditions'] = Set::merge( $querydata['conditions'], $conditions );

			$tConditions = array();
			foreach( $coalesce as $item1 ) {
				foreach( $coalesce as $item2 ) {
					if( $item1 != $item2 ) {
						$cmp = strcmp( $item1, $item2 );
						if( $cmp < 0 ) {
							$tConditions[] = '( ( '.$item1.' = '.$item2.' ) OR '.$item1.' IS NULL OR '.$item2.' IS NULL )';
						}
						else {
							$tConditions[] = '( ( '.$item2.' = '.$item1.' ) OR '.$item2.' IS NULL OR '.$item1.' IS NULL )';
						}
					}
				}
			}
			$querydata['conditions'] = Set::merge( $querydata['conditions'], '( '.implode( ' OR ', array_unique( $tConditions ) ).' )' );
			$querydata['conditions'] = Set::merge( $querydata['conditions'], array( 'COALESCE( '.implode( ',', $coalesce ).' ) IS NOT NULL' ) );

			$querydata = $this->Dossier->Foyer->Personne->PersonneReferent->completeQdReferentParcours( $querydata, $criteresindu );

			return $querydata;
		}
	}
?>