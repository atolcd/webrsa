<?php
	/**
	 * Code source de la classe IndicateursmensuelsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe IndicateursmensuelsController ...
	 *
	 * @package app.Controller
	 */
	class IndicateursmensuelsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Indicateursmensuels';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Gestionzonesgeos',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Indicateurmensuel',
			'Serviceinstructeur',
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
			'contratsinsertion' => 'read',
			'index' => 'read',
			'nombre_allocataires' => 'read',
			'orientations' => 'read',
		);

		/**
		 * Indicateurs mensuels
		 */
		public function index() {
			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$this->_index_58();
			}
			else {
				$this->_index_66_93();
			}
		}

		/**
		 * Indicateurs mensuels des CG 66 et 93
		 */
		protected function _index_66_93() {
			$annee = Set::extract( $this->request->data, 'Indicateurmensuel.annee' );
			if( !empty( $annee ) ) {
				$indicateurs = $this->Indicateurmensuel->liste( $annee );
				$this->set( compact( 'indicateurs' ) );
			}

			$this->render( 'index' );
		}


		/**
		 * Indicateurs mensuels du CG 58
		 */
		protected function _index_58() {
			$Sitecov58 = ClassRegistry::init( 'Sitecov58' );

			if( !empty( $this->request->data ) ) {
				$results = array(
					'Personnecaf' => $this->Indicateurmensuel->personnescaf58( $this->request->data ),
					'Dossiercov58' => $this->Indicateurmensuel->dossierscovs58( $this->request->data ),
					'Dossierep' => $this->Indicateurmensuel->dossierseps58( $this->request->data ),
				);

				$this->set( compact( 'results' ) );
			}

			// Options du formulaire de recherche
			$years = range( date( 'Y' ), 2009, -1 );
			$options = array(
				'Search' => array(
					'year' => array_combine( $years, $years ),
					'sitecov58_id' => $Sitecov58->find( 'list' )
				)
			);
			$this->set( compact( 'options' ) );

			$this->render( 'index_58' );
		}

		/**
		 *
		 */
		public function nombre_allocataires() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Indicateurmensuel->nombreAllocataires( $this->request->data );
				$this->set( compact( 'results' ) );
			}

			$this->set( 'servicesinstructeurs', $this->Serviceinstructeur->find( 'list' ) );
			$this->Gestionzonesgeos->setCantonsIfConfigured();
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			$this->set( 'title_for_layout', 'Nombre d\'allocataires' );
			$this->render( 'nombre_allocataires' ); // FIXME
		}

		/**
		 *
		 */
		public function orientations() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Indicateurmensuel->orientations( $this->request->data );
				$this->set( compact( 'results' ) );
			}

			$this->set( 'servicesinstructeurs', $this->Serviceinstructeur->find( 'list' ) );
			$this->Gestionzonesgeos->setCantonsIfConfigured();
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			$this->set( 'title_for_layout', 'L\'orientation des personnes SDD' );
			$this->render( 'nombre_allocataires' ); // FIXME
		}

		/**
		 *
		 */
		public function contratsinsertion() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Indicateurmensuel->contratsinsertion( $this->request->data );
				$this->set( compact( 'results' ) );
			}

			$this->set( 'servicesinstructeurs', $this->Serviceinstructeur->find( 'list' ) );
			$this->Gestionzonesgeos->setCantonsIfConfigured();
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			$this->set( 'title_for_layout', 'Les CER' );
			$this->render( 'nombre_allocataires' ); // FIXME
		}
	}
?>