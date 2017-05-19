<?php
	/**
	 * Code source de la classe WebrsaTraitementpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractLogic', 'Model');
	App::uses('WebrsaLogicAccessInterface', 'Model/Interface');
	App::uses('WebrsaModelUtility', 'Utility');

	/**
	 * La classe WebrsaTraitementpcg66 possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaTraitementpcg66 extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaTraitementpcg66';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Traitementpcg66');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$fields = array(
				'Traitementpcg66.annule',
				'Traitementpcg66.typetraitement',
				'Traitementpcg66.dateenvoicourrier',
				'Traitementpcg66.reversedo',
			);

			return Hash::merge($query, array('fields' => array_values($fields)));
		}

		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array()) {
			$query = array(
				'fields' => array(
					'Traitementpcg66.id',
					'Traitementpcg66.personnepcg66_id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Traitementpcg66->join('Personnepcg66')
				),
				'contain' => false,
				'order' => array(
					'Traitementpcg66.created' => 'DESC',
					'Traitementpcg66.id' => 'DESC',
				)
			);

			$results = $this->Traitementpcg66->find('all', $this->completeVirtualFieldsForAccess($query));
			return $results;
		}

		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 *
		 * @see WebrsaAccess::getParamsList
		 * @param integer $dossierpcg66_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess($dossierpcg66_id, array $params = array()) {
			$results = array();

			if (in_array('ajoutPossible', $params)) {
				$results['ajoutPossible'] = $this->ajoutPossible($dossierpcg66_id);
			}

			return $results;
		}

		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 *
		 * @param integer $dossierpcg66_id
		 * @return boolean
		 */
		public function ajoutPossible($dossierpcg66_id) {
			return true;
		}

		public function sauvegardeTraitement($data) {
			$passageEpd = false;

			$dossierep = 0;
			if (isset($data['Traitementpcg66']['id']) && !empty($data['Traitementpcg66']['id']))
				$dossierep = $this->Traitementpcg66->Saisinepdoep66->find(
					'count',
					array(
						'conditions'=>array(
							'Saisinepdoep66.traitementpcg66_id'=>$data['Traitementpcg66']['id']
						)
					)
				);


			$success = true;

			$has = array('hascourrier', 'hasrevenu', 'haspiecejointe', 'hasficheanalyse');
			foreach ($has as $field) {
				if (empty($data['Traitementpcg66'][$field]))
					unset($data['Traitementpcg66'][$field]);
			}

			$dataTraitementpcg66 = array( 'Traitementpcg66' => $data['Traitementpcg66'] );

			// Si le type de traitement est "Fiche de calcul"
			if( $data['Traitementpcg66']['typetraitement'] == 'revenu' ) {
				$dataTraitementpcg66['Traitementpcg66']['dateecheance'] = $data['Traitementpcg66']['daterevision'];
			}

			$this->Traitementpcg66->create( $dataTraitementpcg66 );
			$success = $this->Traitementpcg66->save( null, array( 'atomic' => false ) ) && $success;

			$traitementpcg66_id = $this->Traitementpcg66->id;

			if ( $success && isset( $data['Traitementpcg66']['traitmentpdoIdClore'] ) && !empty( $data['Traitementpcg66']['traitmentpdoIdClore'] ) ) {
				foreach( $data['Traitementpcg66']['traitmentpdoIdClore'] as $id => $clore ) {
					if ( $clore == 'O' ) {
						$success = $this->Traitementpcg66->updateAllUnBound( array( 'Traitementpcg66.clos' => '\'O\'' ), array( '"Traitementpcg66"."id"' => $id ) ) && $success;
					}
				}
			}

			// Sauvegarde des modèles liés au courrier pour un traitement donné
			if( $success && $data['Traitementpcg66']['typetraitement'] == 'courrier' ) {
				$modeletypecourrierpcg66_id = Hash::get( $data, 'Modeletraitementpcg66.modeletypecourrierpcg66_id' );

				// Liste des pièces par modèle de courrier
				$listesPieces = $this->Traitementpcg66->Typecourrierpcg66->Modeletypecourrierpcg66->Piecemodeletypecourrierpcg66->find(
					'list',
					array(
						'conditions' => array(
							'Piecemodeletypecourrierpcg66.modeletypecourrierpcg66_id' => $modeletypecourrierpcg66_id
						),
						'contain' => false
					)
				);


				$dataModelTraitementpcg66 = array( 'Modeletraitementpcg66' => $data['Modeletraitementpcg66'] );
				$dataModelTraitementpcg66['Modeletraitementpcg66']['traitementpcg66_id'] = $traitementpcg66_id;

				$this->Traitementpcg66->Modeletraitementpcg66->create( $dataModelTraitementpcg66 );
				$success = $this->Traitementpcg66->Modeletraitementpcg66->save( null, array( 'atomic' => false ) ) && $success;

				$modeletraitementpcg66_id = $this->Traitementpcg66->Modeletraitementpcg66->id;

				if( !empty( $listesPieces ) ) {
					if( $success ) {
						foreach( array( 'piecesmodelestypescourrierspcgs66' ) as $tableliee ) {
							$modelelie = Inflector::classify( $tableliee );
							$modeleliaison = Inflector::classify( "mtpcgs66_pmtcpcgs66" );
							$foreignkey = Inflector::singularize( $tableliee ).'_id';
							$records = $this->Traitementpcg66->Modeletraitementpcg66->{$modeleliaison}->find(
								'list',
								array(
									'fields' => array( "{$modeleliaison}.id", "{$modeleliaison}.{$foreignkey}" ),
									'conditions' => array(
										"{$modeleliaison}.modeletraitementpcg66_id" => $modeletraitementpcg66_id
									)
								)
							);

							$oldrecordsids = array_values( $records );
							$nouveauxids = Hash::filter( (array)Set::extract( "/{$modelelie}/{$modelelie}", $data ) );


							if ( empty( $nouveauxids ) ) {
								$this->Traitementpcg66->Modeletraitementpcg66->{$modelelie}->invalidate( $modelelie, 'Merci de cocher au moins une case' );
								$success = false;
							}
							else {
								// En moins -> Supprimer
								$idsenmoins = array_diff( $oldrecordsids, $nouveauxids );
								$idsenmoins = array_filter( $idsenmoins );
								if( !empty( $idsenmoins ) ) {
									$success = $this->Traitementpcg66->Modeletraitementpcg66->{$modeleliaison}->deleteAll(
										array(
											"{$modeleliaison}.modeletraitementpcg66_id" => $modeletraitementpcg66_id,
											"{$modeleliaison}.{$foreignkey}" => $idsenmoins
										)
									) && $success;
								}

								// En plus -> Ajouter
								$idsenplus = array_diff( $nouveauxids, $oldrecordsids );
								$idsenplus = array_filter( $idsenplus );

								if( !empty( $idsenplus ) ) {
									foreach( $idsenplus as $idenplus ) {
										$record = array(
											$modeleliaison => array(
												"modeletraitementpcg66_id" => $modeletraitementpcg66_id,
												"{$foreignkey}" => $idenplus
											)
										);

										$this->Traitementpcg66->Modeletraitementpcg66->{$modeleliaison}->create( $record );
										$success = $this->Traitementpcg66->Modeletraitementpcg66->{$modeleliaison}->save( null, array( 'atomic' => false ) ) && $success;
									}
								}
							}
						}
					}
				}
				else {
					$success = !empty( $modeletypecourrierpcg66_id );
				}
			}

			// Si aucune date d'échéance, on clôture le traitement automatiquement
			if( $success && !isset( $data['Traitementpcg66']['dateecheance'] ) && $data['Traitementpcg66']['typetraitement'] != 'revenu' ) {
				$success = $this->Traitementpcg66->updateAllUnBound( array( 'Traitementpcg66.clos' => '\'O\'' ), array( '"Traitementpcg66"."id"' => $this->Traitementpcg66->id ) ) && $success;
			}

			// Si la date d'échéance vaut 0 (= aucune), on passe la date à NULL
			if( $success ) {
				if( ( isset ($data['Traitementpcg66']['dureeecheance'] ) && $data['Traitementpcg66']['dureeecheance'] == 0 && $data['Traitementpcg66']['typetraitement'] != 'revenu' ) || ( isset ($data['Traitementpcg66']['dureefinprisecompte'] ) && $data['Traitementpcg66']['dureefinprisecompte'] == 0 && $data['Traitementpcg66']['typetraitement'] == 'revenu' ) ) {
					$success = $this->Traitementpcg66->updateAllUnBound( array( 'Traitementpcg66.dateecheance' => NULL ), array( '"Traitementpcg66"."id"' => $this->Traitementpcg66->id ) ) && $success;
				}
			}

            // Mise à jour de l'état du dossier PCG selon le type de traitement enregistré
            // Soit un traitement de type Document arrivé
            // Soit un traitement de description = Courrier à l'allocataire
            $corbeillepcgDescriptionId = Configure::read( 'Corbeillepcg.descriptionpdoId' ); // Traiteement de description courrier à l'allocataire
            if( $success ) {
				if( ( $data['Traitementpcg66']['typetraitement'] == 'documentarrive' ) || in_array( $data['Traitementpcg66']['descriptionpdo_id'], $corbeillepcgDescriptionId ) ) {
					$success = $this->Traitementpcg66->Personnepcg66->Dossierpcg66->WebrsaDossierpcg66->updatePositionsPcgsById( $this->Traitementpcg66->dossierpcg66Id($this->Traitementpcg66->id) ) && $success;
				}
            }


            /**/
            if( $success ) {
				if( ( $data['Traitementpcg66']['typetraitement'] == 'dossierarevoir' ) ) {
                    $success = $this->Traitementpcg66->Personnepcg66->Dossierpcg66->WebrsaDossierpcg66->updatePositionsPcgsById( $this->Traitementpcg66->dossierpcg66Id($this->Traitementpcg66->id) ) && $success;
				}
            }


			return $success;
		}

		/**
		* Récupère les données pour le PDf
		*/
		public function getPdfFichecalcul( $id ) {
			// TODO: error404/error500 si on ne trouve pas les données
			$optionModel = ClassRegistry::init( 'Option' );
			$qual = $optionModel->qual();
			$services = $this->Traitementpcg66->Personnepcg66->Dossierpcg66->Serviceinstructeur->find( 'list' );
			$decisionspdos = $this->Traitementpcg66->Personnepcg66->Personnepcg66Situationpdo->Decisionpersonnepcg66->Decisionpdo->find( 'list' );
			$situationspdos = $this->Traitementpcg66->Personnepcg66->Personnepcg66Situationpdo->Situationpdo->find( 'list' );
			$conditions = array( 'Traitementpcg66.id' => $id );

			$joins = array(
				array(
					'table'      => 'personnespcgs66',
					'alias'      => 'Personnepcg66',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Personnepcg66.id = Traitementpcg66.personnepcg66_id' )
				),
				array(
					'table'      => 'personnespcgs66_situationspdos',
					'alias'      => 'Personnepcg66Situationpdo',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Personnepcg66.id = Personnepcg66Situationpdo.personnepcg66_id' )
				),
				array(
					'table'      => 'dossierspcgs66',
					'alias'      => 'Dossierpcg66',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Dossierpcg66.id = Personnepcg66.dossierpcg66_id' )
				),
				array(
					'table'      => 'users',
					'alias'      => 'User',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'User.id = Dossierpcg66.user_id' )
				),
				array(
					'table'      => 'personnes',
					'alias'      => 'Personne',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Personne.id = Personnepcg66.personne_id' )
				),
				array(
					'table'      => 'foyers',
					'alias'      => 'Foyer',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Foyer.id = Dossierpcg66.foyer_id' )
				),
				array(
					'table'      => 'dossiers',
					'alias'      => 'Dossier',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Dossier.id = Foyer.dossier_id' )
				),
				array(
					'table'      => 'adressesfoyers',
					'alias'      => 'Adressefoyer',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						'Foyer.id = Adressefoyer.foyer_id',
						'Adressefoyer.rgadr' => '01'
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
					'table'      => 'pdfs',
					'alias'      => 'Pdf',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						'Pdf.modele' => $this->Traitementpcg66->alias,
						'Pdf.fk_value = Traitementpcg66.id'
					)
				),
			);

			$queryData = array(
				'fields' => array(
					'Adresse.numvoie',
					'Adresse.libtypevoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.lieudist',
					'Adresse.numcom',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Adresse.pays',
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Dossier.matricule',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.nir',


				),
				'joins' => $joins,
				'conditions' => $conditions,
				'contain' => false
			);

			$data = $this->Traitementpcg66->find( 'first', $queryData );

			$data['Personne']['qual'] = Set::enum( $data['Personne']['qual'], $qual );

			return $this->Traitementpcg66->ged(
				$data,
				"PCG66/fichecalcul.odt",
				true,
				array()
			);
		}

		/**
		 * Données nécéssaire pour l'impression d'un courrier
		 *
		 * @param integer $id
		 * @param integer $user_id
		 * @param boolean $get_saved_data On récupère les éventuelles données sauvegardé ?
		 * @return array
		 */
		public function getDataForPdfCourrier( $id, $user_id, $get_saved_data = true ) {
			$data = $get_saved_data ?
				$this->Traitementpcg66->Dataimpression->find('first',
					array(
						'fields' => 'Dataimpression.data',
						'conditions' => array(
							'Dataimpression.modele' => 'Traitementpcg66',
							'Dataimpression.fk_value' => $id
						),
						'order' => array('Dataimpression.id' => 'DESC')
					)
				)
				: null
			;

			if ( !empty($data) ) {
				$json = Hash::get($data, 'Dataimpression.data');
				$data = json_decode($json, true);
			}
			else {
				$joins = array(
					$this->Traitementpcg66->join( 'Personnepcg66' ),
					$this->Traitementpcg66->Personnepcg66->join( 'Personnepcg66Situationpdo' ),
					$this->Traitementpcg66->Personnepcg66->join( 'Dossierpcg66' ),
					$this->Traitementpcg66->Personnepcg66->join( 'Personne' ),
					$this->Traitementpcg66->Personnepcg66->Personne->join( 'Bilanparcours66', array( 'type' => 'LEFT OUTER' ) ),
					$this->Traitementpcg66->Personnepcg66->Personne->join( 'Orientstruct', array( 'type' => 'LEFT OUTER' ) ),
					$this->Traitementpcg66->Personnepcg66->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
					$this->Traitementpcg66->Personnepcg66->Dossierpcg66->join( 'Foyer' ),
					$this->Traitementpcg66->Personnepcg66->Dossierpcg66->join( 'Poledossierpcg66', array( 'type' => 'LEFT OUTER' ) ),
					$this->Traitementpcg66->Personnepcg66->Dossierpcg66->Foyer->join( 'Dossier' ),
					$this->Traitementpcg66->Personnepcg66->Dossierpcg66->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Traitementpcg66->Personnepcg66->Dossierpcg66->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$this->Traitementpcg66->join( 'Modeletraitementpcg66' ),
					$this->Traitementpcg66->Modeletraitementpcg66->join( 'Modeletypecourrierpcg66' ),
					$this->Traitementpcg66->join( 'Serviceinstructeur' )
				);

				$conditions = array(
					'Traitementpcg66.id' => $id,
					'OR' => array(
						'Orientstruct.id IS NULL',
						'Orientstruct.id IN ( '.$this->Traitementpcg66->Personnepcg66->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere( 'Orientstruct.personne_id' ).' )'
					),
					'OR' => array(
						'Adressefoyer.id IS NULL',
						'Adressefoyer.id IN ( '.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01( 'Adressefoyer.foyer_id' ).' )'
					)
				);

				$queryData = array(
					'fields' => array_merge(
						$this->Traitementpcg66->fields(),
						$this->Traitementpcg66->Personnepcg66->Dossierpcg66->Poledossierpcg66->fields(),
						$this->Traitementpcg66->Modeletraitementpcg66->fields(),
						$this->Traitementpcg66->Modeletraitementpcg66->Modeletypecourrierpcg66->fields(),
						$this->Traitementpcg66->Personnepcg66->Personne->Bilanparcours66->fields(),
						$this->Traitementpcg66->Personnepcg66->Personne->Orientstruct->fields(),
						$this->Traitementpcg66->Personnepcg66->Personne->Orientstruct->Structurereferente->fields(),
						$this->Traitementpcg66->Personnepcg66->Personne->Bilanparcours66->fields(),
						array(
							'Adresse.numvoie',
							'Adresse.libtypevoie',
							'Adresse.nomvoie',
							'Adresse.complideadr',
							'Adresse.compladr',
							'Adresse.lieudist',
							'Adresse.numcom',
							'Adresse.codepos',
							'Adresse.nomcom',
							'Adresse.pays',
							'Dossier.numdemrsa',
							'Dossier.dtdemrsa',
							'Dossier.matricule',
							'Personne.qual',
							'Personne.nom',
							'Personne.prenom',
							'Dossierpcg66.user_id',
							'Dossierpcg66.orgpayeur',
							'Personne.dtnai',
							'Personne.nir',
						),
						$this->Traitementpcg66->Serviceinstructeur->fields()
					),
					'joins' => $joins,
					'conditions' => $conditions,
					'contain' => false
				);

				$queryData = $this->Traitementpcg66->joinCouple($queryData);

				$data = $this->Traitementpcg66->find( 'first', $queryData );

				$user = $this->Traitementpcg66->User->find(
					'first',
					array(
						'conditions' => array(
							'User.id' => $user_id
						),
						'contain' => false
					)
				);
				$data = Set::merge( $data, $user );

				$gestionnaire['Dossierpcg66'] = $this->Traitementpcg66->User->find(
					'first',
					array(
						'fields' => array(
							$this->Traitementpcg66->User->sqVirtualField( 'nom_complet' )
						),
						'conditions' => array(
							'User.id' => $data['Dossierpcg66']['user_id']
						),
						'contain' => false
						)
					);

				$data = Set::merge( $data, $gestionnaire );

				// Dates calculées sur les 3 mois suivants la date de début de prise en compte du courrier
				$datedebutCourrier = $data['Modeletraitementpcg66']['montantdatedebut'];
				if( !empty( $datedebutCourrier ) ) {
					$datedebutCourrier = strtotime( $datedebutCourrier );
					foreach( array( '0', '1', '2' ) as $i ) {
						$data['Modeletraitementpcg66']["moisprisencompte$i"] = date("Y-m-d", strtotime("+". $i ." months", $datedebutCourrier));
					}
				}
			}

			return $data;
		}


		/**
		* Récupère les données pour le PDf
		*/

		public function getPdfModeleCourrier( $id, $user_id) {
			$data = $this->getDataForPdfCourrier($id, $user_id);

			$modeleodtname = Set::classicExtract( $data, 'Modeletypecourrierpcg66.modeleodt' );
			$modeletraitementpcg66_id = Set::classicExtract( $data, 'Modeletraitementpcg66.id' );
			$piecesmanquantes = $this->Traitementpcg66->Modeletraitementpcg66->find(
				'all',
				array(
					'fields' => array_merge(
						$this->Traitementpcg66->Modeletraitementpcg66->Piecemodeletypecourrierpcg66->fields()
					),
					'contain' => false,
					'joins' => array(
						$this->Traitementpcg66->Modeletraitementpcg66->join( 'Mtpcg66Pmtcpcg66', array( 'type' => 'INNER' ) ),
						$this->Traitementpcg66->Modeletraitementpcg66->Mtpcg66Pmtcpcg66->join( 'Piecemodeletypecourrierpcg66', array( 'type' => 'INNER' ) )
					),
					'conditions' => array(
						'Mtpcg66Pmtcpcg66.modeletraitementpcg66_id' => $modeletraitementpcg66_id
					)
				)
			);

			$options = array(
				'Personne' => array( 'qual' => ClassRegistry::init( 'Option' )->qual() )
			);
			$options = Set::merge( $options, $this->Traitementpcg66->enums() );

			return $this->Traitementpcg66->ged(
				array(
					$data,
					'Piecesmanquantes' => $piecesmanquantes
				),
				"PCG66/Traitementpcg66/{$modeleodtname}.odt",
				true,
				$options
			);
		}

		/**
		 *	Sous-requête afin d'obtenir la liste des traitements PCG
		 *		- non clos
		 *		- et dont la date d'échéance est dépassée
		 */
		public function sqTraitementpcg66Echu( $personnepcg66IdFied = 'Personnepcg66.id' ) {
			return $this->Traitementpcg66->sq(
				array(
					'fields' => array(
						'traitementspcgs66.id'
					),
					'alias' => 'traitementspcgs66',
					'conditions' => array(
						"traitementspcgs66.personnepcg66_id = {$personnepcg66IdFied}",
						'traitementspcgs66.clos' => 'N',
						"traitementspcgs66.dateecheance < NOW()",
					),
					'order' => array( 'traitementspcgs66.datereception DESC' )
				)
			);
		}

		/**
         * Fonction permettant de récupérer les informations de la dernière
         *  fiche de calcul parmi les différents traitements PCGs d'une personne
         *
         * @param type $personneId
         * @param type $action
         * @param type $data
         * @return type
         */
        public function infoDerniereFicheCalcul( $personneId = 'Personne.id', $action, $data = array() ) {

            if( !empty( $personneId ) ) {
                $querydata = array(
                    'fields' => array(
                        'Traitementpcg66.nrmrcs',
                        'Traitementpcg66.dtdebutactivite',
                        'Traitementpcg66.regime',
                        'Traitementpcg66.raisonsocial',
                        'Traitementpcg66.created'
                    ),
                    'joins' => array(
                        $this->Traitementpcg66->Personnepcg66->join( 'Traitementpcg66', array( 'type' => 'INNER' ) )
                    ),
                    'contain' => false,
                    'conditions' => array(
                        'Personnepcg66.personne_id' => $personneId,
                        'Traitementpcg66.typetraitement' => 'revenu'
                    ),
                    'order' => array( 'Traitementpcg66.created DESC' )
                );

				$dataPersonnepcg66 = $this->Traitementpcg66->Personnepcg66->find( 'first', $querydata );

                $data = $dataPersonnepcg66;

            }

            return $data;
        }

		/**
		 * Retourne la query qui permet de trouver les fichiers PDFs d'un ou plusieurs Dossiers PCGs
		 * avec les traitements de type courrier.
		 *
		 * Lancez le find sur Foyer
		 *
		 * Rêgles particulières :
		 * - On ne ratache pas un PDF si il vien d'un "Dossier PCG / Décision / Traitement" annulé.
		 * - On ne peux récupérer le PDF d'une décision que si elle est validé et rataché au dossier PCG.
		 * - Les Traitements à imprimer du dossier PCG sont inclu.
		 * - Les Traitements à imprimer des autres dossiers PCGs d'un même foyer sont inclu si :
		 *		- le dossier PCG ne comporte pas de décision
		 *		- le dossier PCG à été émis par le même pôle (PDA / PDU).
		 *
		 * @param mixed $dossierpcg66_id
		 * @param mixed $conditionTraitementpcg	permet de spécifier un état en particulier
		 *										(etattraitementpcg => imprimer par défaut),
		 * @return array
		 */
		public function getPdfsQuery( $dossierpcg66_id, $conditionTraitementpcg = array('Traitementpcg66.etattraitementpcg' => 'imprimer') ) {
			return array(
				'fields' => array(
					'Traitementpcg66.id'
				),
				'joins' => array(
					array_words_replace(
						$this->Traitementpcg66->Personnepcg66->Dossierpcg66->Foyer->join('Dossierpcg66',
							array(
								'conditions' => array('Dossierpcg66.id' => $dossierpcg66_id),
								'type' => 'INNER',
							)
						),
						array('Dossierpcg66' => 'Dossierpcg66_maitre')
					),
					$this->Traitementpcg66->Personnepcg66->Dossierpcg66->Foyer->join('Dossierpcg66', array('type' => 'INNER')),
					$this->Traitementpcg66->Personnepcg66->Dossierpcg66->join('Personnepcg66', array('type' => 'INNER')),
					$this->Traitementpcg66->Personnepcg66->join('Traitementpcg66', array('type' => 'LEFT')),
				),
				'contain' => false,
				'conditions' => array(
					'NOT' => array(
						'Dossierpcg66.etatdossierpcg' => 'annule',
					),
					'Dossierpcg66.poledossierpcg66_id = Dossierpcg66_maitre.poledossierpcg66_id',
					array(
						$conditionTraitementpcg,
						'Traitementpcg66.imprimer' => '1',
						'Traitementpcg66.annule' => 'N',
					),
				)
			);
		}

		/**
		 * Permet d'obtenir tout les PDFs lié à un dossier PCG (Décision et Traitements)
		 *
		 * @param integer $dossierpcg66_id
		 * @param integer $user_id
		 * @param mixed $conditionTraitementpcg	permet de spécifier un état en particulier
		 *										(etattraitementpcg => imprimer par défaut),
		 * @return array
		 */
		public function getPdfsByDossierpcg66Id( $dossierpcg66_id, $user_id, $conditionTraitementpcg = array('Traitementpcg66.etattraitementpcg' => 'imprimer') ) {
			$query = $this->getPdfsQuery($dossierpcg66_id, $conditionTraitementpcg);

			$results = array();
			foreach ((array)$this->Traitementpcg66->Personnepcg66->Dossierpcg66->Foyer->find('all', $query) as $result) {
				$traitement_id = Hash::get($result, 'Traitementpcg66.id');
				if ($traitement_id) {
					$results[] = $this->getPdfModeleCourrier($traitement_id, $user_id);
				}
			}

			return $results;
		}
	}