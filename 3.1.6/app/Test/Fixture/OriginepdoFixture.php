<?php
	/**
	 * Code source de la classe OriginepdoFixture.
	 *
	 * @package app.Test.Fixture
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/Test/CakePHP Fixture.php.
	 */
	 app::uses('CakeAppTestFixture', 'Test/Fixture');

	/**
	 * La classe OriginepdoFixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class OriginepdoFixture extends CakeAppTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Originepdo',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'libelle' => 'OriginePdo test',
				'originepcg' => 'N',
				'cerparticulier' => 'N',
			)
		);

	}
?>