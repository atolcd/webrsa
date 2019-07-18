<?php
	/**
	 * Code source de la classe TitressuivisautresinfosController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'LocaleHelper', 'View/Helper' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'WebrsaAccessTitressuivisautresinfos', 'Utility' );

	/**
	 * La classe TitressuivisautresinfosController s'occupe du suivi des autres informations des titres de recettes
	 *
	 * @package app.Controller
	 */
	class TitressuivisautresinfosController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Titressuivisautresinfos';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Titresuiviautreinfo',
			'WebrsaTitresuiviautreinfo',
			'Titrecreancier',
			'Creances',
			'Typetitrecreancierautreinfo',
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
		 * Supprime une autre info d'un titre de recettes en remettant l'état du précédent titre de recette
		 *
		 * @param integer $id L'id technique de l'autre info à supprimer
		 * @return void
		 */
		public function delete($id, $titrecreancier_id) {
			$success = $this->Titresuiviautreinfo->delete( $id );
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
		 * Annule une autre info d'un titre de recettes en remettant l'état du précédent titre de recette
		 *
		 * @param integer $id L'id technique de l'autre info à annuler
		 * @return void
		 */
		public function cancel($id, $titrecreancier_id) {
			$data = array();
			$data['id'] = $id;
			$data['etat'] = 'ANNULER';
			$success = $this->Titresuiviautreinfo->save($data);
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
		 * Visualisation d'une autre info d'un Titrecreancier
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
		 * Ajouter une autre info d'un Titrecreancier
		 *
		 * @param integer
		 * @return void
		 */
		public function add($titrecreance_id = null) {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Modification d'une autre info d'un Titrecreancier.
		 *
		 * @param integer $id L'id technique dans la table titrescreanciers.
		 * @return void
		 */
		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Fonction commune d'ajout/modification d'une autre info
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
				$fichiersEnBase = Hash::extract( $this->WebrsaTitresuiviautreinfo->findFichiers($id), '{n}.Fichiermodule' );
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

			// Si edition, récupération de l'id
			$titreAutreInfo = array();
			if( $this->action === 'add' ){
				$titreAutreInfo['Titresuiviautreinfo']['id'] = '';
				$titreAutreInfo['Titresuiviautreinfo'][''] = '';
				$titreAutreInfo['Titresuiviautreinfo']['commentaire'] = '';
				$titreAutreInfo['Titresuiviautreinfo']['typesautresinfos_id'] = '';
			} else{
				$titreAutreInfo = $this->Titresuiviautreinfo->find('first',
				array(
					'conditions' => array(
						'Titresuiviautreinfo.id' => $this->request->params['pass'][0]
					)
					));
				}

			// Ajout des options
			$options = array_merge($this->Titresuiviautreinfo->enums(), $this->Titrecreancier->options() );
			$options['type'] = $this->Typetitrecreancierautreinfo->find('list', array(
				'fields' => 'Typetitrecreancierautreinfo.nom',
				'conditions' => array( 'actif' => true ) ) );

			// Assignations à la vuetypesautresinfos_id
			$this->set( compact( 'options', 'titreAutreInfo', 'titresCreanciers' ) );
		}

		/**
		 * Sauvegarde lors d'une édition ou d'un ajout
		 */
		protected function _save_add_edit(){
			$this->Titresuiviautreinfo->begin();
			$data = $this->request->data;
			$id = $data['Titresuiviautreinfo']['id'];
			$titrecreancier_id = $data['Titresuiviautreinfo']['titrecreancier_id'];
			$data['Titresuiviautreinfo']['etat'] = 'En cours';

			// Récupération du nom du type d'annulation/réduction
			$typeAutreInfo = $this->Typetitrecreancierautreinfo->find('first', array(
				'fields' => 'Typetitrecreancierautreinfo.nom',
				'conditions' => array(
					'Typetitrecreancierautreinfo.id' => $data['Titresuiviautreinfo']['typesautresinfos_id']
					)
				)
			);

			$typeAutreInfo = $typeAutreInfo['Typetitrecreancierautreinfo']['nom'];
			$titrecreancier = $this->Titrecreancier->find('first',
				array(
					'conditions' => array(
						'Titrecreancier.id ' => $titrecreancier_id
					),
					'contain' => false
				)
			);

			$success = $this->Titresuiviautreinfo->save( $data, array( 'validate' => 'first', 'atomic' => false ) );
			if(empty($id)) {
				$id = $this->Titresuiviautreinfo->id;
			}
			if( $success && $this->_saveFichiers($id) ) {
				//Sauvegarde & Mise à jour de l'état du titre
				$this->Titresuiviautreinfo->commit();
				$this->Flash->success( __( 'Save->success' ) );
				$this->redirect( array( 'controller' => 'titressuivis', 'action' => 'index', $titrecreancier_id ) );
			} else {
				$this->Fileuploader->fichiers( $id, false );
				$this->Titresuiviautreinfo->rollback();
				$this->Flash->error( __( 'Save->error' ) );
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
				!Set::classicExtract( $this->request->data, "Titresuiviautreinfo.haspiecejointe" ),
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
				$this->Titresuiviautreinfo->begin();
				$saved = $this->Titresuiviautreinfo->updateAllUnBound(
					array( 'Titresuiviautreinfo.haspiecejointe' => '\''.$this->request->data['Titresuiviautreinfo']['haspiecejointe'].'\'' ),
					array( '"Titresuiviautreinfo"."id"' => $id)
				);

				if( $saved && $this->_saveFichiers($id) ) {
					$this->Titresuiviautreinfo->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('action' => 'filelink', $id, $titrecreancier_id));
				} else {
					$fichiers = $this->Fileuploader->fichiers( $id );
					$this->Titresuiviautreinfo->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->set('titresAutreInfo', $this->Titresuiviautreinfo->find('first', array('conditions' => array('Titresuiviautreinfo.id' => $id))));
			$this->set( 'fichiers', $fichiers );
		}

		/**
		 * Fonction permettant de récupérer la liste complète avec les droits associés des titres d'autres infos
		 * liés à l'id du titre créancier
		 *
		 * @param int $foyer_id
		 * @param array titresCreanciers
		 *
		 * @return array
		 */
		protected function _getList($foyer_id, $titresCreanciers){
			$titrecreancier_id = $titresCreanciers['Titrecreancier']['id'];

			// Liste des autres infos
			$contentIndex = $this->Titresuiviautreinfo->getContext();
			$query = $this->Titresuiviautreinfo->getQuery($titrecreancier_id);
			$titresAutresInfos = $this->WebrsaAccesses->getIndexRecords($foyer_id, $query, $contentIndex);
			return $this->Titresuiviautreinfo->getList($titresAutresInfos, $titresCreanciers['Titrecreancier']['mntinit']);
		}
	}