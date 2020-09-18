<?php
	/**
	 * Code source de la classe HistoriquesdroitsController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe DonneescafController ...
	 *
	 * @package app.Controller
	 */
	class HistoriquesdroitsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Historiquesdroits';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'DossiersMenus',
			'Jetons2',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default2',
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
			'Historiquedroit',
			'Foyer',
			'Option',
			'Personne',
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
			'personne' => 'read',
		);

		/**
		 * Liste des donnees d'une personne
		 *
		 * @param integer $personne_id
		 */
		public function personne($personne_id) {
			$this->assert(valid_int($personne_id), 'invalidParameter');
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id)));

			$this->set('historiques', $historiques = $this->Historiquedroit->find(
				'all', array(
					'fields' => $this->Historiquedroit->fields(),
					'conditions' => array('Historiquedroit.personne_id' => $personne_id),
					'order' => array('Historiquedroit.created DESC')
				)
			));

			$this->set('options', $this->_options());

		}

		/**
		 * Envoi une variable à la vue contenant le contain d'un enregistrement
		 *
		 * Exemple:
		 *  $toExtract = array('Prestation')
		 *	$data = array(0 => array('Prestation' => array(0 => array('id' => 1))))
		 *	une variable nommé <strong>$prestations</strong> contiendra : array(0 => array('Prestation' => array('id' => 1)))
		 *
		 * @param array $toExtract - Liste des contain à extraire
		 * @param array $data - Données du find all
		 */
		protected function _extractAndSet($toExtract, $data) {
			foreach ($toExtract as $extractName) {
				$varName = Inflector::pluralize(Inflector::underscore($extractName));
				$$varName = array();
				foreach (Hash::extract($data, '0.'.$extractName) as $extractedData) {
					${$varName}[][$extractName] = $extractedData;
				}
				$this->set($varName, $$varName);
			}
		}

		/**
		 * Ajoute les options extraites des données CAF
		 *
		 * @return array
		 */
		protected function _options() {
			return Hash::merge(
				$this->Personne->enums(),
				$this->Personne->Historiquedroit->enums()
			);
		}
	}
?>
