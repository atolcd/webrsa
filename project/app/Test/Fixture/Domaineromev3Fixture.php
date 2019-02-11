<?php
	/**
	 * Code source de la classe Domaineromev3Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * La classe Domaineromev3Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Domaineromev3Fixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Domaineromev3',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'familleromev3_id' => 1,
				'code' => '11',
				'name' => 'Engins agricoles et forestiers'
			)
		);

	}
?>