<?php
	/**
	 * Code source de la classe Gestionssanctionseps58Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Gestionssanctionseps58Controller permet de gérer les sanctions émises par une EP pour le cG58.
	 *
	 * @package app.Controller
	 */
	class Gestionssanctionseps58Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Gestionssanctionseps58';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes' => array(
				'traitement',
			),
			'Gedooo.Gedooo',
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Search.SearchPrg' => array(
				'actions' => array(
					'traitement' => array(
						'filter' => 'Search'
					),
					'visualisation',
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
			'Default',
			'Default2',
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Gestionsanctionep58',
			'Commissionep',
			'Dossier',
			'Option',
			'Personne',
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
			
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'exportcsv' => 'read',
			'impressionSanction1' => 'update',
			'impressionSanction2' => 'update',
			'impressionsSanctions1' => 'update',
			'impressionsSanctions2' => 'update',
			'traitement' => 'read',
			'visualisation' => 'read',
		);

		/**
		 * Méthode commune d'envoi des options dans les vues.
		 *
		 * @return void
		 */
		protected function _setOptions() {
			$this->set( 'etatdosrsa', ClassRegistry::init('Dossier')->enum('etatdosrsa') );

			$options = Set::merge(
				$this->Commissionep->Passagecommissionep->Dossierep->enums(),
				$this->Commissionep->enums(),
				$this->Commissionep->CommissionepMembreep->enums(),
				$this->Commissionep->Passagecommissionep->enums(),
				array( 'Foyer' => array( 'sitfam' => $this->Option->sitfam() ) )
			);


			$options['Ep']['regroupementep_id'] = $this->Commissionep->Ep->Regroupementep->find( 'list' );

			// Ajout des enums pour les thématiques du CG uniquement
			$options['Dossierep']['themeep'] = $this->Gestionsanctionep58->themes();
			foreach( $this->Gestionsanctionep58->themes() as $theme => $intitule ) {
				$theme = Inflector::singularize( $theme );
				$modeleDecision = Inflector::classify( "decision{$theme}" );
				if( in_array( 'Enumerable', $this->Commissionep->Passagecommissionep->{$modeleDecision}->Behaviors->attached() ) ) {
					$options = Set::merge( $options, $this->Commissionep->Passagecommissionep->{$modeleDecision}->enums() );
				}
			}

			$this->set( 'listesanctionseps58', $this->Commissionep->Passagecommissionep->Decisionsanctionep58->Listesanctionep58->find( 'list' ) );
			$regularisationlistesanctionseps58 = Set::merge(
				$this->Commissionep->Passagecommissionep->Decisionsanctionep58->enums(),
				$this->Commissionep->Passagecommissionep->Decisionsanctionrendezvousep58->enums()
			);
			$this->set( compact( 'regularisationlistesanctionseps58' ) );
			$this->set( 'typesrdv', $this->Commissionep->Passagecommissionep->Dossierep->Sanctionrendezvousep58->Rendezvous->Typerdv->find( 'list' ) );

			$this->set( compact( 'options' ) );
			$this->set( compact( 'typesorients' ) );
			$this->set( compact( 'structuresreferentes' ) );
			$this->set( compact( 'referents' ) );
			$this->set( 'typevoie', $this->Option->typevoie() );
		}

		/**
		 * Formulaire de traitement des sanctions.
		 *
		 * @return void
		 */
		public function traitement() {
			$this->_index();
		}

		/**
		 * Visualisation des sanctions.
		 *
		 * @return void
		 */
		public function visualisation() {
			$this->_index();
		}

		/**
		 * Méthode générique de traitement ou de visualisation des sanctions.
		 */
		protected function _index() {
			$validationErrors = false;

			// Récupération des noms de modèles de décision et chargement des règles de validation "gestion des sanctions"
			$decisionsClasses = array();
			foreach( $this->Gestionsanctionep58->themes() as $theme => $intitule ) {
				$modelTheme = Inflector::singularize( $theme );
				$decisionModelTheme = 'Decision'.$modelTheme;

				$this->Personne->Dossierep->Passagecommissionep->{$decisionModelTheme}->validate = $this->Personne->Dossierep->Passagecommissionep->{$decisionModelTheme}->validateGestionSanctions;
				$decisionsClasses[] = $decisionModelTheme;
			}

			if( !empty( $this->request->data ) ) {
				$data = $this->request->data;

				unset( $data['Search'], $data['sessionKey'], $data['page'] );

				if( count( $data ) > 0 ) {
					$this->Cohortes->get( Set::extract( '/Foyer/dossier_id', $this->request->data ) );

					$success = true;
					$this->Personne->begin();

					foreach( $decisionsClasses as $decisionModelTheme ) {
						if( !empty( $this->request->data[$decisionModelTheme] ) ) {
							$success = $this->Personne->Dossierep->Passagecommissionep->{$decisionModelTheme}->saveAll( $this->request->data[$decisionModelTheme], array( 'validate' => 'first', 'atomic' => false ) ) && $success;
						}
					}

					if( $success ) {
						$this->Personne->commit();
						$this->Cohortes->release( Set::extract( '/Foyer/dossier_id', $this->request->data ) );

						$this->Session->setFlash( 'Enregistrement effectué.', 'flash/success' );
						unset( $this->request->data[$decisionModelTheme] );
						if( isset( $this->request->data['sessionKey'] ) ) {
							$this->Session->delete( "{$this->SearchPrg->name}.{$this->name}__{$this->action}.{$this->request->data['sessionKey']}" );
						}
					}
					else {
						$this->Personne->rollback();
						$this->Session->setFlash( 'Erreur lors de l\'enregistrement.', 'flash/error' );
						$validationErrors = true;
					}
				}

				$paginate = $this->Gestionsanctionep58->search(
					"Gestion::{$this->action}",
					$this->request->data['Search'],
					(array)$this->Session->read( 'Auth.Zonegeographique' ),
					$this->Session->read( 'Auth.User.filtre_zone_geo' ),
					( ( $this->action == 'traitement' ) ? $this->Cohortes->sqLocked( 'Dossier' ) : null )
				);
				$paginate['limit'] = ( ( $this->action == 'traitement' ) ? 10 : 100 );

				$this->paginate = $paginate;
				$gestionsanctionseps58 = $this->paginate(
					'Personne',
					array(),
					array(),
					!Hash::get( $this->request->data, 'Search.Pagination.nombre_total' )
				);

				if( $this->action == 'traitement' ) {
					$this->Cohortes->get( Set::extract( '/Foyer/dossier_id', $gestionsanctionseps58 ) );

					// Préparation des données du formulaire pour le prochain traitement.
					if( !$validationErrors ) {
						$data = $this->Gestionsanctionep58->prepareFormDataTraitement( $gestionsanctionseps58 );
						$data['Search'] = $this->request->data['Search'];
						$this->request->data = $data;
					}
				}

				$this->set( 'gestionsanctionseps58', $gestionsanctionseps58 );
			}
			else {
				if( $this->action == 'traitement' ) {
					$this->request->data['Search']['Decision']['sanction'] = 'N';
				}
			}


			$this->_setOptions();
			$this->set( 'cantons', $this->Gestionzonesgeos->listeCantons() );
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );

			$compteurs = array( 'Ep' => $this->Commissionep->Ep->find( 'count' ) );
			$this->set( compact( 'compteurs' ) );

			$this->render( $this->action );
		}

		/**
		 * Export du tableau en CSV.
		 *
		 * @return void
		 */
		public function exportcsv() {
			$params = Hash::expand( $this->request->params['named'], '__' );

			//Est-on en traitement ou en visualisation
            $action = Hash::get( $this->request->params['named'], 'Search__action' );

			$queryData = $this->Gestionsanctionep58->search(
                "Gestion::$action",
				$params['Search'],
				(array)$this->Session->read( 'Auth.Zonegeographique' ),
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				null
			);
			unset( $queryData['limit'] );

			$gestionssanctionseps58 = $this->Personne->find( 'all', $queryData );
			$this->_setOptions();

			$this->layout = '';
			$this->set( compact( 'gestionssanctionseps58' ) );

		}


		/**
		 * Fonction d'impression pour le cas des sanctions 1 du CG58
		 *
		 * @param integer $contratinsertion_id
		 */
		public function impressionSanction1 ( $niveauSanction, $passagecommissionep_id, $themeep ) {
			$this->_impressionSanction( '1', $passagecommissionep_id, $themeep );
		}

		/**
		 * Fonction d'impression pour le cas des sanctions 2 du CG58
		 *
		 * @param integer $contratinsertion_id
		 */
		public function impressionSanction2 ( $niveauSanction, $passagecommissionep_id, $themeep ) {
			$this->_impressionSanction( '2', $passagecommissionep_id, $themeep );
		}


		/**
		 * Impression du courrier de fin de sanction.
		 *
		 * @param integer $niveauSanction
		 * @param integer $passagecommissionep_id
		 * @param string $themeep
		 */
		public function _impressionSanction( $niveauSanction, $passagecommissionep_id, $themeep) {
			$pdf = $this->Gestionsanctionep58->getPdfSanction( $niveauSanction, $passagecommissionep_id, $themeep, $this->Session->read( 'Auth.User.id' ) );

			if( !empty( $pdf ) ){
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'impressionSanction-%s.pdf', date( 'Y-m-d' ) ) );
			}
			else {
				$this->Session->setFlash( 'Impossible de générer le courrier.', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}


		/**
 		 * Fonction d'impression en cohorte pour le cas des sanctions 1 du CG58
 		 */
		public function impressionsSanctions1() {
			$this->_impressionsSanctions( '1' );
		}

		/**
		 * Fonction d'impression en cohorte pour le cas des sanctions 2 du CG58
		 */
		public function impressionsSanctions2() {
			$this->_impressionsSanctions( '2' );
		}

		/**
		 * @param integer $id L'id de
		 */
		public function _impressionsSanctions( $niveauSanction = null ) {
			$params = Hash::expand( $this->request->params['named'], '__' );

			// La page sur laquelle nous sommes
			$page = Set::classicExtract( $this->request->params, 'named.page' );
			if( ( intval( $page ) != $page ) || $page < 0 ) {
				$page = 1;
			}

			$pdfs = $this->Gestionsanctionep58->getCohortePdfSanction(
				$niveauSanction,
				'Gestion::visualisation',
				(array)$this->Session->read( 'Auth.Zonegeographique' ),
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				$params['Search'],
				$page,
				$this->Session->read( 'Auth.User.id' )
			);


			if( !empty( $pdfs ) ) {
				$pdf = $this->Gedooo->concatPdfs( $pdfs, 'Gestionsanctionep58' );
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'gestionssanctions-%s.pdf', date( 'Y-m-d' ) ) );
			}
			else {
				$this->Session->setFlash( 'Impossible de générer l\'impression.', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}

	}
?>