<?php
	/**
	 * Code source de la classe Questionnairesb7pdvs93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Questionnairesd2pdvs93Controller ...
	 *
	 * @package app.Controller
	 */
	class Questionnairesb7pdvs93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Questionnairesb7pdvs93';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Questionnaireb7pdv93',
			'WebrsaQuestionnairesb7pdvs93',
			'Typeemploi',
			'Dureeemploi',
			'Personne',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array();

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array();

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read',
			'add' => 'create',
			'edit' => 'update',
			'delete' => 'delete',
		);

		/**
		 * Liste des questionnaires B7 de l'allocataire.
		 */
		public function index( $personne_id ) {
			// Menu gauche du dossier allocataire
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			// Messages à envoyer à l'utilisateur / Options
			$messages = array ();
			$ajoutPossible = true;
			$options = array ();
			$this->set( compact( 'messages', 'ajoutPossible', 'options' ) );

			// Recherche de l'allocataire
			$personne = $this->Questionnaireb7pdv93->getPersonne ($personne_id);
			$this->set( compact( 'personne', 'personne_id' ) );

			// Récupération des questionnaires B7 de l'allocataire avec droits d'accès
			$conditions = array(
				'personne_id' => $personne_id,
			);

			$query = $this->Questionnaireb7pdv93->queryQuestionnaireb7pdv93ByCondition($conditions);
			$query['order'] = array('dateemploi ASC');
			$query = $this->WebrsaQuestionnairesb7pdvs93->completeVirtualFieldsForAccess($query);
			$modele = 'Questionnaireb7pdv93';

			$paramsAccess = $this->WebrsaQuestionnairesb7pdvs93->getParamsForAccess($personne_id, WebrsaAccessQuestionnairesb7pdvs93::getParamsList() + compact('modele'));
			$ajoutPossible = Hash::get($paramsAccess, 'ajoutPossible');
			$questionnaireb7pdv93 = WebrsaAccessQuestionnairesb7pdvs93::accesses($this->Questionnaireb7pdv93->getByPersonne ($personne_id), $paramsAccess);

			$this->set( compact( 'questionnaireb7pdv93' ) );
		}

		/**
		 * Formulaire d'ajout d'un questionnaire B7.
		 */
		public function add( $personne_id ) {
			$this->edit ($personne_id);
		}

		/**
		 * Formulaire de modification d'un questionnaire B7.
		 *
		 * @throws NotFoundException
		 */
		public function edit( $personne_id, $id = null ) {
			// Annulation
			if (!empty ($this->request->data)) {
				if (isset ($this->request->data['Cancel'])) {
					$this->redirect('index/'.$personne_id);
				}
			}

			// Sauvegarde du formulaire et redirection
			if (!empty ($this->request->data)) {
				if( $this->Questionnaireb7pdv93->saveAll( $this->request->data, array( 'validate' => 'only' ) ) ) {
					$this->Questionnaireb7pdv93->begin();

					if (!is_null($id)) {
						$this->request->data['Questionnaireb7pdv93']['id'] = $id;
					}

					if( $this->Questionnaireb7pdv93->saveFormData( $personne_id, $this->request->data ) ) {
						$this->Questionnaireb7pdv93->commit();
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( array( 'action' => 'index', $personne_id ) );
					}
					else {
						$this->Questionnaireb7pdv93->rollback();
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
			}

			// Menu gauche du dossier allocataire
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			// Recherche de l'allocataire
			$personne = $this->Questionnaireb7pdv93->getPersonne ($personne_id);
			$this->set( compact( 'personne' ) );

			// Options
			$options = $this->WebrsaQuestionnairesb7pdvs93->options();
			$this->set( compact( 'options' ) );

			// Récupération du questionnaire B7 (dans le cas d'une modification)
			if( $this->action == 'edit' ) {
				$questionnaireb7pdv93 = $this->Questionnaireb7pdv93->getById ($id);
				$dateemploi = explode ('-', $questionnaireb7pdv93['Questionnaireb7pdv93']['dateemploi']);

				$data['Questionnaireb7pdv93']['dureeemploi'] = $questionnaireb7pdv93['Questionnaireb7pdv93']['dureeemploi_id'];
				$data['Questionnaireb7pdv93']['typeemploi'] = $questionnaireb7pdv93['Questionnaireb7pdv93']['typeemploi_id'];
				$data['Questionnaireb7pdv93']['dateemploi']['day'] = '1';
				$data['Questionnaireb7pdv93']['dateemploi']['month'] = $dateemploi[1];
				$data['Questionnaireb7pdv93']['dateemploi']['year'] = $dateemploi[0];

				$data['Expproromev3']['familleromev3_id'] = $questionnaireb7pdv93['Expproromev3']['familleromev3_id'];
				$data['Expproromev3']['domaineromev3_id'] = $questionnaireb7pdv93['Expproromev3']['familleromev3_id'].'_'.$questionnaireb7pdv93['Expproromev3']['domaineromev3_id'];
				$data['Expproromev3']['metierromev3_id'] = $questionnaireb7pdv93['Expproromev3']['domaineromev3_id'].'_'.$questionnaireb7pdv93['Expproromev3']['metierromev3_id'];
				$data['Expproromev3']['appellationromev3_id'] = $questionnaireb7pdv93['Expproromev3']['metierromev3_id'].'_'.$questionnaireb7pdv93['Expproromev3']['appellationromev3_id'];

				// Début ROME V3
				$dsp = $this->Questionnaireb7pdv93->Expproromev3->prepareFormDataAddEdit( $data );
				// Fin ROME V3
				$this->request->data = $data;
			}

			// Vue
			$this->render( '_add_edit' );
		}

		/**
		 * Suppression d'un questionnaire B7 et redirection vers l'index.
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->{$this->modelClass}->begin();

			if($this->{$this->modelClass}->delete($id)) {
				$this->{$this->modelClass}->commit();
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->{$this->modelClass}->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->redirect($this->referer());
		}
	}
?>