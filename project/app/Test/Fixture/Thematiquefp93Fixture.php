<?php
	/**
	 * Code source de la classe Thematiquefp93Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * La classe Thematiquefp93Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Thematiquefp93Fixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Thematiquefp93',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'type' => 'pdi',
				'name' => 'Thématique de test',
				'created' => null,
				'modified' => null
			)
		);

	}
?>