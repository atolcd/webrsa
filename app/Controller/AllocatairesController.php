<?php
	/**
	 * Code source de la classe AllocatairesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');

	Configure::write(
		'Filtresdefaut.Allocataires_index',
		array(
			'Search' => array(
				'Dossier' => array(
					'dernier' => 1
				),
				'Situationdossierrsa' => array(
					'etatdosrsa_choice' => 1,
					'etatdosrsa' => array( '2', '3', '4' )
				),
				'Calculdroitrsa' => array(
					'toppersdrodevorsa' => 1
				),
				'Pagination' => array(
					'nombre_total' => 1
				)
			)
		)
	);

	/**
	 * La classe AllocatairesController ...
	 *
	 * @package app.Controller
	 */
	class AllocatairesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Allocataires';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'Search.Filtresdefaut' => array(
				'index'
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'index' => array(
						'filter' => 'Search'
					),
				)
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Allocataires',
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
			'Allocataire',
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
			'exportcsv' => 'read',
			'index' => 'read',
		);

		/**
		 * Pagination sur les allocataires.
		 */
		public function index() {
			if( Hash::check( $this->request->data, 'Search' ) ) {
				$query = $this->Allocataire->search( $this->request->data['Search'] );

				// Test en LEFT JOIN
				/*$joins = array(
					'Calculdroitrsa' => 'LEFT OUTER',
					'Foyer' => 'INNER',
					'Prestation' => 'LEFT OUTER',
					'Adressefoyer' => 'LEFT OUTER',
					'Dossier' => 'INNER',
					'Adresse' => 'LEFT OUTER',
					'Situationdossierrsa' => 'LEFT OUTER',
					'Detaildroitrsa' => 'LEFT OUTER',
					'PersonneReferent' => 'LEFT OUTER',
					'Referentparcours' => 'LEFT OUTER',
					'Structurereferenteparcours' => 'LEFT OUTER'
				);
				$query = $this->Allocataire->search( $this->request->data['Search'], $joins );*/

				$query['fields'] = array(
					'Personne.id',
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Dossier.matricule',
					'Personne.nom',
					'Personne.prenom',
					'Prestation.rolepers',
					'Adresse.nomcom',
				);

				$query = $this->Allocataires->completeSearchQuery( $query );

				$results = $this->Allocataires->paginate( $query );

				$this->set( compact( 'results' ) );
			}

			$options = $this->Allocataires->options();
			$this->set( compact( 'options' ) );
		}

		/**
		 * Export CSV des résultats du moteur de recherche.
		 */
		public function exportcsv() {
			$search = (array)Hash::get( (array)Hash::expand( $this->request->params['named'], '__' ), 'Search' );
			$query = $this->Allocataire->search( $search );

			$query['fields'] = array(
				'Personne.id',
				'Dossier.numdemrsa',
				'Dossier.dtdemrsa',
				'Dossier.matricule',
				'Personne.nom',
				'Personne.prenom',
				'Prestation.rolepers',
				'Adresse.nomcom',
			);

			$query = $this->Allocataires->completeSearchQuery( $query );
			$results = $this->Personne->find( 'all', $query );

			$options = $this->Allocataires->options();

			$this->layout = '';
			$this->set( compact( 'results', 'options' ) );
		}
	}
?>
