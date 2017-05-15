<?php
	/**
	 * Code source de la classe GrossesseFixture.
	 *
	 * @package app.Test.Fixture
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/Test/CakePHP Fixture.php.
	 */

	/**
	 * La classe GrossesseFixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class GrossesseFixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Grossesse',
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