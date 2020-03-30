<?php
	/**
	 * Code source de la classe StatistiquesplanpauvreteController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe StatistiquesplanpauvreteController ...
	 *
	 * @package app.Controller
	 */
	class StatistiquesplanpauvreteController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Statistiquesplanpauvrete';

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
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Observer' => array(
				'className' => 'Prototype.PrototypeObserver',
				'useBuffer' => false
			),
			'Search',
			'Csv',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Statistiqueplanpauvrete',
			'Serviceinstructeur',
			'Communautesr',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array();

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'indicateurs_tableau_a1',
			'indicateurs_tableau_a2',
			'indicateurs_tableau_b1',
			'indicateurs_tableau_b4',
			'indicateurs_tableau_b5',
			'indicateurs_tableau_a1v2',
			'indicateurs_tableau_a2av2',
			'indicateurs_tableau_a2bv2',
			'indicateurs_tableau_a1v3',
			'indicateurs_tableau_a2av3',
			'indicateurs_tableau_a2bv3',
			'exportcsv_tableau_a1',
			'exportcsv_tableau_a2',
			'exportcsv_tableau_b1',
			'exportcsv_tableau_b4',
			'exportcsv_tableau_b5',
			'exportcsv_tableau_a12',
			'exportcsv_tableau_a2a2',
			'exportcsv_tableau_a2b2',
			'exportcsv_tableau_a13',
			'exportcsv_tableau_a2a3',
			'exportcsv_tableau_a2b3',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'indicateurs_tableau_a1' => 'read',
			'indicateurs_tableau_a2' => 'read',
			'indicateurs_tableau_b1' => 'read',
			'indicateurs_tableau_b4' => 'read',
			'indicateurs_tableau_b5' => 'read',
			'indicateurs_tableau_a1v2' => 'read',
			'indicateurs_tableau_a2av2' => 'read',
			'indicateurs_tableau_a2bv2' => 'read',
			'indicateurs_tableau_a1v3' => 'read',
			'indicateurs_tableau_a2av3' => 'read',
			'indicateurs_tableau_a2bv3' => 'read',
			'exportcsv_tableau_a1' => 'read',
			'exportcsv_tableau_a2' => 'read',
			'exportcsv_tableau_b1' => 'read',
			'exportcsv_tableau_b4' => 'read',
			'exportcsv_tableau_b5' => 'read',
			'exportcsv_tableau_a12' => 'read',
			'exportcsv_tableau_a2a2' => 'read',
			'exportcsv_tableau_a2b2' => 'read',
			'exportcsv_tableau_a13' => 'read',
			'exportcsv_tableau_a2a3' => 'read',
			'exportcsv_tableau_a2b3' => 'read',
		);

		/**
		 * Envoi des données communes pour les moteurs de recherche.
		 */
		public function beforeFilter() {
			parent::beforeFilter();
			$departement = (int)Configure::read( 'Cg.departement' );

			$this->set( 'communautesr', $this->Communautesr->find( 'list' ) );

			$this->set( 'servicesinstructeurs', $this->Serviceinstructeur->find( 'list' ) );
			$this->Gestionzonesgeos->setCantonsIfConfigured();
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			if( $departement === 58 ) {
				$this->set( 'sitescovs', $this->Gestionzonesgeos->listeSitescovs58() );
			}
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau_a1() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA1( $this->request->data );

				$this->set( compact( 'results') );
			}

			$this->set( 'title_for_layout',  __d('statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a1', '' ) );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau_a1() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA1( $named );

			$export = $this->generationexportcsv( $results, 'Tableaua2');

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau_a2() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA2( $this->request->data );
				$this->set( compact( 'results' ) );
			}
			$this->set( 'title_for_layout', __d('statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a2', '') );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau_a2() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA2( $named );

			$export = $this->generationexportcsv( $results, 'Tableaua2');

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau_b1() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueplanpauvrete->getIndicateursTableauB1( $this->request->data );
				$this->set( compact( 'results' ) );
			}

			$this->set( 'title_for_layout', __d('statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_b1', '') );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau_b1() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueplanpauvrete->getIndicateursTableauB1( $named );

			$export = $this->generationexportcsv( $results, 'Tableaub1');

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau_b4() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueplanpauvrete->getIndicateursTableauB4( $this->request->data );
				$this->set( compact( 'results' ) );
			}

			$this->set( 'title_for_layout', __d('statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_b4', '') );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau_b4() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueplanpauvrete->getIndicateursTableau4( $named );

			$export = $this->generationexportcsv( $results, 'Tableaub4');

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau_b5() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueplanpauvrete->getIndicateursTableauB5( $this->request->data );

				$this->set( compact( 'results') );
			}

			$this->set( 'title_for_layout', __d('statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_b5', '') );
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau_a1v2() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA1V2( $this->request->data );
				$this->set( compact( 'results') );
			}

			$this->set( 'title_for_layout',  __d('statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a1v2', '' ) );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau_a12() {

			$named = Hash::expand( $this->request->named, '__');

			$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA1V2( $named );
			$filename = 'indicateurs_tableau_A1';

			$export = $this->generationexportcsv($results, 'tableaua12');

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau_a2av2() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA2AV2( $this->request->data );

				$this->set( compact( 'results') );
			}

			$this->set( 'title_for_layout',  __d('statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a2av2', '' ) );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau_a2a2() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA2AV2( $named );

			$export = $this->generationexportcsv($results, 'tableaua2a2' );

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau_a2bv2() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA2BV2( $this->request->data );

				$this->set( compact( 'results') );
			}

			$this->set( 'title_for_layout',  __d('statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a2bv2', '' ) );
		}
		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau_a2b2() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA2BV2( $named );

			$export = $this->generationexportcsv($results, 'tableaua2b2' );

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau_a1v3() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA1V3( $this->request->data );
				$this->set( compact( 'results') );
			}

			$this->set( 'title_for_layout',  __d('statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a1v3', '' ) );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau_a13() {

			$named = Hash::expand( $this->request->named, '__');

			$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA1V3( $named );
			$filename = 'indicateurs_tableau_A1';

			$export = $this->generationexportcsv($results, 'tableaua13');

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau_a2av3() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA2AV3( $this->request->data );

				$this->set( compact( 'results') );
			}

			$this->set( 'title_for_layout',  __d('statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a2av3', '' ) );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau_a2a3() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA2AV3( $named );

			$export = $this->generationexportcsv($results, 'tableaua2a3' );

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau_a2bv3() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA2BV3( $this->request->data );

				$this->set( compact( 'results') );
			}

			$this->set( 'title_for_layout',  __d('statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a2bv3', '' ) );
		}
		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau_a2b3() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueplanpauvrete->getIndicateursTableauA2BV3( $named );

			$export = $this->generationexportcsv($results, 'tableaua2b3' );

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Génération de certains csv
		 *
		 * @return void
		 */
		private function generationexportcsv ($results, $intitules_prefix) {
			$export = array ();
			$i = 0;
			// Titre
			foreach ( $results AS $rkey => $table ) {
				$export[$i] = array(
					$rkey,
					__d('statistiquesplanpauvrete','Tableau.jan'),
					__d('statistiquesplanpauvrete','Tableau.feb'),
					__d('statistiquesplanpauvrete','Tableau.mar'),
					__d('statistiquesplanpauvrete','Tableau.apr'),
					__d('statistiquesplanpauvrete','Tableau.may'),
					__d('statistiquesplanpauvrete','Tableau.jun'),
					__d('statistiquesplanpauvrete','Tableau.jul'),
					__d('statistiquesplanpauvrete','Tableau.aug'),
					__d('statistiquesplanpauvrete','Tableau.sep'),
					__d('statistiquesplanpauvrete','Tableau.oct'),
					__d('statistiquesplanpauvrete','Tableau.nov'),
					__d('statistiquesplanpauvrete','Tableau.dec')
				);
				$i++;
				foreach ( $table AS $tkey => $subtable ) {
					$j=0;
					foreach ($subtable as $skey => $element) {
						if ( is_array($element) ){
							$j=0;
							foreach ($element as $ekey => $subElement) {
								if ( $j==0 ) {
									//Titre
									$export[$i][$j] = __d('statistiquesplanpauvrete', $intitules_prefix.'.'.$tkey.$skey );
									$j++;
								}
								//Case
								$export[$i][$j] = $subElement;
								$j++;
							}
							$i++;
						} else {
							if ( $j==0 ) {
								//Titre
								$export[$i][$j] = __d('statistiquesplanpauvrete', $intitules_prefix.'.'.$tkey );
								$j++;
							}
							//Case
							$export[$i][$j] = $element;
							$j++;
						}
					}
					$i++;
					unset($results[$rkey][$tkey]);
				}
				//Saut de ligne
				$export[$i] = array ('', '', '', '', '', '', '', '', '', '', '', '', '' );
				$i++;
			}
			return $export;
		}

	}
?>
