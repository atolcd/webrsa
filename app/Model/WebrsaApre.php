<?php
	/**
	 * Code source de la classe WebrsaApre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractLogic', 'Model');

	/**
	 * La classe WebrsaApre possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaApre extends WebrsaAbstractLogic
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaApre';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Apre');
		
		public $aidesApre = array( 'Formqualif', 'Formpermfimo', 'Actprof', 'Permisb', 'Amenaglogt', 'Acccreaentr', 'Acqmatprof', 'Locvehicinsert' );

		public $modelsFormation = array( 'Formqualif', 'Formpermfimo', 'Permisb', 'Actprof' );
		
		/**
		*
		*/
		public function sqApreNomaide() {
			$dbo = $this->Apre->getDataSource( $this->Apre->useDbConfig );
			$natureAidesApres = ClassRegistry::init( 'Option' )->natureAidesApres();

			$case = "CASE \n";
			foreach( array_keys( $natureAidesApres ) as $aideModel ) {
				$tableName = $dbo->fullTableName( $this->Apre->{$aideModel}, false, false );
				$case .= "WHEN EXISTS( SELECT * FROM {$tableName} AS \"{$aideModel}\" WHERE \"Apre\".\"id\" = \"{$aideModel}\".\"apre_id\" ) THEN '{$aideModel}'\n";
			}
			$case .= 'ELSE NULL END';

			return $case;
		}

		/**
		*
		*/
		public function sqApreAllocation() {
			return "CASE WHEN \"Apre\".\"statutapre\" = 'F' THEN \"Apre\".\"mtforfait\" ELSE \"ApreEtatliquidatif\".\"montantattribue\" END";
		}

		/**
		*
		*/
		public function joinsAidesLiees( $tiersprestataire = false ) {
			$joins = array();
			foreach( $this->aidesApre as $modelAide ) {
				$joins[] = array(
					'table'      => Inflector::tableize( $modelAide ),
					'alias'      => $modelAide,
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array( "Apre.id = {$modelAide}.apre_id" )
				);
			}
			return $joins;
		}

		/**
		*
		*/
		public function qdFormationsPourPdf() {
			$querydata = array();
			$conditionsTiersprestataireapre = array();

			foreach( $this->modelsFormation as $modelAide ) {
				$querydata['joins'][] = array(
					'table'      => Inflector::tableize( $modelAide ),
					'alias'      => $modelAide,
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array( "Apre.id = {$modelAide}.apre_id" )
				);

				$conditionsTiersprestataireapre[] = "{$modelAide}.tiersprestataireapre_id = Tiersprestataireapre.id";
			}

			$querydata['fields'] = array(
				'Tiersprestataireapre.nomtiers',
				'Tiersprestataireapre.guiban',
				'Tiersprestataireapre.etaban',
				'Tiersprestataireapre.numcomptban',
				'Tiersprestataireapre.clerib'
			);

			$querydata['joins'][] = array(
				'table'      => Inflector::tableize( 'Tiersprestataireapre' ),
				'alias'      => 'Tiersprestataireapre',
				'type'       => 'LEFT OUTER',
				'foreignKey' => false,
				'conditions' => array( 'OR' => $conditionsTiersprestataireapre )
			);

			return $querydata;
		}

		/**
		*   Récupération des pièces liées à une APRE ainsi que les pièces des aides liées à cette APRE
		*/

		public function nbrNormalPieces() {
			$nbNormalPieces = array();
			$nbNormalPieces['Apre'] = $this->Apre->Pieceapre->find( 'count' );
			foreach( $this->aidesApre as $model ) {
				$nbNormalPieces[$model] = $this->Apre->{$model}->{'Piece'.strtolower( $model )}->find( 'count' );
			}
			return $nbNormalPieces;
		}

		/**
		*   Détails des APREs afin de récupérer les pièces liés à cette APRE ainsi que les aides complémentaires avec leurs pièces
		*   @param int $id
		*/
		public function details( $apre_id ) {
			$nbNormalPieces = $this->nbrNormalPieces();
			$details['Piecepresente'] = array();
			$details['Piecemanquante'] = array();

			// Nombre de pièces trouvées par-rapport au nombre de pièces prévues - Apre
			$details['Piecepresente']['Apre'] = $this->Apre->AprePieceapre->find( 'count', array( 'conditions' => array( 'apre_id' => $apre_id ) ) );
			$details['Piecemanquante']['Apre'] = abs( $details['Piecepresente']['Apre'] - $nbNormalPieces['Apre'] );

			// Quelles sont les pièces manquantes
			$piecesPresentes = Set::extract(
                $this->Apre->AprePieceapre->find(
                    'all',
                    array(
                        'fields' => array( 'AprePieceapre.pieceapre_id' ),
                        'conditions' => array( 'apre_id' => $apre_id ),
                        'contain' => false
                    )
                ),
                '/AprePieceapre/pieceapre_id'
            );

			$conditions = array();
			if( !empty( $piecesPresentes ) ) {
				$conditions = array( 'NOT' => array( 'Pieceapre.id' => $piecesPresentes ) );
			}

			$piecesAbsentes = $this->Apre->Pieceapre->find( 'list', array( 'conditions' => $conditions, 'recursive' => -1 ) );
			$details['Piece']['Manquante']['Apre'] = $piecesAbsentes;

			/// Essaie de récupération des pièces des aides liées
			foreach( $this->aidesApre as $model ) {
				// Nombre de pièces trouvées par-rapport au nombre de pièces prévues pour chaque type d'aide
				$aides = $this->Apre->{$model}->find(
					'all',
					array(
						'conditions' => array(
							"$model.apre_id" => $apre_id
						),
                        'contain' => array(
                            'Piece'.strtolower( $model )
                        )
					)
				);

				// Combien d'aides liées à l'APRE sont présentes pour chaque type d'aide
				$details['Natureaide'][$model] = count( $aides );

				if( !empty( $aides ) ) {
					$details['Piecepresente'][$model] = count( Hash::filter( (array)Set::extract( $aides, '/Piece'.strtolower( $model ) ) ) );
					$details['Piecemanquante'][$model] = abs( $nbNormalPieces[$model] - $details['Piecepresente'][$model] );

					if( !empty( $details['Piecemanquante'][$model] ) ) {
						$piecesAidesPresentes = Set::extract(
							$aides,
							'/Piece'.strtolower( $model ).'/'.$model.'Piece'.strtolower( $model ).'/piece'.strtolower( $model ).'_id'
						);

						$piecesAidesAbsentes = array();
						$conditions = array();
						if( !empty( $piecesAidesPresentes ) ) {
							$conditions = array( 'NOT' => array( 'Piece'.strtolower( $model ).'.id' => $piecesAidesPresentes ) );
						}
						$piecesAidesAbsentes = $this->Apre->{$model}->{'Piece'.strtolower( $model )}->find( 'list', array( 'recursive' => -1, 'conditions' => $conditions ) );

						$details['Piece']['Manquante'][$model] = $piecesAidesAbsentes;
					}
				}
			}

			return $details;
		}

		/**
		*
		*/
		public function supprimeFormationsObsoletes( $apre ) {
			foreach( $this->modelsFormation as $formation ) {
				if( !isset( $apre[$formation] ) ) {
					$this->Apre->{$formation}->deleteAll( array( "{$formation}.apre_id" => Set::classicExtract( $apre, 'Apre.id' ) ), true, true );
				}
			}
		}

		/**
		*
		*/
		public function supprimeAidesObsoletes( $apre ) {
			foreach( $this->aidesApre as $formation ) {
				if( !isset( $apre[$formation] ) ) {
					$this->Apre->{$formation}->deleteAll( array( "{$formation}.apre_id" => Set::classicExtract( $apre, 'Apre.id' ) ), true, true );
				}
			}
		}

		/**
		* Mise à jour des montants déjà versés pour chacune des APREs
		* FIXME: pas de valeur de retour car $return est à false ?
		*/
		public function calculMontantsDejaVerses( $apre_ids ) {
			$return = true;

			if( !is_array( $apre_ids ) ) {
				$apre_ids = array( $apre_ids );
			}

			foreach( $apre_ids as $id ) {
				$this->Apre->query( "UPDATE apres SET montantdejaverse = ( SELECT SUM( apres_etatsliquidatifs.montantattribue ) FROM apres_etatsliquidatifs WHERE apres_etatsliquidatifs.apre_id = {$id} GROUP BY apres_etatsliquidatifs.apre_id ) WHERE apres.id = {$id};" )/* && $return*/;
			}

			return $return;
		}

		/**
		* Retourne un querydata permettant de sélectionner des APREs pour les faire passer dans un comité
		* qui n'a pas encore eu lieu.
		*
		* Ces APREs:
		* 	- doivent:
		*		* être complémentaires
		*		* être complètes
		*		* être éligibles
		*		* avoir une date de demande inférieure ou égale à la date du comité
		*	- il doit être possible de les associer à ce comité-ci:
		*		* si ce n'est pas pour un recours
		*			- soit elles sont associées à ce comité-ci
		*			- soit elles ne sont associées à aucun comité
		*			- soit le dernier comité auquel elles ont été associées est plus ancien que celui-ci
		*			  et la décision est un ajournement
		*		* si c'est pour un recours
		*			- soit elles sont associées à ce comité-ci, avec un comite_pcd_id
		*			- soit le dernier comité auquel elles ont été associées est plus ancien que celui-ci,
		*			  la décision est un refus pour laquelle il existe un recours
		*
		* @param integer $comiteapre_id L'id du comité pour lequel on veut sélectionner des APREs
		* @param boolean $isRecours Le fait que les APREs que l'on recherche fassent l'objet d'un recours ou non.
		* @return mixed false si le comité pour lequel on demande la liqste n'existe pas, un querydata CakePHP sinon
		*/
		public function qdPourComiteapre( $comiteapre_id, $isRecours ) {
			$dbo = $this->Apre->getDataSource( $this->Apre->ApreComiteapre->useDbConfig );
			$comiteapre = $this->Apre->ApreComiteapre->Comiteapre->find(
				'first',
				array(
					'conditions' => array(
						'Comiteapre.id' => $comiteapre_id
					),
					'contain' => false
				)
			);

			if( empty( $comiteapre ) ) {
				return false;
			}

			$querydata = array(
				'fields' => array(
					'Apre.id',
					'Apre.numeroapre',
					'Apre.datedemandeapre',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
				),
				'conditions' => array(
					// L'APRE doit être complémentaire
					'Apre.statutapre' => 'C',
					// Le dossier d'APRE doit être complèt
					'Apre.etatdossierapre' => 'COM',
					// L'APRE doit être éligible
					'Apre.eligibiliteapre' => 'O',
					// La date de demande d'APRE doit être inférieure ou égale à la date du comité
					'Apre.datedemandeapre <=' => $comiteapre['Comiteapre']['datecomite'],
				),
				'contain' => array(
					'Personne'
				)
			);

			// FIXME: une demande de recours ajournée n'apparait pas dans les demandes de recours
			// INFO: 'apres_comitesapres.recoursapre' => 'O', pour les ajournements également, à priori ce n'est pas copié lors de la création d'un ajournement.
			/*
				20111009: APREs 93 - on veut savoir si un entrée d'apres_comitesapres qui est
				ajournée provient d'une demande de recours ou pas.
				L'idée est de ne pas casser le fonctionnement précédent en mettant
				une valeur de recoursapre à O si l'entrée ne référençait pas
				l'entrée du recours.
			*/
			/*
				FIXME: On ne peut jamais savoir si une APRE ajournée est un recours ou pas
				(ou juste concernant le passage précédent); elle apparaîtra toujours dans la
				liste des APREs pas en recours lors de la sélection avec un comité d'APRE.
			*/
			/*
				FIXME/TODO approuvé: pour les aj, on vérifiera que c'est un recours ou non en regardant si une autre entrée existe pour cette apre, indiquant un recours (pas d'autre condition)
			*/

			// Il doit être possible de les associer à ce comité-ci
			if( $isRecours ) {
				$querydata['conditions']['OR'] = array(
					// soit elles sont associées à ce comité-ci, avec un comite_pcd_id
					'Apre.id IN ('
						.$this->Apre->ApreComiteapre->sq(
							array(
								'fields' => array( 'apres_comitesapres.apre_id' ),
								'alias' => 'apres_comitesapres',
								'conditions' => array(
									'apres_comitesapres.comiteapre_id' => $comiteapre_id,
									'apres_comitesapres.comite_pcd_id IS NOT NULL'
								)
							)
						)
					.')',
					'Apre.id IN ('
						.$this->Apre->ApreComiteapre->sq(
							array(
								'alias' => 'apres_comitesapres',
								'fields' => array( 'apres_comitesapres.apre_id' ),
								'joins' => array(
									array(
										'table' => $dbo->fullTableName( $this->Apre->Comiteapre, true, false ),
										'alias' => 'comitesapres',
										'type' => 'INNER',
										'conditions' => array(
											'apres_comitesapres.comiteapre_id = comitesapres.id'
										)
									)
								),
								'conditions' => array(
									// la décision lors de l'association avec ce dernier comité a été émise et est un refus, tant que ce n'atait pas un ajournement
									'apres_comitesapres.decisioncomite' => 'REF',
									'apres_comitesapres.recoursapre' => 'O',
									'apres_comitesapres.id IN ('
										.$this->Apre->ApreComiteapre->sqDernierComiteApre(
											'Apre.id',
											array(
												'apres_comitesapres.comiteapre_id <>' => $comiteapre_id,
												'apres_comitesapres.decisioncomite <>' => 'AJ'
											)
										)
									.')',
									// la date et l'heure du dernier comité avec lequel elles sont associées est inférieure à la date et l'heure de ce comité-ci
									'CAST( comitesapres.datecomite || \' \' || comitesapres.heurecomite AS TIMESTAMP ) <=' => "{$comiteapre['Comiteapre']['datecomite']} {$comiteapre['Comiteapre']['heurecomite']}",
									// la date du recours doit être inférieure à la date de ce comité-ci
									'apres_comitesapres.daterecours <=' => $comiteapre['Comiteapre']['datecomite'],
								)
							)
						)
					.')'
				);
			}
			else {
				$querydata['conditions']['OR'] = array(
					// soit elles sont associées à ce comité-ci sans comite_pcd_id
					'Apre.id IN ('
						.$this->Apre->ApreComiteapre->sq(
							array(
								'fields' => array( 'apres_comitesapres.apre_id' ),
								'alias' => 'apres_comitesapres',
								'conditions' => array(
									'apres_comitesapres.comiteapre_id' => $comiteapre_id,
									'apres_comitesapres.comite_pcd_id IS NULL'
								)
							)
						)
					.')',
					// soit elles ne sont associées à aucun comité
					'Apre.id NOT IN ('
						.$this->Apre->ApreComiteapre->sq(
							array(
								'fields' => array( 'apres_comitesapres.apre_id' ),
								'alias' => 'apres_comitesapres',
								'conditions' => array(
									'apres_comitesapres.apre_id = Apre.id'
								)
							)
						)
					.')',
					// soit le dernier comité auquel elles ont été associées est plus ancien que celui-ci et la décision est un ajournement
					'Apre.id IN ('
						.$this->Apre->ApreComiteapre->sq(
							array(
								'alias' => 'apres_comitesapres',
								'fields' => array( 'apres_comitesapres.apre_id' ),
								'joins' => array(
									array(
										'table' => $dbo->fullTableName( $this->Apre->Comiteapre, true, false ),
										'alias' => 'comitesapres',
										'type' => 'INNER',
										'conditions' => array(
											'apres_comitesapres.comiteapre_id = comitesapres.id'
										)
									)
								),
								'conditions' => array(
									// la décision lors de l'association avec ce dernier comité a été émise et est un ajournement
									'apres_comitesapres.decisioncomite' => 'AJ',
									'apres_comitesapres.id IN ('
										.$this->Apre->ApreComiteapre->sqDernierComiteApre( 'Apre.id' )
									.')',
									// la date et l'heure du dernier comité avec lequel elles sont associées est plus récente que la date et l'heure de ce comité-ci
									'CAST( comitesapres.datecomite || \' \' || comitesapres.heurecomite AS TIMESTAMP ) <=' => "{$comiteapre['Comiteapre']['datecomite']} {$comiteapre['Comiteapre']['heurecomite']}",
								)
							)
						)
					.')'
				);

				// Il n'existe pas de comité dans lequel cette APRE est passée, pour laquelle elle a été refusée, et dont le refus a engendré un recours
				$querydata['conditions'][] = 'Apre.id NOT IN ('
						.$this->Apre->ApreComiteapre->sq(
							array(
								'alias' => 'apres_comitesapres',
								'fields' => array( 'apres_comitesapres.apre_id' ),
								'joins' => array(
									array(
										'table' => $dbo->fullTableName( $this->Apre->Comiteapre, true, false ),
										'alias' => 'comitesapres',
										'type' => 'INNER',
										'conditions' => array(
											'apres_comitesapres.comiteapre_id = comitesapres.id'
										)
									)
								),
								'conditions' => array(
									'apres_comitesapres.decisioncomite' => 'REF',
									'apres_comitesapres.recoursapre' => 'O',
									'apres_comitesapres.id IN ('
										.$this->Apre->ApreComiteapre->sqDernierComiteApre(
											'Apre.id',
											array(
													'apres_comitesapres.comiteapre_id <>' => $comiteapre_id,
													'apres_comitesapres.decisioncomite <>' => 'AJ'
											)
										)
									.')',
									// la date et l'heure de ce comité avec lequel elles sont associées est plus récente que la date et l'heure de ce comité-ci
									'CAST( comitesapres.datecomite || \' \' || comitesapres.heurecomite AS TIMESTAMP ) <=' => "{$comiteapre['Comiteapre']['datecomite']} {$comiteapre['Comiteapre']['heurecomite']}",
								)
							)
						)
					.')';
			}

			return $querydata;
		}

		/**
		 * Retourne le chemin vers le modèle odt utilisé pour l'APRE
		 *
		 * @param array $data
		 * @return string
		 */
		public function modeleOdt( $data ) {
			return 'APRE/apre.odt';
		}

		/**
		 * Retourne les données nécessaires à l'impression d'une APRE complémentaire pour le CG 93
		 *
		 * @param integer $id
		 * @param integer $user_id
		 * @return array
		 */
		public function getDataForPdf( $id, $user_id ) {
			$querydata = array(
				'fields' => array_merge(
					$this->Apre->fields(),
					$this->Apre->Personne->fields(),
					$this->Apre->Referent->fields(),
					$this->Apre->Structurereferente->fields(),
					$this->Apre->Personne->Foyer->fields(),
					$this->Apre->Personne->Prestation->fields(),
					$this->Apre->Personne->Foyer->Dossier->fields(),
					$this->Apre->Personne->Foyer->Adressefoyer->Adresse->fields(),
					array(
						'( '.$this->Apre->Personne->Foyer->vfNbEnfants().' ) AS "Foyer__nbenfants"'
					)
				),
				'joins' => array(
					$this->Apre->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Apre->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					$this->Apre->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
					$this->Apre->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Apre->Personne->join( 'Prestation', array( 'type' => 'LEFT OUTER' ) ),
					$this->Apre->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Apre->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$this->Apre->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
				),
				'contain' => false,
				'conditions' => array(
					'Apre.id' => $id,
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

			foreach( $this->aidesApre as $aideApre ) {
				$querydata['fields'] = Set::merge(
					$querydata['fields'],
					$this->Apre->{$aideApre}->fields()
				);

				$querydata['joins'][] = $this->Apre->join( $aideApre, array( 'type' => 'LEFT OUTER' ) );
			}

			$deepAfterFind = $this->Apre->deepAfterFind;
			$this->Apre->deepAfterFind = false;

			$apre = $this->Apre->find( 'first', $querydata );
			$this->Apre->deepAfterFind = $deepAfterFind;

			// Récupération du dernier CER signé à la création de l'Apre
			$contratinsertion = $this->Apre->Personne->Contratinsertion->find(
				'first',
				array(
					'conditions' => array(
						'Contratinsertion.personne_id' => $apre['Apre']['personne_id'],
						'Contratinsertion.decision_ci' => 'V',
						'Contratinsertion.dd_ci <=' =>$apre['Apre']['datedemandeapre'],
						'Contratinsertion.df_ci >=' =>$apre['Apre']['datedemandeapre'],
					),
					'contain' => false
				)
			);
			if( empty( $contratinsertion ) ) {
				$fields = $this->Apre->Personne->Contratinsertion->fields();
				$contratinsertion = Hash::expand( Set::normalize( $fields ) );
			}
			$apre = Set::merge( $apre, $contratinsertion );

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
			$apre = Set::merge( $apre, $user );

			return $apre;
		}

		/**
		 * Retourne le PDF par défaut généré par les appels aux méthodes getDataForPdf, modeleOdt et
		 * à la méthode ged du behavior Gedooo
		 *
		 * @param type $id Id de l'APRE
		 * @param type $user_id Id de l'utilisateur connecté
		 * @return string
		 */
		public function getDefaultPdf( $id, $user_id ) {
			$pdf = $this->Apre->getStoredPdf( $id );

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
						'Type' => array(
							'voie' =>  $Option->typevoie(),
						),
						'type' => array(
							'voie' => $Option->typevoie()
						),
					)
				);

				$apre = $this->getDataForPdf( $id, $user_id );
				$modeledoc = $this->modeleOdt( $apre );

				$pdf = $this->Apre->ged( $apre, $modeledoc, false, $options );

				if( !empty( $pdf ) ) {
					$this->Apre->storePdf( $id, $modeledoc, $pdf ); // FIXME ?
				}
			}

			return $pdf;
		}

		/**
		 * Retourne un champ virtuel contenant la liste des aides liées à une
		 * APRE, séparées par la chaîne de caractères $glue.
		 *
		 * Si le nom du champ virtuel est vide, alors le champ non aliasé sera
		 * retourné.
		 *
		 * @param string $fieldName Le nom du champ virtuel; le modèle sera l'alias
		 *	du modèle (Apre) utilisé.
		 * @param string $glue La chaîne de caratcères utilisée pour séparer les
		 *	noms des aides.
		 * @return string
		 */
		public function vfListeAidesLiees93( $fieldName = 'aidesliees', $glue = '\\n\r-' ) {
			$unions = array();

			foreach( $this->aidesApre as $modelAide ) {
				$join = $this->Apre->join( $modelAide );
				$table = Inflector::tableize( $modelAide );
				$sql = $this->Apre->{$modelAide}->sq(
					array_words_replace(
						array(
							'fields' => array( 'COUNT(*)' ),
							'alias' => $table,
							'conditions' => $join['conditions']
						),
						array( $modelAide => $table )
					)
				);
				$unions[] = str_replace( 'COUNT(*)', "'{$modelAide}'", $sql );
			}

			$sql = "TRIM( BOTH ' ' FROM TRIM( TRAILING '{$glue}' FROM ARRAY_TO_STRING( ARRAY( ".implode( ' UNION ', $unions )." ), '{$glue}' ) ) )";

			if( !empty( $fieldName ) ) {
				$sql = "{$sql} AS \"{$this->Apre->alias}__{$fieldName}\"";
			}

			return $sql;
		}
	}