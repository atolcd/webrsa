<?php
	/**
	 * Code source de la classe Decisiondossierpcg66Fixture.
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * La classe Decisiondossierpcg66Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Decisiondossierpcg66Fixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Decisiondossierpcg66',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'dossierpcg66_id' => 1,
				'haspiecejointe' => 0,
			)
		);

	}
?>