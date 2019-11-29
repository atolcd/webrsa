<?php
	/**
	 * Code source de la classe TitressuivisinfospayeursController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'LocaleHelper', 'View/Helper' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe TitressuivisinfospayeursController s'occupe du suivi des informations payeurs des titres de recettes
	 *
	 * @package app.Controller
	 */
	class TitressuivisinfospayeursController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Titressuivisinfospayeurs';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Titresuiviinfopayeur',
			'Titrecreancier',
			'Creances',
			'Typetitrecreancierinfopayeur',
			'WebrsaTitrecreancier',
			'Creance',
			'WebrsaCreance',
			'Dossier',
			'Foyer',
			'Option',
			'Email'
		);

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'DossiersMenus',
			'Jetons2'
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
		);

		/**
		 * Supprime une information payeur d'un titre de recettes
		 *
		 * @param integer $id L'id technique de l'information payeur à supprimer
		 * @return void
		 */
		public function delete($id, $titrecreancier_id) {
			$success = $this->Titresuiviinfopayeur->delete( $id );
			if( $success ) {
				$this->Flash->success( __( 'Delete->success' ) );
			} else {
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->redirect( array('controller' => 'titressuivis', 'action' => 'index', $titrecreancier_id ) );
		}

		/**
		 * Visualisation d'une information payeur d'un Titrecreancier
		 *
		 * @param integer $id L'id technique dans la table titrescreanciers.
		 * @return void
		 */
		public function view($id, $titrecreancier_id) {
			// Retour à l'index si Annulation
			if( isset($this->request->data['Cancel']) ){
				$this->redirect( array('controller' => 'titressuivis', 'action' => 'index', $titrecreancier_id ) );
			}

			$Email = $this->Email->view($id, 'Titresuiviinfopayeur', 'add_edit');
			$optionsEmail = $this->Email->options();
			$this->set( compact( 'optionsEmail', 'Email') );

			$this->_setOptions($titrecreancier_id);

			$this->render('view');
		}


		/**
		 * Ajouter une information payeur d'un Titrecreancier
		 *
		 * @param integer
		 * @return void
		 */
		public function add($titrecreance_id = null) {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Modification d'une info payeur d'un Titrecreancier.
		 *
		 * @param integer $id L'id technique dans la table titrescreanciers.
		 * @return void
		 */
		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Ajout d'un retour payeur sur une infos payeur d'un Titrecreancier.
		 *
		 * @param integer $id L'id technique dans la table titrescreanciers.
		 * @return void
		 */
		public function answer($id, $titrecreancier_id) {
			// Retour à l'index si Annulation
			if( isset($this->request->data['Cancel']) ) {
				$this->redirect( array('controller' => 'titressuivis', 'action' => 'index', $titrecreancier_id ) );
			}

			$this->_setOptions($titrecreancier_id);

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->_save_add_edit();
			}

			$this->render( 'answer' );
		}

		/**
		 * Fonction commune d'ajout/modification d'information payeur
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
			}

			// Retour à l'index si Annulation
			if( isset($this->request->data['Cancel']) ) {
				$this->redirect( array('controller' => 'titressuivis', 'action' => 'index', $titrecreancier_id ) );
			}

			$this->_setOptions($titrecreancier_id);

			// Essai de sauvegarde
			if( !empty( $this->request->data ) ) {
				$this->_save_add_edit();
			}else{
				if( $this->action != 'add' ) {
					$tmpFormDataAddEdit = $this->Email->edit($id, 'Titresuiviinfopayeur', 'add_edit');
				}
				if( $this->action == 'add' || empty($tmpFormDataAddEdit) ) {
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
					$tmpFormDataAddEdit = array (
						'Email' =>  array (
							'foyer_id' => $foyer_id,
							'etat' => 'CREE',
							'user_id' => $this->Session->read('Auth.User.id'),
							'modele' => 'Titresuiviinfopayeur',
							'modele_id' => null,
							'modele_action' => 'add_edit',
							'modeleparent' => 'Titrecreancier',
							'modeleparent_id' => $titrecreancier_id,
						)
					);
				}
				$this->request->data = $tmpFormDataAddEdit;
			}
			$optionsEmail = $this->Email->options();
			$this->set( compact( 'optionsEmail') );

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

			// Ajout des options
			$titresInfosEnCours = array();
			if( $this->action == 'add' ) {
				$titresInfosEnCours['Titresuiviinfopayeur']['typesinfopayeur_id']='';
				$titresInfosEnCours['Titresuiviinfopayeur']['id']='';
				$titresInfosEnCours['Titresuiviinfopayeur']['commentaire']='';

			} else {
				$titresInfosEnCours = $this->Titresuiviinfopayeur->find('first', array(
					'conditions' => array('Titresuiviinfopayeur.id' => $this->request->params['pass'][0])
				));
			}
			$options['Typetitrecreancierinfopayeur'] = $this->Typetitrecreancierinfopayeur->find('list', array(
				'fields' => 'Typetitrecreancierinfopayeur.nom',
				'conditions' => array( 'actif' => true ) ) );
			$options = array_merge($options, $this->Titrecreancier->options() );
			// Assignations à la vue
			$this->set( compact( 'options', 'titresInfosEnCours', 'titresCreanciers' ) );
		}

		/**
		 * Sauvegarde lors d'une édition ou d'un ajout
		 */
		protected function _save_add_edit(){
			$this->Titresuiviinfopayeur->begin();
			$data = $this->request->data;
			$titrecreancier_id = $data['Titresuiviinfopayeur']['titrecreancier_id'];
			$success = $this->Titresuiviinfopayeur->save( $data, array( 'validate' => 'first', 'atomic' => false ) );
			if( $success ) {
				$data['Email']['modele_id'] = $this->Titresuiviinfopayeur->id;
				$success = $this->Email->saveAddEdit($data);
				if( $success ) {
					$this->Email->commit();
					$this->Titresuiviinfopayeur->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'titressuivis', 'action' => 'index', $titrecreancier_id ) );
				} else {
					$this->Titresuiviinfopayeur->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			} else {
				$this->Titresuiviinfopayeur->rollback();
				$this->Flash->error( __( 'Save->error' ) );
			}
		}
	}
