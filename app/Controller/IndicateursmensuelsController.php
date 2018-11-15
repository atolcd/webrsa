<?php
	/**
	 * Code source de la classe IndicateursmensuelsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

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
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
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
			'rdvcer' => 'read',
			'rdvcervagues' => 'read'
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
		 * Indicateurs propres au 93
		 */
		public function rdvcer() {
			$annee = Set::extract( $this->request->data, 'Indicateurmensuel.annee' );
			$structureReferente =   Set::extract( $this->request->data, 'Indicateurmensuel.structuresreferentes' );
			$communautesSrs =   Set::extract( $this->request->data, 'Indicateurmensuel.communautessrs' );
			$commune =   Set::extract( $this->request->data, 'Indicateurmensuel.departement' );
			if( !empty( $annee ) ) {
				$indicateurs = $this->Indicateurmensuel->listeRdvCer( $annee, $structureReferente, $communautesSrs, $commune );
				$this->set( compact( 'indicateurs' ) );
			}

			//liste des PDV
			$StructRef = ClassRegistry::init( 'Structuresreferentes' );
			$options	=   $StructRef->find('list', array(
					'fields'=>array(
					'Structuresreferentes.id',
					'Structuresreferentes.lib_struc',
				),
				'conditions' => array( 'Structuresreferentes.typeorient_id' => '1' ),
				'order' => array( 'Structuresreferentes.lib_struc ASC' )
			));
			$this->set( compact( 'options' ) );

			//liste des EPT
			$comSrs = ClassRegistry::init( 'Communautessrs' );
				$optionsSrs	=   $comSrs->find('list', array(
				'fields'=>array(
					'Communautessrs.id',
					'Communautessrs.name',
				),
				'order' => array( 'Communautessrs.name ASC' )
			));
			$this->set( compact( 'optionsSrs' ) );

			//liste des communes
			$communes = ClassRegistry::init( 'Zonesgeographiques' );
			$optionsDpt	=   $communes->find('list', array(
				'fields'=>array(
					'Zonesgeographiques.codeinsee',
					'Zonesgeographiques.libelle',
				),
				'order' => array( 'Zonesgeographiques.libelle ASC' )
			));
			$this->set( compact( 'optionsDpt' ) );

			$this->render( 'rdvcer' );
		}

		/**
		 * Indicateurs propres au 93
		 */
		public function rdvcervagues() {
			$annee = Set::extract( $this->request->data, 'Indicateurmensuel.annee' );
			$structureReferente =   Set::extract( $this->request->data, 'Indicateurmensuel.structuresreferentes' );
			$communautesSrs =   Set::extract( $this->request->data, 'Indicateurmensuel.communautessrs' );
			$commune =   Set::extract( $this->request->data, 'Indicateurmensuel.departement' );
			if( !empty( $annee ) ) {
				$vagues	=	$this->Indicateurmensuel->listeVagues( $annee);
				$this->set( compact('vagues') );
				$indicateurs = $this->Indicateurmensuel->listeRdvCerVagues( $annee, $structureReferente, $communautesSrs, $commune );
				$this->set( compact( 'indicateurs' ) );
			}

			//liste des PDV
			$StructRef = ClassRegistry::init( 'Structuresreferentes' );
			$options	=   $StructRef->find('list', array(
					'fields'=>array(
					'Structuresreferentes.id',
					'Structuresreferentes.lib_struc',
				),
				'conditions' => array( 'Structuresreferentes.typeorient_id' => '1' ),
				'order' => array( 'Structuresreferentes.lib_struc ASC' )
			));
			$this->set( compact( 'options' ) );

			//liste des EPT
			$comSrs = ClassRegistry::init( 'Communautessrs' );
				$optionsSrs	=   $comSrs->find('list', array(
				'fields'=>array(
					'Communautessrs.id',
					'Communautessrs.name',
				),
				'order' => array( 'Communautessrs.name ASC' )
			));
			$this->set( compact( 'optionsSrs' ) );

			//liste des communes
			$communes = ClassRegistry::init( 'Zonesgeographiques' );
			$optionsDpt	=   $communes->find('list', array(
				'fields'=>array(
					'Zonesgeographiques.codeinsee',
					'Zonesgeographiques.libelle',
				),
				'order' => array( 'Zonesgeographiques.libelle ASC' )
			));
			$this->set( compact( 'optionsDpt' ) );

			$this->render( 'rdvcervagues' );
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