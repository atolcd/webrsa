<?php
	/**
	 * Code source de la classe TitressuivisController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'LocaleHelper', 'View/Helper' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'WebrsaAccessTitressuivisannulationsreductions', 'Utility' );
	App::uses( 'WebrsaAccessTitressuivisautresinfos', 'Utility' );
	App::uses( 'WebrsaAccessTitressuivis', 'Utility' );
	App::uses( 'Titressuivis', 'Controller');
	App::uses( 'Titressuivisannulationsreductions', 'Controller');
	App::uses( 'Titressuivisautresinfos', 'Controller');
	App::uses( 'Emails', 'Controller');

	/**
	 * La classe TitressuivisController s'occupe du suivi des annulations et réduction des titres de recettes
	 *
	 * @package app.Controller
	 */
	class TitressuivisController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Titressuivis';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Titresuivi',
			'Titresuiviinfopayeur',
			'Titresuiviannulationreduction',
			'Titresuiviautreinfo',
			'WebrsaTitresuiviannulationreduction',
			'WebrsaTitresuiviautreinfo',
			'Titrecreancier',
			'Creances',
			'Typetitrecreancierannulationreduction',
			'Typetitrecreancierautreinfo',
			'WebrsaTitrecreancier',
			'Creance',
			'WebrsaCreance',
			'Dossier',
			'Foyer',
			'Option',
		);

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses',
			'Fileuploader'
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Locale',
			'Paginator',
			'Default2',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Fileuploader',
			'Cake1xLegacy.Ajax'
		);

		/**
		 * L'id technique du titre de recette pour laquelle on veut les annulations/réduction.
		 *
		 * @param integer $titrecreancier_id
		 *
		 */
		public function index($titrecreancier_id) {
			// Initialisation / rappel du titre de recette en cours
			$titresCreanciers = $this->Titrecreancier->find('first',
				array(
					'conditions' => array(
						'Titrecreancier.id ' => $titrecreancier_id
					),
					'contain' => false
				)
			);
			// Get foyer Id
			$creance_id = $titresCreanciers['Titrecreancier']['creance_id'];
			$foyer_id = $this->Titrecreancier->foyerId( $creance_id );
			// Calcul montant réduit
			$contentIndex = $this->Titresuiviannulationreduction->getContext();
			$query = $this->Titresuiviannulationreduction->getQuery($titrecreancier_id);
			$titresAnnRed = $this->WebrsaAccesses->getIndexRecords($foyer_id, $query, $contentIndex);
			$montantReduitTotal = 0;
			if( !empty($titresAnnRed) ) {
				foreach($titresAnnRed as $titres ) {
					if ( $titres['Titresuiviannulationreduction']['etat'] == 'CERTIMP' ) {
						$montantReduitTotal += $titres['Titresuiviannulationreduction']['mtreduit'];
					}
				}
			}
			$titresCreanciers['Titrecreancier']['soldetitr'] = $titresCreanciers['Titrecreancier']['mntinit'] - $montantReduitTotal;
			// Traduction de l'état
			$titresCreanciers['Titrecreancier']['etat'] = (__d('titrecreancier', 'ENUM::ETAT::' . $titresCreanciers['Titrecreancier']['etat']));

			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array( 'foyer_id' => $foyer_id )));
			$this->set('urlmenu', $this->set( 'urlmenu', '/creances/index/'.$foyer_id ));

			// ************ Liste des annulations / réductions ************
			$contentIndex = $this->Titresuiviannulationreduction->getContext();
			$query = $this->Titresuiviannulationreduction->getQuery($titrecreancier_id);
			$titresAnnRed = $this->WebrsaAccesses->getIndexRecords($foyer_id, $query, $contentIndex);
			$titresAnnRed =  $this->Titresuiviannulationreduction->getList($titresAnnRed, $titresCreanciers['Titrecreancier']['mntinit']);

			$options = $this->Titrecreancier->options();
			// Inverse d'ajout possible
			$options['annreduc_ajoutDisabled'] = !$this->Titresuiviannulationreduction->ajoutPossible($titrecreancier_id);

			//  ************ Liste des infos payeurs ************
			$titresInfosPayeurs = $this->Titresuiviinfopayeur->getList($titrecreancier_id);

			//  ************ Liste des autres infos ************
			$contentIndex = $this->Titresuiviautreinfo->getContext();
			$query = $this->Titresuiviautreinfo->getQuery($titrecreancier_id);
			$titresAutres = $this->WebrsaAccesses->getIndexRecords($foyer_id, $query, $contentIndex);
			$titresAutres = $this->Titresuiviautreinfo->getList($titresAutres);

			// Assignations à la vue
			$this->set( compact('options', 'titresAnnRed', 'titresCreanciers', 'titresInfosPayeurs', 'titresAutres' ) );
		}
	}
