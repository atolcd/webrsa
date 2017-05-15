<?php

	/**
	 * Code source de la classe Criterestraitementspcgs66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::import('Sanitize');

	/**
	 * La classe Criterestraitementspcgs66Controller ...
	 *
	 * @deprecated since version 3.0.0
	 * @see Traitementspcgs66::search() et Traitementspcgs66::exportcsv()
	 * @package app.Controller
	 */
	class Criterestraitementspcgs66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Criterestraitementspcgs66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
				),
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Default',
			'Default2',
			'Locale',
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Criteretraitementpcg66',
			'Option',
			'Traitementpcg66',
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
			'exportcsv' => 'read',
			'index' => 'read',
		);

		/**
		 *
		 */
		protected function _setOptions() {
			$this->set('qual', $this->Option->qual());
			$this->set('etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa'));
			$this->set('typepdo', $this->Traitementpcg66->Personnepcg66->Dossierpcg66->Typepdo->find('list'));
			$this->set('originepdo', $this->Traitementpcg66->Personnepcg66->Dossierpcg66->Originepdo->find('list'));
			$this->set('descriptionpdo', $this->Traitementpcg66->Descriptionpdo->find('list'));
			$this->set('motifpersonnepcg66', $this->Traitementpcg66->Personnepcg66->Situationpdo->find('list', array('conditions' => array('Situationpdo.isactif' => '1'))));
			$this->set('statutpersonnepcg66', $this->Traitementpcg66->Personnepcg66->Statutpdo->find('list', array('conditions' => array('Statutpdo.isactif' => '1'))));

			$this->set('gestionnaire', $this->User->find(
							'list', array(
						'fields' => array(
							'User.nom_complet'
						),
						'conditions' => array(
							'User.isgestionnaire' => 'O'
						),
						'order' => array('User.nom ASC', 'User.prenom ASC')
							)
					)
			);

			$this->set('polesdossierspcgs66', $this->User->Poledossierpcg66->find(
							'list', array(
						'fields' => array(
							'Poledossierpcg66.name'
						),
						'conditions' => array(
							'Poledossierpcg66.isactif' => '1'
						),
						'order' => array('Poledossierpcg66.name ASC', 'Poledossierpcg66.id ASC')
							)
					)
			);

			$options = $this->Traitementpcg66->enums();
			$this->set('natpf', ClassRegistry::init('Detailcalculdroitrsa')->enum('natpf'));
	// 			$etatdossierpcg = $options['Traitementpcg66']['etatdossierpcg'];
	//
	// 			$options = array_merge(
	// 				$options,
	// 				$this->Traitementpcg66->Personnepcg66->Traitementpcg66->enums()
	// 			);
			$this->set(compact('options'));
			$this->set('exists', array('1' => 'Oui', '0' => 'Non'));
		}

		/**
		 *
		 */
		public function index() {
			$this->Gestionzonesgeos->setCantonsIfConfigured();

			$mesZonesGeographiques = $this->Session->read('Auth.Zonegeographique');
			$mesCodesInsee = (!empty($mesZonesGeographiques) ? $mesZonesGeographiques : array() );


			$params = $this->request->data;
			if (!empty($params)) {
				$paginate = array('Traitementpcg66' => $this->Criteretraitementpcg66->search($this->request->data, $mesCodesInsee, $mesZonesGeographiques));
				$paginate['Traitementpcg66']['limit'] = 10;
				$paginate['Traitementpcg66']['conditions'][] = WebrsaPermissions::conditionsDossier();

				$paginate['Traitementpcg66']['fields'][] = $this->Jetons2->sqLocked('Dossier', 'locked');

				$progressivePaginate = !Hash::get($this->request->data, 'Traitementpcg66.paginationNombreTotal');

				$this->paginate = $paginate;
				$criterestraitementspcgs66 = $this->paginate('Traitementpcg66', array(), array(), $progressivePaginate);

				$this->set(compact('criterestraitementspcgs66'));
			} else {
				$progressivePaginate = SearchProgressivePagination::enabled($this->name, $this->action);
				if (!is_null($progressivePaginate)) {
					$this->request->data['Traitementpcg66']['paginationNombreTotal'] = !$progressivePaginate;
				}
			}

			$this->_setOptions();
			$this->set('mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee());

			$this->set('structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'conditions' => array( 'Structurereferente.orientation' => 'O' ) + $this->InsertionsBeneficiaires->conditions['structuresreferentes'], 'prefix' => false ) ) );
			$this->set('referentsparcours', $this->InsertionsBeneficiaires->referents());

			$this->render($this->action);
		}

		/**
		 * Export au format CSV des résultats de la recherche des traitements PCGs.
		 *
		 * @return void
		 */
		public function exportcsv() {
			$data = Hash::expand($this->request->params['named'], '__');

			$mesZonesGeographiques = (array) $this->Session->read('Auth.Zonegeographique');
			$mesCodesInsee = (!empty($mesZonesGeographiques) ? $mesZonesGeographiques : array() );

			$querydata = $this->Criteretraitementpcg66->search(
					$data, $mesCodesInsee, $mesZonesGeographiques
			);
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();

			unset($querydata['limit']);
	//            $querydata['limit'] = 10;

			$results = $this->Traitementpcg66->find(
					'all', $querydata
			);

			$this->_setOptions();

	//
	//debug($results);
	//die();
			$this->layout = '';
			$this->set(compact('results'));
		}

	}
?>
