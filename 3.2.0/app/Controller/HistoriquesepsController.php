<?php
	/**
	 * Code source de la classe HistoriquesepsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'WebrsaAccessHistoriqueseps', 'Utility' );

	/**
	 * La classe HistoriquesepsController ...
	 *
	 * @package app.Controller
	 */
	class HistoriquesepsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Historiqueseps';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses' => array(
				'mainModelName' => 'Passagecommissionep',
				'webrsaModelName' => 'WebrsaHistoriqueep',
				'webrsaAccessName' => 'WebrsaAccessHistoriqueseps',
				'parentModelName' => 'Personne'
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default2',
			'Xpaginator2',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Dossierep',
			'Option',
			'Passagecommissionep',
			'WebrsaHistoriqueep',
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
			'index' => 'read',
			'view_passage' => 'read',
		);

		/**
		 *
		 * @param string $modeleTheme
		 * @param string $modeleDecision
		 */
		protected function _setOptions( $modeleTheme = null, $modeleDecision = null ) {
			$options = $this->Dossierep->Passagecommissionep->enums();
			$options['Dossierep']['themeep'] = $this->Dossierep->themesCg();
			$options['Dossierep']['actif'] = $this->Dossierep->enum( 'actif' );

			if( !empty( $modeleTheme ) ) {
				$options = Set::merge(
					$options,
					$this->Dossierep->{$modeleTheme}->enums()
				);
			}

			if( !empty( $modeleDecision ) ) {
				$options = Set::merge(
					$options,
					$this->Dossierep->Passagecommissionep->{$modeleDecision}->enums()
				);
			}

			$this->set( compact( 'options' ) );
		}

		/**
		 * Affiche la liste des passages en commission d'EP pour une personne donnée.
		 * Possibilité de filtrer par thématique.
		 *
		 * @param integer $personne_id
		 */
		public function index( $personne_id = null ) {
			$this->assert( valid_int( $personne_id ), 'error404' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$queryData = $this->WebrsaHistoriqueep->completeVirtualFieldsForAccess(
				array(
					'conditions' => array(
						'Dossierep.personne_id' => $personne_id
					),
					'contain' => array(
						'Dossierep',
						'Commissionep' => array(
							'Ep'
						),
					),
					'order' => array(
						'Commissionep.dateseance DESC'
					)
				)
			);

			// Moteur de recherche
			if( !empty( $this->request->data) ) { // FIXME: méthode search dans le modèle Historiqueep (à créer)
				if( !empty( $this->request->data['Search']['Dossierep']['themeep'] ) ) {
					$queryData['conditions']['Dossierep.themeep'] = $this->request->data['Search']['Dossierep']['themeep'];
				}
			}

			$this->paginate = array( 'Passagecommissionep' => $queryData );

			$paramsAccess = $this->WebrsaHistoriqueep->getParamsForAccess($personne_id, WebrsaAccessHistoriqueseps::getParamsList());
			$passages = WebrsaAccessHistoriqueseps::accesses($this->paginate($this->Dossierep->Passagecommissionep), $paramsAccess);

			$this->_setOptions();
			$this->set( compact( 'passages' ) );
			$this->set( 'personne_id', $personne_id );
		}

		/**
		 * Visualisation des détails du passage d'un dossier d'EP en commission d'EP
		 *
		 * @param integer $passagecommssionep_id
		 */
		public function view_passage( $passagecommssionep_id ) {
			$this->WebrsaAccesses->check($passagecommssionep_id);

			$personne_id = $this->Dossierep->Passagecommissionep->personneId( $passagecommssionep_id );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$passage = $this->Dossierep->Passagecommissionep->find(
				'first',
				array(
					'conditions' => array(
						'Passagecommissionep.id' => $passagecommssionep_id
					),
					'contain' => array(
						'Dossierep',
						'Commissionep' => array(
							'Ep'
						),
					)
				)
			);

			$this->assert( !empty( $passage ), 'error404' );

			// TODO: à factoriser avec view_dossier
			$themeSingulier = Inflector::singularize( $passage['Dossierep']['themeep'] );
			$modeleTheme = Inflector::classify( $themeSingulier );
			$modeleDecision = Inflector::classify( "decision{$themeSingulier}" );

			// Thématique
			if( method_exists( $this->Dossierep->{$modeleTheme}, 'containThematique' ) ) {
				$contain = $this->Dossierep->{$modeleTheme}->containThematique();
			}
			else {
				$contain = false;
			}

			$this->Dossierep->{$modeleTheme}->forceVirtualFields = true;
			$donneesTheme = $this->Dossierep->{$modeleTheme}->find(
				'first',
				array(
					'conditions' => array(
						"{$modeleTheme}.dossierep_id" => $passage['Dossierep']['id']
					),
					'contain' => $contain
				)
			);

			// Décision
			if( method_exists( $this->Dossierep->Passagecommissionep->{$modeleDecision}, 'containDecision' ) ) {
				$contain = $this->Dossierep->Passagecommissionep->{$modeleDecision}->containDecision();
			}
			else {
				$contain = false;
			}

			$this->Dossierep->Passagecommissionep->{$modeleDecision}->forceVirtualFields = true;
			$donneesDecision = $this->Dossierep->Passagecommissionep->{$modeleDecision}->find(
				'all',
				array(
					'conditions' => array(
						"{$modeleDecision}.passagecommissionep_id" => $passage['Passagecommissionep']['id']
					),
					'contain' => $contain
				)
			);

			$passage = Set::merge(
				$passage,
				$donneesTheme,
				array( 'Decision' => Set::classicExtract( $donneesDecision, "{n}" ) )
			);

			if( method_exists( $this->Dossierep->Passagecommissionep->{$modeleDecision}, 'suivisanctions58' ) ) {
				$this->set( 'suivisanction58', $this->Dossierep->Passagecommissionep->{$modeleDecision}->suivisanctions58( $passage ) );
			}

			$this->_setOptions( $modeleTheme, $modeleDecision );
			// Fin factorisation

			$this->set( compact( 'modeleTheme', 'modeleDecision', 'passage' ) );
			$this->set( 'urlmenu', "/historiqueseps/index/{$personne_id}" );
		}
	}
?>