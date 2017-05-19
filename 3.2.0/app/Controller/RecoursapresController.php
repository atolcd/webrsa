<?php
	/**
	 * Code source de la classe RecoursapresController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe RecoursapresController ...
	 *
	 * @package app.Controller
	 */
	class RecoursapresController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Recoursapres';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Gedooo.Gedooo',
			'Gestionzonesgeos',
			'Search.SearchPrg' => array(
				'actions' => array(
					'demande',
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
			'Locale',
			'Search',
			'Xform',
			'Xhtml',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Canton',
			'Adresse',
			'Adressefoyer',
			'Apre',
			'ApreComiteapre',
			'Comiteapre',
			'Dossier',
			'Foyer',
			'Option',
			'Personne',
			'Recoursapre',
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
			'demande' => 'read',
			'exportcsv' => 'read',
			'impression' => 'update',
			'visualisation' => 'read',
		);

		/**
		*
		*/

		public function beforeFilter() {
			$return = parent::beforeFilter();
			$options = array(
				'decisioncomite' => array(
					'ACC' => __d( 'apre', 'ENUM::DECISIONCOMITE::ACC' ),
					'AJ' => __d( 'apre', 'ENUM::DECISIONCOMITE::AJ' ),
					'REF' => __d( 'apre', 'ENUM::DECISIONCOMITE::REF' ),
				),
				'recoursapre' => array(
					'N' => __d( 'apre', 'ENUM::RECOURSAPRE::N' ),
					'O' => __d( 'apre', 'ENUM::RECOURSAPRE::O' )
				)
			);
			$this->set( 'options', $options );

			$this->set( 'natureAidesApres', $this->Option->natureAidesApres() );
		}

		/**
		*
		*/

		public function demande() {
			$this->_index( 'Recoursapre::demande' );
		}

		/**
		*
		*/

		public function visualisation() {
			$this->_index( 'Recoursapre::visualisation' );
		}

		/**
		*
		*/

		protected function _index( $avisRecours = null ){
			$this->Gestionzonesgeos->setCantonsIfConfigured();
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = (!empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array( ) );


			$this->Dossier->begin();
			if( !empty( $this->request->data ) ) {
// 			debug($this->request->data);
				if( !empty( $this->request->data['ApreComiteapre'] ) ) {
					$data = Set::extract( $this->request->data, '/ApreComiteapre' );
					if( $this->ApreComiteapre->saveAll( $data, array( 'validate' => 'only', 'atomic' => false ) ) ) {
						$saved = $this->ApreComiteapre->saveAll( $data, array( 'validate' => 'first', 'atomic' => false ) );
						if( $saved && empty( $this->Apre->ApreComiteapre->validationErrors ) ) {
							$this->ApreComiteapre->commit();
							$this->Flash->success( __( 'Save->success' ) );
							$urlData = $this->request->data;
							unset(
								$urlData['Recoursapre'],
								$urlData['Apre'],
								$urlData['ApreComiteapre']
							);
						}
						else {
							$this->ApreComiteapre->rollback();
							$this->Flash->error( __( 'Save->error' ) );
						}
					}
				}

				$recoursapres = $this->Recoursapre->search(
					$avisRecours,
					$mesCodesInsee,
					$this->Session->read( 'Auth.User.filtre_zone_geo' ),
					$this->request->data
				);

				$recoursapres['limit'] = 10;
				$this->paginate = $recoursapres;
				$recoursapres = $this->paginate( 'ApreComiteapre' );

				$this->set( 'recoursapres', $recoursapres );

				$this->Dossier->commit();

			}

			switch( $avisRecours ) {
				case 'Recoursapre::demande':
					$this->set( 'pageTitle', 'Demandes de recours' );
					$this->render( 'formulaire' );
					break;
				case 'Recoursapre::visualisation':
					$this->set( 'pageTitle', 'Visualisation des recours' );
					$this->render( 'visualisation' );
					break;
			}

			$this->Dossier->commit();
		}

		/**
		* Export du tableau en CSV
		*/

		public function exportcsv() {
			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			$querydata = $this->Recoursapre->search(
				"Recoursapre::visualisation",
				$mesCodesInsee,
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				Hash::expand( $this->request->params['named'], '__' )
			);

			unset( $querydata['limit'] );
			$recoursapres = $this->ApreComiteapre->find( 'all', $querydata );

			$this->layout = '';
			$this->set( compact( 'recoursapres' ) );
		}

		/**
		 * Impression d'un recours pour une demande d'APRE, pour un destinataire donné.
		 *
		 * @param integer $apre_id L'id de l'APRE
		 * @return void
		 */
		public function impression( $apre_id = null ) {
			$dest = Set::classicExtract( $this->request->params, 'named.dest' );

			$pdf = $this->Recoursapre->getDefaultPdf(
				$apre_id,
				$dest,
				$this->Session->read( 'Auth.User.id' )
			) ;

			if( !empty( $pdf ) ){
				$this->Gedooo->sendPdfContentToClient( $pdf, sprintf( 'recoursapre_%d-%s-%s.pdf', $apre_id, $dest, date( 'Y-m-d' ) ) );
			}
			else {
				$this->Flash->error( 'Impossible de générer l\'impression du recours d\'APRE.' );
				$this->redirect( $this->referer() );
			}
		}
	}
?>