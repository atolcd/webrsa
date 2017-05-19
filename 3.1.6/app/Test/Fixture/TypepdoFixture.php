<?php
	/**
	 * Code source de la classe TypepdoFixture.
	 *
	 * @package app.Test.Fixture
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/Test/CakePHP Fixture.php.
	 */
	 app::uses('CakeAppTestFixture', 'Test/Fixture');
	 
	/**
	 * La classe TypepdoFixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class TypepdoFixture extends CakeAppTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Typepdo',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'libelle' => 'TypePdo test',
				'originepcg' => 'N',
				'cerparticulier' => 'N',
			)
		);

	}
?>