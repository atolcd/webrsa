<?php
	/**
	 * Code source de la classe TitressuivisannulationsreductionsController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'LocaleHelper', 'View/Helper' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'WebrsaAccessTitressuivisannulationsreductions', 'Utility' );

	/**
	 * La classe TitressuivisannulationsreductionsController s'occupe du suivi des annulations et réduction des titres de recettes
	 *
	 * @package app.Controller
	 */
	class TitressuivisannulationsreductionsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Titressuivisannulationsreductions';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Titresuiviannulationreduction',
			'WebrsaTitresuiviannulationreduction',
			'Titrecreancier',
			'Creances',
			'Typetitrecreancierannulationreduction',
			'WebrsaTitrecreancier',
			'Creance',
			'WebrsaCreance',
			'Dossier',
			'Foyer',
			'Option',
		);

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses',
			'Gedooo.Gedooo',
			'Fileuploader'
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		*/
		public $aucunDroit = array(
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
			'index' => 'Titressuivis:index',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Locale',
			'Paginator',
			'Default2',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Fileuploader',
			'Cake1xLegacy.Ajax'
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
			'ajaxreffonct' => 'read',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'exportcsv' => 'read',
			'filelink' => 'read',
			'fileview' => 'read',
			'impression' => 'read',
			'view' => 'read',
		);

		/**
		 * Supprime une annulation / réduction d'un titre de recettes en remettant l'état du précédent titre de recette
		 *
		 * @param integer $id L'id technique de l'annulation / réduction à supprimer
		 * @return void
		 */
		public function delete($id, $titrecreancier_id) {
			$success = $this->Titresuiviannulationreduction->delete( $id );
			if( $success ) {
				if( $this->Titrecreancier->setEtat($titrecreancier_id, __FUNCTION__) && $this->Titrecreancier->calculMontantTitre($titrecreancier_id) ) {
					$this->Flash->success( __( 'Delete->success' ) );
				} else {
					$this->Flash->error( __( 'Delete->error' ) );
				}
			}
			else {
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->redirect( array('controller' => 'titressuivis', 'action' => 'index', $titrecreancier_id ) );
		}

		/**
		 * Annule une annulation / réduction d'un titre de recettes en remettant l'état du précédent titre de recette
		 *
		 * @param integer $id L'id technique de l'annulation / réduction à annuler
		 * @return void
		 */
		public function cancel($id, $titrecreancier_id) {
			$data = array();
			$data['id'] = $id;
			$data['etat'] = 'ANNULER';
			$success = $this->Titresuiviannulationreduction->save($data);
			if( $success ) {
				if( $this->Titrecreancier->setEtat($titrecreancier_id, __FUNCTION__) && $this->Titrecreancier->calculMontantTitre($titrecreancier_id) ) {
					$this->Flash->success( __( 'Save->success' ) );
				} else {
					$this->Flash->error( __( 'Save->error' ) );
				}
			} else {
				$this->Flash->error( __( 'Save->error' ) );
			}

			$this->redirect( array('controller' => 'titressuivis', 'action' => 'index', $titrecreancier_id ) );
		}

		/**
		 * Visualisation d'une annulation / réduction d'un Titrecreancier
		 *
		 * @param integer $id L'id technique dans la table titrescreanciers.
		 * @return void
		 */
		public function view($id, $titrecreancier_id) {
			// Retour à l'index si Annulation
			if( isset($this->request->data['Cancel']) ){
				$this->redirect( array('controller' => 'titressuivis', 'action' => 'index', $titrecreancier_id ) );
			}

			$this->_setOptions($titrecreancier_id);

			$this->render('view');
		}


		/**
		 * Ajouter une annulation / réduction d'un Titrecreancier
		 *
		 * @param integer
		 * @return void
		 */
		public function add($titrecreance_id = null) {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Modification d'une annulation / réduction d'un Titrecreancier.
		 *
		 * @param integer $id L'id technique dans la table titrescreanciers.
		 * @return void
		 */
		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Fonction commune d'ajout/modification d'une annulation / réduction
		 *
		 * @param integer $id
		 *
		 * @return void
		 */
		protected function _add_edit( $id = null ) {
			// Récupération de l'ID du titre
			if( $this->action == 'add' ) {
				$titrecreancier_id = $this->request->params['pass'][0];
			}

			if( $this->action == 'edit' ) {
				$titrecreancier_id = $this->request->params['pass'][1];
				$fichiersEnBase = Hash::extract( $this->WebrsaTitresuiviannulationreduction->findFichiers($id), '{n}.Fichiermodule' );
				$this->set('fichiersEnBase', $fichiersEnBase);
			}

			// Retour à l'index si Annulation
			if( isset($this->request->data['Cancel']) ) {
				$this->redirect( array('controller' => 'titressuivis', 'action' => 'index', $titrecreancier_id ) );
			}

			$this->_setOptions($titrecreancier_id);

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->_save_add_edit();
			}

			$this->render( 'add_edit' );
		}

		/**
		 * Assigne les options à la vue
		 *
		 * @param int id
		 * @return void
		 */
		protected function _setOptions($titrecreancier_id){
			// Initialisation / rappel du titre de recette en cours
			$titresCreanciers = $this->Titrecreancier->find('first',
				array(
					'conditions' => array(
						'Titrecreancier.id ' => $titrecreancier_id
					),
					'contain' => false
				)
			);

			$titresCreanciers['Titrecreancier']['etat'] = (__d('titrecreancier', 'ENUM::ETAT::' . $titresCreanciers['Titrecreancier']['etat']));

			$creance_id = $titresCreanciers['Titrecreancier']['creance_id'];
			$foyer_id = $this->Titrecreancier->foyerId( $creance_id );

			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id )));

			// Récupération de la liste des titres
			$titresAnnRed = $this->_getList($foyer_id, $titresCreanciers);

			// Ajout des options
			$options = $this->Titresuiviannulationreduction->enums();
			$titresAnnRedEnCours = array();
			if( $this->action == 'add' ) {
				$options['montant']['type'] = 'number';
				$options['montant']['disabled'] = true;

				$montantReduitTotal = 0;
				if( !empty($titresAnnRed) ) {
					foreach($titresAnnRed as $titres ) {
						$montantReduitTotal += $titres['Titresuiviannulationreduction']['mtreduit'];
					}
				}
				$options['montant']['total'] = $montantReduitTotal;

				$titresAnnRedEnCours['Titresuiviannulationreduction']['typeannulationreduction_id']='';
				$titresAnnRedEnCours['Titresuiviannulationreduction']['id']='';
				$titresAnnRedEnCours['Titresuiviannulationreduction']['mtreduit']='';
				$titresAnnRedEnCours['Titresuiviannulationreduction']['commentaire']='';

			} else {
				$options['montant']['total'] = null;
				$options['montant']['type'] = 'hidden';
				$titresAnnRedEnCours = $this->Titresuiviannulationreduction->find('first', array(
					'conditions' => array('Titresuiviannulationreduction.id' => $this->request->params['pass'][0])
				));
				if( $titresAnnRedEnCours['Typetitrecreancierannulationreduction']['nom'] !== 'annulation' ) {
					$options['montant']['disabled'] = false;
				}
			}
			$options['type'] = $this->Typetitrecreancierannulationreduction->find('list', array(
				'fields' => 'Typetitrecreancierannulationreduction.nom',
				'conditions' => array( 'actif' => true ) ) );

			// Assignations à la vue
			$this->set( compact( 'options', 'titresAnnRedEnCours', 'titresCreanciers' ) );
		}

		/**
		 * Sauvegarde lors d'une édition ou d'un ajout
		 */
		protected function _save_add_edit(){
			$this->Titresuiviannulationreduction->begin();
			$data = $this->request->data;
			$id = $data['Titresuiviannulationreduction']['id'];
			$titrecreancier_id = $data['Titresuiviannulationreduction']['titrecreancier_id'];
			$data['Titresuiviannulationreduction']['etat'] = 'ENCOURS';

			// Récupération du nom du type d'annulation/réduction
			$typeAnnulationReduction = $this->Typetitrecreancierannulationreduction->find('first', array(
				'fields' => 'Typetitrecreancierannulationreduction.nom',
				'conditions' => array(
					'Typetitrecreancierannulationreduction.id' => $data['Titresuiviannulationreduction']['typeannulationreduction_id']
					)
				)
			);

			$typeAnnulationReduction = $typeAnnulationReduction['Typetitrecreancierannulationreduction']['nom'];
			$titrecreancier = $this->Titrecreancier->find('first',
				array(
					'conditions' => array(
						'Titrecreancier.id ' => $titrecreancier_id
					),
					'contain' => false
				)
			);

			// Erreur si réduction et pas de montant OU si réduction et montant supérieur au titre
			if( ($typeAnnulationReduction === 'réduction' && $data['Titresuiviannulationreduction']['mtreduit'] === '' )  ||
			($typeAnnulationReduction === 'réduction' && $data['Titresuiviannulationreduction']['mtreduit'] > $titrecreancier['Titrecreancier']['mnttitr'] )  ) {
				$this->Titresuiviannulationreduction->rollback();
				$this->Flash->error( __( 'Save->error' ) );
			} elseif ($typeAnnulationReduction === 'annulation') {
				$data['Titresuiviannulationreduction']['mtreduit'] = $titrecreancier['Titrecreancier']['mnttitr'];
			}

			$success = $this->Titresuiviannulationreduction->save( $data, array( 'validate' => 'first', 'atomic' => false ) );
			if( $success && $this->_saveFichiers($id) ) {
				//Sauvegarde & Mise à jour de l'état du titre
				$this->Titresuiviannulationreduction->commit();
				$success = $this->Titrecreancier->setEtat($titrecreancier_id, $this->action);

				if( $success && $this->Titrecreancier->calculMontantTitre($titrecreancier_id) ) {
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'titressuivis', 'action' => 'index', $titrecreancier_id ) );
				} else {
					$this->Titresuiviannulationreduction->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			} else {
				$this->Fileuploader->fichiers( $id, false );
				$this->Titresuiviannulationreduction->rollback();
				$this->Flash->error( __( 'Save->error' ) );
			}
		}

		/**
		 * Impression du certificat administratif.
		 *
		 * @param integer $id
		 * @return void
		 */
		public function impression( $id, $titrecreancier_id ) {
			// Initialisation / rappel du titre de recette en cours
			$titresCreanciers = $this->Titrecreancier->find('first',
				array(
					'conditions' => array(
						'Titrecreancier.id ' => $titrecreancier_id
					),
					'contain' => false
				)
			);

			$creance_id = $titresCreanciers['Titrecreancier']['creance_id'];
			$foyer_id = $this->Titrecreancier->foyerId( $creance_id );
			$this->DossiersMenus->checkDossierMenu( array( 'foyer_id' => $foyer_id ) );

			$pdf = $this->Titresuiviannulationreduction->WebrsaTitresuiviannulationreduction->getDefaultPdf( $id, $this->Session->read( 'Auth.User.id' ) );

			if( !empty( $pdf ) && $pdf !== false ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'certificatadministratif_suiviannulationreduction-%d-%s.pdf', $id, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Flash->error( __m("Titressuivisannulationsreductions::impession::error") );
				$this->redirect( array( 'controller' => 'titressuivis', 'action' => 'index', $titrecreancier_id ) );
			}
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
		 */
		public function ajaxfiledelete() {
			$this->Fileuploader->ajaxfiledelete();
		}

		/**
		 * Fonction permettant de visualiser les fichiers chargés dans la vue avant leur envoi sur le serveur
		 * 	@param int id
		 */
		public function fileview( $id ) {
			$this->Fileuploader->fileview( $id );
		}

		/**
		 * Téléchargement des fichiers préalablement associés à un traitement donné
		 *
		 * @param int id
		 */
		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}

		/**
		 * Sauvegarde des fichiers liés
		 *
		 * @param integer $id
		 * @return boolean
		 */
		protected function _saveFichiers( $id ) {
			$dir = $this->Fileuploader->dirFichiersModule( $this->action, $this->request->params['pass'][0] );
			return $this->Fileuploader->saveFichiers(
				$dir,
				!Set::classicExtract( $this->request->data, "Titresuiviannulationreduction.haspiecejointe" ),
				$id
			);
		}

		/**
		 * Fonction permettant d'accéder à la page pour lier les fichiers.
		 *
		 * @param int $id
		 * @param int $titrecreancier_id
		 */
		public function filelink( $id, $titrecreancier_id) {
			$this->_setOptions($titrecreancier_id);

			$fichiers = array();

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'titressuivis', 'action' => 'index', $titrecreancier_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Titresuiviannulationreduction->begin();
				$saved = $this->Titresuiviannulationreduction->updateAllUnBound(
					array( 'Titresuiviannulationreduction.haspiecejointe' => '\''.$this->request->data['Titresuiviannulationreduction']['haspiecejointe'].'\'' ),
					array( '"Titresuiviannulationreduction"."id"' => $id)
				);

				if( $saved && $this->_saveFichiers($id) ) {
					$this->Titresuiviannulationreduction->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('action' => 'filelink', $id, $titrecreancier_id));
				} else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Titresuiviannulationreduction->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->set('titresAnnulationReduction', $this->Titresuiviannulationreduction->find('first', array('conditions' => array('Titresuiviannulationreduction.id' => $id))));
			$this->set( 'fichiers', $fichiers );
		}

		/**
		 * Fonction permettant de récupérer la liste complète avec les droits associés des titres d'annulation réduction
		 * liés à l'id du titre créancier
		 *
		 * @param int $foyer_id
		 * @param array titresCreanciers
		 *
		 * @return array
		 */
		protected function _getList($foyer_id, $titresCreanciers){
			$titrecreancier_id = $titresCreanciers['Titrecreancier']['id'];

			// Liste des annulations / réductions
			$contentIndex = $this->Titresuiviannulationreduction->getContext();
			$query = $this->Titresuiviannulationreduction->getQuery($titrecreancier_id);
			$titresAnnRed = $this->WebrsaAccesses->getIndexRecords($foyer_id, $query, $contentIndex);
			return $this->Titresuiviannulationreduction->getList($titresAnnRed, $titresCreanciers['Titrecreancier']['mntinit']);
		}
	}
