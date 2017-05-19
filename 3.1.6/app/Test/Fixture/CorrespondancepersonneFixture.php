<?php
	/**
	 * Code source de la classe CorrespondancepersonneFixture.
	 *
	 * @package app.Test.Fixture
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/Test/CakePHP Fixture.php.
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