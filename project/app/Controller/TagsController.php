<?php
	/**
	 * Code source de la classe TagsController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'WebrsaAccessTags', 'Utility' );

	/**
	 * La classe TagsController ...
	 *
	 * @package app.Controller
	 */
	class TagsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Tags';

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
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'cohorte' => array('filter' => 'Search'),
					'cohorte_heberge' => array('filter' => 'Search'),
				),
			),
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
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Tag',
			'Foyer',
			'Personne',
			'WebrsaCohorteTag',
			'WebrsaTag',
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
			'add' => 'create',
			'cancel' => 'update',
			'cohorte' => 'update',
			'delete' => 'delete',
			'edit' => 'update',
			'index' => 'read',
			'tag_gestionsdoublons_index' => 'update',
		);

		/**
		 * Action d'ajout d'un tag à une personne
		 *
		 * @param integer $id
		 */
		public function add($modele, $id) {
			$this->WebrsaAccesses->check(null, $id);

			// Initialisation
			$this->_init_add_edit($modele, $id);

			// Sauvegarde du formulaire
			if(!empty($this->request->data)) {
				$this->_save_add_edit($modele, $id);
			}

			// Vue
			$this->view = 'edit';
		}

		/**
		 * Action d'edition du tag d'une personne
		 *
		 * @param integer $tag_id
		 */
		public function edit($tag_id) {
			$this->WebrsaAccesses->check($tag_id);

			// Initialisation
			$result = $this->Tag->findTagById($tag_id);
			$this->assert(!empty($result), 'invalidParameter');

			$id = Hash::get($result, 'EntiteTag.fk_value');
			$modele = Hash::get($result, 'EntiteTag.modele');
			$this->_init_add_edit($modele, $id);

			$this->set(
				compact(
					'result'
				)
			);

			// Sauvegarde du formulaire
			if(!empty($this->request->data)) {
				$this->_save_add_edit($modele, $id);
			}
			else {
				$this->request->data = $result;
			}
		}

		/**
		 * Initialisation du formulaire d'edition d'un tag
		 * Jeton et redirection en cas de retour
		 *
		 * @param string $modele
		 * @param integer $id
		 */
		protected function _init_add_edit($modele, $id) {
			// Validité de l'url
			$this->assert(valid_int($id) && isset($this->Tag->EntiteTag->{$modele}), 'invalidParameter');

			// Gestion des jetons
			$dossier_id = $this->Tag->EntiteTag->{$modele}->dossierId($id);
			$this->Jetons2->get($dossier_id);

			// Redirection si Cancel
			if(isset($this->request->data['Cancel'])) {
				$this->Jetons2->release($dossier_id);
				$this->redirect(array('action' => 'index', $modele, $id));
			}

			$urlmenu = implode('/', array('', 'tags', 'index', $modele, $id));

			// Variables pour la vue
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('id' => $dossier_id)));
			$this->set(compact('personne_id', 'dossier_id', 'urlmenu'));

			$this->_setOptions();
		}

		/**
		 * Sauvegarde d'un formulaire add ou edit
		 *
		 * @param integer $id
		 */
		protected function _save_add_edit($modele, $id) {
			$this->Tag->begin();

			$this->Tag->create($this->request->data);
			$success = $this->Tag->save( null, array( 'atomic' => false ) );
			$this->request->data['EntiteTag']['fk_value'] = $id;
			$this->request->data['EntiteTag']['modele'] = $modele;
			$this->request->data['EntiteTag']['tag_id'] = $this->Tag->id;

			$entite = $this->Tag->EntiteTag->find('first',
				array(
					'fields' => 'id',
					'conditions' => $this->request->data['EntiteTag']
				)
			);

			if (empty($entite)) {
				$this->Tag->EntiteTag->create($this->request->data);
				$success = $this->Tag->EntiteTag->save( null, array( 'atomic' => false ) ) && $success;
			}

			if($success) {
				$this->Tag->commit();
				$this->Jetons2->release($this->viewVars['dossier_id']);
				$this->Flash->success( __( 'Save->success' ) );
				$this->redirect(array( 'controller' => 'tags','action' => 'index', $modele, $id));
			}
			else {
				$id && $this->set('fichiers', $this->Fileuploader->fichiers($id));
				$this->Tag->rollback();
				$this->Flash->error( __( 'Save->error' ) );
			}
		}

		/**
		 * Liste des dossiers PCG d'un foyer
		 *
		 * @param string $modele
		 * @param integer $id
		 */
		public function index($modele, $id) {
			$this->assert(valid_int($id) && isset($this->Tag->EntiteTag->{$modele}), 'invalidParameter');

			$dossier_id = $this->Tag->EntiteTag->{$modele}->dossierId($id);
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('id' => $dossier_id)));

			$conditions = array(
				'modele' => $modele,
				'fk_value' => $id
			);

			$query = $this->WebrsaTag->completeVirtualFieldsForAccess(
				$this->Tag->queryTagByCondition($conditions) + array(
					'order' => array('Tag.created' => 'DESC')
				)
			);
			$paramsAccess = $this->WebrsaTag->getParamsForAccess($id, WebrsaAccessTags::getParamsList() + compact('modele'));
			$ajoutPossible = Hash::get($paramsAccess, 'ajoutPossible');
			$results = WebrsaAccessTags::accesses($this->Tag->find('all', $query), $paramsAccess);

			$infos = $this->Tag->EntiteTag->{$modele}->find('first', array('conditions' => array("{$modele}.id" => $id)));

			// Incrustation de texte dans la traduction
			switch ($modele) {
				case 'Personne': $infos['Info']['tag'] = 'de '.Hash::get($infos, 'Personne.nom_complet'); break;
				case 'Foyer': $infos['Info']['tag'] = 'du Foyer'; break;
				default: $infos['Info']['tag'] = ''; break;
			}

			$this->set(compact('results', 'dossier_id', 'id', 'modele', 'infos', 'ajoutPossible'));
			$this->_setOptions();
		}

		/**
		 * Suppression d'un <élément> et redirection vers l'index.
		 *
		 * @param integer $tag_id
		 */
		public function delete($tag_id) {
			$this->WebrsaAccesses->check($tag_id);

			$this->{$this->modelClass}->begin();

			if($this->{$this->modelClass}->delete($tag_id)) {
				$this->{$this->modelClass}->commit();
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->{$this->modelClass}->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->redirect($this->referer());
		}

		/**
		 * Cohorte
		 */
		public function cohorte() {
			$tags = $this->Tag->Valeurtag->find('all', array());
			if(isset($tags) && !empty($tags)) {
				$this->WebrsaCohorteTag->cohorteFields = array(
					'Personne.id' => array('type' => 'hidden', 'label' => '', 'hidden' => true),
					'Foyer.id' => array('type' => 'hidden', 'label' => '', 'hidden' => true),
					'Tag.selection' => array('type' => 'checkbox', 'label' => '&nbsp;'),
					'EntiteTag.modele' => array('type' => 'select', 'label' => ''),
					'Tag.valeurtag_id' => array('type' => 'select', 'label' => ''),
					'Tag.limite' => array('type' => 'date', 'label' => '', 'dateFormat' => 'DMY', 'minYear' => date('Y'), 'maxYear' => date('Y')+4, 'empty' => true),
					'Tag.commentaire' => array('type' => 'textarea', 'label' => ''),
				);
				$Recherches = $this->Components->load('WebrsaCohortesTags');
				$Recherches->cohorte(array('modelName' => 'Dossier'));
			} else {
				$this->Flash->error( __m('Tags::Cohorte::notags') );
				$this->redirect(array(
					'controller' => 'accueils',
					'action' => 'index'
				));
			}
		}

		/**
		 * Annule un tag
		 *
		 * @param integer $tag_id
		 */
		public function cancel($tag_id) {
			$this->WebrsaAccesses->check($tag_id);

			$data = array(
				'id' => $tag_id,
				'etat' => 'annule'
			);

			$this->{$this->modelClass}->begin();

			if($this->{$this->modelClass}->save( $data, array( 'atomic' => false ) )) {
				$this->{$this->modelClass}->commit();
				$this->Flash->success( 'Annulation effectuée' );
			}
			else {
				$this->{$this->modelClass}->rollback();
				$this->Flash->error( 'Erreur lors de l\'annulation' );
			}

			$this->redirect($this->referer());
		}

		/**
		 * Options à renvoyer à la vue
		 *
		 * @return array
		 */
		protected function _setOptions() {
			$options = $this->Tag->enums();

			$query = array(
				'fields' => array(
					'Categorietag.name',
					'Valeurtag.id',
					'Valeurtag.name'
				),
				'joins' => array(
					$this->Tag->Valeurtag->join('Categorietag')
				),
			);

			if (!is_null(Configure::read( 'tag.affichage.actif.attribution' ))) {
				$query['conditions']['Valeurtag.actif'] = Configure::read( 'tag.affichage.actif.attribution' );
			}

			$results = $this->Tag->Valeurtag->find('all', $query);

			foreach ($results as $value) {
				$categorie = Hash::get($value, 'Categorietag.name') ? Hash::get($value, 'Categorietag.name') : 'Sans catégorie';
				$valeur = Hash::get($value, 'Valeurtag.name');
				$valeurtag_id = Hash::get($value, 'Valeurtag.id');
				$options['Tag']['valeurtag_id'][$categorie][$valeurtag_id] = $valeur;
			}

			$this->set(compact('options'));
		}

		/**
		 * Effectue un Tag pour le module Gestionsdoublons
		 *
		 * @param integer $foyer1_id
		 * @param integer $foyer2_id
		 */
		public function tag_gestionsdoublons_index($foyer1_id, $foyer2_id) {
			$valeur_tag = Configure::read('Gestionsdoublons.index.Tag.valeurtag_id'); // N'est pas un doublon
			$this->assert((valid_int($foyer1_id) && valid_int($foyer2_id) && $valeur_tag !== null), 'invalidParameter');

			$query = array(
				'fields' => array(
					'Dossier.numdemrsa',
				),
				'contain' => false,
				'joins' => array(
					$this->Tag->EntiteTag->Foyer->join('Dossier')
				),
			);
			$dataFoyer1 = $this->Tag->EntiteTag->Foyer->find('first', $query + array('conditions' => array('Foyer.id' => $foyer1_id)));
			$dataFoyer2 = $this->Tag->EntiteTag->Foyer->find('first', $query + array('conditions' => array('Foyer.id' => $foyer2_id)));

			$dataTag = array(
				'commentaire' => sprintf(
					'Dossier n°%s en lien avec le dossier n°%s',
					Hash::get($dataFoyer1, 'Dossier.numdemrsa'),
					Hash::get($dataFoyer2, 'Dossier.numdemrsa')
				),
				'valeurtag_id' => $valeur_tag,
				'etat' => 'traite'
			);

			$this->Tag->begin();

			$this->Tag->create($dataTag);
			$success = $this->Tag->save( null, array( 'atomic' => false ) );
			$tag_id = $this->Tag->id;

			$dataEntite = array(
				'tag_id' => $tag_id,
				'modele' => 'Foyer',
			);

			$this->Tag->EntiteTag->create($dataEntite + array('fk_value' => $foyer1_id));
			$success = $success && $this->Tag->EntiteTag->save( null, array( 'atomic' => false ) );

			$this->Tag->EntiteTag->create($dataEntite + array('fk_value' => $foyer2_id));
			$success = $success && $this->Tag->EntiteTag->save( null, array( 'atomic' => false ) );

			if ($success) {
				$this->Tag->commit();
				$this->Flash->success(
					sprintf('Tag effectué sur Dossiers %s et %s',
						Hash::get($dataFoyer1, 'Dossier.numdemrsa'),
						Hash::get($dataFoyer2, 'Dossier.numdemrsa')
					)
				);
            } else {
                $this->Tag->rollback();
                $this->Flash->error( 'Erreur lors du Tag' );
            }

			$this->redirect($this->referer());
		}
	}
?>
