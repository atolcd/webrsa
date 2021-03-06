<?php
	/**
	 * Code source de la classe StatistiquesdreesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe StatistiquesdreesController ...
	 *
	 * @package app.Controller
	 */
	class StatistiquesdreesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Statistiquesdrees';

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
			'Statistiquedrees',
			'Serviceinstructeur',
			'Communautesr',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'exportcsv_caracteristiques_contrats' => 'Statistiquesdrees:exportcsv_caracteristiques_contrats',
			'exportcsv_delais' => 'Statistiquesdrees:exportcsv_delais',
			'exportcsv_motifs_reorientation' => 'Statistiquesdrees:exportcsv_motifs_reorientation',
			'exportcsv_natures_contrats' => 'Statistiquesdrees:exportcsv_natures_contrats',
			'exportcsv_organismes' => 'Statistiquesdrees:exportcsv_organismes',
			'exportcsv_orientations' => 'Statistiquesdrees:exportcsv_orientations',
			'exportcsv_reorientations' => 'Statistiquesdrees:exportcsv_reorientations'
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'exportcsv_tableau1',
			'exportcsv_tableau2',
			'exportcsv_tableau3',
			'exportcsv_tableau4',
			'exportcsv_tableau5',
			'exportcsv_tableau6',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'indicateurs_caracteristiques_contrats' => 'read',
			'indicateurs_delais' => 'read',
			'indicateurs_motifs_reorientation' => 'read',
			'indicateurs_natures_contrats' => 'read',
			'indicateurs_organismes' => 'read',
			'indicateurs_orientations' => 'read',
			'indicateurs_reorientations' => 'read',
			'exportcsv_orientations' => 'read',
			'exportcsv_organismes' => 'read',
			'exportcsv_reorientations' => 'read',
			'exportcsv_caracteristiques_contrats' => 'read',
			'exportcsv_motifs_reorientation' => 'read',
			'exportcsv_natures_contrats' => 'read',
			'exportcsv_delais' => 'read',
		);

		/**
		 * Envoi des données communes pour les moteurs de recherche.
		 */
		public function beforeFilter() {
			parent::beforeFilter();
			$departement = Configure::read( 'Cg.departement' );

			$this->set( 'communautesr', $this->Communautesr->find( 'list' ) );

			$this->set( 'servicesinstructeurs', $this->Serviceinstructeur->find( 'list' ) );
			$this->Gestionzonesgeos->setCantonsIfConfigured();
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			if( $departement == 58 ) {
				$this->set( 'sitescovs', $this->Gestionzonesgeos->listeSitescovs58() );
			}
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau1() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiquedrees->getIndicateursTableau1( $this->request->data );
				$tranches = $this->Statistiquedrees->tranches;

				$this->set( compact( 'results', 'tranches' ) );
			}

			$this->set( 'title_for_layout', 'Tableau 1 - Nombre de personnes soumises aux droits et devoirs au 31/12 de l\'année, selon l\'orientation à cette même date' );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau1() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiquedrees->getIndicateursTableau1( $named );
			$tranches = $this->Statistiquedrees->tranches;
			$filename = 'indicateurs_tableau1';
			$colonnes = array( 'droits_et_devoirs', 'non_orientes', 'orientes', 'orientes_pole_emploi', 'orientes_autre_que_pole_emploi', 'spe_mission_locale',
				'spe_mde_mdef_plie', 'spe_creation_entreprise', 'spe_iae', 'spe_autre_placement_pro', 'hors_spe_ssd', 'hors_spe_caf', 'hors_spe_msa',
				'hors_spe_ccas_cias', 'hors_spe_autre_organisme');
			$intitules = array (
				'Personnes soumises aux droits et devoirs au 31/12 de l\'année (4)', 
				'Personnes soumises aux droits et devoirs au 31/12 de l\'année et non orientées à cette même date (5)', 
				'Personnes soumises aux droits et devoirs au 31/12 de l\'année et orientées à cette même date (5)', 
				'Personnes soumises aux droits et devoirs au 31/12 de l\'année et orientées vers Pôle emploi à cette même date (6)', 
				'Personnes soumises aux droits et devoirs au 31/12 de l\'année et orientées vers un organisme autre que Pôle emploi à cette même date (6)…', 
				'… dont personnes orientées vers un organisme du SPE autre que Pôle emploi (6) (7) Mission locale',
				'… dont personnes orientées vers un organisme du SPE autre que Pôle emploi (6) (7) Maison de l\'emploi (MDE), Maison de l\'emploi et de la formation (MDEF), Plan local pluriannuel pour l\'insertion et l\'emploi (PLIE), Cap Emploi', 
				'… dont personnes orientées vers un organisme du SPE autre que Pôle emploi (6) (7) Structure d\'appui à la création et au développement d\'entreprise (8)', 
				'… dont personnes orientées vers un organisme du SPE autre que Pôle emploi (6) (7) Structure d\'insertion par l\'activité économique (IAE) (9)', 
				'… dont personnes orientées vers un organisme du SPE autre que Pôle emploi (6) (7) Autre organisme de placement professionnel ou de formation professionnelle (10)', 
				'… dont personnes orientées vers un organisme hors SPE (6) (7) Service du Conseil départemental/territorial (11)', 
				'… dont personnes orientées vers un organisme hors SPE (6) (7) Caisse d\'allocations familiales (Caf) (12)', 
				'… dont personnes orientées vers un organisme hors SPE (6) (7) Mutualité sociale agricole (Msa)',
				'… dont personnes orientées vers un organisme hors SPE (6) (7) Centre communal/intercommunal d\'action sociale (CCAS/CIAS) (13)', 
				'… dont personnes orientées vers un organisme hors SPE (6) (7) Autre organisme'
			);

			$export = $this->generationexportcsv($tranches, $results, $colonnes, $intitules);

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau2() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiquedrees->getIndicateursTableau2( $this->request->data );
				$tranches = $this->Statistiquedrees->tranches;

				$this->set( compact( 'results', 'tranches' ) );
			}

			$this->set( 'title_for_layout', 'Tableau 2 - Nombre de personnes soumises aux droits et devoirs et orientées au 31/12 de l\'année inscrites à Pôle emploi ou ayant un CER en cours de validité à cette même date, selon l\'orientation' );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau2() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiquedrees->getIndicateursTableau2( $named );
			$tranches = $this->Statistiquedrees->tranches;
			$filename = 'indicateurs_tableau2';
			$colonnes = array( 'orientes_pole_emploi', 'orientes_autre_que_pole_emploi', 'spe_mission_locale',
				'spe_mde_mdef_plie', 'spe_creation_entreprise', 'spe_iae', 'spe_autre_placement_pro', 'hors_spe_ssd', 'hors_spe_caf', 'hors_spe_msa',
				'hors_spe_ccas_cias', 'hors_spe_autre_organisme');
			$intitules = array (
				'Personnes soumises aux droits et devoirs et orientées vers Pôle emploi au 31/12 de l\'année effectivement inscrites à Pôle emploi à cette même date (4) (6) (14)', 
				'Personnes soumises aux droits et devoirs et orientées vers un organisme autre que Pôle emploi au 31/12 de l\'année ayant un CER en cours de validité à cette même date (4) (6) (15)…', 
				'… dont CER pour les personnes orientées vers un organisme du SPE autre que Pôle emploi (6) (7) Mission locale',
				'… dont CER pour les personnes orientées vers un organisme du SPE autre que Pôle emploi (6) (7) Maison de l\'emploi (MDE), Maison de l\'emploi et de la formation (MDEF), Plan local pluriannuel pour l\'insertion et l\'emploi (PLIE), Cap Emploi', 
				'… dont CER pour les personnes orientées vers un organisme du SPE autre que Pôle emploi (6) (7) Structure d\'appui à la création et au développement d\'entreprise (8)', 
				'… dont CER pour les personnes orientées vers un organisme du SPE autre que Pôle emploi (6) (7) Structure d\'insertion par l\'activité économique (IAE) (9)', 
				'… dont CER pour les personnes orientées vers un organisme du SPE autre que Pôle emploi (6) (7) Autre organisme de placement professionnel ou de formation professionnelle (10)', 
				'… dont CER pour les personnes orientées vers un organisme hors SPE (6) (7) (15) Service du Conseil départemental/territorial (11)', 
				'… dont CER pour les personnes orientées vers un organisme hors SPE (6) (7) (15) Caisse d\'allocations familiales (Caf) (12)', 
				'… dont CER pour les personnes orientées vers un organisme hors SPE (6) (7) (15) Mutualité sociale agricole (Msa)',
				'… dont CER pour les personnes orientées vers un organisme hors SPE (6) (7) (15) Centre communal/intercommunal d\'action sociale (CCAS/CIAS) (13)', 
				'… dont CER pour les personnes orientées vers un organisme hors SPE (6) (7) (15) Autre organisme'
			);

			$export = $this->generationexportcsv($tranches, $results, $colonnes, $intitules);

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau3() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiquedrees->getIndicateursTableau3( $this->request->data );
				$tranches = $this->Statistiquedrees->tranches;

				$this->set( compact( 'results', 'tranches' ) );
			}

			$this->set( 'title_for_layout', 'Tableau 3 - Durées inscrites dans les CER en cours de validité au 31/12 de l\'année des personnes soumises aux droits et devoirs et orientées à cette même date vers un organisme autre que Pôle emploi' );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau3() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiquedrees->getIndicateursTableau3( $named );
			$tranches = $this->Statistiquedrees->tranches;
			$filename = 'indicateurs_tableau3';
			$colonnes = array( 'cer_moins_6_mois', 'cer_6_mois_un_an', 'cer_1_an_et_plus');
			$intitules = array (
				'… d\'une durée inscrite inférieure à 6 mois',
				'… d\'une durée inscrite de 6 mois à moins de 1 an',
				'… d\'une durée inscrite de 1 an et plus'
			);

			$export = $this->generationexportcsv($tranches, $results, $colonnes, $intitules);

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau4() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiquedrees->getIndicateursTableau4( $this->request->data );
				$tranches = $this->Statistiquedrees->tranches;

				$this->set( compact( 'results', 'tranches' ) );
			}

			$this->set( 'title_for_layout', 'Tableau 4 - Actions inscrites dans les CER en cours de validité au 31/12 de l\'année des personnes soumises aux droits et devoirs et orientées à cette même date vers un organisme autre que Pôle emploi' );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau4() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiquedrees->getIndicateursTableau4( $named );
			$tranches = $this->Statistiquedrees->tranches;
			$filename = 'indicateurs_tableau4';
			$colonnes = array( 'acquerir_competences_pro', 'parcours_recherche_emploi', 'iae', 'activite_non_salariale', 'emploi_aide', 'emploi_non_aide',
				'lien_social', 'mobilite', 'acces_logement', 'acces_soins', 'autonomie_financiere', 'famille_parentalite', 'illettrisme',
				'demarches_administratives', 'autres');
			$intitules = array (
				'… au moins une action visant à trouver des activités, stages ou formations destinés à acquérir des compétences professionnelles', 
				'… au moins une action visant à s\'inscrire dans un parcours de recherche d\'emploi', 
				'… au moins une action visant à s\'inscrire dans une mesure d\'insertion par l\'activité économique (IAE)', 
				'… au moins une action aidant à la réalisation d’un projet de création, de reprise ou de poursuite d’une activité non salariée', 
				'… au moins une action visant à trouver un emploi aidé', 
				'… au moins une action visant à trouver un emploi non aidé',
				'… au moins une action visant à faciliter le lien social (développement de l\'autonomie sociale, activités collectives,…)', 
				'… au moins une action visant la mobilité (permis de conduire, acquisition / location de véhicule, frais de transport…)', 
				'… au moins une action visant l\'accès à un logement, au relogement ou à l\'amélioration de l\'habitat', 
				'… au moins une action visant l\'accès aux soins', 
				'… au moins une action visant l\'autonomie financière (constitution d\'un dossier de surendettement,...)', 
				'… au moins une action visant la famille et la parentalité (soutien familial, garde d\'enfant, …)', 
				'… au moins une action visant la lutte contre l\'illettrisme ou l\'acquisition des savoirs de base',
				'… au moins une action visant l\'accès aux droits ou l\'aide dans les démarches administratives', 
				'… au moins une action non classée dans les items précédents'
			);

			$export = $this->generationexportcsv($tranches, $results, $colonnes, $intitules);

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau5() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiquedrees->getIndicateursTableau5( $this->request->data );
				$tranches = $this->Statistiquedrees->tranches;

				$this->set( compact( 'results', 'tranches' ) );
			}

			$this->set( 'title_for_layout', 'Tableau 5 - Délais pour l\'orientation et la contractualisation pour les personnes entrées dans le RSA au cours de l\'année et soumises aux droits et devoirs au 31/12 de l\'année' );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau5() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiquedrees->getIndicateursTableau5( $named );
			$tranches = $this->Statistiquedrees->tranches;
			$filename = 'indicateurs_tableau5';
			$colonnes = array( 'droits_et_devoirs', 'primo_orientes', 'primo_orientes_hors_pe', 'primo_orientes_hors_pe_primo_cer',
				'delai_moyen_primo_orientes', 'delai_moyen_hors_pe_primo_orientes_primo_cer');
			$intitules = array (
				'Personnes entrées dans le RSA au cours de l\'année et soumises aux droits et devoirs au 31/12 de l\'année (17)…',
				'… dont … personnes primo-orientées au 31/12 de l\'année (18)',
				'… dont … personnes primo-orientées vers un organisme autre que Pôle emploi au 31/12 de l\'année (19)',
				'… dont … personnes primo-orientées vers un organisme autre que Pôle emploi et ayant un primo-CER valide au 31/12 de l\'année (20)',
				'Délai moyen entre la date d\'entrée dans le RSA et la date de primo-orientation (en jours) (21)',
				'Délai moyen entre la date de primo-orientation vers un organisme autre que Pôle emploi et la date de signature du primo-CER associé à cette primo-orientation (en jours) (22)'
			);

			$export = $this->generationexportcsvAvecDelais($tranches, $results, $colonnes, $intitules);

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Moteur de recherche pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function indicateurs_tableau6() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiquedrees->getIndicateursTableau6( $this->request->data );
				$tranches = $this->Statistiquedrees->tranches;

				$this->set( compact( 'results', 'tranches' ) );
			}

			$this->set( 'title_for_layout', 'Tableau 6 - Nombre de personnes soumises aux droits et devoirs et orientées au 31 décembre de l\'année ayant connu une réorientation d\'un organisme du SPE vers un organisme hors SPE ou vice versa au cours de l\'année' );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_tableau6() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiquedrees->getIndicateursTableau6( $named );
			$tranches = $this->Statistiquedrees->tranches;
			$filename = 'indicateurs_tableau6';
			$colonnes = array( 'reorientation', 'spe_vers_hors_spe', 'hors_spe_vers_spe');
			$intitules = array (
				'Personnes soumises aux droits et devoirs orientées au 31/12 de l\'année ayant connu une réorientation d\'un organisme du SPE vers un organisme hors SPE ou vice versa au cours de l\'année (4) (5) (7) (23)…',
				'… et dont la dernière réorientation au cours de l\'année est une réorientation … … d\'un organisme du SPE vers un organisme hors SPE (7) (23)',
				'… et dont la dernière réorientation au cours de l\'année est une réorientation … … d\'un organisme hors SPE vers un organisme du SPE (7) (23)'
			);

			$export = $this->generationexportcsv($tranches, $results, $colonnes, $intitules);

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Génération de certains csv
		 *
		 * @return void
		 */
		private function generationexportcsv ($tranches, $results, $colonnes, $intitules) {
			$categories = array ('Age', 'Sexe', 'Situation familliale', 'Ancienneté dans le dispositif, y compris anciens minima (RMI, API)', 'Niveau de formation');

			$total = array ();
			$export = array ();
			$i = 0;
			$k = 0;

			// Titre
			$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			$export[$i++] = array_merge (array ('', 'CAT'), $intitules);
			$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

			foreach ($tranches as $categorie => $groupes) {
				$export[$i++] = array ('', $categories[$k++], '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
				$soustotal = array ();

				foreach ($groupes as $value) {
					$j = 0;
					$export[$i][$j++] = '';
					$export[$i][$j++] = $value;
					$soustotal[$j++] = '';
					$soustotal[$j++] = 'Total';

					foreach ($colonnes as $colonne) {
						// Ligne (écriture)
						$valeur = 0;
						if (isset ($results[$categorie][$colonne][$value])) {
							$valeur = $results[$categorie][$colonne][$value];
						}
						$export[$i][$j] = $valeur;

						// Total (calcul)
						if (!isset ($soustotal[$j])) {
							$soustotal[$j] = 0;
						}
						$soustotal[$j] += $valeur;
						$j++;
					}

					$i++;
				}

				// Total (écriture)
				$export[$i] = $soustotal;
				$i++;
			}

			return $export;
		}

/**
		 * Génération de certains csv
		 *
		 * @return void
		 */
		private function generationexportcsvAvecDelais ($tranches, $results, $colonnes, $intitules) {
			$categories = array ('Age', 'Sexe', 'Situation familliale', 'Ancienneté dans le dispositif, y compris anciens minima (RMI, API)', 'Niveau de formation');

			$export = array ();
			$i = 0;
			$k = 0;

			// Titre
			$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
			$export[$i++] = array_merge (array ('', 'CAT'), $intitules);
			$export[$i++] = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

			foreach ($tranches as $categorie => $groupes) {
				$export[$i++] = array ('', $categories[$k++], '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
				$soustotal = array ();

				foreach ($groupes as $value) {
					$j = 0;
					$export[$i][$j++] = '';
					$export[$i][$j++] = $value;
					$soustotal[$j++] = '';
					$soustotal[$j++] = 'Total';

					foreach ($colonnes as $colonne) {
						// Ligne (écriture)
						$valeur = 0;
						if (isset ($results[$categorie][$colonne][$value])) {
							$valeur = $results[$categorie][$colonne][$value];
						}

						// Spécifique pour les calculs des délais.
						if ($colonne == 'delai_moyen_primo_orientes' && $valeur > 0) {
							$valeur = round ($valeur / $results[$categorie]['primo_orientes'][$value], 0);
						}
						if ($colonne == 'delai_moyen_hors_pe_primo_orientes_primo_cer' && $valeur > 0) {
							$valeur = round ($valeur / $results[$categorie]['primo_orientes_hors_pe_primo_cer'][$value], 0);
						}
						$export[$i][$j] = $valeur;

						// Total (calcul)
						if (!isset ($soustotal[$j])) {
							$soustotal[$j] = 0;
						}
						$soustotal[$j] += $valeur;
						$j++;
					}

					$i++;
				}

				// Spécifique pour les calculs des délais.
				if ($soustotal[8] > 0) {
					$soustotal[8] = round( array_sum($results[$categorie]['delai_moyen_primo_orientes']) / array_sum($results[$categorie]['primo_orientes']) , 0);
				}
				if ($soustotal[9] > 0) {
					$soustotal[9] = round( array_sum($results[$categorie]['delai_moyen_hors_pe_primo_orientes_primo_cer']) / array_sum($results[$categorie]['primo_orientes_hors_pe_primo_cer']) , 0);
				}

				// Total (écriture)
				$export[$i] = $soustotal;
				$i++;
			}
			return $export;
		}

	}
?>
