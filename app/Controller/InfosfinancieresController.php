<?php
	/**
	 * Code source de la classe InfosfinancieresController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe InfosfinancieresController ...
	 *
	 * @package app.Controller
	 */
	class InfosfinancieresController  extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Infosfinancieres';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Gestionzonesgeos',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array(
					'indexdossier',
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
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Infofinanciere',
			'Cohorteindu',
			'Dossier',
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
			'view' => 'Infosfinancieres:index',
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
			'indexdossier' => 'read',
			'view' => 'read',
		);

        protected function _setOptions() {
			$this->set( 'type_allocation', ClassRegistry::init('Infofinanciere')->enum('type_allocation') );
			$this->set( 'natpfcre', ClassRegistry::init('Infofinanciere')->enum('natpfcre') );
			$this->set( 'typeopecompta', ClassRegistry::init('Infofinanciere')->enum('typeopecompta') );
			$this->set( 'sensopecompta', ClassRegistry::init('Infofinanciere')->enum('sensopecompta') );
			$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa') );
		}

		/**
		 *
		 */
		public function indexdossier() {
			$options = array(
				'annees' => $this->Infofinanciere->range(),
				'numcom' => $this->Gestionzonesgeos->listeCodesInsee()
			);
			$this->set( compact( 'options' ) );

			if( !empty( $this->request->data ) ) {
				$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
				$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

				$this->Dossier->begin(); // Pour les jetons

				$paginate = $this->Infofinanciere->search( $mesCodesInsee, $this->Session->read( 'Auth.User.filtre_zone_geo' ), $this->request->data );
				$paginate['limit'] = 15;

				$this->paginate = $paginate;
				$infosfinancieres = $this->paginate( 'Infofinanciere' );

				$this->set( 'infosfinancieres', $infosfinancieres );

				$this->Dossier->commit();
			}
            $this->_setOptions();
		}

		/**
		 *
		 * @param integer $dossier_id
		 */
		public function index( $dossier_id = null ) {
			//Vérification du format de la variable
			$this->assert( valid_int( $dossier_id ), 'invalidParameter' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) ) );

            $querydata = array(
                'fields' => array_merge(
                    $this->Infofinanciere->fields(),
                    array(
                        'Dossier.matricule'
                    )
                ),
                'conditions' => array( 'Infofinanciere.dossier_id' => $dossier_id ),
                'contain' => array(
                    'Dossier'
                ),
                'order' => array(
					'Infofinanciere.moismoucompta DESC'
				)
            );
            $this->paginate = $querydata;
            $infosfinancieres = $this->paginate( 'Infofinanciere' );


			$qd_foyer = array(
				'conditions' => array(
					'Foyer.dossier_id' => $dossier_id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$foyer = $this->Dossier->Foyer->find( 'first', $qd_foyer );

			$personne = $this->Dossier->Foyer->Personne->find(
				'first',
				array(
					'conditions' => array(
						'Personne.foyer_id' => $foyer['Foyer']['id'],
						'( Prestation.natprest = \'RSA\' OR Prestation.natprest = \'PFA\' )',
						'( Prestation.rolepers = \'DEM\' )',
					),
					'recursive' => -1,
					'joins' => array(
						$this->Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) )
					)
				)
			);

			$this->assert( !empty( $personne ), 'invalidParameter' );
			$this->set( 'personne', $personne );

			$this->set( 'dossier_id', $dossier_id );
			$this->set( 'infosfinancieres', $infosfinancieres );
            $this->_setOptions();
		}

		/**
		 *
		 * @param integer $infofinanciere_id
		 */
		public function view( $infofinanciere_id = null ) {
			// Vérification du format de la variable
			$this->assert( valid_int( $infofinanciere_id ), 'error404' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $this->Infofinanciere->dossierId( $infofinanciere_id ) ) ) );

			$infofinanciere = $this->Infofinanciere->find(
				'first',
				array(
					'conditions' => array(
						'Infofinanciere.id' => $infofinanciere_id
					),
				'recursive' => -1
				)
			);

			$this->assert( !empty( $infofinanciere ), 'error404' );
			$dossier_id = Set::classicExtract( $infofinanciere, 'Infofinanciere.dossier_id' );

			$qd_foyer = array(
				'conditions' => array(
					'Foyer.dossier_id' => $dossier_id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$foyer = $this->Dossier->Foyer->find( 'first', $qd_foyer );

			$personne = $this->Dossier->Foyer->Personne->find(
				'first',
				array(
					'conditions' => array(
						'Personne.foyer_id' => $foyer['Foyer']['id'],
						'( Prestation.natprest = \'RSA\' OR Prestation.natprest = \'PFA\' )',
						'( Prestation.rolepers = \'DEM\' )',
					),
					'contain' => array(
						'Prestation'
					)
				)
			);

			$this->assert( !empty( $personne ), 'invalidParameter' );
			$this->set( 'personne', $personne );

			// Assignations à la vue
			$this->set( 'dossier_id', $infofinanciere['Infofinanciere']['dossier_id'] );
			$this->set( 'infofinanciere', $infofinanciere );
			$this->set( 'urlmenu', '/infosfinancieres/index/'.$foyer['Foyer']['id'] );
            $this->_setOptions();
		}

		/**
		 *
		 */
		public function exportcsv() {
			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			$options = $this->Infofinanciere->search( $mesCodesInsee, $this->Session->read( 'Auth.User.filtre_zone_geo' ), Hash::expand( $this->request->params['named'], '__' ) );

			unset( $options['limit'] );
			$infos = $this->Infofinanciere->find( 'all', $options );

            $this->_setOptions();
			$this->layout = ''; // FIXME ?
			$this->set( compact( 'headers', 'infos' ) );
		}
	}
?>