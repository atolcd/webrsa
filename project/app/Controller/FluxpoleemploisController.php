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
		public $aucunDroit = array();

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
			'Motifetatpe',
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
			'exportcsv' => 'read'
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

			// Chargement de la vue, si elle existe, avec le numéro de département en suffixe.
			$this->render (__FUNCTION__, null, true);
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

			// Chargement de la vue, si elle existe, avec le numéro de département en suffixe.
			$this->render (__FUNCTION__, null, true);
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
		 * Change l'état Pôle Emploi de la personne
		 * @param int dossier_id
		 * @param int personne_id
		 */
		public function updateEtat($dossier_id, $personne_id) {
			$options = array();
			$redirect = array( 'controller' => 'dossiers', 'action' => 'view', $dossier_id );

			// Affichage du menu du dossier
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

			// Récupération des informations de la personne
			$personne = $this->Personne->getPersonne($personne_id);
			$infosPE = $this->Informationpe->derniereInformation($personne);

			// Redirection à l'index si l'état PE est vide ou déjà en état cessation ou radiation
			if ( empty($infosPE) ) {
				$this->Flash->error( __m( 'Fluxpoleemplois.erreurEtatVide' ) );
				$this->redirect( $redirect );
			} else if(in_array($infosPE['Historiqueetatpe']['etat'], array('cessation', 'radiation'))) {
				$this->Flash->error( __m( 'Fluxpoleemplois.erreurEtat' ) . $infosPE['Historiqueetatpe']['etat'] );
				$this->redirect( $redirect );
			}
			$etatActuel = $infosPE['Historiqueetatpe']['etat'];

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( $redirect );
			}

			// Récupération de la liste des motifs activés
			$options['motifs'] = $this->Motifetatpe->listOptions();

			// Liste des nouveaux états disponibles
			$options['etatpe'] = array(
				'radiation',
				'cessation'
			);

			// Tentative de sauvegarde
			if( !empty( $this->request->data ) ) {
				if($this->request->data['Modifetatpe']['lib_motif'] == '') {
					$this->Flash->error( __m( 'Fluxpoleemplois.erreurMotifVide' ) );
					$this->redirect( array( 'controller' => 'fluxpoleemplois', 'action' => 'updateEtat', $dossier_id, $personne_id ) );
				} else if( $this->request->data['Modifetatpe']['lib_etatpe'] == '') {
					$this->Flash->error( __m( 'Fluxpoleemplois.erreurEtatPEVide' ) );
					$this->redirect( array( 'controller' => 'fluxpoleemplois', 'action' => 'updateEtat', $dossier_id, $personne_id ) );
				}

				$motif = $options['motifs'][$this->request->data['Modifetatpe']['lib_motif']];
				$etat = $options['etatpe'][$this->request->data['Modifetatpe']['lib_etatpe']];

				// Récupération des anciennes données historiquePE
				$histoPEToSave = $infosPE['Historiqueetatpe'][0];

				// Modification et ajout de l'historique avec les nouvelles informations
				$histoPEToSave['id'] = null;
				$histoPEToSave['date'] = date('Y/m/d');
				$histoPEToSave['etat'] = $etat;
				$histoPEToSave['code'] = null;
				$histoPEToSave['motif'] = $motif;
				$histoPEToSave['date_creation'] = date('Y/m/d h:i:s');
				$histoPEToSave['date_modification'] = date('Y/m/d h:i:s');

				// Récupération des anciennes données Informationpe
				$infoPEToSave = $infosPE['Informationpe'];

				// Modification de l'information PE actuel avec les nouvelles informations
				$infoPEToSave['date_creation'] = date('Y/m/d h:i:s');
				$infoPEToSave['date_modification'] = date('Y/m/d h:i:s');

				// Modification selon le nouvel état choisi
				if ($etat == 'radiation') {
					$histoPEToSave['inscription_date_radiation_ide'] = date('Y/m/d');
					$histoPEToSave['inscription_lib_radiation_ide'] = $motif;
					$infoPEToSave['inscription_date_radiation_ide'] = date('Y/m/d');
					$infoPEToSave['inscription_lib_radiation_ide'] = $motif;
				} else {
					$histoPEToSave['inscription_date_cessation_ide'] = date('Y/m/d');
					$histoPEToSave['inscription_lib_cessation_ide'] = $motif;
					$infoPEToSave['inscription_date_cessation_ide'] = date('Y/m/d');
					$infoPEToSave['inscription_lib_cessation_ide'] = $motif;
				}

				// Sauvegarde
				if($this->Informationpe->save($infoPEToSave) && $this->Historiqueetatpe->save($histoPEToSave) ) {
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( $redirect );
				}
				else{
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->set(compact('etatActuel', 'options'));
		}

		/**
		 * Moteur de recherche par dossier / allocataire
		 *
		 * @return void
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesFluxpoleemplois' );
			$Recherches->search();

			// Chargement de la vue, si elle existe, avec le numéro de département en suffixe.
			$this->render (__FUNCTION__, null, true);
		}

		/**
		 * Export CSV de la recherche par dossier / allocataire
		 *
		 * @return void
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesFluxpoleemplois' );
			$Recherches->exportcsv();
		}

	}
?>
