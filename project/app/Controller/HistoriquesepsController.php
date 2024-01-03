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
			'Dossierep',
			'Option',
			'Passagecommissionep',
			'WebrsaHistoriqueep',
			'Personne',
			'Commissionep',
			'Foyer'
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


			//L'ajout est possible si la personne a un dossier ep en cours qui n'est pas dans une commission
			$query = $this->Dossierep->qdDossiersepsNonAssocies( $personne_id );
			$dossier = $this->Dossierep->find( 'first', $query );
			$ajoutPossible = !empty($dossier);

			$this->_setOptions();
			$this->set( compact( 'passages', 'ajoutPossible') );
			$this->set( 'personne_id', $personne_id );
			$this->set( 'dossierep_id', $ajoutPossible ? $dossier['Dossierep']['id'] : null );
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

		public function affecter($dossier_ep_id = null){

			// Retour à la liste en cas d'annulation
            if( isset( $this->request->data['Cancel'] ) ) {
                $this->redirect( array('action' => 'index', $this->request->data['Cancel']) );
            }

			if( !empty( $this->request->data ) ) {


				//On récupère les infos à mettre dans la table passagecommission
				//L'id de la commission
				$commissionep_id = explode('_', $this->request->data['Commissionep']['dateseance'])[1];
				//L'utilisateur connecté
				$user_id = $this->Session->read('Auth.User.id');

				$data = [
					'commissionep_id' => $commissionep_id,
					'dossierep_id' => $this->request->data['Dossierep']['id'],
					'etatdossierep' => 'associe',
					'user_id' => $user_id
				];


				$success = $this->Passagecommissionep->save($data);

				// Ajout de l'heure de passage
				$passagecommissioneps = $this->Dossierep->Passagecommissionep->gereHeureCommissionEp($commissionep_id);
				$success = $this->Passagecommissionep->saveAll($passagecommissioneps);

				if($success){
					$this->Flash->success( __( 'Save->success' ) );
				} else {
					$this->Flash->error( __( 'Save->error' ) );
				}

				$this->redirect( array('action' => 'index', $this->request->data['Personne']['id']) );

			} else {

				//On récupère le thème du passage en ep
				$dossier_ep = $this->Dossierep->findById($dossier_ep_id);
				$personne = $this->Personne->find(
					'first',
					[
						'conditions' => [
							'Personne.id' => $dossier_ep['Dossierep']['personne_id']
						],
						'fields' => [
							'Personne.nom_complet'
						],
						'recursive' => 0
					]
				);

				//On contruit les options disponibles
				$options['Dossierep']['themeep'][$dossier_ep['Dossierep']['themeep']] = __d('dossierep', 'ENUM::THEMEEP::'.$dossier_ep['Dossierep']['themeep']);
				//les commissions encore ouvertes à ce stade
				$foyer_id = $this->Personne->findById($dossier_ep['Dossierep']['personne_id'])['Personne']['foyer_id'];
				$codeinsee = $this->Foyer->getAdresse($foyer_id)['Adresse']['numcom'];


				$commissions_ok = $this->Commissionep->find(
					'all',
					[
						'fields' => [
							'Commissionep.id',
							'Commissionep.dateseance',
							'Regroupementep.id',
							'Regroupementep.name',
							'Ep.id',
							'Ep.name'
						],
						'conditions' => [
							'Regroupementep.'.Inflector::singularize($dossier_ep['Dossierep']['themeep']).' <>' => 'nontraite',
							'Commissionep.etatcommissionep in (\'cree\', \'associe\')',
							'Ep.actif' => '1',
							'Zonegeographique.codeinsee' => $codeinsee

						],
						'joins' => [
							$this->Commissionep->join('Ep', array( 'type' => 'INNER' ) ),
							$this->Commissionep->Ep->join('Regroupementep', array( 'type' => 'INNER' ) ),
							$this->Commissionep->Ep->join('EpZonegeographique', array( 'type' => 'LEFT' ) ),
							$this->Commissionep->Ep->EpZonegeographique->join('Zonegeographique', array( 'type' => 'LEFT' ) ),
						],
						'order' => [
							'Commissionep.dateseance'
						]
					]
				);

				foreach ($commissions_ok as $comm){
					$options['Ep']['regroupementep_id'][$comm['Regroupementep']['id']] = $comm['Regroupementep']['name'];
					$options['Ep']['id'][$comm['Regroupementep']['id'].'_'.$comm['Ep']['id']] = $comm['Ep']['name'];
					$options['Commissionep']['dateseance'][$comm['Ep']['id'].'_'.$comm['Commissionep']['id']] = date("d/m/Y H:i", strtotime($comm['Commissionep']['dateseance']));
				}


				$this->set( compact('personne', 'dossier_ep', 'options') );
			}

		}
	}
?>