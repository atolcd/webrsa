<?php
	/**
	 * Code source de la classe RecoursgracieuxController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'LocaleHelper', 'View/Helper' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'WebrsaAccessRecoursgracieux', 'Utility' );

	/**
	 * La classe RecoursgracieuxController ...
	 *
	 * @package app.Controller
	 */
	class RecoursgracieuxController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Recoursgracieux';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes',
			'Gedooo.Gedooo',
			'Default',
			'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses',
			'Fileuploader'
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Locale',
			'Paginator',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Cake1xLegacy.Ajax',
			'Search',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read',
			'view' => 'read',
			'add' => 'create',
			'edit' => 'update',
			'affecter' => 'create',
			'email' => 'update',
			'proposer' => 'update',
			'proposer_contestation' => 'update',
			'proposer_remise' => 'update',
			'deleteproposition' => 'delete',
			'decider' => 'update',
			'envoyer' => 'update',
			'traiter' => 'update',
			'delete' => 'delete',
			'search' => 'read',
			'filelink' => 'read',
			'fileview' => 'read',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'ajaxreffonct' => 'read',
			'exportcsv' => 'read'
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Recourgracieux',
			'WebrsaRecourgracieux',
			'Originerecoursgracieux',
			'Typerecoursgracieux',
			'Creancerecoursgracieux',
			'Motifproposrecoursgracieux',
			'Dossier',
			'Foyer',
			'Personne',
			'User',
			'Option',
			'Historiqueetat',
			'Dossierpcg66'
			);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		*/
		public $aucunDroit = array(
			'updateEtatPCGTraiter',
			'ajaxfiledelete',
			'ajaxfileupload',
			'ajaxreffonct',
			'download',
			'fileview'
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'delete' => 'Recoursgracieux:add',
			'view' => 'Recoursgracieux:index'
		);

		/**
		 * Moteur de recherche par Recoursgracieux
		 *
		 * @return void
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesRecoursgracieux' );
			$Recherches->search();
		}

		/**
		 * Pagination sur les <éléments> de la table. *
		 * @param integer $foyer_id L'id technique du Foyer pour lequel on veut les Recoursgracieux.
		 *
		 */
		public function index($foyer_id) {
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id )));
			$this->set( 'options', $this->Recourgracieux->enums() );

			$recoursgracieux = $this->WebrsaAccesses->getIndexRecords(
				$foyer_id,
				array(
					'fields' => array_merge(
						$this->Recourgracieux->fields(),
						 array(
							$this->Recourgracieux->Fichiermodule->sqNbFichiersLies( $this->Recourgracieux, 'nb_fichiers_lies' )
						)
					),
					'conditions' => array(
						'Recourgracieux.foyer_id' => $foyer_id
					),
					'contain' => false,
					'order' => array(
						'Recourgracieux.dtarrivee DESC',
					)
				)
			);
			if ( !empty($recoursgracieux) ){
				foreach ($recoursgracieux as $key => $recourgracieux) {
					$recoursgracieux[$key]['Recourgracieux']['etatDepuis'] =
						__d('recourgracieux', 'ENUM::ETAT::'
							.$recoursgracieux[$key]['Recourgracieux']['etat'])
							.__m('since')
							.date('d/m/Y', strtotime( $recoursgracieux[$key]['Recourgracieux']['modified'] )
						);
				}
			}

			// Historique du titre - si suppression
			$histoDeleted = $this->Historiqueetat->getHisto($this->Recourgracieux->name, '*', 'delete', $foyer_id);

			// Assignations à la vue
			$this->set( 'options', $this->Recourgracieux->options()	);
			$this->set( 'histoDeleted', $histoDeleted );
			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'recoursgracieux', $recoursgracieux );
			$this->set( 'urlmenu', '/recoursgracieux/index/'.$foyer_id );
		}

		/**
		 * Pagination sur les <éléments> de la table. *
		 * @param integer $recourgracieux_id L'id technique du recours à affiché
		 *
		 */
		public function view($recourgracieux_id) {
			$this->WebrsaAccesses->check($recourgracieux_id);
			$this->Recourgracieux->id = $recourgracieux_id;
			$foyer_id = $this->Recourgracieux->field( 'foyer_id' );

			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id )));

			$recoursgracieux = $this->WebrsaAccesses->getIndexRecords(
				$recourgracieux_id,
				array(
					'fields' => array_merge(
						$this->Recourgracieux->fields(),
						array(
							$this->Recourgracieux->Fichiermodule->sqNbFichiersLies( $this->Recourgracieux, 'nb_fichiers_lies' )
						)
					),
					'conditions' => array(
						'Recourgracieux.id' => $recourgracieux_id
					),
					'contain' => false,
					'order' => array(
						'Recourgracieux.created DESC',
					)
				)
			);
			if ( !empty($recoursgracieux) ){
				$typerecoursgracieux = $this->Recourgracieux->Typerecoursgracieux->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Typerecoursgracieux->fields()
						),
						'conditions' => array(
							'Typerecoursgracieux.id' => $recoursgracieux[0]['Recourgracieux']['typerecoursgracieux_id']
						),
						'contain' => FALSE
					)
				);
				foreach ($recoursgracieux as $key => $recourgracieux) {
					$recoursgracieux[$key]['Recourgracieux']['etatDepuis'] =
						__d('recourgracieux', 'ENUM::ETAT::'
							.$recoursgracieux[$key]['Recourgracieux']['etat'])
							.__m('since')
							.date('d/m/Y', strtotime( $recoursgracieux[$key]['Recourgracieux']['modified'] )
						);
				}
				$propositions = $this->Creancerecoursgracieux->find(
						'all',
						array(
							'fields' => array_merge(
								$this->Creancerecoursgracieux->fields()
							),
							'conditions' => array(
								'Creancerecoursgracieux.recours_id' => $recourgracieux_id
							),
						)
					);
			}

			//ListMotifs
			$listMotifs = $this->Motifproposrecoursgracieux->find(
				'list',
				array(
					'fields' => array ('id', 'nom')
				)
			);
			$this->set( 'listMotifs', $listMotifs );

			// Historique de la créance
			$historiques = $this->Historiqueetat->getHisto($this->Recourgracieux->name, $recourgracieux_id, null, $foyer_id);

			foreach($historiques as $key => $histo ) {
				$historiques[$key]['Historiqueetat']['etat'] = (__d('recourgracieux', 'ENUM::ETAT::' . $histo['Historiqueetat']['etat']));
			}

			// Assignation à la vue

			//Liste des Pièces jointes
			$piecesjointes = $this->getPiecejointes( $recourgracieux_id );
			$this->set( 'piecesjointes', $piecesjointes );

			$this->set( 'options', array_merge(
					$this->Recourgracieux->options(),
					$this->Recourgracieux->Foyer->Creance->enums(),
					$this->Recourgracieux->Foyer->Creance->Titrecreancier->enums()
				)
			);
			$this->set( 'historiques', $historiques );
			$this->set( 'foyer_id', $foyer_id );
			$this->set( 'recoursgracieux', $recoursgracieux );
			$this->set( 'typerecoursgracieux', $typerecoursgracieux );
			$this->set( 'propositions', $propositions );
			$this->set( 'urlmenu', '/recoursgracieux /view/'.$recourgracieux_id );
		}

		/**
		 * Ajouter un recours gracieux à un foyer
		 *
		 * @param integer $foyer_id L'id technique du foyer auquel ajouter le recours gracieux
		 * @return void
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Modification d'un recours gracieux du foyer.
		 *
		 * @param integer $id L'id technique dans la table recoursgracieux.
		 * @return void
		 */
		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Fonction commune d'ajout/modification d'un recours gracieux
		 *
		 * @param integer $id
		 * 		Soit l'id technique du foyer auquel ajouter le recours gracieux
		 * 		Soit l'id technique dans la table recoursgracieux a éditer.
		 * @return void
		 */
		protected function _add_edit($id = null) {
			if($this->action == 'add' ) {
				$foyer_id = $id;
				$id = null;
				$dossier_id = $this->Recourgracieux->Foyer->dossierId( $foyer_id );
			}elseif($this->action == 'edit' ){
				$this->WebrsaAccesses->check($id);
				$this->Recourgracieux->id = $id;
				$foyer_id = $this->Recourgracieux->field( 'foyer_id' );
				$dossier_id = $this->Recourgracieux->dossierId( $id );
			}

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Recourgracieux->begin();
				$data = $this->request->data;
				if($this->action == 'add' ) {
					$data['Recourgracieux']['foyer_id'] = $foyer_id;
				}
				$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
				if( $this->Recourgracieux->saveAll( $data, array( 'validate' => 'only' ) ) &&
					$this->Recourgracieux->save( $data ) &&
					$this->Historiqueetat->setHisto(
						$this->Recourgracieux->name,
						$this->Recourgracieux->id,
						$foyer_id,
						__FUNCTION__,
						$data['Recourgracieux']['etat'],
						$foyer_id
					) &&
					$this->Fileuploader->saveFichiers( $dir,FALSE, $this->Recourgracieux->id )
				){
					$this->Recourgracieux->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'Recoursgracieux', 'action' => 'index', $foyer_id ) );
				} else {
					$this->Recourgracieux->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			// Affichage des données
			elseif( $this->action == 'edit' ) {
				$recoursgracieux = $this->Recourgracieux->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Recourgracieux->fields()
						),
						'conditions' => array(
							'Recourgracieux.id' => $id
						),
						'contain' => FALSE
					)
				);
				if (!empty( $recoursgracieux ) ){
					// Assignation au formulaire
					$this->request->data = $recoursgracieux;
				}
				//Liste des Pièces jointes
				$piecesjointes = $this->getPiecejointes( $id );
				$this->set( 'piecesjointes', $piecesjointes );
			}elseif( $this->action == 'add' ){
				// Assignation au formulaire
				$recoursgracieux['Recourgracieux']['dtbutoir'] = date('Y-m-d', strtotime( '+1 Month' )) ;
				$this->request->data = $recoursgracieux;

				//Liste des Pièces jointes
				$piecesjointes = array();
				$this->set( 'piecesjointes', $piecesjointes );
			}

			// Assignation à la vue
			$this->set( 'options',$this->Recourgracieux->options() );
			$this->set( 'urlmenu', '/recoursgracieux/index/'.$foyer_id );
			$this->set( 'foyer_id', $foyer_id );
			$this->render( 'add_edit' );

		}

		/**
		 * Fonction de gestion des email
		 *
		 * @param integer $id L'id technique dans la table recoursgracieux.
		 * @return void
		 *
		 */
		public function email($id = null) {

			$this->WebrsaAccesses->check($id);
			$this->Recourgracieux->id = $id;
			$foyer_id = $this->Recourgracieux->field( 'foyer_id' );

			$this->set( 'urlmenu', '/recoursgracieux/index/'.$foyer_id );
			$this->set( 'foyer_id', $foyer_id );
		}

		/**
		 * Fonction de gestion des affectations
		 *
		 * @param integer $id L'id technique dans la table recoursgracieux.
		 * @return void
		 *
		 */
		public function affecter($id = null) {

			$this->WebrsaAccesses->check($id);
			$this->Recourgracieux->id = $id;
			$foyer_id = $this->Recourgracieux->field( 'foyer_id' );
			$dossier_id = $this->Recourgracieux->dossierId( $id );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Recourgracieux->begin();
				$data = $this->request->data;

				if( $this->Recourgracieux->saveAll( $data, array( 'validate' => 'only' ) ) &&
					$this->Recourgracieux->save( $data ) &&
					$this->Historiqueetat->setHisto(
						$this->Recourgracieux->name,
						$this->Recourgracieux->id,
						$foyer_id,
						__FUNCTION__,
						$data['Recourgracieux']['etat'],
						$foyer_id
					) ) {
					$this->Recourgracieux->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'Recoursgracieux', 'action' => 'index', $foyer_id ) );
				} else {
					$this->Recourgracieux->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			// Affichage des données
			$recoursgracieux = $this->Recourgracieux->find(
				'first',
				array(
					'fields' => array_merge(
						$this->Recourgracieux->fields()
					),
					'conditions' => array(
						'Recourgracieux.id' => $id
					),
					'contain' => FALSE
				)
			);
			if (!empty( $recoursgracieux ) ){
				// Assignation au formulaire
				$this->request->data = $recoursgracieux;
			}

			// Assignation à la vue
			$options = $this->Recourgracieux->options();
			$options = $this->User->Poledossierpcg66->WebrsaPoledossierpcg66->completeOptions(
				$options,
				array($recoursgracieux),
				array('prefix' => true )
			);
			$this->set( 'options',$options );
			$this->set( 'urlmenu', '/recoursgracieux/index/'.$foyer_id );
			$this->set( 'foyer_id', $foyer_id );
		}

		/**
		 * Fonction de gestion des propositions
		 *
		 * @param integer $id L'id technique dans la table recoursgracieux.
		 * @return void
		 *
		 */
		public function proposer($id = null) {
			$this->WebrsaAccesses->check($id);
			$this->Recourgracieux->id = $id;
			$foyer_id = $this->Recourgracieux->field( 'foyer_id' );
			$dossier_id = $this->Recourgracieux->dossierId( $id );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Recourgracieux->begin();
				$data = $this->request->data;

				//A traité -> instructionencours
				if ( $data['Recourgracieux']['encours'] ){
					$data['Recourgracieux']['etat'] = 'INSTRUCTION' ;
				}elseif (
					$data['Recourgracieux']['etat'] == 'INSTRUCTION'
					&& !$data['Recourgracieux']['encours']
				) {
					$data['Recourgracieux']['etat'] = 'ATTVALIDATION';
				}

				if( $this->Recourgracieux->saveAll( $data, array( 'validate' => 'only' ) ) &&
					$this->Recourgracieux->save( $data ) &&
					$this->Historiqueetat->setHisto(
						$this->Recourgracieux->name,
						$this->Recourgracieux->id,
						$foyer_id,
						__FUNCTION__,
						$data['Recourgracieux']['etat'],
						$foyer_id
					)) {
					$this->Recourgracieux->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'Recoursgracieux', 'action' => 'index', $foyer_id ) );
				} else {
					$this->Recourgracieux->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}else {
				// Affichage des données
				$recoursgracieux = $this->Recourgracieux->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Recourgracieux->fields()
						),
						'conditions' => array(
							'Recourgracieux.id' => $id
						),
						'contain' => FALSE
					)
				);

				if (!empty( $recoursgracieux ) ){
					// Affichage des données
					$typerecoursgracieux = $this->Recourgracieux->Typerecoursgracieux->find(
						'first',
						array(
							'fields' => array_merge(
								$this->Typerecoursgracieux->fields()
							),
							'conditions' => array(
								'Typerecoursgracieux.id' => $recoursgracieux['Recourgracieux']['typerecoursgracieux_id']
							),
							'contain' => FALSE
						)
					);
					// Assignation au formulaire
					$this->request->data = array_merge( $recoursgracieux, $typerecoursgracieux);

					$propositions = $this->Creancerecoursgracieux->find(
						'all',
						array(
							'fields' => array_merge(
								$this->Creancerecoursgracieux->fields()
							),
							'conditions' => array(
								'Creancerecoursgracieux.recours_id' => $id
							),
						)
					);
					if ( ! empty ($propositions) ) {
						foreach ($propositions as $key => $proposition ) {
							$propositions[$key]['Creancerecoursgracieux']['perioderegucre'] =
								__m('Recourgracieux::proposer::Periode').
								$proposition['Creancerecoursgracieux']['ddregucre'].
								__m('Recourgracieux::proposer::to').
								$proposition['Creancerecoursgracieux']['dfregucre'];
						}
					}
					//Assignation au formulaire
					$this->set( 'creancesrecoursgracieux',$propositions );

					$creances = $this->Recourgracieux->Foyer->Creance->find(
						'all',
						array(
							'recursive' => 0,
							'fields' => array_merge(
								$this->Recourgracieux->Foyer->Creance->fields(),
								$this->Recourgracieux->Foyer->Creance->Titrecreancier->fields()
							),
							'conditions' => array(
								'Recourgracieux.id' => $id
							),
							'joins' => array(
								$this->Recourgracieux->Foyer->join('Recourgracieux', array('type'=>'INNER'))
							)
						)
					);
					foreach ($creances as $key => $creance) {
						$creances[$key]['Creance']['perioderegucre'] =
							__m('Recourgracieux::proposer::Periode').
							$creance['Creance']['ddregucre'].
							__m('Recourgracieux::proposer::to').
							$creance['Creance']['dfregucre'];
					}
					//Assignation au formulaire
					$this->set( 'creances',$creances );
				}
			}

			//ListMotifs
			$listMotifs = $this->Motifproposrecoursgracieux->find(
				'list',
				array(
					'fields' => array ('id', 'nom')
				)
			);
			$this->set( 'listMotifs', $listMotifs );
			// Assignation à la vue
			$this->set( 'options', array_merge(
					$this->Recourgracieux->options(),
					$this->Recourgracieux->Foyer->Creance->enums(),
					$this->Recourgracieux->Foyer->Creance->Titrecreancier->enums()
				)
			);
			$this->set( 'urlmenu', '/recoursgracieux/index/'.$foyer_id );
			$this->set( 'foyer_id', $foyer_id );
		}

		/**
		 * Fonction de gestion des propositions_contestation
		 *
		 * @param integer $id L'id technique dans la table recoursgracieux.
		 * @return void
		 *
		 */
		public function proposer_contestation($creance_id = null,$recourgracieux_id = null,$typerecoursgracieux_id = null) {
			$this->WebrsaAccesses->check($recourgracieux_id);
			$this->Recourgracieux->id = $recourgracieux_id;
			$foyer_id = $this->Recourgracieux->field( 'foyer_id' );
			$dossier_id = $this->Recourgracieux->dossierId( $recourgracieux_id );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Creancerecoursgracieux->begin();
				$data = $this->request->data;
				if( $this->Creancerecoursgracieux->saveAll( $data, array( 'validate' => 'only' ) ) &&
					$this->Creancerecoursgracieux->save( $data ) ) {
					$this->Creancerecoursgracieux->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'Recoursgracieux', 'action' => 'proposer', $recourgracieux_id ) );
				} else {
					$this->Recourgracieux->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}else {
				// Affichage des données
				$recoursgracieux = $this->Recourgracieux->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Recourgracieux->fields()
						),
						'conditions' => array(
							'Recourgracieux.id' => $recourgracieux_id
						),
						'contain' => FALSE
					)
				);

				if (!empty( $recoursgracieux ) ){
					// Affichage des données
					$typerecoursgracieux = $this->Recourgracieux->Typerecoursgracieux->find(
						'first',
						array(
							'fields' => array_merge(
								$this->Typerecoursgracieux->fields()
							),
							'conditions' => array(
								'Typerecoursgracieux.id' => $recoursgracieux['Recourgracieux']['typerecoursgracieux_id']
							),
							'contain' => FALSE
						)
					);
					// Assignation au formulaire
					$this->request->data = array_merge( $recoursgracieux, $typerecoursgracieux);

					$creances = $this->Recourgracieux->Foyer->Creance->find(
						'first',
						array(
							'recursive' => 0,
							'fields' => array_merge(
								$this->Recourgracieux->Foyer->Creance->fields(),
								$this->Recourgracieux->Foyer->Creance->Titrecreancier->fields()
							),
							'conditions' => array(
								'Creance.id' => $creance_id
							),
							'joins' => array(
								$this->Recourgracieux->Foyer->join('Recourgracieux', array('type'=>'INNER'))
							)
						)
					);
					$creances['Creance']['perioderegucre'] =
						__m('Recourgracieux::proposer::Periode').
						$creances['Creance']['ddregucre'].
						__m('Recourgracieux::proposer::to').
						$creances['Creance']['dfregucre'];
					//Assignation au formulaire
					$this->set( 'creances',$creances );
				}
			}

			// Assignation à la vue
			//ListMotifs
			$listMotifs = $this->Motifproposrecoursgracieux->find(
				'list',
				array(
					'fields' => array ('id', 'nom')
				)
			);
			$this->set( 'listMotifs', $listMotifs );
			$this->set( 'options', array_merge(
					$this->Recourgracieux->options(),
					$this->Recourgracieux->Foyer->Creance->enums(),
					$this->Recourgracieux->Foyer->Creance->Titrecreancier->enums()
				)
			);
			$this->set( 'urlmenu', '/recoursgracieux/index/'.$foyer_id );
			$this->set( 'foyer_id', $foyer_id );
		}

		/**
		 * Fonction de gestion des proposer_remise
		 *
		 * @param integer $id L'id technique dans la table recoursgracieux.
		 * @return void
		 *
		 */
		public function proposer_remise($creance_id = null,$recourgracieux_id = null,$typerecoursgracieux_id = null) {
			$this->WebrsaAccesses->check($recourgracieux_id);
			$this->Recourgracieux->id = $recourgracieux_id;
			$foyer_id = $this->Recourgracieux->field( 'foyer_id' );
			$dossier_id = $this->Recourgracieux->dossierId( $recourgracieux_id );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Creancerecoursgracieux->begin();
				$data = $this->request->data;
				if( $this->Creancerecoursgracieux->saveAll( $data, array( 'validate' => 'only' ) ) &&
					$this->Creancerecoursgracieux->save( $data ) ) {
					$this->Creancerecoursgracieux->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'Recoursgracieux', 'action' => 'proposer', $recourgracieux_id ) );
				} else {
					$this->Recourgracieux->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}else {
				// Affichage des données
				$recoursgracieux = $this->Recourgracieux->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Recourgracieux->fields()
						),
						'conditions' => array(
							'Recourgracieux.id' => $recourgracieux_id
						),
						'contain' => FALSE
					)
				);

				if (!empty( $recoursgracieux ) ){
					// Affichage des données
					$typerecoursgracieux = $this->Recourgracieux->Typerecoursgracieux->find(
						'first',
						array(
							'fields' => array_merge(
								$this->Typerecoursgracieux->fields()
							),
							'conditions' => array(
								'Typerecoursgracieux.id' => $recoursgracieux['Recourgracieux']['typerecoursgracieux_id']
							),
							'contain' => FALSE
						)
					);
					// Assignation au formulaire
					$this->request->data = array_merge( $recoursgracieux, $typerecoursgracieux);

					$creances = $this->Recourgracieux->Foyer->Creance->find(
						'first',
						array(
							'recursive' => 0,
							'fields' => array_merge(
								$this->Recourgracieux->Foyer->Creance->fields(),
								$this->Recourgracieux->Foyer->Creance->Titrecreancier->fields()
							),
							'conditions' => array(
								'Creance.id' => $creance_id
							),
							'joins' => array(
								$this->Recourgracieux->Foyer->join('Recourgracieux', array('type'=>'INNER'))
							)
						)
					);
					$creances['Creance']['perioderegucre'] =
						__m('Recourgracieux::proposer::Periode').
						$creances['Creance']['ddregucre'].
						__m('Recourgracieux::proposer::to').
						$creances['Creance']['dfregucre'];
					//Assignation au formulaire
					$this->set( 'creances',$creances );
				}
			}

			// Assignation à la vue
			//ListMotifs
			$listMotifs = $this->Motifproposrecoursgracieux->find(
				'list',
				array(
					'fields' => array ('id', 'nom')
				)
			);
			$this->set( 'listMotifs', $listMotifs );
			$this->set( 'options', array_merge(
					$this->Recourgracieux->options(),
					$this->Recourgracieux->Foyer->Creance->enums(),
					$this->Recourgracieux->Foyer->Creance->Titrecreancier->enums()
				)
			);
			$this->set( 'urlmenu', '/recoursgracieux/index/'.$foyer_id );
			$this->set( 'foyer_id', $foyer_id );
		}

		/**
		 * Supprime une proposition d'un recours
		 *
		 * @param integer $id L'id technique du recours gracieux a supprimé
		 * @return void
		 */
		public function deleteproposition($id) {
				$this->WebrsaAccesses->check($id);
				$proposition = $this->Creancerecoursgracieux->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Creancerecoursgracieux->fields()
						),
						'conditions' => array(
							'Creancerecoursgracieux.id' => $id
						),
					)
				);
				$success = $this->Creancerecoursgracieux->delete( $id );
				if( $success ) {
					$this->Flash->success( __( 'Delete->success' ) );
				}
				else {
					$this->Flash->error( __( 'Delete->error' ) );
				}
				$this->redirect( array( 'controller' => 'recoursgracieux', 'action' => 'proposer', $proposition['Creancerecoursgracieux']['recours_id'] ) );
		}

		/**
		 * Fonction de gestion des propositions
		 *
		 * @param integer $id L'id technique dans la table recoursgracieux.
		 * @return void
		 *
		 */
		public function decider($id = null) {
			$this->WebrsaAccesses->check($id);
			$this->Recourgracieux->id = $id;
			$foyer_id = $this->Recourgracieux->field( 'foyer_id' );
			$dossier_id = $this->Recourgracieux->dossierId( $id );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Recourgracieux->begin();
				$data = $this->request->data;
				$save = true;
				if ( $data['Recourgracieux']['validation'] == 1){
					//Si le dossier est en regularisation
					if (
						$data['Recourgracieux']['regularisation'] == 1
						&& $data['Recourgracieux']['etat'] != 'VALIDTRAITEMENT'
					){
						//Si la création de PCGs est active
						if (  Configure::read('Recoursgracieux.PCG.Actifs') ) {
							//On prépare les informations du PCG
							$PCG['Dossierpcg66']['foyer_id'] = $data['Recourgracieux']['foyer_id'];
							$PCG['Dossierpcg66']['etatdossierpcg'] = Configure::read('Recoursgracieux.PCG.Etat');
							$PCG['Dossierpcg66']['typepdo_id'] = Configure::read('Recoursgracieux.PCG.Dossierpcg66TypepdoId');
							$PCG['Dossierpcg66']['originepdo_id'] = Configure::read('Recoursgracieux.PCG.Dossierpcg66OriginepdoId');
							$PCG['Dossierpcg66']['orgpayeur'] = Configure::read('Recoursgracieux.PCG.Dossierpcg66Orgpayeur');
							$PCG['Dossierpcg66']['datereceptionpdo'] = array(
								'day' => date ('d'),
								'month' => date ('m'),
								'year' => date ('Y')
							);
							//On crée le dossier de PCGs
							$this->Dossierpcg66->begin();
							$savePCG = $this->Dossierpcg66->saveAll( $PCG, array( 'validate' => 'first', 'atomic' => false ) );
							if( $savePCG ) {
								//Si le dossier est crée on reporte l'identifiant dans les créances du recours
								$creancesrecoursgracieux = $this->Creancerecoursgracieux->find(
									'all',
									array(
										'fields' => array_merge(
											$this->Creancerecoursgracieux->fields()
										),
										'conditions' => array(
											'Creancerecoursgracieux.recours_id' => $data['Recourgracieux']['id']
										),
									)
								);
								//On insert l'id dans chaque créance liée en régularisation du recours
								foreach ($creancesrecoursgracieux as $key => $creancerecoursgracieux ) {
									if ($creancerecoursgracieux['Creancerecoursgracieux']['regularisation'] == 1 ) {
										$this->Creancerecoursgracieux->begin();
										$creancerecoursgracieux['Creancerecoursgracieux']['dossierpcg_id'] = $this->Dossierpcg66->id ;
										if(
											$this->Creancerecoursgracieux->save( $creancerecoursgracieux )
										) {
											$this->Creancerecoursgracieux->commit();
										}
									}
								}
								//On change l'état du Dossier de Recours
								$data['Recourgracieux']['etat'] = 'VALIDREGUL';
							}else{
								$this->Dossierpcg66->rollback();
							}
							$save = $savePCG ;
						}else{
							$data['Recourgracieux']['etat'] = 'ATTSIGNATURE';
						}
					}else{
						$data['Recourgracieux']['etat'] = 'ATTSIGNATURE';
					}
				}else{
					$data['Recourgracieux']['etat'] = 'ATTINSTRUCTION';
				}
				//Sauvegarde du recours gracieux
				$saveRecoursgracieux = $this->Recourgracieux->saveAll( $data, array( 'validate' => 'only' ) );
				if( $saveRecoursgracieux && $save &&
					$this->Recourgracieux->save( $data ) &&
					$this->Historiqueetat->setHisto(
						$this->Recourgracieux->name,
						$this->Recourgracieux->id,
						$foyer_id,
						__FUNCTION__,
						$data['Recourgracieux']['etat'],
						$foyer_id
					)
				) {
					if (  Configure::read('Recoursgracieux.PCG.Actifs') ) {
						$this->Dossierpcg66->commit();
					}
					$this->Recourgracieux->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'Recoursgracieux', 'action' => 'index', $foyer_id ) );
				} else {
					$this->Recourgracieux->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}else {
				// Affichage des données
				$recoursgracieux = $this->Recourgracieux->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Recourgracieux->fields()
						),
						'conditions' => array(
							'Recourgracieux.id' => $id
						),
						'contain' => FALSE
					)
				);
				if (!empty( $recoursgracieux ) ){
					//Correction Etat
					$recoursgracieux['Recourgracieux']['etatDepuis'] =
						__d('recourgracieux', 'ENUM::ETAT::'
							.$recoursgracieux['Recourgracieux']['etat'])
							.__m('since')
							.date('d/m/Y', strtotime( $recoursgracieux['Recourgracieux']['modified'] )
						);
					// Affichage des données
					$typerecoursgracieux = $this->Recourgracieux->Typerecoursgracieux->find(
						'first',
						array(
							'fields' => array_merge(
								$this->Typerecoursgracieux->fields()
							),
							'conditions' => array(
								'Typerecoursgracieux.id' => $recoursgracieux['Recourgracieux']['typerecoursgracieux_id']
							),
							'contain' => FALSE
						)
					);
					// Assignation au formulaire
					$this->request->data = array_merge( $recoursgracieux, $typerecoursgracieux);

					$propositions = $this->Creancerecoursgracieux->find(
						'all',
						array(
							'fields' => array_merge(
								$this->Creancerecoursgracieux->fields()
							),
							'conditions' => array(
								'Creancerecoursgracieux.recours_id' => $id
							),
						)
					);
					if ( ! empty ($propositions) ) {
						foreach ($propositions as $key => $proposition ) {
							$propositions[$key]['Creancerecoursgracieux']['perioderegucre'] =
								__m('Recourgracieux::proposer::Periode').
								$proposition['Creancerecoursgracieux']['ddregucre'].
								__m('Recourgracieux::proposer::to').
								$proposition['Creancerecoursgracieux']['dfregucre'];
							if ($propositions[$key]['Creancerecoursgracieux']['regularisation'] == 1 ) {
								$this->request->data['Recourgracieux']['regularisation'] = 1;
							}
						}
					}
					//Assignation au formulaire
					$this->set( 'creancesrecoursgracieux',$propositions );
				}
			}

			//ListMotifs
			$listMotifs = $this->Motifproposrecoursgracieux->find(
				'list',
				array(
					'fields' => array ('id', 'nom')
				)
			);
			$this->set( 'listMotifs', $listMotifs );
			// Assignation à la vue
			$this->set( 'options', array_merge(
					$this->Recourgracieux->options(),
					$this->Recourgracieux->Foyer->Creance->enums(),
					$this->Recourgracieux->Foyer->Creance->Titrecreancier->enums()
				)
			);
			$this->set( 'urlmenu', '/recoursgracieux/index/'.$foyer_id );
			$this->set( 'foyer_id', $foyer_id );
		}

		/**
		 * Modifie l'état d'un recours gracieux d'un foyer
		 *
		 * @param integer $id L'id technique du PCG dont l'état as changer
		 * @return void
		 */
		public function updateEtatPCGTraiter($id) {

			$creancrecoursgracieux = $this->Creancerecoursgracieux->find(
				'all',
				array(
					'fields' => array_merge(
						$this->Creancerecoursgracieux->fields()
					),
					'conditions' => array(
						'Creancerecoursgracieux.dossierpcg_id' => $id
					),
				)
			);

			// Affichage des données
			$recoursgracieux = $this->Recourgracieux->find(
				'first',
				array(
					'fields' => array_merge(
						$this->Recourgracieux->fields()
					),
					'conditions' => array(
						'Recourgracieux.id' => $creancrecoursgracieux[0]['Creancerecoursgracieux']['recours_id']
					),
					'contain' => FALSE
				)
			);

			if ($recoursgracieux['Recourgracieux']['etat'] == 'VALIDREGUL' ) {
				$recoursgracieux['Recourgracieux']['etat'] = 'VALIDTRAITEMENT';
				$this->Recourgracieux->begin();
				//Sauvegarde du recours gracieux
				$saveRecoursgracieux = $this->Recourgracieux->saveAll( $recoursgracieux, array( 'validate' => 'only' ) );
				if( $saveRecoursgracieux &&
					$this->Recourgracieux->save( $recoursgracieux ) &&
					$this->Historiqueetat->setHisto(
						$this->Recourgracieux->name,
						$this->Recourgracieux->id,
						$recoursgracieux['Recourgracieux']['foyer_id'],
						__FUNCTION__,
						$recoursgracieux['Recourgracieux']['etat'],
						$recoursgracieux['Recourgracieux']['foyer_id']
					)
				) {
					$this->Recourgracieux->commit();
				}
			}
		}

		/**
		 * Défini l'état d'un recours gracieux d'un foyer a ATTENVOIE
		 *
		 * @param integer $id L'id technique du recours gracieux a affecter
		 * @return void
		 */
		public function envoyer($id) {
				$this->WebrsaAccesses->check($id);
				$this->Recourgracieux->id = $id;
				$foyer_id = $this->Recourgracieux->field( 'foyer_id' );
				// Affichage des données
				$recoursgracieux = $this->Recourgracieux->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Recourgracieux->fields()
						),
						'conditions' => array(
							'Recourgracieux.id' => $id
						),
						'contain' => FALSE
					)
				);

				$recoursgracieux['Recourgracieux']['etat'] = 'ATTENVOIE' ;

				//Sauvegarde du recours gracieux
				$this->Recourgracieux->begin();
				$saveRecoursgracieux = $this->Recourgracieux->saveAll( $recoursgracieux, array( 'validate' => 'only' ) );
				if( $saveRecoursgracieux
					&&	$this->Recourgracieux->save( $recoursgracieux )
					&& $this->Historiqueetat->setHisto(
						$this->Recourgracieux->name,
						$id,
						$foyer_id,
						__FUNCTION__,
						$recoursgracieux['Recourgracieux']['etat'],
						$foyer_id
					)
				) {
					$this->Recourgracieux->commit();
					$this->Flash->success( __( 'Save->success' ) );
				}
				else {
					$this->Recourgracieux->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
				$this->redirect( array( 'controller' => 'recoursgracieux', 'action' => 'index', $foyer_id ) );
		}

		/**
		 * Permet le traitement final d'ui recours gracieux
		 *
		 * @param integer $id L'id technique du recours gracieux a traiter
		 * @return void
		 */
		public function traiter($id) {
			$this->WebrsaAccesses->check($id);
			$this->Recourgracieux->id = $id;
			$foyer_id = $this->Recourgracieux->field( 'foyer_id' );
			$dossier_id = $this->Recourgracieux->dossierId( $id );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );
			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $foyer_id ) );
			}
			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->Recourgracieux->begin();
				$data = $this->request->data;

				if ( $data['Recourgracieux']['traiter'] == 1){
					$data['Recourgracieux']['etat'] = 'TRAITER' ;
				}
				$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
				if( $this->Recourgracieux->saveAll( $data, array( 'validate' => 'only' ) ) &&
					$this->Recourgracieux->save( $data ) &&
					$this->Historiqueetat->setHisto(
						$this->Recourgracieux->name,
						$this->Recourgracieux->id,
						$foyer_id,
						__FUNCTION__,
						$data['Recourgracieux']['etat'],
						$foyer_id
					) &&
					$this->Fileuploader->saveFichiers( $dir,FALSE, $this->Recourgracieux->id )
				 ){
					$this->Recourgracieux->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'Recoursgracieux', 'action' => 'index', $foyer_id ) );
				} else {
					$this->Recourgracieux->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			// Affichage des données
			else {
				$recoursgracieux = $this->Recourgracieux->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Recourgracieux->fields()
						),
						'conditions' => array(
							'Recourgracieux.id' => $id
						),
						'contain' => FALSE
					)
				);
				if (!empty( $recoursgracieux ) ){
					// Assignation au formulaire
					$this->request->data = $recoursgracieux;
				}
			}

			//ListMotifs
			$listMotifs = $this->Motifproposrecoursgracieux->find(
				'list',
				array(
					'fields' => array ('id', 'nom')
				)
			);
			$this->set( 'listMotifs', $listMotifs );

			//Liste des Pièces jointes
			$piecesjointes = $this->getPiecejointes( $id );
			$this->set( 'piecesjointes', $piecesjointes );

			// Assignation à la vue
			$this->set( 'options', array_merge(
					$this->Recourgracieux->options(),
					$this->Recourgracieux->Foyer->Creance->enums(),
					$this->Recourgracieux->Foyer->Creance->Titrecreancier->enums()
				)
			);
			$this->set( 'urlmenu', '/recoursgracieux/index/'.$foyer_id );
			$this->set( 'foyer_id', $foyer_id );
		}

		/**
		 * Supprime un recours gracieux d'un foyer
		 *
		 * @param integer $id L'id technique du recours gracieux a supprimé
		 * @return void
		 */
		public function delete($id) {
				$this->WebrsaAccesses->check($id);
				$foyer_id = $this->Recourgracieux->field( 'foyer_id' );
				$success = $this->Recourgracieux->delete( $id );
				if( $success &&
				$this->Historiqueetat->setHisto(
					$this->Recourgracieux->name,
					$id,
					$foyer_id,
					__FUNCTION__,
					'SUP',
					$foyer_id )) {
					$this->Flash->success( __( 'Delete->success' ) );
				}
				else {
					$this->Flash->error( __( 'Delete->error' ) );
				}
				$this->redirect( array( 'controller' => 'recoursgracieux', 'action' => 'index', $foyer_id ) );
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
		 *   Fonction permettant de visualiser les fichiers chargés dans la vue avant leur envoi sur le serveur
		 */
		public function fileview( $id ) {
			$this->Fileuploader->fileview( $id );
		}

		/**
		 *   Téléchargement des fichiers préalablement associés à un traitement donné
		 */
		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}

		/**
		 * Fonction permettant d'accéder à la page pour lier les fichiers.
		 *
		 * @param type $id
		 */
		public function filelink( $id ) {
			$this->WebrsaAccesses->check($id);
			$dossier_id = $this->Recourgracieux->dossierId( $id );
			$this->assert( !empty( $dossier_id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			$fichiers = array();

			$recoursgracieux = $this->Recourgracieux->find(
				'first',
				array(
					'conditions' => array(
						'Recourgracieux.id' => $id
					),
					'contain' => array(
						'Fichiermodule' => array(
							'fields' => array( 'name', 'id', 'created', 'modified' )
						)
					)
				)
			);

			$this->Jetons2->get( $dossier_id );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Recourgracieux->id = $id;
				$this->Jetons2->release( $dossier_id );
				$this->redirect( array( 'action' => 'index', $this->Recourgracieux->field( 'foyer_id' )) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Recourgracieux->begin();
				$saved = $this->Recourgracieux->updateAllUnBound(
					array( 'Recourgracieux.haspiecejointe' => '\''.$this->request->data['Recourgracieux']['haspiecejointe'].'\'' ),
					array( '"Recourgracieux"."id"' => $id)
				);

				if( $saved ) {
					// Sauvegarde des fichiers liés
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
					$saved = $this->Fileuploader->saveFichiers( $dir, FALSE, $id ) && $saved;
				}
				if( $saved ) {
					$this->Recourgracieux->commit();
					$this->Jetons2->release( $dossier_id );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('action' => 'filelink', $id));
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Recourgracieux->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->set( 'options', $this->Recourgracieux->options() );
			$this->set( compact( 'dossier_id', 'personne_id', 'fichiers', 'recoursgracieux' ) );
		}

		/**
		 * Retourne un tableau des pieces jointes liées au recours
		 *
		 * @param integer $recourgracieux_id
		 * @return array des pieces jointes
		 */
		public function getPiecejointes( $id ) {
			$piecejointeFpj = $this->Fileuploader->fichiersEnBase( $id );
			$piecesjointes = array();
			foreach($piecejointeFpj as $key => $piecejointe) {
				$piecesjointes[] = $piecejointe['Fichiermodule'];
			}
			return $piecesjointes;
		}

	}
?>
