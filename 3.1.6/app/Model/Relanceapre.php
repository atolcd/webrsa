<?php
	/**
	 * Code source de la classe Relanceapre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Relanceapre ...
	 *
	 * @package app.Model
	 */
	class Relanceapre extends AppModel
	{
		public $name = 'Relanceapre';

		public $actsAs = array(
			'Enumerable' => array(
				'fields' => array(
					'etatdossierapre' => array( 'type' => 'etatdossierapre', 'domain' => 'apre' )
				)
			),
			'Gedooo.Gedooo',
			'StorablePdf' => array(
				'afterSave' => 'deleteAll'
			),
			'ModelesodtConditionnables' => array(
				93 => 'APRE/Relanceapre/relanceapre.odt'
			)
		);

		public $validate = array(
			'etatdossierapre' => array(
				'rule' => 'notEmpty',
				'message' => 'Champ obligatoire'
			),
			'apre_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
		);

		public $belongsTo = array(
			'Apre' => array(
				'className' => 'Apre',
				'foreignKey' => 'apre_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Doit-on exécuter les différentes requêtes de l'afterFind ?
		 * true par défaut pour ne pas casser le passif.
		 *
		 * @var boolean
		 */
		public $deepAfterFind = true;

		/**
		*
		*/

		public function afterFind( $results, $primary = false ) {
			$resultset = parent::afterFind( $results, $primary );

			if( $this->deepAfterFind && !empty( $resultset ) ) {
				foreach( $resultset as $i => $results ) {
					if( isset( $results['Relanceapre'] ) ) {
						$isArray = true;
						if( isset( $results['Relanceapre']['id'] ) ) {
							$results['Relanceapre'] = array( $results['Relanceapre'] );
							$isArray = false;
						}

						foreach( $results['Relanceapre'] as $key => $result ) {
							$conditions = array();
							if( isset( $result['apre_id'] ) &&  !empty( $result['apre_id'] ) ) {
								$conditions = array( 'AprePieceapre.apre_id' => $result['apre_id'] );
							}

							$piecesPresentes = $this->Apre->AprePieceapre->find(
								'all',
								array(
									'conditions' => $conditions,
									'recursive' => -1
								)
							);

							$conditions = array();
							$piecesApreIds = Set::extract( $piecesPresentes, '/AprePieceapre/pieceapre_id' );
							if( !empty( $piecesApreIds ) ) {
								$conditions = array( 'NOT' => array( 'Pieceapre.id' => $piecesApreIds ) );
							}
							$piecesAbsentes = $this->Apre->Pieceapre->find( 'all', array( 'conditions' => $conditions, 'recursive' => -1 ) );

							$results['Relanceapre'][$key]['Piecemanquante'] = Set::classicExtract( $piecesAbsentes, '{n}.Pieceapre' );
						}

						if( !$isArray ) {
							$results['Relanceapre'] = $results['Relanceapre'][0];
						}

						$resultset[$i] = $results;
					}
				}
			}

			return $resultset;
		}

		/**
		 * Retourne le chemin vers le modèle odt utilisé pour la relance de l'APRE
		 *
		 * @param array $data
		 * @return string
		 */
		public function modeleOdt( $data ) {
			return 'APRE/Relanceapre/relanceapre.odt';
		}

		/**
		 * Retourne les données nécessaires à l'impression d'une relance d'APRE pour le CG 93
		 *
		 * @param integer $id
		 * @param integer $user_id
		 * @return array
		 */
		public function getDataForPdf( $id, $user_id ) {
			$querydata = array(
				'fields' => array_merge(
					$this->fields(),
					$this->Apre->fields(),
					$this->Apre->Personne->fields(),
					$this->Apre->Personne->Foyer->fields(),
					$this->Apre->Personne->Prestation->fields(),
					$this->Apre->Personne->Foyer->Dossier->fields(),
					$this->Apre->Personne->Foyer->Adressefoyer->Adresse->fields(),
					array(
						'( '.$this->Apre->Personne->Foyer->vfNbEnfants().' ) AS "Foyer__nbenfants"'
					)
				),
				'joins' => array(
					$this->Apre->join( 'Relanceapre', array( 'type' => 'INNER' ) ),
					$this->Apre->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Apre->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Apre->Personne->join( 'Prestation', array( 'type' => 'LEFT OUTER' ) ),
					$this->Apre->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Apre->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$this->Apre->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
				),
				'contain' => false,
				'conditions' => array(
					'Relanceapre.id' => $id,
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.id IN ('
								.$this->Apre->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' )
							.')',
						)
					),
				),
			);

			$relanceapre = $this->Apre->find( 'first', $querydata );

			// Formattage des pièces manquantes
			$piecesManquantesAides = Set::classicExtract( $relanceapre, "Apre.Piece.Manquante" );
			$textePiecesManquantes = '';
			$relanceapre['Relanceapre']['Piecemanquante'] = '';;
			foreach( $piecesManquantesAides as $model => $pieces ) {
				if( !empty( $pieces ) ) {
					$relanceapre['Relanceapre']['Piecemanquante'] .= __d( 'apre', $model )."\n" .'  - '.implode( "\n  - ", $pieces )."\n";
				}
			}

			unset( $relanceapre['Apre']['Piecepresente'] );
			unset( $relanceapre['Apre']['Piece'] );
			unset( $relanceapre['Apre']['Piecemanquante'] );
			unset( $relanceapre['Apre']['Natureaide'] );

			// Récupération de l'utilisateur connecté
			$user = $this->Apre->Personne->Contratinsertion->User->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => $user_id
					),
					'contain' => false
				)
			);
			$relanceapre = Set::merge( $relanceapre, $user );

			return $relanceapre;
		}

		/**
		 * Retourne le PDF par défaut généré par les appels aux méthodes getDataForPdf, modeleOdt et
		 * à la méthode ged du behavior Gedooo. Le PDF est stocké après la première génération.
		 *
		 * @param type $id Id de la relance d'APRE
		 * @param type $user_id Id de l'utilisateur connecté
		 * @return string
		 */
		public function getDefaultPdf( $id, $user_id ) {
			$pdf = $this->getStoredPdf( $id );

			if( !empty( $pdf ) ) {
				$pdf = $pdf['Pdf']['document'];
			}
			else {
				$Option = ClassRegistry::init( 'Option' );

				$options = Hash::merge(
					$this->Apre->Personne->Foyer->enums(),
					array(
						'Personne' => array(
							'qual' => $Option->qual(),
						),
						'Prestation' => array(
							'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers'),
						),
						'Foyer' => array(
							'sitfam' => $Option->sitfam(),
							'typeocclog' => ClassRegistry::init('Foyer')->enum('typeocclog'),
						),
						'Type' => array(
							'voie' =>  $Option->typevoie(),
						),
						'type' => array(
							'voie' => $Option->typevoie()
						),
					)
				);

				$relanceapre = $this->getDataForPdf( $id, $user_id );
				$modeledoc = $this->modeleOdt( $relanceapre );

				$pdf = $this->ged( $relanceapre, $modeledoc, false, $options );

				if( !empty( $pdf ) ) {
					$this->storePdf( $id, $modeledoc, $pdf );
				}
			}

			return $pdf;
		}

		/**
		 * Retourne l'id de la personne à laquelle est lié un enregistrement.
		 *
		 * @param integer $id L'id de l'enregistrement
		 * @return integer
		 */
		public function personneId( $id ) {
			$querydata = array(
				'fields' => array( "Apre.personne_id" ),
				'joins' => array(
					$this->join( 'Apre', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					"{$this->alias}.id" => $id
				),
				'recursive' => -1
			);

			$result = $this->find( 'first', $querydata );

			if( !empty( $result ) ) {
				return $result['Apre']['personne_id'];
			}
			else {
				return null;
			}
		}

		/**
		 * Retourne une sous-requète permettant d'obtenir l'identifiant de la
		 * dernière relance pour une apre donnée.
		 *
		 * @param string $apreId
		 * @return string
		 */
		public function sqDerniere( $apreId = 'Apre.id' ) {
			return $this->sq(
				array(
					'alias' => 'relancesapres',
					'fields' => array(
						'relancesapres.id'
					),
					'conditions' => array(
						"relancesapres.apre_id = {$apreId}"
					),
					'order' => array(
						'relancesapres.daterelance DESC',
						'relancesapres.id DESC'
					),
					'limit' => 1
				)
			);
		}
	}
?>