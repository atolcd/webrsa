<?php
	/**
	 * Code source de la classe StatistiquesministeriellesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe StatistiquesministeriellesController ...
	 *
	 * @package app.Controller
	 */
	class StatistiquesministeriellesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Statistiquesministerielles';

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
			'Statistiqueministerielle',
			'Serviceinstructeur',
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
			$departement = (int)Configure::read( 'Cg.departement' );

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
		public function indicateurs_orientations() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueministerielle->getIndicateursOrientations( $this->request->data );
				$tranches = $this->Statistiqueministerielle->tranches;

				$this->set( compact( 'results', 'tranches' ) );
			}

			$this->set( 'title_for_layout', 'Indicateurs d\'orientations' );
		}

		/**
		 * Moteur de recherche pour les indicateurs de motifs de réorientations.
		 *
		 * @return void
		 */
		public function indicateurs_organismes() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueministerielle->getIndicateursOrganismes( $this->request->data );

				$this->set( compact( 'results' ) );
			}

			$this->set( 'title_for_layout', 'Indicateurs d\'organismes' );
		}

		/**
		 * Moteur de recherche pour les indicateurs de délais.
		 *
		 * @return void
		 */
		public function indicateurs_delais() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueministerielle->getIndicateursDelais( $this->request->data );
				$types_cers = $this->Statistiqueministerielle->types_cers;

				$this->set( compact( 'results', 'types_cers' ) );
			}

			$this->set( 'title_for_layout', 'Indicateurs de délais' );
		}

		/**
		 * Moteur de recherche pour les indicateurs de réorientation.
		 *
		 * @return void
		 */
		public function indicateurs_reorientations() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueministerielle->getIndicateursReorientations( $this->request->data );
				$tranches = $this->Statistiqueministerielle->tranches;

				$this->set( compact( 'results', 'tranches' ) );
			}

			$this->set( 'title_for_layout', 'Indicateurs de réorientations' );
		}

		/**
		 * Moteur de recherche pour les indicateurs de motifs de réorientations.
		 *
		 * @return void
		 */
		public function indicateurs_motifs_reorientation() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueministerielle->getIndicateursMotifsReorientation( $this->request->data );
				$tranches = $this->Statistiqueministerielle->tranches;

				$this->set( compact( 'results', 'tranches' ) );
			}

			$this->set( 'title_for_layout', 'Indicateurs de motifs de réorientations' );
		}

		/**
		 * Moteur de recherche pour les indicateurs de caractéristiques de contrats.
		 *
		 * @return void
		 */
		public function indicateurs_caracteristiques_contrats() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueministerielle->getIndicateursCaracteristiquesContrats( $this->request->data );
				$durees_cers = array_keys( $this->Statistiqueministerielle->durees_cers );

				$this->set( compact( 'results', 'durees_cers' ) );
			}

			$this->set( 'title_for_layout', 'Indicateurs de caractéristiques des contrats' );
		}

		/**
		 * Moteur de recherche pour les indicateurs de natures de contrats.
		 *
		 * @return void
		 */
		public function indicateurs_natures_contrats() {
			if( !empty( $this->request->data ) ) {
				$results = $this->Statistiqueministerielle->getIndicateursNaturesContrats( $this->request->data );

				$this->set( compact( 'results' ) );
			}

			$this->set( 'title_for_layout', 'Indicateurs de natures des actions des contrats' );
		}

		/**
		 * Export csv pour les indicateurs d'orientation.
		 *
		 * @return void
		 */
		public function exportcsv_orientations() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueministerielle->getIndicateursOrientations( $named );
			$tranches = $this->Statistiqueministerielle->tranches;
			$filename = 'indicateurs_orientations';

			$export = $this->generationexportcsv($tranches, $results);

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Export csv pour les indicateurs de motifs de réorientations.
		 *
		 * @return void
		 */
		public function exportcsv_organismes() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueministerielle->getIndicateursOrganismes( $named );
			$filename = 'indicateurs_organismes';

			$export = array ();
			$export[0][0] = "Organismes de prise en charge des personnes dans le champ des Droits et Devoirs au 31 décembre de l'année ".$named['Search']['annee'].", dont le référent unique a été désigné";
			$export[0][1] = '';
			$i = 1;

			foreach ($results['Indicateurorganisme'] as $key => $value) {
				$export[$i][0] = $key;
				$export[$i][1] = is_null($value) ? 'NC' : $value;

				$i++;
			}

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Export csv pour les indicateurs de réorientation.
		 *
		 * @return void
		 */
		public function exportcsv_reorientations() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueministerielle->getIndicateursReorientations( $named );
			$tranches = $this->Statistiqueministerielle->tranches;
			$filename = 'indicateurs_reorientations';

			$export = $this->generationexportcsv($tranches, $results);

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Export csv pour les indicateurs de motifs de réorientations.
		 *
		 * @return void
		 */
		public function exportcsv_motifs_reorientation() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueministerielle->getIndicateursMotifsReorientation( $named );
			$filename = 'indicateurs_motifs_reorientation';

			$i = 0;
			$export = array ();
			$export[$i][0] = "Motifs des réorientations d'un organisme appartenant ou participant au SPE vers un organisme hors SPE au cours de l'année ".$named['Search']['annee'];
			$export[$i++][1] = '';
			$export[$i][0] = "Nombre de personnes réorientées d'un organisme appartenant ou participant au SPE vers un organisme hors SPE au cours de l'année";
			$export[$i++][1] = is_null ($results['Indicateursocial']['total']) ? 'ND' : $results['Indicateursocial']['total'];
			$export[$i][0] = "- orientation initiale inadaptée";
			$export[$i++][1] = is_null ($results['Indicateursocial']['orientation_initiale_inadaptee']) ? 'ND' : $results['Indicateursocial']['orientation_initiale_inadaptee'];
			$export[$i][0] = "- changement de situation de la personne (difficultés nouvelles de logement, santé, garde d'enfants, famille, ...)";
			$export[$i++][1] = is_null ($results['Indicateursocial']['changement_situation_allocataire']) ? 'ND' : $results['Indicateursocial']['changement_situation_allocataire'];
			$export[$i][0] = "- autres";
			$export[$i++][1] = is_null ($results['Indicateursocial']['autre']) ? 'ND' : $results['Indicateursocial']['autre'];
			$export[$i][0] = "Recours à l'article L262-31 au cours de l'année ".$named['Search']['annee'];
			$export[$i++][1] = '';
			$export[$i][0] = "Nombre de personnes dont le dossier a été examiné par l'équipe pluridisciplinaire dans le cadre de l'article L262-31 (à l'issue du délai de 6 à 12 mois sans réorientation vers le SPE) au cours de l'année";
			$export[$i++][1] = is_null ($results['Indicateurep']['total']) ? 'ND' : $results['Indicateurep']['total'];
			$export[$i][0] = "dont maintien de l'orientation dans un organisme hors SPE";
			$export[$i++][1] = is_null ($results['Indicateurep']['maintien']) ? 'ND' : $results['Indicateurep']['maintien'];
			$export[$i][0] = "dont réorientation vers un organisme appartenant ou participant au SPE";
			$export[$i++][1] = is_null ($results['Indicateurep']['reorientation']) ? 'ND' : $results['Indicateurep']['reorientation'];

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Export csv pour les indicateurs de caractéristiques de contrats.
		 *
		 * @return void
		 */
		public function exportcsv_caracteristiques_contrats() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueministerielle->getIndicateursCaracteristiquesContrats( $named );
			$durees_cers = array_keys( $this->Statistiqueministerielle->durees_cers );
			$filename = 'indicateurs_caracteristiques_contrats';

			$resultats = $results['Indicateurcaracteristique'];
			$i = 0;
			$export = array ();
			$suffixes = array( 'total', 'droitsdevoirs', 'horsdroitsdevoirs' );

			$export[$i][0] = "Nombre et type de contrats RSA en cours de validité au 31 décembre de l'année ".$named['Search']['annee'];
			$export[$i][1] = "";
			$export[$i][2] = "";
			$export[$i][3] = "";
			$i++;
			$export[$i][0] = "";
			$export[$i][1] = "Total";
			$export[$i][2] = "dont signataire du contrat dans le champ des droits et devoirs au 31 décembre";
			$export[$i][3] = "dont signataire du contrat hors du champ des droits et devoirs au 31 décembre";
			$i++;

			$categories = array( 'cer', 'ppae', 'cer_pro', 'cer_social_pro' );
			foreach ($categories as $categorie) {
				$export[$i][] = __d( 'statistiquesministerielles', "Indicateurcaracteristique.{$categorie}" );

				foreach ($suffixes as $suffixe) {
					$export[$i][] = is_null ($resultats[$categorie.'_'.$suffixe]) ? 'ND' : $resultats[$categorie.'_'.$suffixe];
				}
				$i++;
			}

			$export[$i][0] = "Nombre et type de contrats RSA en cours de validité au 31 décembre de l'année ".$named['Search']['annee'];
			$export[$i][1] = "";
			$export[$i][2] = "";
			$export[$i][3] = "";
			$i++;
			$export[$i][0] = "";
			$export[$i][1] = "Total";
			$export[$i][2] = "dont signataire du contrat dans le champ des droits et devoirs au 31 décembre";
			$export[$i][3] = "dont signataire du contrat hors du champ des droits et devoirs au 31 décembre";
			$i++;

			$categories = array( 'cer_pro', 'cer_social_pro' );
			foreach ($categories as $categorie) {
				$export[$i][] = __d( 'statistiquesministerielles', "Indicateurcaracteristique.{$categorie}_rappel" );
				foreach ($suffixes as $suffixe) {
					$export[$i][] = is_null ($resultats[$categorie.'_'.$suffixe]) ? 'ND' : $resultats[$categorie.'_'.$suffixe];
				}
				$i++;

				foreach ($durees_cers as $duree) {
					$export[$i][] = __d( 'statistiquesministerielles', "Indicateurcaracteristique.{$categorie}_{$duree}" );
					foreach ($suffixes as $suffixe) {
						$export[$i][] = is_null ($resultats[$categorie.'_'.$suffixe]) ? 'ND' : $resultats[$categorie.'_'.$duree.'_'.$suffixe];
					}
					$i++;
				}
			}

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Export csv pour les indicateurs de natures de contrats.
		 *
		 * @return void
		 */
		public function exportcsv_natures_contrats() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueministerielle->getIndicateursNaturesContrats( $named );
			$filename = 'indicateurs_natures_contrats';

			$titre = array(
				'spe' => "Actions des contrats d'engagement réciproque en cours de validité au 31 décembre pour les personnes dont le référent unique au 31 décembre appartenait à un organisme appartenant ou participant au SPE autre que Pôle emploi",
				'horsspe' => "Actions des contrats d'engagement réciproque en cours de validité au 31 décembre pour les personnes dont le référent unique au 31 décembre appartenait à un organisme n'appartenant et ne participant pas au SPE",
			);
			$export = array ();
			$export[0][0] = "Nature des actions d'insertion inscrites dans les contrats d'engagement réciproque en cours de validité au 31 décembre de l'année ".$named['Search']['annee'];
			$export[0][1] = '';
			$i = 1;

			foreach ($results['Indicateurnature'] as $key => $value) {
				$export[$i][0] = $titre[$key];
				$export[$i][1] = '';
				$i++;

				foreach ($value as $key2 => $value2) {
					$export[$i][0] = $key2;
					$export[$i][1] = is_null($value2) ? 'ND' : $value2;
					$i++;
				}
			}

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Export csv pour les indicateurs de délais.
		 *
		 * @return void
		 */
		public function exportcsv_delais() {
			$named = Hash::expand( $this->request->named, '__');
			$results = $this->Statistiqueministerielle->getIndicateursDelais( $named );
			$types_cers = $this->Statistiqueministerielle->types_cers;
			$filename = 'indicateurs_delais';

			$export = array ();
			$export[0][0] = "Délais entre les différentes étapes de l'orientation au cours de l'année ".$named['Search']['annee'];
			$export[0][1] = '';
			$export[1][0] = "a. Délai moyen entre la date d'ouverture de droit, telle qu'enregistrée par les organismes chargés du service de l'allocation, et la décision d'orientation validée par ".__d('default'.Configure::read('Cg.departement'), 'le Président du Conseil Général')." au cours de l'année";
			$export[1][1] = round (Hash::get( $results, 'Indicateurdelai.delai_moyen_orientation' ));
			$export[2][0] = "b. Délai moyen entre la décision d'orientation et la signature d'un contrat au cours de l'année";
			$export[2][1] = round (Hash::get( $results, 'Indicateurdelai.delai_moyen_signature' ));
			$i = 3;

			foreach( $types_cers as $type_cer => $delais ) {
				$export[$i][0] = __d( 'statistiquesministerielles', "Indicateurdelai.{$type_cer}_delai_moyen" );
				$value = Hash::get( $results, "Indicateurdelai.{$type_cer}_delai_moyen" );
				$value = ( is_null( $value ) ? 'ND' : round( $value ) );
				$export[$i][1] = $value;
				$i++;

				$export[$i][0] = __d( 'statistiquesministerielles', "Indicateurdelai.{$type_cer}_nombre_moyen" );
				$value = Hash::get( $results, "Indicateurdelai.{$type_cer}_nombre_moyen" );
				$value = ( is_null( $value ) ? 'ND' : round( $value ) );
				$export[$i][1] = $value;
				$i++;

				$export[$i][0] = __d( 'statistiquesministerielles', "Indicateurdelai.{$type_cer}_delai_{$delais['nbMoisTranche1']}_mois" );
				$value = Hash::get( $results, "Indicateurdelai.{$type_cer}_delai_{$delais['nbMoisTranche1']}_mois" );
				$value = ( is_null( $value ) ? 'ND' : round( $value ) );
				$export[$i][1] = $value;
				$i++;

				$export[$i][0] = __d( 'statistiquesministerielles', "Indicateurdelai.{$type_cer}_delai_{$delais['nbMoisTranche1']}_{$delais['nbMoisTranche2']}_mois" );
				$value = Hash::get( $results, "Indicateurdelai.{$type_cer}_delai_{$delais['nbMoisTranche1']}_{$delais['nbMoisTranche2']}_mois" );
				$value = ( is_null( $value ) ? 'ND' : round( $value ) );
				$export[$i][1] = $value;
				$i++;

				$export[$i][0] = __d( 'statistiquesministerielles', "Indicateurdelai.{$type_cer}_delai_plus_{$delais['nbMoisTranche2']}_mois" );
				$value = Hash::get( $results, "Indicateurdelai.{$type_cer}_delai_plus_{$delais['nbMoisTranche2']}_mois" );
				$value = ( is_null( $value ) ? 'ND' : round( $value ) );
				$export[$i][1] = $value;
				$i++;
			}

			$this->layout = '';
			$this->set( compact( 'export', 'filename' ) );
			$this->render('exportcsv');
		}

		/**
		 * Génération de certains csv
		 *
		 * @return void
		 */
		private function generationexportcsv ($tranches, $results) {
			$colonnes = array( 'sdd', 'orient_pro', 'orient_sociopro', 'orient_sociale', 'attente_orient' );
			$categories = array ('Age', 'Situation familliale', 'Niveau de formation', 'Ancienneté dans le dispositif, y compris anciens minima (RMI, API)');

			$total = array ();
			$export = array ();
			$i = 0;
			$k = 0;

			// Titre
			$export[$i++] = array_merge (array ('CAT'), $colonnes);

			foreach ($tranches as $categorie => $groupes) {
				$export[$i++] = array ($categories[$k++], '', '', '', '', '');
				$soustotal = array ();

				foreach ($groupes as $value) {
					$j = 0;
					$export[$i][$j++] = $value;
					$soustotal[$j++] = 'Total';

					foreach ($colonnes as $colonne) {
						// Ligne (écriture)
						$valeur = 0;
						if (isset ($results['Indicateur'.$categorie][$colonne][$value])) {
							$valeur = $results['Indicateur'.$categorie][$colonne][$value];
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
	}
?>
