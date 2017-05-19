<?php
	/**
	 * Code source de la classe ApreFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * Classe ApreFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class ApreFixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Apre',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'personne_id' => 4,
				'numeroapre' => null,
				'typedemandeapre' => null,
				'datedemandeapre' => null,
				'naturelogement' => null,
				'precisionsautrelogement' => null,
				'anciennetepoleemploi' => null,
				'projetprofessionnel' => null,
				'secteurprofessionnel' => null,
				'activitebeneficiaire' => null,
				'dateentreeemploi' => null,
				'typecontrat' => null,
				'precisionsautrecontrat' => null,
				'nbheurestravaillees' => null,
				'nomemployeur' => null,
				'adresseemployeur' => null,
				'quota' => null,
				'derogation' => null,
				'avistechreferent' => null,
				'etatdossierapre' => 'VAL',
				'eligibiliteapre' => null,
				'mtforfait' => null,
				'secteuractivite' => null,
				'nbenf12' => null,
				'statutapre' => 'C',
				'justificatif' => null,
				'structurereferente_id' => null,
				'referent_id' => null,
				'montantaverser' => null,
				'nbpaiementsouhait' => null,
				'montantdejaverse' => null,
				'dureecontrat' => null,
				'isdecision' => 'N',
				'hasfrais' => null,
				'haspiecejointe' => '0',
				'cessderact' => null,
				'nivetu' => null,
				'isbeneficiaire' => null,
				'hascer' => null,
				'respectdelais' => null,
				'dateimpressionapre' => null,
				'datenotifapre' => null,
				'istraite' => '0',
				'istransfere' => '0',
				'motifannulation' => null,
			),
			array(
				'personne_id' => 5,
				'numeroapre' => null,
				'typedemandeapre' => null,
				'datedemandeapre' => null,
				'naturelogement' => null,
				'precisionsautrelogement' => null,
				'anciennetepoleemploi' => null,
				'projetprofessionnel' => null,
				'secteurprofessionnel' => null,
				'activitebeneficiaire' => null,
				'dateentreeemploi' => null,
				'typecontrat' => null,
				'precisionsautrecontrat' => null,
				'nbheurestravaillees' => null,
				'nomemployeur' => null,
				'adresseemployeur' => null,
				'quota' => null,
				'derogation' => null,
				'avistechreferent' => null,
				'etatdossierapre' => 'VAL',
				'eligibiliteapre' => null,
				'mtforfait' => null,
				'secteuractivite' => null,
				'nbenf12' => null,
				'statutapre' => 'C',
				'justificatif' => null,
				'structurereferente_id' => null,
				'referent_id' => null,
				'montantaverser' => null,
				'nbpaiementsouhait' => null,
				'montantdejaverse' => null,
				'dureecontrat' => null,
				'isdecision' => 'N',
				'hasfrais' => null,
				'haspiecejointe' => '0',
				'cessderact' => null,
				'nivetu' => null,
				'isbeneficiaire' => null,
				'hascer' => null,
				'respectdelais' => null,
				'dateimpressionapre' => null,
				'datenotifapre' => null,
				'istraite' => '0',
				'istransfere' => '0',
				'motifannulation' => null,
			),
		);

	}
?>