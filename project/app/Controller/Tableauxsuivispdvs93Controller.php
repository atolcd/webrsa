<?php
	/**
	 * Code source de la classe Tableauxsuivispdvs93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Tableauxsuivispdvs93Controller ...
	 *
	 * @package app.Controller
	 */
	class Tableauxsuivispdvs93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Tableauxsuivispdvs93';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'InsertionsBeneficiaires',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
					'tableaud1',
					'tableaud2',
					'tableau1b3',
					'tableau1b4',
					'tableau1b5',
					'tableau1b6',
				),
			),
			'WebrsaTableauxsuivispdvs93',
			'WebrsaUsers',
			'Workflowscers93',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Search.SearchForm',
			'Tableaud2',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Tableausuivipdv93',
			'Cohortetransfertpdv93',
			'WebrsaTableausuivipdv93',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(

		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(

		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'delete' => 'delete',
			'exportcsvcorpus' => 'read',
			'exportcsvdonnees' => 'read',
			'historiser' => 'create',
			'index' => 'read',
			'tableau1b3' => 'read',
			'tableau1b4' => 'read',
			'tableau1b5' => 'read',
			'tableau1b6' => 'read',
			'tableaud1' => 'read',
			'tableaud2' => 'read',
			'tableaub7' => 'read',
			'tableaub7d2typecontrat' => 'read',
			'tableaub7d2familleprofessionnelle' => 'read',
			'view' => 'read',
		);

		/**
		 *
		 * @param array $search
		 * @throws RuntimeException
		 */
		protected function _filtersReferentId( array $search, $referent_id ) {
			$intersect = array_values(
				array_intersect(
					$this->InsertionsBeneficiaires->referents(
						array(
							'type' => 'ids',
							'prefix' => false,
							'conditions' => array(
								'Referent.structurereferente_id' => array_keys( $this->WebrsaTableausuivipdv93->listePdvs() )
							)
						)
					),
					(array)$referent_id
				)
			);

			if( !isset( $intersect[0] ) ) {
				$msgstr = sprintf( 'L\'utilisateur %s n\'a pas accès au référent d\'id %d', $this->Session->read( 'Auth.User.username' ), $referent_id );
				throw new RuntimeException( $msgstr, 500 );
			}

			$search['Search']['type'] = 'referent';
			$search['Search']['communautesr_id'] = null;
			$search['Search']['structurereferente_id'] = null;
			$search['Search']['referent_id'] = $intersect[0];

			return $search;
		}

		/**
		 *
		 * @param array $search
		 * @throws RuntimeException
		 */
		protected function _filtersStructurereferenteId( array $search, $structurereferente_id ) {
			$intersect = array_values(
				array_intersect(
					$this->InsertionsBeneficiaires->structuresreferentes(
						array(
							'type' => 'ids',
							'prefix' => false,
							'conditions' => array(
								'Structurereferente.id' => array_keys( $this->WebrsaTableausuivipdv93->listePdvs() )
							)
						)
					),
					(array)$structurereferente_id
				)
			);

			if( !isset( $intersect[0] ) ) {
				$msgstr = sprintf( 'L\'utilisateur %s n\'a pas accès à la structure référente d\'id %d', $this->Session->read( 'Auth.User.username' ), $structurereferente_id );
				throw new RuntimeException( $msgstr, 500 );
			}

			$search['Search']['type'] = 'pdv';
			$search['Search']['communautesr_id'] = null;
			$search['Search']['structurereferente_id'] = $intersect[0];
			$search['Search']['referent_id'] = null;

			return $search;
		}

		/**
		 *
		 * @param array $search
		 * @return type
		 * @throws RuntimeException
		 */
		protected function _filtersStatistiquesInternes( array $search ) {
			$intersect = array_values(
				array_intersect(
					$this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'ids', 'prefix' => false ) ),
					(array)$search['Search']['structurereferente_id']
				)
			);

			if( count( $intersect ) !== count( (array)$search['Search']['structurereferente_id'] ) ) {
				$diff = array_diff(
					(array)$search['Search']['structurereferente_id'],
					$this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'ids', 'prefix' => false ) )
				);
				$msgstr = sprintf( 'L\'utilisateur %s n\'a pas accès aux structures référentes d\'ids %s', $this->Session->read( 'Auth.User.username' ), implode( ', ', $diff ) );
				throw new RuntimeException( $msgstr, 500 );
			}

			$search['Search']['type'] = 'interne';
			$search['Search']['communautesr_id'] = null;
			$search['Search']['structurereferente_id'] = null;
			$search['Search']['referent_id'] = null;
			$search['Search']['Structurereferente']['Structurereferente'] = $intersect;

			return $search;
		}

		/**
		 * Nettoyage et complétion des filtres renvoyés par le moteur de recherche,
		 * suivant le tableau et l'utilisateur connecté.
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _filters( array $search, $tableau = null ) {
			$tableau = ( $tableau === null ) ? $this->action : $tableau;
			$result = array();

			if( !empty( $search ) ) {
				// 1. Nettoyage des filtres
				$filters = (array)Hash::get( $this->WebrsaTableausuivipdv93->filters, $tableau );
				foreach( $filters as $path ) {
					$result = Hash::insert(
						$result,
						$path,
						Hash::get( $search, $path )
					);
				}

				// 2. Complétion des filtres en fonction de l'utilisateur connecté si besoin
				$remove = array();
				$type = $this->Session->read( 'Auth.User.type' );
				if( $type === 'externe_ci' ) {
					$remove = array_merge(
						$remove,
						array(
							'Search.communautesr_id',
							'Search.structurereferente_id'
						)
					);
					$result['Search']['type'] = 'referent';
					$result['Search']['referent_id'] = $this->Session->read( 'Auth.User.referent_id' );
				}
				else if( in_array( $type, array( 'externe_cpdv', 'externe_secretaire' ) ) ) {
					// Par défaut, on filtre sur la structure référente de l'utilisateur
					$remove = array_merge(
						$remove,
						array(
							'Search.communautesr_id'
						)
					);
					$result['Search']['type'] = 'pdv';
					$result['Search']['structurereferente_id'] = $this->Session->read( 'Auth.User.structurereferente_id' );

					// Si l'utilisateur choisit de filtrer sur un référent en particulier
					$referent_id = suffix( Hash::get( $search, 'Search.referent_id' ) );
					if( !empty( $referent_id ) ) {
						$result = $this->_filtersReferentId( $result, $referent_id );
					}
				}
				else if( in_array( $type, array( 'cg', 'externe_cpdvcom' ) ) ) {
					if( $type === 'externe_cpdvcom' ) {
						// Par défaut, on filtre sur la communauté de l'utilisateur
						$result['Search']['type'] = 'communaute';
						$result['Search']['communautesr_id'] = $this->Session->read( 'Auth.User.communautesr_id' );
					}
					else {
						// Par défaut, on filtre sur l'ensemble du département
						$result['Search']['type'] = 'cg';
					}

					$structurereferente_id_choice = Hash::get( $search, 'Search.structurereferente_id_choice' );
					if( !empty( $structurereferente_id_choice ) ) {
						$result = $this->_filtersStatistiquesInternes( $result );
					}
					else {
						// Si l'utilisateur choisit de filtrer sur une communauté
						$communautesr_id = suffix( Hash::get( $search, 'Search.communautesr_id' ) );
						if( $type === 'cg' && !empty( $communautesr_id ) ) {
							$result['Search']['type'] = 'communaute';
							$result['Search']['communautesr_id'] = $communautesr_id;
							$result['Search']['structurereferente_id'] = null;
							$result['Search']['referent_id'] = null;
						}
						else {
							// Si l'utilisateur choisit de filtrer sur un référent en particulier
							$referent_id = suffix( Hash::get( $search, 'Search.referent_id' ) );
							if( !empty( $referent_id ) ) {
								$result = $this->_filtersReferentId( $result, $referent_id );
							}
							else {
								// Si l'utilisateur choisit de filtrer sur un PDV en particulier
								$structurereferente_id = suffix( Hash::get( $search, 'Search.structurereferente_id' ) );
								if( !empty( $structurereferente_id ) ) {
									$result = $this->_filtersStructurereferenteId( $result, $structurereferente_id );
								}
							}
						}
					}
				}

				foreach( $remove as $path ) {
					$result = Hash::remove( $result, $path );
				}
			}

			return $result;
		}

		/**
		 * Formulaire de filtres pour le tableau de suivi D1.
		 */
		public function tableaud1() {
			$search = $this->_filters( $this->request->data );
			$this->_setOptions();
			$this->_prepareFormData( $search );

			if( !empty( $search ) ) {
				$results = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableaud1( $search );
				$this->set( compact( 'results' ) );
			}

			$this->set( 'categories', $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableaud1Categories() );
			$this->set( 'columns', $this->Tableausuivipdv93->WebrsaTableausuivipdv93->columns_d1 );
		}

		/**
		 * Formulaire de filtres pour le tableau de suivi D2.
		 */
		public function tableaud2() {
			$search = $this->_filters( $this->request->data );
			$this->_setOptions();
			$this->_prepareFormData( $search );

			if( !empty( $search ) ) {
				$results = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableaud2( $search );
				$this->set( compact( 'results' ) );
			}

			$this->set( 'categories', $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableaud2Categories() );
		}

		/**
		 * @param integer $user_structurereferente_id
		 */
		protected function _setOptions( $user_structurereferente_id = null ) {
			// FIXME: $user_structurereferente_id / filtre, etc...
			$tableau = null;
			if( in_array( $this->action, array( 'view', 'historiser' ) ) ) {
				$tableau = Hash::get( $this->request->params, 'pass.0' );
			}
			else if( in_array( $this->action, array_keys( $this->WebrsaTableausuivipdv93->tableaux ) ) ) {
				$tableau = $this->action;
			}

			$type = $this->Session->read( 'Auth.User.type' );
			// TODO: lire dans la Configuration
			$pdvs_ids = array_keys( $this->WebrsaTableausuivipdv93->listePdvs() );

			$options = $this->WebrsaTableausuivipdv93->options(
				array(
					'tableau' => $tableau,
					'structuresreferentes' => $this->InsertionsBeneficiaires->structuresreferentes(
						array(
							'type' => 'list',
							'prefix' => false,
							'conditions' => array(
								'Structurereferente.id' => $pdvs_ids
							)
						)
					),
					'referents' => $this->InsertionsBeneficiaires->referents(
						array(
							'type' => 'list',
							'prefix' => true,
							'conditions' => array(
								'Referent.structurereferente_id' => $pdvs_ids
							)
						)
					)
				)
			);
			$options['Search']['user_id'] = $this->WebrsaTableauxsuivispdvs93->photographes();
			$options['Search']['type'] = $this->Tableausuivipdv93->enum( 'type' );

			$options = Hash::merge(
				$options,
				$this->Tableausuivipdv93->enums()
			);

			// Filtrage des types de recherches suivant l'utilisateur connecté
			$type = $this->Session->read( 'Auth.User.type' );
			if( strpos( $type, 'externe_' ) === 0 ) {
				unset( $options['Tableausuivipdv93']['type']['cg'] );
			}
			if( in_array( $type, array( 'externe_cpdv', 'externe_secretaire', 'externe_ci' ) ) ) {
				unset( $options['Tableausuivipdv93']['type']['interne'], $options['Tableausuivipdv93']['type']['communaute'] );
			}
			if( $type === 'externe_ci' ) {
				unset( $options['Tableausuivipdv93']['type']['pdv'] );
			}
			$options['Search']['type'] = $options['Tableausuivipdv93']['type'];

			// TODO: à nettoyer
			$hasMode = in_array( $type, array( 'cg', 'externe_cpdvcom' ) );
			$hasCommunautessrs = $type === 'cg';
			$hasStructuresreferentes = empty( $user_structurereferente_id ) || count( $user_structurereferente_id ) > 1;
			$hasReferents = $type === 'externe_ci';
			$this->set( compact( 'options', 'hasMode', 'hasCommunautessrs', 'hasStructuresreferentes', 'hasReferents' ) );
		}

		/**
		 * Retourne un array contenant les clés communautesr_id, structurereferente_id
		 * et referent_id pas à NULL  lorsque l'on doit ajouter des conditions aux requêtes
		 * en fonction de l'utilisateur connecté (chef communautaire, CPDV,
		 * secrétaire ou chargé d'insertion).
		 *
		 * @return array
		 */
		protected function _getConditionsUtilisateur() {
			$conditions = array(
				'communautesr_id' => null,
				'structurereferente_id' => null,
				'referent_id' => null
			);

			$type = $this->Session->read( 'Auth.User.type' );

			if( $type === 'externe_cpdvcom' ) {
				$conditions['communautesr_id'] = $this->Session->read( 'Auth.User.communautesr_id' );
			}
			// Si l'utilisateur connecté est un référent
			else if( $type === 'externe_ci' ) {
				$user_referent_id = $this->Session->read( 'Auth.User.referent_id' );
				$conditions['referent_id'] = $user_referent_id;
			}
			// Si l'utilisateur connecté est limité à un PDV
			else if( $type !== 'cg' ) {
				$user_structurereferente_id = $this->Workflowscers93->getUserStructurereferenteId( false );
				$conditions['structurereferente_id'] = $user_structurereferente_id;
			}

			return $conditions;
		}

		/**
		 * Prépare les données du formulaire de recherche en fonction de l'URL
		 * et de l'utilisateur connecté pour le premier appel à la page.
		 *
		 * @param array $search
		 */
		protected function _prepareFormData( array $search ) {
			// Si le formulaire n'a pas été envoyé
			if( empty( $search ) ) {
				// Si c'est une méthode d'un des moteurs
				if( in_array( $this->request->action, array_keys( $this->WebrsaTableausuivipdv93->tableaux ) ) ) {
					$configureKey = "{$this->name}.{$this->request->action}.defaults";
					$this->request->data = (array)Configure::read( $configureKey );
				}

				$this->request->data['Search']['mode'] = 'fse';
			}
		}

		/**
		 * Méthode utilitaire permettant d'ajouter des filtres automatiquement
		 * concernant la structure référente (CPDV, secrétaire) ou le référent
		 * connecté (chargé d'insertion).
		 * De plus, les options seront envoyées à la vue, suivant le type
		 * d'utilisateur connecté.
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _applyStructurereferente( array $search ) {
			$conditions = $this->_getConditionsUtilisateur();

			if( !empty( $search ) ) {
				if( !empty( $conditions['communautesr_id'] ) ) {
					$search = Hash::insert( $search, 'Search.communautesr_id', $conditions['communautesr_id'] );
				}
				if( !empty( $conditions['structurereferente_id'] ) ) {
					$search = Hash::insert( $search, 'Search.structurereferente_id', $conditions['structurereferente_id'] );
				}
				if( !empty( $conditions['referent_id'] ) ) {
					$search = Hash::insert( $search, 'Search.referent_id', $conditions['referent_id'] );
				}
			}

			$this->_setOptions( $conditions['structurereferente_id'] );
			$this->_prepareFormData( $search );

			return $search;
		}

		/**
		 * Formulaire de filtres pour le tableau de suivi 1 B3.
		 */
		public function tableau1b3() {
			$search = $this->_filters( $this->request->data );
			$this->_setOptions();
			$this->_prepareFormData( $search );

			if( !empty( $search ) ) {
				$this->set( 'results', $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableau1b3( $search ) );
			}
		}

		/**
		 * Formulaire de filtres pour le tableau de suivi 1 B4.
		 */
		public function tableau1b4() {
			$search = $this->_filters( $this->request->data );
			$this->_setOptions();
			$this->_prepareFormData( $search );

			if( !empty( $search ) ) {
				$this->set( 'results', $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableau1b4( $search ) );
			}
		}

		/**
		 * Formulaire de filtres pour le tableau de suivi 1 B5.
		 */
		public function tableau1b5() {
			$search = $this->_filters( $this->request->data );
			$this->_setOptions();
			$this->_prepareFormData( $search );

			if( !empty( $search ) ) {
				$this->set( 'results', $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableau1b5( $search ) );
			}
		}

		/**
		 * Formulaire de filtres pour le tableau de suivi 1 B6.
		 */
		public function tableau1b6() {
			$search = $this->_filters( $this->request->data );
			$this->_setOptions();
			$this->_prepareFormData( $search );

			if( !empty( $search ) ) {
				$this->set( 'annee', $search['Search']['annee'] );
				$this->set( 'anneeProd', date("d/m/Y" , strtotime(Configure::read('Date.production')[0] ) ) );
				$this->set( 'anneeProdMoinsUnJour', date("d/m/Y" , strtotime(Configure::read('Date.production')[0] . "-1 day" ) ) );
				$this->set( 'results', $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableau1b6( $search ) );
			}
		}

		/**
		 *
		 * @param string $action
		 * @param integer $id
		 * @throws NotFoundException
		 */
		public function exportcsvcorpus( $action, $id ) {
			if( !in_array( $action, array_keys( $this->WebrsaTableausuivipdv93->tableaux ) ) ) {
				throw new NotFoundException();
			}

			$query = array(
				'conditions' => array(
					'Tableausuivipdv93.id' => $id,
					'Tableausuivipdv93.name' => $action,
				),
				'contain' => array(
					'Communautesr',
					'Pdv',
					'Referent' => array(
						'fields' => array(
							$this->Tableausuivipdv93->Referent->sqVirtualField( 'nom_complet' )
						)
					)
				),
			);

			$tableausuivipdv93 = $this->Tableausuivipdv93->find( 'first', $query );

			if( empty( $tableausuivipdv93 ) ) {
				throw new NotFoundException();
			}

			$this->WebrsaTableauxsuivispdvs93->checkAccess( $tableausuivipdv93 );

			// Récupération des données du corpus
			$query = array(
				'conditions' => array(
					'Corpuspdv93.tableausuivipdv93_id' => $id
				),
				'contain' => false
			);

			$corpuspdv93 = $this->Tableausuivipdv93->Corpuspdv93->find( 'first', $query );

			// Nouvelle façon de faire, avec la table corpuspdvs93
			if( !empty( $corpuspdv93 )) {
				// TODO: le faire dans le modèle beforeSave / afterFind ?
				$fields = json_decode( $corpuspdv93['Corpuspdv93']['fields'], true );
				$results = json_decode( $corpuspdv93['Corpuspdv93']['results'], true );
				$options = json_decode( $corpuspdv93['Corpuspdv93']['options'], true );
			}
			// Ancienne façon de faire, tant que l'on n'a pas tout mis à jour
			else {
				if( $action === 'tableaud1' ) {
					$query = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->qdExportcsvCorpusd1( $id );
				}
				else if( $action === 'tableaud2' ) {
					$query = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->qdExportcsvCorpusd2( $id );
				}
				else if( $action === 'tableau1b3' ) {
					$query = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->qdExportcsvCorpus1b3( $id );
				}
				else if( $action === 'tableau1b4' ) {
					$query = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->qdExportcsvCorpus1b4( $id );
				}
				else if( $action === 'tableau1b5' ) {
					$query = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->qdExportcsvCorpus1b5( $id );
				}
				else if( $action === 'tableau1b6' ) {
					$query = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->qdExportcsvCorpus1b6( $id );
				}

				if( !in_array( $action, array( 'tableaud1', 'tableaud2' ) )  ) {
					$query = ConfigurableQueryFields::getFieldsByKeys(
						"{$this->name}.{$action}.{$this->request->action}",
						$query
					);
				}

				$this->Tableausuivipdv93->forceVirtualFields = true;
				$results = $this->Tableausuivipdv93->find( 'all', $query );

				$options = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->getOptions( $action );
			}

			$csvfile = $this->_csvFileName( $this->action, $tableausuivipdv93 );
			$search = unserialize( $tableausuivipdv93['Tableausuivipdv93']['search'] );

			//détail du resultat des corpus afin de récupérer les infos bénéficiaires
			switch($action) {
				case 'tableaub7' :
					$results = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->resultsCorpusTableaub7($search);
				break;
				case 'tableaub7d2typecontrat' :
					$results = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->resultsCorpusTableaub7d2TypeContrat($search);
				break;
				case 'tableaub7d2familleprofessionnelle' :
					$results = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->resultsCorpusTableaub7d2FamilleProfessionnelle($search);
				break;
			}

			$this->set( compact( 'results', 'options', 'csvfile', 'action', 'search' ) );
			$this->layout = null;

			switch($action) {
				case 'tableaud1' :
				case 'tableaud2' :
					$this->view = 'exportcsvcorpus_d1d2';
				break;
				default :
					$this->view = 'exportcsvcorpus';
				break;
			}
		}

		/**
		 * Retourne le nom de fichier utilisé pour un export CSV.
		 *
		 * @param string $typeExport
		 * @param array $tableausuivipdv93
		 * @return string
		 */
		protected function _csvFileName( $typeExport, $tableausuivipdv93 ) {
			$type = Hash::get( $tableausuivipdv93, 'Tableausuivipdv93.type' );

			$communautesr = Hash::get( $tableausuivipdv93, 'Communautesr.name' );
			$communautesr = preg_replace( '/[^a-z0-9\-_]+/i', '_', $communautesr );
			$communautesr = trim( $communautesr, '_' );

			$structurereferente = Hash::get( $tableausuivipdv93, 'Pdv.lib_struc' );
			$structurereferente = preg_replace( '/[^a-z0-9\-_]+/i', '_', $structurereferente );
			$structurereferente = trim( $structurereferente, '_' );

			$referent = Hash::get( $tableausuivipdv93, 'Referent.nom_complet' );
			$referent = preg_replace( '/[^a-z0-9\-_]+/i', '_', $referent );
			$referent = trim( $referent, '_' );

			return implode(
				'-',
				Hash::filter(
					array(
						$typeExport,
						$tableausuivipdv93['Tableausuivipdv93']['name'],
						$type,
						$communautesr,
						$structurereferente,
						$referent,
						$tableausuivipdv93['Tableausuivipdv93']['annee'],
						date( 'Ymd-His' )
					)
				)
			).'.csv';
		}

		/**
		 * Export des données d'un tableau D1 ou D2 au format CSV.
		 *
		 * @fixme 1B4, 1B5
		 *
		 * @param string $action
		 * @param integer $id
		 * @throws NotFoundException
		 */
		public function exportcsvdonnees( $action, $id ) {
			if( !in_array( $action, array_keys( $this->WebrsaTableausuivipdv93->tableaux ) ) ) {
				throw new NotFoundException();
			}

			$query = array(
				'conditions' => array(
					'Tableausuivipdv93.id' => $id,
					'Tableausuivipdv93.name' => $action,
				),
				'contain' => array(
					'Communautesr',
					'Pdv',
					'Referent' => array(
						'fields' => array(
							$this->Tableausuivipdv93->Referent->sqVirtualField( 'nom_complet' )
						)
					)
				),
			);

			$tableausuivipdv93 = $this->Tableausuivipdv93->find( 'first', $query );

			if( empty( $tableausuivipdv93 ) ) {
				throw new NotFoundException();
			}

			$this->WebrsaTableauxsuivispdvs93->checkAccess( $tableausuivipdv93 );

			$results = unserialize( $tableausuivipdv93['Tableausuivipdv93']['results'] );

			if( $action === 'tableaud1' ) {
				$categories = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableaud1Categories();
				$this->set( 'columns', $this->Tableausuivipdv93->WebrsaTableausuivipdv93->columns_d1 );
			}
			else if( $action === 'tableaud2' ) {
				$categories = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableaud2Categories();
			}
			else if( $action === 'tableau1b3' ) {
				$categories = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->problematiques();
			}

			$csvfile = $this->_csvFileName( $this->action, $tableausuivipdv93 );

			$this->set( compact( 'results', 'action', 'categories', 'tableausuivipdv93', 'csvfile' ) );

			$this->layout = null; // FIXME
			$this->render( "exportcsvdonnees_{$action}" );
		}

		/**
		 * Historisation d'un tableau de résultat.
		 *
		 * @param string $action
		 */
		public function historiser( $action ) {
			$search = $this->_filters(
				Hash::expand( $this->request->params['named'] ),
				Hash::get( $this->request->params, 'pass.0' )
			);
			$this->_setOptions();

			$this->Tableausuivipdv93->begin();
			$success = $this->WebrsaTableausuivipdv93->historiser(
				$action,
				$search,
				$this->Session->read( 'Auth.User.id' )
			);

			if( $success ) {
				$this->Tableausuivipdv93->commit();
				$this->Flash->success( __( 'Save->success' ) );
				$this->setAction( 'view', $this->Tableausuivipdv93->id );
			}
			else {
				$this->Tableausuivipdv93->rollback();
				$this->Flash->error( __( 'Save->error' ) );
				$this->redirect( $this->request->referer() );
			}
		}

		/**
		 * Accès à la liste des résultats historisés.
		 *
		 * @param string $action
		 */
		public function index( $action = null ) {
			$search = $this->request->data;
			$this->_setOptions();
			$this->_prepareFormData( $search );

			if( !empty( $search ) ) {
				$query = $this->WebrsaTableausuivipdv93->searchQuery();
				$query = $this->WebrsaTableausuivipdv93->searchConditions( $query, $search );

				// Ajout de filtres en fonction de l'utilisateur connecté
				$or = array(
					'Pdv.id' => array_keys(
						$this->InsertionsBeneficiaires->structuresreferentes(
							array(
								'type' => 'list',
								'prefix' => false,
								'conditions' => array(
									'Structurereferente.id' => array_keys( $this->WebrsaTableausuivipdv93->listePdvs() )
								)
							)
						)
					),
					'Referent.id' => array_keys(
						$this->InsertionsBeneficiaires->referents(
							array(
								'type' => 'list',
								'prefix' => false,
								'conditions' => array(
									'Referent.structurereferente_id' => array_keys( $this->WebrsaTableausuivipdv93->listePdvs() )
								)
							)
						)
					),
					'Photographe.id' => $this->Session->read( 'Auth.User.id' )
				);

				$type = $this->Session->read( 'Auth.User.type' );
				if( $type === 'cg' ) {
					$or[] = array( 'Pdv.id IS NULL' );
					$or[] = array( 'Referent.id IS NULL' );
					$or[] = array( 'Communautesr.id IS NULL' );
				}
				else if( $type === 'externe_cpdvcom' ) {
					$or['Communautesr.id'] = $this->Session->read( 'Auth.User.communautesr_id' );
				}
				else if( in_array( $type, array( 'externe_cpdv', 'externe_secretaire' ) ) ) {
					$query['conditions'][] = array(
						'Pdv.id' => $this->Session->read( 'Auth.User.structurereferente_id' )
					);
				}
				else if( $type === 'externe_ci' ) {
					$query['conditions'][] = array(
						'Referent.id' => $this->Session->read( 'Auth.User.referent_id' )
					);
				}

				$query['conditions'][] = array( 'OR' => $or );

				// Limitation des résultats en fonction de l'utilisateur connecté
				$query['conditions'][] = array(
					'OR' => array(
						'Tableausuivipdv93.user_id IS NULL',
						'Tableausuivipdv93.user_id' => $this->WebrsaTableauxsuivispdvs93->photographesIds()
					)
				);

				// TODO: en paramètre de la recherche + version
				if( !empty( $action ) ) {
					$query['conditions']['Tableausuivipdv93.name'] = $action;
				}

				$this->paginate = array( 'Tableausuivipdv93' => $query + array( 'limit' => 10 ) );
				$results = $this->paginate( 'Tableausuivipdv93', array(), array(), false );
				$this->set( compact( 'results' ) );
			}
		}

		/**
		 * Accès à une version historisée d'un tableau.
		 *
		 * TODO: enlever le lien historiser et ajouter les détails de la capture
		 *
		 * @param string $action
		 *
		 * @throws Error403Exception
		 * @throws NotFoundException
		 */
		public function view( $id ) {
			$query = array(
				'conditions' => array(
					'Tableausuivipdv93.id' => $id
				),
				'contain' => array( 'Structurereferente.id' )
			);

			$tableausuivipdv93 = $this->Tableausuivipdv93->find( 'first', $query );

			if( empty( $tableausuivipdv93 ) ) {
				throw new NotFoundException();
			}

			$this->WebrsaTableauxsuivispdvs93->checkAccess( $tableausuivipdv93 );

			if( in_array( $tableausuivipdv93['Tableausuivipdv93']['name'], array( 'tableaud1', 'tableaud2' ) ) ) {
				$method = $tableausuivipdv93['Tableausuivipdv93']['name'].'Categories';
				$this->set( 'categories', $this->Tableausuivipdv93->WebrsaTableausuivipdv93->{$method}() );
				if( $tableausuivipdv93['Tableausuivipdv93']['name'] == 'tableaud1' ) {
					$this->set( 'columns', $this->Tableausuivipdv93->WebrsaTableausuivipdv93->columns_d1 );
				}
			}

			// Préparation des données du formulaire
			$this->request->data = unserialize( $tableausuivipdv93['Tableausuivipdv93']['search'] );
			//FIXME: pas de type pour le passif....
			// INFO: pour le passif... -> faire un shell ?
			$this->request->data['Search']['type'] = $tableausuivipdv93['Tableausuivipdv93']['type'];
			$this->request->data['Search']['tableau'] = $tableausuivipdv93['Tableausuivipdv93']['name'];
			if( $this->request->data['Search']['type'] === 'interne' ) {
				$this->request->data['Search']['structurereferente_id_choice'] = true;
				$this->request->data['Search']['structurereferente_id'] = Hash::extract( $tableausuivipdv93, 'Structurereferente.{n}.id' );
			}

			// On préfixe l'id du référent avec l'id de sa structure si ce n'est pas déjà fait
			$referent_id = Hash::get( $this->request->data, 'Search.referent_id' );
			if( !empty( $referent_id ) && strpos( $referent_id, '_' ) === false ) {
				$query = array(
					'fields' => array( 'Referent.structurereferente_id' ),
					'contain' => false,
					'conditions' => array( 'Referent.id' => $referent_id )
				);
				$referent = $this->Tableausuivipdv93->Referent->find( 'first', $query );
				$structurereferente_id = Hash::get( $referent, 'Referent.structurereferente_id' );
				$this->request->data['Search']['structurereferente_id'] = $structurereferente_id;
				$this->request->data['Search']['referent_id'] = "{$structurereferente_id}_{$referent_id}";
			}

			$results = unserialize( $tableausuivipdv93['Tableausuivipdv93']['results'] );
			$this->_setOptions();
			$this->set( compact( 'results', 'tableausuivipdv93', 'id' ) );

			// Pour les tableaux 1B4 et 1B5, il existe plusieurs versions
			$name = $tableausuivipdv93['Tableausuivipdv93']['name'];
			$version = $tableausuivipdv93['Tableausuivipdv93']['version'];
			// Par défaut, le nom de la vue est le nom du tableau
			$viewName = $name;

			if( in_array( $name, array( 'tableau1b4', 'tableau1b5' ) ) ) {
				// Entre la version 2.5.1 et la version 2.7.0
				if( version_compare( $version, '2.7', '<') ) {
					$viewName = $name.'_2.5.1';
				}
				// Pour la tableau 1B5, entre la version 2.7.0 et la version 2.7.06
				else if( $name === 'tableau1b5' && version_compare( $version, '2.7.06', '<') ) {
					$viewName = $name.'_2.7.0';
				}
			}

			$this->render( $viewName );
		}

		/**
		 * @param integer $id
		 *
		 * @throws NotFoundException
		 */
		public function delete( $id ) {
			$query = array(
				'fields' => array(
					'Tableausuivipdv93.id',
					'Tableausuivipdv93.user_id'
				),
				'conditions' => array(
					'Tableausuivipdv93.id' => $id
				)
			);

			$record = $this->Tableausuivipdv93->find( 'first', $query );

			if( empty( $record ) ) {
				throw new NotFoundException();
			}

			$this->WebrsaTableauxsuivispdvs93->checkAccess( $record );

			$this->Tableausuivipdv93->begin();

			if( $this->Tableausuivipdv93->delete( $id ) ) {
				$this->Tableausuivipdv93->commit();
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Tableausuivipdv93->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->redirect( $this->request->referer() );
		}

		/**
		 * Tableau B7
		 */
		public function tableaub7() {
			$search = $this->_filters( $this->request->data );

			$this->_setOptions();
			$this->_prepareFormData( $search );

			if( !empty( $search ) ) {
				//$results = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableaub7( $search );
				$this->Tableausuivipdv93->WebrsaTableausuivipdv93->userConnected = $this->Session->read( 'Auth.User' );
				$results = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableaub7( $this->request->data);

				$this->set( compact( 'results' ) );
			}
		}

		/**
		 * Tableau B7 + D2 par type de contrat
		 */
		public function tableaub7d2typecontrat() {
			$search = $this->_filters( $this->request->data );

			$this->_setOptions();
			$this->_prepareFormData( $search );

			if( !empty( $search ) ) {
				//$results = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableaub7d2typecontrat( $search );
				$this->Tableausuivipdv93->WebrsaTableausuivipdv93->userConnected = $this->Session->read( 'Auth.User' );
				$results = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableaub7d2typecontrat( $this->request->data);

				$this->set( compact( 'results' ) );
			}
		}

		/**
		 * Tableau B7 + D2 par famille professionnelle
		 */
		public function tableaub7d2familleprofessionnelle() {
			$search = $this->_filters( $this->request->data );

			$this->_setOptions();
			$this->_prepareFormData( $search );

			if( !empty( $search ) ) {
				//$results = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableaub7d2familleprofessionnelle( $search );
				$this->Tableausuivipdv93->WebrsaTableausuivipdv93->userConnected = $this->Session->read( 'Auth.User' );
				$results = $this->Tableausuivipdv93->WebrsaTableausuivipdv93->tableaub7d2familleprofessionnelle( $this->request->data );

				$this->set( compact( 'results' ) );
			}
		}
	}
?>
