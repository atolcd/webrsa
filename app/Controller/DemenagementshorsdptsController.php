<?php
	/**
	 * Code source de la classe DemenagementshorsdptsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');

	/**
	 * La classe DemenagementshorsdptsController ...
	 *
	 * @package app.Controller
	 */
	class DemenagementshorsdptsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Demenagementshorsdpts';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'DossiersMenus',
			'Jetons2',
			'Search.Filtresdefaut' => array(
				'search',
				'search1',
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'search1' => array('filter' => 'Search'),
					'search'
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
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Search.SearchForm',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Demenagementhorsdpt',
			'Personne',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'search1' => 'Demenagementshorsdpts:search',
			'exportcsv1' => 'Demenagementshorsdpts:exportcsv',
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
			'exportcsv1' => 'read',
			'search' => 'read',
			'search1' => 'read',
		);

		/**
		 * Complète a query avec les champs et les restrictions liées à l'utilisateur.
		 * Si l'utilisateur est un externe, des conditions sur les dates d'emménagement
		 * seront également ajoutées.
		 *
		 * @deprecated since 3.0.00
		 *
		 * @param array $query
		 * @return array
		 */
		protected function _completeQuery( array $query ) {
			$query['fields'] = array(
				'Dossier.id',
				'Dossier.matricule',
				'Personne.nom_complet',
				'Adressefoyer.dtemm',
				'Adresse.numcom',
				'Adresse.nomcom',
				$this->Personne->Foyer->Adressefoyer->Adresse->sqVirtualField( 'localite' ),
				'Adressefoyer2.dtemm',
				'Adresse2.numcom',
				'Adresse2.nomcom',
				str_replace( 'Adresse', 'Adresse2', $this->Personne->Foyer->Adressefoyer->Adresse->sqVirtualField( 'localite' ) ),
				'Adressefoyer3.dtemm',
				'Adresse3.numcom',
				'Adresse3.nomcom',
				str_replace( 'Adresse', 'Adresse3', $this->Personne->Foyer->Adressefoyer->Adresse->sqVirtualField( 'localite' ) ),
			);

			// Conditions sur l'adresse, mais pour les adresses de rang 2 et 3
			$conditions = $query['conditions'];
			$query['conditions'] = array();
			$query = $this->Allocataires->completeSearchQuery( $query );
			$query = $this->Demenagementhorsdpt->searchConditionsAdressesRang0203( $query, $conditions );

			// Conditions sur les dates d'emménagement pour les externes
			if( Configure::read( 'Cg.departement' ) == 93 && ( strpos( $this->Session->read( 'Auth.User.type' ), 'externe_' ) === 0 ) ) {
				$departement = Configure::read( 'Cg.departement' );

				$query['conditions'][] = array(
					'OR' => array(
						// L'allocataire a quitté le CG en rang 01 et l'adresse de rang 2 ...
						array(
							'Adresse2.numcom LIKE' => "{$departement}%",
							"CAST( DATE_PART( 'year', \"Adressefoyer\".\"dtemm\" ) + 1 || '-03-31' AS DATE ) >= NOW()",
						),
						// L'allocataire a quitté l'adresse de rang 3 ...
						array(
							'Adresse3.numcom LIKE' => "{$departement}%",
							"CAST( DATE_PART( 'year', \"Adressefoyer2\".\"dtemm\" ) + 1 || '-03-31' AS DATE ) >= NOW()",
						),
					)
				);
			}

			return $query;
		}

		/**
		 * Moteur de recherche des fiches de prescription.
		 *
		 * @deprecated since 3.0.00
		 * @see search()
		 */
		public function search1() {
			if( Hash::check( $this->request->data, 'Search' ) ) {
				$query = $this->Demenagementhorsdpt->search( $this->request->data['Search'] );
				$query = $this->_completeQuery( $query );

				$results = $this->Allocataires->paginate( $query );

				$this->set( compact( 'results' ) );
			}

			$options = Hash::merge(
				$this->Allocataires->options(),
				$this->Demenagementhorsdpt->options( array( 'allocataire' => false ) )
			);
			$this->set( compact( 'options' ) );
		}

		/**
		 * Export CSV des résultats de la recherche.
		 *
		 * @deprecated since 3.0.00
		 * @see exportcsv()
		 */
		public function exportcsv1() {
			$search = (array)Hash::get( (array)Hash::expand( $this->request->params['named'], '__' ), 'Search' );
			$query = $this->Demenagementhorsdpt->search( $search );
			$query = $this->_completeQuery( $query );
			$query = $this->Components->load( 'Search.SearchPaginator' )->setPaginationOrder( $query );
			unset( $query['limit'] );

			$results = $this->Personne->find( 'all', $query );
			$options = Hash::merge(
				$this->Allocataires->options(),
				$this->Demenagementhorsdpt->options( array( 'allocataire' => false ) )
			);

			$this->set( compact( 'results', 'options' ) );
			$this->layout = null;
		}

		/**
		 * Moteur de recherche
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDemenagementshorsdpts' );
			$Recherches->search( array('modelName' => 'Personne') );
		}

		/**
		 * Export du tableau de résultats de la recherche
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesDemenagementshorsdpts' );
			$Recherches->exportcsv( array('modelName' => 'Personne') );
		}
	}
?>
