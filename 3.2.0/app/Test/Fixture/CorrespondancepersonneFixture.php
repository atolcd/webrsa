<?php
	/**
	 * Code source de la classe CorrespondancepersonneFixture.
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * La classe CorrespondancepersonneFixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class CorrespondancepersonneFixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Correspondancepersonne',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
//			array(
//				'personne1_id' => 1,
//				'personne2_id' => 2,
//			),
//			array(
//				'personne1_id' => 2,
//				'personne2_id' => 1,
//			)
		);

	}
?>