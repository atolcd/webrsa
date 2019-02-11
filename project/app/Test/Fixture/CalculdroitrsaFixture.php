<?php
	/**
	 * Code source de la classe CalculdroitrsaFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * Classe CalculdroitrsaFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class CalculdroitrsaFixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Calculdroitrsa',
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
				'mtpersressmenrsa' => null,
				'mtpersabaneursa' => null,
				'toppersdrodevorsa' => '1',
				'toppersentdrodevorsa' => null
			)
		);

	}
?>