<?php
	/**
	 * Code source de la classe RepsddtefpController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe RepsddtefpController ...
	 *
	 * @package app.Controller
	 */
	class RepsddtefpController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Repsddtefp';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Search.SearchPrg' => array(
				'actions' => array(
					'suivicontrole',
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
			'Locale',
			'Paginator',
			'Xform',
			'Xpaginator',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Apre',
			'Budgetapre',
			'Etatliquidatif',
			'Option',
			'Repddtefp',
			'Zonegeographique',
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
			'exportcsv',
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
			'suivicontrole' => 'read',
		);

		/**
		*
		*/

		public function beforeFilter() {
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', '1024M');
			parent::beforeFilter();
			$this->set( 'sexe', $this->Option->sexe() );
			$this->set( 'qual', $this->Option->qual() );
			$this->set( 'natureAidesApres', $this->Option->natureAidesApres() );
			$this->set( 'options', (array)Hash::get( $this->Apre->enums(), 'Apre' ) );
			$this->set( 'sect_acti_emp', ClassRegistry::init('Contratinsertion')->enum('sect_acti_emp') );

			$this->set( 'quinzaine', $this->Option->quinzaine() );
		}

		/**
		*   Données pour le premier reporting bi mensuel ddtefp
		*/

		public function index() {

			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? array_values( $mesZonesGeographiques ) : array() );

			if( !empty( $this->request->data ) ) {
				$annee = Set::classicExtract( $this->request->data, 'Repddtefp.annee' );
				$semestre = Set::classicExtract( $this->request->data, 'Repddtefp.semestre' );
				$numcom = Set::classicExtract( $this->request->data, 'Repddtefp.numcom' );

				$listeSexe = $this->Repddtefp->listeSexe( $annee, $semestre, $numcom );
				$listeAge = $this->Repddtefp->listeAge( $annee, $semestre, $numcom );

				$this->set( compact( 'listeSexe', 'listeAge', 'numcom' ) );
			}

			if( Configure::read( 'Zonesegeographiques.CodesInsee' ) ) {
				$this->set( 'mesCodesInsee', $this->Zonegeographique->listeCodesInseeLocalites( $mesCodesInsee, $this->Session->read( 'Auth.User.filtre_zone_geo' ) ) );
			}
			else {
				$this->set( 'mesCodesInsee', $this->Dossier->Foyer->Adressefoyer->Adresse->listeCodesInsee() );
			}
		}

		/**
		*   Données à envoyer pour afficehr reporting du suivi et controle de l'enveloppe apre
		*/

		public function suivicontrole() {

			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? array_values( $mesZonesGeographiques ) : array() );

			if( !empty( $this->request->data ) ) {
				$queryData = $this->Repddtefp->search( $this->request->data );
				$queryData['limit'] = 10;
				$this->paginate = array( 'Etatliquidatif' => $queryData );
				$apres = $this->paginate( 'Etatliquidatif' );

				///Détails de l'enveloppe APRE
				$detailsEnveloppe = $this->Repddtefp->detailsEnveloppe( $this->request->data );
				$this->set( 'detailsEnveloppe', $detailsEnveloppe );


				$this->set( 'apres', $apres );
			}


			if( Configure::read( 'Zonesegeographiques.CodesInsee' ) ) {
				$this->set( 'mesCodesInsee', $this->Zonegeographique->listeCodesInseeLocalites( $mesCodesInsee, $this->Session->read( 'Auth.User.filtre_zone_geo' ) ) );
			}
			else {
				$this->set( 'mesCodesInsee', $this->Dossier->Foyer->Adressefoyer->Adresse->listeCodesInsee() );
			}
		}

		/**
		* Export du tableau en CSV
		*/

		public function exportcsv() {
			$queryData = $this->Repddtefp->search( Hash::expand( $this->request->params['named'], '__' ) );
			unset( $queryData['limit'] );

			$this->Etatliquidatif->Apre->deepAfterFind = false;
			$apres = $this->Etatliquidatif->find( 'all', $queryData );

			$this->layout = '';
			$this->set( compact( 'apres' ) );

		}
	}
?>