<?php
	/**
	 * Code source de la classe ContratinsertionFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * Classe ContratinsertionFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class ContratinsertionFixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Contratinsertion',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'personne_id' => 2,
				'structurereferente_id' => 1,
				'typocontrat_id' => null,
				'dd_ci' => '2011-03-01',
				'df_ci' => '2011-05-31',
				'diplomes' => null,
				'form_compl' => null,
				'expr_prof' => null,
				'aut_expr_prof' => null,
				'rg_ci' => 1,
				'actions_prev' => null,
				'obsta_renc' => null,
				'service_soutien' => null,
				'pers_charg_suivi' => null,
				'objectifs_fixes' => null,
				'engag_object' => null,
				'sect_acti_emp' => null,
				'emp_occupe' => null,
				'duree_hebdo_emp' => null,
				'nat_cont_trav' => null,
				'duree_cdd' => null,
				'duree_engag' => null,
				'nature_projet' => null,
				'observ_ci' => null,
				'decision_ci' => 'V',
				'datevalidation_ci' => null,
				'date_saisi_ci' => null,
				'lieu_saisi_ci' => null,
				'emp_trouv' => null,
				'forme_ci' => null,
				'commentaire_action' => null,
				'raison_ci' => null,
				'aviseqpluri' => null,
				'sitfam_ci' => null,
				'sitpro_ci' => null,
				'observ_benef' => null,
				'referent_id' => null,
				'avisraison_ci' => null,
				'type_demande' => null,
				'num_contrat' => null,
				'typeinsertion' => null,
				'bilancontrat' => null,
				'engag_object_referent' => null,
				'outilsmobilises' => null,
				'outilsamobiliser' => null,
				'niveausalaire' => null,
				'zonegeographique_id' => null,
				'autreavisradiation' => null,
				'autreavissuspension' => null,
				'datesuspensionparticulier' => null,
				'dateradiationparticulier' => null,
				'faitsuitea' => null,
				'positioncer' => null,
				'created' => '2012-10-25 12:00:00',
				'modified' => '2012-10-25 12:00:00',
				'current_action' => null,
				'haspiecejointe' => '0',
				'avenant_id' => null,
				'sitfam' => null,
				'typeocclog' => null,
				'persacharge' => null,
				'objetcerprecautre' => null,
				'motifannulation' => null,
				'datedecision' => null,
				'datenotification' => null,
				'actioncandidat_id' => null,
			),
			array(
				'personne_id' => 1,
				'structurereferente_id' => 1,
				'typocontrat_id' => null,
				'dd_ci' => '2011-03-01',
				'df_ci' => '2011-05-31',
				'diplomes' => null,
				'form_compl' => null,
				'expr_prof' => null,
				'aut_expr_prof' => null,
				'rg_ci' => null,
				'actions_prev' => null,
				'obsta_renc' => null,
				'service_soutien' => null,
				'pers_charg_suivi' => null,
				'objectifs_fixes' => null,
				'engag_object' => null,
				'sect_acti_emp' => null,
				'emp_occupe' => null,
				'duree_hebdo_emp' => null,
				'nat_cont_trav' => null,
				'duree_cdd' => null,
				'duree_engag' => null,
				'nature_projet' => null,
				'observ_ci' => null,
				'decision_ci' => 'E',
				'datevalidation_ci' => null,
				'date_saisi_ci' => null,
				'lieu_saisi_ci' => null,
				'emp_trouv' => null,
				'forme_ci' => null,
				'commentaire_action' => null,
				'raison_ci' => null,
				'aviseqpluri' => null,
				'sitfam_ci' => null,
				'sitpro_ci' => null,
				'observ_benef' => null,
				'referent_id' => null,
				'avisraison_ci' => null,
				'type_demande' => null,
				'num_contrat' => null,
				'typeinsertion' => null,
				'bilancontrat' => null,
				'engag_object_referent' => null,
				'outilsmobilises' => null,
				'outilsamobiliser' => null,
				'niveausalaire' => null,
				'zonegeographique_id' => null,
				'autreavisradiation' => null,
				'autreavissuspension' => null,
				'datesuspensionparticulier' => null,
				'dateradiationparticulier' => null,
				'faitsuitea' => null,
				'positioncer' => null,
				'created' => '2012-10-25 12:00:00',
				'modified' => '2012-10-25 12:00:00',
				'current_action' => null,
				'haspiecejointe' => '0',
				'avenant_id' => null,
				'sitfam' => null,
				'typeocclog' => null,
				'persacharge' => null,
				'objetcerprecautre' => null,
				'motifannulation' => null,
				'datedecision' => null,
				'datenotification' => null,
				'actioncandidat_id' => null,
			),
			array(
				'personne_id' => 3,
				'structurereferente_id' => 1,
				'typocontrat_id' => null,
				'dd_ci' => '2011-03-01',
				'df_ci' => '2011-05-31',
				'diplomes' => null,
				'form_compl' => null,
				'expr_prof' => null,
				'aut_expr_prof' => null,
				'rg_ci' => null,
				'actions_prev' => null,
				'obsta_renc' => null,
				'service_soutien' => null,
				'pers_charg_suivi' => null,
				'objectifs_fixes' => null,
				'engag_object' => null,
				'sect_acti_emp' => null,
				'emp_occupe' => null,
				'duree_hebdo_emp' => null,
				'nat_cont_trav' => null,
				'duree_cdd' => null,
				'duree_engag' => null,
				'nature_projet' => null,
				'observ_ci' => null,
				'decision_ci' => 'E',
				'datevalidation_ci' => null,
				'date_saisi_ci' => null,
				'lieu_saisi_ci' => null,
				'emp_trouv' => null,
				'forme_ci' => null,
				'commentaire_action' => null,
				'raison_ci' => null,
				'aviseqpluri' => null,
				'sitfam_ci' => null,
				'sitpro_ci' => null,
				'observ_benef' => null,
				'referent_id' => null,
				'avisraison_ci' => null,
				'type_demande' => null,
				'num_contrat' => null,
				'typeinsertion' => null,
				'bilancontrat' => null,
				'engag_object_referent' => null,
				'outilsmobilises' => null,
				'outilsamobiliser' => null,
				'niveausalaire' => null,
				'zonegeographique_id' => null,
				'autreavisradiation' => null,
				'autreavissuspension' => null,
				'datesuspensionparticulier' => null,
				'dateradiationparticulier' => null,
				'faitsuitea' => null,
				'positioncer' => null,
				'created' => '2012-10-25 12:00:00',
				'modified' => '2012-10-25 12:00:00',
				'current_action' => null,
				'haspiecejointe' => '0',
				'avenant_id' => null,
				'sitfam' => null,
				'typeocclog' => null,
				'persacharge' => null,
				'objetcerprecautre' => null,
				'motifannulation' => null,
				'datedecision' => null,
				'datenotification' => null,
				'actioncandidat_id' => null,
			)
		);

	}
?>