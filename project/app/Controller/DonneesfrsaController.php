<?php
	/**
	 * Code source de la classe DonneesfrsaController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe DonneesfrsaController ...
	 *
	 * @package app.Controller
	 */
	class DonneesfrsaController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Donneesfrsa';

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
			'Option',
			'Personne',
			'Foyer',
			'Personnelangue',
			'Personnefrsadiplomexper',
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
		public function personne($foyer_id, $personne_id = null) {

			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id ) ));

			// Personne à afficher (la première si non défini)
			if (is_null($personne_id)) {
				$query = array(
						'recursive' => -1,
						'fields' => array(
							'Personne.id'
						),
						'conditions' => array(
							'Personne.foyer_id' => $foyer_id
						)
					);
				$personne = $this->Personne->find(	'all', $query );
				$personne_id = $personne[0]['Personne']['id'];
			}

			$query = array(
					'contain' => array(
						'Personnelangue',
						'Personnefrsadiplomexper'
					),
					'conditions' => array('Personne.id' => $personne_id)
				);

			$this->set('personnes', $personnes = $this->Personne->find(	'all', $query ));

			$this->set('options', $this->_options());

			// Pour liste personnes dans les tabs
			$this->set('personnes_list', $this->_getPersonnes_list($personnes[0]['Personne']['foyer_id']));

			/**
			 * Extraction du contain au 1er niveau
			 */
			$this->_extractAndSet(
				array(
					'Personnelangue','Personnefrsadiplomexper'
				), $personnes
			);

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
		 * Permet d'obtenir la liste des Personnes d'un foyer pour affichage des onglets
		 *
		 * @param integer $foyer_id
		 * @return array
		 */
		protected function _getPersonnes_list($foyer_id) {
			$this->Foyer->forceVirtualFields = true;
			return $this->Foyer->find(
				'all', array(
					'fields' => array(
						'Personne.id',
						'Personne.foyer_id',
						'Personne.nom_complet',
						'Prestation.rolepers',
					),
					'contain' => false,
					'joins' => array(
						$this->Foyer->join('Personne'),
						$this->Foyer->Personne->join('Prestation'),
					),
					'conditions' => array(
						'Personne.foyer_id' => $foyer_id
					),
					'order' => array(
						'("Prestation"."rolepers" = \'DEM\')' => 'DESC NULLS LAST',
						'("Prestation"."rolepers" = \'CJT\')' => 'DESC NULLS LAST',
						'("Prestation"."rolepers" = \'ENF\')' => 'DESC NULLS LAST',
						'Personne.nom' => 'ASC',
						'Personne.prenom' => 'ASC',
						'Personne.id' => 'ASC',
					)
				)
			);
		}

		/**
		 * Ajoute les options extraites des données FRSA
		 *
		 * @return array
		 */
		protected function _options() {
			return Hash::merge(
				$this->Personne->enums(),
				$this->Personnelangue->enums(),
				$this->Personnefrsadiplomexper->enums()
			);
		}
	}
?>
