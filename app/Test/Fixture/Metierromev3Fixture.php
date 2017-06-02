<?php
	/**
	 * Code source de la classe Metierromev3Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * La classe Metierromev3Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Metierromev3Fixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Metierromev3',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'domaineromev3_id' => 1,
				'code' => '01',
				'name' => 'Conduite d\'engins d\'exploitation agricole et forestière'
			)
		);

	}
?>