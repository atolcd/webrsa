<?php

	/**
	 * Code source de la classe ContratsinsertionController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'WebrsaAccessContratsinsertion', 'Utility' );
	App::uses( 'WebrsaPermissions', 'Utility' );

	/**
	 * La classe ContratsinsertionController permet la gestion des contrats d'insertion au niveau du dossier
	 * de l'allocataire.
	 *
	 * @package app.Controller
	 */
	class ContratsinsertionController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Contratsinsertion';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes',
			'DossiersMenus',
			'Fileuploader',
			'Gedooo.Gedooo',
			'InsertionsBeneficiaires',
			'Jetons2',
			'RequestHandler',
			'Search.SearchPrg' => array(
				'actions' => array(
					'search',
					'cohorte_nouveaux' => array('filter' => 'Search'),
					'cohorte_valides' => array('filter' => 'Search'),
					'cohorte_cersimpleavalider' => array('filter' => 'Search'),
					'cohorte_cerparticulieravalider' => array('filter' => 'Search'),
					'search_valides' => array('filter' => 'Search'),
				),
			),
			'WebrsaAccesses',
			'WebrsaAjaxInsertions'
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Widget',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Contratinsertion',
			'Option',
			'Personne',
			'WebrsaContratinsertion',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Contratsinsertion:edit',
			'view' => 'Contratsinsertion:index',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajaxaction',
			'ajaxfiledelete',
			'ajaxfileupload',
			'ajaxref',
			'ajaxstruct',
			'download',
			'fileview',
			'notificationsop',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajaxaction' => 'read',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'ajaxref' => 'update',
			'ajaxstruct' => 'update',
			'cancel' => 'update',
			'cohorte_cerparticulieravalider' => 'update',
			'cohorte_cersimpleavalider' => 'update',
			'cohorte_nouveaux' => 'update',
			'cohorte_valides' => 'read',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'exportcsv' => 'read',
			'exportcsv_cerparticulieravalider' => 'update',
			'exportcsv_cersimpleavalider' => 'update',
			'exportcsv_search_valides' => 'update',
			'exportcsv_valides' => 'read',
			'ficheliaisoncer' => 'read',
			'filelink' => 'read',
			'fileview' => 'read',
			'impression' => 'read',
			'index' => 'read',
			'notifbenef' => 'read',
			'notification' => 'update',
			'notificationsop' => 'read',
			'reconduction_cer_plus_55_ans' => 'read',
			'search' => 'read',
			'search_valides' => 'read',
			'valider' => 'update',
			'validerparticulier' => 'update',
			'validersimple' => 'update',
			'view' => 'read',
		);

		/**
		 * Variables utilisées pour certains fonction (notamment le calcul du cumul des CER)
		 */
		private $cumulDuree = 0;
		private $dateFinCERPrecedent = '';
		private $debutPlacePrecedente = '';
		private $finPlacePrecedente = '';

		/**
		 * Envoi des options communes à la vue (CG 58, 66, 93).
		 *
		 * @return void
		 */
		protected function _setOptions() {
			$options = array_merge(
				$this->Contratinsertion->enums(),
				$this->Contratinsertion->Personne->Dossierep->Passagecommissionep->enums()
			);

			if (in_array($this->action, array('index', 'add', 'edit', 'view', 'valider', 'validersimple', 'validerparticulier'))) {
				$this->set( 'duree_engag', $this->Option->duree_engag() );
				$options = array_merge($options, $this->Contratinsertion->Propodecisioncer66->enums());
				$this->set('decision_ci', ClassRegistry::init('Contratinsertion')->enum('decision_ci'));
				$forme_ci = array();
				if (Configure::read('nom_form_ci_cg') == 'cg93') {
					$forme_ci = array('S' => 'Simple', 'C' => 'Complexe');
				} else if (Configure::read('nom_form_ci_cg') == 'cg66') {
					$forme_ci = array('S' => 'Simple', 'C' => 'Particulier');
				}
				$this->set('forme_ci', $forme_ci);
			}

			if (in_array($this->action, array('add', 'edit', 'view', 'valider'))) {
				$this->set('formeci', ClassRegistry::init('Cer93')->enum('formeci'));
			}

			if (in_array($this->action, array('add', 'edit', 'view', 'valider', 'validersimple', 'validerparticulier'))) {
				$this->set('qual', $this->Option->qual());
				$this->set('raison_ci', ClassRegistry::init('Contratinsertion')->enum('raison_ci'));
				if (Configure::read('Cg.departement') == 66) {
					$this->set('avisraison_ci', ClassRegistry::init('Contratinsertion')->enum('avisraison_ci'));
				} else if (Configure::read('Cg.departement') == 93) {
					$this->set('avisraison_ci', array('D' => 'Defaut de conclusion', 'N' => 'Non respect du contrat'));
				}
				$this->set('aviseqpluri', ClassRegistry::init('Contratinsertion')->enum('aviseqpluri'));
				$this->set('sect_acti_emp', ClassRegistry::init('Contratinsertion')->enum('sect_acti_emp'));
				$this->set('emp_occupe', ClassRegistry::init('Contratinsertion')->enum('emp_occupe'));
				$this->set('duree_hebdo_emp', ClassRegistry::init('Contratinsertion')->enum('duree_hebdo_emp'));
				$this->set('nat_cont_trav', ClassRegistry::init('Contratinsertion')->enum('nat_cont_trav'));
				$this->set('duree_cdd', ClassRegistry::init('Contratinsertion')->enum('duree_cdd'));
				$this->set( 'duree_engag', $this->Option->duree_engag() );

				$this->set('nivetus', $this->Contratinsertion->Personne->Dsp->enum('nivetu'));
				$this->set('nivdipmaxobt', $this->Contratinsertion->Personne->Dsp->enum('nivdipmaxobt'));
				$this->set('typeserins', $this->Option->typeserins());

				$this->set('lib_action', ClassRegistry::init('Actioninsertion')->enum('lib_action'));
				$this->set('typo_aide', ClassRegistry::init('Aidedirecte')->enum('typo_aide'));
				$this->set('soclmaj', ClassRegistry::init('Infofinanciere')->enum('natpfcre', array('type' => 'soclmaj')));
				$this->set('rolepers', ClassRegistry::init('Prestation')->enum('rolepers'));
				$this->set('sitfam', $this->Option->sitfam());
				$this->set('typeocclog', ClassRegistry::init('Foyer')->enum('typeocclog'));
				$this->set('emp_trouv', array('N' => 'Non', 'O' => 'Oui'));
				$this->set('zoneprivilegie', ClassRegistry::init('Zonegeographique')->find('list'));
				$this->set('actions', $this->Contratinsertion->Action->grouplist('prest'));
				$this->set('fiches', (array) Hash::get($this->Contratinsertion->Personne->ActioncandidatPersonne->Actioncandidat->enums(), 'Actioncandidat'));

				$options = array_merge(
						$options, (array) Hash::get($this->Contratinsertion->Autreavissuspension->enums(), 'Autreavissuspension'), (array) Hash::get($this->Contratinsertion->Autreavisradiation->enums(), 'Autreavisradiation'), $options['Contratinsertion']
				);
			}

			if ( Configure::read( 'Cg.departement' ) == 66 ) {
				$Entretien = ClassRegistry::init( 'Entretien' );

				$options = array_merge($options, $Entretien->options());
				$options['Entretien']['structurereferente_id'] = $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) );
			}

			$this->set('options', $options);
		}

		/**
		 * Ajax pour les partenaires fournissant l'action liée au CER (CG 66).
		 *
		 * @param type $actioncandidat_id
		 */
		public function ajaxaction($actioncandidat_id = null) {
			Configure::write('debug', 2);

			$dataActioncandidat_id = Set::extract($this->request->data, 'Contratinsertion.actioncandidat_id');
			$actioncandidat_id = ( empty($actioncandidat_id) && !empty($dataActioncandidat_id) ? $dataActioncandidat_id : $actioncandidat_id );

			if (!empty($actioncandidat_id)) {
				$actioncandidat = $this->Contratinsertion->Actioncandidat->find(
						'first', array(
					'conditions' => array(
						'Actioncandidat.id' => $actioncandidat_id
					),
					'contain' => array(
						'Contactpartenaire' => array(
							'Partenaire'
						),
						'Fichiermodule',
						'Referent'
					)
						)
				);
				$this->set(compact('actioncandidat'));
			}
			$this->render('ajaxaction', 'ajax');
		}

		/**
		 * Ajax pour les coordonnées du référent (CG 58, 66, 93).
		 *
		 * @param integer $referent_id
		 */
		public function ajaxref($referent_id = null) {
			return $this->WebrsaAjaxInsertions->referent( $referent_id );
		}

		/**
		 * Ajax pour les coordonnées de la structure référente liée (CG 58, 66, 93).
		 *
		 * @param type $structurereferente_id
		 */
		public function ajaxstruct( $structurereferente_id = null ) {
			return $this->WebrsaAjaxInsertions->structurereferente( $structurereferente_id );
		}

		/**
		 * http://valums.com/ajax-upload/
		 * http://doc.ubuntu-fr.org/modules_php
		 * increase post_max_size and upload_max_filesize to 10M
		 * debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
		 *
		 * (CG 58, 66, 93)
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
		 *
		 * (CG 58, 66, 93)
		 */
		public function ajaxfiledelete() {
			$this->Fileuploader->ajaxfiledelete();
		}

		/**
		 * Fonction permettant de visualiser les fichiers chargés dans la vue
		 * avant leur envoi sur le serveur (CG 58, 66, 93).
		 */
		public function fileview($id) {
			$this->Fileuploader->fileview($id);
		}

		/**
		 * Téléchargement des fichiers préalablement associés à un traitement
		 * donné (CG 58, 66, 93).
		 */
		public function download($fichiermodule_id) {
			$this->assert(!empty($fichiermodule_id), 'error404');
			$this->Fileuploader->download($fichiermodule_id);
		}

		/**
		 * Fonction permettant d'accéder à la page pour lier les fichiers au CER
		 * (CG 58, 66, 93).
		 *
		 * @param type $id
		 */
		public function filelink($id) {
			$this->assert(valid_int($id), 'invalidParameter');

			$fichiers = array();
			$contratinsertion = $this->Contratinsertion->find(
					'first', array(
				'conditions' => array(
					'Contratinsertion.id' => $id
				),
				'contain' => array(
					'Fichiermodule' => array(
						'fields' => array('name', 'id', 'created', 'modified')
					)
				)
					)
			);

			$personne_id = $contratinsertion['Contratinsertion']['personne_id'];
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id)));
			$this->WebrsaAccesses->check($id);

			$dossier_id = $this->Contratinsertion->Personne->dossierId($personne_id);
			$this->assert(!empty($dossier_id), 'invalidParameter');

			$this->Jetons2->get($dossier_id);

			// Retour à l'index en cas d'annulation
			if (isset($this->request->data['Cancel'])) {
				$this->Jetons2->release($dossier_id);
				if (Configure::read('Cg.departement') == 93) {
					$this->redirect(array('controller' => 'cers93', 'action' => 'index', $personne_id));
				} else {
					$this->redirect(array('action' => 'index', $personne_id));
				}
			}

			if (!empty($this->request->data)) {
				$this->Contratinsertion->begin();

				$saved = $this->Contratinsertion->updateAllUnBound(
						array('Contratinsertion.haspiecejointe' => '\'' . $this->request->data['Contratinsertion']['haspiecejointe'] . '\''), array(
					'"Contratinsertion"."personne_id"' => $personne_id,
					'"Contratinsertion"."id"' => $id
						)
				);

				if ($saved) {
					// Sauvegarde des fichiers liés à une PDO
					$dir = $this->Fileuploader->dirFichiersModule($this->action, $this->request->params['pass'][0]);
					$saved = $this->Fileuploader->saveFichiers($dir, !Set::classicExtract($this->request->data, "Contratinsertion.haspiecejointe"), $id) && $saved;
				}

				if ($saved) {
					$this->Contratinsertion->commit();
					$this->Jetons2->release($dossier_id);
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect($this->referer());
				} else {
					$fichiers = $this->Fileuploader->fichiers($id);
					$this->Contratinsertion->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->_setOptions();
			$this->set(compact('dossier_id', 'personne_id', 'fichiers', 'contratinsertion'));
			$this->set('urlmenu', '/contratsinsertion/index/' . $personne_id);
		}

		/**
		 * Liste des CER pour un allocataire donné (CG 58, 66, 93).
		 *
		 * @param integer $personne_id L'id technique de la personne.
		 */
		public function index($personne_id = null) {
			$departement = (int)Configure::read('Cg.departement');
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id));
			$this->_setEntriesAncienDossier($personne_id, 'Contratinsertion');

			/**
			 * Informations nécéssaires pour l'affichage des messages
			 */
			$personne = $this->Contratinsertion->Personne->find(
				'first', array(
					'fields' => array(
						'Personne.qual',
						'Personne.nom',
						'Personne.prenom',
						$this->Contratinsertion->Personne->sqVirtualField('age'),
						'PersonneReferent.id',
					),
					'joins' => array(
						$this->Contratinsertion->Personne->join(
							'PersonneReferent', array(
								'conditions' => array('PersonneReferent.dfdesignation IS NULL')
							)
						),
					),
					'contain' => false,
					'conditions' => array(
						'Personne.id' => $personne_id
					)
				)
			);
			$this->request->data = $personne;

			/**
			 * Messages liés a l'impossibilitée d'ajouter un CER
			 */
			$controle = $this->WebrsaContratinsertion->haveNeededDatas($personne_id);
			$messages = array();
			if ($controle['haveOrient'] === false) {
				$message = "Cette personne ne possède pas d'orientation. Impossible de créer un CER.";
				$messages[$message] = 'error';
			}
			if (!$controle['needReorientationsociale'] && $controle['haveOrientEmploi']) {
				$message = "Cette personne possède actuellement une orientation professionnelle. Impossible de créer un CER.";
				$messages[$message] = 'error';
			}
			if ($controle['haveCui']) {
				$message = "Cette personne possède actuellement un CUI en cours. Impossible de créer un CER.";
				$messages[$message] = 'error';
			}
			if ($controle['haveDossiercovnonfinal']) {
				$message = "Cette personne possède un contrat d'engagement réciproque en attente de passage en COV.";
				$messages[$message] = 'notice';
			}
			if ($controle['haveDemandemaintiencovnonfinal']) {
				$message = "Cette personne est en cours de passage en COV pour la thématique &laquo; "
					. "maintien en social &raquo;. Impossible de créer un CER.";
				$messages[$message] = 'error';
			}
			if ($controle['needReorientationsociale']) {
				$message = "Cette personne possède actuellement une orientation professionnelle. Impossible de créer un CER.<br /> "
					. "Une réorientation sociale doit être sollicitée pour pouvoir enregistrer un CER.";
				$messages[$message] = 'error';
			}
			if ($departement === 58 && !$controle['isSoumisdroitetdevoir']) {
				$message = "Cette personne n'est pas soumise à droit et devoir. Impossible de créer un CER.";
				$messages[$message] = 'error';
			}

			// Contrôles supplémentaire utile pour un CG en particulier
			if ($departement === 58) {
				$querydata = $this->WebrsaContratinsertion->qdThematiqueEp('Sanctionep58', $personne_id);
				$querydata['fields'] = Set::merge($querydata['fields'],
					array(
						'Sanctionep58.id',
						'Sanctionep58.contratinsertion_id',
						'Sanctionep58.created',
						'Sanctionep58.modified',
					)
				);
				$querydata = $this->WebrsaContratinsertion->completeVirtualFieldsForAccess($querydata);

				// Note : completeVirtualFieldsForAccess fait la jointure sur Dossierep
				$querydata = WebrsaModelUtility::unsetJoin('Dossierep', $querydata);

				$sanctionseps58 = WebrsaAccessContratsinsertion::accesses(
					$this->Contratinsertion->Signalementep93->Dossierep->find('all', $querydata), $controle
				);
				$this->set('sanctionseps58', $sanctionseps58);

				$controle['erreursCandidatePassage'] = $this->Contratinsertion
					->Sanctionep58->Dossierep->getErreursCandidatePassage($personne_id)
				;
				$controle['haveSanctionep'] = !empty($sanctionseps58);

				$qdEnCours = $this->Contratinsertion->Propocontratinsertioncov58nv
					->WebrsaPropocontratinsertioncov58->completeVirtualFieldsForAccess(
						$this->Contratinsertion->Personne->Dossiercov58->Propocontratinsertioncov58->qdEnCours($personne_id)
					)
				;
				App::uses('WebrsaAccessProposcontratsinsertioncovs58', 'Utility');
				$this->set('propocontratinsertioncov58',
					WebrsaAccessProposcontratsinsertioncovs58::accesses(
						array($this->Contratinsertion->Personne->Dossiercov58->Propocontratinsertioncov58->find('first', $qdEnCours))
					)
				);
			}

			// Pas de blocage pour le 976, donc il faut retirer la mention
			// "Impossible de créer un CER." et mettre le message sous la class "notice"
			if ($departement === 976) {
				$newMessages = array();
				foreach ($messages as $message => $class) {
					if ($class === 'error' && strpos($message, "Impossible de créer un CER.")) {
						$message = str_replace('Impossible de créer un CER.', '', $message);
						$class = 'notice';
					}
					$newMessages[$message] = $class;
				}
				$messages = $newMessages;
			}

			/**
			 * Contrôle d'accès
			 */
			$querydata = Hash::merge(
				$this->Contratinsertion->WebrsaContratinsertion->qdIndex($personne_id),
				array(
					'fields' => array(
						'(SELECT COUNT(*) FROM fichiersmodules AS a WHERE a.modele = \'Contratinsertion\' AND a.fk_value = "Contratinsertion"."id") AS "Fichiermodule__count"',
						'Contratinsertion.personne_id'
					)
				)
			);
			$querydata['contain'] = false;

			$contratsinsertion = $this->WebrsaAccesses->getIndexRecords($personne_id, $querydata);

			$options = array_merge(
				$this->Contratinsertion->enums(),
				$this->Contratinsertion->Personne->Dossierep->Passagecommissionep->enums()
			);

			if ($departement === 58) {
				$options = array_merge(
					$options,
					$this->Contratinsertion->Personne->Orientstruct->Personne->Dossiercov58->Passagecov58->enums(),
					$this->Contratinsertion->Personne->Orientstruct->Personne->Dossiercov58->Propocontratinsertioncov58->enums()
				);
			}

			$this->set('options', $options);

			//récupère le cumul des CER
			foreach($contratsinsertion as $index=>$value) {
				$contratsinsertion[$index]["Contratinsertion"]["totalCumulCER"] = $this->getDureeCERVersion2($value);
				//$contratsinsertion[$index]["Contratinsertion"]["total"] = $this->getDureeCER($value);
			}

			/**
			 * Spécifique aux Départements
			 */
			if ($departement === 66) {
				//$dureeTotalCER = $this->getDureeTotalCER($contratsinsertion);
				$dateLastEpParcours = $this->_dateLastEpParcours($personne_id, $contratsinsertion);
				$dureeTotalCER = $this->getDureeTotalCERPostLastEP($contratsinsertion, $dateLastEpParcours);

				//message supprimé le 20/12/2018 à la demande de Mijo
				/*if (Hash::get($personne, 'Personne.age') < (int)Configure::read('Tacitereconduction.limiteAge')
					&& $dureeTotalCER > Configure::read( 'cer.duree.tranche' )) {
					$message = 'Cet allocataire dépasse les '.Configure::read( 'cer.duree.tranche' ).' mois de contractualisation '
						. 'dans une orientation SOCIALE. Vous devez donc proposer un bilan pour passage en EPL.'
						. '<br />'
						. 'Il cumule '.$dureeTotalCER.' mois de CER depuis la dernière EP qui a eu lieu le '.date_format (date_create ($dateLastEpParcours), 'd/m/Y').'.'
						. '';
					$messages[$message] = 'error';
				}*/
				if (!Hash::get($personne, 'PersonneReferent.id')) {
					$message = "Aucun référent n'est lié au parcours de cette personne.";
					$messages[$message] = 'error';
				}
				if (!Hash::get($contratsinsertion, '0.Contratinsertion.id')) {
					$message = "Cette personne ne possède pas encore de CER.";
					$messages[$message] = 'notice';
				}

				foreach ($contratsinsertion as $key => $contratinsertion) {
					$positioncer = Hash::get($contratinsertion, 'Contratinsertion.positioncer');

					// Ajoute à la position traduite la mention "le <date>"
					if (in_array($positioncer, array('nonvalid', 'encours'))
						&& $datenotif = Hash::get($contratinsertion, 'Contratinsertion.datenotification')
					) {
						$contratsinsertion[$key]['Contratinsertion']['positioncer'] = Hash::get(
							Hash::get($options, 'Contratinsertion.positioncer'),
							$positioncer
						).' le '.date_short($datenotif);
					}
				}
			}

			//inversion du tableau
			$contratsinsertion = array_reverse($contratsinsertion);

			$this->set(compact('personne_id', 'contratsinsertion', 'messages', 'dossierMenu', 'ajoutPossible'));
			$this->view = Configure::read('nom_form_ci_cg') ? 'index_'.Configure::read('nom_form_ci_cg') : 'index';
		}

		/**
		 * Inverse l'ordre complet d'un array dans les cas où array_reverse n'est pas utilisable
		 *
		 * @param array $array
		 */
		private function reverseArray($array) {
			$arrayTemp	=	array();
			$boucle = count($array)-1;
			foreach($array as $index=>$value) {
				$arrayTemp[$boucle] = $value;
				$boucle--;
			}
			sort($arrayTemp, SORT_NUMERIC);

			return $arrayTemp;
		}

		/**
		 * Calcul la durée cumulée des CER
		 * En incluant les plages de dates communes
		 *
		 * @param object $contratInsertion Infos du CER
		 */
		private function getDureeCER($contratInsertion) {
			$dureeCER = 0;
			$dureeTemp = array_keys ($this->Option->duree_engag ());
			$dureeMaximaleCER = array_pop ($dureeTemp);

			//si un contrat est validé
			//ne fonctionne pas s'il est annulé ou en attente de décision
			if($contratInsertion["Contratinsertion"]["decision_ci"] == 'V' && $contratInsertion["Contratinsertion"]["datevalidation_ci"] != null) {
				$this->cumulDuree += $contratInsertion["Contratinsertion"]["duree_engag"];

				if($this->finPlacePrecedente == '' || $contratInsertion["Contratinsertion"]["dd_ci"]>=$this->finPlacePrecedente) {
					if($this->cumulDuree <= $dureeMaximaleCER) {
						$dureeCER = $this->cumulDuree;
					}
					else {
						$dureeCER = $this->cumulDuree - $dureeMaximaleCER;
						$this->cumulDuree = $dureeCER;
					}
				}
				else {//on doit déterminer le nombre de mois commun entre le contrat précédent et le contrat actuel
					$diffMois	=	$this->getNbMoisEntre2Dates($contratInsertion["Contratinsertion"]["dd_ci"], $this->finPlacePrecedente);
					$this->cumulDuree -=  $diffMois;
					$dureeCER = $this->cumulDuree;
				}

				$this->debutPlacePrecedente = $contratInsertion["Contratinsertion"]["dd_ci"];
				$this->finPlacePrecedente = $contratInsertion["Contratinsertion"]["df_ci"];
			}

			if($dureeCER == 0) {
				$dureeCER = '';
			}
			else {
				$dureeCER .= ' mois';
			}

			return $dureeCER;
		}

		/**
		 * Calcul la durée cumulée des CER
		 * En incluant les plages de dates communes
		 * Nouveau système de calcul définit par le 66 le 18/12/2018
		 *
		 * @param object $contratInsertion Infos du CER
		 */
		private function getDureeCERVersion2($contratInsertion) {
			$dureeCER = 0;
			$dureeTemp = array_keys ($this->Option->duree_engag ());
			$dureeMaximaleCER = array_pop ($dureeTemp);

			//si un contrat est validé
			//ne fonctionne pas s'il est annulé ou en attente de décision
			if($contratInsertion["Contratinsertion"]["decision_ci"] == 'V' && $contratInsertion["Contratinsertion"]["datevalidation_ci"] != null) {

				$dureeCER = $contratInsertion["Contratinsertion"]["duree_engag"] + $this->cumulDuree;

				if($this->finPlacePrecedente != '' && $contratInsertion["Contratinsertion"]["dd_ci"]<$this->finPlacePrecedente) {
					//on doit déterminer le nombre de mois commun entre le contrat précédent et le contrat actuel
					$diffMois	=	$this->getNbMoisEntre2Dates($contratInsertion["Contratinsertion"]["dd_ci"], $this->finPlacePrecedente);
					$dureeCER -= $diffMois;
					$this->cumulDuree -=  $diffMois;
				}

				$this->cumulDuree += $contratInsertion["Contratinsertion"]["duree_engag"];

				if($this->cumulDuree>=$dureeMaximaleCER)
					$this->cumulDuree = 0;

				$this->debutPlacePrecedente = $contratInsertion["Contratinsertion"]["dd_ci"];
				$this->finPlacePrecedente = $contratInsertion["Contratinsertion"]["df_ci"];
			}

			$dureeCER = ($dureeCER == 0) ? '' : $dureeCER.' mois';

			return $dureeCER;
		}

		/**
		 * Calcul le nombre de mois entre 2 dates
		 *
		 * @param date $dateDebut Date de début du CER
		 * @param date $dateFin Date de fin du CER précédent
		 */
		private function getNbMoisEntre2Dates ($dateDebut, $dateFin) {
			$dtDeb = new DateTime($dateDebut);
			$dtFin = new DateTime($dateFin);
			$interval = $dtDeb->diff($dtFin);
			$nbmonth= $interval->format('%m');
			$nbyear = $interval->format('%y');
			return 12 * $nbyear + $nbmonth;
		}

		/**
		 * Visualisation d'un CER en particulier (CG 58, 66, 93).
		 *
		 * @param integer $contratinsertion_id
		 */
		public function view($contratinsertion_id = null) {
			$this->WebrsaAccesses->check($contratinsertion_id);
			$query = array(
				'fields' => array_merge(
						$this->Contratinsertion->fields(),
						$this->Contratinsertion->Action->fields(),
						$this->Contratinsertion->Actioninsertion->fields(),
						$this->Contratinsertion->Propodecisioncer66->fields(),
						array(
							$this->Contratinsertion->Referent->sqVirtualField( 'nom_complet' ),
							'Structurereferente.lib_struc',
							'Typeorient.lib_type_orient',
							$this->Contratinsertion->Personne->sqVirtualField( 'nom_complet' ),
							$this->Contratinsertion->Propodecisioncer66->Motifcernonvalid66Propodecisioncer66->Motifcernonvalid66->vfListeMotifs( 'Propodecisioncer66.id', '', ', ' ).' AS "Propodecisioncer66__listeMotifs66"'
					)
				),
				'joins' => array(
					$this->Contratinsertion->join( 'Action', array( 'type' => 'LEFT OUTER' ) ),
					$this->Contratinsertion->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					$this->Contratinsertion->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Contratinsertion->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
					$this->Contratinsertion->Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) ),
					$this->Contratinsertion->join( 'Actioninsertion', array( 'type' => 'LEFT OUTER' ) ),
					$this->Contratinsertion->join( 'Propodecisioncer66', array( 'type' => 'LEFT OUTER' ) ),
				),
				'conditions' => array(
					'Contratinsertion.id' => $contratinsertion_id
				),
				'recursive' => -1,
				'contain' => false
			);

			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$query['fields'][] = 'Contratinsertion.num_contrat_66';
			}

			$contratinsertion = $this->Contratinsertion->find( 'first', $query );

			$this->assert(!empty($contratinsertion), 'invalidParameter');

			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $contratinsertion['Contratinsertion']['personne_id'])));

			// Utilisé pour les détections de fiche de candidature pour savoir si des actions sont en cours ou non
			$fichescandidature = $this->Contratinsertion->Personne->ActioncandidatPersonne->find(
					'all', array(
				'conditions' => array(
					'ActioncandidatPersonne.personne_id' => $contratinsertion['Contratinsertion']['personne_id'],
					'ActioncandidatPersonne.positionfiche = \'encours\'',
				),
				'contain' => array(
					'Actioncandidat' => array(
						'Contactpartenaire' => array(
							'Partenaire'
						)
					),
					'Referent'
				)
					)
			);

			$this->_setOptions();
			$this->set(compact('contratinsertion', 'fichescandidature'));
			$this->set('personne_id', $contratinsertion['Contratinsertion']['personne_id']);
			$this->set('urlmenu', '/contratsinsertion/index/' . $contratinsertion['Contratinsertion']['personne_id']);

			// Retour à la liste en cas d'annulation
			if (isset($this->request->data['Cancel'])) {
				$this->redirect(array('action' => 'index', $contratinsertion['Contratinsertion']['personne_id']));
			}
		}

		/**
		 * Formulaire d'ajout d'un CER (CG 58, 66, 93).
		 *
		 * @param integer $personne_id
		 */
		public function add($personne_id = null) {
			$this->WebrsaAccesses->check(null, $personne_id);
			$args = func_get_args();
			call_user_func_array(array($this, '_add_edit'), $args);
		}

		/**
		 * Formulaire modification d'un CER (CG 58, 66, 93).
		 *
		 * @param integer $id
		 */
		public function edit($id = null) {
			$this->WebrsaAccesses->check($id);
			$args = func_get_args();
			call_user_func_array(array($this, '_add_edit'), $args);
		}

		/**
		 * Récupération des données socio pro (notamment Niveau etude) lié au
		 * contrat (CG 93).
		 *
		 * @param type $personne_id
		 * @return type
		 */
		protected function _getDsp($personne_id) {
			$this->Contratinsertion->Personne->Dsp->unbindModelAll();
			$dsp = $this->Contratinsertion->Personne->Dsp->find(
					'first', array(
				'fields' => array(
					'Dsp.id',
					'Dsp.personne_id',
					'Dsp.nivetu',
					'Dsp.nivdipmaxobt',
					'Dsp.annobtnivdipmax',
				),
				'conditions' => array(
					'Dsp.personne_id' => $personne_id
				),
				'recursive' => -1
					)
			);

			if (empty($dsp)) {
				$dsp = array('Dsp' => array('personne_id' => $personne_id));

				$this->Contratinsertion->Personne->Dsp->set($dsp);
				if ($this->Contratinsertion->Personne->Dsp->save( $dsp, array( 'atomic' => false ) )) {
					$qd_dsp = array(
						'conditions' => array(
							'Dsp.personne_id' => $personne_id
						),
						'fields' => null,
						'order' => null,
						'recursive' => -1
					);
					$dsp = $this->Contratinsertion->Personne->Dsp->find('first', $qd_dsp);
				} else {
					$this->cakeError('error500');
				}
				$this->assert(!empty($dsp), 'error500');
			}

			$return = array();
			$return['Dsp'] = array(
				'id' => $dsp['Dsp']['id'],
				'personne_id' => $dsp['Dsp']['personne_id']
			);
			$return['Dsp']['nivetu'] = ( ( isset($dsp['Dsp']['nivetu']) ) ? $dsp['Dsp']['nivetu'] : null );
			$return['Dsp']['nivdipmaxobt'] = ( ( isset($dsp['Dsp']['nivdipmaxobt']) ) ? $dsp['Dsp']['nivdipmaxobt'] : null );
			$return['Dsp']['annobtnivdipmax'] = ( ( isset($dsp['Dsp']['annobtnivdipmax']) ) ? $dsp['Dsp']['annobtnivdipmax'] : null );

			return $return;
		}

		/**
		 * Formulaire d'ajout ou de modification d'un CER (CG 58, 66, 93).
		 *
		 * INFO: 521 lignes @20120928.15:52
		 *
		 * @param integer $id
		 */
		protected function _add_edit($id = null) {
			$this->assert(!empty($id), 'invalidParameter');

			$valueFormeci = null;
			if ($this->action == 'add') {
				$personne_id = $id;

				// TODO: $this->request->data Contratinsertion.forme_ci
				$valueFormeci = 'S';

				// TODO: $this->request->data Contratinsertion.num_contrat
				$nbContratsPrecedents = $this->Contratinsertion->find('count', array('recursive' => -1, 'conditions' => array('Contratinsertion.personne_id' => $personne_id)));
				if ($nbContratsPrecedents >= 1) {
					$tc = 'REN';
				} else {
					$tc = 'PRE';
				}
			} else if ($this->action == 'edit') {
				$contratinsertion = $this->Contratinsertion->find(
						'first', array(
					'conditions' => array(
						'Contratinsertion.id' => $id
					),
					'contain' => array(
						'Autreavissuspension',
						'Autreavisradiation',
					)
						)
				);
				$this->assert(!empty($contratinsertion), 'invalidParameter');

				$personne_id = $contratinsertion['Contratinsertion']['personne_id'];

				// TODO: $this->request->data Contratinsertion.forme_ci
				$valueFormeci = Set::classicExtract($contratinsertion, 'Contratinsertion.forme_ci');

				$tc = Set::classicExtract($contratinsertion, 'Contratinsertion.num_contrat');
			}

			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id)));

			// Récupération de l'id du dossier
			$dossier_id = $this->Contratinsertion->Personne->dossierId($personne_id);
			$this->assert(!empty($dossier_id), 'invalidParameter');
	//			$this->set( 'dossier_id', $dossier_id );
			// Tentative d'acquisition du jeton sur le dossier
			$this->Jetons2->get($dossier_id);

			// Retour à la liste en cas d'annulation (on relache le jeton sur le dossier)
			if (isset($this->request->data['Cancel'])) {
				$this->Jetons2->release($dossier_id);
				$this->redirect(array('action' => 'index', $personne_id));
			}

			/**
			 *   Utilisé pour les dates de suspension et de radiation
			 *   Si les dates ne sont pas présentes en base, elles ne seront pas affichées
			 *   Situation dossier rsa : dtclorsa -> date de radiation
			 *   Suspension droit : ddsusdrorsa -> date de suspension
			 *
			 * CG 66 et 93
			 */
			// TODO: $this->request->data Contratinsertion.dateradiationparticulier et Contratinsertion.datesuspensionparticulier
			$situationdossierrsa = $this->Contratinsertion->Personne->Foyer->Dossier->Situationdossierrsa->find(
					'first', array(
				'conditions' => array(
					'Situationdossierrsa.dossier_id' => $dossier_id
				),
				'contain' => array(
					'Suspensiondroit' => array(
						'fields' => array(
							'Suspensiondroit.ddsusdrorsa'
						),
						'order' => 'Suspensiondroit.ddsusdrorsa DESC',
						'limit' => 1
					)
				)
					)
			);

			// On ajout l'ID de l'utilisateur connecté afin de récupérer son service instructeur
			$personne = $this->Contratinsertion->Personne->WebrsaPersonne->newDetailsCi($personne_id, $this->Session->read('Auth.User.id'));

			/// Calcul du numéro du contrat d'insertion
			$nbrCi = $this->Contratinsertion->find('count', array('conditions' => array('Personne.id' => $personne_id)));

			$numouverturedroit = $this->Contratinsertion->WebrsaContratinsertion->checkNumDemRsa($personne_id);

			//$this->set( 'nbContratsPrecedents', $nbContratsPrecedents );
			$this->set('tc', $tc);
			$this->set(compact('situationdossierrsa'));
			$this->set('personne', $personne);
			$this->set('numouverturedroit', $numouverturedroit);
			$this->set('valueFormeci', $valueFormeci);

			// Utilisé pour les détections de fiche de candidature pour savoir
			// si des actions sont en cours ou non, (CG 66, affichage)
			if (Configure::read('Cg.departement') == 66) {
				$fichescandidature = $this->Contratinsertion->Personne->ActioncandidatPersonne->find(
						'all', array(
					'conditions' => array(
						'ActioncandidatPersonne.personne_id' => $personne_id,
						'ActioncandidatPersonne.positionfiche = \'encours\''
					),
					'contain' => array(
						'Actioncandidat' => array(
							'Contactpartenaire' => array(
								'Partenaire'
							)
						),
						'Referent'
					)
						)
				);
				$this->set(compact('fichescandidature'));

				$cersPrecedents = $this->Contratinsertion->find(
						'all', array(
					'fields' => array_merge(
							$this->Contratinsertion->Actioncandidat->fields(), $this->Contratinsertion->Actioncandidat->Contactpartenaire->fields(), $this->Contratinsertion->Actioncandidat->Contactpartenaire->Partenaire->fields(), $this->Contratinsertion->Referent->fields(), array(
						'Contratinsertion.id',
						'Contratinsertion.actioncandidat_id'
							)
					),
					'conditions' => array(
						'Contratinsertion.personne_id' => $personne_id
					),
					'joins' => array(
						$this->Contratinsertion->join('Actioncandidat', array('type' => 'INNER')),
						$this->Contratinsertion->Actioncandidat->join('Contactpartenaire', array('type' => 'LEFT OUTER')),
						$this->Contratinsertion->Actioncandidat->Contactpartenaire->join('Partenaire', array('type' => 'LEFT OUTER')),
						$this->Contratinsertion->join('Referent', array('type' => 'LEFT OUTER')),
					),
					'contain' => false
						)
				);
				$action = null;
				foreach ($cersPrecedents as $i => $cerPrecedent) {
					$action = $cerPrecedent;
				}
				$this->set('action', $action);
			}

			/// Essai de sauvegarde
			if (!empty($this->request->data)) { // INFO: 168 lignes @20120928.16:09
				$this->Contratinsertion->begin();

				if ($this->action == 'add') {
					$this->request->data['Contratinsertion']['rg_ci'] = $nbrCi + 1;
				}

				if (Configure::read('nom_form_ci_cg') == 'cg58') {
					$this->request->data['Contratinsertion']['forme_ci'] = 'S';
					$this->request->data['Contratinsertion']['datevalidation_ci'] = Set::classicExtract($this->request->data, 'Contratinsertion.dd_ci');
				}

				$contratinsertionRaisonCi = Set::classicExtract($this->request->data, 'Contratinsertion.raison_ci');
				if ($contratinsertionRaisonCi == 'S') {
					$this->request->data['Contratinsertion']['avisraison_ci'] = Set::classicExtract($this->request->data, 'Contratinsertion.avisraison_suspension_ci');
				} else if ($contratinsertionRaisonCi == 'R') {
					$this->request->data['Contratinsertion']['avisraison_ci'] = Set::classicExtract($this->request->data, 'Contratinsertion.avisraison_radiation_ci');
				}

				/**
				 *   Utilisé pour les dates de suspension et de radiation
				 *   Si les dates ne sont pas présentes en base, elles ne seront pas affichées
				 *   Situation dossier rsa : dtclorsa -> date de radiation
				 *   Suspension droit : ddsusdrorsa -> date de suspension
				 */
				if (isset($situationdossierrsa)) {
					if (!empty($situationdossierrsa['Situationdossierrsa']['dtclorsa'])) {
						$this->request->data['Contratinsertion']['dateradiationparticulier'] = $situationdossierrsa['Situationdossierrsa']['dtclorsa'];
					}
					if (!empty($situationdossierrsa['Suspensiondroit'][0]['ddsusdrorsa'])) {
						$this->request->data['Contratinsertion']['datesuspensionparticulier'] = $situationdossierrsa['Suspensiondroit'][0]['ddsusdrorsa'];
					}
				}

				// Si Contratinsertion.objetcerprecautre est disabled, on enregistre null
				$this->request->data = Set::merge(array('Contratinsertion' => array('objetcerprecautre' => null)), $this->request->data);

				$this->Contratinsertion->create($this->request->data);
				$success = $this->Contratinsertion->save( null, array( 'atomic' => false ) );

				// Enregistrement des DSP (CG 93)
				if (Configure::read('nom_form_ci_cg') == 'cg93') {
					$dspStockees = $this->_getDsp($personne_id);
					$this->request->data['Dsp'] = Set::merge(
									isset($dspStockees['Dsp']) ? Hash::filter((array) $dspStockees['Dsp']) : array(), isset($this->request->data['Dsp']) ? Hash::filter((array) $this->request->data['Dsp']) : array()
					);

					$isDsp = Hash::filter((array) $this->request->data['Dsp']);
					if (!empty($isDsp)) {
						$success = $this->Contratinsertion->Personne->Dsp->save( array( 'Dsp' => $this->request->data['Dsp'] ), array( 'atomic' => false ) ) && $success;
					}
				}

				// Sauvegarde des numéros de téléphone si ceux-ci ne sont pas présents en amont (CG 66)
				if (isset($this->request->data['Personne'])) {
					$isDataPersonne = Hash::filter((array) $this->request->data['Personne']);
					if (!empty($isDataPersonne)) {
						$success = $this->Contratinsertion->Personne->save( array( 'Personne' => $this->request->data['Personne'] ), array( 'atomic' => false ) ) && $success;
					}
				}

				// CGs 66, 93
				$models = array('Autreavissuspension', 'Autreavisradiation');
				foreach ($models as $model) {
					if ($this->action == 'add') {
						$this->{$this->modelClass}->{$model}->set('contratinsertion_id', $this->{$this->modelClass}->id);
					} else if ($this->action == 'edit') {
						$this->Contratinsertion->{$model}->deleteAll(array("{$model}.contratinsertion_id" => $this->Contratinsertion->id));
					}

					if (isset($this->request->data[$model])) {
						$is{$model} = Hash::filter((array) $this->request->data[$model]);
						if (!empty($is{$model})) {
							$Autresavis = Set::extract($is{$model}, "/{$model}");
							$data = array($model => array());

							foreach ($Autresavis as $i => $Autreavis) {
								$data[$model][] = array(
									'contratinsertion_id' => $this->Contratinsertion->id,
									strtolower($model) => $Autreavis
								);
							}
							$success = $this->Contratinsertion->{$model}->saveAll($data[$model], array('atomic' => false)) && $success;
						}
					}
				}

				// CG 93
				if (isset($this->request->data['Actioninsertion'])) {
					$isActioninsertion = Hash::filter((array) $this->request->data['Actioninsertion']);
					$this->{$this->modelClass}->Actioninsertion->set('contratinsertion_id', $this->{$this->modelClass}->id);

					if (!empty($isActioninsertion)) {
						$success = $this->Contratinsertion->Actioninsertion->save( array( 'Actioninsertion' => $this->request->data['Actioninsertion'] ), array( 'atomic' => false ) ) && $success;
					}
				}

				// Un contrat complexe est directement envoyé en EP (CG 93)
				if (Configure::read('Cg.departement') == 93 && $this->request->data['Contratinsertion']['forme_ci'] == 'C') {
					$dossierep = array(
						'Dossierep' => array(
							'themeep' => 'contratscomplexeseps93',
							'personne_id' => $personne_id
						)
					);

					$this->Contratinsertion->Personne->Dossierep->create($dossierep);
					$tmpSuccess = $this->Contratinsertion->Personne->Dossierep->save( null, array( 'atomic' => false ) );

					// Sauvegarde des données de la thématique
					if ($tmpSuccess) {
						$contratcomplexeep93 = array(
							'Contratcomplexeep93' => array(
								'dossierep_id' => $this->Contratinsertion->Personne->Dossierep->id,
								'contratinsertion_id' => $this->Contratinsertion->id
							)
						);

						$this->Contratinsertion->Personne->Dossierep->Contratcomplexeep93->create($contratcomplexeep93);
						$success = $this->Contratinsertion->Personne->Dossierep->Contratcomplexeep93->save( null, array( 'atomic' => false ) ) && $success;
					}
					$success = $success && $tmpSuccess;
				}

				if ($success) {
					$saved = true;

					// Au 66, si on enregistre un CER pour l'allocataire, on passe le statut de son RDV
					// "01 - Convocation à un Entretien - Contrat" de "Prévu" à "Venu(e)"
					if (Configure::read('Cg.departement') == 66) {
						$cg66Rendezvous = Configure::read('Contratinsertion.Cg66.Rendezvous');
						$lastrdvorient = $this->Contratinsertion->Referent->Rendezvous->find(
								'first', array(
							'fields' => array(
								'Rendezvous.id'
							),
							'conditions' => array(
								'Rendezvous.typerdv_id' => $cg66Rendezvous['conditions']['typerdv_id'],
								'Rendezvous.personne_id' => $this->request->data['Contratinsertion']['personne_id'],
								'Rendezvous.statutrdv_id' => $cg66Rendezvous['conditions']['statutrdv_id']
							),
							'contain' => false,
							'order' => array('Rendezvous.daterdv DESC')
								)
						);

						if (!empty($lastrdvorient)) {
							$lastrdvorient['Rendezvous']['statutrdv_id'] = $cg66Rendezvous['statutrdv_id'];
							$saved = $this->Contratinsertion->Referent->Rendezvous->save( $lastrdvorient, array( 'atomic' => false ) ) && $saved;
						}
					}

					if ($saved) {
						$this->Contratinsertion->commit();
						$this->Jetons2->release($dossier_id);
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect(array('controller' => 'contratsinsertion', 'action' => 'index', $personne_id));
					} else {
						$this->Contratinsertion->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				} else {
					$this->Contratinsertion->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			} else { // Préparation des données du formulaire ...: prepareFormData ?
				if ($this->action == 'edit') {
					$this->request->data = $contratinsertion;

					// CG 93
					$actioninsertion = $this->Contratinsertion->Actioninsertion->find(
							'first', array(
						'conditions' => array(
							'Actioninsertion.contratinsertion_id' => $contratinsertion['Contratinsertion']['id'],
							'Actioninsertion.dd_action IS NOT NULL'
						),
						'recursive' => -1,
						'order' => array('Actioninsertion.dd_action DESC')
							)
					);
					$this->request->data['Actioninsertion'] = Hash::get($actioninsertion, 'Actioninsertion');

					// Suspension / Radiation (CG 66, 93)
					if ($this->request->data['Contratinsertion']['raison_ci'] == 'S') {
						$this->request->data['Contratinsertion']['avisraison_suspension_ci'] = $this->request->data['Contratinsertion']['avisraison_ci'];
					} else if ($this->request->data['Contratinsertion']['raison_ci'] == 'R') {
						$this->request->data['Contratinsertion']['avisraison_radiation_ci'] = $this->request->data['Contratinsertion']['avisraison_ci'];
					}

					// Si on est en présence d'un deuxième contrat -> Alors renouvellement
					$nbrCi = $contratinsertion['Contratinsertion']['rg_ci'];
				}

				// CG 93
				$this->request->data = Set::merge($this->request->data, $this->_getDsp($personne_id));
			}

			$this->set('nbrCi', $nbrCi);

			// Doit-on setter les valeurs par défault ?
			$dataStructurereferente_id = Set::classicExtract($this->request->data, "{$this->Contratinsertion->alias}.structurereferente_id");
			$dataReferent_id = Set::classicExtract($this->request->data, "{$this->Contratinsertion->alias}.referent_id");

			// Si le formulaire ne possède pas de valeur pour ces champs, on met celles par défaut
			if (empty($dataStructurereferente_id) && empty($dataReferent_id)) {
				// Recherche du type d'orientation
				$orientstruct = $this->Contratinsertion->Structurereferente->Orientstruct->find(
						'first', array(
					'conditions' => array(
						'Orientstruct.personne_id' => $personne_id,
						'Orientstruct.typeorient_id IS NOT NULL',
						'Orientstruct.statut_orient' => 'Orienté'
					),
					'order' => 'Orientstruct.date_valid DESC',
					'recursive' => -1
						)
				);

				// Référent du parcours
				$personne_referent = $this->Contratinsertion->Personne->PersonneReferent->find(
						'first', array(
					'conditions' => array(
						'PersonneReferent.personne_id' => $personne_id,
						'PersonneReferent.dfdesignation IS NULL'
					),
					'recursive' => -1
						)
				);

				$structurereferente_id = $referent_id = null;
				// Valeur par défaut préférée: à partir de personnes_referents
				if (!empty($personne_referent)) {
					$structurereferente_id = Set::classicExtract($personne_referent, "{$this->Contratinsertion->Personne->PersonneReferent->alias}.structurereferente_id");
					$referent_id = Set::classicExtract($personne_referent, "{$this->Contratinsertion->Personne->PersonneReferent->alias}.referent_id");
				}
				// Valeur par défaut de substitution: à partir de orientsstructs
				else if (!empty($orientstruct)) {
					$structurereferente_id = Set::classicExtract($orientstruct, "{$this->Contratinsertion->Personne->Orientstruct->alias}.structurereferente_id");
					$referent_id = Set::classicExtract($orientstruct, "{$this->Contratinsertion->Personne->Orientstruct->alias}.referent_id");
				}

				if (!empty($structurereferente_id)) {
					$this->request->data = Hash::insert($this->request->data, "{$this->Contratinsertion->alias}.structurereferente_id", $structurereferente_id);
				}

				if (!empty($structurereferente_id) && !empty($referent_id)) {
					$this->request->data = Hash::insert($this->request->data, "{$this->Contratinsertion->alias}.referent_id", preg_replace('/^_$/', '', "{$structurereferente_id}_{$referent_id}"));
				}
			}

			// Ajout des listes de strctures référentes et de référents
			if (Configure::read('Cg.departement') == 66) {
				// TODO: grep -nr "Configure::read.*Orientstruct\.typeorientprincipale" app | grep -v "\.svn"
				$typeOrientPrincipaleEmploiId = Configure::read('Orientstruct.typeorientprincipale.Emploi');
				if (is_array($typeOrientPrincipaleEmploiId) && isset($typeOrientPrincipaleEmploiId[0])) {
					$typeOrientPrincipaleEmploiId = $typeOrientPrincipaleEmploiId[0];
				} else {
					trigger_error(__('Le type orientation principale Emploi n\'est pas bien défini.'), E_USER_WARNING);
				}

				$structures = $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'conditions' => array( 'Typeorient.parentid <>' => $typeOrientPrincipaleEmploiId ) + $this->InsertionsBeneficiaires->conditions['structuresreferentes'], 'prefix' => false ) );

				//On affiche les actions inactives en édition mais pas en ajout,
				// afin de pouvoir gérer les actions n'étant plus prises en compte mais toujours en cours
				$isactive = 'O';
				if ($this->action == 'edit') {
					$isactive = array('O', 'N');
				}
				$actionsSansFiche = $this->{$this->modelClass}->Actioncandidat->listePourFicheCandidature(null, $isactive, array('0', '1'));
				$this->set('actionsSansFiche', $actionsSansFiche);
			} else {
				$structures = $this->Contratinsertion->Structurereferente->listOptions();
			}

			$referents = $this->Contratinsertion->Referent->WebrsaReferent->listOptions();

			$struct_id = Set::classicExtract($this->request->data, 'Contratinsertion.structurereferente_id');
			// FIXME: $this->request->data Contratinsertion.structurereferente_id
			$this->set('struct_id', $struct_id);

			$referent_id = Set::classicExtract($this->request->data, 'Contratinsertion.referent_id');
			$referent_id = preg_replace('/^[0-9]+_([0-9]+)$/', '\1', $referent_id);
			// TODO: $this->request->data Contratinsertion.referent_id
			$this->set('referent_id', $referent_id);

			// CG 66
			if (!empty($referent_id) && !empty($this->request->data['Contratinsertion']['referent_id'])) {
				$contratinsertionReferentId = preg_replace('/^[0-9]+_([0-9]+)$/', '\1', $this->request->data['Contratinsertion']['referent_id']);
				$referent = $this->Contratinsertion->Structurereferente->Referent->find(
						'first', array(
					'fields' => array(
						'Referent.email',
						'Referent.fonction',
						'Referent.nom',
						'Referent.prenom',
						'Referent.numero_poste',
					),
					'conditions' => array(
						'Referent.id' => $contratinsertionReferentId
					),
					'recursive' => -1
						)
				);

				$this->set('ReferentNom', $referent['Referent']['nom'] . ' ' . $referent['Referent']['prenom']);
			}

			if ( Configure::read( 'Cg.departement' ) == 66 && isset($personne_id) ) {
				$entretiens = $this->Contratinsertion->Personne->Entretien->find( 'all', $this->Contratinsertion->Personne->Entretien->queryEntretiens( $personne_id )	);
				$this->set( compact( 'entretiens' ) );
			}

			$this->_setOptions();
			$this->set(compact('structures', 'referents'));
			$this->set('urlmenu', '/contratsinsertion/index/' . $personne_id);

			//récupère le cumul des CER
			$querydata = Hash::merge(
				$this->Contratinsertion->WebrsaContratinsertion->qdIndex($personne_id),
				array(
					'fields' => array(
						'(SELECT COUNT(*) FROM fichiersmodules AS a WHERE a.modele = \'Contratinsertion\' AND a.fk_value = "Contratinsertion"."id") AS "Fichiermodule__count"',
						'Contratinsertion.personne_id'
					)
				)
			);
			$querydata['contain'] = false;
			$contratsinsertion = $this->WebrsaAccesses->getIndexRecords($personne_id, $querydata);

			// Date de la dernière EP
			//$dateLastEpParcours = $this->_dateLastEpParcours($personne_id, $contratsinsertion);
			$tempContratsinsertion = $contratsinsertion;
			$dateFinDernierContrat = array_pop ($tempContratsinsertion);

			//plus de notions de date EP pour le moment
			//$dureeTotalCER = $this->getDureeTotalCERPostLastEP($contratsinsertion, $dateLastEpParcours);
			$dureeTotalCER = $this->getDureeTotalCERVersion2($contratsinsertion);
			$infosPersonne = $this->Personne->find('first', array('recursive'=>(-1), 'conditions'=>array('Personne.id'=>$personne_id)));
			$agePersonne = $infosPersonne['Personne']['age'];
			$this->set(compact('dureeTotalCER', 'agePersonne'));

			$duree_engag = $this->Option->duree_engag();

			//$isEpParcoursAfterLastCer = $this->_isEpParcoursAfterLastCer($dateLastEpParcours, $dateFinDernierContrat['Contratinsertion']['dd_ci']);
			$tabDureeEngag = $this->setDureeEngag($duree_engag, $dureeTotalCER, $agePersonne);
			$this->set('duree_engag', $duree_engag);
			$dureeTmp = array_keys ($duree_engag);
			$this->set('dureeMaximaleTrancheContrat', array_pop ($dureeTmp));
			$this->set('tabDureeEngag', $tabDureeEngag);
			//$this->set('isEpParcoursAfterLastCer', $isEpParcoursAfterLastCer);

			$this->render( 'add_edit_specif_'.Configure::read( 'nom_form_ci_cg' ) );
		}

		/**
		 * Calcul le cumul total des CER, et renvoi le nombre de mois restants par plage de X mois (24 mois par défaut)
		 * Exemple : si le cumule est de 30mois, il renvoi 18mois encore possible
		 *
		 * @param object $CER
		 */
		private function getDureeTotalCERPostLastEP($CER, $dateLastEpParcours) {
			$dureeTotalCER = 0;

            //initialisation, car la variable de classe peut être utilisée par une autre fonction
			$this->finPlacePrecedente = '';

			foreach($CER as $index=>$value) {
				if($value["Contratinsertion"]["decision_ci"] == 'V' && $value["Contratinsertion"]["datevalidation_ci"] != null) {
					// Les périodes après la dernière EP 
					if ($dateLastEpParcours <= $value["Contratinsertion"]["dd_ci"]
						|| ($dateLastEpParcours >= $value["Contratinsertion"]["dd_ci"] && $dateLastEpParcours <= $value["Contratinsertion"]["df_ci"])) {

						// Si l'EP est entre les dates
						if ($dateLastEpParcours >= $value["Contratinsertion"]["dd_ci"] && $dateLastEpParcours <= $value["Contratinsertion"]["df_ci"]) {
							$value["Contratinsertion"]["duree_engag"] = $this->getNbMoisEntre2Dates($value["Contratinsertion"]["df_ci"], $dateLastEpParcours);
							$value["Contratinsertion"]["dd_ci"] = $dateLastEpParcours;
						}

						$dureeTotalCER += $value["Contratinsertion"]["duree_engag"];

						if($this->finPlacePrecedente != '' && $value["Contratinsertion"]["dd_ci"]<=$this->finPlacePrecedente) {
							//on doit déterminer le nombre de mois commun entre le contrat précédent et le contrat actuel
							$diffMois	=	$this->getNbMoisEntre2Dates($value["Contratinsertion"]["dd_ci"], $this->finPlacePrecedente);
							$dureeTotalCER -=  $diffMois;
						}

						$this->debutPlacePrecedente = $value["Contratinsertion"]["dd_ci"];
						$this->finPlacePrecedente = $value["Contratinsertion"]["df_ci"];
					}
				}
			}

			return $dureeTotalCER;
		}

		/**
		 * Définition de la liste déroulante possible pour la durée d'ajout d'un CER
		 *
		 * @param array $duree_engag
		 */
		protected function _isEpParcoursAfterLastCer ($dateLastEpParcours, $dateFinDernierContrat) {
			if (!is_null ($dateFinDernierContrat) && !is_null ($dateLastEpParcours)) {
				$datetimeFinDernierContrat = new DateTime($dateFinDernierContrat);
				$datetimeLastEpParcours = new DateTime($dateLastEpParcours);

				$interval = $datetimeFinDernierContrat->diff($datetimeLastEpParcours);

				// La date de la dernière Équipe Pluridisciplinaire EST APRÈS la date de fin de dernier de contrat
				if ($interval->format('%R') !== '+') {
					return true;
				}
			}

			return false;
		}

		/**
		 * Définition de la liste déroulante possible pour la durée d'ajout d'un CER
		 *
		 * @param array $duree_engag
		 */
		protected function _dateLastEpParcours ($personne_id, $contratsinsertion) {
			$dateFinDernierContrat = array_pop ($contratsinsertion);

			$Passagecommissionep = ClassRegistry::init ('Passagecommissionep');
			$passage = $Passagecommissionep->find (
				'first',
				array (
					'recursive' => 1,
					'joins' => array (
						array(
							'alias' => 'Decisiondefautinsertionep66',
							'table' => 'decisionsdefautsinsertionseps66',
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Decisiondefautinsertionep66.passagecommissionep_id = Passagecommissionep.id',
							)
						),
						array(
							'alias' => 'Decisionsaisinebilanparcoursep66',
							'table' => 'decisionssaisinesbilansparcourseps66',
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Decisionsaisinebilanparcoursep66.passagecommissionep_id = Passagecommissionep.id',
							)
						),
						array(
							'alias' => 'Decisionssaisinespdoseps66',
							'table' => 'decisionssaisinespdoseps66',
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Decisionssaisinespdoseps66.passagecommissionep_id = Passagecommissionep.id',
							)
						),
						array(
							'alias' => 'Decisionsnonorientationsproseps66',
							'table' => 'decisionsnonorientationsproseps66',
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Decisionsnonorientationsproseps66.passagecommissionep_id = Passagecommissionep.id',
							)
						)
					),
					'conditions' => array (
						'Dossierep.personne_id' => $personne_id,
						'Dossierep.themeep' => array (
								'saisinesbilansparcourseps66',
								'nonorientationsproseps66',
								'defautsinsertionseps66',
								'saisinespdoseps66',
							),
						'Passagecommissionep.etatdossierep' => 'traite',
					),
				)
			);

			if (isset ($passage['Commissionep']['dateseance'])) {
				return $passage['Commissionep']['dateseance'];
			}

			return null;
		}

		/**
		 * Définition de la liste déroulante possible pour la durée d'ajout d'un CER
		 *
		 * @param array $duree_engag
		 */
		protected function setDureeEngag ($duree_engag, $dureeTotalCER, $agePersonne, $isEpParcoursAfterLastCer = false) {
			// Pas de limite de contrat si l'allocataire a plus que l'age de tacite reconduction (55 ans).
			if ($agePersonne >= Configure::read( 'Tacitereconduction.limiteAge' )) {
				return $duree_engag;
			}

			// Pas de limite de contrat si l'allocataire est passé en EP PARCOURS et si la date de décision
			// de l'EP est postérieure à la date de fin du dernier CER
			if ($isEpParcoursAfterLastCer) {
				return $duree_engag;
			}

			// La différence entre le plus long contrat et le temps de contrat déjà effectué pour une tranche
			// Plus d'actualité pour le moment seulement, laisser le code pour le moment.
/*
			$dureeCerMax = array_pop (array_keys ($duree_engag)) - $dureeTotalCER;
			foreach ($duree_engag as $key => $value) {
				if ($key > $dureeCerMax) {
					unset ($duree_engag[$key]);
				}
			}
*/
			return $duree_engag;
		}

		/**
		 * Calcul le cumul total des CER, et renvoi le nombre de mois restants par plage de X mois (24 mois par défaut)
		 * Exemple : si le cumule est de 30mois, il renvoi 18mois encore possible
		 *
		 * @param object $CER
		 */
		private function getDureeTotalCER($CER) {
			$dureeTotalCER = 0;

            //initialisation, car la variable de classe peut être utilisée par une autre fonction
			$this->finPlacePrecedente = '';

			foreach($CER as $index=>$value) {
				if($value["Contratinsertion"]["decision_ci"] == 'V' && $value["Contratinsertion"]["datevalidation_ci"] != null) {
					$dureeTotalCER += $value["Contratinsertion"]["duree_engag"];

					if($this->finPlacePrecedente != '' && $value["Contratinsertion"]["dd_ci"]<=$this->finPlacePrecedente) {
						//on doit déterminer le nombre de mois commun entre le contrat précédent et le contrat actuel
						$diffMois	=	$this->getNbMoisEntre2Dates($value["Contratinsertion"]["dd_ci"], $this->finPlacePrecedente);
						$dureeTotalCER -=  $diffMois;
					}

					$this->debutPlacePrecedente = $value["Contratinsertion"]["dd_ci"];
					$this->finPlacePrecedente = $value["Contratinsertion"]["df_ci"];
				}
			}

			return ($dureeTotalCER % Configure::read( 'cer.duree.tranche' ));
		}

		/**
		 * Calcul le cumul total des CER, et renvoi le nombre de mois restants par plage de X mois (24 mois par défaut)
		 * Exemple : si le cumule est de 30mois, il renvoi 18mois encore possible
		 *
		 * @param object $CER
		 */
		private function getDureeTotalCERVersion2($CER) {
			$dureeTotalCER = 0;
			$dureeTmp = array_keys ($this->Option->duree_engag ());
			$dureeMaximaleCER = array_pop ($dureeTmp);
			$finPlacePrecedente = '';

			foreach($CER as $index=>$value) {
				if($value["Contratinsertion"]["decision_ci"] == 'V' && $value["Contratinsertion"]["datevalidation_ci"] != null) {

					if($finPlacePrecedente != '' && $value["Contratinsertion"]["dd_ci"]<$finPlacePrecedente) {
						//on doit déterminer le nombre de mois commun entre le contrat précédent et le contrat actuel
						$diffMois	=	$this->getNbMoisEntre2Dates($value["Contratinsertion"]["dd_ci"], $finPlacePrecedente);
						$dureeTotalCER -=  $diffMois;
					}

					$dureeTotalCER += $value["Contratinsertion"]["duree_engag"];

					if($dureeTotalCER>=$dureeMaximaleCER)
						$dureeTotalCER = 0;

					$finPlacePrecedente = $value["Contratinsertion"]["df_ci"];
				}
			}

			return $dureeTotalCER;
		}

		/**
		 * Formulaire de validation d'un CER (CG 66, 93).
		 *
		 * @param integer $contratinsertion_id
		 */
		public function valider($contratinsertion_id = null) {
			if (Configure::read('Cg.departement') == 66) {
				$fields = array(
					'Contratinsertion.id',
					'Contratinsertion.personne_id',
					'Contratinsertion.structurereferente_id',
					'Contratinsertion.forme_ci',
					'Contratinsertion.observ_ci',
					'Contratinsertion.datevalidation_ci',
					'Contratinsertion.datedecision',
					'Contratinsertion.decision_ci',
					'Contratinsertion.positioncer',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
					'Contratinsertion.duree_engag',
					'Propodecisioncer66.isvalidcer',
					'Propodecisioncer66.datevalidcer',
					$this->Contratinsertion->Referent->sqVirtualField('nom_complet'),
				);
				$contain = array(
					'Propodecisioncer66',
					'Referent'
				);
			} else {
				$fields = array(
					'Contratinsertion.id',
					'Contratinsertion.personne_id',
					'Contratinsertion.structurereferente_id',
					'Contratinsertion.forme_ci',
					'Contratinsertion.observ_ci',
					'Contratinsertion.datevalidation_ci',
					'Contratinsertion.decision_ci',
					'Contratinsertion.positioncer',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci'
				);
				$recursive = -1;
				$contain = false;
			}

			$contratinsertion = $this->Contratinsertion->find(
					'first', array(
				'fields' => $fields,
				'conditions' => array(
					'Contratinsertion.id' => $contratinsertion_id
				),
				'contain' => $contain
					)
			);

			$this->assert(!empty($contratinsertion), 'invalidParameter');
			$this->set('contratinsertion', $contratinsertion);

			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $contratinsertion['Contratinsertion']['personne_id'])));

			$dossier_id = $this->Contratinsertion->dossierId($contratinsertion_id);
			$this->Jetons2->get($dossier_id);

			$this->set('personne_id', $contratinsertion['Contratinsertion']['personne_id']);

			// Retour à la liste en cas d'annulation
			if (!empty($this->request->data) && isset($this->request->data['Cancel'])) {
				$this->Jetons2->release($dossier_id);
				$this->redirect(array('action' => 'index', $contratinsertion['Contratinsertion']['personne_id']));
			}

			if (!empty($this->request->data)) {
				if ($this->Contratinsertion->WebrsaContratinsertion->valider($this->request->data)) {
					$this->Jetons2->release($dossier_id);
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('controller' => 'contratsinsertion', 'action' => 'index', $contratinsertion['Contratinsertion']['personne_id']));
				}
			} else {
				$this->request->data = $contratinsertion;
			}

			$this->_setOptions();
			$this->set('urlmenu', '/contratsinsertion/index/' . $contratinsertion['Contratinsertion']['personne_id']);
			$this->render('valider');
		}

		/**
		 * *Fonction de validation pour les CERs Simples (CG 66).
		 *
		 * @param type $contratinsertion_id
		 *
		 */
		public function validersimple($contratinsertion_id = null) {
			$this->Contratinsertion->id = $contratinsertion_id;
			$forme_ci = $this->Contratinsertion->field('forme_ci');
			$this->assert(( $forme_ci == 'S'), 'error500');

			$this->valider($contratinsertion_id);
		}

		/**
		 * Fonction de validation pour les CERs Particuliers (CG 66).
		 *
		 * @param type $contratinsertion_id
		 *
		 */
		public function validerparticulier($contratinsertion_id = null) {
			$this->Contratinsertion->id = $contratinsertion_id;
			$forme_ci = $this->Contratinsertion->field('forme_ci');
			$this->assert(( $forme_ci == 'C'), 'error500');

			$this->valider($contratinsertion_id);
		}

		/**
		 * Suppression d'un CER (CG 58, 93).
		 *
		 * @param integer $id
		 */
		public function delete($id) {
			$dossier_id = $this->Contratinsertion->dossierId($id);
			$this->DossiersMenus->checkDossierMenu(array('id' => $dossier_id));
			$this->WebrsaAccesses->check($id);

			$this->Jetons2->get($dossier_id);

			$this->{$this->modelClass}->begin();
			$success = $this->{$this->modelClass}->Actioninsertion->deleteAll(array('Actioninsertion.contratinsertion_id' => $id));
			$success = $this->{$this->modelClass}->delete($id) && $success;

			if ($success) {
				$this->{$this->modelClass}->commit();
				$this->Flash->success( __( 'Delete->success' ) );
				$this->Jetons2->release($dossier_id);
			} else {
				$this->{$this->modelClass}->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->redirect($this->referer());
		}

		/**
		 * Fonction pour annuler le CER (CG 66).
		 *
		 * @param type $id
		 */
		public function cancel($id) {
			$qd_contrat = array(
				'conditions' => array(
					$this->modelClass . '.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$contrat = $this->{$this->modelClass}->find('first', $qd_contrat);

			$personne_id = Set::classicExtract($contrat, 'Contratinsertion.personne_id');
			$this->set('personne_id', $personne_id);

			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id)));
			$this->WebrsaAccesses->check($id);

			$dossier_id = $this->Contratinsertion->dossierId($id);
			$this->Jetons2->get($dossier_id);

			// Retour à la liste en cas d'annulation
			if (!empty($this->request->data) && isset($this->request->data['Cancel'])) {
				$this->Jetons2->release($dossier_id);
				$this->redirect(array('action' => 'index', $personne_id));
			}

			// Dans ce contexte-ci, la raison de l'annulation est obligatoire
			$this->Contratinsertion->validate['motifannulation'][NOT_BLANK_RULE_NAME] = array(
				'rule' => array( NOT_BLANK_RULE_NAME ),
				'message' => 'Champ obligatoire'
			);

			if (!empty($this->request->data)) {
				$this->Contratinsertion->begin();

				$this->request->data['Contratinsertion']['positioncer'] = 'annule';
				$this->request->data['Contratinsertion']['decision_ci'] = 'A';

				$saved = $this->Contratinsertion->save( $this->request->data, array( 'atomic' => false ) );

				$saved = $saved && $this->Contratinsertion->WebrsaContratinsertion->updatePositionsCersByConditions(
					array( 'Contratinsertion.personne_id' => $contrat['Contratinsertion']['personne_id'] )
				);

				if ($saved) {
					$this->Contratinsertion->commit();
					$this->Jetons2->release($dossier_id);
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('action' => 'index', $personne_id));
				} else {
					$this->Contratinsertion->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			} else {
				$this->request->data = $contrat;
			}
			$this->set('urlmenu', '/contratsinsertion/index/' . $personne_id);
		}

		/**
		 * Retourn le PDF de notification d'un CER pour l'OP (CG 66).
		 *
		 * @param integer $id L'id du CER pour lequel générer la notification.
		 * @return void
		 */
		public function notificationsop($contratinsertion_id = null) {
			$personne_id = $this->Contratinsertion->personneId($contratinsertion_id);
			$this->DossiersMenus->checkDossierMenu(array('personne_id' => $personne_id));
			$this->WebrsaAccesses->check($contratinsertion_id);

			$pdf = $this->Contratinsertion->WebrsaContratinsertion->getNotificationopPdf($contratinsertion_id, $this->Session->read('Auth.User.id'));

			if (!empty($pdf)) {
				$this->Gedooo->sendPdfContentToClient($pdf, sprintf("contratinsertion_%d_notificationop_%s.pdf", $contratinsertion_id, date('Y-m-d')));
			} else {
				$this->Flash->error( 'Impossible de générer la notification du CER pour l\'OP.' );
				$this->redirect($this->referer());
			}
		}

		/**
		 * Impression de la fiche de liaison d'un CER (CG 66).
		 *
		 * @param integer $contratinsertion_id
		 * @return void
		 */
		public function ficheliaisoncer($contratinsertion_id) {
			$personne_id = $this->Contratinsertion->personneId($contratinsertion_id);
			$this->DossiersMenus->checkDossierMenu(array('personne_id' => $personne_id));
			$this->WebrsaAccesses->check($contratinsertion_id);

			$pdf = $this->Contratinsertion->WebrsaContratinsertion->getPdfFicheliaisoncer($contratinsertion_id, $this->Session->read('Auth.User.id'));

			if (!empty($pdf)) {
				$this->Gedooo->sendPdfContentToClient($pdf, "contratinsertion_{$contratinsertion_id}_FicheLiaison.pdf");
			} else {
				$this->Flash->error( 'Impossible de générer la fiche de liaison' );
				$this->redirect($this->referer());
			}
		}

		/**
		 * Impression d'une notification pour le bénéficiaire concernant une
		 * proposition de décision d'un CER  (CG 66).
		 *
		 * @param integer $id
		 * @return void
		 */
		public function notifbenef($contratinsertion_id) {
			$personne_id = $this->Contratinsertion->personneId($contratinsertion_id);
			$this->DossiersMenus->checkDossierMenu(array('personne_id' => $personne_id));
			$this->WebrsaAccesses->check($contratinsertion_id);

			$pdf = $this->Contratinsertion->WebrsaContratinsertion->getPdfNotifbenef($contratinsertion_id, $this->Session->read('Auth.User.id'));

			if (!empty($pdf)) {
				$this->Gedooo->sendPdfContentToClient($pdf, "contratinsertion_{$contratinsertion_id}_NotificationBeneficiaire_.pdf");
			} else {
				$this->Flash->error( 'Impossible de générer la notification du bénéficiaire' );
				$this->redirect($this->referer());
			}
		}

		/**
		 * Imprime un CER (CG 58, 66, 93).
		 * INFO: http://localhost/webrsa/trunk/contratsinsertion/impression/44327
		 * FIXME: ajouter une colonne de date de première impression ?
		 *
		 * @param integer $contratinsertion_id
		 * @return void
		 */
		public function impression($contratinsertion_id = null) {
			$personne_id = $this->Contratinsertion->personneId($contratinsertion_id);
			$this->DossiersMenus->checkDossierMenu(array('personne_id' => $personne_id));
			$this->WebrsaAccesses->check($contratinsertion_id);

			$pdf = $this->Contratinsertion->WebrsaContratinsertion->getDefaultPdf($contratinsertion_id, $this->Session->read('Auth.User.id'));

			if (!empty($pdf)) {
				$this->Gedooo->sendPdfContentToClient($pdf, "contratinsertion_{$contratinsertion_id}_nouveau.pdf");
			} else {
				$this->Flash->error( 'Impossible de générer le courrier de contrat d\'insertion.' );
				$this->redirect($this->referer());
			}
		}

		/**
		 * Fonction permettant d'enregistrer la date de la notification au
		 * bénéficiaire (CG 66).
		 *
		 * @param type $id
		 */
		public function notification($id) {
			$this->assert(!empty($id), 'error404');

			$contratinsertion = $this->Contratinsertion->find(
					'first', array(
				'conditions' => array(
					'Contratinsertion.id' => $id
				),
				'contain' => false
					)
			);

			$this->assert(!empty($contratinsertion), 'invalidParameter');
			$this->set('contratinsertion', $contratinsertion);

			$personne_id = $contratinsertion['Contratinsertion']['personne_id'];
			$this->set('personne_id', $personne_id);

			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id)));
			$this->WebrsaAccesses->check($id);

			$dossier_id = $this->Contratinsertion->Personne->dossierId($personne_id);
			$this->assert(!empty($dossier_id), 'invalidParameter');

			$this->Jetons2->get($dossier_id);

			// Retour à l'index en cas d'annulation
			if (isset($this->request->data['Cancel'])) {
				$this->Jetons2->release($dossier_id);
				$this->redirect(array('action' => 'index', $personne_id));
			}

			if (!empty($this->request->data)) {
				$this->Contratinsertion->begin();

				$datenotification = $this->request->data['Contratinsertion']['datenotification'];
				$saved = $this->Contratinsertion->updateAllUnBound(
						array('Contratinsertion.datenotification' => "'{$datenotification['year']}-{$datenotification['month']}-{$datenotification['day']}'"), array(
					'"Contratinsertion"."personne_id"' => $personne_id,
					'"Contratinsertion"."id"' => $id
						)
				);

				$saved = $saved && $this->Contratinsertion->WebrsaContratinsertion->updatePositionsCersByConditions(
					array( 'Contratinsertion.personne_id' => $personne_id )
				);

				if ($saved) {
					$this->Contratinsertion->commit();
					$this->Jetons2->release($dossier_id);
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('controller' => 'contratsinsertion', 'action' => 'index', $personne_id));
				} else {
					$this->Contratinsertion->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			} else {
				$this->request->data = $contratinsertion;
			}

			$this->set('urlmenu', '/contratsinsertion/index/' . $contratinsertion['Contratinsertion']['personne_id']);
			$this->render('notification');
		}

		/**
		 * Impression d'une notification pour les bénéficiaires de + 55ans (CG 66).
		 *
		 * @param integer $id
		 * @return void
		 */
		public function reconduction_cer_plus_55_ans($contratinsertion_id) {
			$personne_id = $this->Contratinsertion->personneId($contratinsertion_id);
			$this->DossiersMenus->checkDossierMenu(array('personne_id' => $personne_id));
			$this->WebrsaAccesses->check($contratinsertion_id);

			$pdf = $this->Contratinsertion->WebrsaContratinsertion->getPdfReconductionCERPlus55Ans($contratinsertion_id, $this->Session->read('Auth.User.id'));

			$success = true;
			if (!empty($pdf)) {
				$success = $this->Contratinsertion->updateAllUnBound(
								array('Contratinsertion.datetacitereconduction' => date("'Y-m-d'")), array(
							'"Contratinsertion"."id"' => $contratinsertion_id,
							'"Contratinsertion"."datetacitereconduction" IS NULL'
								)
						) && $success;
				$this->Gedooo->sendPdfContentToClient($pdf, "taciteReconductionPlus55ans.pdf");
			} else {
				$this->Flash->error( 'Impossible de générer la notification du bénéficiaire' );
			}
			$this->redirect($this->referer());
		}

		/**
		 * Moteur de recherche
		 */
		public function search() {
			$this->helpers[] = 'Search.SearchForm';

			$Recherches = $this->Components->load( 'WebrsaRecherchesContratsinsertion' );
			$Recherches->search();
			$this->Contratinsertion->validate = array();
			$this->Contratinsertion->Structurereferente->Orientstruct->validate = array();
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesContratsinsertion' );
			$Recherches->exportcsv();
		}

		/**
		 * Cohorte de validation de CER
		 */
		public function cohorte_nouveaux() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesContratsinsertionNouveaux' );

			$Cohortes->cohorte(
				array(
					'modelName' => 'Contratinsertion',
					'modelRechercheName' => 'WebrsaCohorteContratinsertionNouveau'
				)
			);
		}

		/**
		 * Cohorte de CER validés
		 */
		public function cohorte_valides() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesContratsinsertionValides' );
			$Cohortes->search(
				array(
					'modelName' => 'Contratinsertion',
					'modelRechercheName' => 'WebrsaCohorteContratinsertionValide'
				)
			);
		}

		/**
		 * Cohorte de CER validés
		 */
		public function exportcsv_valides() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesContratsinsertionValides' );
			$Cohortes->exportcsv(
				array(
					'modelName' => 'Contratinsertion',
					'modelRechercheName' => 'WebrsaCohorteContratinsertionValide'
				)
			);
		}

		/**
		 * Cohorte
		 */
		public function cohorte_cersimpleavalider() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesContratsinsertion' );
			$Cohortes->cohorte( array( 'modelRechercheName' => 'WebrsaCohorteContratinsertionCersimpleavalider' ) );
		}

		/**
		 * Export CSV
		 */
		public function exportcsv_cersimpleavalider() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesContratsinsertion' );
			$Cohortes->exportcsv( array( 'modelRechercheName' => 'WebrsaCohorteContratinsertionCersimpleavalider' ) );
		}

		/**
		 * Cohorte
		 */
		public function cohorte_cerparticulieravalider() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesContratsinsertion' );
			$Cohortes->cohorte( array( 'modelRechercheName' => 'WebrsaCohorteContratinsertionCersimpleavalider' ) );
			$this->view = 'cohorte_cersimpleavalider';
		}

		/**
		 * Export CSV
		 */
		public function exportcsv_cerparticulieravalider() {
			$Cohortes = $this->Components->load( 'WebrsaCohortesContratsinsertion' );
			$Cohortes->exportcsv( array( 'modelRechercheName' => 'WebrsaCohorteContratinsertionCersimpleavalider' ) );
		}

		/**
		 * Cohorte
		 */
		public function search_valides() {
			$Recherche = $this->Components->load( 'WebrsaRecherchesContratsinsertion' );
			$Recherche->search( array( 'modelRechercheName' => 'WebrsaRechercheContratinsertionValides' ) );
			$this->Contratinsertion->validate = array();
		}

		/**
		 * Export CSV
		 */
		public function exportcsv_search_valides() {
			$Recherche = $this->Components->load( 'WebrsaRecherchesContratsinsertion' );
			$Recherche->exportcsv( array( 'modelRechercheName' => 'WebrsaRechercheContratinsertionValides' ) );
		}
	}
?>
