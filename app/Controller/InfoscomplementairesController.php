<?php
	/**
	 * Code source de la classe InfoscomplementairesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe InfoscomplementairesController ...
	 *
	 * @package app.Controller
	 */
	class InfoscomplementairesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Infoscomplementaires';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Jetons2',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Theme',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Personne',
			'Activite',
			'Allocationsoutienfamilial',
			'Creancealimentaire',
			'Dossier',
			'Option',
			'Titresejour',
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
			'view' => 'read',
		);

		/**
		 *
		 */
		public function beforeFilter() {
			$return = parent::beforeFilter();

			$this->set( 'qual', $this->Option->qual() );
			$this->set( 'sitfam', $this->Option->sitfam() );
			$this->set( 'act', ClassRegistry::init('Activite')->enum('act') );
			$this->set( 'reg', ClassRegistry::init('Activite')->enum('reg') );
			$this->set( 'paysact', ClassRegistry::init('Activite')->enum('paysact') );
			$this->set( 'orioblalim', ClassRegistry::init('Creancealimentaire')->enum('orioblalim') );
			$this->set( 'etatcrealim', ClassRegistry::init('Creancealimentaire')->enum('etatcrealim') );
			$this->set( 'verspa', $this->Creancealimentaire->enum('verspa') );
			$this->set( 'topjugpa', ClassRegistry::init('Creancealimentaire')->enum('topjugpa') );
			$this->set( 'motidiscrealim', ClassRegistry::init('Creancealimentaire')->enum('motidiscrealim') );
			$this->set( 'engproccrealim', ClassRegistry::init('Creancealimentaire')->enum('engproccrealim') );
			$this->set( 'topdemdisproccrealim', ClassRegistry::init('Creancealimentaire')->enum('topdemdisproccrealim') );
			$this->set( 'sitasf', ClassRegistry::init('Allocationsoutienfamilial')->enum('sitasf') );
			$this->set( 'parassoasf', ClassRegistry::init('Allocationsoutienfamilial')->enum('parassoasf') );

			return $return;
		}

		/**
		 *
		 * @param integer $id
		 */
		public function view( $id = null ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $id ) ) );

			/** Tables necessaire à l'ecran de synthèse

			  OK -> Dossier
			  OK -> Foyer

			  OK -> Creance
			  OK -> Dossiercaf
			  OK -> Personne (DEM/CJT)
			  OK -> allocationssoutienfamilial
			  OK -> activites
			  OK -> dossierscaf (premier/dernier)
			  OK -> titressejours
			  OK ->  creancesalimentaires 
			 */
			$details = array( );

			$qd_tDossier = array(
				'conditions' => array(
					'Dossier.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$tDossier = $this->Dossier->find( 'first', $qd_tDossier );
			$details = Set::merge( $details, $tDossier );

			$qd_tFoyer = array(
				'conditions' => array(
					'Foyer.dossier_id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$tFoyer = $this->Dossier->Foyer->find( 'first', $qd_tFoyer );
			$details = Set::merge( $details, $tFoyer );

			/**
			  Personnes
			 */
			$personnesFoyer = $this->Personne->find(
				'all',
				array(
					'conditions' => array(
						'Personne.foyer_id' => $tFoyer['Foyer']['id'],
						'Prestation.rolepers' => array( 'DEM', 'CJT' )
					),
					'contain' => array(
						'Prestation',
						'Dossiercaf'
					)
				)
			);

			$roles = Set::extract( '{n}.Prestation.rolepers', $personnesFoyer );

			foreach( $roles as $index => $role ) {
				///Créances alimentaires
				$tCreancealimentaire = $this->Creancealimentaire->find(
					'first',
					array(
						'conditions' => array( 'Creancealimentaire.personne_id' => $personnesFoyer[$index]['Personne']['id'] ),
						'recursive' => -1,
						'order' => 'Creancealimentaire.ddcrealim DESC',
					)
				);

				if( !empty( $tCreancealimentaire ) ) {
					$personnesFoyer[$index]['Creancealimentaire'] = $tCreancealimentaire['Creancealimentaire'];
				}

				///Titres séjour
				$tTitresejour = $this->Titresejour->find(
					'first',
					array(
						'conditions' => array(
							'Titresejour.personne_id' => $personnesFoyer[$index]['Personne']['id']
						),
						'order' => 'Titresejour.ddtitsej DESC',
						'recursive' => -1
					)
				);
				if( !empty( $tTitresejour ) ) {
					$personnesFoyer[$index]['Titresejour'] = $tTitresejour['Titresejour'];
				}

				///Activités
				$tActivite = $this->Activite->find(
					'first',
					array(
						'conditions' => array(
							'Activite.personne_id' => $personnesFoyer[$index]['Personne']['id']
						),
						'order' => 'Activite.ddact DESC',
						'recursive' => -1
					)
				);
				if( !empty( $tActivite ) ) {
					$personnesFoyer[$index]['Activite'] = $tActivite['Activite'];
				}

				///Allocation au soutien familial
				$tAllocationsoutienfamilial = $this->Allocationsoutienfamilial->find(
					'first',
					array(
						'conditions' => array(
							'Allocationsoutienfamilial.personne_id' => $personnesFoyer[$index]['Personne']['id']
						),
						'order' => 'Allocationsoutienfamilial.ddasf DESC',
						'recursive' => -1
					)
				);
				if( !empty( $tAllocationsoutienfamilial ) ) {
					$personnesFoyer[$index]['Allocationsoutienfamilial'] = $tAllocationsoutienfamilial['Allocationsoutienfamilial'];
				}

				$details[$role] = $personnesFoyer[$index];
			}
			$this->set( 'details', $details );
		}

	}
?>