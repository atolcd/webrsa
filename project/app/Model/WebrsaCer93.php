<?php
	/**
	 * Code source de la classe WebrsaCer93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractLogic', 'Model' );
	App::uses( 'WebrsaLogicAccessInterface', 'Model/Interface' );
	App::uses( 'WebrsaModelUtility', 'Utility' );

	/**
	 * La classe WebrsaCer93 contient la logique métier concernant les CER du
	 * CD 93.
	 *
	 * @package app.Model
	 */
	class WebrsaCer93 extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCer93';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Cer93' );

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @todo
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess( array $query = array(), array $params = array() ) {
			$sqDossierepEncours = $this->Cer93->Contratinsertion->Personne->Dossierep->vfDossierepEnCours(
				'Contratinsertion.personne_id',
				array( 'signalementseps93', 'contratscomplexeseps93' )
			);

			$sqDossierepPossible = $this->Cer93->Contratinsertion->Personne->Dossierep->vfDossierepPossible(
				'Contratinsertion.personne_id'
			);

			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'Contratinsertion.decision_ci',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
					'Cer93.positioncer',
					"( {$sqDossierepPossible} ) AS \"Dossierep__possible\"",
					"( {$sqDossierepEncours} ) AS \"Dossierep__encours_cer\""
				)
			);

			return $query;
		}

		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 *
		 * @todo
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array()) {
			$query = array(
				'fields' => array(),
				'joins' => array(
					$this->Cer93->join( 'Contratinsertion', array( 'type' => 'INNER' ) )
				),
				'conditions' => $conditions,
				'contain' => false,
			);

			$results = $this->Cer93->find( 'all', $this->completeVirtualFieldsForAccess( $query ) );
			return $results;
		}

		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 *
		 * @todo
		 *
		 * @see WebrsaAccess::getParamsList
		 * @param integer $personne_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess($personne_id, array $params = array()) {
			$results = array();

			if( in_array( 'ajoutPossible', $params ) ) {
				$results['ajoutPossible'] = $this->ajoutPossible( $personne_id ) 
					&& $this->Cer93->Contratinsertion->Personne->PersonneReferent->hasReferent($personne_id);
			}

			return $results;
		}

		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 *
		 * @fixme: plus les passages en EP
		 *
		 * @param integer $personne_id
		 * @param array $messages
		 * @return boolean
		 */
		public function ajoutPossible( $personne_id ) {
			$query = array(
				'fields' => array(
					'Contratinsertion.id',
					'Cer93.positioncer',
				),
				'contain' => false,
				'joins' => array(
					$this->Cer93->join( 'Contratinsertion', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					'Contratinsertion.personne_id' => $personne_id,
					'Cer93.positioncer NOT LIKE' => '99%'
				)
			);

			$result = $this->Cer93->find( 'first', $query );
			return empty( $result );
		}

		// ---------------------------------------------------------------------

		/**
		 * Retourne un query "Dossiers d'EP en cours de traitement".
		 *
		 * @return array
		 */
		protected function _qdDossierepEnCours() {
			$Commissionep = $this->Cer93->Contratinsertion->Personne->Dossierep->Passagecommissionep->Commissionep;
			$query = $this->Cer93->Contratinsertion->Personne->Dossierep->getDossiersQuery();
			$query['fields'] = array(
				'Dossierep.id',
				'Dossierep.personne_id',
				'Dossierep.themeep',
				'Dossierep.created',
				'Dossierep.modified',
				'Passagecommissionep.etatdossierep',
				'Contratinsertion.dd_ci',
				'Contratinsertion.df_ci',
			);
			$query['conditions'][] = array(
				'Dossierep.actif' => '1',
				array(
					'OR' => array(
						'Commissionep.id IS NULL',
						'Commissionep.etatcommissionep' => $Commissionep::$etatsEnCours
					)
				),
				array(
					'OR' => array(
						'Passagecommissionep.id IS NULL',
						'NOT' => array(
							'Passagecommissionep.etatdossierep' => array( 'traite', 'annule' )
						)
					)
				)
			);

			return $query;
		}

		/**
		 * Dossiers d'EP "Contrat complexe" en cours ?
		 *
		 * @param array $conditions Conditions à ajouter au query
		 * @return array
		 */
		public function qdContratscomplexes( array $conditions = array() ) {
			$query = $this->_qdDossierepEnCours();
			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'Contratcomplexeep93.id',
					'Contratcomplexeep93.created'
				)
			);
			$query['joins'] = array_merge(
				$query['joins'],
				array(
					$this->Cer93->Contratinsertion->Personne->Dossierep->join( 'Contratcomplexeep93', array( 'type' => 'INNER' ) ),
					$this->Cer93->Contratinsertion->Personne->Dossierep->Contratcomplexeep93->join( 'Contratinsertion', array( 'type' => 'INNER' ) )
				)
			);

			$query['conditions'][] = $conditions;

			return $query;
		}

		/**
		 * Dossiers d'EP "Signalements pour non respect du contrat" en cours ?
		 *
		 * @param array $conditions
		 */
		public function qdSignalementseps93( array $conditions = array() ) {
			$query = $this->_qdDossierepEnCours();
			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'Signalementep93.id',
					'Signalementep93.date',
					'Signalementep93.rang'
				)
			);
			$query['joins'] = array_merge(
				$query['joins'],
				array(
					$this->Cer93->Contratinsertion->Personne->Dossierep->join( 'Signalementep93', array( 'type' => 'INNER' ) ),
					$this->Cer93->Contratinsertion->Personne->Dossierep->Signalementep93->join( 'Contratinsertion', array( 'type' => 'INNER' ) )
				)
			);

			$query['conditions'][] = $conditions;

			return $query;
		}

		/**
		 * Liste des options envoyées à la vue pour le CER93
		 *
		 * @return array
		 */
		public function optionsView() {
			$qual = $this->Cer93->Contratinsertion->Personne->enum( 'qual' );

			$options = array(
				'Cer93' => array(
					'qual' => $qual
				),
				'Contratinsertion' => array(
					'structurereferente_id' => $this->Cer93->Contratinsertion->Structurereferente->listOptions(),
					'referent_id' => $this->Cer93->Contratinsertion->Referent->WebrsaReferent->listOptions()
				),
				'Prestation' => array(
					'rolepers' => $this->Cer93->Contratinsertion->Personne->Prestation->enum( 'rolepers' )
				),
				'Personne' => array(
					'qual' => $qual
				),
				'Serviceinstructeur' => array(
					'typeserins' => ClassRegistry::init( 'Option' )->typeserins()
				),
				'Expprocer93' => array(
					'metierexerce_id' => $this->Cer93->Expprocer93->Metierexerce->find( 'list' ),
					'secteuracti_id' => $this->Cer93->Expprocer93->Secteuracti->find( 'list' )
				),
				'Foyer' => array(
					'sitfam' => $this->Cer93->Contratinsertion->Personne->Foyer->enum('sitfam')
				),
				'dureehebdo' => array_range( '0', '39' ),
				'dureecdd' => $this->Cer93->Contratinsertion->enum('duree_cdd'),
				'Naturecontrat' => array(
					'naturecontrat_id' => $this->Cer93->Naturecontrat->find( 'list' )
				)
			);

			$options = Hash::merge(
				$this->Cer93->Contratinsertion->Personne->Dsp->enums(),
				$this->Cer93->enums(),
				$this->Cer93->Histochoixcer93->enums(),
				$this->Cer93->Expprocer93->enums(),
				$options
			);

			return $options;

		}

		/**
		 * Retourne l'ensemble de données liées au CER en cours
		 *
		 * @param integer $id Id du CER
		 * @return array
		 */
		public function dataView( $contratinsertion_id ) {

			// Recherche du contrat pour l'affichage
			$data = $this->Cer93->Contratinsertion->find(
				'first',
				array(
					'conditions' => array(
						'Contratinsertion.id' => $contratinsertion_id
					),
					'contain' => array(
						'Cer93' => array(
							'Annulateur' => array(
								'fields' => array( 'nom_complet' )
							),
							'Compofoyercer93',
							'Diplomecer93',
							'Expprocer93' => array(
								'Entreeromev3' => array(
									'Familleromev3',
									'Domaineromev3',
									'Metierromev3',
									'Appellationromev3'
								)
							),
							'Histochoixcer93' => array(
								'User' => array(
									'fields' => array( 'nom_complet' )
								),
								'order' => array( 'Histochoixcer93.etape ASC' ),
								'Commentairenormecer93'
							),
							'Sujetcer93',
							'Emptrouvromev3' => array(
								'Familleromev3',
								'Domaineromev3',
								'Metierromev3',
								'Appellationromev3',
							),
							'Sujetromev3' => array(
								'Familleromev3',
								'Domaineromev3',
								'Metierromev3',
								'Appellationromev3',
							)
						),
						'Structurereferente' => array(
							'Typeorient'
						),
						'Referent',
						'Personne' => array(
							'Foyer' => array(
								'Adressefoyer' => array(
									'Adresse',
									'conditions' => array(
										'Adressefoyer.id IN (
											'.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01('Adressefoyer.foyer_id').'
										)'
									),
									'NvTransfertpdv93'
								)
							)
						)
					)
				)
			);

			$data = $this->Cer93->Contratinsertion->Personne->Foyer->Adressefoyer->NvTransfertpdv93->calculVfdateAnterieureTransfert(
				$data,
				'Personne.Foyer.Adressefoyer.0.NvTransfertpdv93.created',
				'Contratinsertion.date_saisi_ci',
				'Personne.Foyer.Adressefoyer.0.NvTransfertpdv93.encoursvalidation'
			);

			$data['Adresse'] = $data['Personne']['Foyer']['Adressefoyer'][0]['Adresse'];

			$sousSujetsIds = Hash::filter( (array)Set::extract( $data, '/Cer93/Sujetcer93/Cer93Sujetcer93/soussujetcer93_id' ) );
			$valeursparSousSujetsIds = Hash::filter( (array)Set::extract( $data, '/Cer93/Sujetcer93/Cer93Sujetcer93/valeurparsoussujetcer93_id' ) );
			if( !empty( $sousSujetsIds ) ) {
				$sousSujets = $this->Cer93->Sujetcer93->Soussujetcer93->find( 'list', array( 'conditions' => array( 'Soussujetcer93.id' => $sousSujetsIds ) ) );

				foreach( $data['Cer93']['Sujetcer93'] as $key => $values ) {
					if( isset( $values['Cer93Sujetcer93']['soussujetcer93_id'] ) && !empty( $values['Cer93Sujetcer93']['soussujetcer93_id'] ) ) {
						$data['Cer93']['Sujetcer93'][$key]['Cer93Sujetcer93']['Soussujetcer93'] = array( 'name' => $sousSujets[$values['Cer93Sujetcer93']['soussujetcer93_id']] );
					}
					else {
						$data['Cer93']['Sujetcer93'][$key]['Cer93Sujetcer93']['Soussujetcer93'] = array( 'name' => null );
					}

					if( !empty( $valeursparSousSujetsIds ) ) {
						// Valeur par sous sujet
						$valeursparSousSujets = $this->Cer93->Sujetcer93->Soussujetcer93->Valeurparsoussujetcer93->find( 'list', array( 'conditions' => array( 'Valeurparsoussujetcer93.id' => $valeursparSousSujetsIds ) ) );

						//Valeur par sous s sujet
						if( isset( $values['Cer93Sujetcer93']['valeurparsoussujetcer93_id'] ) && !empty( $values['Cer93Sujetcer93']['valeurparsoussujetcer93_id'] ) ) {
							$data['Cer93']['Sujetcer93'][$key]['Cer93Sujetcer93']['Valeurparsoussujetcer93'] = array( 'name' => $valeursparSousSujets[$values['Cer93Sujetcer93']['valeurparsoussujetcer93_id']] );
						}
						else {
							$data['Cer93']['Sujetcer93'][$key]['Cer93Sujetcer93']['Valeurparsoussujetcer93'] = array( 'name' => null );
						}
					}
				}
			}

			return $data;
		}

		/**
		 * Recherche des données CAF liées à l'allocataire dans le cadre du Cer93.
		 *
		 * @param integer $personne_id
		 * @return array
		 * @throws NotFoundException
		 * @throws InternalErrorException
		 */
		public function dataCafAllocataire( $personne_id ) {
			$Informationpe = ClassRegistry::init( 'Informationpe' );

			$querydataCaf = array(
				'fields' => array_merge(
					$this->Cer93->Contratinsertion->Personne->fields(),
					$this->Cer93->Contratinsertion->Personne->Prestation->fields(),
					$this->Cer93->Contratinsertion->Personne->Dsp->fields(),
					$this->Cer93->Contratinsertion->Personne->DspRev->fields(),
					$this->Cer93->Contratinsertion->Personne->Foyer->fields(),
					$this->Cer93->Contratinsertion->Personne->Foyer->Adressefoyer->Adresse->fields(),
					$this->Cer93->Contratinsertion->Personne->Foyer->Dossier->fields(),
					array(
						'Historiqueetatpe.identifiantpe',
						'Historiqueetatpe.etat'
					)
				),
				'joins' => array(
					$Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', 'LEFT OUTER' ),
					$Informationpe->join( 'Historiqueetatpe', array( 'type' => 'LEFT OUTER' ) ),
					$this->Cer93->Contratinsertion->Personne->join( 'Dsp', array( 'type' => 'LEFT OUTER' )),
					$this->Cer93->Contratinsertion->Personne->join( 'DspRev', array( 'type' => 'LEFT OUTER' )),
					$this->Cer93->Contratinsertion->Personne->join( 'Foyer', array( 'type' => 'INNER' )),
					$this->Cer93->Contratinsertion->Personne->join( 'Prestation', array( 'type' => 'LEFT OUTER'  )),
					$this->Cer93->Contratinsertion->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Cer93->Contratinsertion->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$this->Cer93->Contratinsertion->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
				),
				'conditions' => array(
					'Personne.id' => $personne_id,
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.id IN ( '.$this->Cer93->Contratinsertion->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
						)
					),
					array(
						'OR' => array(
							'Dsp.id IS NULL',
							'Dsp.id IN ( '.$this->Cer93->Contratinsertion->Personne->Dsp->WebrsaDsp->sqDerniereDsp( 'Personne.id' ).' )'
						)
					),
					array(
						'OR' => array(
							'DspRev.id IS NULL',
							'DspRev.id IN ( '.$this->Cer93->Contratinsertion->Personne->DspRev->sqDerniere( 'Personne.id' ).' )'
						)
					),
					array(
						'OR' => array(
							'Informationpe.id IS NULL',
							'Informationpe.id IN( '.$Informationpe->sqDerniere( 'Personne' ).' )'
						)
					),
					array(
						'OR' => array(
							'Historiqueetatpe.id IS NULL',
							'Historiqueetatpe.id IN( '.$Informationpe->Historiqueetatpe->sqDernier( 'Informationpe' ).' )'
						)
					)
				),
				'contain' => false
			);
			$dataCaf = $this->Cer93->Contratinsertion->Personne->find( 'first', $querydataCaf );

			// On copie les DspsRevs si elles existent à la place des DSPs (on garde l'information la plus récente)
			if( !empty( $dataCaf['DspRev']['id'] ) ) {
				$dataCaf['Dsp'] = $dataCaf['DspRev'];
			}
			unset( $dataCaf['DspRev'] );

			// On s'assure d'avoir trouvé l'allocataire
			if( empty( $dataCaf ) ) {
				throw new NotFoundException();
			}

			// Et que celui-ci soit bien demandeur ou conjoint
			if( !in_array( $dataCaf['Prestation']['rolepers'], array( 'DEM', 'CJT' ) ) ) {
				throw new InternalErrorException( "L'allocataire \"{$personne_id}\" doit être demandeur ou conjont" );
			}

			// Bloc 2 : Composition du foyer
			// Récupération des informations de composition du foyer de l'allocataire
			$composfoyerscers93 = $this->Cer93->Contratinsertion->Personne->find(
				'all',
				array(
					'fields' => array(
						'"Personne"."qual" AS "Compofoyercer93__qual"',
						'"Personne"."nom" AS "Compofoyercer93__nom"',
						'"Personne"."prenom" AS "Compofoyercer93__prenom"',
						'"Personne"."dtnai" AS "Compofoyercer93__dtnai"',
						'"Prestation"."rolepers" AS "Compofoyercer93__rolepers"'
					),
					'conditions' => array( 'Personne.foyer_id' => $dataCaf['Foyer']['id'] ),
					'contain' => array(
						'Prestation'
					)
				)
			);
			$composfoyerscers93 = array( 'Compofoyercer93' => Set::classicExtract( $composfoyerscers93, '{n}.Compofoyercer93' ) );
			$dataCaf = Set::merge( $dataCaf, $composfoyerscers93 );

			return $dataCaf;
		}

		/**
		 * Préparation des données du formulaire d'ajout ou de modification d'un
		 * CER pour le CG 93.
		 *
		 * @param integer $personne_id
		 * @param integer $contratinsertion_id
		 * @param integer $user_id
		 * @return array
		 * @throws InternalErrorException
		 * @throws NotFoundException
		 */
		public function prepareFormDataAddEdit( $personne_id, $contratinsertion_id, $user_id ) {
			// Recherche des données CAF.
			$dataCaf = $this->Cer93->WebrsaCer93->dataCafAllocataire( $personne_id );

			// Querydata pour le contrat
			$querydataCer = array(
				'contain' => array(
					'Cer93' => array(
						'Diplomecer93' => array(
							'order' => array( 'Diplomecer93.annee DESC' )
						),
						'Expprocer93' => array(
							'Entreeromev3',
							'order' => array( 'Expprocer93.anneedeb DESC' )
						),
						'Sujetcer93',
						'Emptrouvromev3',
						'Sujetromev3' => array(
							'Familleromev3',
							'Domaineromev3',
							'Metierromev3',
							'Appellationromev3'
						)
					),
				)
			);

			// Données de l'utilisateur
			$querydataUser = array(
				'conditions' => array(
					'User.id' => $user_id
				),
				'contain' => array(
					'Structurereferente',
					'Referent' => array(
						'Structurereferente'
					)
				)
			);
			$dataUser = $this->Cer93->Contratinsertion->User->find( 'first', $querydataUser );

			// On s'assure que l'utilisateur existe
			if( empty( $dataUser ) ) {
				throw new InternalErrorException( "Utilisateur non trouvé \"{$user_id}\"" );
			}

			// Si c'est une modification, on lit l'enregistrement, on actualise
			// les données (CAF et dernier CER validé) et on renvoit.
			if( !empty( $contratinsertion_id ) ) {
				$querydataCerActuel = $querydataCer;
				$querydataCerActuel['conditions'] = array(
					'Contratinsertion.id' => $contratinsertion_id
				);
				$dataCerActuel = $this->Cer93->Contratinsertion->find( 'first', $querydataCerActuel );

				// Il faut que l'enregistrement à modifier existe
				if( empty( $dataCerActuel ) ) {
					throw new NotFoundException();
				}

				// Il faut que l'enregistrement à modifier soit enregistré
				if( $dataCerActuel['Cer93']['positioncer'] !== '00enregistre' ) {
					throw new InternalErrorException( 'Impossible de modifier un CER déjà traité ou en cours de traitement' );
				}

				$data = $dataCerActuel;

				$modelsToCopy = array( 'Diplomecer93', 'Expprocer93', 'Sujetcer93' );
				foreach( $modelsToCopy as $modelName ) {
					$data[$modelName] = $dataCerActuel['Cer93'][$modelName];
					unset( $data['Cer93'][$modelName] );
					// FIXME: vérifier lors de la copie de entrées ROME v3
				}

				// Partie "Expériences professionnelles", préfixes
				foreach( $data['Expprocer93'] as $key => $expprocer93 ) {
					$data['Expprocer93'][$key] = $this->Cer93->Expprocer93->Entreeromev3->prepareFormDataAddEdit( $expprocer93 );
				}

				// Partie "Avez-vous trouvé un emploi ?" > "Si oui, veuillez préciser :"
				$data = Hash::merge(
					$data,
					$this->Cer93->Emptrouvromev3->prepareFormDataAddEdit( array( 'Emptrouvromev3' => $dataCerActuel['Cer93']['Emptrouvromev3'] ) ),
					$this->Cer93->Sujetromev3->prepareFormDataAddEdit( array( 'Sujetromev3' => $dataCerActuel['Cer93']['Sujetromev3'] ) )
				);

				// Bloc 6 : Liste des sujets sur lesquels le CER porte
				$data['Sujetcer93'] = array( 'Sujetcer93' => Set::classicExtract( $data, 'Sujetcer93.{n}.Cer93Sujetcer93' ) );
			}
			// Sinon, on construit un nouvel enregistrement vide, on y met les
			// données CAF et ancien CER.
			else {
				// En cas d'ajout, aucun autre CER ne peut avoir une positioncer autre que finale (99xxxx)
				$query = array(
					'fields' => array(
						'Contratinsertion.id'
					),
					'conditions' => array(
						'Contratinsertion.personne_id' => $personne_id,
						'NOT' => array( 'Cer93.positioncer LIKE' => '99%' )
					),
					'contain' => false,
					'joins' => array(
						$this->Cer93->join( 'Contratinsertion', array( 'type' => 'LEFT OUTER' ) )
					)
				);
				$cerNon99 = $this->Cer93->find( 'first', $query );
				if( !empty( $cerNon99 ) ) {
					throw new InternalErrorException( 'Impossible d\'ajouter un CER à cet allocataire car un autre CER est en cours de traitement' );
				}

				// Création d'un "enregistrement type" vide.
				$data = array(
					'Contratinsertion' => array(
						'id' => null,
						'decision_ci' => 'E',
						'rg_ci' => null
					),
					'Cer93' => array(
						'id' => null,
						'contratinsertion_id' => null,
						'nomutilisateur' => null,
						'structureutilisateur' => null,
						'nivetu' => null
					),
					'Compofoyercer93' => array(),
					'Diplomecer93' => array(),
					'Expprocer93' => array(),
					'Sujetcer93' => array(),
				);

				$dataReferentParcours = $this->Cer93->Contratinsertion->Personne->PersonneReferent->find(
					'first',
					array(
						'conditions' => array(
							'PersonneReferent.personne_id' => $personne_id,
							'PersonneReferent.dfdesignation IS NULL'
						),
						'contain' => array(
							'Referent'
						)
					)
				);
				// On préremplit le formulaire avec des données du référent affecté (du parcours actuel)
				if( !empty( $dataReferentParcours ) ) {
					$data['Contratinsertion']['structurereferente_id'] = $dataReferentParcours['Referent']['structurereferente_id'];
					$data['Contratinsertion']['referent_id'] = $dataReferentParcours['Referent']['id'];
				}
				// On préremplit le formulaire avec des données de l'utilisateur connecté si possible
				else {
					if( !empty( $dataUser['Structurereferente']['id'] ) ) {
						$data['Contratinsertion']['structurereferente_id'] = $dataUser['Structurereferente']['id'];
					}
					else if( !empty( $dataUser['Referent']['id'] ) ) {
						$data['Contratinsertion']['structurereferente_id'] = $dataUser['Referent']['structurereferente_id'];
						$data['Contratinsertion']['referent_id'] = $dataUser['Referent']['id'];
					}
				}
			}

			// On ajoute d'autres données de l'utilisateur connecté
			// TODO: du coup, on peut faire on delete set null (+la structure ?)
			$data['Cer93']['user_id'] = $user_id;
			$data['Cer93']['nomutilisateur'] = $dataUser['User']['nom_complet'];
			if( !empty( $dataUser['Structurereferente']['id'] ) ) {
				$data['Cer93']['structureutilisateur'] = $dataUser['Structurereferente']['lib_struc'];;
			}
			else if( !empty( $dataUser['Referent']['id'] ) ) {
				$data['Cer93']['structureutilisateur'] = $dataUser['Referent']['Structurereferente']['lib_struc'];
			}

			// Fusion avec les données CAF
			$data = Set::merge( $data, $dataCaf );

			// 1. Récupération de l'adresse complète afin de remplir le champ adresse du CER93
			$adresseComplete = trim( $dataCaf['Adresse']['numvoie'].' '.$dataCaf['Adresse']['libtypevoie'].' '.$dataCaf['Adresse']['nomvoie']."\n".$dataCaf['Adresse']['compladr'].' '.$dataCaf['Adresse']['complideadr'] );

			// 2. Transposition des données
			//Bloc 2 : Etat civil
			$data['Cer93']['matricule'] = $dataCaf['Dossier']['matricule'];
			$data['Cer93']['numdemrsa'] = $dataCaf['Dossier']['numdemrsa'];
			$data['Cer93']['rolepers'] = $dataCaf['Prestation']['rolepers'];
			$data['Cer93']['dtdemrsa'] = $dataCaf['Dossier']['dtdemrsa'];
			$data['Cer93']['identifiantpe'] = $dataCaf['Historiqueetatpe']['identifiantpe'];
			$data['Cer93']['qual'] = $dataCaf['Personne']['qual'];
			$data['Cer93']['nom'] = $dataCaf['Personne']['nom'];
			$data['Cer93']['nomnai'] = $dataCaf['Personne']['nomnai'];
			$data['Cer93']['prenom'] = $dataCaf['Personne']['prenom'];
			$data['Cer93']['dtnai'] = $dataCaf['Personne']['dtnai'];
			$data['Cer93']['adresse'] = $adresseComplete;
			$data['Cer93']['codepos'] = $dataCaf['Adresse']['codepos'];
			$data['Cer93']['nomcom'] = $dataCaf['Adresse']['nomcom'];
			$data['Cer93']['sitfam'] = $dataCaf['Foyer']['sitfam'];
			$data['Cer93']['dtdemrsa'] = $dataCaf['Dossier']['dtdemrsa'];

			// Bloc 3
			if( !isset( $data['Cer93']['inscritpe'] ) || is_null( $data['Cer93']['inscritpe'] ) ) {
				$data['Cer93']['inscritpe'] = null;
				if( isset( $dataCaf['Historiqueetatpe']['etat'] ) && !empty( $dataCaf['Historiqueetatpe']['etat'] ) ) {
					$data['Cer93']['inscritpe'] = ( $dataCaf['Historiqueetatpe']['etat'] == 'inscription' );
				}
			}

			// Copie des données du dernier CER validé en cas d'ajout
			if( empty( $contratinsertion_id ) ) {
				// Données du dernier CER validé
				$sqDernierCerValide = $this->Cer93->Contratinsertion->sq(
					array(
						'alias' => 'derniercervalide',
						'fields' => array( 'derniercervalide.id' ),
						'conditions' => array(
							'derniercervalide.personne_id = Contratinsertion.personne_id',
							'derniercervalide.decision_ci' => 'V',
						),
						'order' => array( 'derniercervalide.rg_ci DESC' ),
						'limit' => 1
					)
				);
				$querydataDernierCerValide = $querydataCer;
				$querydataDernierCerValide['conditions'] = array(
					'Contratinsertion.personne_id' => $personne_id,
					"Contratinsertion.id IN ( {$sqDernierCerValide} )"
				);

				$dataDernierCerValide = $this->Cer93->Contratinsertion->find( 'first', $querydataDernierCerValide );

				// Copie des données du dernier CER validé
				if( !empty( $dataDernierCerValide ) ) {
					//Champ pour le bloc 5 reprenant ce qui était prévu dans le pcd CER
					$data['Cer93']['prevupcd'] = $dataDernierCerValide['Cer93']['prevu'];

					// Copie des champs du CER précédent
					$data['Cer93']['isemploitrouv'] = 'N';
					$cer93FieldsToCopy = array( 'incoherencesetatcivil', 'cmu', 'cmuc', 'nivetu', 'autresexps' );
					foreach( $cer93FieldsToCopy as $field ) {
						$data['Cer93'][$field] = $dataDernierCerValide['Cer93'][$field];
					}

					// Copie des enregistrements liés
					$cer93ModelsToCopy = array( 'Diplomecer93', 'Expprocer93', 'Sujetcer93' );
					foreach( $cer93ModelsToCopy as $modelName ) {
						if( isset( $dataDernierCerValide['Cer93'][$modelName] ) ) {
							$data[$modelName] = $dataDernierCerValide['Cer93'][$modelName];
							if( !empty( $data[$modelName] ) ) {
								foreach( array_keys( $data[$modelName] ) as $key ) {
									unset(
										$data[$modelName][$key]['id'],
										$data[$modelName][$key]['cer93_id'],
										$data[$modelName][$key]['Cer93Sujetcer93']['id'],
										$data[$modelName][$key]['Cer93Sujetcer93']['cer93_id'],
										$data[$modelName][$key]['created'],
										$data[$modelName][$key]['modified']
									);
								}
							}
						}
					}

					if( !empty( $data['Expprocer93'] ) ) {
						foreach( $data['Expprocer93'] as $key => $expprocer93 ) {
							unset(
								$expprocer93['id'],
								$expprocer93['entreeromev3_id'],
								$expprocer93['Entreeromev3']['id'],
								$expprocer93['Entreeromev3']['created'],
								$expprocer93['Entreeromev3']['modified']
							);
							$data['Expprocer93'][$key] = $this->Cer93->Expprocer93->Entreeromev3->prepareFormDataAddEdit( $expprocer93 );
						}
					}

					if( !empty( $data['Sujetcer93'] ) ) {
						$sousSujetsIds = Hash::filter( (array)Set::extract( $data, '/Sujetcer93/Cer93Sujetcer93/soussujetcer93_id' ) );
						$valeursparSousSujetsIds = Hash::filter( (array)Set::extract( $data, '/Sujetcer93/Cer93Sujetcer93/valeurparsoussujetcer93_id' ) );

						if( !empty( $sousSujetsIds ) ) {
							$sousSujets = $this->Cer93->Sujetcer93->Soussujetcer93->find( 'list', array( 'conditions' => array( 'Soussujetcer93.id' => $sousSujetsIds ) ) );
							foreach( $data['Sujetcer93'] as $key => $values ) {
								if( isset( $values['Cer93Sujetcer93']['soussujetcer93_id'] ) && !empty( $values['Cer93Sujetcer93']['soussujetcer93_id'] ) ) {
									$data['Sujetcer93'][$key]['Cer93Sujetcer93']['Soussujetcer93'] = array( 'name' => $sousSujets[$values['Cer93Sujetcer93']['soussujetcer93_id']] );
								}
								else {
									$data['Sujetcer93'][$key]['Cer93Sujetcer93']['Soussujetcer93'] = array( 'name' => null );
								}

								if( !empty( $valeursparSousSujetsIds ) ) {
									// Valeur par sous sujet
									$valeursparSousSujets = $this->Cer93->Sujetcer93->Soussujetcer93->Valeurparsoussujetcer93->find( 'list', array( 'conditions' => array( 'Valeurparsoussujetcer93.id' => $valeursparSousSujetsIds ) ) );

									//Valeur par sous sujet
									if( isset( $values['Cer93Sujetcer93']['valeurparsoussujetcer93_id'] ) && !empty( $values['Cer93Sujetcer93']['valeurparsoussujetcer93_id'] ) ) {
										$data['Sujetcer93'][$key]['Cer93Sujetcer93']['Valeurparsoussujetcer93'] = array( 'name' => $valeursparSousSujets[$values['Cer93Sujetcer93']['valeurparsoussujetcer93_id']] );
									}
									else {
										$data['Sujetcer93'][$key]['Cer93Sujetcer93']['Valeurparsoussujetcer93'] = array( 'name' => null );
									}
								}
							}
						}

						// Informations complémentaires
						$sujetromev3 = (array)Hash::get( $dataDernierCerValide, 'Cer93.Sujetromev3' );

						$data['Cer93']['sujetpcd'] = serialize( array( 'Sujetcer93' => $data['Sujetcer93'], 'Sujetromev3' => $sujetromev3 ) );
						$data['Sujetcer93'] = array();
					}

					// Cas où on a un dernier CER validé
					$data['Contratinsertion']['rg_ci'] = ( $dataDernierCerValide['Contratinsertion']['rg_ci'] ) + 1;
				}
				else {
					$data['Contratinsertion']['rg_ci'] = 1;

					// RG CER précédent, puis RG DSP
					$data['Cer93']['nivetu'] = $dataCaf['Dsp']['nivetu'];

					// Si le niveau d'étude vient des DSP, alors le niveau 1201 doit être rempli manuellement car il est scindé dans les DSP
					if( $data['Cer93']['nivetu'] === '1201' ) {
						$data['Cer93']['nivetu'] = null;
					}
				}
			}

			// Les données CAF prévalent
			$data['Cer93']['natlog'] = $dataCaf['Dsp']['natlog'];

			return $data;
		}

		/**
		 * 	Fonction permettant la sauvegarde du formulaire du CER 93.
		 *
		 * 	Une règle de validation est supprimée en amont
		 * 	Les valeurs de la table Compofoyercer93 sont mises à jour à chaque modifciation
		 *
		 * 	@param $data Les données à sauvegarder.
		 * 	@return boolean
		 */
		public function saveFormulaire( $data, $typeUser ) {
			$success = true;

			// Sinon, ça pose des problèmes lors du add car les valeurs n'existent pas encore
			$this->Cer93->unsetValidationRule( 'contratinsertion_id', NOT_BLANK_RULE_NAME );

			// Si aucun sujet n'est renseigné, alors on lance un erreur
			if( empty( $data['Sujetcer93']['Sujetcer93'] ) ) {
				$success = false;
				$this->Cer93->Sujetcer93->invalidate( 'Sujetcer93', 'Il est obligatoire de saisir au moins un sujet.' );
			}

			foreach( array( 'Compofoyercer93', 'Diplomecer93', 'Expprocer93' ) as $hasManyModel ) {
				$this->Cer93->{$hasManyModel}->unsetValidationRule( 'cer93_id', NOT_BLANK_RULE_NAME );

				if( isset( $data['Cer93']['id'] ) && !empty( $data['Cer93']['id'] ) ) {
					$expsproscers93 = array();
					if( $hasManyModel === 'Expprocer93' ) {
						$query = array(
							'fields' => array(
								'Expprocer93.id',
								'Expprocer93.entreeromev3_id'
							),
							'conditions' => array(
								'Expprocer93.cer93_id' => $data['Cer93']['id']
							),
							'contain' => false
						);

						$entreesromesv3_ids = $this->Cer93->Expprocer93->find( 'list', $query );

						if( !empty( $entreesromesv3_ids ) ) {
							$success = $this->Cer93->Expprocer93->Entreeromev3->deleteAll(
								array( 'Entreeromev3.id' => $entreesromesv3_ids ),
								false,
								false
							) && $success;
						}
					}

					$success = $this->Cer93->{$hasManyModel}->deleteAll(
						array( "{$hasManyModel}.cer93_id" => $data['Cer93']['id'] )
					) && $success;
				}
			}

			// On passe les champs du fieldset emploi trouvé si l'allocataire déclare
			// ne pas avoir trouvé d'emploi
			if( $data['Cer93']['isemploitrouv'] == 'N' ) {
				$fields = array( 'secteuracti_id', 'metierexerce_id', 'dureehebdo', 'naturecontrat_id', 'dureecdd' );
				foreach( $fields as $field ) {
					$data['Cer93'][$field] = null;
				}

				// Suppression de l'entrée de l'emploi trouvé, s'i y a lieu
				$emptrouvromev3_id = Hash::get( $data, 'Cer93.emptrouvromev3_id' );
				if( !empty( $emptrouvromev3_id ) ) {
					$success = $success && $this->Cer93->Emptrouvromev3->delete( $emptrouvromev3_id );
				}
				$data['Cer93']['emptrouvromev3_id'] = null;
			}

			if( !isset( $data['Cer93']['dureecdd'] ) ){
				$data['Cer93']['dureecdd'] = null;
			}

			// On passe le champ date de point de aprcours à null au cas où l'allocataire
			// décide finalement de faire le point à la find e son contrat
			if( $data['Cer93']['pointparcours'] == 'alafin' ) {
				$fields = array( 'datepointparcours' );
				foreach( $fields as $field ) {
					$data['Cer93'][$field] = null;
				}
			}

			// ...
			$activationPath = Configure::read( 'Cer93.Sujetcer93.Romev3.path' );
			$activationValues = (array)Configure::read( 'Cer93.Sujetcer93.Romev3.values' );

			$activationSujetcer93 = ( 'Sujetcer93.Sujetcer93.{n}.sujetcer93_id' === $activationPath );
			$activationSoussujetcer93 = ( 'Sujetcer93.Sujetcer93.{n}.soussujetcer93_id' === $activationPath );

			$values = Hash::filter( (array)Hash::extract( $data, $activationPath ) );
			$intersect = array_intersect( $values, $activationValues );

			if( !empty( $activationPath ) && !empty( $activationValues ) ) {
				// Pas obligatoire
				if( !$intersect ) {
					$sujetromev3_id = Hash::get( $data, 'Sujetromev3.id' );
					if( !empty( $sujetromev3_id ) ) {
						$success = $success && $this->Cer93->Sujetromev3->delete( $sujetromev3_id );
						$data['Cer93']['sujetromev3_id'] = null;
					}
					unset( $data['Sujetromev3'] );
				}
			}

			$tmp = (array)$data['Sujetcer93']['Sujetcer93'];
			unset( $data['Sujetcer93'] );

			foreach( $tmp as $k => $v ) {
				foreach( array( 'autresoussujet', 'valeurparsoussujetcer93_id' ) as $w ) {
					if( isset( $v[$w] ) && empty( $v[$w] ) ) {
						unset( $tmp[$k][$w] );
					}
				}
			}

			$success = $this->Cer93->saveResultAsBool(
				$this->Cer93->saveAssociated( $data, array( 'validate' => 'first', 'atomic' => false, 'deep' => true ) )
			) && $success;

			if( !empty( $activationPath ) && !empty( $activationValues ) ) {
				// Champs obligatoires
				if( $intersect ) {
					// Ajout de l'erreur "Champ obligatoire" pour tous les champs
					foreach( $this->Cer93->Sujetromev3->romev3Fields as $fieldName ) {
						if( empty( $data['Sujetromev3'][$fieldName] ) ) {
							$success = false;
							$this->Cer93->Sujetromev3->invalidate( $fieldName, __d('cer93', 'Cer93.Error.Champobligatoire') );
						}
					}

					$this->Cer93->Sujetromev3->validationErrors = dedupe_validation_errors( $this->Cer93->Sujetromev3->validationErrors );
				}
			}

			// Validation des entrées ROME v.3 dans les expériences professionnelles
			if( isset( $data['Expprocer93'] ) && !empty( $data['Expprocer93'] ) ) {
				foreach( $data['Expprocer93'] as $key => $expprocer93 ) {
					// Ajout de l'erreur "Champ obligatoire" pour tous les champs
					foreach( $this->Cer93->Expprocer93->Entreeromev3->romev3Fields as $fieldName ) {
						if( empty( $expprocer93['Entreeromev3'][$fieldName] ) ) {
							$success = false;
							$this->Cer93->Expprocer93->validationErrors[$key]['Entreeromev3'][$fieldName][] = __d('cer93', 'Cer93.Error.Champobligatoire');
						}
					}

					if( !empty( $this->Cer93->Expprocer93->validationErrors[$key]['Entreeromev3'] ) ) {
						$this->Cer93->Expprocer93->validationErrors[$key]['Entreeromev3'] = dedupe_validation_errors( $this->Cer93->Expprocer93->validationErrors[$key]['Entreeromev3'] );
					}
				}
			}

			// Validation "Avez-vous trouvé un emploi" > "Si oui, veuillez préciser :" > "Emploi trouvé"
			if( $data['Cer93']['isemploitrouv'] === 'O' ) {
				// Ajout de l'erreur "Champ obligatoire" pour tous les champs
				foreach( $this->Cer93->Emptrouvromev3->romev3Fields as $fieldName ) {
					if( empty( $data['Emptrouvromev3'][$fieldName] ) ) {
						$success = false;
						$this->Cer93->Emptrouvromev3->invalidate( $fieldName, __d('cer93', 'Cer93.Error.Champobligatoire') );
					}
				}

				$this->Cer93->Emptrouvromev3->validationErrors = dedupe_validation_errors( $this->Cer93->Emptrouvromev3->validationErrors );
			}

			// Validation de la date de début de contrat : non vide et compris entre les dates configurés dans la variable de configuration Cer93.dateCER
			$dtDebutContrat = date_cakephp_to_sql($data['Contratinsertion']['dd_ci']);
			$dtDebutMax = $this->Cer93->getDebutContratMax();
			if($dtDebutContrat == false) {
				$success = false;
				$this->Cer93->Contratinsertion->invalidate('dd_ci', __d('cer93', 'Cer93.Error.Champobligatoire'));
			} else if( $dtDebutContrat < Configure::read('Cer93.dateCER.dtdebutMin') || $dtDebutContrat > $dtDebutMax ) {
				$success = false;
				$this->Cer93->Contratinsertion->invalidate('dd_ci', sprintf(
					__d('cer93', 'Cer93.Error.Datedebutcontrat'),
					date('d/m/Y', strtotime(Configure::read('Cer93.dateCER.dtdebutMin'))),
					date('d/m/Y', strtotime($dtDebutMax) ) )
				);
			}

			if( $success ) {
				$tmp = array( 'Sujetcer93' => array( 'Sujetcer93' => $tmp ) );
				$tmp['Cer93']['id'] = $this->Cer93->id;
				$success = $this->Cer93->saveResultAsBool(
				$this->Cer93->saveAll( $tmp, array( 'validate' => 'first', 'atomic' => false, 'deep' => false ) )
				) && $success;
			}


			// Dans le cas d'un ajout de CER, on vérifie s'il faut ajouter un rendez-vous implicite
			if( $success && empty( $data['Cer93']['id'] ) && Configure::read( 'Contratinsertion.RdvAuto.active' ) === true && ( $typeUser != 'cg' ) ) {
				$created = date( 'Y-m-d H:i:s' );

				$querydata = array(
					'conditions' => array(
						'Rendezvous.personne_id' => $data['Contratinsertion']['personne_id'],
						'Rendezvous.structurereferente_id' => $data['Contratinsertion']['structurereferente_id'],
						'Rendezvous.typerdv_id' => Configure::read( 'Contratinsertion.RdvAuto.typerdv_id' ),
						'daterdv' => date( 'Y-m-d', strtotime( $created ) ),
					),
					'contain' => false,
				);

				// Si on utilise la thématique...
				$useThematiquerdv = $this->Cer93->Contratinsertion->Personne->Rendezvous->Thematiquerdv->used();
				if( $useThematiquerdv ) {
					$querydata['joins'] = array(
						$this->Cer93->Contratinsertion->Personne->Rendezvous->join( 'RendezvousThematiquerdv', array( 'type' => 'INNER' ) )
					);
					$querydata['conditions']['RendezvousThematiquerdv.thematiquerdv_id'] = Configure::read( 'Contratinsertion.RdvAuto.thematiquerdv_id' );
				}

				if( $this->Cer93->Contratinsertion->Personne->Rendezvous->find( 'count', $querydata ) == 0 ) {
					$rendezvous = array(
						'Rendezvous' => array(
							'personne_id' => $data['Contratinsertion']['personne_id'],
							'structurereferente_id' => $data['Contratinsertion']['structurereferente_id'],
							'referent_id' => suffix( $data['Contratinsertion']['referent_id'] ),
							'objetrdv' => null,
							'commentairerdv' => null,
							'typerdv_id' => Configure::read( 'Contratinsertion.RdvAuto.typerdv_id' ),
							'statutrdv_id' => Configure::read( 'Contratinsertion.RdvAuto.statutrdv_id' ),
							'daterdv' => date( 'Y-m-d', strtotime( $created ) ),
							'heurerdv' => date( 'H:i:s', strtotime( $created ) - ( strtotime( $created ) % ( 5 * 60 ) ) ),
							'permanence_id' => null,
							'isadomicile' => '0',
						)
					);

					if( $useThematiquerdv ) {
						$rendezvous['Thematiquerdv'] = array( 'Thematiquerdv' => Configure::read( 'Contratinsertion.RdvAuto.thematiquerdv_id' ) );
					}

					$this->Cer93->Contratinsertion->Personne->Rendezvous->create( $rendezvous );
					$success = $this->Cer93->Contratinsertion->Personne->Rendezvous->save( null, array( 'atomic' => false ) ) && $success;
					if( !$success ) {
						$this->Cer93->log(
							sprintf(
								'Erreur(s) lors de l\'enregistrement automatique d\'un rendez-vous lors de la création d\'un CER (erreurs de validation: %s)',
								var_export( $this->Cer93->Contratinsertion->Personne->Rendezvous->validationErrors, true )
							),
							LOG_ERROR
						);
					}
				}
			}

			return $success;
		}
	}
?>