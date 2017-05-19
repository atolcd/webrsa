<?php
	/**
	 * Code source de la classe ActionsinsertionController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe ActionsinsertionController ...
	 *
	 * @package app.Controller
	 */
	class ActionsinsertionController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Actionsinsertion';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Jetons2',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(

		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Actioninsertion',
			'Action',
			'Aidedirecte',
			'Contratinsertion',
			'Option',
			'Prestform',
			'Refpresta',
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
			'edit' => 'update',
			'index' => 'read',
		);

		/**
		 *
		 */
		public function beforeFilter() {
			parent::beforeFilter();
			$this->set( 'lib_action', ClassRegistry::init('Actioninsertion')->enum('lib_action') );
			$this->set( 'actions', $this->Action->grouplist( 'aide' ) );
			$this->set( 'actions', $this->Action->grouplist( 'prest' ) );
			$this->set( 'typo_aide', ClassRegistry::init('Aidedirecte')->enum('typo_aide') );
		}

		/**
		 *
		 * @param integer $contratinsertion_id
		 */
		public function index( $contratinsertion_id = null ){
			$this->assert( valid_int( $contratinsertion_id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Actioninsertion->Contratinsertion->personneId( $contratinsertion_id ) ) ) );

			$contratinsertion = $this->Contratinsertion->find(
				'first',
				array(
					'conditions' => array(
						'Contratinsertion.id' => $contratinsertion_id
					),
					'recursive' => -1
				)
			);

			// Si contrat n'existe pas -> 404
			if( empty( $contratinsertion ) ) {
				$this->cakeError( 'error404' );
			}

			$actionsinsertion = $this->Actioninsertion->find(
				'all',
				array(
					'conditions' => array(
						'Actioninsertion.contratinsertion_id' => $contratinsertion_id
					),
					'contain' => array(
						'Aidedirecte',
						'Prestform' => array(
							'Refpresta'
						)
					)
				)
			);


			$actions = $this->Action->find(
				'list',
				array(
					'fields' => array(
						'Action.code',
						'Action.libelle'
					)
				)
			);

			$this->set( 'actions', $actions );
			$this->set( 'actionsinsertion', $actionsinsertion );
			$this->set( 'contratinsertion_id', $contratinsertion_id);
			$this->set( 'personne_id', $contratinsertion['Contratinsertion']['personne_id'] );
		}

		/**
		 *
		 * @param integer $contratinsertion_id
		 */
		public function edit( $contratinsertion_id = null ){
			$this->assert( valid_int( $contratinsertion_id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Actioninsertion->Contratinsertion->personneId( $contratinsertion_id ) ) ) );

			$contratinsertion = $this->Actioninsertion->Contratinsertion->find(
				'first',
				array(
					'conditions' => array(
						'Contratinsertion.id' => $contratinsertion_id
					),
					'recursive' => -1
				)
			);

			// Si action n'existe pas -> 404
			if( empty( $contratinsertion ) ) {
				$this->cakeError( 'error404' );
			}

			if( !empty( $this->request->data ) ) {
				if( $this->Actioninsertion->saveAll( $this->request->data ) ) {
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'actionsinsertion', 'action' => 'index', $contratinsertion['Actioninsertion']['personne_id']) );
				}
			}
			else {
				$this->request->data = $contratinsertion;
			}

			$this->set('personne_id', $contratinsertion['Contratinsertion']['personne_id']);
			$this->render( 'add_edit' );
		}
	}
?>