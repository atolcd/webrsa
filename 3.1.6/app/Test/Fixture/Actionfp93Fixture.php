<?php
	/**
	 * Code source de la classe Actionfp93Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * La classe Actionfp93Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Actionfp93Fixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Actionfp93',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'filierefp93_id' => 1,
				'adresseprestatairefp93_id' => 1,
				'name' => 'Action de test',
				'numconvention' => '93XXX1300001',
				'annee' => 2013,
				'actif' => '0',
				'created' => null,
				'modified' => null
			),
			array(
				'filierefp93_id' => 1,
				'adresseprestatairefp93_id' => 1,
				'name' => 'Action de test',
				'numconvention' => '93XXX1400001',
				'annee' => 2014,
				'actif' => '1',
				'created' => null,
				'modified' => null
			),
		);

	}
?>