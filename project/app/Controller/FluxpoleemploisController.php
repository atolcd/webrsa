<?php
	/**
	 * Code source de la classe FluxpoleemploisController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe FluxpoleemploisController ...
	 *
	 * @package app.Controller
	 */
	class FluxpoleemploisController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Fluxpoleemplois';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'search',
				)
			),
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'search',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Allocataires',
			'Csv',
			'Locale',
			'Xform',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Search',
			'Search.SearchForm',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Fluxpoleemploi',
			'Fluxpoleemploirejet',
			'Foyer',
			'Personne',
			'Informationpe',
			'Historiqueetatpe',
			'Dossier',
			'Option',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'personne' => 'read',
			'historique' => 'read',
			'rejets' => 'read',
			'search' => 'read',
		);

		/**
		 *
		 * @param integer $foyer_id
		 * @param integer $personne_id
		 */
		public function personne ($foyer_id, $personne_id = null) {
			// Menu dossier gauche
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );

			// Liste des personnes du foyer
			$query = array (
				'recursive' => -1,
				'conditions' => array (
					'Personne.foyer_id' => $foyer_id
				),
				'joins' => array(
					array(
						'alias' => 'Prestation',
						'table' => 'prestations',
						'type' => 'INNER',
						'conditions' => array(
							'Prestation.personne_id = Personne.id'
						)
					)
				),
				// Pour garder le même ordre que dans le menu gauche.
				'order' => array (
					'CASE Prestation.rolepers WHEN \'DEM\' THEN 1 WHEN \'CJT\' THEN 2 ELSE 3 END ASC',
					'Personne.nom ASC',
					'Personne.prenom ASC',
				)
			);
			$personnes = $this->Personne->find ('list', $query);

			// Personne à afficher (la première si non défini)
			if (is_null($personne_id)) {
				foreach ($personnes as $key => $value) {
					$personne_id = $key;
					break;
				}
			}

			// Personne à afficher
			$personne = $this->Personne->find ('first', array ('recursive' => -1, 'conditions' => array ('id' => $personne_id)));

			// Récupération des données Pôle Emploi de la personne à afficher
			$query = array (
				'recursive' => -1,
				'conditions' => array (
					$this->Informationpe->qdConditionsJoinPersonneOnValues ('Informationpe', $personne['Personne']),
					//'Fluxpoleemploi.nir' => '189099935289634'//trim($personne['Personne']['nir'])
				)
			);
			$donnees = $this->Informationpe->find ('first', $query);

			// Vue
			$this->set (compact ('personnes', 'donnees', 'foyer_id', 'personne_id'));
		}

		/**
		 *
		 * @param integer $foyer_id
		 * @param integer $personne_id
		 */
		public function historique ($foyer_id, $personne_id = null) {
			// Menu dossier gauche
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'foyer_id' => $foyer_id ) ) );

			// Liste des personnes du foyer
			$query = array (
				'recursive' => -1,
				'conditions' => array (
					'Personne.foyer_id' => $foyer_id
				),
				'joins' => array(
					array(
						'alias' => 'Prestation',
						'table' => 'prestations',
						'type' => 'INNER',
						'conditions' => array(
							'Prestation.personne_id = Personne.id'
						)
					)
				),
				// Pour garder le même ordre que dans le menu gauche.
				'order' => array (
					'CASE Prestation.rolepers WHEN \'DEM\' THEN 1 WHEN \'CJT\' THEN 2 ELSE 3 END ASC',
					'Personne.nom ASC',
					'Personne.prenom ASC',
				)
			);
			$personnes = $this->Personne->find ('list', $query);

			// Personne à afficher (la première si non défini)
			if (is_null($personne_id)) {
				foreach ($personnes as $key => $value) {
					$personne_id = $key;
					break;
				}
			}

			// Récupération de l'historique des données Pôle Emploi de la personne à afficher
			$historiques = null;
			// Infos de la personne
			$personne = $this->Personne->find ('first', array ('conditions' => array ('Personne.id' => $personne_id)));
			// Id de Informationpe
			$query = array (
				'conditions' => array(
					$this->Informationpe->qdConditionsJoinPersonneOnValues ('Informationpe', $personne['Personne']),
				),
			);
			$informationpe = $this->Informationpe->find ('first', $query);
			// Historique
			if (isset ($informationpe['Informationpe']['id'])) {
				$query = array (
					'conditions' => array(
						'informationpe_id' => $informationpe['Informationpe']['id'],
					),
					'order' => 'date DESC',
				);
				$historiques = $this->Historiqueetatpe->find ('all', $query);
			}

			// Vue
			$this->set (compact ('personnes', 'historiques', 'foyer_id', 'personne_id'));
		}

		/**
		 *
		 */
		public function rejets () {
			//
			$query = array (
				'recursive' => -1,
			);
			$donnees = $this->Fluxpoleemploirejet->find ('count', $query);

			// Vue
			$this->set (compact ('donnees'));
		}

		/**
		 * Moteur de recherche par dossier / allocataire
		 *
		 * @return void
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesFluxpoleemplois' );
			$Recherches->search();
		}

	}
?>
