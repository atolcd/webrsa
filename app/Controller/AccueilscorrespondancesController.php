<?php
	/**
	 * Code source de la classe AccueilscorrespondancesController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe AccueilscorrespondancesController ...
	 *
	 * @package app.Controller
	 */
	class AccueilscorrespondancesController extends AppController
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read'
		);

		/**
		 * Page de correspondance utilisateurs / référents
		 */
		public function index() {

			if (!empty ($this->request->data)) {
				$this->loadModel('User');
				$this->loadModel('Referent');

				if (isset ($this->request->data['Search_Correspondance_nom'])) {
					$this->request->data['Search']['Correspondance']['nom'] = $this->request->data['Search_Correspondance_nom'];
				}
				if (isset ($this->request->data['Search_Correspondance_prenom'])) {
					$this->request->data['Search']['Correspondance']['prenom'] = $this->request->data['Search_Correspondance_prenom'];
				}
				if (isset ($this->request->data['Search_Correspondance_groupe'])) {
					$this->request->data['Search']['Correspondance']['groupe'] = $this->request->data['Search_Correspondance_groupe'];
				}

				// Enregistrement de la correspondance utilisateurs / référents
				if (isset ($this->request->data['referent_id'])) {
					foreach ($this->request->data['referent_id'] as $key => $value) {
						if (is_numeric($value)) {
							$this->User->query("UPDATE users SET accueil_referent_id = ".$value." WHERE id = ".$key.";");
						}
					}

					$this->Flash->success( __( 'Save->success' ) );
				}

				// Enregistrement de la correspondance utilisateurs / référents
				if (isset ($this->request->data['reference_affichage'])) {
					foreach ($this->request->data['reference_affichage'] as $key => $value) {
							$this->User->query("UPDATE users SET accueil_reference_affichage = '".$value."' WHERE id = ".$key.";");
					}

					$this->Flash->success( __( 'Save->success' ) );
				}

				// Recherche des utilisateurs
				$query = array ('recursive' => -1);
				$query['conditions'] = array ();

				if (isset ($this->request->data['Search']['Correspondance'])) {
					$conditions = $this->request->data['Search']['Correspondance'];

					if (!empty ($conditions['nom'])) {
						$query['conditions']['User.nom LIKE'] =  trim (str_replace('*', '%', $conditions['nom']));
					}
					if (!empty ($conditions['prenom'])) {
						$query['conditions']['User.prenom LIKE'] = trim (str_replace('*', '%', $conditions['prenom']));
					}
					if (!empty ($conditions['groupe'])) {
						$query['conditions']['User.group_id'] = $conditions['groupe'];
					}
				}

				$users = $this->User->find ('all', $query);
				$referents = $this->Referent->find ('all', array ('recursive' => -1));

				$this->set(compact('users', 'referents'));
			}

			// Liste des groupes d'utilisateurs
			$this->loadModel('Group');
			$query = array ('recursive' => -1, 'order by' => 'name ASC');
			$results = $this->Group->find ('all', $query);
			$groups = array ();
			$groups[] = '';
			foreach ($results as $result) {
				$groups[$result['Group']['id']] = $result['Group']['name'];
			}
			$this->set(compact('groups'));

			$this->set( 'options', $this->User->enums() );
		}
	}
?>