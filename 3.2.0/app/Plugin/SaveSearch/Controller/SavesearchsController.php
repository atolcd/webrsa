<?php
	/**
	 * Code source de la classe SavesearchsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');

	/**
	 * La classe SavesearchsController ...
	 *
	 * @package app.Controller
	 */
	class SavesearchsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Savesearchs';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(

		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Ajax2' => array(
				'className' => 'Prototype.PrototypeAjax',
				'useBuffer' => false
			),
			'Cake1xLegacy.Ajax',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Observer' => array(
				'className' => 'Prototype.PrototypeObserver',
				'useBuffer' => true
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Savesearch',
		);

		/**
		 * Utilise les droits d'un autre Controller::action
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
			'ajax_geturl',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'ajax_geturl' => 'read',
			'delete' => 'update',
			'delete_group' => 'update',
			'edit' => 'update',
			'edit_group' => 'update',
			'index' => 'read',
			'save' => 'update',
			'save_group' => 'update',
		);

		/**
		 * Affichage des recherches sauvegardés
		 */
		public function index() {
			$nom_complet = '("User"."prenom" || \' \' || "User"."nom") AS "User__nom_complet"';
			$results = $this->Savesearch->find('all',
				array(
					'fields' => array_merge(
						$this->Savesearch->fields(),
						array(
							$nom_complet => $nom_complet
						)
					),
					'conditions' => array(
						'OR' => array(
							'Savesearch.user_id' => $this->Session->read('Auth.User.id'),
							array(
								'Savesearch.group_id' => $this->Session->read('Auth.User.group_id'),
								'Savesearch.isforgroup' => 1
							)
						),
					),
					'joins' => array(
						$this->Savesearch->join('User')
					)
				)
			);

			$options['Savesearch'] = array(
				'isforgroup' => array(0 => 'Non', 1 => 'Oui'),
				'isformenu' => array(0 => 'Non', 1 => 'Oui'),
			);

			$this->set(compact('results', 'options'));
		}

		/**
		 * Sauvegarde d'une recherche
		 */
		public function save() {
			// Vérifications
			$this->assert(!empty($this->request->data));

			// Redirige soit vers le moteur de recherche où c'est fait la sauvegarde, ou vers l'index de Savesearch en cas d'edition
			$redirectTo = explode('/', trim($this->referer(null, true), '/'));
			if (Hash::get($redirectTo, '0') === 'savesearchs') {
				$redirectTo = '/savesearchs';
			} else {
				$redirectTo = $this->referer(null, true);
			}

			$alreadyExists = $this->Savesearch->find('first',
				array(
					'fields' => 'Savesearch.name',
					'conditions' => array(
						'OR' => array(
							'Savesearch.user_id' => $this->Session->read('Auth.User.id'),
							array(
								'Savesearch.group_id' => $this->Session->read('Auth.User.group_id'),
								'Savesearch.isforgroup' => 1
							)
						),
						'Savesearch.url' => $this->request->data['Savesearch']['url']
					)
				)
			);

			if (!empty($alreadyExists) && !Hash::get($this->request->data, 'Savesearch.id')) {
				$this->Session->setFlash('Une sauvegarde existe déjà pour les même critères : '.Hash::get($alreadyExists, 'Savesearch.name'), 'default', array( 'class' => 'error' ) );
				$this->redirect($redirectTo);
			}

			// Vérification du formulaire à la main pour affichage flash des erreurs
			$v =& $this->request->data;

			// Vérification de la présence du champ name (seul élément obligatoire par l'utilisateur)
			if (!Hash::get($v, 'Savesearch.name')) {
				$this->Session->setFlash('Le Nom de la recherche doit être renseigné', 'default', array( 'class' => 'error' ) );
				$this->redirect($this->referer(null, true));
			}

			// Vérification des champs hidden obligatoire
			if (!Hash::get($v, 'Savesearch.url')
				|| !Hash::get($v, 'Savesearch.action')
				|| !Hash::get($v, 'Savesearch.controller')
			) {
				$this->Session->setFlash('Une erreur s\'est produite lors de l\'enregistrement.', 'default', array( 'class' => 'error' ) );
				$this->redirect($redirectTo);
			}

			// Si c'est une sauvegarde pour le groupe, on vérifi avant tout les droits
			if (!Hash::get($v, 'Savesearch.isforgroup')) {
				return $this->save_group();
			}

			else {
				return $this->_save();
			}

		}

		/**
		 * Public uniquement pour les permissions
		 */
		public function save_group() {
			if (!WebrsaPermissions::check($this->name, __FUNCTION__)) {
				$this->request->data['Savesearch']['isforgroup'] = 0;
			}

			return $this->_save();
		}

		/**
		 * Logique de sauvegarde
		 */
		protected function _save() {
			$data = $this->request->data;
			if ($this->action !== 'edit_group') {
				$data['Savesearch']['user_id'] = $this->Session->read('Auth.User.id');
			}
			$data['Savesearch']['group_id'] = $this->Session->read('Auth.User.group_id');

			if ($this->Savesearch->save($data, array( 'atomic' => false ))) {
				$this->Session->setFlash('Recherche sauvegardée avec succès.', 'default', array( 'class' => 'success' ) );
			} else {
				$this->Session->setFlash('Une erreur s\'est produite lors de l\'enregistrement.', 'default', array( 'class' => 'error' ) );
			}

			// Retire le cache
			$this->Session->write('Module.Monmenu', null);
			Cache::delete('element_'.$this->Session->read( 'Auth.User.username' ), 'views');

			// Redirige soit vers le moteur de recherche où c'est fait la sauvegarde, ou vers l'index de Savesearch en cas d'edition
			$redirectTo = explode('/', trim($this->referer(null, true), '/'));
			if (Hash::get($redirectTo, '0') === 'savesearchs') {
				$redirectTo = '/savesearchs';
			} else {
				$redirectTo = $this->referer(null, true);
			}

			$this->redirect($redirectTo);
		}

		/**
		 * Formulaire d'edition
		 *
		 * @params integer $savesearch_id
		 * @throws ForbiddenException
		 */
		public function edit($savesearch_id) {
			// Vérification que l'utilisateur a bien le droit de modifier cette sauvegarde
			$conditions = array();
			if (WebrsaPermissions::check($this->name, 'edit_group')) {
				$conditions['OR'] = array(
					'Savesearch.user_id' => $this->Session->read('Auth.User.id'),
					array(
						'Savesearch.group_id' => $this->Session->read('Auth.User.group_id'),
						'Savesearch.isforgroup' => 1,
					)
				);
			} else {
				$conditions['Savesearch.user_id'] = $this->Session->read('Auth.User.id');
			}

			$result = $this->Savesearch->find('first',
				array(
					'conditions' => array(
						'Savesearch.id' => $savesearch_id,
					) + $conditions
				)
			);

			if (empty($result)) {
				throw new ForbiddenException;
			}

			if (!empty($this->request->data)) {
				if (isset( $this->request->data['Cancel'])) {
					$this->redirect(array('controller' => strtolower($this->name), 'action' => 'index'));
				}

				$this->request->data['Savesearch']['id'] = $savesearch_id;
				$this->request->data['Savesearch']['controller'] = Hash::get($result, 'Savesearch.controller');
				$this->request->data['Savesearch']['action'] = Hash::get($result, 'Savesearch.action');
				$this->request->data['Savesearch']['url'] = Hash::get($result, 'Savesearch.url');
				$this->request->data['Savesearch']['group_id'] = $this->Session->read('Auth.User.group_id');
				$this->request->data['Savesearch']['user_id'] = Hash::get($result, 'Savesearch.user_id');

				return $this->save();
			} else {
				$this->request->data = $result;
			}

			$options['Savesearch'] = array(
				'isforgroup' => array(0 => 'Non', 1 => 'Oui'),
				'isformenu' => array(0 => 'Non', 1 => 'Oui'),
			);

			$this->set('options', $options);
		}

		/**
		 * Permet de controller les droits sur l'édition d'une sauvegarde pour le groupe
		 *
		 * @param integer $savesearch_id
		 */
		public function edit_group($savesearch_id) {
			$this->view = 'edit';
			return $this->edit($savesearch_id);
		}

		/**
		 * Suppression d'un enregistrement personnel
		 *
		 * @param integer $savesearch_id
		 * @throws ForbiddenException
		 */
		public function delete($savesearch_id) {
			$result = $this->Savesearch->find('first',
				array(
					'fields' => 'Savesearch.id',
					'conditions' => array(
						'Savesearch.id' => $savesearch_id,
						'Savesearch.user_id' => $this->Session->read('Auth.User.id')
					)
				)
			);

			if (empty($result)) {
				throw new ForbiddenException;
			}

			$this->_delete($savesearch_id);
		}

		/**
		 * Suppression d'un enregistrement de groupe
		 *
		 * @param integer $savesearch_id
		 * @throws ForbiddenException
		 */
		public function delete_group($savesearch_id) {
			$result = $this->Savesearch->find('first',
				array(
					'fields' => 'Savesearch.id',
					'conditions' => array(
						'Savesearch.id' => $savesearch_id,
						'Savesearch.group_id' => $this->Session->read('Auth.User.group_id'),
						'Savesearch.isforgroup' => 1
					)
				)
			);

			if (empty($result)) {
				throw new ForbiddenException;
			}

			$this->_delete($savesearch_id);
		}

		/**
		 * Suppression d'un enregistrement générique
		 *
		 * @param integer $savesearch_id
		 */
		protected function _delete($savesearch_id) {
			if ($this->Savesearch->delete($savesearch_id)) {
				$this->Session->setFlash('Recherche supprimée avec succès.', 'default', array( 'class' => 'success' ) );
			} else {
				$this->Session->setFlash('Une erreur s\'est produite lors de la suppression.', 'default', array( 'class' => 'error' ) );
			}

			$this->redirect(array('controller' => strtolower($this->name), 'action' => 'index'));
		}

		/**
		 * Permet par ajax, à partir d'un id, de récupérer l'url pour redirection
		 */
		public function ajax_geturl() {
			$savesearch_id = $this->request->data['id'];
			$result = $this->Savesearch->find('first', array('fields' => 'url', 'conditions' => array('id' => $savesearch_id)));

			$this->set('json', $this->request->base.Hash::get($result, 'Savesearch.url'));
			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}
	}
?>