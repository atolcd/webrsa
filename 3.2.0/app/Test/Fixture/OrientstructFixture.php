<?php
	/**
	 * Code source de la classe OrientstructFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * Classe OrientstructFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class OrientstructFixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Orientstruct',
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
				'typeorient_id' => 1,
				'structurereferente_id' => 1,
				'propo_algo' => null,
				'valid_cg' => null,
				'date_propo' => null,
				'date_valid' => '2009-06-24',
				'statut_orient' => 'Orienté',
				'date_impression' => null,
				'daterelance' => null,
				'statutrelance' => null,
				'date_impression_relance' => null,
				'referent_id' => null,
				'etatorient' => null,
				'rgorient' => 1,
				'structureorientante_id' => null,
				'referentorientant_id' => null,
				'user_id' => null,
				'haspiecejointe' => '0',
				'origine' => 'manuelle',
				'typenotification' => null,
			)
		);

	}
?>