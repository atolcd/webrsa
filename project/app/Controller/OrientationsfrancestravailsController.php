<?php
	/**
	 * Code source de la classe OrientationsfrancestravailsController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe OrientationsfrancestravailsController ...
	 *
	 * @package app.Controller
	 */
	class OrientationsfrancestravailsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Orientationsfrancestravails';

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
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault',
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array('Orientationfrancetravail', 'Personne');

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
		public $aucunDroit = array();

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
		public function getOptions() {
			return [
				'Orientationfrancetravail' => [
					'crit_origine_calcul' => [
						'INSCRIPTION_VOLONTAIRE' => 'Inscription volontaire',
						'INSCRIPTION_AUTO_MILO' => 'Inscription automatique Mission Locale',
						'INSCRIPTION_AUTO_CAF' => 'Inscription automatique CAF',
						'INSCRIPTION_AUTO_MSA' => 'Inscription automatique MSA',
						'INSCRIPTION_CONSEILLER_FT' => 'Inscription conseiller France Travail',
						'RDS_LPE' => 'Reprise de stock Loi Plein Emploi',
					],
					'crit_situation_professionnelle' => [
						'EN_ACTIVITE' => 'En activité',
						'DEUX_DERNIERES_ANNEES' => 'En activité dans les 2 dernières années',
						'PLUS_DEUX_ANS' => 'En activité il y a plus de 2 ans',
						'JAMAIS' => 'N\'a jamais travaillé',
					],
					'crit_type_emploi' => [
						'SAISONNIER' => 'Saisonnier',
						'ARTISTE_OU_AUTEUR' => 'Artiste ou auteur',
						'INTERMITTENT_SPECTACLE' => 'Intermittent du spectacle',
						'EXPLOITANT_AGRICOLE' => 'Exploitant agricole',
						'ASSISTANTE_MATERNELLE' => 'Assistante maternelle',
						'CREATEUR_ENTREPRISE_OU_INDEPENDANT' => 'Créateur d\'entreprise ou indépendant',
						'AUTRE' => 'Autre',
					],
					'crit_niveau_etude' => [
						'AFS' => 'Aucune formation scolaire',
						'C12' => '2nd ou 1ère achevée',
						'C3A' => '3ème achevée ou Brevet',
						'CFG' => '4ème achevée',
						'CP4' => 'Primaire à 4ème',
						'NV1' => 'Bac+5 et plus ou équivalents',
						'NV2' => 'Bac+3, Bac+4 ou équivalents',
						'NV3' => 'Bac+2 ou équivalents',
						'NV4' => 'Bac ou équivalent',
						'NV5' => 'CAP, BEP et équivalents',
					],
					'crit_capacite_a_travailler' => [
						false => 'Non',
						true => 'Oui',
					],
					'crit_projet_pro' => [
						'SALARIAT' => 'Salariat',
						'ENTREPRENARIAT' => 'Entreprenariat',
						'FORMATION' => 'Formation',
						'AUCUN' => 'Aucun',
					],
					'crit_contrainte_sante' => [
						'INCAPACITE_RECHERCHE_ACTIVITE_PRO' => 'Incapacité à rechercher une activité professionnelle',
						'IMPACT_FORT_RECHERCHE_EMPLOI' => 'Impact fort sur la recherche d\'emploi',
						'IMPACT_FAIBLE_RECHERCHE_EMPLOI' => 'Impact faible sur la recherche d\'emploi',
						'AUCUNE' => 'Aucune',
					],
					'crit_contrainte_logement' => [
						'SANS_LOGEMENT' => 'Sans logement',
						'PROCEDURE_EXPULSION' => 'En procédure d\'expulsion',
						'IMPACT_FORT_RECHERCHE_EMPLOI' => 'Impact fort sur la recherche d\'emploi',
						'IMPACT_FAIBLE_RECHERCHE_EMPLOI' => 'Impact faible sur la recherche d\'emploi',
						'AUCUNE' => 'Aucune',
					],
					'crit_contrainte_mobilite' => [
						'AUCUN_MOYEN_DE_TRANSPORT' => 'Aucun moyen de transport',
						'DEPENDANT_TRANSPORT' => 'Dépendant des transports en commun',
						'AUTRE' => 'Autre',
						'AUCUNE' => 'Aucune',
					],
					'crit_contrainte_familiale' => [
						'AIDANT_FAMILIAL' => 'Aidant familial',
						'ENFANT_SANS_SOLUTION_GARDE' => 'Enfant sans solution de garde',
						'IMPACT_FORT_RECHERCHE_EMPLOI' => 'Impact fort sur la recherche d\'emploi',
						'IMPACT_FAIBLE_RECHERCHE_EMPLOI' => 'Impact faible sur la recherche d\'emploi',
						'AUCUNE' => 'Aucune',
					],
					'crit_contrainte_financiere' => [
						'IMPACT_FORT_RECHERCHE_EMPLOI' => 'Impact fort sur la recherche d\'emploi',
						'IMPACT_FAIBLE_RECHERCHE_EMPLOI' => 'Impact faible sur la recherche d\'emploi',
						'AUCUNE' => 'Aucune',
					],
					'crit_contrainte_numerique' => [
						'NON_ACCES_INTERNET' => 'Pas d\'accès à internet',
						'NON_MAITRISE' => 'Non maitrisé',
						'REFUS' => 'Refus',
						'AUCUNE' => 'Aucune',
					],
					'crit_contrainte_admin_jur' => [
						'IMPACT_FORT_RECHERCHE_EMPLOI' => 'Impact fort sur la recherche d\'emploi',
						'IMPACT_FAIBLE_RECHERCHE_EMPLOI' => 'Impact faible sur la recherche d\'emploi',
						'AUCUNE' => 'Aucune',
					],
					'crit_contrainte_francais_calcul' => [
						'IMPACT_FORT_RECHERCHE_EMPLOI' => 'Impact fort sur la recherche d\'emploi',
						'IMPACT_FAIBLE_RECHERCHE_EMPLOI' => 'Impact faible sur la recherche d\'emploi',
						'AUCUNE' => 'Aucune',
					],
					'crit_boe' => [
						false => 'Non',
						true => 'Oui',
					],
					'crit_baeeh' => [
						false => 'Non',
						true => 'Oui',
					],
					'crit_scolarite_etab_spec' => [
						false => 'Non',
						true => 'Oui',
					],
					'crit_esat' => [
						false => 'Non',
						true => 'Oui',
					],
					'crit_boe_souhait_accompagnement' => [
						false => 'Non',
						true => 'Oui',
					],
					'crit_msa_autonomie_recherche_emploi' => [

					],
					'crit_msa_demarches_professionnelles' => [
						'AUCUNE_DEMARCHE' => 'Aucune démarche',
						'PEU_DEMARCHES' => 'Peu de démarches',
						'PLUSIEURS_DEMARCHES' => 'Plusieurs démarches',
					],
					'decision_etat' => [
						'REFUSEE' => 'Refusée',
						'ACCEPTEE' => 'Acceptée',
					],
					'decision_organisme' => [
						'IND' => 'Indéterminé',
						'FT' => 'France Travail',
						'CD' => 'Conseil départemental',
						'ML' => 'Mission Locale',
						'CE' => 'Cap Emploi',
					],
					'decision_motif_refus' => [
						'DECLARATION_INEXACTE' => 'Déclaration inexacte',
						'SITUATION_NON_COUVERTE' => 'Situation non couverte',
						'APPRECIATION_DIFFERENTE' => 'Appréciation différente',
						'MANQUE_CAPACITE_ACCUEIL' => 'Manque de capacité d\'accueil',
						'AUTRE' => 'Autre',
					],
				]
			];
		}
		/**
		 *
		 * @param integer $infoagricole_id
		 */
		public function view( $personne_id ) {
			$this->assert( valid_int( $personne_id ), 'error404' );

			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$personne = $this->Personne->find(
				'first',
				array(
					'conditions' => array(
						'Personne.id' => $personne_id
					),
					'recursive' => -1
				)
			);

			$orientation_france_travail = $this->Orientationfrancetravail->find(
				'first',
				array(
					'conditions' => array(
						'Orientationfrancetravail.personne_id' => $personne_id
					),
					'recursive' => -1
				)
			);

			// Liste des traductions des critères
			$options = $this->getOptions();

			// Assignations à la vue
			$this->set( compact('personne', 'orientation_france_travail', 'options') );
		}
	}
?>