<?php
	/**
	 * Code source de la classe ActioncandidatPersonneFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * La classe ActioncandidatPersonneFixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class ActioncandidatPersonneFixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'ActioncandidatPersonne',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'personne_id' => 1,
				'actioncandidat_id' => 1, // TEST sur Action Région -> Doit avoir une progfichecandidature66_id
				'referent_id' => 1,
				'ddaction' => null,
				'dfaction' => null,
				'motifdemande' => '...',
				'enattente' => null,
				'datesignature' => '2012-11-12',
				'bilanvenu' => 'VEN',
				'bilanretenu' => 'RET',
				'infocomplementaire' => null,
				'datebilan' => '2012-12-10',
				'rendezvouspartenaire' => '0',
				'mobile' => '1',
				'naturemobile' => "commune",
				'typemobile' => "véhicule",
				'bilanrecu' => null,
				'daterecu' => null,
				'personnerecu' => null,
				'pieceallocataire' => null,
				'autrepiece' => null,
				'precisionmotif' => null,
				'presencecontrat' => null,
				'integrationaction' => null,
				'horairerdvpartenaire' => null,
				'sortiele' => null,
				'motifsortie_id' => 1,
				'positionfiche' => "encours",
				'issortie' => null,
				'haspiecejointe' => '1',
				'motifannulation' => null,
				'lieurdvpartenaire' => null,
				'personnerdvpartenaire' => null,
				'poursuitesuivicg' => null,
				'formationregion' => null,
				'progfichecandidature66_id' => 1,
				'nomprestataire' => null,
			),
		);

	}
?>