<?php

	/**
	 * Code source de la classe Traitementspcgs66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'WebrsaAccessTraitementspcgs66', 'Utility' );

	/**
	 * La classe Traitementspcgs66Controller (CG 66).
	 *
	 * @package app.Controller
	 */
	class Traitementspcgs66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Traitementspcgs66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'DossiersMenus',
			'Fileuploader',
			'Gedooo.Gedooo',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array( 'search' )
			),
			'WebrsaAccesses',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Autrepiecetraitementpcg66',
			'Cake1xLegacy.Ajax',
			'Csv',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Locale',
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Traitementpcg66',
			'Dossierpcg66',
			'Option',
			'Personnepcg66',
			'WebrsaTraitementpcg66',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Traitementspcgs66:edit',
			'view' => 'Traitementspcgs66:index',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajaxfiledelete',
			'ajaxfileupload',
			'ajaxpiece',
			'ajaxpiece_cohorte',
			'download',
			'fileview',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'ajaxpiece' => 'read',
			'ajaxpiece_cohorte' => 'update',
			'cancel' => 'update',
			'clore' => 'update',
			'delete' => 'delete',
			'deverseDO' => 'update',
			'download' => 'read',
			'edit' => 'update',
			'envoiCourrier' => 'read',
			'exportcsv' => 'read',
			'fileview' => 'read',
			'index' => 'read',
			'printFicheCalcul' => 'read',
			'printModeleCourrier' => 'read',
			'reverseDO' => 'update',
			'search' => 'read',
			'switch_imprimer' => 'read',
			'view' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			$options = $this->Traitementpcg66->enums();

			$options[$this->modelClass]['descriptionpdo_id'] = $this->Traitementpcg66->Descriptionpdo->find('list');
			$options[$this->modelClass]['situationpdo_id'] = $this->Traitementpcg66->Situationpdo->find('list');
			$options[$this->modelClass]['listeDescription'] = $this->Traitementpcg66->Descriptionpdo->find('all', array('contain' => false));
			$options[$this->modelClass]['compofoyerpcg66_id'] = $this->Traitementpcg66->Compofoyerpcg66->find('list');

			$this->set(compact('options'));

			$descriptionspdos = $this->Traitementpcg66->Descriptionpdo->find(
					'list', array(
				'fields' => array(
					'Descriptionpdo.id',
					'Descriptionpdo.nbmoisecheance'
				),
				'contain' => false
					)
			);
			$this->set(compact('descriptionspdos'));

			$this->set('typescourrierspcgs66', $this->Traitementpcg66->Typecourrierpcg66->find(
					'list', array(
						'fields' => array(
							'Typecourrierpcg66.name'
						),
						'conditions' => array(
							'Typecourrierpcg66.isactif' => '1'
						)
					)
				)
			);

			// Liste des service instructeurs à contacter pour les traitements PCGs insertion
			// La liste est : AFIJ, ADRH, MLJ (=Organisme agréé) + MSPs
			$this->set('services', $this->Traitementpcg66->Serviceinstructeur->listOptions(true));
		}

		public function ajaxpiece_cohorte( $i ) {
			$this->request->data = $this->request->data['Cohorte'][$i];
			return $this->ajaxpiece();
		}

		/**
		 * Ajax pour les pièces liées à un type de courrier
		 */
		public function ajaxpiece() { // FIXME
			$datas = array();
			foreach (array('Modeletraitementpcg66', 'Piecemodeletypecourrierpcg66') as $M) {
				if (isset($this->request->data[$M])) {
					$datas[$M] = $this->request->data[$M];
				}
			}

			$traitementpcg66_id = Set::extract($this->request->data, 'Traitementpcg66.id');
			$typecourrierpcg66_id = Set::extract($this->request->data, 'Traitementpcg66.typecourrierpcg66_id');

			// Liste des modèles de courrier lié au type de courrier
			if (!empty($typecourrierpcg66_id)) {
				$modeletypecourrierpcg66 = $this->Traitementpcg66->Typecourrierpcg66->Modeletypecourrierpcg66->find(
					'list', array(
						'conditions' => array(
							'Modeletypecourrierpcg66.typecourrierpcg66_id' => $typecourrierpcg66_id,
							'Modeletypecourrierpcg66.isactif' => '1'
						),
						'fields' => array('Modeletypecourrierpcg66.id', 'Modeletypecourrierpcg66.name'),
						'contain' => false
					)
				);

				$modeletypecourrierpcg66avecmontant = $this->Traitementpcg66->Typecourrierpcg66->Modeletypecourrierpcg66->find(
					'list', array(
						'conditions' => array(
							'Modeletypecourrierpcg66.typecourrierpcg66_id' => $typecourrierpcg66_id,
							'Modeletypecourrierpcg66.ismontant' => '1',
							'Modeletypecourrierpcg66.isactif' => '1'
						),
						'fields' => array('Modeletypecourrierpcg66.id'),
						'contain' => false
					)
				);

				$modeletypecourrierpcg66avecDates = $this->Traitementpcg66->Typecourrierpcg66->Modeletypecourrierpcg66->find(
					'list', array(
						'conditions' => array(
							'Modeletypecourrierpcg66.typecourrierpcg66_id' => $typecourrierpcg66_id,
							'Modeletypecourrierpcg66.isdates' => '1',
							'Modeletypecourrierpcg66.isactif' => '1'
						),
						'fields' => array('Modeletypecourrierpcg66.id'),
						'contain' => false
					)
				);
			}

			// Liste des pièces liées aux modèles de courrier
			$listepieces = array();
			if (!empty($modeletypecourrierpcg66)) {
				foreach ($modeletypecourrierpcg66 as $i => $value) {
					$listepieces[$i] = $this->Traitementpcg66->Typecourrierpcg66->Modeletypecourrierpcg66->Piecemodeletypecourrierpcg66->find(
						'list', array(
							'conditions' => array(
								'Piecemodeletypecourrierpcg66.modeletypecourrierpcg66_id' => $i,
								'Piecemodeletypecourrierpcg66.isactif' => '1'
							),
							'contain' => false
						)
					);

					$listePiecesWithAutre[$i] = $this->Traitementpcg66->Typecourrierpcg66->Modeletypecourrierpcg66->Piecemodeletypecourrierpcg66->find(
						'list', array(
							'conditions' => array(
								'Piecemodeletypecourrierpcg66.modeletypecourrierpcg66_id' => $i,
								'Piecemodeletypecourrierpcg66.isactif' => '1'
							),
							'fields' => array('Piecemodeletypecourrierpcg66.id', 'Piecemodeletypecourrierpcg66.isautrepiece'),
							'contain' => false
						)
					);

	// 					debug( $listePiecesWithAutre[$i] );
				}
			}
			$this->set(compact('listepieces', 'listePiecesWithAutre'));

			if (!empty($traitementpcg66_id) && !isset($this->request->data['Piecemodeletypecourrierpcg66'])) {
				$datas = $this->Traitementpcg66->Modeletraitementpcg66->find(
					'first', array(
						'conditions' => array(
							'Modeletraitementpcg66.traitementpcg66_id' => $traitementpcg66_id
						),
						'contain' => array(
							'Piecemodeletypecourrierpcg66'
						)
					)
				);

				$this->request->data = Set::merge($this->request->data, $datas);
			}

			$this->set(compact('modeletypecourrierpcg66', 'modeletypecourrierpcg66avecmontant', 'modeletypecourrierpcg66avecDates'));
			$this->render('ajaxpiece', 'ajax');
		}

		/**
		 * http://valums.com/ajax-upload/
		 * http://doc.ubuntu-fr.org/modules_php
		 * increase post_max_size and upload_max_filesize to 10M
		 * debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
		 */
		public function ajaxfileupload() {
			$this->Fileuploader->ajaxfileupload();
		}

		/**
		 * http://valums.com/ajax-upload/
		 * http://doc.ubuntu-fr.org/modules_php
		 * increase post_max_size and upload_max_filesize to 10M
		 * debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
		 * FIXME: traiter les valeurs de retour
		 */
		public function ajaxfiledelete() {
			$this->Fileuploader->ajaxfiledelete();
		}

		/**
		 * Fonction permettant de visualiser les fichiers chargés dans la vue avant leur envoi sur le serveur
		 *
		 * @param type $id
		 */
		public function fileview($id) {
			$this->Fileuploader->fileview($id);
		}

		/**
		 * Téléchargement des fichiers préalablement associés à un traitement donné
		 *
		 * @param type $fichiermodule_id
		 */
		public function download($fichiermodule_id) {
			$this->assert(!empty($fichiermodule_id), 'error404');
			$this->Fileuploader->download($fichiermodule_id);
		}

		/**
		 *
		 * @param integer $personne_id
		 * @param integer $dossierpcg66_id
		 */
		public function index($personne_id = null, $dossierpcg66_id = null) {
			$this->assert(valid_int($personne_id), 'error404');

			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id)));

			// Anti Undefined variable
			$this->set('personnepcg66_id', null);
			$this->set('ajoutPossible', null);

			// Récupération du nom de l'allocataire
			$personne = $this->Traitementpcg66->Personnepcg66->Personne->find(
					'first', array(
				'fields' => array(
					$this->Traitementpcg66->Personnepcg66->Personne->sqVirtualField('nom_complet')
				),
				'conditions' => array(
					'Personne.id' => $personne_id
				),
				'contain' => false
					)
			);
			$nompersonne = Set::classicExtract($personne, 'Personne.nom_complet');

			$this->set(compact('nompersonne'));
			if (!empty($this->request->data)) {
				$dossierpcgId = $this->request->data['Search']['Personnepcg66']['dossierpcg66_id'];
				if (!empty($dossierpcgId)) {
					$this->redirect(array('controller' => 'traitementspcgs66', 'action' => 'index', $personne_id, $dossierpcgId));
				}
			}
			$this->set('dossierpcgId', $dossierpcg66_id);

			//Formulaire de recherche pour trouver l'historique de tous les dossiers PCG d'une personne
			$queryData = $this->WebrsaTraitementpcg66->completeVirtualFieldsForAccess(
				array(
					'fields' => array(
						'Situationpdo.libelle',
						'Traitementpcg66.id',
						//'Traitementpcg66.descriptionpdo_id',
						'Descriptionpdo.name',
						'Traitementpcg66.datedepart',
						'Traitementpcg66.datereception',
						'Traitementpcg66.daterevision',
						'Traitementpcg66.dateecheance',
						'Traitementpcg66.typetraitement',
						'Traitementpcg66.dtdebutperiode',
						'Traitementpcg66.datefinperiode',
						'Traitementpcg66.typetraitement',
						'Traitementpcg66.imprimer',
						'Traitementpcg66.dateenvoicourrier',
						'Traitementpcg66.etattraitementpcg',
						'Traitementpcg66.reversedo',
						'Traitementpcg66.clos',
						'Traitementpcg66.annule',
						'Traitementpcg66.motifannulation',
						'Traitementpcg66.created',
					),
					'joins' => array(
						$this->Traitementpcg66->join('Personnepcg66', array('type' => 'INNER')),
						$this->Traitementpcg66->join('Situationpdo', array('type' => 'LEFT OUTER')),
						$this->Traitementpcg66->join('Descriptionpdo', array('type' => 'LEFT OUTER')),
					),
					'contain' => false,
					'conditions' => array(
						'Personnepcg66.personne_id' => $personne_id,
						'Personnepcg66.dossierpcg66_id' => $dossierpcg66_id
					),
					'order' => array(
						'Traitementpcg66.created DESC',
						'Traitementpcg66.id DESC'
					)
				)
			);

			if (!empty($dossierpcg66_id)) {
				$actionsParams = WebrsaAccessTraitementspcgs66::getParamsList();
				$paramsAccess = $this->WebrsaTraitementpcg66->getParamsForAccess($dossierpcg66_id, $actionsParams);
				$this->set('ajoutPossible', $paramsAccess['ajoutPossible']);

				$this->paginate = array('Traitementpcg66' => $queryData);
				$listeTraitements = WebrsaAccessTraitementspcgs66::accesses(
					$this->paginate($this->Traitementpcg66),
					$paramsAccess
				);
				$this->set(compact('listeTraitements'));

				//Liste des liens entre un dossier et un allocataire
				$personnespcgs66 = $this->Traitementpcg66->Personnepcg66->find(
						'all', array(
					'fields' => array('id', 'dossierpcg66_id'),
					'conditions' => array(
						'Personnepcg66.personne_id' => $personne_id,
						'Personnepcg66.dossierpcg66_id' => $dossierpcg66_id
					),
					'contain' => false
						)
				);

				//On récupère les Ids de la personnePCG 66 liée au dossier PCG
				$personnespcgs66s_ids = (array) Set::extract($personnespcgs66, '{n}.Personnepcg66.id');
				foreach ($personnespcgs66s_ids as $value) {
					$personnepcg66_id = $value;
				}
				$this->set('personnepcg66_id', $personnepcg66_id);

				foreach ($personnespcgs66 as $personnepcg66) {
					$personnepcg66_id = Set::classicExtract($personnepcg66, 'Personnepcg66.id');

					$dossierpcg66_id = Set::classicExtract($personnepcg66, 'Personnepcg66.dossierpcg66_id');
					$this->set('dossierpcg66_id', $dossierpcg66_id);

					//Recherche des personnes liées au dossier
					$qd_personnepcg66 = array(
						'conditions' => array(
							'Personnepcg66.id' => $personnepcg66_id
						),
						'fields' => null,
						'order' => null,
						'recursive' => -1
					);
					$personnepcg66 = $this->Traitementpcg66->Personnepcg66->find('first', $qd_personnepcg66);

					$this->set('personnepcg66', $personnepcg66);
				}
			}

			$personnespcgs66 = $this->Traitementpcg66->Personnepcg66->find(
					'all', array(
				'fields' => array(
					'Personnepcg66.dossierpcg66_id'
				),
				'conditions' => array(
					'Personnepcg66.personne_id' => $personne_id
				),
				'contain' => false
					)
			);

			$listDossierspcgs66 = array();
			foreach ($personnespcgs66 as $personnepcg66) {
				$listDossierspcgs66[] = $personnepcg66['Personnepcg66']['dossierpcg66_id'];
			}

			if (!empty($listDossierspcgs66)) {
				$query = array(
					'fields' => array(
						'Dossierpcg66.id',
						'Dossierpcg66.datereceptionpdo',
						'Typepdo.libelle',
						'User.nom_complet'
					),
					'joins' => array(
						$this->Traitementpcg66->Personnepcg66->Dossierpcg66->join( 'Typepdo', array( 'type' => 'INNER' ) ),
						$this->Traitementpcg66->Personnepcg66->Dossierpcg66->join( 'User', array( 'type' => 'LEFT OUTER' ) )

					),
					'contain' => false,
					'conditions' => array(
						'Dossierpcg66.id' => $listDossierspcgs66
					)
				);
				$this->Traitementpcg66->Personnepcg66->Dossierpcg66->forceVirtualFields = true;
				$dossierspcgs66 = $this->Traitementpcg66->Personnepcg66->Dossierpcg66->find( 'all', $query );
			} else {
				$dossierspcgs66 = array();
			}

			$searchOptions['Personnepcg66']['dossierpcg66_id'] = array();
			foreach ($dossierspcgs66 as $dossierpcg66) {
				$searchOptions['Personnepcg66']['dossierpcg66_id'][$dossierpcg66['Dossierpcg66']['id']] = $dossierpcg66['Typepdo']['libelle'] . ' (' . date_short($dossierpcg66['Dossierpcg66']['datereceptionpdo']) . ')' . ' géré par ' . $dossierpcg66['User']['nom_complet'];
			}

			$options = $this->Traitementpcg66->enums();

			$this->set( compact( 'options', 'searchOptions', 'personne_id' ) );
			$this->set('urlmenu', '/traitementspcgs66/index/' . $personne_id);
		}

		/**
		 *
		 */
		public function add($personnepcg66_id) {
			$this->WebrsaAccesses->setMainModel('Personnepcg66')->check($personnepcg66_id);
			$args = func_get_args();
			call_user_func_array(array($this, '_add_edit'), $args);
		}

		/**
		 *
		 */
		public function edit($id) {
			$this->WebrsaAccesses->check($id);
			$args = func_get_args();
			call_user_func_array(array($this, '_add_edit'), $args);
		}

		/**
		 *
		 * @param integer $id
		 */
		protected function _add_edit($id = null) {
			$this->assert(valid_int($id), 'invalidParameter');

			$fichiers = array();

			// Récupération des id afférents
			if ($this->action == 'add') {
				$personnepcg66_id = $id;
				$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $this->Traitementpcg66->Personnepcg66->personneId($personnepcg66_id))));

				$conditionsDatas = $this->Traitementpcg66->Personnepcg66->find( 'first',
					array(
						'fields' => array(
							'Dossierpcg66.foyer_id',
							'Dossierpcg66.poledossierpcg66_id'
						),
						'contain' => false,
						'joins' => array(
							$this->Traitementpcg66->Personnepcg66->join( 'Dossierpcg66', array( 'type' => 'INNER' ) ),
						),
						'conditions' => array(
							'Personnepcg66.id' => $personnepcg66_id,
						)
					)
				);
				$traitementAImprimer = $this->Traitementpcg66->Personnepcg66->Dossierpcg66->Foyer->find( 'first',
					array(
						'fields' => 'Decisiondossierpcg66.id',
						'contain' => false,
						'joins' => array(
							$this->Traitementpcg66->Personnepcg66->Dossierpcg66->Foyer->join( 'Dossierpcg66', array( 'type' => 'INNER' ) ),
							$this->Traitementpcg66->Personnepcg66->Dossierpcg66->join( 'Decisiondossierpcg66', array( 'type' => 'INNER' ) ),
						),
						'conditions' => array(
							'Foyer.id' => Hash::get($conditionsDatas, 'Dossierpcg66.foyer_id'),
							'Dossierpcg66.poledossierpcg66_id' => Hash::get($conditionsDatas, 'Dossierpcg66.poledossierpcg66_id'),
							'Decisiondossierpcg66.etatdossierpcg IS NULL',
							'Decisiondossierpcg66.validationproposition' => 'O',
							'(Decisiondossierpcg66.created)::date = NOW()::date'
						)
					)
				);

				$imprimer = (int)!empty( $traitementAImprimer );

			} else if ($this->action == 'edit') {
				$traitementpcg66_id = $id;
				$traitementpcg66 = $this->Traitementpcg66->find(
						'first', array(
					'conditions' => array(
						'Traitementpcg66.id' => $traitementpcg66_id
					),
					'contain' => array(
						'Modeletraitementpcg66'
					)
						)
				);
				$this->assert(!empty($traitementpcg66), 'invalidParameter');

				$imprimer = Hash::get( $traitementpcg66, 'Traitementpcg66.imprimer');

				$personnepcg66_id = Set::classicExtract($traitementpcg66, 'Traitementpcg66.personnepcg66_id');
				$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $this->Traitementpcg66->Personnepcg66->personneId($personnepcg66_id))));
			}

			$this->set( compact('imprimer') );

			//Récupération des informations de la personne conernée par les traitements + du dossier
			$personnepcg66 = $this->Traitementpcg66->Personnepcg66->find(
					'first', array(
				'conditions' => array(
					'Personnepcg66.id' => $personnepcg66_id
				),
				'contain' => array(
					'Statutpdo',
					'Situationpdo'
				)
					)
			);
			$this->set('personnepcg66', $personnepcg66);


			$listeMotifs = $this->Traitementpcg66->Personnepcg66->Personnepcg66Situationpdo->Situationpdo->find(
				'list', array(
					'fields' => array('Situationpdo.id', 'Situationpdo.libelle'),
					'joins' => array(
						$this->Traitementpcg66->Personnepcg66->Personnepcg66Situationpdo->Situationpdo->join('Personnepcg66Situationpdo')
					),
					'conditions' => array(
						'Personnepcg66Situationpdo.personnepcg66_id' => $personnepcg66_id
					),
				)
			);

			$this->set(compact('listeMotifs'));

			// On récupère l'utilisateur connecté et qui exécute l'action
			$userConnected = $this->Session->read('Auth.User.id');
			$this->set(compact('userConnected'));


			$dossierpcg66_id = Set::classicExtract($personnepcg66, 'Personnepcg66.dossierpcg66_id');
			$personne_id = Set::classicExtract($personnepcg66, 'Personnepcg66.personne_id');

			$dossierpcg66 = $this->Traitementpcg66->Personnepcg66->Dossierpcg66->find(
					'first', array(
				'conditions' => array(
					'Dossierpcg66.id' => $dossierpcg66_id
				),
				'contain' => false
					)
			);
			$this->set(compact('dossierpcg66'));
			$foyer_id = Set::classicExtract($dossierpcg66, 'Dossierpcg66.foyer_id');
			$dossier_id = $this->Traitementpcg66->Personnepcg66->Dossierpcg66->Foyer->dossierId($foyer_id);

			$dossier = $this->Traitementpcg66->Personnepcg66->Dossierpcg66->Foyer->Dossier->find(
					'first', array(
				'fields' => array(
					'dtdemrsa'
				),
				'conditions' => array(
					'Dossier.id' => $dossier_id
				),
				'contain' => false
					)
			);
			$dtdemrsa = Set::classicExtract($dossier, 'Dossier.dtdemrsa');
			$this->set('dtdemrsa', $dtdemrsa);

			$qd_personne = array(
				'conditions' => array(
					'Personne.id' => $personne_id
				),
				'fields' => array(
					$this->Traitementpcg66->Personnepcg66->Personne->sqVirtualField('nom_complet')
				),
				'order' => null,
				'recursive' => -1
			);
			$personne = $this->Traitementpcg66->Personnepcg66->Personne->find('first', $qd_personne);

			$nompersonne = Set::classicExtract($personne, 'Personne.nom_complet');

			$this->set(compact('nompersonne'));

			//Gestion des jetons
			$dossier_id = $this->Traitementpcg66->Personnepcg66->Dossierpcg66->Foyer->dossierId($foyer_id);
			$this->Jetons2->get($dossier_id);

			// Retour à la liste en cas d'annulation
			if (!empty($this->request->data) && isset($this->request->data['Cancel'])) {
				$this->Jetons2->release($dossier_id);
				$this->redirect(array('action' => 'index', $personne_id, $dossierpcg66_id));
			}

			if (!empty($this->request->data)) {
				$this->Traitementpcg66->begin();

				// Gestion de la position du traitement
				if ($this->action == 'add' && Hash::get($this->request->data, 'Traitementpcg66.typetraitement') === 'courrier' ) {
					$this->request->data['Traitementpcg66']['etattraitementpcg'] = $imprimer === 1 ? 'imprimer' : 'contrôler';
				}

				$dataToSave = $this->request->data;
				// INFO: attention, on peut se le permettre car il n'y a pas de règle de validation sur le commentaire
				if (!empty($dataToSave['Modeletraitementpcg66']['modeletypecourrierpcg66_id'])) {
					$dataToSave['Modeletraitementpcg66']['commentaire'] = $dataToSave['Modeletraitementpcg66'][$dataToSave['Modeletraitementpcg66']['modeletypecourrierpcg66_id']]['commentaire'];

					if (!empty($dataToSave['Modeletraitementpcg66'][$dataToSave['Modeletraitementpcg66']['modeletypecourrierpcg66_id']]['montantsaisi'])) {
						$dataToSave['Modeletraitementpcg66']['montantsaisi'] = $dataToSave['Modeletraitementpcg66'][$dataToSave['Modeletraitementpcg66']['modeletypecourrierpcg66_id']]['montantsaisi'];
					}

					if (!empty($dataToSave['Modeletraitementpcg66'][$dataToSave['Modeletraitementpcg66']['modeletypecourrierpcg66_id']]['montantdatedebut'])) {

						$dataToSave['Modeletraitementpcg66']['montantdatedebut'] = $dataToSave['Modeletraitementpcg66'][$dataToSave['Modeletraitementpcg66']['modeletypecourrierpcg66_id']]['montantdatedebut'];

						$dataToSave['Modeletraitementpcg66']['montantdatefin'] = $dataToSave['Modeletraitementpcg66'][$dataToSave['Modeletraitementpcg66']['modeletypecourrierpcg66_id']]['montantdatefin'];
					}

					unset($dataToSave['Modeletraitementpcg66'][$dataToSave['Modeletraitementpcg66']['modeletypecourrierpcg66_id']]);
				}

				$saved = $this->Traitementpcg66->WebrsaTraitementpcg66->sauvegardeTraitement($dataToSave);

				// Clôture des traitements PCGs non clôturés, appartenant même à un autre dossier
				// que celui auquel je suis lié

				if ($saved && !empty($dataToSave['Traitementpcg66']['Traitementpcg66'])) {
					$saved = $this->Traitementpcg66->updateAllUnBound(
									array('Traitementpcg66.clos' => '\'O\''), array(
								'Traitementpcg66.id' => $dataToSave['Traitementpcg66']['Traitementpcg66']
									)
							) && $saved;
				}


				if ($saved) {
					// Début sauvegarde des fichiers attachés, en utilisant le Component Fileuploader
					$dir = $this->Fileuploader->dirFichiersModule($this->action, $this->request->params['pass'][0]);
					$saved = $this->Fileuploader->saveFichiers(
									$dir, !Set::classicExtract($dataToSave, "Traitementpcg66.haspiecejointe"), ( ( $this->action == 'add' ) ? $this->Traitementpcg66->id : $id)
							) && $saved;

					if ($saved) {
						$this->Traitementpcg66->commit();
						$this->Jetons2->release($dossier_id);
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect(array('controller' => 'traitementspcgs66', 'action' => 'index', $personne_id, $dossierpcg66_id));
					} else {
						$this->Traitementpcg66->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				} else {
					$fichiers = $this->Fileuploader->fichiers($id, false);

					$this->Traitementpcg66->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			} else if ($this->action == 'edit') {
				$this->request->data = $traitementpcg66;
			}

			$fichiersEnBase = array();
			if ($this->action == 'edit') {
				$fichiersEnBase = Hash::extract(
					$this->Fileuploader->fichiersEnBase($id),
					'{n}.Fichiermodule'
				);
			}
			$this->set('fichiersEnBase', $fichiersEnBase);

			if ($this->action == 'edit') {
				$conditions = array(
					'Traitementpcg66.personnepcg66_id' => $personnepcg66_id,
					'Traitementpcg66.clos' => 'N',
					'Traitementpcg66.annule' => 'N',
					'Traitementpcg66.id NOT' => $id
				);
			} else {
				$conditions = array(
					'Traitementpcg66.personnepcg66_id' => $personnepcg66_id,
					'Traitementpcg66.clos' => 'N',
					'Traitementpcg66.annule' => 'N'
				);
			}

			$traitementspcgsouverts = $this->Traitementpcg66->find(
					'all', array(
				'conditions' => $conditions,
				'contain' => array(
					'Descriptionpdo'
				),
				'order' => array('Traitementpcg66.dateecheance DESC')
					)
			);

			$this->set(compact('traitementspcgsouverts', 'fichiers'));

			//Liste des traitements non clos appartenant aux dossiers liés à mon Foyer
			$listeTraitementsNonClos = $this->Traitementpcg66->Personnepcg66->listeTraitementpcg66NonClos($personne_id, $this->action, $this->request->data, $traitementspcgsouverts);
			$this->set('listeTraitementsNonClos', $listeTraitementsNonClos);

			// Récupération et vérification d'une fiche de calcul existante parmi les traitements d'un dossier PCG passé
			$infoDerniereFicheCalcul = $this->Traitementpcg66->WebrsaTraitementpcg66->infoDerniereFicheCalcul($personne_id, $this->action, $this->request->data);
			$this->set('infoDerniereFicheCalcul', $infoDerniereFicheCalcul);

			$this->_setOptions();

			$this->set(compact('personne_id', 'dossier_id', 'dossierpcg66_id', 'personnepcg66_id'));
			$this->set('urlmenu', '/traitementspcgs66/index/' . $personne_id);

			$this->render('add_edit');
		}

		/**
		 *
		 * @param integer $id
		 */
		public function view($id = null) {
			$this->WebrsaAccesses->check($id);
			$traitementpcg66 = $this->Traitementpcg66->find(
					'first', array(
				'conditions' => array(
					'Traitementpcg66.id' => $id
				),
				'contain' => array(
					'Personnepcg66' => array(
						'Personne'
					),
					'Fichiermodule',
					'Descriptionpdo'
				)
					)
			);
			$this->assert(!empty($traitementpcg66), 'invalidParameter');

			$personnepcg66_id = Set::classicExtract($traitementpcg66, 'Traitementpcg66.personnepcg66_id');

			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $this->Traitementpcg66->Personnepcg66->personneId($personnepcg66_id))));

			$dossierpcg66_id = Set::classicExtract($traitementpcg66, 'Personnepcg66.dossierpcg66_id');
			$personne_id = Set::classicExtract($traitementpcg66, 'Personnepcg66.personne_id');

			// Retour à l'entretien en cas de retour
			if (isset($this->request->data['Cancel'])) {
				$this->redirect(array('controller' => 'traitementspcgs66', 'action' => 'index', $personne_id, $dossierpcg66_id));
			}

			$this->set( 'options', $this->Traitementpcg66->enums() );
			$this->set(compact('traitementpcg66', 'personne_id'));

			$this->set('urlmenu', '/traitementspcgs66/index/' . $personne_id);
		}

		/**
		 *
		 * @param integer $id
		 */
		public function cancel($id) {
			$this->WebrsaAccesses->check($id);
			$traitementpcg66 = $this->Traitementpcg66->find(
					'first', array(
				'fields' => array_merge(
						$this->Traitementpcg66->fields(), $this->Traitementpcg66->Personnepcg66->fields(), $this->Traitementpcg66->Personnepcg66->Dossierpcg66->fields()
				),
				'conditions' => array(
					'Traitementpcg66.id' => $id
				),
				'recursive' => -1,
				'joins' => array(
					$this->Traitementpcg66->join('Personnepcg66', array('type' => 'INNER')),
					$this->Traitementpcg66->Personnepcg66->join('Dossierpcg66', array('type' => 'INNER'))
				)
					)
			);


			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $traitementpcg66['Personnepcg66']['personne_id'])));

			//Gestion des jetons
			$dossier_id = $this->Traitementpcg66->Personnepcg66->Dossierpcg66->Foyer->dossierId($traitementpcg66['Dossierpcg66']['foyer_id']);
			$this->Jetons2->get($dossier_id);

			// Retour à la liste en cas d'annulation
			if (!empty($this->request->data) && isset($this->request->data['Cancel'])) {
				$this->Jetons2->release($dossier_id);
				$this->redirect(array('controller' => 'traitementspcgs66', 'action' => 'index', $traitementpcg66['Personnepcg66']['personne_id'], $traitementpcg66['Personnepcg66']['dossierpcg66_id']));
			}

			if (!empty($this->request->data)) {
				$this->Traitementpcg66->begin();

				$saved = $this->Traitementpcg66->save( $this->request->data, array( 'atomic' => false ) );
				$saved = $this->Traitementpcg66->updateAllUnBound(
								array(
							'Traitementpcg66.clos' => '\'O\'',
							'Traitementpcg66.annule' => '\'O\''
								), array(
							'"Traitementpcg66"."personnepcg66_id"' => $traitementpcg66['Traitementpcg66']['personnepcg66_id'],
							'"Traitementpcg66"."id"' => $traitementpcg66['Traitementpcg66']['id']
								)
						) && $saved;

				// Remise à jour de l'état du dossier PCG
				$typetraitementpcg = $traitementpcg66['Traitementpcg66']['typetraitement'];
				$etatdossierpcg = $traitementpcg66['Dossierpcg66']['etatdossierpcg'];
				$dossierpcg66_id = $traitementpcg66['Dossierpcg66']['id'];
				if ($saved && $typetraitementpcg == 'documentarrive' && $etatdossierpcg == 'attinstrdocarrive') {
					$this->Traitementpcg66->Personnepcg66->Dossierpcg66->id = $dossierpcg66_id;
					$this->Traitementpcg66->Personnepcg66->Dossierpcg66->saveField('etatdossierpcg', 'attinstrattpiece') && $saved;
				}

				if ($saved) {
					$this->Traitementpcg66->commit();
					$this->Jetons2->release($dossier_id);
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('controller' => 'traitementspcgs66', 'action' => 'index', $traitementpcg66['Personnepcg66']['personne_id'], $traitementpcg66['Personnepcg66']['dossierpcg66_id']));
				} else {
					$this->Traitementpcg66->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			} else {
				$this->request->data = $traitementpcg66;
			}
		}

		/**
		 *
		 * @param integer $id
		 */
		public function clore($id) {
			$this->WebrsaAccesses->check($id);
			$traitementpcg66 = $this->Traitementpcg66->find(
					'first', array(
				'conditions' => array(
					'Traitementpcg66.id' => $id
				),
				'contain' => array(
					'Personnepcg66'
				)
					)
			);
			$this->DossiersMenus->checkDossierMenu(array('personne_id' => $traitementpcg66['Personnepcg66']['personne_id']));

			$this->Traitementpcg66->begin();
			$this->Traitementpcg66->id = $id;
			$success = $this->Traitementpcg66->saveField('clos', 'O');

			if ($success) {
				$this->Traitementpcg66->commit();
				$this->Flash->success( 'Le traitement est clôturé' );
			} else {
				$this->Traitementpcg66->rollback();
				$this->Flash->error( 'Erreur lors de la clôture du traitement' );
			}
			$this->redirect(array('controller' => 'traitementspcgs66', 'action' => 'index', $traitementpcg66['Personnepcg66']['personne_id'], $traitementpcg66['Personnepcg66']['dossierpcg66_id']));
		}

		/**
		 *
		 * @param integer $id
		 */
		public function reverseDO($id) {
			$this->WebrsaAccesses->check($id);
			$traitementpcg66 = $this->Traitementpcg66->find(
					'first', array(
				'conditions' => array(
					'Traitementpcg66.id' => $id
				),
				'contain' => array(
					'Personnepcg66'
				)
					)
			);

			$this->DossiersMenus->checkDossierMenu(array('personne_id' => $traitementpcg66['Personnepcg66']['personne_id']));

			$this->Traitementpcg66->begin();
			$this->Traitementpcg66->id = $id;
			$success = $this->Traitementpcg66->saveField('reversedo', '1');

			if ($success) {
				$this->Traitementpcg66->commit();
				$this->Flash->success( 'La fiche de calcul sera repercutée dans la décision' );
			} else {
				$this->Traitementpcg66->rollback();
				$this->Flash->error( 'Erreur lors de la répercussion de la fiche de calcul' );
			}
			$this->redirect(array('controller' => 'traitementspcgs66', 'action' => 'index', $traitementpcg66['Personnepcg66']['personne_id'], $traitementpcg66['Personnepcg66']['dossierpcg66_id']));
		}

		/**
		 *
		 * @param integer $id
		 */
		public function deverseDO($id) {
			$this->WebrsaAccesses->check($id);
			$traitementpcg66 = $this->Traitementpcg66->find(
					'first', array(
				'conditions' => array(
					'Traitementpcg66.id' => $id
				),
				'contain' => array(
					'Personnepcg66'
				)
					)
			);
			$this->DossiersMenus->checkDossierMenu(array('personne_id' => $traitementpcg66['Personnepcg66']['personne_id']));

			$this->Traitementpcg66->begin();
			$this->Traitementpcg66->id = $id;
			$success = $this->Traitementpcg66->saveField('reversedo', '0');

			if ($success) {
				$this->Traitementpcg66->commit();
				$this->Flash->success( 'La fiche de calcul ne sera plus repercutée dans la décision' );
			} else {
				$this->Traitementpcg66->rollback();
				$this->Flash->error( 'Erreur lors de la non répercussion de la fiche de calcul' );
			}
			$this->redirect(array('controller' => 'traitementspcgs66', 'action' => 'index', $traitementpcg66['Personnepcg66']['personne_id'], $traitementpcg66['Personnepcg66']['dossierpcg66_id']));
		}

		/**
		 * Enregistrement du document pour la fiche de calcul lors de l'enregistrement du traitement
		 *
		 * @param integer $id
		 */
		public function printFicheCalcul($id) {
			$this->WebrsaAccesses->check($id);
			$this->assert(!empty($id), 'error404');
			$this->DossiersMenus->checkDossierMenu(array('personne_id' => $this->Traitementpcg66->personneId($id)));

			$pdf = $this->Traitementpcg66->WebrsaTraitementpcg66->getPdfFichecalcul($id);

			if ($pdf) {
				$this->Gedooo->sendPdfContentToClient($pdf, 'Décision.pdf');
			} else {
				$this->Flash->error( 'Impossible de générer la fiche de calcul' );
				$this->redirect($this->referer());
			}
		}

		/**
		 * Enregistrement du modèle de document lié au type de courrier lors de l'enregistrement du traitement
		 *
		 * @param integer $id
		 */
		public function printModeleCourrier($id) {
			$this->WebrsaAccesses->check($id);
			$this->assert(!empty($id), 'error404');
			$this->DossiersMenus->checkDossierMenu(array('personne_id' => $this->Traitementpcg66->personneId($id)));

			$pdf = $this->Traitementpcg66->WebrsaTraitementpcg66->getPdfModeleCourrier($id, $this->Session->read('Auth.User.id'));

			if ($pdf) {
				$this->Gedooo->sendPdfContentToClient($pdf, 'ModeleCourrier.pdf');
			} else {
				$this->Traitementpcg66->rollback();
				$this->Flash->error( 'Impossible de générer le modèle de courrier' );
				$this->redirect($this->referer());
			}
		}

		/**
		 *
		 * @param integer $id
		 */
		public function delete($id) {
			$this->WebrsaAccesses->check($id);
			$this->DossiersMenus->checkDossierMenu(array('personne_id' => $this->Traitementpcg66->personneId($id)));

			$this->Traitementpcg66->begin();

			// On récupère l'id du dossier pcg
			$data = $this->Traitementpcg66->find( 'first',
				array(
					'fields' => 'Personnepcg66.dossierpcg66_id',
					'conditions' => array(
						'Traitementpcg66.id' => $id,
					),
					'contain' => false,
					'joins' => array(
						$this->Traitementpcg66->join('Personnepcg66', array('type' => 'INNER')),
						$this->Traitementpcg66->Personnepcg66->join('Dossierpcg66', array('type' => 'INNER')),
					)
				)
			);
			$success = !empty($data);
			$success = $success && $this->Traitementpcg66->delete($id);

			// On recalcule la position du dossier pcg
			$success = $success && $this->Traitementpcg66->Personnepcg66->Dossierpcg66->WebrsaDossierpcg66->updatePositionsPcgsById( $data['Personnepcg66']['dossierpcg66_id'] );

			if ( $success ){
				$this->Flash->success( __( 'Delete->success' ) );
				$this->Traitementpcg66->commit();
			}
			else{
				$this->Flash->error( __( 'Delete->error' ) );
				$this->Traitementpcg66->rollback();
			}

			$this->redirect($this->referer());
		}

		/**
		 *
		 */
		public function envoiCourrier($id) {
			$this->WebrsaAccesses->check($id);
			$this->DossiersMenus->checkDossierMenu(array('personne_id' => $this->Traitementpcg66->personneId($id)));

			$traitementpcg66 = $this->Traitementpcg66->find(
					'first', array(
				'conditions' => array(
					'Traitementpcg66.id' => $id
				),
				'contain' => array(
					'Personnepcg66'
				)
					)
			);
			$this->Traitementpcg66->id = $id;
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $traitementpcg66['Personnepcg66']['personne_id'])));

			// Retour à la liste en cas d'annulation
			if (!empty($this->request->data) && isset($this->request->data['Cancel'])) {
				$this->redirect(array('controller' => 'traitementspcgs66', 'action' => 'index', $traitementpcg66['Personnepcg66']['personne_id'], $traitementpcg66['Personnepcg66']['dossierpcg66_id']));
			}

			if (!empty($this->request->data)) {
				$this->Traitementpcg66->begin();

				$success = $this->Traitementpcg66->updateAllUnbound(
					array(
						'dateenvoicourrier' => "'".date_cakephp_to_sql($this->request->data['Traitementpcg66']['dateenvoicourrier'])."'",
						'etattraitementpcg' => "'envoyé'"
					),
					array( 'id' => $id, )
				);

				$dataCourrier = $this->Traitementpcg66->WebrsaTraitementpcg66->getDataForPdfCourrier($id, $this->Session->read('Auth.User.id'), false);
				$data = array(
					'modele' => 'Traitementpcg66',
					'fk_value' => $id,
					'data' => json_encode($dataCourrier)
				);
				$success = $success && $this->Traitementpcg66->Dataimpression->save( $data, array( 'atomic' => false ) );

				if ($success) {
					$this->Traitementpcg66->commit();
					$this->Flash->success( 'La date d\'envoi du courrier a bien été enregistrée' );
					$this->redirect(array('controller' => 'traitementspcgs66', 'action' => 'index', $traitementpcg66['Personnepcg66']['personne_id'], $traitementpcg66['Personnepcg66']['dossierpcg66_id']));
				} else {
					$this->Traitementpcg66->rollback();
					$this->Flash->error( 'Erreur lors de l\'enregistrement de la date' );
				}
			}
			$this->set(compact('traitementpcg66'));
		}

		/**
		 * Moteur de recherche
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesTraitementspcgs66' );
			$Recherches->search();
			$this->Traitementpcg66->validate = array();
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesTraitementspcgs66' );
			$Recherches->exportcsv();
		}

		/**
		 * Change la valeur de Traitementpcg66.imprimer par son contraire (0 ou 1)
		 *
		 * @param type $traitement_id
		 */
		public function switch_imprimer( $traitement_id ) {
			$this->WebrsaAccesses->check($traitement_id);
			$this->DossiersMenus->checkDossierMenu(array('personne_id' => $this->Traitementpcg66->personneId($traitement_id)));
			$this->Traitementpcg66->begin();

			$oldImprimer = $this->Traitementpcg66->find( 'first',
				array(
					'fields' => array(
						'imprimer',
						'etattraitementpcg'
					),
					'contain' => false,
					'conditions' => array(
						'id' => $traitement_id
					),
				)
			);

			$imprimer = (int)!Hash::get( $oldImprimer, 'Traitementpcg66.imprimer' );
			$ancienetatpcg = Hash::get( $oldImprimer, 'Traitementpcg66.etattraitementpcg' );
			/*
			$etattraitementpcg = $imprimer === 1 && $ancienetatpcg === "contrôler" ? "'imprimer'"
				: ($imprimer === 0 && $ancienetatpcg === "imprimer" ? "'contrôler'" : "'".$ancienetatpcg."'")
			;
			*/
			$etattraitementpcg = '\''.$ancienetatpcg.'\'';
			if ($imprimer === 1 && $ancienetatpcg === "contrôler") {
				$etattraitementpcg = '\'imprimer\'';
			}
			elseif ($imprimer === 0 && $ancienetatpcg === "imprimer") {
				$etattraitementpcg = '\'contrôler\'';
			}
			elseif ($imprimer === 1 && $ancienetatpcg === "attente") {
				$etattraitementpcg = '\'imprimer\'';
			}
			elseif ($imprimer === 0 && $ancienetatpcg === "attente") {
				$etattraitementpcg = '\'contrôler\'';
			}

			// Pour éviter de passer dans beforeValidate() qui ajoute des champs à valeur NULL
			$success = $this->Traitementpcg66->updateAllUnbound(
				array(
					'imprimer' => $imprimer,
					'etattraitementpcg' => $etattraitementpcg
				),
				array( 'id' => $traitement_id, )
			);

			if ( $success ) {
				$this->Traitementpcg66->commit();

				$message = !Hash::get( $oldImprimer, 'Traitementpcg66.imprimer' )
					? 'Ce courrier est désormais prêt à être imprimé en cohorte.'
					: 'Ce courrier n\'est plus disponible en cohorte'
				;

				$this->Flash->success($message);
			}
			else {
				$this->Traitementpcg66->rollback();
				$this->Flash->error( 'Une erreur s\'est produite !' );
			}

			$this->redirect($this->referer());
		}
	}
?>