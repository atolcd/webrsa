<?php
	/**
	 * Code source de la classe ConfigurationsController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe ConfigurationsController permet d'insérer et de lire en base de donnée le paramétrage de l'application WebRSA.
	 *
	 * @package app.Controller
	 */
	class ConfigurationsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Configurations';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Configuration', 'ConfigurationCategorie', 'Configurationhistorique');

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'index',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Configurations:edit'
		);

		/**
		 * Moteur de recherche par catégorie (fichiers) de configurations
		 */
 		public function index() {
			$this->departement = (int)Configure::read( 'Cg.departement' );

			$recherche = $this->Session->read('Search.Configuration');
			if (empty( $this->request->data ) && !empty ($recherche)) {
				$this->request->data = $recherche;
			}

			$search = (array)Hash::get( $this->request->data, 'Search' );

			if( !empty( $search ) ) {
				if($this->request->data['Configuration']['lib_variable'] !== '')
					$search['Configuration']['lib_variable'] = $this->request->data['Configuration']['lib_variable'];
				$query = $this->Configuration->_query($search);
				$query['limit'] = false;
				$results = $this->Configuration->find('all', $query);

				$this->Session->write('Search.Configuration', $this->request->data);

				$this->set( compact( 'results' ) );
			}

			$options['ConfigurationCategorie']['lib_categorie'] = $this->ConfigurationCategorie->find('list', array('fields' => 'ConfigurationCategorie.lib_categorie'));
			$this->set( compact( 'options' ) );
		}

		/**
		 * Formulaire de modification d'une configuration
		 *
		 */
 		public function edit( $id = null ) {
			if(!empty($this->request->data)) {
				$JSONdecode = json_decode($this->request->data['Configuration']['value_variable'], true);
				$JSONresult = json_encode($JSONdecode, JSON_UNESCAPED_UNICODE);
				$this->request->data['Configuration']['value_variable'] = $JSONresult;
				// Il y a une erreur dans le JSON
				if(is_null($JSONdecode))
				{
					$this->Flash->error( __d('configuration','Configuration.erreurJSON'));
					$this->redirect(array('controller' => 'configurations', 'action' => 'edit', $id));
				}
				$this->Configurationhistorique->saveHisto($this->request->data['Configuration']);
			}
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'edit' ) );
			$histo = $this->Configurationhistorique->getHisto($id);
			$options = $this->viewVars['options'];
			$this->set( compact( 'options', 'histo' ) );
		}
	}
?>