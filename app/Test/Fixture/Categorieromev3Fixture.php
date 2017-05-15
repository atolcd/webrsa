<?php
	/**
	 * Code source de la classe Categorieromev3Fixture.
	 *
	 * @package app.Test.Fixture
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/Test/CakePHP Fixture.php.
	 */

	/**
	 * La classe Categorieromev3Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Categorieromev3Fixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Entreeromev3',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
		);

	}
?>